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
$module = 'register';
$domain = $domain;
$path = $path;
$mpath = $path . 'register/';
$mbase = 'subether/modules/register';
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

$document->_bodyClasses = ( $document->_bodyClasses ? $document->_bodyClasses . ' register' : 'register' );

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

$document->addResource ( 'javascript', 'subether/javascript/utf8.js' );
$document->addResource ( 'javascript', 'subether/javascript/jsdate.js' );
$document->addResource ( 'javascript', 'subether/javascript/functions.js' );
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
	', false, 'modules/register/module.php' );
}
else if( isset( $webuser ) && $webuser->ID > 0 )
{
	$uinfo = $database->fetchObjectRow( '
		SELECT * 
		FROM SBookContact 
		WHERE UserID = \'' . $webuser->ID . '\' 
		ORDER BY ID DESC 
	', false, 'modules/register/module.php' );
}

// Setup base templates -----------------------------------------------------------
$tmp = new cPTemplate ( $mbase . '/templates/module.php' );
//$tmp->agent = UserAgent();
//$tmp->domain = $domain;
//$tmp->path = $path;
//$tmp->mpath = $mpath;
//$tmp->route = $route;
//$tmp->module = $module;
//$tmp->mbase = $mbase;
$tmp->MiddleCol = '';

// If we have userinfo that's valid, render the module -------------------------
//$cuser = setupUserData ( $uinfo );
//$user = setupUserData ();
//$tmp->cuser =& $cuser;
//$tmp->webuser =& $user;

$parent = new stdClass();

$parent->webuser = setupUserData ();
$parent->cuser = setupUserData ( $uinfo, 1 );

$parent->agent = UserAgent();
$parent->domain = $domain;
$parent->path = $path;
$parent->mpath = $mpath;
$parent->route = $route;
$parent->module = $module;
$parent->mbase = $mbase;

$foundComponent = FindComponentByRoute ( $mpath );

//$tmp->folder = getCategoryID( $foundComponent, $tmp->cuser->ID );
//$tmp->access = CategoryAccess( $tmp->cuser->ContactID, $tmp->folder->CategoryID );

$parent->folder = getCategoryID( $foundComponent, $parent->cuser->UserID );
$parent->access = CategoryAccess( $parent->cuser->ContactID, $parent->folder->CategoryID );

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
	die( 'failed function request - register module' );
}

// Get component by ajax request -------------------------------------------
if( isset( $_REQUEST[ 'component' ] ) ) 
{
	die( 'ok<!--separate-->' . GetComponentByRequest( $_REQUEST[ 'component' ], $parent ) );
}

// List components position and render -------------------------------------
$listComponents = ListComponentPosition( $module );
if( $listComponents && is_array( $listComponents ) )
{
	foreach( $listComponents as $component )
	{
		if( !isset( $_REQUEST['rendercomponent'] ) && !isset( $_REQUEST['excludecomponent'] ) )
		{
			$parent->position = $component->Position;
			$tmp->{$component->Position} .= IncludeComponent( $component->Name, $parent );
		}
		else if( !$_REQUEST['excludecomponent'] && strstr( strtolower( $_REQUEST['rendercomponent'] ), strtolower( $component->Name ) ) )
		{
			$parent->position = $component->Position;
			$tmp->{$component->Position} .= IncludeComponent( $component->Name, $parent );
		}
		else if( !$_REQUEST['rendercomponent'] && !strstr( strtolower( $_REQUEST['excludecomponent'] ), strtolower( $component->Name ) ) )
		{
			$parent->position = $component->Position;
			$tmp->{$component->Position} .= IncludeComponent( $component->Name, $parent );
		}
		
		//$tmp->position = $component->Position;
		//$tmp->{$component->Position} .= IncludeComponent( $component->Name, $parent );
	}
}

// output -----------------------------------------------------------------------
$extension .= $tmp->render ();

?>
