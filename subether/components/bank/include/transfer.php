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

$str  = '<table style="width:100%;">';
if ( $rows = $database->fetchObjectRows( '
	SELECT
		*
	FROM
		SBookAccounts
	WHERE
		UserID = \'' . $webuser->ID . '\' 
	ORDER BY
		ID ASC
' ) )
{
	$str .= '<tr><td>From Account</td><td><select name="From">';
	foreach ( $rows as $row )
	{
		$str .= '<option value="' . $row->Account . '">' . $row->Name . ' ' . account_format( $row->Account ) . ' (Disp. ' . number_format( $row->Disposable, 2, ',', '' ) . ')</option>';
	}
	$str .= '</select></td></tr>';
	$str .= '<tr><td>To Account</td><td><select name="To">';
	$str .= '<option>Choose account</option>';
	foreach ( $rows as $row )
	{
		$str .= '<option value="' . $row->Account . '">' . $row->Name . ' ' . account_format( $row->Account ) . ' (Disp. ' . number_format( $row->Disposable, 2, ',', '' ) . ')</option>';
	}
	$str .= '</select></td></tr>';
}
$str .= '<tr><td>Process Date</td><td><input name="ProcessDate" type="text" value="' . date( 'd.m.Y' ) . '" /></td></tr>';
$str .= '<tr><td>Amount</td><td><input name="Amount" type="text" /></td></tr>';
$str .= '<tr><td>Message</td><td><input name="Message" type="text" /></td></tr>';
$str .= '</table>';

?>
