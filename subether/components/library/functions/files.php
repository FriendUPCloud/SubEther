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

$fid = false;

// Make some vars readable
$mainName  = $parent->folder->MainName;   // Folder name
$perms     = $parent->folder->Permission; // Permissions here
$catid	   = $parent->folder->CategoryID; // Folder CategoryID
$wid       = $parent->webuser->UserID;    // UserID for logged in user
$cid  	   = $parent->cuser->UserID;      // Library owner UserID
$maxMiniCharWidth = 17;
$maxMiniCharWidth2 = 22;
if ( isset( $_REQUEST['testchris'] ) ) die( print_r( $parent,1 ) . ' --' );
// Temporary ... TODO: FIX Access levels
$perms = ( $mainName != 'Profile' ? 'admin' : $perms );

// Check access
//$hasAccess = ( libraryAccess( $mainName, $perms, $wid, $cid ) ? true : false );
$hasAccess = ( isset( $parent->access->IsAdmin ) ? true : false );

$view = ( isset( $_POST['view'] ) ? $_POST['view'] : false );

// Check if mid (folderid) is set or fid (file id)
if( isset( $_POST[ 'mid' ] ) || isset( $_POST[ 'fid' ] ) )
{
	$fid = ( $_POST['fid'] > 0 ? ( 'fid_' . $_POST['fid'] ) : ( 'mid_' . $_POST['mid'] ) );
}

// Get fid from route if not found by post
if( !$fid && $parent->url && is_numeric( end( $parent->url ) ) )
{
	$fid = ( 'mid_' . ( $_POST['mid'] = trim( end( $parent->url ) ) ) );
}

// TODO: Fix this, it doesn't work
// Check if user or group has default folders if not make them.
if ( !isset( $_SESSION['library_check_defcats'] ) || !strstr( $_SESSION['library_check_defcats'], $parent->folder->CategoryID ) )
{
	$_SESSION['library_check_defcats'] = ( $_SESSION['library_check_defcats'] ? ( $_SESSION['library_check_defcats'] . ',' . $parent->folder->CategoryID ) : $parent->folder->CategoryID );
	
	$lib = new Library ();
	$lib->UserID = $parent->cuser->UserID;
	if( strtolower( $parent->folder->MainName ) != 'profile' )
	{
		$lib->CategoryID = $parent->folder->CategoryID;
	}
	$lib->GetFolders ();
}

// Set root folder
$root = new dbFolder();
$root = $root->getRootFolder();



$usrs = ( $webuser && isset( $webuser->ContactID ) ? getUserContactsID( $webuser->ContactID, true ) : false );
$usrs = ( $usrs && is_array( $usrs ) ? implode( ',', $usrs ) : ( $webuser ? $webuser->ID : false ) );

$acat = ( $webuser && isset( $webuser->ContactID ) ? CategoryAccess( $webuser->ContactID, false, -1, 'IsAdmin' ) : false );
$acat = ( $acat && isset( $acat['CategoryID'] ) ? $acat['CategoryID'] : false );

$ucat = ( $webuser && isset( $webuser->ContactID ) ? CategoryAccess( $webuser->ContactID, false, -1 ) : false );
$ucat = ( $ucat && isset( $ucat['CategoryID'] ) ? $ucat['CategoryID'] : false );



// Fetch home folder
$hq = '
	SELECT 
		mf.ID, 
		mf.Name, 
		mf.Parent, 
		mf.DiskPath, 
		mf.SortOrder, 
		mf.Access, 
		mf.DateCreated, 
		mf.DateModified, 
		mr.Title AS Name, 
		mr.UserID, 
		mr.CategoryID, 
		mr.SortOrder, 
		ca.Name AS GroupName, 
		co.Username AS UserName,
		co.Display AS UserDisplay,
		co.Firstname,
		co.Middlename,
		co.Lastname
	FROM 
		Folder rf, 
		Folder hf, 
		Folder mf, 
		SBookMediaRelation mr 
			LEFT JOIN `SBookCategory` ca ON
			(
				ca.ID = mr.CategoryID 
			) 
			LEFT JOIN `SBookContact` co ON
			(
				co.UserID = mr.UserID 
			)
	WHERE 
			rf.Parent = \'' . $root->ID . '\' 
		AND hf.Parent = rf.ID 
		AND mf.Parent = hf.ID 
		AND mr.MediaID = mf.ID 
		AND mr.MediaType = "Folder" 
		' . ( strtolower( $mainName ) != 'profile' && $catid > 0 ? '
		AND mr.CategoryID = \'' . $catid . '\'
		AND mr.UserID = "0" 
		' : '
		AND 
		( 
			(		
					mr.UserID = \'' . $cid . '\' 
				AND mr.CategoryID = "0" 
			) 
			OR
			(
					mr.CategoryID IN (' . ( $ucat ? $ucat : 'NULL' ) . ') 
				AND mr.UserID = "0" 
			)
		)
		' ) . ' 
	GROUP BY 
		mf.ID 
	ORDER BY 
		mr.CategoryID ASC, 
		mr.SortOrder ASC, 
		mf.ID ASC 
