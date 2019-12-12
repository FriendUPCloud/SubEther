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

// -----------------------------------------------------------------------------
//
// Class to represent one calendar day and all the calendar information 
// associated with it.
//

class CalendarDay
{
	var $date;  // Date of this day
	var $week;  // Week of this day
	var $year;  // Year of this day
	var $month; // Month of this day
	var $day;   // Day number of this day
	var $name;  // Full name of this day

	public function __construct( $date, $week = false )
	{
		$datetime = strtotime( $date );
		$this->week = ( $week ? $week : date ( 'W', $datetime ) );
		$this->date = date ( 'Y-m-d', $datetime );
		
		list ( $year, $month, $day ) = explode ( '-', $this->date );
		
		$this->name = date( 'l', $datetime );
		
		// Assign the rest
		$this->year = (int)$year;
		$this->month = (int)$month;
		$this->day = (int)$day;
	}
	
	// Import events
	public function ImportEvents ( $obj, $fromkey = false, $tokey = false )
	{
		if ( !is_array ( $obj ) ) return false;
		
		foreach( $obj as $event )
		{
			$fromdate = strtotime ( ( $fromkey ? $event->$fromkey : $event->DateFrom ) );
			$todate = strtotime ( ( $tokey ? $event->$tokey : $event->DateTo ) );
			$this->ImportData ( $event, $fromdate, $todate );
		}
		return true;
	}
	
	// Import data on from time and to time (unix time)
	private function ImportData ( $data, $fromdate, $todate )
	{
		if( !$data || !$fromdate ) return false;
		
		if( !$todate || $todate < 0 )
		{
			$todate = $fromdate;
		}
		
		$fromdate = strtotime( date( 'Y-m-d', $fromdate ) );
		$todate = strtotime( date( 'Y-m-d', $todate ) );
		
		for ( $c = $fromdate; $c <= $todate; $c += 86400 )
		{
			$date = date ( 'Y-m-d', $c );
			if( $this->date == $date )
			{
				$this->events[] = $data;
			}
		}
	}
}

// -----------------------------------------------------------------------------
//
// Class to represent a week of a month.
// This class also contains an array of the days of this week
//
class CalendarWeek
{
	var $week;    // Week number of the month
	var $date;    // First day in the week
	var $year;    // Year of the week
	var $month;   // Month of the week
	var $name;    // Name of the first day of the week
	var $day;     // Day of the month this week starts at
	var $current; // Current date of month from date;
	var $dates;   // Array of dates in a week
	var $days;    // Array of 7 CalendarDays
	
	public function __construct( $date, $week = false )
	{
		$this->current = date( 'Y-m-d', strtotime( $date ) );
		
		// Find the date to start at
		$year = intval( date( 'Y', strtotime( $date ) ), 10 );
		$monh = intval( date( 'm', strtotime( $date ) ), 10 );
		$week = ( $week ? $week : intval( date ( 'W', strtotime( $date ) ), 10 ) );
		$year = ( $week == 1 && $monh > 1 ? ( $year+1 ) : $year );
		$year = ( $week > 50 && $monh == 1 ? ( $year-1 ) : $year );
		
		$date = new DateTime();
		$date->setISODate( $year, $week, 1 );
		$date = $date->format('Y-m-d');
		
		$this->week = intval( date ( 'W', strtotime( $date ) ), 10 );
		$this->date = date( 'Y-m-d', strtotime( $date ) );
		$this->name = date( 'l', strtotime( $date ) );
		$this->year = intval( date( 'Y', strtotime( $date ) ), 10 );
		$this->month = intval( date( 'm', strtotime( $date ) ), 10 );
		$this->day = intval( date( 'd', strtotime( $date ) ), 10 );
		
		// Fill this week with days
		$this->days = array();
		for( $d = 0; $d < 7; $d++ )
		{
			$this->dates[$d] = date( 'Y-m-d', strtotime( $date." +$d days" ) );
			$this->days[$this->dates[$d]] = new CalendarDay( $this->dates[$d], $this->week );
		}
	}
	
	// Get day by number in week
	public function GetDayByNumber( $number )
	{
		if( !$number ) return false;
		$daydate = date( 'Y-m-d', strtotime( $this->date." +$number days" ) );
		return $this->days[$daydate] = new CalendarDay( $daydate, $this->week );
	}
	
	// Returns dates in an array in between a datespan
	public function DateSpan( $fromdate, $todate )
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
	
