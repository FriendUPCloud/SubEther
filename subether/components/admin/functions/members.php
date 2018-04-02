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

// TODO: Fix navigation ........ looks to messy now ...

// TODO: Make the search more intelligent

$search = array(); $datefrom = ''; $dateto = '';

if ( isset( $_POST['search'] ) && isset( $_POST['fromdate'] ) && isset( $_POST['todate'] ) )
{
	$_REQUEST['search'] = $_POST['search'];
	$_REQUEST['fromdate'] = $_POST['fromdate'];
	$_REQUEST['todate'] = $_POST['todate'];
}

if ( !$_REQUEST['search'] && !$_REQUEST['fromdate'] && !$_REQUEST['todate'] && $dobj )
{
	$_REQUEST['search'] = $dobj->placename;
	$_REQUEST['fromdate'] = $dobj->fromdate;
	$_REQUEST['todate'] = $dobj->todate;
}

if ( $_REQUEST['search'] )
{
	$_REQUEST['search'] = str_replace( array( ',', '+' ), array( '+', '+' ), $_REQUEST['search'] );
	$_REQUEST['search'] = explode( '+', $_REQUEST['search'] );
	
	if ( is_array( $_REQUEST['search'] ) )
	{
		foreach ( $_REQUEST['search'] as $p )
		{
			$search[] = 'c.Firstname LIKE "%' . trim( $p ) . '%" OR c.Middlename LIKE "%' . trim( $p ) . '%" OR c.Lastname LIKE "%' . trim( $p ) . '%" OR c.Username LIKE "%' . trim( $p ) . '%"';
		}
	}
	else
	{
		$search = 'c.Firstname LIKE "%' . trim( $_REQUEST['search'] ) . '%" OR c.Middlename LIKE "%' . trim( $_REQUEST['search'] ) . '%" OR c.Lastname LIKE "%' . trim( $_REQUEST['search'] ) . '%" OR c.Username LIKE "%' . trim( $_REQUEST['search'] ) . '%"';
	}
}

if ( $_REQUEST['fromdate'] && $_REQUEST['todate'] )
{
	$fromdate 	= date( 'Y-m-d 00:00:00.000000', $_REQUEST['fromdate'] );
	$todate 	= date( 'Y-m-d 23:59:59.000000', $_REQUEST['todate'] );
}

$str = '<div id="Members" class="orders">';

$str .= '<div class="heading"><table><tr>';
$str .= '<td class="col1">#</td>';
$str .= '<td class="col2">ID</td>';
$str .= '<td class="col3">Name</td>';
$str .= '<td class="col4"></td>';
$str .= '<td class="col5">Hours</td>';
$str .= '<td class="col6">Date</td>';
$str .= '</tr></table></div>';

