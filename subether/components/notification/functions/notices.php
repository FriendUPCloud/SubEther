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

$limit = 50;
/*
$guci = getUserContactsID( $webuser->ContactID );
$gucids = $guci ? implode( ',', $guci ) : $webuser->ContactID;
$uci = getUserGroupsID( $webuser->ID );
$ucids = $uci ? implode( ',', $uci ) : false;

$qp = '
	SELECT * FROM
	(
		(
			SELECT 
				sm.*,
				sm.ID AS PostID, 
				us.Username AS Name, 
				us.ImageID AS Image,
				u2.Username AS User_Name, 
				ca.Name AS SBC_Name, 
				ca.ID AS SBC_ID, 
				( sm.CategoryID != !GetWallID! ) AS `IsGroup` 
			FROM
				SBookContact us, 
				SBookMessage sm 
				LEFT JOIN SBookContact u2 ON ( sm.ReceiverID = u2.ID AND sm.ReceiverID > 0 ) 
				LEFT JOIN SBookCategory ca ON ( sm.CategoryID = ca.ID AND sm.CategoryID > 0 ) 
			WHERE 
				sm.Type IN ( "post", "vote" ) 
				AND sm.ParentID = "0"
				AND ( sm.ThreadID = sm.ID OR sm.ThreadID = "0" ) 
				AND sm.NodeID = "0" 
				AND sm.SenderID != !ContactID!
				AND sm.Date >= !UserDateCreated! 
				AND ( ' .  ( $ucids ? '( sm.CategoryID IN ( !UcIDS! ) 
				AND us.ID = sm.SenderID ) 
				OR  ' : '' ) . '( sm.CategoryID = !GetWallID! 
				AND us.ID = sm.SenderID 
				AND sm.ReceiverID IN ( !GucIDS! ) ) ) 
				AND ( ( sm.Access = "2" 
				AND sm.SenderID = !ContactID! ) 
				OR  ( sm.Access = "1" 
				AND sm.SenderID IN ( !GucIDS! ) ) 
				OR  ( sm.Access = "0" ) ) 
		)
		UNION
		(
			SELECT 
				sc.*, 
				sm.ID AS PostID, 
				us.Username AS Name, 
				us.ImageID AS Image, 
				u2.Username AS User_Name, 
				ca.Name AS SBC_Name, 
				ca.ID AS SBC_ID, 
				( sm.CategoryID != !GetWallID! ) AS `IsGroup` 
			FROM
				SBookContact us, 
				SBookMessage sc, 
				SBookMessage sm 
				LEFT JOIN SBookContact u2 ON ( sm.ReceiverID = u2.ID AND sm.ReceiverID > 0 ) 
				LEFT JOIN SBookCategory ca ON ( sm.CategoryID = ca.ID AND sm.CategoryID > 0 ) 
			WHERE 
				sm.Type IN ( "post", "vote" ) 
				AND sm.ParentID = "0"
				AND ( sm.ThreadID = sm.ID OR sm.ThreadID = "0" ) 
				AND sm.NodeID = "0" 
				AND sm.SenderID = !ContactID!
				AND sm.Date >= !UserDateCreated! 
				AND sm.Type = "post" 
				AND sc.ThreadID = sm.ID 
				AND sc.Type = "comment" 
				AND sc.SenderID != !ContactID! 
				AND us.ID = sc.SenderID 
		)
	)
	z
	ORDER BY
		z.ID DESC 
	LIMIT ' . $limit . ' 
';

$qp = str_replace( '!GetWallID!', getWallID(), $qp );
$qp = str_replace( '!ContactID!', $webuser->ContactID, $qp );
$qp = str_replace( '!UserDateCreated!', ( '\'' . $webuser->DateCreated . '\'' ), $qp );
$qp = str_replace( '!UcIDS!', $ucids, $qp );
$qp = str_replace( '!GucIDS!', $gucids, $qp );

$plugins = false;

// Check plugins for wall sharedposts functionality
if ( file_exists ( 'subether/plugins' ) )
{
	if ( $dir = opendir ( 'subether/plugins' ) )
	{
		while ( $file = readdir ( $dir ) )
		{
			if ( $file{0} == '.' ) continue;
			if ( !file_exists ( 'subether/plugins/' . $file . '/notification' ) )
			{
				continue;
			}
			if ( !file_exists ( $f = 'subether/plugins/' . $file . '/notification/notices.php' ) )
			{
				continue;
			}
			include ( $f );
		}
		closedir ( $dir );
	}
}

$str = ''; $output = array();

if( $plugins && is_array( $plugins ) )
{
	foreach( $plugins as $plgs )
	{
		if( !is_array( $plgs ) ) continue;
		
		foreach( $plgs as $pl )
		{
			for( $a = 0; $a < 100; $a++ )
			{
				$sorting = $pl->Date;
				
				if( !isset( $output[strtotime($sorting).'_'.$a] ) )
				{
					$output[strtotime($sorting).'_'.$a] = $pl;
					break;
				}
			}
		}
	}
}

//if( $webuser->ID == 81 ) die( print_r( $output,1 ) . ' -- ' . $webuser->ContactID . ' -- ' );

if( $posts = $database->fetchObjectRows( $qp ) )
{
	foreach( $posts as $pos )
	{
		for( $a = 0; $a < 100; $a++ )
		{
			$sorting = $pos->Date;
			
			if( !isset( $output[strtotime($sorting).'_'.$a] ) )
			{
				$output[strtotime($sorting).'_'.$a] = $pos;
				break;
			}
		}
	}
}

krsort( $output );*/

