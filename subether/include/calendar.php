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

if ( !function_exists ( 'renderCalendarYear' ) )
{
	function renderCalendarYear( $date, $view = false, $obj = false, $access = false, $mode = false )
	{
		$year = date( 'Y', strtotime( $date ) );
		$month = date( 'm', strtotime( $date ) );
		$day = date( 'd', strtotime( $date ) );
		$week = date( 'W', strtotime( $date ) );
		
		$mode = ( !strstr( $mode, 'web' ) && $mode ? 's_' : 'd_' );
		
		$o = new CalendarYear( $date = ( $year . '-' . $month . '-' . $day ) );
		
		if ( count ( $obj ) && is_array ( $obj ) )
		{
			$o->ImportEvents ( $obj, 'DateStart', 'DateEnd' );
		}
		
		//die( print_r( $o,1 ) . ' --' );
		
		$str = '<div class="CalendarYear">';
		foreach( $o->months as $m )
		{
			$nstr = '<tr class="nv"><th colspan="8">';
			$nstr .= '<div class="nav">';
			$nstr .= '<span class="Left">';
			$nstr .= ' <a href="javascript:void(0)" class="prevYear" title="' . i18n ( 'i18n_Previous_Year' ) . '" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm', strtotime($m->current) ), date( 'd', strtotime($m->current) ), date( 'Y', strtotime($m->current) )-1 ) . ')"> < </a> ';
			//$nstr .= '<a href="javascript:void(0)" class="prevYear" title="' . i18n ( 'i18n_Previous_Year' ) . '" onclick="changeDate( \'' . floor( $m->month ) . '\', \'' . floor( $m->year-1 ) . '\' )"> << </a>';
			//$nstr .= '<a href="javascript:void(0)" class="prevMonth" title="' . i18n ( 'i18n_Previous_Month' ) . '" onclick="changeDate( \'' . floor( $m->month-1 ) . '\', \'' . $m->year . '\' )"> < </a>';
			$nstr .= '</span>';
			$nstr .= '<span class="Center" onclick="ChangeCalendar(\'month\',' . strtotime($m->year.'-'.$m->month.'-'.$m->day) . ')">'/*<a href="javascript:void(0)" title="' . i18n ( 'i18n_Todays_Date' ) . '" onclick="changeDate( \'' . date( 'n' ) . '\', \'' . date( 'Y' ) . '\' )">'*/ . i18n ( 'i18n_' . $mode . date ( 'F', strtotime ( $m->year . '-' . $m->month ) ) ) . ', ' . $m->year . /*'</a>*/'</span>';
			$nstr .= '<span class="Right">';
			//$nstr .= '<a href="javascript:void(0)" class="nextMonth" title="' . i18n ( 'i18n_Next_Month' ) . '" onclick="changeDate( \'' . floor( $m->month+1 ) . '\', \'' . $m->year . '\' )"> > </a>';
			//$nstr .= '<a href="javascript:void(0)" class="nextYear" title="' . i18n ( 'i18n_Next_Year' ) . '" onclick="changeDate( \'' . floor( $m->month ) . '\', \'' . floor( $m->year+1 ) . '\' )"> >> </a>';
			$nstr .= ' <a href="javascript:void(0)" class="nextYear" title="' . i18n ( 'i18n_Next_Year' ) . '" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm', strtotime($m->current) ), date( 'd', strtotime($m->current) ), date( 'Y', strtotime($m->current) )+1 ) . ')"> > </a> ';
			$nstr .= '</span>';
			$nstr .= '</div>';
			$nstr .= '</th></tr>';
			
			$i = 0; $sw = 2; $s = 0; $y = 0;
			$str .= '<div style="float: left;" class="Month"><table><tbody>' . ( $view || 1==1 ? $nstr : '' );
			foreach( $m->weeks as $key=>$w )
			{	
				$x = 0;
				$ch = 1 + $s;
				$sw = ( $sw == '1' ? '2' : '1' );
				$hstr = '<tr class="hd">';
				$dstr = '<tr class="sw' . $sw . '">';
				foreach( $w->days as $k=>$d )
				{
					$ch = ( $ch + 1 ) % 2; $evnt = '';
					
					$ct = strtotime( $d->date );
					
					$cw = new DateTime();
					$cw->setISODate( date( 'Y', $ct ), date( 'W', $ct ), 1 );
					
					if( $d->events )
					{
						$iii = 0;
						
						foreach( $d->events as $e )
						{						
							//$evnt .= '<span title="' . $e->Name . ' | ' . $e->DateStart . ' - ' . $e->DateEnd . '"></span>';
							
							$evnt .= '<span ' . ( $e->Type ? ( 'class="' . $e->Type . '" ' ) : '' ) . 'onmouseover="tooltips(this,\'open\')" onmouseout="tooltips(this,\'close\')" onclick="ViewEvent(' . strtotime( $e->DateStart ) . ')">';
							$evnt .= '<div class="tooltips">';
							$evnt .= '<div class="inner">';
							if( isset( $e->ImageData->ImgUrl ) )
							{
								$evnt .= '<div class="image" style="background-image:url(\'' . $e->ImageData->ImgUrl . '\');background-repeat:no-repeat;background-position:center center;background-size:cover;">';
								$evnt .= '<div class="eventdate">';
								$evnt .= '<div class="month">' . i18n( 'i18n_' . $mode . date( 'F', strtotime( $e->DateStart ) ) ) . '</div>';
								$evnt .= '<div class="day">' . date( 'd', strtotime( $e->DateStart ) ) . '</div>';
								$evnt .= '</div>';
								$evnt .= '</div>';
							}
							$evnt .= '<div class="text">';
							$evnt .= '<h3>' . $e->Name . '</h3>';
							
							if( $e->Component == 'orders' && $e->Hours )
							{
								$evnt .= '<div class="timedate">' . date( 'l, F j', strtotime( $e->DateStart ) ) . ' (' . $e->Hours . 'hours)</div>';
							}
							else
							{
								$evnt .= '<div class="timedate">' . date( 'l, F j', strtotime( $e->DateStart ) ) . ' at ' . date( 'H:i', strtotime( $e->DateStart ) ) . '</div>';
							}
							
							$evnt .= '<div class="place">' . $e->Place . '</div>';
							$evnt .= '</div>';
							$evnt .= '<div class="clearboth" style="clear:both"></div>';
							$evnt .= '</div>';
							$evnt .= '</div>';
							$evnt .= '</span>';
							
							$iii++;
						}
					}
					
					if( $x == 0 )
					{
						$hstr .= '<th><div class="Week"><div class="heading">' . i18n ( 'i18n_' . ( $view || 1==1 ? 's_' : $mode ) . 'Week' ) . '</div></div></th>';
						$dstr .= '<td><div class="Week" onclick="ChangeCalendar(\'week\',' . strtotime( $cw->format( 'Y-m-d' ) ) . ')"><div class="number">' . $d->week . '</div></div></td>';
					}
					
					if( $i == 0 )
					{
						$hstr .= '<th><div class="' . $d->name . '"><div class="heading">' . i18n ( 'i18n_' . ( $view || 1==1 ? 's_' : $mode ) . $d->name ) . '</div></div></th>';
					}
					
					if( date( 'Y-m-d', strtotime( $d->date ) ) == $date ) $sel = ' Selected'; else $sel = '';
					
					//$dstr .= ( $d->month == $m->month ? '<td class="ch' . ( $ch + 1 ) . '"><div class="' . $d->name . $sel . '" onclick="SetEventDate(' . $ct . ')"><div class="number">' . $d->day . '</div><div class="events">' . $evnt . '<div style="clear: both;"></div></div></div></td>' : '<td class="ch' . ( $ch + 1 ) . '"><div class="Space" onclick="SetEventDate(' . $ct . ')"><div class="number">' . $d->day . '</div></div></td>' );
					$dstr .= ( $d->month == $m->month ? '<td class="ch' . ( $ch + 1 ) . '"><div class="' . $d->name . $sel . ' Day" onclick="ChangeCalendar(\'month\',' . $ct . ')"><div class="number">' . $d->day . '</div>' . ( $evnt && $iii > 1 ? '<div class="toolnum">' . $iii . '</div>' : '' ) . '<div class="events">' . $evnt . '<div style="clear: both;"></div></div></div></td>' : '<td class="ch' . ( $ch + 1 ) . '"><div class="Space"><div class="number">' . $d->day . '</div></div></td>' );
					
					$x++;
				}
				if( $i == 0 && $hstr ) $str .= $hstr . '</tr>';
				if( $dstr ) $str .= $dstr . '</tr>';
				$s = ( $s + 1 ) % 2;
				$i++; $y++;
			}
			$str .= '</tbody></table></div>';
		}
		return $str .= '<div style="clear: both;"></div></div>';
	}
}

