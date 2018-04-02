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

if( isset( $_FILES ) && $_FILES[ 'fileupload' ] )
{
	die( print_r( $_FILES,1 ) . ' ..' );
}

//die( print_r( $_FILES,1 ) . ' ..' );

if( !$folder && $parent ) $folder = $parent->folder;

if( $webuser && $_REQUEST[ 'action' ] == 'uploadfile' )
{	
	if( isset( $_FILES[ 'avatar' ] ) )
	{
		$lib = new Library ();
		if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}
		else
		{
			$lib->UserID = $parent->cuser->ID;
		}
		$lib->ParentFolder = 'Album';
		$lib->FolderName = 'Profile Pictures';
		$lib->FolderAccess = 0;
		$lib->FileAccess = 0;
		$lib->UploadFile( $_FILES[ 'avatar' ] );
		
		// Get contact
		$c = new dbObject( 'SBookContact' );
		$c->UserID = $webuser->ID;
		if ( $c->Load() )
		{	
			// Load user
			$u = new dbObject( 'Users' );
			$u->Load( $c->UserID );
			
			$u->Image = $lib->FileID;
			$u->Save();
			$c->ImageID = $lib->FileID;
			$c->Save();
		}
		
		$image = new dbImage( $lib->FileID );
		
		die ( '
		<script>
			parent.refreshAvatar( \'' . $image->getImageHTML ( 160, 160, 'framed', false, 0xffffff ) . '\' );
		</script>' . print_r( $file,1 ) );
	}
	else if( isset( $_FILES[ 'cover' ] ) )
	{
		$lib = new Library ();
		if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}
		else
		{
			$lib->UserID = $parent->cuser->ID;
		}
		$lib->ParentFolder = 'Album';
		$lib->FolderName = 'Cover Photos';
		$lib->FolderAccess = 0;
		$lib->FileAccess = 0;
		$lib->UploadFile( $_FILES[ 'cover' ] );
		
		$image = new dbImage( $lib->FileID );
		
		die ( '
		<script>
			parent.refreshCover( \'' . $image->getImageHTML ( 1000, 315, 'framed', false, 0x000000 ) . '\' );
		</script>' . print_r( $lib,1 ) );
	}
	else if( isset( $_FILES[ 'news' ] ) )
	{
		$lib = new Library ();
		$lib->CategoryID = $folder->CategoryID;
		$lib->FolderName = 'News';
		$lib->FolderAccess = 0;
		$lib->FileAccess = 0;
		$lib->UploadFile( $_FILES[ 'news' ] );
		
		// Save to news
		$n = new dbObject( 'SBookNews' );
		$n->CategoryID = $folder->CategoryID;
		if( isset( $_REQUEST[ 'nid' ] ) ) 
		{
			$n->Load( $_REQUEST[ 'nid' ] );
		}
		else
		{
			$n->Tags = 'Current';
			if( !$n->Load() )
			{
				$n->PostedID = $webuser->ID;
				$n->DateAdded = date( 'Y-m-d H:i:s' );
			}
		}
		$n->DateModified = date( 'Y-m-d H:i:s' );
		$n->MediaType = $lib->MediaType;
		$n->MediaID = $lib->MediaID;
		$n->Save();
		
		die ( '
		<script>
			parent.refreshNews(\'Current\');
		</script>' . print_r( $lib,1 ) );
	}
	else if( isset( $_FILES[ 'events' ] ) && $_POST[ 'eventid' ] > 0 )
	{
		$lib = new Library ();
		$lib->UserID = $parent->cuser->UserID;
		if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}
		$lib->ParentFolder = 'Library';
		$lib->FolderName = 'Events';
		$lib->FolderAccess = 0;
		$lib->FileAccess = 0;
		$lib->UploadFile( $_FILES[ 'events' ] );
		
		// Get event
		$e = new dbObject( 'SBookEvents' );
		$e->ID = $_POST[ 'eventid' ];
		if ( $e->Load() )
		{	
			$e->ImageID = $lib->FileID;
			$e->Save();
		}
		
		$image = new dbImage( $lib->FileID );
		
		if( isset( $_REQUEST['jax'] ) )
		{
			die ( '
			<script>
				refreshEvent( \'' . $image->getImageHTML ( 1000, 338, 'framed', false, 0x000000 ) . '\' );
			</script>' . print_r( $lib,1 ) );
		}
		else
		{
			die ( '
			<script>
				parent.refreshEvent( \'' . $image->getImageHTML ( 1000, 338, 'framed', false, 0x000000 ) . '\' );
			</script>' . print_r( $lib,1 ) );
		}
	}
	else if( isset( $_FILES[ 'wall' ] ) )
	{
		$lib = new Library ();
		if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}
		else
		{
			$lib->UserID = $parent->cuser->ID;
		}
		$lib->ParentFolder = 'Library';
		$lib->FolderName = 'Unsorted';
		$lib->FolderAccess = 0;
		$lib->FileAccess = 0;
		$lib->UploadFile( $_FILES[ 'wall' ] );
		
		die ( '
		<script>
			parent.IncludeMedia( \'' . $lib->FileID . '\', \'' . $lib->FolderID . '\', \'' . $lib->MediaID . '\', \'' . $lib->MediaType . '\', \'' . $lib->FolderPath . '\' );
		</script>' . print_r( $lib,1 ) );
	}
	else if( isset( $_FILES[ 'library' ] ) )
	{
		$lib = new Library ();
		if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}
		else
		{
			$lib->UserID = $parent->cuser->ID;
		}
		if( $_POST[ 'folderid' ] > 0 )
		{
			$lib->FileFolder = $_POST[ 'folderid' ];
		}
		$lib->FolderAccess = 0;
		$lib->FileAccess = 0;
		$lib->UploadFile( $_FILES[ 'library' ] );
		
		die ( '
		<script>
			parent.refreshFilesDirectory( \'' . $lib->FolderID . '\', \'' . $lib->FileID . '\' );
		</script>' . print_r( $lib,1 ) );
	}
}

?>
