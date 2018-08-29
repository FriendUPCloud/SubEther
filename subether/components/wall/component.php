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

statistics( $parent->module, 'wall' );

$width = '743';
$height = '420';

$root = 'subether/';
$cbase = 'subether/components/wall';

i18nAddLocalePath ( $cbase . '/locale' );

include_once ( $root . '/components/notification/include/functions.php' );
include_once ( $root . '/classes/library.class.php' );
include_once ( $cbase . '/include/functions.php' );


// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/wall.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/editor.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/arenaeditor.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/share.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/parse.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/media.css' );
$document->addResource ( 'javascript', $cbase . '/javascript/wall.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/editor.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/media.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/presentation.js' );

$document->addResource ( 'javascript', $root . 'components/library/javascript/jsupload.js' );

// Include arena resources
$document->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
//$document->addResource ( 'javascript', 'lib/javascript/texteditor.js' );
$document->addResource ( 'javascript', 'lib/javascript/bajax.js' );

$user = $parent->webuser;
$cuser = $parent->cuser;
$folder = $parent->folder;
$module = $parent->module;
$path = $parent->path;
$mpath = $parent->mpath;

// Template: Module, Component, CategoryID, Status
SessionSet ( $parent->module, 'wall', $parent->folder->CategoryID, 'online' );

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) && !isset( $_REQUEST['bypass'] ) )
{
	if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	output( 'failed action request - wall' );
}
// Check for user functions ----------------------------------------------------
else if ( isset( $_REQUEST[ 'function' ] ) && !isset( $_REQUEST['bypass'] ) )
{
	if ( file_exists ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' ) )
    {
       include ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' );
    }
	else if ( file_exists ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' ) )
	{
		include ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' );
	}
	output( 'failed function request - wall' );
}
else
{
	include ( $cbase . '/functions/sharedposts.php' );
	$Component->Content = $str;
}

statistics( $parent->module, 'wall' );

?>
