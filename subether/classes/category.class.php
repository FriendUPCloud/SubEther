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

// SBookCategory is a savable, loadable database object like dbObject
class SBookCategory extends dbObject
{
	function __construct ( $id = false, $webuser = false )
	{
		global $webuser;
		
		parent::dbObject ( 'SBookCategory' );
		if ( $id ) $this->Load ( $id );
		if ( !$webuser )
		{
			$this->webuser = $webuser;
		}
		else $this->webuser = $webuser;
	}
	
	static function GetUserCategories ( $webuser )
	{
		global $database;
		if ( $rows = $database->fetchObjectRows ( '
			SELECT c.* FROM SBookCategory c, SBookCategoryRelation r
			WHERE
				r.CategoryID = c.ID AND r.ObjectType="Users" AND r.ObjectID=\'' . $webuser->ID . '\'
		' ) )
		{
			$out = array ();
			foreach ( $rows as $row )
			{
				$r = new SBookCategory ( $row->ID, $webuser );
				if ( $r->ID > 0 )
				{
					$out[] = $r;
				}
			}
			return $out;
		}
		return false;
	}
	
	static function GetByName ( $name, $webuser = false )
	{
		global $database;
		$webuser = $webuser ? $webuser : $GLOBALS[ 'User' ];
		if ( !$webuser || !$webuser->ID ) return false;
		
		if ( $row = $database->fetchObjectRow ( '
			SELECT c.* FROM SBookCategory c, SBookCategoryRelation r
			WHERE
				c.Name =\'' . $name . '\' AND 
				c.ID = r.CategoryID AND r.ObjectType="User" AND r.ObjectID=\'' . $webuser->ID . '\'
		' ) )
		{
			$fld = new SBookCategory ( $row->ID, $webuser );
			if ( $fld->ID > 0 )
			{
				return $fld;
			}
		}
		return false;
	}
	
	function Save ()
	{
		global $database, $webuser;
		
		$tf = true;
		
		// Build query
		$wheres = array ();
		if( !$this->_table->_fieldNames ) return false;
		foreach ( $this->_table->_fieldNames as $r )
		{
			if ( isset ( $this->{$r} ) )
			{
				$wheres[] = 'c.'.$r.' = ' . $this->formatField ( $r, $this->$r );
			}
		}
		// Find by query
		if ( $row = $database->fetchObjectRow ( $q = '
			SELECT c.* FROM SBookCategory c 
			WHERE
				' . ( count ( $wheres ) ? ( '( ' . implode ( ') AND (', $wheres ) . ' ) ' ) : '' ) . ' 
		' ) )
		{
			$this->ID = $row->ID;
		}
		else $tf = parent::save ();
		
		// When saving, make sure we have a SBookCategoryRelation to the logged in user
		if ( $this->ID && $this->ID > 0 )
		{
			if ( !( $row = $database->fetchObjectRow ( '
				SELECT * FROM SBookCategoryRelation 
				WHERE 
					CategoryID = \'' . $this->ID . '\' AND ObjectType="Users" AND ObjectID = \'' . $this->webuser->ID . '\'
			' ) ) )
			{
				$database->query ( '
					INSERT INTO SBookCategoryRelation ( CategoryID, ObjectType, ObjectID ) VALUES ( \'' . $this->ID . '\', "Users", \'' . $this->webuser->ID . '\')
				' );
			}
		}
		return $tf;
	}
	
	function Load ( $id = false )
	{
		global $database;
		if ( $id ) 
		{ 
			return parent::Load ( $id ); 
		}
		else if ( isset ( $this->ID ) ) 
		{ 
			return parent::Load ( $this->ID ); 
		}
		else
		{
			// Build query
			$wheres = array ();
			if( !$this->_table->_fieldNames ) return false;
			foreach ( $this->_table->_fieldNames as $r )
			{
				if ( isset ( $this->{$r} ) )
				{
					$wheres[] = 'c.'.$r.' = ' . $this->formatField ( $r, $this->$r );
				}
			}
			// Find by query and relation
			if ( $row = $database->fetchObjectRow ( $q = '
				SELECT c.* FROM SBookCategory c, SBookCategoryRelation r
				WHERE
					' . ( count ( $wheres ) ? ( '( ' . implode ( ') AND (', $wheres ) . ' ) AND' ) : '' ) . '
					r.CategoryID = c.ID AND r.ObjectID=\'' . $this->webuser->ID . '\' AND r.ObjectType = "Users"
			' ) )
			{
				return $this->Load ( $row->ID );
			}
		}
		return false;
	}
	
	function Delete ()
	{
		global $database;
		$id = $this->ID;
		if ( !$id ) return;
		if ( !$this->webuser ) return;
		
		$tf = parent::delete ();
		
		$database->query ( '
			DELETE FROM SBookCategoryRelation 
			WHERE 
				ObjectType="Users" AND ObjectID=\'' . $this->webuser->ID . '\' AND CategoryID=\'' . $id . '\'
		' );
		
		return $tf;
	}
}

?>
