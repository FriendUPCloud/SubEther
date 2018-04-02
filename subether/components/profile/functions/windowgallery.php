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

global $database;

if( $folder = $database->fetchObjectRow ( 'SELECT * FROM SBookMediaRelation WHERE UserID = \'' . $ParentID . '\' AND CategoryID = "0" AND MediaType = "Folder" AND Name = \'' . ( $Act == 'cover' ? 'Cover Photos' : 'Profile Pictures' ) . '\' ORDER BY ID ASC' ) )
{
	if( $Mode == 'fullscreen' )
	{
		$res = explode( 'x', $Resolution );
		$imageWidth = $res[0];
		$imageHeight = $res[1];
	}
	else
	{
		$imageWidth = 600;
		$imageHeight = 450;
	}

	$str = '<div id="' . ( $Act == 'cover' ? 'Cover_Showroom' : 'Avatar_Showroom' ) . '">';
	if( $images = $database->fetchObjectRows ( 'SELECT * FROM Image WHERE ImageFolder = \'' . $folder->MediaID . '\' ORDER BY ID DESC' ) )
	{
		$folder->Images = 0;
		foreach( $images as $i )
		{
			$img = new dbImage( $i->ID );
			$str .= '<span folder="' . $folder->Name . '" fid="' . $i->ID . '" title="' . $img->getImageUrl ( $imageWidth, $imageHeight, 'centered' ) . '" width="' . $imageWidth . '" height="' . $imageHeight . '" description="" extended=""></span>';
			$folder->Images++;
		}
	}
	$str = $str . '</div>';
	
	$obj = $folder;
}
else
{
	if( $Act == 'cover' || $Act == 'profile' ) die( 'empty' );
}

?>
