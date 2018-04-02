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

global $database, $webuser;

// Create Folder ------------------------------------------------------------------------------------------------------------------------------ //
if( $_REQUEST['option'] == 'createfolder' )
{
	$lib = new Library ( 'Folder' );
	if( strtolower( $folder->MainName ) != 'profile' )
	{
		$lib->CategoryID = $folder->CategoryID;
	}
	$lib->Parent = $_POST[ 'pid' ];
	$lib->Name = $_POST[ 'directoryname' ];
	$lib->Save();
	
	if( $lib->FolderID > 0 )
	{
		die( 'ok<!--separate-->' . $lib->FolderID . '<!--separate-->' . print_r( $lib,1 ) );
	}
	die( 'fail ' . print_r( $lib,1 ) );
}

// Save Folder -------------------------------------------------------------------------------------------------------------------------------- //
if( $_REQUEST['option'] == 'savefolder' )
{
	$lib = new Library ( 'Folder' );
	if( $lib->Load( $_POST[ 'fid' ] ) )
	{
		if( $_POST[ 'foldername' ] ) $lib->Name = $_POST[ 'foldername' ];
		if( isset( $_POST[ 'sortorder' ] ) ) $lib->SortOrder = ( $_POST[ 'sortorder' ] > 0 ? $_POST[ 'sortorder' ] : '0' );
		$lib->Save();
		
		die( 'ok<!--separate-->' . $lib->FolderID . '<!--separate-->' . print_r( $lib,1 ) );
	}
	die( 'fail ' . print_r( $lib,1 ) );
}

// Delete Folder ----------------------------------------------------------------------------------------------------------------------------- //
if( $_REQUEST['option'] == 'deletefolder' )
{
	$lib = new Library ( 'Folder' );
	if( $lib->Load( $_POST[ 'deletefolder' ] ) )
	{
		$lib->Delete();
		
		die( 'ok<!--separate-->' . $lib->ParentID . '<!--separate-->' . print_r( $lib,1 ) );
	}
	die( 'fail ' . print_r( $lib,1 ) );
}

// Save File --------------------------------------------------------------------------------------------------------------------------------- //
if( $_REQUEST['option'] == 'savefile' )
{
	$lib = new Library ( ( $_POST[ 'filetype' ] == 'image' || $_POST[ 'filetype' ] == 'site' ) ? 'Image' : 'File' );
	if( $lib->Load( $_POST[ 'fid' ] ) )
	{
		if( $lib->IsEdit == 0 || $lib->IsEdit == $webuser->ID )
		{
			if( $_POST[ 'pid' ] ) $lib->FileFolder = $_POST[ 'pid' ];
			if( $_POST[ 'filename' ] ) $lib->Title = $_POST[ 'filename' ];
			if( $_POST[ 'filecontent' ] ) $lib->FileContent = trim( $_POST[ 'filecontent' ] );
			if( isset( $_POST[ 'sortorder' ] ) ) $lib->SortOrder = ( $_POST[ 'sortorder' ] > 0 ? $_POST[ 'sortorder' ] : '0' );
			$lib->Save();
			
			die( 'ok<!--separate-->' . $lib->FolderID . '<!--separate-->' . $lib->FileID . '<!--separate-->' . $lib->FileContent . '<!--separate-->' . print_r( $lib,1 ) );
		}
	}
	die( 'fail ' . print_r( $lib,1 ) );
}

// Save File --------------------------------------------------------------------------------------------------------------------------------- //
if( $_REQUEST['option'] == 'savefiletextcontent' )
{
	// Is always a file
	$lib = new Library ( 'File' );
	if( $lib->Load( $_POST[ 'fid' ] ) )
	{
		if( $lib->IsEdit == 0 || $lib->IsEdit == $webuser->ID )
		{
			if( $_POST[ 'filecontent' ] ) $lib->FileContent = trim( $_POST[ 'filecontent' ] );
			$lib->Save();
			die( 'ok<!--separate-->' . $lib->FolderID . '<!--separate-->' . $lib->FileID . '<!--separate-->' . $lib->FileContent . '<!--separate-->' );
		}
	}
	die( 'fail' );
}

// Create File ------------------------------------------------------------------------------------------------------------------------------- //
if( $_REQUEST['option'] == 'createfile' )
{
	$lib = new Library ( 'File' );
	if( strtolower( $folder->MainName ) != 'profile' )
	{
		$lib->CategoryID = $folder->CategoryID;
	}
	if( $_POST[ 'pid' ] ) $lib->FileFolder = $_POST[ 'pid' ];
	if( $_POST[ 'filename' ] ) $lib->Filename = $_POST[ 'filename' ];
	$lib->FileContent = trim( $_POST[ 'filecontent' ] );
	$lib->Save();
	
	if( $lib->FileID > 0 )
	{
		die( 'ok<!--separate-->' . $lib->FolderID . '<!--separate-->' . $lib->FileID . '<!--separate-->' . $lib->FileContent . '<!--separate-->' );
	}
	die( 'fail ' . print_r( $lib,1 ) );
}

// Delete File ------------------------------------------------------------------------------------------------------------------------------- //
if( $_REQUEST['option'] == 'deletefile' )
{
	$lib = new Library ( ( $_POST[ 'filetype' ] == 'image' || $_POST[ 'filetype' ] == 'site' ) ? 'Image' : 'File' );
	if( $lib->Load( $_POST[ 'deletefile' ] ) )
	{
		$lib->Delete();
		
		die( 'ok<!--separate-->' . $lib->FolderID . '<!--separate-->' . print_r( $lib,1 ) );
	}
	die( 'fail ' . print_r( $lib,1 ) );
}

die( 'fail' );

?>