';


// Fetch files and images
$fq = '
	SELECT * FROM
	(
		(
			SELECT 
				i.ID,
				i.UniqueID,
				i.Title,
				i.Filename,
				i.Description,
				i.Tags, 
				i.DateCreated,
				i.DateModified,
				i.SortOrder,
				i.Filetype, 
				i.UserID,
				i.CategoryID,
				i.Filesize,
				0 AS Width,
				0 AS Height,
				i.FileFolder AS FolderID,
				i.Access AS FileAccess,
				i.ModID,
				i.IsEdit,
				i.Verified,
				f.Name AS FolderName,
				f.DiskPath AS FolderPath,
				f.Access AS FolderAccess,
				f.UserID AS FolderUserID,
				f.CategoryID AS FolderCategoryID,
				c.Name AS CategoryName,
				c.Privacy AS CategoryPrivacy,
				c.IsSystem AS CategorySystem, 
				"file" AS MediaType 
			FROM 
				`Folder` f, 
				`File` i 
					LEFT JOIN `SBookCategory` c ON
					(
						i.CategoryID = c.ID
					)
			WHERE 
					i.NodeID = "0" 
				AND i.FileFolder = f.ID
				AND f.ID IN ( !FolderIDS! ) 
		) 
		UNION 
		(
			SELECT 
				i.ID,
				i.UniqueID,
				i.Title,
				i.Filename,
				i.Description,
				i.Tags, 
				i.DateCreated,
				i.DateModified,
				i.SortOrder,
				i.Filetype, 
				i.UserID,
				i.CategoryID,
				i.Filesize,
				i.Width,
				i.Height,
				i.ImageFolder AS FolderID,
				i.Access AS FileAccess,
				i.ModID,
				i.IsEdit,
				i.Verified,
				f.Name AS FolderName,
				f.DiskPath AS FolderPath,
				f.Access AS FolderAccess,
				f.UserID AS FolderUserID,
				f.CategoryID AS FolderCategoryID,
				c.Name AS CategoryName,
				c.Privacy AS CategoryPrivacy,
				c.IsSystem AS CategorySystem, 
				"image" AS MediaType 
			FROM 
				`Folder` f, 
				`Image` i 
					LEFT JOIN `SBookCategory` c ON
					(
						i.CategoryID = c.ID
					)
			WHERE 
					i.NodeID = "0" 
				AND i.ImageFolder = f.ID
				AND f.ID IN ( !FolderIDS! ) 
		)
	)
	z
	WHERE
		' . ( IsSystemAdmin() ? '
		(
				z.FileAccess != "2"
			AND z.FolderAccess != "2" 
		)
		' : '
		(
			
			/* --- Public / Members Access --- */
			
			(
				(
						z.FileAccess = "0"
					AND z.FolderAccess <= z.FileAccess 
				)
				AND
				(
					
					/* --- Profile / Other Access --- */
					
					(
						z.CategoryID = "0"
					)
					OR
					(
							z.CategoryID > 0
						AND z.CategorySystem = "1" 
					)
					
					/* --- Group Access --- */
					
					OR 
					(
							z.CategoryID > 0
						AND z.CategoryPrivacy = "OpenGroup" 
					)
					OR 
					(
							z.CategoryID > 0
						AND z.CategoryPrivacy = "ClosedGroup" 
						AND z.FolderName = "Cover Photos" 
					)
					OR
					(
							z.CategoryID > 0
						AND z.CategoryID IN ( !UserCats! ) 
					)
				)
			)
			
			/* --- Contact Access --- */
			
			OR
			(
					z.FileAccess = "1"
				AND z.UserID > 0 
				AND z.UserID IN ( !UserIDS! )
				AND z.FolderAccess <= z.FileAccess 
			)
			
			/* --- File Owner Access --- */
			
			OR
			(
					z.UserID = !UserID!
				AND z.UserID > 0 
			)
			
			/* --- Admin Access --- */
			
			OR
			(
					z.FileAccess != "2"
				AND z.CategoryID > 0 
				AND z.CategoryID IN ( !AdminCats! ) 
			)
			
			/* --- No Owner / All Access --- */
			
			OR
			(
					z.UserID = "0" 
				AND z.CategoryID = "0" 
			)
			
		)' ) . '
	ORDER BY
		z.SortOrder ASC,
		z.DateCreated ASC
