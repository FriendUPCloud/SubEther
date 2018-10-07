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

i18nAddLocalePath ( 'subether/locale' );

$root = 'subether/';
$module = 'account';
$path = $path . 'account/';
$mpath = $path;
$mbase = 'subether/modules/account';
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

$document->_bodyClasses = ( $document->_bodyClasses ? $document->_bodyClasses . ' home account' : 'home account' );

// Setup resources ---------------------------------------------------------------
$document->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
$document->addResource ( 'javascript', 'lib/javascript/bajax.js' );
$document->addResource ( 'javascript', 'lib/javascript/gui.js' );
$document->addResource ( 'stylesheet', 'subether/css/home.css' );
$document->addResource ( 'stylesheet', 'subether/css/popupwindow.css' );
$document->addResource ( 'stylesheet', 'subether/css/windowslideshow.css' );
$document->addResource ( 'stylesheet', 'subether/components/events/css/events.css' );
$document->addResource ( 'stylesheet', 'subether/components/groups/css/group.css' );

$document->addResource ( 'javascript', 'subether/thirdparty/javascript/cryptodeps_1.js' );
$document->addResource ( 'javascript', 'subether/javascript/fcrypto_1.js' );

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

// Setup base templates -----------------------------------------------------------
$tmp = new cPTemplate ( $mbase . '/templates/module.php' );
//$tmp->agent = UserAgent();
//$tmp->module = $module;
//$tmp->mbase = $mbase;
//$tmp->mode = $url[4];
//$tmp->path = $path;
//$tmp->mpath = $mpath;
//$tmp->route = $route;
//$tmp->url = $url;
//$tmp->curl = getUrl();
//$tmp->page =& $page;
$tmp->LeftCol = '';
$tmp->MiddleCol = '';
$tmp->Chat = '';

// Setup parent -------------------------------------------------------------------
$parent = new stdClass();
//$parent->agent = $tmp->agent;
//$parent->module = $tmp->module;
//$parent->mbase = $tmp->mbase;
//$parent->mode = $tmp->mode;
//$parent->path = $tmp->path;
//$parent->mpath = $tmp->mpath;
//$parent->route = $tmp->route;
//$parent->url = $tmp->url;
//$parent->curl = $tmp->curl;

$parent->agent = UserAgent();
$parent->module = $module;
$parent->mbase = $mbase;
$parent->mode = $url[4];
$parent->path = $path;
$parent->mpath = $mpath;
$parent->route = $route;
$parent->url = $url;
$parent->curl = getUrl();

// If we have userinfo that's valid, render the module ----------------------------
//$cuser = setupUserData ();
//$user = setupUserData ();
//$tmp->cuser =& $cuser;
//$tmp->webuser =& $user;

//$parent->cuser = $tmp->cuser;
//$parent->webuser = $tmp->webuser;

$parent->webuser = setupUserData ();
$parent->cuser = $cuser = setupUserData ( $cuser, 1 );

