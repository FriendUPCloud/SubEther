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

statistics( $parent->module, 'notification' );

$root = 'subether';
$cbase = 'subether/components/notification';

include_once ( $root . '/components/wall/include/functions.php' );
include_once ( $cbase . '/include/functions.php' );

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/notification.css' );
$document->addResource ( 'javascript', $cbase . '/javascript/desktopnotify.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/notification.js' );
$document->addResource ( 'javascript', $root . '/javascript/messagehandler.js' );
$document->addResource ( 'javascript', $root . '/components/chat/javascript/chatObject.js' );
$document->addResource ( 'javascript', $root . '/components/chat/javascript/audio.js' );

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
    if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	output( 'failed action request - notification' );
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
	output( 'failed function request - notification' );
}
else
{
	include ( $cbase . '/functions/notification.php' );

	$Component->Messages = str_replace( 'messages<!--separate-->', '', $messages );
	$Component->Contacts = str_replace( 'contacts<!--separate-->', '', $contacts );
	$Component->Notices = str_replace( 'notices<!--separate-->', '', $notices );
	$Component->Cart = str_replace( 'cart<!--separate-->', '', $cart );
}

statistics( $parent->module, 'notification' );

?>
