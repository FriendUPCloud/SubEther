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

$basetime = ( $_SESSION['events_basetime'] ? $_SESSION['events_basetime'] : strtotime( date( 'Y-m-d H:i:s' ) ) );

$evtmode = false; $events = array();

// Main Feed ------------------------------------------------------------------------------------------------------------------------------
if( strtolower( $parent->folder->Name ) == 'wall' && strtolower( $parent->module ) == 'main' )
{	
	$qe = '
		SELECT
			"newsfeed" AS `EventMode`, 
			e.*,
			u.Display,
			u.Firstname,
			u.Middlename,
			u.Lastname,
			u.Username,
			u.ImageID AS UserImage, 
			u2.Username AS User_Name, 
			c.Name AS SBC_Name, 
			c.ID AS SBC_ID, 
			( e.CategoryID > 0 ) AS `IsGroup` 
		FROM 
			SBookContact u,
			SBookEvents e 
				LEFT JOIN SBookContact u2 ON
				(
						e.UserID = u2.ID
					AND e.UserID > 0
				) 
				LEFT JOIN SBookCategory c ON
				(
						e.CategoryID = c.ID
					AND e.CategoryID > 0
				) 
		WHERE
			(
				' . ( getUserGroupsID( $parent->cuser->UserID ) ? '
				(
					e.CategoryID IN ( !CategoryIDs! )
				) 
				OR
				' : '' ) . '
				(
						e.UserID IN ( !ContactIDs! )
					AND e.CategoryID = "0"
				)
			) 
			AND e.IsDeleted = "0" 
			AND u.ID = e.UserID
			AND
			(
				(
						e.DateStart >= !DateStart!
					AND e.DateEnd <= !DateEnd!
				)
				OR
				(
					e.Component = "booking" 
				)
			)
			AND
			(
				(
					e.Access = "0"
				)
				' . ( isset( $parent->access->IsAdmin ) ? '
				OR
				(
						e.Access = "4"
					AND e.CategoryID = !CategoryID!
				) 
				' : '' ) . ( $webuser->ContactID > 0 ? '
				OR
				(
						e.Access = "4"
					AND e.UserID = !ContactID!
				) 
				OR
				(
						e.Access = "2"
					AND e.UserID = !ContactID!
				) 
				OR
				(
						e.Access = "1"
					AND e.UserID IN ( !ContactIDs! )
				)
				' : '' ) . '
			) 
		ORDER BY 
			e.ID DESC
		' . ( isset( $limit ) ? ( 'LIMIT ' . $limit ) : '' ) . '
	';
	
	$evtmode = 'newsfeed';
}
// Profile --------------------------------------------------------------------------------------------------------------------------------
else if( strtolower( $parent->folder->MainName ) == 'profile' )
{		
	/*$qe = '
		SELECT 
			"profile" AS `EventMode`, 
			e.*,
			e.DateStart AS EventDateStart,
			e.DateEnd AS EventDateEnd,
			h.DateStart AS DateStart, 
			h.DateEnd AS DateEnd, 
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
			AND h.DateStart >= !DateStart! 
			AND h.DateEnd <= !DateEnd! 
			AND h.IsDeleted = "0" 
			AND h.ProjectID = e.ID 
			AND e.IsDeleted = "0" 
			AND u.ID = e.UserID
			' . ( isset( $_REQUEST['event'] ) ? 'AND e.ID = \'' . $_REQUEST['event'] . '\' ' : '' ) . '
		ORDER BY 
			h.DateStart ASC 
	';*/
	
	$qe = '
		SELECT * FROM 
		(
			( 
				SELECT
					"profile v1" AS `EventMode`,
					e.ID,
					"" AS ExtraID,
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
					' . ( isset( $_REQUEST['event'] ) ? '
					AND e.ID = \'' . $_REQUEST['event'] . '\' 
					' : '' ) . '
			)
			UNION 
			( 
				SELECT
					"profile v2" AS `EventMode`,
					e.ID,
					"" AS ExtraID,
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
					AND h.Title = "events" 
					AND e.UserID != h.UserID 
					AND e.DateStart >= !DateStart! 
					AND e.DateEnd <= !DateEnd! 
					AND e.Component = "events" 
					AND e.IsDeleted = "0" 
					AND u.ID = e.UserID
					' . ( isset( $_REQUEST['event'] ) ? '
					AND e.ID = \'' . $_REQUEST['event'] . '\'
					' : '' ) . '
			)
			UNION 
			( 
				SELECT
					"profile v3" AS `EventMode`,
					e.ID,
					"" AS ExtraID,
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
					' . ( isset( $_REQUEST['event'] ) ? '
					AND e.ID = \'' . $_REQUEST['event'] . '\'
					' : '' ) . '
			)
			UNION 
			(
				SELECT
					"profile v4" AS `EventMode`,
					o.ID,
					o.OrderID AS ExtraID,
					h.Title AS Component, 
					h.Type,
					o.F6 AS Name, 
					"" AS Place,
					o.F2 AS Details,
					o.Price,
					o.UserID,
					"" AS ImageID,
					o.CategoryID,
					o.DateCreated,
					o.DateModified,
					o.IsFinished,
					h.Access,
					h.NodeID,
					h.NodeMainID,
					h.DateStart, 
					h.DateEnd, 
					u.Display, 
					u.Firstname, 
					u.Middlename, 
					u.Lastname, 
					u.Username, 
					u.ImageID AS UserImage, 
					"1" AS `IsGroup` 
				FROM 
					SBookContact u, 
					SBookOrders o, 
					SBookHours h 
				WHERE 
						h.UserID = !ContactID!
					AND h.IsDeleted = "0" 
					AND h.ProjectID = o.ID
					AND h.DateStart >= !DateStart! 
					AND h.DateEnd <= !DateEnd! 
					AND h.Title = "orders"
					AND 
					( 
						(
							o.UserID > 0 AND o.UserID = !ContactID! 
						)
						OR 
						(
							o.Participants LIKE ("%' . $parent->cuser->ContactID . '%") 
						)
					) 
					AND o.IsDeleted = "0" 
					AND u.ID = o.UserID
					' . ( isset( $_REQUEST['event'] ) ? '
					AND o.ID = NULL 
					' : '' ) . '
			) 
		) z 
		ORDER BY 
			z.DateStart ASC
		' . ( isset( $limit ) ? ( 'LIMIT ' . $limit ) : '' ) . '
	';
	
	$evtmode = 'profile';
}
// Groups ---------------------------------------------------------------------------------------------------------------------------------
else
{	
	$qe = '
		SELECT
			"groups" AS `EventMode`, 
			e.*,
			u.Display,
			u.Firstname,
			u.Middlename,
			u.Lastname,
			u.Username,
			u.ImageID AS UserImage, 
			u2.Username AS User_Name, 
			c.Name AS SBC_Name, 
			c.ID AS SBC_ID, 
			"1" AS `IsGroup` 
		FROM 
			SBookContact u,
			SBookEvents e
				LEFT JOIN SBookContact u2 ON
				(
						e.UserID = u2.ID
					AND e.UserID > 0
				) 
				LEFT JOIN SBookCategory c ON
				(
						e.CategoryID = c.ID
					AND e.CategoryID > 0
				) 
		WHERE 
				e.CategoryID = !CategoryID! 
			AND e.IsDeleted = "0"
			AND u.ID = e.UserID 
			AND
			(
				(
						e.DateStart >= !DateStart!
					AND e.DateEnd <= !DateEnd!
				)
				OR
				(
					e.Component = "booking"
				)
			)
			AND
			(
				(
					e.Access = "0"
				) 
				' . ( isset( $parent->access->IsAdmin ) ? '
				OR 
				(
						e.Access = "4"
					AND e.CategoryID = !CategoryID!
				) 
				' : '' ) . ( $webuser->ContactID > 0 ? '
				OR 
				(
						e.Access = "4"
					AND e.UserID = !ContactID!
				) 
				OR 
				(
						e.Access = "2"
					AND e.UserID = !ContactID!
				) 
				OR 
				(
						e.Access = "1"
					AND e.UserID IN ( !ContactIDs! )
				)
				' : '' ) . '
			) 
			' . ( isset( $_REQUEST['event'] ) ? '
			AND e.ID = \'' . $_REQUEST['event'] . '\' 
			' : '' ) . '
		ORDER BY 
			e.ID DESC
		' . ( isset( $limit ) ? ( 'LIMIT ' . $limit ) : '' ) . '
	';
	
	$evtmode = 'groups';
}

