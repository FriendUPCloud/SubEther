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

$dstr = ''; $lib = new Library();

if( $folders )
{
	$dstr .= '<div class="library_component mobile directory">';
	
	foreach( $folders as $mfolder )
	{
		if( !$mfolder->ID || !$mfolder->Name ) continue;
		
		// --- Home Folders ------------------------------------------------------------------------------------------------------------------- //
		
		$dstr .= '<h4 class="mainfolder ' . strtolower( $mfolder->Name ) . '"><div>';
		$dstr .= '<span class="foldername" id="FolderID_' . $mfolder->ID . '" foldername="' . $mfolder->Name . '">' . $mfolder->Name . '</span>';
		if( $mfolder->HasAccess )
		{
			$dstr .= '<div class="edit_icons">';
			$dstr .= '<span class="sorting">';
			$dstr .= '<div class="sortUp" onclick="sortDown( \'' . $mfolder->ID . '\', \'' . ( $mfolder->SortOrder > 0 ? ( $mfolder->SortOrder - 1 ) : 0 ) . '\', \'folder\', \'' . $fid . '\' )" title="Up"><img class="Icon" src="admin/gfx/icons/bullet_up.png"></div>';
			$dstr .= '<div class="sortDown" onclick="sortUp( \'' . $mfolder->ID . '\', \'' . ( $mfolder->SortOrder + 1 ) . '\', \'folder\', \'' . $fid . '\' )" title="Down"><img class="Icon" src="admin/gfx/icons/bullet_down.png"></div>';
			$dstr .= '</span>';
			$dstr .= '<span class="newfile" onclick="createNewFile( \'' . ( $_POST['mid'] > 0 ? $_POST['mid'] : $mfolder->SubFolders[0]->ID ) . '\', \'' . $fid . '\' )" title="Create New File"><img class="Icon" src="admin/gfx/icons/page_add.png"></span>';
			$dstr .= '<span class="parsefile" onclick="openWindow( \'Library\', \'' . $fid . '\', \'parse\' )" title="Create File By Url"><img class="Icon" src="admin/gfx/icons/page_world.png"></span>';
			if( !in_array( strtolower( $mfolder->Name ), array( 'library', 'album' ) ) ) 
			{
				$dstr .= '<span class="editfolder" onclick="editFolder( \'' . $mfolder->ID . '\', event )" title="Edit Folder"><img class="Icon" src="admin/gfx/icons/folder_edit.png"></span>';
			}
			$dstr .= '<span class="newfolder" onclick="createNewFolder( \'' . $mfolder->ID . '\', \'' . $fid . '\' )" title="Create New Folder"><img class="Icon" src="admin/gfx/icons/folder_add.png"></span>';
			if( !in_array( strtolower( $mfolder->Name ), array( 'library', 'album' ) ) ) 
			{
				$dstr .= '<span class="deletefolder" onclick="deleteFolder( \'' . $mfolder->ID . '\', \'' . $fid . '\' )" title="Delete Folder"><img class="Icon" src="admin/gfx/icons/folder_delete.png"></span>';
			}
			//$dstr .= '<span>';
			//$dstr .= '</span>';
			$dstr .= '</div>';
		}
		$dstr .= '</div></h4>';
		
		if( $mfolder->SubFolders )
		{
			$dstr .= '<ul class="subfolders">';
			
			foreach( $mfolder->SubFolders as $sfolder )
			{
				// --- Sub Folders ------------------------------------------ //
				
				$dstr .= '<li class="subfolder ' . strtolower( $sfolder->Name ) . '">';
				$dstr .= '<div class="subfolder' . ( $sfolder->Current > 0 ? ' current' : '' ) . '" onclick="refreshFilesDirectory( \'' . $sfolder->ID . '\', false, false, event )">';
				$dstr .= '<span class="toggle" onclick="openSubList( this, \'' . $sfolder->ID . '\', event )"><img class="Icon" src="lib/icons/bullet_toggle_' . ( $sfolder->Open > 0 ? 'minus' : 'plus' ) . '.png"></span>';
				$dstr .= '<span class="foldername" id="FolderID_' . $sfolder->ID . '" ondragover="this.style.fontWeight = \'bold\'; handleDragOver(event); return false" ondragleave="this.style.fontWeight = \'normal\'; handleDragLeave(event); return false" ondrop="handleDrop(window.dragId,window.dragType,' . $sfolder->ID . ',false,false,event); return false;" foldername="' . $sfolder->Name . '">' . dotTrim( $sfolder->Name, 18 ) . '</span>';
				if( $sfolder->HasAccess )
				{
					$dstr .= '<div class="edit_icons">';
					$dstr .= '<span class="sorting">';
					$dstr .= '<div class="sortUp" onclick="sortDown( \'' . $sfolder->ID . '\', \'' . ( $sfolder->SortOrder > 0 ? ( $sfolder->SortOrder - 1 ) : 0 ) . '\', \'folder\', \'' . $fid . '\' )" title="Up"><img class="Icon" src="admin/gfx/icons/bullet_up.png"></div>';
					$dstr .= '<div class="sortDown" onclick="sortUp( \'' . $sfolder->ID . '\', \'' . ( $sfolder->SortOrder + 1 ) . '\', \'folder\', \'' . $fid . '\' )" title="Down"><img class="Icon" src="admin/gfx/icons/bullet_down.png"></div>';
					$dstr .= '</span>';
					$dstr .= '<span class="fileupload" onclick="ge(\'FilesUploadBtn\').click();return false;" title="Upload Files"><img class="Icon" src="admin/gfx/icons/derivatives.png"></span>';
					$dstr .= '<span class="editfolder" onclick="editFolder( \'' . $sfolder->ID . '\', event ); return cancelBubble( event )" title="Edit Folder"><img class="Icon" src="admin/gfx/icons/page_edit.png"></span>';
					$dstr .= '<span class="deletefolder" onclick="deleteFolder( \'' . $sfolder->ID . '\', \'' . $fid . '\' )" title="Delete Folder"><img class="Icon" src="admin/gfx/icons/page_delete.png"></span>';
					$dstr .= '<span class="folderaccess"> <select title="Folder Access" onclick="cancelBubble(event)" onchange="UpdateAccess(\'' . $sfolder->ID . '\',\'folder\',this.value)">';
					$dstr .= '<option title="Public" value="0"' . ( $sfolder->FolderAccess == 0 ? ' selected="selected"' : '' ) . '>0 -> Public</option>';
					$dstr .= '<option title="Contacts" value="1"' . ( $sfolder->FolderAccess == 1 ? ' selected="selected"' : '' ) . '>1 -> Contacts</option>';
					$dstr .= '<option title="Only Me" value="2"' . ( $sfolder->FolderAccess == 2 ? ' selected="selected"' : '' ) . '>2 -> Only Me</option>';
					//$dstr .= '<option title="Custom" value="3"' . ( $sfolder->FolderAccess == 3 ? ' selected="selected"' : '' ) . '>3 -> Custom</option>';
					if( isset( $parent->access->IsAdmin ) )
					{
						$dstr .= '<option title="Admin" value="4"' . ( $sfolder->FolderAccess == 4 ? ' selected="selected"' : '' ) . '>4 -> Admin</option>';
					}
					$dstr .= '</select></span>';
					$dstr .= '</div>';
				}
				$dstr .= '</div>';
				
				if( $sfolder->Files )
				{
					$imgs = 0; $sfstr = '';
					
					$dstr .= '<ul ' . ( $sfolder->Open > 0 ? 'class="open"' : '' ) . '>';
					
					foreach( $sfolder->Files as $file )
					{
						$mimetype = $lib->MimeType( $file->Filename, ( BASE_DIR.'/'.$file->FolderPath ) );
						$dlurl = ( $mimetype . ':' . $file->Filename . ':' . BASE_URL . $file->FolderPath . $file->Filename );
						
						//$dragdata = ' draggable="true" ondragstart="handleDragStart(this,\''.$file->ID.'\',\''.$file->MediaType.'\',event)" data-downloadurl="' . $dlurl . '"';
						$dragdata = ' draggable="true" ondragstart="handleDragStart(this,\''.$file->ID.'\',\''.$file->MediaType.'\',event)"';
						//$downloadLink = '?component=library&action=download&type=' . ( $file->MediaType == 'image' ? 'image' : 'file' ) . '&fid=' . $file->ID;
						$downloadLink = $file->DownloadUrl;
						$downloadIcon = '<div class="Download"><a href="' . $downloadLink . '"><img src="admin/gfx/icons/disk.png"/></a></div>';
						
						$fpath = ( !$file->ID && $file->FilePath ? $file->FilePath : false );
						
						// --- Files -------------------------------------------- //
						
						$sfstr .= '<li class="file ' . $file->Filetype . '">';
						
						switch( $file->MediaType )
						{
							// --- Image -----------------------------------------------------------
							case 'image':
								$onclick = 'openFullscreen( \'Library\', \'' . $file->FolderID . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( ' . (string)$imgs . ' ); } )';
								$icon = '<img class="Icon" src="subether/gfx/icons/' . ( libraryIcons( $file->Filetype, 16 ) ? libraryIcons( $file->Filetype, 16 ) : libraryIcons( 'png', 16 ) ) . '"> ';
								$imgs++;
								break;
							// --- Video -----------------------------------------------------------
							case 'video':
								$onclick = 'openFullscreen( \'Library\', \'' . $file->ID . '\', \'content\' )';
								//$onclick = 'refreshFilesDirectory( \'' . $file->FolderID . '\', \'' . $file->ID . '\', false, event )';
								$icon = '<img class="Icon" src="subether/gfx/icons/' . libraryIcons( 'mov', 16 ) . '"> ';
								break;
							// --- Audio -----------------------------------------------------------
							case 'audio':
								$onclick = 'openFullscreen( \'Library\', \'' . $file->ID . '\', \'content\' )';
								//$onclick = 'refreshFilesDirectory( \'' . $file->FolderID . '\', \'' . $file->ID . '\', false, event )';
								$icon = '<img class="Icon" src="subether/gfx/icons/' . libraryIcons( 'mp3', 16 ) . '"> ';
								break;
							// --- Site -----------------------------------------------------------
							case 'site':
								$onclick = 'openFullscreen( \'Library\', \'' . $file->ID . '\', \'content\' )';
								//$onclick = 'refreshFilesDirectory( \'' . $file->FolderID . '\', \'' . $file->ID . '\', false, event )';
								$icon = '<img class="Icon" src="subether/gfx/icons/' . libraryIcons( 'url', 16 ) . '"> ';
								break;
							// --- File -----------------------------------------------------------
							default:
								$onclick = ( in_array( $file->Filetype, array( 'txt', 'plain', 'css', 'pdf', 'parse', 'meta' ) ) ? ( ( $file->IsEdit == 0 || $file->IsEdit == $webuser->ID ) ? 'openFullscreen( \'Library\', \'' . $file->ID . '\', \'content\', \'' . $fpath . '\' )' : 'alert( \'File is open somewhere else\' )' ) : 'document.location=\'' . $downloadLink . '\'' );
								//$onclick = ( in_array( $file->Filetype, array( 'txt', 'plain', 'pdf', 'parse' ) ) ? ( ( $file->IsEdit == 0 || $file->IsEdit == $webuser->ID ) ? 'refreshFilesDirectory( \'' . $file->FolderID . '\', \'' . $file->ID . '\', false, event )' : 'alert( \'File is open somewhere else\' )' ) : 'document.location=\'' . $downloadLink . '\'' );
								$icon = '<img class="Icon" src="subether/gfx/icons/' . ( libraryIcons( $file->Filetype, 16 ) ? libraryIcons( $file->Filetype, 16 ) : libraryIcons( 'txt', 16 ) ) . '"> ';
								break;
						}
						
						$sfstr .= '<div class="file ' . ( $file->Current > 0 ? ' current' : '' ) . '"><a' . $dragdata . ' onclick="return false" href="' . ( BASE_URL . $file->FolderPath . $file->Filename ) . '"><span class="filetype"><img class="Icon" src="subether/gfx/icons/' . ( libraryIcons( $file->Filetype, 16 ) ? libraryIcons( $file->Filetype, 16 ) : libraryIcons( 'txt', 16 ) ) . '"></span>';
						$sfstr .= '<span class="filename" onclick="' . $onclick . '" class="files" id="FileID_' . $file->ID . '" filetype="' . $file->MediaType . '" filename="' . $file->Title . '">' . dotTrim( $file->Title, $maxMiniCharWidth2, true ) . '</span>';
						$sfstr .= '<span class="filesize" style="float:right;padding-right:20px;">' . libraryFilesize( $file->Filesize ) . '</span>';
						$sfstr .= '<span class="download" onclick="document.location=\'' . $downloadLink . '\'"><img src="admin/gfx/icons/disk.png"/></span></a>';
						if( $file->HasAccess && $file->ID > 0 )
						{
							$sfstr .= '<div class="edit_icons">';
							$sfstr .= '<span class="sorting">';
							$sfstr .= '<div class="sortUp" onclick="sortDown( \'' . $file->ID . '\', \'' . ( $file->SortOrder > 0 ? ( $file->SortOrder - 1 ) : 0 ) . '\', \'' . $file->MediaType . '\', \'' . $fid . '\' )" title="Up"><img class="Icon" src="admin/gfx/icons/bullet_up.png"></div>';
							$sfstr .= '<div class="sortDown" onclick="sortUp( \'' . $file->ID . '\', \'' . ( $file->SortOrder + 1 ) . '\', \'' . $file->MediaType . '\', \'' . $fid . '\' )" title="Down"><img class="Icon" src="admin/gfx/icons/bullet_down.png"></div>';
							$sfstr .= '</span>';
							$sfstr .= '<span class="editfile" onclick="editFile( \'' . $file->ID . '\', event );return false;" title="Edit File"><img class="Icon" src="admin/gfx/icons/page_edit.png"></span>';
							$sfstr .= '<span class="deletefile" onclick="deleteFile( \'' . $file->ID . '\', \'' . $file->MediaType . '\', \'' . $fid . '\' );return false;" title="Delete File"><img class="Icon" src="admin/gfx/icons/page_delete.png"></span>';
							$sfstr .= '<span class="fileaccess"> <select title="File Access" onclick="cancelBubble(event)" onchange="UpdateAccess(\'' . $file->ID . '\',\'' . ( $file->MediaType == 'image' ? 'image' : 'file' ) . '\',this.value)">';
							$sfstr .= '<option title="Public" value="0"' . ( $file->FileAccess == 0 ? ' selected="selected"' : '' ) . '>0 -> Public</option>';
							$sfstr .= '<option title="Contacts" value="1"' . ( $file->FileAccess == 1 ? ' selected="selected"' : '' ) . '>1 -> Contacts</option>';
							$sfstr .= '<option title="Only Me" value="2"' . ( $file->FileAccess == 2 ? ' selected="selected"' : '' ) . '>2 -> Only Me</option>';
							//$sfstr .= '<option title="Custom" value="3"' . ( $file->FileAccess == 3 ? ' selected="selected"' : '' ) . '>3 -> Custom</option>';
							if( isset( $parent->access->IsAdmin ) )
							{
								$sfstr .= '<option title="Admin" value="4"' . ( $file->FileAccess == 4 ? ' selected="selected"' : '' ) . '>4 -> Admin</option>';
							}
							$sfstr .= '</select></span>';
							$sfstr .= '</div>';
						}
						$sfstr .= '</div>';
						
						$sfstr .= '</li>';
					}
					
					$dstr .= $sfstr;
					
					$dstr .= '</ul></li>';
				}
			}
			
			$dstr .= '</ul>';
		}
	}
	
	$dstr .= '</div>';
}

// If we just need the file index list return choosen filelist only
if( $folders && !isset( $_REQUEST['global'] ) && isset( $_REQUEST[ 'bajaxrand' ] ) && isset( $_REQUEST['index'] ) && $_REQUEST['index'] == 'files' )
{
	die( 'ok<!--separate-->' . $sfstr );
}

?>
