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

if ( $_POST )
{
	$a = new dbObject( 'SBookApiAccounts' );
	$a->ID = $_POST['aid'];
	if ( !$a->Load() )
	{
		$a->UniqueID = UniqueKey();
		$a->UserID = $webuser->ID;
		$a->IsGlobal = 1;
		$a->DateCreated = date( 'Y-m-d H:i:s' );
	}
	
	$a->Name = $_POST['Name'];
	$a->App = $_POST['App'];
	$a->Url = $_POST['Url'];
	$a->Username = $_POST['Username'];
	$a->Password = ( trim( $_POST['Password'] ) && strlen( trim( $_POST['Password'] ) ) != 64 ? hex_sha256( trim( $_POST['Password'] ) ) : trim( $_POST['Password'] ) );
	$a->DateModified = date( 'Y-m-d H:i:s' );
	$a->Save();
	
	die( 'ok<!--separate-->' . $a->ID );
}

die( 'fail' );

?>
