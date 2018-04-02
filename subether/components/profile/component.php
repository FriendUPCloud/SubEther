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

global $document, $page;

statistics( $parent->module, 'profile' );

$root = 'subether/';
$cbase = 'subether/components/profile';
$path = 'en/home/profile/';

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/profile.css' );
$document->addResource ( 'javascript', $cbase . '/javascript/profile.js' );
$document->addResource ( 'javascript', $cbase . '/javascript/coverslideshow.js' );

//$document->addResource ( 'javascript', 'subether/restapi/v1/api.js' );

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if( $_REQUEST[ 'action' ] == 'uploadfile' )
	{
		include ( 'subether/include/uploadfile.php' );
	}
	else if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	die( 'failed action request - profile' );
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
	die( 'failed function request - profile' );
}

$userimage = '';

include ( $cbase . '/functions/album.php' );

$page->MenuTitle = ( ( $parent->cuser->DisplayName ? htmlentities( $parent->cuser->DisplayName ) : htmlentities( $parent->cuser->Username ) ) . ' - ' . strtolower( $parent->folder->Name ) );

setupMetaData( ( $parent->cuser->DisplayName ? htmlentities( $parent->cuser->DisplayName ) : htmlentities( $parent->cuser->Username ) ), ( BASE_URL . $parent->cuser->Username ), $userimage, ( $parent->cuser->About ? trim( strip_tags( str_replace( '"', "'", $parent->cuser->About ) ) ) : ' ' )/*, [Keywords here]*/ );

$Component->profileimage = $pimg;
$Component->coverimage = $cimg;
$Component->slideshow = $simg;
$Component->path = $path;

statistics( $parent->module, 'profile' );

?>
