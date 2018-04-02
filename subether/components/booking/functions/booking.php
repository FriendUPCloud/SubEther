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

$str = ''; $evts = '';

$placename = array(); $slots = ''; $people = ''; $datefrom = ''; $dateto = '';

if( !$_POST['placename'] && !$_POST['fromdate'] && !$_POST['todate'] && $dobj )
{
	$_POST['placename'] = $dobj->placename;
	$_POST['number'] = $dobj->number;
	$_POST['adults'] = $dobj->adults;
	$_POST['children'] = $dobj->children;
	$_POST['fromdate'] = $dobj->fromdate;
	$_POST['todate'] = $dobj->todate;
}

if( $_POST['placename'] )
{
	$_POST['placename'] = str_replace( array( ',', '+' ), array( '+', '+' ), $_POST['placename'] );
	$_POST['placename'] = explode( '+', $_POST['placename'] );
	
	if( is_array( $_POST['placename'] ) )
	{
		foreach( $_POST['placename'] as $p )
		{
			$placename[] = '`Name` LIKE "%' . trim( $p ) . '%" OR `Place` LIKE "%' . trim( $p ) . '%"';
		}
	}
	else
	{
		$placename = '`Name` LIKE "%' . trim( $_POST['placename'] ) . '%" OR `Place` LIKE "%' . trim( $_POST['placename'] ) . '%"';
	}
}

if( $_POST['number'] && ( $_POST['adults'] || $_POST['children'] ) )
{
	$slots = $_POST['number'];
	$people = ( ( $_POST['adults'] + $_POST['children'] ) / $slots );
}

if( $_POST['fromdate'] && $_POST['todate'] )
{
	$fromdate 	= date( 'Y-m-d 00:00:00.000000', $_POST['fromdate'] );
	$todate 	= date( 'Y-m-d 23:59:59.000000', $_POST['todate'] );
}

if ( isset( $parent->access->IsAdmin ) )
{
	$str .= '<div class="event admin closed">';
	$str .= '<div class="information" onclick="EditBooking(this)"></div>';
	$str .= '</div>';
}

