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

global $webuser, $database;

$cbase = 'subether/components/wall';

include_once ( $cbase . '/include/functions.php' );

$cids = getUserGroupsID( $parent->cuser->UserID );
$cods = getUserContactsID( $webuser->ContactID );
$cwds = getUserContactsID( $parent->cuser->ContactID );

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
		( m.CategoryID != !GetWallID! ) AS `IsGroup`, 
		"newsfeed" AS `Mode` 
	FROM
		SBookContact u, 
		SBookMessage m 
			LEFT JOIN SBookContact u2 ON
			(
					m.ReceiverID = u2.ID
				AND m.ReceiverID > 0
			) 
			LEFT JOIN SBookCategory c ON
			(
					m.CategoryID = c.ID
				AND m.CategoryID > 0
			) 
	WHERE
			m.Type IN ( "post", "vote" ) 
		AND
		(
				m.ThreadID = m.ID
			OR  m.ThreadID = "0"
		)
		AND m.ParentID = "0"
		AND m.NodeID = "0" 
		AND
		(
			' .  ( $cids ? ( '
			(
					m.CategoryID IN ( !CIDS! )
				AND u.ID = m.SenderID
			) ' ) : '' ) . '
			OR
			(
					m.CategoryID = !GetWallID!
				AND u.ID = m.SenderID
				AND m.ReceiverID > 0
				AND m.ReceiverID IN ( !CWDS! )
			)
		) 
		AND
		(
			(
					m.Access = "2"
				AND m.SenderID = !ContactID! 
			) 
			OR
			(
					m.Access = "4"
				AND m.SenderID = !ContactID! 
			) 
			' . ( isset( $parent->access->IsAdmin ) ? '
			OR
			(
					m.Access = "4"
				AND m.CategoryID = !CategoryID! 
			)
			' : '' ) . '
			OR
			(
					m.Access = "1"
				AND m.SenderID IN ( !CODS! )
			)
			OR
			(
				m.Access = "0"
			)
		) 
		AND m.ID = \'' . $_POST['pid'] . '\' 
	ORDER BY 
		m.Date DESC 
	LIMIT 1 
';

$qp = str_replace( '!GetWallID!', '\'' . getWallID() . '\'', $qp );
$qp = str_replace( '!ContactID!', '\'' . $webuser->ContactID . '\'', $qp );
$qp = str_replace( '!CategoryID!', '\'' . $parent->access->CategoryID . '\'', $qp );
$qp = str_replace( '!CIDS!', ( $cids ? implode( ',', $cids ) : '' ), $qp );
$qp = str_replace( '!CWDS!', ( $cwds ? implode( ',', $cwds ) : $cuser->ContactID ), $qp );
$qp = str_replace( '!CODS!', ( $cods ? implode( ',', $cods ) : $webuser->ContactID ), $qp );