if ( !function_exists ( 'renderCalendarMonth' ) )
{
	function renderCalendarMonth( $date, $view = false, $obj = false, $access = false, $mode = false )
	{
		global $database, $webuser;
		
		$year = date( 'Y', strtotime( $date ) );
		$month = date( 'm', strtotime( $date ) );
		$day = date( 'd', strtotime( $date ) );
		$week = date( 'W', strtotime( $date ) );
		
		$mode = ( !strstr( $mode, 'web' ) && $mode ? 's_' : 'd_' );
		
		$hasAccess = '';
		
		if( $access && $access->Read && $access->Write && $access->Delete && $access->Admin )
		{
			$hasAccess = true;
		}
		
		$nstr  = '<tr class="nv"><th colspan="8">';
		$nstr .= '<div class="nav">';
		$nstr .= '<span class="Left">';
		$nstr .= '<a href="javascript:void(0)" class="prevYear" title="' . i18n ( 'i18n_Previous_Year' ) . '" onclick="changeDate( \'' . floor( $month ) . '\', \'' . floor( $year-1 ) . '\' )"> << </a>';
		//$nstr .= '<a href="javascript:void(0)" class="prevMonth" title="' . i18n ( 'i18n_Previous_Month' ) . '" onclick="changeDate( \'' . floor( $month-1 ) . '\', \'' . $year . '\' )"> < </a>';
		$nstr .= ' <a href="javascript:void(0)" class="prevMonth" title="' . i18n ( 'i18n_Previous_Month' ) . '" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm', strtotime($date) )-1, date( 'd', strtotime($date) ), date( 'Y', strtotime($date) ) ) . ')"> < </a> ';
		$nstr .= '</span>';
		$nstr .= '<span class="Center" onclick="ChangeCalendar(\'month\',' . strtotime(date('Y-m-d')) . ')">'/*<a href="javascript:void(0)" title="' . i18n ( 'i18n_Todays_Date' ) . '" onclick="changeDate( \'' . date( 'n' ) . '\', \'' . date( 'Y' ) . '\' )">'*/ . i18n ( 'i18n_' . $mode . date ( 'F', strtotime ( $year . '-' . $month ) ) ) . ', ' . $year . /*'</a>*/'</span>';
		$nstr .= '<span class="Right">';
		$nstr .= ' <a href="javascript:void(0)" class="nextMonth" title="' . i18n ( 'i18n_Next_Month' ) . '" onclick="SetEventDate(' . mktime( 0, 0, 0, date( 'm', strtotime($date) )+1, date( 'd', strtotime($date) ), date( 'Y', strtotime($date) ) ) . ')"> > </a> ';
		//$nstr .= '<a href="javascript:void(0)" class="nextMonth" title="' . i18n ( 'i18n_Next_Month' ) . '" onclick="changeDate( \'' . floor( $month+1 ) . '\', \'' . $year . '\' )"> > </a>';
		$nstr .= '<a href="javascript:void(0)" class="nextYear" title="' . i18n ( 'i18n_Next_Year' ) . '" onclick="changeDate( \'' . floor( $month ) . '\', \'' . floor( $year+1 ) . '\' )"> >> </a> ';
		$nstr .= '</span>';
		$nstr .= '</div>';
		$nstr .= '</th></tr>';
		
		$o = new CalendarMonth( $date = ( $year . '-' . $month . '-' . $day ) );
		
		if ( count ( $obj ) && is_array ( $obj ) )
		{
			$o->ImportEvents ( $obj, 'DateStart', 'DateEnd' );
		}
		
		//die( print_r( $o,1 ) . ' -- ' . print_r( $obj,1 ) );
		
		
		
		$i = 0; $sw = 2; $s = 0; $y = 0; $evt = '';
		$str = '<table class="CalendarMonth"><tbody>' . ( $view || 1==1 ? $nstr : '' );
		foreach( $o->weeks as $key=>$w )
		{	
			$x = 0;
			$ch = 1 + $s;
			$sw = ( $sw == '1' ? '2' : '1' );
			$hstr = '<tr class="hd">';
			$dstr = '<tr class="sw' . $sw . '">';
			foreach( $w->days as $k=>$d )
			{
				$ch = ( $ch + 1 ) % 2; $evnt = '';
				
				$ct = strtotime( $d->date );
				
				$cw = new DateTime();
				$cw->setISODate( date( 'Y', $ct ), date( 'W', $ct ), 1 );
				
				if( $d->events )
				{
					$ids = array(); $img = array();
					
					foreach( $d->events as $e )
					{
						$ids[$e->ID] = $e->ID;
						$img[$e->ImageID] = $e->ImageID;
					}
					
					if( $ids && $hrs = $database->fetchObjectRows( '
						SELECT 
							h.* 
						FROM 
							SBookHours h 
						WHERE 
								h.ProjectID IN ( ' . implode( ',', $ids ) . ' ) 
							AND h.IsDeleted = "0"
							AND h.DateStart >= \'' . date( 'Y-m-d 00:00:00.000000', strtotime( $date ) ) . '\' 
							AND h.DateEnd <= \'' . date( 'Y-m-d 23:59:59.000000', strtotime( $date ) ) . '\' 
						ORDER BY 
							h.DateStart ASC 
					', false, 'include/calendar.php' ) )
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
								$hours[$hr->ProjectID]['Hours'] = ( isset( $hours[$hr->ProjectID]['Hours'] ) ? ( $hours[$hr->ProjectID]['Hours'] + $hr->Hours ) : $hr->Hours );
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
					
					$iii = 0;
					
					foreach( $d->events as $e )
					{
						//die( print_r( $e,1 ) . ' --' );
						//$evnt .= '<span title="' . $e->Name . ' | ' . $e->DateStart . ' - ' . $e->DateEnd . '" onclick="alert(\'patience :)\')">';
						$evnt .= '<span ' . ( $e->Type ? ( 'class="' . $e->Type . '" ' ) : '' ) . 'onmouseover="tooltips(this,\'open\')" onmouseout="tooltips(this,\'close\')" onclick="ViewEvent(\'' . $e->ID . '\',\'' . $e->CategoryID . '\',\'' . strtotime( $e->DateStart ) . '\')">';
						$evnt .= '<div class="tooltips">';
						$evnt .= '<div class="inner">';
						if( isset( $e->ImageData->ImgUrl ) )
						{
							$evnt .= '<div class="image" style="background-image:url(\'' . $e->ImageData->ImgUrl . '\');background-repeat:no-repeat;background-position:center center;background-size:cover;">';
							$evnt .= '<div class="eventdate">';
							$evnt .= '<div class="month">' . i18n( 'i18n_' . $mode . date( 'F', strtotime( $e->DateStart ) ) ) . '</div>';
							$evnt .= '<div class="day">' . date( 'd', strtotime( $e->DateStart ) ) . '</div>';
							$evnt .= '</div>';
							$evnt .= '</div>';
						}
						$evnt .= '<div class="text">';
						$evnt .= '<h3>' . $e->Name . '</h3>';
						
						if( $e->Component == 'orders' && $e->Hours )
						{
							$evnt .= '<div class="timedate">' . date( 'l, F j', strtotime( $e->DateStart ) ) . ' (' . $e->Hours . 'hours)</div>';
						}
						else
						{
							$evnt .= '<div class="timedate">' . date( 'l, F j', strtotime( $e->DateStart ) ) . ' at ' . date( 'H:i', strtotime( $e->DateStart ) ) . '</div>';
						}
						
						$evnt .= '<div class="place">' . $e->Place . '</div>';
						$evnt .= '</div>';
						$evnt .= '<div class="clearboth" style="clear:both"></div>';
						$evnt .= '</div>';
						$evnt .= '</div>';
						$evnt .= '</span>';
						
						$iii++;
					}
				}
				
				if( date( 'Y-m-d', strtotime( $d->date ) ) == $date )
				{
					$evt = $d->events;
				}
				
				if( $x == 0 )
				{	
					$hstr .= '<th><div class="Week"><div class="number">' . i18n ( 'i18n_' . ( $view ? 's_' : $mode ) . 'Week' ) . '</div></div></th>';
					$dstr .= '<td><div class="Week" onclick="ChangeCalendar(\'week\',' . strtotime( $cw->format( 'Y-m-d' ) ) . ')"><div class="number">' . $d->week . '</div></div></td>';
				}
				
				if( $i == 0 )
				{
					$hstr .= '<th><div class="' . $d->name . '"><div class="number">' . i18n ( 'i18n_' . ( $view ? 's_' : $mode ) . $d->name ) . '</div></div></th>';
				}
				
				if( date( 'Y-m-d', strtotime( $d->date ) ) == $date ) $sel = ' Selected'; else $sel = '';
				
				$create = ( $hasAccess && 1!=1 ? '<div class="icon" onclick="EditEvent(this.parentNode.parentNode,event,\'' . $d->date . '\',false,false,\'extended\');cancelBubble(event);"></div>' : '' );
				
				$dstr .= ( $d->month == $o->month ? '<td class="ch' . ( $ch + 1 ) . '"><div class="' . $d->name . $sel . ' Day" onclick="SetEventDate(' . $ct . ');cancelBubble(event);"><div class="create">' . $create . '</div><div class="number">' . $d->day . '</div><div class="events">' . $evnt . '</div>' . ( $evnt && $iii > 1 ? '<div class="toolnum">' . $iii . '</div>' : '' ) . '</div></td>' : '<td class="ch' . ( $ch + 1 ) . '"><div class="Space" onclick="SetEventDate(' . $ct . ')"><div class="number">' . $d->day . '</div></div></td>' );
				
				$x++;
			}
			if( $i == 0 && $hstr ) $str .= $hstr . '</tr>';
			if( $dstr ) $str .= $dstr . '</tr>';
			$s = ( $s + 1 ) % 2;
			$i++; $y++;
		}
		$str .= '</tbody></table>';
		
		if ( !$view )
		{
			$str .= '<div class="EventList">';
			$str .= '<div class="heading">Events</div>';
			$str .= '<div class="events">';
			
			// --- Add new event --- //
			
			if( $hasAccess )
			{
				$str .= '<div class="event editor closed">';
				
				$str .= '<div class="description" onclick="EditEvent(ge(\'EventEditorWrapper\'),event,\'' . $date . '\',false,false,\'extended\');cancelBubble(event);this.scrollIntoView();">' . i18n( 'i18n_Add event' ) . '</div>';
				$str .= '<div id="EventEditorWrapper"></div>';
				
				$str .= '</div>';
			}
			
			// --- List events --- //
			
			if( $evt )
			{
				$ii = 1;
				
				foreach( $evt as $e )
				{
					$str .= '<div class="event closed">';
					
					// --- Image ----------------------------------------------------------------------------------------------
					
					$str .= '<div id="EventImage_' . $e->ID . '" class="image' . ( !$e->ImageData ? ' edit' : '' ) . '">';
					
					if( $e->ImageData && isset( $e->ImageData->ImgUrl ) )
					{
						$str .= '<div class="imagecontainer" style="background-image:url(\'' . $e->ImageData->ImgUrl . '\');background-repeat:no-repeat;background-position:center center;background-size:cover;width:100%;height:100%;max-width:' . $e->ImageData->Width . 'px;max-height:' . $e->ImageData->Height . 'px;"></div>';
					}
					
					$str .= '<div class="eventdate">';
					$str .= '<div class="month">' . i18n( 'i18n_' . $mode . date( 'F', strtotime( $e->DateStart ) ) ) . '</div>';
					$str .= '<div class="day">' . date( 'd', strtotime( $e->DateStart ) ) . '</div>';
					$str .= '</div>';
					
					if( $hasAccess && ( $access->UserID == $e->UserID ) )
					{
						$str .= '<div class="fileupload" onclick="ge(\'FilesUploadBtn_' . $e->ID . '\').click()"><div></div></div>';
					}
					
					$str .= '</div>';
					
					$str .= '<div class="text">';
					$str .= '<h3>' . $e->Name . '</h3>';
					
					if( $e->Component == 'orders' && $e->Hours )
					{
						$str .= '<div class="timedate">' . date( 'l, F j', strtotime( $e->DateStart ) ) . ' (' . $e->Hours . 'hours)</div>';
					}
					else
					{
						$str .= '<div class="timedate">' . date( 'l, F j', strtotime( $e->DateStart ) ) . ' at ' . date( 'H:i', strtotime( $e->DateStart ) ) . '</div>';
					}
					
					$str .= '<div class="place">' . $e->Place . '</div>';
					$str .= '</div>';
					
					// --- Buttons --------------------------------------------------------------------------------------------
					
					$str .= '<div class="buttons">';
					
					if( isset( $hours[$e->ID]['Available'] ) && !isset( $hours[$e->ID]['Mine'] ) )
					{
						$str .= '<button onclick="SignupEvent(\'' . $hours[$e->ID]['HourID'] . '\',this)">Sign up</button>';
					}
					else if( isset( $hours[$e->ID]['Pending'] ) )
					{
						$str .= '<span><button onclick="SignupEvent(\'' . $hours[$e->ID]['HourID'] . '\',this)">Accept</button>';
						$str .= '<button onclick="SignoffEvent(\'' . $hours[$e->ID]['HourID'] . '\',this)">Decline</button></span>';
					}
					else if( isset( $hours[$e->ID]['Mine'] ) )
					{
						$str .= '<button onclick="SignoffEvent(\'' . $hours[$e->ID]['HourID'] . '\',this)">Sign off</button>';
					}
					
					//$str .= '<button>Sign On Event</button>';
					//die( print_r( $e,1 ) . ' --' );
					$str .= '<button onclick="EditEvent(ge(\'EventEdit_' . $ii . '\'),event,\'' . $date . '\',\'' . $e->ID . '\',false,\'extended\')">Edit</button>';
					$str .= '<button onclick="ViewEvent(\'' . $e->ID . '\',\'' . $e->CategoryID . '\',\'' . strtotime( $e->DateStart ) . '\')">View</button>';
					//$str .= '<button onclick="DeleteEvent(' . $e->ID . ')">Delete</button>';
					$str .= '</div>';
					
					// --- Upload ----------------------------------------------------------------------------------------------
					
					if( $hasAccess && ( $access->UserID == $e->UserID ) )
					{
						$str .= '<div class="upload_btn">';
						$str .= '<div><span>Upload Image</span></div>';
						$str .= '<form method="post" target="fileIframe" name="FilesUpload_' . $e->ID . '" enctype="multipart/form-data" action="' . $parent->route . '?component=library&action=uploadfile">';
						$str .= '<input type="file" class="file_upload_btn" id="FilesUploadBtn_' . $e->ID . '" name="events" onchange="fileselect( this, \'FilesUpload_' . $e->ID . '\' )"/>';
						$str .= '<input type="hidden" id="EventID_' . $e->ID . '" name="eventid" value="' . $e->ID . '">';
						$str .= '</form>';
						$str .= '<script>setOpacity ( ge(\'FilesUploadBtn_' . $e->ID . '\' ), 0 );</script>';
						$str .= '</div>';
					}
					
					$str .= '<div id="EventEdit_' . $ii . '" class="inner"></div>';
					
					$str .= '<div class="clearboth" style="clear:both"></div>';
					$str .= '</div>';
					
					$ii++;
				}
			}
			/*else
			{
				$str .= '<div>No events for this day.</div>';
			}*/
			
			$str .= '<div class="clearboth" style="clear:both"></div>';
			
			$str .= '</div>';
			$str .= '</div>';
		}
		
		return $str;
	}
}

