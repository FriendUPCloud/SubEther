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

include_once ( 'subether/functions/globalfuncs.php' );

$required = array(
	'SessionID', 'ID', 'Vote'
);

if ( isset( $_POST ) )
{
	foreach( $_POST as $k=>$p )
	{
		if( !in_array( $k, $required ) )
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
	$sess->Token = $_POST[ 'SessionID' ];
	if ( $sess->Load () )
	{
		$u = new dbObject ( 'SBookContact' );
		$u->UserID = $sess->UserID;
		if( !$u->Load () )
		{
			throwXmlError ( AUTHENTICATION_ERROR );
		}
	}
	
	$found = false;
	
	$m = new dbObject( 'SBookMessage' );
	
	if( $m->Load( $_POST['ID'] ) )
	{
		$m->Rating = json_obj_decode( $m->Rating );
		
		if( !isset( $m->Rating->Votes ) )
		{
			$m->Rating->Votes = new stdClass();
		}
		
		foreach( $m->Rating->Votes as $check )
		{
			if( is_array( $check ) && in_array( $u->ID, $check ) )
			{
				$found = true;
			}
		}
		
		if( !$found )
		{
			if( !isset( $m->Rating->Votes->{$_POST['Vote']} ) )
			{
				$m->Rating->Votes->{$_POST['Vote']} = array();
			}
			
			$m->Rating->Votes->{$_POST['Vote']}[] = $u->ID;
			
			$m->Rating = json_obj_encode( $m->Rating );
			$m->DateModified = date ( 'Y-m-d H:i:s' );
			$m->Save();
		}
		
		showXmlData ( $m->ID );
	}
	
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