	// Import events
	public function ImportEvents ( $obj, $fromkey = false, $tokey = false )
	{
		if ( !is_array ( $obj ) ) return false;
		
		foreach( $obj as $event )
		{
			$fromdate = strtotime ( ( $fromkey ? $event->$fromkey : $event->DateFrom ) );
			$todate = strtotime ( ( $tokey ? $event->$tokey : $event->DateTo ) );
			$this->ImportData ( $event, $fromdate, $todate );
		}
		return true;
	}
	
	// Import data on from time and to time (unix time)
	private function ImportData ( $data, $fromdate, $todate )
	{
		if( !$data || !$fromdate ) return false;
		
		if( !$todate || $todate < 0 )
		{
			$todate = $fromdate;
		}
		
		$fromdate = strtotime( date( 'Y-m-d', $fromdate ) );
		$todate = strtotime( date( 'Y-m-d', $todate ) );
		
		for ( $c = $fromdate; $c <= $todate; $c += 86400 )
		{
			$date = date ( 'Y-m-d', $c );
			if( $this->days[$date] )
			{
				$this->days[$date]->events[] = $data;
			}
		}
	}
}

// -----------------------------------------------------------------------------
//
// Class to represent a calendarCalendarMonth month + a few days before and after.
// The class holds 6 weeks in total.
//
class CalendarMonth
{
	var $date;    // First day of the month
	var $year;    // Year of the month
	var $month;   // Number of the month
	var $day;     // Day of the month
	var $days;    // Number of days in the month
	var $week;    // Week number the month starts at
	var $current; // Current week of month from date;
	var $numbers; // Array of weeks in a month
	var $weeks;   // Array of 6 CalendarWeeks
	
	public function __construct( $date )
	{
		$this->current = date( 'Y-m-d', strtotime( $date ) );
		
		// First day of month
		$date = date( 'Y-m-01', strtotime( $date ) );
		
		// Find the date to start at
		$year = intval( date( 'Y', strtotime( $date ) ), 10 );
		$monh = intval( date( 'm', strtotime( $date ) ), 10 );
		$week = intval( date ( 'W', strtotime( $date ) ), 10 );
		$year = ( $week == 1 && $monh > 1 ? ( $year+1 ) : $year );
		$year = ( $week > 50 && $monh == 1 ? ( $year-1 ) : $year );
		
		$this->year = intval( date( 'Y', strtotime( $date ) ), 10 );
		$this->month = intval( date( 'm', strtotime( $date ) ), 10 );
		$this->days = intval( date( 't', strtotime( $date ) ), 10 );
		
		$date = new DateTime();
		
		$date->setISODate( $year, $week, 1 );
		$date = $date->format('Y-m-d');
		
		$this->date = date( 'Y-m-d', strtotime( $date ) );
		$this->day = intval( date( 'd', strtotime( $date ) ), 10 );
		$this->week = intval( date ( 'W', strtotime( $date ) ), 10 );
		
		// Generate the calendar
		$this->weeks = array();
		for( $w = 0; $w < 6; $w++ )
		{
			$week = new DateTime();
			$week->setISODate( $year, $this->week+$w, 1 );
			$this->numbers[$w] = intval( date ( 'W', strtotime( $week->format( 'Y-m-d' ) ) ), 10 );
			$this->weeks[$this->numbers[$w]] = new CalendarWeek( $this->current, $this->week+$w );
		}
	}
	
	// Get day by number in week
	public function GetDayByNumber( $number )
	{
		if( !$number ) return false;
		$daydate = date( 'Y-m-d', strtotime( $this->date." +$number days" ) );
		return $this->days[$daydate] = new CalendarDay( $daydate, $this->week );
	}
	
	// Get week by number in month
	public function GetWeekByNumber( $number )
	{
		if( !$number ) return false;
		$week = intval( date( 'W', strtotime( $this->date." +$number weeks" ) ), 10 );
		return $this->weeks[$week] = new CalendarWeek( $this->date, ( $this->week+$number ) );
	}
	
	// Returns dates in an array in between a datespan
	public function DateSpan( $fromdate, $todate )
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
	