if ( $p = $database->fetchObjectRow( $qp, false, 'components/wall/functions/share.php' ) )
{
	$userimage = ''; $userimg = '';
	
	$_POST['mid'] = $_POST['pid'];
	
	// --- Webuser image -----------------------------------------------------------------------------------
	
	$obj = new stdClass();
	
	$defaultimg = 'admin/gfx/arenaicons/user_johndoe_32.png';
	
	if ( $img = $database->fetchObjectRow( '
		SELECT
			f.DiskPath, i.* 
		FROM
			Folder f, Image i
		WHERE
			i.ID = \'' . $webuser->Image . '\' AND f.ID = i.ImageFolder
		ORDER BY
			ID ASC
	', false, 'components/wall/functions/share.php' ) )
	{
		$obj->ID = $img->ID;
		$obj->Filename = $img->Filename;
		$obj->FileFolder = $img->ImageFolder;
		$obj->Filesize = $img->Filesize;
		$obj->FileWidth = $img->Width;
		$obj->FileHeight = $img->Height;
		$obj->DiskPath = str_replace( ' ', '%20', ( $img->DiskPath != '' ? $img->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $img->Filename );
	}
	
	$userimage = '<img style="background-image:url(\'' . ( $obj->DiskPath ? $obj->DiskPath : $defaultimg ) . '\');background-position: center center;background-repeat: no-repeat;background-size: cover;width:100%;height:100%;" src="' . ( $obj->DiskPath ? $obj->DiskPath : $defaultimg ) . '"/>';
	$userimg = ( $obj->DiskPath ? $obj->DiskPath : $defaultimg );
	
	// --- Gather some variables -----------------------------------------------------------------------------
	
	$comments = array(); $imgs = array(); $closedmode = array();
	
	if ( is_string( $p->Data ) )
	{
		$p->Data = json_obj_decode( $p->Data );
	}
	
	if ( isset( $p->Data->FileID ) || isset( $p->Data->LibraryFiles ) )
	{
		if( isset( $p->Data->LibraryFiles ) && is_array( $p->Data->LibraryFiles ) )
		{
			foreach( $p->Data->LibraryFiles as $fi )
			{
				switch( $fi->MediaType )
				{
					case 'image':
					case 'album':
						$imgs[$fi->FileID] = $fi->FileID;
						break;
				}
			}
		}
		else
		{
			switch( $p->Data->MediaType )
			{
				case 'image':
					$imgs[$u->Data->FileID] = $p->Data->FileID;
					break;
			}
		}
	}
	
	// The rest of it ---
	
	if( !$p->NodeID && $p->Image > 0 && !$imgs[$p->Image] )
	{
		$imgs[$p->Image] = $p->Image;
	}
	
	// --- Image destinations -----------------------------------------------------------------------------------
	
	if ( $imgs && $img = $database->fetchObjectRows( '
		SELECT
			f.DiskPath, i.* 
		FROM
			Folder f, Image i
		WHERE
			i.ID IN (' . implode( ',', $imgs ) . ') AND f.ID = i.ImageFolder
		ORDER BY
			ID ASC
	', false, 'components/wall/functions/share.php' ) )
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
			
			$imgs[$i->ID] = $obj;
		}
	}
	
	// --- Set some more variables ----------------------------------------------------------------------------
	
	// Decode json
	$p->SeenBy = is_string( $p->SeenBy ) ? json_decode( $p->SeenBy ) : false;
	$p->RateDownBy = is_string( $p->RateDownBy ) ? json_decode( $p->RateDownBy ) : false;
	$p->RateUpBy = is_string( $p->RateUpBy ) ? json_decode( $p->RateUpBy ) : false;
	
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
	
	// --- Render articles and posts --------------------------------------------------------------------------
	
	include ( $dest = ( $cbase . '/functions/posts_v2.php' ) );
	
	$obj = new stdClass();
	
	if ( $str )
	{
		$obj->Message = $p->Message;
		$obj->Content = $p->ParsedData;
	}
}

//die( $str );

/*if( isset( $_POST[ 'pid' ] ) && $_POST[ 'pid' ] > 0 )
{
	$q = '
		SELECT 
			m.*, u.Username AS Name, u.ImageID AS Image 
		FROM 
			SBookMessage m, 
			SBookContact u 
		WHERE 
			m.ID = \'' . $_POST[ 'pid' ] . '\' 
			AND m.ParentID = "0" 
			AND u.UserID = m.SenderID 
		ORDER BY  
			m.ID DESC 
	';
		
	$obj = $database->fetchObjectRow( $q );
	
	$i = new dbImage ();
	if( $i->load( $obj->Image ) )
	{
		$obj->ImageHTML = $i->getImageHTML ( 154, 154, 'framed', false, 0xffffff );
	}
	
	if( $obj->Type == 'article' )
	{
		$obj->Content = '<div class="html"><div class="ParseContent"><div class="image"><a href="en/home/' . $obj->Name . '">' . $obj->ImageHTML . '</a></div><div class="text"><h3>Status Update</h3><p>By ' . $obj->Name . '<br><br></p><p><strong>' . $obj->Subject . '</strong></p><p>' . $obj->Leadin . '</p></div></div></div>';
	}
	else if( $obj->HTML )
	{
		$obj->Content = $obj->HTML;
	}
	else
	{
		$obj->Content = '<div class="html"><div class="ParseContent"><div class="image"><a href="en/home/' . $obj->Name . '">' . $obj->ImageHTML . '</a></div><div class="text"><h3>Status Update</h3><p>By ' . $obj->Name . '<br><br></p><p>' . $obj->Message . '</p></div></div></div>';
	}
}*/

?>
