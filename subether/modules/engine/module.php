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

global $document, $webuser, $Session;

i18nAddLocalePath ( 'subether/locale' );

$root = 'subether/';
$module = 'engine';
$domain = $domain;
$path = $path;
$mpath = $path . 'engine/';
$mbase = 'subether/modules/engine';
$route = $_REQUEST['route'];
$url = explode( '/', strtolower( $_REQUEST['route'] ) );

// Global helper funcs -------------------------------------------------------------
include_once ( 'subether/functions/userfuncs.php' );
include_once ( 'subether/functions/modulefuncs.php' );
include_once ( 'subether/functions/componentfuncs.php' );
include_once ( 'subether/include/functions.php' );

//$document->_bodyClasses = 'engine';

// Setup resources -----------------------------------------------------------------
$document->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
$document->addResource ( 'javascript', 'lib/javascript/bajax.js' );
$document->addResource ( 'javascript', 'lib/javascript/gui.js' );

// Setup base templates ------------------------------------------------------------
$tmp = new cPTemplate ( $mbase . '/templates/module.php' );
//$tmp->agent = UserAgent();
//$tmp->domain = $domain;
//$tmp->path = $path;
//$tmp->mpath = $mpath;
//$tmp->module = $module;
//$tmp->mbase = $mbase;
//$tmp->route = $route;
//$tmp->url = $url;
//$tmp->curl = getUrl();
$tmp->GlobalSearch = '';
$tmp->SearchEngine = '';

// Setup parent --------------------------------------------------------------------
$parent = new stdClass();
//$parent->agent = $tmp->agent;
//$parent->domain = $tmp->domain;
//$parent->path = $tmp->path;
//$parent->mpath = $tmp->mpath;
//$parent->module = $tmp->module;
//$parent->mbase = $tmp->mbase;
//$parent->route = $tmp->route;
//$parent->url = $tmp->url;
//$parent->curl = $tmp->curl;

$parent->agent = UserAgent();
$parent->domain = $domain;
$parent->path = $path;
$parent->mpath = $mpath;
$parent->module = $module;
$parent->mbase = $mbase;
$parent->route = $route;
$parent->url = $url;
$parent->curl = getUrl();

// If we have userinfo that's valid, render the module ----------------------------
//$cuser = setupUserData ( $webuser );
//$user = setupUserData ();
//$tmp->cuser =& $cuser;
//$tmp->webuser =& $user;

//$parent->cuser = $tmp->cuser;
//$parent->webuser = $tmp->webuser;

$parent->webuser = setupUserData ();
$parent->cuser = setupUserData ( $webuser, 1 );

// Render top search component -----------------------------------------------------
if ( file_exists ( 'subether/components/search' ) )
{
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
		die( 'failed function request - profile module' );
	}
	
	// Get component by ajax request -------------------------------------------
	if( isset( $_REQUEST[ 'component' ] ) ) 
	{
		die( 'ok<!--separate-->' . GetComponentByRequest( $_REQUEST[ 'component' ], $parent ) );
	}
	
	if( $conf->Type == 'engine' && $conf->Components == 'search' )
	{
		// Render top search component ---------------------------------------------
		$parent->position = 'GlobalSearch';
		if( $parent->webuser->ID > 0 )
		{
			//$tmp->GlobalSearch .= renderComponent( 'notification', $parent );
			$tmp->GlobalSearch .= IncludeComponent( 'notification', $parent );
		}
		//$tmp->GlobalSearch .= renderComponent( 'search', $parent );
		$tmp->GlobalSearch .= IncludeComponent( 'search', $parent );
		
		//$document->_bodyClasses = 'engine';
		$document->_bodyClasses = ( $document->_bodyClasses ? ( $document->_bodyClasses . ' engine' ) : 'engine' );
	}
	else
	{
		// Render main search component --------------------------------------------
		$parent->position = 'SearchEngine';
		//$tmp->SearchEngine .= renderComponent( 'search', $parent );
		$tmp->SearchEngine .= IncludeComponent( 'search', $parent );
		
		//$document->_bodyClasses = 'search';
		//$document->_bodyClasses = ( $document->_bodyClasses ? $document->_bodyClasses . ' search' : 'search' );
	}
}

// output --------------------------------------------------------------------------
$extension .= $tmp->render ();

?>
