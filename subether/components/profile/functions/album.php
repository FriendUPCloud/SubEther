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

if( $folder = $database->fetchObjectRow ( '
	SELECT
		*
	FROM
		SBookMediaRelation
	WHERE
		(
			UserID = \'' . $parent->cuser->ID . '\'
		)
		AND CategoryID = "0"
		AND MediaType = "Folder"
		AND Name = "Profile Pictures"
	ORDER BY
		ID ASC
' ) )
{
	$cn = 0;
	if ( $fldimgs = $database->fetchRows ( '
		SELECT
			*
		FROM
			Image
		WHERE
				NodeID = "0"
			AND ImageFolder=\'' . $folder->MediaID . '\'
		ORDER BY
			ID ASC
	' ) )
	{
		foreach ( $fldimgs as $fim )
		{
			if ( $fim['ID'] == $im->ID ) break;
			$cn++;
		}
	}
	
	//$str = '<div id="Avatar" class="image" onclick="openFullscreen( \'Library\', \'' . $folder->MediaID . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( ' . (string)$cn . ' ); } )">';
	//$pimg .= '<a href="javascript:void(0)">';
	/*$i = new dbImage ();
	if( $parent->cuser->NodeID > 0 )
	{
		$i->NodeID = $parent->cuser->NodeID;
		$i->NodeMain = $parent->cuser->ImageID;
	}
	else
	{
		$i->ID = $parent->cuser->ImageID;
	}
	if( $i->Load() )
	{
		$pimg .= $i->getImageHTML ( 160, 160, 'framed', false, 0xffffff );
	}*/
	
	if( $img = $database->fetchObjectRow( '
		SELECT
			f.DiskPath,
			i.* 
		FROM
			Folder f,
			Image i
		WHERE
				i.NodeID = "0" 
			AND i.ImageFolder = "' . $folder->MediaID . '"
			AND f.ID = i.ImageFolder
		ORDER BY
			i.ID DESC 
		LIMIT 1 
	', false, 'components/profile/templates/component.php' ) )
	{
		$obj = new stdClass();
		$obj->ID = $img->ID;
		$obj->Filename = $img->Filename;
		$obj->FileFolder = $img->ImageFolder;
		$obj->Filesize = $img->Filesize;
		$obj->FileWidth = $img->Width;
		$obj->FileHeight = $img->Height;
		$obj->DiskPath = str_replace( ' ', '%20', ( $img->DiskPath != '' ? $img->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $img->Filename );
		if ( $img->Filename )
		{
			$obj->DiskPath = ( BASE_URL . 'secure-files/images/' . ( $img->UniqueID ? $img->UniqueID : $img->ID ) . '/' );
		}
		
		$pimg  = '<div id="Avatar" class="image" onclick="openFullscreen( \'Library\', \'' . $folder->MediaID . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( ' . (string)$cn . ' ); } )">';
		$pimg .= '<div style="background-image:url(\'' . ( $userimage = $obj->DiskPath ) . '\')"></div>';
		$pimg .='</div>';
		
		if ( !FileExists( $obj->DiskPath ) )
		{
			$pimg = false;
		}
		
		//$str .= '<img style="height:100%;max-height:100%;" src="' . $obj->DiskPath . '"/>';
		
		//$str .= '</a>';
		//$str .= '</div>';
		
		//$pimg = $str;
	}
}

if( $folder = $database->fetchObjectRow ( '
	SELECT
		*
	FROM
		SBookMediaRelation
	WHERE
		(
			UserID = \'' . $parent->cuser->ID . '\'
		)
		AND CategoryID = "0"
		AND MediaType = "Folder"
		AND Name = "Cover Photos"
	ORDER BY
		ID ASC
	' ) )
{
	$cn = 0;
	if ( $fldimgs = $database->fetchRows ( '
		SELECT
			*
		FROM
			Image
		WHERE
				NodeID = "0"
			AND ImageFolder=\'' . $folder->MediaID . '\'
		ORDER BY
			ID ASC
	' ) )
	{
		foreach ( $fldimgs as $fim )
		{
			if ( $fim['ID'] == $im->ID ) break;
			$cn++;
		}
	}
	
	$imageWidth = 1000;
	$imageHeight = 338;
	
	if( $images = $database->fetchObjectRows( '
		SELECT
			i.*, 
			f.DiskPath, 
			f.Name AS FolderName 
		FROM
			Image i,
			Folder f
		WHERE
				i.NodeID = "0" 
			AND i.ImageFolder = \'' . $folder->MediaID . '\' 
			AND f.ID = i.ImageFolder 
		ORDER BY
			i.ID DESC
	', false, 'components/profile/templates/component.php' ) )
	{		
		$str = '<div id="MainImage">';
		
		$folder->Images = 0;
		foreach( $images as $i )
		{
			if ( !$imageWidth )
			{
				$imageWidth = $i->Width;
			}
			if ( !$imageHeight )
			{
				$imageHeight = $i->Height;
			}
			
			$imagePath = ( BASE_URL . 'secure-files/images/' . ( $i->UniqueID ? $i->UniqueID : $i->ID ) . '/' );
			//$imagePath = str_replace( ' ', '%20', ( $i->DiskPath != '' ? $i->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $i->Filename );
			
			if ( !FileExists( $imagePath ) )
			{
				$imagePath = false;
			}
			
			if ( $imagePath )
			{
				$str .= '<span folder="' . $i->FolderName . '" unique="' . $i->UniqueID . '" fid="' . $i->ID . '" index="' . (string)$folder->Images . '" title="' . $imagePath . '" width="' . $imageWidth . '" height="' . $imageHeight . '" description="" extended=""></span>';
				$folder->Images++;
				$folder->Name = $i->FolderName;
			}
		}
		
		if ( $folder->Images > 0 )
		{
			$simg  = $str . '</div>';
			$simg .= '<script> Showroom.init( \'MainImage\' ); </script>';
		}
	}
	
	
	
	if ( $cn > 0 && $folder->Images > 0 )
	{
		$cimg  = '<div class="view_btn_cover" onclick="openFullscreen( \'Library\', \'' . $folder->MediaID . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( ' . (string)$cn . ' ); } )">';
		$cimg .= '<div><span>' . i18n( 'i18n_View Album' ) . '</span></div>';
		$cimg .= '</div>';
	}
}

?>
