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

function microtime_float()
{
    list( $usec, $sec ) = explode( " ", microtime() );
    return ( (float)$usec + (float)$sec );
}

function getContentFast ( $url )
{
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	return curl_exec( $ch );
}

function updateSearchEntry ( $r, &$d )
{
	if( !$r || !$d ) return false;
	
	//ob_clean ();
	if ( preg_match ( '/\<title\>([\w\W]*?)\<\/title\>/i', $r, $m ) )
	{
		$d->Title = trim ( strip_tags ( $m[1] ) );
	}
	if ( preg_match ( '/\<meta name\=\"description\" content\=\"([^"]*?)\"/i', $r, $desc ) )
	{
		$d->Description = trim ( strip_tags ( $desc[1] ) );
	}
	if ( preg_match ( '/\<h1[^>]*?\>[\w\W]*?\<\/h1\>([\w\W]*)/i', $r, $h ) )
	{
		$h = trim ( preg_replace ( '/\<li[^>]*?\>[\w\W]*?\<\/li\>/i', '', $h[1] ) );
		if ( trim ( strip_tags ( $h ) ) )
		{
			$d->Leadin = trim ( strip_tags ( $h ) );
		}
	}
	else if ( preg_match ( '/\<h2[^>]*?\>[\w\W]*?\<\/h2\>([\w\W]*)/i', $r, $h ) )
	{
		$h = trim ( preg_replace ( '/\<li[^>]*?\>[\w\W]*?\<\/li\>/i', '', $h[1] ) );
		if ( trim ( strip_tags ( $h ) ) )
		{
			$d->Leadin = trim ( strip_tags ( $h ) );
		}
	}
	else if ( preg_match ( '/\<h3[^>]*?\>[\w\W]*?\<\/h3\>([\w\W]*)/i', $r, $h ) )
	{
		$h = trim ( preg_replace ( '/\<li[^>]*?\>[\w\W]*?\<\/li\>/i', '', $h[1] ) );
		if ( trim ( strip_tags ( $h ) ) )
		{
			$d->Leadin = trim ( strip_tags ( $h ) );
		}
	}
	else if ( preg_match ( '/\<strong[^>]*?\>[\w\W]*?\<\/strong\>([\w\W]*)/i', $r, $h ) )
	{
		$h = trim ( preg_replace ( '/\<li[^>]*?\>[\w\W]*?\<\/li\>/i', '', $h[1] ) );
		if ( trim ( strip_tags ( $h ) ) )
		{
			$d->Leadin = trim ( strip_tags ( $h ) );
		}
	}
	//ob_clean ();
	
	return true;
}

function link_exists( $url ) 
{
	$c = curl_init( $url );

	curl_setopt( $c, CURLOPT_NOBODY, true );

	$r = curl_exec( $c );

	$o = false;

	if ( $r !== false ) 
	{
		$s = curl_getinfo( $c, CURLINFO_HTTP_CODE );  

		if ( $s == 200 )
		{
			$o = true;
		}
	}

	curl_close( $c );

	return $o;
}

function get_links_from_url( $url )
{
	if( link_exists( $url ) != false || $url != '' ) 
	{
		if( $r = getContentFast ( $url ) )
		{
			return $r;
		}
		return false;
	}
	return false;
}

$root = '/var/www/subether';
include_once ( "$root/lib/classes/database/cdatabase.php" );
include_once ( "$root/lib/classes/dbObjects/dbObject.php" );
include_once ( "$root/lib/lib.php" );

$database = new cDatabase ();
$database->SetUsername ( 'root' );
$database->SetPassword ( 'fjops590' );
$database->SetHostname ( 'localhost' );
$database->SetDb ( 'subether2' );

$database->open () or die ( 'Failed to connect' );

dbObject::globalValueSet ( 'database', $database );

$time_start = microtime_float();

print ( "\n" . 'Storing to Engine!' . "\n\n" );

$i = 0; $ii = 0; $broken = 0; $parsed = 0;

if ( $rows = $database->fetchObjectRows ( 'SELECT * FROM SEngineLinks WHERE IsParsed = "0" AND IsBroken = "0" ORDER BY ID ASC LIMIT 50' ) )
{
	foreach ( $rows as $row )
	{
		$s = ''; $r = '';
		
		$r = get_links_from_url( $row->Link );
		
		$d = new dbObject( 'SEngineSearch' );
		$d->Link = $row->Link;
		if( !$d->Load() )
		{
			$d->DateCreated = date( 'Y-m-d H:i:s' );
		}
		
		$s = updateSearchEntry( $r, $d );
		
		if( $r == false )
		{
			// Update Link
			$l = new dbObject( 'SEngineLinks' );
			if( $l->Load( $row->ID ) )
			{
				$l->IsBroken = 1;
				$l->DateModified = date( 'Y-m-d H:i:s' );
				$l->Save();
				
				if( $ii >= 10 )
				{
					$lc = "\n";
					$ii = 0;
				}
				else $lc = '';
				
				print ( 'L' . $l->ID . 'D ' . $lc );
				
				$broken++; $ii++;
			}
		}
		else if( $s )
		{	
			// Search Data
			$d->DateModified = date( 'Y-m-d H:i:s' );
			$d->Save();
			
			// Update Link
			$l = new dbObject( 'SEngineLinks' );
			if( $l->Load( $row->ID ) )
			{
				$l->IsBroken = 0;
				$l->IsParsed = 1;
				$l->DateModified = date( 'Y-m-d H:i:s' );
				$l->Save();
				
				if( $ii >= 10 )
				{
					$lc = "\n";
					$ii = 0;
				}
				else $lc = '';
				
				print ( 'L' . $l->ID . 'U ' . $lc );
				
				$parsed++; $ii++;
			}	
		}
		
		/*$o = new dbObject ( 'SearchBank' );
		if ( $o->load ( $row->ID ) )
		{
			$o->Parsed = '1';
			$o->Date = date ( 'Y-m-d H:i:s' );
			$o->save ();
			$cnt = getContentFast ( $row->Link );
			print ( "Trying {$row->Link}\n" );
			if ( preg_match_all ( '/\<a.*?href\=\"([^>"]*?)\"/i', $cnt, $matches ) )
			{
				foreach ( $matches[1] as $match )
				{
					if ( substr ( $match, 0, 7 ) != 'http://' ) continue;
					if ( substr ( $match, -1, 1 ) != '/' && !strstr ( $match, '.php' ) && !strstr ( $match, '.html' ) && !strstr ( $match, '.asp' ) )
					{
						continue;
					}
					// Add new link
					$d = new dbObject ( 'SearchBank' );
					$d->Link = trim ( $match );
					if ( !$d->Load () )
					{
						$d->Date = date ( 'Y-m-d H:i:s' );
						$d->Save ();
					}
					$links++;
				}
			}
		}*/	
		$i++;
	}
}

$time_end = microtime_float();

print ( "\n\n" . 'Total of ' . $i . ' links ' . $broken . ' is broken and ' . $parsed . ' are parsed in (' . number_format( ( $time = $time_end - $time_start ), 2, '.', '' ) . ' seconds)' . "\n" );

$database->close ();
print ( "\nDone\n" );

$log = fopen ( 'engine.log', 'a+' );
fwrite ( $log, date ( 'd/m/Y H:i:s' ) . ' - Processed - Total of ' . $i . ' links ' . $broken . ' is broken and ' . $parsed . ' are parsed in (' . number_format( ( $time = $time_end - $time_start ), 2, '.', '' ) . ' seconds)' . "\n" );
fclose ( $log );

?>
