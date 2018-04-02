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

include_once ( 'subether/functions/userfuncs.php' );

$required = array(
	'SessionID', 'Component', 'Type', 'Action'
);

$options = array(
	'ContactID', 'CategoryID', 'Source', 'Data'
);

// Temporary to view data in browser for development
if( !$_POST && $_REQUEST )
{
	$_POST = $_REQUEST;
}
unset( $_POST['route'] );

// User action log from external system
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
	
	//
	
	if( $log = LogStats( $_POST['Component'], $_POST['Action'], $_POST['Type'], $u->ID, $_POST['ContactID'], $_POST['CategoryID'], ( $_POST['Source'] ? $_POST['Source'] : 'api' ) ) )
	{
		showXmlData ( $log->Counter );
	}
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
