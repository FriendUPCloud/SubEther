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

include_once ( 'subether/classes/library.class.php' );

switch( $_POST['extra'] )
{
	
	case 'wall':
		$q1 = 'SELECT * FROM SBookMessage WHERE ID = \'' . $ParentID . '\' ORDER BY ID ASC';
		break;
	
	default:
		$q1 = 'SELECT * FROM Folder WHERE ID = \'' . $ParentID . '\' ORDER BY ID ASC';
		break;
}

if( $folder = $database->fetchObjectRow ( $q1, false, 'components/library/functions/windowgallery.php' ) )
{	
	if( $Mode == 'fullscreen' )
	{
		//$res = explode( 'x', $Resolution );
		$imageWidth = false;
		$imageHeight = false;
	}
	else
	{
		$imageWidth = 600;
		$imageHeight = 450;
	}
	
	if( $folder->Data )
	{
		$lib = new Library();
		
		$folder->ImageID = array();
		
		$folder->Data = json_obj_decode( $folder->Data );
		
		if( $folder->Data && ( isset( $folder->Data->LibraryFiles ) || is_array( $folder->Data ) ) )
		{
			if( isset( $folder->Data->LibraryFiles ) )
			{
				foreach( $folder->Data->LibraryFiles as $fi )
				{
					if( $lib->FileTypes( $fi->FileName ) == 'image' )
					{
						$folder->ImageID[] = $fi->FileID;
					}
				}
			}
			if( is_array( $folder->Data ) )
			{
				foreach( $folder->Data as $fi )
				{
					if( $lib->FileTypes( $fi->FileName ) == 'image' )
					{
						$folder->ImageID[] = $fi->FileID;
					}
				}
			}
		}
	}
	
	switch( $_POST['extra'] )
	{
		
		case 'wall':
			$q2 = '
				SELECT
					i.*,
					f.DiskPath, 
					f.Name AS FolderName 
				FROM
					Image i,
					Folder f
				WHERE
						i.ID IN ( ' . ( $folder->ImageID ? implode( ',', $folder->ImageID ) : '' ) . ' ) 
					AND f.ID = i.ImageFolder 
				ORDER BY
					i.ID ASC';
			break;
		
		default:
			$q2 = '
				SELECT
					i.*, 
					f.DiskPath, 
					f.Name AS FolderName 
				FROM
					Image i,
					Folder f
				WHERE
						i.ImageFolder = \'' . $folder->ID . '\' 
					AND f.ID = i.ImageFolder 
				ORDER BY
					i.ID ASC';
			break;
	}
	
	$str = '<div id="Album_Showroom">';
	
	//die( $q2 . ' -- ' . print_r( $folder->ImageID,1 ) . ' .. ' . print_r( $folder->Data,1 ) );
	
	if( $images = $database->fetchObjectRows ( $q2, false, 'components/library/functions/windowgallery.php' ) )
	{
		$folder->Images = 0;
		foreach( $images as $i )
		{
			//$img = new dbImage( $i->ID );
			if ( !$imageWidth )
			{
				$imageWidth = $img->Width;
			}
			if ( !$imageHeight )
			{
				$imageHeight = $img->Height;
			}
			
			$imagePath = '';
			
			if( $i->Filename )
			{
				$imagePath = ( BASE_URL . 'secure-files/images/' . ( $i->UniqueID ? $i->UniqueID : $i->ID ) . '/' );
				//$imagePath = str_replace( ' ', '%20', ( $i->DiskPath != '' ? $i->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $i->Filename );
				
				$str .= '<span folder="' . $i->FolderName . '" unique="' . $i->UniqueID . '" fid="' . $i->ID . '" index="' . (string)$folder->Images . '" title="' . $imagePath . '" width="' . $imageWidth . '" height="' . $imageHeight . '" description="" extended=""></span>';
				$folder->Images++;
				$folder->Name = $i->FolderName;
			}
		}
	}
	
	$str = $str . '</div>';
	
	$obj = $folder;
}
else
{
	if( $Act == 'album' ) die( 'empty' );
}

?>
