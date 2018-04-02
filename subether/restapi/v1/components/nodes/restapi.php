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
	'UniqueID','PublicKey', 'Url', 'Name', 'Version', 'Owner',
	'Email', 'Location', 'Users', 'Open', 'Created'
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
	$node->UniqueID = $_POST['UniqueID'];
	$node->Load();
	if ( !$node->IsDenied )
	{
		$node->PublicKey = $_POST['PublicKey'];
		$node->Url = $_POST['Url'];
		$node->Name = $_POST['Name'];
		$node->Version = $_POST['Version'];
		$node->Owner = $_POST['Owner'];
		$node->Email = $_POST['Email'];
		$node->Location = $_POST['Location'];
		$node->Users = $_POST['Users'];
		$node->Open = $_POST['Open'];
		$node->DateCreated = $_POST['Created'];
		$node->DateModified = date( 'Y-m-d H:i:s' );
		$node->IsPending = 0;
		$node->IsConnected = 1;
		$node->Save();
		
		if( $node->ID > 0 )
		{
			$main = getNodeData( $root );
			
			showXmlData ( $main->PublicKey, 'publickey' );
		}
	}
	
	// Give access denied message
	throwXmlError ( ACCESS_DENIED );
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
