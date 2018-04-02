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

include_once ( 'subether/functions/globalfuncs.php' );
include_once ( 'subether/functions/userfuncs.php' );

$required = array(
	'SessionID', 'Type', 'Message' 
);

$options = array(
	'CategoryID', 'ReceiverID', 'ParentID',
	'PostID', 'CommentID', 'Access', 'Data' 
);

$json = array(
	'Url', 'Domain', 'Title', 'Leadin', 'Type',
	'Media', 'ImageID', 'Images', 'src',
	'width', 'height', 'type', 'bits', 'mime'
);

// TODO: remove when live or done debug testing
unset( $_REQUEST['route'] );

if ( isset( $_REQUEST ) )
{
	foreach ( $_REQUEST as $k=>$p )
	{
		if ( !in_array( $k, $required ) && !in_array( $k, $options ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	foreach ( $required as $r )
	{
		if ( !isset( $_REQUEST[$r] ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	
	if ( isset( $_REQUEST['Data'] ) )
	{
		$_REQUEST['Data'] = json_obj_decode( $_REQUEST['Data'] );
		
		if ( $_REQUEST['Data'] && is_object( $_REQUEST['Data'] ) )
		{
			foreach ( $_REQUEST['Data'] as $key=>$val )
			{
				if ( $val && is_array( $val ) )
				{
					foreach ( $val as $ke=>$va )
					{
						if ( is_object ( $va ) )
						{
							foreach ( $va as $k=>$v )
							{
								if ( !in_array( $k, $json ) )
								{
									throwXmlError ( WRONG_JSON_FORMAT );
								}
							}
						}
					}
				}
				else if ( !in_array( $key, $json ) )
				{
					throwXmlError ( WRONG_JSON_FORMAT );
				}
			}
		}
		else
		{
			throwXmlError ( WRONG_JSON_FORMAT );
		}
	}
	
	// Get User data from sessionid
	$sess = new dbObject ( 'UserLogin' );
	$sess->Token = $_REQUEST['SessionID'];
	if ( $sess->Load () )
	{
		$u = new dbObject ( 'SBookContact' );
		$u->UserID = $sess->UserID;
		if( !$u->Load () )
		{
			throwXmlError ( AUTHENTICATION_ERROR );
		}
	}
	else
	{
		throwXmlError ( SESSION_MISSING );
	}
	
	$found = false;
	
	$m = new dbObject( 'SBookMessage' );
	
	switch ( $_REQUEST['Type'] )
	{
		// --- Handle posts -------------------------------------------------------------------------------------------------
		
		case 'post':
			if ( !isset( $_REQUEST['ParentID'] ) || isset( $_REQUEST['PostID'] ) )
			{
				// Load and save ---
				if( $_REQUEST['PostID'] > 0 && $m->Load( $_REQUEST['PostID'] ) )
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
					$m->CategoryID = ( isset( $_REQUEST['CategoryID'] ) ? $_REQUEST['CategoryID'] : getWallID() );
					$m->SenderID = $u->ID;
					$m->Access = ( isset( $_REQUEST['Access'] ) ? $_REQUEST['Access'] : 0 );
					// TODO: Shouldn't be nessesary to set receiver if it is to self or profile wall
					$m->ReceiverID = ( isset( $_REQUEST['ReceiverID'] ) ? $_REQUEST['ReceiverID'] : ( !isset( $_REQUEST['CategoryID'] ) ? $m->SenderID : 0 ) );
				}
				
				if ( isset( $_REQUEST['Message'] ) && trim( $_REQUEST['Message'] ) )
				{
					$m->Message = $_REQUEST['Message'];
					//$m->Message = sanitizeText( $_REQUEST['Message'] );
					//$m->Message = str_replace ( '<!--separate-->', '', $m->Message );
					//$m->Tags = strtolower( str_replace( '#', '', implode( ',', gethashtags( $m->Message ) ) ) );
				}
				
				$found = true;
			}
			break;
		
		// --- Handle comment ------------------------------------------------------------------------------------------------
		
		case 'comment':
			if ( isset( $_REQUEST['ParentID'] ) || isset( $_REQUEST['CommentID'] ) )
			{
				// Load and save ---
				if ( $_REQUEST['CommentID'] > 0 && $m->Load( $_REQUEST['CommentID'] ) )
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
					$m->CategoryID = ( isset( $_REQUEST['CategoryID'] ) ? $_REQUEST['CategoryID'] : getWallID() );
					$m->SenderID = $u->ID;
					$m->Access = ( isset( $_REQUEST['Access'] ) ? $_REQUEST['Access'] : 0 );
					$m->ReceiverID = 0;
					$m->ParentID = ( $_REQUEST['ParentID'] ? $_REQUEST['ParentID'] : 0 );
				}
				
				if ( isset( $_REQUEST['Message'] ) && trim( $_REQUEST['Message'] ) )
				{
					$m->Message = $_REQUEST['Message'];
					//$m->Message = sanitizeText( $_REQUEST['Message'] );
					//$m->Message = str_replace ( '<!--separate-->', '', $m->Message );
					//$m->Tags = strtolower( str_replace( '#', '', implode( ',', gethashtags( $m->Message ) ) ) );
				}
				
				// Update modified date on parent
				$c = new dbObject( 'SBookMessage' );
				if ( $c->Load( $_REQUEST['ParentID'] ) )
				{
					$c->DateModified = date ( 'Y-m-d H:i:s' );
					$c->Save();
				}
				
				$found = true;
			}
			break;
	}
	
	if ( ( !isset( $_REQUEST['PostID'] ) && !isset( $_REQUEST['CommentID'] ) ) && $found )
	{
		// Data set
		if ( isset( $_REQUEST['Data'] ) && is_object( $_REQUEST['Data'] ) )
		{
			$data = $_REQUEST['Data'];
		}
		else
		{
			$data = false;
		}
		
		if ( $data && is_object( $data ) )
		{
			$meta = $data;
		}
		else
		{
			$meta = new stdClass();
		}
		
		// Message set
		if( $_REQUEST['Message'] )
		{
			//$meta->Message = htmlentities( sanitizeText( $_REQUEST['Message'] ) );
			$meta->Message = htmlentities( $_REQUEST['Message'] );
			
			/*// If title is missing create one from message
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
			}*/
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
		if( in_array( $_REQUEST['Type'], array( 'post', 'comment' ) ) )
		{
			// Create file
			$lib = new Library ();
			if ( isset( $_REQUEST['CategoryID'] ) )
			{
				$lib->CategoryID = $_REQUEST['CategoryID'];
			}
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
		}
		
		$m->Data = json_obj_encode( $meta );
	}
	
	if ( $found )
	{
		// Save
		$m->Save();
		
		//die( print_r( $_REQUEST,1 ) . ' --' );
		
		if ( $m->ID > 0 )
		{
			showXmlData ( $m->UniqueID );
		}
	}
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
