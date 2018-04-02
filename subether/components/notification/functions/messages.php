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

//$limit = 50;

// Chat Settings
$wu = new dbObject( 'SBookContact' );
$wu->UserID = $webuser->ID;
if( $wu->Load() )
{
	$wu->Data = json_decode( $wu->Data );
}

// Build a list of all contacts
$q = array(); $usrs = array(); $msgs = array();

if( $contacts = $database->fetchObjectRows( '
	SELECT 
		c.ID, 
		MAX(m.ID) AS MessageID 
	FROM 
		SBookMail m, 
		SBookContact c 
	WHERE 
		( 
			( 
					m.ReceiverID = ' . $webuser->ContactID . ' 
				AND c.ID = m.SenderID 
			) 
			OR 
			( 
					m.SenderID = ' . $webuser->ContactID . ' 
				AND m.Type != "cm" 
				AND c.ID = m.ReceiverID 
			) 
		) 
		AND m.Message != "" 
		AND m.SenderID > 0 
		AND m.ReceiverID > 0 
	GROUP BY 
		c.ID 
' ) )
{
	
	// Loop through all contacts
	foreach( $contacts as $cd )
	{
		$usrs[$cd->ID] = $cd->ID;
		$msgs[$cd->ID] = $cd->MessageID;
	}
	
	$q = '
		SELECT 
			c.ID, 
			c.UserID, 
			c.Username, 
			m.Message, 
			m.Date, 
			m.ID AS MessageID, 
			m.SenderID, 
			m.ReceiverID, 
			m.IsRead, 
			i.ID AS ImageID, 
			i.UniqueID AS ImageUniqueID, 
			i.Filename, 
			f.DiskPath 
		FROM 
			SBookMail m, 
			SBookContact c 
				LEFT JOIN Image i ON 
				( 
					i.ID = c.ImageID 
				) 
				LEFT JOIN Folder f ON 
				( 
					i.ImageFolder = f.ID 
				) 
		WHERE 
				m.ID IN ( ' . ( $msgs ? implode( ',', $msgs ) : 'NULL' ) . ' ) 
			AND m.Message != "" 
			AND m.SenderID > 0 
			AND m.ReceiverID > 0 
			AND
			( 
				( 
						m.ReceiverID = ' . $webuser->ContactID . ' 
					AND c.ID = m.SenderID 
				) 
				OR 
				( 
						m.SenderID = ' . $webuser->ContactID . ' 
					AND m.Type != "cm" 
					AND c.ID = m.ReceiverID 
				) 
			) 
		ORDER BY
			m.Date DESC 
	';
}


$str = '';
if( $sm = $database->fetchObjectRows ( $q ) )
{
	$sids = array();
	
	$unam = GetUserDisplayname( $usrs );
	
	if ( $usr = $database->fetchObjectRows ( '
		SELECT
			UserID
		FROM
			SBookContact
		WHERE
			ID IN ( ' . implode( ',', $usrs ) . ' )
		ORDER BY
			ID ASC
	' ) ) 
	{
		$usrs = array();
		
		foreach ( $usr as $u )
		{
			$usrs[$u->UserID] = $u->UserID;
		}
	}
	
	$uonl = IsUserOnline( $usrs );
	
	$str = '<div class="inner"><ul>';
	foreach( $sm as $m )
	{
		// Assign some extra vars
		$m->Date = ( TimeToHuman( $m->Date, 'medium' ) ? TimeToHuman( $m->Date, 'medium' ) : date( 'H:i', strtotime( $m->Date ) ) );
		
		// If mobile version
		if( $parent && $parent->agent && $parent->agent != 'web' && $parent->agent != 'browser' )
		{
			$onclick = false;
		}
		// TODO: Clean up the old code, new code is working and old is obsolete ...
		//else if( !defined( 'CHAT_VERSION' ) )
		//{
		//	$onclick = 'addPrivChat( \'' . $m->ID . '\', \'' . $m->Username . '\', \'' . ( isset( $uonl[$m->UserID] ) && $uonl[$m->UserID] ? 1 : 0 ) . '\', \'default\' );return false;';
		//	if( $wu->Data && $wu->Data->Settings->Chat == '1' )
		//	{
		//		$onclick = 'openWindow( \'Chat\', \'' . $m->UserID . '\', \'chatwindow\', function(){ openPrivChat( \'' . $m->ID . '\', \'' . $m->Username . '\', \'window\' ); } );closeNotificationBox();return false;';
		//	}
		//}
		else
		{
			$onclick = 'chatObject.addPrivateChat( \'' . $m->ID . '\', \'' . $m->Username . '\' );return false;';
			// TODO: Remove or implement!
			if( $wu->Data && $wu->Data->Settings->Chat == '1' )
			{
				$onclick = 'openWindow( \'Chat\', \'' . $m->UserID . '\', \'chatwindow\', function(){ openPrivChat( \'' . $m->ID . '\', \'' . $m->Username . '\', \'window\' ); } );closeNotificationBox();return false;';
			}
		}
		$str .= '<li ' . ( $m->IsRead == 0 ? 'class="NotRead"' : '' ) . '><div><a href="en/home/messages/' . $m->ID . '" ' . ( $onclick ? 'onclick="' . $onclick . '"' : '' ) . '>';
		//$str .= '<span><div class="image">';
		$str .= '<span>';
		
		if ( !FileExists( ( BASE_URL . 'secure-files/images/' . ( $m->ImageUniqueID ? $m->ImageUniqueID : $m->ImageID ) . '/' ) ) )
		{
			$m->DiskPath = false;
		}
		
		if( $m->ImageID > 0 && $m->DiskPath && $m->Filename )
		{
			//$img = $m->DiskPath . '/' . $m->Filename;
			$img = ( BASE_URL . 'secure-files/images/' . ( $m->ImageUniqueID ? $m->ImageUniqueID : $m->ImageID ) . '/' );
			//$str .= '<img src="' . $img . '" style="background-image: url(\'' . $img . '\')"/>';
			$str .= '<div class="image" style="background-image: url(\'' . $img . '\')"></div>';
		}
		else
		{
			//$str .= '<img src="admin/gfx/arenaicons/user_johndoe_32.png" style="background-image:url(admin/gfx/arenaicons/user_johndoe_32.png"/>';
			$str .= '<div class="image" style="background-image:url(\'admin/gfx/arenaicons/user_johndoe_32.png\')"></div>';
		}
		$str .= '<span>';
		//$str .= '</div></span>';
		$str .= '<span><div class="name">' . ( isset( $unam[$m->ID] ) && is_string( $unam[$m->ID] ) ? $unam[$m->ID] : $m->Username ) . '</div><div>' . renderSmileys( dotTrim( $m->Message, 25 ) ) . '</div></span>';
		$str .= '<span><div class="time">' . $m->Date . '</div></span>';
		$str .= '</a></div></li>';
		
		//$usr = ( $usr ? ( $usr . ',' . $m->ID ) : $m->ID );
		
		if( $m->IsNoticed == 0 && !isset( $sids[$m->MessageID] ) )
		{
			$sids[$m->MessageID] = $m->MessageID;
		}
	}
	$str .= '</ul></div>';
	
	$str .= '<div class="all"><a href="en/home/messages/"><span>' . i18n( 'i18n_See All' ) . '</span></a></div>';
	
	// Set status IsNoticed on user notify
	IsNoticed( $sids );
}
die( 'ok<!--separate-->' . $str . '<!--separate-->' . ( $sids ? implode( ',', $sids ) : '' ) );

?>
