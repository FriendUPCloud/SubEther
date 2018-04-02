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

$limit = 50;

$keywords = ( isset( $_REQUEST[ 'q' ] ) && $_REQUEST[ 'q' ] != '' ? str_replace( ' ', '+', $_REQUEST[ 'q' ] ) : '' );
$_REQUEST[ 'p' ] > 1 ? $page = $_REQUEST[ 'p' ] : $page = 1;

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
	'url'=>'url/url-128_32.png',
	'mp3'=>'mp3/mp3-128_32.png', 
	'txt'=>'text/text-128_32.png'
);

$time_start = microtime_float();

$mstr .= '<div class="network">';

// NEW QUERY -----------------------------------------------------------------------------------------------

//$query = '
//	SELECT * FROM 
//	( 
//		( 
//			SELECT
//				i.ID AS ID,
//				i.UniqueID AS UniqueID,
//				i.Title AS Title,
//				i.Filename AS Filename,
//				i.Description AS Description, 
//				i.Filetype AS Filetype,
//				i.Filesize AS Filesize,
//				i.DateCreated AS DateCreated, 
//				f.DiskPath AS FolderPath, 
//				f.Name AS FolderName, 
//				f.ID AS FolderID, 
//				f.UserID AS UserID, 
//				f.CategoryID AS CategoryID, 
//				c.ID AS ContactID, 
//				c.ImageID AS ImageID, 
//				c.Username AS Username,
//				"" AS Data,
//				"" AS `IsGroup`, 
//				"file" AS MediaType 
//			FROM 
//				`File` i, 
//				`Folder` f 
//				LEFT JOIN `SBookContact` c ON ( f.UserID = c.UserID AND f.UserID > 0 ) 
//			WHERE 
//				i.NodeID = "0" 
//				AND f.ID = i.FileFolder
//				AND i.Filetype = "pdf" 
//		) 
//		/*UNION 
//		( 
//			SELECT
//				i.ID AS ID,
//				i.UniqueID AS UniqueID,
//				i.Title AS Title,
//				i.Filename AS Filename,
//				i.Description AS Description, 
//				i.Filetype AS Filetype,
//				i.Filesize AS Filesize,
//				i.DateCreated AS DateCreated, 
//				f.DiskPath as FolderPath, 
//				f.Name AS FolderName, 
//				f.ID AS FolderID, 
//				f.UserID AS UserID, 
//				f.CategoryID AS CategoryID, 
//				c.ID AS ContactID, 
//				c.ImageID AS ImageID, 
//				c.Username AS Username,
//				"" AS Data,
//				"" AS `IsGroup`, 
//				"image" AS MediaType
//			FROM 
//				`Image` i, 
//				`Folder` f 
//				LEFT JOIN `SBookContact` c ON ( f.UserID = c.UserID AND f.UserID > 0 ) 
//			WHERE 
//				i.NodeID = "0" 
//				AND f.ID = i.ImageFolder 
//		)*/
//		/*UNION 
//		( 
//			SELECT 
//				f.ID AS ID,
//				"" AS UniqueID,
//				f.Name AS Title,
//				"" AS Filename,
//				f.Description AS Description, 
//				"" AS Filetype,
//				"" AS Filesize,
//				f.DateCreated AS DateCreated, 
//				f.DiskPath AS FolderPath, 
//				"" AS FolderName, 
//				f.ID AS FolderID, 
//				f.UserID AS UserID, 
//				f.CategoryID AS CategoryID, 
//				c.ID AS ContactID, 
//				c.ImageID AS ImageID, 
//				c.Username AS Username,
//				"" AS Data,
//				"" AS `IsGroup`, 
//				"folder" AS MediaType
//			FROM 
//				`Folder` f 
//				LEFT JOIN `SBookContact` c ON ( f.UserID = c.UserID AND f.UserID > 0 ) 
//			WHERE 
//				f.NodeID = "0" 
//		)*/
//		UNION 
//		( 
//			SELECT 
//				c.ID AS ID,
//				"" AS UniqueID,
//				c.Username AS Title,
//				i.Filename AS Filename,
//				c.Work AS Description, 
//				i.Filetype AS Filetype,
//				i.Filesize AS Filesize,
//				c.DateCreated AS DateCreated, 
//				f.DiskPath as FolderPath, 
//				f.Name AS FolderName, 
//				f.ID AS FolderID, 
//				c.UserID AS UserID, 
//				f.CategoryID AS CategoryID, 
//				c.ID AS ContactID, 
//				c.ImageID AS ImageID, 
//				"" AS Username,
//				"" AS Data,
//				"" AS `IsGroup`, 
//				"contact" AS MediaType
//			FROM 
//				`SBookContact` c
//				LEFT JOIN `Image` i ON ( c.ImageID > 0 AND i.ID = c.ImageID )
//				LEFT JOIN `Folder` f ON ( i.ImageFolder > 0 AND f.ID = i.ImageFolder ) 
//			WHERE 
//				c.NodeID = "0" 
//		)
//		/*UNION 
//		( 
//			SELECT 
//				g.ID AS ID,
//				"" AS UniqueID,
//				g.Name AS Title,
//				"" AS Filename,
//				g.Description AS Description, 
//				"" AS Filetype,
//				"" AS Filesize,
//				"" AS DateCreated, 
//				"" as FolderPath, 
//				"" AS FolderName, 
//				"" AS FolderID, 
//				"" AS UserID, 
//				"" AS CategoryID, 
//				"" AS ContactID, 
//				"" AS ImageID, 
//				"" AS Username,
//				"" AS Data,
//				"" AS `IsGroup`, 
//				"group" AS MediaType
//			FROM 
//				`SBookCategory` g 
//			WHERE 
//					g.NodeID = "0" 
//				AND g.Privacy != "SecretGroup" 
//				AND g.Type = "SubGroup" 
//				AND g.IsSystem = "0" 
//		)*/
//		UNION 
//		( 
//			SELECT 
//				m.ID AS ID,
//				"" AS UniqueID,
//				m.Subject AS Title,
//				i.Filename AS Filename,
//				m.Message AS Description, 
//				i.Filetype AS Filetype,
//				i.Filesize AS Filesize,
//				m.Date AS DateCreated, 
//				f.DiskPath as FolderPath, 
//				f.Name AS FolderName, 
//				f.ID AS FolderID, 
//				c.UserID AS UserID, 
//				m.CategoryID AS CategoryID, 
//				c.ID AS ContactID, 
//				c.ImageID AS ImageID, 
//				c.Username AS Username,
//				m.Data AS Data,
//				( m.CategoryID != !WallID! ) AS `IsGroup`, 
//				"wall" AS MediaType
//			FROM 
//				`SBookMessage` m, 
//				`SBookCategory` g, 
//				`SBookContact` c 
//					LEFT JOIN `Image` i ON ( c.ImageID > 0 AND i.ID = c.ImageID ) 
//					LEFT JOIN `Folder` f ON ( i.ImageFolder > 0 AND f.ID = i.ImageFolder ) 
//			WHERE 
//					m.NodeID = "0" 
//				AND m.ParentID = "0" 
//				AND m.CategoryID > 0 
//				AND g.ID = m.CategoryID 
//				AND c.ID = m.SenderID 
//				AND ( ( m.Access = "0" AND g.Privacy = "OpenGroup" ) 
//				OR  (   m.Access = "0" AND g.ID = !WallID! ) 
//				' . ( $webuser->ContactID > 0 ? '
//				OR  (   m.Access = "2" AND m.SenderID = !WebUserID! ) 
//				OR  (   m.Access = "0" AND g.ID IN ( !CategoryIDS! ) AND g.ID != !WallID! ) 
//				OR  (   m.Access = "1" AND g.ID IN ( !CategoryIDS! ) AND m.SenderID IN ( !ContactIDS! ) ) ) 
//				' : ')' ) . '
//		) 
//	) z 
//	' . ( $keywords != '' ? '
//	WHERE
//		(  z.Title LIKE "%' . $keywords . '%"
//		OR z.Description LIKE "%' . $keywords . '%"
//		OR z.Data LIKE "%' . $keywords . '%" ) 
//	' : '' ) . '
//	ORDER BY 
//		z.DateCreated DESC, z.Title ASC 
//	LIMIT ' . $limit . ' 
//';

