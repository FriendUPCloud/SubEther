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

$str = '';

if( !isset( $_REQUEST['js'] ) )
{
	$str .= '<div id="CalendarContent">';
}

$str .= '<div class="day eventview">';

$hasAccess = '';

if( $parent->access && $parent->access->Read && $parent->access->Write && $parent->access->Delete && $parent->access->Admin )
{
	$hasAccess = true;
}

if( $events )
{
	//die( print_r( $events,1 ) . ' --' );
	
	$ids = array(); $hours = array();
	
	foreach( $events as $e )
	{
		$ids[$e->ID] = $e->ID;
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
	', false, 'events/functions/eventview.php' ) )
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
	
	foreach( $events as $e )
	{
		$str .= '<div class="event CalendarDay"><div>';
		$str .= '<div id="EventImage_' . $e->ID . '" class="image' . ( !$e->ImageData ? ' edit' : '' ) . '">';
		
		if( $e->ImageData && isset( $e->ImageData->ImgUrl ) )
		{
			$str .= '<div class="imagecontainer" style="background-image:url(\'' . $e->ImageData->ImgUrl . '\');background-repeat:no-repeat;background-position:center center;background-size:cover;width:100%;height:100%;max-width:' . $e->ImageData->Width . 'px;max-height:' . $e->ImageData->Height . 'px;"></div>';
		}
		
		$str .= '<div class="eventdate">';
		$str .= '<div class="month">' . i18n( 'i18n_a_' . date( 'F', strtotime( $e->DateStart ) ) ) . '</div>';
		$str .= '<div class="day">' . date( 'd', strtotime( $e->DateStart ) ) . '</div>';
		$str .= '</div>';
		
		if( $hasAccess && ( $parent->access->UserID == $e->UserID ) )
		{
			$str .= '<div class="fileupload" onclick="ge(\'FilesUploadBtn_' . $e->ID . '\').click()"><div></div></div>';
		}
		
		$str .= '</div>';
		
		$str .= '<div class="clearboth" style="clear:both;"></div>';
		
		if( $webuser->ID == 81 )
		{
			//$parent->cuser = true;
			//$parent->webuser = true;
			//die( print_r( $parent,1 ) . ' -- ' . print_r( $e,1 ) );
		}
		
		$str .= '<br>';
		
		$str .= '<div style="float:right;" class="buttons">';
		
		if( isset( $hours[$e->ID]['Available'] ) && !isset( $hours[$e->ID]['Mine'] ) )
		{
			$str .= '<span><button onclick="SignupEvent(\'' . $hours[$e->ID]['HourID'] . '\',this)">' . i18n( 'i18n_Sign up' ) . '</button></span>';
		}
		else if( isset( $hours[$e->ID]['Pending'] ) )
		{
			$str .= '<span><button onclick="SignupEvent(\'' . $hours[$e->ID]['HourID'] . '\',this)">' . i18n( 'i18n_Accept' ) . '</button>';
			$str .= '<button onclick="SignoffEvent(\'' . $hours[$e->ID]['HourID'] . '\',this)">' . i18n( 'i18n_Decline' ) . '</button></span>';
		}
		else if( isset( $hours[$e->ID]['Mine'] ) )
		{
			$str .= '<span><button onclick="SignoffEvent(\'' . $hours[$e->ID]['HourID'] . '\',this)">' . i18n( 'i18n_Sign off' ) . '</button></span>';
		}
		
		$str .= '<button onclick="EditEvent(this.parentNode.parentNode,event,\'' . $e->DateStart . '\',\'' . $e->ID . '\',false,\'extended\')">' . i18n( 'i18n_Edit' ) . '</button>';
		$str .= '<button onclick="DeleteEvent(' . $e->ID . ')">' . i18n( 'i18n_Delete' ) . '</button>';
		$str .= '</div>';
		
		$str .= '<div class="eventinfo">';
		$str .= '<div class="name"><h3>' . $e->Name . ( $hasAccess ? ' <a href="javascript:void(0)" onclick="InviteByICS(' . $e->ID . ')">[ics]</a>' : '' ) . '</h3></div>';
		$str .= '<div class="timedate">' . i18n( 'i18n_' . date( 'l', strtotime( $e->DateStart ) ) ) . ', ' . i18n( 'i18n_' . date( 'F', strtotime( $e->DateStart ) ) ) . ' ' . date( 'j', strtotime( $e->DateStart ) ) . ' ' . i18n( 'i18n_at' ) . ' ' . date( 'H:i', strtotime( $e->DateStart ) ) . ' - ';
		$str .= i18n( 'i18n_' . date( 'l', strtotime( $e->DateEnd ) ) ) . ', ' . i18n( 'i18n_' . date( 'F', strtotime( $e->DateEnd ) ) ) . ' ' . date( 'j', strtotime( $e->DateEnd ) ) . ' ' . i18n( 'i18n_at' ) . ' ' . date( 'H:i', strtotime( $e->DateEnd ) ) . '</div>';
		$str .= '<div class="place">' . $e->Place . '</div>';
		$str .= '<div class="moreinfo"></div>';
		$str .= '<div class="description">' . $e->Details . '</div>';
		$str .= '</div>';
		
		$str .= '<div class="clearboth" style="clear:both;"></div>';
		
		$str .= '</div>';
		
		//$str .= '<div id="Content">' . ( $parent->mode ? IncludeComponent( $parent->mode, $parent ) : '' ) . '</div>';
		//$str .= '<div id="Content">' . ( $parent->mode ? IncludeComponent( $parent->mode, $parent, true ) : '' ) . '</div>';
		
		$str .= '</div>';
	}
	
	if( $hasAccess )
	{
		$str .= '<div class="upload_btn">';
		$str .= '<div><span>' . i18n( 'i18n_Upload Image' ) . '</span></div>';
		$str .= '<form method="post" target="fileIframe" name="FilesUpload_' . $e->ID . '" enctype="multipart/form-data" action="' . /*$parent->route . */'?component=library&action=uploadfile">';
		$str .= '<input type="file" class="file_upload_btn" id="FilesUploadBtn_' . $e->ID . '" name="events" onchange="fileselect( this, \'FilesUpload_' . $e->ID . '\' )"/>';
		$str .= '<input type="hidden" id="EventID_' . $e->ID . '" name="eventid" value="' . $e->ID . '">';
		$str .= '</form>';
		$str .= '<script>setOpacity ( ge(\'FilesUploadBtn_' . $e->ID . '\' ), 0 );</script>';
		$str .= '</div>';
	}
}

$str .= '</div>';

if( !isset( $_REQUEST['js'] ) )
{
	$str .= '</div>';
	
	$str .= '<div id="Content">' . ( $parent->mode ? IncludeComponent( $parent->mode, $parent, true ) : '' ) . '</div>';
}

?>
