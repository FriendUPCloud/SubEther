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

function findMembers ( $keywords )
{
	global $database, $webuser;
	
	if( !$keywords ) return false;
	
	$q = '
		SELECT 
			c.* 
		FROM
			Users u,
			SBookContact c, 
			SBookContactRelation r 
		WHERE 
				r.ObjectID = \'' . $webuser->ID . '\' 
			AND r.ObjectType = "Users" 
			AND c.ID = r.ContactID 
			AND c.Username LIKE "' . $keywords . '%"
			AND u.ID = c.UserID
			AND u.IsDeleted = "0"
		ORDER BY  
			c.Username DESC 
	';
	
	if( $rows = $database->fetchObjectRows( $q ) )
	{
		return $rows;
	}
	return false;
}

?>
