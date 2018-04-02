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

if( $node->ID > 0 && $sc->ID > 0 && $us->ID > 0 )
{
	$ph = new PostHandler ( $node->Url . 'authenticate/' );
	$ph->AddVar ( 'Source', 'node' );
	$ph->AddVar ( 'Username', $sc->Email );
	$ph->AddVar ( 'Password', $sc->AuthKey );
	$xml = simplexml_load_string ( $ph->send () );
	
	if ( $xml->response == 'ok' && $xml->sessionid )
	{
		$sess = new dbObject ( 'UserLogin' );
		$sess->UserID = $us->ID;
		if( !$sess->Load() )
		{
			$sess->DateCreated = date( 'Y-m-d H:i:s' );
		}
		$sess->DataSource = 'node';
		$sess->Token = (string)$xml->sessionid;
		$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
		$sess->DateExpired = date( 'Y-m-d H:i:s', ( mktime () + ( 60 * 60 ) ) );
		$sess->Save();
		
		// Update login
		$ul = new dbObject ( 'Users' );
		if( $sess->ID > 0 && $ul->Load( $us->ID ) )
		{
			$ul->DateLogin = date ( 'Y-m-d H:i:s' );
			$ul->Save();
		}
	}
	else 
	{
		die( print_r( $xml,1 ) . ' ..' );
	}
}

?>
