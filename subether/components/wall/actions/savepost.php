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

global $webuser;

$found = false;

$m = new dbObject( 'SBookMessage' );

switch ( $_POST['Type'] )
{
	// --- Handle posts -------------------------------------------------------------------------------------------------
	
	case 'post':
		if ( !$_POST['ParentID'] || $_POST['sid'] > 0 )
		{
			// Load and save ---
			if( $_POST['sid'] > 0 && $m->Load( $_POST['sid'] ) )
			{
				$m->DateModified = date ( 'Y-m-d H:i:s' );
			}
			// Create new
			else
			{
				$m->UniqueID = UniqueKey();
				$m->Date = date ( 'Y-m-d H:i:s' );
				$m->DateModified = $m->Date;
				$m->Type = 'post';
				$m->CategoryID = ( $_POST['CategoryID'] ? $_POST['CategoryID'] : $parent->folder->CategoryID );
				$m->SenderID = $webuser->ContactID;
				$m->Access = ( $_POST['Access'] ? $_POST['Access'] : 0 );
				$m->ReceiverID = ( strtolower( $parent->folder->MainName ) == 'profile' ? $parent->cuser->ContactID : 0 );
				//$m->ThreadID = ( $_POST['ThreadID'] ? $_POST['ThreadID'] : 0 );
			}
			
			if( isset( $_POST['Message'] ) && trim( $_POST['Message'] ) )
			{
				$m->Message = sanitizeText( $_POST['Message'] );
				$m->Message = str_replace ( '<!--separate-->', '', $m->Message );
				$m->Tags = strtolower( str_replace( '#', '', implode( ',', gethashtags( $m->Message ) ) ) );
			}
			
			$found = true;
		}
		break;
	
	// --- Handle events -----------------------------------------------------------------------------------------------
	
	case 'event':
		if ( $_POST['ThreadID'] > 0 || $_POST['sid'] > 0 )
		{
			// Load and save ---
			if( $_POST['sid'] > 0 && $m->Load( $_POST['sid'] ) )
			{
				$m->DateModified = date ( 'Y-m-d H:i:s' );
			}
			// Create new
			else
			{
				$m->UniqueID = UniqueKey();
				$m->Date = date ( 'Y-m-d H:i:s' );
				$m->DateModified = $m->Date;
				$m->Type = 'event';
				$m->CategoryID = ( $_REQUEST['categoryid'] ? $_REQUEST['categoryid'] : $parent->folder->CategoryID );
				$m->SenderID = $webuser->ContactID;
				$m->Access = ( $_POST['Access'] ? $_POST['Access'] : 0 );
				//$m->ReceiverID = ( strtolower( $parent->folder->MainName ) == 'profile' ? $parent->cuser->ContactID : 0 );
				$m->ReceiverID = 0;
				$m->ThreadID = ( $_POST['ThreadID'] ? $_POST['ThreadID'] : 0 );
				$m->ParentID = 0;
			}
			
			if( isset( $_POST['Message'] ) && trim( $_POST['Message'] ) )
			{
				$m->Message = sanitizeText( $_POST['Message'] );
				$m->Message = str_replace ( '<!--separate-->', '', $m->Message );
				$m->Tags = strtolower( str_replace( '#', '', implode( ',', gethashtags( $m->Message ) ) ) );
			}
			
			$found = true;
		}
		break;
	
	// --- Handle comment ------------------------------------------------------------------------------------------------
	
	case 'comment':
		if ( $_POST['ParentID'] > 0 || $_POST['sid'] > 0 )
		{
			// Load and save ---
			if( $_POST['sid'] > 0 && $m->Load( $_POST['sid'] ) )
			{
				$m->DateModified = date ( 'Y-m-d H:i:s' );
			}
			// Create new
			else
			{
				$m->UniqueID = UniqueKey();
				$m->Date = date ( 'Y-m-d H:i:s' );
				$m->DateModified = $m->Date;
				$m->Type = 'comment';
				$m->CategoryID = $parent->folder->CategoryID;
				$m->SenderID = $webuser->ContactID;
				$m->Access = ( $_POST['Access'] ? $_POST['Access'] : 0 );
				//$m->ReceiverID = ( strtolower( $parent->folder->MainName ) == 'profile' ? $parent->cuser->ContactID : 0 );
				$m->ReceiverID = 0;
				//$m->ThreadID = ( $_POST['ThreadID'] ? $_POST['ThreadID'] : 0 );
				$m->ParentID = ( $_POST['ParentID'] ? $_POST['ParentID'] : 0 );
			}
			
			if( isset( $_POST['Message'] ) && trim( $_POST['Message'] ) )
			{
				$m->Message = sanitizeText( $_POST['Message'] );
				$m->Message = str_replace ( '<!--separate-->', '', $m->Message );
				$m->Tags = strtolower( str_replace( '#', '', implode( ',', gethashtags( $m->Message ) ) ) );
			}
			
			if( file_exists( $cbase . '/functions/parse.php' ) )
			{
				include_once ( $cbase . '/functions/parse.php' );
				
				// Update modified date on parent
				$c = new dbObject( 'SBookMessage' );
				if( $c->Load( $_POST['ParentID'] ) )
				{
					$c->DateModified = date ( 'Y-m-d H:i:s' );
					$c->Save();
				}
			}
			
			$found = true;
		}
		break;
}

