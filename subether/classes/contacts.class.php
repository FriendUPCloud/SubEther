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

class SBookContact extends dbObject
{
	var $_tableName = 'SBookContact';
	var $Contacts;
	var $Groups;
	var $Wall;
	var $Media;
	
	function __construct ( $userid = false )
	{
		$this->loadTable ();
		if ( $userid )
		{
			$this->UserID = $userid;
			$this->Load ( );
		}
	}
	
	/*// Load my contacts (related to this contact)
	function LoadContacts ()
	{
		$q = '
			SELECT 
				c.* 
			FROM 
				SBookContact c, 
				SBookContactRelation r 
			WHERE 
					 r.ObjectID = \'' . $this->UserID . '\' 
				AND r.ObjectType = "Users" 
				AND c.ID = r.ContactID 
			ORDER BY 
				c.ID ASC 
		';
		if ( $cr = $this->find ( $q ) )
		{
			$this->Contacts = $cr;
		}
		return false;
	}
	
	// List my contacts
	function ListContacts ()
	{
		if ( !$this->Contacts ) return;
		$str = '';
		foreach ( $this->Contacts as $cnt )
		{
			$str .= $cnt->Username . '<br>';
		}
		return $str;
	}*/
	
	function onLoaded ()
	{
		global $database;
		
		/* --- Contacts --- */
		
		$c = '
			SELECT 
				c.* 
			FROM 
				SBookContact c, 
				SBookContactRelation r 
			WHERE 
					r.ObjectID = \'' . $this->UserID . '\' 
				AND r.ObjectType = "Users" 
				AND c.ID = r.ContactID 
			ORDER BY 
				c.ID ASC 
		';
		
		if ( $rows = $database->fetchObjectRows ( $c ) )
		{
			$ids = array();
			$mids = array();
			foreach ( $rows as $row )
			{
				$ids[] = $row->ID;
				$mids[] = $row->UserID;
			}
			$this->Contacts = array( 'ID'=>implode( ',', $ids ), 'UserID'=>implode( ',', $mids ), 'Object'=>$rows );
			unset( $rows, $ids, $mids );
		}
		
		/* --- Groups --- */
		
		$group = new dbObject( 'SBookCategory' );
		$group->Type = 'Group';
		$group->Name = 'Groups';
		$group->load();
		
		$g = '
			SELECT 
				c.*, r.ID as FolderID  
			FROM 
				SBookCategory c, 
				SBookCategoryRelation r 
			WHERE 
					r.ObjectID = \'' . $this->UserID . '\' 
				AND r.ObjectType = "Users" 
				AND c.ID = r.CategoryID 
				AND c.CategoryID = \'' . $group->ID . '\' 
			ORDER BY 
				c.ID ASC 
		';
		
		if ( $rows = $database->fetchObjectRows ( $g ) )
		{
			$ids = array();
			$mids = array();
			foreach ( $rows as $row )
			{
				$ids[] = $row->FolderID;
				$mids[] = $row->ID;
			}
			$this->Groups = array( 'CategoryID'=>implode( ',', $ids ), 'MainCategoryID'=>implode( ',', $mids ), 'Object'=>$rows );
			unset( $rows, $ids, $mids );
		}
		
		/* --- Wall --- */ 
		
		$w = '
			SELECT 
				r.* 
			FROM 
				SBookCategory c, 
				SBookCategoryRelation r 
			WHERE 
					r.ObjectID = \'' . $this->UserID . '\' 
				AND r.ObjectType = "Users" 
				AND c.ID = r.CategoryID 
				AND c.Type = "SubGroup" 
				AND c.Name = "Wall" 
			ORDER BY 
				r.ID ASC 
		';
		
		if ( $rows = $database->fetchObjectRows ( $w ) )
		{
			$ids = array();
			$mids = array();
			foreach ( $rows as $row )
			{
				$ids[] = $row->ID;
				$mids[] = $row->CategoryID;
			}
			$this->Wall = array( 'CategoryID'=>implode( ',', $ids ), 'MainCategoryID'=>implode( ',', $mids ), 'Object'=>$rows );
			unset( $rows, $ids, $mids );
		}
		
		/* --- Media --- */
		
		$m = '
			SELECT 
				m.* 
			FROM 
				 SBookMediaRelation m  
			WHERE 
					m.UserID = \'' . $this->UserID . '\' 
				AND m.MediaType = "Folder" 
			ORDER BY 
				m.SortOrder ASC 
		';
		
		if ( $rows = $database->fetchObjectRows ( $m ) )
		{
			$ids = array();
			$mids = array();
			foreach ( $rows as $row )
			{
				$ids[] = $row->MediaID;
				$mids[] = $row->Tags;
			}
			$this->Media = array( 'MediaID'=>implode( ',', $ids ), 'Tags'=>implode( ',', $mids ), 'Object'=>$rows );
			unset( $rows, $ids, $mids );
		}
	}
}


$c = new SBookContact ( 110 );
//die( print_r( dbObjectClean( $c ) ) . ' ..' );

?>
