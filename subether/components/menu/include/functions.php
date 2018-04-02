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

function renderMenuList()
{
	global $database;
	
	$type = ( UserAgent() == 'web' ? 'global' : 'mobile' );
	$position = 'Top';
	
	$q = '
		SELECT *
		FROM SModules
		WHERE Type = \'' . $type . '\' AND Position = \'' . $position . '\' AND Visible = "2"
		' . ( IsSystemAdmin() ? 'AND ( UserLevels = ",99,1," OR UserLevels = ",99," OR UserLevels = ",0," ) ' : 'AND ( UserLevels = ",99,1," OR UserLevels = ",0," ) ' ) . '
		ORDER BY SortOrder ASC, Name ASC 
	';

	if( $rows = $database->fetchObjectRows( $q, false, 'components/menu/include/functions.php' ) )
	{
		return $rows;
	}
	return false;
}


?>
