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

include_once ( ( $root ? ( $root.'/' ) : '' ) . 'subether/classes/posthandler.class.php' );

$nodes = $database->fetchObjectRows( 'SELECT ID, Url, SessionID FROM SNodes WHERE SessionID != "" AND IsConnected = "1" AND IsDenied = "0" ORDER BY ID ASC' );
$group = $database->fetchObjectRow ( 'SELECT ID, Type, Name FROM SBookCategory WHERE Type = "Group" AND Name = "Groups" ORDER BY ID ASC' );

if( $nodes )
{
	foreach( $nodes as $node )
	{
		$ph = new PostHandler ( $node->Url . 'components/groups/' );
		$ph->AddVar ( 'Url', $node->Url );
		$ph->AddVar ( 'SessionID', $node->SessionID );
		$xml = simplexml_load_string ( ( $res = $ph->send () ) );
		$dat = $xml->items->Groups;
		if ( $xml->response == 'ok' && $dat->Group )
		{
			foreach( $dat->Group as $gr )
			{
				$g = new dbObject( 'SBookCategory' );
				$g->NodeID = $node->ID;
				$g->NodeMainID = (string)$gr->ID;
				$g->Load();
				$g->CategoryID = $group->ID;
				$g->Type = 'SubGroup';
				$g->Name = (string)$gr->Name;
				$g->Privacy = (string)$gr->Privacy;
				$g->Settings = (string)$gr->Settings;
				$g->Description = (string)$gr->Description;
				$g->Save();
			}
		}
		else 
		{
			//die ( 'Must reauthenticate!' );
		}
	}
}

?>