if( $cuser )
{	
	// Render component by route or fallback to default ----------------------------
	$foundComponent = FindComponentByRoute ( $mpath );
	if ( $foundComponent && file_exists ( 'subether/components/' . $foundComponent ) || $foundComponent = 'account' )
	{	
		$foundID = FindIdByRoute ( $mpath . $foundComponent . '/' );
		
		// Assign to template ------------------------------------------------------
		//$tmp->folder = getCategoryID( ( $foundID ? $foundID : $foundComponent ), $tmp->cuser->ID );
		//$tmp->tabs = renderTabs( ( $foundID ? 'account_' . $foundComponent : 'account_general' ), $module );
		//$tmp->nav = $tmp->path . ( $tmp->folder ? $foundComponent . '/' . $tmp->folder->CategoryID . '/' : '' );
		
		// Assign to parent --------------------------------------------------------
		//$parent->folder = $tmp->folder;
		//$parent->tabs = $tmp->tabs;
		//$parent->nav = $tmp->nav;
		
		$parent->folder = getCategoryID( ( $foundID ? $foundID : $foundComponent ), $parent->cuser->UserID );
		$parent->tabs = renderTabs( ( $foundID ? 'account_' . $foundComponent : 'account_general' ), $module );
		$parent->nav = $parent->path . ( $parent->folder ? $foundComponent . '/' . $parent->folder->CategoryID . '/' : '' );
		
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
		
		//die( print_r( $tmp,1 ) . ' --' );
		
		/*// Render left component ---------------------------------------------------
		$leftComponent = FindComponentPosition( 'left', $foundComponent, $module );
		if( $leftComponent && is_array( $leftComponent ) )
		{
			foreach( $leftComponent as $k=>$v )
			{
				$tmp->LeftCol .= renderComponent( $leftComponent[$k], $parent, $foundComponent, $parent->tabs );
			}
		}
		// Render middle component -------------------------------------------------
		$middleComponent = FindComponentPosition( 'middle', $foundComponent, $module );
		if( $middleComponent && is_array( $middleComponent ) )
		{
			foreach( $middleComponent as $k=>$v )
			{
				$tmp->MiddleCol .= renderComponent( $middleComponent[$k], $parent, $foundComponent, $parent->tabs );
			}
		}
		// Render right component --------------------------------------------------
		$rightComponent = FindComponentPosition( 'right', $foundComponent, $module );
		if( $rightComponent && is_array( $rightComponent ) )
		{
			foreach( $rightComponent as $k=>$v )
			{
				$tmp->RightCol .= renderComponent( $rightComponent[$k], $parent, $foundComponent, $parent->tabs );
			}
		}
		// Render chat component ---------------------------------------------------
		$tmp->Chat .= renderComponent( 'chat', $parent );*/
	}
	
	
	
	/*// Render component by route or fallback to default ----------------------------
	$foundComponent = FindComponentByRoute ( $path );
	if ( $foundComponent && file_exists ( 'subether/components/' . $foundComponent ) )
	{
		// Assign to template ------------------------------------------------------
		$tmp->folder = getCategoryID( ( $url[3] ? $url[3] : $foundComponent ), $tmp->cuser->ID );
		$tmp->tabs = renderTabs( ( $url[3] ? 'account_' . $url[3] : 'account_general' ) );
		$tmp->nav = $tmp->path . ( $tmp->folder ? $foundComponent . '/' . $tmp->folder->CategoryID . '/' : '' );
		
		// Get component by ajax request -------------------------------------------
		if( isset( $_REQUEST[ 'component' ] ) ) 
		{
			die( 'ok<!--separate-->' . GetComponentByRequest( $_REQUEST[ 'component' ] ) );
		}
		
		// Render left component ---------------------------------------------------
		$leftComponent = FindComponentPosition( 'left', $foundComponent, $module );
		if( $leftComponent && is_array( $leftComponent ) )
		{
			foreach( $leftComponent as $k=>$v )
			{
				$tmp->LeftCol .= renderComponent( $leftComponent[$k], $tmp );
			}
		}
		// Render middle component -------------------------------------------------
		$middleComponent = FindComponentPosition( 'middle', $foundComponent, $module );
		if( $middleComponent && is_array( $middleComponent ) )
		{
			foreach( $middleComponent as $k=>$v )
			{
				$tmp->MiddleCol .= renderComponent( $middleComponent[$k], $tmp );
			}
		}
	}*/
}

/*
$tabs = renderTabs( 'account_panel' );
		
$tmp->LeftCol .= renderTemplates( 'account_panel', $path, $folder, $url, $tabs );

if( in_array( 'account', $url ) && in_array( 'security', $url ) )
{
	$tabs = renderTabs( 'account_security' );
}
else if( in_array( 'account', $url ) && in_array( 'privacy', $url ) )
{
	$tabs = renderTabs( 'account_privacy' );
}
else if( in_array( 'account', $url ) && in_array( 'tagging', $url ) )
{
	$tabs = renderTabs( 'account_tagging' );
}
else if( in_array( 'account', $url ) && in_array( 'blocking', $url ) )
{
	$tabs = renderTabs( 'account_blocking' );
}
else if( in_array( 'account', $url ) && in_array( 'notifications', $url ) )
{
	$tabs = renderTabs( 'account_notifications' );
}
else if( in_array( 'account', $url ) && in_array( 'mobile', $url ) )
{
	$tabs = renderTabs( 'account_mobile' );
}
else if( in_array( 'account', $url ) && in_array( 'followers', $url ) )
{
	$tabs = renderTabs( 'account_followers' );
}
else if( in_array( 'account', $url ) && in_array( 'apps', $url ) )
{
	$tabs = renderTabs( 'account_apps' );
}
else if( in_array( 'account', $url ) && in_array( 'ads', $url ) )
{
	$tabs = renderTabs( 'account_ads' );
}
else if( in_array( 'account', $url ) && in_array( 'payments', $url ) )
{
	$tabs = renderTabs( 'account_payments' );
}
else if( in_array( 'account', $url ) && in_array( 'gifts', $url ) )
{
	$tabs = renderTabs( 'account_gifts' );
}
else if( in_array( 'account', $url ) && in_array( 'support', $url ) )
{
	$tabs = renderTabs( 'account_support' );
}
else
{
	$tabs = renderTabs( 'account_general' );
}

$tmp->MiddleCol .= renderTemplates( 'account', $path, $folder, $url, $tabs, false, $_SESSION[ 'accountmode' ] );*/

// output --------------------------------------------------------------------------
$extension .= $tmp->render ();

?>
