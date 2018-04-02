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

if( !$folder && $parent ) $folder = $parent->folder;

if( $webuser && $_REQUEST[ 'action' ] == 'uploadfile' )
{	
	if( isset( $_FILES[ 'avatar' ] ) )
	{
		$lib = new Library ();
		$lib->FolderAccess = 0;
		$lib->FileAccess = 0;
		$lib->UserID = $parent->cuser->UserID;
		if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}
		$lib->ParentFolder = 'Album';
		$lib->FolderName = 'Profile Pictures';
		$lib->UploadFile( $_FILES[ 'avatar' ] );
		
		// Get contact
		$c = new dbObject( 'SBookContact' );
		$c->UserID = $parent->cuser->UserID;
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
		
		//$image = new dbImage( $lib->FileID );
		$image = false;
		
		if( $img = $database->fetchObjectRow( '
			SELECT
				f.DiskPath,
				i.* 
			FROM
				Folder f,
				Image i
			WHERE
					i.NodeID = "0" 
				AND i.ID = "' . $lib->FileID . '"
				AND f.ID = i.ImageFolder 
		' ) )
		{
			$obj = new stdClass();
			$obj->ID = $img->ID;
			$obj->Filename = $img->Filename;
			$obj->FileFolder = $img->ImageFolder;
			$obj->Filesize = $img->Filesize;
			$obj->FileWidth = $img->Width;
			$obj->FileHeight = $img->Height;
			$obj->DiskPath = str_replace( ' ', '%20', ( $img->DiskPath != '' ? $img->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $img->Filename );
			
			$image = $obj->DiskPath;
		}
		
		if( isset( $_REQUEST['jax'] ) )
		{
			/*die ( '
			<script>
				refreshAvatar( \'' . $image->getImageHTML ( 160, 160, 'framed', false, 0xffffff ) . '\' );
			</script>' . print_r( $lib,1 ) );*/
			die ( '
			<script>
				refreshAvatar( \'<div style="background-image:url(' . $image . ')"></div>\' );
			</script>' . print_r( $lib,1 ) );
		}
		else
		{
			/*die ( '
			<script>
				parent.refreshAvatar( \'' . $image->getImageHTML ( 160, 160, 'framed', false, 0xffffff ) . '\' );
			</script>' . print_r( $lib,1 ) );*/
			die ( '
			<script>
				parent.refreshAvatar( \'<div style="background-image:url(' . $image . ')"></div>\' );
			</script>' . print_r( $lib,1 ) );
		}
	}
	else if( isset( $_FILES[ 'cover' ] ) )
	{
		$lib = new Library ();
		$lib->UserID = $parent->cuser->UserID;
		if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}
		$lib->ParentFolder = 'Album';
		$lib->FolderName = 'Cover Photos';
		$lib->FolderAccess = 0;
		$lib->FileAccess = 0;
		$lib->UploadFile( $_FILES[ 'cover' ] );
		
		//$image = new dbImage( $lib->FileID );
		$image = false;
		
		if( $img = $database->fetchObjectRow( '
			SELECT
				f.DiskPath,
				i.* 
			FROM
				Folder f,
				Image i
			WHERE
					i.NodeID = "0" 
				AND i.ID = "' . $lib->FileID . '"
				AND f.ID = i.ImageFolder
		' ) )
		{
			$obj = new stdClass();
			$obj->ID = $img->ID;
			$obj->Filename = $img->Filename;
			$obj->FileFolder = $img->ImageFolder;
			$obj->Filesize = $img->Filesize;
			$obj->FileWidth = $img->Width;
			$obj->FileHeight = $img->Height;
			$obj->DiskPath = str_replace( ' ', '%20', ( $img->DiskPath != '' ? $img->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $img->Filename );
			
			$image = $obj->DiskPath;
		}
		
		if( isset( $_REQUEST['jax'] ) )
		{
			/*die ( '
			<script>
				refreshCover( \'' . $image->getImageHTML ( 1000, 338, 'framed', false, 0x000000 ) . '\' );
			</script>' . print_r( $lib,1 ) );*/
			die ( '
			<script>
				refreshCover( \'<div style="background-image:url(' . $image . ');width:100%;height:100%;background-repeat:no-repeat;background-size:cover;background-position:center center;"></div>\' );
			</script>' . print_r( $lib,1 ) );
		}
		else
		{
			/*die ( '
			<script>
				parent.refreshCover( \'' . $image->getImageHTML ( 1000, 338, 'framed', false, 0x000000 ) . '\' );
			</script>' . print_r( $lib,1 ) );*/
			die ( '
			<script>
				parent.refreshCover( \'<div style="background-image:url(' . $image . ');width:100%;height:100%;background-repeat:no-repeat;background-size:cover;background-position:center center;"></div>\' );
			</script>' . print_r( $lib,1 ) );
		}
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
		
		if( isset( $_REQUEST['jax'] ) )
		{
			die ( '
			<script>
				refreshNews(\'Current\');
			</script>' . print_r( $lib,1 ) );
		}
		else
		{
			die ( '
			<script>
				parent.refreshNews(\'Current\');
			</script>' . print_r( $lib,1 ) );
		}
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
				RefreshEvent(false,\'' . $_POST[ 'eventid' ] . '\');
			</script>' . print_r( $lib,1 ) );
		}
		else
		{
			die ( '
			<script>
				parent.RefreshEvent(false,\'' . $_POST[ 'eventid' ] . '\');
			</script>' . print_r( $lib,1 ) );
		}
	}
	else if( isset( $_FILES[ 'mail' ] ) )
	{
		$lib = new Library ();
		$lib->UserID = $parent->cuser->UserID;
		/*if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}*/
		$lib->ParentFolder = 'Library';
		$lib->FolderName = 'Emails';
		$lib->FolderAccess = 2;
		$lib->FileAccess = 2;
		$lib->UploadFile( $_FILES[ 'mail' ] );
		
		/*// Get mail
		$m = new dbObject( 'SBookMailHeaders' );
		$m->ID = $_POST[ 'messageid' ];
		if ( $m->Load() && $lib->FileID > 0 )
		{
			$m->Files = json_obj_decode( $m->Files, 'array' );
			$m->Files[] = $lib->FileID;
			$m->Files = json_obj_encode( $m->Files, 'array' );
			$m->Save();
		}*/
		
		if( isset( $_REQUEST['jax'] ) )
		{
			die ( '
			<script>
				refreshMailFiles(' . $lib->FileID . ');
			</script>' . print_r( $lib,1 ) );
		}
		else
		{
			die ( '
			<script>
				parent.refreshMailFiles(' . $lib->FileID . ');
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
		$lib->ParentFolder = 'Library';
		$lib->FolderName = 'Unsorted';
		$lib->FolderAccess = 0;
		$lib->FileAccess = 0;
		$lib->UploadFile( $_FILES[ 'wall' ] );
		
		if( isset( $_REQUEST['jax'] ) )
		{
			die ( '
			<script>
				IncludeMedia( \'' . $lib->FileID . '\', \'' . $lib->FolderID . '\', \'' . $lib->MediaID . '\', \'' . $lib->MediaType . '\', \'' . $lib->FolderPath . '\' );
			</script>' . print_r( $lib,1 ) );
		}
		else
		{
			die ( '
			<script>
				parent.IncludeMedia( \'' . $lib->FileID . '\', \'' . $lib->FolderID . '\', \'' . $lib->MediaID . '\', \'' . $lib->MediaType . '\', \'' . $lib->FolderPath . '\' );
			</script>' . print_r( $lib,1 ) );
		}
	}
	else if( isset( $_FILES[ 'store' ] ) )
	{
		$lib = new Library ();
		if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}
		$lib->ParentFolder = 'Library';
		$lib->FolderName = 'Unsorted';
		$lib->FolderAccess = 0;
		$lib->FileAccess = 1;
		$lib->UploadFile( $_FILES[ 'store' ] );
		
		
		if( $lib->FileID > 0 && $_POST['productid'] > 0 )
		{
			// Get product and update it
			$p = new dbObject( 'SBookProducts' );
			$p->ID = $_POST['productid'];
			if ( $p->Load() )
			{
				$p->Images = ( strstr( $p->Images, ',' ) ? explode( ',', $p->Images ) : array( $p->Images ) );
				
				foreach( $p->Images as $k=>$img )
				{
					if( isset( $_POST['fileid'] ) && $img == $_POST['fileid'] )
					{
						$p->Images[$k] = $lib->FileID;
					}
					else if( $img == $lib->FileID )
					{
						unset( $p->Images[$k] );
					}
				}
				
				if( !isset( $_POST['fileid'] ) )
				{
					$p->Images[] = $lib->FileID;
				}
				
				$p->Images = implode( ',', $p->Images );
				$p->Save();
			}
		}
		
		if( isset( $_REQUEST['jax'] ) )
		{
			die ( '
			<script>
				IncludeProductImage( \'' . $lib->FileID . '\', \'' . $_POST['productid'] . '\', \'' . $_POST['fileid'] . '\' );
			</script>' . print_r( $lib,1 ) );
		}
		else
		{
			die ( '
			<script>
				parent.IncludeProductImage( \'' . $lib->FileID . '\', \'' . $_POST['productid'] . '\', \'' . $_POST['fileid'] . '\' );
			</script>' . print_r( $lib,1 ) );
		}
	}
	else if( isset( $_FILES[ 'crowdfunding' ] ) )
	{
		$lib = new Library ();
		if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}
		$lib->ParentFolder = 'Library';
		$lib->FolderName = 'Unsorted';
		$lib->FolderAccess = 0;
		$lib->FileAccess = 1;
		$lib->UploadFile( $_FILES[ 'crowdfunding' ] );
		
		
		if( $lib->FileID > 0 && $_POST['fundraiserid'] > 0 )
		{
			// Get product and update it
			$p = new dbObject( 'SBookCrowdfunding' );
			$p->ID = $_POST['fundraiserid'];
			if ( $p->Load() )
			{
				$p->Images = ( strstr( $p->Images, ',' ) ? explode( ',', $p->Images ) : array( $p->Images ) );
				
				foreach( $p->Images as $k=>$img )
				{
					if( isset( $_POST['fileid'] ) && $img == $_POST['fileid'] )
					{
						$p->Images[$k] = $lib->FileID;
					}
					else if( $img == $lib->FileID )
					{
						unset( $p->Images[$k] );
					}
				}
				
				if( !isset( $_POST['fileid'] ) )
				{
					$p->Images[] = $lib->FileID;
				}
				
				$p->Images = implode( ',', $p->Images );
				$p->Save();
			}
		}
		
		if( isset( $_REQUEST['jax'] ) )
		{
			die ( '
			<script>
				IncludeFundraiserImage( \'' . $lib->FileID . '\', \'' . $_POST['fundraiserid'] . '\', \'' . $_POST['fileid'] . '\' );
			</script>' . print_r( $lib,1 ) );
		}
		else
		{
			die ( '
			<script>
				parent.IncludeFundraiserImage( \'' . $lib->FileID . '\', \'' . $_POST['fundraiserid'] . '\', \'' . $_POST['fileid'] . '\' );
			</script>' . print_r( $lib,1 ) );
		}
	}
	else if( isset( $_FILES[ 'library' ] ) )
	{
		$lib = new Library ();
		$lib->UserID = $parent->cuser->UserID;
		if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}
		if( $_POST[ 'folderid' ] > 0 )
		{
			$lib->FileFolder = $_POST[ 'folderid' ];
		}
		$lib->FolderAccess = 0;
		$lib->FileAccess = 1;
		$lib->UploadFile( $_FILES[ 'library' ] );
		
		if( isset( $_REQUEST['jax'] ) )
		{
			die ( '
			<script>
				refreshFilesDirectory( \'' . $lib->FolderID . '\' );
			</script>' . print_r( $lib,1 ) );
		}
		else
		{
			die ( '
			<script>
				parent.refreshFilesDirectory( \'' . $lib->FolderID . '\', \'' . $lib->FileID . '\' );
			</script>' . print_r( $lib,1 ) );
		}
	}
}

?>
