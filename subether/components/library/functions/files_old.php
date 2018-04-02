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

$hasAccess = false; $fid = false;

// Make some vars readable
$pMainName = $parent->folder->MainName;   // Parent folder name
$mainName  = $folder->MainName;           // Folder name
$wid       = $parent->webuser->ID;        // UserID for logged in user
$perms     = $parent->folder->Permission; // Permissions here
$cuserid   = $parent->cuser->ID;          // Library owner UserID
$maxMiniCharWidth = 17;
$maxMiniCharWidth2 = 22;

// Temporary ... TODO: FIX Access levels
if( $pMainName != 'Profile' )
{
	$perms = 'admin';
}

// Check access
if( ( $pMainName == 'Profile' && $wid == $cuserid ) || ( $perms == 'admin' || $perms == 'owner' ) || IsSystemAdmin() )
{
	$hasAccess = true;
}

// Check if mid (folderid) is set or fid (file id)
$pmid = $pfid = $view = false;
if( isset( $_POST[ 'mid' ] ) || isset( $_POST[ 'fid' ] ) )
{
	$pmid = $_POST['mid'];
	$pfid = $_POST['fid'];
	$fid = ( $pfid > 0 ? ( 'fid_' . $pfid ) : ( 'mid_' . $pmid ) );
}
if( isset( $_POST['view'] ) )
{
	$view = $_POST['view'];
}

// Set root folder
$root = new dbFolder();
$root = $root->getRootFolder();

// Fetch home folder
$home = $database->fetchObjectRows ( $q = '
	SELECT 
		mf.*, mr.Title AS Name, mr.SortOrder 
	FROM 
		Folder rf, 
		Folder hf, 
		Folder mf, 
		SBookMediaRelation mr 
	WHERE 
			rf.Parent = \'' . $root->ID . '\' 
		AND hf.Parent = rf.ID 
		AND mf.Parent = hf.ID 
		AND mr.MediaID = mf.ID 
		AND mr.MediaType = "Folder" 
		' . ( strtolower( $mainName ) != 'profile' && $folder->CategoryID > 0 ? 'AND mr.CategoryID = \'' . $folder->CategoryID . '\' AND mr.UserID = "0" ' : 'AND mr.UserID = \'' . $cuser->ID . '\' AND mr.CategoryID = "0" ' ) . '
	ORDER BY 
		mr.SortOrder ASC, mf.ID ASC 
' );

