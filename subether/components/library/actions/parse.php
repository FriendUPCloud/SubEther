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

if( isset( $_POST['data'] ) )
{
	$_POST[ 'data' ] = json_decode( stripslashes( $_POST[ 'data' ] ) );
	
	$lib = new Library ();
	if( strtolower( $folder->MainName ) != 'profile' )
	{
		$lib->CategoryID = $folder->CategoryID;
	}
	if( $_POST[ 'pid' ] )
	{
		$lib->FileFolder = $_POST[ 'pid' ];
	}
	$lib->SaveParsedData( $_POST[ 'data' ] );
	
	die( 'ok<!--separate-->' . $lib->FolderID . '<!--separate-->' . $lib->FileID . '<!--separate-->' . print_r( $lib,1 ) );
}
die( 'fail' );

?>
