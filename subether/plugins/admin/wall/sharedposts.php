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

global $document, $database;

$plugin = true;

$root = 'subether/';
$pbase = 'subether/components/admin';

include_once ( $pbase . '/include/functions.php' );

$document->addResource ( 'stylesheet', $pbase . '/css/admin.css' );
$document->addResource ( 'javascript', $pbase . '/javascript/admin.js' );

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if ( file_exists ( $pbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $pbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	die( 'failed action request - admin' );
}

include ( $pbase . '/functions/case.php' );

if( !$plugins && !is_array( $plugins ) )
{
	$plugins = array();
}

/*if( $content = $database->fetchObjectRows( 'SELECT * FROM SBookCaseList WHERE CategoryID = \'' . $parent->folder->CategoryID . '\' ORDER BY ID ASC' ) )
{
	$arr = array(); $i = 0;
	
	foreach( $content as $cnt )
	{
		$str  = '';
		$str .= '<div onmouseout="IsEditing()" onmouseover="IsEditing(1)" class="Comment">';
		$str .= '';
		$str .= '</div>';
		$arr[] = $str;
	}
	
	$obj = new stdClass();
	$obj->Name = 'case';
	$obj->Content = $content;
	$Component->Plugins[] = $obj;
}*/

if( $str )
{
	$plugins[] = '<div id="AdminPlugin">' . $str . '</div>';
}

?>
