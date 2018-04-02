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

if( $_POST['type'] )
{
	$str = '';
	
	switch( $_POST['type'] )
	{
		// --- Users ----------------------------------------------------------------------------------------------------
		
		case 'users':
			if( $users = $database->fetchObjectRows( $q = '
				SELECT * FROM 
				( 
					( 
						SELECT
							c.ID, 
							c.ImageID, 
							c.UserID,
							c.Username,
							c.Firstname,
							c.Middlename,
							c.Lastname 
						FROM 
							SBookContactRelation r, 
							SBookContact c 
						WHERE 
								r.ObjectType = "SBookContact" 
							AND r.IsApproved = "1" 
							AND ( ( r.ContactID = \'' . $webuser->ContactID . '\' AND c.ID = r.ObjectID ) 
							OR  ( r.ObjectID = \'' . $webuser->ContactID . '\' AND c.ID = r.ContactID ) )
							AND c.NodeID = "0"
							AND c.NodeMainID = "0" 
					) 
					UNION 
					( 
						SELECT 
							c.ID, 
							c.ImageID, 
							c.UserID, 
							c.Username, 
							c.Firstname, 
							c.Middlename, 
							c.Lastname 
						FROM 
							SBookCategoryRelation r, 
							SBookContact c 
						WHERE 
								r.ObjectType = "Users"
							AND r.CategoryID = \'' . $parent->folder->CategoryID . '\' 
							AND c.UserID = r.ObjectID 
							AND c.NodeID = "0" 
							AND c.NodeMainID = "0" 
					)
				) z 
				' . ( $_POST['keyword'] ? 'WHERE 
					( 	z.Username LIKE "' . $_POST['keyword'] . '%" 
					OR  z.Firstname LIKE "' . $_POST['keyword'] . '%" 
					OR  z.Middlename LIKE "' . $_POST['keyword'] . '%" 
					OR 	z.Lastname LIKE "' . $_POST['keyword'] . '%" ) ' : '' ) . '
				GROUP BY 
					z.ID 
				ORDER BY 
					z.Username, 
					z.Firstname 
				' . ( $_POST['limit'] ? ( 'LIMIT ' . $_POST['limit'] ) : '' ) . ' 
			', false, 'components/events/functions/eventsearch.php' ) )
			{
				$str .= '<div class="searchlist"><ul>';
				foreach( $users as $u )
				{
					$str .= '<li onclick="SetSearchValue(this,\'EventAttendee\')" value="' . $u->ID . '">' . GetUserDisplayname( $u->ID ) . '</li>';
				}
				$str .= '</ul></div>';
				
				die( 'ok<!--separate-->' . $str );
			}
			break;
		
		// --- Event names ----------------------------------------------------------------------------------------------
		
		case 'events':
			if( $events = $database->fetchObjectRows( $q = '
				SELECT 
					e.* 
				FROM 
					SBookEvents e 
				WHERE 
						' . ( ( $parent && strtolower( $parent->folder->MainName ) != 'profile' ) ? 'e.CategoryID = \'' . $parent->folder->CategoryID . '\' ' : 'e.UserID = \'' . $parent->cuser->ContactID . '\'
					AND e.CategoryID = "0" ' ) . '
					AND e.IsDeleted = "0" 
					' . ( $_POST['keyword'] ? 'AND e.Name LIKE "' . $_POST['keyword'] . '%" ' : '' ) . '
				GROUP BY 
					e.Name 
				ORDER BY 
					e.Name ASC 
				' . ( $_POST['limit'] ? ( 'LIMIT ' . $_POST['limit'] ) : '' ) . ' 
			', false, 'components/events/functions/eventsearch.php' ) )
			{
				$str .= '<div class="searchlist"><ul>';
				foreach( $events as $e )
				{
					$str .= '<li onclick="SetSearchValue(this,\'EventName\')" value="' . $e->ID . '">' . $e->Name . '</li>';
				}
				$str .= '</ul></div>';
				
				die( 'ok<!--separate-->' . $str );
			}
			break;
		
		// --- Role names -----------------------------------------------------------------------------------------------
		
		case 'roles':
			if( $roles = $database->fetchObjectRows( $q = '
				SELECT 
					h.* 
				FROM 
					SBookEvents e,
					SBookHours h 
				WHERE 
						' . ( ( $parent && strtolower( $parent->folder->MainName ) != 'profile' ) ? 'e.CategoryID = \'' . $parent->folder->CategoryID . '\' ' : 'e.UserID = \'' . $parent->cuser->ContactID . '\'
					AND e.CategoryID = "0" ' ) . '
					AND e.IsDeleted = "0"
					AND h.ProjectID = e.ID
					AND h.IsDeleted = "0"
					' . ( $_POST['keyword'] ? 'AND h.Role LIKE "' . $_POST['keyword'] . '%" ' : '' ) . '
				GROUP BY 
					h.Role 
				ORDER BY 
					h.Role ASC 
				' . ( $_POST['limit'] ? ( 'LIMIT ' . $_POST['limit'] ) : '' ) . ' 
			', false, 'components/events/functions/eventsearch.php' ) )
			{
				$str .= '<div class="searchlist"><ul>';
				foreach( $roles as $r )
				{
					$str .= '<li onclick="SetSearchValue(this,\'EventRole\')" value="' . $r->Role . '">' . $r->Role . '</li>';
				}
				$str .= '</ul></div>';
				
				die( 'ok<!--separate-->' . $str );
			}
			break;
	}
}

die( 'fail' );

?>