if ( $rows = $database->fetchObjectRows( $q = '
	SELECT
		*
	FROM
		SBookEvents
	WHERE
		' . ( isset( $_POST['eid'] ) ? '`ID` = \'' . $_POST['eid'] . '\' AND' : '' ) . '
			`Component` = "booking"
		AND `CategoryID` = \'' . $parent->folder->CategoryID . '\'
		AND `IsDeleted` = "0"
		' . ( $slots ? 'AND `Slots` >= ' . $slots . ' ' : '' ) . '
		' . ( $people ? 'AND `Limit` >= ' . $people . ' ' : '' ) . '
		' . ( $placename ? is_array( $placename ) ? 'AND ( ' . implode( ' OR ', $placename ) . ' )' : 'AND ( ' . $placename . ' )' : '' ) . '
	ORDER BY
		ID DESC 
' ) )
{
	$img = array(); $slot = array(); $hours = array();
	
	foreach( $rows as $e )
	{
		$img[$e->ImageID] = $e->ImageID;
		$slot[$e->ID] = $e->ID;
	}
	
	if( $img && $im = $database->fetchObjectRows( $iq = '
		SELECT
			f.DiskPath, i.Filename, i.ID, i.Width, i.Height 
		FROM
			Folder f, Image i
		WHERE
			i.ID IN (' . implode( ',', $img ) . ') AND f.ID = i.ImageFolder
		ORDER BY
			ID ASC
	' ) )
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
			
			$img[$i->ID] = $obj;
		}
	}
	
	if( $slot && $fromdate && $todate && ( $hr = $database->fetchObjectRows( $hq = '
		SELECT 
			h.* 
		FROM 
			SBookHours h  
		WHERE 
				h.ProjectID IN (' . implode( ',', $slot ) . ') 
			AND h.DateStart >= \'' . $fromdate . '\' 
			AND h.DateEnd <= \'' . $todate . '\'
			AND h.IsDeleted = "0" 
		ORDER BY 
			h.ID ASC 
	' ) ) )
	{
		$hours = array();
		
		foreach( $hr as $h )
		{
			if( !isset( $hours[$h->ProjectID] ) )
			{
				$hours[$h->ProjectID] = array();
			}
			
			if( $h->UserID == $webuser->ContactID )
			{
				$hours[$h->ProjectID]['Mine'] = true;
			}
			
			$hours[$h->ProjectID][] = $h;
		}
	}
	
	foreach ( $rows as $row )
	{
		if( isset( $hours[$row->ID] ) && !isset( $hours[$row->ID]['Mine'] ) && count( $hours[$row->ID] ) >= $row->Slots )
		{
			continue;
		}
		
		if( !isset( $_POST['eid'] ) )
		{
			$evts .= '<div class="event' . ( !isset( $img[$row->ImageID] ) ? ' edit' : '' ) . ( isset( $hours[$row->ID]['Mine'] ) ? ' mine' : '' ) . '" id="BookingID_' . $row->ID . '">';
		}
		
		if( isset( $hours[$row->ID]['Mine'] ) && isset( $parent->access->IsAdmin ) )
		{
			$onclick = 'SignoffBooking(this,\'' . $row->ID . '\',\'' . $slots . '\',\'' . $people . '\',\'' . $_POST['fromdate'] . '\',\'' . $_POST['todate'] . '\')';
		}
		else if( isset( $hours[$row->ID]['Mine'] ) )
		{
			$onclick = 'alert(\'Contact an admin to cancel your booking\');this.getElementsByTagName(\'input\')[0].checked=true;';
		}
		else
		{
			$onclick = 'SignupBooking(this,\'' . $row->ID . '\',\'' . $slots . '\',\'' . $people . '\',\'' . $_POST['fromdate'] . '\',\'' . $_POST['todate'] . '\')';
		}
		
		$evts .= '<div class="checkbox" onclick="' . $onclick . '"><input type="checkbox" ' . ( isset( $hours[$row->ID]['Mine'] ) ? 'checked="checked"' : '' ) . '/></div>';
		
		if ( isset( $parent->access->IsAdmin ) )
		{
			//$evts .= '<div class="edit"><div onclick="" class="options"></div></div>';
			
			$evts .= '<div class="fileupload" onclick="ge(\'FilesUploadBtn_' . $row->ID . '\').click()"><div></div></div>';
			$evts .= '<div class="upload_btn">';
			$evts .= '<form method="post" target="fileIframe" name="FilesUpload_' . $row->ID . '" enctype="multipart/form-data" action="' . $parent->route . '?component=library&action=uploadfile">';
			$evts .= '<input type="file" class="file_upload_btn" id="FilesUploadBtn_' . $row->ID . '" name="events" onchange="fileselect( this, \'FilesUpload_' . $row->ID . '\' )"/>';
			$evts .= '<input type="hidden" id="EventID_' . $row->ID . '" name="eventid" value="' . $row->ID . '">';
			$evts .= '</form>';
			$evts .= '<script>setOpacity ( ge(\'FilesUploadBtn_' . $row->ID . '\' ), 0 );</script>';
			$evts .= '</div>';
		}
		
		$evts .= '<div class="information" onclick="EditBooking(this,\'' . $row->ID . '\')">';
		
		$evts .= '<div class="image"' . ( isset( $img[$row->ImageID]->ImgUrl ) ? ( ' style="background-image:url(\'' . $img[$row->ImageID]->ImgUrl . '\')"' ) : '' ) . '></div>';
		
		$evts .= '<div class="inputs">';
		$evts .= '<div class="heading"><a href="' . $parent->route . '?e=' . $row->ID . '">' . $row->Name . '</a></div>';
		$evts .= '<div class="place">' . $row->Place . '</div>';
		$evts .= '<div class="description">' . $row->Details . '</div>';
		$evts .= '<div class="price">' . $row->Price . '</div>';
		$evts .= '</div>';
		$evts .= '</div>';
		
		if( !isset( $_POST['eid'] ) )
		{
			$evts .= '</div>';
		}
	}
}

$str .= $evts;

$str .= '<div class="clearboth"></div>';

if( isset( $_REQUEST['function'] ) )
{
	die( 'ok<!--separate-->' . ( isset( $_POST['eid'] ) ? $evts : $str ) );
}

?>
