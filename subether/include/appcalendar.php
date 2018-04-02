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

// Build calendar --------------------------------------------------------------------------

if( $_POST[ 'month' ] AND $_POST[ 'year' ] )
{
	$month = $_POST[ 'month' ];
	$year = $_POST[ 'year' ];
}
else
{
	$month = date( 'n' );
	$year = date( 'Y' );
}

if( $_REQUEST[ 'fid' ] ) $cid = $_REQUEST[ 'fid' ]; 
else if ( $pubField->ContentID ) $cid = $pubField->ContentID; 
else if ( $page->MainID ) $cid = $page->MainID;

if( $_REQUEST[ 'adm' ] && !$adm ) $adm = $_REQUEST[ 'adm' ];

if ( !function_exists ( 'appCalendar' ) )
{
	function appCalendar ( $year, $month, $cid, $adm )
	{
		global $page, $database;

		$i = 1;
		$rows = array ();
		$days = array ();
		$weeks = array ();
	
		while ( checkdate ( $month, $i, $year ) )
		{		
			$day = ( date ( 'D', strtotime ( ( $year . '-' . $month . '-' . $i ) ) ) );
		
			$days[$day] = $i;
		
			if ( !isset ( $days['Week'] ) )
			{
				$week = ( date ( 'W', strtotime ( ( $year . '-' . $month . '-' . $i ) ) ) );
				$days['Week'] = $week;
			}
			
			if ( $day == 'Sun' )
			{	
				$rows[] = $days;
				$days = array ();
				$weeks = array ();
			}
		
			$i++;
		}
	
		if ( count ( $days ) ) 
			$rows[] = $days;
	
		$dayNames = array ( 'Week', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' );
		$str = '';
		$appID = '';
		$sw = 2;
		$wn = 1;
		$start = 0;
		$i = 0;
	
		// Get next month
		$nyear = $year;
		$nmonth = $month + 1;
		if ( $nmonth > 12 ) 
		{
			$nmonth = 1;
			$nyear++;
		}	
	
		// Try to get day events
		$events = array ();
		if ( $devs = $database->fetchObjectRows ( '
			SELECT * FROM AppCalendar
			WHERE
				`Date` >= \'' . $year . '-' . $month . '-01\' AND
				`Date` < \'' . $nyear . '-' . $nmonth . '-01\' AND
				`ContentID` = \'' . $cid . '\'
		' ) )
		{
			foreach ( $devs as $d )
			{
				$dn = date ( 'd', strtotime ( $d->Date ) );
				if ( !$events[$dn] )
				{
					$events[$dn] = array ();
				}
				$events[$dn][] = $d;
			}
		}
	
		if( !$status ) $status = '0';
		foreach ( $rows as $key => $row )
		{
			$sw = ( $sw == '1' ? '2' : '1' );
			$str .= '<tr class="sw' . $sw . '">';		
			$ch = 1 + $start;
			foreach ( $dayNames as $dn )
			{
				$ch = ( $ch + 1 ) % 2;
				$se = '';
				$st = '';
				$ti = '';
				$ex = $dn;
				if ( !$row[$dn] )
				{
					$row[$dn] = '<div class="Space">&nbsp;</div>';
				}
				else if ( $dn == 'Week' )
				{
					$row[$dn] = '<div class="' . $ex . ' Nr' . $wn++ .'">' . $row[$dn] . '</div>';
				}
				else
				{
					// Get data
					$appID = 
						str_pad ( $row[$dn], 2, '0', STR_PAD_LEFT ) .
						str_pad ( $month, 2, '0', STR_PAD_LEFT ) . 
						$year;
					$monyear = str_pad ( $month, 2, '0', STR_PAD_LEFT ) . $year;
					$day = $row[$dn];
					$thisdate = date( 'Ymd', mktime( 0, 0, 0, str_pad ( $month, 2, '0', STR_PAD_LEFT ), str_pad ( $row[$dn], 2, '0', STR_PAD_LEFT ), $year ) );
					$maxdate = date( 'Ymd', mktime( 0, 0, 0, date('m'), date('d'), date('Y')+1 ) );
				
					if( $appID == date( 'dmY' ) ) 
					{
						$se = ' Selected';
					}
				
					// Make a padded day for checking in the events array
					$eday = str_pad ( $day, 2, '0', STR_PAD_LEFT );
				
					// If Saturday or Sunday set Closed
					if( $dn == 'Wed' OR $dn == 'Sun' )
					{
						$st = ' Closed';
						$ti = i18n ( 'Closed' );
						if( $adm == 1 ) $oc = 'onclick="markDay(\'' . $appID . '\')"'; else $oc = '';
					}					
					// If we have an event on this day
					else if( isset ( $events[$eday] ) ) 
					{
						// Check which title and status to set on day
						foreach ( $events[$eday] as $ev )
						{
							if ( $ev->Status == '1' )
							{	
								$st = ' Closed';
								$ti = i18n ( 'Closed' );
								if( $adm == 1 ) $oc = 'onclick="markDay(\'' . $appID . '\')"'; else $oc = '';
							}
							else if ( $ev->Status == '2' )
							{	
								$st = ' Full';
								$ti = i18n ( 'Fullbooked' );
								if( $adm == 1 ) $oc = 'onclick="markDay(\'' . $appID . '\')"'; else $oc = '';
							}
							else if ( $thisdate >= date( 'Ymd' ) && $thisdate <= $maxdate )
							{
								$st = ' Available';
								$ti = i18n ( 'Available' );
								$oc = 'onclick="markDay(\'' . $appID . '\')"';
							}
							else
							{
								$st = ' Disabled';
								$ti = '';
								if( $adm == 1 ) $oc = 'onclick="markDay(\'' . $appID . '\')"'; else $oc = '';
							}
						}
					}
					else 
					{
						if ( $thisdate >= date( 'Ymd' ) && $thisdate <= $maxdate )
						{
							$st = ' Available';
							$ti = i18n ( 'Available' );
							$oc = 'onclick="markDay(\'' . $appID . '\')"';
						}
						else
						{
							$st = ' Disabled';
							$ti = '';
							if( $adm == 1 ) $oc = 'onclick="markDay(\'' . $appID . '\')"'; else $oc = '';
						}
					}
					
					$row[$dn] = '<div id="Day_' . $appID . '" class="' . $ex . $st . $se . '" title="' . $ti . '" ' . $oc . '>' . $row[$dn] . '</div>';							
				}
				$str .= '<td class="ch' . ( $ch + 1 ) . '">' . $row[$dn] . '</td>';
				$ii++;
			}
			$str .= '</tr>';
			$start = ( $start + 1 ) % 2;
		}
	
		$nav = '<div class="Calendar">' .
			   '<table>' .
			   '<tr class="nv">' .
			   '<th colspan="8">' .
			   '<div class="nav">' .
			   '<span class="Left"> <a href="javascript:void(0)" class="prevYear" title="' . i18n ( 'Previous_Year' ) . '" onclick="changeDate( '."'". floor($month) ."'".', '."'". floor($year-1) ."'".' )"><<</a> <a href="javascript:void(0)" class="prevMonth" title="' . i18n ( 'Previous_Month' ) . '" onclick="changeDate( '."'". floor($month-1) ."'".', '."'". $year ."'".' )"><</a> </span>' .
			   '<span class="Center"> <a href="javascript:void(0)" title="' . i18n ( 'Todays_Date' ) . '" onclick="changeDate( '."'". date( 'n' ) ."'".', '."'". date( 'Y' ) ."'".' )">' . i18n ( date ( 'F', strtotime ( $year . '-' . $month ) ) ) . ', ' . $year . '</a> </span>' .
			   '<span class="Right"> <a href="javascript:void(0)" class="nextMonth" title="' . i18n ( 'Next_Month' ) . '" onclick="changeDate( '."'". floor($month+1) ."'".', '."'". $year ."'".' )"> ></a> <a href="javascript:void(0)" class="nextYear" title="' . i18n ( 'Next_Year' ) . '" onclick="changeDate( '."'". floor($month) ."'".', '."'". floor($year+1) ."'".' )">>></a> </span>' .
			   '</div>' .
			   '</th>' .
			   '</tr>';
	
		return $nav . 
			   '<tr class="hd">' .
			   '<th><div class="Week">' . i18n ( 'W' ) . '</div></th>' .
			   '<th><div class="Mon">' . i18n ( 'M' ) . '</div></th>' .
			   '<th><div class="Tue">' . i18n ( 'T' ) . '</div></th>' . 
			   '<th><div class="Wed">' . i18n ( 'W' ) . '</div></th>' . 
			   '<th><div class="Thu">' . i18n ( 'T' ) . '</div></th>' . 
			   '<th><div class="Fri">' . i18n ( 'F' ) . '</div></th>' . 
			   '<th><div class="Sat">' . i18n ( 'S' ) . '</div></th>' . 
			   '<th><div class="Sun">' . i18n ( 'S' ) . '</div></th>' .
			   '</tr>' . 
			   $str . 
			   '</table>' .
			   '</div>';
	}
}

// Run calendar ----------------------------------------------------------------------------

//$acal = appCalendar( $year, $month, $cid, $adm );

if ( !function_exists ( 'SbookCalendar' ) )
{
	function SbookCalendar( $date )
	{
		$year = date( 'Y', strtotime( $date ) );
		$month = date( 'm', strtotime( $date ) );
		$day = date( 'd', strtotime( $date ) );
		
		$nstr = '<tr class="nv"><th colspan="8">';
		$nstr .= '<div class="nav">';
		$nstr .= '<span class="Left"><a href="javascript:void(0)" class="prevYear" title="' . i18n ( 'i18n_Previous_Year' ) . '" onclick="changeDate( \'' . floor( $month ) . '\', \'' . floor( $year-1 ) . '\' )"> << </a>';
		$nstr .= '<a href="javascript:void(0)" class="prevMonth" title="' . i18n ( 'i18n_Previous_Month' ) . '" onclick="changeDate( \'' . floor( $month-1 ) . '\', \'' . $year . '\' )"> < </a></span>';
		$nstr .= '<span class="Center"><a href="javascript:void(0)" title="' . i18n ( 'i18n_Todays_Date' ) . '" onclick="changeDate( \'' . date( 'n' ) . '\', \'' . date( 'Y' ) . '\' )">' . i18n ( 'i18n_' . date ( 'F', strtotime ( $year . '-' . $month ) ) ) . ', ' . $year . '</a></span>';
		$nstr .= '<span class="Right"><a href="javascript:void(0)" class="nextMonth" title="' . i18n ( 'i18n_Next_Month' ) . '" onclick="changeDate( \'' . floor( $month+1 ) . '\', \'' . $year . '\' )"> > </a>';
		$nstr .= '<a href="javascript:void(0)" class="nextYear" title="' . i18n ( 'i18n_Next_Year' ) . '" onclick="changeDate( \'' . floor( $month ) . '\', \'' . floor( $year+1 ) . '\' )"> >> </a> </span>';
		$nstr .= '</div>';
		$nstr .= '</th></tr>';
		
		$o = new CalendarMonth( $date = ( $year . '-' . $month . '-' . $day ) );
		
		$i = 0; $sw = 2; $s = 0;
		$str = '<table class="Calendar"><tbody>' . $nstr;
		foreach( $o->weeks as $key=>$w )
		{	
			if( $key == 0 && $w->days[6]->month != $o->month ) continue;
			else if ( $key == 5 && $w->days[0]->month != $o->month ) continue;
			
			$ch = 1 + $s;
			$sw = ( $sw == '1' ? '2' : '1' );
			$hstr = '<tr class="hd">';
			$dstr = '<tr class="sw' . $sw . '">';
			foreach( $w->days as $k=>$d )
			{
				$ch = ( $ch + 1 ) % 2;
				if( $k == 0 )
				{
					$hstr .= '<th><div class="Week">' . i18n ( 'i18n_Week' ) . '</div></th>';
					$dstr .= '<td><div class="Week">' . $d->week . '</div></td>';
				}
				if( $i == 0 )
				{
					$hstr .= '<th><div class="' . $d->name . '">' . i18n ( 'i18n_' . $d->name ) . '</div></th>';
				}
				if( date( 'Y-m-d', strtotime( $d->date ) ) == $date ) $sel = ' Selected'; else $sel = '';
				$dstr .= ( $d->month == $o->month ? '<td class="ch' . ( $ch + 1 ) . '"><div class="' . $d->name . $sel . '">' . $d->day . '</div></td>' : '<td class="ch' . ( $ch + 1 ) . '"><div class="Space">&nbsp;</div></td>' );
			}
			if( $i == 0 && $hstr ) $str .= $hstr . '</tr>';
			if( $dstr ) $str .= $dstr . '</tr>';
			$s = ( $s + 1 ) % 2;
			$i++;
		}
		return $str .= '</tbody></table>';
	}
}

$acal = SbookCalendar( date( 'Y-m-d' ) );

if ( isset( $_REQUEST[ 'fid' ] ) && isset( $_REQUEST[ 'bajaxrand' ] ) ) die ( 'ok<!--separate-->' . $acal );


?>






