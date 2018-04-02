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
include_once ( "$root/subether/classes/library.class.php" );

if( $_POST['src'] && defined( 'BASE_DIR' ) )
{
	$fnm = explode( '/', $_POST['src'] );
	
	// TODO: Check all required parents folders write rights before starting download
	
	if( end( $fnm ) )
	{
		$dst = BASE_DIR . '/subether/upload/releases/' . end( $fnm );
		
		if( !file_exists( $dst ) )
		{
			$lib = new Library();
			if( $lib->CopyFile( $_POST['src'], $dst ) )
			{
				switch( $_POST['type'] )
				{
					case 'arena':
						// If there exists a arena folder in temp erase it
						if( file_exists( BASE_DIR . '/subether/upload/temp/arena' ) )
						{
							$lib->DeleteFolder( BASE_DIR . '/subether/upload/temp/', 'arena' );
						}
						// Unzip new version to /upload/temp/arena
						if( $lib->UnZip( $dst, ( BASE_DIR . '/subether/upload/temp' ) ) )
						{
							die( 'ok<!--separate-->success' );
						}
						break;
					
					default:
						// If there exists a treeroot folder in temp erase it
						if( file_exists( BASE_DIR . '/subether/upload/temp/treeroot' ) )
						{
							$lib->DeleteFolder( BASE_DIR . '/subether/upload/temp/', 'treeroot' );
						}
						// Unzip new version to /upload/temp/treeroot
						if( $lib->UnZip( $dst, ( BASE_DIR . '/subether/upload/temp' ) ) )
						{
							die( 'ok<!--separate-->success' );
						}
						break;
				}
			}
		}
	}
}

die( 'fail' );

?>
