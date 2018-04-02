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

global $document, $webuser;

statistics( $parent->module, 'authentication' );

$root = 'subether/';
$cbase = 'subether/components/authentication';

include_once ( 'subether/classes/mail.class.php' );
include_once ( 'subether/include/functions.php' );
include_once ( $cbase . '/include/functions.php' );


// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/authentication.css' );
$document->addResource ( 'javascript', $root . '/javascript/md5.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/authentication.js' );

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	output( 'failed action request - authentication' );
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
	output( 'failed function request - authentication' );
}
else
{
	if ( $parent->position == 'MiddleCol' )
	{
		// TODO: Temporary defined by config, quickfix that needs to be changed and made dynamic later
		if ( defined( 'HIDE_LOGIN_SCREEN' ) && HIDE_LOGIN_SCREEN && $webuser->ID > 0 )
		{
			header( 'Location: ' . BASE_URL . 'home/' );
		}
		
		include_once ( $cbase . '/functions/loginform.php' );
	}
	else if ( $parent->position == 'Top' )
	{
		include_once ( $cbase . '/functions/component.php' );
	}
	
	// Make sure we have these
	if ( !isset ( $Component ) )
	{
		$Component = new stdclass ();
	}
	// Set base dir
	$Component->cbase = $cbase;
	
	$Component->Content = $str;
}

statistics( $parent->module, 'authentication' );

?>
