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

statistics( 'main' );

i18nAddLocalePath ( 'subether/locale' );

$root = 'subether/';
$module = 'main';
$domain = $domain;
$path = $path;
$mpath = $path;
$mbase = 'subether/modules/main';
$route = $_REQUEST['route'];
$url = explode( '/', strtolower( $_REQUEST['route'] ) );

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
include_once ( 'subether/include/dbcheck.php' );
include_once ( 'subether/include/cleanup.php' );
include_once ( 'subether/include/functions.php' );
include_once ( 'subether/include/calendar.php' );

$document->_bodyClasses = ( $document->_bodyClasses ? $document->_bodyClasses . ' home' : 'home' );

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

$document->addResource ( 'javascript', 'subether/thirdparty/javascript/cryptodeps.js' );
$document->addResource ( 'javascript', 'subether/javascript/fcrypto.js' );

$document->addResource ( 'javascript', 'subether/javascript/virtualcronjobs.js' );
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
//$document->addResource ( 'javascript', 'https://s3.amazonaws.com/plivosdk/web/plivo.min.js' );

if( isset( $_REQUEST['debug'] ) )
{
	$str = Library::GetRemoteDataByUrl( 'http://www.ideverket.no' );
	//die( print_r( $str,1 ) . ' ..' );
	$doc = new SimpleHTML( $str );
	$ele = $doc->getElementById( 'ImageText' );
	die( print_r( $ele,1 ) . ' .. ' . $doc->innerHTML( $ele[0]->tagName, $ele[0]->nodeid ) );
	//$ele = $doc->getElementsByTagName( 'div', 'MainContentWrapper' );
	//die( print_r( $doc,1 ) . ' ..' );
	$ele = $doc->getElementsByTagName( 'p' );
	//die( print_r( $doc,1 ) . ' ..' );
	foreach( $ele as $k=>$e )
	{
		if( $ele[$k]->className == 'Next' )
		{
			//die( print_r( $ele[$k],1 ) . ' --' );
		}
	}
	
	die( print_r( $ele,1 ) . ' ..' );
}

// Render xml template if requested and found
if( $url && in_array( 'conference.xml', $url ) )
{
	if( file_exists ( $f = $root . 'components/meeting/include/conference.xml' ) )
	{
		header ( 'Content-type: text/xml; charset=utf-8' );
		$xml = new cPTemplate ( $f );
		die( $xml->Render() );
	}
}

