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

class SBook
{
	var $UserID;
	var $CategoryID;
	var $CategoryName;
	var $FileFolder;
	var $MediaFolder;
	var $MediaType;
	var $MediaID;
	var $FolderName;
	var $FileName;
	var $FileParent;
	var $FileContent;
	var $SortOrder = 0;
	
	function __construct ( $table = false )
	{
		global $webuser;
		
		!$this->UserID ? $this->UserID = $webuser->ID : '';
	}
	
	function CreateFileFolder ()
	{
		global $database;
		
		$newfolder = ( $this->FolderName != '' ? 1 : 0 );
		
		if( $this->UserID || $this->CategoryID )
		{
			// Get root folder
			$rf = new dbFolder ();
			$rf = $rf->getRootFolder ();
			
			$folders = array( 'SocialNetwork' );
			$folders[] = $this->CategoryID > 0 && $this->CategoryName != '' ? $this->CategoryName : 'Profile'; 
			$folders[] = $this->CategoryID > 0 && $this->CategoryName != '' ? $this->CategoryID : $this->UserID; 
			$folders[] = $this->FolderName != '' ? $this->FolderName : 'Library'; 
			
			//die( print_r( $folders,1 ) . ' ..' );
			
			if( $folders )
			{
				// Get or create folders
				foreach( $folders as $fname )
				{
					$db = new dbFolder ();
					//$db->Name = ( $this->FolderName == 'Folder_1' && $this->FileFolder > 0 ? $this->GetUniqueFileFolder( $fname ) : $fname );
					$db->Name = ( $this->FolderName == $fname && $fname == 'Folder_1' ? $this->GetUniqueFileFolder( $fname ) : $fname );
					$db->Parent = $lf > 0 ? $lf : $rf->ID;
					if( !$db->Load () )
					{
						$db->Save ();
					}
					$lf = $db->ID;
					$cf = $db->Name;
				}
			}
			
			//die( $this->FolderName . ' .. ' . $this->FileFolder . ' .. ' . $lf . ' .. ' . $cf . ' .. ' . $newfolder );
			
			// Assign FildeFolder
			$lf > 0 ? $this->FileFolder = $lf : '';
			
			// Assign FolderName
			$cf != '' ? $this->FolderName = $cf : '';
			
			//die( $this->FolderName . ' .. ' . $this->FileFolder . ' .. ' . $lf . ' .. ' . $cf . ' .. ' . $newfolder );
			
			if( $newfolder == 0 && $this->GetFileFolder()->Tags != '' )
			{
				$this->FolderName = $this->GetFileFolder()->Tags;
				//die( $this->FolderName . ' .. ' . $this->FileFolder . ' .. ' . $lf . ' .. ' . $cf . ' .. ' . $newfolder );
			}
			
			//die( $this->FolderName . ' .. ' . $this->FileFolder . ' .. ' . $lf . ' .. ' . $cf . ' .. ' . $newfolder );
			
		}

	}
	
	function CreateMediaRelation ()
	{
		if( $this->FileFolder && $this->FolderName && ( $this->UserID || $this->CategoryID ) )
		{
			// Save media relation if it doesnt exist
			$db = new dbObject( 'SBookMediaRelation' );
			$db->Tags = $this->FolderName;
			$db->Name = $this->FolderName;
			$db->Title = $db->Name;
			$db->MediaID = $this->FileFolder;
			$db->MediaType = 'Folder';
			if( $this->CategoryID > 0 ) $db->CategoryID = $this->CategoryID;
			else $db->UserID = $this->UserID;
			if( !$db->Load() )
			{
				$db->SortOrder = $this->SortOrder > 0 ? $this->SortOrder : floor( $this->GetFolderSortOrder()->SortOrder + 1 );
				$db->Save();
			}
		}
	}
	
