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

global $document, $database, $webuser;

statistics( $parent->module, 'register' );

$root = 'subether/';
$cbase = 'subether/components/register';

include_once ( 'subether/classes/mail.class.php' );
include_once ( 'subether/include/functions.php' );
include_once ( $cbase . '/include/functions.php' );
//include_once ( $cbase . '/functions/component.php' );

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/component.css' );
$document->addResource ( 'javascript', $root . '/javascript/md5.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/register.js' );

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	die( 'failed action request - register' );
}
// Check for user functions ----------------------------------------------------
else if ( isset( $_REQUEST[ 'function' ] ) )
{
	if ( file_exists ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' ) )
    {
       include ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' );
    }
	else if ( file_exists ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' ) )
	{
		include ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' );
	}
	die( 'failed function request - register' );
}

if( $webuser->ID > 0 )
{
	$group = $database->fetchObjectRow( '
		SELECT
			*
		FROM
			SBookCategory
		WHERE
				Type = "Group"
			AND Name = "Groups"
	' );
	
	if( isset( $parent->access['IsSystemAdmin'] ) )
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
						AND c.IsSystem = "0"
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
	
	if( $rows = $database->fetchObjectRows( $q ) )
	{
		$opt = '';
		
		$def = ( defined( 'NODE_DEFAULT_GROUP' ) && NODE_DEFAULT_GROUP ? NODE_DEFAULT_GROUP : 'SubEther' );
		
		foreach( $rows as $r )
		{
			$s = ( $def == $r->Name ? ' selected="selected"' : '' );
			
			$opt .= '<option value="' . $r->Name . '"' . $s . '>' . $r->Name . '</option>';
		}
		
		$Component->Groups = $opt;
	}
}

if( isset( $_REQUEST['activate'] ) )
{
	$Component->Load( $cbase . '/templates/activate.php' );
}
else if( isset( $_REQUEST['recover'] ) )
{
	$Component->Load( $cbase . '/templates/recover.php' );
}
else if( isset( $_REQUEST['invite'] ) && $webuser->ID > 0 )
{
	$Component->Load( $cbase . '/templates/invite.php' );
}
else if( isset( $_REQUEST['limited'] ) && $webuser->ID > 0 && isset( $parent->access['IsSystemAdmin'] ) )
{
	$Component->Load( $cbase . '/templates/limited.php' );
}
else if( $webuser->ID > 0 || $database->fetchObjectRow( 'SELECT * FROM SNodes WHERE IsMain = "1" AND Open = "1"' ) || !$database->fetchObjectRow( 'SELECT * FROM SNodes' ) )
{
	$Component->Load( $cbase . '/templates/component.php' );
}
else
{
	$Component->Load( $cbase . '/templates/empty.php' );
}

statistics( $parent->module, 'register' );

?>
