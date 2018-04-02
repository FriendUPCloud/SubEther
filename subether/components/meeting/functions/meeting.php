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

if( $folder && $folder->CategoryID > 0 )
{
	$fid = $folder->CategoryID;
	
	/* --- Participants --- */
	
	$pstr = ''; $wstr = '';
	if( $members = getSBookGroupMembers( $fid ) )
	{
		foreach( $members as $m )
		{
			// get user status
			//$us = checkUserStatus( $m->ObjectID );
			$us = IsUserOnline ( $m->ObjectID, $fid, $parent->module, 'meeting' );
			
			if( $us )
			{
				// get online user
				$u = new dbObject( 'SBookContact' );
				$u->UserID = $m->ObjectID;
				$u->load();
				// render online user
				$pstr .= '<li>';
				$pstr .= '<div>';
				$pstr .= '<span><div class="image">';
				$img = new dbImage ();
				if( $img->Load( $u->ImageID ) )
				{
					$pstr .= $img->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
				}
				$pstr .= '</div></span>';
				$pstr .= '<span>' . $u->Username . '</span>';
				$pstr .= '<span></span>';
				$pstr .= '</div>';
				$pstr .= '</li>';
				
				// render webcam / or picture for user
				$cam = new dbImage ();
				$cam->Load( $u->ImageID );
				$wstr .= '<div style="background-image:url( ' . $cam->getImageUrl ( 152, 152, 'framed', false, 0xffffff ) . ' )" class="webcam">';
				$wstr .= '<div class="username">' . $u->Username . '</div>';
				$wstr .= '</div>';
			}
		}
		$wstr .= '<div class="clearboth"></div>';
	}
	
	/* --- Messages --- */
	
	// First time we get the messages
	$im = new dbObject ( 'SBookChat' );
	$q = 'SELECT * FROM SBookChat WHERE Type="meeting" AND CategoryID=\'' . $fid . '\' ORDER BY ID DESC LIMIT 50';
	if ( $q && $im = $im->find ( $q ) )
	{
		$mstr = '';
		foreach( $im as $m )
		{
			// get message sender
			$u = new dbObject ( 'SBookContact' );
			$u->UserID = $m->SenderID;
			$u->load();
			// render message
			$mstr .= '<div class="chatmessage">';
			$mstr .= '<span class="time">[' . date( 'H:i', strtotime( $m->Date ) ) . ']</span>';
			$mstr .= '<span class="user"> &lt;' . $u->Username . '&gt; </span>';
			$mstr .= '<span class="message">' . $m->Message . '</span>';
			$mstr .= '</div>';
		}
	}
	
	if( isset( $_POST[ 'getmessages' ] ) ) die( 'ok<!--separate-->' . $mstr . '<!--separate-->' . $pstr . '<!--separate-->' . $wstr );
	
}

?>
