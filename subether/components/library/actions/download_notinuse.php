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

include_once ( 'subether/classes/library.class.php' );

// TODO: Add support for access on downloading files based on who can be downloaded only logged in and what is public
if( $_REQUEST['fid'] > 0 && $_REQUEST['type'] )
{
	switch( strtolower( $_REQUEST['type'] ) )
	{
		case 'image':
			$q = '
				SELECT 
					im.*, 
					im.Access AS FileAccess, 
					fl.DiskPath AS FolderPath, 
					fl.Access AS FolderAccess 
				FROM 
					Image im, 
					Folder fl 
				WHERE 
						im.ImageFolder = fl.ID 
					AND im.ID = \'' . $_REQUEST['fid'] . '\' 
			';
			break;
		
		default:
			$q = '
				SELECT 
					fi.*, 
					fi.Access AS FileAccess, 
					fl.DiskPath AS FolderPath, 
					fl.Access AS FolderAccess 
				FROM 
					File fi, 
					Folder fl 
				WHERE 
						fi.FileFolder = fl.ID 
					AND fi.ID = \'' . $_REQUEST['fid'] . '\' 
			';
			break;
	}
	
	if( $file = $database->fetchObjectRow( $q, false, 'components/library/actions/download.php' ) )
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
		
		$filePath = ( BASE_DIR . '/' . $file->FolderPath . $file->Filename );
		
		$lib = new Library();
		$mimetype = $lib->MimeType( $file->Filename, ( BASE_DIR . '/' . $file->FolderPath ) );
		
		//die( $mimetype . ' -- ' . basename( $filePath ) . ' -- ' . filesize( $filePath ) );
		
		if ( file_exists( $filePath ) )
		{
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: ' . ( $mimetype ? $mimetype : 'application/octet-stream' ) . '' );
			//header( 'Content-Type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename="' . basename( $filePath ) . '"' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate' );
			header( 'Pragma: public' );
			header( 'Content-Length: ' . filesize( $filePath ) );
			readfile( $filePath );
			exit();
		}
	}
}
header( "HTTP/1.0 404 Not Found" );
die( '404 Not Found' );

?>
