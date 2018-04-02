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

$required = array(
	'SessionID' 
);

$options = array(
	'Encoding'
);

// Temporary to view data in browser for development
if( !$_POST && $_REQUEST )
{
	$_POST = $_REQUEST;
}

unset( $_POST['route'] );

if ( isset( $_POST ) )
{
	foreach( $_POST as $k=>$p )
	{
		if( !in_array( $k, $required ) && !in_array( $k, $options ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	foreach( $required as $r )
	{
		if( !isset( $_POST[$r] ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	
	// Get User data from sessionid
	$sess = new dbObject ( 'UserLogin' );
	$sess->Token = $_POST['SessionID'];
	if ( $sess->Load () )
	{
		$u = new dbObject ( 'SBookContact' );
		$u->UserID = $sess->UserID;
		if( !$u->Load () )
		{
			throwXmlError ( AUTHENTICATION_ERROR );
		}
	}
	else
	{
		throwXmlError ( SESSION_MISSING );
	}
	
	
	
	// Run Query	
	if ( $rows = $database->fetchObjectRows ( '
		SELECT g.* 
		FROM
			SBookCategory c,
			SBookCategory g,
			SBookCategoryRelation r 
		WHERE 
				c.Type = "Group" 
			AND c.Name = "Groups" 
			AND g.CategoryID = c.ID 
			AND g.NodeID = "0" 
			AND g.NodeMainID = "0" 
			AND r.CategoryID = g.ID
			AND r.ObjectType = "Users" 
			' . ( /*1!=1 && */isset( $u ) ? '
			AND r.ObjectID > 0 
			AND r.ObjectID = \'' . $u->UserID . '\' 
			' : '
			AND g.Privacy != "SecretGroup" 
			' ) . '
		GROUP BY g.ID 
		ORDER BY g.ID DESC 
		LIMIT 500 
	' ) )
	{
		$json = new stdClass();
		$json->Groups = [];
		$json->Listed = count( $rows );
		
		$xml .= '<Groups>';
		
		foreach ( $rows as $row )
		{
			$xml .= '<Group>';
			$xml .= '<ID>' . $row->ID . '</ID>';
			$xml .= '<UniqueID>' . $row->UniqueID . '</UniqueID>';
			$xml .= '<Name><![CDATA[' . $row->Name . ']]></Name>';
			$xml .= '<Privacy>' . $row->Privacy . '</Privacy>';
			//$xml .= '<Settings><![CDATA[' . $row->Settings . ']]></Settings>';
			$xml .= '<Description><![CDATA[' . $row->Description . ']]></Description>';
			$xml .= '<Owner>' . $row->Owner . '</Owner>';
			$xml .= '<ParentID>' . $row->ParentID . '</ParentID>';
			$xml .= '</Group>';
			
			$obj = new stdClass();
			$obj->ID = $row->ID;
			$obj->UniqueID = $row->UniqueID;
			$obj->Name = $row->Name;
			$obj->Privacy = $row->Privacy;
			//$obj->Settings = $row->Settings;
			$obj->Description = $row->Description;
			$obj->Owner = $row->Owner;
			$obj->ParentID = $row->ParentID;
			
			$json->Groups[] = $obj;
		}
		
		$xml .= '</Groups>';
		
		$xml .= '<Listed>' . count( $rows ) . '</Listed>';
		
		$out = ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' ? $json : $xml );
		
		outputXML ( $out );
	}
	
	throwXmlError ( EMPTY_LIST );

}

throwXmlError ( MISSING_PARAMETERS );

?>
