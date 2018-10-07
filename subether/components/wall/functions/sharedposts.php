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

global $database, $webuser, $cachedUsers;

//$limit = 50;
$limit = 25;

$keywords = ( isset( $_REQUEST[ 'q' ] ) && $_REQUEST[ 'q' ] != '' ? str_replace( '+', ' ', $_REQUEST[ 'q' ] ) : '' );
$focusid = ( isset( $_REQUEST[ 'f' ] ) && $_REQUEST[ 'f' ] != '' ? $_REQUEST[ 'f' ] : false );
$bookmarks = ( isset( $bookmarks ) && is_array( $bookmarks ) ? implode( ',', $bookmarks ) : false );
//die( print_r( $cachedUsers,1 ) . ' -- ' . print_r( $parent,1 ) . ' --' );
if( ( $user && $folder->CategoryID > 0 ) || $folder->ID > 0 )
{
	$mode = false;
	
	//die( print_r( $parent,1 ) . ' --' );
	
	$useragent = $parent->agent;
	
	// Events ---------------------------------------------------------------------------------------------------------------------------------------
	if( ( strtolower( $parent->folder->Name ) == 'events' || strtolower( $parent->folder->MainName ) == 'groups' ) && $_REQUEST['event'] > 0 )
	{
		$cids = getUserContactsID( $webuser->ContactID );
		
		$qp = '
			SELECT 
				m.*,
				u.Display,
				u.Firstname,
				u.Middlename,
				u.Lastname,
				u.Username AS Name,
				u.ImageID AS Image 
			FROM 
				SBookMessage m, 
				SBookContact u 
			WHERE
					m.Type IN ( "event", "vote" ) 
				AND m.CategoryID = \'' . ( $_REQUEST['categoryid'] > 0 ? $_REQUEST['categoryid'] : $parent->folder->CategoryID ) . '\' 
				AND m.ThreadID = \'' . $_REQUEST['event'] . '\'
				AND m.ParentID = "0" 
				AND u.ID = m.SenderID 
				AND ( m.Access = "0"
				' . ( isset( $parent->access->IsAdmin ) ? '
				OR  ( m.Access = "4" AND m.CategoryID = \'' . $parent->access->CategoryID . '\' ) 
				' : '' ) . '
				' . ( $webuser->ContactID > 0 ? '
				OR  ( m.Access = "4" AND m.SenderID = \'' . $webuser->ContactID . '\' ) 
				OR  ( m.Access = "2" AND m.SenderID = \'' . $webuser->ContactID . '\' ) 
				OR  ( m.Access = "1" AND m.SenderID IN ( ' . ( $cids ? implode( ',', $cids ) : $webuser->ContactID ) . ' ) ) )
				' : ')' ) . ( $keywords != '' ? '
				AND ( m.Subject LIKE "%' . $keywords . '%"
				OR m.Message LIKE "%' . $keywords . '%"
				OR m.Data LIKE "%' . $keywords . '%" ) ' : '' ) . '
				' . ( $_POST['mid'] > 0 ? '
				AND m.ID = \'' . $_POST['mid'] . '\' ' : '' ) . '
			ORDER BY  
				m.DateModified DESC, m.ID DESC
			LIMIT ' . $limit . ' 
		';
		
		$mode = 'events';
	}
	// Profile --------------------------------------------------------------------------------------------------------------------------------------
	else if( strtolower( $folder->MainName ) == 'profile' && strtolower( $module ) == 'profile' )
	{
		$cids = getUserContactsID( $webuser->ContactID );
		
		$qp = '
			SELECT 
				m.*,
				u.Display,
				u.Firstname,
				u.Middlename,
				u.Lastname,
				u.Username AS Name,
				u.ImageID AS Image,
				"0" AS `IsGroup` 
			FROM 
				SBookMessage m, 
				SBookContact u 
			WHERE
					m.Type IN ( "post", "vote" )
				AND ( m.ThreadID = m.ID OR m.ThreadID = "0" )
				AND m.CategoryID = \'' . $parent->folder->CategoryID . '\'
				AND m.ParentID = "0" 
				AND m.ReceiverID = \'' . $parent->cuser->ContactID . '\'
				AND u.ID = m.SenderID
				AND ( m.Access = "0"
				' . ( isset( $parent->access->IsAdmin ) ? '
				OR  ( m.Access = "4" AND m.CategoryID = \'' . $parent->access->CategoryID . '\' ) 
				' : '' ) . '
				' . ( $webuser->ContactID > 0 ? '
				OR  ( m.Access = "4" AND m.SenderID = \'' . $webuser->ContactID . '\' ) 
				OR  ( m.Access = "2" AND m.SenderID = \'' . $webuser->ContactID . '\' ) 
				OR  ( m.Access = "1" AND m.SenderID IN ( ' . ( $cids ? implode( ',', $cids ) : $webuser->ContactID ) . ' ) ) ) 
				' : ')' ) . ( $keywords != '' ? '
				AND ( m.Subject LIKE "%' . $keywords . '%"
				OR m.Message LIKE "%' . $keywords . '%"
				OR m.Data LIKE "%' . $keywords . '%" ) ' : '' ) . '
				' . ( $_POST['mid'] > 0 ? '
				AND m.ID = \'' . $_POST['mid'] . '\' ' : '' ) . '
			ORDER BY  
				m.Date DESC
			LIMIT ' . $limit . ' 
		';
		
		$mode = 'profile';
	}
	// Groups -------------------------------------------------------------------------------------------------------------------------------------
	else if( strtolower( $folder->MainName ) == 'groups' && strtolower( $module ) == 'main' )
	{
		$cids = getUserContactsID( $webuser->ContactID );
		
		$qp = '
			SELECT 
				m.*,
				u.Display,
				u.Firstname,
				u.Middlename,
				u.Lastname,
				u.Username AS Name,
				u.ImageID AS Image,
				c.Settings AS GroupSettings, 
				"1" AS `IsGroup` 
			FROM
				SBookContact u, 
				SBookMessage m
				LEFT JOIN SBookCategory c ON ( m.CategoryID = c.ID AND m.CategoryID > 0 ) 
			WHERE
					m.Type IN ( "post", "vote" )
				AND ( m.ThreadID = m.ID OR m.ThreadID = "0" )
				AND m.CategoryID = \'' . $parent->folder->CategoryID . '\' 
				AND m.ParentID = "0" 
				AND u.ID = m.SenderID
				AND ( m.Access = "0"
				' . ( isset( $parent->access->IsAdmin ) ? '
				OR  ( m.Access = "4" AND m.CategoryID = \'' . $parent->access->CategoryID . '\' ) 
				' : '' ) . '
				' . ( $webuser->ContactID > 0 ? '
				OR  ( m.Access = "4" AND m.SenderID = \'' . $webuser->ContactID . '\' ) 
				OR  ( m.Access = "2" AND m.SenderID = \'' . $webuser->ContactID . '\' ) 
				OR  ( m.Access = "1" AND m.SenderID IN ( ' . ( $cids ? implode( ',', $cids ) : $webuser->ContactID ) . ' ) ) )
				' : ')' ) . ( $keywords != '' ? '
				AND ( m.Subject LIKE "%' . $keywords . '%"
				OR m.Message LIKE "%' . $keywords . '%"
				OR m.Data LIKE "%' . $keywords . '%" ) ' : '' ) . '
				' . ( $_POST['mid'] > 0 ? '
				AND m.ID = \'' . $_POST['mid'] . '\' ' : '' ) . '
			ORDER BY  
				m.DateModified DESC, m.ID DESC
			LIMIT ' . $limit . ' 
		';
		
		$mode = 'groups';
	}
	// Main Feed ------------------------------------------------------------------------------------------------------------------------------
	else if( strtolower( $module ) == 'main' )
	{	
		$cids = getUserGroupsID( $cuser->UserID );
		$cods = getUserContactsID( $webuser->ContactID );
		$cwds = getUserContactsID( $cuser->ContactID );
		
		$qp = '
			SELECT 
				m.*, 
				u.Display, 
				u.Firstname, 
				u.Middlename, 
				u.Lastname, 
				u.Username AS Name, 
				u.ImageID AS Image,
				u2.Username AS User_Name, 
				c.Name AS SBC_Name, 
				c.ID AS SBC_ID,
				c.Settings AS GroupSettings, 
				( m.CategoryID != \'' . getWallID() . '\' ) AS `IsGroup` 
			FROM
				SBookContact u, 
				SBookMessage m 
				LEFT JOIN SBookContact u2 ON ( m.ReceiverID = u2.ID AND m.ReceiverID > 0 ) 
				LEFT JOIN SBookCategory c ON ( m.CategoryID = c.ID AND m.CategoryID > 0 ) 
			WHERE
					m.Type IN ( "post", "vote" ) 
				AND ( m.ThreadID = m.ID OR m.ThreadID = "0" )
				AND m.ParentID = "0"
				AND m.NodeID = "0" 
				' . ( $bookmarks ? '
				AND m.ID IN ( ' . $bookmarks . ' ) 
				' : '' ) . '
				AND ( ' .  ( $cids ? ( '( m.CategoryID IN ( ' . implode( ',', $cids ) . ' ) AND u.ID = m.SenderID ) ' ) : '' ) . '
				OR ( m.CategoryID = \'' . getWallID() . '\' AND u.ID = m.SenderID AND m.ReceiverID > 0 AND m.ReceiverID IN ( ' . ( $cwds ? implode( ',', $cwds ) : $cuser->ContactID ) . ' ) ) ) 
				AND ( ( m.Access = "2" AND m.SenderID = \'' . $webuser->ContactID . '\' ) 
				OR ( m.Access = "4" AND m.SenderID = \'' . $webuser->ContactID . '\' ) 
				' . ( isset( $parent->access->IsAdmin ) ? '
				OR ( m.Access = "4" AND m.CategoryID = \'' . $parent->access->CategoryID . '\' )
				' : '' ) . '
				OR ( m.Access = "1" AND m.SenderID IN ( ' . ( $cods ? implode( ',', $cods ) : $webuser->ContactID ) . ' ) )
				OR ( m.Access = "0" ) )
				' . ( $keywords != '' ? '
				AND ( m.Subject LIKE "%' . $keywords . '%"
				OR m.Message LIKE "%' . $keywords . '%"
				OR m.Data LIKE "%' . $keywords . '%" ) ' : '' ) . '
				' . ( $_POST['mid'] > 0 ? '
				AND m.ID = \'' . $_POST['mid'] . '\' ' : '' ) . '
			ORDER BY 
				m.Date DESC 
			LIMIT ' . $limit . ' 
		';
		
		$mode = 'newsfeed';
	}
	
	//die( $qp . ' -- ' . $mode );
	
	// TODO: Sort by ID added instead of date when parsing bookmarks
	
	$bookarray = false;
	
	if ( $bookmarks )
	{
		$bookarray = array();
		
		foreach ( explode( ',', $bookmarks ) as $v )
		{
			$bookarray[$v] = false;
		}
	}
	
	$plugins = false;
	
	// Check plugins for wall sharedposts functionality
	if ( /*$mode == 'groups' && */file_exists ( 'subether/plugins' ) )
	{
		if ( $dir = opendir ( 'subether/plugins' ) )
		{
			while ( $file = readdir ( $dir ) )
			{
				if ( $file{0} == '.' ) continue;
				if ( !file_exists ( 'subether/plugins/' . $file . '/wall' ) )
				{
					continue;
				}
				if ( !file_exists ( $f = 'subether/plugins/' . $file . '/wall/sharedposts.php' ) )
				{
					continue;
				}
				include ( $f );
			}
			closedir ( $dir );
		}
	}
	
	if( isset( $_REQUEST['plugins'] ) ) die( print_r( $plugins,1 ) . ' --' );
	
	$str = ''; $output = array();
	
	if( !isset( $_POST['mid'] ) && $plugins && is_array( $plugins ) )
	{
		foreach( $plugins as $key=>$plg )
		{
			if( $key == 'events' ) continue;
			
			$str .= '<div onmouseout="IsEdit()" onmouseover="IsEdit(1)" class="Comment">';
			$str .= $plg;
			$str .= '</div>';
		}
		
		foreach( $plugins as $plgs )
		{
			if( !is_array( $plgs ) ) continue;
			
			foreach( $plgs as $pl )
			{
				for( $a = 0; $a < 100; $a++ )
				{
					$sorting = ( $mode == 'groups' ? $pl->DateModified : $pl->Date );
					
					if( !isset( $output[strtotime($sorting).'_'.$a] ) )
					{
						$output[strtotime($sorting).'_'.$a] = $pl;
						break;
					}
				}
			}
		}
	}
	
	$posts = $database->fetchObjectRows( $qp, false, 'components/wall/functions/sharedposts.php' );
	
	// Render posts
	if( $output || $posts )
	{
		$userimage = ''; $userimg = '';
		
		$obj = new stdClass();
		
		$defaultimg = 'admin/gfx/arenaicons/user_johndoe_32.png';
		
		if( $img = $database->fetchObjectRow( '
			SELECT
				f.DiskPath, i.* 
			FROM
				Folder f,
				Image i
			WHERE
					i.NodeID = "0"
				AND i.ID = \'' . $user->Image . '\'
				AND f.ID = i.ImageFolder
			ORDER BY
				i.ID ASC
		', false, 'components/wall/functions/sharedposts.php' ) )
		{
			$obj->ID = $img->ID;
			$obj->Filename = $img->Filename;
			$obj->FileFolder = $img->ImageFolder;
			$obj->Filesize = $img->Filesize;
			$obj->FileWidth = $img->Width;
			$obj->FileHeight = $img->Height;
			$obj->DiskPath = str_replace( ' ', '%20', ( $img->DiskPath != '' ? $img->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $img->Filename );
			if ( $img->Filename )
			{
				$obj->DiskPath = ( BASE_URL . 'secure-files/images/' . ( $img->UniqueID ? $img->UniqueID : $img->ID ) . '/' );
			}
			if ( !FileExists( $obj->DiskPath ) )
			{
				$obj->DiskPath = false;
			}
		}
		
		$userimage = '<img style="background-image:url(\'' . ( $obj->DiskPath ? $obj->DiskPath : $defaultimg ) . '100x100\');background-position: center center;background-repeat: no-repeat;background-size: cover;width:100%;height:100%;" src="' . ( $obj->DiskPath ? $obj->DiskPath : $defaultimg ) . '"/>';
		$userimg = ( $obj->DiskPath ? $obj->DiskPath : $defaultimg );
		$username = $user->Username;
		$userdisplay = $user->DisplayName;
		
		/*// TODO: remove this dbimage method
		$img = new dbImage ();
		if( $img->load( $user->Image ) )
		{
			$userimage = $img->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
			$userimg = $img->getImageURL ( 30, 28, 'framed', false, 0xffffff );
		}*/
		
		$posids = array(); $nodids = array(); $nomids = array();
		$comments = array();
		
		$ids = array(); $imgs = array(); $nods = array(); $mans = array();
		
		if( $posts )
		{
			foreach( $posts as $pos )
			{
				if( $bookarray && isset( $bookarray[$pos->ID] ) )
				{
					$bookarray[$pos->ID] = $pos;
				}
				else
				{
					// Sort array by DateModified
					
					for( $a = 0; $a < 100; $a++ )
					{
						$sorting = ( $mode == 'groups' ? $pos->DateModified : $pos->Date );
						
						if( !isset( $output[strtotime($sorting).'_'.$a] ) )
						{
							$output[strtotime($sorting).'_'.$a] = $pos;
							break;
						}
					}
				}
				
				// Gather normal postid's and posts from other nodes ids
				
				if( $pos->NodeID > 0 && $pos->NodeMainID > 0 )
				{
					$nodids[$pos->NodeID] = $pos->NodeID;
					$nomids[$pos->NodeMainID] = $pos->NodeMainID;
				}
				else if( $pos->ID > 0 )
				{
					$posids[$pos->ID] = $pos->ID;
				}
			}
		}
		
		if ( $bookarray )
		{
			$output = [];
			
			foreach( $bookarray as $key=>$val )
			{
				if( $val )
				{
					$output[$key] = $val;
				}
			}
		}
		else
		{
			krsort( $output );
		}
		
		// --- Comments --------------------------------------------------------------------------------------------------
		
		$qPosids = ( is_array( $posids ) ? implode( ',', $posids ) : false );
		$qNodids = ( is_array( $nodids ) ? implode( ',', $nodids ) : false );
		$qNomids = ( is_array( $nomids ) ? implode( ',', $nomids ) : false );
		
		// --- Local post comments
		if( $qPosids )
		{
			$qPosids = '( m.ParentID IN ( ' . $qPosids . ' ) AND m.NodeID = "0" )';
		}
		
		$qSql = false;
		
		// --- Other Node post comments
		
		if( $qNodids && $qNomids )
		{
			$qSql = '( m.NodeID > 0 AND m.NodeID IN ( ' . $qNodids . ' ) AND m.ParentID IN ( ' . $qNomids . ' ) )';
		}
		
		if( ( $posids || ( $nodids && $nomids ) ) && ( $comnts = $database->fetchObjectRows( $cq = '
			SELECT 
				m.*, 
				u.Display, 
				u.Firstname, 
				u.Middlename, 
				u.Lastname, 
				u.Username AS Name, 
				u.ImageID AS Image 
			FROM 
				SBookMessage m, 
				SBookContact u 
			WHERE
				(
					' . (
						  ( $qPosids && $qSql ) ? ( $qPosids . ' OR ' . $qSql ) :
						  ( $qPosids ? $qPosids : ( $qSql ? $qSql : 'FALSE' ) )
						) .
					'
				) 
				AND u.ID = m.SenderID 
			ORDER BY  
				m.ID ASC
		', false, 'components/wall/functions/sharedposts.php' ) ) )
		{	
			foreach( $comnts as $com )
			{
				if( !isset( $comments[$com->ParentID] ) )
				{
					$comments[$com->ParentID] = array();
				}
				
				$comments[$com->ParentID][] = $com;
				
				// Comment images
				
				if( $com->Image > 0 )
				{
					$imgs[$com->Image] = $com->Image;
				}
				
				// Other comment images
				
				$comdata = false;
				
				if( is_string( $com->Data ) )
				{
					$comdata = json_obj_decode( $com->Data );
				}
				
				if( isset( $comdata->FileID ) || isset( $comdata->LibraryFiles ) || is_array( $comdata ) )
				{
					if( isset( $comdata->LibraryFiles ) && is_array( $comdata->LibraryFiles ) )
					{
						foreach( $comdata->LibraryFiles as $cfi )
						{
							switch( $cfi->MediaType )
							{
								case 'image':
								case 'album':
									$imgs[$cfi->FileID] = $cfi->FileID;
									break;
							}
						}
					}
					else if( isset( $comdata ) && is_array( $comdata ) )
					{
						foreach( $comdata as $fi )
						{
							switch( $cfi->MediaType )
							{
								case 'image':
								case 'album':
									$imgs[$cfi->FileID] = $cfi->FileID;
									break;
							}
						}
					}
					else
					{
						switch( $comdata->MediaType )
						{
							case 'image':
								$imgs[$comdata->FileID] = $comdata->FileID;
								break;
						}
					}
				}
			}
		}
		
		// --- ID && Image id's -------------------------------------------------------------------------------------------
		
		foreach( $output as $u )
		{
			// Get imageid's from parsed json data ---
			
			// TODO: Make check for utf8 or not ---
			if( is_string( $u->Data ) )
			{
				$u->Data = json_obj_decode( $u->Data );
			}
			
			if( isset( $u->Data->FileID ) || isset( $u->Data->LibraryFiles ) || is_array( $u->Data ) )
			{
				if( isset( $u->Data->LibraryFiles ) && is_array( $u->Data->LibraryFiles ) )
				{
					foreach( $u->Data->LibraryFiles as $fi )
					{
						switch( $fi->MediaType )
						{
							case 'image':
							case 'album':
								$imgs[$fi->FileID] = $fi->FileID;
								break;
							default:
								if( $fi->FilePath && $fi->FileID )
								{
									$fi->DiskPath = ( BASE_URL . 'secure-files/files/' . $fi->FileID . '/' );
								}
								break;
						}
					}
				}
				else if( isset( $u->Data ) && is_array( $u->Data ) )
				{
					foreach( $u->Data as $fi )
					{
						switch( $fi->MediaType )
						{
							case 'image':
							case 'album':
								$imgs[$fi->FileID] = $fi->FileID;
								break;
							default:
								if( $fi->FilePath && $fi->FileID )
								{
									$fi->DiskPath = ( BASE_URL . 'secure-files/files/' . $fi->FileID . '/' );
								}
								break;
						}
					}
				}
				else
				{
					switch( $u->Data->MediaType )
					{
						case 'image':
							$imgs[$u->Data->FileID] = $u->Data->FileID;
							break;
						default:
							if( $u->Data->FilePath && $u->Data->FileID )
							{
								$u->Data->DiskPath = ( BASE_URL . 'secure-files/files/' . $u->Data->FileID . '/' );
							}
							break;
					}
				}
			}
			
			// The rest of it ---
			
			if( !$u->NodeID && $u->Image > 0 && !$imgs[$u->Image] )
			{
				$imgs[$u->Image] = $u->Image;
			}
			else if( $u->NodeID > 0 && !$nods[$u->Image] )
			{
				$nods[$u->Image] = $u->Image;
				$mais[$u->Image] = $u->NodeID;
			}
			
			if( !$ids[$u->NodeID] )
			{
				$ids[$u->NodeID] = array();
			}
			
			if( $u->NodeID > 0 && !$ids[$u->NodeID][$u->NodeMainID] )
			{
				$ids[$u->NodeID][$u->NodeMainID] = $u->NodeMainID;
			}
			else if( !$u->NodeID && !$ids[$u->NodeID][$u->ID] )
			{
				$ids[$u->NodeID][$u->ID] = $u->ID;
			}
		}
		
		// --- Image destinations -----------------------------------------------------------------------------------
		
		if( $imgs && $img = $database->fetchObjectRows( '
			SELECT
				f.DiskPath,
				i.* 
			FROM
				Folder f,
				Image i
			WHERE
					i.NodeID = "0"
				AND i.ID IN (' . implode( ',', $imgs ) . ')
				AND f.ID = i.ImageFolder
			ORDER BY
				i.ID ASC
		', false, 'components/wall/functions/sharedposts.php' ) )
		{
			$imgs = array();
			
			foreach( $img as $i )
			{
				$obj = new stdClass();
				$obj->ID = $i->ID;
				$obj->Filename = $i->Filename;
				$obj->FileFolder = $i->ImageFolder;
				$obj->Filesize = $i->Filesize;
				$obj->FileWidth = $i->Width;
				$obj->FileHeight = $i->Height;
				$obj->DiskPath = str_replace( ' ', '%20', ( $i->DiskPath != '' ? $i->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $i->Filename );
				if ( $i->Filename )
				{
					$obj->DiskPath = ( BASE_URL . 'secure-files/images/' . ( $i->UniqueID ? $i->UniqueID : $i->ID ) . '/' );
				}
				
				$imgs[$i->ID] = $obj;
				
				if ( !FileExists( $obj->DiskPath ) )
				{
					//unset( $imgs[$i->ID] );
				}
			}
		}
		
		if( $nods && $mans && $img = $database->fetchObjectRows( '
			SELECT
				f.DiskPath,
				i.* 
			FROM
				Folder f,
				Image i
			WHERE
					i.NodeMainID IN (' . implode( ',', $nods ) . ')
				AND i.NodeID IN (' . implode( ',', $mans ) . ')
				AND f.ID = i.ImageFolder
			ORDER BY
				i.ID ASC
		', false, 'components/wall/functions/sharedposts.php' ) )
		{
			$nods = array();
			
			foreach( $img as $i )
			{
				$obj = new stdClass();
				$obj->ID = $i->ID;
				$obj->Filename = $i->Filename;
				$obj->FileFolder = $i->ImageFolder;
				$obj->Filesize = $i->Filesize;
				$obj->FileWidth = $i->Width;
				$obj->FileHeight = $i->Height;
				$obj->DiskPath = str_replace( ' ', '%20', ( $i->DiskPath != '' ? $i->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $i->Filename );
				
				$nods[$i->ID] = $obj;
			}
		}
		
		// --- Bookmarks -------------------------------------------------------------------------------------------
		
		$bmarks = false;
		
		if( $bmrows = $database->fetchObjectRow( '
			SELECT
				*
			FROM
				SBookBookmarks
			WHERE
					UserID = \'' . $webuser->ContactID . '\'
				AND Component = "wall" 
			ORDER BY
				DateModified DESC
		', false, 'components/wall/functions/sharedposts.php' ) )
		{
			$bmarks = json_obj_decode( $bmrows->Bookmarks, 'array' );
		}
		
		// --- Output -----------------------------------------------------------------------------------------------
		
		$ii = 0; $closedmode = array();
		
		$mstr = array(); $pstr = array(); $curnt = array();
		
		$str .= '<div id="PendingPosts" class="pendingposts"></div>';
		
		foreach( $output as $p )
		{
			if( $ii >= $limit )
			{
				break;
			}
			
			if( isset( $_REQUEST[ 'hashtag' ] ) && $_REQUEST[ 'hashtag' ] != '' && !strstr( $p->Tags, $_REQUEST[ 'hashtag' ] ) )
				continue;
			
			// Decode json
			$p->SeenBy = is_string( $p->SeenBy ) ? json_obj_decode( $p->SeenBy, 'array' ) : false;
			$p->RateDownBy = is_string( $p->RateDownBy ) ? json_obj_decode( $p->RateDownBy, 'array' ) : false;
			$p->RateUpBy = is_string( $p->RateUpBy ) ? json_obj_decode( $p->RateUpBy, 'array' ) : false;
			//$p->Data = is_string( $p->Data ) ? json_decode( $p->Data ) : false;
			
			if ( isset( $p->GroupSettings ) && is_string( $p->GroupSettings ) )
			{
				$p->GroupSettings = json_decode( $p->GroupSettings );
			}
			
			// Assign CategoryName
			if( $p->IsGroup && isset ( $p->SBC_Name ) )
			{
				$p->CategoryName = $p->SBC_Name;
			}
			
			// Assign Reciever
			if( !$p->IsGroup && $p->ReceiverID > 0 && isset ( $p->SBR_Username ) )
			{
				$p->Receiver = $p->SBR_Username;
			}
			else if ( $p->ReceiverID > 0 && $p->User_Name )
			{
				$p->Receiver = $p->User_Name;
			}
			
			if( strstr( $useragent, 'presentation' ) )
			{
				if ( $p->Type && in_array( $p->Type, array( 'post' ) ) )
				{
					include ( $cbase . '/functions/presentation.php' );
				}
			}
			else
			{
				// Render articles and posts
				if( $p->Type == 'article' )
				{
					include ( $cbase . '/functions/articles.php' );
				}
				else if( $p->Type == 'vote' )
				{
					include ( $cbase . '/functions/votes.php' );
				}
				else
				{
					if( NODE_DEV_MODE && !isset( $_REQUEST['oldmode'] ) )
					{
						include ( $cbase . '/functions/posts_v2.php' );
					}
					else
					{
						include ( $cbase . '/functions/posts.php' );
					}
				}
			}
			$ii++;
		}
		
		if( strstr( $useragent, 'presentation' ) )
		{
			include ( $cbase . '/functions/plugins.php' );
			
			// TODO: Update plugins based on what's new, and also remember current view on mediaslider and text scroll, and the plugins status without moving the user away from current doings.
			
			if ( $estr )
			{
				$str .= '<div id="Plugins" onclick="this.className==\'closed\'?this.className=\'\':this.className=\'closed\'">' . $estr . '</div>';
			}
			
			if ( $mstr )
			{
				$str .= '<div id="MediaSlideshow">';
				$str .= '<div class="Settings">';
				$str .= '<span class="icon_cover" title="' . i18n('i18n_cover' ) . '" onclick="this.parentNode.parentNode.className=\'cover\';">' . i18n('i18n_cover' ) . '</span> ';
				$str .= '<span class="icon_seperator"> | </span> ';
				$str .= '<span class="icon_contain" title="' . i18n('i18n_contain' ) . '" onclick="this.parentNode.parentNode.className=\'contain\';">' . i18n('i18n_contain' ) . '</span> ';
				$str .= '</div>';
				$str .= '<div class="Pages"></div>';
				$str .= '<div class="Arrows"></div>';
				$str .= '<div class="ImageContainer">' . implode( $mstr ) . '</div>';
				$str .= '</div>';
				$str .= '<script> SlidePresent.init( \'MediaSlideshow\' ); </script>';
			}
			
			if ( $pstr )
			{
				$str .= '<div id="PostScroller">';
				$str .= '<div class="TextOverlayLeft"></div>';
				$str .= '<div class="TextOverlayRight"></div>';
				$str .= '<div class="TextContainer"><table><tr>';
				
				foreach( $pstr as $pst )
				{
					$str .= '<td>' . $pst . '</td>';
					$str .= '<td> ... </td>';
				}
				
				$str .= '</tr></table></div></div>';
				$str .= '<script> ScrollPresent.init( \'PostScroller\' ); </script>';
			}
		}
		
		if( isset( $_REQUEST[ 'p' ] ) && $_REQUEST[ 'p' ] > 0 )
		{
			$str = $astr;
		}
		
		$str .= '<input id="WallModeClosed" type="hidden" value="' . ( $closedmode ? implode( ',', $closedmode ) : '' ) . '"/>';
		
		if( isset( $_REQUEST['function'] ) && !isset( $_REQUEST['bypass'] ) ) die( 'ok<!--separate-->' . $str );
	}
}

if( isset( $_REQUEST['function'] ) && $_REQUEST['function'] == 'sharedposts' && !isset( $_REQUEST['bypass'] ) ) die( 'ok<!--separate2-->' . ( $str ? $str : 'no wall data can be rendered atm, working on it' ) );

?>
