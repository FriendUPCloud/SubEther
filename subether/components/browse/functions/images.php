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

$mstr .= '<div class="images">';

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
//		Image i, 
//		Folder f 
//			LEFT JOIN SBookContact c ON
//			(
//				f.UserID = c.UserID AND f.UserID > 0
//			) 
//	WHERE 
//		i.NodeID = "0"
//		AND f.ID = i.ImageFolder 
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
		`Image` i 
			LEFT JOIN `SBookCategory` c ON
			(
				i.CategoryID = c.ID
			)
	WHERE
			i.NodeID = "0" 
		AND i.ImageFolder = f.ID 
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
	foreach( $rows as $r )
	{
		$cn = 0;
		// TODO: Do something about this, it's slowing things down ...
		if ( $fldimgs = $database->fetchRows ( 'SELECT * FROM Image WHERE ImageFolder=\'' . $r->ImageFolder . '\' ORDER BY ID ASC' ) )
		{
			foreach ( $fldimgs as $fim )
			{
				if ( $fim['ID'] == $r->ID ) break;
				$cn++;
			}
		}
		
		$href = './';
		$thmb = ( $r->FolderPath . $r->Filename );
		
		if( $r->UserID > 0 || $r->CategoryID > 0 )
		{
			$href = ( $r->UserID > 0 && $r->Username ? ( 'groups/' . $r->Username . '/library/' ) : ( 'groups/' . $r->CategoryID . '/library/' ) );
		}
		
		if ( $r->Filename )
		{
			$href = ( BASE_URL . 'secure-files/images/' . ( $r->UniqueID ? $r->UniqueID : $r->ID ) . ( $webuser->ID > 0 && $webuser->GetToken() ? ( '/' . $webuser->GetToken() ) : '' ) . '/' );
			$thmb = ( BASE_URL . 'secure-files/images/' . ( $r->UniqueID ? $r->UniqueID : $r->ID ) . '/' );
		}
		
		if ( !FileExists( $href ) )
		{
			$thmb = false;
		}
		
		$mstr .= '<div class="imagebox">';
		
		if ( $thmb )
		{
			$mstr .= '<div class="thumb">';
			$mstr .= '<a href="javascript:void(0)" onclick="openFullscreen( \'Library\', \'' . $r->ImageFolder . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( ' . (string)$cn . ' ); } )">';
			$mstr .= '<img style="background-image:url(\'' . $thmb . '\');">';
			$mstr .= '</a>';
			//$mstr .= '<div class="source"></div>';
			//$mstr .= '<div class="duration"></div>';
			$mstr .= '</div>';
		}
		
		$mstr .= '<div class="content">';
		$mstr .= '<a href="' . $href . '">';
		$mstr .= '<span>' . dotTrim( $r->Title, 15, true ) . '</span>';
		$mstr .= '</a>';
		//$mstr .= '<div><span></span></div>';
		$mstr .= '</div></div>';
	}
}

$mstr .= '</div>';

?>
