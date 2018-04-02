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

global $webuser;

$str  = '<ul>';

$downloadurl = ( BASE_URL . 'secure-files/images/' . ( $_POST['unique'] ? $_POST['unique'] : $_POST['fid'] ) . ( $webuser->ID > 0 && $webuser->GetToken() ? ( '/' . $webuser->GetToken() ) : '' ) . '/' );

$str .= '<li><div onclick="document.location=\'' . $downloadurl . '\'"><span>' . i18n( 'i18n_Download' ) . '</span></div></li>';

if( $acc = libraryFileAccess( $_POST['fid'], 'Image' ) )
{
	$str .= '<li><div onclick="deleteImage()"><span>' . i18n( 'i18n_Delete This Photo' ) . '</span></div></li>';
}

$str .= '</ul>';

die( 'ok<!--separate-->' . $str );

?>
