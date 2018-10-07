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
	$istr .= '<div class="library_component listview"><table><tr class="heading">';
	$istr .= '<th><h4 class="name">' . i18n( 'i18n_Name' ) . '</h4></th>';
	$istr .= '<th><h4 class="type">' . i18n( 'i18n_Type' ) . '</h4></th>';
	$istr .= '<th><h4 class="modified">' . i18n( 'i18n_Modified' ) . '</h4></th>';
	$istr .= '<th></th></tr>';
	
	$imgs = 0; $lib = new Library();
	
	foreach( $folders['thumbs'] as $thumb )
	{
		//$downloadLink = '?component=library&action=download&type=' . ( $thumb->MediaType == 'image' ? 'image' : 'file' ) . '&fid=' . $thumb->ID;
		$downloadLink = $thumb->DownloadUrl;
		$downloadIcon = ( $thumb->ID > 0 ? '<span class="download" style="float:left;"><a href="' . $downloadLink . '"><img src="admin/gfx/icons/disk.png"/></a></span>' : '' );
		$verifyIcon = ' <img src="admin/gfx/icons/page_edit.png"/>';
		$verifiedIcon = ' <img src="admin/gfx/icons/accept.png"/>';
		
		$deleteIcon = ( $thumb->ID > 0 ? '<span class="deletefile" style="float:left;cursor:pointer;margin-right:10px;" onclick="deleteFile( \'' . $thumb->ID . '\', \'' . $thumb->MediaType . '\', \'mid_' . $thumb->FolderID . '\' ); return cancelBubble( event )" title="' . i18n( 'i18n_Delete File' ) . '"><img class="Icon" src="admin/gfx/icons/page_delete.png"></span>' : '' );
		
		$mimetype = $lib->MimeType( $thumb->Filename, ( BASE_DIR.'/'.$thumb->FolderPath ) );
		$dlurl = ( $mimetype . ':' . $thumb->Filename . ':' . BASE_URL . $thumb->FolderPath . $thumb->Filename );
		
		//$dragdata = ' draggable="true" ondragstart="handleDragStart(this,\''.$thumb->ID.'\',\''.$thumb->MediaType.'\',event)" data-downloadurl="' . $dlurl . '"';
		//$dragdata = ' draggable="yes" ondragstart="window.dragId=\'' . $thumb->ID . '\'; window.dragType=\'' . $thumb->MediaType . '\'"';
		$dragdata = ' draggable="true" ondragstart="handleDragStart(this,\''.$thumb->ID.'\',\''.$thumb->MediaType.'\',event)"';
		
		$fpath = ( !$thumb->ID && $thumb->FilePath ? $thumb->FilePath : false );
		
		switch( $thumb->MediaType )
		{
			// --- Image -----------------------------------------------------------
			case 'image':
				$onclick = 'openFullscreen( \'Library\', \'' . $thumb->FolderID . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( ' . (string)$imgs . ' ); }, false, event )';
				$icon = '<img class="Icon" src="subether/gfx/icons/' . ( libraryIcons( $thumb->Filetype, 16 ) ? libraryIcons( $thumb->Filetype, 16 ) : libraryIcons( 'png', 16 ) ) . '"> ';
				$imgs++;
				break;
			// --- Video -----------------------------------------------------------
			case 'video':
				$onclick = 'refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\', false, event )';
				$icon = '<img class="Icon" src="subether/gfx/icons/' . libraryIcons( 'mov', 16 ) . '"> ';
				break;
			// --- Audio -----------------------------------------------------------
			case 'audio':
				$onclick = 'refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\', false, event )';
				$icon = '<img class="Icon" src="subether/gfx/icons/' . libraryIcons( 'mp3', 16 ) . '"> ';
				break;
			// --- Site -----------------------------------------------------------
			case 'site':
				$onclick = 'refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\', false, event )';
				$icon = '<img class="Icon" src="subether/gfx/icons/' . libraryIcons( 'url', 16 ) . '"> ';
				break;
			// --- File -----------------------------------------------------------
			default:
				$onclick = ( in_array( $thumb->Filetype, array( 'txt', 'plain', 'css', 'pdf', 'parse', 'meta' ) ) ? ( ( $thumb->IsEdit == 0 || $thumb->IsEdit == $webuser->ID ) ? 'refreshFilesDirectory( \'' . $thumb->FolderID . '\', \'' . $thumb->ID . '\', false, event, false, \'' . $fpath . '\' )' : 'alert( \'' . i18n( 'i18n_File is open somewhere else' ) . '\' )' ) : 'document.location=\'' . $downloadLink . '\'' );
				$icon = '<img class="Icon" src="subether/gfx/icons/' . ( libraryIcons( $thumb->Filetype, 16 ) ? libraryIcons( $thumb->Filetype, 16 ) : libraryIcons( 'txt', 16 ) ) . '"> ';
				break;
		}
		
		if( $thumb->ModID > 0 )
		{
			$thumb->UserID = $thumb->ModID;
		}
		
		$istr .= '<tr class="file ' . $thumb->Filetype . '" id="FileID_' . $thumb->ID . '" filetype="' . $thumb->MediaType . '">';
		$istr .= '<td><div class="name" onclick="' . $onclick . '">';
		$istr .= '<a' . $dragdata . ' onclick="return false" href="' . $thumb->DownloadUrl . '"><span class="filetype">' . $icon . '</span><span class="filename">' . dotTrim( $thumb->Title, $maxMiniCharWidth2, true ) . '</span><span class="verify">' . ( $thumb->Verified == '-1' ? $verifyIcon : '' ) . ( $thumb->Verified > 0 ? $verifiedIcon : '' ) . '</span></a>';
		$istr .= '</div></td>';
		$istr .= '<td><div class="type">' . $thumb->MediaType . '</div></td>';
		$istr .= '<td><div class="modified">' . date( 'd/m/Y H:i', strtotime( $thumb->DateModified ) ) . ( $thumb->UserID > 0 && ContactID( $thumb->UserID ) ? ' (' . Initials( GetUserDisplayname( ContactID( $thumb->UserID ) ) ) . ')' : '' ) . '</div></td>';
		$istr .= '<td>' . ( $thumb->HasAccess ? $deleteIcon : '' ) . ' ' . $downloadIcon . '</td>';
		$istr .= '</tr>';
	}
	
	$istr .= '</table>';
}

?>
