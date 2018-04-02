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

global $webuser, $database;

if( $webuser && $webuser->ID && $_POST[ 'pid' ] && $_POST[ 'message' ] )
{
	$m = new dbObject( 'SBookChat' );
	$m->Type = 'im';
	$m->SenderID = $webuser->ID;
	$m->CategoryID = $_POST[ 'pid' ];
	$m->Message = $_POST[ 'message' ];
	$m->Date = date( 'Y-m-d H:i:s' );
	$m->save();
	
	if( $m->ID > 0 ) die( 'ok<!--separate-->' );
	else die( 'fail' . print_r( $_POST,1 ) );
}
die( 'fail' . print_r( $_POST,1 ) );

?>
