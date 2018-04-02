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

if( $parent && ( ( isset( $_POST['Message'] ) && trim( $_POST['Message'] ) ) || trim( $_POST['Data'] ) ) )
{
	$sm = new dbObject( 'SBookMessage' );
	if ( $_POST['sid'] && $sm->Load( $_POST['sid'] ) )
	{
		$sm->DateModified = date ( 'Y-m-d H:i:s' );
	}
	else 
	{
		$sm->Date = date ( 'Y-m-d H:i:s' );
		$sm->DateModified = $sm->Date;
		$sm->Type = 'post';
		$sm->CategoryID = $parent->folder->CategoryID;
		$sm->SenderID = $parent->webuser->ContactID;
	}
	if( isset( $_POST['Message'] ) && trim( $_POST['Message'] ) )
	{
		$sm->Message = sanitizeText( $_POST['Message'] );
		$sm->Message = str_replace ( '<!--separate-->', '', $sm->Message );
		$sm->Tags = strtolower( str_replace( '#', '', implode( ',', gethashtags( $sm->Message ) ) ) );
	}
	else
	{
		$sm->Message = '';
		$sm->Tags = '';
	}
	if( isset( $_POST['Access'] ) )
	{
		$sm->Access = ( $_POST['Access'] ? $_POST['Access'] : 0 );
	}
	if( strtolower( $parent->folder->MainName ) == 'profile' )
	{
		$sm->ReceiverID = $parent->cuser->ContactID;
	}
	if ( $_REQUEST[ 'ThreadID' ] )
	{
		$sm->ThreadID = $_REQUEST[ 'ThreadID' ];
	}
	if ( $_REQUEST[ 'ParentID' ] )
	{
		include_once ( $cbase . '/functions/parse.php' );
		$sm->ParentID = $_REQUEST[ 'ParentID' ];
		$sm->Type = 'comment';
		
		// Update modified date on parent
		$sc = new dbObject( 'SBookMessage' );
		if( $sc->Load( $_REQUEST[ 'ParentID' ] ) )
		{
			$sc->DateModified = date ( 'Y-m-d H:i:s' );
			$sc->Save();
		}
	}
	
	// Parser ------------------------------------------------------------------
	if( isset( $_POST[ 'Data' ] ) || $data || ( !$_REQUEST['ParentID'] && $_POST['Message'] ) )
	{
		if( isset( $_POST[ 'Data' ] ) )
		{
			// Sanity check and fix
			//$json = trim( stripslashes( $_POST['Data'] ) );
			//$json = trim( str_replace( '\\', '', stripslashes( $_POST[ 'Data' ] ) ) );
			$json = $_POST['Data'];
			$data = json_obj_decode( $json );
			$error = json_last_error_msg();
			
			//die( $json . ' -- ' . $error . ' .. ' . print_r( $data,1 ) . ' .. ' . $_POST[ 'Data' ] );
		}
		if( !$data )
		{
			$data = new stdClass();
		}
		
		if( is_array( $data ) )
		{
			$meta = new stdClass();
			$meta->LibraryFiles = $data;
		}
		else
		{
			$meta = $data;
		}
		
		
		
		if( $_POST['Message'] )
		{
			$meta->Message = $_POST['Message'];
			
			// If title is missing create one from message
			if( !$meta->Title )
			{
				$meta->Title = dot_trim( strip_tags( $meta->Message ), 100, false, false );
			}
			
			// If type is missing set as meta
			if( !$meta->Type )
			{
				$meta->Type = 'meta';
			}
			
			// If type is missing set as meta
			if( !$meta->Media )
			{
				$meta->Media = 'meta';
			}
		}
		
		// Save as meta data
		$meta->WallData = new stdClass();
		$meta->WallData->Date = $sm->Date;
		$meta->WallData->DateModified = $sm->DateModified;
		$meta->WallData->Type = $sm->Type;
		$meta->WallData->CategoryID = $sm->CategoryID;
		$meta->WallData->SenderID = $sm->SenderID;
		$meta->WallData->Tags = $sm->Tags;
		$meta->WallData->Access = $sm->Access;
		$meta->WallData->ReceiverID = $sm->ReceiverID;
		$meta->WallData->ThreadID = $sm->ThreadID;
		$meta->WallData->ParentID = $sm->ParentID;
		
		// Create file
		$lib = new Library ();
		if( strtolower( $parent->folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $parent->folder->CategoryID;
		}
		//$lib->ParentFolder = 'Album';
		//$lib->FolderName = 'Other';
		$lib->ParentFolder = 'Library';
		$lib->FolderName = 'Posts';
		$lib->SaveParsedData( $meta );
		
		if( $lib->FileID > 0 )
		{
			// Save json Data to sql row
			$meta->FileID = $lib->FileID;
			$meta->MediaType = $lib->MediaType;
			$meta->Filename = $lib->Filename;
			$meta->FilePath = $lib->FilePath;
			$meta->FileWidth = $lib->FileWidth;
			$meta->FileHeight = $lib->FileHeight;
			$meta->FolderPath = $lib->FolderPath;
		}
		
		// Move from unsorted to correct folders, TODO: make this method simpler, and make sure it works all other places the change is affected ...
		if( $meta->LibraryFiles && is_array( $meta->LibraryFiles ) )
		{
			foreach( $meta->LibraryFiles as $fls )
			{
				// If image file store as image if file store as file
				$mov = new Library ( ( ( $fls->FileWidth > 0 && $fls->FileHeight > 0 ) ? 'Image' : 'File' ) );
				if( $mov->Load( $fls->FileID ) )
				{
					if( strtolower( $parent->folder->MainName ) != 'profile' )
					{
						$mov->CategoryID = $parent->folder->CategoryID;
					}
					
					// TODO create a move file to new folder function for this
					
					// If Image
					if( $fls->FileWidth > 0 && $fls->FileHeight > 0 )
					{
						$mov->ParentFolder = 'Album';
						$mov->FolderName = 'Wall';
					}
					// Else file
					else
					{
						$mov->ParentFolder = 'Library';
						$mov->FolderName = 'Wall';
					}
					
					$mov->Save();
				}
			}
		}
		
		$sm->Data = mysql_real_escape_string( json_obj_encode( $meta ) );
		//$sm->Data = json_obj_encode( $meta );
		
		//$sm->Data = trim( str_replace( '\\', '', stripslashes( $sm->Data ) ) );
		
		//$data = json_obj_decode( $sm->Data );
		//$error = json_last_error_msg();
		//die( $sm->Data . ' -- ' . $error );
	}
	
	$sm->Save ();
	
	if ( !$sm->ThreadID ) 
	{ 
		$sm->ThreadID = $sm->ID; 
		$sm->Save (); 
	}
	die ( $sm->ID > 0 ? ( 'ok<!--separate-->' . $sm->ID ) : 'fail<!--separate-->0' );
}
die( 'fail' );

?>