//die( print_r( $output,1 ) . ' --' );

//if( $webuser->ID == 81 ) die( print_r( GetNotifications( true ), 1 ) . ' --' );

// TODO: Use this instead.
$output = GetNotifications( $limit );

//die( print_r( $output,1 ) . ' --' );

if( $output )
{
	$cid = array(); $ids = array(); $uid = array(); $sids = array(); $eids = array(); $ii = 0;
	
	// --- Image id's -------------------------------------------------------------------------------------------
	
	foreach( $output as $u )
	{
		if( $u->SenderID > 0 && !isset( $uid[$u->SenderID] ) )
		{
			$uid[$u->SenderID] = $u->SenderID;
		}
		if( $u->ReceiverID > 0 && !isset( $uid[$u->ReceiverID] ) )
		{
			$uid[$u->ReceiverID] = $u->ReceiverID;
		}
		if( $u->OwnerID > 0 && !isset( $uid[$u->OwnerID] ) )
		{
			$uid[$u->OwnerID] = $u->OwnerID;
		}
		
		if( $u->Image > 0 && !$cid[$u->Image] )
		{
			$cid[$u->Image] = $u->Image;
		}
	}
	
	// --- Image destinations -----------------------------------------------------------------------------------
	
	$defaultimg = 'admin/gfx/arenaicons/user_johndoe_128.png';
	
	if( $cid && $img = $database->fetchObjectRows( '
		SELECT
			f.DiskPath, i.UniqueID, i.Filename, i.ID
		FROM
			Folder f, Image i
		WHERE
			i.ID IN (' . implode( ',', $cid ) . ') AND f.ID = i.ImageFolder
		ORDER BY
			ID ASC
	' ) )
	{
		$cid = array();
		
		foreach( $img as $i )
		{
			$obj = new stdClass();
			$obj->ID = $i->ID;
			$obj->Filename = $i->Filename;
			$obj->DiskPath = ( $i->DiskPath != '' ? $i->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $i->Filename;
			if ( $i->Filename )
			{
				$obj->DiskPath = ( BASE_URL . 'secure-files/images/' . ( $i->UniqueID ? $i->UniqueID : $i->ID ) . '/' );
			}
			
			$cid[$i->ID] = $obj;
			
			if ( !FileExists( $obj->DiskPath ) )
			{
				unset( $cid[$i->ID] );
			}
		}
	}
	
	//if( $webuser->ID == 81 ) die( ' -- ' . print_r( $uid,1 ) );
	
	$uid = GetUserDisplayname( $uid );
	//die( print_r( $uid,1 ) . ' --' );
	//if( $webuser->ID == 81 ) die( $webuser->ContactID . ' -- ' . print_r( $uid,1 ) );
	
	// --- Output -----------------------------------------------------------------------------------------------
	
	$str = '<div class="inner"><ul>';
	
	foreach( $output as $m )
	{
		if( $m->IsGroup && $m->ContentType != 'events' )
		{
			$m->ContentType = 'group';
		}
		/*else if( $m->Type == 'event' )
		{
			$m->ContentType = 'event';
		}*/
		else if( ( $m->ReceiverID != $m->SenderID ) || ( $m->ReceiverID == $m->SenderID && $m->Type == 'comment' ) )
		{
			$m->ContentType = 'contact';
		}
		
		switch( $m->ContentType )
		{
			// --- Groups -----------------------------------------------------------------------------------------------------------------------------
			case 'group':
				$str .= '<li' . ( !strstr( $m->ReadBy, $webuser->ContactID ) ? ' class="NotRead"' : '' ) . '><div><a href="en/home/groups/' . $m->SBC_ID . '/wall/?f=' . $m->PostID . '#MessageID_' . $m->PostID . '">';
				if( $cid[$m->Image] )
				{
					$str .= '<span><div class="image" style="background-image:url(\'' . $cid[$m->Image]->DiskPath . '\');background-size:cover;background-repeat:no-repeat;"></div></span>';
				}
				else
				{
					$str .= '<span><div class="image" style="background-image:url(\'' . $defaultimg . '\');background-size:cover;background-repeat:no-repeat;"></div></span>';
				}
				//$str .= '<span><div>' . ( GetUserDisplayname( $m->SenderID ) ? GetUserDisplayname( $m->SenderID ) : $m->Name ) . '</div><div>' . dotTrim( strip_tags( $m->Message ), 25 ) . '</div></span>';
				if( $m->Type == 'comment' )
				{
					$str .= '<span><div><u class="name">' . ( isset( $uid[$m->SenderID] ) && is_string( $uid[$m->SenderID] ) ? $uid[$m->SenderID] : $m->Name ) . '</u> ' . i18n( 'i18n_commented on your post in' ) . ' <u>' . $m->SBC_Name . '</u></div></span>';
				}
				else if( $m->Type == 'event' )
				{
					$str .= '<span><div><u class="name">' . ( isset( $uid[$m->SenderID] ) && is_string( $uid[$m->SenderID] ) ? $uid[$m->SenderID] : $m->Name ) . '</u> ' . i18n( 'i18n_made an event in' ) . ' <u>' . $m->SBC_Name . '</u></div></span>';
				}
				else
				{
					$str .= '<span><div><u class="name">' . ( isset( $uid[$m->SenderID] ) && is_string( $uid[$m->SenderID] ) ? $uid[$m->SenderID] : $m->Name ) . '</u> ' . i18n( 'i18n_posted in' ) . ' <u>' . $m->SBC_Name . '</u></div></span>';
				}
				$str .= '<span><div class="time">' . ( $m->Date > 0 ? TimeToHuman( $m->Date ) : '' ) . '</div></span>';
				$str .= '</a></div></li>';
				break;
			// --- Contacts ---------------------------------------------------------------------------------------------------------------------------
			case 'contact':
				$str .= '<li' . ( !strstr( $m->ReadBy, $webuser->ContactID ) ? ' class="NotRead"' : '' ) . '><div><a href="en/home/' . $m->User_Name . '?f=' . $m->PostID . '#MessageID_' . $m->PostID . '">';
				if( $cid[$m->Image] )
				{
					$str .= '<span><div class="image" style="background-image:url(\'' . $cid[$m->Image]->DiskPath . '\');background-size:cover;background-repeat:no-repeat;"></div></span>';
				}
				else
				{
					$str .= '<span><div class="image" style="background-image:url(\'' . $defaultimg . '\');background-size:cover;background-repeat:no-repeat;"></div></span>';
				}
				//$str .= '<span><div>' . ( GetUserDisplayname( $m->SenderID ) ? GetUserDisplayname( $m->SenderID ) : $m->Name ) . '</div><div>' . dotTrim( strip_tags( $m->Message ), 25 ) . '</div></span>';
				if( $m->Type == 'comment' )
				{
					$str .= '<span><div><u class="name">' . ( isset( $uid[$m->SenderID] ) && is_string( $uid[$m->SenderID] ) ? $uid[$m->SenderID] : $m->Name ) . '</u> ' . i18n( 'i18n_commented on your post' ) . '.</div></span>';
				}
				else if( $m->ReceiverID != $webuser->ContactID )
				{
					$str .= '<span><div><u class="name">' . ( isset( $uid[$m->SenderID] ) && is_string( $uid[$m->SenderID] ) ? $uid[$m->SenderID] : $m->Name ) . '</u> ' . i18n( 'i18n_wrote on' ) . ' <u class="contact">' . ( isset( $uid[$m->ReceiverID] ) ? $uid[$m->ReceiverID] : $m->User_Name ) . '</u>' . i18n( 'i18n_\'s wall' ) . '</div></span>';
				}
				else
				{
					$str .= '<span><div><u class="name">' . ( isset( $uid[$m->SenderID] ) && is_string( $uid[$m->SenderID] ) ? $uid[$m->SenderID] : $m->Name ) . '</u> ' . i18n( 'i18n_wrote on your wall' ) . '</div></span>';
				}
				$str .= '<span><div class="time">' . ( $m->Date > 0 ? TimeToHuman( $m->Date ) : '' ) . '</div></span>';
				$str .= '</a></div></li>';
				break;
			// --- Events ---------------------------------------------------------------------------------------------------------------------------
			case 'events':
				$str .= '<li' . ( !strstr( $m->ReadBy, $webuser->ContactID ) ? ' class="NotRead"' : '' ) . '><div><a href="en/home/' . $m->Href . '">';
				if( $cid[$m->Image] )
				{
					$str .= '<span><div class="image" style="background-image:url(\'' . $cid[$m->Image]->DiskPath . '\');background-size:cover;background-repeat:no-repeat;"></div></span>';
				}
				else
				{
					$str .= '<span><div class="image" style="background-image:url(\'' . $defaultimg . '\');background-size:cover;background-repeat:no-repeat;"></div></span>';
				}
				$str .= '<span><div><u class="name">' . ( isset( $uid[$m->OwnerID] ) && is_string( $uid[$m->OwnerID] ) ? $uid[$m->OwnerID] : $m->Username ) . '</u> ' . i18n( 'i18n_invited you to' ) . ' <u>' . $m->EventName . '</u></div></span>';
				$str .= '<span><div class="time button"><span>' . ( $m->Date > 0 ? TimeToHuman( $m->Date ) : '' ) . ' </span>';
				//die( print_r( $m,1 ) . ' --' );
				$str .= '<button class="dropdown_btn accept' . ( $m->IsAccepted == 1 ? ' selected' : '' ) . '" onclick="SignupEvent(' . $m->HourID . ');refreshNotices();return false;"' . ( $m->IsAccepted == 1 ? ' disabled="disabled"' : '' ) . '><span> ' . i18n( 'i18n_accept' ) . ' </span></button>';
				$str .= '<button class="dropdown_btn decline' . ( $m->IsAccepted == -1 ? ' selected' : '' ) . '" onclick="SignoffEvent(' . $m->HourID . ');refreshNotices();return false;"' . ( $m->IsAccepted == -1 ? ' disabled="disabled"' : '' ) . '><span> ' . i18n( 'i18n_decline' ) . ' </span></button>';
				
				$str .= '</div></span>';
				//$str .= '<span><div class="button">';
				//$str .= '<button class="dropdown_btn accept"><span>accept</span></button>';
				//$str .= '<button class="dropdown_btn decline"><span>decline</span></button>';
				//$str .= '</div></span>';
				$str .= '</a></div></li>';
				break;
			// --- Default ----------------------------------------------------------------------------------------------------------------------------
			default:
				$str .= '<li' . ( !strstr( $m->ReadBy, $webuser->ContactID ) ? ' class="NotRead"' : '' ) . '><div><a href="en/home/' . $m->Name . '?f=' . $m->PostID . '#MessageID_' . $m->PostID . '">';
				if( $cid[$m->Image] )
				{
					$str .= '<span><div class="image" style="background-image:url(\'' . $cid[$m->Image]->DiskPath . '\');background-size:cover;background-repeat:no-repeat;"></div></span>';
				}
				else
				{
					$str .= '<span><div class="image" style="background-image:url(\'' . $defaultimg . '\');background-size:cover;background-repeat:no-repeat;"></div></span>';
				}
				//$str .= '<span><div>' . ( GetUserDisplayname( $m->SenderID ) ? GetUserDisplayname( $m->SenderID ) : $m->Name ) . '</div><div>' . dotTrim( strip_tags( $m->Message ), 25 ) . '</div></span>';
				if( $m->Type == 'comment' )
				{
					$str .= '<span><div><u class="name">' . ( isset( $uid[$m->SenderID] ) && is_string( $uid[$m->SenderID] ) ? $uid[$m->SenderID] : $m->Name ) . '</u> ' . i18n( 'i18n_commented on your post' ) . '.</div></span>';
				}
				else if( $m->Type == 'event' )
				{
					$str .= '<span><div><u class="name">' . ( isset( $uid[$m->SenderID] ) && is_string( $uid[$m->SenderID] ) ? $uid[$m->SenderID] : $m->Name ) . '</u> ' . i18n( 'i18n_made an event' ) . '.</div></span>';
				}
				else
				{
					$str .= '<span><div><u class="name">' . ( isset( $uid[$m->SenderID] ) && is_string( $uid[$m->SenderID] ) ? $uid[$m->SenderID] : $m->Name ) . '</u> ' . i18n( 'i18n_wrote a post' ) . '.</div></span>';
				}
				$str .= '<span><div class="time">' . ( $m->Date > 0 ? TimeToHuman( $m->Date ) : '' ) . '</div></span>';
				$str .= '</a></div></li>';
				break;
		}
		
		//$ids[] = $m->ID;
		
		if( !isset( $m->SeenBy ) && $m->IsNoticed == 0 && $m->NotificationID > 0 )
		{
			$eids[$m->NotificationID] = $m->NotificationID;
		}
		
		$m->SeenBy = json_obj_decode( $m->SeenBy, 'array' );
		if( $m->ID > 0 && is_array( $m->SeenBy ) && !in_array( $webuser->ContactID, $m->SeenBy ) && $m->SenderID != $webuser->ContactID )
		{
			$sids[$m->ID] = $m->ID;
		}
		
		$ii++;
	}
	
	// Set status IsNoticed on user notify
	if( $sids ) IsNoticed( $sids );
	if( $eids ) IsNoticed( $eids, 'events' );
	
	$str .= '</ul></div>';
	
	die( 'ok<!--separate-->' . $str . '<!--separate-->' . ( $sids && is_array( $sids ) ? implode( ',', $sids ) : '' ) );
}
die( 'fail<!--separate-->' );

?>