if ( !function_exists ( 'renderCalendarWeek' ) )
{
	function renderCalendarWeek( $date, $view = false, $obj = false, $access = false, $mode = false )
	{
		global $database, $webuser;
		
		$year = date( 'Y', strtotime( $date ) );
		$month = date( 'm', strtotime( $date ) );
		$day = date( 'd', strtotime( $date ) );
		$week = date( 'W', strtotime( $date ) );
		
		$mode = ( !strstr( $mode, 'web' ) && $mode ? 's_' : 'd_' );
		
		$hasAccess = '';
		
		if( $access && $access->Read && $access->Write && $access->Delete && $access->Admin )
		{
			$hasAccess = true;
		}
		
		$nstr = '<tr class="nv"><th colspan="8">';
		$nstr .= '<div class="nav">';
		//$nstr .= '<span class="Left"><a href="javascript:void(0)" class="prevYear" title="' . i18n ( 'i18n_Previous_Year' ) . '" onclick="changeDate( \'' . floor( $month ) . '\', \'' . floor( $year-1 ) . '\' )"> << </a>';
		//$nstr .= '<a href="javascript:void(0)" class="prevMonth" title="' . i18n ( 'i18n_Previous_Month' ) . '" onclick="changeDate( \'' . floor( $month-1 ) . '\', \'' . $year . '\' )"> < </a></span>';
		$nstr .= '<span class="Center">'/*<a href="javascript:void(0)" title="' . i18n ( 'i18n_Todays_Date' ) . '" onclick="changeDate( \'' . date( 'n' ) . '\', \'' . date( 'Y' ) . '\' )">'*/ . i18n ( 'i18n_' . $mode . date ( 'F', strtotime ( $year . '-' . $month ) ) ) . ', ' . $year . /*'</a>*/'</span>';
		//$nstr .= '<span class="Right"><a href="javascript:void(0)" class="nextMonth" title="' . i18n ( 'i18n_Next_Month' ) . '" onclick="changeDate( \'' . floor( $month+1 ) . '\', \'' . $year . '\' )"> > </a>';
		//$nstr .= '<a href="javascript:void(0)" class="nextYear" title="' . i18n ( 'i18n_Next_Year' ) . '" onclick="changeDate( \'' . floor( $month ) . '\', \'' . floor( $year+1 ) . '\' )"> >> </a> </span>';
		$nstr .= '</div>';
		$nstr .= '</th></tr>';
		
		$o = new CalendarWeek( $date = ( $year . '-' . $month . '-' . $day ), $week );
		
		if ( count ( $obj ) && is_array ( $obj ) )
		{
			$o->ImportEvents ( $obj, 'DateStart', 'DateEnd' );
		}
		
		$events = array();
		//die( print_r( $o->days,1 ) . ' -- ' . print_r( $obj,1 ) );
		foreach( $o->days as $w )
		{
			if( $w->events )
			{
				foreach( $w->events as $e )
				{
					if( !$events[$e->Name] )
					{
						$events[$e->Name] = array();
						$events[$e->Name]['rows'] = false;
						$events[$e->Name]['name'] = $e->Name;
						$events[$e->Name]['id'] = $e->ID;
						$events[$e->Name]['owner'] = $e->UserID;
					}
					if( !$events[$e->Name][$w->date] )
					{
						$events[$e->Name][$w->date] = array();
					}
					if( $e->HourSlots/*$hours = $database->fetchObjectRows( '
						SELECT 
							* 
						FROM 
							SBookHours 
						WHERE
								ProjectID = \'' . $e->ID . '\' 
							AND IsDeleted = "0" 
						ORDER BY 
							DateStart ASC 
					' )*/ )
					{
						//$e->HourSlots = $hours;
						$events[$e->Name]['rows'] = $events[$e->Name]['rows'] > count( $e->HourSlots ) ? $events[$e->Name]['rows'] : count( $e->HourSlots );
					}
					$events[$e->Name][$w->date] = $e;
				}
			}
		}
		
		if( $hasAccess )
		{
			$events[0] = array( 'rows'=>0 );
		}
		
		
		$str = ''; $r = 0;
		$head = '<table class="CalendarWeek"><tbody>';
		foreach( $events as $eid=>$evt )
		{
			if( isset( $evt['rows'] ) )
			{
				$i = 0; $sw = 2; $s = 0; $y = 0;
				$str .= '<div class="heading">' . ( isset( $evt['name'] ) ? $evt['name'] : '&nbsp;' ) . '</div>';
				$str .= '<table class="CalendarWeek"><tbody>';
				
				for( $slot = 0; $slot <= ( ( ( $hasAccess && ( $evt['owner'] == $access->UserID || $access->IsSystemAdmin ) ) || $evt['rows'] == 0 ) ? $evt['rows'] : ( $evt['rows'] - 1 ) ); $slot++ )
				{
					$x = 0;
					$ch = 1 + $s;
					$sw = ( $sw == '1' ? '2' : '1' );
					$hstr = ( $view || 1==1 ? $nstr : '' ) . '<tr class="hd">';
					$dstr = '<tr class="sw' . $sw . '">';
					foreach( $o->days as $k=>$d )
					{
						$ch = ( $ch + 1 ) % 2;
						
						$event = isset( $evt[$d->date] ) ? $evt[$d->date] : false;
						
						if( $x == 0 )
						{
							$hstr .= '<th><div class="Week">' . i18n ( 'i18n_' . $mode . 'Week' ) . ' ' . $o->week . '</div></th>';
							$dstr .= '<td><div class="Hour">' . str_pad ( $slot, 2, '0', STR_PAD_LEFT ) . '</div></td>';
						}
						
						if( $i == 0 && $r == 0 )
						{
							$hstr .= '<th><div class="' . $d->name . '">' . i18n ( 'i18n_' . $mode . $d->name ) . ' ' . $d->day . '</div></th>';
						}
						
						if( date( 'Y-m-d', strtotime( $d->date ) ) == $date ) $sel = ' Selected'; else $sel = '';
						
						$evtslot = ( isset( $evt[$d->date]->HourSlots[$slot] ) ? $evt[$d->date]->HourSlots[$slot] : false );
						//if ( $d->date == '2016-10-30' ) die( print_r( $evtslot,1 ) . ' --' );
						if( $evtslot && $evtslot->DateStart >= date( 'Y-m-d 00:00:00.000000', strtotime( $d->date ) ) && $evtslot->DateEnd <= date( 'Y-m-d 23:59:59.000000', strtotime( $d->date ) ) )
						{
							//die( print_r( $access,1 ) . ' --' );
							$onclick = ( ( $hasAccess && ( $evt['owner'] == $access->UserID || $access->IsSystemAdmin ) ) ? 'EditEvent(this.parentNode,event,\'' . $d->date . '\',\'' . $event->ID . '\',\'' . $evtslot->ID . '\')' : false );
							$dstr .= '<td class="ch' . ( $ch + 1 ) . '">';
							$dstr .= '<div id="' . $d->date . '_' . $event->ID . '_' . $evtslot->ID . '" class="' . $d->name . $sel . ( $evtslot->IsAccepted ? ' Accepted' : ( !$evtslot->UserID ? ' Available' : ( $evtslot->UserID && !$evtslot->IsAccepted ? ' Pending' : '' ) ) ) . ' Event">';
							//$dstr .= '<div onclick="EditEvent(this,event)">';
							$dstr .= '<div ' . ( $onclick ? ( 'onclick="' . $onclick . '"' ) : '' ) . '>';
							$dstr .= '<div><span>' . ( GetUserDisplayname( $evtslot->UserID ) ? dotTrim( GetUserDisplayname( $evtslot->UserID ), 14 ) : 'Free slot' ) . '</span></div>';
							$dstr .= '<div><span>' . $event->Name . '</span></div>';
							$dstr .= '<div>';
							$dstr .= '<span>' . date( 'H:i', strtotime( $evtslot->DateStart ) ) . '</span>';
							$dstr .= '<span> - </span>';
							$dstr .= '<span>' . date( 'H:i', strtotime( $evtslot->DateEnd ) ) . '</span>';
							$dstr .= '</div>';
							$dstr .= '</div></div></td>';
						}
						else
						{
							$onclick = ( $hasAccess && ( ( $evt['owner'] == $access->UserID || $access->IsSystemAdmin ) || ( !$o->events && !$event ) || $evt['rows'] == 0 ) ? 'EditEvent(this.parentNode,event,\'' . $d->date . '\'' . ( isset( $evt['id'] ) ? ( ',\'' . ( $event ? $event->ID : $evt['id'] ) . '\'' ) : '' ) . ')' : false );
							$dstr .= '<td class="ch' . ( $ch + 1 ) . '">';
							$dstr .= '<div id="' . $d->date . '_' . ( isset( $evt['id'] ) ? ( $event ? $event->ID : $evt['id'] ) : '0' ) . '_0" class="' . $d->name . $sel . '">';
							//$dstr .= '<div onclick="CreateEvent(this.parentNode,event)">Create</div>';
							$dstr .= '<div ' . ( $onclick ? 'onclick="' . $onclick . '"' : '' ) . '>' . ( $hasAccess && ( ( $evt['owner'] == $access->UserID || $access->IsSystemAdmin ) || ( !$o->events && !$event ) || $evt['rows'] == 0 ) ? 'Create' : '' ) . '</div>';
							$dstr .= '</div></td>';
						}
						/*else
						{
							$dstr .= '<td class="ch' . ( $ch + 1 ) . '"><div class="Space">&nbsp;</div></td>';
						}*/
						
						$x++;
					}
					if( $i == 0 && $r == 0 && $hstr ) $head .= $hstr . '</tr>';
					if( $dstr ) $str .= $dstr . '</tr>';
					$s = ( $s + 1 ) % 2;
					$i++; $y++;
				}
				$r++;
			}
			$str .= '</tbody></table>';
		}
		$head .= '</tbody></table>';
		return $head . $str;
	}
}

