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

$t = new cPTemplate ( 'subether/components/orders/templates/history.php' );

$str = '';

if ( isset( $_POST['vars'] ) && ( $data = json_decode( $_POST['vars'] ) ) )
{
	if ( $data->oid && !$data->hid )
	{
		$rows = GetUserActivity( '*', false, false, 'SBookOrders', $data->oid );
		
		if ( $hours = $database->fetchObjectRows ( '
			SELECT ID 
			FROM SBookHours
			WHERE Title = "orders"
			AND ProjectID = \'' . $data->oid . '\' 
			ORDER BY ID ASC
		' ) )
		{
			$hids = array();
			
			foreach( $hours as $hr )
			{
				$hids[] = $hr->ID;
			}
			
			if ( $hids && is_array( $hids ) )
			{
				$rows = GetUserActivity( '*', false, false, 'SBookHours', implode( ',', $hids ), $rows );
			}
		}
	}
	else if ( $data->oid && $data->hid )
	{
		$rows = GetUserActivity( '*', false, false, 'SBookHours', $data->hid );
	}
}

if ( $rows )
{
	$str .= '<table style="width:100%">';
	
	$str .= '<tr>';
	$str .= '<td><strong>ID:</strong></td>';
	$str .= '<td><strong>Date:</strong></td>';
	$str .= '<td><strong>Admin:</strong></td>';
	$str .= '<td><strong>Description:</strong></td>';
	$str .= '<td><strong>User:</strong></td>';
	$str .= '</tr>';
	
	foreach ( $rows as $row )
	{
		$str .= '<tr>';
		$str .= '<td>#' . $row->ObjectID . '</td>';
		$str .= '<td>' . date( 'd/m/Y H:i', strtotime( $row->DateCreated ) ) . '</td>';
		$str .= '<td>' . $row->UserName . '</td>';
		$str .= '<td>' . $row->Subject . '</td>';
		$str .= '<td>' . $row->ConnectedName . '</td>';
		$str .= '</tr>';
	}
	
	$str .= '</table>';
}

$t->Content = $str;

die ( 'ok<!--separate-->' . $t->render() );

?>