	// Import events
	public function ImportEvents ( $obj, $fromkey = false, $tokey = false )
	{
		if ( !is_array ( $obj ) ) return false;
		
		foreach( $obj as $evt )
		{
			// Multiple events
			if ( is_array ( $evt ) )
			{
				foreach ( $evt as $event )
				{
					$fromdate = strtotime ( ( $fromkey ? $event->$fromkey : $event->DateFrom ) );
					$todate = strtotime ( ( $tokey ? $event->$tokey : $event->DateTo ) );
					$this->ImportData ( $event, $fromdate, $todate );
				}
			}
			// Single event
			else if ( is_object ( $evt ) )
			{
				$event = $evt;
				$fromdate = strtotime ( ( $fromkey ? $event->$fromkey : $event->DateFrom ) );
				$todate = strtotime ( ( $tokey ? $event->$tokey : $event->DateTo ) );
				$this->ImportData ( $event, $fromdate, $todate );
			}
		}
		return true;
	}
	
	// Import data on from time and to time (unix time)
	private function ImportData ( $data, $fromdate, $todate )
	{		
		if( !$data || !$fromdate ) return false;
		
		if( !$todate || $todate < 0 )
		{
			$todate = $fromdate;
		}
		
		$fromdate = strtotime( date( 'Y-m-d', $fromdate ) );
		$todate = strtotime( date( 'Y-m-d', $todate ) );
		
		for ( $c = $fromdate; $c <= $todate; $c += 86400 )
		{
			$date = date ( 'Y-m-d', $c );
			$week = intval( date ( 'W', strtotime( $date ) ), 10 );
			
			if( $this->weeks[$week]->days[$date] )
			{
				$this->weeks[$week]->days[$date]->events[] = $data;
			}
		}
	}
}

// -----------------------------------------------------------------------------
//
// Class to represent a calendar year
// The class holds 12 months in total.
// Notice: Cannot be "skewed": Always starts at January and ends at December
//
class CalendarYear
{
	var $date;   // First day of the year
	var $year;   // Year this calendar represents
	var $months; // Array of 6 CalendarWeeks
	
	public function __construct( $date )
	{
		// Find some useful date-related information
		$this->date = date( 'Y-01-01', strtotime( $date ) );          // First day of the year
		$this->year = intval( date ( 'Y', strtotime( $date ) ), 10 ); // The year
	
		// Generate the calendar
		$this->months = array();
		for( $m = 0; $m < 12; $m++ )
		{
			$this->months[$m+1] = new CalendarMonth( date( 'Y-m-d', strtotime( $this->date." +$m months" ) ) );
		}
	}
	
	// Returns dates in an array in between a datespan
	public function DateSpan( $fromdate, $todate )
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
	
	// Import events
	public function ImportEvents ( $obj, $fromkey = false, $tokey = false )
	{
		if ( !is_array ( $obj ) ) return false;
		foreach( $obj as $event )
		{
			$fromdate = strtotime ( ( $fromkey ? $event->$fromkey : $event->DateFrom ) );
			$todate = strtotime ( ( $tokey ? $event->$tokey : $event->DateTo ) );
			$this->ImportData ( $event, $fromdate, $todate );
		}
		return true;
	}
	
	// Import data on from time and to time (unix time)
	private function ImportData ( $data, $fromdate, $todate )
	{
		if( !$data || !$fromdate ) return false;
		
		if( !$todate || $todate < 0 )
		{
			$todate = $fromdate;
		}
		
		$fromdate = strtotime( date( 'Y-m-d', $fromdate ) );
		$todate = strtotime( date( 'Y-m-d', $todate ) );
		
		for ( $c = $fromdate; $c <= $todate; $c += 86400 )
		{
			$date = date ( 'Y-m-d', $c );
			$month = intval( date ( 'm', strtotime( $date ) ), 10 );
			$week = intval( date ( 'W', strtotime( $date ) ), 10 );
			if( $this->months[$month]->weeks[$week]->days[$date] )
			{
				$this->months[$month]->weeks[$week]->days[$date]->events[] = $data;
			}
		}
	}
}

// -----------------------------------------------------------------------------
//
// Class to represent a calendar invite for ics, ical and other external formats.
//
class CalendarInvite
{
	var $mode;   // Mode to send invite as
	var $uid;
	var $prodid;
	var $created;
	var $lastModified;
	var $summary;
	var $description;
	var $start;
	var $end;
	var $allDay;
	var $location;
	var $organizerEmail;
	var $organizerName;
	var $attendees;
	var $filename;
	var $generated;
	
	public function __construct( $uid = false, $mode = 'ics' )
	{
		$this->mode = $mode;
		$this->prodid = 'Treeroot';
		$this->allDay = false;
		$this->attendees = array();
		$this->filename = 'invite.ics';
		
		if ( !$uid )
		{
			$this->uid = uniqid( rand( 0, getmypid() ) ) . "@treeroot.org";
        }
		else
		{
			$this->uid = $uid . "@treeroot.org";
		}
	}
	
