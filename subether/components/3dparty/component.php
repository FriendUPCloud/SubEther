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

statistics( $parent->module, '3dparty' );

$root = 'subether/';
$cbase = 'subether/components/3dparty';

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	die( 'failed action request - 3dparty' );
}
// Check for user functions ----------------------------------------------------
else if ( isset( $_REQUEST[ 'function' ] ) )
{
	if ( file_exists ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' ) )
    {
       include ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' );
    }
	die( 'failed function request - 3dparty' );
}

$c = new dbObject( 'SBookCategory' );
if( $parent->folder->CategoryID > 0 && $c->Load( $parent->folder->CategoryID ) )
{
	// Get Settings
	$c->Settings = is_string( $c->Settings ) ? json_decode( $c->Settings ) : new stdClass();
	$Component->Settings = $c->Settings;
	//die(  print_r( $c->Settings,1 ) . ' --' );
}

statistics( $parent->module, '3dparty' );

?>