	function CreateFile ()
	{
		if( $this->FileFolder > 0 )
		{
			if( $this->FileName || !$cf = $this->GetCurrentFile() )
			{
				// Get or create Files folder
				$ff = new dbFolder ();
				$ff->Name = ( !$this->FileName || $this->FileName == 'File_1' ? $this->GetUniqueFileName( 'File_' . date( 'Ymd' ) . '_1' ) : $this->FileName );
				$ff->Parent = $this->FileFolder;
				if( !$ff->Load () )
				{
					$ff->Save ();
				}
			}
			
			// Clear current tag
			$this->clearCurrentTag();
			
			// Save Content to Files folder
			$db = new dbObject( 'SBookFiles' );
			$db->FileFolder = $this->FileFolder;
			$db->MediaID = $ff->ID > 0 ? $ff->ID : $cf->MediaID;
			$db->MediaType = 'folder';
			$db->Filename = $ff->Name != '' ? $ff->Name : $cf->Filename;
			if( !$db->Load() )
			{
				$db->Title = $db->Filename;
				$db->DateCreated = date( 'Y-m-d H:i:s' );
			}
			$db->Tags = 'Current';
			if( $this->FileContent ) $db->ContentData = trim( $this->FileContent );
			$db->DateModified = date( 'Y-m-d H:i:s' );
			$db->Save();
			
			// Assign FileName
			$this->FileName = $db->Filename;
			
			// Assign FileParent
			$this->FileParent = $db->ID;
			
			// Assign MediaFolder
			$this->MediaFolder = $db->MediaID;
			
			// Return File
			return $db;
		}
	}
	
	function UploadFile ( $files )
	{
		if( $this->UserID || $this->CategoryID )
		{
			$this->CreateFileFolder();
			$this->CreateMediaRelation();
			$this->CreateFile();
			
			//die( $this->FileFolder . ' .. ' . $this->FileParent . ' .. ' . print_r( $files,1 ) );
			
			if( $this->FileFolder && $this->MediaFolder && $this->FileParent )
			{
				// Get filetype
				$filetype = explode( '/', $files[ 'type' ] );
				
				if( $filetype && $filetype[0] == 'image' )
				{
					// Save image to folder
					$file = new dbImage ();
					$file->ReceiveUpload( $files );
					$file->ImageFolder = $this->MediaFolder;
					$file->Save();
					
					//die( $file->ID . ' .. ' . print_r( $this,1 ) );
				}
				else if( $filetype && $filetype[0] == 'video' )
				{
					// Save video to folder
					$file = new dbFile ();
					$file->ReceiveUpload( $files );
					$file->FileFolder = $this->MediaFolder;
					$file->Save();
					
					// Video Converter
					$mediahandler = new MediaHandler();
					$rootpath = 'upload/';
					$inputpath = $rootpath . '';
					$outputpath = $rootpath . '';
					$thumbpath = $rootpath . '';
					$outfile = $mediahandler->convert_media( $file->Filename, $rootpath, $inputpath, $outputpath, 400, 300, 32, 22050 );
					$image_name = $mediahandler->grab_image( $outfile, $rootpath, $outputpath, $thumbpath, 1, 2, 'png', 400, 300 );
					$file->Fileduration = $mediahandler->get_duration( $outfile, $outputpath );
					$mediahandler->set_buffering( $outfile, $rootpath, $outputpath );
				}
				else
				{
					// Save file to folder
					$file = new dbFile ();
					$file->ReceiveUpload( $files );
					$file->FileFolder = $this->MediaFolder;
					$file->Save();
				}
				
				// Save Content to Files folder
				$db = new dbObject( 'SBookFiles' );
				$db->Parent = $this->FileParent;
				$db->FileFolder = $this->FileFolder;
				$db->Filename = $file->Filename;
				$db->Title = $file->Title;
				$db->Filesize = $file->Filesize;
				$db->Fileduration = $file->Fileduration;
				$db->Filetype = $file->Filetype;
				$db->MediaID = $file->ID;
				$db->MediaType = $this->MediaType = ( $filetype && $filetype[0] == 'image' ? 'image' : ( $filetype && $filetype[0] == 'video' ? 'video' : 'file' ) );
				$db->DateCreated = date( 'Y-m-d H:i:s' );
				$db->DateModified = date( 'Y-m-d H:i:s' );
				$db->Save();
				
				// Set MediaID
				$this->MediaID = $db->MediaID;
				// Set FileID
				$this->FileID = $db->ID;
			}
			return $this->FileParent;
		}
	}
	
