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

$mbase = 'subether/modules/nodes';

$nodes = $database->fetchObjectRows( 'SELECT Url, Version, Uptime, Open, Users, Rating, Location FROM SNodes WHERE IsConnected = "1" AND IsPending = "0" AND IsDenied = "0" AND Open >= "0" ORDER BY ID ASC' );

// Setup base templates -----------------------------------------------------------
$tmp = new cPTemplate ( $mbase . '/templates/module.php' );
//$tmp->MiddleCol = '<div class="Box">Coming Soon</div>';

$str = '<style>
#Field_middle
{
	padding: 15px;
}

#Field_middle table, #Field_middle tbody
{
	//border-collapse: collapse;
	border-collapse: separate;
	border-spacing: 0;
	padding: 0;
}

#Field_middle table
{
	width: 100%;
	border: 1px solid #c0c0c0;
	border-radius: 3px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
}

#Field_middle table th + th, table td + td
{
	border-left: 1px solid #c0c0c0;
}

#Field_middle table tr + tr td
{
	border-top: 1px solid #c0c0c0;	
}

#Field_middle table th
{
	text-align: left;
	text-transform: uppercase;
}

#Field_middle table th, #Field_middle table td
{
	padding: 10px;
}

#Field_middle table tr.sw1
{
	background: #dcdddd;
}
</style>';

if( $nodes )
{
	$sw = 2;
	$str .= '<table><tr>';
	$str .= '<th>Node</th>';
	$str .= '<th>Version</th>';
	$str .= '<th>Uptime</th>';
	$str .= '<th>Signups</th>';
	$str .= '<th>Total Users</th>';
	$str .= '<th>Rating</th>';
	$str .= '<th>Location</th></tr>';
	foreach( $nodes as $node )
	{
		$sw = ( $sw == 2 ? 1 : 2 );
		$str .= '</tr><tr class="sw' . $sw . '">';
		$str .= '<td><a target="_BLANK" href="' . $node->Url . '">' . $node->Url . '</a></td>';
		$str .= '<td>' . $node->Version . '</td>';
		$str .= '<td>' . ( $node->Uptime ? $node->Uptime : '100%' ) . '</td>';
		$str .= '<td>' . ( $node->Open ? 'Open' : 'Closed' ) . '</td>';
		$str .= '<td>' . $node->Users . '</td>';
		$str .= '<td>' . ( $node->Rating ? $node->Rating : 'No rating yet' ) . '</td>';
		$str .= '<td>' . $node->Location . '</td>';
	}
	$str .= '</tr></table>';
}

$tmp->MiddleCol = $str;

$extension .= $tmp->render ();

?>
