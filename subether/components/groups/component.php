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

global $document, $webuser, $page;

statistics( $parent->module, 'groups' );

$root = 'subether/';
$cbase = 'subether/components/groups';

i18nAddLocalePath ( $cbase . '/locale' );

include_once ( $cbase . '/include/functions.php' );
include_once ( $root . 'components/wall/include/functions.php' );

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/group.css' );
$document->addResource ( 'javascript', $cbase . '/javascript/groups.js' );
//$document->addResource ( 'javascript', $cbase . '/javascript/instantmessage.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/coverslideshow.js' );
$document->addResource ( 'javascript', 'subether/components/orders/javascript/select.js' );

// Assign parent ---------------------------------------------------------------
$parent = $Component->parent;

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	die( 'failed action request - groups' );
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
	die( 'failed function request - groups' );
}

// Render content to template --------------------------------------------------
if( $parent->mode == 'groups' )
{
	include ( $cbase . '/functions/grouplist.php' );
	
	$Component->subpage = $str;
	
	include ( $cbase . '/functions/about.php' );
	include ( $cbase . '/functions/groups.php' );
	
	$page->MenuTitle = ( htmlentities( $parent->folder->Name ) . ' - ' . $parent->mode );
	
	setupMetaData( ( $parent->folder->Name ), ( BASE_URL . 'groups/' . $parent->folder->CategoryID . '/' ), $groupimage, ( $parent->folder->Description ? trim( strip_tags( str_replace( '"', "'", $parent->folder->Description ) ) ) : ' ' )/*, [Keywords here]*/ );
}
else if( strtolower( $parent->module ) == 'profile' )
{	
	include ( $cbase . '/functions/grouplist.php' );
}
else
{
	$groupimage = '';
	
	include ( $cbase . '/functions/about.php' );
	include ( $cbase . '/functions/groups.php' );
	
	$page->MenuTitle = ( htmlentities( $parent->folder->Name ) . ' - ' . $parent->mode );
	
	setupMetaData( htmlentities( $parent->folder->Name ), ( BASE_URL . 'groups/' . $parent->folder->CategoryID . '/' ), $groupimage, ( $parent->folder->Description ? trim( strip_tags( str_replace( '"', "'", $parent->folder->Description ) ) ) : ' ' )/*, [Keywords here]*/ );
}

if( $parent->folder->Privacy == 'SecretGroup' && !isset( $parent->folder->Permission ) && !isset( $parent->access->IsSystemAdmin ) )
{
	$str = '';
}

$Component->Content .= $str;

statistics( $parent->module, 'groups' );

?>
