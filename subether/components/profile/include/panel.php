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

$str .= '<h4 class="heading profile"><span>' . i18n( 'i18n_Profile' ) . '</span></h4><ul class="list profile">';

if ( $rows = $database->fetchObjectRows( '
	SELECT 
		c2.* 
	FROM 
		SBookCategory c, 
		SBookCategory c2 
	WHERE 
			c.CategoryID = "0" 
		AND c.Type = "Group" 
		AND c.Name = "Profile" 
		AND c.IsSystem = "1" 
		AND c.NodeID = "0" 
		AND c2.CategoryID = c.ID 
		AND c2.Type = "SubGroup" 
		AND c2.IsSystem = "1" 
		AND c2.NodeID = "0" 
	ORDER BY 
		c2.ID ASC 
', false, 'components/profile/include/panel.php' ) )
{
	foreach( $rows as $r )
	{			
		if ( ComponentExists( urlStr( $r->Name ), $parent->module ) && ComponentAccess( urlStr( $r->Name ), $parent->module ) )
		{
			$str .= '<li class="' . urlStr( $r->Name ) . ( urlStr( $parent->folder->Name ) == urlStr( $r->Name ) ? ' current' : '' ) . '">';
			$str .= '<div>';
			$str .=	'<a href="' . $parent->cuser->Username . '/' . urlStr( $r->Name ) . '/">';
			$str .= '<span class="icon"></span>';
			$str .=	'<span class="name">' . i18n( 'i18n_' . ( urlStr( $r->Name ) == 'events' ? 'Calendar' : $r->Name ) ) . '</span>';
			$str .=	'<span class="noti"></span>';
			$str .=	'</a>';
			$str .=	'</div>';
			$str .= '</li>';
		}
	}
}

$str .= '</ul>';

?>
