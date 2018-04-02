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

// TODO: Check if user has admin access

if ( isset( $parent->folder->CategoryID ) && $_POST && $webuser->ID > 0 )
{
	$a = new dbObject( 'SBookCategoryAccess' );
	if ( isset( $_POST['ID'] ) )
	{
		$a->ID = $_POST['ID'];
		$a->CategoryID = $parent->folder->CategoryID;
		if ( $a->Load() )
		{
			$a->MemberID = $_POST['MemberID'];
			$a->Save();
		}
		
		if ( $a->ID > 0 )
		{
			die( 'ok<!--separate-->' . $a->ID );
		}
		
		die( 'fail<!--separate-->couldn\'t save' );
	}
}

die( 'fail' );

?>