// Handle parsed data ---------------------------------------------------------------------------------------------

if ( !$_POST['sid'] && $found )
{
	// Data set
	if( isset( $_POST['Data'] ) )
	{
		$json = $_POST['Data'];
		$data = json_obj_decode( $json );
		$error = json_last_error_msg();
	}
	//if( $webuser->ID == 81 ) die( print_r( $data,1 ) . ' --' );
	if( $data && is_array( $data ) )
	{
		$meta = new stdClass();
		$meta->LibraryFiles = $data;
	}
	else if( $data && is_object( $data ) )
	{
		$meta = $data;
	}
	else
	{
		$meta = new stdClass();
	}
	
	// Message set
	if( $_POST['Message'] )
	{
		$meta->Message = htmlentities( sanitizeText( $_POST['Message'] ) );
		
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
	$meta->WallData->Date = $m->Date;
	$meta->WallData->DateModified = $m->DateModified;
	$meta->WallData->Type = $m->Type;
	$meta->WallData->CategoryID = $m->CategoryID;
	$meta->WallData->SenderID = $m->SenderID;
	$meta->WallData->Tags = $m->Tags;
	$meta->WallData->Access = $m->Access;
	$meta->WallData->ReceiverID = $m->ReceiverID;
	$meta->WallData->ThreadID = $m->ThreadID;
	$meta->WallData->ParentID = $m->ParentID;
	
	
	
	// If this is a event or an post, comment
	// TODO: Fix this, it's not finished, not working properly either
	if( in_array( $_POST['Type'], array( 'post', 'comment', 'event' ) ) )
	{
		// Create file
		$lib = new Library ();
		$lib->FolderAccess = 0;
		$lib->FileAccess = $m->Access;
		if( strtolower( $parent->folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = ( $_POST['CategoryID'] ? $_POST['CategoryID'] : $parent->folder->CategoryID );
		}
		$lib->ParentFolder = 'Library';
		$lib->FolderName = ( $_POST['Type'] == 'event' ? 'Events' : 'Posts' );
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
					$mov->FolderAccess = 0;
					$mov->FileAccess = $m->Access;
					
					if( strtolower( $parent->folder->MainName ) != 'profile' )
					{
						$mov->CategoryID = ( $_POST['CategoryID'] ? $_POST['CategoryID'] : $parent->folder->CategoryID );
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
	}
	
	$m->Data = json_obj_encode( $meta );
	//$m->Data = addslashes( $m->Data );
	//$m->Data = stripslashes( $m->Data );
	//if( $webuser->ID == 81 ) die( print_r( json_obj_decode( $m->Data, true ),1 ) . ' --' );
}

// Output ----------------------------------------------------------------------------------------------------------------

if ( $found )
{
	// Save
	$m->Save();
	
	if( !$_POST['sid'] )
	{
		LogStats( 'wall', 'save', $m->Type, $m->SenderID, $m->ReceiverID, $m->CategoryID );
	}
	
	die( 'ok<!--separate-->' . $m->ID );
}

die( 'fail' );

?>
