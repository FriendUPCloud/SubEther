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

include_once ( 'subether/components/orders/include/functions.php' );

$document->addResource ( 'javascript', 'subether/components/orders/javascript/calendar.js' );

$hasAccess = '';
		
if( $parent->access && $parent->access->Read && $parent->access->Write && $parent->access->Delete && $parent->access->Admin )
{
	$hasAccess = true;
}

if( $_POST['date'] && $webuser->ID > 0 )
{
	// --- If we have an hourslot to edit -----------------------------------------------------------------------
	if( $_POST['sid'] )
	{
		$q = '
			SELECT 
				h.*,
				e.Name as EventName 
			FROM 
				SBookHours h,
				SBookEvents e 
			WHERE 
					h.ID = \'' . $_POST['sid'] . '\' 
				AND e.ID = h.ProjectID 
			ORDER BY 
				h.ID DESC 
		';
		
		if( $slot = $database->fetchObjectRow( $q, false, 'components/events/functions/eventedit.php' ) )
		{
			$str .= '<div onclick="cancelBubble(event)" id="EditEvent" Date="' . $_POST['date'] . '" EventID="' . ( isset( $_POST['eid'] ) ? $_POST['eid'] : 0 ) . '" EventSlotID="' . ( isset( $_POST['sid'] ) ? $_POST['sid'] : 0 ) . '">';
			$str .= '<div><input onclick="SelectEventInput(this,\'users\',event)" onkeyup="SearchEvent(this,\'users\',event)" id="EventAttendee" type="text" style="width:94%;" placeholder="' . i18n( 'i18n_EventAttendee' ) . '" value="' . ( GetUserDisplayname( $slot->UserID ) ? GetUserDisplayname( $slot->UserID ) : '' ) . '" vid="' . $slot->UserID . '"/></div>';
			$str .= '<div><input onclick="SelectEventInput(this,\'roles\',event)" onkeyup="SearchEvent(this,\'roles\',event)" id="EventRole" type="text" style="width:94%;" placeholder="' . i18n( 'i18n_Role' ) . '" value="' . $slot->Role . '" vid="' . $slot->Role . '"/></div>';
			$str .= '<div><input onclick="SelectEventInput(this,\'events\',event)" onkeyup="SearchEvent(this,\'events\',event)" id="EventName" type="text" style="width:94%" placeholder="' . i18n( 'i18n_EventName' ) . '" value="' . $slot->EventName . '" vid="' . $slot->ProjectID . '"/>';
			$str .= '</div>';
			$str .= '<div><input onclick="SelectEventInput(this)" id="EventStart" type="text" style="width:37.5%;" placeholder="' . i18n( 'i18n_TimeStart' ) . '" value="' . date( 'H:i', strtotime( $slot->DateStart ) ) . '"/>';
			$str .= '<span style="width:10%;"> - </span>';
			$str .= '<input onclick="SelectEventInput(this)" id="EventEnd" type="text" style="width:37.5%;" placeholder="' . i18n( 'i18n_TimeEnd' ) . '" value="' . date( 'H:i', strtotime( $slot->DateEnd ) ) . '"/></div>';
			$str .= '</div>';
			
			die( 'ok<!--separate-->' . $str );
		}
	}
	// --- Else get the empty form ---------------------------------------------------------------------------
	else
	{
		$slots = 1;
		
		if( $_POST['eid'] )
		{
			$q = '
				SELECT 
					e.*, 
					i.UniqueID AS ImageUniqueID, 
					i.Width, 
					i.Height 
				FROM 
					SBookEvents e
						LEFT JOIN Image i ON
						(
								e.ImageID > 0
							AND e.ImageID = i.ID
						) 
				WHERE 
						e.ID = \'' . $_POST['eid'] . '\' 
				ORDER BY 
					e.ID DESC 
			';
			
			$event = $database->fetchObjectRow( $q, false, 'components/events/functions/eventedit.php' );
			
			$slots = $database->fetchObjectRows( '
				SELECT ID
				FROM SBookHours
				WHERE ProjectID = \'' . $_POST['eid'] . '\'
				AND IsDeleted = "0"
				AND DateStart >= \'' . date( 'Y-m-d 00:00:00.000000', strtotime( $_POST['date'] ) ) . '\' 
				AND DateEnd <= \'' . date( 'Y-m-d 23:59:59.000000', strtotime( $_POST['date'] ) ) . '\' 
				ORDER BY ID DESC
			', false, 'components/events/functions/eventedit.php' );
			
			$slots = ( $slots ? count( $slots ) : 1 );
		}
		
		switch( $_POST['mode'] )
		{
			// Extended mode has more options
			case 'extended':
				
				$str .= '<div id="EventEditor" class="extended ext1" Date="' . $_POST['date'] . '" EventID="' . ( isset( $_POST['eid'] ) ? $_POST['eid'] : 0 ) . '" EventSlotID="' . ( isset( $_POST['sid'] ) ? $_POST['sid'] : 0 ) . '">';
				
				// Event Image Editor
				
				if( $_POST['eid'] > 0 )
				{
					$str .= '<div id="EventImage_' . $event->ID . '" class="image' . ( !$event->ImageID ? ' edit' : '' ) . '">';
					
					if( $event->ImageID > 0 )
					{
						$str .= '<div class="imagecontainer" style="background-image:url(\'' . ( BASE_URL . 'secure-files/images/' . ( $event->ImageUniqueID ? $event->ImageUniqueID : $event->ImageID ) . '/' ) . '\');background-repeat:no-repeat;background-position:center center;background-size:cover;width:100%;height:100%;max-width:' . $event->Width . 'px;max-height:' . $event->Height . 'px;"></div>';
					}
					
					$str .= '<div class="eventdate">';
					$str .= '<div class="month">' . i18n( 'i18n_' . $mode . date( 'F', strtotime( $event->DateStart ) ) ) . '</div>';
					$str .= '<div class="day">' . date( 'd', strtotime( $event->DateStart ) ) . '</div>';
					$str .= '</div>';
					
					if( $hasAccess && ( !$event || $event->UserID == $parent->access->UserID ) )
					{
						$str .= '<div class="fileupload" onclick="ge(\'FilesUploadBtn_' . $event->ID . '\').click()"><div></div></div>';
					}
					
					$str .= '</div>';
				}
				
				
				$str .= '<div class="field nr1"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Name' ) . '</td>';
				$str .= '<td class="value"><input id="EventName" type="text" onclick="SelectEventInput(this,\'events\',event)" onkeyup="SearchEvent(this,\'events\',event)" placeholder="' . i18n( 'i18n_EventName' ) . '" ' . ( $_POST['eid'] > 0 ? ( 'value="' . $event->Name . '"' . ( $_POST['date'] == date( 'Y-m-d', strtotime( $event->DateStart ) ) ? ( ' vid="' . $event->ID . '"' ) : '' ) ) : '' ) . '/></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr2"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Place' ) . '</td>';
				$str .= '<td class="value"><input id="EventPlace" type="text" onclick="SelectEventInput(this)" placeholder="' . i18n( 'i18n_EventPlace' ) . '" value="' . $event->Place . '"/></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr3"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Details' ) . '</td>';
				$str .= '<td class="value"><textarea id="EventDetails" placeholder="' . i18n( 'i18n_EventDetails' ) . '">' . $event->Details . '</textarea></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr4"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Type' ) . '</td>';
				$str .= '<td class="value"><select id="EventType">';
				$str .= '<option value=""' . ( isset( $event->Type ) && $event->Type == '' ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Other' ) . '</option>';
				$str .= '<option value="meeting"' . ( isset( $event->Type ) && $event->Type == 'meeting' ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Meeting' ) . '</option>';
				$str .= '<option value="notification"' . ( isset( $event->Type ) && $event->Type == 'notification' ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Notification' ) . '</option>';
				//$str .= '<option value="vacation"' . ( isset( $event->Type ) && $event->Type == 'vacation' ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Vacation' ) . '</option>';
				$str .= '</select></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr4"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Members' ) . '</td>';
				$str .= '<td class="value"><div class="customfield">';
				$str .= '<span id="FindMembers">';
				
				if( $event->ID > 0 && ( $hrs = $database->fetchObjectRows( $q = '
					SELECT
						c.*
					FROM
						SBookHours h,
						SBookContact c 
					WHERE
							h.ProjectID = \'' . $event->ID . '\'
						AND h.Title = "events" 
						AND h.UserID > 0
						AND h.IsDeleted = "0"
						AND h.DateStart >= \'' . date( 'Y-m-d 00:00:00.000000', strtotime( $_POST['date'] ) ) . '\' 
						AND h.DateEnd <= \'' . date( 'Y-m-d 23:59:59.000000', strtotime( $_POST['date'] ) ) . '\' 
						AND c.ID = h.UserID 
					ORDER BY
						h.ID ASC 
				' ) ) )
				{
					$uid = array();
					
					foreach( $hrs as $hr )
					{
						$uid[$hr->ID] = $hr->ID;
					}
					
					$uids = GetUserDisplayname( $uid );
					
					foreach( $hrs as $hr )
					{
						$str .= '<span class="member">' . ( isset( $uids[$hr->ID] ) ? $uids[$hr->ID] : $hr->Username ) . '<input type="hidden" value="' . $hr->UserID . '" id="uid_' . $hr->UserID . '"><a onclick="removeMember( ' . $hr->UserID . ' )" href="javascript:void(0);">x</a></span>';
					}
				}
				//die( $q . ' --' );
				$str .= '</span>';
				$str .= '<input id="MemberSearch" type="text" name="search" placeholder="' . i18n( 'i18n_Who do you want to add to the event' ) . '?" onkeyup="findMembers( false, this )"/>';
				$str .= '</div><div id="ListFoundMembers"></div></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr5"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Start' ) . '</td>';
				$str .= '<td class="value">' . renderHtmlFields( 'datetimefield', 'text', array( 'EventStartDate', 'EventStart' ), ( $_POST['eid'] > 0 && $event->DateStart ? $event->DateStart : date( 'Y-m-d 08:00:00', strtotime( $_POST['date'] ) ) ), i18n( 'i18n_DateStart' ), '', '', 'onclick="OrderCalendar(this)"' ) . '</td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr5"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_End' ) . '</td>';
				$str .= '<td class="value">' . renderHtmlFields( 'datetimefield', 'text', array( 'EventEndDate', 'EventEnd' ), ( $_POST['eid'] > 0 && $event->DateEnd ? $event->DateEnd : date( 'Y-m-d 15:30:00', strtotime( $_POST['date'] ) ) ), i18n( 'i18n_DateEnd' ), '', '', 'onclick="OrderCalendar(this)"' ) . '</td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr7"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Slots' ) . '</td>';
				$str .= '<td class="value"><input id="EventSlots" type="text" onclick="SelectEventInput(this)" placeholder="' . i18n( 'i18n_EventSlots' ) . '" value="' . $slots . '"/></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr8"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Access' ) . '</td>';
				$str .= '<td class="value"><select id="EventAccess">';
				$str .= '<option value="0"' . ( isset( $event->Access ) && $event->Access == 0 ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Public' ) . '</option>';
				$str .= '<option value="1"' . ( isset( $event->Access ) && $event->Access == 1 ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Contacts' ) . '</option>';
				$str .= '<option value="2"' . ( isset( $event->Access ) && $event->Access == 2 ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Only Me' ) . '</option>';
				//$str .= '<option value="3"' . ( isset( $event->Access ) && $event->Access == 3 ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Custom' ) . '</option>';
				if( isset( $parent->access->IsAdmin ) )
				{
					$str .= '<option value="4"' . ( isset( $event->Access ) && $event->Access == 4 ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Admin' ) . '</option>';
				}
				$str .= '</select></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="buttons">';
				
				if( $hasAccess && ( !$event || $event->UserID == $parent->access->UserID ) )
				{
					if( $event->ID > 0 )
					{
						$str .= '<button onclick="DeleteEvent(' . $event->ID . ')">' . i18n( 'i18n_Delete' ) . '</button>';
					}
					$str .= '<button onclick="SaveEvent()" type="button" class="btn_save">' . i18n( 'i18n_Save' ) . '</button>';
				}
				
				$str .= '<button onclick="CloseEvent()" type="button" class="btn_close">' . i18n( 'i18n_Close' ) . '</button>';
				
				$str .= '</div>';
				
				$str .= '<div class="clearboth" style="clear:both;float:none;"></div>';
				
				$str .= '</div>';
				
				break;
			
			// Extended mode has more options
			case 'extended2':
				
				$str .= '<div onclick="cancelBubble(event)" id="EventEditor" class="extended ext2" Date="' . $_POST['date'] . '" EventID="' . ( isset( $_POST['eid'] ) ? $_POST['eid'] : 0 ) . '" EventSlotID="' . ( isset( $_POST['sid'] ) ? $_POST['sid'] : 0 ) . '">';
				
				// Event Image Editor
				
				if( $_POST['eid'] > 0 )
				{
					$str .= '<div id="EventImage_' . $event->ID . '" class="image' . ( !$event->ImageID ? ' edit' : '' ) . '">';
					
					if( $event->ImageID > 0 )
					{
						$str .= '<div class="imagecontainer" style="background-image:url(\'' . ( BASE_URL . 'secure-files/images/' . ( $event->ImageUniqueID ? $event->ImageUniqueID : $event->ImageID ) . '/' ) . '\');background-repeat:no-repeat;background-position:center center;background-size:cover;width:100%;height:100%;max-width:' . $event->Width . 'px;max-height:' . $event->Height . 'px;"></div>';
					}
					
					$str .= '<div class="eventdate">';
					$str .= '<div class="month">' . i18n( 'i18n_' . $mode . date( 'F', strtotime( $event->DateStart ) ) ) . '</div>';
					$str .= '<div class="day">' . date( 'd', strtotime( $event->DateStart ) ) . '</div>';
					$str .= '</div>';
					
					if( $hasAccess && ( !$event || $event->UserID == $parent->access->UserID ) )
					{
						$str .= '<div class="fileupload" onclick="ge(\'FilesUploadBtn_' . $event->ID . '\').click()"><div></div></div>';
					}
					
					$str .= '</div>';
				}
				
				
				$str .= '<div class="field nr1"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Name' ) . '</td>';
				$str .= '<td class="value"><input id="EventName" type="text" onclick="SelectEventInput(this,\'events\',event)" onkeyup="SearchEvent(this,\'events\',event)" placeholder="' . i18n( 'i18n_EventName' ) . '" ' . ( $_POST['eid'] > 0 ? ( 'value="' . $event->Name . '"' . ( $_POST['date'] == date( 'Y-m-d', strtotime( $event->DateStart ) ) ? ( ' vid="' . $event->ID . '"' ) : '' ) ) : '' ) . '/></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr2"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Place' ) . '</td>';
				$str .= '<td class="value"><input id="EventPlace" type="text" onclick="SelectEventInput(this)" placeholder="' . i18n( 'i18n_EventPlace' ) . '" value="' . $event->Place . '"/></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr3"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Details' ) . '</td>';
				$str .= '<td class="value"><textarea id="EventDetails" placeholder="' . i18n( 'i18n_EventDetails' ) . '">' . $event->Details . '</textarea></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr4"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Type' ) . '</td>';
				$str .= '<td class="value"><select id="EventType">';
				$str .= '<option value=""' . ( isset( $event->Type ) && $event->Type == '' ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Other' ) . '</option>';
				$str .= '<option value="meeting"' . ( isset( $event->Type ) && $event->Type == 'meeting' ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Meeting' ) . '</option>';
				$str .= '<option value="notification"' . ( isset( $event->Type ) && $event->Type == 'notification' ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Notification' ) . '</option>';
				//$str .= '<option value="vacation"' . ( isset( $event->Type ) && $event->Type == 'vacation' ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Vacation' ) . '</option>';
				$str .= '</select></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr4"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Members' ) . '</td>';
				$str .= '<td class="value"><div class="customfield">';
				$str .= '<span id="FindMembers"></span>';
				$str .= '<input id="MemberSearch" type="text" name="search" placeholder="' . i18n( 'i18n_Who do you want to add to the event' ) . '?" onkeyup="findMembers( false, this )"/>';
				$str .= '</div><div id="ListFoundMembers"></div></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr5"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Start' ) . '</td>';
				$str .= '<td class="value">' . renderHtmlFields( 'datetimefield', 'text', array( 'EventStartDate', 'EventStart' ), ( $_POST['eid'] > 0 && $event->DateStart ? $event->DateStart : date( 'Y-m-d 08:00:00', strtotime( $_POST['date'] ) ) ), i18n( 'i18n_DateStart' ), '', '', 'onclick="OrderCalendar(this)"' ) . '</td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr6"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_End' ) . '</td>';
				$str .= '<td class="value">' . renderHtmlFields( 'datetimefield', 'text', array( 'EventEndDate', 'EventEnd' ), ( $_POST['eid'] > 0 && $event->DateEnd ? $event->DateEnd : date( 'Y-m-d 15:30:00', strtotime( $_POST['date'] ) ) ), i18n( 'i18n_DateEnd' ), '', '', 'onclick="OrderCalendar(this)"' ) . '</td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr7"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Slots' ) . '</td>';
				$str .= '<td class="value"><input id="EventSlots" type="text" onclick="SelectEventInput(this)" placeholder="' . i18n( 'i18n_EventSlots' ) . '" value="' . $slots . '"/></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="field nr8"><table><tr>';
				$str .= '<td class="label">' . i18n( 'i18n_Access' ) . '</td>';
				$str .= '<td class="value"><select id="EventAccess">';
				$str .= '<option value="0"' . ( isset( $event->Access ) && $event->Access == 0 ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Public' ) . '</option>';
				$str .= '<option value="1"' . ( isset( $event->Access ) && $event->Access == 1 ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Contacts' ) . '</option>';
				$str .= '<option value="2"' . ( isset( $event->Access ) && $event->Access == 2 ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Only Me' ) . '</option>';
				//$str .= '<option value="3"' . ( isset( $event->Access ) && $event->Access == 3 ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Custom' ) . '</option>';
				if( isset( $parent->access->IsAdmin ) )
				{
					$str .= '<option value="4"' . ( isset( $event->Access ) && $event->Access == 4 ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_Admin' ) . '</option>';
				}
				$str .= '</select></td>';
				$str .= '</tr></table></div>';
				
				$str .= '<div class="clearboth" style="clear:both;float:none;"></div>';
				
				$str .= '</div>';
				
				break;
			
			// Default mode only shows, fields needed
			default:
				
				$str .= '<div onclick="cancelBubble(event)" id="EditEvent" Date="' . $_POST['date'] . '" EventID="' . ( isset( $_POST['eid'] ) && $_POST['date'] == date( 'Y-m-d', strtotime( $event->DateStart ) ) ? $_POST['eid'] : 0 ) . '" EventSlotID="' . ( isset( $_POST['sid'] ) ? $_POST['sid'] : 0 ) . '">';
				$str .= '<div><input onclick="SelectEventInput(this,\'users\',event)" onkeyup="SearchEvent(this,\'users\',event)" id="EventAttendee" type="text" style="width:94%;" placeholder="' . i18n( 'i18n_EventAttendee' ) . '"/></div>';
				$str .= '<div><input onclick="SelectEventInput(this,\'roles\',event)" onkeyup="SearchEvent(this,\'roles\',event)" id="EventRole" type="text" style="width:94%;" placeholder="' . i18n( 'i18n_Role' ) . '"/></div>';
				$str .= '<div><input onclick="SelectEventInput(this,\'events\',event)" onkeyup="SearchEvent(this,\'events\',event)" id="EventName" type="text" style="width:94%" placeholder="' . i18n( 'i18n_EventName' ) . '" ' . ( $_POST['eid'] > 0 ? ( 'value="' . $event->Name . '"' . ( $_POST['date'] == date( 'Y-m-d', strtotime( $event->DateStart ) ) ? ( ' vid="' . $event->ID . '"' ) : '' ) ) : '' ) . '/>';
				$str .= '</div>';
				$str .= '<div><input onclick="SelectEventInput(this)" id="EventStart" type="text" style="width:37.5%;" placeholder="' . i18n( 'i18n_TimeStart' ) . '" ' . ( $_POST['eid'] > 0 && $_POST['date'] == date( 'Y-m-d', strtotime( $event->DateStart ) ) ? ( 'value="' . date( 'H:i', strtotime( $event->DateStart ) ) . '"' ) : 'value="08:00"' ) . '/>';
				$str .= '<span style="width:10%;"> - </span>';
				$str .= '<input onclick="SelectEventInput(this)" id="EventEnd" type="text" style="width:37.5%;" placeholder="' . i18n( 'i18n_TimeEnd' ) . '" ' . ( $_POST['eid'] > 0 && $_POST['date'] == date( 'Y-m-d', strtotime( $event->DateStart ) ) ? ( 'value="' . date( 'H:i', strtotime( $event->DateEnd ) ) . '"' ) : 'value="15:30"' ) . '/></div>';
				$str .= '</div>';
				
				break;
		}
		
		if( $_REQUEST['function'] == 'eventedit' )
		{
			die( 'ok<!--separate-->' . $str );
		}
	}
}

if( $_REQUEST['function'] == 'eventedit' )
{
	die( 'fail' );
}

?>
