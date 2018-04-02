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

statistics( $parent->module, 'library' );

$root = 'subether/';
$cbase = 'subether/components/library';
$cfolder = getCategoryID( 'library' );
$url = strtolower( $_REQUEST['route'] );

include_once ( $cbase . '/include/functions.php' );

include_once ( 'subether/classes/library.class.php' );

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/files.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/mobile.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/parse.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/content.css' );
$document->addResource ( 'javascript', $cbase . '/javascript/files.js' );

// Get Current User for this page and assign cuser var
$Component->parent->cuser->ID > 0 ? $cuser =& $Component->parent->cuser : $cuser =& $webuser;

// Get Current Folder for this page and assign folder var
$Component->parent->folder->ID > 0 && $Component->parent->folder->CategoryID != $cfolder->ID ? $folder =& $Component->parent->folder : $folder = '';

// Template: Module, Component, CategoryID, Status
SessionSet ( $parent->module, 'library', $parent->folder->CategoryID, 'online' );

// Check for user actions ------------------------------------------------------
if ( $_REQUEST['component'] == 'library' && isset( $_REQUEST[ 'action' ] ) && !isset( $_REQUEST['bypass'] ) )
{
	if( $_REQUEST[ 'action' ] == 'uploadfile' )
	{
		include ( 'subether/include/uploadfile.php' );
	}
    else if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	die( 'failed action request - library' );
}
// Check for user functions ----------------------------------------------------
else if ( $_REQUEST['component'] == 'library' && !isset( $_REQUEST['global'] ) && isset( $_REQUEST[ 'function' ] ) && !isset( $_REQUEST['bypass'] ) )
{
	if ( file_exists ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' ) )
    {
       include ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' );
    }
	else if ( file_exists ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' ) )
	{
		include ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' );
	}
	die( 'failed function request - library' );
}

include ( $cbase . '/functions/files.php' );

if( $Component )
{
	$dstr = '<script src="subether/thirdparty/javascript/ckeditor/ckeditor.js"></script>' . $dstr;
	
	if( strstr( $parent->agent, 'mobile' ) )
	{
		if( $parent->url && is_numeric( end( $parent->url ) ) )
		{
			$Component->Buttons = '<div id="Controls">' . $bstr . '</div>';
			$Component->Directory  = '<div id="Content" onclick="focusEditor()" ondragover="this.style.outline = \'1px solid black\'; handleDragOver(event); return false" ondragleave="this.style.outline = \'\'; handleDragLeave(event); return false" ondrop="handleDrop(window.dragId,window.dragType,ge(\'FolderID\').value,false,false,event); this.style.outline = \'\'; return false;">';
			$Component->Directory .= '<div id="ContentInner">' . $istr . '</div></div>';
		}
		else
		{
			$Component->Directory = '<div id="Directory">' . $dstr . '</div>';
		}
	}
	else
	{
		$Component->Buttons = '<div id="Controls">' . $bstr . '</div>';
		
		$Component->Directory = '<div id="Directory">' . $dstr . '</div>';
		
		$Component->ContentInner  = '<div id="Content" onclick="focusEditor()" ondragover="this.style.outline = \'1px solid black\'; handleDragOver(event); return false" ondragleave="this.style.outline = \'\'; handleDragLeave(event); return false" ondrop="handleDrop(window.dragId,window.dragType,ge(\'FolderID\').value,false,false,event); this.style.outline = \'\'; return false;">';
		$Component->ContentInner .= '<div id="ContentInner">' . $istr . '</div></div>';
	}
	
	$Component->url = $url;
}

statistics( $parent->module, 'library' );

?>
