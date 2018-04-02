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

function renderBookingCalendar( $date, $id, $inpid, $parent = false, $events = false )
{
	switch( $parent->agent )
	{
		case 'web':
			return renderWebCalendar( $date, $id, $inpid, $events );
			break;
		
		default:
			return renderMobileCalendar( $date, $id, $inpid, $events );
			break;
	}
}

function renderWebCalendar( $date, $id, $inpid, $events = false )
{
	$year = date( 'Y', strtotime( $date ) );
	$month = date( 'm', strtotime( $date ) );
	$day = date( 'd', strtotime( $date ) );
	$week = date( 'W', strtotime( $date ) );
	
	$nstr = '<tr class="nv"><th colspan="8">';
	$nstr .= '<div class="nav">';
	$nstr .= '<span class="Left"><a href="javascript:void(0)" class="prevYear" title="' . i18n ( 'i18n_Previous_Year' ) . '" onclick="RefreshBookingCalendar(\'' . $id . '\',\'' . $inpid . '\',' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) ), date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) )-1 ) . ')"> << </a>';
	$nstr .= '<a href="javascript:void(0)" class="prevMonth" title="' . i18n ( 'i18n_Previous_Month' ) . '" onclick="RefreshBookingCalendar(\'' . $id . '\',\'' . $inpid . '\',' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) )-1, date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) ) ) . ')"> < </a></span>';
	$nstr .= '<span class="Center"><a href="javascript:void(0)" title="' . i18n ( 'i18n_Todays_Date' ) . '" onclick="RefreshBookingCalendar(\'' . $id . '\',\'' . $inpid . '\',' . mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) ) . ')">' . i18n ( 'i18n_' . date ( 'F', strtotime ( $year . '-' . $month ) ) ) . ', ' . $year . '</a></span>';
	$nstr .= '<span class="Right"><a href="javascript:void(0)" class="nextMonth" title="' . i18n ( 'i18n_Next_Month' ) . '" onclick="RefreshBookingCalendar(\'' . $id . '\',\'' . $inpid . '\',' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) )+1, date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) ) ) . ')"> > </a>';
	$nstr .= '<a href="javascript:void(0)" class="nextYear" title="' . i18n ( 'i18n_Next_Year' ) . '" onclick="RefreshBookingCalendar(\'' . $id . '\',\'' . $inpid . '\',' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) ), date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) )+1 ) . ')"> >> </a> </span>';
	$nstr .= '</div>';
	$nstr .= '</th></tr>';
	
	$o = new CalendarMonth( $date = ( $year . '-' . $month . '-' . $day ) );
	
	if ( count ( $events ) && is_array ( $events ) )
	{
		$o->ImportEvents ( $events, 'DateStart', 'DateEnd' );
	}
	
	$i = 0; $sw = 2; $s = 0; $y = 0; $evt = '';
	$str = '<table class="CalendarMonth"><tbody>' . $nstr;
	foreach( $o->weeks as $key=>$w )
	{	
		$x = 0;
		$ch = 1 + $s;
		$sw = ( $sw == '1' ? '2' : '1' );
		$hstr = '<tr class="hd">';
		$dstr = '<tr class="sw' . $sw . '">';
		foreach( $w->days as $k=>$d )
		{
			$ch = ( $ch + 1 ) % 2; $evnt = '';
			
			$ct = strtotime( $d->date );
			
			$cw = new DateTime();
			$cw->setISODate( date( 'Y', $ct ), date( 'W', $ct ), 1 );
			
			if( $x == 0 )
			{	
				$hstr .= '<th><div class="Week"><div class="heading">' . i18n ( 'i18n_s_Week' ) . '</div></div></th>';
				$dstr .= '<td><div class="Week"><div class="number">' . $d->week . '</div></div></td>';
			}
			
			if( $i == 0 )
			{
				$hstr .= '<th><div class="' . $d->name . '"><div class="number">' . i18n ( 'i18n_s_' . $d->name ) . '</div></div></th>';
			}
			
			if( date( 'Y-m-d', strtotime( $d->date ) ) == $date ) $sel = ' Selected'; else $sel = '';
			
			$slot = ( $d->events && $d->events[0]->Reserved ? ' Reserved' : false );
			
			$dstr .= ( $d->month == $o->month ? '<td class="ch' . ( $ch + 1 ) . '"><div class="' . $d->name . ( $slot ? $slot : ' Available' ) . $sel . '" ' . ( !$slot ? 'onclick="SetBookingDate(\'' . $inpid . '\',\'' . date( 'D, d.m.Y', $ct ) . '\',' . $ct . ')"' : '' ) . '><div class="events">' . $evnt . '</div><div class="number">' . $d->day . '</div></div></td>' : '<td class="ch' . ( $ch + 1 ) . '"><div class="Space" ' . ( !$slot ? 'onclick="SetBookingDate(\'' . $inpid . '\',\'' . date( 'D, d.m.Y', $ct ) . '\',' . $ct . ')"' : '' ) . '><div class="number">' . $d->day . '</div></div></td>' );
			
			$x++;
		}
		if( $i == 0 && $hstr ) $str .= $hstr . '</tr>';
		if( $dstr ) $str .= $dstr . '</tr>';
		$s = ( $s + 1 ) % 2;
		$i++; $y++;
	}
	$str .= '</tbody></table>';
	return $str;
}

