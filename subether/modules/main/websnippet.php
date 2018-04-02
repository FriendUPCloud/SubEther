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

// Using our new engine

statistics( 'main' );

i18nAddLocalePath ( 'subether/locale' );

// Global helper funcs ---------------------------------------------------------
include_once ( 'lib/classes/dbObjects/dbFile.php' );
include_once ( 'subether/functions/userfuncs.php' );
include_once ( 'subether/functions/modulefuncs.php' );
include_once ( 'subether/functions/componentfuncs.php' );
include_once ( 'subether/functions/globalfuncs.php' );
include_once ( 'subether/classes/htmlparser.class.php' );
include_once ( 'subether/classes/calendar.class.php' );
include_once ( 'subether/classes/category.class.php' );
include_once ( 'subether/classes/media.class.php' );
include_once ( 'subether/classes/sbook.class.php' );
include_once ( 'subether/classes/library.class.php' );
include_once ( 'subether/classes/sms.class.php' );
include_once ( 'subether/classes/mail.class.php' );
include_once ( 'subether/classes/posthandler.class.php' );
include_once ( 'subether/include/dbcheck.php' );
include_once ( 'subether/include/cleanup.php' );
include_once ( 'subether/include/functions.php' );
include_once ( 'subether/include/calendar.php' );

// Setup resources -------------------------------------------------------------
$document->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
$document->addResource ( 'javascript', 'lib/javascript/bajax.js' );
$document->addResource ( 'javascript', 'lib/javascript/gui.js' );
$document->addResource ( 'javascript', 'subether/javascript/json.js' );
$document->addResource ( 'javascript', 'subether/javascript/jsdate.js' );
$document->addResource ( 'javascript', 'subether/javascript/functions.js' );
$document->addResource ( 'javascript', 'subether/javascript/editor.js' );
$document->addResource ( 'javascript', 'subether/javascript/jaxqueue.js' );
$document->addResource ( 'javascript', 'subether/javascript/heartbeat.js' );
$document->addResource ( 'javascript', 'subether/javascript/windowslideshow.js' );
$document->addResource ( 'javascript', 'subether/javascript/popupwindow.js' );
$document->addResource ( 'javascript', 'subether/javascript/formcheck.js' );

// Set module
$root = 'subether/';
$module = 'main';
$path = 'en/home/'; // FIXME: Make removable
$mpath = 'en/home/'; // FIXME: Make removable
$mbase = 'subether/modules/main'; // FIXME: Make removable
$route = $_REQUEST['route'];
$url = explode( '/', strtolower( $_REQUEST['route'] ) );

// Setup base templates --------------------------------------------------------
// TODO: Move variables to $parent
$tmp = new cPTemplate ( $mbase . '/templates/module.php' );
$tmp->module = $module;
$tmp->mbase = $mbase;
$tmp->mode = $url[4];
//$tmp->switch = switchCalendarMode( false, $_REQUEST[ 'r' ] );
$tmp->path = $path;
$tmp->mpath = $mpath;
$tmp->route = $route;
$tmp->url = $url;
$tmp->curl = getUrl();
$tmp->page =& $page;
$tmp->LeftCol = '';
$tmp->MiddleCol = '';
$tmp->RightCol = '';
$tmp->Chat = '';

// Setup parent ----------------------------------------------------------------
$parent = new stdClass();
$parent->module = $tmp->module;
$parent->mbase = $tmp->mbase;
$parent->mode = $tmp->mode;
//$parent->switch = $tmp->switch;
$parent->path = $tmp->path;
$parent->mpath = $tmp->mpath;
$parent->route = $tmp->route;
$parent->url = $tmp->url;
$parent->curl = $tmp->curl;

// If we have userinfo that's valid, render the module -------------------------
$cuser = setupUserData ();
$user = setupUserData ();
$parent->cuser =& $cuser;
$parent->webuser =& $user;

// Check if user has default folders if not make them.
if ( !isset ( $_SESSION[ 'subether_check_defcats' ] ) )
{
	$_SESSION[ 'subether_check_defcats' ] = 1;
	checkDefaultCategories();
}	
// Check for user global functions -----------------------------------------
if( isset( $_REQUEST[ 'global' ] ) && ( isset( $_REQUEST[ 'function' ] ) || isset( $_REQUEST[ 'action' ] ) ) )
{
	if ( file_exists ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' ) )
	{
		include ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' );
	}
	else if ( file_exists ( $root . '/include/' . $_REQUEST[ 'action' ] . '.php' ) )
	{
		include ( $root . '/include/' . $_REQUEST[ 'action' ] . '.php' );
	}
	die( 'failed function request - main module' );
}
	
// Get component by ajax request -----------------------------------------------
if( isset( $_REQUEST[ 'component' ] ) ) 
{
	die( 'ok<!--separate-->' . GetComponentByRequest( $_REQUEST[ 'component' ], $parent ) );
}

// Render / calc stats ---------------------------------------------------------
statistics( 'main' );

if( isset( $_REQUEST['debug'] ) )
{
	die( print_r( $_SESSION['statistics'],1 ) );
}

?>
