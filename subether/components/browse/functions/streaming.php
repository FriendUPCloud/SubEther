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

if( $rows = WatchSeriesParser( str_replace( ' ', '+', $_REQUEST[ 'q' ] ), $_REQUEST[ 'l' ] ) )
{
	$mstr .= '<div class="streaming"><table>';
	if( !is_array( $rows ) )
	{
		$mstr .= '<tr><td>' . $rows . '</td></tr>';
	}
	else
	{
		foreach( $rows as $obj )
		{
			$mstr .= '<tr><td class="category" colspan="4"><h4>' . $obj->title . '</h4></td></tr>';
			if( $obj->item[0]->views )
			{
				$mstr .= '<tr class="head"><th class="title">Name</th>';
				$mstr .= '<th class="views">Views</th>';
			}
			else
			{
				$mstr .= '<tr class="head"><th colspan="2" class="title">Name</th>';
			}
			$mstr .= '</tr>';
			if( $obj->item )
			{
				foreach( $obj->item as $r )
				{
					if( $obj->item[0]->views )
					{
						$mstr .= '<tr><td><a class="title" title="' . $r->title . '" href="' . ( !strstr( $r->href, 'http' ) && $_REQUEST[ 'm' ] ? $obj->url : ( /*$parent->path .*/ 'browse/?r=streaming&l=true&q=' ) ) . $r->href . '">' . dot_trim( $r->name, 100 ) . '</a></td>';
						$mstr .= '<td><span class="views">' . $r->views . '</span></td>';
					}
					else
					{
						$mstr .= '<tr><td colspan="2"><a class="title" title="' . $r->title . '" href="' . ( !strstr( $r->href, 'http' ) && $_REQUEST[ 'm' ] ? $obj->url : ( /*$parent->path .*/ 'browse/?r=streaming&l=true&q=' ) ) . $r->href . '">' . dot_trim( $r->name, 100 ) . '</a></td>';
					}
					$mstr .= '</tr>';
				}
			}
		}
	}
	$mstr .= '</table></div>';
}

?>
