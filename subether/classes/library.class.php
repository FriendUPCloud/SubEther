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

class Library
{
	var $Root = 'subether/upload/';
	var $Component;
	var $Table;
	var $Home;
	var $UserID;
	var $UserName;
	var $CategoryID;
	var $ParentID;
	var $ParentFolder;
	var $ParentPath;
	var $FolderID;
	var $FolderUniqueID;
	var $FolderName;
	var $FolderPath;
	var $FolderChmod = 777;
	var $FolderAccess = 1;
	var $Folders;
	var $FileID;
	var $FileUniqueID;
	var $FileFolder;
	var $ImageFolder;
	var $Filetitle;
	var $Filename;
	var $FileType;
	var $FilePath;
	var $Filesize;
	var $FileWidth;
	var $FileHeight;
	var $FileDuration;
	var $FileColorSpace;
	var $FileEncoding;
	var $FileChmod = 777;
	var $FileAccess = 1;
	var $MediaID;
	var $MediaType;
	var $SortOrder;
	var $IsEdit = 0;
	
	/* --- Component --------------------------------------------------------------------------------------------------------------------------- */
	
	function __construct ( $_function = false )
	{
		global $webuser;
		
		if( strtolower( $_function ) == 'folder' || strtolower( $_function ) == 'file' || strtolower( $_function ) == 'image' )
		{
			$this->Table = $_function;
		}
		
		if( !$this->UserID )
		{
			$this->UserID = $webuser->ID;
			$this->UserName = $webuser->Name;
		}
	}
	
	function SetComponent ()
	{
		if( !$this->Component )
		{
			if( $this->CategoryID > 0 )
			{
				$this->Component = 'Groups';
			}
			else
			{
				$this->Component = 'Profile';
			}
		}
		
		if( $this->Component )
		{
			if( strtolower( $this->Component ) == 'profile' && $this->UserID > 0 )
			{
				$this->Home = $this->UserID;
			}
			else if( $this->CategoryID > 0 )
			{
				$this->Home = $this->CategoryID;
			}
			
			return true;
		}
		
		return false;
	}
	
	/* --- Default ----------------------------------------------------------------------------------------------------------------------------- */
	
	function GetDefaultFolders ()
	{
		$folders1 = new stdClass();
		$folders1->Parent = 'Library';
		$folders1->Folders = array( 'file'=>'Documents', 'image'=>'Photos', 'audio'=>'Audio', 'video'=>'Video', 'other'=>'Bookmarks' );
		
		$folders2 = new stdClass();
		$folders2->Parent = 'Theme';
		$folders2->Folders = array( 'theme'=>'Default' );
		
		return array( $folders1, $folders2 );
	}
	
	/* --- Folder ------------------------------------------------------------------------------------------------------------------------------ */
	
	function GetFolderPath ( $_id = false )
	{
		global $database;
		
		if( !$_id && ( $this->FileFolder > 0 || $this->ImageFolder > 0 ) )
		{
			$_id = ( $this->FileFolder ? $this->FileFolder : $this->ImageFolder );
			
			$q = '
				SELECT 
					mf.ID AS ParentID, 
					mf.Name AS ParentFolder, 
					mf.DiskPath AS ParentPath,
					sf.ID AS FolderID, 
					sf.Name AS FolderName, 
					sf.DiskPath AS FolderPath 
				FROM 
					Folder mf, 
					Folder sf 
				WHERE 
					sf.ID = \'' . $_id . '\' 
					AND mf.ID = sf.Parent 
				ORDER BY 
					sf.ID ASC 
			';
		}
		else if( !$_id && $this->Parent > 0 )
		{
			$_id = $this->Parent;
			
			$q = '
				SELECT 
					mf.ID AS ParentID, 
					mf.Name AS ParentFolder, 
					mf.DiskPath AS ParentPath 
				FROM 
					Folder mf 
				WHERE 
					mf.ID = \'' . $_id . '\' 
				ORDER BY 
					mf.ID ASC 
			';
		}
		
		if( $_id > 0 && $folder = $database->fetchObjectRow ( $q, false, 'classes/library.class.php' ) )
		{
			if( $folder->ParentID ) $this->ParentID = $folder->ParentID;
			if( $folder->ParentFolder ) $this->ParentFolder = $folder->ParentFolder;
			if( $folder->ParentPath ) $this->ParentPath = $folder->ParentPath;
			if( $folder->FolderID ) $this->FolderID = $folder->FolderID;
			if( $folder->FolderName ) $this->FolderName = $folder->FolderName;
			if( $folder->FolderPath ) $this->FolderPath = $folder->FolderPath;
		}
		
		return true;
	}
	
	function SetFolders ()
	{
		$mimetype = false;
		
		$filetypes = array(
			'application'=>'file',
			'text'=>'file',
			'image'=>'image',
			'audio'=>'audio',
			'video'=>'video',
			'theme'=>'theme'
		);
		
		$folders = $this->GetDefaultFolders();
		
		if( $folders && $this->ParentFolder != '' )
		{
			$parent = new stdClass();
			$parent->Parent = $this->ParentFolder;
			$parent->Folders = array();
			
			$folders[] = $parent;
		}
		
		if( $folders && $this->FolderName != '' )
		{
			end( $folders )->Folders['defined'] = $this->FolderName;
		}
		
		if( $this->FileType && is_array( $this->FileType ) )
		{
			$mimetype = $filetypes[$this->FileType[0]];
		}
		
		if( defined( 'BASE_DIR' ) && BASE_DIR && !file_exists( BASE_DIR . '/' . $this->Root ) )
		{
			mkdir( BASE_DIR . '/' . $this->Root, 0777, true );
			chmod( BASE_DIR . '/' . $this->Root, 0777 );
			
			$fp = fopen( $this->Root . 'test', 'w' );
			if( $fp ) fclose( $fp );
			
			if ( !file_exists( $this->Root . 'test' ) )
			{
				return false;
			}
			
			@unlink( $this->Root . 'test' );
		}
		
		if( $folders && is_array( $folders ) && file_exists( $this->Root ) && $this->Component && $this->Home )
		{
			$this->Folders = array();
			
			foreach( $folders as $obj )
			{
				$comp = trim( $this->Component );
				$home = trim( $this->Home );
				$prnt = trim( $obj->Parent );
				
				$i = 0;
				
				foreach( $obj->Folders as $key=>$folder )
				{
					$path = '';
					
					$pathfolders = array( $comp, $home, $prnt, trim( $folder ) );
					
					$this->Folders[$i] = new stdClass();
					$this->Folders[$i]->FolderID = false;
					$this->Folders[$i]->FolderType = ( is_numeric( $key ) ? 'other' : $key );
					$this->Folders[$i]->FolderName = $folder;
					$this->Folders[$i]->FolderPath = trim( strtolower( $this->Root . implode( '/', $pathfolders ) . '/' ) );
					$this->Folders[$i]->PathFolders = array();
					
					if( ( $mimetype && !$this->FolderName && $key == $mimetype ) || ( $this->FolderName && $key == 'defined' ) )
					{
						$this->FolderName = $this->Folders[$i]->FolderName;
						$this->FolderPath = $this->Folders[$i]->FolderPath;
					}
					
					foreach( $pathfolders as $k=>$pathfolder )
					{
						$path = trim( strtolower( $path . $pathfolder .'/' ) );
						
						$this->Folders[$i]->PathFolders[$k] = new stdClass();
						$this->Folders[$i]->PathFolders[$k]->ID = false;
						$this->Folders[$i]->PathFolders[$k]->Type = ( $pathfolder == $comp || $pathfolder == $home ? 'root' : 'media' );
						
						if( $this->Folders[$i]->PathFolders[$k]->Type == 'media' )
						{
							$this->Folders[$i]->PathFolders[$k]->MediaID = false;
						}
						
						$this->Folders[$i]->PathFolders[$k]->Name = $pathfolder;
						$this->Folders[$i]->PathFolders[$k]->Path = $this->Root . $path;
						
						if( !$this->ParentPath && ( strtolower( $pathfolder ) == strtolower( $this->ParentFolder ) || ( !$this->ParentFolder && strtolower( $pathfolder ) == 'library' ) ) )
						{
							$this->ParentFolder = $this->Folders[$i]->PathFolders[$k]->Name;
							$this->ParentPath = $this->Folders[$i]->PathFolders[$k]->Path;
						}
					}
					
					$i++;
				}
			}
			
			return true;
		}
		
		return false;
	}
	
	function SaveFileFolder ()
	{
		if( $this->Folders && is_array( $this->Folders ) && $this->FolderChmod )
		{
			foreach( $this->Folders as $folder )
			{
				if( $folder->PathFolders && is_array( $folder->PathFolders ) )
				{
					foreach( $folder->PathFolders as $pathfolder )
					{
						if( !$pathfolder->Name || !$pathfolder->Path || !$this->CreateFolder( $this->Root, $pathfolder->Path, $this->FolderChmod ) )
						{
							return false;
						}
					}
				}
			}
			
			return true;
		}
		
		return false;
	}
	
	function SaveDatabaseFolder ()
	{
		if( $this->Folders && is_array( $this->Folders ) )
		{
			// Get root folder
			$rfolder = new dbFolder();
			$rfolder = $rfolder->getRootFolder();
			
			foreach( $this->Folders as $folder )
			{
				if( $folder->PathFolders && is_array( $folder->PathFolders ) )
				{
					$fparent = '';
					
					foreach( $folder->PathFolders as $obj )
					{
						// Create / Save Folder
						$db = new dbFolder();
						$db->Name = $obj->Name;
						$db->Parent = ( $fparent > 0 ? $fparent : $rfolder->ID );
						$db->NodeID = 0;
						$db->NodeMainID = 0;
						if( !$db->Load() )
						{
							$db->UniqueID = $this->UniqueKey();
							$db->DiskPath = $obj->Path;
							$db->BasePath = $obj->Path;
							$db->UserID = $this->UserID;
							$db->CategoryID = $this->CategoryID;
							$db->Access = $this->FolderAccess;
							$db->DateCreated = date( 'Y-m-d H:i:s' );
							$db->DateModified = date( 'Y-m-d H:i:s' );
							$db->Save();
						}
						$fparent = $db->ID;
						// Set ParentID
						if( strtolower( $db->Name ) == strtolower( $this->ParentFolder ) )
						{
							$this->ParentID = $db->ID;
						}
						// Set FolderID
						if( strtolower( $db->Name ) == strtolower( $this->FolderName ) )
						{
							$this->FolderID = $db->ID;
						}
						// Assign ID to Folders array
						$obj->ID = $db->ID;
					}
					
					// Assign FolderID to Folders array
					$folder->FolderID = $fparent;
				}
			}
			
			return true;
		}
		
		return false;
	}
	
	function EditDatabaseFolder ( $_id )
	{
		// If Name is defined
		if( isset( $this->Name ) )
		{
			$this->FolderName = $this->Name;
		}
		
		if( $_id )
		{
			// Save edit to database Folder
			$db = new dbObject( 'SBookMediaRelation' );
			$db->MediaType = 'Folder';
			$db->MediaID = $this->FolderID = $_id;
			if( $db->Load() )
			{
				if( $this->FolderName )
				{
					$db->Title = $this->FolderName;
				}
				if( isset( $this->SortOrder ) )
				{
					$db->SortOrder = $this->SortOrder;
				}
				$db->Save();
				
				return true;
			}
		}
		
		return false;
	}
	
