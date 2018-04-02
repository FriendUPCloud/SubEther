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

if( defined( 'BASE_DIR' ) && ( $job = $database->fetchObjectRow( '
	SELECT
		*
	FROM
		SBookCronJobs 
	WHERE
			Filename = "maintenance_cron.php"
		AND IsActive = "1" 
', false, 'restapi/cron/maintenance_cron.php' ) ) )
{
	//sleep(60);
	sleep(15);
	
	// Take backup of current core ---------------------------------------------------------------------------------------------
	
	// TODO: Check all require parent folders write rights before starting maintenance
	
	switch( $job->Type )
	{
		case 'arena':
			if( file_exists( BASE_DIR . '/subether/upload/temp/arena' ) )
			{
				$backup = new Library();
				$backup->Zip( BASE_DIR, ( BASE_DIR . '/subether/upload/backup/latest_backup' ), ( '/subether/upload/backup/' . date( 'Ymd' ) . '.zip' ) );
				$backup->MySqlExport( BASE_DIR, ( '/subether/upload/backup/latest_backup/' . date( 'Ymd' ) . '.sql' ) );
				
				$backup->RSync( ( BASE_DIR . '/subether/upload/temp/arena/admin' ), ( BASE_DIR . '/subether/upload/backup/latest_backup/admin' ) );
				$backup->RSync( ( BASE_DIR . '/subether/upload/temp/arena/extensions' ), ( BASE_DIR . '/subether/upload/backup/latest_backup/extensions' ) );
				$backup->RSync( ( BASE_DIR . '/subether/upload/temp/arena/friend' ), ( BASE_DIR . '/subether/upload/backup/latest_backup/friend' ) );
				$backup->RSync( ( BASE_DIR . '/subether/upload/temp/arena/lib' ), ( BASE_DIR . '/subether/upload/backup/latest_backup/lib' ) );
				$backup->RSync( ( BASE_DIR . '/subether/upload/temp/arena/web' ), ( BASE_DIR . '/subether/upload/backup/latest_backup/web' ) );
				
				// Remove old backups so it doesn't stack up, limit of backups in compressed format is 2 -----------------------------------
				
				$clean = new Library();
				if( $files = $clean->OpenFolder( BASE_DIR . '/subether/upload/', 'backup/' ) )
				{
					$i = 0;
					
					rsort( $files );
					
					foreach( $files as $file )
					{
						if( isset( $file->name ) && is_file( $file->dir . $file->name ) && strstr( $file->name, '.' ) )
						{
							if( $i >= 2 )
							{
								$clean->DeleteFile( $file->dir, $file->name );
							}
							
							$i++;
						}
					}
				}
				
				// Update current core with new version ----------------------------------------------------------------------------------
				
				$update = new Library();
				$list = $update->RSync( ( BASE_DIR . '/subether/upload/temp/arena/admin' ), ( BASE_DIR . '/admin' ) );
				$list = $update->RSync( ( BASE_DIR . '/subether/upload/temp/arena/extensions' ), ( BASE_DIR . '/extensions' ), false, $list );
				$list = $update->RSync( ( BASE_DIR . '/subether/upload/temp/arena/friend' ), ( BASE_DIR . '/friend' ), false, $list );
				$list = $update->RSync( ( BASE_DIR . '/subether/upload/temp/arena/lib' ), ( BASE_DIR . '/lib' ), false, $list );
				$list = $update->RSync( ( BASE_DIR . '/subether/upload/temp/arena/web' ), ( BASE_DIR . '/web' ), false, $list );
				
				if( $list && is_array( $list ) )
				{
					$updated = '';
					
					foreach( $list as $l )
					{
						$updated .= str_replace( BASE_DIR, '', $l ) . "\n";
					}
					
					$file = new Library();
					$file->SaveToFile( ( BASE_DIR . '/subether/upload/' ), 'arena.txt', $updated );
				}
				
			}
			break;
		
		default:
			if( file_exists( BASE_DIR . '/subether/upload/temp/treeroot' ) )
			{
				$backup = new Library();
				$backup->Zip( BASE_DIR, ( BASE_DIR . '/subether/upload/backup/latest_backup' ), ( '/subether/upload/backup/' . date( 'Ymd' ) . '.zip' ) );
				$backup->MySqlExport( BASE_DIR, ( '/subether/upload/backup/latest_backup/' . date( 'Ymd' ) . '.sql' ) );
				
				$backup->RSync( ( BASE_DIR . '/subether/upload/temp/treeroot/subether' ), ( BASE_DIR . '/subether/upload/backup/latest_backup/subether' ) );
				$backup->RSync( ( BASE_DIR . '/subether/upload/temp/treeroot/extensions/sbook' ), ( BASE_DIR . '/subether/upload/backup/latest_backup/extensions/sbook' ) );
				$backup->RSync( ( BASE_DIR . '/subether/upload/temp/treeroot/extensions/templates' ), ( BASE_DIR . '/subether/upload/backup/latest_backup/extensions/templates' ) );
				
				// Remove old backups so it doesn't stack up, limit of backups in compressed format is 2 -----------------------------------
				
				$clean = new Library();
				if( $files = $clean->OpenFolder( BASE_DIR . '/subether/upload/', 'backup/' ) )
				{
					$i = 0;
					
					rsort( $files );
					
					foreach( $files as $file )
					{
						if( isset( $file->name ) && is_file( $file->dir . $file->name ) && strstr( $file->name, '.' ) )
						{
							if( $i >= 2 )
							{
								$clean->DeleteFile( $file->dir, $file->name );
							}
							
							$i++;
						}
					}
				}
				
				// Update current core with new version ----------------------------------------------------------------------------------
				
				$update = new Library();
				$list = $update->RSync( ( BASE_DIR . '/subether/upload/temp/treeroot/subether' ), ( BASE_DIR . '/subether' ) );
				$list = $update->RSync( ( BASE_DIR . '/subether/upload/temp/treeroot/extensions/sbook' ), ( BASE_DIR . '/extensions/sbook' ), false, $list );
				$list = $update->RSync( ( BASE_DIR . '/subether/upload/temp/treeroot/extensions/templates' ), ( BASE_DIR . '/extensions/templates' ), false, $list );
				
				if( $list && is_array( $list ) )
				{
					$updated = '';
					
					foreach( $list as $l )
					{
						$updated .= str_replace( BASE_DIR, '', $l ) . "\n";
					}
					
					$file = new Library();
					$file->SaveToFile( ( BASE_DIR . '/subether/upload/' ), 'treeroot.txt', $updated );
				}
				
			}
			break;
	}
	
	// Stop maintenance job ---------------------------------------------------------------------------------------------------
	
	$stop = new dbObject( 'SBookCronJobs' );
	$stop->ID = $job->ID;
	if( $stop->Load() )
	{
		$stop->IsActive = 0;
		$stop->Save();
	}
}

?>
