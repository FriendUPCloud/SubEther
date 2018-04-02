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

$group = $database->fetchObjectRow( '
	SELECT
		*
	FROM
		SBookCategory
	WHERE
			Type = "Group"
		AND Name = "Groups"
', false, 'components/groups/include/panel.php' );

$str .= '<h4 class="heading groups"><span>' . i18n( 'i18n_Groups' ) . '</span></h4><ul class="list groups">';

if( isset( $parent->access->IsSystemAdmin ) )
{
	$q = '
		SELECT * FROM 
		( 
			( 
				SELECT 
					c.*,
					"" AS UserID,
					"" AS RelationID 
				FROM 
					SBookCategory c 
				WHERE 
						c.CategoryID = \'' . $group->ID . '\' 
					AND c.Type = "SubGroup" 
					/*AND c.IsSystem = "0"*/
					AND c.NodeID = "0"
					AND c.NodeMainID = "0" 
			) 
			UNION 
			( 
				SELECT 
					c.*, 
					r.ObjectID as UserID, 
					r.ID as RelationID 
				FROM 
					SBookCategory c, 
					SBookCategoryRelation r 
				WHERE 
						r.ObjectType = "Users" 
					AND r.ObjectID = \'' . $webuser->ID . '\' 
					AND c.CategoryID = \'' . $group->ID . '\' 
					AND c.Type = "SubGroup"
					AND c.IsSystem = "0" 
					AND c.ID = r.CategoryID  
			) 
		) z
		GROUP BY
			z.ID 
		ORDER BY 
			z.ID ASC 
	';
}
else
{
	$q = '
		SELECT 
			c.*, 
			r.ObjectID as UserID, 
			r.ID as RelationID 
		FROM 
			SBookCategory c, 
			SBookCategoryRelation r 
		WHERE 
				r.ObjectType = "Users" 
			AND r.ObjectID = \'' . $webuser->ID . '\' 
			AND c.CategoryID = \'' . $group->ID . '\' 
			AND c.Type = "SubGroup"
			AND c.IsSystem = "0" 
			AND c.ID = r.CategoryID 
		ORDER BY 
			r.SortOrder ASC, 
			c.ID ASC 
	';
}

if ( $group->ID > 0 && ( $rows = $database->fetchObjectRows( $q, false, 'components/groups/include/panel.php' ) ) )
{
	$output = array(); $subs = array(); $tabs = array();
	
	// If mobile version
	if( $parent && $parent->agent && $parent->agent != 'web' )
	{
		$output = $rows;
	}
	// Else desktop version
	else
	{
		foreach( $rows as $s )
		{
			if( $s->ParentID > 0 )
			{
				if( !isset( $subs[$s->ParentID] ) )
				{
					$subs[$s->ParentID] = array();
				}
				
				$subs[$s->ParentID][] = $s;
			}
			
			$output[$s->ID] = $s;
		}
		
		foreach( $output as $k=>$o )
		{
			if( $o->ParentID > 0 && isset( $subs[$o->ParentID] ) && isset( $output[$o->ParentID] ) )
			{
				unset( $output[$k] );
			}
		}
	}
	
	foreach( $output as $r )
	{
		$str .= '<li class="' . urlStr( $r->Name ) . ( FindIdByRoute( 'en/home/groups/' ) == $r->ID ? ' current' : '' ) . '">';
		$str .= '<div>';
		$str .=	'<a href="groups/' . $r->ID . '/">';
		$str .= '<span class="icon"></span>';
		$str .=	'<span class="name">' . $r->Name . '</span>';
		$str .=	'<span class="noti"></span>';
		$str .=	'</a>';
		$str .=	'</div>';
		
		// Mobile version list tabs for current group
		if( $parent && $parent->agent && $parent->agent != 'web' && FindIdByRoute( 'home/groups/' ) == $r->ID )
		{
			if( $tabs = ListComponentTabs( 'groups', 'main', false, false, false ) )
			{
				$ii = 0;
				
				$str .= '<ul class="open tabs">';
				
				foreach( $tabs as $key=>$tab )
				{
					if( $ii++ == 0 ) continue;
					
					// TODO: Add styling for tabs, check for tablet mode and check for access and folder info
					$str .= '<li class="' . urlStr( $key ) . '">';
					$str .= '<div>';
					$str .=	'<a href="groups/' . $r->ID . '/' . $key . '/">';
					$str .= '<span class="icon"></span>';
					$str .=	'<span class="name">' . $tab . '</span>';
					$str .=	'<span class="noti"></span>';
					$str .=	'</a>';
					$str .=	'</div>';
				}
				
				$str .= '</ul>';
			}
		}
		
		// List sub groups in desktop mode
		if( isset( $subs[$r->ID] ) )
		{
			$str .= '<ul class="open">';
			
			foreach( $subs[$r->ID] as $sub )
			{
				$str .= '<li class="' . urlStr( $sub->Name ) . ( FindIdByRoute( 'en/home/groups/' ) == $sub->ID ? ' current' : '' ) . '">';
				$str .= '<div>';
				$str .=	'<a href="groups/' . $sub->ID . '/">';
				$str .= '<span class="icon"></span>';
				$str .=	'<span class="name">' . $sub->Name . '</span>';
				$str .=	'<span class="noti"></span>';
				$str .=	'</a>';
				$str .=	'</div>';
			}
			
			$str .= '</ul>';
		}
		
		$str .= '</li>';
	}
}

$str .= '<li class="create">';
$str .= '<a onclick="openWindow( \'Groups\', \'' . FindIdByRoute( 'en/home/groups/' ) . '\', \'create\' )" href="javascript:void(0)">';
$str .= '<span class="icon"></span>';
$str .= '<span class="create">' . i18n( 'i18n_Create Group' ) . '...</span>';
$str .= '<span></span>';
$str .= '</a>';
$str .= '</li>';
$str .= '</ul>';

?>