//$guci = getUserContactsID( $webuser->ContactID );
//$gucids = $guci ? implode( ',', $guci ) : $webuser->ContactID;
//$uci = getUserGroupsID( $webuser->ID );
//$uci[] = getWallID();
//$ucids = $uci ? implode( ',', $uci ) : false;

//$query = str_replace( '!WallID!', getWallID(), $query );
//$query = str_replace( '!WebUserID!', $webuser->ContactID, $query );
//$query = str_replace( '!CategoryIDS!', $ucids, $query );
//$query = str_replace( '!ContactIDS!', $gucids, $query );

//die( $query . ' ..' );

$query = '
	SELECT * FROM 
	( 
		(
			SELECT 
				i.ID AS ID,
				i.UniqueID AS UniqueID,
				i.Title AS Title,
				i.Filename AS Filename,
				i.Description AS Description, 
				i.Filetype AS Filetype,
				i.Filesize AS Filesize,
				i.DateCreated AS DateCreated, 
				f.DiskPath AS FolderPath, 
				f.Name AS FolderName, 
				f.ID AS FolderID, 
				f.UserID AS UserID, 
				f.CategoryID AS CategoryID, 
				u.ID AS ContactID, 
				u.ImageID AS ImageID, 
				u.Username AS Username,
				"" AS Data,
				"" AS `IsGroup`, 
				"file" AS MediaType 
			FROM 
				`Folder` f 
					LEFT JOIN `SBookContact` u ON
					(
							f.UserID = u.UserID
						AND f.UserID > 0
					), 
				`File` i 
					LEFT JOIN `SBookCategory` c ON
					(
						i.CategoryID = c.ID
					)
			WHERE
					i.NodeID = "0" 
				AND i.FileFolder = f.ID 
				AND i.Filetype = "pdf" 
				AND 
				(
					
					/* --- Public / Members Access --- */
					
					(
						(
								i.Access = "0"
							AND f.Access <= i.Access 
						)
						AND
						(
							
							/* --- Profile / Other Access --- */
							
							(
								i.CategoryID = "0"
							)
							OR
							(
									i.CategoryID > 0
								AND c.IsSystem = "1" 
							)
							
							/* --- Group Access --- */
							
							OR 
							(
								i.CategoryID > 0
								AND c.Privacy = "OpenGroup" 
							)
							OR 
							(
									i.CategoryID > 0
								AND c.Privacy = "ClosedGroup" 
								AND f.Name = "Cover Photos" 
							)
							OR
							(
									i.CategoryID > 0
								AND i.CategoryID IN ( !UserCats! ) 
							)
						)
					)
					
					/* --- Contact Access --- */
					
					OR
					(
							i.Access = "1"
						AND i.UserID > 0 
						AND i.UserID IN ( !UserIDS! )
						AND f.Access <= i.Access 
					)
					
					/* --- File Owner Access --- */
					
					OR
					(
							i.UserID = !UserID!
						AND i.UserID > 0 
					)
					
					/* --- Admin Access --- */
					
					OR
					(
							i.Access != "2"
						AND i.CategoryID > 0 
						AND i.CategoryID IN ( !AdminCats! ) 
					)
					
					/* --- No Owner / All Access --- */
					
					OR
					(
							i.UserID = "0" 
						AND i.CategoryID = "0" 
					)
					
				) 
		)
		UNION 
		( 
			SELECT 
				c.ID AS ID,
				"" AS UniqueID,
				c.Username AS Title,
				i.Filename AS Filename,
				c.Work AS Description, 
				i.Filetype AS Filetype,
				i.Filesize AS Filesize,
				c.DateCreated AS DateCreated, 
				f.DiskPath as FolderPath, 
				f.Name AS FolderName, 
				f.ID AS FolderID, 
				c.UserID AS UserID, 
				f.CategoryID AS CategoryID, 
				c.ID AS ContactID, 
				c.ImageID AS ImageID, 
				"" AS Username,
				"" AS Data,
				"" AS `IsGroup`, 
				"contact" AS MediaType
			FROM 
				`SBookContact` c
					LEFT JOIN `Image` i ON
					(
							c.ImageID > 0
						AND i.ID = c.ImageID
					)
					LEFT JOIN `Folder` f ON
					(
							i.ImageFolder > 0
						AND f.ID = i.ImageFolder
					) 
			WHERE 
				c.NodeID = "0" 
		)
		UNION 
		( 
			SELECT 
				m.ID AS ID,
				"" AS UniqueID,
				m.Subject AS Title,
				i.Filename AS Filename,
				m.Message AS Description, 
				i.Filetype AS Filetype,
				i.Filesize AS Filesize,
				m.Date AS DateCreated, 
				f.DiskPath as FolderPath, 
				f.Name AS FolderName, 
				f.ID AS FolderID, 
				c.UserID AS UserID, 
				m.CategoryID AS CategoryID, 
				c.ID AS ContactID, 
				c.ImageID AS ImageID, 
				c.Username AS Username,
				m.Data AS Data,
				( m.CategoryID != !WallID! ) AS `IsGroup`, 
				"wall" AS MediaType
			FROM 
				`SBookMessage` m, 
				`SBookCategory` g, 
				`SBookContact` c 
					LEFT JOIN `Image` i ON
					(
							c.ImageID > 0
						AND i.ID = c.ImageID
					) 
					LEFT JOIN `Folder` f ON
					(
							i.ImageFolder > 0
						AND f.ID = i.ImageFolder
					) 
			WHERE 
					m.NodeID = "0" 
				AND m.ParentID = "0" 
				AND m.CategoryID > 0 
				AND g.ID = m.CategoryID 
				AND c.ID = m.SenderID 
				AND
				(
					(
							m.Access = "0"
						AND g.Privacy = "OpenGroup"
					) 
					OR
					(
							m.Access = "0"
						AND g.ID = !WallID!
					) 
					' . ( $webuser->ContactID > 0 ? '
					OR
					(
							m.Access = "2"
						AND m.SenderID = !WebUserID!
					) 
					OR
					(
							m.Access = "0"
						AND g.ID IN ( !UserCats! )
						AND g.ID != !WallID!
					) 
					OR
					(
							m.Access = "1"
						AND g.ID IN ( !UserCats! )
						AND m.SenderID IN ( !ContactIDS! )
					)
					' : '' ) . '
				) 
		) 
	) z 
	' . ( $keywords != '' ? '
	WHERE
		(
				z.Title LIKE "%' . $keywords . '%" 
			OR  z.Description LIKE "%' . $keywords . '%"
			OR  z.Data LIKE "%' . $keywords . '%"
		) 
	' : '' ) . '
	ORDER BY 
		z.DateCreated DESC,
		z.Title ASC 
	LIMIT ' . $limit . ' 
