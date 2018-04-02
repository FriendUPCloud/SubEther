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

if( $webuser->ContactID > 0 && ( isset( $_POST['name'] ) || isset( $_POST['pid'] ) ) )
{
	$p = new dbObject( 'SBookProducts' );
	
	if( $_POST['pid'] > 0 )
	{
		$p->Load( $_POST['pid'] );
	}
	
	$p->Name 			= ( isset( $_POST['name'] ) ? $_POST['name'] : $p->Name );
	$p->Info 			= ( isset( $_POST['info'] ) ? $_POST['info'] : $p->Info );
	$p->Details 		= ( isset( $_POST['details'] ) ? $_POST['details'] : $p->Details );
	$p->Images 			= ( isset( $_POST['images'] ) ? $_POST['images'] : $p->Images );
	$p->Price 			= ( isset( $_POST['price'] ) ? $_POST['price'] : $p->Price );
	$p->Access 			= ( isset( $_POST['access'] ) ? $_POST['access'] : ( $p->Access ? $p->Access : 1 ) );
	$p->UserID 			= $webuser->ContactID;
	$p->CategoryID 		= $parent->folder->CategoryID;
	$p->UniqueID 		= ( $p->UniqueID ? $p->UniqueID : UniqueKey() );
	$p->DateCreated 	= ( $p->DateCreated ? $p->DateCreated : date( 'Y-m-d H:i:s' ) );
	$p->DateModified 	= date( 'Y-m-d H:i:s' );
	
	$p->Save();
	
	die( 'ok<!--separate-->' . $p->ID );
}

die( 'fail' );

?>
