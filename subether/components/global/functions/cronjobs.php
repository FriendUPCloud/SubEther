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

$str = '<h2>Cronjobs</h2><br>';

if( $jobs = $database->fetchObjectRows ( '
	SELECT * 
	FROM SBookCronJobs 
	ORDER BY ID ASC, SortOrder ASC 
' ) )
{
	$str .= '<table style="width:100%;margin-bottom:30px;"><tr>';
	$str .= '<td><strong>Delay</strong></td>';
	$str .= '<td><strong>Filename</strong></td>';
	$str .= '<td><strong>Lastrun</strong></td>';
	$str .= '<td><strong>Error</strong></td>';
	$str .= '<td><strong>IsRunning</strong></td>';
	$str .= '<td><strong>IsActive</strong></td>';
	$str .= '<td><strong>IsMaintenance</strong></td>';
	$str .= '<td><strong>#</strong></td>';
	$str .= '</tr>';
	
	foreach( $jobs as $j )
	{
		$str .= '<tr>';
		$str .= '<td>' . $j->MinDelay . '</td>';
		$str .= '<td>' . $j->Filename . '</td>';
		$str .= '<td>' . $j->LastExec . '</td>';
		$str .= '<td>' . $j->Error . '</td>';
		$str .= '<td>' . ( $j->IsRunning ? 'yes' : 'no' ) . '</td>';
		$str .= '<td>' . ( $j->IsActive ? 'yes' : 'no' ) . '</td>';
		$str .= '<td>' . ( $j->IsMaintenance ? 'yes' : 'no' ) . '</td>';
		$str .= '<td>';
		if ( !$j->IsMaintenance || ( $j->IsMaintenance && $j->IsActive ) )
		{
			$str .= '<button onclick="updateCronjob(\'' . $j->ID . '\')">' . ( $j->IsActive ? 'Stop' : 'Start' ) . '</button>';
		}
		$str .= '</td>';
		$str .= '</tr>';
	}
	
	$str .= '</table>';
}
else
{
	$str .= '<div>No cronjobs ...</div>';
}

?>