$qe = str_replace( '!CategoryID!', '\'' . $parent->folder->CategoryID . '\'', $qe );
$qe = str_replace( '!CategoryIDs!', ( getUserGroupsID( $parent->cuser->UserID ) ? implode( ',', getUserGroupsID( $parent->cuser->UserID ) ) : '' ), $qe );
$qe = str_replace( '!DateStart!', '\'' . date( 'Y-01-01 00:00:00', $basetime ) . '\'', $qe );
$qe = str_replace( '!DateEnd!', '\'' . date( 'Y-12-31 23:59:59', $basetime ) . '\'', $qe );
$qe = str_replace( '!ContactID!', '\'' . $parent->cuser->ContactID  . '\'', $qe );
$qe = str_replace( '!ContactIDs!', ( getUserContactsID( $parent->cuser->ContactID ) ? implode( ',', getUserContactsID( $parent->cuser->ContactID ) ) : $parent->cuser->ContactID ), $qe );

//die( $qe . ' -- ' );

if( $rows = $database->fetchObjectRows( $qe, false, 'components/events/functions/events.php' ) )
{
	$ids = array(); $img = array(); $hours = array();
	
	//die( print_r( $rows,1 ) . ' -- ' . $qe );
	
	foreach( $rows as $e )
	{
		$ids[$e->ID] = $e->ID;
		
		if( $e->ImageID > 0 )
		{
			$img[$e->ImageID] = $e->ImageID;
		}
	}
	
	if( $ids && ( $hr = $database->fetchObjectRows( $hq = '
		SELECT 
			h.* 
		FROM 
			SBookHours h
				LEFT JOIN SBookEvents e ON 
				(
					e.ID = h.ProjectID 
				) 
		WHERE 
				h.ProjectID IN (' . implode( ',', $ids ) . ') 
			AND h.IsDeleted = "0" 
			' . ( $evtmode == 'profile' ? ( '
			AND 
			(
					h.UserID = \'' . $parent->cuser->ContactID . '\' 
				OR  e.UserID = \'' . $parent->cuser->ContactID . '\' 
			) 
			' ) : '' ) . '
		ORDER BY 
			h.DateStart ASC 
	', false, 'components/events/functions/events.php' ) ) )
	{
		foreach( $hr as $h )
		{
			if( !isset( $hours[$h->ProjectID] ) )
			{
				$hours[$h->ProjectID] = array();
			}
			
			if( !isset( $hours[$h->ProjectID][date('Ymd',strtotime($h->DateStart))] ) )
			{
				$hours[$h->ProjectID][date('Ymd',strtotime($h->DateStart))] = array();
				$hours[$h->ProjectID][date('Ymd',strtotime($h->DateStart))]['DateStart'] = $h->DateStart;
				$hours[$h->ProjectID][date('Ymd',strtotime($h->DateStart))]['DateEnd'] = $h->DateEnd;
				$hours[$h->ProjectID][date('Ymd',strtotime($h->DateStart))]['HourSlots'] = array();
			}
			
			$hours[$h->ProjectID][date('Ymd',strtotime($h->DateStart))]['HourSlots'][] = $h;
			$hours[$h->ProjectID][date('Ymd',strtotime($h->DateStart))]['Hours'] = ( isset( $hours[$h->ProjectID][date('Ymd',strtotime($h->DateStart))]['Hours'] ) ? ( $hours[$h->ProjectID][date('Ymd',strtotime($h->DateStart))]['Hours'] + $h->Hours ) : $h->Hours );
		}
	}
	
	if( $img && ( $im = $database->fetchObjectRows( '
		SELECT 
			f.DiskPath, 
			i.Filename, 
			i.ID,
			i.UniqueID, 
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
	', false, 'components/events/functions/events.php' ) ) )
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
			if ( $i->Filename )
			{
				$obj->DiskPath = ( BASE_URL . 'secure-files/images/' . ( $i->UniqueID ? $i->UniqueID : $i->ID ) . '/' );
				$obj->ImgUrl = $obj->DiskPath;
			}
			
			$img[$i->ID] = $obj;
			
			if ( !FileExists( $obj->DiskPath ) )
			{
				unset( $img[$i->ID] );
			}
		}
	}
	
	foreach( $rows as $r )
	{
		if( isset( $img[$r->ImageID] ) )
		{
			$r->ImageData = $img[$r->ImageID];
		}
		
		if( $r->DateStart == '0000-00-00 00:00:00' && $r->DateEnd == '0000-00-00 00:00:00' && isset( $hours[$r->ID] ) && is_array( $hours[$r->ID] ) )
		{
			foreach( $hours[$r->ID] as $h )
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
		else if( isset( $hours[$r->ID][date('Ymd',strtotime($r->DateStart))] ) )
		{
			if( $r->ExtraID && $r->Name )
			{
				$r->Name = ( $r->Name . ' (' . $r->ExtraID . ')' );
			}
			
			$r->Hours = $hours[$r->ID][date('Ymd',strtotime($r->DateStart))]['Hours'];
			$r->HourSlots = $hours[$r->ID][date('Ymd',strtotime($r->DateStart))]['HourSlots'];
			
			$events[] = $r;
		}
		else
		{
			$events[] = $r;
		}
	}
}

if( isset( $_REQUEST['dbugchris'] ) ) die( $parent->cuser->ContactID . ' .. ' . $qe . ' -- ' . $hq . ' [] ' . /*print_r( $events,1 ) . ' || '.*/ print_r( $rows,1 ) );


// If we have eventid render eventview else render the calendar
if( !$plugin && isset( $_REQUEST['event'] ) && $_REQUEST['event'] > 0 )
{
	include( 'eventview.php' );
	
	$cstr = $str;
	
	if( isset( $_REQUEST['js'] ) )
	{
		die( 'ok<!--separate-->' . $cstr );
	}
}
else
{
	$cstr = switchCalendarMode( date( 'Y-m-d', $basetime ), ( isset( $_REQUEST['r'] ) ? $_REQUEST['r'] : false ), false, $events, $parent->access );
	
	if( isset( $_REQUEST['js'] ) )
	{
		die( 'ok<!--separate-->' . $cstr );
	}
	
	$cstr = '<div id="CalendarContent">' . $cstr . '</div>';
}

?>
