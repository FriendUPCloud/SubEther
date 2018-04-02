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

include( 'subether/components/meeting/data/teamspeak/ts3admin.class.php' );

$ts3_ip = '80.240.135.113';
$ts3_queryport = 10011;
$ts3_port = 9987;
$ts3_user = 'serveradmin';
$ts3_pass = 'password';

$ts3 = new ts3admin( $ts3_ip, $ts3_queryport );

if( $ts3->getElement( 'success', $ts3->connect() ) )
{
	#login as serveradmin
	//$ts3->login( $ts3_user, $ts3_pass );
	
	#get serverlist
	//$servers = $tsAdmin->serverList();
	
	#select teamspeakserver
	$ts3->selectServer( $ts3_port );
	
	#get clientlist
	$clients = $ts3->clientList();
}

if( isset( $_REQUEST['teamspeak'] ) ) die( print_r( $clients,1 ) . ' --' );

$tstr = 'test';

?>
