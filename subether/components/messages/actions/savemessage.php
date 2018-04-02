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

global $webuser;

$_POST[ 'u' ] = $_POST[ 'userid' ];
$_POST[ 'm' ] = $_POST[ 'message' ];

if( $_POST[ 'u' ] && $_POST[ 'm' ] )
{
	if ( $nuser = CheckNodeUser( $_POST[ 'u' ] ) )
	{
		require( 'subether/restapi/components/chat/functions/restapi.php' );
		die( 'fail' );
	}
	
	$m = new dbObject( 'SBookMail' );
	$m->SenderID = $webuser->ContactID;
	$m->ReceiverID = $_POST[ 'u' ];
	$m->CategoryID = 0;
	$m->Type = 'im';
	$m->Message = $_POST[ 'm' ];
	$m->Date = date( 'Y-m-d H:i:s' );
	$m->save();
	
	if( $m->ID > 0 ) die( 'ok<!--separate-->' );
	else die( 'fail' . print_r( $_POST,1 ) );
}
die( 'fail' . print_r( $_POST,1 ) );

?>
