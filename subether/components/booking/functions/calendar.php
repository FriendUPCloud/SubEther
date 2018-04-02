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

$events = array(); $slots = array();

$id = ( $_POST['id'] ? $_POST['id'] : $id );
$inpid = ( $_POST['inpid'] ? $_POST['inpid'] : $inpid );
$date = ( $_POST['date'] ? date( 'Y-m-d', $_POST['date'] ) : date( 'Y-m-d', $date ) );

if( $rows = $database->fetchObjectRows( $q = '
	SELECT 
		e.* 
	FROM 
		SBookEvents e 
	WHERE 
			e.Component = "booking" 
		AND e.CategoryID = \'' . $parent->folder->CategoryID . '\' 
		AND e.IsDeleted = "0" 
	ORDER BY 
		e.ID DESC 
' ) )
{
	$ids = array(); $hours = array();
	
	foreach( $rows as $e )
	{
		if( $e->Component == 'booking' )
		{
			$ids[$e->ID] = $e->ID;
		}
	}
	
	if( $ids && ( $hr = $database->fetchObjectRows( '
		SELECT 
			h.* 
		FROM 
			SBookHours h 
		WHERE 
				h.ProjectID IN (' . implode( ',', $ids ) . ')
			AND h.DateStart >= \'' . date( 'Y-m-01 00:00:00.000000', strtotime( $date ) ) . '\' 
			AND h.DateEnd <= \'' . date( 'Y-m-31 23:59:59.000000', strtotime( $date ) ) . '\'
			AND h.IsDeleted = "0" 
		ORDER BY 
			h.ID ASC 
	' ) ) )
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
					if( !isset( $slots[strtotime($d)] ) )
					{
						$slots[strtotime($d)] = array();
						$slots[strtotime($d)]['reserved'] = true;
					}
					
					if( !isset( $slots[strtotime($d)][$h->ProjectID] ) )
					{
						$slots[strtotime($d)][$h->ProjectID] = array();
					}
					
					$slots[strtotime($d)][$h->ProjectID]['limit'] = $h->BookingSlots;
					$slots[strtotime($d)][$h->ProjectID]['slots'] = ( $slots[strtotime($d)][$h->ProjectID]['slots'] ? ( $slots[strtotime($d)][$h->ProjectID]['slots'] + 1 ) : 1 );
					
					if( $slots[strtotime($d)][$h->ProjectID]['slots'] < $slots[strtotime($d)][$h->ProjectID]['limit'] )
					{
						$slots[strtotime($d)]['reserved'] = false;
					}
					
					
					
					if( !isset( $hours[$h->ProjectID][date('Ymd',strtotime($d))] ) )
					{
						$hours[$h->ProjectID][date('Ymd',strtotime($d))] = $d;
					}
				}
			}
			
			if( isset( $ids[$h->ProjectID] ) )
			{
				unset( $ids[$h->ProjectID] );
			}
		}
	}
	
	// Loop through all events (with additional info through joins)
	foreach( $rows as $e )
	{
		// If we have a booking on this event
		if( isset( $hours[$e->ID] ) && is_array( $hours[$e->ID] ) )
		{
			// Find the 
			foreach( $hours[$e->ID] as $d )
			{
				// Make a clone with the values of the event
				$o = new stdClass();
				foreach( $e as $k=>$v ) $o->$k = $v;
				$o->DateStart = $d;
				$o->DateEnd = $d;
				
				if( !$ids && isset( $slots[strtotime(date('Y-m-d',strtotime($o->DateStart)))]['reserved'] ) )
				{
					$o->Reserved = true;
				}
				
				// Add to event list
				$events[] = $o;
			}
		}
		else
		{
			$events[] = $e;
		}
	}
}

$cal = renderBookingCalendar( $date, $id, $inpid, $parent, $events );

if( $_POST && $_REQUEST['function'] == 'calendar' )
{
	die( 'ok<!--separate-->' . $cal );
}

?>
