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

function ConvertToTime( $time )
{
	if( !$time ) return;
	
	if( strstr( $time, ':' ) )
	{
		$time = str_replace( ':', '', $time );
	}
	if( substr( $time, 0, 1 ) == '0' )
	{
		$time = str_pad( $time, 4, 0, STR_PAD_RIGHT );
	}
	else if( strlen( $time ) == 3 )
	{
		$time = str_pad( $time, 4, 0, STR_PAD_LEFT );
	}
	else if( strlen( $time ) == 2 )
	{
		$time = str_pad( $time, 4, 0, STR_PAD_RIGHT );
	}
	else if( strlen( $time ) == 1 )
	{
		$time = str_pad( $time, 4, 0, STR_PAD_BOTH );
	}
	
	$time = explode( ':', substr_replace( $time, ':', -2, 0 ) );
	
	$hours = ( $time[0] > '23' ? '00' : $time[0] );
	$minutes = ( $time[1] > '59' ? '00' : $time[1] );
	
	return $hours . ':' . $minutes;
}

if( !function_exists( 'DateSpan' ) )
{
	function DateSpan( $fromdate, $todate )
	{
		if( !$fromdate && $todate ) return false;
		
		$dates = array();
		$current = strtotime( $fromdate );
		$last = strtotime( $todate );
		
		while( $current <= $last ) 
		{ 
			$dates[] = date( 'Y-m-d', $current );
			$current = strtotime( '+1 day', $current );
		}
		
		return $dates;
	}
}

?>
