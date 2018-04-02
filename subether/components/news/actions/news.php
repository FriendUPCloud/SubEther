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

if( isset( $_POST[ 'delete' ] ) )
{
	$ns = new dbObject ( 'SBookNews' );
	if( $ns->Load( $_POST[ 'delete' ] ) )
	{
		$ns->Delete();
	}
}
else
{
	$ns = new dbObject ( 'SBookNews' );
	$ns->CategoryID = $folder->CategoryID;
	if( isset( $_POST[ 'nid' ] ) && $_POST[ 'nid' ] != 'Current' )
	{
		if( !$ns->Load( $_POST[ 'nid' ] ) )
		{
			$ns->DateAdded = date( 'Y-m-d H:i:s' );
		}
		$ns->DateModified = date( 'Y-m-d H:i:s' );
		$ns->Tags = '';
	}
	else if( isset( $_POST[ 'nid' ] ) && $_POST[ 'nid' ] == 'Current' )
	{
		$ns->Tags = 'Current';
		if( !$ns->Load() )
		{
			$ns->DateAdded = date( 'Y-m-d H:i:s' );
		}
		$ns->DateModified = date( 'Y-m-d H:i:s' );
	}
	else 
	{
		$ns->Tags = 'Current';
		if( !$ns->Load() )
		{
			$ns->DateAdded = date( 'Y-m-d H:i:s' );
		}
		$ns->DateModified = date( 'Y-m-d H:i:s' );
		$ns->Tags = '';
	}
	$ns->Title = $_POST[ 'Title' ];
	$ns->Leadin = $_POST[ 'Leadin' ];
	$ns->Article = $_POST[ 'Article' ];
	$ns->Type = $_POST[ 'Type' ];
	$ns->PostedID = $webuser->ID;
	$ns->IsPublished = $_POST[ 'Status' ] == 'IsPublished' ? 1 : 0;
	$ns->IsSticky = $_POST[ 'Status' ] == 'IsSticky' ? 1 : 0;
	$ns->IsFocus = $_POST[ 'Status' ] == 'IsFocus' ? 1 : 0;
	$ns->Save();
}

die( 'ok<!--separate-->Saved' );

?>
