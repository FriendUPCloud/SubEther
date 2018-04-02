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

global $database;

if ( $group = $database->fetchObjectRow( '
	SELECT 
		c.* 
	FROM 
		SBookCategory c 
	WHERE
		c.ID = \'' . $parent->folder->CategoryID . '\' 
' ) )
{
	$group->Settings = json_obj_decode( $group->Settings );
	
	// Parent Settings
	if ( $group->ParentID > 0 && ( $pset = $database->fetchObjectRow( '
		SELECT 
			*
		FROM 
			SBookCategory
		WHERE
				ID = \'' . $group->ParentID . '\' 
			AND Type = "SubGroup" 
			AND IsSystem = "0" 
			AND ParentID = "0" 
	' ) ) )
	{
		$pset->Settings = json_obj_decode( $pset->Settings );
		
		if ( isset( $pset->Settings->AccessLevels ) )
		{
			$group->Settings->AccessLevels = $pset->Settings->AccessLevels;
		}
	}
	
	if ( is_object( $group->Settings ) && ( !isset( $group->Settings->AccessLevels[0] ) || !$group->Settings->AccessLevels[0]->ID && !$group->Settings->AccessLevels[0]->Name ) )
	{
		$group->Settings->AccessLevels = json_obj_decode( '[{"ID":"n","Name":"Owner","Display":"@","r":"1","w":"1","d":"1","a":"1","o":"1"},{"ID":"o","Name":"Admin","Display":"@","r":"1","w":"1","d":"1","a":"1","o":"0"},{"ID":"v","Name":"Moderator","Display":"+","r":"1","w":"1","d":"0","a":"1","o":"0"},{"ID":"i","Name":"Member","Display":"","r":"1","w":"1","d":"1","a":"0","o":"0"}]' );
	}
}

if ( $parts = $database->fetchObjectRows( $q = '
	SELECT 
		r.*, 
		c.ID AS ContactID,
		a.MemberID,
		a.Access,
		a.Read,
		a.Write,
		a.Delete,
		a.Admin,
		a.Owner
	FROM 
		SBookCategoryRelation r, 
		SBookContact c,
		SBookCategoryAccess a 
	WHERE 
			r.CategoryID = \'' . $parent->folder->CategoryID . '\' 
		AND r.ObjectType = "Users" 
		AND r.ObjectID > 0
		AND c.UserID = r.ObjectID
		AND a.CategoryID = r.CategoryID
		AND a.ContactID = c.ID 
	ORDER BY 
		r.ID ASC 
' ) )
{
	$data = new stdClass(); $usrs = array(); $levels = array(); $members = array( '-'=>'---' );
	
	if ( $group->Settings->AccessLevels )
	{
		foreach( $group->Settings->AccessLevels as $lvl )
		{
			if ( $lvl->ID && $lvl->Name )
			{
				$levels[$lvl->ID] = $lvl->Name;
			}
		}
	}
	
	foreach ( $parts as $k=>$v )
	{
		//$members[$v->ContactID] = GetUserDisplayname( $v->ContactID );
		$usrs[$v->ContactID] = $v->ContactID;
	}
	
	$usrs = GetUserDisplayname( $usrs );
	
	$members = array_replace( $members, $usrs );
	
	if ( $levels )
	{
		$data->levels = $levels;
	}
	
	if ( $members )
	{
		$data->members = $members;
	}
	
	$arr = array(); $cat = array(); $uid = array();
	
	// TODO: get most of this from data instead of sql
	
	// TODO: Find out why some names disapear when changing from a higher level to a lower
	
	$array = json_decode( $_POST['Data'] );
	
	if ( $array )
	{
		foreach ( $array as $key=>$val )
		{
			$obj = array();
			
			$parts = explode( ',', $val );
			
			if ( $parts )
			{
				foreach ( $parts as $v )
				{
					if ( $_POST['value'] == '-' && $_POST['parent'] == $key && $_POST['current'] == $v )
					{
						continue;
					}
					else if ( $_POST['current'] == '-' && $_POST['value'] == $v )
					{
						continue;
					}
					else if ( $_POST['parent'] == $key && $_POST['current'] == $v )
					{
						$v = $_POST['value'];
					}
					else if ( $_POST['value'] == $v )
					{
						$v = $_POST['current'];
					}
					
					$obj[$v] = $arr;
					
					$uid[$key] = ( $uid[$key] ? ( $uid[$key] . ',' . $v ) : $v );
				}
				
				// Add new to this access level
				if ( $_POST['parent'] == $key && $_POST['current'] == '-' && $_POST['value'] != '-' )
				{
					$obj[$_POST['value']] = $arr;
					
					$uid[$key] = ( $uid[$key] ? ( $uid[$key] . ',' . $_POST['value'] ) : $_POST['value'] );
				}
			}
			
			if ( !$obj )
			{
				$uid[$key] = '';
			}
			
			if ( $obj || !$parts )
			{
				$obj['-'] = array();
				
				$arr = array( $key=>$obj );
			}
			else
			{
				$obj['-'] = array();
			}
			
			$cat[$key] = $obj;
		}
		
		$cat = array_reverse( $cat );
		
		if ( $cat )
		{
			$data->hierarchy = $cat;
		}
		
		if ( $uid )
		{
			$data->participants = $uid;
		}
		
		if ( isset( $_POST['open'] ) && $_POST['open'] )
		{
			$data->open = $_POST['open'];
		}
		
		$js = '';
		
		$str = renderHtmlFields( 'structuremanager', '', '', '', '', '', $data, 'onchange="UpdateStructureManager(\''.( $_POST['oid'] ? $_POST['oid'] : '0' ).'\',this)"', '' );
		
		die( 'ok<!--separate-->' . $str );
	}
}

die( ' failure ' );

?>
