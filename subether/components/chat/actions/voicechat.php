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

if ( $_POST['u'] && $_POST['m'] )
{
	$m = new dbObject( 'SBookMail' );
	$m->UniqueID = UniqueKey();
	$m->SenderID = $webuser->ContactID;
	$m->ReceiverID = $_POST['u'];
	$m->CategoryID = 0;
	$m->Type = 'vm';
	
	$m->Message = $_POST['m'];
	
	$m->Date = date( 'Y-m-d H:i:s' );
	$m->Save();
	
	UserActivity( 'messages', 'lastmessage', $m->SenderID, $m->ReceiverID, $m->ID, '' );
	
	if ( $m->ID > 0 )
	{
		die( 'ok<!--separate-->' );
	}
}

die( 'fail' . print_r( $_POST,1 ) );

?>
