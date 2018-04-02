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

global $document;

statistics( $parent->module, 'testing' );

$root = 'subether/';
$cbase = 'subether/components/testing';

//include_once ( $cbase . '/include/functions.php' );

// Setup resources -------------------------------------------------------------

//$document->addResource ( 'javascript', 'subether/thirdparty/javascript/cryptojs/rollups/aes.js' );
//$document->addResource ( 'javascript', 'subether/thirdparty/javascript/cryptojs/rollups/pbkdf2.js' );

//$document->addResource ( 'javascript', 'subether/thirdparty/javascript/jsencrypt.js' );
//$document->addResource ( 'javascript', 'subether/thirdparty/javascript/base64.js' );
//$document->addResource ( 'javascript', 'subether/thirdparty/javascript/jscrypto.js' );
//$document->addResource ( 'javascript', 'subether/thirdparty/javascript/hash.js' );
//$document->addResource ( 'javascript', 'subether/thirdparty/javascript/jsbn.js' );
//$document->addResource ( 'javascript', 'subether/thirdparty/javascript/random.js' );
//$document->addResource ( 'javascript', 'subether/thirdparty/javascript/rsa.js' );

//$document->addResource ( 'javascript', 'subether/javascript/fcrypto.js' );

//$document->addResource ( 'stylesheet', $cbase . '/css/store.css' );
//$document->addResource ( 'javascript', $cbase . '/javascript/store.js' );
$document->addResource ( 'javascript', 'subether/javascript/virtualcronjobs.js' );

// Template: Module, Component, CategoryID, Status
SessionSet ( $parent->module, 'testing', $parent->folder->CategoryID, 'online' );

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	die( 'failed action request - testing' );
}
// Check for user functions ----------------------------------------------------
else if ( isset( $_REQUEST[ 'function' ] ) )
{
	if ( file_exists ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' ) )
    {
       include ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' );
    }
	die( 'failed function request - testing' );
}
else
{
	include ( $cbase . '/functions/testing.php' );
	
	$Component->Content = $str;
}

statistics( $parent->module, 'testing' );

?>
