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

$store = false;

$store = $database->fetchObjectRow( '
	SELECT
		*
	FROM
		`SBookProducts`
	WHERE
			`ID` = \'' . $_POST['pid'] . '\' 
		AND	`CategoryID` = \'' . $parent->folder->CategoryID . '\'
		AND `IsDeleted` = "0"
	ORDER BY
		ID DESC 
' );

$str = '';

$str .= '<div class="inputs">';
$str .= '<div class="heading">';
$str .= '<input id="ProductName" name="Name" type="text" placeholder="Name" value="' . ( isset( $store->Name ) ? $store->Name : '' ) . '"/>';
$str .= '</div>';
$str .= '<div class="info">';
$str .= '<textarea id="ProductInfo" name="Info" type="text" placeholder="Info">' . ( isset( $store->Info ) ? $store->Info : '' ) . '</textarea>';
$str .= '</div>';
$str .= '<div class="rating"></div>';
$str .= '<div class="price">';
$str .= '<input id="ProductPrice" name="Price" type="text" placeholder="Price" value="' . ( isset( $store->Price ) ? $store->Price : '' ) . '"/>';
$str .= '</div>';
$str .= '</div>';

die( 'ok<!--separate-->' . $str );

?>
