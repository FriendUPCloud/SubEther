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

$limit = 100;

$mstr .= '<div class="files">';

//$query = '
//	SELECT 
//		i.*, 
//		f.DiskPath as FolderPath, 
//		f.Name AS FolderName, 
//		f.ID AS FolderID, 
//		f.UserID, 
//		f.CategoryID,
//		c.ID AS ContactID,
//		c.ImageID,
//		c.Username 
//	FROM 
//		File i, 
//		Folder f 
//		LEFT JOIN SBookContact c ON ( f.UserID = c.UserID AND f.UserID > 0 ) 
//	WHERE 
//		i.NodeID = "0"
//		AND f.ID = i.FileFolder 
//		' . ( isset( $_REQUEST[ 'q' ] ) ? 'AND i.Title LIKE "%' . str_replace( ' ', '+', $_REQUEST[ 'q' ] ) . '%"' : '' ) . '
//	ORDER BY 
//		i.ID DESC
//	' . ( $limit ? ( 'LIMIT ' . $limit . '' ) : '' ) . ' 
//';

$query = '
	SELECT 
		i.*, 
		f.DiskPath as FolderPath, 
		f.Name AS FolderName, 
		f.ID AS FolderID, 
		f.UserID, 
		f.CategoryID,
		u.ID AS ContactID,
		u.ImageID,
		u.Username 
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
		AND i.Filetype NOT IN ( "meta", "parse", "htm", "html", "php", "js", "jsx", "exe", "css" ) 
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
		' . ( isset( $_REQUEST[ 'q' ] ) ? '
		AND i.Title LIKE "%' . str_replace( ' ', '+', $_REQUEST[ 'q' ] ) . '%" 
		' : '' ) . '
	ORDER BY
		i.ID DESC
	' . ( $limit ? ( '
	LIMIT ' . $limit . '
	' ) : '' ) . '
';



$usrs = ( $webuser && isset( $webuser->ContactID ) ? getUserContactsID( $webuser->ContactID, true ) : false );
$usrs = ( $usrs && is_array( $usrs ) ? implode( ',', $usrs ) : ( $webuser ? $webuser->ID : false ) );

$acat = ( $webuser && isset( $webuser->ContactID ) ? CategoryAccess( $webuser->ContactID, false, -1, 'IsAdmin' ) : false );
$acat = ( $acat && isset( $acat['CategoryID'] ) ? $acat['CategoryID'] : false );

$ucat = ( $webuser && isset( $webuser->ContactID ) ? CategoryAccess( $webuser->ContactID, false, -1 ) : false );
$ucat = ( $ucat && isset( $ucat['CategoryID'] ) ? $ucat['CategoryID'] : false );

// TODO: Add support for admins of groups and members of groups and super admin of the system

$query = str_replace( '!UserID!', ( isset( $webuser ) && $webuser ? ( '\'' . $webuser->ID . '\'' ) : 'NULL' ), $query );
$query = str_replace( '!UserIDS!', ( isset( $usrs ) && $usrs ? $usrs : 'NULL' ), $query );
$query = str_replace( '!AdminCats!', ( isset( $acat ) && $acat ? $acat : 'NULL' ), $query );
$query = str_replace( '!UserCats!', ( isset( $ucat ) && $ucat ? $ucat : 'NULL' ), $query );



if( $rows = $database->fetchObjectRows ( $query ) )
{
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
	
	foreach( $rows as $r )
	{		
		$href = './';
		
		if( $r->UserID > 0 || $r->CategoryID > 0 )
		{
			$href = ( $r->UserID > 0 && $r->Username ? ( 'groups/' . $r->Username . '/library/' ) : ( 'groups/' . $r->CategoryID . '/library/' ) );
		}
		
		if ( $r->Filename )
		{
			$href = ( BASE_URL . 'secure-files/files/' . ( $r->UniqueID ? $r->UniqueID : $r->ID ) . ( $webuser->ID > 0 && $webuser->GetToken() ? ( '/' . $webuser->GetToken() ) : '' ) . '/' );
		}
		
		$mstr .= '<div class="filebox">';
		$mstr .= '<div class="thumb' . ( $r->Filetype ? ( ' ' . $r->Filetype ) : '' ) . '">';
		$mstr .= '<a href="javascript:void(0)">';
		$mstr .= '<img style="width:90px;height:90px;background-image:url(\'subether/gfx/icons/' . ( $ext_large[$r->Filetype] ? $ext_large[$r->Filetype] : $ext_large['txt'] ) . '\')">';
		$mstr .= '</a>';
		//$mstr .= '<div class="source"></div>';
		//$mstr .= '<div class="duration"></div>';
		$mstr .= '</div>';
		$mstr .= '<div class="content">';
		$mstr .= '<a href="' . $href . '">';
		$mstr .= '<span>' . dotTrim( $r->Title, 8, true ) . '</span>';
		$mstr .= '</a>';
		//$mstr .= '<div><span></span></div>';
		$mstr .= '</div></div>';
	}
}

$mstr .= '</div>';

?>