';



//$guci = getUserContactsID( $webuser->ContactID );
//$gucids = $guci ? implode( ',', $guci ) : $webuser->ContactID;
//$uci = getUserGroupsID( $webuser->ID );
//$uci[] = getWallID();
//$ucids = $uci ? implode( ',', $uci ) : false;

//$query = str_replace( '!WallID!', getWallID(), $query );
//$query = str_replace( '!WebUserID!', $webuser->ContactID, $query );
//$query = str_replace( '!CategoryIDS!', $ucids, $query );
//$query = str_replace( '!ContactIDS!', $gucids, $query );

$csrs = ( $webuser && isset( $webuser->ContactID ) ? getUserContactsID( $webuser->ContactID ) : false );
$csrs = ( $csrs && is_array( $csrs ) ? implode( ',', $csrs ) : ( $webuser ? $webuser->ContactID : false ) );

$usrs = ( $webuser && isset( $webuser->ContactID ) ? getUserContactsID( $webuser->ContactID, true ) : false );
$usrs = ( $usrs && is_array( $usrs ) ? implode( ',', $usrs ) : ( $webuser ? $webuser->ID : false ) );

$acat = ( $webuser && isset( $webuser->ContactID ) ? CategoryAccess( $webuser->ContactID, false, -1, 'IsAdmin' ) : false );
$acat = ( $acat && isset( $acat['CategoryID'] ) ? $acat['CategoryID'] : false );

