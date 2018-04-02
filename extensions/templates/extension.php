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

global $database, $document, $content;

i18nAddLocalePath ( 'extensions/templates/locale' );

$tpldir = 'extensions/templates/templates';
$document->addResource ( 'stylesheet', 'extensions/templates/css/main.css' );
include ( 'extensions/templates/include/helpers.php' );
require_once ( 'lib/classes/dbObjects/dbContent.php' );

if ( !isset ( $_SESSION['templates_cid'] ) )
{
	$cnt = new dbContent ();
	$cnt = $cnt->getRootContent ();
	$_SESSION['templates_cid'] = $cnt->MainID;
}
if ( isset ( $_REQUEST[ 'cid' ] ) )
{
	$_SESSION['templates_cid'] = $_REQUEST[ 'cid' ];
}

// Actions
if ( isset ( $_REQUEST[ 'action' ] ) )
{
	$ac = str_replace ( '../', '', $_REQUEST['action'] );
	if ( file_exists ( $f = 'extensions/templates/actions/' . $ac . '.php' ) )
		include ( $f );
}

// Functions
$f = 'main';
if ( isset ( $_REQUEST[ 'function' ] ) )
	$f = $_REQUEST[ 'function' ];
if ( !file_exists ( $f = 'extensions/templates/functions/' . $f . '.php' ) )
	include ( 'extensions/templates/functions/main.php' );
else include ( $f );

?>
