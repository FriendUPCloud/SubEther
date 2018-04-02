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

$str = '';

$str .= '<div class="Box"><table style="width:100%;"><tbody>';

if ( $_REQUEST['accid'] && ( $acc = $database->fetchObjectRow( '
	SELECT
		*
	FROM
		SBookAccounts
	WHERE
		UserID = \'' . $webuser->ID . '\' AND ID = \'' . $_REQUEST['accid'] . '\' 
	ORDER BY
		ID ASC
' ) ) )
{
	$str .= '<tr>';
	$str .= '<td colspan="3" align="left"><strong>' . $acc->Name . ' ' . account_format( $acc->Account ) . '</strong>';
	$str .= ' <a href="javascript:void(0)" onclick="openWindow( \'Bank\', \'' . $acc->ID . '\', \'payment\' )"><u>Payment</u></a>';
	$str .= ' <a href="javascript:void(0)" onclick="openWindow( \'Bank\', \'' . $acc->ID . '\', \'transfer\' )"><u>Transfer</u></a></td>';
	$str .= '<td align="right"><strong>Balance <span' . ( $acc->Balance < 0 ? ' class="red"' : '' ) . '>' . number_format( $acc->Balance, 2, ',', '' ) . '</span></strong></td>';
	$str .= '<td align="right"><strong>Disposable <span' . ( $acc->Disposable < 0 ? ' class="red"' : '' ) . '>' . number_format( $acc->Disposable, 2, ',', '' ) . '</span></strong></td>';
	$str .= '</tr>';
	
	$str .= '<tr>';
	$str .= '<th align="left" style="width:100px;">Date</th>';
	$str .= '<th align="left">Description</th>';
	$str .= '<th style="width:120px;" align="right">Out</th>';
	$str .= '<th style="width:120px;" align="right">Inn</th>';
	$str .= '<th style="width:120px;" align="right">More</th>';
	$str .= '</tr>';
	
	if ( $rows = $database->fetchObjectRows( $q = '
		SELECT
			t.*, c.Username, c.Firstname, c.Middlename, c.Lastname 
		FROM
			SBookTransaction t,
			SBookAccounts a, 
			SBookContact c 
		WHERE
			( ( t.From = \'' . $acc->Account . '\' AND a.Account = t.To ) OR ( t.To = \'' . $acc->Account . '\' AND a.Account = t.From ) )
			AND a.UserID = c.UserID AND c.NodeID = "0" 
		ORDER BY
			t.ProcessCreated DESC, t.ID DESC 
	' ) )
	{
		foreach ( $rows as $row )
		{
			$dn = ( $row->From == $acc->Account ? 'To: ' : 'From: ' );
			
			$str .= '<tr>';
			$str .= '<td>' . date( 'd.m.Y', strtotime( $row->ProcessCreated ) ) . '</td>';
			$str .= '<td>' . ( $row->Name ? $row->Name : ( $dn . $row->Username ) ) . '</td>';
			$str .= '<td align="right" class="red">' . ( $row->From == $acc->Account ? number_format( $row->Amount, 2, ',', '' ) : '' ) . '</td>';
			$str .= '<td align="right">' . ( $row->To == $acc->Account ? number_format( $row->Amount, 2, ',', '' ) : '' ) . '</td>';
			$str .= '<td align="right"><em></em></td>';
			$str .= '</tr>';
		}
	}
	//die( $q . ' --' );
}

$str .= '</tbody></table></div>';

?>
