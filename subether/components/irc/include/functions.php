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

function parseHighlights ( $string, $cid = false )
{
	if( !$string ) return;

	if( setAudioNotification( 'pling', ( $user = getUserFromString( $string ) ), $cid ) )
	{
		$string = str_replace( ( checkUserPermission( $user, $cid ) . $user->Username ), $user->Username, $string );
		$string = str_replace( $user->Username, ( '[highlight]' . checkUserPermission( $user, $cid ) . $user->Username . '[/highlight]' ), $string );
		return strip_tags ( $string );
	}
	return strip_tags ( $string );
}

function parseCommands ( $mstr, $uid, $cid = false )
{
	$user = getUserFromID( $uid );
	list ( $command, ) = explode ( ' ', substr ( $mstr, 1, strlen ( $mstr ) - 1 ) );
	$command = strtolower ( trim ( $command ) );
	$string = trim ( substr ( $mstr, strlen($command)+1, strlen ( $mstr ) - (strlen($command)+1) ) );
	switch ( $command )
	{
		case 'me':
			return '<strong class="me">*** ' . checkUserPermission( $user, $cid ) . $user->Username . ' ' . strip_tags ( $string ) . '</strong>';
			break;
		case 'op':
			if( changePermission( 'admin', getUserFromString( $string ), $cid ) )
			{
				return '<strong class="op">*** ' . checkUserPermission( $user, $cid ) . $user->Username . ' sets mode +o ' . strip_tags ( $string ) . '</strong>';
			}
			return '';
			break;
		case 'deop':
			if( changePermission( '', getUserFromString( $string ), $cid ) )
			{
				return '<strong class="deop">*** ' . checkUserPermission( $user, $cid ) . $user->Username . ' sets mode -o ' . strip_tags ( $string ) . '</strong>';
			}
			return '';
			break;
		case 'voice':
			if( changePermission( 'moderator', getUserFromString( $string ), $cid ) )
			{
				return '<strong class="voice">*** ' . checkUserPermission( $user, $cid ) . $user->Username . ' sets mode +v ' . strip_tags ( $string ) . '</strong>';
			}
			return '';
			break;
		case 'devoice':
			if( changePermission( '', getUserFromString( $string ), $cid ) )
			{
				return '<strong class="devoice">*** ' . checkUserPermission( $user, $cid ) . $user->Username . ' sets mode -v ' . strip_tags ( $string ) . '</strong>';
			}
			return '';
		case 'invite':
			if( inviteUserByID( getUserFromString( $string ), $cid ) )
			{
				return '<strong class="invite">*** ' . checkUserPermission( $user, $cid ) . $user->Username . ' sets mode +i ' . strip_tags ( $string ) . '</strong>';
			}
			return '';
			break;
		case 'kick':
			if( kickUserByID( getUserFromString( $string ), $cid ) )
			{
				return '<strong class="kick">*** ' . checkUserPermission( $user, $cid ) . $user->Username . ' sets mode +k ' . strip_tags ( $string ) . '</strong>';
			}
			return '';
			break;
		case 'ping':
			if( setAudioNotification( 'pling', getUserFromString( $string ), $cid ) )
			{
				return '<strong class="ping">*** ' . checkUserPermission( $user, $cid ) . $user->Username . ' pings ' . strip_tags ( $string ) . '</strong>';
			}
			return '';
			break;
		case 'sound':
			if( setAudioNotification( strip_tags( $string ), '0', $cid ) )
			{
				return '<strong class="ping">*** ' . checkUserPermission( $user, $cid ) . $user->Username . ' plays a sound called ' . strip_tags ( $string ) . '</strong>';
			}
			return '';
			break;
		case 'topic':
			if( setTopic ( strip_tags ( $string ), $user, $cid ) )
			{
				return '<strong class="topic">*** ' . checkUserPermission( $user, $cid ) . $user->Username . ' changed topic to "' . strip_tags ( $string ) . '"</strong>';
			}
			return '';
			break;
		default: break;
	}
	return strip_tags ( $mstr );
}

function setTopic ( $message, $user, $cid )
{
	if( !$message || !$user || !$cid ) return false;
	
	$r = new dbObject( 'SBookCategoryRelation' );
	$r->ObjectType = 'Users';
	$r->CategoryID = $cid;
	$r->ObjectID = $user->UserID;
	if( $r->Load() && $r->Permission != '' )
	{
		if( $r->Permission == 'admin' || $r->Permission == 'owner' )
		{
			$m = new dbObject( 'SBookChat' );
			$m->Type = 'topic';
			$m->CategoryID = $cid;
			$m->Load();
			$m->SenderID = $user->UserID;
			$m->Message = $message;
			$m->Status = 'command';
			$m->Date = date( 'Y-m-d H:i:s' );
			$m->Save();
			
			return true;
		}
		else return false;
	}
	return false;
}

