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

include_once ( 'subether/functions/globalfuncs.php' );
include_once ( 'subether/functions/userfuncs.php' );
include_once ( 'subether/classes/calendar.class.php' );

$required = array(
	'SessionID' 
);

$options = array(
	'Mode', 'Type', 'Date', 'CategoryID', 'Encoding' 
);

// Temporary to view data i browser for development
if( !$_POST && $_REQUEST )
{
	$_POST = $_REQUEST;
	unset( $_POST['route'] );
}
else
{
	unset( $_POST['route'] );
}


if ( isset( $_POST ) )
{
	foreach( $_POST as $k=>$p )
	{
		if( !in_array( $k, $required ) && !in_array( $k, $options ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	foreach( $required as $r )
	{
		if( !isset( $_POST[$r] ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	
	// Get User data from sessionid
	$sess = new dbObject ( 'UserLogin' );
	$sess->Token = $_POST[ 'SessionID' ];
	if ( $sess->Load () )
	{
		$u = new dbObject ( 'SBookContact' );
		$u->UserID = $sess->UserID;
		if( !$u->Load () )
		{
			throwXmlError ( AUTHENTICATION_ERROR );
		}
	}
	else
	{
		throwXmlError ( SESSION_MISSING );
	}
	
	$basetime = ( $_POST['Date'] ? strtotime( $_POST['Date'] ) : strtotime( date( 'Y-m-d H:i:s' ) ) );
	
	$q = '
		SELECT 
			e.ID AS EventID, 
			e.UserID, 
			e.CategoryID, 
			e.Name AS EventName, 
			e.Type AS EventType,
			e.Place AS EventPlace, 
			e.Details AS EventDetails, 
			e.DateStart AS EventDateStart, 
			e.DateEnd AS EventDateEnd, 
			h.DateStart AS DateStart, 
			h.DateEnd AS DateEnd, 
			e.ImageID,
			e.Access, 
			e.IsFinished, 
			e.DateCreated, 
			e.DateModified, 
			( e.CategoryID > 0 ) AS `IsGroup` 
		FROM 
			SBookContact u, 
			SBookEvents e, 
			SBookHours h 
		WHERE 
				( h.UserID = !ContactID! OR e.UserID = !ContactID! ) 
			AND h.DateStart >= !DateStart! 
			AND h.DateEnd <= !DateEnd! 
			AND h.IsDeleted = "0" 
			AND h.ProjectID = e.ID 
			AND e.IsDeleted = "0" 
			AND u.ID = e.UserID
		GROUP BY
			e.ID 
		ORDER BY 
			h.DateStart ASC 
	';
	
	// TODO: Use this instead later on
	/*$q = '
		SELECT * FROM 
		(
			( 
				SELECT
					"profile" AS `EventMode`,
					e.ID, 
					e.Component,
					e.Type,
					e.Name,
					e.Place,
					e.Details,
					e.Price,
					e.UserID,
					e.ImageID,
					e.CategoryID,
					e.DateCreated,
					e.DateModified,
					e.IsFinished,
					e.Access,
					e.NodeID,
					e.NodeMainID,
					e.DateStart, 
					e.DateEnd, 
					u.Display, 
					u.Firstname, 
					u.Middlename, 
					u.Lastname, 
					u.Username, 
					u.ImageID AS UserImage, 
					( e.CategoryID > 0 ) AS `IsGroup` 
				FROM 
					SBookContact u, 
					SBookEvents e 
				WHERE 
						e.UserID = !ContactID!
					AND e.DateStart >= !DateStart! 
					AND e.DateEnd <= !DateEnd! 
					AND e.Component = "events" 
					AND e.IsDeleted = "0" 
					AND u.ID = e.UserID
			)
			UNION 
			( 
				SELECT
					"profile" AS `EventMode`,
					e.ID, 
					e.Component,
					e.Type,
					e.Name,
					e.Place,
					e.Details,
					e.Price,
					e.UserID,
					e.ImageID,
					e.CategoryID,
					e.DateCreated,
					e.DateModified,
					e.IsFinished,
					e.Access,
					e.NodeID,
					e.NodeMainID,
					e.DateStart, 
					e.DateEnd, 
					u.Display, 
					u.Firstname, 
					u.Middlename, 
					u.Lastname, 
					u.Username, 
					u.ImageID AS UserImage, 
					( e.CategoryID > 0 ) AS `IsGroup` 
				FROM 
					SBookContact u, 
					SBookEvents e, 
					SBookHours h 
				WHERE 
						h.UserID = !ContactID!
					AND h.IsDeleted = "0" 
					AND h.ProjectID = e.ID
					AND e.DateStart >= !DateStart! 
					AND e.DateEnd <= !DateEnd! 
					AND e.Component = "events" 
					AND e.IsDeleted = "0" 
					AND u.ID = e.UserID
			)
			UNION 
			( 
				SELECT
					"profile" AS `EventMode`,
					e.ID, 
					e.Component,
					e.Type,
					e.Name,
					e.Place,
					e.Details,
					e.Price,
					e.UserID,
					e.ImageID,
					e.CategoryID,
					e.DateCreated,
					e.DateModified,
					e.IsFinished,
					e.Access,
					e.NodeID,
					e.NodeMainID,
					h.DateStart, 
					h.DateEnd, 
					u.Display, 
					u.Firstname, 
					u.Middlename, 
					u.Lastname, 
					u.Username, 
					u.ImageID AS UserImage, 
					( e.CategoryID > 0 ) AS `IsGroup` 
				FROM 
					SBookContact u, 
					SBookEvents e, 
					SBookHours h 
				WHERE 
						h.UserID = !ContactID!
					AND h.IsDeleted = "0" 
					AND h.ProjectID = e.ID
					AND h.DateStart >= !DateStart! 
					AND h.DateEnd <= !DateEnd! 
					AND e.Component = "booking" 
					AND e.IsDeleted = "0" 
					AND u.ID = e.UserID
			) 
		) z 
		ORDER BY 
			z.DateStart ASC 
	';*/
	
	$q = str_replace( '!CategoryID!', '\'' . $_POST['CategoryID'] . '\'', $q );
	$q = str_replace( '!CategoryIDs!', ( implode( ',', getUserGroupsID( $u->UserID ) ) ), $q );
	$q = str_replace( '!DateStart!', '\'' . date( 'Y-01-01 00:00:00', $basetime ) . '\'', $q );
	$q = str_replace( '!DateEnd!', '\'' . date( 'Y-12-31 23:59:59', $basetime ) . '\'', $q );
	$q = str_replace( '!ContactID!', '\'' . $u->ID  . '\'', $q );
	$q = str_replace( '!ContactIDs!', ( getUserContactsID( $u->ID ) ? implode( ',', getUserContactsID( $u->ID ) ) : $u->ID ), $q );
	
	if( $rows = $database->fetchObjectRows( $q ) )
	{
		$ids = array(); $img = array(); $hours = array();
		
		foreach( $rows as $e )
		{
			$ids[$e->EventID] = $e->EventID;
			
			if( $e->ImageID > 0 )
			{
				$img[$e->ImageID] = $e->ImageID;
			}
		}
		
		if( $ids && ( $hr = $database->fetchObjectRows( '
			SELECT 
				h.ID AS HourID, 
				h.UserID AS ContactID, 
				h.Role AS HourRole, 
				h.ProjectID AS EventID, 
				h.DateStart, 
				h.DateEnd, 
				h.IsNight, 
				h.Access, 
				h.DateCreated, 
				h.DateModified 
			FROM 
				SBookEvents e, 
				SBookHours h 
			WHERE
					e.ID IN (' . implode( ',', $ids ) . ') 
				AND h.ProjectID = e.ID 
				AND h.IsDeleted = "0" 
			ORDER BY 
				h.DateStart ASC 
		' ) ) )
		{
			foreach( $hr as $h )
			{
				if( !isset( $hours[$h->EventID] ) )
				{
					$hours[$h->EventID] = array();
				}
				
				if( !isset( $hours[$h->EventID][date('Ymd',strtotime($h->DateStart))] ) )
				{
					$hours[$h->EventID][date('Ymd',strtotime($h->DateStart))] = array();
					$hours[$h->EventID][date('Ymd',strtotime($h->DateStart))]['DateStart'] = $h->DateStart;
					$hours[$h->EventID][date('Ymd',strtotime($h->DateStart))]['DateEnd'] = $h->DateEnd;
					$hours[$h->EventID][date('Ymd',strtotime($h->DateStart))]['HourSlots'] = array();
				}
				
				$hours[$h->EventID][date('Ymd',strtotime($h->DateStart))]['HourSlots'][] = $h;
			}
		}
		
		// Use library api to get images and files through the api
		/*if( $img && ( $im = $database->fetchObjectRows( '
			SELECT 
				f.DiskPath, 
				i.Filename, 
				i.ID, 
				i.Width, 
				i.Height,
				i.ImageFolder,
				i.Filesize 
			FROM 
				Folder f, 
				Image i 
			WHERE 
					i.ID IN (' . implode( ',', $img ) . ') 
				AND f.ID = i.ImageFolder 
			ORDER BY 
				ID ASC 
		' ) ) )
		{
			$img = array();
			
			foreach( $im as $i )
			{
				$obj = new stdClass();
				$obj->ID = $i->ID;
				$obj->Width = $i->Width;
				$obj->Height = $i->Height;
				$obj->Filename = $i->Filename;
				$obj->ImageFolder = $i->ImageFolder;
				$obj->Filesize = $i->Filesize;
				$obj->DiskPath = ( $i->DiskPath != '' ? $i->DiskPath : ( BASE_URL . 'upload/images-master/' ) );
				$obj->ImgUrl = ( $obj->DiskPath && $obj->Filename ? ( $obj->DiskPath . $obj->Filename ) : false );
				
				$img[$i->ID] = $obj;
			}
		}*/
		
		foreach( $rows as $r )
		{
			/*if( isset( $img[$r->ImageID] ) )
			{
				$r->ImageData = $img[$r->ImageID];
			}*/
			
			if( $r->DateStart == '0000-00-00 00:00:00' && $r->DateEnd == '0000-00-00 00:00:00' && isset( $hours[$r->EventID] ) && is_array( $hours[$r->EventID] ) )
			{
				foreach( $hours[$r->EventID] as $h )
				{
					if( $h['DateStart'] && $h['DateEnd'] )
					{
						// Make a clone with the values of the event
						$o = new stdClass();
						foreach( $r as $k=>$v ) $o->$k = $v;
						$o->DateStart = $h['DateStart'];
						$o->DateEnd = $h['DateEnd'];
						$o->HourSlots = $h['HourSlots'];
						
						// Add to event list
						$events[] = $o;
					}
				}
			}
			else if( isset( $hours[$r->EventID][date('Ymd',strtotime($r->DateStart))] ) )
			{
				$r->HourSlots = $hours[$r->EventID][date('Ymd',strtotime($r->DateStart))]['HourSlots'];
				
				$events[] = $r;
			}
			else
			{
				$events[] = $r;
			}
		}
	}
	
	
	
	if( $events )
	{
		if( isset( $_POST['Type'] ) && $_POST['Type'] == 'events' )
		{
			$obj = new stdClass();
			$obj->Events = $events;
		}
		else
		{
			switch( $_POST['Mode'] )
			{
				case 'day':
					$obj = new CalendarDay( date( 'Y-m-d', $basetime ) );
					break;
			
				case 'week':
					$obj = new CalendarWeek( date( 'Y-m-d', $basetime ) );
					break;
			
				case 'year':
					$obj = new CalendarYear( date( 'Y-m-d', $basetime ) );
					break;
			
				default:
					$obj = new CalendarMonth( date( 'Y-m-d', $basetime ) );
					break;
			}
		
			if ( count ( $events ) && is_array ( $events ) )
			{
				$obj->ImportEvents ( $events, 'DateStart', 'DateEnd' );
			}
		}
	}
	
	if( $obj )
	{
		$xml = XMLSerializer::generateValidXmlFromObj( $obj );
		
		outputXML ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' ? $obj : $xml );
		//outputXML ( $xml );
	}
	
	throwXmlMsg ( EMPTY_LIST );
}

throwXmlError ( MISSING_PARAMETERS );

?>
