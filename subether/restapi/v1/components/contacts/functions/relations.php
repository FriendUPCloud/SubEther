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

global $database, $webuser;

include_once ( 'subether/classes/posthandler.class.php' );

$login = new dbObject( 'UserLogin' );
$login->DataSource = 'site';
$login->UserID = '113';
$login->Load();

$cuser = new dbObject( 'SBookContact' );
$cuser->UserID = '113';
$cuser->Load();

$ph = new PostHandler ( NODE_URL . 'components/contacts/relations/' );
$ph->AddVar ( 'SessionID', $login->Token );
$ph->AddVar ( 'ID', $cuser->ID );
$res = $ph->send();
//die( print_r( $res,1 ) . ' ..' );
if( $res && substr( $res, 0, 5 ) == "<?xml" )
{
	$xml = simplexml_load_string ( trim( $res ) );
	
	if( $xml->response == 'ok' )
	{
		die( print_r( $xml,1 ) . ' --' );
	}
	else
	{
		die( print_r( $xml,1 ) . ' ..' );
	}
}
die( 'fail' );

?>
