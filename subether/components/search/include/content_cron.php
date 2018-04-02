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

print ( "\n" . 'Storing Content!' . "\n\n" );

$i = 0; $ii = 0; $broken = 0; $stored = 0;

if ( $rows = $database->fetchObjectRows ( 'SELECT * FROM SEngineLinks WHERE IsParsed = "1" AND IsBroken = "0" AND IsStored = "0" ORDER BY ID ASC LIMIT 100' ) )
{
	foreach ( $rows as $row )
	{
		$r = getContentFast ( $row->Link );
		
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
				
				print ( 'SL' . $l->ID . 'DL ' . $lc );
				
				$broken++; $ii++;
			}
		}
		else if( $r != false )
		{
			// Content
			$c = new dbObject( 'SEngineContent' );
			$c->Link = $d->Link;
			if( !$c->Load() )
			{
				$c->DateCreated = date( 'Y-m-d H:i:s' );
			}
			$c->Content = trim( $r );
			$c->DateModified = date( 'Y-m-d H:i:s' );
			$c->Save();
			
			// Search Data
			$d = new dbObject( 'SEngineSearch' );
			$d->Link = $row->Link;
			if( !$d->Load() )
			{
				$d->DateCreated = date( 'Y-m-d H:i:s' );
			}
			$d->ContentID = $c->ID;
			$d->DateModified = date( 'Y-m-d H:i:s' );
			$d->Save();
			
			// Update Link
			$l = new dbObject( 'SEngineLinks' );
			if( $l->Load( $row->ID ) )
			{
				$l->ContentID = $c->ID;
				$l->IsBroken = 0;
				$l->IsStored = 1;
				$l->DateModified = date( 'Y-m-d H:i:s' );
				$l->Save();
				
				if( $ii >= 10 )
				{
					$lc = "\n";
					$ii = 0;
				}
				else $lc = '';
				
				print ( 'SL' . $l->ID . 'UL ' . $lc );
				
				$stored++; $ii++;
			}	
		}
		$i++;
	}
}

$time_end = microtime_float();

print ( "\n\n" . 'Total of ' . $i . ' links ' . $broken . ' is broken and ' . $stored . ' are stored in (' . number_format( ( $time = $time_end - $time_start ), 2, '.', '' ) . ' seconds)' . "\n" );

$database->close ();
print ( "\nDone\n" );

$log = fopen ( 'content.log', 'a+' );
fwrite ( $log, date ( 'd/m/Y H:i:s' ) . ' - Processed - Total of ' . $i . ' links ' . $broken . ' is broken and ' . $stored . ' are stored in (' . number_format( ( $time = $time_end - $time_start ), 2, '.', '' ) . ' seconds)' . "\n" );
fclose ( $log );

?>
