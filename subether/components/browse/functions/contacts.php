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

$query = array(); $keywords = '';

if ( isset( $_REQUEST[ 'q' ] ) && $_REQUEST[ 'q' ] )
{
	$keywords = explode( '+', str_replace( ' ', '+', $_REQUEST[ 'q' ] ) );
	
	foreach( $keywords as $key )
	{
		$query[] = 'c.Firstname LIKE "' . $key . '%"';
	}
}

if ( $rows = $database->fetchObjectRows ( '
    SELECT 
        c.*,
		i.UniqueID,
		i.Filename,
		f.DiskPath 
    FROM
		Users u,
        SBookContact c
			LEFT JOIN Image i ON
			(
				c.ImageID = i.ID
			)
			LEFT JOIN Folder f ON
			(
				i.ImageFolder = f.ID
			) 
    WHERE
		u.ID = c.UserID
		AND u.IsDeleted = "0" 
        AND c.Username != "" 
		' . ( is_array( $keywords ) ? '
		AND 
		(
			(
					c.Display = "0"
				AND c.Username LIKE "' . $keywords[0] . '%" 
			) 
			OR
			(
					c.Display > 0
				AND
				(
					' . implode( ' OR ', $query ) . '
				) 
			)
		)
		' : '' ) . '
    ORDER BY
		c.ID DESC, 
        c.Firstname ASC,
		c.Username ASC 
' ) )
{
	$usrs = array(); $ids = array();
	
	foreach ( $rows as $usr )
    {
		if( $usr->ID > 0 )
		{
			$ids[$usr->ID] = $usr->ID;
		}
		if ( $usr->UserID > 0 )
		{
			$usrs[$usr->UserID] = $usr->UserID;
		}
	}
	
	$online = IsUserOnline( $usrs );
	$dnam = GetUserDisplayname( $ids );
	
	$relations = ContactRelations();
	
	$array = array();
	
    $td = 0; $k = 10;
    $mstr .= '<div class="contacts">';
	
    foreach ( $rows as $r )
    {
		// --- Contact button --- //
		
		$r->Online = 'admin/gfx/icons/bullet_white.png';
		
		if ( isset( $online[$r->UserID] ) && ( $r->OnlineStatus = $online[$r->UserID] ) )
		{
			if( ( date( 'YmdHi' ) - date( 'YmdHi', strtotime( $online[$r->UserID]->LastActivity ) ) ) > 10 )
			{
				$r->IsOnline = false;
			}
			else
			{
				$r->Online = 'admin/gfx/icons/bullet_green.png';
				$r->IsOnline = true;
			}
		}
		
		$btn = '';
		
		if ( isset( $relations[$r->ID] ) && $relations[$r->ID]->Status == 'Contact' )
		{
			$btn .= '<button onclick="profileOptions()">';
			$btn .= '<span>' . i18n( 'Contact' ) . '</span>';
			$btn .= '</button>';
		}
		else if ( isset( $relations[$r->ID] ) && $relations[$r->ID]->Status == 'Pending' )
		{
			$btn .= '<button>';
			$btn .= '<span>' . i18n( 'Pending' ) . '</span>';
			$btn .= '</button>';
		}
		else if ( $webuser->ID > 0 && $r->UserID != $parent->webuser->ID )
		{
			$btn .= '<button onclick="addContact( \'' . $r->ID . '\', this )">';
			$btn .= '<span>+ ' . i18n( 'Add Contact' ) . '</span>';
			$btn .= '</button>';
		}
		
		// --- Contact info --- //
		
		// TODO: Fix and remove this later
		if( $r->NodeID == 0 || $r->NodeID > 0 && IsSystemAdmin() )
		{
			$td++;
			
			$con = '<div class="contact"><table><tr>';
			
			$img = '';
			
			if ( !FileExists( ( BASE_URL . 'secure-files/images/' . ( $r->UniqueID ? $r->UniqueID : $r->ImageID ) . '/' ) ) )
			{
				$r->Filename = false;
			}
			
			if( $r->ImageID > 0 && $r->Filename )
			{
				//$img = ' style="background-image: url(\'' . $r->DiskPath . '/' . $r->Filename . '\')"';
				$img = ' style="background-image: url(\'' . ( BASE_URL . 'secure-files/images/' . ( $r->UniqueID ? $r->UniqueID : $r->ImageID ) . '/' ) . '\')"';
			}
			else
			{
				$img = ' style="background-image: url(\'admin/gfx/arenaicons/user_johndoe_128.png\')"';
			}			
			
			
			$con .= '<td><div class="image"' . $img . '><a href="' . $parent->path . $r->Username . '">';
			
			$con .= '</a></div>';
			
			$con .= '<div class="status"><img src="' . $r->Online . '"></div>';
			
			$con .= '</td><td>';
			$con .= '<div><a href="'/* . $parent->path*/ . $r->Username . '"><strong>' . ( isset( $dnam[$r->ID] ) ? $dnam[$r->ID] : $r->Username ) . '</strong></a></div>';
			$con .= '<p>' . dotTrim( $r->About, 255 ) . '</p>';
			if( $r->NodeID == 0 )
			{
				$con .= '<div>' . $btn . '</div>';
			}
			$con .= '</td></tr></table></div>';
			
			
			
			$key = ( isset( $r->IsOnline ) ? ( $r->IsOnline ? ( '1' . str_pad( $k++, 8, '0', STR_PAD_LEFT ) ) : ( '3' . str_pad( $k++, 8, '0', STR_PAD_LEFT ) ) ) : ( '3' . str_pad( $k++, 8, '0', STR_PAD_LEFT ) ) );
			
			$array[$key] = $con;
		}
    }
    
	if ( $array && is_array( $array ) )
	{
		ksort( $array );
		
		$mstr .= implode( $array );
	}
	
	$mstr .= '<div class="clearboth" style="clear:both"></div>';
    $mstr .= '</div>';
}

?>