function renderMobileCalendar( $date, $id, $inpid, $events = false )
{
	$year = date( 'Y', strtotime( $date ) );
	$month = date( 'm', strtotime( $date ) );
	$day = date( 'd', strtotime( $date ) );
	$week = date( 'W', strtotime( $date ) );
	
	$nstr .= '<div class="nav">';
	$nstr .= '<span class="Left"><a href="javascript:void(0)" class="prevYear" title="' . i18n ( 'i18n_Previous_Year' ) . '" onclick="RefreshBookingCalendar(\'' . $id . '\',\'' . $inpid . '\',' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) ), date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) )-1 ) . ')"> << </a>';
	$nstr .= '<a href="javascript:void(0)" class="prevMonth" title="' . i18n ( 'i18n_Previous_Month' ) . '" onclick="RefreshBookingCalendar(\'' . $id . '\',\'' . $inpid . '\',' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) )-1, date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) ) ) . ')"> < </a></span>';
	$nstr .= '<span class="Center"><a href="javascript:void(0)" title="' . i18n ( 'i18n_Todays_Date' ) . '" onclick="RefreshBookingCalendar(\'' . $id . '\',\'' . $inpid . '\',' . mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) ) . ')">' . i18n ( 'i18n_' . date ( 'F', strtotime ( $year . '-' . $month ) ) ) . ', ' . $year . '</a></span>';
	$nstr .= '<span class="Right"><a href="javascript:void(0)" class="nextMonth" title="' . i18n ( 'i18n_Next_Month' ) . '" onclick="RefreshBookingCalendar(\'' . $id . '\',\'' . $inpid . '\',' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) )+1, date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) ) ) . ')"> > </a>';
	$nstr .= '<a href="javascript:void(0)" class="nextYear" title="' . i18n ( 'i18n_Next_Year' ) . '" onclick="RefreshBookingCalendar(\'' . $id . '\',\'' . $inpid . '\',' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) ), date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) )+1 ) . ')"> >> </a> </span>';
	$nstr .= '</div>';
	
	$o = new CalendarMonth( $date = ( $year . '-' . $month . '-' . $day ) );
	
	if ( count ( $events ) && is_array ( $events ) )
	{
		$o->ImportEvents ( $events, 'DateStart', 'DateEnd' );
	}
	
	$i = 0; $sw = 2; $s = 0; $y = 0; $evt = '';
	$str = '<div class="CalendarMonth">' . $nstr;
	foreach( $o->weeks as $key=>$w )
	{	
		$x = 0;
		$ch = 1 + $s;
		$sw = ( $sw == 1 ? 2 : 1 );
		
		foreach( $w->days as $k=>$d )
		{
			$ch = ( $ch + 1 ) % 2; $evnt = '';
			
			$ct = strtotime( $d->date );
			
			$cw = new DateTime();
			$cw->setISODate( date( 'Y', $ct ), date( 'W', $ct ), 1 );
			
			if( date( 'Y-m-d', strtotime( $d->date ) ) == $date ) $sel = ' Selected'; else $sel = '';
			
			$slot = ( $d->events && $d->events[0]->Reserved ? ' Reserved' : false );
			
			$dstr .= ( $d->month == $o->month ? '<div class="' . $d->name . ( $slot ? $slot : ' Available' ) . $sel . '" ' . ( !$slot ? 'onclick="SetBookingDate(\'' . $inpid . '\',\'' . date( 'D, d.m.Y', $ct ) . '\',' . $ct . ')"' : '' ) . '><div class="number">' . date( 'D, d.m.Y', $ct ) . '</div></div>' : '<div class="Space" ' . ( !$slot ? 'onclick="SetBookingDate(\'' . $inpid . '\',\'' . date( 'D, d.m.Y', $ct ) . '\',' . $ct . ')"' : '' ) . '><div class="number">' . date( 'D, d.m.Y', $ct ) . '</div></div>' );
			
			$x++;
		}
		$s = ( $s + 1 ) % 2;
		$i++; $y++;
	}
	if( $dstr ) $str .= $dstr;
	$str .= '</div>';
	return $str;
}

?>
