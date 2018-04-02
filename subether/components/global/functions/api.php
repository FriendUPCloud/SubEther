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

$str = '<h2>API</h2><br>';

$str .= '<table><tr>';
$str .= '<td><strong>Url</strong></td>';
$str .= '<td><strong>Name</strong></td>';
$str .= '<td><strong>App</strong></td>';
$str .= '<td><strong>Username</strong></td>';
$str .= '<td><strong>Password</strong></td>';
$str .= '<td><strong>#</strong></td>';
$str .= '</tr>';

if ( $accounts = $database->fetchObjectRows ( '
	SELECT * 
	FROM SBookApiAccounts 
	ORDER BY ID ASC 
' ) )
{
	foreach( $accounts as $acc )
	{
		$str .= '<tr title="' . $acc->SessionID . '">';
		$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveApiAccount(this,\'' . $acc->ID . '\')}" name="Url" value="' . $acc->Url . '"/></td>';
		$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveApiAccount(this,\'' . $acc->ID . '\')}" name="Name" value="' . $acc->Name . '"/></td>';
		$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveApiAccount(this,\'' . $acc->ID . '\')}" name="App" value="' . $acc->App . '"/></td>';
		$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveApiAccount(this,\'' . $acc->ID . '\')}" name="Username" value="' . $acc->Username . '"/></td>';
		$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveApiAccount(this,\'' . $acc->ID . '\')}" name="Password" value="' . $acc->Password . '"/></td>';
		$str .= '<td><div onclick="DeleteApiAccount(\'' . $acc->ID . '\')"> x </div></td>';
		$str .= '</tr>';
	}
}

$str .= '<tr>';
$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveApiAccount(this)}" name="Url" value=""/></td>';
$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveApiAccount(this)}" name="Name" value=""/></td>';
$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveApiAccount(this)}" name="App" value=""/></td>';
$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveApiAccount(this)}" name="Username" value=""/></td>';
$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveApiAccount(this)}" name="Password" value=""/></td>';
$str .= '<td></td>';
$str .= '</tr></table>';

?>
