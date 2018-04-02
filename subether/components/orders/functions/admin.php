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

// --- Navigation Orders -----------------------------------------------

$nstr  = '<div class="tabs">';
//$nstr .= '<label for="show-browse-menu" class="show-browse-menu">Show Menu</label> <input type="checkbox" id="show-browse-menu" role="button">';
$nstr .= '<ul id="OrdersMenu">';
$nstr .= '<li class="pending">';
$nstr .= '<a ' . ( $_REQUEST[ 's' ] == '0' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?r=' . $_REQUEST['r'] . '&s=0">';
$nstr .= '<span class="icon"></span>';
$nstr .= '<span class="name">' . i18n( 'Pending' ) . '</span>';
$nstr .= '</a>';
$nstr .= '</li>';
$nstr .= '<li class="active">';
$nstr .= '<a ' .  ( $_REQUEST[ 's' ] == '' || $_REQUEST[ 's' ] == '1' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?r=' . $_REQUEST['r'] . '&s=1">';
$nstr .= '<span class="icon"></span>';
$nstr .= '<span class="name">' . i18n( 'Active' ) . '</span>';
$nstr .= '</a>';
$nstr .= '</li>';
$nstr .= '<li class="onhold">';
$nstr .= '<a ' . ( $_REQUEST[ 's' ] == '2' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?r=' . $_REQUEST['r'] . '&s=2">';
$nstr .= '<span class="icon"></span>';
$nstr .= '<span class="name">' . i18n( 'OnHold' ) . '</span>';
$nstr .= '</a>';
$nstr .= '</li>';
$nstr .= '<li class="canceled">';
$nstr .= '<a ' . ( $_REQUEST[ 's' ] == '3' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?r=' . $_REQUEST['r'] . '&s=3">';
$nstr .= '<span class="icon"></span>';
$nstr .= '<span class="name">' . i18n( 'Canceled' ) . '</span>';
$nstr .= '</a>';
$nstr .= '</li>';
$nstr .= '<li class="finished">';
$nstr .= '<a ' . ( $_REQUEST[ 's' ] == '4' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?r=' . $_REQUEST['r'] . '&s=4">';
$nstr .= '<span class="icon"></span>';
$nstr .= '<span class="name">' . i18n( 'Finished' ) . '</span>';
$nstr .= '</a>';
$nstr .= '</li>';
$nstr .= '<li class="archived">';
$nstr .= '<a ' . ( $_REQUEST[ 's' ] == '5' ? 'class="current"' : '' ) . ' href="' . $parent->route . '?r=' . $_REQUEST['r'] . '&s=5">';
$nstr .= '<span class="icon"></span>';
$nstr .= '<span class="name">' . i18n( 'Archived' ) . '</span>';
$nstr .= '</a>';
$nstr .= '</li>';
$nstr .= '</ul>';
$nstr .= '<div style="clear:both" class="clearboth"></div>';
$nstr .= '</div>';

// --- Orders ----------------------------------------------------------------

if( $mainorders = $database->fetchObjectRows( $q = '
	SELECT 
		o.* 
	FROM 
		SBookOrders o
	WHERE
			o.Status = \'' . ( ( !isset( $_REQUEST['s'] ) || $_REQUEST['s'] == '' ) ? '1' : ( $_REQUEST['s'] == '0' ? '0' : $_REQUEST['s'] ) ) . '\' 
		AND o.IsDeleted = "0"
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
	$iiii = 0; $oids = array(); $subs = array(); $output = array();
	
	if ( isset( $_REQUEST['h'] ) && $_REQUEST['h'] > 0 )
	{
		$checkaccess = CategoryAccess( $webuser->ContactID );
		
		foreach( $mainorders as $o )
		{
			$IsAdmin = false;
			
			$webaccess = ( isset( $checkaccess[$o->CategoryID][$webuser->ContactID] ) ? $checkaccess[$o->CategoryID][$webuser->ContactID] : false );
			
			if ( $webaccess && $webaccess->IsAdmin )
			{
				$IsAdmin = true;
			}
			
			if( !$database->fetchObjectRow( '
				SELECT 
					h.* 
				FROM 
					SBookHours h 
				WHERE 
						h.ProjectID = \'' . $o->ID . '\' 
					AND h.Title = "Orders" 
					AND h.IsDeleted = "0" 
					' . ( !$IsAdmin || $_REQUEST['mode'] == 'members' ? '
					AND h.UserID = \'' . $webuser->ContactID . '\'
					' : '' ) . ( $filterhours ? $filterhours : '' ) . '
				ORDER BY 
					h.DateStart ASC
				LIMIT 1
			' ) )
			{
				$oids[$o->ID] = $o->ID;
			}
			
			$comid[$o->CustomerID] = $o->CustomerID;
		}
	}
	
	$comid = GetUserDisplayname( $comid );
	
	foreach( $mainorders as $o )
	{
		if( isset( $oids[$o->ID] ) )
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
		
		$output[$o->ID] = $o;
	}
	
	
	if ( $output )
	{
		foreach( $output as $o )
		{
			if ( $o->ParentID > 0 && isset( $subs[$o->ParentID] ) && isset( $output[$o->ParentID] ) )
			{
				continue;
			}
			
			$cstr .= '<li><div class="header sw' . ( $sw = ( $sw == 1 ? 2 : 1 ) ) . '" id="OrderID_' . $o->ID . '" onclick="EditOrder(' . $o->ID . ')"><table><tr>';
			$cstr .= '<td class="col1" id="oid_' . $o->ID . '">[' . ++$iiii . ']</td>';
			$cstr .= '<td class="col2"><span class="title">' . ( isset( $comid[$o->CustomerID] ) ? $comid[$o->CustomerID] : $o->F6 ) . ( $o->OrderID ? ' (' . $o->OrderID . ( $o->JobID ? ( '-' . $o->JobID ) : '' ) . ')' : '' ) . ' </span> './*'<span onclick="OrderHistory(\'' . $o->ID . '\',event)">(i)</span>'.*/' <span class="hours">' . /*'() ' . */'</span></td>';
			$cstr .= '<td class="col3">'/* . ( $o->Progress ? ( $o->Progress . '%' ) : '0%' )*/ . '</td>';
			$cstr .= '<td class="col4">';
			$cstr .= '<button onclick="EditOrder(\'' . $o->ID . '\',false,true);return cancelBubble(event);">' . i18n( 'Create_suborder' ) . '</button>';
			$cstr .= '</td>';
			$cstr .= '</tr></table></div>';
			
			$odstr = ''; //$_REQUEST['oid'] = $o->ID;
			
			//if ( $_REQUEST['h'] <= 2 )
			//{
			//	include ( $cbase . '/functions/orderdetails.php' );
			//}
			
			$cstr .= '<div class="inner' . /*( !$oadmin && $_REQUEST['h'] <= 2 ? ' open' : '' ) . */'" id="OrderDetails_' . $o->ID . '">' . $odstr . '</div>';
			
			$cstr .= '<div class="inner" id="OrderDetails_' . $o->ID . '_0"></div>';
			
			// Check if there is suborders on this order and render them in an sublist --------------------------------------------------------------------
			
			if ( isset( $subs[$o->ID] ) )
			{
				$iiiii = 1;
				
				$cstr .= '<ul class="open">';
				
				foreach( $subs[$o->ID] as $s )
				{
					$cstr .= '<li><div class="header sw' . ( $sw = ( $sw == 1 ? 2 : 1 ) ) . '" id="OrderID_' . $s->ID . '" onclick="EditOrder(' . $s->ID . ')"><table><tr>';
					$cstr .= '<td class="col1" id="oid_' . $s->ID . '">[-]</td>';
					$cstr .= '<td class="col2"><span class="title">' . ( isset( $comid[$s->CustomerID] ) ? $comid[$s->CustomerID] : $s->F6 ) . ( $s->OrderID ? ' (' . $s->OrderID . ( $s->JobID ? ( '-' . $s->JobID ) : '' ) . ')' : '' ) . ' </span> './*'<span onclick="OrderHistory(\'' . $s->ID . '\',event)">(i)</span>'.*/' <span class="hours">' . /*'() ' . */'</span></td>';
					$cstr .= '<td class="col3">'/* . ( $s->Progress ? ( $s->Progress . '%' ) : '0%' )*/ . '</td>';
					$cstr .= '<td class="col4">';
					$cstr .= '</td>';
					$cstr .= '</tr></table></div>';
					
					$odstr = ''; //$_REQUEST['oid'] = $s->ID;
					
					//if ( !$oadmin && $_REQUEST['h'] <= 2 )
					//{
						//include ( $cbase . '/functions/orderdetails.php' );
					//}
					
					$cstr .= '<div class="inner' . /*( !$oadmin && $_REQUEST['h'] <= 2 ? ' open' : '' ) . */'" id="OrderDetails_' . $s->ID . '">' . $odstr . '</div>';
				}
				
				$cstr .= '</ul>';
			}
			
			$cstr .= '</li>';
		}
	}
}

// --- Create new order -----------------------------------------------------

$astr  = '<li><div class="header sw2" id="OrderID_0" onclick="EditOrder(\'0\')"><table><tr>';
$astr .= '<td colspan="3">' . i18n( 'Create' ) . '</td>';
$astr .= '<td class="col4">';
$astr .= '</td>';
$astr .= '</tr></table></div>';

$astr .= '<div class="inner" id="OrderDetails_0"></div></li>';

$cstr  = ( $astr . $cstr );

$cstr .= '';

// --- Heading --------------------------------------------------------------

$hstr  = '<div class="heading admin"><table><tr>';
$hstr .= '<td class="col1">#</td>';
$hstr .= '<td class="col2">' . i18n( 'Projects' ) . '</td>';

//$hstr .= '<td class="col3">Progress</td>';
$hstr .= '<td class="col3"></td>';
$hstr .= '<td class="col4"></td>';

$hstr .= '</tr></table></div>';

?>