// Render xml template if requested and found
if( $url && in_array( 'dial.xml', $url ) )
{
	$cdata = UserData( $cuser->UserID );
	
	/*if( file_exists ( $f = $root . 'components/profile/include/dial.xml' ) )
	{*/
		header ( 'Content-type: text/xml; charset=utf-8' );
		/*die( '<Response>
				<Dial>
					<User>sip:' . ( $cdata->Settings->Plivo->URI ? $cdata->Settings->Plivo->URI : '' ) . '</User>
				</Dial>
			</Response>' );*/
		die( '<Response>
				<Conference enterSound="beep:1">my_conf</Conference>
			 </Response>' );
		/*$xml = new cPTemplate ( $f );
		die( $xml->Render() );*/
	/*}*/
}

// Setup base templates -----------------------------------------------------------
$tmp = new cPTemplate ( $mbase . '/templates/module.php' );
//$tmp->agent = UserAgent();
//$tmp->module = $module;
//$tmp->mbase = $mbase;
//$tmp->mode = $url[4];
//$tmp->switch = switchCalendarMode( false, $_REQUEST[ 'r' ] );
//$tmp->domain = $domain;
//$tmp->path = $path;
//$tmp->mpath = $mpath;
//$tmp->route = $route;
//$tmp->url = $url;
//$tmp->curl = getUrl();
//$tmp->page =& $page;
$tmp->LeftCol = '';
$tmp->MiddleCol = '';
$tmp->RightCol = '';
$tmp->Chat = '';

// Setup parent -------------------------------------------------------------------
$parent = new stdClass();
//$parent->agent = $tmp->agent;
//$parent->module = $tmp->module;
//$parent->mbase = $tmp->mbase;
//$parent->mode = $tmp->mode;
//$parent->domain = $tmp->domain;
//$parent->path = $tmp->path;
//$parent->mpath = $tmp->mpath;
//$parent->route = $tmp->route;
//$parent->url = $tmp->url;
//$parent->curl = $tmp->curl;

$parent->agent = UserAgent();
$parent->module = $module;
$parent->mbase = $mbase;
$parent->mode = FindComponentByRoute( $module, true );
$parent->domain = $domain;
$parent->path = $path;
$parent->mpath = $mpath;
$parent->route = $route;
$parent->url = $url;
$parent->curl = getUrl();

// If we have userinfo that's valid, render the module ----------------------------
//$cuser = $user = setupUserData ();
//$tmp->cuser =& $cuser;
//$tmp->webuser =& $user;

//$parent->cuser = $tmp->cuser;
//$parent->webuser = $tmp->webuser;

$parent->webuser = setupUserData();
$parent->cuser = setupUserData( $cuser, 1 );

/*if( $cuser )
{*/
	// Check if user has default folders if not make them.
	if ( !isset ( $_SESSION[ 'subether_check_defcats' ] ) )
	{
		if( $webuser->ContactID > 0 )
		{
			$_SESSION[ 'subether_check_defcats' ] = 1;
		}
		checkDefaultCategories();
	}
	// Render component by route or fallback to default ----------------------------
	$foundComponent = FindComponentByRoute ( $module );
	if ( $foundComponent || $foundComponent = 'wall' )
	{
		$foundID = FindIdByRoute ( $mpath . $foundComponent . '/' );
		
		//die( $foundComponent . ' -- ' . $foundID );
		
		// Assign to template ------------------------------------------------------
		//$tmp->folder = getCategoryID( ( $foundID ? $foundID : $foundComponent ), $tmp->cuser->ID );
		//$tmp->access = CategoryAccess( $tmp->cuser->ContactID, $tmp->folder->CategoryID );
		//$tmp->panel = FindComponentList( $module );
		//$tmp->tabs = renderTabs( $foundComponent, $tmp->folder, $module, $tmp->access );
		//$tmp->nav = $tmp->path . ( $tmp->folder ? $foundComponent . '/' . $tmp->folder->CategoryID . '/' : '' );
		
		// Assign to parent --------------------------------------------------------
		//$parent->folder = $tmp->folder;
		//$parent->access = $tmp->access;
		//$parent->panel = $tmp->panel;
		//$parent->tabs = $tmp->tabs;
		//$parent->nav = $tmp->nav;
		
		$parent->folder = getCategoryID( ( $foundID ? $foundID : $foundComponent ), $parent->cuser->UserID );
		$parent->access = CategoryAccess( $parent->cuser->ContactID, $parent->folder->CategoryID );
		$parent->panel = FindComponentList( $module );
		$parent->tabs = renderTabs( $foundComponent, $parent->folder, $module, $parent->access );
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
			die( 'failed function request - main module' );
		}
		
		// Get component by ajax request -------------------------------------------
		if( isset( $_REQUEST[ 'component' ] ) ) 
		{
			die( 'ok<!--separate-->' . GetComponentByRequest( $_REQUEST['component'], $parent ) );
		}
		
		// List components position and render -------------------------------------
		$listComponents = ListComponentPosition( $module, $foundComponent );
		//die( print_r( $listComponents,1 ) . ' -- ' . $module . ' .. ' . $foundComponent );
		$start = time ();
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
			}
			
			// Check if user or group has default folders if not make them.
			if ( !isset( $_SESSION['library_check_defcats'] ) )
			{
				$_SESSION[ 'library_check_defcats' ] = 1;
				$lib = new Library ();
				$lib->UserID = $parent->cuser->ID;
				if( strtolower( $parent->folder->MainName ) != 'profile' )
				{
					$lib->CategoryID = $parent->folder->CategoryID;
				}
				$lib->GetFolders ();
			}
		}
		else
		{
			//die( 'No access ... <pre>' . print_r( $_SESSION,1 ) . '</pre> [] <pre>' . print_r( $webuser,1 ) . '</pre> [] <pre>' . print_r( $_COOKIE,1 ) . '</pre> ...' );
			if( defined( 'BASE_URL' ) && BASE_URL )
			{
				header( 'Location: ' . BASE_URL );
			}
			else if( $_SERVER['HTTPS_HOST'] )
			{
				header( 'Location: https://' . $_SERVER['HTTPS_HOST'] );
			}
			else if( $_SERVER['HTTP_HOST'] )
			{
				header( 'Location: http://' . $_SERVER['HTTP_HOST'] );
			}
			die( 'No access ...' );
		}
	}
/*}*/

// output --------------------------------------------------------------------------
$extension .= $tmp->render();

statistics( 'main' );

if( isset( $_REQUEST['debug'] ) )
{
	die( print_r( $_SESSION['statistics'],1 ) );
}

?>
