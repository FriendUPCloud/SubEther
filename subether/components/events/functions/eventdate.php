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

if( isset( $_REQUEST['basetime'] ) )
{
	$_SESSION['events_basetime'] = ( $_REQUEST['basetime'] ? $_REQUEST['basetime'] : strtotime( date( 'Y-m-d H:i:s' ) ) );
}

if( isset( $_POST['date'] ) )
{
	$_SESSION['events_basetime'] = ( $_POST['date'] ? $_POST['date'] : strtotime( date( 'Y-m-d H:i:s' ) ) );
}

$ct = ( $_SESSION['events_basetime'] ? $_SESSION['events_basetime'] : strtotime( date( 'Y-m-d H:i:s' ) ) );

$lw = new DateTime();
$lw->setISODate( date( 'Y', $ct ), date( 'W', $ct )-1, 1 );

$cw = new DateTime();
$cw->setISODate( date( 'Y', $ct ), date( 'W' ), 1 );

$nw = new DateTime();
$nw->setISODate( date( 'Y', $ct ), date( 'W', $ct )+1, 1 );

$estr  = '<span class="nav arrowleft" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm', $ct ), date( 'd', $ct )-1, date( 'Y', $ct ) ) . ')"> < </span> ';
$estr .= '<span class="info day" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm', $ct ), date( 'd' ), date( 'Y', $ct ) ) . ')">' . date( 'j', $ct ) . '</span> ';
$estr .= '<span class="nav arrowright" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm', $ct ), date( 'd', $ct )+1, date( 'Y', $ct ) ) . ')"> > </span> ';
$estr .= '<span class="nav arrowleft" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm', $ct )-1, date( 'd', $ct ), date( 'Y', $ct ) ) . ')"> < </span> ';
$estr .= '<span class="info month" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm' ), date( 'd', $ct ), date( 'Y', $ct ) ) . ')">' . date( 'F', $ct ) . '</span> ';
$estr .= '<span class="nav arrowright" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm', $ct )+1, date( 'd', $ct ), date( 'Y', $ct ) ) . ')"> > </span> ';
$estr .= '<span class="nav arrowleft" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm', $ct ), date( 'd', $ct ), date( 'Y', $ct )-1 ) . ')"> < </span> ';
$estr .= '<span class="info year" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm', $ct ), date( 'd', $ct ), date( 'Y' ) ) . ')">' . date( 'Y', $ct ) . '</span> ';
$estr .= '<span class="nav arrowright" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm', $ct ), date( 'd', $ct ), date( 'Y', $ct )+1 ) . ')"> > </span> ';
$estr .= '<span class="nav arrowleft" onclick="SetEventDate(' . strtotime( $lw->format( 'Y-m-d' ) ) . ')"> < </span> ';
$estr .= '<span class="info week" onclick="SetEventDate(' . strtotime( $cw->format( 'Y-m-d' ) ) . ')">' . ( 'Week ' . date( 'W', $ct ) ) . '</span> ';
$estr .= '<span class="nav arrowright" onclick="SetEventDate(' . strtotime( $nw->format( 'Y-m-d' ) ) . ')"> > </span> ';

if( isset( $_REQUEST['js'] ) )
{
	die( 'ok<!--separate-->' . $estr );
}

?>
