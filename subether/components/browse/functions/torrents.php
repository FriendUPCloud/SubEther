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

if( $rows = TorrentFeed( str_replace( ' ', '+', $_REQUEST[ 'q' ] ) ) )
{
	//die( print_r( $rows,1 ) . ' ..' );
	$mstr .= '<div class="torrents"><table>';
	foreach( $rows as $obj )
	{
		$mstr .= '<tr><td class="category" colspan="4"><h4>' . $obj->title . '</h4></td></tr>';
		$mstr .= '<tr class="head"><th class="title">Torrent</th>';
		$mstr .= '<th class="age">Age</th>';
		$mstr .= '<th class="seeds">Seed</th>';
		$mstr .= '<th class="peers">Leech</th></tr>';
		if( $obj->item )
		{
			foreach( $obj->item as $r )
			{
				$icons = '<span class="icons"><a href="' . $r->magnetURI . '"><img style="position:relative;top:2px;height:14px;" title="Magnet Link" src="subether/gfx/magnet.png"></a></span>';
				$mstr .= '<tr><td>' . $icons . '<a class="title" target="_blank" href="' . $r->link . '">' . dot_trim( $r->title, 50 ) . '</a></td>';
				$mstr .= '<td><span class="age">' . TimeToHuman ( $r->pubDate ) . '</span></td>';
				$mstr .= '<td><span class="seeds green">' . ( $r->seeds ? $r->seeds : '0' ) . '</span></td>';
				$mstr .= '<td><span class="peers red">' . ( $r->peers ? $r->peers : '0' ) . '</span></td></tr>';
			}
		}
	}
	$mstr .= '</table></div>';
}

?>
