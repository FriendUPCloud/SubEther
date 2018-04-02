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

if( isset( $_POST['mid'] ) && $_POST['mid'] > 0 && $_POST['type'] != '' )
{
	switch( $_POST['type'] )
	{
		case 'folder':
			$m = new dbObject( 'Folder' );
			break;
		case 'file':
			$m = new dbObject( 'File' );
			break;
		case 'image':
			$m = new dbObject( 'Image' );
			break;
		default:
			die( 'fail' );
			break;
	}
	// TODO: Check on if the user has access to this change
	if( $m->Load( $_POST['mid'] ) )
	{
		// Temporary if UserID and CategoryID is null update
		if( $parent && !$m->UserID )
		{
			$m->UserID = $parent->cuser->UserID;
		}
		if( $parent && strtolower( $parent->folder->MainName ) != 'profile' && !$m->CategoryID )
		{
			$m->CategoryID = $parent->folder->CategoryID;
		}
		
		$m->Access = $_POST['value'];
		$m->DateModified = date ( 'Y-m-d H:i:s' );
		$m->Save();
		die( 'ok<!--separate-->' );
	}
}
die( 'fail' );

?>
