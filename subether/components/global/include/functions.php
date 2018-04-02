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

function ComponentTabs ( $module, $component = false, $position = false, $key = false )
{
	global $database;
	
	if( !$module ) return false;
	
	$type = 'global';
	
	$q = '
		SELECT *
		FROM STabs
		WHERE
				Type = \'' . $type . '\'
			AND Module = \'' . $module . '\'
			' . ( $component ? 'AND Component = \'' . $component . '\'' : '' ) . '
			' . ( $position ? 'AND Position = \'' . $position . '\'' : '' ) . '
		ORDER BY Tab ASC
	';

	if( $rows = $database->fetchObjectRows( $q ) )
	{
		if( $key )
		{
			$i = 0;
			foreach( $rows as $obj )
			{
				$rows[$obj->$key] = $obj;
				unset( $rows[$i] );
				$i++;
			}
			return $rows;
		}
		return $rows;
	}
	return false;
}

function ComponentPosition ( $module, $position = false, $key = false, $component = false, $type = false )
{
	global $database;
	
	if( !$module ) return false;
	
	if( !$type ) $type = 'global';
	
	$q = '
		SELECT *
		FROM SComponents
		WHERE
				Type = \'' . $type . '\'
			AND Module = \'' . $module . '\'
			' . ( $component ? 'AND Name = \'' . $component . '\'' : '' ) . '
			' . ( $position ? 'AND Position = \'' . $position . '\'' : '' ) . '
		ORDER BY Name ASC
	';

	if( $rows = $database->fetchObjectRows( $q ) )
	{
		if( $key )
		{
			$i = 0;
			foreach( $rows as $obj )
			{
				$rows[$obj->$key] = $obj;
				unset( $rows[$i] );
				$i++;
			}
			
			return $rows;
		}
		return $rows;
	}
	return false;
}

function ModulePosition ( $module, $type = false, $root = false )
{
	global $database;
	
	if( !$module ) return false;
	
	if( !$type ) $type = 'global';
	
	$q = '
		SELECT *
		FROM SModules
		WHERE
				Type = \'' . $type . '\'
			AND Name = \'' . $module . '\' 
		ORDER BY Name ASC
	';

	if( $rows = $database->fetchObjectRows( $q ) )
	{
		return $rows;
	}
	if( file_exists( $f = ( BASE_DIR . '/subether/modules/' . $module . '/info' ) ) )
	{
		$cnt = file_get_contents( $f, true );
		
		$arr = array();
		
		if( $cnt && ( $pos = explode( ',', $cnt ) ) )
		{
			foreach( $pos as $p )
			{
				$obj = new stdClass();
				$obj->Name = $module;
				$obj->Type = $type;
				$obj->Position = $p;
				
				$arr[] = $obj;
			}
			
			return $arr;
		}
	}
	return false;
}

?>