	protected static function IcsDate ( $dateTime, $format = 'Ymd\THis' )
	{
        $return = false;
		
        if ( get_class( $dateTime ) == 'DateTime' )
		{
            $return = $dateTime->format ( $format );
        }
		
        return $return;
    }
	
	protected function GetTimestamp (  )
	{
        $date = new \DateTime();
        return self::IcsDate( $date );
    }
	
	public function GetCreated ( $icsFormat = false )
	{
        if ( $icsFormat )
		{
			if ( !$this->created )
			{
				return $this->GetTimestamp();
			}
            // return $this->_created->format("Ymd\THis\Z");
            return self::IcsDate( $this->created );
        }
		
        return $this->created;
    }
	
	public function GetLastModified ( $icsFormat = false )
	{
        if ( $icsFormat )
		{
			if ( !$this->lastModified )
			{
				return $this->GetTimestamp();
			}
            // return $this->_created->format("Ymd\THis\Z");
            return self::IcsDate( $this->lastModified );
        }
		
        return $this->lastModified;
    }
	
	protected function IcsAllDayDate ( $dateTime )
	{
        $format = 'Ymd\THis';
		
        if ( $this->allDay )
		{
            // only add date (without time) if allday event
            $format = 'Ymd';
        }
		
        return self::IcsDate ( $dateTime, $format );
    }
	
	public function SetSummary ( $str )
	{
		$this->summary = $str;
	}
	
	public function SetDescription ( $str )
	{
		$this->description = $str;
	}
	
	public function SetStart ( $datetime )
	{
		if ( is_string( $datetime ) )
		{
			$datetime = new DateTime( $datetime );
		}
		
		$this->start = $datetime;
	}
	
	public function SetEnd ( $datetime )
	{
		if ( is_string( $datetime ) )
		{
			$datetime = new DateTime( $datetime );
		}
		
		$this->end = $datetime;
	}
	
	public function GetStart ( $icsFormat = false )
	{
        if ( $icsFormat )
		{
            return $this->IcsAllDayDate ( $this->start );
        }
		
        return $this->start;
    }
	
	public function GetEnd ( $icsFormat = false )
	{
        if ( $icsFormat )
		{
            if ( $this->allDay )
			{
                // add one day for allday events
                $this->end->add( new \DateInterval('P1D') );
            }
			
            return $this->IcsAllDayDate ( $this->end );
        }
		
        return $this->end;
    }
	
	public function SetAllDay( $allDay )
	{
        $this->allDay = $allDay;
    }
	
	public function SetLocation ( $str )
	{
		$this->location = $str;
	}
	
	public function SetOrganizer ( $email, $name = false )
	{
		if ( !$name )
		{
			$name = $email;
		}
		
		$this->organizerEmail = $email;
		$this->organizerName = $name;
	}
	
	public function AddAttendee ( $email, $name = false )
	{
		if ( !$name )
		{
			$name = $email;
		}
		
		if ( !isset( $this->attendees[$email] ) )
		{
            $this->attendees[$email] = $name;
        }
	}
	
	public function isValid()
	{
        if ( $this->created || $this->start || $this->end || $this->summary || $this->organizerEmail || $this->organizerName || is_array( $this->attendees ) )
		{
            return true;
        }
		
        return false;
    }
	
