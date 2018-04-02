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

function generatePageStructure ( $parent = 0, $current = 0, $depth = 0 )
{
	global $database;
	if ( $rows = $database->fetchObjectRows ( '
		SELECT * FROM ContentElement
		WHERE
			    ID != MainID
			AND Parent = \'' . (string)$parent . '\'
		ORDER BY SortOrder ASC
	' ) )
	{
		foreach ( $rows as $row )
		{
			$c = '';
			if ( $current == $row->MainID )
			{
				$c = ' class="current"';
			}
			$str .= '<ul><li' . $c . '><a href="admin.php?module=extensions&extension=templates&cid=' . $row->MainID . '">' . $row->MenuTitle . '</a>';
			$str .= generatePageStructure ( $row->MainID, $current, $depth + 1 );
			$str .= '</li></ul>';
		}
		return $str;
	}
	return '';
}

?>
