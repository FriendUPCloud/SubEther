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

$dobj = new stdClass();
$dobj->placename = '';
$dobj->fromdate = mktime( 0, 0, 0, date('m'), date('d'), date('Y') );
$dobj->todate = mktime( 0, 0, 0, date('m'), date('d')+1, date('Y') );
$dobj->number = 1;
$dobj->adults = 1;
$dobj->children = 0;

$dstr = '';

$dstr .= '<div class="navigator">';

// --- Placename --------------------------------------------------------

$dstr .= '<div class="wrapper placename">';
$dstr .= '<div class="container placename">';
$dstr .= '<div class="heading placename">Place or Name</div>';
$dstr .= '<div class="input placename">';
$dstr .= '<div>';
$dstr .= '<input type="text" name="placename" value="' . $dobj->placename . '" onkeyup="if(event.keyCode==13){FilterBooking();}"/>';
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
$dstr .= '<button onclick="BookingCalendar(\'' . $id . '\',\'' . $inpid . '\',ge(\'InputFromDate0\').value)">Select</button>';
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
$dstr .= '<button onclick="BookingCalendar(\'' . $id . '\',\'' . $inpid . '\',ge(\'InputToDate0\').value)">Select</button>';
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '<div id="CalendarToDate" class="calendar todate">';
$dstr .= $cal;
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '</div>';

// --- Number -----------------------------------------------------------

$dstr .= '<div class="wrapper number">';
$dstr .= '<div class="container number">';
$dstr .= '<div class="heading number">Rooms</div>';
$dstr .= '<div class="input number">';
$dstr .= '<div>';
$dstr .= '<input type="text" name="number" value="' . $dobj->number . '" onkeyup="if(event.keyCode==13){FilterBooking();}"/>';
$dstr .= '<button>Select</button>';
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '</div>';

// --- Adults -----------------------------------------------------------

$dstr .= '<div class="wrapper adults">';
$dstr .= '<div class="container adults">';
$dstr .= '<div class="heading adults">Adults</div>';
$dstr .= '<div class="input adults">';
$dstr .= '<div>';
$dstr .= '<input type="text" name="adults" value="' . $dobj->adults . '" onkeyup="if(event.keyCode==13){FilterBooking();}"/>';
$dstr .= '<button>Select</button>';
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '</div>';

// --- Children ---------------------------------------------------------

$dstr .= '<div class="wrapper children">';
$dstr .= '<div class="container children">';
$dstr .= '<div class="heading children">Children</div>';
$dstr .= '<div class="input children">';
$dstr .= '<div>';

$dstr .= '<input type="text" name="children" value="' . $dobj->children . '" onkeyup="if(event.keyCode==13){FilterBooking();}"/>';
$dstr .= '<button>Select</button>';

/*$obj = new stdClass();
$obj->options = array( 1,2,3,4,5,6,7,8,9 );

$dstr .= renderCustomSelect( $obj, 'children' );*/

$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '</div>';
$dstr .= '</div>';

$dstr .= '<div class="clearboth"></div>';

$dstr .= '</div>';

?>
