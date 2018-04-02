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

// --- Navigation Hours --------------------------------------------------

$tstr  = '<div class="tabs">';
//$tstr .= '<label for="show-browse-menu" class="show-browse-menu">Show Menu</label> <input type="checkbox" id="show-browse-menu" role="button">';
$tstr .= '<ul id="OrdersMenu">';
$tstr .= '<li class="active">';
$tstr .= '<a ' .  ( $_REQUEST[ 'h' ] == '' || $_REQUEST[ 'h' ] == '0' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?h=0">';
$tstr .= '<span class="icon"></span>';
$tstr .= '<span class="name">' . i18n( 'i18n_h_Active' ) . '</span>';
$tstr .= '</a>';
$tstr .= '</li>';
$tstr .= '<li class="pending">';
$tstr .= '<a ' . ( $_REQUEST[ 'h' ] == '1' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?h=1">';
$tstr .= '<span class="icon"></span>';
$tstr .= '<span class="name">' . i18n( 'i18n_h_Pending' ) . '</span>';
$tstr .= '</a>';
$tstr .= '</li>';
$tstr .= '<li class="finished">';
$tstr .= '<a ' . ( $_REQUEST[ 'h' ] == '2' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?h=2">';
$tstr .= '<span class="icon"></span>';
$tstr .= '<span class="name">' . i18n( 'i18n_h_Finished' ) . '</span>';
$tstr .= '</a>';
$tstr .= '</li>';
$tstr .= '</ul>';
$tstr .= '<div style="clear:both" class="clearboth"></div>';
$tstr .= '</div>';

// --- Orders ----------------------------------------------------------------

$webadm = false;

if( $mainorders = $database->fetchObjectRows( $q = '
	SELECT 
		o.* 
	FROM 
		SBookOrders o
	WHERE
			o.IsDeleted = "0"
		' . ( ( $_REQUEST['h'] == 0 || !$_REQUEST['h'] ) ? '
		AND o.Status = \'' . ( ( !isset( $_REQUEST['s'] ) || $_REQUEST['s'] == '' ) ? '1' : ( $_REQUEST['s'] == '0' ? '0' : $_REQUEST['s'] ) ) . '\'
		' : '' ) . '
		AND (
			' . ( $parent->folder->MainName == 'Groups' ? '
				(
					o.CategoryID = \'' . $parent->folder->CategoryID . '\' 
				)
			' : '
				(
					o.UserID > 0 AND o.UserID = \'' . $webuser->ContactID . '\' 
				)
			OR 
				(
					o.Participants LIKE ("%' . $webuser->ContactID . '%") 
				)
			' . ( isset( $cats['CategoryID'] ) ? '
			OR 
				(
					o.CategoryID IN (' . $cats['CategoryID'] . ') 
				)
			' : '' ) ) . '
			)
	ORDER BY 
		o.ID ASC 
' ) )
{
	$iiii = 1; $tot = array();
	
	$oids = array();
	
	$checkaccess = CategoryAccess( $webuser->ContactID );
	
	foreach( $mainorders as $o )
	{
		$IsAdmin = false;
		
		$webaccess = ( isset( $checkaccess[$o->CategoryID][$webuser->ContactID] ) ? $checkaccess[$o->CategoryID][$webuser->ContactID] : false );
		
		if ( $webaccess && $webaccess->IsAdmin )
		{
			$IsAdmin = true; $webadm = true;
		}
		else if ( $webaccess && $webaccess->IsModerator && $_REQUEST['h'] == 1 )
		{
			$IsModerator = true; $webadm = true;
		}
		
		/*if( !$database->fetchObjectRow( '
			SELECT 
				h.* 
			FROM 
				SBookHours h 
			WHERE 
					h.ProjectID = \'' . $o->ID . '\' 
				AND h.Title = "Orders" 
				AND h.IsDeleted = "0" 
				' . ( ( !$IsAdmin && !$IsModerator ) || $_REQUEST['mode'] == 'members' ? '
				AND h.UserID = \'' . $webuser->ContactID . '\'
				' : '' ) . ( $filterhours ? $filterhours : '' ) . '
			ORDER BY 
				h.DateStart ASC
			LIMIT 1
		' ) )
		{
			//if ( checkOrderAccess( $h->UserID, $o->Data ) )
			//{
				$oids[$o->ID] = $o->ID;
			//}
		}*/
		
		if( $hours = $database->fetchObjectRows( '
			SELECT 
				h.* 
			FROM 
				SBookHours h 
			WHERE 
					h.ProjectID = \'' . $o->ID . '\' 
				AND h.Title = "Orders" 
				AND h.IsDeleted = "0" 
				' . ( !$IsAdmin && !$IsModerator ? '
				AND h.UserID = \'' . $webuser->ContactID . '\'
				' : '' ) . ( $filterhours ? $filterhours : '' ) . '
			ORDER BY 
				h.DateStart ASC 
		' ) )
		{
			foreach( $hours as $h )
			{
				if ( $h->UserID > 0 )
				{
					if ( checkOrderAccess( $h->UserID, $o->Data ) )
					{
						$oids[$o->ID] = $o->ID;
					}
					
					$tot[$o->ID] = ( isset( $tot[$o->ID] ) ? ( $tot[$o->ID] + $h->Hours ) : $h->Hours );
				}
			}
		}
		
		
		if ( checkOrderAccess( $webuser->ContactID, $o->Data ) && ( !isset( $_REQUEST['h'] ) || $_REQUEST['h'] == 0 ) )
		{
			$oids[$o->ID] = $o->ID;
		}
		
		$comid[$o->CustomerID] = $o->CustomerID;
	}
	
	$comid = GetUserDisplayname( $comid );
	
	foreach( $mainorders as $o )
	{
		if( isset( $oids ) && !isset( $oids[$o->ID] ) )
		{
			continue;
		}
		
		if( $o->ParentID > 0 )
		{
			if( !isset( $subs[$o->ParentID] ) )
			{
				$subs[$o->ParentID] = array();
			}
			
			$subs[$o->ParentID][] = $o;
		}
		else
		{
			$output[$o->ID] = $o;
		}
	}
	
	
	foreach( $output as $o )
	{
		$cstr .= '<li><div class="header sw' . ( $sw = ( $sw == 1 ? 2 : 1 ) ) . '" id="OrderID_' . $o->ID . '" onclick="EditOrder(' . $o->ID . ')"><table><tr>';
		$cstr .= ( $_REQUEST['h'] <= 0 ? ( '<td class="col1" id="oid_' . $o->ID . '"><input type="checkbox" value="' . $o->ID . '" onclick="MarkAllHours(this,event)"></td>' ) : ( '<td class="col1" id="oid_' . $o->ID . '">[' . $iiii++ . ']</td>' ) );
		$cstr .= '<td class="col2"' . ( $_REQUEST['h'] <= 0 ? ' colspan="2"' : '' ) . '><span class="title">' . ( isset( $comid[$o->CustomerID] ) ? $comid[$o->CustomerID] : $o->F6 ) . ( $o->OrderID ? ' (' . $o->OrderID . ( $o->JobID ? ( '-' . $o->JobID ) : '' ) . ')' : '' ) . ' </span> './*'<span onclick="OrderHistory(\'' . $o->ID . '\',event)">(i)</span>'.*/' <span class="hours">(' . ( isset( $tot[$o->ID] ) ? $tot[$o->ID] : 0 ) . 't) </span></td>';
		$cstr .= '</tr></table></div>';
		
		$odstr = ''; $_REQUEST['oid'] = $o->ID;
		
		if ( $_REQUEST['h'] <= 2 )
		{
			//include ( $cbase . '/functions/orderdetails.php' );
		}
		
		$cstr .= '<div class="inner' . /*( $_REQUEST['h'] <= 2 && $odstr ? ' open' : '' ) . */'" id="OrderDetails_' . $o->ID . '">' . $odstr . '</div>';
		
		$cstr .= '<div class="inner" id="OrderDetails_' . $o->ID . '_0"></div>';
		
		// Check if there is suborders on this order and render them in an sublist --------------------------------------------------------------------
		
		if ( isset( $subs[$o->ID] ) )
		{
			$iiiii = 1;
			
			$cstr .= '<ul class="open">';
			
			foreach( $subs[$o->ID] as $s )
			{
				$cstr .= '<li><div class="header sw' . ( $sw = ( $sw == 1 ? 2 : 1 ) ) . '" id="OrderID_' . $s->ID . '" onclick="EditOrder(' . $s->ID . ')"><table><tr>';
				$cstr .= ( $_REQUEST['h'] <= 0 ? ( '<td class="col1" id="oid_' . $s->ID . '"><input type="checkbox" value="' . $s->ID . '" onclick="MarkAllHours(this,event)"></td>' ) : ( '<td class="col1" id="oid_' . $s->ID . '">[-]</td>' ) );
				$cstr .= '<td class="col2"' . ( $_REQUEST['h'] <= 0 ? ' colspan="2"' : '' ) . '><span class="title">' . ( isset( $comid[$s->CustomerID] ) ? $comid[$s->CustomerID] : $s->F6 ) . ( $s->OrderID ? ' (' . $s->OrderID . ( $s->JobID ? ( '-' . $s->JobID ) : '' ) . ')' : '' ) . ' </span> './*'<span onclick="OrderHistory(\'' . $s->ID . '\',event)">(i)</span>'.*/' <span class="hours">(' . ( isset( $tot[$s->ID] ) ? $tot[$s->ID] : 0 ) . 't) </span></td>';
				$cstr .= '</tr></table></div>';
				
				$odstr = ''; //$_REQUEST['oid'] = $s->ID;
				
				//if ( $_REQUEST['h'] <= 2 )
				//{
					//include ( $cbase . '/functions/orderdetails.php' );
				//}
				
				$cstr .= '<div class="inner' . /*( $_REQUEST['h'] <= 2 ? ' open' : '' ) . */'" id="OrderDetails_' . $s->ID . '">' . $odstr . '</div>';
			}
			
			$cstr .= '</ul>';
		}
		
		$cstr .= '</li>';
	}
}

// --- Heading --------------------------------------------------------------

$sort = '';

if ( $_REQUEST['h'] >= 1 )
{
	$sort  = '<form>';
	$sort .= '<input type="hidden" name="h" value="' . ( isset( $_REQUEST['h'] ) ? $_REQUEST['h'] : 0 ) . '"/>';
	$sort .= '<select name="sort" onchange="this.parentNode.submit()">';
	$sort .= '<option value="1"' . ( isset( $_REQUEST['sort'] ) && $_REQUEST['sort'] == '1' ? 'selected="selected"' : '' ) . '>' . i18n( 'Projects' ) . '</option>';
	$sort .= '<option value="2"' . ( isset( $_REQUEST['sort'] ) && $_REQUEST['sort'] == '2' ? 'selected="selected"' : '' ) . '>' . i18n( 'Participants' ) . '</option>';
	$sort .= '</select></form>';
}

$hstr  = '<div class="heading"><table><tr>';
$hstr .= '<td class="col1">#</td>';
//$hstr .= '<td class="col2">' . i18n( 'Projects' ) . '</td>';
$hstr .= '<td class="col2">' . $sort . '</td>';
if ( $_REQUEST['h'] <= 0 )
{
	$hstr .= '<td class="col4"><div class="buttons">';
	$hstr .= '<button onclick="CheckAllHours(\'Vil du sende inn disse timene nå?\',true)">Send timer</button>';
	$hstr .= '</div></td>';
}
else if ( $_REQUEST['h'] <= 1 && $webadm )
{
	$hstr .= '<td class="col4"><div class="buttons">';
	$hstr .= '<button onclick="CheckAllHours(\'Vil du godkjenne disse timene nå?\')">Godkjenn timer</button>';
	$hstr .= '</div></td>';
}
$hstr .= '</tr></table></div>';

?>
