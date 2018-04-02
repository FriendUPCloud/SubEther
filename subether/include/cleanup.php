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

if ( !$_SESSION[ 'cachecleanup' ] )
{
	$_SESSION[ 'cachecleanup' ] = 1; $cleanup = false;
	
	if ( $db = $database->fetchObjectRows ( '
		SELECT
			r.ID 
		FROM 
			ContentRoute r LEFT JOIN Image i ON ( i.ID = r.ElementID ) 
		WHERE 
			r.ElementType = "Image" 
			AND i.ID IS NULL 
		LIMIT 100 
	') )
	{
		if( !$cleanup ) $cleanup = array();
		
		$i = 0;
		
		foreach( $db as $d )
		{
			$r = new dbObject( 'ContentRoute' );
			if( $r->Load( $d->ID ) && $r->Route != '' )
			{
				if( file_exists( $r->Route ) )
				{
					unlink( $r->Route );
					$i++;
				}
				$r->Delete();
			}
		}
		
		$cleanup['images-cache']['Total'] = $i;
	}
	
	if( $cleanup )
	{
		file_put_contents('cleanup.log', "--------------------\n" . print_r( $cleanup, true ) . "\n" . 'Timestamp: ' . date( 'Y-m-d H:i:s' ) . "\n", FILE_APPEND );
	}
}

?>