if ( !function_exists( 'renderCalendarDay' ) )
{
	function renderCalendarDay( $date, $view = false, $obj = false, $access = false, $mode = false )
	{
		global $database, $webuser;
		
		$year = date( 'Y', strtotime( $date ) );
		$month = date( 'm', strtotime( $date ) );
		$day = date( 'd', strtotime( $date ) );
		$week = date( 'W', strtotime( $date ) );
		
		$mode = ( !strstr( $mode, 'web' ) && $mode ? 's_' : 'd_' );
		
		$str = ''; $hasAccess = '';
		
		if( $access && $access->Read && $access->Write && $access->Delete && $access->Admin )
		{
			$hasAccess = true;
		}
		
		$o = new CalendarDay( $date = ( $year . '-' . $month . '-' . $day ), $week );
		
		if ( count ( $obj ) && is_array ( $obj ) )
		{
			$o->ImportEvents ( $obj, 'DateStart', 'DateEnd' );
		}
		
		if( $o->events )
		{
			$ids = array(); $img = array();
			
			foreach( $o->events as $e )
			{
				if ( $e->ID > 0 )
				{
					$ids[$e->ID] = $e->ID;
				}
				
				if ( $e->ImageID > 0 )
				{
					$img[$e->ImageID] = $e->ImageID;
				}
			}
			
			if( $img && $im = $database->fetchObjectRows( $q = '
				SELECT
					f.DiskPath, i.Filename, i.ID, i.UniqueID, i.Width, i.Height 
				FROM
					Folder f, Image i
				WHERE
					i.ID IN (' . implode( ',', $img ) . ') AND f.ID = i.ImageFolder
				ORDER BY
					ID ASC
			', false, 'include/calendar.php' ) )
			{
				$imgs = array();
				
				foreach( $im as $i )
				{
					$obj = new stdClass();
					$obj->ID = $i->ID;
					$obj->Width = $i->Width;
					$obj->Height = $i->Height;
					$obj->Filename = $i->Filename;
					$obj->DiskPath = ( $i->DiskPath != '' ? $i->DiskPath : ( BASE_URL . 'upload/images-master/' ) );
					$obj->ImgUrl = ( $obj->DiskPath && $obj->Filename ? ( $obj->DiskPath . $obj->Filename ) : false );
					if ( $i->Filename )
					{
						$obj->DiskPath = ( BASE_URL . 'secure-files/images/' . ( $i->UniqueID ? $i->UniqueID : $i->ID ) . '/' );
						$obj->ImgUrl = $obj->DiskPath;
					}
					
					$imgs[$i->ID] = $obj;
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
			', false, 'include/calendar.php' ) )
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
			
			$str .= '<div class="CalendarDay">';
			foreach( $o->events as $e )
			{
				$e->DateStart = ( isset( $hours[$e->ID]['DateStart'] ) ? $hours[$e->ID]['DateStart'] : $e->DateStart );
				$e->DateEnd = ( isset( $hours[$e->ID]['DateEnd'] ) ? $hours[$e->ID]['DateEnd'] : $e->DateEnd );
				$e->Hours = ( isset( $hours[$e->ID] ) ? $hours[$e->ID] : false );
				$e->ImageData = ( $e->ImageID > 0 && isset( $imgs[$e->ImageID] ) ? $imgs[$e->ImageID] : false );
				
				//$onclick = ( $hasAccess && ( $access->UserID == $e->UserID ) ? 'EditEvent(this.parentNode.parentNode,event,\'' . date( 'Y-m-d', strtotime( $e->DateStart ) ) . '\',\'' . $e->ID . '\',false,\'extended\')' : false );
				$onclick = false;
				
				$str .= '<div class="event">';
				$str .= '<div id="EventImage_' . $e->ID . '" class="image' . ( !$e->ImageData ? ' edit' : '' ) . '">';
				
				if( $e->ImageData && isset( $e->ImageData->ImgUrl ) )
				{
					$str .= '<div class="imagecontainer" style="background-image:url(\'' . $e->ImageData->ImgUrl . '\');max-width:' . $e->ImageData->Width . 'px;max-height:' . $e->ImageData->Height . 'px;"></div>';
				}
				
				$str .= '<div class="eventdate">';
				$str .= '<div class="month">' . i18n ( 'i18n_' . $mode . date( 'F', strtotime( $e->DateStart ) ) ) . '</div>';
				$str .= '<div class="day">' . date( 'd', strtotime( $e->DateStart ) ) . '</div>';
				$str .= '</div>';
				
				if( $hasAccess && ( $access->UserID == $e->UserID ) )
				{
					$str .= '<div class="fileupload" onclick="ge(\'FilesUploadBtn_' . $e->ID . '\').click()"><div></div></div>';
				}
				$str .= '</div>';
				
				$str .= '<div id="EventContent_' . $e->ID . '" class="wrapper">';
				$str .= '<div>';
				$str .= '<h3 onclick="ViewEvent(\'' . $e->ID . '\',\'' . $e->CategoryID . '\',\'' . strtotime( $e->DateStart ) . '\')"> ' . $e->Name . ' </h3>';
				$str .= '<div ' . ( $onclick ? ( 'onclick=" ' . $onclick . '"' ) : '' ) . ' class="timedate">' . date( 'l, F j', strtotime( $e->DateStart ) ) . ' at ' . date( 'H:i', strtotime( $e->DateStart ) ) . '</div>';
				$str .= '<div ' . ( $onclick ? ( 'onclick=" ' . $onclick . '"' ) : '' ) . ' class="place">' . $e->Place . '</div>';
				//$str .= '<div class="content"> Wall posts </div>';
				
				if( isset( $hours[$e->ID]['Available'] ) && !isset( $hours[$e->ID]['Mine'] ) )
				{
					$str .= '<div class="button"><button onclick="SignupEvent(\'' . $hours[$e->ID]['HourID'] . '\',this)">Sign up</button></div>';
				}
				else if( isset( $hours[$e->ID]['Pending'] ) )
				{
					$str .= '<div class="button"><button onclick="SignupEvent(\'' . $hours[$e->ID]['HourID'] . '\',this)">Accept</button>';
					$str .= '<button onclick="SignoffEvent(\'' . $hours[$e->ID]['HourID'] . '\',this)">Decline</button></div>';
				}
				else if( isset( $hours[$e->ID]['Mine'] ) )
				{
					$str .= '<div class="button"><button onclick="SignoffEvent(\'' . $hours[$e->ID]['HourID'] . '\',this)">Sign off</button></div>';
				}
				
				$str .= '</div>';
				$str .= '</div>';
				
				if( $hasAccess && ( $access->UserID == $e->UserID ) )
				{
					$str .= '<div class="upload_btn">';
					$str .= '<div><span>Upload Image</span></div>';
					$str .= '<form method="post" target="fileIframe" name="FilesUpload_' . $e->ID . '" enctype="multipart/form-data" action="' . $parent->route . '?component=library&action=uploadfile">';
					$str .= '<input type="file" class="file_upload_btn" id="FilesUploadBtn_' . $e->ID . '" name="events" onchange="fileselect( this, \'FilesUpload_' . $e->ID . '\' )"/>';
					$str .= '<input type="hidden" id="EventID_' . $e->ID . '" name="eventid" value="' . $e->ID . '">';
					$str .= '</form>';
					$str .= '<script>setOpacity ( ge(\'FilesUploadBtn_' . $e->ID . '\' ), 0 );</script>';
					$str .= '</div>';
				}
				$str .= '<div class="clearboth" style="clear:both;"></div>';
				$str .= '</div>';
			}
			$str .= '</div>';
		}
		
		return $str;
		
	}
}

if ( !function_exists ( 'switchCalendarMode' ) )
{
	function switchCalendarMode( $date = false, $mode = false, $view = false, $obj = false, $access = false )
	{
		$date = $date ? date( 'Y-m-d', strtotime( $date ) ) : date( 'Y-m-d' );
		
		$agent = UserAgent();
		
		switch( $mode )
		{
			case 'day':
				$cal = '<div class="day">' . renderCalendarDay( $date, $view, $obj, $access, $agent ) . '</div>';
				break;
			case 'week':
				$cal = '<div class="week">' . renderCalendarWeek( $date, $view, $obj, $access, $agent ) . '</div>';
				break;
			case 'year':
				$cal = '<div class="year">' . renderCalendarYear( $date, $view, $obj, $access, $agent ) . '</div>';
				break;
			default:
				$cal = '<div class="month">' . renderCalendarMonth( $date, $view, $obj, $access, $agent ) . '</div>';
				break;
		}
		return $cal;
	}
}

?>