	public function Generate (  )
	{
		if ( $this->isValid() )
		{
			// TODO: Check on various system if this works. and Update it to support all variabled of calendar first creation and later updates.
			
            $content  = "BEGIN:VCALENDAR\r\n";
			$content .= "PRODID:" . $this->prodid . "\r\n";
            $content .= "VERSION:2.0\r\n";
            $content .= "CALSCALE:GREGORIAN\r\n";
            //$content .= "METHOD:PUBLISH\r\n"; // will ask in which calendar (at least on apple calendar)
			$content .= "METHOD:REQUEST\r\n";
			
            $timezoneIdentifier = '';
            /*if ( !$this->allDay )
			{
                // define timezone static -> will break in outlook if allday and timezone is set
                $timezoneIdentifier = ';TZID=Europe/Zurich';
                $content .= "BEGIN:VTIMEZONE
TZID:Europe/Zurich
X-LIC-LOCATION:Europe/Zurich
BEGIN:DAYLIGHT
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:CEST
DTSTART:19700329T020000
RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=3
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:CET
DTSTART:19701025T030000
RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10
END:STANDARD
END:VTIMEZONE\r\n";
            }*/
			
            $content .= "BEGIN:VEVENT\r\n";
            $content .= "DTSTART".$timezoneIdentifier.":{$this->GetStart(true)}\r\n";
            $content .= "DTEND".$timezoneIdentifier.":{$this->GetEnd(true)}\r\n";
            $content .= "DTSTAMP".$timezoneIdentifier.":{$this->GetTimestamp()}\r\n";
            $content .= "ORGANIZER;CN=3D{$this->organizerName}:mailto:{$this->organizerEmail}\r\n";
			$content .= "UID:{$this->uid}\r\n";
			
            foreach ( $this->attendees as $email => $name )
			{
                $content .= "ATTENDEE;PARTSTAT=3DNEEDS-ACTION;RSVP=3DTRUE;CN=3D{$name};X-NUM-GUESTS=3D0:mailto:{$email}\r\n";
				//ATTENDEE;CUTYPE=3DINDIVIDUAL;ROLE=3DREQ-PARTICIPANT;PARTSTAT=3DNEEDS-ACTION=;RSVP=3D TRUE;CN=3DThomas Wollburg;X-NUM-GUESTS=3D0:mailto:tw@friendup.cloud
            }
			
            $content .= "CREATED".$timezoneIdentifier.":{$this->GetCreated(true)}\r\n";
            $content .= "DESCRIPTION:{$this->description}\r\n";
            $content .= "LAST-MODIFIED".$timezoneIdentifier.":{$this->GetLastModified(true)}\r\n";
            $content .= "LOCATION:{$this->location}\r\n";
			$content .= "SEQUENCE:0\r\n";
			//$content .= "STATUS:NEEDS-ACTION\r\n";
			//$content .= "CONFIRMED\r\n";
            $content .= "SUMMARY:{$this->summary}\r\n";
            $content .= "TRANSP:OPAQUE\r\n";
            $content .= "END:VEVENT\r\n";
            $content .= "END:VCALENDAR";
			
			//BEGIN:VCALENDAR
			//PRODID:Treeroot
			//VERSION:2.0
			//CALSCALE:GREGORIAN
			//METHOD:REQUEST
			//BEGIN:VEVENT
			//DTSTART:20161005T100000Z
			//DTEND:20161005T110000Z
			//DTSTAMP:20161005T090859Z
			//ORGANIZER;CN=3DChris Andrè Strømland:acezerox@hotmail.com
			//UID:1605257fce8b0e3b3d@treeroot.org
			//ATTENDEE;CUTYPE=3DINDIVIDUAL;ROLE=3DREQ-PARTICIPANT;PARTSTAT=3DACCEPTED;RSV=P=3DTRUE ;CN=3DChris Andrè Strømland;X-NUM-GUESTS=3D0:mailto:chris@ideverket.no
			//ATTENDEE;CUTYPE=3DINDIVIDUAL;ROLE=3DREQ-PARTICIPANT;PARTSTAT=3DNEEDS-ACTION=;RSVP=3D TRUE;CN=3DThomas Wollburg;X-NUM-GUESTS=3D0:mailto:tw@friendup.cloud
			//ATTENDEE;CUTYPE=3DINDIVIDUAL;ROLE=3DREQ-PARTICIPANT;PARTSTAT=3DNEEDS-ACTION=;RSVP=3D TRUE;CN=3DChris Andrè Strømland;X-NUM-GUESTS=3D0:mailto:cas@friendup.cloud
			//CREATED:20161005T090736Z
			//DESCRIPTION:fthdftdtdy dfgdfgsgf
			//LAST-MODIFIED:20161005T090859Z
			//LOCATION:hftrdfgr
			//SEQUENCE:0
			//STATUS:CONFIRMED
			//SUMMARY:TestEvent
			//TRANSP:OPAQUE
			//END:VEVENT
			//END:VCALENDAR
			
            $this->generated = $content;
			
            return $this->generated;
        }
		
        return false;
	}
	
	public function Download (  )
	{
        $generate = $this->Generate();
		
        header( "Pragma: public" );
        header( "Expires: 0" );
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header( "Cache-Control: public" );
        header( "Content-Description: File Transfer" );
        header( "Content-type: application/octet-stream" );
        header( "Content-Disposition: attachment; filename=\"" . $this->filename . "\"" );
        header( "Content-Transfer-Encoding: binary" );
        header( "Content-Length: " . strlen( $generate ) );
		
        print $generate;
    }
	
}

?>
