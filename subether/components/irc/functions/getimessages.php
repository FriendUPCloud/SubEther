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

$limit = 2500;

if( isset( $_REQUEST['pid'] ) || isset( $folder->CategoryID ) )
{	
	$_REQUEST['pid'] > 0 ? ( $pid = $_REQUEST['pid'] ) : ( $pid = $folder->CategoryID );

	/* --- User Status --- */
	
	$cl = '';
	// Get all member relations
	if( $members = getSBookGroupMembers ( $pid ) )
	{	
		foreach( $members as $m )
		{
			// get user status (ObjectID is UserID)
			//$us = checkUserStatus( $m->ObjectID, false, false, $pid );
			$us = IsUserOnline ( $m->ObjectID, $pid, $parent->module, 'irc' );
			$afk = IsUserAFK ( $m->ObjectID, 'irc', $pid );
			
			/*if( $us && $us[0] == 'online' && $us[1]->ObjectID == $pid )
			{*/
			if( $us )
			{
				if( $webuser->ID == $m->ObjectID )
				{
					chatStatusMessage ( $webuser->ID, $pid, 'joins' );
				}
				// get online user
				$u = new dbObject( 'SBookContact' );
				$u->UserID = $m->ObjectID;
				$u->load();
				// get group permissions for user
				$r = new dbObject ( 'SBookCategoryRelation' );
				$r->ObjectType = 'Users';
				$r->CategoryID = $pid;
				$r->ObjectID = $u->UserID;
				$r->load();
				if( $afk )
				{
					$u->Username = $u->Username . '(afk)';
				}
				// render online user
				$cl .= '<div class="onlineuser" onclick="addPrivChat( \'' . $u->UserID . '\', \'' . $u->Username . '\', \'1\', \'open\' )">';
				if( $r->Permission == 'owner' )
				{
					$cl .= '<span class="user"><span class="owner">@</span>' . $u->Username . '</span>';
				}
				else if( $r->Permission == 'admin' )
				{
					$cl .= '<span class="user"><span class="admin">@</span>' . $u->Username . '</span>';
				}
				else if( $r->Permission == 'moderator' )
				{
					$cl .= '<span class="user"><span class="moderator">+</span>' . $u->Username . '</span>';
				}
				else
				{
					$cl .= '<span class="user">' . $u->Username . '</span>';
				}
				$cl .= '</div>';
			}
			else/* if( $us && ( $us[0] == 'offline' || $us[1]->ObjectID != $pid ) )*/
			{
				chatStatusMessage( $m->ObjectID, $pid, 'quits' );
			}
		}
	}
	
	/* --- Messages --- */
	
	// First time we get the messages
	$init = false;
	$im = new dbObject ( 'SBookChat' );
	if ( ( isset ( $_REQUEST['init'] ) && $_REQUEST['init'] == 'yes' ) || $firstload == 'yes' )
	{
		$q = '
		SELECT * FROM 
		SBookChat 
		WHERE 
			    Type="im" AND CategoryID=\'' . $pid . '\' 
			AND Message != "" 
		ORDER BY 
			ID DESC 
		LIMIT ' . $limit . '
		';
	}
	// Next time, we only ask next new message
	else if ( $_REQUEST['lastmessage'] > 0 )
	{
		$q = '
		SELECT * FROM 
		SBookChat 
		WHERE 
			    Type="im" AND CategoryID=\'' . $pid . '\' 
			AND ID > \'' . $_REQUEST['lastmessage'] . '\'
			AND Message != "" 
		ORDER BY 
			ID DESC 
		LIMIT ' . $limit . '
		';
	}
	else $q = false;
	if ( $q && ( $im = $im->find ( $q ) ) )
	{
		$lm = '0';
		$imstr = '';
		
		$ids = array();
		
		// Buffer over users
		$userz = array ();
		foreach( $im as $m )
		{
			if ( !isset ( $m->Message ) || !trim ( $m->Message ) ) continue;
			
			// FIXME: Dette boker chat'en (IRC chat'en) på øæå
			if( !$m->Status )
			{
				//$m->Message = str_replace ( array ( '<', '>' ), ( '&lt;', '&gt;' ), $m->Message );
				//$m->Message = htmlentities( utf8_decode( $m->Message ), ENT_QUOTES );
			}
			
			// get message sender
			$u = new dbObject ( 'SBookContact' );
			$u->UserID = $m->SenderID;
			$u->load();
			
			// get group permissions on sender
			$r = new dbObject ( 'SBookCategoryRelation' );
			$r->ObjectType = 'Users';
			$r->CategoryID = $pid;
			$r->ObjectID = $u->UserID;
			$r->load();
			
			// Fix links
			if ( preg_match ( '/(http\:\/\/[^\s]*)/i', $m->Message, $mz ) || preg_match ( '/(https\:\/\/[^\s]*)/i', $m->Message, $mz ) )
			{
				$m->Message = str_replace ( $mz[0], ' <a target="_blank" href="' . $mz[0] . '">' . $mz[0] . '</a>', $m->Message );
			}
			
			// render message
			$imstr .= '<div class="instantmessage' . ( $m->Status ? ' ' . $m->Status : '' ) . '">';
			
			$imstr .= '<span class="time">[' . date( 'H:i', strtotime( $m->Date ) ) . ']</span>';
			
			if ( $m->Status == 'command' )
			{
				$imstr .= '&nbsp;<span class="message">' . $m->Message . '</span>';
			}
			else
			{
				// Fix bold
				if( $m->Message )
				{
					$m->Message = str_replace( '[highlight]', '<span class="highlight">', $m->Message );
					$m->Message = str_replace( '[/highlight]', '</span>', $m->Message );
				}
				
				if( $r->Permission == 'owner' )
				{
					$permission = '<span class="owner">@</span>';
				}
				else if( $r->Permission == 'admin' )
				{
					$permission = '<span class="admin">@</span>';
				}
				else if( $r->Permission == 'moderator' )
				{
					$permission = '<span class="moderator">+</span>';
				}
				else
				{
					$permission = '';
				}
				$imstr .= '<span class="user"> ' . ( $m->Status ? '*** ' : '&lt;' ) . $permission . $u->Username . ( $m->Status ? '' : '&gt;' ) . ' </span>';
				$imstr .= '<span class="message">' . $m->Message . '</span>';
			}
			
			$imstr .= '</div><!--message-->';
			
			$ids[] = $m->ID;
		}
		$lm = $im[0]->ID;
		
		// Set topic if found
		if( $topic = $database->fetchObjectRow( 'SELECT * FROM SBookChat WHERE Type = "topic" AND CategoryID = \'' . $pid . '\' ORDER BY ID DESC' ) )
		{
			/*// get message sender
			$u = new dbObject ( 'SBookContact' );
			$u->UserID = $topic->SenderID;
			$u->load();
			
			// render topic
			$imstr .= '<div class="instantmessage' . ( $topic->Status ? ' ' . $topic->Status : '' ) . '">';
			$imstr .= '<span class="time">[' . date( 'H:i', strtotime( $topic->Date ) ) . ']</span>';
			$imstr .= '<span class="message"> *** Set by ' . $u->Username . ' on ' . date( 'D M j H:i:s', strtotime( $topic->Date ) ) . '</span>';
			$imstr .= '</div><!--message-->';
			
			$imstr .= '<div class="instantmessage' . ( $topic->Status ? ' ' . $topic->Status : '' ) . '">';
			$imstr .= '<span class="time">[' . date( 'H:i', strtotime( $topic->Date ) ) . ']</span>';
			$imstr .= '<span class="message"> *** Topic is "' . $topic->Message . '"</span>';
			$imstr .= '</div><!--message-->';*/
		}
		
		if( isset( $_REQUEST['init'] ) && $_REQUEST['init'] == 'yes' && $ids )
		{
			// Delete old rows
			$database->query ( '
			DELETE FROM 
				SBookChat 
			WHERE 
					Type="im" AND CategoryID=\'' . $pid . '\' 
				AND Message != "" AND ID NOT IN ( ' . implode( ',', $ids ) . ' )
			ORDER BY 
				ID DESC 
			' );
		}
		
		// Strip last message divider
		$imstr = substr ( $imstr, 0, strlen ( $imstr ) - strlen ( '<!--message-->') );
		
		if( $_REQUEST[ 'function' ] == 'getimessages' ) 
		{
			die ( 
				'ok<!--separate-->' . 
				$imstr . '<!--separate-->' . 
				$lm . '<!--separate-->' . 
				$cl . '<!--separate-->' . 
				checkAudioNotifications ( $webuser->ID, $pid ) 
			);
		}
	}
	else if ( $_REQUEST[ 'function' ] == 'getimessages' ) 
	{
		die ( 'fail<!--separate-->' );
	}
}

?>