';

// TODO: Make sure folders don't dissapear when your the owner of the library :)

// Fetch sub folders
$sq = '
	SELECT
		f.ID,
		f.UniqueID,
		f.Name, 
		f.Parent, 
		f.DiskPath, 
		f.SortOrder,
		f.UserID,
		f.CategoryID,
		f.Access, 
		f.DateCreated, 
		f.DateModified, 
		r.Title AS Name,
		r.SortOrder,
		c.Name AS CategoryName,
		c.Privacy AS CategoryPrivacy,
		c.IsSystem AS CategorySystem 
	FROM 
		SBookMediaRelation r, 
		Folder f 
			LEFT JOIN SBookCategory c ON 
			(
				f.CategoryID = c.ID 
			) 
	WHERE 
			f.Parent IN ( !SubFolderIDS! ) 
		AND r.MediaID = f.ID
		AND
		' . ( IsSystemAdmin() ? '
		(
			f.Access != "2"
		)
		' : '
		(
			
			/* --- Public / Members Access --- */
			
			(
				(
					f.Access = "0"
				)
				AND
				(
					
					/* --- Profile / Other Access --- */
					
					(
						f.CategoryID = "0"
					)
					OR
					(
							f.CategoryID > 0
						AND c.IsSystem = "1" 
					)
					
					/* --- Group Access --- */
					
					OR 
					(
							f.CategoryID > 0
						AND c.Privacy = "OpenGroup" 
					)
					OR 
					(
							f.CategoryID > 0	
						AND c.Privacy = "ClosedGroup" 
						AND f.Name = "Cover Photos" 
					)
					OR
					(
							f.CategoryID > 0
						AND f.CategoryID IN ( !UserCats! ) 
					)
				)
			)
			
			/* --- Contact Access --- */
			
			OR
			(
					f.Access = "1"
				AND f.UserID > 0 
				AND f.UserID IN ( !UserIDS! ) 
			)
			
			/* --- File Owner Access --- */
			
			OR
			(
					f.UserID = !UserID!
				AND f.UserID > 0 
			)
			
			/* --- Admin Access --- */
			
			OR
			(
					f.Access != "2"
				AND f.CategoryID > 0 
				AND f.CategoryID IN ( !AdminCats! ) 
			)
			
			/* --- No Owner / All Access --- */
			
			OR
			(
					f.UserID = "0" 
				AND f.CategoryID = "0" 
			)
			
		)' ) . ' 
	ORDER BY 
		r.SortOrder ASC,
		f.ID ASC 
';



// TODO: Add support for admins of groups and members of groups and super admin of the system

$fq = str_replace( '!UserID!', ( isset( $webuser ) && $webuser ? ( '\'' . $webuser->ID . '\'' ) : 'NULL' ), $fq );
$fq = str_replace( '!UserIDS!', ( isset( $usrs ) && $usrs ? $usrs : 'NULL' ), $fq );
$fq = str_replace( '!AdminCats!', ( isset( $acat ) && $acat ? $acat : 'NULL' ), $fq );
$fq = str_replace( '!UserCats!', ( isset( $ucat ) && $ucat ? $ucat : 'NULL' ), $fq );

$sq = str_replace( '!UserID!', ( isset( $webuser ) && $webuser ? ( '\'' . $webuser->ID . '\'' ) : 'NULL' ), $sq );
$sq = str_replace( '!UserIDS!', ( isset( $usrs ) && $usrs ? $usrs : 'NULL' ), $sq );
$sq = str_replace( '!AdminCats!', ( isset( $acat ) && $acat ? $acat : 'NULL' ), $sq );
$sq = str_replace( '!UserCats!', ( isset( $ucat ) && $ucat ? $ucat : 'NULL' ), $sq );

$library = array();