$ucat = ( $webuser && isset( $webuser->ContactID ) ? CategoryAccess( $webuser->ContactID, false, -1 ) : false );
$ucat = ( $ucat && isset( $ucat['CategoryID'] ) ? $ucat['CategoryID'] : false );

// TODO: Add support for admins of groups and members of groups and super admin of the system

$query = str_replace( '!WallID!', ( getWallID() ? getWallID() : 'NULL' ), $query );
$query = str_replace( '!UserID!', ( isset( $webuser ) && $webuser ? ( '\'' . $webuser->ID . '\'' ) : 'NULL' ), $query );
$query = str_replace( '!WebUserID!', ( isset( $webuser ) && $webuser ? ( '\'' . $webuser->ContactID . '\'' ) : 'NULL' ), $query );
$query = str_replace( '!ContactIDS!', ( isset( $csrs ) && $csrs ? $csrs : 'NULL' ), $query );
$query = str_replace( '!UserIDS!', ( isset( $usrs ) && $usrs ? $usrs : 'NULL' ), $query );
$query = str_replace( '!AdminCats!', ( isset( $acat ) && $acat ? $acat : 'NULL' ), $query );
$query = str_replace( '!UserCats!', ( isset( $ucat ) && $ucat ? $ucat : 'NULL' ), $query );

