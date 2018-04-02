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

if( $parent && $_POST )
{
	$m = new dbObject ( 'SBookMessage' );
	$m->Date = date ( 'Y-m-d H:i:s' );
	$m->DateModified = $m->Date;
	$m->Message = $_POST[ 'Content' ];
	$m->Options = $_POST['Options'];
	$m->Type = 'vote';
	$m->Access = ( $_POST['Access'] ? $_POST['Access'] : 0 );
	$m->CategoryID = $parent->folder->CategoryID;
	$m->SenderID = $parent->webuser->ContactID;
	$m->ThreadID = ( $_POST['ThreadID'] > 0 ? $_POST['ThreadID'] : 0 );
	if( strtolower( $parent->folder->MainName ) == 'profile' )
	{
		$m->ReceiverID = $parent->cuser->ContactID;
	}
	$m->Save();
	
	die ( 'ok<!--separate-->' . $m->ID );
}

die( 'fail' );

?>
