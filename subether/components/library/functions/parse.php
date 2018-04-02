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

if( isset( $_POST['url'] ) )
{
	$width = '743';
	$height = '420';
	
	$lib = new Library ();
	$obj = $lib->ParseUrl( $_POST['url'] );
	$obj->ImageID = '0';
	$obj->Limit['width'] = $width;
	$obj->Limit['height'] = $height;
	
	die( 'ok<!--separate-->' . json_encode( $obj ) );
}
die( 'fail' );

?>