//die( $query . ' ..' );

$count = $database->fetchObjectRow ( str_replace( array( 'SELECT * FROM', ( 'LIMIT ' . $limit ) ), array( 'SELECT COUNT(*) AS Total FROM', '' ), $query ) );

if( $rows = $database->fetchObjectRows ( $query ) )
{
	$l = 1;
	$result = '<ol>';
	foreach( $rows as $r )
	{
		$obj = new stdClass();
		
		if ( $r->MediaType && $r->Filename )
		{
			$r->Link = ( BASE_URL . 'secure-files/' . ( $r->MediaType == 'image' ? 'images' : 'files' ) . '/' . ( $r->UniqueID ? $r->UniqueID : $r->ID ) . ( $webuser->ID > 0 && $webuser->GetToken() ? ( '/' . $webuser->GetToken() ) : '' ) . '/' . $r->Filename );
			$r->Thumb = ( BASE_URL . 'secure-files/' . ( $r->MediaType == 'image' ? 'images' : 'files' ) . '/' . ( $r->UniqueID ? $r->UniqueID : $r->ID ) . '/' );
		}
		else
		{
			$r->Link = ( $r->FolderPath . $r->Filename );
		}
		
		$r->Title = strip_tags( $r->Title );
		$r->Description = strip_tags( $r->Description );
		$r->Data = ( is_string( $r->Data ) ? json_decode( $r->Data ) : false );
		$r->Data = ( is_array( $r->Data ) ? $r->Data[0] : $r->Data );
		
		switch( $r->MediaType )
		{
			// --- File ---------------------------------------------------------------------------------------------------
			case 'file':
				$r->Image = '<img style="width:100%;height:100%;" src="subether/gfx/icons/' . ( $ext_large[$r->Filetype] ? $ext_large[$r->Filetype] : $ext_large['txt'] ) . '">';
				$r->BgImage = 'subether/gfx/icons/' . ( $ext_large[$r->Filetype] ? $ext_large[$r->Filetype] : $ext_large['txt'] );
				break;
			// --- Contact ------------------------------------------------------------------------------------------------
			case 'contact':
				$placeholder = 'admin/gfx/arenaicons/user_johndoe_128.png';
				$r->Link = ( BASE_URL . $r->Title );
				$r->Title = ( GetUserDisplayname( $r->ContactID ) ? GetUserDisplayname( $r->ContactID ) : $r->Title );
				//$i = new dbImage ();
				/*if( $r->NodeID > 0 )
				{
					$i->NodeID = $r->NodeID;
					$i->NodeMainID = $r->ImageID;
				}
				else
				{*/
					//$i->ID = $r->ImageID;
				/*}*/
				//if( $i->Load() )
				if( $img = $database->fetchObjectRow( '
					SELECT
						f.DiskPath, i.* 
					FROM
						Folder f, Image i
					WHERE
						i.ID = "' . $r->ImageID . '" AND f.ID = i.ImageFolder
					ORDER BY
						i.ID ASC
					LIMIT 1 
				', false, 'components/browse/functions/network.php' ) )
				{
					$obj->ID = $img->ID;
					$obj->Filename = $img->Filename;
					$obj->FileFolder = $img->ImageFolder;
					$obj->Filesize = $img->Filesize;
					$obj->FileWidth = $img->Width;
					$obj->FileHeight = $img->Height;
					//$obj->DiskPath = str_replace( ' ', '%20', ( $img->DiskPath != '' ? $img->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $img->Filename );
					if ( $img->Filename )
					{
						$obj->DiskPath = ( BASE_URL . 'secure-files/images/' . ( $img->UniqueID ? $img->UniqueID : $img->ID ) . '/' );
					}
					
					if ( !FileExists( $obj->DiskPath ) )
					{
						$obj->DiskPath = false;
						$obj->Filename = false;
					}
				}
				//$r->Image = $i->getImageHTML ( 70, 70, 'framed', false, 0xffffff );
				//$r->BgImage = $i->getImageURL ( 150, 150, 'framed', false, 0xffffff );
				$imgurl = ( $obj->Filename ? $obj->DiskPath : $placeholder );
				$r->Image = '<img style="width:100%;" src="' . $imgurl . '"/>';
				$r->BgImage = $imgurl;
				break;
			// --- Wall ----------------------------------------------------------------------------------------------------
			case 'wall':
				if( $r->Data )
				{
					if( $r->Data->Title )
					{
						$r->Title = $r->Data->Title;
					}
					if( $r->Data->Leadin )
					{
						$r->Description = $r->Data->Leadin;
					}
					//$r->Link = $r->Data->Url;
					//$i = new dbImage ();
					/*if( $r->NodeID > 0 )
					{
						$i->NodeID = $r->NodeID;
						$i->NodeMainID = $r->ImageID;
					}
					else
					{*/
						//$i->ID = $r->Data->FileID;
					/*}*/
					//die( print_r( $r->Data,1 ) . ' --' );
					if( isset( $r->Data->LibraryFiles[0] ) )
					{
						$r->Data->FileID = $r->Data->LibraryFiles[0]->FileID;
						$r->Data->MediaType = $r->Data->LibraryFiles[0]->MediaType;
						$r->Data->FileType = $r->Data->LibraryFiles[0]->FileType;
						
						if( !$r->Filetype )
						{
							$r->Filetype = $r->Data->LibraryFiles[0]->FileType;
						}
					}
					
					if( $r->Data->MediaType == 'image' && ( $img = $database->fetchObjectRow( '
						SELECT
							f.DiskPath, i.* 
						FROM
							Folder f, Image i
						WHERE
							i.ID = "' . $r->Data->FileID . '" AND f.ID = i.ImageFolder
						ORDER BY
							i.ID ASC
						LIMIT 1 
					', false, 'components/browse/functions/network.php' ) ) )
					{
						$obj->ID = $img->ID;
						$obj->Filename = $img->Filename;
						$obj->FileFolder = $img->ImageFolder;
						$obj->Filesize = $img->Filesize;
						$obj->FileWidth = $img->Width;
						$obj->FileHeight = $img->Height;
						//$obj->DiskPath = str_replace( ' ', '%20', ( $img->DiskPath != '' ? $img->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $img->Filename );
						if ( $img->Filename )
						{
							$obj->DiskPath = ( BASE_URL . 'secure-files/images/' . ( $img->UniqueID ? $img->UniqueID : $img->ID ) . '/' );
						}
						
						if ( !FileExists( $obj->DiskPath ) )
						{
							$obj->DiskPath = false;
							$obj->Filename = false;
						}
					}
					
					if( $r->Data->MediaType == 'image' && $r->Data->FileID > 0 && $obj && $obj->ID > 0 && $obj->Filename )
					{
						//$r->Image = $i->getImageHTML ( 70, 70, 'framed', false, 0xffffff );
						//$r->BgImage = $i->getImageURL ( 150, 150, 'framed', false, 0xffffff );
						$imgurl = $obj->DiskPath;
						$r->Image = '<img style="width:100%;" src="' . $imgurl . '"/>';
						$r->BgImage = $imgurl;
						if( $r->Data->Type == 'video' )
						{
							$r->Image .= '<em></em>';
						}
						if( $r->Description == '' && $r->Title == '' && $r->Data->FileName )
						{
							$r->Description = $r->Data->FileName;
						}
					}
					else if( $r->Data->Type == 'file' || $r->Data->MediaType == 'file' )
					{
						$r->Image = '<img style="width:100%;height:100%;" src="subether/gfx/icons/' . ( $ext_large[$r->Data->FileType] ? $ext_large[$r->Data->FileType] : $ext_large['txt'] ) . '">';
						//$r->BgImage = 'subether/gfx/icons/' . ( $ext_large[$r->Data->FileType] ? $ext_large[$r->Data->FileType] : $ext_large['txt'] );
						$r->MediaType = 'file';
					}
				}
				if( $r->Title == '' )
				{
					$r->Title = $r->Description;
					$r->Description = 'Posted by ' . ( GetUserDisplayname( $r->ContactID ) ? GetUserDisplayname( $r->ContactID ) : $r->Username );
				}
				if( !$r->Image && $r->ImageID > 0 )
				{
					$r->Image = '<img style="width:100%;height:100%;" src="subether/gfx/icons/' . $ext_large['url'] . '">';
					//$r->BgImage = 'subether/gfx/icons/' . $ext_large['url'];
					$r->MediaType = 'file';
					/*$i = new dbImage ();
					if( $r->NodeID > 0 )
					{
						$i->NodeID = $r->NodeID;
						$i->NodeMainID = $r->ImageID;
					}
					else
					{
						$i->ID = $r->ImageID;
					}
					if( $i->Load() )
					{
						$r->Image = $i->getImageHTML ( 70, 70, 'framed', false, 0xffffff );
					}*/
				}
				if( $r->IsGroup )
				{
					$r->Link = ( BASE_URL . 'groups/' . $r->CategoryID . '/?q=' . strtolower( str_replace( ' ', '+', $r->Title ) ) . '&r=wall' );
				}
				else
				{
					$r->Link = ( BASE_URL . $r->Username . '?q=' . strtolower( str_replace( ' ', '+', $r->Title ) ) . '&r=wall' );
				}
				break;
			// --- Default ------------------------------------------------------------------------------------------------
			default:
				$r->Icon = $r->MediaType;
				break;
		}
		
		//$r->Image = '';
		//if( $r->ID == 7649 ) die( print_r( $r,1 ) . ' --' );
		$result .= '<li><table><tr>';
		if( $r->Image )
		{
			$result .= '<td class="' . $r->MediaType . '" style="width:100px;"><div ' . ( $r->BgImage ? 'style="background-image:url(\'' . $r->BgImage . '\')"' : '' ) . ' class="image' . ( $r->Filetype ? ( ' ' . $r->Filetype ) : '' ) . '">'/* . $r->Image*/ . '</div></td>';
		}
		$result .= '<td><div><h3>';
		//$result .= '<a href="' . $r->Link . '">' . str_mark( dot_trim( $r->Title, 50 ), $keywords ) . '</a>';
		$result .= '<a href="' . $r->Link . '">' . str_mark( dot_trim( $r->Title, 100 ), $keywords ) . '</a>';
		$result .= '</h3><div>';
		//$result .= '<div><p>' . ( $r->Icon ? '[' . $r->Icon . '] ' : '' ) . str_mark( dot_trim( $r->Link, 50 ), $keywords ) . '</p></div>';
		$result .= '<div><p>' . ( $r->Icon ? '[' . $r->Icon . '] ' : '' ) . str_mark( dot_trim( $r->Link, 100 ), $keywords ) . '</p></div>';
		//$result .= '<div><span>' . str_mark( dot_trim( $r->Description, 50, $keywords ), $keywords ) . '</span></div>';
		$result .= '<div><span>' . str_mark( dot_trim( $r->Description, 100, $keywords ), $keywords ) . '</span></div>';
		$result .= '</div></div></td>';
		$result .= '</tr></table></li>';
		$l++;
	}
	$result .= '</ol>';
	
	//die( print_r( $rows,1 ) . ' --' );
	
	$time_end = microtime_float();
	
	if( $count )
	{
		$time = ( $time_end - $time_start );
		
		$mstr .= '<div>' . $count->Total . ' ' . i18n( 'results' ) . ' (' . number_format( $time, 2, '.', '' ) . ' ' . i18n( 'seconds' ) . ')</div>';
	}
	
	$mstr .= $result;
}

$mstr .= '</div>';

?>
