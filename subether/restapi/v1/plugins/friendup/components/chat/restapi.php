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

//global $database, $webuser;
//die( 'hm ???? --- adadadasdasd hva' );
include_once ( BASE_DIR . '/subether/classes/posthandler.class.php' );
//die( 'hm ???? ---' );

$sessionid = '845061cf57d10a4a6ec80b97b49f02d4e48cd44f';

$ph = new PostHandler ( 'https://bomb.openfriendup.net:6502/webclient/app.html?app=FriendChat&sessionid=' . $sessionid );
$res = $ph->send();
die( $res . ' .. ' );
if ( $res && ( $json = json_decode( trim( $res ) ) ) )
{
	if ( $json->ErrorMessage == 0 )
	{
		die( print_r( $json,1 ) . ' -- ok ' );
	}
	else
	{
		die( print_r( $json,1 ) . ' .. fail' );
	}
}

die( 'fail' );

?>
