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

/*if( isset( $_REQUEST[ 'sf' ] ) && isset( $_REQUEST[ 'q' ] ) && $webuser->ID > 0 )
{
	die( 'route<!--separate-->en/home/browse/?q=' . $_REQUEST[ 'q' ] . '&r=' . $_REQUEST[ 'sf' ] );
}*/
if( isset( $_REQUEST[ 'sf' ] ) && isset( $_REQUEST[ 'q' ] ) && $_REQUEST[ 'sf' ] == 'wall' )
{
	die( 'route<!--separate-->' . $parent->route . '?q=' . $_REQUEST[ 'q' ] . '&r=' . $_REQUEST[ 'sf' ] );
	//die( print_r( $parent,1 ) . ' --' );
}
else if( isset( $_REQUEST[ 'sf' ] ) && isset( $_REQUEST[ 'q' ] ) )
{
	die( 'route<!--separate-->' . BASE_URL . 'browse/?q=' . $_REQUEST[ 'q' ] . '&r=' . $_REQUEST[ 'sf' ] );
}

$seconds = round( microtime_float() - $time_start );

//die( $seconds . ' .. ' );

if( isset( $_REQUEST[ 'q' ] ) && ( isset( $_REQUEST[ 'a' ] ) ) || isset( $_REQUEST[ 'p' ] ) )
{

	$keywords = str_replace( ' ', '+', $_REQUEST[ 'q' ] );
	$_REQUEST[ 'p' ] > 1 ? $page = $_REQUEST[ 'p' ] : $page = 1;
	$maxlimit = 10;
	
	$select  = 'SELECT e.* ';
	$from    = 'FROM SEngineSearch e ';
	$where   = 'WHERE Title LIKE "%' . $keywords . '%" OR Link LIKE "%' . $keywords . '%" OR Leadin LIKE "%' . $keywords . '%" OR KeyWords LIKE "%' . $keywords . '%" ';
	$orderby = 'ORDER BY e.SortOrder ASC, e.DateModified DESC ';
	$limit   = 'LIMIT ' . ( $page > 1 ? ( round( ( $page-1 ) * $maxlimit ) . ', ' . $maxlimit ) : $maxlimit );
	
	if ( MODULE_ENGINE == 'bing' && $rows = BingParser( $keywords, $maxlimit, $page ) )
	{
		if( isset( $_REQUEST['encoding'] ) && $_REQUEST['encoding'] == 'json' )
		{
			die( 'ok<!--separate-->' . json_encode( $rows ) );
		}
		
		//die( print_r( $rows,1 ) . ' ..' );
		/*if ( $tc = $rows['results'] )
		{
			$i = 0;
			$ii = 0;
			$checked = array();
			$out = '';
			$toptbl = '<table>';
			foreach( $tc as $t )
			{
				$i++;
				$check = search_options( $t->Title, $keywords );
				if( ++$ii <= 4 && !in_array( $check, $checked ) ) $out .= '<tr><td>' . ( $checked[] = $check ) . '</td></tr>';
			}
			$btmtbl = '</table>';
			if( count( $checked ) > 1 ) $options = $toptbl . $out . $btmtbl;
			$total = $i;
			$maxpge = round( $total / $maxlimit );
		}*/
		
		if( count( $rows['pages'] ) > 1 )
		{
			$nav = '<table><tr>';
			$page > 1 ? ( $nav .= '<td><a class="btn prev" href="?q=' . $keywords . '&p=' . ( $page > 1 ? ( $page-1 ) : 1 ) . '">Previous</a></td>' ) : '';
			foreach( $rows['pages'] as $k=>$a )
			{
				$nav .= '<td><a ' . ( $page == $k ? 'class="current"' : '' ) . ' href="?q=' . $keywords . '&p=' . $k . '">' . $k . '</a></td>';
			}
			$nav .= '<td><a class="btn next" href="?q=' . $keywords . '&p=' . ( $page != end( $rows['pages'] ) ? ( $page+1 ) : end( $rows['pages'] ) ) . '">Next</a></td>';
			$nav .= '</tr></table>';
		}
		
		$l = 1;
		$result = '<ol>';
		if( $rows['results'] )
		{
			foreach( $rows['results'] as $r )
			{
				$result .= '<li><div>';
				$result .= '<h3>';
				$result .= '<a target="_BLANK" href="' . $r->Link . '">' . str_mark( dot_trim( $r->Title, 75 ), $keywords ) . '</a>';
				$result .= '</h3><div>';
				$result .= '<div><p>' . str_mark( dot_trim( $r->Link, 75 ), $keywords ) . '</p></div>';
				$result .= '<div><span>' . str_mark( dot_trim( $r->Leadin, 200, $keywords ), $keywords ) . '</span></div>';
				$result .= '</div>';
				$result .= '</div></li>';
				$l++;
			}
		}
		$result .= '</ol>';
		
		$time_end = microtime_float();
		
		$stats = '<div>' . ( $page > 1 ? 'Page ' . $page . ' of about ' : 'About ' ) . $rows['count'] . ' results (' . number_format( ( $time = $time_end - $time_start ), 2, '.', '' ) . ' seconds)</div>';
		
		$navigation = '<div>' . $nav . '</div>';
		
		if( isset( $_REQUEST[ 'bajaxrand' ] ) ) die ( 'ok<!--separate-->' . $stats . $result . $navigation . ( count( $checked ) > 1 ? '<!--separate-->' . $options : '' ) );
	}
	else if ( $rows = $database->fetchObjectRows ( $q = ( $select . $from . $where . $orderby . $limit ) ) )
	{
		if ( $tc = $database->fetchObjectRows ( $select . $from . $where . $orderby ) )
		{
			$i = 0;
			$ii = 0;
			$checked = array();
			$out = '';
			$toptbl = '<table>';
			foreach( $tc as $t )
			{
				$i++;
				$check = search_options( $t->Title, $keywords );
				if( ++$ii <= 4 && !in_array( $check, $checked ) ) $out .= '<tr><td>' . ( $checked[] = $check ) . '</td></tr>';
			}
			$btmtbl = '</table>';
			if( count( $checked ) > 1 ) $options = $toptbl . $out . $btmtbl;
			$time_end = microtime_float();
			$total = $i;
			$maxpge = round( $total / $maxlimit );
		}
		
		if( $maxpge > 1 )
		{
			$nav = '<table><tr>';
			$page > 1 ? $nav .= '<td><a class="btn prev" href="/search?q=' . $keywords . '&p=' . ( $page > 1 ? ( $page-1 ) : 1 ) . '">Previous</a></td>' : '';
			for ( $a = 0; $a < $maxpge; $a++ )
			{
				$nav .= '<td><a ' . ( $page == ( $a+1 ) ? 'class="current"' : '' ) . ' href="/search?q=' . $keywords . '&p=' . ( $a+1 ) . '">' . ( $a+1 ) . '</a></td>';
			}
			$nav .= '<td><a class="btn next" href="/search?q=' . $keywords . '&p=' . ( $page != $maxpge ? ( $page+1 ) : $maxpge ) . '">Next</a></td>';
			$nav .= '</tr></table>';
		}
		
		$result = '<ol>';
		foreach( $rows as $r )
		{
			$result .= '<li><div>';
			$result .= '<h3>';
			$result .= '<a target="_BLANK" href="' . $r->Link . '">' . str_mark( dot_trim( $r->Title, 75 ), $keywords ) . '</a>';
			$result .= '</h3><div>';
			$result .= '<div><p>' . str_mark( dot_trim( $r->Link, 75 ), $keywords ) . '</p></div>';
			$result .= '<div><span>' . str_mark( dot_trim( $r->Leadin, 200, $keywords ), $keywords ) . '</span></div>';
			$result .= '</div>';
			$result .= '</div></li>';
			
		}
		$result .= '</ol>';
		
		$stats = '<div>' . ( $page > 1 ? 'Page ' . $page . ' of about ' : 'About ' ) . $total . ' results (' . number_format( ( $time = $time_end - $time_start ), 2, '.', '' ) . ' seconds)</div>';
		
		$navigation = '<div>' . $nav . '</div>';
		
		if( isset( $_REQUEST[ 'bajaxrand' ] ) ) die ( 'ok<!--separate-->' . $stats . $result . $navigation . ( count( $checked ) > 1 ? '<!--separate-->' . $options : '' ) );
	}
	else
	{
		die ( 'no results ' . $keywords );
	}

}
die();

?>
