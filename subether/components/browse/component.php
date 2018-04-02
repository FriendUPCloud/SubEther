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

statistics( $parent->module, 'browse' );

$root = 'subether/';
$cbase = 'subether/components/browse';
$folder = $Component->parent->folder;

i18nAddLocalePath ( $cbase . '/locale' );

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/network.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/browse.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/images.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/files.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/torrents.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/streaming.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/contacts.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/groups.css' );
$document->addResource ( 'javascript', $cbase . '/javascript/browse.js' );
$document->addResource ( 'javascript', $root . 'components/profile/javascript/profile.js' );
$document->addResource ( 'javascript', $root . 'components/groups/javascript/groups.js' );

include_once ( $cbase . '/include/functions.php' );
include_once ( $root . 'components/wall/include/functions.php' );

// Template: Module, Component, CategoryID, Status
SessionSet ( $parent->module, 'browse', $parent->folder->CategoryID, 'online' );

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	die( 'failed action request - browse' );
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
	die( 'failed function request - browse' );
}

$mstr = ''; 

if( $_REQUEST[ 'r' ] == 'videos' )
{
	include ( $cbase . '/functions/browse.php' );
}
else if( $_REQUEST[ 'r' ] == 'images' )
{
	include ( $cbase . '/functions/images.php' );
}
else if( $_REQUEST[ 'r' ] == 'files' )
{
	include ( $cbase . '/functions/files.php' );
}
else if( $_REQUEST[ 'r' ] == 'streaming' )
{
	include ( $cbase . '/functions/streaming.php' );
}
else if( $_REQUEST[ 'r' ] == 'torrents' )
{
	include ( $cbase . '/functions/torrents.php' );
}
else if( $_REQUEST[ 'r' ] == 'groups' )
{
	include ( $cbase . '/functions/groups.php' );
}
else if( $_REQUEST[ 'r' ] == 'contacts' )
{
	include ( $cbase . '/functions/contacts.php' );
}
else
{
	/*$mstr .= '<h4><span>Videos</span></h4>';
	include ( $cbase . '/functions/browse.php' );
	//$mstr .= '<h4><span>Torrents</span></h4>';
	//include ( $cbase . '/functions/torrents.php' );
	//$mstr .= '<h4><span>Streaming</span></h4>';
	//include ( $cbase . '/functions/streaming.php' );
	$mstr .= '<h4><span>Groups</span></h4>';
	include ( $cbase . '/functions/groups.php' );
	$mstr .= '<h4><span>Contacts</span></h4>';
	include ( $cbase . '/functions/contacts.php' );*/
	include ( $cbase . '/functions/network.php' );
}

$Component->Content = $mstr;

statistics( $parent->module, 'browse' );

?>
