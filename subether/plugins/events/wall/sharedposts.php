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

global $document, $database, $webuser;

$plugin = true;

$root = 'subether/';
$pbase = 'subether/components/events';

include_once ( $pbase . '/include/functions.php' );

$document->addResource ( 'stylesheet', $pbase . '/css/events.css' );
$document->addResource ( 'javascript', $pbase . '/javascript/events.js' );

if( !$plugins && !is_array( $plugins ) )
{
	$plugins = array();
}

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if ( file_exists ( $pbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $pbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	die( 'failed action request - events' );
}

$events = false;

if( strtolower( $parent->folder->Name ) != 'events' && !$_REQUEST['event'] )
{
	include_once ( $pbase . '/functions/events.php' );
}

if( $events )
{
	$evts = array(); $ids = array(); $img = array(); $hours = array();
	
	$MaxImgWidth = 743;
	$MaxImgHeight = 420;
	
	foreach( $events as $evt )
	{
		$ids[$evt->ID] = $evt->ID;
		
		if ( $evt->ImageID > 0 )
		{
			$img[$evt->ImageID] = $evt->ImageID;
		}
	}
	
	if( $img && $im = $database->fetchObjectRows( '
		SELECT
			f.DiskPath, i.Filename, i.ID, i.UniqueID, i.Width, i.Height 
		FROM
			Folder f, Image i
		WHERE
			i.ID IN (' . implode( ',', $img ) . ') AND f.ID = i.ImageFolder
		ORDER BY
			ID ASC
	', false, 'plugins/events/wall/sharedposts.php' ) )
	{
		$img = array();
		
		foreach( $im as $i )
		{
			$obj = new stdClass();
			$obj->ID = $i->ID;
			$obj->Width = $i->Width;
			$obj->Height = $i->Height;
			$obj->Filename = $i->Filename;
			$obj->DiskPath = ( $i->DiskPath != '' ? $i->DiskPath : ( BASE_URL . 'upload/images-master/' ) );
			$obj->ImgUrl = ( $obj->DiskPath && $obj->Filename ? ( $obj->DiskPath . $obj->Filename ) : false );
			$obj->ImageRes = ( $MaxImgWidth <= $obj->Width ? ' big' : ' small' );
			
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
	
	if( $ids && $hrs = $database->fetchObjectRows( '
		SELECT 
			h.* 
		FROM 
			SBookHours h 
		WHERE 
				h.ProjectID IN ( ' . implode( ',', $ids ) . ' ) 
			AND h.IsDeleted = "0" 
		ORDER BY 
			h.DateStart ASC 
	', false, 'plugins/events/wall/sharedposts.php' ) )
	{
		foreach( $hrs as $hr )
		{
			$hours[$hr->ProjectID] = ( isset( $hours[$hr->ProjectID] ) ? $hours[$hr->ProjectID] : array() );
			$hours[$hr->ProjectID][] = $hr;
			
			if( $hr->UserID == $webuser->ContactID )
			{
				if( $hr->IsAccepted )
				{
					$hours[$hr->ProjectID]['Mine'] = true;
				}
				else
				{
					$hours[$hr->ProjectID]['Pending'] = true;
				}
				$hours[$hr->ProjectID]['HourID'] = ( isset( $hours[$hr->ProjectID]['HourID'] ) ? $hours[$hr->ProjectID]['HourID'] : $hr->ID );
			}
			
			if( $hr->UserID == 0 )
			{
				$hours[$hr->ProjectID]['Available'] = ( isset( $hours[$hr->ProjectID]['Available'] ) ? ( $hours[$hr->ProjectID]['Available'] + 1 ) : 1 );
				$hours[$hr->ProjectID]['DateStart'] = ( isset( $hours[$hr->ProjectID]['DateStart'] ) ? $hours[$hr->ProjectID]['DateStart'] : $hr->DateStart );
				$hours[$hr->ProjectID]['DateEnd'] = ( isset( $hours[$hr->ProjectID]['DateEnd'] ) ? $hours[$hr->ProjectID]['DateEnd'] : $hr->DateEnd );
				$hours[$hr->ProjectID]['HourID'] = ( isset( $hours[$hr->ProjectID]['HourID'] ) ? $hours[$hr->ProjectID]['HourID'] : $hr->ID );
			}
		}
	}
	
	foreach( $events as $evt )
	{
		// If event is booking or evetmode is profile and userid is not current userid jump over it
		if( $evt->Component == 'booking' || $evt->Component == 'orders' || ( $evt->EventMode == 'profile' && $evt->UserID != $parent->cuser->ContactID ) )
		{
			continue;
		}
		
		$evt->DateStart = ( isset( $hours[$evt->ID]['DateStart'] ) ? $hours[$evt->ID]['DateStart'] : $evt->DateStart );
		$evt->DateEnd = ( isset( $hours[$evt->ID]['DateEnd'] ) ? $hours[$evt->ID]['DateEnd'] : $evt->DateEnd );
		$evt->Href = ( $evt->IsGroup ? ( $parent->path . 'groups/' . ( $evt->SBC_ID ? $evt->SBC_ID : $evt->CategoryID ) . '/' ) : ( $parent->path . ( $evt->User_Name ? $evt->User_Name : $evt->Username ) . '/' ) ) . 'events/?r=day&categoryid=' . $evt->CategoryID . '&event=' . $evt->ID . '&basetime=' . strtotime( $evt->DateStart );
		$evt->ImageData = ( $evt->ImageID > 0 && isset( $img[$evt->ImageID] ) ? $img[$evt->ImageID] : false );
		
		$obj = new stdClass();
		$obj->ID = $evt->ID . '#events';
		$obj->Date = $evt->DateCreated;
		$obj->DateModified = $evt->DateModified;
		$obj->CategoryID = $evt->CategoryID;
		$obj->Access = $evt->Access;
		$obj->SenderID = $evt->UserID;
		$obj->Component = $evt->Component;
		$obj->Type = 'event';
		$obj->ParentID = 0;
		$obj->IsGroup = $evt->IsGroup;
		$obj->Name = $evt->Username;
		$obj->Image = $evt->UserImage;
		$obj->User_Name = $evt->User_Name;
		$obj->SBC_Name = $evt->SBC_Name;
		$obj->SBC_ID = $evt->SBC_ID;
		$obj->Hours = ( isset( $hours[$evt->ID] ) ? $hours[$evt->ID] : false );
		$obj->HTML  = '<div class="html">';
		$obj->HTML .= '<div class="ParseContent Event">';
		
		if( $evt->ImageData && isset( $evt->ImageData->ImgUrl ) )
		{
			$obj->HTML .= '<div class="image event ' . $evt->ImageData->ImageRes . '">';
			$obj->HTML .= '<div class="imagecontainer">';
			$obj->HTML .= '<a style="background-image: url(\'' . $evt->ImageData->ImgUrl . '\')" href="' . $evt->Href . '"><img style="background-image:url(\'' . $evt->ImageData->ImgUrl . '\');max-width:' . $evt->ImageData->Width . 'px;max-height:' . $evt->ImageData->Height . 'px;" src="' . $evt->ImageData->ImgUrl . '"></a>';
			$obj->HTML .= '</div>';
			$obj->HTML .= '<div class="eventdate">';
			$obj->HTML .= '<div class="month">' . i18n ( 'i18n_a_' . date( 'F', strtotime( $evt->DateStart ) ) ) . '</div>';
			$obj->HTML .= '<div class="day">' . date( 'd', strtotime( $evt->DateStart ) ) . '</div>';
			$obj->HTML .= '</div>';
			$obj->HTML .= '</div>';
		}
		
		$obj->HTML .= '<div class="text">';
		$obj->HTML .= '<h3><a href="' .$evt->Href . '">' . $evt->Name . '</a></h3>';
		$obj->HTML .= '<p class="timedate">' . date( 'l, F j', strtotime( $evt->DateStart ) ) . ' at ' . date( 'H:i', strtotime( $evt->DateStart ) ) . '</p>';
		$obj->HTML .= '<p class="place">' . $evt->Place . '</p>';
		
		if( isset( $hours[$evt->ID]['Available'] ) && !isset( $hours[$evt->ID]['Mine'] ) )
		{
			$obj->HTML .= '<p class="button"><button onclick="SignupEvent(\'' . $hours[$evt->ID]['HourID'] . '\',this)">Sign up</button></p>';
		}
		else if( isset( $hours[$evt->ID]['Pending'] ) )
		{
			$obj->HTML .= '<p class="button"><button onclick="SignupEvent(\'' . $hours[$evt->ID]['HourID'] . '\',this)">Accept</button>';
			$obj->HTML .= '<button onclick="SignoffEvent(\'' . $hours[$evt->ID]['HourID'] . '\',this)">Decline</button></p>';
		}
		else if( isset( $hours[$evt->ID]['Mine'] ) )
		{
			$obj->HTML .= '<p class="button"><button onclick="SignoffEvent(\'' . $hours[$evt->ID]['HourID'] . '\',this)">Sign off</button></p>';
		}
		
		$obj->HTML .= '</div>';
		
		$obj->HTML .= '<div style="clear:both" class="clearboth"></div>';
		
		$obj->HTML .= '</div>';
		$obj->HTML .= '</div>';
		
		$evts[] = $obj;
	}
	
	$plugins['events'] = $evts;
}

//die( print_r( $events,1 ) . ' -- ' . $q );

?>
