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

global $document, $webuser;

if( IsSystemAdmin() )
{
	statistics( $parent->module, 'globals' );
	
	$root = 'subether/';
	$cbase = 'subether/components/global';
	
	include_once ( $root . '/components/library/include/functions.php' );
	include_once ( $cbase . '/include/functions.php' );
	
	// Setup resources -------------------------------------------------------------
	$document->addResource ( 'stylesheet', $cbase . '/css/component.css' );
	$document->addResource ( 'javascript', $cbase . '/javascript/component.js' );
	$document->addResource ( 'javascript', $cbase . '/javascript/updates.js' );
	$document->addResource ( 'javascript', $cbase . '/javascript/backup.js' );
	$document->addResource ( 'javascript', $cbase . '/javascript/reports.js' );
	$document->addResource ( 'javascript', $cbase . '/javascript/cronjobs.js' );
	$document->addResource ( 'javascript', $cbase . '/javascript/nodes.js' );
	$document->addResource ( 'javascript', $cbase . '/javascript/api.js' );
	$document->addResource ( 'javascript', $cbase . '/javascript/statistics.js' );
	
	// Assign parent ---------------------------------------------------------------
	$parent = $Component->parent;
	
	// Check for user actions ------------------------------------------------------
	if ( isset( $_REQUEST[ 'action' ] ) )
	{
		if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
		{
		   include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
		}
		die( 'failed action request - global' );
	}
	// Check for user functions ----------------------------------------------------
	else if ( isset( $_REQUEST[ 'function' ] ) )
	{
		if ( file_exists ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' ) )
		{
		   include ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' );
		}
		else if ( file_exists ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' ) )
		{
			include ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' );
		}
		die( 'failed function request - global' );
	}
	
	// Render content to template --------------------------------------------------
	
	$foundComponent = false;
	
	if( $parent->route )
	{
		$url = explode( '/', $parent->route );
		$url = array_reverse( $url );
		
		foreach( $url as $u )
		{
			if ( trim( $u ) != '' && !is_numeric( $u ) && !$foundComponent )
			{
				$vars = explode( '?', $u );
				
				if ( $vars[0] && !strstr( $vars[0], '?' ) )
				{
					$foundComponent = strtolower( trim( $vars[0] ) );
				}
			}
		}
	}
	
	//$foundComponent = strtolower( trim( str_replace( array( $parent->mpath, '/' ), array( '', '' ), $parent->route ) ) );
	
	if( $foundComponent != 'settings' && file_exists( $cbase . '/functions/' . $foundComponent . '.php' ) )
	{
		include ( $cbase . '/functions/' . $foundComponent . '.php' );
	}
	else
	{
		include ( $cbase . '/functions/updates.php' );
	}
	$Component->Content = $str;
	
	statistics( $parent->module, 'globals' );
}

?>
