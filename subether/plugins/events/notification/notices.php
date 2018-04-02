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

global $database, $webuser;

$plimit = 50;

//include ( 'subether/plugins/events/wall/sharedposts.php' );

if( !$plugins && !is_array( $plugins ) )
{
	$plugins = array();
}

$q = '
	SELECT
		"events" AS ContentType, 
		e.ID AS EventID,
		e.DateCreated AS Date, 
		e.DateModified,
		e.CategoryID,
		' . ( !$notseen ? '
		e.UserID AS OwnerID, 
		e.Name AS EventName, 
		e.Place AS EventPlace, 
		e.Details AS EventDetails, 
		e.DateStart AS EventDateStart, 
		e.DateEnd AS EventDateEnd, 
		e.ImageID, 
		e.Access, 
		e.IsFinished, 
		/*r.IsAccepted,*/ 
		u.ImageID AS Image, 
		' : '' ) . '
		r.IsNoticed, 
		r.ID AS NotificationID, 
		u.Username,
		h.ID AS HourID,
		h.DateStart, 
		h.DateEnd, 
		h.DateCreated, 
		h.IsAccepted, 
		( e.CategoryID > 0 ) AS `IsGroup` 
	FROM 
		SBookEvents e, 
		SBookHours h,
		SBookNotification r, 
		SBookContact u 
	WHERE
			r.ReceiverID = !ContactID!
		AND r.Type = "events"
		AND r.ReceiverID = h.UserID
		AND h.ID = r.ObjectID 
		AND h.IsDeleted = "0" 
		AND h.ProjectID = e.ID 
		AND e.IsDeleted = "0"
		AND e.Component = "events"
		AND u.ID = e.UserID 
	ORDER BY 
		h.DateCreated DESC
	LIMIT ' . $plimit . '
';

$q = str_replace( '!ContactID!', '\'' . $webuser->ContactID  . '\'', $q );

if( $evts = $database->fetchObjectRows( $q ) )
{
	foreach( $evts as $evt )
	{
		//$evt->Href = ( $evt->IsGroup ? ( $parent->path . 'groups/' . $evt->CategoryID . '/' ) : ( $parent->path . $evt->Username . '/' ) ) . 'events/?r=day&basetime=' . strtotime( $evt->EventDateStart );
		$evt->Href = ( $evt->IsGroup ? ( $parent->path . 'groups/' . $evt->CategoryID . '/' ) : ( $parent->path . $evt->Username . '/' ) ) . 'events/?r=day&categoryid=' . $evt->CategoryID . '&event=' . $evt->EventID . '&basetime=' . strtotime( $evt->DateStart );
	}
	
	$plugins['events'] = $evts;
}

//if( $webuser->ID == 81 ) die( print_r( $evts,1 ) . ' -- ' . $webuser->ContactID . ' -- ' . $q );

?>
