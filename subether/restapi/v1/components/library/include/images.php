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

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include_once ( 'subether/functions/globalfuncs.php' );
include_once ( 'subether/functions/componentfuncs.php' );
include_once ( 'subether/functions/userfuncs.php' );

// TODO: Make it possible to return thumb picture based on resolution from header

// Find fileid in database based on sha256 id string

if ( preg_match ( '/\/secure-files\/images\/([a-z0-9]*?)\/.*/i', $_SERVER['REQUEST_URI'], $uniqueid ) )
{
	// If we found uniqueid continue
	
	// TODO: Add cache possibility to not check everything in the database, maybe on the query
	
	$data = explode( '/', $_SERVER['REQUEST_URI'] );
	
	preg_match ( '/\/secure-files\/images\/[a-z0-9]*?\/([a-z0-9]*?)\/.*/i', $_SERVER['REQUEST_URI'], $token );
	
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
					im.ID,
					im.UniqueID,
					im.Title,
					im.Filename,
					im.Filetype, 
					im.UserID,
					im.CategoryID,
					im.Filesize,
					im.Width,
					im.Height,
					im.ImageFolder AS FolderID,
					im.Access AS FileAccess,
					fl.Access AS FolderAccess,
					fl.Name AS FolderName, 
					fl.DiskPath AS FolderPath, 
					ca.Name AS CategoryName,
					ca.Privacy AS CategoryPrivacy,
					ca.IsSystem AS CategorySystem, 
					"image" AS MediaType 
				FROM
					Folder fl, 
					Image im 
						LEFT JOIN SBookCategory ca ON
						(
							im.CategoryID = ca.ID
						)
				WHERE
						im.NodeID = "0" 
					AND im.ImageFolder = fl.ID ' 
					. ( strlen( $unid ) == 64 ? ' 
					AND im.UniqueID != "" 
					AND im.UniqueID = \'' . $unid . '\' ' 
					: '
					AND im.ID = \'' . $unid . '\' ' 
					) . '
					AND im.Access != "2" 
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
					im.ID,
					im.UniqueID,
					im.Title,
					im.Filename,
					im.Filetype, 
					im.UserID,
					im.CategoryID,
					im.Filesize,
					im.Width,
					im.Height,
					im.ImageFolder AS FolderID,
					im.Access AS FileAccess,
					fl.Access AS FolderAccess,
					fl.Name AS FolderName, 
					fl.DiskPath AS FolderPath, 
					ca.Name AS CategoryName,
					ca.Privacy AS CategoryPrivacy,
					ca.IsSystem AS CategorySystem, 
					"image" AS MediaType 
				FROM
					Folder fl, 
					Image im 
						LEFT JOIN SBookCategory ca ON
						(
							im.CategoryID = ca.ID
						)
				WHERE 
						im.NodeID = "0" 
					AND im.ImageFolder = fl.ID ' 
					. ( strlen( $unid ) == 64 ? ' 
					AND im.UniqueID != "" 
					AND im.UniqueID = \'' . $unid . '\' ' 
					: '
					AND im.ID = \'' . $unid . '\' ' 
					) . '
					AND 
					(
						
						/* --- Public / Members Access --- */
						
						(
							(
									im.Access = "0" 
								AND fl.Access <= im.Access 
							)
							AND
							(
								
								/* --- Profile / Other Access --- */
								
								(
									im.CategoryID = "0"
								)
								OR
								(
										im.CategoryID > 0
									AND ca.IsSystem = "1" 
								)
								
								/* --- Group Access --- */
								
								OR 
								(
										im.CategoryID > 0
									AND ca.Privacy = "OpenGroup" 
								)
								OR 
								(
										im.CategoryID > 0
									AND ca.Privacy = "ClosedGroup" 
									AND fl.Name = "Cover Photos" 
								)
								OR
								(
										im.CategoryID > 0 
									AND im.CategoryID IN ( !UserCats! ) 
								)
							)
						)
						
						/* --- Contact Access --- */
						
						OR
						(
								im.Access = "1"
							AND im.UserID > 0 
							AND im.UserID IN ( !UserIDS! )
							AND fl.Access <= im.Access 
						)
						
						/* --- File Owner Access --- */
						
						OR
						(
								im.UserID = !UserID!
							AND im.UserID > 0 
						)
						
						/* --- Admin Access --- */
						
						OR
						(
								im.Access != "2" 
							AND im.CategoryID > 0 
							AND im.CategoryID IN ( !AdminCats! ) 
						)
						
						/* --- No Owner / All Access --- */
						
						OR
						(
								im.UserID = "0" 
							AND im.CategoryID = "0" 
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
		
		if ( $file = $database->fetchObjectRow( $fq, false, 'subether/restapi/components/library/include/images.php' ) )
		{
			$filePath = ( ( $file->FolderPath != '' ? ( BASE_DIR . '/' . $file->FolderPath ) : ( BASE_DIR . '/upload/images-master/' ) ) . $file->Filename );
			
			$lib = new Library();
			$mimetype = $lib->MimeType( $file->Filename, ( BASE_DIR . '/' . $file->FolderPath ) );
			
			//if( $webuser->ID == 103 ) die( $fq . ' -- ' . print_r( $file,1 ) . ' [] ' );
			
			if ( file_exists( $filePath ) )
			{
				ob_clean();
				
				header( 'Content-Type: ' . ( $mimetype ? $mimetype : 'application/octet-stream' ) . '' );
				
				if ( $tokn || ( !$token && $filename ) )
				{
					header( 'Content-Description: File Transfer' );
					header( 'Content-Disposition: attachment; filename="' . $file->Filename . '"' );
					header( 'Expires: 0' );
					header( 'Content-Length: ' . filesize( $filePath ) );
				}
				else
				{
					header( 'Expires: ' . gmdate( 'D, d M Y H:i:s \G\M\T', time() + 86400 ) );
					
					// Generate some useful time vars based on file date
					$last_modified_time = filemtime( $filePath ); 
					$etag = md5_file( $filePath );
					
					// Always send headers
					header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $last_modified_time ) . ' GMT' ); 
					header( 'Etag: ' . $etag ); 
					
					// Exit if not modified
					if ( ( @strtotime( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) == $last_modified_time ) ||  ( @trim( $_SERVER['HTTP_IF_NONE_MATCH'] ) == $etag ) )
					{
						header( 'HTTP/1.1 304 Not Modified' ); 
						die();
					}
				}
				
				header( 'Cache-Control: max-age=86400' );
				header( 'Pragma: public' );
				
				die( readfile( $filePath ) );
			}
		}
		
		//if( $webuser->ID == 103 ) die( $fq . ' -- ' . print_r( $file,1 ) . ' [] ' );
	}
}

if ( file_exists( BASE_DIR . '/subether/gfx/404.png' ) )
{
	header( 'Content-Type: image/png' );
	die( readfile( BASE_DIR . '/subether/gfx/404.png' ) );
}

header( "HTTP/1.0 404 Not Found" );
die( '404 Not Found' );

throwXmlError ( MISSING_PARAMETERS );

?>
