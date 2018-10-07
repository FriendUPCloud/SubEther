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

if( isset( $folders['thumbs'] ) )
{
	$imgs = 0; $lib = new Library();
	
	foreach( $folders['thumbs'] as $thumb )
	{
		$icon = ''; $bgimg = false; $onclick = false;
		
		//$mimetype = $lib->MimeType( $thumb->Filename, ( BASE_DIR.'/'.$thumb->FolderPath ) );
		//$dlurl = ( $mimetype . ':' . $thumb->Filename . ':' . BASE_URL . $thumb->FolderPath . $thumb->Filename );
		
		//$downloadLink = '?component=library&action=download&type=' . ( $thumb->MediaType == 'image' ? 'image' : 'file' ) . '&fid=' . $thumb->ID;
		$downloadLink = $thumb->DownloadUrl;
		//die( $thumb->ThumbUrl . ' --' );
		//$dlurl = ( $mimetype . ':' . $thumb->Filename . ':' . ( BASE_URL . $downloadLink ) );
		
		//$dragdata = ' draggable="true" ondragstart="window.dragId=\'' . $thumb->ID . '\'; window.dragType=\'' . $thumb->MediaType . '\'"';
		//$dragdata = ' draggable="true" ondragstart="handleDragStart(this,\''.$thumb->ID.'\',\''.$thumb->MediaType.'\',event)" data-downloadurl="' . $dlurl . '"';
		$dragdata = ' draggable="true" ondragstart="handleDragStart(this,\''.$thumb->ID.'\',\''.$thumb->MediaType.'\',event)"';
		
		$downloadIcon = ( $thumb->ID > 0 ? '<span class="Download"><a href="' . $downloadLink . '"><img src="admin/gfx/icons/disk.png"/></a></span>' : '' );
		
		$deleteIcon = ( $thumb->ID > 0 ? '<span class="Delete" onclick="deleteFile( \'' . $thumb->ID . '\', \'' . $thumb->MediaType . '\', \'mid_' . $thumb->FolderID . '\' ); return cancelBubble( event )" title="' . i18n( 'i18n_Delete File' ) . '"><img class="Icon" src="admin/gfx/icons/page_delete.png"></span>' : '' );
		
		$fpath = ( !$thumb->ID && $thumb->FilePath ? $thumb->FilePath : false );
		
		switch( $thumb->MediaType )
		{
			// --- Image -----------------------------------------------------------
			case 'image':
				$onclick = 'openFullscreen( \'Library\', \'' . $thumb->FolderID . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( ' . (string)$imgs . ' ); } )';
				//$icon = '<img class="Icon" style="width:90px;height:90px;background-image:url(\'subether/gfx/icons/' . ( libraryIcons( $thumb->Filetype, 128 ) ? libraryIcons( $thumb->Filetype, 128 ) : libraryIcons( 'png', 128 ) ) . '\')" src="subether/gfx/icons/' . ( libraryIcons( $thumb->Filetype, 128 ) ? libraryIcons( $thumb->Filetype, 128 ) : libraryIcons( 'png', 128 ) ) . '"> ';
				// Check for thumb image
				if( libraryThumbs( $thumb->FolderPath, $thumb->Filename ) )
				{
					//$icon = '<img class="Icon" style="width:105px;height:90px;background-image:url(\'' . libraryThumbs( $thumb->FolderPath, $thumb->Filename ) . '\')" src="' . libraryThumbs( $thumb->FolderPath, $thumb->Filename ) . '"> ';
					$bgimg = ' style="background-image:url(\'' . /*libraryThumbs( $thumb->FolderPath, $thumb->Filename )*/libraryThumbs( $thumb->ThumbUrl ) . '\')"';
				}
				$imgs++;
				break;
			// --- Video -----------------------------------------------------------
			case 'video':
				$onclick = 'refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\', false, event )';
				//$icon  = '<img class="Icon" style="width:90px;height:90px;background-image:url(\'subether/gfx/icons/' . libraryIcons( 'mov', 128 ) . '\')" src="subether/gfx/icons/' . libraryIcons( 'mov', 128 ) . '"> ';
				// Check for thumb image
				if( libraryThumbs( $thumb->FolderPath, $thumb->Filename, 'video', $thumb->Filetype ) )
				{
					//$icon = '<img class="Icon" style="width:105px;height:90px;background-image:url(\'' . libraryThumbs( $thumb->FolderPath, $thumb->Filename, 'video' ) . '\')" src="' . libraryThumbs( $thumb->FolderPath, $thumb->Filename, 'video' ) . '"> ';
					$bgimg = ' style="background-image:url(\'' . /*libraryThumbs( $thumb->FolderPath, $thumb->Filename, 'video', $thumb->Filetype )*/libraryThumbs( $thumb->ThumbUrl, false, 'video', $thumb->Filetype ) . '\')"';
				}
				$icon .= '<i></i>';
				break;
			// --- Audio -----------------------------------------------------------
			case 'audio':
				$onclick = 'refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\', false, event )';
				//$icon  = '<img class="Icon" style="width:90px;height:90px;background-image:url(\'subether/gfx/icons/' . libraryIcons( 'mp3', 128 ) . '\')" src="subether/gfx/icons/' . libraryIcons( 'mp3', 128 ) . '"> ';
				$icon .= '<i></i>';
				break;
			// --- Site ------------------------------------------------------------
			case 'site':
				$onclick = 'refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\', false, event )';
				//$icon = '<img class="Icon" style="width:90px;height:90px;background-image:url(\'subether/gfx/icons/' . libraryIcons( 'url', 128 ) . '\')" src="subether/gfx/icons/' . libraryIcons( 'url', 128 ) . '"/> ';
				// Check for thumb image
				if( libraryThumbs( $thumb->FolderPath, $thumb->Filename ) )
				{
					//$icon = '<img class="Icon" style="width:90px;height:90px;background-image:url(\'' . libraryThumbs( $thumb->FolderPath, $thumb->Filename ) . '\')" src="' . libraryThumbs( $thumb->FolderPath, $thumb->Filename ) . '"> ';
					$bgimg = ' style="background-image:url(\'' . /*libraryThumbs( $thumb->FolderPath, $thumb->Filename )*/libraryThumbs( $thumb->ThumbUrl ) . '\')"';
				}
				break;
			// --- Files -----------------------------------------------------------
			default:
				$onclick = ( in_array( $thumb->Filetype, array( 'txt', 'plain', 'css', 'pdf', 'parse', 'meta' ) ) ? ( ( $thumb->IsEdit == 0 || $thumb->IsEdit == $webuser->ID ) ? 'refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\', false, event, false, \'' . $fpath . '\' )' : 'alert( \'' . i18n( 'i18n_File is open somewhere else' ) . '\' )' ) : 'document.location=\'' . $downloadLink . '\'' );
				//$icon = '<img class="Icon" style="width:90px;height:90px;background-image:url(\'subether/gfx/icons/' . ( libraryIcons( $thumb->Filetype, 128 ) ? libraryIcons( $thumb->Filetype, 128 ) : libraryIcons( 'txt', 128 ) ) . '\')" src="subether/gfx/icons/' . ( libraryIcons( $thumb->Filetype, 128 ) ? libraryIcons( $thumb->Filetype, 128 ) : libraryIcons( 'txt', 128 ) ) . '"/> ';
				break;
		}
		
		if( $thumb->ModID > 0 )
		{
			$thumb->UserID = $thumb->ModID;
		}
		
		$istr .= '<div class="thumbs ' . $thumb->Filetype . ( $bgimg ? ' image' : '' ) . '" onclick="' . $onclick . '" id="FileID_' . $thumb->ID . '" filetype="' . $thumb->Filetype . '">';
		$istr .= ( $bgimg ? '<div class="image"' . $bgimg . '></div>' : '' );
		$istr .= $icon;
		$istr .= ( $thumb->HasAccess ? $deleteIcon : '' );
		$istr .= $downloadIcon;
		$istr .= '<a' . $dragdata . ' onclick="return false" href="' . $thumb->DownloadUrl . '"><span class="filename">' . dotTrim( $thumb->Title, 10, true ) . '</span></a>';
		$istr .= '</div>';
	}
}

?>
