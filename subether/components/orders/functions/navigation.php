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

$dates = new CalendarWeek( date( 'Y-m-d H:i:s' ) );

//die( print_r( $dates,1 ) . ' --' );

$dobj = new stdClass();
$dobj->placename = '';
$dobj->fromdate = strtotime( $dates->dates[0] );
$dobj->todate = strtotime( $dates->dates[6] );
//$dobj->fromdate = mktime( 0, 0, 0, date('m'), date('d'), date('Y') );
//$dobj->todate = mktime( 0, 0, 0, date('m'), date('d')+1, date('Y') );

$dstr = '';

$dstr .= '<div class="navigator">';

// --- Placename --------------------------------------------------------

$dstr .= '<div class="wrapper placename">';
$dstr .= '<div class="container placename">';
$dstr .= '<div class="heading placename">Name</div>';
$dstr .= '<div class="input placename">';
$dstr .= '<div>';
$dstr .= '<input type="text" name="search" value="' . $dobj->placename . '" onkeyup="if(event.keyCode==13){FilterBooking();}"/>';
$dstr .= '<button onclick="FilterBooking()">Search</button>';
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '</div>';

// --- Fromdate ---------------------------------------------------------

$id = 'CalendarFromDate';
$inpid = 'InputFromDate';
$date = $dobj->fromdate;

include ( $cbase . '/functions/calendar.php' );

$dstr .= '<div class="wrapper fromdate">';
$dstr .= '<div class="container fromdate">';
$dstr .= '<div class="heading fromdate">From date</div>';
$dstr .= '<div class="input fromdate">';
$dstr .= '<div>';
$dstr .= '<input id="InputFromDate1" type="text" value="' . date( 'D, d.m.Y', $dobj->fromdate ) . '" readonly/>';
$dstr .= '<input id="InputFromDate0" type="hidden" name="fromdate" value="' . $dobj->fromdate . '"/>';
$dstr .= '<button onclick="AdminCalendar(\'' . $id . '\',\'' . $inpid . '\',ge(\'InputFromDate0\').value)">Select</button>';
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '<div id="CalendarFromDate" class="calendar fromdate">';
$dstr .= $cal;
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '</div>';

// --- Todate -----------------------------------------------------------

$id = 'CalendarToDate';
$inpid = 'InputToDate';
$date = $dobj->todate;

include ( $cbase . '/functions/calendar.php' );

$dstr .= '<div class="wrapper todate">';
$dstr .= '<div class="container todate">';
$dstr .= '<div class="heading todate">To date</div>';
$dstr .= '<div class="input todate">';
$dstr .= '<div>';
$dstr .= '<input id="InputToDate1" type="text" value="' . date( 'D, d.m.Y', $dobj->todate ) . '" readonly/>';
$dstr .= '<input id="InputToDate0" type="hidden" name="todate" value="' . $dobj->todate . '"/>';
$dstr .= '<button onclick="AdminCalendar(\'' . $id . '\',\'' . $inpid . '\',ge(\'InputToDate0\').value)">Select</button>';
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '<div id="CalendarToDate" class="calendar todate">';
$dstr .= $cal;
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '</div>';

// --- Buttons -----------------------------------------------------------

$dstr .= '<div class="wrapper buttons">';
$dstr .= '<button onclick="ExportMemberHours(\'pdf\')"><img src="admin/gfx/icons/page_white_acrobat.png">PDF</button>';
$dstr .= '<button onclick="ExportMemberHours(\'xls\')"><img src="admin/gfx/icons/page_white_excel.png">XLS</button>';
$dstr .= '</div>';

$dstr .= '<div class="clearboth"></div>';

$dstr .= '</div>';

?>