	function UploadFileByUrl ( $url )
	{
		if( !$url ) return false;
		
		$root = 'upload/';
		
		if( $this->UserID || $this->CategoryID )
		{
			$this->CreateFileFolder();
			$this->CreateMediaRelation();
			$this->CreateFile();
			
			if( $this->FileFolder && $this->MediaFolder && $this->FileParent )
			{
				// Image root destination
				$root = $root . 'images-master/';
				
				if( $image = $this->GrabImage( $url, $root ) )
				{
					// Save image to folder
					$file = new dbImage ();
					$file->FilePath = $root;
					$file->Filename = $image;
					$file->Title = $image;
					if( $imagesize = getimagesize( $root . $image ) )
					{
						$file->Filesize = $imagesize['bits'];
						$file->Width = $imagesize[0];
						$file->Height = $imagesize[1];
						$file->Filetype = end( explode( '/', $imagesize['mime'] ) );
						$file->FilenameOriginal = $image;
					}
					$file->ImageFolder = $this->MediaFolder;
					$file->Save();
					
					// Save Content to Files folder
					$db = new dbObject( 'SBookFiles' );
					$db->Parent = $this->FileParent;
					$db->FileFolder = $this->FileFolder;
					$db->Filename = $file->Filename;
					$db->Title = $file->Title;
					$db->Filesize = $file->Filesize;
					$db->Filetype = $file->Filetype;
					$db->MediaID = $file->ID;
					$db->MediaType = $this->MediaType = 'image';
					$db->DateCreated = date( 'Y-m-d H:i:s' );
					$db->DateModified = date( 'Y-m-d H:i:s' );
					$db->Save();
					
					// Set MediaID
					$this->MediaID = $db->MediaID;
					// Set FileID
					$this->FileID = $db->ID;
					
					return $this->MediaID;
				}
			}
		}
	}
	
	function SaveContentToFile ()
	{
		$root = 'upload/';
		
		if( $this->UserID || $this->CategoryID )
		{
			$this->CreateFileFolder();
			$this->CreateMediaRelation();
			$this->CreateFile();
			
			if( $this->FileFolder && $this->MediaFolder && $this->FileParent && $this->FileName )
			{
				if( !strstr( $this->FileName, '.' ) )
				{
					$this->FileName = $this->FileName . '.txt';
				}
				
				if( $media = $this->SaveToFile( $root, ( $this->CategoryID . $this->UserID . $this->FileName ), $this->FileContent ) )
				{
					// Save file to folder
					$file = new dbFile ();
					$file->FilePath = $root;
					$file->Filename = $media;
					$file->Title = $this->FileName;
					$file->FileFolder = $this->MediaFolder;
					$file->FilenameOriginal = $media;
					if( !$file->Load() )
					{
						$file->DateCreated = date( 'Y-m-d H:i:s' );
					}
					if( $stats = stat( $root . $media ) )
					{
						$file->Filesize = $stats['size'];
						$file->Filetype = end( explode( '.', $media ) );
					}
					$file->DateModified = date( 'Y-m-d H:i:s' );
					$file->Save();
					
					// Save Content to Files folder
					$db = new dbObject( 'SBookFiles' );
					$db->Parent = $this->FileParent;
					$db->FileFolder = $this->FileFolder;
					$db->Filename = $file->Filename;
					$db->Title = $file->Title;
					$db->MediaID = $file->ID;
					$db->MediaType = $this->MediaType = 'file';
					if( !$db->Load() )
					{
						$db->DateCreated = date( 'Y-m-d H:i:s' );
					}
					$db->DateModified = date( 'Y-m-d H:i:s' );
					$db->Filesize = $file->Filesize;
					$db->Filetype = $file->Filetype;
					$db->Save();
					
					// Set MediaID
					$this->MediaID = $db->MediaID;
					// Set FileID
					$this->FileID = $db->ID;
					
					return $this->MediaID;
				}
			}
		}
	}
	
	function GetFolderSortOrder ()
	{
		global $database;
		
		if( $this->FileFolder && ( $this->CategoryID || $this->UserID ) )
		{
			// Get highest row
			return $database->fetchObjectRow ( '
				SELECT * FROM SBookMediaRelation 
				WHERE ' . ( $this->CategoryID > 0 ? 'CategoryID = \'' . $this->CategoryID . '\'' : 'UserID = \'' . $this->UserID . '\'' ) . ' 
				AND MediaID = \'' . $this->FileFolder . '\' 
				ORDER BY SortOrder DESC 
			' );
		}
	}
	