//die( $hq . ' --' );
if( $folders = $database->fetchObjectRows ( $hq, false, 'components/library/functions/files.php' ) )
{
	$mfids = array(); $folds = array(); $flids = array(); $files = array(); $first = 0;
	
	foreach( $folders as $mf )
	{
		$mf->IsGroup = ( isset( $mf->GroupName ) ? '1' : '0' );
		
		if( $mf->IsGroup == 0 && !$first )
		{
			$first = $mf->ID;
		}
		
		$mfids[] = $mf->ID;
	}

	
	
	// Prebuffer all sub folders
	if( $mfids && $flds = $database->fetchObjectRows ( str_replace( '!SubFolderIDS!', implode( ',', $mfids ), $sq ), false, 'components/library/functions/files.php' ) )
	{
		$iii = 0; $defset = 0;
		
		foreach( $flds as $sf )
		{
			/*// Temporary because of unset userid or categoryid
			if ( !$sf->UserID && !$sf->CategoryID )
			{
				$sf->Access = 0;
			}*/
			
			// If no main folder is defined show the first
			if( !$_POST['mid'] && !$_POST['fid'] && $defset == 0 && $sf->Parent == $first )
			{
				$flids[$sf->ID] = $sf->ID; $defset = 1;
			}
			// Else if main folder is defined show only this one
			else if( $_POST['mid'] && $_POST['mid'] == $sf->ID )
			{
				$flids[$sf->ID] = $sf->ID;
			}
			
			// If index is defined only show main folder with files indexed
			if( !isset( $_REQUEST['index'] ) || ( isset( $_REQUEST['index'] ) && $_POST['mid'] == $sf->ID ) )
			{
				$folds[$sf->Parent] = ( isset( $folds[$sf->Parent] ) ? $folds[$sf->Parent] : array() );
				$folds[$sf->Parent][] = $sf;
			}
			
			//$flids[$sf->ID] = $sf->ID;
			
			$iii++;
		}
	}
	
	// Prebuffer all images and files
	if( $data = $database->fetchObjectRows( str_replace( '!FolderIDS!', implode( ',', $flids ), $fq ), false, 'components/library/functions/files.php' ) )
	{
		foreach( $data as $da )
		{
			/*// Temporary because of unset userid or categoryid
			if ( !$da->UserID && !$da->CategoryID )
			{
				$da->FileAccess = 0;
			}*/
			
			if ( $da->MediaType && $da->Filename )
			{
				$da->DownloadUrl = ( BASE_URL . 'secure-files/' . ( $da->MediaType == 'image' ? 'images' : 'files' ) . '/' . ( $da->UniqueID ? $da->UniqueID : $da->ID ) . ( $webuser->ID > 0 && $webuser->GetToken() ? ( '/' . $webuser->GetToken() ) : '' ) . '/' . $da->Filename );
				$da->ThumbUrl = ( BASE_URL . 'secure-files/' . ( $da->MediaType == 'image' ? 'images' : 'files' ) . '/' . ( $da->UniqueID ? $da->UniqueID : $da->ID ) . '/' );
				
				if ( !FileExists( $da->ThumbUrl ) )
				{
					$da->ThumbUrl = false;
				}
			}
			
			$files[$da->FolderID] = ( isset( $files[$da->FolderID] ) ? $files[$da->FolderID] : array() );
			$files[$da->FolderID][] = $da;
		}
	}
	
	
	
	foreach( $folders as $key=>$mfolder )
	{
		$mfolder->HasAccess = ( ( isset( $parent->access->IsAdmin ) && !$mfolder->CategoryID ) || ( isset( $acat ) && strstr( ','.$acat.',', ','.$mfolder->CategoryID.',' ) ) ? '1' : '0' );
		
		if( $mfolder->UserDisplay )
		{
			switch ( $mfolder->UserDisplay )
			{
				case 1:
					$mfolder->UserName = trim( $mfolder->Firstname . ' ' . $mfolder->Middlename . ' ' . $mfolder->Lastname );
					break;
				case 2:
					$mfolder->UserName = trim( $mfolder->Firstname . ' ' . $mfolder->Lastname );
					break;
				case 3:
					$mfolder->UserName = trim( $mfolder->Lastname . ' ' . $mfolder->Firstname );
					break;
			}
		}
		
		$mfolder->LibraryName = ( isset( $mfolder->GroupName ) ? $mfolder->GroupName : $mfolder->UserName );
		$mfolder->IsGroup = ( isset( $mfolder->GroupName ) ? '1' : '0' );
		
		$mfolder->SubFolders = isset( $folds[$mfolder->ID] ) ? $folds[$mfolder->ID] : false;
		
		if( $mfolder->Name == 'Theme' && $mfolder->IsGroup == '1' )
		{
			unset( $folders[$key] );
		}
		
		if( $mfolder->SubFolders )
		{
			$ii = 0;
			
			foreach( $mfolder->SubFolders as $sfolder )
			{
				$sfolder->Files = array(); $tmfiles = array();
				
				// If we have a special case like theme ...
				
				$sfolder->HasAccess = $mfolder->HasAccess;
				
				if( $mfolder->Name == 'Theme' && $sfolder->Name == 'Default' && $mfolder->IsGroup != '1' && defined( 'NODE_THEME' ) )
				{
					$thm = new Library ( 'Folder' );
					$thm->UserID = $parent->cuser->UserID;
					$thm->ParentFolder = 'Theme';
					$thm->FolderName = 'Default';
					
					if( $thm->Load( $sfolder->ID ) )
					{
						$thm->UpdateDatabaseFiles();
						
						if( $flds = $thm->OpenFolder( BASE_DIR . '/subether/themes/', NODE_THEME ) )
						{
							foreach( $flds as $fld )
							{
								if( $fld->isfile )
								{
									$tmfiles[$fld->name] = $fld;
								}
							}
						
							if( $defs = $thm->OpenFolder( BASE_DIR . '/' . $mfolder->DiskPath, 'default' ) )
							{
								foreach( $defs as $def )
								{
									if( isset( $tmfiles[$def->name] ) )
									{
										unset( $tmfiles[$def->name] );
									}
								}
							}
							
							if( isset( $tmfiles ) && $tmfiles )
							{
								foreach( $tmfiles as $tm )
								{
									$fil = new stdClass();
									$fil->Title = $tm->name;
									$fil->Filename = $tm->name;
									$fil->Filetype = $tm->type;
									$fil->Filesize = $tm->size;
									$fil->FolderID = $sfolder->ID;
									$fil->FilePath = str_replace( BASE_DIR . '/', '', $tm->dir ) . $tm->name;
									
									if( !isset( $files[$sfolder->ID] ) )
									{
										$files[$sfolder->ID] = array();
									}
									
									$files[$sfolder->ID][] = $fil;
								}
							}
						}
					}
				}
				
				if( isset( $files[$sfolder->ID] ) )
				{
					foreach( $files[$sfolder->ID] as $file )
					{
						//if( $webuser->ID == 81 && $file->ID == 3441 ) die( print_r( $file,1 ) . ' --' );
						
						$file->HasAccess = $sfolder->HasAccess;
						
						$file->Parse = getMetaFileData( $file->FolderPath, $file->Filename );
						
						// If we have parsed data set set new title to filename
						if( is_object( $file->Parse ) && $file->Parse->Title && $file->Parse->Type )
						{
							if( $file->Title == $file->Filename )
							{
								$file->Title = str_replace( ' ', '_', $file->Parse->Title ) . '.' . $file->Parse->Type;
							}
							$file->MediaType = $file->Parse->Type;
						}
						
						// Set mediatype based on filetype if found
						if( libraryMedia( $file->Filetype ) )
						{
							$file->MediaType = libraryMedia( $file->Filetype );
						}
						
						// If we have parsed data and requested fileid is the same as fileid render file content
						if( $file->Parse && $file->Parse->Media && $_POST[ 'fid' ] == $file->ID && $file->ID > 0 )
						{
							switch( $file->Parse->Media )
							{
								// Audio ---------------------------------------
								case 'audio':
									$file->Content = embedAudio( $file->Parse->Url, 495, 480 );
									break;
								// Video ---------------------------------------
								case 'video':
									$file->Content = embedVideo( $file->Parse->Url, 495, 480 );
									break;
								// File ----------------------------------------
								case 'file':
									$file->Content = embedPDF( $file->Parse->Url, 495, 480 );
									break;
								// Youtube -------------------------------------
								case 'youtube':
									$file->Content = embedYoutube( $file->Parse->Url, 495, 480 );
									break;
								// Vimeo ---------------------------------------
								case 'vimeo':
									$file->Content = embedVimeo( $file->Parse->Url, 495, 480 );
									break;
								// Livestream ----------------------------------
								case 'livestream':
									$file->Content = embedLivestream( $file->Parse->Url, 495, 280 );
									break;
								// Remote site data ----------------------------
								case 'site':
									//$file->Content = embedMetaFileData( $file->Parse->Url, $file->Parse->Message, $file->Parse->Title, $file->Parse->Leadin, ( $file->FolderPath . $file->Filename ), $file->Width, $file->Height, 472, 480 );
									$file->Content = embedMetaFileData( $file->Parse->Url, $file->Parse->Message, $file->Parse->Title, $file->Parse->Leadin, $file->ThumbUrl, $file->Width, $file->Height, 472, 480 );
									break;
								// Meta data -----------------------------------
								case 'meta':
									//$file->Content = embedMetaFileData( $file->Parse->Url, $file->Parse->Message, $file->Parse->Title, $file->Parse->Leadin, ( $file->FolderPath . $file->Filename ), $file->Width, $file->Height, 472, 480 );
									$file->Content = embedMetaFileData( $file->Parse->Url, $file->Parse->Message, $file->Parse->Title, $file->Parse->Leadin, $file->ThumbUrl, $file->Width, $file->Height, 472, 480 );
									break;
							}
							
							$file->Current = 1;
							$sfolder->Open = 1;
							$folders['content'] = $file;
							$current = true;
						}
						// If we have requested fileid and it's the same as fileid render file content
						if( ( $_POST[ 'fid' ] == $file->ID && $file->ID > 0 ) || ( isset( $_POST['fpath'] ) && strstr( $_POST['fpath'], $file->Filename ) ) )
						{
							switch( libraryMedia( $file->Filetype ) )
							{
								// Content -----------------------------------
								case 'content':
									$lib = new Library ( 'File' );
									if( !$file->ID && isset( $_POST['fpath'] ) )
									{
										$file->Content = $lib->OpenFile( $_POST['fpath'] );
									}
									elseif( $lib->Load( $file->ID ) )
									{
										$file->Content = $lib->GetFileContent();
									}
									break;
								// Pdf ---------------------------------------
								case 'pdf':
									//$file->Content = embedPDF( ( $file->FolderPath . $file->Filename ), 495, 480 );
									$file->Content = embedPDF( $file->ThumbUrl, 495, 480 );
									break;
								// Video -------------------------------------
								case 'video':
									//$file->Content = embedVideo( ( $file->FolderPath . $file->Filename ), 495, 480, 'video' );
									$file->Content = embedVideo( ( $file->ThumbUrl . $file->Filename ), 495, 480, 'video' );
									break;
								// Audio -------------------------------------
								case 'audio':
									//$file->Content = embedAudio( ( $file->FolderPath . $file->Filename ), 495, 480 );
									$file->Content = embedAudio( $file->ThumbUrl, 495, 480 );
									break;
							}
							
							$file->Current = 1;
							$sfolder->Open = 1;
							$folders['content'] = $file;
							$current = true;
						}
						
						$sfolder->Files[] = $file;
					}
				}
				//if( $tmfiles ) die( print_r( $sfolder,1 ) . ' -- ' . print_r( $files,1 ) . ' [] ' . print_r( $tmfiles,1 ) );
				// Set current folder based on variable data or by first in list
				if( !$current && ( ( !$_POST['mid'] && !$_POST['fid'] && $ii == 0 ) || ( $_POST['mid'] > 0 && $_POST['mid'] == $sfolder->ID ) ) )
				{
					$sfolder->Current = 1;
					$current = true;
					$folders['thumbs'] = $sfolder->Files;
					$folders['folderid'] = $sfolder->ID;
				}
				
				$ii++;
			}
		}
	}
	
	foreach( $folders as $mf )
	{
		if( isset( $library[$mf->CategoryID] ) && isset( $library[$mf->CategoryID]->MainFolders ) )
		{
			$library[$mf->CategoryID]->MainFolders[] = $mf;
		}
		else
		{
			$obj = new stdClass();
			$obj->Name = ( isset( $mf->GroupName ) ? $mf->GroupName : $mf->UserName );
			$obj->IsGroup = ( isset( $mf->GroupName ) ? '1' : '0' );
			$obj->MainFolders = array( $mf );
			
			$library[$mf->CategoryID] = $obj;
		}
	}
}

