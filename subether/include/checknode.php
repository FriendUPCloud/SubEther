<?php

/*******************************************************************************
*   SubEther, The Decentralized Network.                                       *
*   Copyright (C) 2012 Friend Studios AS                                       *
*                                                                              *
*   This program is free software: you can redistribute it and/or modify       *
*   it under the terms of the GNU Affero General Public License as             *
*   published by the Free Software Foundation, either version 3 of the         *
*   License, or (at your option) any later version.                            *
*                                                                              *
*   This program is distributed in the hope that it will be useful,            *
*   but WITHOUT ANY WARRANTY; without even the implied warranty of             *
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the              *
*   GNU Affero General Public License for more details.                        *
*                                                                              *
*   You should have received a copy of the GNU Affero General Public License   *
*   along with this program.  If not, see <https://www.gnu.org/licenses/>.     *
*******************************************************************************/

$root = ( file_exists( "config.php" ) ? '.' : '../..' );

include_once ( "$root/subether/restapi/functions.php" );


function CheckNodeInformation ( $url )
{
	$agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:33.0) Gecko/20100101 Firefox/33.0' );
	
	// Only need the first 10 bytes
	//$headers = array( 'Range: bytes=0-10' );
	// Only need the first 6kb
	$headers = array( 'Range: bytes=0-60000' );
	
	$ok = false; $out = false;
	
	if( !$url ) return false;
	
	// Create support for other means if we don't have curl support
	
	$c = curl_init();
	
	curl_setopt( $c, CURLOPT_URL, $url );
	curl_setopt( $c, CURLOPT_FAILONERROR, 1 );
	
	curl_setopt( $c, CURLOPT_HTTPHEADER, $headers );
	
	curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $c, CURLOPT_USERAGENT, $agent );
	
	$r = curl_exec( $c );
	
	$ok = curl_getinfo( $c );
	
	if( $r !== false && $ok && ( $ok['http_code'] == 200 || $ok['http_code'] == 301 || $ok['http_code'] == 206 ) )
	{
		$out = $r;
	}
	
	curl_close( $c );
	
	return $out;
}



if( isset( $_POST['url'] ) )
{
	$verify = getNodeVerification();
	
	$url = $_POST['url'] . ( substr( $_POST['url'], -1 ) != '/' ? '/' : '' );
	
	$res = CheckNodeInformation( $url . 'api-json/information/' . ( $verify ? ( $verify . '/' ) : '' ) );
	
	if( !$res || ( $res && !strstr( $res, '{"response":"ok"' ) ) )
	{
		$http = explode( '://', $url );
		$domain = ( isset( $http[1] ) ? explode( '/', $http[1] ) : false ); 
		
		if( $domain && isset( $domain[0] ) && $http[0] )
		{
			$url = ( $http[0] . '://' . $domain[0] );
			
			$res = CheckNodeInformation( $url . 'api-json/information/' . ( $verify ? ( $verify . '/' ) : '' ) );
		}
	}
	
	if( $res && strstr( $res, '{"response":"ok"' ) )
	{
		$json = json_decode( $res );
		
		if( $json && isset( $json->items ) && isset( $json->items->Information ) && isset( $json->items->Information->UniqueID ) && $json->items->Information->UniqueID != '' )
		{
			$nodes = new dbObject( 'SNodes' );
			$nodes->UniqueID = (string)$json->items->Information->UniqueID;
			if( !$nodes->Load() )
			{
				$nodes->Url = (string)$json->items->Information->Url;
				$nodes->Save();
				
				if( isset( $json->items->Information->Nodes ) )
				{
					foreach( $json->items->Information->Nodes as $v )
					{
						if( isset( $v->Node->UniqueID ) && isset( $v->Node->Url ) )
						{
							$nod = new dbObject( 'SNodes' );
							$nod->UniqueID = (string)$v->Node->UniqueID;
							if( !$nod->Load() )
							{
								$nod->Url = (string)$v->Node->Url;
								$nod->Save();
							}
						}
					}
				}
				
				die( 'ok<!--separate-->' . $res . ' [] query: ' . ( $url . 'api-json/information/' . ( $verify ? ( $verify . '/' ) : '' ) ) );
			}
			
			die( 'no new nodes<!--separate-->' . $res . ' [] query: ' . ( $url . 'api-json/information/' . ( $verify ? ( $verify . '/' ) : '' ) ) );
		}
	}
}

die( 'fail' );

?>
