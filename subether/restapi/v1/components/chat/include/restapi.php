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
	'Url', 'SessionID', 'ID', 'SenderID',
	'ReceiverID', 'Message', 'Date'
);

// Node Connect
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
	
	$node = new dbObject( 'SNodes' );
	$node->Url = $_POST['Url'];
	if( $node->Load() )
	{
		$us = new dbObject( 'SBookContact' );
		$us->NodeMainID = $_POST['SenderID'];
		$us->Load();
		
		$m = new dbObject( 'SBookMail' );
		$m->SenderID = $us->ID;
		$m->ReceiverID = $_POST['ReceiverID'];
		$m->CategoryID = 0;
		$m->Type = 'im';
		$m->Message = $_POST['Message'];
		//$m->Date = $_POST['Date'];
		$m->Date = date( 'Y-m-d H:i:s' );
		$m->NodeID = $node->ID;
		$m->NodeMainID = $_POST['ID'];
		$m->Save();
	}
	
	if( $node->ID > 0 && $m->ID > 0 )
	{
		showXmlData ( $m->NodeMainID );
	}
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