	function GetFileSortOrder ( $parent = false )
	{
		global $database;
		
		if( $this->FileFolder && ( $this->CategoryID || $this->UserID ) )
		{
			// Get highest row
			return $database->fetchObjectRow ( '
				SELECT f.* FROM SBookFiles f, SBookMediaRelation r  
				WHERE ' . ( $this->CategoryID > 0 ? 'r.CategoryID = \'' . $this->CategoryID . '\'' : 'r.UserID = \'' . $this->UserID . '\'' ) . ' 
				AND r.MediaID = \'' . $this->FileFolder . '\' AND f.FileFolder = r.MediaID AND f.Parent = \'' . ( $parent > 0 ? $parent : 0 ) . '\' 
				ORDER BY SortOrder DESC 
			' );
		}
	}
	
	function GetUniqueFileFolder ( $foldername )
	{
		global $database;
		
		if( $foldername && ( $this->UserID || $this->CategoryID ) )
		{
			$q = '
				SELECT 
					* 
				FROM 
					SBookMediaRelation 
				WHERE 
					MediaType = "Folder" 
					' . ( $this->CategoryID ? 'AND CategoryID = \'' . $this->CategoryID . '\' ' : 'AND UserID = \'' . $this->UserID . '\' ' ) . ' 
				ORDER BY 
					ID ASC 
			';
			
			if( $cnt = $database->fetchObjectRows ( $q ) )
			{
				foreach( $cnt as $ct )
				{
					$count = explode( '_', $ct->Tags );
					$count[1] > $fn ? $fn = $count[1] : '';
				}
			}
			
			if( $prfo = $database->fetchObjectRows ( $q ) )
			{
				foreach( $prfo as $fo )
				{
					if( trim( strtolower( $foldername ) ) == trim( strtolower( $fo->Tags ) ) )
					{
						$folder = explode( '_', $foldername );
						return $folder[0] . '_' . ( $fn + 1 );
					}
				}
			}
			
			return $foldername;
		}
	}
	
	function GetUniqueFileName ( $filename )
	{
		global $database;
		
		if( $filename && ( $this->UserID || $this->CategoryID ) )
		{
			$q = '
				SELECT 
					f.* 
				FROM 
					SBookFiles f, 
					SBookMediaRelation r 
				WHERE 
					' . ( $this->CategoryID > 0 ? 'r.CategoryID = \'' . $this->CategoryID . '\'' : 'r.UserID = \'' . $this->UserID . '\'' ) . ' 
					AND r.MediaType = "Folder" 
					AND f.FileFolder = r.MediaID 
					AND f.Parent = "0" 
				ORDER BY 
					f.ID ASC 
			';
			
			if( $cnt = $database->fetchObjectRows ( $q ) )
			{
				foreach( $cnt as $ct )
				{
					$count = explode( '_', $ct->Filename );
					$count[2] > $fn ? $fn = $count[2] : '';
				}
			}
			
			if( $pref = $database->fetchObjectRows ( $q ) )
			{
				foreach( $pref as $pf )
				{
					if( trim( strtolower( $filename ) ) == trim( strtolower( $pf->Filename ) ) )
					{
						$file = explode( '_', $filename );
						return $file[0] . '_' . $file[1] . '_' . ( $fn + 1 );
					}
				}
			}
			
			return $filename;
		}
	}
	
	function GetFileFolder ( $fid = false )
	{
		global $database;
		
		if( $this->CategoryID || $this->UserID )
		{
			if( $fid > 0 )
			{
				$this->FileFolder = $fid;
			}
			else if( $this->GetCurrentFile()->FileFolder )
			{
				$this->FileFolder = $this->GetCurrentFile()->FileFolder;
			}
			/*$fid > 0 ? $this->FileFolder = $fid : '';
			!$this->FileFolder ? $this->FileFolder = $this->GetCurrentFile()->FileFolder : '';*/
			
			// Get current folder info
			$db = $database->fetchObjectRow ( '
				SELECT 
					r.* 
				FROM 
					SBookMediaRelation r 
				WHERE 
					' . ( $this->CategoryID > 0 ? 'r.CategoryID = \'' . $this->CategoryID . '\'' : 'r.UserID = \'' . $this->UserID . '\'' ) . ' 
					' . ( $this->FileFolder > 0 ? 'AND r.MediaID = \'' . $this->FileFolder . '\' ' : '' ) . ' 
					AND r.MediaType = "Folder" 
				ORDER BY 
					r.ID DESC 
			' );
			
			return $db;
		}
	}
	
	function GetCurrentFile ()
	{
		global $database;
		
		if( $this->CategoryID || $this->UserID )
		{
			// Get current file info
			if( $db = $database->fetchObjectRow ( '
				SELECT 
					f.* 
				FROM 
					SBookFiles f, 
					SBookMediaRelation r 
				WHERE 
					' . ( $this->CategoryID > 0 ? 'r.CategoryID = \'' . $this->CategoryID . '\'' : 'r.UserID = \'' . $this->UserID . '\'' ) . ' 
					AND r.MediaType = "Folder" 
					AND f.FileFolder = r.MediaID 
					AND f.Parent = "0" 
					AND f.Tags = "Current" 
				ORDER BY 
					f.ID DESC 
			' ) )
			{
				return $db;
			}
		}
	}
	
	function ClearCurrentTag ()
	{
		global $database;
		
		if( $this->CategoryID || $this->UserID )
		{
			// Clear current file info
			if( $db = $database->fetchObjectRows ( '
				SELECT 
					f.* 
				FROM 
					SBookFiles f, 
					SBookMediaRelation r 
				WHERE 
					' . ( $this->CategoryID > 0 ? 'r.CategoryID = \'' . $this->CategoryID . '\'' : 'r.UserID = \'' . $this->UserID . '\'' ) . ' 
					AND r.MediaType = "Folder" 
					AND f.FileFolder = r.MediaID 
					AND f.Parent = "0" 
					AND f.Tags = "Current" 
				ORDER BY 
					f.ID DESC 
			' ) )
			{
				foreach( $db as $d )
				{
					$ef = new dbObject( 'SBookFiles' );
					if( $ef->Load( $d->ID ) )
					{
						$ef->Tags = '';
						$ef->Save();
					}
				}
			}
		}
	}
	
	function SaveFile ( $fid )
	{
		if( $fid && $this->FileName )
		{
			// Save edit to file title
			$db = new dbObject( 'SBookFiles' );
			if( $db->Load( $fid ) )
			{
				if( $db->MediaID > 0 && $db->MediaType == 'image' )
				{
					$image = new dbObject( 'Image' );
					if( $image->Load( $f->MediaID ) )
					{
						$image->Title = $this->FileName;
						$image->DateModified = date( 'Y-m-d H:i:s' );
						$image->Save();
					}
				}
				else if( $db->MediaID > 0 && $db->MediaType != 'image' )
				{
					$file = new dbObject( 'File' );
					if( $file->Load( $f->MediaID ) )
					{
						$file->Title = $this->FileName;
						$file->DateModified = date( 'Y-m-d H:i:s' );
						$file->Save();
					}
				}
				$db->Title = $this->FileName;
				$db->DateModified = date( 'Y-m-d H:i:s' );
				$db->Save();
			}
		}
	}
	
	function SaveFolder ( $fid )
	{
		if( $fid && $this->FolderName )
		{
			// Save edit to tag and name
			$db = new dbObject( 'SBookMediaRelation' );
			if( $this->CategoryID ) $db->CategoryID = $this->CategoryID;
			else $db->UserID = $this->UserID;
			$db->MediaType = 'Folder';
			$db->MediaID = $fid;
			if( $db->Load() )
			{		
				$af = new dbObject( 'Folder' );
				if( $af->Load( $fid ) )
				{
					// Save edit to arena folder
					/*$af->Name = $this->FolderName;*/
					$af->DateModified = date( 'Y-m-d H:i:s' );
					$af->Save();
				}
				
				// Save edit in SBookMediaRelations
				$db->Title = $this->FolderName;
				$db->Save();
			}
		}
	}
	
	function DeleteFile ( $fid )
	{
		// Delete file
		$db = new dbObject( 'SBookFiles' );
		if( $db->Load( $fid ) )
		{
			// Find files attached to this file
			$fi = new dbObject( 'SBookFiles' );
			$fi->Parent = $fid;
			if( $fi = $fi->Find() )
			{
				foreach( $fi as $f )
				{
					if( $f->MediaID > 0 && $f->MediaType == 'image' )
					{
						$image = new dbObject( 'Image' );
						if( $image->Load( $f->MediaID ) )
						{
							$image->Delete();
						}
					}
					else if( $f->MediaID > 0 && $f->MediaType != 'file' )
					{
						$file = new dbObject( 'File' );
						if( $file->Load( $f->MediaID ) )
						{
							$file->Delete();
						}
					}
				
					$f->Delete();
				}
			}
		
			if( $db->MediaID > 0 && $db->MediaType == 'image' )
			{
				$image = new dbObject( 'Image' );
				if( $image->Load( $db->MediaID ) )
				{
					$image->Delete();
				}
			}
			else if( $db->MediaID > 0 && $db->MediaType == 'file' )
			{
				$file = new dbObject( 'File' );
				if( $file->Load( $db->MediaID ) )
				{
					$file->Delete();
				}
			}
			else if( $db->MediaID > 0 && $db->MediaType == 'folder' )
			{
				$folder = new dbObject( 'Folder' );
				if( $folder->Load( $db->MediaID ) )
				{
					$folder->Delete();
				}
			}
		
			$db->Delete();
		}
	}
	
	function DeleteFolder ( $fid )
	{
		// Delete folder and files in the folder
		$db = new dbObject( 'SBookMediaRelation' );
		if( $this->CategoryID ) $db->CategoryID = $this->CategoryID;
		else $db->UserID = $this->UserID;
		$db->MediaType = 'Folder';
		$db->MediaID = $fid;
		if( $db->Load() )
		{
			$df = new dbObject( 'SBookFiles' );
			$df->FileFolder = $fid;
			if( $df = $df->Find() )
			{
				foreach( $df as $d )
				{
					// Find files attached to this file
					$sfi = new dbObject( 'SBookFiles' );
					$sfi->Parent = $d->ID;
					if( $sfi = $sfi->Find() )
					{
						foreach( $sfi as $sf )
						{
							if( $sf->MediaID > 0 && $sf->MediaType == 'image' )
							{
								$image = new dbObject( 'Image' );
								if( $image->Load( $sf->MediaID ) )
								{
									$image->Delete();
								}
							}
							else if( $sf->MediaID > 0 && $sf->MediaType != 'image' )
							{
								$file = new dbObject( 'File' );
								if( $file->Load( $sf->MediaID ) )
								{
									$file->Delete();
								}
							}
						
							$sf->Delete();
						}
					}
				
					$f = new dbObject( 'SBookFiles' );
					if( $f->Load( $d->ID ) )
					{
						if( $f->MediaID > 0 && $f->MediaType == 'image' )
						{
							$image = new dbObject( 'Image' );
							if( $image->Load( $f->MediaID ) )
							{
								$image->Delete();
							}
						}
						else if( $f->MediaID > 0 && $f->MediaType == 'file' )
						{
							$file = new dbObject( 'File' );
							if( $file->Load( $df->MediaID ) )
							{
								$file->Delete();
							}
						}
						else if( $f->MediaID > 0 && $f->MediaType == 'folder' )
						{
							$folder = new dbObject( 'Folder' );
							if( $folder->Load( $df->MediaID ) )
							{
								$folder->Delete();
							}
						}
					
						// Delete file
						$f->Delete();
					}
				}
			}
		
			$af = new dbObject( 'Folder' );
			if( $af->Load( $fid ) )
			{
				// Delete arena folder
				$af->Delete();
			}
		
			// Delete folder in SBookMediaRelations
			$db->Delete();
		}
	}
	
	function SortFileUp ( $fid )
	{
		if( $fid )
		{
			$db = new dbObject( 'SBookFiles' );
			if( $db->Load( $fid ) )
			{
				if( $fid != $highest->ID && $highest = $this->GetFileSortOrder( $db->Parent ) )
				{
					$db->SortOrder = floor( $db->SortOrder + 1 );
					$db->Save();
				}
			}
		}
	}
	
	function SortFileDown ( $fid )
	{
		if( $fid )
		{
			$db = new dbObject( 'SBookFiles' );
			if( $db->Load( $fid ) )
			{
				if( $fid > 0 && $db->SortOrder > 0 )
				{
					$db->SortOrder = floor( $db->SortOrder - 1 );
					$db->Save();
				}
			}
		}
	}
	
	function SortFolderUp ( $fid )
	{
		if( $fid )
		{
			$db = new dbObject( 'SBookMediaRelation' );
			if( $db->Load( $fid ) )
			{
				if( $fid != $highest->ID && $highest = $this->GetFolderSortOrder () )
				{
					$db->SortOrder = floor( $db->SortOrder + 1 );
					$db->Save();
				}
			}
		}
	}
	
	function SortFolderDown ( $fid )
	{
		if( $fid )
		{
			$db = new dbObject( 'SBookMediaRelation' );
			if( $db->Load( $fid ) )
			{
				if( $fid > 0 && $db->SortOrder > 0 )
				{
					$db->SortOrder = floor( $db->SortOrder - 1 );
					$db->Save();
				}
			}
		}
	}
	
	function Save ()
	{	
		if( $this->UserID || $this->CategoryID )
		{
			$this->CreateFileFolder();
			$this->CreateMediaRelation();
			$this->CreateFile();
		}
	}
	
	// Helper functions --------------------------------
	
	function GrabImage( $url, $path )
	{
		if( !$url || !$path ) return false;
		$file = end( explode( '/', $url ) );
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_BINARYTRANSFER,1 );
		$info = curl_getinfo( $ch );
		$raw = curl_exec( $ch );
		curl_close( $ch );
		if( $file && $imagesize = getimagesize( $url ) )
		{
			$file = explode( '.', $file );
			$file = $this->SanitizeFilename( $file[0] );
			$file = $file . '.' . end( explode( '/', $imagesize['mime'] ) );
		}
		if( file_exists( $path . $file ) )
		{
			$file = $this->uniqueFilename( $path, $file );
		}
		if( $file && $raw )
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
	
	function SaveToFile( $path, $file, $content )
	{
		if( $path && $file )
		{
			$fp = fopen( $path . $file, 'w' );
			if( fwrite( $fp, $content ) )
			{
				fclose( $fp );
				return $file;
			}
		}
		return false;
	}
	
	function SanitizeFilename( $filename )
	{
		if( !$filename ) return false;
		
		$clean_name = strtr( $filename, array( '\8A' => 'S','\8E' => 'Z','\9A' => 's','\9E' => 'z','\9F' => 'Y','\C0' => 'A','\C1' => 'A','\C2' => 'A','\C3' => 'A','\C4' => 'A','\C5' => 'A','\C7' => 'C','\C8' => 'E','\C9' => 'E','\CA' => 'E','\CB' => 'E','\CC' => 'I','\CD' => 'I','\CE' => 'I','\CF' => 'I','\D1' => 'N','\D2' => 'O','\D3' => 'O','\D4' => 'O','\D5' => 'O','\D6' => 'O','\D8' => 'O','\D9' => 'U','\DA' => 'U','\DB' => 'U','\DC' => 'U','\DD' => 'Y','\E0' => 'a','\E1' => 'a','\E2' => 'a','\E3' => 'a','\E4' => 'a','\E5' => 'a','\E7' => 'c','\E8' => 'e','\E9' => 'e','\EA' => 'e','\EB' => 'e','\EC' => 'i','\ED' => 'i','\EE' => 'i','\EF' => 'i','\F1' => 'n','\F2' => 'o','\F3' => 'o','\F4' => 'o','\F5' => 'o','\F6' => 'o','\F8' => 'o','\F9' => 'u','\FA' => 'u','\FB' => 'u','\FC' => 'u','\FD' => 'y','\FF' => 'y' ) );
		$clean_name = strtr( $clean_name, array( '\DE' => 'TH', '\FE' => 'th', '\D0' => 'DH', '\F0' => 'dh', '\DF' => 'ss', '\8C' => 'OE', '\9C' => 'oe', '\C6' => 'AE', '\E6' => 'ae', '\B5' => 'u' ) );
		$clean_name = preg_replace( array( '/\s/', '/\.[\.]+/', '/[^\w_\.\-]/' ), array( '_', '.', '' ), $clean_name );
		
		return $clean_name;
	}
	
	function uniqueFilename( $path, $filename )
	{
		if( !$path || !$filename ) return false;
		
		$parts = explode ( '.', $filename );
		// Get the file extension, if any
		$ext = '';
		if( count( $parts ) > 1 )
		{
			$ext = array_pop( $parts );
		}
		
		// Get the file name
		$filen = implode( '.', $parts );
		$depth = 0;
		
		if( file_exists ( $path . $filen . '.' . $ext ) )
		{
			while( file_exists ( $path . $filen . '.' . $ext ) )
			{
				$filen .= '_copy';
				$depth++;
				if( $depth >= 100 ) return false;
			}
		}
		return $filen . '.' . $ext;
	}
}

?>
