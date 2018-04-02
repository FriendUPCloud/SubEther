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

// Set limit
$limit = $_POST[ 'limit' ] ? $_POST[ 'limit' ] : 50;

// Chat Settings
$usdata = UserData( $webuser->ID );

// Get chat log for a user and you! --------------------------------------------
if( isset( $_POST[ 'u' ] ) )
{
	$defimg = 'admin/gfx/arenaicons/user_johndoe_32.png';

	// TODO: update notification to support cid
	if( $sc = $database->fetchObjectRow ( '
		SELECT 
			c.*, i.UniqueID AS ImageUniqueID, i.Filename, f.DiskPath, u.PublicKey 
		FROM 
			SBookContact c 
				LEFT JOIN Image i ON ( c.ImageID = i.ID )
				LEFT JOIN Folder f ON ( i.ImageFolder = f.ID )
				LEFT JOIN Users u ON ( u.ID = c.UserID ) 
		WHERE 
			c.ID = \'' . $_POST[ 'u' ] . '\' 
	', false, 'components/chat/functions/chat.php' ) )
	{
		if ( !FileExists( ( BASE_URL . 'secure-files/images/' . ( $sc->ImageUniqueID ? $sc->ImageUniqueID : $sc->ImageID ) . '/' ) ) )
		{
			$sc->DiskPath = false;
		}
		
		$sc->Image = ( $sc->ImageID > 0 && $sc->DiskPath && $sc->Filename ? ( BASE_URL . 'secure-files/images/' . ( $sc->ImageUniqueID ? $sc->ImageUniqueID : $sc->ImageID ) . '/' ) : $defimg );
	}
	else
	{
		$sc = new stdClass();
		$sc->ID = '0';
		$sc->Image = '';
	}
	
	// Get encrytion key from storage to open encrypted messages
	if( $st = $database->fetchObjectRow( '
		SELECT 
			m.*,
			u.PublicKey AS UserPublicKey 
		FROM 
			SBookMail m, 
			Users u, 
			SBookContact c 
		WHERE
				m.SenderID = \'' . $sc->ID . '\' 
			AND m.ReceiverID = \'' . $webuser->ContactID . '\' 
			AND m.Type IN ( "cm" ) 
			AND c.ID = m.ReceiverID 
			AND u.ID = c.UserID 
			AND u.PublicKey = m.PublicKey 
		ORDER BY 
			m.ID DESC
	', false, 'components/chat/functions/chat.php' ) );
	else if( $st = $database->fetchObjectRow( '
		SELECT 
			s.*, 
			u.PublicKey AS UserPublicKey 
		FROM 
			Users u, 
			SBookStorage s 
		WHERE 
				s.ContactID = \'' . $webuser->ContactID . '\' 
			AND s.IDs IN (' . $sc->ID . ') 
			AND s.Relation = "SBookContact" 
			AND s.IsDeleted = "0" 
			AND s.PublicKey = u.PublicKey 
			AND s.UserID = u.ID 
		ORDER BY s.ID DESC 
	', false, 'components/chat/functions/chat.php' ) );
	
	
	$q = '
		SELECT 
			m.*,
			m.EncryptionKey AS CryptoKey, 
			m.ContactID AS PosterID,
			c.ImageID,
			c.Username,
			i.UniqueID AS ImageUniqueID,
			i.Filename, f.DiskPath
		FROM 
			SBookMail m 
				LEFT JOIN SBookContact c ON 
				(
					c.ID = m.ContactID 
				) 
				LEFT JOIN Image i ON
				(
					i.ID = c.ImageID
				) 
				LEFT JOIN Folder f ON
				(
					f.ID = i.ImageFolder
				)
		WHERE 
			(
				(
						m.SenderID = \'' . $webuser->ContactID . '\' 
					AND m.Type IN ( "im", "vm" ) 
				) 
				OR 
				(		m.ReceiverID = \'' . $webuser->ContactID . '\' 
					AND m.Type IN ( "im", "vm" ) 
				)
				OR
				(
						m.ReceiverID = \'' . $webuser->ContactID . '\' 
					AND m.Type IN ( "cm" ) 
				)
			) 
			AND m.Message != "" 
			' . ( $_POST[ 'lastmessage' ] > 0 ? '
			AND m.ID > \'' . $_POST[ 'lastmessage' ] . '\'
			' : '' ) . '
		ORDER BY 
			m.ID DESC
		LIMIT ' . $limit . '
	';
	
	$us = '';
	
	if( $sc->UserID && ( $us = IsUserOnline( $sc->UserID ) ) )
	{
		$time = TimeToHuman( $us->LastActivity, 'mini' );
		if( ( date( 'YmdHi' ) - date( 'YmdHi', strtotime( $us->LastActivity ) ) ) > 6 )
		{
			$us = '';
		}
	}
	
	$notify = ( $_POST['notify'] ? '1' : '' );
	$seen = ( $_POST[ 'lastseen' ] ? $_POST[ 'lastseen' ] : '' );
	$read = ( $_POST['read'] ? '1' : '' );
	
	if ( !isset( $usdata->Settings->Sound ) || ( isset( $usdata->Settings->Sound ) && $usdata->Settings->Sound == 1 ) )
	{
		$sound = 'pling';
	}
	else
	{
		$sound = 'mute';
	}
	
	// TODO: Connect encryptionkey to every message.
	
	$pubkey = ( $sc->PublicKey ? fcrypto::stripHeader( $sc->PublicKey ) : '' );
	$enckey = ( $st->EncryptionKey ? ( fcrypto::stripHeader( $st->EncryptionKey ) . ( $st->UniqueID ? ( '<!--encryptionid-->' . $st->UniqueID ) : '' ) ) : '' );
	
	$str = ''; $img = '';

	if( $sm = $database->fetchObjectRows ( $q, false, 'components/chat/functions/chat.php' ) )
	{	
		$ii = 0; $lastmessage = ''; $read = ''; $notify = ''; $liveurl = ''; $liveconnect = ''; $alert = false;
		
		$displaynames = array( '0' => 'Anonymous' );
		
		// 
		$ids = array();
		foreach( $sm as $m ) if( $m->PosterID > 0 ) $ids[] = $m->PosterID;
		$contacts = ( !$ids ? array() : $database->fetchObjectRows( '
			SELECT * FROM SBookContact WHERE ID IN ( ' . implode( ', ', $ids ) . ' )
		', false, 'components/chat/functions/chat.php' ) );
		
		if( $contacts )
		{
			// Get all display names
			$carr = array();
			foreach( $contacts as $c )
			{
				$carr[] = $c->ID;
			}
			$displaynames = GetUserDisplayname( $carr );
		}
		
		foreach( $sm as $m )
		{
			$date = '';
			
			$human = TimeToHuman( $m->Date, 'medium' );
			$m->DateTime = $m->Date;
			$m->Date = ( $human ? $human : date( 'H:i', strtotime( $m->Date ) ) );
			
			// Set lastmessage and notify
			if( $ii == 0 )
			{
				$lastmessage = $m->ID;
				
				
				$notify = ( $us && $m->ReceiverID == $webuser->ContactID && $m->IsNoticed == '0' ? ( $m->Message ? $m->Message : '1' ) : '0' );
				
				// If last message is sent to current user and is not read alert user
				if( $m->IsAlerted == '0' && $m->ReceiverID == $webuser->ContactID )
				{					
					$alert = $sound;
				}
				
				// If we have voicechat notification
				if ( ( $m->Type == 'vm' || strstr( $m->Message, '&zwnj;&zwnj;&zwnj;' ) ) && $alert )
				{
					
					$alert = ( $alert != 'mute' ? 'call_out' : $alert );
					
					if ( strstr( $m->Message, '&zwnj;&zwnj;&zwnj;' ) && ( preg_match( '/&zwnj;&zwnj;&zwnj;.*(http[s]?:\/\/[^\\s]+)/i', $m->Message, $matches ) ) )
					{
						$liveurl = $matches[1];
					}
					else
					{
						$liveurl = $m->Message;
					}
				}
				
				// Message the sender if receiver has accepted or declined
				if ( ( $m->Type == 'vm' || strstr( $m->Message, '&zwnj;&zwnj;&zwnj;' ) ) && $m->PosterID == $webuser->ContactID && $m->IsAccepted != '0' && $m->IsConnected == '0' )
				{
					$liveconnect = $m->IsAccepted;
				}
				
				// If last message is sent to current user and is read and datemodified is over 2min in delay from date set seen date
				if ( $m->IsRead == '1' && $m->PosterID == $webuser->ContactID && $m->DateModified != '0000-00-00 00:00:00.000000' )
				{
					$seen = 'Seen ' . date( 'D H:i', strtotime( $m->DateModified ) );
				}
				
				// TODO: Look at the code and find out why IsNoticed and IsAlerted is not set correctly for API messages outside of TR ... 
				
				// If the message is older then 10min and haven't been noticed yet, don't alert about it.
				if( $notify && ( date( 'YmdHi' ) - date( 'YmdHi', strtotime( $m->Date ) ) ) > 10 )
				{
					$notify = '0'; $alert = '0';
				}
				
				$read = ( $m->IsRead == '1' && $m->ReceiverID == $webuser->ContactID ? '1' : '0' );
			}
			
			// Voicechat hide link
			if( $m->Type == 'vm' || strstr( $m->Message, '&zwnj;&zwnj;&zwnj;' ) )
			{
				$m->Message = ( ( /*$m->IsAccepted != 0 ? ( $m->IsAccepted == 1 ? 'accepted' : 'declined' ) : */'sent' ) . ': Live invite' );
			}
			
			if( $m->Type == 'im' || $m->Type == 'vm' || $m->Type == 'cm' )
			{
				// TODO: Fix this link maker in javascript because it's fucking up the BASE_URL, so I had to comment it out.
				
				if ( !FileExists( ( BASE_URL . 'secure-files/images/' . ( $m->ImageUniqueID ? $m->ImageUniqueID : $m->ImageID ) . '/' ) ) )
				{
					$m->Filename = false;
				}
				$imgurl = ( $m->Filename ? ( 'secure-files/images/' . ( $m->ImageUniqueID ? $m->ImageUniqueID : $m->ImageID ) . '/' ) : 'admin/gfx/arenaicons/user_johndoe_32.png' );
				$chatImage = '<img src="' . $imgurl . '"/>';
				$str .= '<div class="ChatRow" rowid="' . $m->ID . '" userid="' . $m->PosterID . '" datetime="' . $m->DateTime . '">';
				$str .= '<div class="ChatInfo">';
				
				$str .= '<div class="Nick">' . ( isset( $displaynames[$m->PosterID] ) && is_string( $displaynames[$m->PosterID] ) ? $displaynames[$m->PosterID] : $m->Username ) . ( $m->IsCrypto ? ( ' <i class="fa fa-fw fa-lock">(crypto)</i>' ) : '' ) . '</div>';
				$str .= '<div class="Time">' . $m->Date . '</div>';
				$str .= '</div>';
				// If this is an encrypted string send it pure to client
				if( $m->IsCrypto )
				{
					$crypto  = "-----ENCRYPTION-----\n";
					$crypto .= $m->Message . "\n";
					$crypto .= "-----ENCRYPTION-----";
					
					$str .= '<div class="ChatMessage">' . $crypto . '</div>';
				}
				// Else do the default rendering of a message
				else
				{
					$str .= '<div class="ChatMessage">' . str_replace( array( '<!--separate-->', '<!--message-->', '<!--data-->' ), array( '', '', '' ), html_entity_decode( $m->Message ) ) . '</div>';
				}
				
				$str .= '</div><!--data-->' . ( $m->CryptoKey ? fcrypto::stripHeader( $m->CryptoKey ) : ( $st->EncryptionKey ? fcrypto::stripHeader( $st->EncryptionKey ) : '' ) ) . '<!--data-->' .  ( $m->UniqueKey ? $m->UniqueKey : '' ) . '<!--message-->';
			}
			
			$ii++;
		}
	}
	else if( /*!isset( $_POST[ 'lastseen' ] ) && */$_POST[ 'lastmessage' ] > 0 && ( $m = $database->fetchObjectRow ( '
		SELECT 
			m.*,
			c.ID AS PosterID,
			c.ImageID,
			c.Username 
		FROM 
			SBookMail m, 
			SBookContact c 
		WHERE 
				m.ID = \'' . $_POST[ 'lastmessage' ] . '\'
			AND
			( 
				(
						m.Type IN ( "im", "vm" ) 
					AND m.Message != "" 
					AND c.ID = m.SenderID
				)
				OR
				(
						m.Type IN ( "cm" )
					AND m.Message != "" 
					AND c.ID = m.ContactID 
				) 
			)
		ORDER BY 
			m.ID DESC
	', false, 'components/chat/functions/chat.php' ) ) )
	{
		$notify = ''; $seen = ''; $read = ''; $liveconnect = '';
		
		
		$notify = ( $us && $m->ReceiverID == $webuser->ContactID && $m->IsNoticed == '0' ? ( $m->Message ? $m->Message : '1' ) : '0' );
		
		// TODO: Look at the code and find out why IsNoticed and IsAlerted is not set correctly for API messages outside of TR ... 
		
		// If the message is older then 10min and haven't been noticed yet, don't alert about it.
		if( $notify && ( date( 'YmdHi' ) - date( 'YmdHi', strtotime( $m->Date ) ) ) > 10 )
		{
			$notify = '0';
		}
		
		// Message the sender if receiver has accepted or declined
		if ( ( $m->Type == 'vm' || strstr( $m->Message, '&zwnj;&zwnj;&zwnj;' ) ) && $m->PosterID == $webuser->ContactID && $m->IsAccepted != '0' && $m->IsConnected == '0' )
		{
			$liveconnect = $m->IsAccepted;
		}
		
		// If last message is sent to current user and is read and datemodified is over 2min in delay from date set seen date
		if( $m->IsRead == '1' && $m->PosterID == $webuser->ContactID && $m->DateModified != '0000-00-00 00:00:00.000000' )
		{
			$seen = 'Seen ' . date( 'D H:i', strtotime( $m->DateModified ) );
		}
		
		if( isset( $_POST[ 'lastseen' ] ) && $_POST[ 'lastseen' ] == $seen )
		{
			$seen = '';
		}
		
		$read = ( $m->IsRead == '1' && $m->ReceiverID == $webuser->ContactID ? '1' : '0' );
	}
	
	if( isset( $_REQUEST[ 'function' ] ) )
	{
		
		// Output splittable "array" with index list :
		output( 'ok<!--separate-->' .             // 0
			$str . '<!--separate-->' .            // 1
			( $us ? 1 : 0 ) . '<!--separate-->' . // 2 online status
			$notify . '<!--separate-->' .         // 3 whether user is notified
			$lastmessage . '<!--separate-->' .    // 4 last message id
			$alert . '<!--separate-->' .          // 5 to alert user
			$seen . '<!--separate-->' .           // 6 is the message seen
			$sc->ID . '<!--separate-->' .         // 7 contact id
			$sc->Image . '<!--separate-->' .      // 8 contact image
			$read . '<!--separate-->' .			  // 9 whether user has read
			$pubkey . '<!--separate-->'	.		  // 10 users publickey
			$enckey . '<!--separate-->' .		  // 11 message encryptionkey and encryptionid
			$liveurl . '<!--separate-->' . 		  // 12 videochat invite url
			$liveconnect . '<!--separate-->' 	  // 13 videochat connected
		);
	}
	
}
// Get the contact list --------------------------------------------------------
else
{
	$_SESSION['plugins']['friendup'] = true;
	
	// Make sure we only refresh the contact list every 15 secs
	$time = time ();
	
	if( !isset( $_SESSION['contactlist_lasttime'] ) )
	{
		$_SESSION['contactlist_lasttime'] = $time;
	}
	
	if(
	    ( $time - $_SESSION['contactlist_lasttime'] ) > 15 ||
		!isset( $_SESSION['contactlist'] ) ||
		!isset( $_SESSION['contactlist_presense'] ) )
	{
		// Get all the online presenses
		$uids = array(); $usrs = array(); $imgs = array(); $ii = 0;
		
		// TODO: Add support for images connected to user
		if( $contacts = ContactRelations( false, 'Contact' ) )
		{
			$uid = array();
			
			foreach( $contacts as $cs )
			{
				$us = '';
				
				// Can only be used if user and contact is using chrome
				if( ( stripos( $cs->UserAgent, 'Chrome' ) !== false || $cs->DataSource == 'node' ) && stripos( $_SERVER['HTTP_USER_AGENT'], 'Chrome' ) !== false )
				{
					$cs->VideoEnabled = true;
				}
				
				$cs->status = 'admin/gfx/icons/bullet_white.png';
				if( $us = $cs->OnlineStatus )
				{
					$cs->time = $cs->LastActivity = $us->LastActivity;
					if( ( date( 'YmdHi' ) - date( 'YmdHi', strtotime( $us->LastActivity ) ) ) > 10 )
					{
						$cs->IsOnline = false;
					}
					else
					{
						$cs->IsOnline = true;
						$cs->time = '';
						$cs->title = ( $us->CategoryID > 0 ? getCategoryByID( $us->CategoryID )->Name : '' );
						$cs->status = 'admin/gfx/icons/bullet_green.png';
						$uid[] = $cs->UserID;
						$ii++;
					}
				}
				
				$uids[$cs->UserID] = $cs->UserID;
				$usrs[$cs->ID] = $cs->ID;
				
				if( $cs->ImageID > 0 )
				{
					$imgs[$cs->ImageID] = $cs->ImageID;
				}
			}
			
			// --- Image destinations -----------------------------------------------------------------------------------
			
			if( $imgs && $img = $database->fetchObjectRows( '
				SELECT
					f.DiskPath, i.* 
				FROM
					Folder f, Image i
				WHERE
					i.ID IN (' . implode( ',', $imgs ) . ') AND f.ID = i.ImageFolder
				ORDER BY
					ID ASC
			', false, 'components/chat/functions/chat.php' ) )
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
						unset( $imgs[$i->ID] );
					}
				}
			}
			
			$_SESSION['contactlist'] = $contacts;
			$_SESSION['contactlist_online'] = ( $uid ? implode( ',', $uid ) : 0 );
		}
		
		$_SESSION['contactlist_lasttime'] = time();
		
		$_SESSION['contactlist_images'] = $imgs;
		
		// TODO: Add this also to ContactRelations
		$_SESSION['contactlist_displayname'] = GetUserDisplayname( $usrs );
		
		$_SESSION['contactlist_userdata'] = UserData( $uids );
	}
	
	$str = '';
	
	// Refresh contacts from session	
	if( ( $_POST['init'] || $_SESSION['contactlist_online'] != $_POST['online'] ) && ( $contacts = $_SESSION['contactlist'] ) )
	{
		$array = array();
		
		$ii = 0; $k = 10; $mode = 'default'; $uid = array();
		
		$str .= '<div class="inner"><ul>';
		
		foreach( $contacts as $cs )
		{
			
			$title = ''; $time = ''; $us = ''; $con = ''; $cdata = ''; $vchat = '';
			
			$cdata = $_SESSION['contactlist_userdata'][$cs->UserID];
			
			$cs->Img = ( isset( $_SESSION['contactlist_images'][$cs->ImageID]->DiskPath ) ? $_SESSION['contactlist_images'][$cs->ImageID]->DiskPath : 'admin/gfx/arenaicons/user_johndoe_32.png' );
			
			// Onclick
			$onclick = 'chatObject.addPrivateChat(\'' . $cs->ID . '\',\'' . ( ( isset( $_SESSION['contactlist_displayname'][$cs->ID] ) && is_string( $_SESSION['contactlist_displayname'][$cs->ID] ) ) ? $_SESSION['contactlist_displayname'][$cs->ID] : $cs->Username )  . '\',\'' . $cs->Img . '\');';
			
			// If mobile version
			if( ( $parent && $parent->agent && $parent->agent != 'web' && $parent->agent != 'browser' ) || UserAgent() != 'web' )
			{
				$onclick = 'chatObject.openContactMessage(\'' . $cs->ID . '\');';
			}
			else if( $usdata && $usdata->Settings->Chat == '1' )
			{
				$onclick = 'openWindow( \'Chat\', \'' . $cs->UserID . '\', \'chatwindow\', function(){ openPrivChat( \'' . $cs->ID . '\', \'' . ( ( isset( $_SESSION['contactlist_displayname'][$cs->ID] ) && is_string( $_SESSION['contactlist_displayname'][$cs->ID] ) ) ? $_SESSION['contactlist_displayname'][$cs->ID] : $cs->Username ) . '\', \'window\' ); } );openChat();';
			}
			
			$con .= $cs->ID . '<!--var--><div class="contact" ' . ( $cs->title ? ( 'title="idling at ' . $cs->title . '"' ) : '' ) . '><div id="c' . $cs->ID . '" class="anchor" style="visibility:hidden;"></div><a href="javascript:void(0)" onclick="' . $onclick . 'return false;">';
			$con .= '<span><div class="image">';
			$con .= ( $cs->Img ? '<img src="' . $cs->Img . '" style="background-image:url(\'' . $cs->Img . '\')"/>' : '' );
			$con .= '</div></span>';
			$con .= '<span>' . ( ( isset( $_SESSION['contactlist_displayname'][$cs->ID] ) && is_string( $_SESSION['contactlist_displayname'][$cs->ID] ) ) ? $_SESSION['contactlist_displayname'][$cs->ID] : $cs->Username ) . '</span><span>';
			
			$con .= '<div class="status"><span class="' . ( $vchat ? 'voicechat' : 'time' ) . '" time="' . $cs->LastActivity . '">' . ( $cs->time ? date( 'H:i', strtotime( $cs->time ) ) : $vchat ) . '</span> <img src="' . $cs->status . '"></div></span>';
			
			$con .= '</a>';
			
			if( isset( $_SESSION['plugins']['friendup'] ) && isset( $cs->IsOnline ) && $cs->IsOnline && isset( $cs->VideoEnabled ) )
			{
				
				$con .= '<div class="voicechat" onclick="chatObject.callUser(\'' . $cs->ID . '\',\'' . ( ( isset( $_SESSION['contactlist_displayname'][$cs->ID] ) && is_string( $_SESSION['contactlist_displayname'][$cs->ID] ) ) ? $_SESSION['contactlist_displayname'][$cs->ID] : $cs->Username )  . '\',\'' . $cs->Img . '\',event)" style="background-image:url(\'subether/gfx/conf_icon.png\')"></div>';
			}
			
			$con .= '</div>';
			
			// If we have contactid from url get messages for this contact
			if( $cid > 0 && $cid == $cs->ID )
			{
				// Don't need this atm
				//$con .= $mstr;
			}
			
			$key = ( isset( $cs->IsOnline ) ? ( $cs->IsOnline ? ( '1' . str_pad( $k++, 8, '0', STR_PAD_LEFT ) ) : ( '2' . str_pad( $k++, 8, '0', STR_PAD_LEFT ) ) ) : ( '3' . str_pad( $k++, 8, '0', STR_PAD_LEFT ) ) );
			
			
			$array[$key] = $con;
		}
		
		ksort( $array );
		
		if( isset( $_REQUEST[ 'function' ] ) && $_REQUEST[ 'function' ] == 'chat' )
		{
			
			output( 'ok<!--separate-->' . ( $array && is_array( $array ) ? implode( '<!--contacts-->', $array ) : '' )  . '<!--separate-->' . $_SESSION['contactlist_online'] . '<!--separate-->' . $voicechat . '<!--separate-->' . ( $usdata && $usdata->Settings->Crypto == '1' ? '1' : '' ) );
		}
		
		foreach( $array as $key=>$arr )
		{
			if( strstr( $arr, '<!--var-->' ) )
			{
				$var = explode( '<!--var-->', $arr );
				
				$array[$key] = '<li sort="' . $key . '" id="ChatContact_' . $var[0] . '"' /*. ( $cid > 0 && $cid == $var[0] ? 'class="active "' : '' )*/ . '>' . $var[1] . '</li>';
			}
		}
		
		$str .= implode( $array );
		$str .= '</ul></div>';
	}
	else
	{
		if( isset( $_REQUEST[ 'function' ] ) && $_REQUEST[ 'function' ] == 'chat' )
		{
			output( 'no new updates' );
		}
	}
}

if( !isset( $_REQUEST[ 'global' ] ) && isset( $_REQUEST[ 'function' ] ) && $_REQUEST[ 'function' ] == 'chat' )
{
	output( 'fail' );
}

?>
