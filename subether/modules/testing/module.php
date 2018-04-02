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

global $document, $page, $webuser, $path;

statistics( 'testing' );

i18nAddLocalePath ( 'subether/locale' );

$root = 'subether/';
$module = 'testing';
$mname = 'testing';
$domain = $domain;
$path = $path;
$mpath = $path . 'testing/';
$mbase = 'subether/modules/testing';
$route = $_REQUEST['route'];
$url = explode( '/', strtolower( $_REQUEST['route'] ) );

unset( $root );

// Global helper funcs -----------------------------------------------------------
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
include_once ( 'subether/classes/fcrypto.class.php' );
include_once ( 'subether/include/dbcheck.php' );
include_once ( 'subether/include/cleanup.php' );
include_once ( 'subether/include/functions.php' );
include_once ( 'subether/include/calendar.php' );

$document->_bodyClasses = ( $document->_bodyClasses ? $document->_bodyClasses . ' testing' : 'testing' );

// Setup resources ---------------------------------------------------------------
$document->addResource ( 'stylesheet', 'subether/css/home.css' );
$document->addResource ( 'stylesheet', 'subether/css/popupwindow.css' );
$document->addResource ( 'stylesheet', 'subether/css/windowslideshow.css' );
$document->addResource ( 'stylesheet', 'subether/css/emoticons.css' );
$document->addResource ( 'stylesheet', 'subether/components/events/css/events.css' );
$document->addResource ( 'stylesheet', 'subether/components/groups/css/group.css' );
$document->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
$document->addResource ( 'javascript', 'lib/javascript/bajax.js' );
$document->addResource ( 'javascript', 'lib/javascript/gui.js' );

$document->addResource ( 'javascript', 'subether/thirdparty/javascript/cryptojs/rollups/aes.js' );
$document->addResource ( 'javascript', 'subether/thirdparty/javascript/cryptojs/rollups/pbkdf2.js' );
$document->addResource ( 'javascript', 'subether/thirdparty/javascript/jsencrypt.js' );
$document->addResource ( 'javascript', 'subether/thirdparty/javascript/base64.js' );
$document->addResource ( 'javascript', 'subether/thirdparty/javascript/hash.js' );
$document->addResource ( 'javascript', 'subether/thirdparty/javascript/jsbn.js' );
$document->addResource ( 'javascript', 'subether/thirdparty/javascript/random.js' );
$document->addResource ( 'javascript', 'subether/thirdparty/javascript/rsa.js' );
$document->addResource ( 'javascript', 'subether/thirdparty/javascript/jscrypto.js' );
$document->addResource ( 'javascript', 'subether/javascript/fcrypto.js' );

$document->addResource ( 'javascript', 'subether/javascript/php.js' );
$document->addResource ( 'javascript', 'subether/javascript/utf8.js' );
$document->addResource ( 'javascript', 'subether/javascript/json.js' );
$document->addResource ( 'javascript', 'subether/javascript/jsdate.js' );
$document->addResource ( 'javascript', 'subether/javascript/functions.js' );
$document->addResource ( 'javascript', 'subether/javascript/editor.js' );
$document->addResource ( 'javascript', 'subether/javascript/jaxqueue.js' );
$document->addResource ( 'javascript', 'subether/javascript/heartbeat.js' );
$document->addResource ( 'javascript', 'subether/javascript/maintenance.js' );
$document->addResource ( 'javascript', 'subether/javascript/windowslideshow.js' );
$document->addResource ( 'javascript', 'subether/javascript/popupwindow.js' );
$document->addResource ( 'javascript', 'subether/javascript/formcheck.js' );
$document->addResource ( 'javascript', 'subether/javascript/responsive.js' );
$document->addResource ( 'javascript', 'subether/javascript/fullscreen.js' );

// Setup base templates --------------------------------------------------------
$tmp = new cPTemplate ( $mbase . '/templates/module.php' );
$tmp->LeftCol = '';
$tmp->MiddleCol = '';
$tmp->RightCol = '';

// Setup parent -------------------------------------------------------------------
$parent = new stdClass();
$parent->agent = UserAgent();
$parent->module = $module;
$parent->mbase = $mbase;
$parent->mode = $url[4];
$parent->domain = $domain;
$parent->path = $path;
$parent->mpath = $mpath;
$parent->route = $route;
$parent->url = $url;
$parent->curl = getUrl();

$parent->webuser = setupUserData ();
$parent->cuser = setupUserData ( $cuser, 1 );

// Render component by route or fallback to default ----------------------------
$foundComponent = FindComponentByRoute ( $mpath );
if ( $foundComponent && file_exists ( 'subether/components/' . $foundComponent ) || $foundComponent = 'testing' )
{
	$foundID = FindIdByRoute ( $mpath . $foundComponent . '/' );
	
	$parent->folder = getCategoryID( ( $foundID ? $foundID : $foundComponent ), $parent->cuser->UserID );
	$parent->panel = FindComponentList( $module );
	$parent->tabs = renderTabs( $foundComponent, $parent->folder, $module, $parent->access );
	$parent->nav = $parent->path . ( $parent->folder ? $foundComponent . '/' . $parent->folder->CategoryID . '/' : '' );
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
		die( 'failed function request - trading module' );
	}
	
	// Get component by ajax request -------------------------------------------
	if( isset( $_REQUEST[ 'component' ] ) ) 
	{
		die( 'ok<!--separate-->' . GetComponentByRequest( $_REQUEST[ 'component' ], $parent ) );
	}
	
	// List components position and render -------------------------------------
	$listComponents = ListComponentPosition( $module, $foundComponent );
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
			
			//$parent->position = $component->Position;
			//$tmp->{$component->Position} .= IncludeComponent( $component->Name, $parent );
		}
	}
}


// output
$extension .= $tmp->render ();

statistics( 'testing' );

?>
