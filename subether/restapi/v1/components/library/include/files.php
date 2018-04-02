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

global $database, $webuser;

include_once ( 'subether/functions/globalfuncs.php' );
include_once ( 'subether/functions/componentfuncs.php' );
include_once ( 'subether/functions/userfuncs.php' );

// Find fileid in database based on sha256 id string

// TODO: Add posibility to check if file exists with only header and multiple files at ones

if ( preg_match ( '/\/secure-files\/files\/([a-z0-9]*?)\/.*/i', $_SERVER['REQUEST_URI'], $uniqueid ) )
{
	// If we found uniqueid continue
	
	// TODO: Add cache possibility to not check everything in the database, maybe on the query
	
	$data = explode( '/', $_SERVER['REQUEST_URI'] );
	
	preg_match ( '/\/secure-files\/files\/[a-z0-9]*?\/([a-z0-9]*?)\/.*/i', $_SERVER['REQUEST_URI'], $token );
	
	$unid = ( is_array( $uniqueid ) && isset( $uniqueid[1] ) ? $uniqueid[1] : false );
	$tokn = ( is_array( $token ) && isset( $token[1] ) ? $token[1] : false );
	$sess = ( $tokn ? $tokn : ( $webuser->ID > 0 ? $webuser->GetToken() : false ) );
	
	$filename = ( is_array( $data ) ? end( $data ) : false );
	$filename = ( $filename && strstr( $filename, '.' ) ? $filename : false );
	
	$usr  = verifySessionId( $sess, true );
	$usr  = ( $usr && $usr->ID ? $usr : false );
	
	$sysadmin = IsSystemAdmin( isset( $usr ) && $usr ? $usr->ID : false );
	
	if ( $unid && ComponentAccess( 'library', 'main', false, ( isset( $usr ) && $usr ? ( $sysadmin ? 99 : 1 ) : false ) ) )
	{		
		// TODO: Make this even more secure to check all parent folders and access not just on files and images
		
		// Check if we have webuser object and it is authenticated
		
		if ( $sysadmin )
		{
			$fq = '
				SELECT 
					fi.ID,
					fi.UniqueID,
					fi.Title,
					fi.Filename,
					fi.Filetype, 
					fi.UserID,
					fi.CategoryID,
					fi.Filesize,
					fi.FileFolder AS FolderID,
					fi.Access AS FileAccess,
					fl.Access AS FolderAccess,
					fl.Name AS FolderName, 
					fl.DiskPath AS FolderPath, 
					ca.Name AS CategoryName,
					ca.Privacy AS CategoryPrivacy,
					ca.IsSystem AS CategorySystem, 
					"file" AS MediaType 
				FROM
					Folder fl, 
					File fi 
						LEFT JOIN SBookCategory ca ON
						(
							fi.CategoryID = ca.ID
						)
				WHERE
						fi.NodeID = "0" 
					AND fi.FileFolder = fl.ID ' 
					. ( strlen( $unid ) == 64 ? ' 
					AND fi.UniqueID != "" 
					AND fi.UniqueID = \'' . $unid . '\' ' 
					: '
					AND fi.ID = \'' . $unid . '\' ' 
					) . '
					AND fi.Access != "2" 
			';
		}
		else
		{
			$usrs = ( $usr && isset( $usr->ContactID ) ? getUserContactsID( $usr->ContactID, true ) : false );
			$usrs = ( $usrs && is_array( $usrs ) ? implode( ',', $usrs ) : ( $usr ? $usr->ID : false ) );
			
			$acat = ( $usr && isset( $usr->ContactID ) ? CategoryAccess( $usr->ContactID, false, -1, 'IsAdmin' ) : false );
			$acat = ( $acat && isset( $acat['CategoryID'] ) ? $acat['CategoryID'] : false );
			
			$ucat = ( $usr && isset( $usr->ContactID ) ? CategoryAccess( $usr->ContactID, false, -1 ) : false );
			$ucat = ( $ucat && isset( $ucat['CategoryID'] ) ? $ucat['CategoryID'] : false );
			
			$fq = '
				SELECT 
					fi.ID,
					fi.UniqueID,
					fi.Title,
					fi.Filename,
					fi.Filetype, 
					fi.UserID,
					fi.CategoryID,
					fi.Filesize,
					fi.FileFolder AS FolderID,
					fi.Access AS FileAccess,
					fl.Access AS FolderAccess,
					fl.Name AS FolderName, 
					fl.DiskPath AS FolderPath, 
					ca.Name AS CategoryName,
					ca.Privacy AS CategoryPrivacy,
					ca.IsSystem AS CategorySystem, 
					"file" AS MediaType 
				FROM
					Folder fl, 
					File fi 
						LEFT JOIN SBookCategory ca ON
						(
							fi.CategoryID = ca.ID
						)
				WHERE
						fi.NodeID = "0" 
					AND fi.FileFolder = fl.ID ' 
					. ( strlen( $unid ) == 64 ? ' 
					AND fi.UniqueID != "" 
					AND fi.UniqueID = \'' . $unid . '\' ' 
					: '
					AND fi.ID = \'' . $unid . '\' ' 
					) . '
					AND 
					(
						
						/* --- Public / Members Access --- */
						
						(
							(
									fi.Access = "0"
								AND fl.Access <= fi.Access 
							)
							AND
							(
								
								/* --- Profile / Other Access --- */
								
								(
									fi.CategoryID = "0"
								)
								OR
								(
										fi.CategoryID > 0
									AND ca.IsSystem = "1" 
								)
								
								/* --- Group Access --- */
								
								OR 
								(
										fi.CategoryID > 0
									AND ca.Privacy = "OpenGroup" 
								)
								OR 
								(
										fi.CategoryID > 0
									AND ca.Privacy = "ClosedGroup" 
									AND fl.Name = "Cover Photos" 
								)
								OR
								(
										fi.CategoryID > 0
									AND fi.CategoryID IN ( !UserCats! ) 
								)
							)
						)
						
						/* --- Contact Access --- */
						
						OR
						(
								fi.Access = "1"
							AND fi.UserID > 0 
							AND fi.UserID IN ( !UserIDS! )
							AND fl.Access <= fi.Access 
						)
						
						/* --- File Owner Access --- */
						
						OR
						(
								fi.UserID = !UserID!
							AND fi.UserID > 0 
						)
						
						/* --- Admin Access --- */
						
						OR
						(
								fi.Access != "2"
							AND fi.CategoryID > 0 
							AND fi.CategoryID IN ( !AdminCats! ) 
						)
						
						/* --- No Owner / All Access --- */
						
						OR
						(
								fi.UserID = "0" 
							AND fi.CategoryID = "0" 
						)
						
					) 
			';
			
			// TODO: Add support for admins of groups and members of groups and super admin of the system
			
			$fq = str_replace( '!UserID!', ( isset( $usr ) && $usr ? ( '\'' . $usr->ID . '\'' ) : 'NULL' ), $fq );
			$fq = str_replace( '!UserIDS!', ( isset( $usrs ) && $usrs ? $usrs : 'NULL' ), $fq );
			$fq = str_replace( '!AdminCats!', ( isset( $acat ) && $acat ? $acat : 'NULL' ), $fq );
			$fq = str_replace( '!UserCats!', ( isset( $ucat ) && $ucat ? $ucat : 'NULL' ), $fq );
			
			// TODO: Add support for NULL category on groups and NULL UserID on profile
		}
		
		if ( $file = $database->fetchObjectRow( $fq, false, 'subether/restapi/components/library/include/files.php' ) )
		{
			/*$fullPath = BASE_DIR . '/' . $file->FolderPath . $file->Filename;
			
			if( $fd = fopen( $fullPath, 'rb' ) )
			{
				$fsize = filesize( $fullPath );
				$fname = basename( $fullPath );
				
				//header ( 'Content-type: application/vnd.ms-excel; charset=utf-8' );
				//header ( 'Content-Disposition: download; filename="Hourlist_' . date( 'Ymd' ) . '.xls"' );
				
				header( "Pragma: " );
				header( "Cache-Control: " );
				header( "Content-type: application/octet-stream" );
				header( "Content-Disposition: attachment; filename=\"".$file->Filename."\"" );
				header( "Content-length: $fsize" );
				
				fpassthru( $fd );
				fclose( $fd );
				exit();
			}*/
			
			$lib = new Library();
			$mimetype = $lib->MimeType( $file->Filename, ( BASE_DIR . '/' . $file->FolderPath ) );
			
			// Temporary hack to find thumb images on the disk for videos
			//$filename = ( $filename && strstr( $filename, '.png' ) ? $filename : $file->Filename );
			
			if ( !$filename && !$tokn && strstr( $mimetype, 'video' ) )
			{
				$ext = explode( '.', $file->Filename );
				
				if ( isset( $ext[1] ) )
				{
					$filename = str_replace( $ext[1], 'png', $file->Filename );
				}
			}
			else if ( !$filename )
			{
				$filename = $file->Filename;
			}
			
			//$filePath = ( BASE_DIR . '/' . $file->FolderPath . $file->Filename );
			$filePath = ( ( $file->FolderPath != '' ? ( BASE_DIR . '/' . $file->FolderPath ) : ( BASE_DIR . '/upload/' ) ) . $filename );
			
			if ( file_exists( $filePath ) )
			{
				ob_clean();
				
				header( 'Content-Type: ' . ( $mimetype ? $mimetype : 'application/octet-stream' ) . '' );
				
				if ( $tokn )
				{
					header( 'Content-Description: File Transfer' );
					header( 'Content-Disposition: attachment; filename="' . $file->Filename . '"' );
					header( 'Expires: 0' );
					header( 'Cache-Control: must-revalidate' );
					header( 'Pragma: public' );
					header( 'Content-Length: ' . filesize( $filePath ) );
				}
				
				die( readfile( $filePath ) );
			}
		}
	}
}

header( "HTTP/1.0 404 Not Found" );
die( '404 Not Found' );

throwXmlError ( MISSING_PARAMETERS );

?>