if( $home )
{	
	$dstr = ''; $cstr = ''; $thumbs = ''; $content = ''; $currentFolder = '';
	
	$ext_small = array(
		'pdf'=>'pdf/pdf-16_32.png',
		'xls'=>'xls_win/xlsx_win-16_32.png',
		'doc'=>'docx_win/docx_win-16_32.png',
		'docx'=>'docx_win/docx_win-16_32.png',
		'jpg'=>'jpeg/jpeg-16_32.png', 
		'jpeg'=>'jpeg/jpeg-16_32.png',
		'png'=>'png/png-16_32.png',
		'gif'=>'gif/gif-16_32.png', 
		'mov'=>'mov/mov-16_32.png',
		'avi'=>'mov/mov-16_32.png',
		'ogv'=>'mov/mov-16_32.png',
		'mp4'=>'mov/mov-16_32.png',
		'swf'=>'mov/mov-16_32.png',
		'url'=>'url/url-16_32.png',
		'mp3'=>'mp3/mp3-16_32.png', 
		'txt'=>'text/text-16_32.png'
	);
	
	$ext_large = array(
		'pdf'=>'pdf/pdf-128_32.png',
		'xls'=>'xls_win/xlsx_win-128_32.png',
		'doc'=>'docx_win/docx_win-128_32.png',
		'docx'=>'docx_win/docx_win-128_32.png',
		'jpg'=>'jpeg/jpeg-128_32.png', 
		'jpeg'=>'jpeg/jpeg-128_32.png',
		'png'=>'png/png-128_32.png',
		'gif'=>'gif/gif-128_32.png', 
		'mov'=>'mov/mov-128_32.png',
		'avi'=>'mov/mov-128_32.png',
		'ogv'=>'mov/mov-128_32.png',
		'mp4'=>'mov/mov-128_32.png',
		'swf'=>'mov/mov-128_32.png',
		'url'=>'url/url-128_32.png',
		'mp3'=>'mp3/mp3-128_32.png', 
		'txt'=>'text/text-128_32.png'
	);
	
	// Prebuffer all sub folders
	$bufferedSubfolders = array();
	foreach ( $home as $mfolder )
	{
		$bufferedSubfolders[] = $mfolder->ID;
	}
	$subs = array();
	if( $folds = $database->fetchObjectRows ( '
		SELECT
			f.*, r.Title AS Name, r.SortOrder
		FROM 
			Folder f, 
			SBookMediaRelation r 
		WHERE 
			f.Parent IN ( ' . implode( ', ', $bufferedSubfolders ) . ' )
			AND r.MediaID = f.ID 
		ORDER BY 
			r.SortOrder ASC,
			f.ID ASC 
	' ) )
	{
		foreach ( $folds as $fold )
		{
			if( !isset( $subs[$fold->Parent] ) )
				$subs[$fold->Parent] = array();
			$subs[$fold->Parent][] = $fold;
		}
	}
	unset( $bufferedSubfolders, $folds );
	
	// Prebuffer all images and files
	$imageBuf  = array();
	$fileBuf   = array();
	$folderids = array();
	$query = '
	SELECT * FROM
	(
		(
			SELECT
				f.ID, f.Title, f.Filename, f.Description, f.Tags, 
				f.DateCreated, f.DateModified, f.SortOrder, f.Filetype, 
				f.UserID, f.Filesize, 0 AS Width, 0 AS Height,
				f.FileFolder AS FolderID,
				f.Access AS FileAccess,
				f.ModID, f.IsEdit, f.Verified, 
				"file" AS MediaType,
				fl.DiskPath AS FolderPath,
				fl.Access AS FolderAccess 
			FROM
				File f,
				Folder fl
			WHERE
				f.FileFolder = fl.ID
				AND fl.ID IN ( !FolderIDS! )
		)
		UNION
		(
			SELECT
				f.ID, f.Title, f.Filename, f.Description, f.Tags, 
				f.DateCreated, f.DateModified, f.SortOrder, f.Filetype, 
				f.UserID, f.Filesize, f.Width, f.Height,
				f.ImageFolder AS FolderID,
				f.Access AS FileAccess,
				f.ModID, f.IsEdit, f.Verified, 
				"image" AS MediaType,
				fl.DiskPath AS FolderPath,
				fl.Access AS FolderAccess 
			FROM
				Image f,
				Folder fl
			WHERE
				f.ImageFolder = fl.ID
				AND fl.ID IN ( !FolderIDS! )
		)
	)
	z
	ORDER BY
		z.SortOrder ASC,
		z.DateCreated ASC
	';
	foreach ( $subs as $id=>$sub )
	{
		foreach ( $sub as $sobj )
		{
			$folderids[] = $sobj->ID;
		}
	}
	$query = str_replace( '!FolderIDS!', implode( ', ', $folderids ), $query );
	if( $data = $database->fetchObjectRows( $query ) )
	{
		foreach ( $data as $dat )
		{
			if( $dat->MediaType == 'image' )
			{
				if( !isset( $imageBuf[$dat->FolderID] ) )
					$imageBuf[$dat->FolderID] = array();
				$imageBuf[$dat->FolderID][] = $dat;
			}
			else
			{
				if( !isset( $fileBuf[$dat->FolderID] ) )
					$fileBuf[$dat->FolderID] = array();
				$fileBuf[$dat->FolderID][] = $dat;
			}
		}
	}
	
	foreach( $home as $mfolder )
	{
		$mfolder->SubFolders = isset( $subs[$mfolder->ID] ) ? $subs[$mfolder->ID] : false;
		
		// --- Home Folders ------------------------------------------------------------------------------------------------------------------- //
		
		$dstr .= '<h4 class="mainfolder ' . strtolower( $mfolder->Name ) . '">';
		$dstr .= '<span class="foldername" onclick="' . ( $mfolder->SubFolders[0]->ID > 0 ? 'refreshFilesDirectory( \'' . $mfolder->SubFolders[0]->ID . '\' )' : ( $hasAccess ? 'createNewFile()' : '' ) ) . '" id="FolderID_' . $mfolder->ID . '">' . $mfolder->Name . '</span>';
		if( $hasAccess )
		{
			$dstr .= '<div class="edit_icons">';
			$dstr .= '<span class="sorting">';
			$dstr .= '<div class="sortUp" onclick="sortDown( \'' . $mfolder->ID . '\', \'' . ( $mfolder->SortOrder > 0 ? ( $mfolder->SortOrder - 1 ) : 0 ) . '\', \'folder\', \'' . $fid . '\' )" title="Up"></div>';
			$dstr .= '<div class="sortDown" onclick="sortUp( \'' . $mfolder->ID . '\', \'' . ( $mfolder->SortOrder + 1 ) . '\', \'folder\', \'' . $fid . '\' )" title="Down"></div>';
			$dstr .= '</span>';
			$dstr .= '<span class="newfile" onclick="createNewFile( \'' . ( $pmid > 0 ? $pmid : $mfolder->SubFolders[0]->ID ) . '\', \'' . $fid . '\' )" title="Create New File"><img class="Icon" src="admin/gfx/icons/page_add.png"></span>';
			$dstr .= '<span class="parsefile" onclick="openWindow( \'Library\', \'' . $fid . '\', \'parse\' )" title="Create File By Url"><img class="Icon" src="admin/gfx/icons/page_world.png"></span>';
			if( $mfolder->Name != 'Library' && $mfolder->Name != 'Album' ) 
			{
				$dstr .= '<span class="editfolder" onclick="editFolder( \'' . $mfolder->ID . '\', \'' . $fid . '\' )" title="Edit Folder"><img class="Icon" src="admin/gfx/icons/folder_edit.png"></span>';
			}
			$dstr .= '<span class="newfolder" onclick="createNewFolder( \'' . $mfolder->ID . '\', \'' . $fid . '\' )" title="Create New Folder"><img class="Icon" src="admin/gfx/icons/folder_add.png"></span>';
			if( $mfolder->Name != 'Library' && $mfolder->Name != 'Album' ) 
			{
				$dstr .= '<span class="deletefolder" onclick="deleteFolder( \'' . $mfolder->ID . '\', \'' . $fid . '\' )" title="Delete Folder"><img class="Icon" src="admin/gfx/icons/folder_delete.png"></span>';
			}
			//$dstr .= '<span>';
			//$dstr .= '</span>';
			$dstr .= '</div>';
		}
		$dstr .= '</h4>';
		
		if( $mfolder->SubFolders )
		{
			$images = ''; $files = ''; $ii = 0;
			
			$dstr .= '<ul class="subfolders">';
			
			foreach( $mfolder->SubFolders as $sfolder )
			{
				$sfolder->Files = array(); $obj = false;
				
				$images = isset( $imageBuf[$sfolder->ID] ) ? $imageBuf[$sfolder->ID] : false;
				$files = isset( $fileBuf[$sfolder->ID] ) ? $fileBuf[$sfolder->ID] : false;
				
				$total = ( count( $images ) > count( $files ) ? count( $images ) : count( $files ) );
				
				for( $i = 0; $i < $total; $i++ )
				{
					if( $files[$i] )
					{
						$ext = explode( '.', $files[$i]->Filename );
						
						if( file_exists( $files[$i]->FolderPath . ( $ext[0] . '.parse' ) ) )
						{
							$lib = new Library ();
							$obj = $lib->OpenFile( $files[$i]->FolderPath, ( $ext[0] . '.parse' ) );
							$obj = $obj ? json_decode( $obj ) : false;
							
							if( $obj )
							{
								$files[$i]->Parse = new stdClass();
								$files[$i]->Parse->Url = $obj->Url;
								$files[$i]->Parse->Domain = $obj->Domain;
								$files[$i]->Parse->Title = $obj->Title;
								$files[$i]->Parse->Leadin = $obj->Leadin;
								$files[$i]->Parse->Type = $obj->Type;
								$files[$i]->Parse->Media = $obj->Media;
								$files[$i]->Parse->Limit = $obj->Limit;
								
								$files[$i]->Title = str_replace( ' ', '_', $files[$i]->Parse->Title ) . '.' . $files[$i]->Parse->Type;
								$files[$i]->MediaType = $files[$i]->Parse->Type;
							}
							//die( print_r( $files[$i],1 ) . ' .. ' . print_r( $obj,1 ) );
						}
						
						$sfolder->Files[] = $files[$i];
						
						if( isset( $_POST[ 'fid' ] ) && $_POST[ 'fid' ] == $files[$i]->ID && $files[$i]->Parse )
						{
							switch( $files[$i]->Parse->Media )
							{
								// Audio ---------------------------------------
								case 'audio':
									$files[$i]->Content = embedAudio( $files[$i]->Parse->Url, 495, 480 );
								// Video ---------------------------------------
								case 'video':
									$files[$i]->Content = embedVideo( $files[$i]->Parse->Url, 495, 480 );
								// File ----------------------------------------
								case 'file':
									$files[$i]->Content = embedPDF( $files[$i]->Parse->Url, 495, 480 );
									break;
							}
							$files[$i]->Current = 1;
							$sfolder->Open = 1;
							$content = $files[$i];
							$current = true;
						}
						
						if( ( $files[$i]->Filetype == 'txt' || $files[$i]->Filetype == 'plain' ) && isset( $_POST[ 'fid' ] ) && $_POST[ 'fid' ] == $files[$i]->ID )
						{
							$lib = new Library ( 'File' );
							if( $lib->Load( $files[$i]->ID ) )
							{
								$files[$i]->Content = $lib->GetFileContent();
								$files[$i]->Current = 1;
								$sfolder->Open = 1;
								$content = $files[$i];
								$current = true;
							}
						}
						else if( $files[$i]->Filetype == 'pdf' && isset( $_POST[ 'fid' ] ) && $_POST[ 'fid' ] == $files[$i]->ID )
						{
							$lib = new Library ( 'File' );
							if( $lib->Load( $files[$i]->ID ) )
							{
								$files[$i]->Content = embedPDF( $files[$i]->FolderPath . $files[$i]->Filename, 495, 480 );
								$files[$i]->Current = 1;
								$sfolder->Open = 1;
								$content = $files[$i];
								$current = true;
							}
						}
						else if( in_array( $files[$i]->Filetype, array( 'webm', 'ogg', 'ogv', 'mp4', 'swf' ) ) && isset( $_POST[ 'fid' ] ) && $_POST[ 'fid' ] == $files[$i]->ID )
						{
							$lib = new Library ( 'File' );
							if( $lib->Load( $files[$i]->ID ) )
							{
								$files[$i]->Content = embedVideo( $files[$i]->FolderPath . $files[$i]->Filename, 495, 480, 'video' );
								//$files[$i]->Content = embedVideo( $files[$i]->FolderPath . $files[$i]->Filename, 495, 480 );
								$files[$i]->Current = 1;
								$sfolder->Open = 1;
								$content = $files[$i];
								$current = true;
							}
						}
					}
					if( $images[$i] )
					{
						$ext = explode( '.', $images[$i]->Filename );
						
						if( file_exists( $images[$i]->FolderPath . ( $ext[0] . '.parse' ) ) )
						{
							$lib = new Library ();
							$obj = $lib->OpenFile( $images[$i]->FolderPath, ( $ext[0] . '.parse' ) );
							$obj = $obj ? json_decode( $obj ) : false;
							
							if( $obj )
							{
								$images[$i]->Parse = new stdClass();
								$images[$i]->Parse->Url = $obj->Url;
								$images[$i]->Parse->Domain = $obj->Domain;
								$images[$i]->Parse->Title = $obj->Title;
								$images[$i]->Parse->Leadin = $obj->Leadin;
								$images[$i]->Parse->Type = $obj->Type;
								$images[$i]->Parse->Media = $obj->Media;
								$images[$i]->Parse->Limit = $obj->Limit;
								
								$images[$i]->Title = str_replace( ' ', '_', $images[$i]->Parse->Title ) . '.' . $images[$i]->Parse->Type;
								$images[$i]->MediaType = $images[$i]->Parse->Type;
							}
							//die( print_r( $images[$i],1 ) . ' .. ' . print_r( $obj,1 ) );
						}
						
						$sfolder->Files[] = $images[$i];
						
						if( isset( $_POST[ 'fid' ] ) && $_POST[ 'fid' ] == $images[$i]->ID && $images[$i]->Parse )
						{
							switch( $images[$i]->Parse->Media )
							{
								// Youtube ----------------------------------------------------------------------------------------------------
								case 'youtube':
									$images[$i]->Content = embedYoutube( $images[$i]->Parse->Url, 495, 480 );
									break;
								// Vimeo ------------------------------------------------------------------------------------------------------
								case 'vimeo':
									$images[$i]->Content = embedVimeo( $images[$i]->Parse->Url, 495, 480 );
									break;
								// Livestream -------------------------------------------------------------------------------------------------
								case 'livestream':
									$images[$i]->Content = embedLivestream( $images[$i]->Parse->Url, 495, 280 );
									break;
								// Remote site data -------------------------------------------------------------------------------------------
								default:
									$site = new dbImage ();
									$site->Load( $images[$i]->ID );
									$images[$i]->Content  = '<div class="ParseContent"><div class="image site' . ( 472 <= $images[$i]->Width ? ' big' : ' small' ) . '">';
									$images[$i]->Content .= '<a href="' . $images[$i]->FolderPath . $images[$i]->Filename . '" target="_blank">';
									$images[$i]->Content .= '<img style="background-image:url(' . $site->getImageURL ( 472, 480, 'framed', false, 0xffffff ) . ');max-width:' . $images[$i]->Width . 'px;max-height:' . $images[$i]->Height . 'px;">';
									$images[$i]->Content .= '</a></div><div class="text">';
									$images[$i]->Content .= '<h3><a href="' . $images[$i]->Parse->Url . '" target="_blank">' . $images[$i]->Parse->Title . '</a></h3>';
									$images[$i]->Content .= '<p><a href="' . $images[$i]->Parse->Url . '" target="_blank">' . $images[$i]->Parse->Leadin . '</a></p>';
									//$images[$i]->Content .= '<p class="url"><a href="' . $images[$i]->Parse->Domain . '" target="_blank">' . $images[$i]->Parse->Domain . '</a></p>';
									$images[$i]->Content .= '</div></div>';
									break;
							}
							$images[$i]->Current = 1;
							$sfolder->Open = 1;
							$content = $images[$i];
							$current = true;
						}
					}
					
					if( $i >= 10000 ) return false;
				}
				
				if( ( ( !$pmid && !$_POST[ 'fid' ] && $ii == 0 && !$current ) || ( !$current && isset( $pmid ) && $pmid == $sfolder->ID ) ) )
				{
					$sfolder->Current = 1;
					$current = true;
					$thumbs = $sfolder->Files;
				}
				
				// --- Sub Folders ------------------------------------------ //
				
				$dstr .= '<li class="subfolder ' . strtolower( $sfolder->Name ) . '"><div' . ( $sfolder->Current > 0 ? ' class="current"' : '' ) . ' onclick="refreshFilesDirectory( \'' . $sfolder->ID . '\' )">';
				$dstr .= '<span class="toggle" onclick="openSubList( this, \'' . $sfolder->ID . '\', event )"><img class="Icon" src="lib/icons/bullet_toggle_' . ( $sfolder->Open > 0 ? 'minus' : 'plus' ) . '.png"></span>';
				$dstr .= '<span class="foldername" id="FolderID_' . $sfolder->ID . '" ondragover="this.style.fontWeight = \'bold\'; event.preventDefault( event ); return false" ondragleave="this.style.fontWeight = \'normal\'; event.preventDefault( event ); return false" ondrop="moveFile(window.dragId, window.dragType, ' . $sfolder->ID . '); delete window.dragId; delete window.dragType; event.preventDefault( event ); return false;">' . dotTrim( $sfolder->Name, 18 ) . '</span>';
				if( $hasAccess )
				{
					$dstr .= '<div class="edit_icons">';
					$dstr .= '<span class="sorting">';
					$dstr .= '<div class="sortUp" onclick="sortDown( \'' . $sfolder->ID . '\', \'' . ( $sfolder->SortOrder > 0 ? ( $sfolder->SortOrder - 1 ) : 0 ) . '\', \'folder\', \'' . $fid . '\' )" title="Up"></div>';
					$dstr .= '<div class="sortDown" onclick="sortUp( \'' . $sfolder->ID . '\', \'' . ( $sfolder->SortOrder + 1 ) . '\', \'folder\', \'' . $fid . '\' )" title="Down"></div>';
					$dstr .= '</span>';
					$dstr .= '<span class="editfolder" onclick="editFolder( \'' . $sfolder->ID . '\', \'' . $fid . '\' ); return cancelBubble( event )" title="Edit Folder"><img class="Icon" src="admin/gfx/icons/page_edit.png"></span>';
					$dstr .= '<span class="deletefolder" onclick="deleteFolder( \'' . $sfolder->ID . '\', \'' . $fid . '\' )" title="Delete Folder"><img class="Icon" src="admin/gfx/icons/page_delete.png"></span>';
					$dstr .= '<span class="folderaccess"> <select onclick="cancelBubble(event)" onchange="UpdateAccess(\'' . $sfolder->ID . '\',\'folder\',this.value)">';
					$dstr .= '<option title="Public" value="0"' . ( $sfolder->FolderAccess == 0 ? ' selected="selected"' : '' ) . '>0</option>';
					$dstr .= '<option title="Contacts" value="1"' . ( $sfolder->FolderAccess == 1 ? ' selected="selected"' : '' ) . '>1</option>';
					$dstr .= '<option title="Only Me" value="2"' . ( $sfolder->FolderAccess == 2 ? ' selected="selected"' : '' ) . '>2</option>';
					//$dstr .= '<option title="Custom" value="3"' . ( $sfolder->FolderAccess == 3 ? ' selected="selected"' : '' ) . '>3</option>';
					$dstr .= '</select></span>';
					$dstr .= '</div>';
				}
				$dstr .= '</div>';
				
				$dstr .= '<ul ' . ( $sfolder->Open > 0 ? 'class="open"' : '' ) . '>';
				
				$imgs = 0;
				foreach( $sfolder->Files as $file )
				{
					$dragdata = ' draggable="yes" ondragstart="window.dragId=\'' . $file->ID . '\'; window.dragType=\'' . $file->MediaType . '\'"';
					
					// --- Files -------------------------------------------- //
					
					$dstr .= '<li class="file ' . $file->Filetype . '">';
					
					//$img = new dbImage ();
					//if( $file->MediaType == 'image' && $img->Load( $file->ID ) )
					if( $file->MediaType == 'image' )
					{
						//$dstr .= '<div>';
						//$dstr .= '<span class="files">' . $img->getImageHTML ( 16, 16, 'framed', false, 0xffffff ) . '</span>';
						//$dstr .= '<span onclick="openFullscreen( \'Library\', \'' . $file->ImageFolder . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( ' . (string)$imgs . ' ); } )" class="files" id="FileID_' . $file->ID . '" filetype="' . $file->MediaType . '">' . dotTrim( $file->Title, 25 ) . '</span>';
						$dstr .= '<div ' . ( $file->Current > 0 ? 'class="current"' : '' ) . '><span class="filetype"><img class="Icon"' . $dragdata . ' src="subether/gfx/icons/' . ( $ext_small[$file->Filetype] ? $ext_small[$file->Filetype] : $ext_small['png'] ) . '"></span>';
						$dstr .= '<span class="filename" onclick="refreshFilesDirectory( \'' . $file->FolderID . '\', \'' . $file->ID . '\' )" class="files" id="FileID_' . $file->ID . '" filetype="' . $file->MediaType . '">' . dotTrim( $file->Title, $maxMiniCharWidth, true ) . '</span>';
						$imgs++;
					}
					//else if( $file->MediaType == 'video' && $img->Load( $file->ID ) )
					else if( $file->MediaType == 'video' )
					{
						//$dstr .= '<div ' . ( $file->Current > 0 ? 'class="current"' : '' ) . '>';
						//$dstr .= '<span class="files">' . $img->getImageHTML ( 16, 16, 'framed', false, 0xffffff ) . '<i></i></span>';
						//$dstr .= '<span onclick="refreshFilesDirectory( \'' . $file->FolderID . '\', \'' . $file->ID . '\' )" class="files" id="FileID_' . $file->ID . '" filetype="' . $file->MediaType . '">' . dotTrim( $file->Title, 25 ) . '</span>';
						$dstr .= '<div ' . ( $file->Current > 0 ? 'class="current"' : '' ) . '><span class="filetype"><img' . $dragdata . ' class="Icon" src="subether/gfx/icons/' . $ext_small['mov'] . '"></span>';
						$dstr .= '<span class="filename" onclick="refreshFilesDirectory( \'' . $file->FolderID . '\', \'' . $file->ID . '\' )" class="files" id="FileID_' . $file->ID . '" filetype="' . $file->MediaType . '">' . dotTrim( $file->Title, $maxMiniCharWidth, true ) . '</span>';
					}
					//else if( $file->MediaType == 'site' && $img->Load( $file->ID ) )
					else if( $file->MediaType == 'site' )
					{
						//$dstr .= '<div ' . ( $file->Current > 0 ? 'class="current"' : '' ) . '>';
						//$dstr .= '<span class="files">' . $img->getImageHTML ( 16, 16, 'framed', false, 0xffffff ) . '</span>';
						//$dstr .= '<span onclick="refreshFilesDirectory( \'' . $file->FolderID . '\', \'' . $file->ID . '\' )" class="files" id="FileID_' . $file->ID . '" filetype="' . $file->MediaType . '">' . dotTrim( $file->Title, 25 ) . '</span>';
						$dstr .= '<div ' . ( $file->Current > 0 ? 'class="current"' : '' ) . '><span class="filetype"><img' . $dragdata . ' class="Icon" src="subether/gfx/icons/' . $ext_small['url'] . '"></span>';
						$dstr .= '<span class="filename" onclick="refreshFilesDirectory( \'' . $file->FolderID . '\', \'' . $file->ID . '\' )" class="files" id="FileID_' . $file->ID . '" filetype="' . $file->MediaType . '">' . dotTrim( $file->Title, $maxMiniCharWidth, true ) . '</span>';
					}
					else
					{
						$dstr .= '<div ' . ( $file->Current > 0 ? 'class="current"' : '' ) . '><span class="filetype"><img' . $dragdata . ' class="Icon" src="subether/gfx/icons/' . ( $ext_small[$file->Filetype] ? $ext_small[$file->Filetype] : $ext_small['txt'] ) . '"></span>';
						$dstr .= '<span class="filename" onclick="' . ( ( $thumb->IsEdit == 0 || $thumb->IsEdit == $webuser->ID ) ? 'refreshFilesDirectory( \'' . $file->FolderID . '\', \'' . $file->ID . '\' )' : 'alert( \'File is open somewhere else\' )' ) . '" class="files" id="FileID_' . $file->ID . '" filetype="' . $file->MediaType . '">' . dotTrim( $file->Title, $maxMiniCharWidth, true ) . '</span>';
					}
					
					if( $hasAccess )
					{
						$dstr .= '<div class="edit_icons">';
						$dstr .= '<span class="sorting">';
						$dstr .= '<div class="sortUp" onclick="sortDown( \'' . $file->ID . '\', \'' . ( $file->SortOrder > 0 ? ( $file->SortOrder - 1 ) : 0 ) . '\', \'' . $file->MediaType . '\', \'' . $fid . '\' )" title="Up"></div>';
						$dstr .= '<div class="sortDown" onclick="sortUp( \'' . $file->ID . '\', \'' . ( $file->SortOrder + 1 ) . '\', \'' . $file->MediaType . '\', \'' . $fid . '\' )" title="Down"></div>';
						$dstr .= '</span>';
						$dstr .= '<span class="editfile" onclick="editFile( \'' . $file->ID . '\', \'' . $fid . '\' );return false;" title="Edit File"><img class="Icon" src="admin/gfx/icons/page_edit.png"></span>';
						$dstr .= '<span class="deletefile" onclick="deleteFile( \'' . $file->ID . '\', \'' . $file->MediaType . '\', \'' . $fid . '\' );return false;" title="Delete File"><img class="Icon" src="admin/gfx/icons/page_delete.png"></span>';
						$dstr .= '<span class="fileaccess"> <select onclick="cancelBubble(event)" onchange="UpdateAccess(\'' . $file->ID . '\',\'' . ( $file->MediaType == 'image' ? 'image' : 'file' ) . '\',this.value)">';
						$dstr .= '<option title="Public" value="0"' . ( $file->FileAccess == 0 ? ' selected="selected"' : '' ) . '>0</option>';
						$dstr .= '<option title="Contacts" value="1"' . ( $file->FileAccess == 1 ? ' selected="selected"' : '' ) . '>1</option>';
						$dstr .= '<option title="Only Me" value="2"' . ( $file->FileAccess == 2 ? ' selected="selected"' : '' ) . '>2</option>';
						//$dstr .= '<option title="Custom" value="3"' . ( $file->FileAccess == 3 ? ' selected="selected"' : '' ) . '>3</option>';
						$dstr .= '</select></span>';
						$dstr .= '</div>';
					}
					$dstr .= '</div></li>';
				}
				
				$dstr .= '</ul></li>';
				
				if( $sfolder->Current > 0 || $sfolder->Open )
					$Component->FolderID = $currentFolder = $sfolder->ID;
				
				$ii++;
			}
			
			$dstr .= '</ul>';
		}
	}
}

// --- Content / Thumbs ----------------------------------------------------- //

$istr = '<div class="thumbview">';

// Control buttons
$bstr  = '<div class="topcontrols">';
$bstr .= '<select id="ThumbView" mid="' . $pmid . '" onchange="refreshFilesDirectory(this.getAttribute(\'mid\'),false,this.value)">';
$bstr .= '<option value="0" ' . ( $view == 0 ? 'selected="selected"' : '' ) . '>Icon View</option>';
$bstr .= '<option value="1" ' . ( $view == 1 ? 'selected="selected"' : '' ) . '>List View</option></select>';
$bstr .= '</div>';

if( $hasAccess )
{
	$istr .= '<div class="fileupload" onclick="ge(\'FilesUploadBtn\').click()"><div></div></div>';
}

// For editing text content --------------------------------------------------------------------------------------------------------------------------------
if( $content || !$home )
{
	$istr = '<div class="editview"><div id="ContentEditor" ' . 
		( $hasAccess && !$content->Parse ? 'contenteditable="true" fileid="' . 
		$content->ID . '" onkeyup="IsEditing(' . $content->ID . ')"' : '' ) . 
		' class="textarea">' . $content->Content . '</div>';
}
// For showing listview ------------------------------------------------------------------------------------------------------------------------------------
else if( $view == 1 && $thumbs )
{
	$istr  = '<div class="listview"><table><tr class="heading">';
	$istr .= '<th><h4 class="name">Name</h4></th>';
	$istr .= '<th><h4 class="type">Type</h4></th>';
	$istr .= '<th><h4 class="modified">Modified</h4></th>';
	$istr .= '<th></th></tr>';
	
	$imgs = 0;
	foreach( $thumbs as $thumb )
	{
		//$downloadIcon = '<div><a href="' . $thumb->FolderPath . '/' . $thumb->Filename . '"><img src="admin/gfx/icons/disk.png"/></a></div>';
		$downloadLink = $parent->route . '?component=library&action=download&type=' . ( $thumb->MediaType == 'image' ? 'image' : 'file' ) . '&fid=' . $thumb->ID;
		$downloadIcon = '<div class="download"><a href="' . $downloadLink . '"><img src="admin/gfx/icons/disk.png"/></a></div>';
		$verifyIcon = ' <img src="admin/gfx/icons/page_edit.png"/>';
		$verifiedIcon = ' <img src="admin/gfx/icons/accept.png"/>';
		
		$dragdata = ' draggable="yes" ondragstart="window.dragId=\'' . $thumb->ID . '\'; window.dragType=\'' . $thumb->MediaType . '\'"';
		
		switch( $thumb->MediaType )
		{
			
			case 'image':
				$onclick = 'openFullscreen( \'Library\', \'' . $thumb->FolderID . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( ' . (string)$imgs . ' ); } )';
				$icon = '<img class="Icon" src="subether/gfx/icons/' . ( $ext_small[$thumb->Filetype] ? $ext_small[$thumb->Filetype] : $ext_small['png'] ) . '"> ';
				$imgs++;
				break;
			
			case 'video':
				$onclick = 'refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\' )';
				$icon = '<img class="Icon" src="subether/gfx/icons/' . $ext_small['mov'] . '"> ';
				break;
			
			case 'audio':
				$onclick = 'refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\' )';
				$icon = '<img class="Icon" src="subether/gfx/icons/' . $ext_small['mp3'] . '"> ';
				break;
			
			case 'site':
				$onclick = 'refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\' )';
				$icon = '<img class="Icon" src="subether/gfx/icons/' . $ext_small['url'] . '"> ';
				break;
			
			default:
				$onclick = ( ( $thumb->Filetype == 'txt' || $thumb->Filetype == 'plain' || $thumb->Filetype == 'pdf' || $thumb->Filetype == 'parse' ) ? ( ( $thumb->IsEdit == 0 || $thumb->IsEdit == $webuser->ID ) ? 'refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\' )' : 'alert( \'File is open somewhere else\' )' ) : 'document.location=\'' . $downloadLink . '\'' );
				$icon = '<img class="Icon" src="subether/gfx/icons/' . ( $ext_small[$thumb->Filetype] ? $ext_small[$thumb->Filetype] : $ext_small['txt'] ) . '"> ';
				break;
		}
		
		if( $thumb->ModID > 0 )
		{
			$thumb->UserID = $thumb->ModID;
		}
		
		$istr .= '<tr class="file ' . $thumb->Filetype . '" id="FileID_' . $thumb->ID . '" filetype="' . $thumb->MediaType . '" ' . $dragdata . '>';
		$istr .= '<td><div class="name" onclick="' . $onclick . '"><span class="filetype">' . $icon . '</span><span class="filename">' . dotTrim( $thumb->Title, $maxMiniCharWidth2, true ) . '</span><span class="verify">' . ( $thumb->Verified == '-1' ? $verifyIcon : '' ) . ( $thumb->Verified > 0 ? $verifiedIcon : '' ) . '</span></div></td>';
		$istr .= '<td><div class="type">' . $thumb->MediaType . '</div></td>';
		$istr .= '<td><div class="modified">' . date( 'd/m/Y H:i', strtotime( $thumb->DateModified ) ) . ( $thumb->UserID > 0 && ContactID( $thumb->UserID ) ? ' (' . Initials( GetUserDisplayname( ContactID( $thumb->UserID ) ) ) . ')' : '' ) . '</div></td>';
		$istr .= '<td>' . $downloadIcon . '</td>';
		$istr .= '</tr>';
	}
	
	$istr .= '</table>';
}
// For showing thumbnails ------------------------------------------------------------------------------------------------------------------------------------
else if( $thumbs )
{
	$img = array(); $fil = array();

	foreach( $thumbs as $thumb )
	{
		if( $thumb->MediaType == 'image' || $thumb->MediaType == 'video' || $thumb->MediaType == 'site' )
		{
			$img[$thumb->ID] = $thumb->ID;
		}
		else if( in_array( $thumb->Filetype, array( 'webm', 'ogg', 'mp4', 'swf' ) ) )
		{
			if( file_exists( $thumb->FolderPath . str_replace( end( explode( '.', $thumb->Filename ) ), '', $thumb->Filename ) . 'png' ) )
			{
				$fil[$thumb->ID] = '<img class="icon" style="width:105px;height:90px;background-image:url(\'' . $thumb->FolderPath . str_replace( end( explode( '.', $thumb->Filename ) ), '', $thumb->Filename ) . 'png\')"/>';
			}
		}
	}
	
	$im = new dbImage ();
	if( $img && ( $im = $im->find ( 'SELECT * FROM Image WHERE ID IN (' . implode( ',', $img ) . ') ORDER BY ID ASC' ) ) )
	{
		$img = array();
		foreach ( $im as $i )
		{
			$img[$i->ID] = $i->getImageHTML( 105, 90, 'framed', false, 0xffffff );
		}
	}
	if( isset( $_REQUEST['chris'] ) ) die( print_r( $thumbs,1 ) . ' --' );
	$imgs = 0;
	foreach( $thumbs as $thumb )
	{
		$dragdata = ' draggable="yes" ondragstart="window.dragId=\'' . $thumb->ID . '\'; window.dragType=\'' . $thumb->MediaType . '\'"';
		//$downloadIcon = '<div class="Download"><a href="' . $thumb->FolderPath . '/' . $thumb->Filename . '"><img src="admin/gfx/icons/disk.png"/></a></div>';
		$downloadLink = $parent->route . '?component=library&action=download&type=' . ( $thumb->MediaType == 'image' ? 'image' : 'file' ) . '&fid=' . $thumb->ID;
		$downloadIcon = '<div class="Download"><a href="' . $downloadLink . '"><img src="admin/gfx/icons/disk.png"/></a></div>';
		
		// --- Image -----------------------------------------------------------
		if( $thumb->MediaType == 'image' )
		{
			$istr .= '<div class="thumbs ' . $thumb->Filetype . '"' . $dragdata . ' onclick="openFullscreen( \'Library\', \'' . $thumb->FolderID . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( ' . (string)$imgs . ' ); } )" filetype="' . $thumb->Filetype . '">';
			$istr .= $img[$thumb->ID];
			$istr .= $downloadIcon;
			$istr .= '<span class="filename">' . dotTrim( $thumb->Title, 10, true ) . '</span>';
			$istr .= '</div>';
			$imgs++;
		}
		// --- Video -----------------------------------------------------------
		else if( $thumb->MediaType == 'video' || in_array( $thumb->Filetype, array( 'webm', 'ogg', 'ogv', 'mp4', 'swf' ) ) )
		{
			$istr .= '<div class="thumbs ' . $thumb->Filetype . '"' . $dragdata . ' onclick="refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\' )" filetype="' . $thumb->Filetype . '">';
			
			if( $img[$thumb->ID] )
			{
				$istr .= $img[$thumb->ID];
			}
			else if( $fil[$thumb->ID] )
			{
				$istr .= $fil[$thumb->ID];
			}
			else
			{
				$istr .= '<img style="width:90px;height:90px;background-image:url(\'subether/gfx/icons/' . ( $ext_large[$thumb->Filetype] ? $ext_large[$thumb->Filetype] : $ext_large['mov'] ) . '\')"/>';
			}
			$istr .= $downloadIcon;
			$istr .= '<i></i>';
			// TODO: Replace disk icon
			$istr .= '<span class="filename">' . dotTrim( $thumb->Title, 10, true ) . '</span>';
			$istr .= '</div>';
		}
		// --- Site ------------------------------------------------------------
		else if( $thumb->MediaType == 'site' )
		{	
			$istr .= '<div class="thumbs ' . $thumb->Filetype . '"' . $dragdata . ' onclick="refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\' )" filetype="' . $thumb->Filetype . '">';
			
			if( $img[$thumb->ID] )
			{
				$istr .= $img[$thumb->ID];
			}
			else
			{
				$istr .= '<img class="icon" style="width:90px;height:90px;background-image:url(\'subether/gfx/icons/' . ( $ext_large[$thumb->Filetype] ? $ext_large[$thumb->Filetype] : $ext_large['url'] ) . '\')" src="subether/gfx/icons/' . ( $ext_large[$thumb->Filetype] ? $ext_large[$thumb->Filetype] : $ext_large['url'] ) . '\')"/>';
			}
			$istr .= $downloadIcon;
			$istr .= '<span class="filename">' . dotTrim( $thumb->Title, 10, true ) . '</span>';
			$istr .= '</div>';
		}
		// --- Audio -----------------------------------------------------------
		else if( $thumb->MediaType == 'audio' )
		{
			$istr .= '<div class="thumbs ' . $thumb->Filetype . '"' . $dragdata . ' onclick="refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\' )" filetype="' . $thumb->Filetype . '">';
			$istr .= '<img style="width:90px;height:90px;background-image:url(\'subether/gfx/icons/' . ( $ext_large[$thumb->Filetype] ? $ext_large[$thumb->Filetype] : $ext_large['mp3'] ) . '\')"/>';
			$istr .= '<i></i>';
			$istr .= '<span class="filename">' . dotTrim( $thumb->Title, 10, true ) . '</span>';
			$istr .= '</div>';
		}
		// --- File ------------------------------------------------------------
		else if( $thumb->MediaType == 'file' )
		{
			$istr .= '<div class="thumbs ' . $thumb->Filetype . '"' . $dragdata . ( ( $thumb->Filetype == 'txt' || $thumb->Filetype == 'plain' || $thumb->Filetype == 'pdf' || $thumb->Filetype == 'parse' ) ? ( ( $thumb->IsEdit == 0 || $thumb->IsEdit == $webuser->ID ) ? ' onclick="refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\' )"' : ' onclick="alert( \'File is open somewhere else\' )"' ) : ' onclick="document.location=\'' . $downloadLink . '\'"' ) . ' filetype="' . $thumb->Filetype . '">';
			$istr .= $downloadIcon;
			$istr .= '<img class="icon" style="width:90px;height:90px;background-image:url(\'subether/gfx/icons/' . ( $ext_large[$thumb->Filetype] ? $ext_large[$thumb->Filetype] : $ext_large['txt'] ) . '\')" src="subether/gfx/icons/' . ( $ext_large[$thumb->Filetype] ? $ext_large[$thumb->Filetype] : $ext_large['txt'] ) . '"/>';
			$istr .= '<span class="filename">' . dotTrim( $thumb->Title, 10, true ) . '</span>';
			$istr .= '</div>';
		}
	}
}

$istr .= '<div class="clearboth" style="clear:both"></div>';
$istr .= '</div>';

if( $home && isset( $_REQUEST[ 'bajaxrand' ] ) ) die( 'ok<!--separate-->' . $dstr . '<!--separate-->' . $currentFolder . '<!--separate-->' . $istr . '<!--separate-->' . $content->Content );

?>
