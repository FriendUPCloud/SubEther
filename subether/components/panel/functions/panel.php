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

if( $parent && $module && $path && $user && $module != 'profile' )
{
	$pstr = '';
	$pstr .= '<table class="profile thumb">';
	$pstr .= '<tr>';
	$pstr .= '<td>';
	$pstr .= '<div class="image">';
	$pstr .= '<a href="' /*. $path*/ . $user->Username . '">';
	/*$i = new dbImage ();
	if( $i->load( $user->ImageID ) )
	{
		$pstr .= $i->getImageHTML ( 50, 50, 'framed', false, 0xffffff );
	}*/
	
	$obj = new stdClass();
	
	$defaultimg = 'admin/gfx/arenaicons/user_johndoe_128.png';
	
	if( $img = $database->fetchObjectRow( '
		SELECT
			f.DiskPath, i.* 
		FROM
			Folder f, Image i
		WHERE
			i.ID = \'' . $user->ImageID . '\' AND f.ID = i.ImageFolder
		ORDER BY
			ID ASC
	', false, 'components/panel/functions/panel.php' ) )
	{
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
		if ( !FileExists( $obj->DiskPath ) )
		{
			$obj->DiskPath = false;
		}
	}
	
	$pstr .= '<img style="background-image:url(\'' . ( $obj->DiskPath ? $obj->DiskPath : $defaultimg ) . '\');background-position: center center;background-repeat: no-repeat;background-size: cover;width:100%;height:100%;" src="' . ( $obj->DiskPath ? $obj->DiskPath : $defaultimg ) . '"/>';
	
	$pstr .= '</a>';
	$pstr .= '</div>';
	$pstr .= '</td>';
	$pstr .= '<td>';
	$pstr .= '<div>';
	$pstr .= '<a href="' /*. $path*/ . $user->Username . '">' . ( $user->DisplayName ? $user->DisplayName : $user->Username ) . '</a>';
	$pstr .= '<a class="edit" href="'/* . $path*/ . $user->Username . '">' . i18n( 'i18n_Edit Profile' ) . '</a>';
	$pstr .= '</div>';
	$pstr .= '</td>';
	$pstr .= '</tr>';
	$pstr .= '</table>';
}

// --- New way -----------------------------------------------------------------------------------------------------

$str = '';

// --- Profile -----------------------------------------------------------------------------------------------------

if ( $module == 'profile' && ComponentExists( 'profile', $parent->module ) && ComponentAccess( 'profile', $parent->module ) && file_exists( 'subether/components/profile/include/panel.php' ) )
{
	include_once ( 'subether/components/profile/include/panel.php' );
}

// --- Favorites ---------------------------------------------------------------------------------------------------

if ( $module != 'profile' && ComponentExists( 'favorites', $parent->module ) && ComponentAccess( 'favorites', $parent->module ) && file_exists( 'subether/components/favorites/include/panel.php' ) )
{
	include_once ( 'subether/components/favorites/include/panel.php' );
}

// --- Events ------------------------------------------------------------------------------------------------------

if ( $module != 'profile' && ComponentExists( 'events', $parent->module ) && ComponentAccess( 'events', $parent->module ) && file_exists( 'subether/components/events/include/panel.php' ) )
{
	include_once ( 'subether/components/events/include/panel.php' );
}

// --- Groups ------------------------------------------------------------------------------------------------------

if ( $module != 'profile' && ComponentExists( 'groups', $parent->module ) && ComponentAccess( 'groups', $parent->module ) && file_exists( 'subether/components/groups/include/panel.php' ) )
{
	include_once ( 'subether/components/groups/include/panel.php' );
}

// --- Pages -------------------------------------------------------------------------------------------------------

if ( $module != 'profile' && ComponentExists( 'pages', $parent->module ) && ComponentAccess( 'pages', $parent->module ) && file_exists( 'subether/components/pages/include/panel.php' ) )
{
	include_once ( 'subether/components/pages/include/panel.php' );
}

// --- Bank --------------------------------------------------------------------------------------------------------

if ( $module != 'profile' && ComponentExists( 'bank', $parent->module ) && ComponentAccess( 'bank', $parent->module ) && file_exists( 'subether/components/bank/include/panel.php' ) )
{
	include_once ( 'subether/components/bank/include/panel.php' );
}

?>
