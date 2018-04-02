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

global $document, $page, $webuser;

i18nAddLocalePath ( 'subether/locale' );

$root = 'subether/';
$module = 'global';
$path = 'en/home/global/';
$mpath = 'en/home/global/';
$mbase = 'subether/modules/global';
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

$document->_bodyClasses = ( $document->_bodyClasses ? $document->_bodyClasses . ' home global' : 'home global' );

// Setup resources ---------------------------------------------------------------
$document->addResource ( 'stylesheet', 'subether/css/home.css' );
$document->addResource ( 'stylesheet', 'subether/css/popupwindow.css' );
$document->addResource ( 'stylesheet', 'subether/css/windowslideshow.css' );
$document->addResource ( 'stylesheet', 'subether/components/events/css/events.css' );
$document->addResource ( 'stylesheet', 'subether/components/groups/css/group.css' );
$document->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
$document->addResource ( 'javascript', 'lib/javascript/bajax.js' );
$document->addResource ( 'javascript', 'lib/javascript/gui.js' );
$document->addResource ( 'javascript', 'subether/javascript/virtualcronjobs.js' );
$document->addResource ( 'javascript', 'subether/javascript/json.js' );
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
$parent->cuser = $cuser = setupUserData ( false, 1 );

if( $cuser && IsSystemAdmin() )
{	
	// Render component by route or fallback to default ----------------------------
	$foundComponent = FindComponentByRoute ( $mpath );
	if ( $foundComponent && file_exists ( 'subether/components/' . $foundComponent ) || $foundComponent = 'global' )
	{	
		$foundID = FindIdByRoute ( $mpath . $foundComponent . '/' );
		
		// Assign to template ------------------------------------------------------
		//$tmp->folder = getCategoryID( ( $foundID ? $foundID : $foundComponent ), $tmp->cuser->ID );
		//$tmp->tabs = renderTabs( ( $foundID ? 'global_' . $foundComponent : 'global_settings' ), $module );
		//$tmp->nav = $tmp->path . ( $tmp->folder ? $foundComponent . '/' . $tmp->folder->CategoryID . '/' : '' );
		
		// Assign to parent --------------------------------------------------------
		//$parent->folder = $tmp->folder;
		//$parent->tabs = $tmp->tabs;
		//$parent->nav = $tmp->nav;
		
		$parent->folder = getCategoryID( ( $foundID ? $foundID : $foundComponent ), $parent->cuser->UserID );
		$parent->tabs = renderTabs( ( $foundID ? 'global_' . $foundComponent : 'global_settings' ), $module );
		$parent->nav = /*$parent->path . */( $parent->folder ? $foundComponent . '/' . $parent->folder->CategoryID . '/' : '' );
		
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
		
		/*// Render left component ---------------------------------------------------
		$leftComponent = FindComponentPosition( 'left', $foundComponent, $module );
		if( $leftComponent && is_array( $leftComponent ) )
		{
			foreach( $leftComponent as $k=>$v )
			{
				$parent->position = 'LeftCol';
				$tmp->LeftCol .= renderComponent( $leftComponent[$k], $parent, $foundComponent, $parent->tabs );
			}
		}
		// Render middle component -------------------------------------------------
		$middleComponent = FindComponentPosition( 'middle', $foundComponent, $module );
		if( $middleComponent && is_array( $middleComponent ) )
		{
			foreach( $middleComponent as $k=>$v )
			{
				$parent->position = 'MiddleCol';
				$tmp->MiddleCol .= renderComponent( $middleComponent[$k], $parent, $foundComponent, $parent->tabs );
			}
		}
		// Render right component --------------------------------------------------
		$rightComponent = FindComponentPosition( 'right', $foundComponent, $module );
		if( $rightComponent && is_array( $rightComponent ) )
		{
			foreach( $rightComponent as $k=>$v )
			{
				$parent->position = 'RightCol';
				$tmp->RightCol .= renderComponent( $rightComponent[$k], $parent, $foundComponent, $parent->tabs );
			}
		}
		// Render chat component ---------------------------------------------------
		$parent->position = 'Chat';
		$tmp->Chat .= renderComponent( 'chat', $parent );*/
	}

}
	
// output --------------------------------------------------------------------------
$extension .= $tmp->render ();

?>
