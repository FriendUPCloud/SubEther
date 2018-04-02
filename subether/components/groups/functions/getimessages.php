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

function parseCommands ( $mstr, $user )
{
	die( $mstr . ' .. ' . $user->Username );
	list ( $command, ) = explode ( ' ', substr ( $mstr, 1, strlen ( $mstr ) - 1 ) );
	$command = strtolower ( trim ( $command ) );
	$string = trim ( substr ( $mstr, strlen($command)+1, strlen ( $mstr ) - (strlen($command)+1) ) );
	switch ( $command )
	{
		case 'me':
			return '<strong style="color: #aa0000">*** ' . $user->Username . ' ' . strip_tags ( $string ) . '</strong>';
			break;
		case 'op':
			return 'dette: ' . $mstr . ' | bruker: ' . $user->Username;
			break;
		default: break;
	}
	return strip_tags ( $mstr );
}


if( isset( $_REQUEST['pid'] ) )
{	
	$pid = $_REQUEST['pid'];

	/* --- User Status --- */
	
	$cl = '';
	if( $members = getSBookGroupMembers( $pid ) )
	{	
		foreach( $members as $m )
		{
			// get user status
			$us = checkUserStatus( $m->ObjectID );
			
			if( $us && $us[0] == 'online' && $us[1]->ObjectID == $pid )
			{
				if( $webuser->ID == $m->ObjectID )
				{
					chatStatusMessage( $webuser->ID, $pid, 'joins' );
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
				// render online user
				$cl .= '<div class="onlineuser">';
				$cl .= '<span class="user">' . ( $r->Permission == 'admin' || $r->Permission == 'owner' ? '<span class="admin">@</span>' : '' ) . $u->Username . '</span>';
				$cl .= '</div>';
			}
			else if( $us && $us[0] == 'offline' || $us[1]->ObjectID != $pid )
			{
				chatStatusMessage( $m->ObjectID, $pid, 'quits' );
			}
		}
	}
	
	/* --- Messages --- */
	
	// First time we get the messages
	$init = false;
	$im = new dbObject ( 'SBookChat' );
	if ( isset ( $_REQUEST['init'] ) && $_REQUEST['init'] == 'yes' )
	{
		$q = 'SELECT * FROM SBookChat WHERE Type="im" AND CategoryID=\'' . $pid . '\' ORDER BY ID DESC LIMIT 50';
	}
	// Next time, we only ask next new message
	else if ( $_REQUEST['lastmessage'] > 0 )
	{
		$q = 'SELECT * FROM SBookChat WHERE Type="im" AND CategoryID=\'' . $pid . '\' AND ID > \'' . $_REQUEST['lastmessage'] . '\' ORDER BY ID DESC LIMIT 50';
	}
	else $q = false;
	if ( $q && $im = $im->find ( $q ) )
	{
		$lm = '0';
		$imstr = '';
		// Buffer over users
		$userz = array ();
		foreach( $im as $m )
		{
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
			// render message
			$imstr .= '<div class="instantmessage' . ( $m->Status ? ' ' . $m->Status : '' ) . '">';
			$imstr .= '<span class="time">[' . date( 'H:i', strtotime( $m->Date ) ) . ']</span>';
			if ( $m->Message{0} == '/' )
				$m->Message = parseCommands ( $m->Message, $u );
			if ( $m->Message{0} == '<' )
			{
				$imstr .= '&nbsp;<span class="message">' . $m->Message . '</span>';
			}
			else
			{
				$imstr .= '<span class="user"> ' . ( $m->Status ? '*** ' : '&lt;' ) . ( $r->Permission == 'admin' || $r->Permission == 'owner' ? '<span class="admin">@</span>' : '' ) . $u->Username . ( $m->Status ? '' : '&gt;' ) . ' </span>';
				$imstr .= '<span class="message">' . $m->Message . '</span>';
			}
			$imstr .= '</div><!--message-->';
		}
		$lm = $im[0]->ID;
		
		if( $_REQUEST[ 'function' ] == 'getimessages' ) 
		{
			die ( 'ok<!--separate-->' . $imstr . '<!--separate-->' . $lm . '<!--separate-->' . $cl );
		}
	}
	else if ( $_REQUEST[ 'function' ] == 'getimessages' ) 
	{
		die ( 'fail<!--separate-->' );
	}
}

?>
