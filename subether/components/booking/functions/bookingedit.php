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

$booking = $database->fetchObjectRow( '
	SELECT
		*
	FROM
		SBookEvents
	WHERE
			ID = \'' . $_POST['eid'] . '\' 
		AND Component = "booking"
		AND CategoryID = \'' . $parent->folder->CategoryID . '\' 
	ORDER BY
		ID DESC 
' );

$str = '';

$str .= '<div class="inputs">';
$str .= '<div class="heading"><input id="EventName" name="Name" type="text" placeholder="Name" value="' . ( isset( $booking->Name ) && $booking->Name ? $booking->Name : '' ) . '"/></div>';
$str .= '<div class="place"><input id="EventPlace" name="Place" type="text" placeholder="Place" value="' . ( isset( $booking->Place ) && $booking->Place ? $booking->Place : '' ) . '"/></div>';
$str .= '<div class="description"><input id="EventDescription" name="Description" type="text" placeholder="Description" value="' . ( isset( $booking->Details ) && $booking->Details ? $booking->Details : '' ) . '"/></div>';
$str .= '<div class="price"><input id="EventPrice" name="Price" type="text" placeholder="Price" value="' . ( isset( $booking->Price ) && $booking->Price ? $booking->Price : '' ) . '"/></div>';
$str .= '<div class="slots"><input id="EventSlots" name="Slots" type="text" placeholder="Slots" value="' . ( isset( $booking->Slots ) && $booking->Slots ? $booking->Slots : '' ) . '"/></div>';
$str .= '<div class="Limit"><input id="EventLimit" name="Limit" type="text" placeholder="Limit" value="' . ( isset( $booking->Limit ) && $booking->Limit ? $booking->Limit : '' ) . '"/></div>';
$str .= '</div>';

die( 'ok<!--separate-->' . $str );

?>
