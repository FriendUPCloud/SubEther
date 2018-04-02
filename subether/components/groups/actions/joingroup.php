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

if( $webuser->ID > 0 && isset( $_POST[ 'groupid' ] ) )
{
	// Old method - TODO: Remove when every trace of this is deleted in the system
	$c = new dbObject( 'SBookCategoryRelation' );
	$c->ObjectType = 'Users';
	$c->CategoryID = $_POST[ 'groupid' ];
	$c->ObjectID = $webuser->ID;
	$c->Load();
	$c->Save();
	
	// New method
	$a = new dbObject( 'SBookCategoryAccess' );
	$a->CategoryID = $_POST[ 'groupid' ];
	$a->UserID = $webuser->ContactID;
	$a->ContactID = $webuser->ContactID;
	if( !$a->Load() )
	{
		$a->Read = 1;
		$a->Write = 1;
		$a->Delete = 1;
		$a->Save();
	}
	
	die( 'ok<!--separate-->' );
}
die( 'fail' );

?>
