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

if( $_POST['pid'] > 0 && !strstr( $_POST['pid'], '#' ) && $webuser->ID > 0 )
{
	$added = true;
	
	$b = new dbObject( 'SBookBookmarks' );
	$b->UserID = $webuser->ContactID;
	$b->Component = 'wall';
	if( !$b->Load() )
	{
		$b->DateCreated = date( 'Y-m-d H:i:s' );
	}
	$b->Bookmarks = json_obj_decode( $b->Bookmarks, 'array' );
	
	if( is_array( $b->Bookmarks ) )
	{
		if( in_array( $_POST['pid'], $b->Bookmarks ) )
		{
			foreach( $b->Bookmarks as $k=>$v )
			{
				if( $v == $_POST['pid'] )
				{
					// Remove bookmark
					unset( $b->Bookmarks[$k] );
					
					$added = false;
				}
			}
		}
		else
		{
			// Add bookmark
			$b->Bookmarks[] = $_POST['pid'];
		}
	}
	
	$b->Bookmarks = json_obj_encode( $b->Bookmarks );
	$b->DateModified = date( 'Y-m-d H:i:s' );
	$b->Save();
	
	die( 'ok<!--separate-->' . ( $added ? 'added ' : 'removed ' ) . ' ' . $b->ID );
}

die( 'fail' );

?>
