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

// Chat Settings
$c = new dbObject( 'SBookContact' );
$c->UserID = $webuser->ID;
if( $c->Load() )
{
	$c->Data = json_decode( $c->Data );
}

if( isset( $_POST[ 'u' ] ) )
{
	$q = '
		SELECT 
			m.* 
		FROM 
			SBookMail m, 
			SBookContact c 
		WHERE 
			(	m.SenderID = \'' . $webuser->ID . '\' 
			AND m.ReceiverID = \'' . $_POST[ 'u' ] . '\'
			AND c.UserID = m.ReceiverID ) 
			OR 
			(	m.SenderID = \'' . $_POST[ 'u' ] . '\' 
			AND m.ReceiverID = \'' . $webuser->ID . '\'
			AND c.UserID = m.SenderID ) 
		ORDER BY 
			m.ID ASC 
	';
	
	$str = '';
	if( $sm = $database->fetchObjectRows ( $q ) )
	{
		$str = '<div id="Chat_Messages_' . $_POST[ 'u' ] . '" class="inner"><ul>';
		foreach( $sm as $m )
		{
			$u = new dbObject( 'Users' );
			if( $u->Load( $m->SenderID ) )
			{
				$str .= '<li><div>';
				$str .= '<span><div class="image">';
				$i = new dbImage ();
				if( $i->load( $u->Image ) )
				{
					//$str .= $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
					$str .= '<img style="background-image:url(' . $i->getImageURL ( 30, 28, 'framed', false, 0xffffff ) . ');">';
				}
				$str .= '</div></span>';
				$str .= '<span><div>' . $u->Name . '</div><div>' . renderSmileys( stripslashes( $m->Message ) ) . '</div></span>';
				$str .= '<span><div class="time">' . ( $m->Date > 0 ? TimeToHuman( $m->Date, 'medium' ) : '' ) . '</div></span>';
				$str .= '</div></li>';
			}
		}
		$str .= '</ul></div>';
	}
	
	if( $us = IsUserOnline( $_POST[ 'u' ] ) )
	{
		$time = TimeToHuman( $us->LastActivity, 'mini' );
		if( strstr( $time, 'm' ) && str_replace( 'm' , '', $time ) > 4 )
		{
			$us = '';
		}
	}
	
	$u = new dbObject( 'Users' );
	$u->Load( $_POST[ 'u' ] );
	
	if( !isset( $_REQUEST[ 'global' ] ) && isset( $_REQUEST[ 'function' ] ) ) die( 'ok<!--separate-->' . $str . '<!--separate-->' . ( $us ? 1 : 0 ) . '<!--separate-->' . ( $us && $m->ReceiverID == $webuser->ID && $m->IsNoticed == '0' ? 1 : 0 ) );
}
else
{
	if( $contacts = getContacts( 'Users', $webuser->ID, false, $_POST[ 'q' ] ) )
	{
		$array = array();
		
		$ii = 0; $k = 10; $mode = 'default';
		$str = '<div class="inner"><ul>';
		foreach( $contacts as $cs )
		{
			if( !ContactRelation( $cs->UserID, $webuser->ID, true ) ) continue;
			
			$title = ''; $time = ''; $us = ''; $con = '';
			$status = 'admin/gfx/icons/bullet_white.png';
			if( $us = IsUserOnline( $cs->UserID ) )
			{
				$time = TimeToHuman( $us->LastActivity, 'mini' );
				//die( ( date( 'Hi' ) - date( 'Hi', strtotime( $us->LastActivity ) ) ) . ' ..' );
				//if( strstr( $time, 'm' ) && str_replace( 'm' , '', $time ) > 4 )
				if( ( date( 'Hi' ) - date( 'Hi', strtotime( $us->LastActivity ) ) ) > 4 )
				{
					$us = '';
				}
				else
				{
					$time = '';
					$title = ( $us->CategoryID > 0 ? getCategoryByID( $us->CategoryID )->Name : '' );
					$status = 'admin/gfx/icons/bullet_green.png';
					$ii++;
				}
			}
			$onclick = 'addPrivChat( \'' . $cs->UserID . '\', \'' . $cs->Username . '\', \'' . ( $us ? 1 : 0 ) . '\', \'' . $mode . '\' )';
			if( $c->Data && $c->Data->Settings->Chat == '1' )
			{
				$onclick = 'openWindow( \'Chat\', \'' . $cs->UserID . '\', \'chatwindow\', function(){ openPrivChat( \'' . $cs->UserID . '\', \'' . $cs->Username . '\', \'window\' ); } );openChat();';
			}
			$con .= '<li><div ' . ( $title ? ( 'title="idling at ' . $title . '"' ) : '' ) . '><a href="javascript:void(0)" onclick="' . $onclick . '">';
			$con .= '<span><div class="image">';
			$i = new dbImage ();
			if( $i->load( $cs->ImageID ) )
			{
				//$con .= $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
				$con .= '<img style="background-image:url(' . $i->getImageURL ( 30, 28, 'framed', false, 0xffffff ) . ');">';
			}
			$con .= '</div></span>';
			$con .= '<span>' . ( $cs->DisplayName ? $cs->DisplayName : $cs->Username ) . '</span>';
			$con .= '<span><div class="status">' . $time . ' <img src="' . $status . '"></div></span>';
			$con .= '</a></div></li>';
			
			$key = ( $us ? ( '1' . $k++ ) : ( $time ? ( '2' . $k++ ) : ( '3' . $k++ ) ) );
			$array[$key] = $con;
		}
		ksort( $array );
		//die( print_r( $array,1 ) );
		$str .= implode( $array );
		$str .= '</ul></div>';
		
		if( isset( $_REQUEST[ 'function' ] ) ) die( 'ok<!--separate-->' . $str . '<!--separate-->' . $ii );
	}
	if( isset( $_REQUEST[ 'function' ] ) ) die( 'fail' );
}

?>
