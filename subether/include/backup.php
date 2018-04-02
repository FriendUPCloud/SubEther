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

if( defined( 'BASE_DIR' ) && defined( 'BASE_URL' ) )
{
	$backup = new Library();
	
	// Backup all data -------------------------------------------------------------------------------------------------
	
	//$backup->RSync( ( BASE_DIR . '/admin' ), ( BASE_DIR . '/backup/subether_backup/admin' ) );
	//$backup->RSync( ( BASE_DIR . '/extensions' ), ( BASE_DIR . '/backup/subether_backup/extensions' ) );
	//$backup->RSync( ( BASE_DIR . '/lib' ), ( BASE_DIR . '/backup/subether_backup/lib' ) );
	//$backup->RSync( ( BASE_DIR . '/subether' ), ( BASE_DIR . '/backup/subether_backup/subether' ) );
	//$backup->RSync( ( BASE_DIR . '/upload' ), ( BASE_DIR . '/backup/subether_backup/upload' ) );
	//$backup->RSync( ( BASE_DIR . '/web' ), ( BASE_DIR . '/backup/subether_backup/web' ) );
	//$backup->RSync( ( BASE_DIR . '/admin.php' ), ( BASE_DIR . '/backup/subether_backup/admin.php' ) );
	//$backup->RSync( ( BASE_DIR . '/favicon.ico' ), ( BASE_DIR . '/backup/subether_backup/favicon.ico' ) );
	//$backup->RSync( ( BASE_DIR . '/index.php' ), ( BASE_DIR . '/backup/subether_backup/index.php' ) );
	//$backup->RSync( ( BASE_DIR . '/resources.php' ), ( BASE_DIR . '/backup/subether_backup/resources.php' ) );
	
	// Remove vital files like configs ---------------------------------------------------------------------------------
	
	//$backup->DeleteFile( BASE_DIR . '/backup/subether_backup/lib/', 'core_config.php' );
	
	// Make a zip file of all data -------------------------------------------------------------------------------------
	
	//$backup->Zip( BASE_DIR, BASE_DIR, ( 'backup/subether_backup/subether_backup.zip' ) );
	
	//$backup->Zip( BASE_DIR, ( BASE_DIR . '/subether' ), ( 'backup/subether_backup/subether_backup.zip' ) );
	
	$backup->MySqlExport( BASE_DIR, ( '/backup/database_backup/' . date( 'Ymd' ) . '.sql' ) );
	
	// If success send the download link to client for download --------------------------------------------------------
	
	/*if( file_exists( BASE_DIR . '/backup/subether_backup/subether_backup.zip' ) )
	{
		die( 'ok<!--separate-->' . BASE_URL . 'backup/subether_backup/subether_backup.zip' );
	}*/
	
	if( file_exists( BASE_DIR . '/backup/database_backup/' . date( 'Ymd' ) . '.sql' ) )
	{
		die( 'ok<!--separate-->' . BASE_URL . 'backup/database_backup/' . date( 'Ymd' ) . '.sql' );
	}
}

die( 'fail' );

?>
