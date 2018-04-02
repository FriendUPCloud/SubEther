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
$limit = $_POST[ 'limit' ] ? $_POST[ 'limit' ] : 300;

if( isset( $_POST[ 'userid' ] ) || isset( $cid ) )
{
	$_POST[ 'userid' ] ? ( $cid = $_POST[ 'userid' ] ) : '';
	
	// TODO: update notification to support cid
	$sc = new dbObject( 'SBookContact' );
	$sc->Load( $cid );
	
	$q = '
		SELECT 
			m.*, c.ID AS PosterID, c.ImageID, c.Username, i.Filename, f.DiskPath
		FROM 
			SBookMail m, 
			SBookContact c 
				LEFT JOIN Image i ON ( c.ImageID = i.ID ) 
				LEFT JOIN Folder f ON ( i.ImageFolder = f.ID )
		WHERE 
				( ( m.SenderID = \'' . $webuser->ContactID . '\' AND m.ReceiverID = \'' . $sc->ID . '\' ) 
			OR  ( m.SenderID = \'' . $sc->ID . '\' AND m.ReceiverID = \'' . $webuser->ContactID . '\' ) ) 
			AND c.ID = m.SenderID 
			AND m.Type = "im" 
			AND m.Message != "" 
			' . ( $_POST[ 'lastmessage' ] > 0 ? 'AND m.ID > \'' . $_POST[ 'lastmessage' ] . '\' ' : '' ) . ' 
		ORDER BY 
			m.ID DESC
		LIMIT ' . $limit . '
	';
	
	$mstr = '';
	
	if( $sm = $database->fetchObjectRows ( $q ) )
	{
		// Get all display names
		$carr = array();
		foreach( $sm as $c )
		{
			$carr[] = $c->PosterID;
		}
		$displaynames = GetUserDisplayname( $carr );
		
		$ii = 0; $lastmessage = '';
		
		foreach( $sm as $m )
		{
			$m->Date = ( TimeToHuman( $m->Date, 'medium' ) ? TimeToHuman( $m->Date, 'medium' ) : date( 'g:sa', strtotime( $m->Date ) ) );
			
			// Set lastmessage
			if( $ii == 0 )
			{
				$lastmessage = $m->ID;
			}
			
			$img = str_replace( " ", "%20", $m->DiskPath . $m->Filename );
			$img = $m->Filename ? ( '<img src="' . $img . '" style="background-image:url(' . $img . ');"/>' ) : '<img src="admin/gfx/arenaicons/user_johndoe_32.png" style="background-image:url(admin/gfx/arenaicons/user_johndoe_32.png"/>';
			
			$mstr .= '<div class="message">';
			$mstr .= '<div class="image">';
			$mstr .= $img;
			$mstr .= '</div>';
			$mstr .= '<div class="name">' . $displaynames[$m->PosterID] . '</div>';
			$mstr .= '<div class="content">' . renderSmileys( stripslashes( makeLinks( $m->Message ) ) ) . '</div>';
			$mstr .= '<div class="time">' . $m->Date . '</div>';
			$mstr .= '</div><!--message-->';
			
			// Set messages to IsRead
			$s = new dbObject( 'SBookMail' );
			if( $s->Load( $m->ID ) )
			{
				$s->IsRead = 1;
				$s->Save();
			}
			
			$ii++;
		}
	}
	
	if( isset( $_REQUEST[ 'function' ] ) ) die( 'ok<!--separate-->' . $mstr . '<!--separate-->' . $lastmessage );
}
else
{
	$q = '
		SELECT 
			m.*, c.ID AS PosterID, c.ImageID, c.Username, i.Filename, f.DiskPath
		FROM 
			SBookMail m, 
			SBookContact c 
				LEFT JOIN Image i ON ( c.ImageID = i.ID ) 
				LEFT JOIN Folder f ON ( i.ImageFolder = f.ID )
		WHERE 
				( ( m.SenderID = \'' . $webuser->ContactID . '\' ) 
			OR  ( m.ReceiverID = \'' . $webuser->ContactID . '\' ) ) 
			AND c.ID = m.SenderID 
			AND m.Type = "im" 
			AND m.Message != "" 
		ORDER BY 
			m.Date DESC 
	';
	
	$str = '';
	
	if( $sm = $database->fetchObjectRows ( $q ) )
	{
		$mes = array(); $uid = array(); $img = array();
		
		foreach( $sm as $m )
		{
			$id = $m->SenderID == $webuser->ContactID ? $m->ReceiverID : $m->SenderID;
			
			if( !in_array( $id, $uid ) && $id != $webuser->ContactID )
			{
				$mes[] = $m;
				$uid[] = $id;
			}
		}
		
		if( count( $uid ) > 0 && ( $con = $database->fetchObjectRows( '
			SELECT 
				c.ID AS PosterID, c.ImageID, c.Username, c.NodeID, c.NodeMainID, i.Filename, f.DiskPath
			FROM 
				SBookContact c 
					LEFT JOIN Image i ON ( c.ImageID = i.ID ) 
					LEFT JOIN Folder f ON ( i.ImageFolder = f.ID )
			WHERE 
				c.ID IN (' . implode( ',', $uid ) . ') 
			ORDER BY 
				c.ID DESC 
		' ) ) )
		{
			foreach( $con as $c )
			{
				$im = str_replace( " ", "%20", $c->DiskPath . $c->Filename );
				$img[$c->PosterID] = $c->NodeID == 0 && $c->Filename ? ( '<img src="' . $im . '" style="background-image:url(' . $im . ');"/>' ) : '<img src="admin/gfx/arenaicons/user_johndoe_32.png" style="background-image:url(admin/gfx/arenaicons/user_johndoe_32.png"/>';
			}
		}
		
		$displaynames = GetUserDisplayname( $uid );
		
		$i = 0; $cid = '';
		
		$str = '<div class="inner"><ul>';
		
		foreach( $mes as $m )
		{
			$id = $m->SenderID == $webuser->ContactID ? $m->ReceiverID : $m->SenderID;
			
			if( $i == 0 )
			{
				$cid = $id;
			}
			if( $parent->url[3] && is_numeric( $parent->url[3] ) && $parent->url[3] == $id )
			{
				$cid = $id;
			}
			
			$isread = $m->IsRead;
			if ( $m->SenderID ) $isread = 1;
			$str .= '<li id="Message_' . $id . '" class="' . ( $isread == 0 ? 'NotRead ' : '' ) . ( $_POST[ 'init' ] != '1' && $i == 0 ? 'current' : '' ) . '">';
			$str .= '<div class="contact"><a href="javascript:void(0)" onclick="openMessage(' . $id . ')">';
			$str .= '<div class="image">';
			$str .= ( isset( $img[$id] ) ? $img[$id] : '' );
			$str .= '</div>';
			$str .= '<div class="name">' . $displaynames[$id] . '</div>';
			$str .= '<div class="content">' . dotTrim( $m->Message, 25 ) . '</div>';
			$str .= '<div class="time">' . ( $m->Date > 0 ? TimeToHuman( $m->Date, 'day' ) : '' ) . '</div>';
			$str .= '</a></div></li>';
			$i++;
		}
		$str .= '</ul></div>';
	}
	
	if( isset( $_REQUEST[ 'function' ] ) ) die( 'ok<!--separate-->' . $str );
}

?>
