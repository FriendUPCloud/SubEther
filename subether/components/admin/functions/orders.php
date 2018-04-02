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

$str = '<div id="Orders">';

$str .= '<div class="tabs">';
//$str .= '<label for="show-browse-menu" class="show-browse-menu">Show Menu</label> <input type="checkbox" id="show-browse-menu" role="button">';
$str .= '<ul id="OrdersMenu">';
$str .= '<li class="pending">';
$str .= '<a ' . ( $_REQUEST[ 's' ] == '0' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?r=' . $_REQUEST['r'] . '&s=0">';
$str .= '<span class="icon"></span>';
$str .= '<span class="name">Pending</span>';
$str .= '</a>';
$str .= '</li>';
$str .= '<li class="active">';
$str .= '<a ' .  ( $_REQUEST[ 's' ] == '' || $_REQUEST[ 's' ] == '1' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?r=' . $_REQUEST['r'] . '&s=1">';
$str .= '<span class="icon"></span>';
$str .= '<span class="name">Active</span>';
$str .= '</a>';
$str .= '</li>';
$str .= '<li class="onhold">';
$str .= '<a ' . ( $_REQUEST[ 's' ] == '2' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?r=' . $_REQUEST['r'] . '&s=2">';
$str .= '<span class="icon"></span>';
$str .= '<span class="name">OnHold</span>';
$str .= '</a>';
$str .= '</li>';
$str .= '<li class="canceled">';
$str .= '<a ' . ( $_REQUEST[ 's' ] == '3' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?r=' . $_REQUEST['r'] . '&s=3">';
$str .= '<span class="icon"></span>';
$str .= '<span class="name">Canceled</span>';
$str .= '</a>';
$str .= '</li>';
$str .= '<li class="finished">';
$str .= '<a ' . ( $_REQUEST[ 's' ] == '4' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?r=' . $_REQUEST['r'] . '&s=4">';
$str .= '<span class="icon"></span>';
$str .= '<span class="name">Finished</span>';
$str .= '</a>';
$str .= '</li>';
$str .= '<li class="archived">';
$str .= '<a ' . ( $_REQUEST[ 's' ] == '5' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?r=' . $_REQUEST['r'] . '&s=5">';
$str .= '<span class="icon"></span>';
$str .= '<span class="name">Archived</span>';
$str .= '</a>';
$str .= '</li>';
$str .= '</ul>';
$str .= '<div style="clear:both" class="clearboth"></div>';
$str .= '</div>';

$str .= '<div class="heading"><table><tr>';
$str .= '<td class="col1">#</td>';
$str .= '<td class="col2">Name</td>';
$str .= '<td class="col3">Progress</td>';
$str .= '<td class="col4"></td>';
$str .= '</tr></table></div>';

$str .= '<div class="list"><ul>';

if( $orders = $database->fetchObjectRows( $q = '
	SELECT 
		o.* 
	FROM 
		SBookOrders o,
		SBookContact c 
	WHERE
			o.Status = \'' . ( ( !isset( $_REQUEST['s'] ) || $_REQUEST['s'] == '' ) ? '1' : ( $_REQUEST['s'] == '0' ? '' : $_REQUEST['s'] ) ) . '\' 
		AND o.CategoryID = \'' . $parent->folder->CategoryID . '\' 
		AND o.IsDeleted = "0"
		AND c.ID = o.CustomerID 
	ORDER BY 
		o.ID ASC 
' ) )
{
	$i = 1;
	
	foreach( $orders as $o )
	{
		$str .= '<li><div class="header sw' . ( $sw = ( $sw == 1 ? 2 : 1 ) ) . '" id="OrderID_' . $o->ID . '" onclick="EditOrder(' . $o->ID . ')"><table><tr>';
		$str .= '<td class="col1">[' . $i++ . ']</td>';
		$str .= '<td class="col2">' . GetUserDisplayname( $o->CustomerID ) . '</td>';
		$str .= '<td class="col3">' . ( $o->Progress ? ( $o->Progress . '%' ) : '0%' ) . '</td>';
		$str .= '<td class="col4">';
		$str .= '<span onclick="SaveOrder(' . $o->ID . ')"> [save] </span>';
		$str .= '<span onclick="DeleteOrder(' . $o->ID . ')"> [delete] </span>';
		$str .= '</td>';
		$str .= '</tr></table></div>';
		
		$str .= '<div class="inner" id="OrderDetails_' . $o->ID . '"></div></li>';
	}
}

// --- Create new order -----------------------------------------------------

$str .= '<li><div class="header sw' . ( $sw = ( $sw == 1 ? 2 : 1 ) ) . '" id="OrderID_0" onclick="EditOrder(\'0\')"><table><tr>';
$str .= '<td colspan="3">Create</td>';
$str .= '<td class="col4">';
$str .= '<span onclick="SaveOrder(\'0\')"> [save] </span>';
$str .= '</td>';
$str .= '</tr></table></div>';

$str .= '<div class="inner" id="OrderDetails_0"></div></li>';

$str .= '</ul></div>';

$str .= '</div>';

?>
