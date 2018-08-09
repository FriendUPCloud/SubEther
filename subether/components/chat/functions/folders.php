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

// Build a list of all contacts
$q = array(); $usrs = array(); $msgs = array(); $lmsg = true; $cid = ''; 

if( $parent && is_numeric( end( $parent->url ) ) )
{
	$cid = trim( end( $parent->url ) );
}

if( $_POST['cid'] )
{
	$cid = $_POST['cid'];
}

// Check for new messages if we have lastmessage id
if( $_POST['lastmessage'] > 0 && !$database->fetchObjectRow( '
	SELECT 
		m.ID 
	FROM 
		SBookMail m 
	WHERE 
			m.ID > ' . $_POST['lastmessage'] . ' 
		AND m.Type = "im" 
		AND m.Message != "" 
		AND m.SenderID > 0 
		AND m.ReceiverID > 0 
		AND ( m.SenderID = \'' . $webuser->ContactID . '\' 
		OR  m.ReceiverID = \'' . $webuser->ContactID . '\' ) 
	ORDER BY 
		ID DESC 
	LIMIT 1 
' ) )
{
	$lmsg = false;
}

if( $lmsg && $contacts = $database->fetchObjectRows( /*'
	SELECT
		DISTINCT(c.ID) `ID`
	FROM
		SBookMail m,
		SBookContact c
	WHERE
		(   ( m.SenderID = \'' . $webuser->ContactID . '\' AND c.ID = m.ReceiverID )
		OR  ( m.ReceiverID = \'' . $webuser->ContactID . '\' AND c.ID = m.SenderID ) ) 
		AND m.Type = "im" 
		AND m.Message != ""
		AND m.SenderID > 0
		AND m.ReceiverID > 0					 
'*/'
	SELECT 
		c.ID, 
		MAX(m.ID) AS MessageID 
	FROM 
		SBookMail m
			LEFT JOIN SBookContact c ON 
			(
				c.ID = m.SenderID 
			)  
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
				AND c.ID = m.ContactID 
			) 
		) 
		AND m.Message != "" 
		AND m.ReceiverID > 0 
	GROUP BY 
		c.ID 
' ) )
{
	// Loop through all contacts
	foreach( $contacts as $cd )
	{
		/*$q[] = '
		(
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
					LEFT JOIN Image i ON ( i.ID = c.ImageID )
					LEFT JOIN Folder f ON ( i.ImageFolder = f.ID )
			WHERE
				(   ( m.SenderID = \'' . $webuser->ContactID . '\' AND m.ReceiverID = \'' . $cd->ID . '\' AND c.ID = m.ReceiverID )
				OR  ( m.ReceiverID = \'' . $webuser->ContactID . '\' AND m.SenderID = \'' . $cd->ID . '\' AND c.ID = m.SenderID ) ) 
				AND m.Type = "im" 
				AND m.Message != ""
				AND m.SenderID > 0
				AND m.ReceiverID > 0
			ORDER BY
				m.Date DESC
			LIMIT 1
		)
		';*/
		
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
			SBookMail m 
				LEFT JOIN SBookContact c ON 
				(
					c.ID = m.SenderID 
				) 
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
			AND m.ReceiverID > 0 
			AND 
			( 
				( 
						m.ReceiverID = ' . $webuser->ContactID . ' 
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
	
	//die( $q . ' -- ' . print_r( $contacts,1 ) . ' [] ' . print_r( $msgs,1 ) . ' -- ' . $lmsg . ' [] ' . print_r( $database->fetchObjectRows ( $q ),1 ) );
	
	//$q = implode( 'UNION', $q );
	//$q = 'SELECT * FROM ( ' . $q . ' ) z ORDER BY z.Date DESC, z.MessageID DESC';
}

$fstr = ''; $lastmessage = ''; $i = 0;

if( $lmsg && $sm = $database->fetchObjectRows ( $q ) )
{
	$unam = array( '0' => 'Anonymous' ); $uonl = array();	
	
	if( $usrs )
	{
		$unam = GetUserDisplayname( $usrs );
	}
	
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
	
	$defimg = 'admin/gfx/arenaicons/user_johndoe_32.png';
	
	$fstr .= '<div class="inner"><ul>';
	
	foreach( $sm as $m )
	{
		if( $i == 0 )
		{
			if( !$cid )
			{
				$cid = $m->ID;
			}
			
			$lastmessage = $m->MessageID;
		}
		
		// Assign some extra vars
		$m->Online = isset( $uonl[$m->UserID] ) ? 1 : 0;
		$m->DisplayName = isset( $unam[$m->ID] ) ? $unam[$m->ID] : $m->Username;
		$m->Reply = $webuser->ContactID == $m->SenderID ? 1 : 0;
		
		if ( !FileExists( ( BASE_URL . 'secure-files/images/' . ( $m->ImageUniqueID ? $m->ImageUniqueID : $m->ImageID ) . '/' ) ) )
		{
			$m->DiskPath = false;
		}
		
		// Render html
		$fstr .= '<li id="Message_' . $m->ID . '"' . ( $m->IsRead == 0 || $cid == $m->ID ? ' class="' . ( $cid == $m->ID ? 'current ' : '' ) . ( $m->IsRead == 0 ? 'NotRead' : '' ) . '"' : '' ) . '>';
		$fstr .= '<div class="contact"><a href="javascript:void(0)" onclick="chatObject.openPrivateMessage(' . $m->ID . ')">';
		//$fstr .= '<div class="image" style="background-image: url(\'' . ( $m->ImageID > 0 && $m->DiskPath && $m->Filename ? ( $m->DiskPath . $m->Filename ) : $defimg ) . '\')"></div>';
		$fstr .= '<div class="image" style="background-image: url(\'' . ( $m->ImageID > 0 && $m->DiskPath && $m->Filename ? ( BASE_URL . 'secure-files/images/' . ( $m->ImageUniqueID ? $m->ImageUniqueID : $m->ImageID ) . '/' ) : $defimg ) . '\')"></div>';
		$fstr .= '<div class="name">' . $m->DisplayName . '</div>';
		//$fstr .= '<div class="content">' . dotTrim( renderSmileys( stripslashes( html_entity_decode( $m->Message ) ) ), 25 ) . '</div>';
		$fstr .= '<div class="content">' . dotTrim( stripslashes( html_entity_decode( $m->Message ) ), 25 ) . '</div>';
		$fstr .= '<div class="time">' . ( $m->Date > 0 ? TimeToHuman( $m->Date, 'day' ) : '' ) . '</div>';
		$fstr .= '</a></div></li>';
		
		$i++;
	}
	
	$fstr .= '</ul></div>';
	
	if( isset( $_REQUEST[ 'function' ] ) && $_REQUEST[ 'function' ] == 'folders' )
	{
		output( 'ok<!--separate-->' . $fstr . '<!--separate-->' . $lastmessage );
	}
}

if( isset( $_REQUEST[ 'function' ] ) && $_REQUEST[ 'function' ] == 'folders' )
{
	output( 'no new updates' );
}

?>
