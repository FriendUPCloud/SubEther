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

global $webuser, $database;

include_once ( 'subether/classes/calendar.class.php' );
include_once ( 'subether/include/calendar.php' );
include_once ( 'subether/components/events/include/functions.php' );

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', 'subether/components/events/css/events.css' );
$document->addResource ( 'javascript', 'subether/components/events/javascript/events.js' );
$document->addResource ( 'javascript', 'subether/components/orders/javascript/calendar.js' );

//$basetime = ( $_SESSION['events_basetime'] ? $_SESSION['events_basetime'] : strtotime( date( 'Y-m-d H:i:s' ) ) );
$basetime = strtotime( date( 'Y-m-d H:i:s' ) );

$nextmonth = mktime( 0, 0, 0, date( 'm', $basetime )+1, date( 'd', $basetime ), date( 'Y', $basetime ) );

$nextweek = mktime( 0, 0, 0, date( 'm', $basetime ), date( 'd', $basetime )+7, date( 'Y', $basetime ) );

$endofmonth = strtotime( date( 'Y-m-31', $basetime ) );

//die( print_r( date( 'Y-m-d 23:59:59', $nextweek ),1 ) );

$pcContactID = $parent->cuser->ContactID;

$evtstr = '';

if ( $rows = $database->fetchObjectRows( $q = '
	SELECT
		"profile" AS `EventMode`,
		e.*,
		h.IsAccepted, 
		h.DateStart AS HourDateStart, 
		h.DateEnd AS HourDateEnd,
		u.Display,
		u.Firstname,
		u.Middlename,
		u.Lastname,
		u.Username,
		u.ImageID AS UserImage,
		( e.CategoryID > 0 ) AS `IsGroup` 
	FROM 
		SBookEvents e, 
		SBookContact u,
		SBookHours h LEFT JOIN SBookNotification r ON
			(
				    r.ReceiverID = \'' . $pcContactID . '\'
				AND r.Type = "events"
				AND h.ID = r.ObjectID
			)
	WHERE
			h.ProjectID = e.ID 
		AND h.IsDeleted = "0" 
		AND h.DateStart >= \'' . date( 'Y-m-01 00:00:00', $basetime ) . '\' 
		AND h.DateEnd <= \'' . date( 'Y-m-d 23:59:59', ( $nextweek >= $endofmonth ? $nextweek : $endofmonth ) ) . '\' 
		AND e.IsDeleted = "0" 
		AND u.ID = e.UserID 
		AND h.UserID = \'' . $pcContactID . '\' 
	ORDER BY 
		h.DateStart ASC 
', false, 'components/events/include/panel.php' ) )
{
	$ids = array(); $hours = array();
	
	foreach( $rows as $k=>$e )
	{
		if( $e->Component == 'booking' )
		{
			$ids[$e->ID] = $e->ID;
		}
		
		// Only show accepted events
		if( $e->Component == 'events' && $e->IsAccepted <= 0 )
		{
			unset( $rows[$k] );
		}
	}
	
	//die( print_r( $rows,1 ) . ' --' );
	
	if( $ids && ( $hr = $database->fetchObjectRows( '
		SELECT 
			* 
		FROM 
			SBookHours 
		WHERE 
			ProjectID IN (' . implode( ',', $ids ) . ') 
		ORDER BY 
			ID ASC 
	', false, 'components/events/include/panel.php' ) ) )
	{
		foreach( $hr as $h )
		{
			if( $dates = DateSpan( date( 'Y-m-d', strtotime( $h->DateStart ) ), date( 'Y-m-d', strtotime( $h->DateEnd ) ) ) )
			{
				if( !isset( $hours[$h->ProjectID] ) )
				{
					$hours[$h->ProjectID] = array();
				}
				
				foreach( $dates as $d )
				{
					$hours[$h->ProjectID][] = $d;
				}
			}
		}
	}
	
	if( $rows )
	{
		// Loop through all events (with additional info through joins)
		foreach( $rows as $e )
		{
			// If we have a booking on this event
			if( isset( $hours[$e->ID] ) && is_array( $hours[$e->ID] ) )
			{
				// Make a clone with the values of the event
				$o = new stdClass();
				foreach( $e as $k=>$v ) $o->$k = $v;
				$o->DateStart = $hours[$e->ID][0];
				$o->DateEnd = end( $hours[$e->ID] );
				
				// Add to event list
				$events[] = $o;
			}
			else
			{
				$events[] = $e;
			}
		}
		
		$month = false;
		
		foreach( $events as $evt )
		{
			if ( $evt->HourDateStart >= date( 'Y-m-d 00:00:00', $basetime ) )
			{
				if ( $month != date( 'F', strtotime( $evt->HourDateStart ) ) && date( 'F', strtotime( $evt->HourDateStart ) ) != date( 'F', $basetime ) )
				{
					$evtstr .= '<strong>' . i18n ( 'i18n_' . date( 'F', strtotime( $evt->HourDateStart ) ) ) . '</strong>';
				}
				
				//$evt->Href = ( $evt->IsGroup ? ( $parent->path . 'groups/' . ( $evt->SBC_ID ? $evt->SBC_ID : $evt->CategoryID ) . '/' ) : ( $parent->path . ( $evt->User_Name ? $evt->User_Name : $evt->Username ) . '/' ) ) . 'events/?r=day&basetime=' . strtotime( $evt->DateStart );
				//$evt->Href = ( $evt->IsGroup ? ( $parent->path . 'groups/' . $evt->CategoryID . '/' ) : ( $parent->path . $evt->Username . '/' ) ) . 'events/?r=day&categoryid=' . $evt->CategoryID . '&event=' . $evt->ID . '&basetime=' . strtotime( $evt->HourDateStart );
				$evt->Href = ( $evt->IsGroup ? ( 'groups/' . $evt->CategoryID . '/' ) : ( $evt->Username . '/' ) ) . 'events/?r=day&categoryid=' . $evt->CategoryID . '&event=' . $evt->ID . '&basetime=' . strtotime( $evt->HourDateStart );
				
				$evtstr .= '<li class="' . urlStr( $evt->Component ) . ' ' . i18n ( 'i18n_day_' . date( 'd', strtotime( $evt->HourDateStart ) ) ) . '">';
				$evtstr .= '<div>';
				$evtstr .= '<a href="' . $evt->Href . '">';
				$evtstr .= '<span class="icon"></span>';
				$evtstr .= '<span class="name">' . $evt->Name . '</span>';
				$evtstr .= '<span class="noti"></span>';
				$evtstr .= '</a>';
				$evtstr .= '</div>';
				$evtstr .= '</li>';
				
				$month = date( 'F', strtotime( $evt->HourDateStart ) );
			}
		}
	}
}

if( !isset( $_REQUEST['bajaxrand'] ) )
{
	$str .= '<div id="EventPanel">';
}

$str .= '<h4 class="heading calendar mini"><span>' . i18n( 'i18n_Calendar' ) . '</span></h4>';

$str .= '<div id="CalendarMini" class="calendar">';
$str .= renderCalendarMonth( date( 'Y-m-d H:i:s' ), true, $events );
$str .= '</div>';


$str .= '<h4 class="heading upcoming events"><span>' . i18n( 'i18n_Events' ) . '</span></h4>';

$str .= '<ul class="list upcoming events">' . $evtstr;

//if( isset( $_REQUEST['dbugdettan'] ) ) die( print_r( $events,1 ) . ' -- ' . $q . ' ' . mysql_error() . '..' );

$str .= '<li class="create">';
$str .= '<a onclick="openWindow( \'Events\', false, \'create\' )" href="javascript:void(0)">';
//$str .= '<a href="javascript:void(0)">';
$str .= '<span class="icon"></span>';
$str .= '<span class="create">' . i18n( 'i18n_Create Event' ) . '...</span>';
$str .= '<span></span>';
$str .= '</a>';
$str .= '</li>';
$str .= '</ul>';

if( !isset( $_REQUEST['bajaxrand'] ) )
{
	$str .= '</div>';
}

if( isset( $_REQUEST['bajaxrand'] ) )
{
	die( 'ok<!--separate-->' . $str );
}

?>
