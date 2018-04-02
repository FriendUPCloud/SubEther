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

global $document;

statistics( $parent->module, 'admin' );
	
$root = 'subether/';
$cbase = 'subether/components/admin';

include_once ( 'subether/components/groups/include/functions.php' );
include_once ( $cbase . '/include/dbcheck.php' );
include_once ( $cbase . '/include/functions.php' );

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/admin.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/case.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/mailing.css' );
$document->addResource ( 'javascript', $cbase . '/javascript/calendar.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/admin.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/mailing.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/orders.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/members.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/accounting.js' );

// Assign parent ---------------------------------------------------------------
$parent = $Component->parent;

// Template: Module, Component, CategoryID, Status
SessionSet ( $parent->module, 'admin', $parent->folder->CategoryID, 'online' );

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	die( 'failed action request - admin' );
}
// Check for user functions ----------------------------------------------------
else if ( isset( $_REQUEST[ 'function' ] ) )
{
	if ( file_exists ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' ) )
    {
       include ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' );
    }
	else if ( file_exists ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' ) )
	{
		include ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' );
	}
	die( 'failed function request - admin' );
}

if( $_REQUEST[ 'r' ] == 'mailing' )
{
	include ( $cbase . '/functions/mailing.php' );
}
else if( $_REQUEST[ 'r' ] == 'orders' )
{
	//include ( $cbase . '/functions/orders.php' );
	
	include ( 'subether/components/orders/component.php' );
}
else if( $_REQUEST[ 'r' ] == 'members' )
{
	$document->addResource ( 'javascript', 'subether/components/orders/javascript/calendar.js' );
	$document->addResource ( 'javascript', 'subether/components/orders/javascript/select.js' );
	$document->addResource ( 'javascript', 'subether/components/orders/javascript/orders.js' );
	
	include ( $cbase . '/functions/navigation.php' );
	include ( $cbase . '/functions/members.php' );
}
else if( $_REQUEST[ 'r' ] == 'clients' )
{
	include ( $cbase . '/functions/clients.php' );
}
else if( $_REQUEST[ 'r' ] == 'accounting' )
{
	include ( $cbase . '/functions/accounting.php' );
}
else if( $_REQUEST[ 'r' ] == 'import' )
{
	include ( $cbase . '/functions/import.php' );
}
else if( $_REQUEST[ 'r' ] == 'shopdb' )
{
	include ( $cbase . '/functions/shopdb.php' );
}
else if( $_REQUEST[ 'r' ] == 'recruitment' )
{
	include ( $cbase . '/functions/recruitment.php' );
}
else if( $_REQUEST[ 'r' ] == 'budget' )
{
	include ( $cbase . '/functions/admin.php' );
}
else if( $_REQUEST[ 'r' ] == 'case' )
{
	include ( $cbase . '/functions/case.php' );
}
else
{
	//include ( $cbase . '/functions/orders.php' );
	//include ( $cbase . '/functions/mailing.php' );
	//include ( $cbase . '/functions/case.php' );
	
	include ( 'subether/components/orders/component.php' );
}

if( $parent->folder->Permission == 'admin' || $parent->folder->Permission == 'owner' || isset( $parent->access->IsAdmin ) )
{
	$Component->Navigation = $dstr;
	$Component->Content = $str;
}
else
{
	$parent->tabs['admin'] = 0;
}

statistics( $parent->module, 'admin' );

?>