	function DeleteDatabaseFolder ( $_id )
	{
		global $database;
		
		if( $_id )
		{
			if( $db = $database->fetchObjectRows ( '
				SELECT 
					* 
				FROM 
					Folder 
				WHERE 
					ID = \'' . $_id . '\' 
					OR Parent = \'' . $_id . '\' 
				ORDER BY 
					ID ASC 
			', false, 'classes/library.class.php' ) )
			{
				foreach( $db as $folder )
				{
					// Delete files found in folder
					$fil = new dbObject( 'File' );
					$fil->FileFolder = $folder->ID;
					if( $fil = $fil->Find() )
					{
						foreach( $fil as $fi )
						{
							$f = new dbObject( 'File' );
							if( $f->Load( $fi->ID ) )
							{
								$this->DeleteDatabaseFile( $f->ID );
								
								$f->Delete();
							}
						}
					}
					
					// Delete images found in folder
					$img = new dbObject( 'Image' );
					$img->ImageFolder = $folder->ID;
					if( $img = $img->Find() )
					{
						foreach( $img as $im )
						{
							$i = new dbObject( 'Image' );
							if( $i->Load( $im->ID ) )
							{
								$this->DeleteDatabaseImage( $i->ID );
								
								$i->Delete();
							}
						}
					}
					
					// Delete folder
					$fld = new dbObject( 'Folder' );
					if( $fld->Load( $folder->ID ) )
					{
						$this->DeleteFolder( $folder->DiskPath, $folder->Name );
						
						// Delete relation
						$rel = new dbObject( 'SBookMediaRelation' );
						$rel->MediaID = $folder->ID;
						if( $rel->Load() )
						{
							$rel->Delete();
						}
						
						$fld->Delete();
					}
				}
				
				return true;
			}
		}
		
		return false;
	}
	
	function CreateNewFolder ()
	{
		if( $this->Name )
		{
			$this->FolderName = $this->Name;
		}
		
		$sc = $this->SetComponent();
		$fp = $this->GetFolderPath();
		
		if( $this->FolderName )
		{
			$this->FolderName = $this->UniqueFolder( $this->FolderName );
		}
		
		$sf = $this->SetFolders();
		$ff = $this->SaveFileFolder();
		$df = $this->SaveDatabaseFolder();
		$mr = $this->SaveMediaRelation();
		
		if( $sc && $fp && $sf && $ff && $df && $mr )
		{
			return true;
		}
		
		return false;
	}
	
	function GetFolders ()
	{
		$sc = $this->SetComponent();
		$fp = $this->GetFolderPath();
		$sf = $this->SetFolders();
		$ff = $this->SaveFileFolder();
		$df = $this->SaveDatabaseFolder();
		$mr = $this->SaveMediaRelation();
		
		if( $sc && $fp && $sf && $ff && $df && $mr )
		{
			return $this->Folders;
		}
		
		return false;
	}
	
	/* --- MediaRelation ----------------------------------------------------------------------------------------------------------------------- */
	
	function SaveMediaRelation ()
	{
		if( $this->Folders && is_array( $this->Folders ) )
		{
			foreach( $this->Folders as $folder )
			{
				if( $folder->PathFolders && is_array( $folder->PathFolders ) )
				{
					foreach( $folder->PathFolders as $obj )
					{
						if( $obj->Type != 'media' ) continue;
						
						// Create / Save Media Relation
						$db = new dbObject( 'SBookMediaRelation' );
						$db->MediaID = $obj->ID;
						$db->MediaType = $this->MediaType = 'Folder';
						if( !$db->Load() )
						{
							if( $this->CategoryID > 0 )
							{
								$db->CategoryID = $this->CategoryID;
							}
							else
							{
								$db->UserID = $this->UserID;
							}
							$db->Name = $obj->Name;
							$db->Title = $obj->Name;
							$db->SortOrder = $this->SortOrder;
						}
						$db->Save();
						
						// Set MediaID
						if( strtolower( $db->Name ) == strtolower( $this->FolderName ) )
						{
							$this->MediaID = $db->ID;
						}
						
						$obj->MediaID = $db->ID;
					}	
				}	
			}
			
			return true;
		}
		
		return false;
	}
	
	/* --- File -------------------------------------------------------------------------------------------------------------------------------- */
	
	function SetFileType ( $filename = false )
	{
		if( $filename && strstr( $filename, '.' ) && !strstr( $filename, '/' ) )
		{
			$this->FileType = explode( '/', $this->MimeType( $filename ) );
		}
		else if( $filename && strstr( $filename, '/' ) )
		{
			if( $ext = getimagesize( $filename ) )
			{
				$this->FileType = explode( '/', $ext['mime'] );
			}
		}
		
		if( !$this->FileType )
		{
			$this->FileType = array( 'application', 'octet-stream' );
		}
		
		$this->FileType[2] = $this->FileTypes( $filename );
		
		if( $filename && strstr( $filename, '.' ) && !strstr( $filename, '/' ) )
		{
			$parts = explode( '.', $filename );
			$this->FileType[3] = end( $parts );
		}
		
		return $this->FileType;
	}
	
	function UpdateDatabaseFiles ()
	{
		$sc = $this->SetComponent();
		$fp = $this->GetFolderPath();
		$sf = $this->SetFolders();
		$ff = $this->SaveFileFolder();
		$df = $this->SaveDatabaseFolder();
		$mr = $this->SaveMediaRelation();
		
		if( $sc && $fp && $sf && $ff && $df && $mr )
		{
			if( $this->ParentPath && $this->FolderPath && $this->FolderName )
			{
				if( $files = $this->OpenFolder( $this->ParentPath, strtolower( $this->FolderName ) ) )
				{
					foreach( $files as $file )
					{
						if( $this->SetFileType( $file->name ) )
						{
							$this->Filename = $file->name;
							$this->Filesize = $file->size;
							$this->FilePath = $this->FolderPath . $this->Filename;
							
							$this->SaveDatabaseFile( true );
						}
					}
					
					return true;
				}
			}
		}
		
		return false;
	}
	
	function SaveDatabaseFile ( $newonly = false )
	{	
		if( $this->Filename && $this->FileType && $this->FilePath && $this->FolderPath && $this->FolderID )
		{
			switch( $this->FileType[2] )
			{
				case 'image':
					// Save image data to db
					$db = new dbImage();
					$db->Filename = $this->SanitizeFilename( $this->Filename );
					$db->ImageFolder = $this->ImageFolder = $this->FolderID;
					if( !$db->Load() )
					{
						$db->UniqueID = $this->UniqueKey();
						$db->FilenameOriginal = $this->SanitizeFilename( $this->Filename );
						$db->DateCreated = date( 'Y-m-d H:i:s' );
					}
					else if( $newonly )
					{
						return false;
					}
					if( $imagesize = getimagesize( $this->FilePath ) )
					{
						$db->Width = $this->FileWidth = $imagesize[0];
						$db->Height = $this->FileHeight = $imagesize[1];
					}
					$db->FilePath = $this->FilePath;
					$db->Title = ( $this->Filetitle ? $this->Filetitle : $this->Filename );
					$db->Filesize = $this->Filesize;
					$db->Filetype = ( isset( $this->FileType[3] ) ? $this->FileType[3] : $this->FileType[1] );
					$db->UserID = $this->UserID;
					$db->CategoryID = $this->CategoryID;
					$db->Access = $this->FileAccess;
					$db->DateModified = date( 'Y-m-d H:i:s' );
					$db->UserID = $this->UserID;
					$db->Save();
					$this->MediaType = 'image';
					break;
					
				case 'video':
					// Video Converter
					$mediahandler = new MediaHandler();
					$rootpath = $this->FolderPath;
					$inputpath = $rootpath . '';
					$outputpath = $rootpath . '';
					$thumbpath = $rootpath . '';
					$parts = explode( '.', $this->Filename );
					//if ( in_array( end( explode( '.', $this->Filename ) ), array( 'avi', 'webm', 'ogg', 'mp4', 'swf' ) ) )
					if ( in_array( end( $parts ), array( 'webm', 'ogg', 'ogv', 'mp4', 'swf' ) ) )
					{
						$image_name = $mediahandler->grab_image( $this->Filename, $rootpath, $outputpath, $thumbpath, 1, 2, 'png', 400, 300 );
					}
					else if ( $outfile = $mediahandler->convert_media( $this->Filename, $rootpath, $inputpath, $outputpath, 400, 300, 32, 22050 ) )
					{
						$image_name = $mediahandler->grab_image( $outfile, $rootpath, $outputpath, $thumbpath, 1, 2, 'png', 400, 300 );
						$this->FileDuration = $mediahandler->get_duration( $outfile, $outputpath );
						$mediahandler->set_buffering( $outfile, $rootpath, $outputpath );
						
						$parts = explode( '.', $this->Filename );
						$this->Filename = $outfile;
						$this->FileType[3] = end( $parts );
					}
					else
					{
						$parts = explode( '.', $this->Filename );
						if ( file_exists( $f = ( $inputpath . $this->Filename ) ) )
						{
							unlink( $f );
						}
						if ( file_exists( $f = ( $outputpath . str_replace( '.' . end( $parts ), '', $this->Filename ) . '.mp4' ) ) )
						{
							unlink( $f );
						}
						// TODO: Install support for ffmpeg if doesn't exists on server
						die( 'Video not uploaded, ffmpeg not installed on server. ' . $mediahandler->Error );
						return false;
					}
					
					// Save video file data to db
					$db = new dbFile();
					$db->Filename = $this->Filename;
					$db->FileFolder = $this->FileFolder = $this->FolderID;
					if( !$db->Load() )
					{
						$db->UniqueID = $this->UniqueKey();
						$db->FilenameOriginal = $this->Filename;
						$db->DateCreated = date( 'Y-m-d H:i:s' );
					}
					else if( $newonly )
					{
						return false;
					}
					$db->FilePath = $this->FilePath;
					$db->Title = ( $this->Filetitle ? $this->Filetitle : $this->Filename );
					$db->Filesize = $this->Filesize;
					$db->Filetype = ( isset( $this->FileType[3] ) ? $this->FileType[3] : $this->FileType[1] );
					$db->UserID = $this->UserID;
					$db->CategoryID = $this->CategoryID;
					$db->Access = $this->FileAccess;
					$db->DateModified = date( 'Y-m-d H:i:s' );
					$db->UserID = $this->UserID;
					$db->Save();
					
					$this->MediaType = 'video';
					break;
					
				default:
					// Save file data to db
					$db = new dbFile();
					$db->Filename = $this->SanitizeFilename( $this->Filename );
					$db->FileFolder = $this->FileFolder = $this->FolderID;
					if( !$db->Load() )
					{
						$db->UniqueID = $this->UniqueKey();
						$db->FilenameOriginal = $this->SanitizeFilename( $this->Filename );
						$db->DateCreated = date( 'Y-m-d H:i:s' );
					}
					else if( $newonly )
					{
						return false;
					}
					$db->FilePath = $this->FilePath;
					$db->Title = ( $this->Filetitle ? $this->Filetitle : $this->Filename );
					$db->Filesize = $this->Filesize;
					$db->Filetype = ( isset( $this->FileType[3] ) ? $this->FileType[3] : $this->FileType[1] );
					$db->UserID = $this->UserID;
					$db->CategoryID = $this->CategoryID;
					$db->Access = $this->FileAccess;
					$db->DateModified = date( 'Y-m-d H:i:s' );
					$db->UserID = $this->UserID;
					$db->Save();
					$this->MediaType = 'file';
					break;
			}
			
			// Set FileID
			$this->FileID = $db->ID;
			
			return $db->ID;
		}
		return false;
	}
	
	function UploadFile ( $_file )
	{
		if( $_file && is_array( $_file ) )
		{
			$this->UploadFile = $_file;
			
			if( $this->SetFileType( $_file['name'] ) )
			{
				$sc = $this->SetComponent();
				$fp = $this->GetFolderPath();
				$sf = $this->SetFolders();
				$ff = $this->SaveFileFolder();
				$df = $this->SaveDatabaseFolder();
				$mr = $this->SaveMediaRelation();
				
				if( $sc && $fp && $sf && $ff && $df && $mr )
				{	
					if( $this->FolderPath && file_exists( $this->FolderPath ) && $this->FileChmod )
					{
						$this->Filetitle = $this->UniqueFile( $_file['name'], $this->FolderPath );
						$this->Filename = basename( $this->SanitizeFilename( $this->Filetitle ) );
						$this->Filesize = $_file['size'];
						$this->FilePath = $this->FolderPath . $this->Filename;
						
						if( !$_file['error'] && $this->Filename && move_uploaded_file( $_file['tmp_name'], $this->FilePath ) )
						{
							// Check image rotation
							list ( , , $type ) = getimagesize ( $this->FilePath );
							if( $type == IMAGETYPE_JPEG && function_exists( 'exif_read_data' ) )
							{
								$exif = exif_read_data( $this->FilePath );
								if( !empty( $exif['Orientation'] ) )
								{
									$image = imagecreatefromstring( file_get_contents( $this->FilePath ) );
									switch( $exif['Orientation'] )
									{
										case 8:
											$image = imagerotate( $image, 90, 0 );
											break;
										case 3:
											$image = imagerotate( $image, 180, 0 );
											break;
										case 6:
											$image = imagerotate( $image, -90, 0 );
											break;
									}
								}
								if( $image )
								{
									// Write rotated image
									imagejpeg( $image, $this->FilePath, 90 );
								}
							}
							
							$df = $this->SaveDatabaseFile();
							
							if( $df )
							{
								return $this->Filename;
							}
						}
					}
					
					$error = array(
						1=>'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
						'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
						'The uploaded file was only partially uploaded.',
						'No file was uploaded.',
						6=>'Missing a temporary folder.',
						'Failed to write file to disk.',
						'A PHP extension stopped the file upload.'
					);
					
					$this->FileError = ( isset( $error[$_file['error']] ) ? $error[$_file['error']] : $_file['error'] ) . ' ' . print_r( error_get_last(), 1 );
				}
			}
		}
		return false;
	}
	
	function UploadFileByUrl ( $_url )
	{
		if( $_url && is_string( $_url ) )
		{
			if( $this->SetFileType( $_url ) )
			{
				$sc = $this->SetComponent();
				$fp = $this->GetFolderPath();
				$sf = $this->SetFolders();
				$ff = $this->SaveFileFolder();
				$df = $this->SaveDatabaseFolder();
				$mr = $this->SaveMediaRelation();
				
				if( $sc && $fp && $sf && $ff && $df && $mr )
				{	
					if( file_exists( $this->FolderPath ) )
					{
						if( $image = $this->SaveRemoteImage( $_url, $this->FolderPath ) )
						{
							$this->FilePath = $this->FolderPath .'/'. basename( $image );
							
							// Save image data to db
							$db = new dbImage();
							$db->FilePath = $this->FilePath;
							$db->Filename = $image;
							$db->Title = $image;
							$db->ImageFolder = $this->ImageFolder = $this->FolderID;
							if( !$db->Load() )
							{
								$db->UniqueID = $this->UniqueKey();
								$db->FilenameOriginal = $image;
								$db->DateCreated = date( 'Y-m-d H:i:s' );
							}
							if( $imagesize = getimagesize( $this->FilePath ) )
							{
								$db->Width = $this->FileWidth = $imagesize[0];
								$db->Height = $this->FileHeight = $imagesize[1];
							}
							$db->Filesize = $this->Filesize;
							$db->Filetype = $this->FileType[1];
							$db->UserID = $this->UserID;
							$db->CategoryID = $this->CategoryID;
							$db->Access = $this->FileAccess;
							$db->DateModified = date( 'Y-m-d H:i:s' );
							$db->Save();
							
							// Set RemotePath
							$this->RemotePath = $_url;
							// Set FileID
							$this->FileID = $db->ID;
							// Set MediaType
							$this->MediaType = 'image';
							
							return $db->ID;
						}
					}
				}
			}
		}
		
		return false;
	}
	
	function SaveContentToFile ()
	{
		if( !$this->Filename )
		{
			$this->Filename = 'File_1';
		}
		
		if( $this->Filename )
		{
			if( !strstr( $this->Filename, '.' ) )
			{
				$this->Filename = explode( '_', $this->Filename );
				$this->Filename = $this->Filename[0] .'_'. date( 'Ymd' ) .'_'. $this->Filename[1] . '.txt';
			}
			
			if( $this->SetFileType( $this->Filename ) )
			{
				$sc = $this->SetComponent();
				$fp = $this->GetFolderPath();
				$sf = $this->SetFolders();
				$ff = $this->SaveFileFolder();
				$df = $this->SaveDatabaseFolder();
				$mr = $this->SaveMediaRelation();
				
				if( $sc && $fp && $sf && $ff && $df && $mr )
				{
					$this->Filename = $this->UniqueFile( $this->Filename );
					
					if( file_exists( $this->FolderPath ) && $media = $this->SaveToFile( $this->FolderPath, $this->Filename, $this->FileContent ) )
					{					
						$this->FilePath = $this->FolderPath .'/'. basename( $media );
						
						// Save file data to db
						$db = new dbFile();
						$db->FilePath = $this->FilePath;
						$db->Filename = $media;
						$db->Title = $media;
						$db->FileFolder = $this->FileFolder = $this->FolderID;
						if( !$db->Load() )
						{
							$db->UniqueID = ( $this->FileUniqueID ? $this->FileUniqueID : $this->UniqueKey() );
							$db->FilenameOriginal = $media;
							$db->DateCreated = date( 'Y-m-d H:i:s' );
						}
						$db->Filesize = $this->Filesize;
						$db->Filetype = $this->FileType[1];
						$db->UserID = $this->UserID;
						$db->CategoryID = $this->CategoryID;
						$db->Access = $this->FileAccess;
						$db->DateModified = date( 'Y-m-d H:i:s' );
						$db->Save();
						
						// Set FileID
						$this->FileID = $db->ID;
						// Set MediaType
						$this->MediaType = 'file';
						
						return $db->ID;
					}
				}
			}
		}
		
		return false;
	}
	
	function SaveParsedData ( $_data )
	{
		if( $_data && is_object( $_data ) )
		{
			$this->Data = $_data;
			
			if( $this->SetFileType() )
			{
				$sc = $this->SetComponent();
				$fp = $this->GetFolderPath();
				$sf = $this->SetFolders();
				$ff = $this->SaveFileFolder();
				$df = $this->SaveDatabaseFolder();
				$mr = $this->SaveMediaRelation();
				
				if( $sc && $fp && $sf && $ff && $df && $mr )
				{	
					if( file_exists( $this->FolderPath ) )
					{
						//$_data->ImageID = ( isset( $_data->ImageID ) ? $_data->ImageID : 0 );
						
						if( $_data->Images && isset( $_data->Images[0] ) && $image = $this->SaveRemoteImage( $_data->Images[$_data->ImageID]->src, $this->FolderPath, $_data->Title ) )
						{
							if( $_data->Type != 'picture' )
							{
								$file = $this->SaveRemoteDataToFile( $this->FolderPath, $image, $_data );
							}
							
							$this->FilePath = $this->FolderPath . basename( $image );
							
							// Save image data to db
							$db = new dbImage();
							$db->FilePath = $this->FilePath;
							$db->Filename = $image;
							$db->Title = $image;
							$db->ImageFolder = $this->ImageFolder = $this->FolderID;
							if( !$db->Load() )
							{
								$db->UniqueID = $this->UniqueKey();
								$db->FilenameOriginal = $image;
								$db->DateCreated = date( 'Y-m-d H:i:s' );
							}
							if( $imagesize = getimagesize( $this->FilePath ) )
							{
								$db->Width = $this->FileWidth = $imagesize[0];
								$db->Height = $this->FileHeight = $imagesize[1];
							}
							$db->Filesize = $this->Filesize;
							$db->Filetype = $this->FileType[1];
							$db->UserID = $this->UserID;
							$db->CategoryID = $this->CategoryID;
							$db->Access = $this->FileAccess;
							$db->DateModified = date( 'Y-m-d H:i:s' );
							$db->Save();
							
							// Set RemotePath
							$this->RemotePath = $_data->Images[$_data->ImageID]->src;
							// Set FileID
							$this->FileID = $db->ID;
							// Set MediaType
							$this->MediaType = 'image';
							
							return $db->ID;
						}
						else if( !$_data->Images && $_data->Title && $file = $this->SaveRemoteDataToFile( $this->FolderPath, str_replace( ' ', '_', $_data->Title ), $_data ) )
						{
							$this->FilePath = $this->FolderPath . basename( $file );
							
							// Save file data to db
							$db = new dbFile();
							$db->FilePath = $this->FilePath;
							$db->Filename = $file;
							$db->Title = $file;
							$db->FileFolder = $this->FileFolder = $this->FolderID;
							if( !$db->Load() )
							{
								$db->UniqueID = $this->UniqueKey();
								$db->FilenameOriginal = $file;
								$db->DateCreated = date( 'Y-m-d H:i:s' );
							}
							if( $this->Filesize )
							{
								$db->Filesize = $this->Filesize;
							}
							$parts = explode( '.', $file );
							$db->Filetype = end( $parts );
							$db->UserID = $this->UserID;
							$db->CategoryID = $this->CategoryID;
							$db->Access = $this->FileAccess;
							$db->DateModified = date( 'Y-m-d H:i:s' );
							$db->Save();
							
							// Set RemotePath
							$this->RemotePath = $_data->Url;
							// Set FileID
							$this->FileID = $db->ID;
							// Set MediaType
							$this->MediaType = 'file';
							
							return $db->ID;
						}
					}
				}
			}
		}
		
		return false;
	}
	
	function GetFileContent ()
	{
		global $database;
		
		// IF table is file and its loaded return file content
		if( strtolower( $this->Table ) == 'file' && $this->ID > 0 && $this->IsLoaded )
		{
			if( $file = $database->fetchObjectRow ( '
				SELECT 
					m.*, 
					f.Name AS Folder, 
					f.Parent, 
					f.DiskPath 
				FROM 
					File m, 
					Folder f 
				WHERE 
					m.ID = \'' . $this->ID . '\'
					AND f.ID = m.FileFolder 
				ORDER BY 
					m.ID DESC 
			', false, 'classes/library.class.php' ) )
			{
				if( $file->DiskPath != '' && $file->Filename )
				{
					$this->FilePath = $file->DiskPath;
					$this->Filename = $file->Filename;
					
					if( $content = $this->OpenFile( $file->DiskPath, $file->Filename ) )
					{
						return $content;
					}
				}
			}
		}
		
		return false;
	}
	
	// TODO FIX SortOrder organize of fiels in folder and folders in folder
	function EditDatabaseFile ( $_id )
	{
		if( $this->ParentFolder && $this->FolderName )
		{
			$folders = $this->GetFolders();
		}
		
		if( $_id )
		{
			// Save edit to database File
			$fil = new dbObject( 'File' );
			$fld = new dbObject( 'Folder' );
			if( $fil->Load( $_id ) && $fld->Load( $fil->FileFolder ) )
			{
				if( $fil->IsEdit > 0 )
				{
					$this->IsEdit = $fil->IsEdit;
				}
				
				$this->FolderID = $fld->ID;
				$this->FolderName = $fld->Name;
				$this->FileID = $fil->ID;
				
				// If Title is defined
				if( isset( $this->Title ) )
				{
					$this->Filename = $this->Title;
				}
				// If FileFolder is defined and is different from current
				if( isset( $this->FileFolder ) && $this->FileFolder > 0 && $this->FileFolder != $fil->FileFolder )
				{
					$des = new dbObject( 'Folder' );
					if( $des->Load( $this->FileFolder ) && ( $fol = $this->MoveFile( $fld->DiskPath, $fil->FilenameOriginal, $des->DiskPath ) ) )
					{
						$fil->FileFolder = $this->FileFolder;
						$fil->FilenameOriginal = $fol;
						
						$this->FolderID = $des->ID;
						$this->FolderName = $des->Name;
					}
				}
				// SortOrder
				if( $this->SortOrder )
				{
					$fil->SortOrder = $this->SortOrder;
				}
				// Save Filename
				if( $this->Filename )
				{
					$fil->Title = $this->Filename;
				}
				
				$fil->DateModified = date( 'Y-m-d H:i:s' );
				$fil->ModID = $this->UserID;
				$fil->Save();
				
				// Save FileContent
				if( isset( $this->FileContent ) && ( $fil->Filetype == 'txt' || $fil->Filetype == 'css' || $fil->Filetype == 'plain' ) )
				{
					$this->SaveToFile( $fld->DiskPath, $fil->Filename, $this->FileContent );
				}
				
				return true;
			}
		}
		
		return false;
	}
	
	function EditDatabaseImage ( $_id )
	{
		if( $this->ParentFolder && $this->FolderName )
		{
			$folders = $this->GetFolders();
		}
		
		if( $_id )
		{
			// Save edit to database Image
			$img = new dbObject( 'Image' );
			$fld = new dbObject( 'Folder' );
			if( $img->Load( $_id ) && $fld->Load( $img->ImageFolder ) )
			{
				$this->FolderID = $fld->ID;
				$this->FolderName = $fld->Name;
				$this->FileID = $img->ID;
				
				// If Title is defined
				if( isset( $this->Title ) )
				{
					$this->Filename = $this->Title;
				}
				
				// If FileFolder is defined and is different from current
				if( isset( $this->FileFolder ) && $this->FileFolder > 0 && $this->FileFolder != $img->ImageFolder )
				{
					$des = new dbObject( 'Folder' );
					if( $des->Load( $this->FileFolder ) && ( $fol = $this->MoveFile( $fld->DiskPath, $img->FilenameOriginal, $des->DiskPath ) ) )
					{
						$img->ImageFolder = $this->FileFolder;
						$img->FilenameOriginal = $fol;
						
						$this->FolderID = $des->ID;
						$this->FolderName = $des->Name;
					}
				}
				// SortOrder
				if( $this->SortOrder )
				{
					$img->SortOrder = $this->SortOrder;
				}
				// Save Filename
				if( $this->Filename )
				{
					$img->Title = $this->Filename;
				}
				
				$img->DateModified = date( 'Y-m-d H:i:s' );
				$img->ModID = $this->UserID;
				$img->Save();
				
				return true;
			}
		}
		
		return false;
	}
	
	function DeleteDatabaseFile ( $_id )
	{
		if( $_id )
		{
			$fil = new dbObject( 'File' );
			$fld = new dbObject( 'Folder' );
			if( $fil->Load( $_id ) && $fld->Load( $fil->FileFolder ) )
			{
				$this->FolderID = $fld->ID;
				$this->FolderName = $fld->Name;
				$this->FileID = $fil->ID;
				$this->Filename = $fil->Title;
				
				//$this->DeleteFile( $fld->DiskPath, $fil->Filename, ( in_array( $fil->Filetype, array( 'avi', 'ogv', 'mp4', 'swf' ) ) ? 'video' : false ) );
				$this->DeleteFile( $fld->DiskPath, $fil->Filename, ( in_array( $fil->Filetype, array( 'webm', 'ogg', 'mp4', 'swf' ) ) ? 'video' : false ) );
				
				$fil->Delete();
				
				return true;
			}
		}
		
		return false;
	}
	
	function DeleteDatabaseImage ( $_id )
	{
		if( $_id )
		{
			$img = new dbObject( 'Image' );
			$fld = new dbObject( 'Folder' );
			if( $img->Load( $_id ) && $fld->Load( $img->ImageFolder ) )
			{
				$this->FolderID = $fld->ID;
				$this->FolderName = $fld->Name;
				$this->FileID = $img->ID;
				$this->Filename = $img->Title;
				
				$this->DeleteFile( $fld->DiskPath, $img->Filename );
				
				$img->Delete();
				
				return true;
			}
		}
		
		return false;
	}
	
	/* --- Load -------------------------------------------------------------------------------------------------------------------------------- */
	
	function Load ( $_id )
	{
		global $database;
		
		if( $_id && $this->Table )
		{
			if( $db = $database->fetchObjectRow ( '
				SELECT
					*
				FROM
					' . $this->Table . '
				WHERE
					ID = \'' . $_id . '\'
				ORDER BY
					ID DESC
			', false, 'classes/library.class.php' ) )
			{
				$this->ID = $_id;
				$this->IsLoaded = 1;
				
				$obj = new stdClass();
				$obj->ID = $db->ID;
				if( isset( $db->ImageFolder ) )
				{
					$obj->FileFolder = $db->ImageFolder;
				}
				if( isset( $db->FileFolder ) )
				{
					$obj->FileFolder = $db->FileFolder;
				}
				if( isset( $db->DiskPath ) )
				{
					$obj->DiskPath = $db->DiskPath;
				}
				if( isset( $db->Filename ) )
				{
					$obj->Filename = $db->Filename;
				}
				if( isset( $db->Parent ) )
				{
					$obj->Parent = $db->Parent;
				}
				if( isset( $db->UserID ) )
				{
					$obj->UserID = $db->UserID;
				}
				if( isset( $db->CategoryID ) )
				{
					$obj->CategoryID = $db->CategoryID;
				}
				if( isset( $db->Access ) )
				{
					$obj->Access = $db->Access;
				}
				
				$this->Loaded = $obj;
				
				return true;
			}
		}
		
		return false;
	}
	
	/* --- Save -------------------------------------------------------------------------------------------------------------------------------- */
	
	function Save ()
	{
		$sf = '';
		
		if( $this->Table )
		{
			// IF table is loaded and request is to edit
			if( $this->ID > 0 && $this->IsLoaded )
			{	
				switch( strtolower( $this->Table ) )
				{
					case 'folder':
						$sf = $this->EditDatabaseFolder( $this->ID );
						break;
						
					case 'file':
						$sf = $this->EditDatabaseFile( $this->ID );
						break;
						
					case 'image':
						$sf = $this->EditDatabaseImage( $this->ID );
						break;
				}
			}
			// Else if request is to create new
			else
			{
				switch( strtolower( $this->Table ) )
				{
					case 'folder':
						$sf = $this->CreateNewFolder();
						break;
						
					case 'file':
						$sf = $this->SaveContentToFile();
						break;
				}
			}
			
			if( $sf )
			{
				return true;
			}
		}
		
		return false;
	}
	
	/* --- Delete ------------------------------------------------------------------------------------------------------------------------------ */
	
	function Delete ()
	{
		$df = '';
		
		if( $this->Table )
		{
			if( $this->ID > 0 && $this->IsLoaded )
			{
				switch( strtolower( $this->Table ) )
				{
					case 'folder':
						$df = $this->DeleteDatabaseFolder( $this->ID );
						break;
						
					case 'file':
						$df = $this->DeleteDatabaseFile( $this->ID );
						break;
						
					case 'image':
						$df = $this->DeleteDatabaseImage( $this->ID );
						break;
				}
				
				if( $df )
				{
					return true;
				}
			}
		}
		
		return false;
	}
	
	/* --- Helper functions -------------------------------------------------------------------------------------------------------------------- */
	
	function CurlUrlExists ( $url, $param = false, $limit = false )
	{
		$agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:33.0) Gecko/20100101 Firefox/33.0' );
		//$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
		
		// Only need the first 10 bytes
		//$headers = array( 'Range: bytes=0-10' );
		// Only need the first 6kb
		$headers = array( 'Range: bytes=0-60000' );
		
		$ok = false;
		
		if( !$url ) return false;
		
		$c = curl_init();
		
		curl_setopt( $c, CURLOPT_URL, $url );
		curl_setopt( $c, CURLOPT_FAILONERROR, 1 );
		
		if( $param )
		{
			curl_setopt( $c, CURLOPT_HTTPHEADER, $headers );
		}
		else
		{
			curl_setopt( $c, CURLOPT_NOBODY, 1 );
		}
		
		curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $c, CURLOPT_USERAGENT, $agent );
		
		if( method_exists( $this, 'curl_exec_follow' ) )
		{
			$r = $this->curl_exec_follow( $c );
		}
		else
		{
			$r = curl_exec( $c );
		}
		
		$ok = curl_getinfo( $c );
		
		//die( $r . ' -- ' . $url . ' -- ' . print_r( $ok,1 ) );
		
		if( $r !== false && $ok && ( $ok['http_code'] == 200 || $ok['http_code'] == 301 || $ok['http_code'] == 206 ) )
		{
			// Check if parameters match
			if( $param && strstr( $param, '=>' ) )
			{
				$param = explode( '=>', trim( $param ) );
				if( !strstr( $ok[ trim( $param[0] ) ], trim( $param[1] ) ) )
				{
					$ok = false;
				}
				else if( $ok['content_type'] && strstr( $ok['content_type'], 'image' ) )
				{
					if( $im = @imagecreatefromstring( $r ) )
					{
						$ok['image_width'] = imagesx( $im );
						$ok['image_height'] = imagesy( $im );
						$ok['image_type'] = ''; /* (channels) */
						$ok['image_bits'] = ''; /* (bits) */
						$ok['image_mime'] = $ok['content_type'];
						
						if( $limit && strstr( $limit, 'x' ) )
						{
							$limit = explode( 'x', trim( $limit ) );
							
							if( $limit && ( $limit[0] > $ok['image_width'] || $limit[1] > $ok['image_height'] ) )
							{
								$ok = false;
							}
						}
					}
					else
					{
						$ok = false;
					}
				}
				/*if( $ok )
				{
					die( print_r( $ok,1 ) . ' -- ' );
				}*/
			}
		}
		else
		{
			$ok = false;
		}
		curl_close( $c );
		return $ok;
	}
	
	function curl_exec_follow( $cu, &$maxredirect = null )
	{		
		$mr = 5;
		
		if ( ini_get( 'open_basedir' ) == '' && ini_get( 'safe_mode' == 'Off' ) )
		{
			curl_setopt( $cu, CURLOPT_FOLLOWLOCATION, $mr > 0 );
			curl_setopt( $cu, CURLOPT_MAXREDIRS, $mr );
		}
		else
		{
			curl_setopt( $cu, CURLOPT_FOLLOWLOCATION, false );
			
			if ( $mr > 0 )
			{
				$newurl = curl_getinfo( $cu, CURLINFO_EFFECTIVE_URL );
				$rch = curl_copy_handle( $cu );
				
				curl_setopt( $rch, CURLOPT_HEADER, true );
				curl_setopt( $rch, CURLOPT_NOBODY, true );
				curl_setopt( $rch, CURLOPT_FORBID_REUSE, false );
				curl_setopt( $rch, CURLOPT_RETURNTRANSFER, true );
				do
				{
					curl_setopt( $rch, CURLOPT_URL, $newurl );
					
					$header = curl_exec( $rch );
					
					if ( curl_errno( $rch ) ) 
					{
						$code = 0;
					}
					else
					{
						$code = curl_getinfo( $rch, CURLINFO_HTTP_CODE );
						
						if ( $code == 301 || $code == 302 || $code == 303 )
						{
							preg_match( '/Location:(.*?)\n/', $header, $matches );
							
							if ( !$matches )
							{
								preg_match( '/location:(.*?)\n/', $header, $matches );
							}
							
							$oldurl = $newurl;
							$newurl = trim( array_pop( $matches ) );
							
							if ( $newurl && !strstr( $newurl, 'http://' ) && !strstr( $newurl, 'https://' ) )
							{
								if ( strstr( $oldurl, 'https://' ) )
								{
									$parts = explode( '/', str_replace( 'https://', '', $oldurl ) );
									$newurl = ( 'https://' . reset( $parts ) . ( $newurl{0} != '/' ? '/' : '' ) . $newurl );
								}
								if ( strstr( $oldurl, 'http://' ) )
								{
									$parts = explode( '/', str_replace( 'http://', '', $oldurl ) );
									$newurl = ( 'http://' . reset( $parts ) . ( $newurl{0} != '/' ? '/' : '' ) . $newurl );
								}
								
							}
						}
						else
						{
							$code = 0;
						}
					}
				}
				while ( $code && --$mr );
				curl_close( $rch );
				if ( !$mr )
				{
					if ( $maxredirect === null )
					{
						return false;
					}
					else
					{
						$maxredirect = 0;
					}
					
					return false;
				}
				
				curl_setopt( $cu, CURLOPT_URL, $newurl );
			}
		}
		
		$cu = curl_exec( $cu );
		
		if( $cu )
		{
			return $cu;
		}
		
		return false;
	}
	
	function GetRemoteDataByUrl( $url )
	{
		if( !$url ) return;
		
		$url = str_replace( '&amp;', '&', $url );
		
		// TODO: Get useragent from current client useragent not from server.
		
		$agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:33.0) Gecko/20100101 Firefox/33.0' );
		//$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
		
		if( function_exists( 'curl_init' ) )
		{
			// Get data
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			//curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Accept-charset: UTF-8' ) );
			curl_setopt( $ch, CURLOPT_ENCODING, 'UTF-8' );
			//curl_setopt( $ch, CURLOPT_ENCODING, 1 );
			curl_setopt( $ch, CURLOPT_USERAGENT, $agent );
			
			//die( curl_exec( $ch ) . ' -- ' . print_r( curl_getinfo( $ch ),1 ) );
			
			// Check if we have a redirect and redirect if found
			if( method_exists( $this, 'curl_exec_follow' ) && ( $cu = $this->curl_exec_follow( $ch ) ) )
			{
				$inf = curl_getinfo( $ch );
				
				//die( $cu . ' -- ' . print_r( $inf,1 ) . ' .. ' . $url );
				
				// TODO: Why stripslashes?
				//$str = stripslashes ( curl_exec( $ch ) );
				//$cu = str_replace( '\\', '', stripslashes ( $cu ) );
				
				$str = $cu;
				//die( $str . ' --' );
				/*
				// FIXME: We need UTF-8 - do not convert to ISO-8859-1 (def enc)
				if( strstr( $str, '�' ) && $enc == 'UTF-8' )
				{
					$str = utf8_decode( $str );
				}
				else if( strstr( $str, '�' ) )
				{
					$str = utf8_encode( $str );
				}
				else
				{
					$str = $str;
				}
				
				*/
				
				/*// Convert to unicode
				if ( strtoupper( $encoding ) != 'UTF-8' )
				{
					$str = iconv( $encoding, 'UTF-8', $str );
				}
				else if ( $enc == 'UTF-8' )
				{
					$str = utf8_decode( $str );
				}*/
				
				//$utf8_1 = utf8_encode($str);
				//$utf8_2 = iconv('ISO-8859-1', 'UTF-8', $str);
				
				//$str = iconv( $encoding, 'UTF-8', $str );
				//$str = utf8_decode( $str );
				
				//$str = iconv('UTF-8', 'ISO-8859-1', $str);
				
				//die( $enc . ' -- ' . $str );
				
				// Return
				// TODO: add FixCharacters() here
				//curl_close( $ch );
				
				/*// Detect encoding
				$encoding = mb_detect_encoding ( $str );
				if ( !$encoding ) $encoding = 'UTF-8';
				
				// Make sure we have UTF-8
				if( strtoupper( $encoding ) != 'UTF-8' )
				{
					$str = mb_convert_encoding( $str, 'UTF-8', $encoding );
				}*/
				
				// If not UTF-8 force UTF-8
				if( @iconv( 'utf-8', 'utf-8//IGNORE', $str ) != $str )
				{
					$str = utf8_encode( $str );
				}
				
				//die( $str . ' --' );
				
				return $str;
			}
		}
		return false;
	}
	
	function correct_encoding( $str, $param = false )
	{
		/*if( !$str ) return;
		$str = mb_convert_encoding( $str, "UTF-8", "auto" );
		if( $param )
		{
			$str = utf8_decode( $str );
		}*/
		
		$found = false;
		
		$utf8 = array ( 'Ã¸', 'Ã¥', 'Ã¦', 'â' );
		
		foreach( $utf8 as $match )
		{
			if( strstr( $str, $match ) )
			{
				$found = true;
			}
		}
		
		if( $found )
		{
			$enc = mb_detect_encoding( $str, 'auto' );
			if( $enc == 'UTF-8' )
			{
				$str = utf8_decode( $str );
			}
			/*else if( $enc = iconv_get_encoding ( $str ) )
			{
				$str = iconv ( $enc, 'UTF-8', $str );
			}*/
		}
		//die( $enc . ' -- ' . ' .. ' . $found );
		return $str;
	}
	
	function is_base64_encoded( $data )
	{
		if( !$data ) return;
		
		$regex = '/[a-zA-Z0-9]*\={0,2}/i';
		//$regex = '%^[a-zA-Z0-9/+]*={0,2}$%';
		//$regex = '^([A-Za-z0-9+/]{4})*([A-Za-z0-9+/]{4}|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{2}==)$';
		
		if ( preg_match( $regex, $data ) )
		{
		    return true;
		}
		else
		{
		    return false;
		}
	}
	
	function getPNGImageXY( $data )
	{
		//The identity for a PNG is 8Bytes (64bits)long
		$ident = unpack( 'Nupper/Nlower', $data );
		//Make sure we get PNG
		if( $ident['upper'] !== 0x89504E47 || $ident['lower'] !== 0x0D0A1A0A )
		{
			return false;
		}
		//Get rid of the first 8 bytes that we processed
		$data = substr( $data, 8 );
		//Grab the first chunk tag, should be IHDR
		$chunk = unpack( 'Nlength/Ntype', $data );
		//IHDR must come first, if not we return false
		if( $chunk['type'] === 0x49484452 )
		{
			//Get rid of the 8 bytes we just processed
			$data = substr( $data, 8 );
			//Grab our x and y
			$info = unpack( 'NX/NY', $data );
			//Return in common format
			return array( $info['X'], $info['Y'] );
		}
		else
		{
			return false;
		}
	}
	
	function getGIFImageXY( $data )
	{
		//The identity for a GIF is 6bytes (48Bits)long
		$ident = unpack( 'nupper/nmiddle/nlower', $data );
		//Make sure we get GIF 87a or 89a
		if( $ident['upper'] !== 0x4749 || $ident['middle'] !== 0x4638 || ( $ident['lower'] !== 0x3761 && $ident['lower'] !== 0x3961 ) )
		{
			return false;
		}
		//Get rid of the first 6 bytes that we processed
		$data = substr( $data, 6 );
		//Grab our x and y, GIF is little endian for width and length
		$info = unpack( 'vX/vY', $data );
		//Return in common format
		return array( $info['X'], $info['Y'] );
	}
	
	function FindUrls( $str )
	{
		$str = strip_tags( trim( $str ) );
		
		$str = preg_replace( '/^\s+|\n|\r|\s+$/m', ' ', $str );
		$str = preg_replace( '/\s*$^\s*/m', ' ', $str );
		$str = preg_replace( '/[ \t]+/', ' ', $str );
		
		$out = array();
		
		$regex = array( '/https?\:\/\/[^\" ]+/i', '/http?\:\/\/[^\" ]+/i' );
		
		foreach( $regex as $rgx )
		{
			preg_match_all( $rgx, $str, $matches );
			
			foreach( $matches[0] as $m )
			{
				if( !in_array( $m, $out ) )
				{
					$out[] = $m;
				}
			}
		}
		
		return array_reverse( $out );
	}
	
	function ParseUrl( $str )
	{
		if( !$str ) return false;
		
		$obj = false;
		
		$data = $str;
		
		$str = $this->FindUrls( $str );
		
		$url = $str[0];
		$prt = explode( '.', $url );
		$ext = end( $prt );
		
		$url = trim( strip_tags( $url ) );
		
		$url = str_replace( '&amp;', '&', $url );
		
		$str = strtolower( $url );
		
		// Find Content Type by Url ----------------------------------------------------------------------------------------
		
		if( $ext && in_array( $ext, $this->VideoFormats() ) )
		{
			$type = 'video';
		}
		else if( $ext && in_array( $ext, $this->AudioFormats() ) )
		{
			$type = 'audio';
		}
		else if( $ext && in_array( $ext, $this->FileFormats() ) )
		{
			$type = 'file';
		}
		else if( strstr( $str, 'youtube.com/watch?v=' ) || strstr( $str, 'youtu.be/' ) )
		{
			$type = 'youtube';
		}
		else if( strstr( $str, 'vimeo.com/' ) )
		{
			$type = 'vimeo';
		}
		else if( ( strstr( $str, 'livestream.com/' ) || strstr( $str, 'livestre.am/' ) ) )
		{
			$type = 'livestream';
		}
		else if( strstr( $data, 'spotify:track:' ) )
		{
			$type = 'spotify';
		}
		else if( strstr( $data, BASE_URL ) && strstr( $data, '/library/' ) )
		{
			$type = 'library';
		}
		else if( strstr( $str, 'http' ) && ( strstr( $str, '.jpg' ) || strstr( $str, '.png' ) || strstr( $str, '.gif' ) || strstr( $str, '.jpeg' ) ) 
		)
		{
			$type = 'image';
		}
		else if( strstr( $str, 'http' ) )
		{
			$type = 'site';
		}
		
		// Get data by type -----------------------------------------------------------------------------------------------
		
		switch( $type )
		{
			// Youtube ----------------------------------------------------------------------------------------------------
			case 'youtube':
				if( $url )
				{
					// New method
					if( $content = $this->GetRemoteDataByUrl( 'https://www.youtube.com/oembed?url=' . $url . '&format=json' ) )
					{
						$json = json_decode( trim( $content ) );
						
						if( $json && is_object( $json ) )
						{
							$domain = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', trim( $json->provider_url ) ) ) );
							
							$obj = new stdClass();
							$obj->Type = 'video';
							$obj->Media = 'youtube';
							$obj->Url = $url;
							$obj->Title = $json->title;
							$obj->Description = '';
							$obj->Domain = $domain[0];
							
							$obj->Images = array();
							$obj->Images[0] = new stdClass();
							$obj->Images[0]->src = $json->thumbnail_url;
							
							break;
						}
					}
					// Old method
					if( $content = $this->GetRemoteDataByUrl( $url ) )
					{
						$obj = $this->FilterRemoteContent( $content, $url );
					
						if( $obj && is_object( $obj ) )
						{
							$obj->Type = 'video';
							$obj->Media = 'youtube';
							$obj->Title = $obj->Meta['title'];
							$obj->Description = $obj->Meta['description'];
						
							if( isset( $obj->Images ) && is_array( $obj->Images ) )
							{
								$obj->Images[0]->src = $obj->Meta['og:image'];
							}
							
							break;
						}
					}
				}
				break;
			// Vimeo ------------------------------------------------------------------------------------------------------
			case 'vimeo':
				if( $content = $this->GetRemoteDataByUrl( $url ) )
				{
					$obj = $this->FilterRemoteContent( $content, $url );
					
					if( $obj && is_object( $obj ) )
					{
						$obj->Type = 'video';
						$obj->Media = 'vimeo';
						$obj->Title = $obj->Meta['og:title'];
						$obj->Description = $obj->Meta['og:description'];
						
						if( isset( $obj->Images ) && is_array( $obj->Images ) )
						{
							if( isset( $obj->Meta['thumbnailUrl'] ) )
							{
								$obj->Images[0]->src = $obj->Meta['thumbnailUrl'];
							}
						}
					}
				}
				break;
			// Livestream -------------------------------------------------------------------------------------------------
			case 'livestream':
				if( $content = $this->GetRemoteDataByUrl( $url ) )
				{
					$obj = $this->FilterRemoteContent( $content, $url );
					
					if( $obj && is_object( $obj ) )
					{
						$domain = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', trim( $obj->Meta['og:url'] ) ) ) );
						$obj->Type = 'video';
						$obj->Media = 'livestream';
						$obj->Url = $obj->Meta['og:url'];
						$obj->Title = $obj->Meta['og:title'];
						$obj->Description = $obj->Meta['og:description'];
						$obj->Domain = $domain[0];
						
						if( isset( $obj->Images ) && is_array( $obj->Images ) )
						{
							$obj->Images[0]->src = $obj->Meta['og:image'];
						}
						
						// TODO: Update to the newest version ...
						//die( print_r( $obj,1 ) . ' Url: ' . $url );
					}
				}
				break;
			// Spotify ----------------------------------------------------------------------------------------------------
			case 'spotify':
				if ( $data )
				{
					$spotid = explode( 'spotify:track:', $data );
					$spotid = ( isset( $spotid[1] ) ? explode( ' ', $spotid[1] ) : false );
					$spotid = ( isset( $spotid[0] ) ? trim( $spotid[0] ) : false );
					
					if( $spotid && ( $content = $this->GetRemoteDataByUrl( 'https://api.spotify.com/v1/tracks/' . $spotid ) ) )
					{
						$json = json_decode( trim( $content ) );
						
						if( $json && is_object( $json ) )
						{
							if ( isset( $json->external_urls->spotify ) && isset( $json->preview_url ) && isset( $json->artists[0]->name ) && isset( $json->name ) )
							{
								$domain = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', trim( $json->external_urls->spotify ) ) ) );
								$obj = new stdClass();
								$obj->Type = 'audio';
								$obj->Media = 'spotify';
								$obj->Url = $json->preview_url;
								$obj->Title = $json->artists[0]->name;
								$obj->Description = $json->name;
								$obj->Domain = $domain[0];
								$obj->Images = array();
								if ( isset( $json->album->images[0] ) )
								{
									$obj->Images[0] = new stdClass();
									$obj->Images[0]->src = $json->album->images[0]->url;
									$obj->Images[0]->width = $json->album->images[0]->width;
									$obj->Images[0]->height = $json->album->images[0]->height;
								}
							}
						}
					}
				}
				break;
			// Remote image -----------------------------------------------------------------------------------------------
			case 'image':
				if( $this->CurlUrlExists( trim( $url ), 'content_type=>image' ) )
				{
					$domain = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', trim( $url ) ) ) );
					$imagesize = getimagesize( $url );
					$obj = new stdClass();
					$obj->Type = 'picture';
					$obj->Media = 'image';
					$obj->Url = $url;
					$obj->Title = $url;
					$obj->Domain = $domain[0];
					$obj->Images = array();
					$obj->Images[0] = new stdClass();
					$obj->Images[0]->src = $url;
					$obj->Images[0]->width = $imagesize[0];
					$obj->Images[0]->height = $imagesize[1];
				}
				break;
			// Remote video ------------------------------------------------------------------------------------------------
			case 'video':
				$domain = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', trim( $url ) ) ) );
				//$imagesize = getimagesize( $url );
				$parts = explode( '/', trim( $url ) );
				$obj = new stdClass();
				$obj->Type = 'video';
				$obj->Media = 'video';
				$obj->Url = $url;
				$obj->Title = end( $parts );
				$obj->Domain = $domain[0];
				//$obj->Images[0]->src = $url;
				//$obj->Images[0]->width = $imagesize[0];
				//$obj->Images[0]->height = $imagesize[1];
				break;
			// Remote audio ------------------------------------------------------------------------------------------------
			case 'audio':
				$domain = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', trim( $url ) ) ) );
				//$imagesize = getimagesize( $url );
				$parts = explode( '/', trim( $url ) );
				$obj = new stdClass();
				$obj->Type = 'audio';
				$obj->Media = 'audio';
				$obj->Url = $url;
				$obj->Title = end( $parts );
				$obj->Domain = $domain[0];
				//$obj->Images[0]->src = $url;
				//$obj->Images[0]->width = $imagesize[0];
				//$obj->Images[0]->height = $imagesize[1];
				break;
			// Remote file ------------------------------------------------------------------------------------------------
			case 'file':
				$domain = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', trim( $url ) ) ) );
				//$imagesize = getimagesize( $url );
				$parts = explode( '/', trim( $url ) );
				$obj = new stdClass();
				$obj->Type = 'file';
				$obj->Media = 'file';
				$obj->Url = $url;
				$obj->Title = end( $parts );
				$obj->Domain = $domain[0];
				//$obj->Images[0]->src = $url;
				//$obj->Images[0]->width = $imagesize[0];
				//$obj->Images[0]->height = $imagesize[1];
				break;
			// Remote site data -------------------------------------------------------------------------------------------
			case 'site':
				if( $content = $this->GetRemoteDataByUrl( $url ) )
				{
					$obj = $this->FilterRemoteContent( $content, $url );
					
					if( $obj && is_object( $obj ) )
					{
						$obj->Type = 'site';
						$obj->Media = 'site';
					}
				}
				break;
			// Library data -------------------------------------------------------------------------------------------
			case 'library':
				$domain = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', trim( $url ) ) ) );
				$parts = explode( '/', trim( $url ) );
				$obj = new stdClass();
				$obj->Type = 'library';
				$obj->Media = 'library';
				$obj->Url = $url;
				$obj->Title = end( $parts );
				$obj->Domain = $domain[0];
				break;
			// Else return false ------------------------------------------------------------------------------------------
			default:
				$obj = false;
				break;
		}
		
		// Output ---------------------------------------------------------------------------------------------------------
		
		if( $obj )
		{
			return $obj;
		}
		
		return false;
	}
	
	function FilterRemoteContent( $str, $url = false )
	{
		if( !$str ) return;
		
		// Parse the document -----------------------------------------------------------
		/*$doc = new DOMDocument();
		@$doc->loadHTML( $str );*/
		$doc = new SimpleHTML( $str );
		
		//die( print_r( $doc,1 ) . ' --' );
		//die( print_r( $doc->getElementsByTagName( 'video' ),1 ) . ' --' );
		
		if( $doc )
		{
			$data = new stdClass();
			
			if( $url )
			{
				$data->Url = trim( strip_tags( $url ) );
				$url = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', $data->Url ) ) );
				$data->Domain = $url[0];
			}
			
			// Meta ----------------------------------------------------------------
			$content = ''; $key = ''; $data->Meta = array();
			
			foreach( $doc->getElementsByTagName( 'meta' ) as $meta )
			{
				foreach( array( 'name', 'property', 'itemprop' ) as $k )
				{
					$key = trim( $meta->tagAttributes[$k] );
					$content = trim( $meta->tagAttributes['content'] );
					$content = stripslashes( $content );
					//$content = mysql_real_escape_string( $content );
					$content = stripslashes( $content );
					if( /*$content != '' && $content != '0' && */$key != '' )
					{
						$data->Meta[$key] = $content;
					}
				}
			}
			
			// Link ----------------------------------------------------------------
			
			$content = ''; $key = ''; $data->Link = array();
			
			foreach( $doc->getElementsByTagName( 'link' ) as $link )
			{
				foreach( array( 'rel' ) as $k )
				{
					$key = trim( $link->tagAttributes[$k] );
					$content = trim( $link->tagAttributes['href'] );
					$content = stripslashes( $content );
					//$content = mysql_real_escape_string( $content );
					$content = stripslashes( $content );
					if( /*$content != '' && $content != '0' && */$key != '' )
					{
						if( isset( $data->Link[$key] ) )
						{
							$arr = ( is_array( $data->Link[$key] ) ? $data->Link[$key] : array( $data->Link[$key] ) );
							$arr[] = $content;
							$data->Link[$key] = $arr;
						}
						else
						{
							$data->Link[$key] = $content;
						}
					}
				}
			}
			
			// Title ---------------------------------------------------------------
			$value = '';
			/*foreach( $doc->getElementsByTagName( 'title' ) as $title )
			{
				$value = trim( $title->nodeValue );
				if( ( $tite = $data->Meta['og:title'] ) )
				{
					$value = $tite;
				}
				$value = mysql_real_escape_string( $value );
				if( $value != '' && $value != '0' )
				{
					//$data->Title = htmlentities( stripslashes( $value ) );
					//$data->Title = utf8_encode( stripslashes( $value ) );
					$data->Title = stripslashes( $value );
				}
			}*/
			
			if( ( $tite = $data->Meta['og:title'] ) || ( $tite = $data->Meta['title'] ) )
			{
				$value = $tite;
			}
			else
			{
				foreach( $doc->getElementsByTagName( 'title' ) as $title )
				{
					if( !$value && $title->tagName && $title->nodeid )
					{
						$value = trim( strip_tags( $doc->innerHTML( $title->tagName, $title->nodeid ) ) );
					}
				}
			}
			
			if( $value != '' && $value != '0' )
			{
				$value = stripslashes( $value );
				//$value = mysql_real_escape_string( $value );
				$value = stripslashes( $value );
				$data->Title = $value;
			}
			
			// Images --------------------------------------------------------------
			$src = ''; $data->Images = array(); $i = 0; $srcs = array();
			
			foreach( array( 'og:image', 'image', 'image_url' ) as $imn )
			{
				if( isset( $data->Meta[$imn] ) && ( $im = $data->Meta[$imn] ) )
				{
					
					$im = ( substr( $im, 0, 4 ) == 'http' ? $im : ( 'http://' . $data->Domain . ( substr( $im, -1 ) != '/' ? '/' : '' ) . $im ) );
					$im = str_replace( ' ', '%20', $im );
					
					$srcs[] = $src = trim( $im );			
					if( $src != '' && ( $curl = $this->CurlUrlExists( $src, 'content_type=>image', '154x154' ) ) )
					{
						$data->Images[$i] = new stdClass();
						$data->Images[$i]->src = ( $curl['url'] ? $curl['url'] : $src );
						
						$data->Images[$i]->width = $curl['image_width'];
						$data->Images[$i]->height = $curl['image_height'];
						$data->Images[$i]->type = $curl['image_type'];
						$data->Images[$i]->bits = $curl['image_bits'];
						$data->Images[$i]->mime = $curl['image_mime'];
						
						$i++;
					}
				}
			}
			
			// TODO: make support for images in div via computet style and css files
			
			$imgs = 0;
			foreach( $doc->getElementsByTagName( 'img' ) as $img )
			{
				$im = trim( $img->tagAttributes[ 'src' ] );
				
				if( trim( $im ) )
				{
					if( $i >= 4 || $imgs > 10 ) break;
					
					$im = ( substr( $im, 0, 4 ) == 'http' ? $im : ( 'http://' . $data->Domain . ( substr( $im, -1 ) != '/' ? '/' : '' ) . $im ) );
					$im = str_replace( ' ', '%20', $im );
					
					$srcs[] = $src = $im;			
					if( $src != '' && ( $curl = $this->CurlUrlExists( $src, 'content_type=>image', '154x154' ) ) )
					{
						$data->Images[$i] = new stdClass();
						$data->Images[$i]->src = ( $curl['url'] ? $curl['url'] : $src );
						
						$data->Images[$i]->width = $curl['image_width'];
						$data->Images[$i]->height = $curl['image_height'];
						$data->Images[$i]->type = $curl['image_type'];
						$data->Images[$i]->bits = $curl['image_bits'];
						$data->Images[$i]->mime = $curl['image_mime'];
						
						$i++;
					}
					$imgs++;
				}
			}
			
			// Leadin -------------------------------------------------------------
			$content = ''; $cstr = ''; $data->Leadin = '';
			
			if( ( $desc = $data->Meta['og:description'] ) || ( $desc = $data->Meta['description'] ) )
			{
				$desc = stripslashes( $desc );
				//$desc = mysql_real_escape_string( $desc );
				$desc = stripslashes( $desc );
				$data->Leadin = $desc;
			}
			else
			{
				foreach( $doc->getElementsByTagName( 'p' ) as $body )
				{
					$content = trim( $doc->innerHTML( $body->tagName, $body->nodeid ) );
					$content = preg_replace( '/^\s+|\n|\r|\s+$/m', ' ', $content );
					$content = preg_replace( '/\s*$^\s*/m', ' ', $content );
					$content = preg_replace( '/[ \t]+/', ' ', $content );
					$content = strip_tags( $content );
					$content = stripslashes( $content );
					//$content = mysql_real_escape_string( $content );
					$content = stripslashes( $content );
					if( strlen( $content ) > 100 && strlen( $cstr ) < 700 )
					{
						$cstr = ( $cstr ? ( $cstr . ' ' . $content ) : $content );
					}
				}
			}
			if( $cstr )
			{
				$data->Leadin = $cstr;
				
			}
			
			//die( print_r( $data,1 ) . ' .. ' . print_r( $srcs,1 ) . ' -- ' . print_r( $doc->getElementsByTagName( 'img' ),1 ) );
			
			if( $data->Title )
			{
				return $data;
			}
			return false;
		}
		
		return false;
	}
	
	function SaveRemoteDataToFile( $path, $file, $obj )
	{
		if( !$path || !$file || !$obj ) return false;
		
		if( substr( $path, -1 ) != '/' )
		{
			$path = $path . '/';
		}
		
		if( !file_exists( $path ) )
		{
			return false;
		}
		
		$file = explode( '.', $file );
		//$file = $file[0] . '.parse';
		$file = $file[0];
		//$file = str_replace( array( '.', ' ' ), array( '_', '_' ), $file );
		//$file = $this->StrTrim( $file, 50, false, false );
		$file = $file . '.meta';
		
		$file = $this->SanitizeFilename( $file );
		$file = $this->UniqueFile( $file, $path );
		
		$filepath = ( $path . $file );
		
		// Create file ------------------------------------------------------------------
		
		if( $obj )
		{
			$fp = fopen( $filepath, 'wb' );
			if( fwrite( $fp, json_encode( $obj ) ) )
			{			
				fclose( $fp );
				return $file;
			}
		}
		
		return false;
	}
	
	function SaveRemoteImage( $url, $path, $title = false )
	{
		if( !$url || !$path ) return false;
		if( substr( $path, -1 ) != '/' )
		{
			$path = $path . '/';
		}
		$file = '';
		$parts = explode( '/', $url );
		if( $parts )
		{
			foreach( $parts as $pt )
			{
				if( $pt != '' )
				{
					$file = trim( $pt );
				}
			}
		}
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_BINARYTRANSFER,1 );
		$info = curl_getinfo( $ch );
		$raw = curl_exec( $ch );
		curl_close( $ch );
		if( $file && $imagesize = getimagesize( $url ) )
		{
			$parts = explode( '/', $imagesize['mime'] );
			$file = explode( '.', $file );
			$file = $this->SanitizeFilename( $file[0] );
			$file = $file . '.' . end( $parts );
			$file = $this->Filename = $this->UniqueFile( $file, $path, $title );
			$this->FileType = explode( '/', $imagesize['mime'] );
			$this->Filesize = $imagesize['bits'];
		}
		if( !file_exists( $path . $file ) && $file && $raw )
		{
			$fp = fopen( $path . $file, 'x' );
			if( fwrite( $fp, $raw ) )
			{
				fclose( $fp );
				return $file;
			}
		}
		return false;
	}
	
	function SaveToFile( $path, $file, $content = false )
	{
		if( $path && $file )
		{
			if( substr( $path, -1 ) != '/' )
			{
				$path = $path . '/';
			}
			
			// Get filedata
			if( $content && $this->FileEncoding == 'base64' )
			{
				$content = base64_decode( trim( $content ), true );
			}
			
			if( !$content )
			{
				$content = ' ';
			}
			
			$filepath = ( $path . $file );
			
			// Check file/folder rights
			if ( $error = $this->CheckWriteRights( $filepath, 'wb' ) )
			{
				die( $error );
			}
			
			$fp = fopen( $filepath, 'wb' );
			if( fwrite( $fp, $content ) )
			{
				if( $stats = stat( $filepath ) )
				{
					$parts = explode( '.', $file );
					$this->Filesize = $stats['size'];
					$this->FileType = array( 'file', end( $parts ) );
				}
				
				fclose( $fp );
				return $file;
			}
		}
		return false;
	}
	
	function OpenFile( $path, $file = '' )
	{
		if( $path )
		{
			if( $file )
			{
				if( substr( $path, -1 ) != '/' )
				{
					$path = $path . '/';
				}
			}
			
			$filepath = ( $path . $file );
			
			if( file_exists( $filepath ) )
			{
				if( $stats = stat( $filepath ) )
				{
					$parts = explode( '.', $filepath );
					$this->Filesize = $stats['size'];
					$this->FileType = array( 'file', end( $parts ) );
				}
				
				if( $content = file_get_contents( $filepath, true ) )
				{
					return $content;
				}
			}
		}
		return false;
	}
	
	function MoveFile( $path, $file, $dest )
	{
		if( $path && $file && $dest )
		{
			if( substr( $path, -1 ) != '/' )
			{
				$path = $path . '/';
			}
			
			if( substr( $dest, -1 ) != '/' )
			{
				$dest = $dest . '/';
			}
			
			$newfile = $file;
			
			if( file_exists( $dest . $file ) )
			{
				$newfile = $this->Filename = $this->UniqueFile( $file, $dest );
			}
			
			if( file_exists( $path . $oldfile ) )
			{
				rename( $path . $file, $dest . $newfile );
				
				return $newfile;
			}
		}
		return false;
	}
	
	function DeleteFile( $path, $file, $type = false )
	{
		$done = false;
		
		if( !$path || !$file ) return;
		
		if( substr( $path, -1 ) != '/' )
		{
			$path = $path . '/';
		}
		
		$ext = explode( '.', $file );
		
		if( file_exists ( $path . $file ) && unlink( $path . $file ) )
		{
			$done = true;
		}
		if( file_exists ( $path . $ext[0] . '.parse' ) && unlink( $path . $ext[0] . '.parse' ) )
		{
			$done = true;
		}
		if( $type == 'video' && file_exists ( $path . $ext[0] . '.png' ) && unlink( $path . $ext[0] . '.png' ) )
		{
			$done = true;
		}
		
		if( $done )
		{
			return true;
		}
		
		return false;
	}
	
	function CopyFile( $src, $dst, $chmod = false )
	{
		if( !$src || !$dst ) return false;
		
		// Check if parent folders exist if not create them
		if( !file_exists( dirname( $dst ) ) )
		{
			//@mkdir( dirname( $dst ), ( $chmod ? $chmod : 0777 ), true );
			@mkdir( dirname( $dst ), 0777, true );
			@chmod( dirname( $dst ), 0777 );
		}
		
		// Check file/folder rights
		if( $error = $this->CheckWriteRights( $dst ) )
		{
			die( $error );
		}
		
		if( $this->CurlUrlExists( $src ) && ( $cnt = @file_get_contents( $src ) ) )
		{
			if( $fp = fopen( $dst, 'w+' ) )
			{
				fwrite ( $fp, $cnt );
				fclose ( $fp );
				
				return true;
			}
		}
		
		return false;
	}
	
	function CheckWriteRights( $dst, $mode = false, $rights = false )
	{
		if ( !$dst ) return false;
		
		// Check if file or folder exists
		if ( !file_exists( $dst ) )
		{
			$parent = dirname( $dst );
			
			if ( $parent && file_exists( $parent ) )
			{
				if ( !( $fp = fopen ( "$parent/_test_", 'w' ) ) )
				{
					return 'The "'.$parent.'" folder is not writable. Please change the permissions on it to "' . ( $rights ? $rights : '777' ) . '".';
				}
				if ( file_exists( "$parent/_test_" ) )
				{
					@unlink( "$parent/_test_" );
				}
				if ( $fp ) fclose ( $fp );
				
				// Everything ok return false
				return false;
			}
			else
			{
				return 'No "'.$parent.'" directory exists!';
			}
		}
		// Check if file is writable
		else if ( is_file( $dst ) )
		{
			if ( !( $fp = fopen ( $dst, ( $mode ? $mode : 'w' ) ) ) )
			{
				return 'Can not open '.$dst.' for writing! Please change the permissions on it to "' . ( $rights ? $rights : '777' ) . '".';
			}
			if ( $fp ) fclose ( $fp );
			
			// Everything ok return false
			return false;
		}
		// Check if folder is writable
		else if ( is_dir( $dst ) )
		{
			if ( !( $fp = fopen ( "$dst/_test_", 'w' ) ) )
			{
				return 'The "'.$dst.'" folder is not writable. Please change the permissions on it to "' . ( $rights ? $rights : '777' ) . '".';
			}
			if ( file_exists( "$dst/_test_" ) )
			{
				@unlink( "$dst/_test_" );
			}
			if ( $fp ) fclose ( $fp );
			
			// Everything ok return false
			return false;
		}
		else
		{
			return 'Something went wrong ...';
		}
	}
	
	function ListFilesAndFolders( $src, $list = false )
	{
		if( !$src || !file_exists( $src ) ) return false;
		
		$list = ( $list ? $list : array() );
		
		if ( is_dir( $src ) )
		{
			$objects = scandir( $src );
			
            if ( sizeof( $objects ) > 0 )
            {
                foreach( $objects as $file )
                {
					if ( $file == "." || $file == ".." )
					{
                        continue;
					}
					
					$src_file = $src."/".$file;
					$src_dir = dirname( $src_file );
					
					if( !in_array( $src_dir, $list ) )
					{
						$list[] = $src_dir;
					}
					
					if( !in_array( $src_file, $list ) )
					{
						$list[] = $src_file;
					}
					
					if ( is_dir( $src_file ) )
					{
						$list = $this->ListFilesAndFolders( $src_file, $list );
					}
				}
				
				return $list;
			}
		}
		elseif ( is_file( $src ) )
        {
			if( !in_array( dirname( $src ), $list ) )
			{
				$list[] = dirname( $src );
			}
			
			if( !in_array( $src, $list ) )
			{
				$list[] = $src;
			}
			
			return $list;
        }
        else
        {
            return false;
        }
		
		return false;
	}
	
	function RSync( $src, $dst, $opt = false, $list = false )
	{
		if ( !$src || !file_exists( $src ) ) return false;
		
		$list = ( $list ? $list : array() );
		
		// Check if destination folders exist if not create them
		if ( !file_exists( $dst ) )
		{
			@mkdir( $dst, 0777, true );
			@chmod( $dst, 0777 );
		}
		
		// Check file/folder rights
		if ( $error = $this->CheckWriteRights( $dst ) )
		{
			die( $error );
		}
		
		if ( is_dir( $src ) )
		{
			$objects = scandir( $src );
			
            if ( sizeof( $objects ) > 0 )
            {
                foreach( $objects as $file )
                {
					if ( $file == "." || $file == ".." )
					{
                        continue;
					}
					
					// TODO: Make it possible to store modified time on copied files and folders
					
					$src_file = $src."/".$file;
					$path_file = $dst."/".$file;
					$buf = @file_get_contents( $src_file );
					$new_dir = dirname( $path_file );
					
					if( !in_array( $new_dir, $list ) )
					{
						$list[] = $new_dir;
					}
					
					if ( !file_exists( $new_dir ) )
					{
						// Create Recursive Directory
						@mkdir( $new_dir, 0777, true );
						@chmod( $new_dir, 0777 );
					}
					
					if( !in_array( $path_file, $list ) )
					{
						$list[] = $path_file;
					}
					
					if ( is_file( $src_file ) && ( $fp = @fopen( $path_file, "w+" ) ) )
					{
						fwrite( $fp, $buf );
						fclose( $fp );
					}
					else if ( is_dir( $src_file )/* && !file_exists( $path_file )*/ )
					{
						$list = $this->RSync( $src_file, $path_file, false, $list );
					}
				}
				
				return $list;
			}
		}
		elseif ( is_file( $src ) )
        {
			if( !in_array( dirname( $dst ), $list ) )
			{
				$list[] = dirname( $dst );
			}
			
			if( !file_exists( dirname( $dst ) ) )
			{
				@mkdir( dirname( $dst ), 0777, true );
				@chmod( dirname( $dst ), 0777 );
			}
			
			if( !in_array( $dst, $list ) )
			{
				$list[] = $dst;
			}
			
			if( $cnt = @file_get_contents( $src ) )
			{
				if( $fp = fopen( $dst, "w+" ) )
				{
					fwrite ( $fp, $cnt );
					fclose ( $fp );
				}
			}
			
			return $list;
        }
        else
        {
            return false;
        }
		
		return false;
	}
	
	function UnZip( $src, $dst )
	{
		if ( !$src || !file_exists( $src ) || !$dst ) return false;
		
		// Check if destination folders exist if not create them
		if ( !file_exists( $dst ) )
		{
			@mkdir( $dst, 0777, true );
			@chmod( $dst, 0777 );
		}
		
		// Check file/folder rights
		if ( $error = $this->CheckWriteRights( $dst ) )
		{
			die( $error );
		}
		
		if ( is_file( $src ) )
		{
			ob_end_clean();
			
			if ( strstr( $src, '.tgz' ) || strstr( $src, '.tar.gz' ) || strstr( $src, '.tar' ) )
			{
				// TODO: Find something that supports unzipping tar.gz and .tgz files
				
				//$p = new PharData( $src );
				//$p->decompress();
				
				die( 'mja, not supported yet. And not supported on windows servers --- ' . phpversion() );
			}
			else
			{
				$zip = new ZipArchive;
				$res = $zip->open( $src );
				if ( $res === TRUE )
				{
					$zip->extractTo( $dst );
					$zip->close();
					
					return true;
				}
				else
				{
					switch( $res )
					{
						case ZipArchive::ER_EXISTS:
							$ErrMsg = "File already exists.";
							break;
							
						case ZipArchive::ER_INCONS:
							$ErrMsg = "Zip archive inconsistent.";
							break;
						   
						case ZipArchive::ER_MEMORY:
							$ErrMsg = "Malloc failure.";
							break;
						   
						case ZipArchive::ER_NOENT:
							$ErrMsg = "No such file.";
							break;
						   
						case ZipArchive::ER_NOZIP:
							$ErrMsg = "Not a zip archive.";
							break;
						   
						case ZipArchive::ER_OPEN:
							$ErrMsg = "Can't open file.";
							break;
						   
						case ZipArchive::ER_READ:
							$ErrMsg = "Read error.";
							break;
						   
						case ZipArchive::ER_SEEK:
							$ErrMsg = "Seek error.";
							break;
						
						default:
							$ErrMsg = "Unknow (Code $res)";
							break;
					}
					
					die( 'failed, ' . $ErrMsg );
				}
			}
			
			/*$zip = zip_open( $src );
			
			if ( $zip )
			{
				$i = 0;
				
				while ( $zip_entry = zip_read( $zip ) )
				{
					if ( zip_entry_open( $zip, $zip_entry, "r" ) )
					{
						if( $i > 100000 )
						{
							return false; break;
						}
						
						// TODO: Make it possible to store modified time on copied files and folders
						
						$buf = zip_entry_read( $zip_entry, zip_entry_filesize( $zip_entry ) );
						$path_file = $dst."/".zip_entry_name( $zip_entry );
						$new_dir = dirname( $path_file );
						
						if ( !file_exists( $new_dir ) )
						{
							// Create Recursive Directory
							@mkdir( $new_dir, 0777, true );
						}
						
						if ( $fp = @fopen( $path_file, "w+" ) )
						{
							fwrite( $fp, $buf );
							fclose( $fp );
						}
						else if ( !file_exists( $path_file ) )
						{
							@mkdir( $path_file, 0777, true );
						}
						
						zip_entry_close( $zip_entry );
					}
					
					$i++;
				}
				
				zip_close( $zip );
				
				return true;
			}*/
		}
		
		return false;
	}
	
	function Zip( $basedir, $data, $arcpf, &$obj = '' )
	{
		if ( !$basedir || !file_exists( $basedir ) || !file_exists( $data ) ) return false;
		
		$absoluterpfad = ( is_object( $obj ) == false ? $data : $basedir );
        $arcpf = $basedir.'/'.$arcpf;
		
		//die( $basedir . ' -- ' . $data . ' -- ' . $arcpf . ' -- ' . $absoluterpfad );
		
		if ( is_object( $obj ) == false )
		{
			$archiv = new ZipArchive();
			$archiv->open( $arcpf, ZipArchive::CREATE );
		}
		else
		{
			$archiv =& $obj;
		}
		
		if ( is_array( $data ) == true )
		{
			foreach( $data as $dtmp )
			{
				$archiv =& $this->Zip( $absoluterpfad, $dtmp, $arcpf, $archiv );
			}
		}
		else
		{
			if ( is_dir( $data ) == true )
			{
				// Add only sub folders not the main parent
				if ( $obj != '' )
				{
					$archiv->addEmptyDir( str_replace( $absoluterpfad.'/', '', $data ) );
				}
				$files = scandir( $data );
				$bad = array( '.', '..' );
				$files = array_diff( $files, $bad );
				//die( print_r( $files,1 ) . ' --' );
				foreach ( $files as $ftmp )
				{
					if ( is_dir( $data.'/'.$ftmp ) == true )
					{
						$archiv->addEmptyDir( str_replace( $absoluterpfad.'/', '', $data.'/'.$ftmp ) );
						$archiv =& $this->Zip( $absoluterpfad, $data.'/'.$ftmp, $arcpf, $archiv );
					}
					elseif ( is_file( $data.'/'.$ftmp ) == true )
					{
						$archiv->addFile( $data.'/'.$ftmp, str_replace( $absoluterpfad.'/', '', $data.'/'.$ftmp ) );
					}
				}
			}
			elseif ( is_file( $data ) == true )
			{
				$archiv->addFile( $data, str_replace( $absoluterpfad.'/', '', $data ) );
			}
		}
		
		if ( is_object( $obj ) == false )
		{
			$archiv->close();
			return true;
		}
		else
		{
			return $archiv;
		}
	}
	
	function MySqlExport( $bdir, $dst )
	{
		global $database;
		
		if( !$bdir || !$dst ) return false;
		
		$file = $bdir . $dst;
		
		// Check if destination folders exist if not create them
		if ( !file_exists( dirname( $file ) ) )
		{
			@mkdir( dirname( $file ), 0777, true );
			@chmod( dirname( $file ), 0777 );
		}
		
		// Check file/folder rights
		if ( $error = $this->CheckWriteRights( $file ) )
		{
			die( $error );
		}
		
		if( $_site_ = $database->fetchObjectRow( '
			SELECT * FROM Sites 
			ORDER BY ID ASC 
			LIMIT 1 
		' ) )
		{
			$Host = $_site_->SqlHost;
			$UserName = $_site_->SqlUser;
			$Password = $_site_->SqlPass;
			$Database = $_site_->SqlDatabase;
		}
		
		$connection = mysql_connect( "$Host", "$UserName", "$Password" );
		mysql_select_db( $Database, $connection );
		$sql = 'SHOW TABLES FROM ' . $Database;
		$result = mysql_query( $sql );
		$contents = "-- Database: " . $Database . "\n-- Created: ".date('M j, Y')." at ".date('h:i A')."\n\n";
		while ( $tables = mysql_fetch_array( $result ) )
		{
			$TableList[] = $tables[0];
		}
		foreach ( $TableList as $table )
		{
			$row = mysql_fetch_assoc( mysql_query( 'SHOW CREATE TABLE ' . $table ) );
			$contents .= $row["Create Table"] . ";\n\n";
			$sql = 'SELECT * FROM ' . $table;
			$result = mysql_query( $sql );
			$columns = explode( ',', $row["Create Table"] );
			$i = 0;
			while ( $records = mysql_fetch_array( $result ) )
			{
				$contents .= "INSERT INTO " . $table . " VALUES (";
				for ( $i=0; $i <= count($records)/2; $i++ )
				{
					if ( $i < count($records)/2-1 )
					{
						if ( strstr( $columns[$i], "varchar" ) || strstr( $columns[$i], "text" ) || strstr( $columns[$i], datetime ) )
						{
							$contents .= "'" . mysql_real_escape_string( $records[$i] ) . "',";
						}
						else
						{
							$contents .= mysql_real_escape_string( $records[$i] ) . ",";
						}
					}
					else
					{
						if ( strstr( $columns[$i], "varchar" ) || strstr( $columns[$i], "text" ) || strstr( $columns[$i], datetime ) )
						{
							$contents .= "'" . mysql_real_escape_string( $records[$i] ) . "'";
						}
						else
						{
							$contents .= mysql_real_escape_string( $records[$i] ) . "";
						}
					}
				}
				$contents .= ");\n";
				$i++;
			}
			$contents .= "\n";
		}
		
		/*if( !file_exists( dirname( $file ) ) )
		{
			@mkdir( dirname( $file ), 0777, true );
		}*/
		if( $handle = fopen( $file, 'w+' ) )
		{
			fwrite( $handle, $contents );
			fclose ( $handle );
			
			return true;
		}
		
		return false;
	}
	
	function CreateFolder( $root, $folderpath, $chmod )
	{
		if( !$root || !$folderpath || !$chmod ) return;
		
		// Add dot and slash at beginning if it doesnt have one
		if( $root{0} != '.' && $root{1} != '/' )
		{
			$root = ltrim( $root, '/' );
			$root = ltrim( $root, '.' );
			$root = './' . $root;
		}
		
		// Add slash at end if it doesn't have one
		if( $folderpath && substr( $folderpath, -1 ) != '/' )
		{
			$folderpath = $folderpath . '/';
		}
		
		$folderpath = trim( strtolower( $folderpath ) );
		
		if( file_exists( $root ) )
		{
			if( !file_exists( $folderpath ) )
			{
				if( !mkdir( $folderpath, 0777, true ) )
				{
					return false;
				}
				chmod( $folderpath, 0777 );
			}
			
			return true;
		}
		
		return false;
	}
	
	function DeleteFolder( $path, $folder )
	{
		if( !$path || !$folder ) return;
		
		if( substr( $path, -1 ) != '/' )
		{
			$path = $path . '/';
		}
		
		$folderpath = ( $path . $folder );
		
		if( file_exists ( $folderpath ) )
		{
			if( $directory = $this->OpenFolder( $path, $folder ) )
			{
				foreach( $directory as $key=>$dir )
				{
					if( $dir->dir && $dir->path && $dir->name )
					{
						// IsFolder
						if( $dir->isdir )
						{
							if( $this->DeleteFolder( $dir->dir, $dir->name ) )
							{
								unset( $directory[$key] );
							}
						}
						// IsFile
						else if( $dir->isfile )
						{
							if( $this->DeleteFile( $dir->dir, $dir->name ) )
							{
								unset( $directory[$key] );
							}
						}
					}
				}
			}
			
			if( !$directory )
			{
				@rmdir( $folderpath );
				
				return true;
			}
		}
		
		return false;
	}
	
	function OpenFolder( $path, $folder )
	{
		if( !$path || !$folder ) return;
		
		if( substr( $path, -1 ) != '/' )
		{
			$path = $path . '/';
		}
		
		$folderpath = ( $path . $folder );
		
		if( file_exists ( $folderpath ) && $dir = opendir ( $folderpath ) )
		{
			if( substr( $folderpath, -1 ) != '/' )
			{
				$folderpath = $folderpath . '/';
			}
			
			$depth = 0;
			$out = array();
			while ( $file = readdir ( $dir ) )
			{
				if ( $file{0} == '.' && strlen( $file ) <= 2 ) continue;
				$filepath = ( $folderpath . $file );
				//$stats = stat( $filepath );
				$parts = explode( '.', $file );
				$type = end( $parts );
				$version = $this->FileVersion( $file );
				$title = str_replace( ( $version ? $version : '' ).( $type ? ( '.'.$type ) : '' ), '', $file );
				$obj = new stdClass();
				$obj->dir = ( defined( 'BASE_DIR' ) ? ( BASE_DIR . /*'/' .*/ str_replace( BASE_DIR, '', $folderpath ) ) : $folderpath );
				$obj->path = ( defined( 'BASE_URL' ) ? BASE_URL : '' ) . ( defined( 'BASE_DIR' ) ? str_replace( ( BASE_DIR . '/' ), '', $filepath ) : $filepath );
				$obj->name = $file;
				$obj->title = $title;
				$obj->type = $type;
				$obj->isdir = ( is_dir( $filepath ) ? 1 : 0 );
				$obj->isfile = ( is_file( $filepath ) ? 1 : 0 );
				//$obj->size = $stats['size'];
				$obj->size = filesize( $filepath );
				$obj->size = $this->ReadableFilesize( $obj->size );
				$obj->modified = filemtime( $filepath );
				$obj->version = $version;
				$out[] = $obj;
				$depth++;
				if( $depth >= 10000 ) return false;
			}
			closedir ( $dir );
			return $out;
		}
		return false;
	}
	
	function StrTrim( $str, $max, $find = false, $dotted = true )
	{
		if( $find ) 
		{
			$pos = strpos( strtolower( $str ), strtolower( $find ) );
		}
		if ( $max && strlen ( $str ) > $max )
		{
			return substr ( $str, ( $find ? $pos : 0 ), $max ) . ( $dotted ? '...' : '' );
		}
		return $str;
	}
	
	function FileVersion( $filename )
	{
		if( !$filename ) return false;
		
		$ext = explode( '.', $filename );
		$ext = end( $ext );
		
		$name = str_replace( ( '.' . $ext ), '', $filename );
		
		$version = preg_replace( '/[^0-9\.]/', '', $name );
		
		if ( $version )
		{
			return  trim( $version );
		}
		
		return false;
	}
	
	function ReadableFilesize( $size )
	{
		if( !$size ) return '0b';
		
		// --- gigabytes ----------------------
		
		if( $size >= 1000000000 )
		{
			return ( round( $size / 1000000000, 1 ) . 'gb' );
		}
		
		// --- megabytes ----------------------
		
		if( $size >= 1000000 )
		{
			return ( round( $size / 1000000, 1 ) . 'mb' );
		}
		
		// --- kilobytes ----------------------
		
		if( $size >= 1000 )
		{
			return ( round( $size / 1000, 1 ) . 'kb' );
		}
		
		return ( $size . 'b' );
	}
	
	function SanitizeFilename( $filename )
	{
		if( !$filename ) return false;
		
		$clean_name = strtr( $filename, array( '' => 'S','' => 'Z','' => 's','' => 'z','' => 'Y','À' => 'A','Á' => 'A','Â' => 'A','Ã' => 'A','Ä' => 'A','Å' => 'A','Ç' => 'C','È' => 'E','É' => 'E','Ê' => 'E','Ë' => 'E','Ì' => 'I','Í' => 'I','Î' => 'I','Ï' => 'I','Ñ' => 'N','Ò' => 'O','Ó' => 'O','Ô' => 'O','Õ' => 'O','Ö' => 'O','Ø' => 'O','Ù' => 'U','Ú' => 'U','Û' => 'U','Ü' => 'U','Ý' => 'Y','à' => 'a','á' => 'a','â' => 'a','ã' => 'a','ä' => 'a','å' => 'a','ç' => 'c','è' => 'e','é' => 'e','ê' => 'e','ë' => 'e','ì' => 'i','í' => 'i','î' => 'i','ï' => 'i','ñ' => 'n','ò' => 'o','ó' => 'o','ô' => 'o','õ' => 'o','ö' => 'o','ø' => 'o','ù' => 'u','ú' => 'u','û' => 'u','ü' => 'u','ý' => 'y','ÿ' => 'y' ) );
		$clean_name = strtr( $clean_name, array( 'Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', '' => 'OE', '' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u' ) );
		$clean_name = preg_replace( array( '/\s/', '/\.[\.]+/', '/[^\w_\.\-]/' ), array( '_', '.', '' ), $clean_name );
		$clean_name = str_replace( ' ', '_', $clean_name );
		$clean_name = str_replace( array( 'æ', 'ø', 'å', 'Æ', 'Ø', 'Å' ), array( 'ae', 'o', 'aa', 'Ae', 'O', 'Aa' ), $clean_name );
		
		$file = explode( '.', $clean_name );
		
		if( strlen( $file[0] ) > 128 )
		{
			return substr( $file[0], 0, 128 ) . ( isset( $file[1] ) ? ( '.' . end( $file ) ) : '' );
		}
		
		return $clean_name;
	}
	
	function UniqueFile( $filename, $path = false, $title = false )
	{
		$ext = '';
		
		if( !$path && $this->FolderPath )
		{
			$path = $this->FolderPath;
		}
		
		if( !$filename || !$path ) return false;
		
		if( substr( $path, -1 ) != '/' )
		{
			$path = $path . '/';
		}
		
		$parts = explode ( '.', $filename );
		// Get the file extension, if any
		if( count( $parts ) > 1 )
		{
			$ext = array_pop ( $parts );
		}
		
		// Get the filename
		$file = implode( '.', $parts );
		
		if( $title )
		{
			$file = $this->SanitizeFilename( $title );
		}
		
		if ( file_exists ( $path . $file . '.' . $ext ) )
		{
			$depth = 0;
			while ( file_exists ( $path . $file . '.' . $ext ) )
			{
				$parts = explode( '_', $file );
				$num = end( $parts );
				$file = str_replace( ( '_' . $num ), '', $file );
				$file .= ( $num && is_numeric( $num ) ? ( '_' . ( $num + 1 ) ) : '_1' );
				$depth++;
				if( $depth >= 1000 ) return false;
			}
		}
		return ( $file . '.' . $ext );
	}
	
	function UniqueFolder( $folder, $path = false )
	{
		if( !$path && $this->ParentPath )
		{
			$path = $this->ParentPath;
		}
		
		if( !$folder || !$path ) return false;
		
		if( substr( $path, -1 ) != '/' )
		{
			$path = $path . '/';
		}
		
		if ( file_exists ( strtolower( $path . $folder ) ) && is_dir( strtolower( $path . $folder ) ) )
		{
			$depth = 0;
			while ( file_exists ( strtolower( $path . $folder ) ) )
			{
				$parts = explode( '_', $folder );
				$num = end( $parts );
				$folder = str_replace( ( '_' . $num ), '', $folder );
				$folder .= ( $num && is_numeric( $num ) ? ( '_' . ( $num + 1 ) ) : '_1' );
				$depth++;
				if( $depth >= 1000 ) return false;
			}
		}
		return ( $folder );
	}
	
	function UniqueKey( $option1 = false, $option2 = false, $option3 = false, $option4 = false )
	{
		$host = ( BASE_URL ? ( BASE_URL . '|' ) : '' );
		$option1 = ( $option1 ? ( $option1 . '|' ) : '' );
		$option2 = ( $option2 ? ( $option2 . '|' ) : '' );
		$option3 = ( $option3 ? ( $option3 . '|' ) : '' );
		$option4 = ( $option4 ? ( $option4 . '|' ) : '' );
		$current = ( time() . '|' );
		$random = str_replace( ' ', '', rand(0,999).rand(0,999).rand(0,999).microtime() );
		$hexkey = hex_sha256( $host.$option1.$option2.$option3.$option4.$current.$random );
		
		return $hexkey;
	}
	
	function VideoFormats()
	{
		return array(
			'webm', 'mkv', 'flv', 'ogv', 'ogg', 'drc', 'mng',
			'avi', 'mov', 'qt', 'wmv', 'rm', 'rmvb', 'asf',
			'mp4', 'm4p', 'm4v', 'mpg', 'mp2', 'mpeg', 'mpg',
			'mpe', 'mpv', 'mpeg', 'm2v', 'm4v', 'svi', '3gp',
			'3g2', 'mxf', 'roq', 'nsv'
		);
	}
	
	function AudioFormats()
	{
		return array(
			'3gp', 'act', 'aiff', 'aac', 'amr', 'au', 'awb',
			'dct', 'dss', 'dvf', 'flac', 'gsm', 'iklax', 'ivs',
			'm4a', 'm4p', 'mmf', 'mp3', 'mpc', 'msv', 'ogg', 'oga',
			'opus', 'ra', 'rm', 'raw', 'tta', 'vox', 'wav', 'wma', 'wv'
		);
	}
	
	function FileFormats()
	{
		return array(
			'pdf', 'psd', 'ai', 'eps', 'ps', 'doc', 'rtf', 'xls', 'ppt',
			'odt', 'ods', 'zip', 'rar', 'exe', 'msi', 'cab', 'txt'
		);
	}
	
	function FileTypes( $filename )
	{
		if( !$filename ) return false;
		
		$file_types = array(
            'txt' => 'text',
            'htm' => 'text',
            'html' => 'text',
            'php' => 'text',
            'css' => 'text',
            'js' => 'file',
            'json' => 'file',
            'xml' => 'file',
            'swf' => 'file',
            'flv' => 'video',
			
            // images
            'png' => 'image',
            'jpe' => 'image',
            'jpeg' => 'image',
            'jpg' => 'image',
            'gif' => 'image',
            'bmp' => 'image',
            'ico' => 'image',
            'tiff' => 'image',
            'tif' => 'image',
            'svg' => 'image',
            'svgz' => 'image',
			
            // archives
            'zip' => 'file',
            'rar' => 'file',
            'exe' => 'file',
            'msi' => 'file',
            'cab' => 'file',
			
            // audio/video
            'mp3' => 'audio',
            'qt' => 'video',
            'mov' => 'video',
			'avi' => 'video',
			'mp4' => 'video',
			'ogg' => 'video',
			'ogv' => 'video',
			'webm' => 'video',
			'swf' => 'video',
			
            // adobe
            'pdf' => 'file',
            'psd' => 'file',
            'ai' => 'file',
            'eps' => 'file',
            'ps' => 'file',
			
            // ms office
            'doc' => 'file',
			'docx' => 'file',
            'rtf' => 'file',
            'xls' => 'file',
            'ppt' => 'file',
			
            // open office
            'odt' => 'file',
            'ods' => 'file',
        );
		
		$parts = explode( '.', $filename );
		$ext = strtolower( array_pop( $parts ) );
		
        if( array_key_exists( $ext, $file_types ) )
		{
            return $file_types[$ext];
        }
        else
		{
            return 'file';
        }
	}
	
	function MimeType( $filename, $path = false )
	{
		if( !$filename ) return false;
		
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
			
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
			
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
			
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
			'avi' => 'video/avi',
			'mp4' => 'video/mp4',
			'ogg' => 'video/ogg',
			'ogv' => 'video/ogg',
			'webm' => 'video/webm',
			'swf' => 'video/swf',
			
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
			
            // ms office
            'doc' => 'application/msword',
			'docx' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
			
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
		
		$parts = explode( '.', $filename );
        $ext = strtolower( array_pop( $parts ) );
		
        if( array_key_exists( $ext, $mime_types ) )
		{
            return $mime_types[$ext];
        }
        elseif( function_exists( 'finfo_open' ) && $path && file_exists( $path . $filename ) )
		{
            $finfo = finfo_open( FILEINFO_MIME_TYPE );
            $mimetype = finfo_file( $finfo, ( $path . $filename ) );
            finfo_close( $finfo );
			
            return $mimetype;
        }
        else
		{
            return 'application/octet-stream';
        }
    }
}

?>