function changePermission ( $command, $user, $cid )
{
	global $webuser;
	
	if( !$user || !$cid ) return false;
	
	$a = new dbObject( 'SBookCategoryRelation' );
	$a->ObjectType = 'Users';
	$a->CategoryID = $cid;
	$a->ObjectID = $webuser->ID;
	
	$r = new dbObject( 'SBookCategoryRelation' );
	$r->ObjectType = 'Users';
	$r->CategoryID = $cid;
	$r->ObjectID = $user->UserID;
	if( $r->Load() && $a->Load() && $r->Permission != $command 
	&& ( $a->Permission == $command || ( $a->Permission == 'admin' && $r->Permission != 'owner' ) || $a->Permission == 'owner' ) )
	{
		$r->Permission = $command;
		$r->Save();
		return true;
	}
	return false;
}

function checkUserPermission ( $user, $cid )
{
	if( !$user || !$cid ) return false;
	
	$r = new dbObject( 'SBookCategoryRelation' );
	$r->ObjectType = 'Users';
	$r->CategoryID = $cid;
	$r->ObjectID = $user->UserID;
	if( $r->Load() && $r->Permission != '' )
	{
		if( $r->Permission == 'owner' )
		{
			return '<span class="owner">@</span>';
		}
		else if( $r->Permission == 'admin' )
		{
			return '<span class="admin">@</span>';
		}
		else if( $r->Permission == 'moderator' )
		{
			return '<span class="moderator">+</span>';
		}
		else return false;
		
	}
	return false;
}

function getUserFromString ( $string )
{
	global $database;
	
	if( !$string ) return false;
	
	if( $user = $database->fetchObjectRows( 'SELECT * FROM SBookContact ORDER BY ID DESC' ) )
	{
		foreach( $user as $u )
		{
			if( strstr( $u->Username, strip_tags( trim( $string ) ) ) )
			{
				return $u;
			}
		}
	}
	return false;
	
	/*if( $u = $database->fetchObjectRow( '
		SELECT * FROM SBookContact 
		WHERE Username = \'' . strip_tags( trim( $string ) ) . '\' 
		ORDER BY ID DESC LIMIT 1
	' ) )
	{
		return $u;
	}
	return false;*/
}

function getUserFromID ( $id )
{
	global $database;
	
	if( !$id ) return false;
	
	if( $u = $database->fetchObjectRow( '
		SELECT * FROM SBookContact 
		WHERE UserID = \'' . $id . '\' 
		ORDER BY ID DESC LIMIT 1
	' ) )
	{
		return $u;
	}
	return false;
}

function inviteUserByID ( $user, $cid )
{
	if( !$user || !$cid ) return false;
	
	$c = new dbObject( 'SBookCategoryRelation' );
	$c->ObjectType = 'Users';
	$c->CategoryID = $cid;
	$c->ObjectID = $user->UserID;
	if( !$c->Load() )
	{
		$c->Save();
		if( $c->ID > 0 ) return true;
		else return false;
	}
	return false;
}

function kickUserByID ( $user, $cid )
{
	global $webuser;
	
	if( !$user || !$cid ) return false;
	
	$a = new dbObject( 'SBookCategoryRelation' );
	$a->ObjectType = 'Users';
	$a->CategoryID = $cid;
	$a->ObjectID = $webuser->ID;
	
	$c = new dbObject( 'SBookCategoryRelation' );
	$c->ObjectType = 'Users';
	$c->CategoryID = $cid;
	$c->ObjectID = $user->UserID;
	if( $c->Load() && $a->Load() && ( ( $a->Permission == 'admin' && $c->Permission != 'owner' ) || $a->Permission == 'owner' ) )
	{
		$c->Delete();
		return true;
	}
	return false;
}

function setAudioNotification ( $command, $user, $cid )
{
	global $webuser;
	
	if( $user == '' || !$cid ) return false;

	$n = new dbObject( 'SBookNotification' );
	$n->Type = 'irc';
	$n->Command = trim( $command );
	$n->ObjectID = $cid;
	$n->SenderID = $webuser->ID;
	$n->ReceiverID = ( $user == '0' ? '0' : $user->UserID );
	$n->Load();
	$n->IsNoticed = '1';
	$n->Save();
	
	return true;
}

function checkAudioNotifications ( $uid, $cid )
{
	if( !$uid || !$cid ) return false;
	
	$n = new dbObject( 'SBookNotification' );
	$n->Type = 'irc';
	$n->ObjectID = $cid;
	$n->ReceiverID = $uid;
	$n->IsNoticed = '1';
	if( $n->Load() && $n->Command != '' )
	{
		return $n->Command;
	}
	$n->ReceiverID = '0';
	if( $n->Load() && $n->Command != '' )
	{
		return $n->Command;
	}
	return false;
}

?>
