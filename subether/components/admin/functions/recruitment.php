<?php/*******************************************************************************
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
*******************************************************************************/$collect = array(); $collect2 = array();$cats = array( 'Admin', 'Journalist', 'Moderator', 'Leader' );$typs = array( 'Admin', 'Journalist', 'Moderator', 'Leader' );foreach( $cats as $k=>$c ){	$cat = new stdClass();	$cat->ID = $k;	$cat->Category = $c;	$collect[] = $cat;}$cnts = array( 	array( 'ID'=>1, 'Location'=>'Internet', 'Start'=>'Y-m-d', 'Deadline'=>'Y-m-d', 'Position'=>'Testing', 'Company'=>'SubEther', '>Description'=>'testingstestings' ) );foreach( $cnts as $ke=>$cn ){	$cnt = new stdClass();	foreach( $cn as $k=>$c )	{		$cnt->$k = $c;	}	$collect2[] = $cnt;}$str = '<div class="recruitment"><table>';if( $collect2 ){	foreach( $collect2 as $cnt )	{		$str .= '<tr class="sw' . $sw = ( $sw == 2 ? 1 : 2 ) . '">';		$str .= '<td><span>' . $cnt->ID . '</span></td>';		$str .= '<td><span>' . $cnt->Position . '</span></td>';		$str .= '<td><span>' . $cnt->Company . '</span></td>';		$str .= '<td><span>' . $cnt->Location . '</span></td>';		$str .= '<td><span>' . $cnt->Deadline . '</span></td>';		$str .= '</tr>';	}}$str .= '</table></div>';?>