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

global $database;

include_once ( BASE_DIR . '/subether/classes/posthandler.class.php' );
include_once ( BASE_DIR . '/subether/classes/fcrypto.class.php' );

if ( $nodes = $database->fetchObjectRows( '
	SELECT ID, UniqueID, Url
	FROM SNodes
	WHERE IsMain = "0" AND IsConnected = "1" AND IsDenied = "0" AND IsAllowed = "1" AND PublicKey != "" 
	ORDER BY ID ASC
' ) )
{
	$main = new dbObject( 'SNodes' );
	$main->IsMain = 1;
	if ( $main->Load() && $main->UniqueID && $main->PublicKey )
	{
		foreach( $nodes as $node )
		{
			// Check login state
			if ( $node->Url )
			{
				$au = new PostHandler ( $node->Url . 'authenticate/' );
				$au->AddVar ( 'Source', 'node' );
				$au->AddVar ( 'UniqueID', $main->UniqueID );
				$res = $au->send ();
				$dat = simplexml_load_string ( $res );
				
				// Log api activity
				logActivity ( $res, $node->Url . 'authenticate/', $au->vars, 'authenticate.log' );
				
				if ( $dat->response == 'ok' && $dat->authkey )
				{
					if ( $privkey = getServerKeys( $root, 'privatekey' ) )
					{
						$fcrypt = new fcrypto();
						
						$plaintext = $fcrypt->decryptRSA( (string)$dat->authkey, $privkey );
						
						if ( $plaintext && ( $signature = $fcrypt->signString( $plaintext, $privkey ) ) )
						{
							$ph = new PostHandler ( $node->Url . 'authenticate/' );
							$ph->AddVar ( 'Source', 'node' );
							$ph->AddVar ( 'UniqueID', $main->UniqueID );
							$ph->AddVar ( 'Signature', $signature );
							$res = $ph->send ();
							$xml = simplexml_load_string ( $res ); 
							
							// Log api activity
							logActivity ( $res, $node->Url . 'authenticate/', $ph->vars, 'authenticate.log' );
							
							if ( $xml->response == 'ok' && $xml->sessionid )
							{
								$n = new dbObject( 'SNodes' );
								if ( $n->Load( $node->ID ) )
								{
									$sessionid = $fcrypt->decryptRSA( (string)$xml->sessionid, $privkey );
									
									$n->AuthKey = $plaintext;
									$n->SessionID = ( $sessionid ? $sessionid : (string)$xml->sessionid );
									$n->DateModified = date( 'Y-m-d H:i:s' );
									$n->DateLogin = date( 'Y-m-d H:i:s' );
									$n->Save();
								}
							}
						}
						else
						{
							// Log api activity
							logActivity ( ( $privkey.' [] '.(string)$dat->authkey.' [] '.$plaintext.' [] '.$signature ), $node->Url . 'authenticate/', false, 'error.log' );
						}
					}
					else
					{
						// Log api activity
						logActivity ( ( $privkey.' [] '.(string)$dat->authkey ), $node->Url . 'authenticate/', false, 'error.log' );
					}
				}
			}
			
		}
	}
}

?>
