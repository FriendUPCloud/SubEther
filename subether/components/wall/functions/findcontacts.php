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

if( isset( $_POST['search'] ) && $_POST['search'] )
{
	if( $rows = $database->fetchObjectRows( $q = '
		SELECT 
			c.* 
		FROM 
			SBookContact c, 
			SBookContactRelation r 
		WHERE 
				( c.Username LIKE "' . $_POST['search'] . '%" 
			OR  c.Firstname LIKE "' . $_POST['search'] . '%" 
			OR  c.Middlename LIKE "' . $_POST['search'] . '%" 
			OR  c.Lastname LIKE "' . $_POST['search'] . '%" ) 
			AND r.ObjectType = "SBookContact" 
			AND r.IsApproved = "1" 
			AND ( ( r.ContactID = c.ID 
			AND r.ObjectID = \'' . $webuser->ContactID . '\' ) 
			OR  ( r.ObjectID = c.ID 
			AND r.ContactID = \'' . $webuser->ContactID . '\' ) ) 
		ORDER BY 
			c.Username ASC, 
			c.Firstname ASC, 
			c.Middlename ASC, 
			c.Lastname ASC 
			' ) )
	{
		$str = '<div><table>';
		foreach( $rows as $c )
		{
			$str .= '<tr onclick="selectContact( \'' . $c->ID . '\', \'' . GetUserDisplayname( $c->ID ) . '\' )">';
			$str .= '<td style="width:35px;"><div class="image">';
			$i = new dbImage ();
			if( $i->load( $c->ImageID ) )
			{
				$str .= $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
			}
			$str .= '</div></td>';
			$str .= '<td><div>' . GetUserDisplayname( $c->ID ) . '</div></td>';
			$str .= '</tr>';
		}
		$str .= '</table></div>';
		
		die ( 'ok<!--separate-->' . $str );
	}
}
die( 'fail' );

?>
