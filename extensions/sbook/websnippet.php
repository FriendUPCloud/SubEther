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

global $document, $page, $webuser, $database, $Session;

// If HTTPS isn't defined and we have BASE_URL redirect site to BASE_URL
if( !isset( $_SERVER['HTTPS'] ) && BASE_URL )
{
	$bsu = explode( '://', BASE_URL );
	
	if( isset( $bsu[0] ) && strtolower( $bsu[0] ) == 'https' )
	{
		header( 'Location: ' . BASE_URL );
	}
}

// Check if a maintenance job is running, if so lock the system until done
if( $maintenance = $database->fetchObjectRow( '
	SELECT 
		MinDelay, Filename, LastExec 
	FROM 
		SBookCronJobs 
	WHERE 
			IsMaintenance = "1" 
		AND IsActive = "1" 
		AND IsRunning = "1" 
	ORDER BY
		ID ASC
', false, 'sbook/websnippet.php' ) )
{
	$GLOBALS['maintenance'] = true;
}

i18nAddLocalePath ( 'subether/locale' );

// Api check -------------------------------------------------------------------
//include_once ( 'subether/restapi/websnippet.php' );
include_once ( 'subether/restapi/functions.php' );

// Global helper funcs ---------------------------------------------------------
include_once ( 'subether/include/dbcheck.php' );
include_once ( 'subether/functions/userfuncs.php' );
include_once ( 'subether/functions/modulefuncs.php' );
include_once ( 'subether/functions/componentfuncs.php' );
include_once ( 'subether/functions/globalfuncs.php' );

// Logg all ...
logActivity ( false, '$_REQUEST' . ( $webuser ? ' [ID#'.$webuser->ID.']' : '' ), ( $_REQUEST ? json_encode( $_REQUEST ) : false ), 'all-site.log' );

setupNodeInfo();
setupUserData();
setupMetaData();

// Speedlane -------------------------------------------------------------------
if( isset( $_REQUEST['fastlane'] ) && isset( $_REQUEST['component'] ) )
{
	if ( isset( $_REQUEST['action'] ) && file_exists ( 'subether/components/' . $_REQUEST['component'] . '/actions/' . $_REQUEST['action'] . '.php' ) )
	{
		include ( 'subether/components/' . $_REQUEST['component'] . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
	}
	else if ( isset( $_REQUEST['function'] ) && file_exists ( 'subether/components/' . $_REQUEST['component'] . '/functions/' . $_REQUEST['function'] . '.php' ) )
	{
		include ( 'subether/components/' . $_REQUEST['component'] . '/functions/' . $_REQUEST['function'] . '.php' );
	}
	else
	{
		include ( 'subether/components/' . $_REQUEST['component'] . '/component.php' );
	}
	die( 'failed speedlane request' );
}

UserLanguage();

// Get field config for this ARENA module --------------------------------------
$conf = CreateObjectFromString ( $fieldObject->DataMixed );

// Set the global subether path
$path = $Session->LanguageCode . '/';
$domain = BASE_URL;
$GLOBALS['path'] =& $path;
$GLOBALS['domain'] =& $domain;

// Find Username by route ------------------------------------------------------
$cuser = LoadSBookUserByUsername ( SanitizeUsername ( $_REQUEST['route'], $path ) );



$con = $database->fetchObjectRow( '
	SELECT
		*
	FROM
		SBookContact
	WHERE
			NodeID = "0"
		AND NodeMainID = "0"
		AND UserID = \'' . $webuser->ID . '\'
	ORDER BY
		ID DESC
', false, 'sbook/websnippet.php' );
if( $webuser->ID > 0 && $con->ID > 0 )
{
	if( $theme = ThemeData( $con->Theme ) )
	{
		if( isset( $theme->Dir ) )
		{
			$_SESSION['theme'] = $theme->Dir;
		}
	
		$_SESSION['theme'] = ( isset( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : $_SESSION['theme'] );
	}
	
	// Earlydays implementation of custom theme overrides for the user in his or her library
	
	if( file_exists( BASE_DIR . '/subether/upload/profile/'.$webuser->ID.'/theme/default/theme.css' ) )
	{
		$_SESSION['theme_path_override'] = ( $con->Theme == 0 ? 'subether/upload/profile/'.$webuser->ID.'/theme/default/' : '' );
	}
}
else
{
	$_SESSION['theme_path_override'] = '';
}

if( $cuser && $cuser->UserID > 0 )
{
	// Earlydays implementation of custom theme overrides for the user in his or her library
	
	if( file_exists( BASE_DIR . '/subether/upload/profile/'.$cuser->UserID.'/theme/default/theme.css' ) )
	{
		$_SESSION['theme_path_override'] = ( $cuser->Theme == 0 ? 'subether/upload/profile/'.$cuser->UserID.'/theme/default/' : '' );
	}
}

// Ugly hack, but will be like this for now ...

if ( !isset( $_REQUEST['save'] ) || $_REQUEST['save'] )
{
	$_SESSION['rendermodule'] = $_REQUEST['rendermodule'] = ( isset( $_REQUEST['rendermodule'] ) ? $_REQUEST['rendermodule'] : $_SESSION['rendermodule'] );
	
	$_SESSION['excludecomponent'] = $_REQUEST['excludecomponent'] = ( isset( $_REQUEST['excludecomponent'] ) ? $_REQUEST['excludecomponent'] : $_SESSION['excludecomponent'] );
	$_SESSION['rendercomponent'] = $_REQUEST['rendercomponent'] = ( isset( $_REQUEST['rendercomponent'] ) ? $_REQUEST['rendercomponent'] : $_SESSION['rendercomponent'] );
}

if( isset( $_REQUEST['wall_default_categoryid'] ) )
{
	if( $_REQUEST['wall_default_categoryid'] )
	{
		$_SESSION['wall_default_categoryid'] = $_REQUEST['wall_default_categoryid'];
	}
	else if( isset( $_SESSION['wall_default_categoryid'] ) )
	{
		unset( $_SESSION['wall_default_categoryid'] );
	}
}

// Try to find on user (shortcut!) ---------------------------------------------
if ( $conf->Type != 'authentication' && $conf->Type != 'engine' && $cuser )
{
	$conf->Type = 'profile';
}

if( $conf->Type == 'default' )
{
	if( ModuleExists( 'main' ) )
	{
		include ( 'subether/modules/main/module.php' );
		//include ( 'subether/modules/main/main.php' );
	}
}
else if( $conf->Type )
{
	if( ModuleExists( $conf->Type ) )
	{
		if( in_array( $conf->Type, array( 'main', 'authentication', 'engine' ) ) && defined( 'AJAX_ONLY' ) )
		{
			include ( 'subether/modules/' . $conf->Type . '/' . $conf->Type . '.php' );
		}
		else include ( 'subether/modules/' . $conf->Type . '/module.php' );
	}
}

?>
