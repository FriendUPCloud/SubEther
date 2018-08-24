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

$mstr = '';

if( $parent && is_numeric( end( $parent->url ) ) )
{
	$cid = trim( end( $parent->url ) );
}

// Get chat log for a user and you! --------------------------------------------
if( isset( $_POST[ 'u' ] ) || isset( $cid ) )
{
	$cid = ( $_POST[ 'u' ] ? $_POST[ 'u' ] : $cid );
	
	$defimg = 'admin/gfx/arenaicons/user_johndoe_32.png';
	
	// TODO: update notification to support cid
	$sc = $database->fetchObjectRow ( '
		SELECT 
			c.*,
			i.UniqueID AS ImageUniqueID,
			i.Filename,
			f.DiskPath 
		FROM 
			SBookContact c 
				LEFT JOIN Image i ON ( c.ImageID = i.ID )
				LEFT JOIN Folder f ON ( i.ImageFolder = f.ID ) 
		WHERE 
			c.ID = \'' . $cid . '\' 
	' );
	
	//$sc->Image = ( $sc->ImageID > 0 && $sc->DiskPath && $sc->Filename ? str_replace( ' ', '%20', $sc->DiskPath . $sc->Filename ) : $defimg );
	$sc->Image = ( $sc->ImageID > 0 && $sc->DiskPath && $sc->Filename ? ( BASE_URL . 'secure-files/images/' . ( $sc->ImageUniqueID ? $sc->ImageUniqueID : $sc->ImageID ) . '/' ) : $defimg );
	
	$q1 = '
		SELECT 
			m.*,
			c.ID AS PosterID,
			c.ImageID,
			c.Username,
			i.UniqueID AS ImageUniqueID,
			i.Filename,
			f.DiskPath
		FROM 
			SBookMail m, 
			SBookContact c 
				LEFT JOIN Image i ON ( c.ImageID = i.ID ) 
				LEFT JOIN Folder f ON ( i.ImageFolder = f.ID )
		WHERE 
			( (	m.SenderID = \'' . $webuser->ContactID . '\' 
			AND m.ReceiverID = \'' . $sc->ID . '\' 
			AND m.Type = "im" 
			AND c.ID = m.SenderID ) 
			OR 
			(	m.SenderID = \'' . $sc->ID . '\' 
			AND m.ReceiverID = \'' . $webuser->ContactID . '\' 
			AND m.Type = "im" 
			AND c.ID = m.SenderID ) ) 
			AND m.Message != "" 
			' . ( $_POST[ 'lastmessage' ] > 0 ? 'AND m.ID > \'' . $_POST[ 'lastmessage' ] . '\' ' : '' ) . '
		ORDER BY 
			m.ID DESC
		LIMIT ' . $limit . '
	';
	
	if( !isset( $_POST[ 'lastseen' ] ) && $_POST[ 'lastmessage' ] > 0 )
	{
		$q2 = '
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
				AND m.Type = "im"
				AND m.Message != "" 
				AND c.ID = m.SenderID 
			ORDER BY 
				m.ID DESC
		';
	}
	
	$uonl = IsUserOnline( $sc->UserID );
	
	if( $uonl )
	{
		if( ( date( 'YmdHi' ) - date( 'YmdHi', strtotime( $uonl->LastActivity ) ) ) > 6 )
		{
			$uonl = '';
		}
	}
	
	$msgs = array();
	
	$notify = ( $_POST['notify'] ? '1' : '' );
	$seen = ( $_POST[ 'lastseen' ] ? $_POST[ 'lastseen' ] : '' );
	$read = ( $_POST['read'] ? '1' : '' );
	
	$lastmessage = ''; $alert = '';

	if( $sm = $database->fetchObjectRows ( $q1 ) )
	{	
		$ii = 0; $ids = array();
		
		// Assign list of senders id
		foreach( $sm as $m )
		{
			$ids[$m->PosterID] = $m->PosterID;
		}
		
		$unam = GetUserDisplayname( $ids );
		
		foreach( $sm as $m )
		{
			// Set lastmessage and notify
			if( $ii == 0 )
			{
				$lastmessage = $m->ID;
				$notify = ( $uonl && $m->ReceiverID == $webuser->ContactID && $m->IsNoticed == '0' ? '1' : '' );
				$alert = ( $uonl && $m->ReceiverID == $webuser->ContactID && $m->IsAlerted == '0' ? 'pling' : '' );
				
				// If is read
				$read = ( $m->IsRead == '1' && $m->ReceiverID == $webuser->ContactID ? '1' : '0' );
				
				// If last message is sent to current user and is read and datemodified is over 2min in delay from date set seen date
				$seen = ( $m->IsRead == '1' && $m->SenderID == $webuser->ContactID && $m->DateModified > 0 ? ( 'Seen ' . date( 'D H:i', strtotime( $m->DateModified ) ) ) : '' );
				
				
				
				if( $notify && ( date( 'YmdHi' ) - date( 'YmdHi', strtotime( $m->Date ) ) ) > 10 )
				{
					$notify = '0'; $alert = '0';
				}
			}
			
			// Assign some extra vars
			$m->Date = ( TimeToHuman( $m->Date, 'medium' ) ? TimeToHuman( $m->Date, 'medium' ) : date( 'H:i', strtotime( $m->Date ) ) );
			$m->DisplayName = ( isset( $unam[$m->PosterID] ) ? $unam[$m->PosterID] : $m->Username );
			$m->Reply = ( $webuser->ContactID == $m->PosterID ? 1 : 0 );
			
			// Render html				
			$msg  = '<div class="ChatRow" rowid="' . $m->ID . '">';
			$msg .= '<div class="ChatInfo">';
			//$msg .= '<div class="Image" style="background-image:url(\'' . ( $m->ImageID > 0 && $m->DiskPath && $m->Filename ? str_replace( ' ', '%20', $m->DiskPath . $m->Filename ) : $defimg ) . '\')"></div>';
			$msg .= '<div class="Image" style="background-image:url(\'' . ( $m->ImageID > 0 && $m->DiskPath && $m->Filename ? ( BASE_URL . 'secure-files/images/' . ( $m->ImageUniqueID ? $m->ImageUniqueID : $m->ImageID ) . '/' ) : $defimg ) . '\')"></div>';
			$msg .= '<div class="Nick">' . $m->DisplayName . '</div>';
			$msg .= '<div class="Time">' . $m->Date . '</div>';
			$msg .= '</div>';
			//$msg .= '<div class="ChatMessage">' . renderSmileys( stripslashes( makeLinks( nl2br( html_entity_decode( $m->Message ) ) ) ) ) . '</div>';
			$msg .= '<div class="ChatMessage">' . $m->Message . '</div>';
			$msg .= '</div>';
			
			$msgs[] = $msg;
			
			$ii++;
		}
	}
	else if( isset( $q2 ) && ( $m = $database->fetchObjectRow ( $q2 ) ) )
	{
		$notify = ( $uonl && $m->ReceiverID == $webuser->ContactID && $m->IsNoticed == '0' ? '1' : '' );
		
		// If is read
		$read = ( $m->IsRead == '1' && $m->ReceiverID == $webuser->ContactID ? '1' : '0' );
		
		// If last message is sent to current user and is read and datemodified is over 2min in delay from date set seen date
		$seen = ( $m->IsRead == '1' && $m->SenderID == $webuser->ContactID && $m->DateModified > 0 ? ( 'Seen ' . date( 'D H:i', strtotime( $m->DateModified ) ) ) : '' );
		
		
		
		if( $notify && ( date( 'YmdHi' ) - date( 'YmdHi', strtotime( $m->Date ) ) ) > 10 )
		{
			$notify = '0';
		}
	}
	
	//die( $q1 . ' -- ' . print_r( $sm,1 ) );
	
	if( isset( $_REQUEST[ 'component' ] ) && $_REQUEST[ 'component' ] == 'chat' )
	{
		// Output splittable "array" with index list :
		output( 'ok<!--separate-->' .                									// 0
			( $msgs ? implode( '<!--message-->', $msgs ) : '' ) . '<!--separate-->' .  	// 1
			( $uonl ? 1 : 0 ) . '<!--separate-->' .  									// 2 online status
			$notify . '<!--separate-->' .            									// 3 whether user is notified
			$lastmessage . '<!--separate-->' .       									// 4 last message id
			$alert . '<!--separate-->' .             									// 5 to alert user
			$seen . '<!--separate-->' .              									// 6 is the message seen
			$sc->ID . '<!--separate-->' .            									// 7 contact id
			$sc->Image . '<!--separate-->' .      										// 8 contact image
			$read                              											// 9 whether user has read
		);
	}
	
	if( $limit && $msgs )
	{
		$mstr .= '<div id="Chat_' . $cid . '" class="open">';
		$mstr .= '<div id="Chat_inner_' . $cid . '" class="messages">';
		$mstr .= '<div id="Chat_Messages_' . $cid . '" class="inner"><ul>';
		
		for( $l = $limit; $l >= 0; $l-- )
		{
			if( $msgs[$l] )
			{
				$mstr .= '<li class="line_' . $l . '">' . $msgs[$l] . '</li>';
			}
		}
		
		if( $seen )
		{
			$mstr .= '<li class="line_info">';
			$mstr .= '<span class="icon"></span>';
			$mstr .= '<span class="info">' . $seen . '</span>';
			$mstr .= '</li>';
		}
		
		$mstr .= '</ul></div>';
		$mstr .= '</div>';
		$mstr .= '<div class="post"><textarea onkeyup="chatObject.sendMessage(\'' . $cid . '\',this,event)"></textarea></div>';
		$mstr .= '<div class="toolbar"><input name="crypto" type="checkbox" value="1" onclick="chatObject.saveChatSettings(this)"><span> crypto</span></div>';
		$mstr .= '</div>';
	}
}

if( isset( $_REQUEST[ 'component' ] ) && $_REQUEST[ 'component' ] == 'chat' )
{
	output( 'fail' );
}

?>
