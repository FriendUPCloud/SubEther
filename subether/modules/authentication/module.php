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

global $document, $webuser, $path;

i18nAddLocalePath ( 'subether/locale' );

$root = 'subether/';
$module = 'authentication';
$domain = $domain;
$path = $path;
$mpath = $path . 'authentication/';
$mbase = 'subether/modules/authentication';
$route = $_REQUEST['route'];
$url = explode( '/', strtolower( $_REQUEST['route'] ) );

// Global helper funcs -----------------------------------------------------------
include_once ( 'lib/classes/dbObjects/dbFile.php' );
include_once ( 'subether/functions/userfuncs.php' );
include_once ( 'subether/functions/modulefuncs.php' );
include_once ( 'subether/functions/componentfuncs.php' );
include_once ( 'subether/functions/globalfuncs.php' );
include_once ( 'subether/classes/calendar.class.php' );
include_once ( 'subether/classes/category.class.php' );
include_once ( 'subether/classes/media.class.php' );
include_once ( 'subether/classes/sbook.class.php' );
include_once ( 'subether/classes/sms.class.php' );
include_once ( 'subether/classes/mail.class.php' );
include_once ( 'subether/include/dbcheck.php' );
include_once ( 'subether/include/functions.php' );
include_once ( 'subether/include/calendar.php' );

// If authenticated set body class
if( $webuser->ID > 0 )
{
	$document->_bodyClasses = ( $document->_bodyClasses ? $document->_bodyClasses . ' authentication' : 'authentication' );
}

// Setup resources ---------------------------------------------------------------
$document->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
$document->addResource ( 'javascript', 'lib/javascript/bajax.js' );
$document->addResource ( 'javascript', 'lib/javascript/gui.js' );
$document->addResource ( 'stylesheet', 'subether/css/home.css' );
$document->addResource ( 'stylesheet', 'subether/css/panel.css' );
$document->addResource ( 'stylesheet', 'subether/css/contacts.css' );
$document->addResource ( 'stylesheet', 'subether/css/popupwindow.css' );
$document->addResource ( 'stylesheet', 'subether/css/windowslideshow.css' );
$document->addResource ( 'stylesheet', 'subether/components/events/css/events.css' );
$document->addResource ( 'stylesheet', 'subether/components/groups/css/group.css' );

$document->addResource ( 'javascript', 'subether/thirdparty/javascript/cryptodeps.js' );
$document->addResource ( 'javascript', 'subether/javascript/fcrypto.js' );

$document->addResource ( 'javascript', 'subether/javascript/php.js' );
$document->addResource ( 'javascript', 'subether/javascript/utf8.js' );
$document->addResource ( 'javascript', 'subether/javascript/jsdate.js' );
$document->addResource ( 'javascript', 'subether/javascript/functions.js' );
$document->addResource ( 'javascript', 'subether/javascript/jaxqueue.js' );
$document->addResource ( 'javascript', 'subether/javascript/heartbeat.js' );
$document->addResource ( 'javascript', 'subether/javascript/maintenance.js' );
$document->addResource ( 'javascript', 'subether/javascript/windowslideshow.js' );
$document->addResource ( 'javascript', 'subether/javascript/popupwindow.js' );
$document->addResource ( 'javascript', 'subether/javascript/formcheck.js' );
$document->addResource ( 'javascript', 'subether/javascript/responsive.js' );
$document->addResource ( 'javascript', 'subether/javascript/fullscreen.js' );

// If we don't find the user info by url, load the loggedin webuser ------------
$uinfo = false;
if( isset( $cuser ) && $cuser->ID > 0 )
{
	$uinfo = $database->fetchObjectRow( '
		SELECT * 
		FROM SBookContact 
		WHERE ID = \'' . $cuser->ID . '\' 
		ORDER BY ID DESC 
	', false, 'modules/authentication/module.php' );
}
else if( isset( $webuser ) && $webuser->ID > 0 )
{
	$uinfo = $database->fetchObjectRow( '
		SELECT * 
		FROM SBookContact 
		WHERE UserID = \'' . $webuser->ID . '\' 
		ORDER BY ID DESC 
	', false, 'modules/authentication/module.php' );
}

// Setup base templates -----------------------------------------------------------
$tmp = new cPTemplate ( $mbase . '/templates/module.php' );
$tmp->Top = '';
$tmp->MiddleCol = '';

// Setup parent -------------------------------------------------------------------
$parent = new stdClass();

$parent->agent = UserAgent();
$parent->domain = $domain;
$parent->module = $module;
$parent->mbase = $mbase;
$parent->mode = $url[4];
$parent->path = $path;
$parent->mpath = $mpath;
$parent->route = $route;
$parent->url = $url;
$parent->curl = getUrl();

$parent->webuser = setupUserData ();
$parent->cuser = setupUserData ( $uinfo, 1 );

$foundComponent = FindComponentByRoute ( $path );

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
	die( 'failed function request - authentication module' );
}

// Get component by ajax request -------------------------------------------
if( isset( $_REQUEST[ 'component' ] ) ) 
{
	die( 'ok<!--separate-->' . GetComponentByRequest( $_REQUEST[ 'component' ] ) );
}

// TODO: Make this dynamic
/*// List components position and render -------------------------------------
$listComponents = ListComponentPosition( $module );
if( $listComponents && is_array( $listComponents ) )
{
	foreach( $listComponents as $component )
	{
		$parent->position = $component->Position;
		$tmp->{$component->Position} .= IncludeComponent( $component->Name, $parent );
	}
}*/

if( $conf->Type == 'authentication' && $conf->Components == 'authentication' )
{
	// Render middle authentication component ---------------------------------------------
	$parent->position = 'MiddleCol';
	$tmp->MiddleCol .= IncludeComponent( 'authentication', $parent );
}
else
{
	// Render top authentication and menu component --------------------------------------------
	$parent->position = 'Top';
	$tmp->Top .= IncludeComponent( 'authentication', $parent );
	$tmp->Top .= IncludeComponent( 'menu', $parent );
}

// output -----------------------------------------------------------------------
$extension .= $tmp->render ();

?>
