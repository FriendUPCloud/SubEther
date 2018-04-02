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

if ( $_POST['security'] != '' && $_POST['name'] != '' )
{
	$count = $database->fetchObjectRow( 'SELECT MAX(Account) AS Account FROM SBookAccounts ORDER BY Account ASC' );
	if ( $verify = $database->fetchObjectRow( 'SELECT * FROM SBookAccounts WHERE UserID = \'' . $webuser->ID . '\' ORDER BY ID ASC' ) )
	{
		if ( $verify->Security != str_replace( array( '.', ' ' ), array( '', '' ), trim( $_POST['security'] ) ) )
		{
			die( '"Social Security Number" doesnt match with user account' );
		}
	}
	else if ( $verify = $database->fetchObjectRow( 'SELECT * FROM SBookAccounts WHERE Security = \'' . str_replace( array( '.', ' ' ), array( '', '' ), trim( $_POST['security'] ) ) . '\' ORDER BY ID ASC' ) )
	{
		if ( $verify->UserID != $webuser->ID )
		{
			die( '"Social Security Number" allready exists on a different profile' );
		}
	}
	
	$a = new dbObject( 'SBookAccounts' );
	$a->UniqueID = UniqueKey();
	$a->Type = 'Annual';
	$a->UserID = $webuser->ID;
	$a->Security = str_replace( array( '.', ' ' ), array( '', '' ), trim( $_POST['security'] ) );
	$a->Account = ( $count->Account ? ( $count->Account + 1 ) : '10000000000' );
	$a->Name = $_POST['name'];
	$a->Verified = 0;
	$a->DateCreated = date( 'Y-m-d H:i:s' );
	$a->Save();
	
	if( $a->ID > 0 )
	{
		die( 'ok<!--separate-->' );
	}
}

die( 'fail' );

?>
