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

$str = '<h2>Statistics</h2><br>';

$str .= '<table><tr>';
$str .= '<td><strong>Component</strong></td>';
$str .= '<td><strong>Type</strong></td>';
$str .= '<td><strong>Name</strong></td>';
$str .= '<td><strong>Counter</strong></td>';
$str .= '<td><strong>#</strong></td>';
$str .= '</tr>';

if ( $settings = $database->fetchObjectRows ( '
	SELECT * 
	FROM SBookSettings 
	ORDER BY ID ASC 
' ) )
{
	foreach( $settings as $set )
	{
		$str .= '<tr>';
		$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveStatSetting(this,\'' . $set->ID . '\')}" name="Component" value="' . $set->Component . '"/></td>';
		$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveStatSetting(this,\'' . $set->ID . '\')}" name="Type" value="' . $set->Type . '"/></td>';
		$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveStatSetting(this,\'' . $set->ID . '\')}" name="Name" value="' . $set->Name . '"/></td>';
		$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveStatSetting(this,\'' . $set->ID . '\')}" name="Counter" value="' . $set->Counter . '"/></td>';
		$str .= '<td><div onclick="DeleteStatSetting(\'' . $set->ID . '\')"> x </div></td>';
		$str .= '</tr>';
	}
}

$str .= '<tr>';
$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveStatSetting(this)}" name="Component" value=""/></td>';
$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveStatSetting(this)}" name="Type" value=""/></td>';
$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveStatSetting(this)}" name="Name" value=""/></td>';
$str .= '<td><input type="text" onkeyup="if(event.keyCode == 13){SaveStatSetting(this)}" name="Counter" value=""/></td>';
$str .= '<td></td>';
$str .= '</tr></table>';

$str .= '<br><h4>What\'s in the database:</h4>';

if( $dbrows = $database->fetchObjectRows ( '
	SELECT *, SUM(Counter) AS Counter 
	FROM SBookStats 
	GROUP BY Component 
	ORDER BY Counter DESC 
' ) )
{
	foreach( $dbrows as $db )
	{
		$str .= '<div>' . $db->Component . ' (' . $db->Counter . ')</div>';
	}
}

?>
