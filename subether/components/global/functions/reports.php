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

$str = '<h2>Reports</h2><br>';

if( $reports = $database->fetchObjectRows ( '
	SELECT * 
	FROM SBookCaseList
	WHERE
			( Type = "Case" OR Type = "Bug" OR Type = "Feature" OR Type = "Other" )
		AND !IsFinished
	ORDER BY ID ASC 
' ) )
{
	$str .= '<table style="width:100%;margin-bottom:30px;"><tr>';
	$str .= '<td><strong>ID</strong></td>';
	$str .= '<td><strong>Type</strong></td>';
	$str .= '<td><strong>Name</strong></td>';
	$str .= '<td><strong>Description</strong></td>';
	$str .= '<td><strong>Category</strong></td>';
	$str .= '<td><strong>User</strong></td>';
	$str .= '<td><strong>Reported</strong></td>';
	$str .= '<td><strong>#</strong></td>';
	$str .= '</tr>';
	
	$cats = array(); $usrs = array();
	
	foreach( $reports as $u )
	{
		if( $u->CategoryID > 0 )
		{
			$cats[$u->CategoryID] = $u->CategoryID;
		}
		if( $u->UserID > 0 )
		{
			$usrs[$r->UserID] = $u->UserID;
		}
	}
	
	if( $cats && ( $cat = $database->fetchObjectRows ( '
		SELECT * 
		FROM SBookCategory
		WHERE ID IN (' . implode( ',', $cats ) . ')
		ORDER BY ID ASC 
	' ) ) )
	{
		$cats = array();
		
		foreach( $cat as $c )
		{
			$cats[$c->ID] = $c->Name;
		}
	}
	
	if( $usrs && ( $usr = $database->fetchObjectRows ( '
		SELECT * 
		FROM Users
		WHERE ID IN (' . implode( ',', $usrs ) . ')
		ORDER BY ID ASC 
	' ) ) )
	{
		$usrs = array();
		
		foreach( $usr as $u )
		{
			$usrs[$u->ID] = $u->Name;
		}
	}
	
	foreach( $reports as $r )
	{
		$image = false;
		
		if( $r->FileID && ( $img = $database->fetchObjectRow ( '
			SELECT * 
			FROM Image
			WHERE ID = \'' . $r->FileID . '\' 
			ORDER BY ID ASC 
		' ) ) )
		{
			$image = 'secure-files/images/' . $img->UniqueID . '/';
		}
		
		$str .= '<tr>';
		$str .= '<td fileid="' . ( $r->FileID > 0 ? $r->FileID : '0' ) . '">' . ( $image ? ( '<a target="_BLANK" href="' . $image . '">#' . $r->ID . '</a>' ) : ( '#' . $r->ID ) ) . '</td>';
		$str .= '<td>' . $r->Type . '</td>';
		$str .= '<td>' . $r->Name . '</td>';
		$str .= '<td>' . $r->Description . '</td>';
		$str .= '<td>' . ( isset( $cats[$r->CategoryID] ) ? $cats[$r->CategoryID] : $r->CategoryID ) . '</td>';
		$str .= '<td>' . ( isset( $usrs[$r->UserID] ) ? $usrs[$r->UserID] : $r->UserID ) . '</td>';
		$str .= '<td>' . $r->DateCreated . '</td>';
		$str .= '<td><button onclick="updateReport(\'' . $r->ID . '\',1)">Finish</button></td>';
		$str .= '</tr>';
	}
	
	$str .= '</table>';
}
else
{
	$str .= '<div>No pending reports ...</div>';
}



?>
