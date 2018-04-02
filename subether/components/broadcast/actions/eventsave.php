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

// --- Event -----------------------------------------------------------------------------------------------------------

if( isset( $_POST['name'] ) || isset( $_POST['eid'] ) )
{
	$e = new dbObject( 'SBookEvents' );
	if( $_POST['eid'] ) $e->Load( $_POST['eid'] );
	if( $_POST['name'] ) $e->Name = $_POST['name'];
	if( $_POST['type'] ) $e->Type = $_POST['type'];
	if( $_POST['start'] ) $e->DateStart = date( 'Y-m-d H:i:S', strtotime( $_POST['start'] ) );
	if( $_POST['url'] ) $e->ExternalUrl = $_POST['url'];
	if( !isset( $_POST['eid'] ) )
	{
		$e->Component = 'broadcast';
		$e->Access = 1;
		$e->UserID = $webuser->ContactID;
		$e->CategoryID = $parent->folder->CategoryID;
		$e->DateCreated = date( 'Y-m-d H:i:s' );
	}
	$e->DateModified = date( 'Y-m-d H:i:s' );
	$e->Save();
	
	die( 'ok<!--separate-->' );
}

die( 'fail' );

?>