if( isset( $_REQUEST['dbg'] ) ) die( print_r( $folders,1 ) . ' [] ' . print_r( $flids,1 ) . ' [] ' . print_r( $files,1 ) . ' [] ' . print_r( $folds,1 ) );

//die( print_r( $folders,1 ) . ' -- ' . print_r( $_POST,1 ) );

// Mobile mode isn't web, it's paper based

if( $parent->agent != 'web' && 1!=1 )
{
	// --- Mobile view list ----------------------------------------------------- //
	
	include ( $cbase . '/functions/mobile.php' );
	
	// For editing text content ---------------------------------------------------
	if( isset( $folders['content'] ) || !$folders )
	{
		$str = '<div class="editview"><div id="ContentEditor" ' . 
			( $folders['content']->HasAccess && !$folders['content']->Parse ? 'contenteditable="true" fileid="' . 
			$folders['content']->ID . '" folderid="' . $folders['content']->FolderID . '" filename="' . $folders['content']->Filename . '" onkeyup="IsEditing(' . $folders['content']->ID . ')"' : '' ) . 
			' class="textarea">' . $folders['content']->Content . '</div>';
		$obj = $folders['content'];
	}
}
else
{
	// --- Directory list ------------------------------------------------------- //
	
	include ( $cbase . '/functions/directory.php' );
	
	// --- Control buttons ------------------------------------------------------ //
	
	include ( $cbase . '/functions/buttons.php' );
	
	// --- Content / Thumbs ----------------------------------------------------- //
	
	$istr = '<div class="library_component thumbview">';
	
	if( $hasAccess )
	{
		$istr .= '<div class="fileupload" onclick="ge(\'FilesUploadBtn\').click()"><div></div></div>';
	}
	
	//if( $_POST['fid'] == 706 ) die( print_r( $folders,1 ) . ' --' );
	
	// For editing text content ---------------------------------------------------
	if( isset( $folders['content'] ) || !$folders )
	{
		//die( print_r( $folders['content'],1 ) . ' --' );
		if( $folders['content']->MediaType == 'content' && in_array( $folders['content']->Filetype, array( 'css' ) ) )
		{
			$istr = $str = '<div class="editview editor"><textarea name="ContentEditor" id="TextAreaEditor"' . ( $folders['content']->HasAccess && !$folders['content']->Parse ? ( 'fileid="' . 
			$folders['content']->ID . '" folderid="' . $folders['content']->FolderID . '" filename="' . $folders['content']->Filename . '" onkeyup="IsEditing(' . $folders['content']->ID . ')"' ) : '' ) . '>' . $folders['content']->Content . '</textarea>';
		}
		else if( $folders['content']->MediaType == 'content' )
		{
			$istr = $str = '<div class="editview editor"><textarea name="ContentEditor" id="ContentEditor"' . ( $folders['content']->HasAccess && !$folders['content']->Parse ? ( 'fileid="' . 
			$folders['content']->ID . '" folderid="' . $folders['content']->FolderID . '" filename="' . $folders['content']->Filename . '" onkeyup="IsEditing(' . $folders['content']->ID . ')"' ) : '' ) . '>' . $folders['content']->Content . '</textarea>';
		}
		else
		{
			$istr = $str = '<div class="editview"><div id="ContentEditor" class="textarea">' . $folders['content']->Content . '</div>';
		}
		
		$obj = $folders['content'];
	}
	// For showing listview -------------------------------------------------------
	else if( $view == 1 && isset( $folders['thumbs'] ) )
	{
		$istr = '';
		
		include ( $cbase . '/functions/listview.php' );
	}
	// For showing thumbnails -----------------------------------------------------
	else if( isset( $folders['thumbs'] ) )
	{
		include ( $cbase . '/functions/thumbs.php' );
	}
	
	$istr .= '<div class="clearboth" style="clear:both"></div>';
	$istr .= '</div>';
}

if( $Component )
{
	$Component->FolderID = ( isset( $folders['folderid'] ) ? $folders['folderid'] : '' );
}

if( $folders && $_REQUEST['component'] == 'library' && !isset( $_REQUEST['global'] ) && isset( $_REQUEST[ 'bajaxrand' ] ) )
{
	die( 'ok<!--separate-->' . $dstr . '<!--separate-->' . ( isset( $folders['folderid'] ) ? $folders['folderid'] : '' ) . '<!--separate-->' . $istr . '<!--separate-->' . ( isset( $folders['content'] ) ? $folders['content']->Content : '' ) );
}

?>