if ( $rows = $database->fetchObjectRows( $q = '
	SELECT 
		c.*,
		a.ID AS AccessID,
		a.MemberID 
	FROM 
		SBookCategoryRelation r,
		SBookContact c,
		SBookCategoryAccess a 
	WHERE
			r.CategoryID = \'' . $parent->folder->CategoryID . '\' 
		AND r.ObjectType = "Users" 
		AND r.ObjectID > 0 
		AND c.UserID = r.ObjectID 
		AND c.NodeID = "0"
		AND a.CategoryID = r.CategoryID 
		AND a.ContactID = c.ID
		' . ( $search ? is_array( $search ) ? 'AND ( ' . implode( ' OR ', $search ) . ' )' : 'AND ( ' . $search . ' )' : '' ) . '
	ORDER BY 
		c.ID ASC 
' ) )
{
	$usrs = array(); $pts = array(); $hrs = array(); $tot = array();
	
	foreach ( $rows as $row )
	{
		$usrs[$row->ID] = $row->ID;
	}
	
	$comid = GetUserDisplayname( $usrs );
	
	// Parent Settings
	if ( $pset = $database->fetchObjectRows( '
		SELECT 
			a.* 
		FROM 
			SBookCategory c, 
			SBookCategory p,
			SBookCategoryAccess a 
		WHERE
				c.ID = \'' . $parent->folder->CategoryID . '\'
			AND c.Type = "SubGroup"
			AND c.IsSystem = "0"
			AND c.ParentID > 0 
			AND p.ID = c.ParentID 
			AND p.Type = "SubGroup" 
			AND p.IsSystem = "0" 
			AND p.ParentID = "0"
			AND a.CategoryID = p.ID
		ORDER BY
			a.ID ASC 
	' ) )
	{
		foreach ( $pset as $ps )
		{
			if ( $ps->MemberID )
			{
				$pts[$ps->ContactID] = $ps->MemberID;
			}
		}
	}
	
	if ( $usrs && ( $events = $database->fetchObjectRows( $q = '
		SELECT 
			h.*, o.OrderID, o.JobID, /*o.CustomerID, c.Username AS Name, */o.F6 AS ProjectName 
		FROM 
			SBookOrders o, 
			/*SBookContact c,*/ 
			SBookHours h 
		WHERE 
				o.CategoryID = \'' . $parent->folder->CategoryID . '\' 
			/*AND o.CustomerID = c.ID*/
			AND h.ProjectID = o.ID
			AND h.IsFinished = "0" 
			AND h.UserID IN (' . implode( ',', $usrs ) . ') 
			' . ( $fromdate ? 'AND h.DateStart >= \'' . $fromdate . '\' ' : '' ) . '
			' . ( $todate ? 'AND h.DateEnd <= \'' . $todate . '\' ' : '' ) . '
		ORDER BY 
			o.ID ASC, h.ID ASC 
	' ) ) )
	{
		// TODO: Fix from date and todate here ... so you can display total hours not dealth with
		
		foreach ( $events as $evt )
		{
			if ( $evt->UserID > 0 )
			{
				if ( !isset( $hrs[$evt->UserID] ) )
				{
					$obj = new stdClass();
					$obj->Hours = array();
					$obj->Projects = array();
					$hrs[$evt->UserID] = $obj;
				}
				
				$evt->Name = ( $evt->ProjectName ? $evt->ProjectName : $evt->Name );
				
				if ( $evt->DateEnd != '0000-00-00 00:00:00' && $evt->DateStart != '0000-00-00 00:00:00' )
				{
					// Time difference in hours
					$diffInHours = ( ( strtotime( $evt->DateEnd ) - strtotime( $evt->DateStart ) ) / 3600 );
					$hrs[$evt->UserID]->Hours[$evt->ID] = $diffInHours;
					$hrs[$evt->UserID]->FromDate = ( isset( $hrs[$evt->UserID]->FromDate ) && ( strtotime( $hrs[$evt->UserID]->FromDate ) < strtotime( $evt->DateStart ) ) ? $hrs[$evt->UserID]->FromDate : $evt->DateStart );
					$hrs[$evt->UserID]->ToDate = ( isset( $hrs[$evt->UserID]->ToDate ) && ( strtotime( $hrs[$evt->UserID]->ToDate ) > strtotime( $evt->DateEnd ) ) ? $hrs[$evt->UserID]->ToDate : $evt->DateEnd );
					$hrs[$evt->UserID]->Projects[$evt->ID] = ( $evt->Name . ' ' . date( 'd.m.Y', strtotime( $evt->DateStart ) ) . ' (' . $diffInHours . 'h)' );
					$hrs[$evt->UserID]->Total = ( isset( $hrs[$evt->UserID]->Total ) ? ( $hrs[$evt->UserID]->Total + $diffInHours ) : $diffInHours );
				}
				else if ( $evt->Hours && $evt->DateStart != '0000-00-00 00:00:00' )
				{
					// Time difference in hours
					$hrs[$evt->UserID]->Hours[$evt->ID] = $evt->Hours;
					$hrs[$evt->UserID]->FromDate = ( isset( $hrs[$evt->UserID]->FromDate ) && ( strtotime( $hrs[$evt->UserID]->FromDate ) < strtotime( $evt->DateStart ) ) ? $hrs[$evt->UserID]->FromDate : $evt->DateStart );
					$hrs[$evt->UserID]->ToDate = ( isset( $hrs[$evt->UserID]->ToDate ) && ( strtotime( $hrs[$evt->UserID]->ToDate ) > strtotime( $evt->DateStart ) ) ? $hrs[$evt->UserID]->ToDate : $evt->DateStart );
					$hrs[$evt->UserID]->Projects[$evt->ID] = ( $evt->Name . ' ' . date( 'd.m.Y', strtotime( $evt->DateStart ) ) . ' (' . $evt->Hours . 'h)' );
					$hrs[$evt->UserID]->Total = ( isset( $hrs[$evt->UserID]->Total ) ? ( $hrs[$evt->UserID]->Total + $evt->Hours ) : $evt->Hours );
				}
			}
		}
	}
	
	if ( $usrs && ( $total = $database->fetchObjectRows( $q = '
		SELECT 
			h.*, o.OrderID, o.JobID, /*o.CustomerID, c.Username AS Name*/ 
		FROM 
			SBookOrders o, 
			/*SBookContact c,*/ 
			SBookHours h 
		WHERE 
				o.CategoryID = \'' . $parent->folder->CategoryID . '\' 
			/*AND o.CustomerID = c.ID*/ 
			AND h.ProjectID = o.ID 
			AND h.UserID IN (' . implode( ',', $usrs ) . ') 
		ORDER BY 
			o.ID ASC, h.ID ASC 
	' ) ) )
	{
		foreach ( $total as $tal )
		{
			if ( $tal->UserID > 0 )
			{
				$tot[$tal->UserID] = ( isset( $tot[$tal->UserID] ) ? ( $tot[$tal->UserID] + $tal->Hours ) : $tal->Hours );
			}
		}
	}
	
	$str .= '<div class="list"><ul>';
	
	foreach ( $rows as $row )
	{
		$str .= '<li><div class="header sw' . ( $sw = ( $sw == 1 ? 2 : 1 ) ) . '" id="MemberID_' . $row->ID . '" onclick="EditMemberHours(\'' . $row->ID . '\',\'' . $_REQUEST['fromdate'] . '\',\'' . $_REQUEST['todate'] . '\');"><table><tr>';
		$str .= '<td class="col1" id="uid_' . $row->ID . '">';
		$str .= '<input name="uid" type="checkbox" onclick="cancelBubble(event)" value="' . $row->ID . '"/>';
		$str .= '</td>';
		$str .= '<td class="col2"><input type="text" onclick="cancelBubble(event)" name="MemberID" ' . ( isset( $pts[$row->ID] ) ? ( 'value="' . $pts[$row->ID] . '" disabled' ) : ( 'value="' . ( $row->MemberID ? $row->MemberID : $row->ID ) . '" onchange="saveMemberData(\'' . $row->AccessID . '\',this)" onkeydown="memIsEdit(this)"' ) ) . '/></td>';
		$str .= '<td class="col3">' . ( isset( $comid[$row->ID] ) ? $comid[$row->ID] : $row->ID ) . '</td>';
		$str .= '<td class="col4"><div><span></span></div></td>';
		$str .= '<td class="col5">' . ( isset( $hrs[$row->ID]->Total ) ? $hrs[$row->ID]->Total : 0 ) . '</td>';
		$str .= '<td class="col6">' . ( isset( $hrs[$row->ID]->FromDate ) && isset( $hrs[$row->ID]->ToDate ) ? ( date( 'D d M y', strtotime( $hrs[$row->ID]->FromDate ) ) . ' - ' . date( 'D d M y', strtotime( $hrs[$row->ID]->ToDate ) ) ) : ( isset( $hrs[$row->ID]->FromDate ) ? ( date( 'D d M y', strtotime( $hrs[$row->ID]->FromDate ) ) ) : '' ) ) . '</td>';
		$str .= '</tr></table></div>';
		
		$str .= '<div id="MemberDetails_' . $row->ID . '" class="inner"></div>';
		
		$str .= '</li>';
	}
	
	$str .= '</ul></div>';
}

$str .= '</div>';

if ( isset( $_REQUEST['function'] ) )
{
	die( 'ok<!--separate-->' . $str );
}

?>
