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

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);

$odstr = ( $odstr ? $odstr : '' );

$admin = false; $datefrom = ''; $dateto = ''; $filterhours = '';

if ( isset( $_POST['fromdate'] ) && isset( $_POST['todate'] ) )
{
	$_REQUEST['fromdate'] = $_POST['fromdate'];
	$_REQUEST['todate'] = $_POST['todate'];
}

if ( $_REQUEST['fromdate'] && $_REQUEST['todate'] )
{
	$fromdate 	= date( 'Y-m-d 00:00:00.000000', $_REQUEST['fromdate'] );
	$todate 	= date( 'Y-m-d 23:59:59.000000', $_REQUEST['todate'] );
}

// TODO: Find hours based on userid and based on date spans from hours connected to the orders and loop it.

if ( $_REQUEST['mode'] == 'members' )
{
	$orders = $database->fetchObjectRows( $q = '
		SELECT 
			o.*, c.Mobile, c.Email 
		FROM  
			SBookOrders o 
				LEFT JOIN SBookContact c ON ( c.ID = o.CustomerID ) 
		WHERE 
				o.IsDeleted = "0"
			' . ( isset( $_REQUEST['oid'] ) ? '
			AND o.ID IN (' . $_REQUEST['oid'] . ') 
			' : '
			AND o.CategoryID = \'' . $parent->folder->CategoryID . '\' 
			' ) . '
		ORDER BY 
			o.SortOrder ASC, o.ID ASC 
	' );
}
else
{
	$order = $database->fetchObjectRow( $q = '
		SELECT 
			o.*, c.Mobile, c.Email 
		FROM 
			SBookOrders o
				LEFT JOIN SBookContact c ON ( c.ID = o.CustomerID ) 
		WHERE
				o.ID = \'' . $_REQUEST['oid'] . '\' 
			AND o.IsDeleted = "0" 
		ORDER BY 
			o.SortOrder ASC, o.ID ASC 
	' );
	
	$orders = array( $order );
}

// If you have admin access to the order and this is a group
if ( $parent->folder->MainName == 'Groups' && !isset( $_REQUEST['mode'] ) && isset( CategoryAccess( $webuser->ContactID, $parent->folder->CategoryID )->IsAdmin ) )
{
	$admin = true;
}

if ( isset( $_REQUEST['h'] ) || ( !$admin && !$_REQUEST['mode'] ) )
{
	$hourstatus = array(
		0 => 'AND h.IsReady = "0" AND h.IsAccepted = "0"',
		1 => 'AND h.IsReady >= "1" AND h.IsAccepted = "0"',
		2 => 'AND h.IsReady >= "1" AND h.IsAccepted >= "1"'
	);
	
	$filterhours = $hourstatus[(isset($_REQUEST['h'])?$_REQUEST['h']:0)];
}

if ( $orders )
{
	$groups = array(); $subs = array(); $output = array(); $projects = array();
	
	if ( $_REQUEST['mode'] == 'members' )
	{
		foreach ( $orders as $order )
		{
			if ( $order->ParentID > 0 )
			{
				if ( !isset( $subs[$order->ParentID] ) )
				{
					$subs[$order->ParentID] = array();
				}
				
				$subs[$order->ParentID][] = $order;
			}
			else
			{
				$output[$order->ID] = $order;
			}
		}
		
		
		
		foreach ( $output as $out )
		{
			$projects[] = $out;
			
			if ( isset( $subs[$out->ID] ) )
			{
				foreach ( $subs[$out->ID] as $sub )
				{
					$projects[] = $sub;
				}
			}
		}
	}
	else
	{
		$projects = $orders;
	}
	
	$checkaccess = CategoryAccess( $webuser->ContactID );
	
	foreach ( $projects as $order )
	{
		$IsAdmin = false; $IsModerator = false;
		
		//$webaccess = CategoryAccess( $webuser->ContactID, $order->CategoryID );
		$webaccess = ( isset( $checkaccess[$order->CategoryID][$webuser->ContactID] ) ? $checkaccess[$order->CategoryID][$webuser->ContactID] : false );
		
		if ( $webaccess && $webaccess->IsAdmin )
		{
			$IsAdmin = true;
		}
		else if ( $webaccess && $webaccess->IsModerator && $_REQUEST['h'] == 1 )
		{
			$IsModerator = true;
		}
		
		// TODO: Some Fields only show for admin not even hidden for user...
		
		if ( $admin && ( $tmp1 = $database->fetchObjectRows( '
			SELECT 
				* 
			FROM 
				SBookTemplateFields 
			WHERE 
					`TemplateID` = \'' . ( isset( $order->TemplateID ) ? $order->TemplateID : 1 ) . '\' 
				AND `Target` = "SBookOrders" 
				AND `IsDeleted` = "0" 
			ORDER BY 
				SortOrder ASC, ID ASC 
		' ) ) )
		{
			// --- Order ---------------------------------------------------------------------------------
			
			$td = ''; $hidden = array(); $ii = 1;
			
			foreach ( $tmp1 as $f )
			{
				// If we have a suborder request with some overwrites run them here
				if ( isset( $_REQUEST['newsub'] ) && isset( $_POST[$f->tName] ) && isset( $order->{$f->tName} ) )
				{
					$order->{$f->tName} = $_POST[$f->tName];
					
					if ( $f->Value )
					{
						$f->Value = '';
					}
				}
				
				if ( $output = renderOrderFields( $f, $order, $order, $f->Data, false, $order->ID, $parent->folder->CategoryID, $f->TemplateID, $_REQUEST['s'] ) )
				{
					if ( strstr( $f->Function, 'hidden' ) )
					{
						$hidden[] = $output;
					}
					else
					{
						$td .= '<div class="field nr' . $ii++ . '">';
						$td .= '<div class="label">' . i18n( $f->Heading ) . '</div>';
						$td .= '<div class="value">' . $output . '</div>';
						$td .= '</div>';
					}
				}
			}
			
			$hstr  = '<div class="hidden">';
			$hstr .= ( $hidden ? implode( $hidden ) : '' );
			$hstr .= '</div>';
			
			$odstr .= ( $td ? ( '<div class="order" table="SBookOrders">' . $td . $hstr . '<div style="clear:both" class="clearboth"></div></div>' ) : '' );
		}
		
		// --- Items ----------------------------------------------------------------------------------
		
		// TODO: Hide items list atm until it has been made as hourslist is now ...
		
		if( 1!=1 && !isset( $_REQUEST['newsub'] ) && $order->ID > 0 && $admin && ( $tmp2 = $database->fetchObjectRows( '
			SELECT 
				* 
			FROM 
				SBookTemplateFields 
			WHERE 
					`TemplateID` = \'' . ( isset( $order->TemplateID ) ? $order->TemplateID : 1 ) . '\' 
				AND `Target` = "SBookOrderItems" 
				AND `IsDeleted` = "0" 
			ORDER BY 
				SortOrder ASC, ID ASC 
		' ) ) )
		{
			$ii = 1;
			
			//$odstr .= '<div class="products" table="SBookOrderItems"><table>';
			$odstr .= '<div class="products"><table>';
			
			$odstr .= '<tr>';
			
			foreach ( $tmp2 as $head )
			{
				$odstr .= '<td class="col' . $ii++ . '">' . i18n( $head->Heading ) . '</td>';
			}
			
			$odstr .= '</tr>';
			
			if ( $items = $database->fetchObjectRows( '
				SELECT 
					* 
				FROM 
					SBookOrderItems 
				WHERE 
						`OrderID` = \'' . ( isset( $order->ID ) ? $order->ID : '' ) . '\' 
					AND `IsDeleted` = "0" 
				ORDER BY 
					SortOrder ASC, ID ASC 
			' ) )
			{
				$group = array();
				
				foreach ( $items as $i )
				{
					$td = ''; $ii = 1;
					
					foreach ( $tmp2 as $f )
					{
						if ( $output = renderOrderFields( $f, $i, $i, $f->Data, false, $i->ID, $parent->folder->CategoryID, $f->TemplateID, $_REQUEST['s'] ) )
						{
							$td .= '<td class="col' . $ii++ . '">' . $output . '</td>';
						}
					}
					
					$odstr .= ( $td ? ( '<tr>' . $td . '</tr>' ) : '' );
				}
			}
			
			// --- Items editor ------------------------------------------------------------------
			
			$ii = 1;
			
			$odstr .= '<tr>';
			
			foreach( $tmp2 as $f )
			{
				if ( $output = renderOrderFields( $f, false, false, $f->Data, false, false, $parent->folder->CategoryID, $f->TemplateID, $_REQUEST['s'] ) )
				{
					$odstr .= '<td class="col' . $ii++ . '">' . $output . '</td>';
				}		
			}
			
			$odstr .= '</tr>';
			
			$odstr .= '</table></div>';
		}
		
		// --- Hours ----------------------------------------------------------------------------------
		
		if ( !isset( $_REQUEST['newsub'] ) && $order->ID > 0 && ( $tmp3 = $database->fetchObjectRows( '
			SELECT 
				* 
			FROM 
				SBookTemplateFields 
			WHERE 
					`TemplateID` = \'' . ( isset( $order->TemplateID ) ? $order->TemplateID : 1 ) . '\' 
				AND `Target` = "SBookHours"
				AND `IsDeleted` = "0" 
			ORDER BY 
				SortOrder ASC, ID ASC 
		' ) ) )
		{
			$hdstr = '';
			
			$hdstr  = '<div class="hours' . ( isset( $_REQUEST['mode'] ) ? ( ' ' . $_REQUEST['mode'] ) : '' ) . '">';
			$hdstr .= '<div class="heading"><div>';
			
			
			
			$hdstr .= '<div class="cols"><table><tr>';
			$hdstr .= '<td class="col1"><div></div></td>';
			
			$ii = 2; 
			
			foreach ( $tmp3 as $head )
			{
				if ( $head->Heading )
				{
					// If this is only for admin, hide it if this user is not admin
					if ( ( $head->Access == 2 && !$admin ) || strstr( $head->Function, 'extended' ) )
					{
						$hdstr .= '';
					}
					else
					{
						$hdstr .= '<td class="col' . $ii++ . '">' . i18n( $head->Heading ) . '</td>';
					}
				}
			}
			
			$hdstr .= '<td class="hidden"><div></div></td>';
			$hdstr .= '</tr></table></div></div></div>';
			
			
			
			$hours = array(); $group = array();
			
			if ( $items = $database->fetchObjectRows( $q = '
				SELECT 
					h.* 
				FROM 
					SBookHours h 
				WHERE 
						h.ProjectID = \'' . ( isset( $order->ID ) ? $order->ID : '' ) . '\'
					AND h.Title = "Orders" 
					AND h.IsDeleted = "0" 
					' . ( ( !$admin && $_REQUEST['h'] <= 0 ) || ( !$IsAdmin && !$IsModerator ) || $_REQUEST['mode'] == 'members' ? '
					AND h.UserID = \'' . ( isset( $_REQUEST['uid'] ) ? $_REQUEST['uid'] : $webuser->ContactID ) . '\'
					' : '' ) . '
					' . ( $fromdate ? 'AND h.DateStart >= \'' . $fromdate . '\' ' : '' ) . '
					' . ( $todate ? 'AND h.DateStart <= \'' . $todate . '\' ' : '' ) . '
					' . ( $filterhours ? $filterhours : '' ) . '
				ORDER BY 
					h.DateStart ASC 
			' ) )
			{
				$users = array(); $tothours = array();
				
				foreach ( $items as $usr )
				{
					if ( $usr->UserID > 0 )
					{
						$users[$usr->UserID] = $usr->UserID;
						
						if ( !isset( $tothours[$usr->UserID] ) )
						{
							$tothours[$usr->UserID] = array();
						}
						
						if ( !isset( $tothours[$usr->UserID][date('Ymd',strtotime($usr->DateStart))] ) )
						{
							$obj = new stdClass();
							$obj->Hours = array();
							$obj->Total = array();
							
							$tothours[$usr->UserID][date('Ymd',strtotime($usr->DateStart))] = $obj;
						}
						
						$usr->Type = ( !$usr->Type ? 0 : $usr->Type );
						
						$tothours[$usr->UserID][date('Ymd',strtotime($usr->DateStart))]->Hours[] = $usr->Hours;
						$tothours[$usr->UserID][date('Ymd',strtotime($usr->DateStart))]->Total[$usr->Type] = ( isset( $tothours[$usr->UserID][date('Ymd',strtotime($usr->DateStart))]->Total[$usr->Type] ) ? $tothours[$usr->UserID][date('Ymd',strtotime($usr->DateStart))]->Total[$usr->Type] + $usr->Hours : $usr->Hours );
					}
				}
				
				$useraccess = CategoryAccess( $users, $order->CategoryID );
				
				$usrnm = GetUserDisplayname( $users );
				
				foreach ( $items as $i )
				{
					$hasAccess = false; $totalHours = false;
					
					
					$hdstr  = '<div class="hours' . ( isset( $_REQUEST['mode'] ) ? ( ' ' . $_REQUEST['mode'] ) : '' ) . '">';
					$hdstr .= '<div class="heading"><div>';
					
					if ( isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] == 'members' )
					{
						$hdstr .= '<div class="name">' . $order->F6 . ' (' . ( $order->OrderID > 0 ? $order->OrderID : $order->ID ) . ( $order->JobID ? ( '-' . $order->JobID ) : '' ) . ') ' . '</div>';
					}
					else
					{
						if ( $_REQUEST['h'] >= 1 )
						{
							$hdstr .= '<div class="name">' . ( isset( $usrnm[$i->UserID] ) ? $usrnm[$i->UserID] : $i->UserID ) . '</div>';
						}
					}
					
					$hdstr .= '<div class="cols"><table><tr>';
					$hdstr .= '<td class="col1"><div></div></td>';
					
					$ii = 2; 
					
					foreach ( $tmp3 as $head )
					{
						if ( $head->Heading )
						{
							// If this is only for admin, hide it if this user is not admin
							if ( ( $head->Access == 2 && !$admin ) || strstr( $head->Function, 'extended' ) )
							{
								$hdstr .= '';
							}
							else
							{
								$hdstr .= '<td class="col' . $ii++ . '">' . i18n( $head->Heading ) . '</td>';
							}
						}
					}
					
					$hdstr .= '<td class="hidden"><div></div></td>';
					$hdstr .= '</tr></table></div></div></div>';
					
					
					
					// TODO: Check who is owner of this order and prioritize him, also look at people with same permission level
					
					//if ( $_REQUEST['uid'] == 22 ) die( 'WebAccess: ' . print_r( $webaccess,1 ) . ' || UserAccess: ' . print_r( $useraccess,1 ) . ' || ' . print_r( $users,1 ) );
					
					// TODO: Find a way to add all projects in a group even if one doesn't have access to it, also if some hours are accepted and some are not, until all of them are accepted.
					
					// TODO: If the user has higher access don't show this users pending hours.
					
					// TODO: Show user for moderator also on hour form.
					
					if ( isset( $webaccess->Settings->Permission )
						&& ( !isset( $useraccess[$i->UserID]->Settings->Permission ) 
						|| $webaccess->ContactID == $useraccess[$i->UserID]->ContactID 
						|| IsSystemAdmin() 
						|| $webaccess->ContactID == $order->UserID 
						|| $webaccess->Settings->Permission > $useraccess[$i->UserID]->Settings->Permission ) 
					)
					{
						$hasAccess = $webaccess;
						
						if( $webaccess->IsModerator && $_REQUEST['h'] == 2 )
						{
							$hasAccess = false;
						}
					}
					
					if ( !checkOrderAccess( $i->UserID, $order->Data ) )
					{
						$hasAccess = false;
					}
					
					//die( $i->UserID . ' [] ' . $order->Data . ' [] ' . print_r( $hasAccess,1 ) . ' [] ' . $webuser->ContactID );
					
					if ( isset( $tothours[$i->UserID][date('Ymd',strtotime($i->DateStart))]->Total[$i->Type] ) )
					{
						$totalHours = $tothours[$i->UserID][date('Ymd',strtotime($i->DateStart))]->Total[$i->Type];
					}
					
					$td = ''; $ii = 2; $ext = 1; $sw = 2; $hidden = array(); $extended = array();
					
					$td .= '<td class="col1"><div class="toggle" onclick="openHourList( this )"><span>[+]</span></div></td>';
					
					foreach ( $tmp3 as $f )
					{
						if ( $output = renderOrderFields( $f, $order, $i, $f->Data, /*'onkeyup="if(event.keyCode==13){SaveHour(this,\''.$order->ID.'\')}"'*/false, $i->ID, $parent->folder->CategoryID, $f->TemplateID, $_REQUEST['s'] ) )
						{
							if ( strstr( $f->Function, 'hidden' ) || ( $f->Access == 2 && !$IsAdmin ) )
							{
								$hidden[] = $output;
							}
							else if ( strstr( $f->Function, 'extended' ) )
							{
								$extstr  = '<div class="field nr' . $ext++ . ' sw' . ( $sw = ( $sw == 2 ? 1 : 2 ) ) . '"><table><tr>';
								$extstr .= '<td class="label">' . i18n( $f->Heading ) . '</td>';
								$extstr .= '<td class="value">' . $output . '</td>';
								$extstr .= '</tr></table></div>';
								
								$extended[] = $extstr;
							}
							else
							{
								$td .= '<td class="col' . $ii++ . '">' . $output . '</td>';
							}
						}
					}
					
					$hstr  = '<div class="hidden">';
					$hstr .= '<input type="hidden" value="orders" name="Title"/>';
					$hstr .= ( $hidden ? implode( $hidden ) : '' );
					$hstr .= '</div>';
					
					$dstr  = '<div class="description" onclick="openHourEditor( this )">';
					
					if ( ( $hasAccess && ( $i->IsReady || $parent->folder->MainName != 'Groups' ) && ( $_REQUEST['h'] == 0 || $hasAccess->IsAdmin || $hasAccess->IsModerator ) ) )
					{
						if ( $_REQUEST['h'] > 1 || $parent->folder->MainName == 'Groups' )
						{
							$dstr .= '<span class="checked"> <input type="checkbox" value="' . $order->ID . '_' . $i->ID . '" onclick="CheckHour(this,event,true)"' . ( $i->IsAccepted ? ' checked="checked"' : '' ) . ( $i->IsFinished ? ' disabled="true"' : '' ) . '> </span>';
						}
						else
						{
							$dstr .= '<span class="checked"> <input type="checkbox" value="' . $i->ID . '" onclick="CheckThis(this,event)"' . ( $i->IsAccepted ? ' checked="checked"' : '' ) . ( $i->IsFinished ? ' disabled="true"' : '' ) . '> </span>';
						}
					}
					
					$dstr .= ( $admin ? '<span class="user"> ' . ( isset( $usrnm[$i->UserID] ) ? $usrnm[$i->UserID] : $i->UserID ) . ' </span>' : '' );
					$dstr .= '<span class="hours' . ( $i->Hours > 7.5 || ( $totalHours && $totalHours > 7.5 ) ? ' alert' : '' ) . '"> ' . ( $i->Type ? $i->Type . '% - ' : '' ) . '(' . $i->Hours . i18n( 'h' ) . ') </span>';
					$dstr .= '<span class="date"> ' . i18n( date( 'D', strtotime( $i->DateStart ) ) ) . date( ', d.m.Y', strtotime( $i->DateStart ) ) . ' </span>';
					//$dstr .= '<span class="info" onclick="HourHistory(\'' . $i->ID . '\',\'' . $order->ID . '\',event)"> (i) </span>';
					$dstr .= '</div>';
					
					$btn  = '<div class="toolbar"><div class="buttons">';
					
					if ( $i->IsFinished )
					{
						if ( $hasAccess && $hasAccess->IsAdmin )
						{
							$btn .= '<button class="btn_open" type="button" onclick="OpenHour(\'' . $i->ID . '\',\'' . $order->ID . '\',\'' . $i->UserID . '\'' . ( $_REQUEST['fromdate'] && $_REQUEST['todate'] ? ',\'' . $_REQUEST['fromdate'] . '\',\'' . $_REQUEST['todate'] . '\'' : '' ) . ')">' . i18n( 'Open' ) . '</button>';
						}
					}
					else
					{
						if ( $hasAccess && ( ( !$i->IsAccepted && !$i->IsReady ) || $hasAccess->IsAdmin/* || $hasAccess->IsModerator*/ ) )
						{
							if ( $hasAccess->Delete || $_REQUEST['h'] <= 0 )
							{
								$btn .= '<button class="btn_delete" type="button"  style="background:transparent; color: #444;" onclick="DeleteHour(\'' . $i->ID . '\',\'' . $order->ID . '\',\'' . $i->UserID . '\'' . ( $_REQUEST['fromdate'] && $_REQUEST['todate'] ? ',\'' . $_REQUEST['fromdate'] . '\',\'' . $_REQUEST['todate'] . '\'' : '' ) . ')">' . i18n( 'Delete' ) . '</button>';
							}
							//$btn .= '<button class="btn_delete" type="button" onclick="DeleteHour(\'' . $i->ID . '\',\'' . $order->ID . '\')">' . i18n( 'Delete' ) . '</button>';
							//$btn .= '<div class="btn_separator"></div>';
							if ( $hasAccess->Write || $_REQUEST['h'] <= 0 )
							{
								$btn .= '<button class="btn_save" type="button" onclick="SaveHour(false,\'' . $order->ID . '\',\'' . $i->ID . '\',\'' . $i->UserID . '\'' . ( $_REQUEST['fromdate'] && $_REQUEST['todate'] ? ',\'' . $_REQUEST['fromdate'] . '\',\'' . $_REQUEST['todate'] . '\'' : '' ) . ')">' . i18n( 'Save' ) . '</button>';
							}
						}
					}
					
					$btn .= '<button class="btn_close" type="button" onclick="openHourEditor(false,\'' . ( $order->ID . '_' . $i->ID ) . '\')">' . i18n( 'Close' ) . '</button>';
					$btn .= '</div></div>';
					
					$estr  = '<div class="inner">' . ( $extended ? implode( $extended ) : '' ) . '<div class="clearboth" style="clear:both"></div></div>' . $btn;
					
					$hrstr = ( $td ? ( '<div class="hour closed' . ( !$i->IsFinished ? ( $i->IsAccepted ? ' allowed' : ( $i->IsReady ? ' pending' : '' ) ) : '' ) . ( $i->IsFinished ? ' finished' : '' ) . '" id="HourID_' . $order->ID . '_' . $i->ID . '"' . ( $i->GroupID ? ( ' groupid="' . $i->GroupID . '"' ) : '' ) . '>' . $dstr . '<div class="table"><div><table><tr>' . $td . '</tr></table></div></div>' . $estr . $hstr . '</div>' ) : '' );
					
					if ( $i->GroupID && $parent->folder->MainName != 'Groups' )
					{
						// Assign to group
						
						if ( $i->GroupID && !isset( $group[$i->GroupID] ) )
						{
							$group[$i->GroupID] = array();
							
							$obj = new stdClass();
							$obj->Value = $hdstr;
							
							$group[$i->GroupID][] = $obj;
						}
						
						$obj = new stdClass();
						$obj->DateStart = $i->DateStart;
						$obj->DateEnd = $i->DateEnd;
						$obj->Hours = $i->Hours;
						$obj->Hours50 = $i->Hours50;
						$obj->Hours100 = $i->Hours100;
						$obj->GroupID = $i->GroupID;
						$obj->Value = $hrstr;
						
						$group[$i->GroupID][] = $obj;
					}
					else
					{
						$hours[] = $hrstr;
					}
				}
			}
			
			// --- Hours editor ----------------------------------------------------------------------------
			
			if ( $_REQUEST['mode'] != 'members' && $_REQUEST['h'] <= 0 )
			{
				$ii = 2; $ext = 1; $sw = 2; $td = ''; $hidden = array(); $extended = array();
				
				$td .= '<td class="col1"><div class="toggle" onclick="openHourList( this )"><span>[+]</span></div></td>';
				
				foreach ( $tmp3 as $f )
				{
					if ( $output = renderOrderFields( $f, $order, false, $f->Data, /*'onkeyup="if(event.keyCode==13){SaveHour(this,\''.$order->ID.'\')}"'*/false, false, $parent->folder->CategoryID, $f->TemplateID, $_REQUEST['s'] ) )
					{
						if ( strstr( $f->Function, 'hidden' ) || ( $f->Access == 2 && !$IsAdmin ) )
						{
							$hidden[] = $output;
						}
						else if ( strstr( $f->Function, 'extended' ) )
						{
							$extstr  = '<div class="field nr' . $ext++ . ' sw' . ( $sw = ( $sw == 2 ? 1 : 2 ) ) . '"><table><tr>';
							$extstr .= '<td class="label">' . i18n( $f->Heading ) . '</td>';
							$extstr .= '<td class="value">' . $output . '</td>';
							$extstr .= '</tr></table></div>';
							
							$extended[] = $extstr;
						}
						else
						{
							$td .= '<td class="col' . $ii++ . '">' . $output . '</td>';
						}
					}
				}
				
				$hstr  = '<div class="hidden">';
				$hstr .= '<input type="hidden" value="orders" name="Title"/>';
				$hstr .= ( $hidden ? implode( $hidden ) : '' );
				$hstr .= '</div>';
				
				$dstr  = '<div class="description" onclick="openHourEditor( this )">' . i18n( 'Add hours' ) . '</div>';
				
				$btn  = '<div class="toolbar"><div class="buttons">';
				$btn .= '<button class="btn_save" type="button" onclick="SaveHour(false,\'' . $order->ID . '\')">' . i18n( 'Save' ) . '</button>';
				$btn .= '<button class="btn_close" type="button" onclick="openHourEditor(false,\'' . $order->ID . '_0\')">' . i18n( 'Close' ) . '</button>';
				$btn .= '</div></div>';
				
				$estr  = '<div class="inner">' . ( $extended ? implode( $extended ) : '' ) . '<div class="clearboth" style="clear:both"></div></div>' . $btn;
				
				$hours[] = ( $td ? ( '<div class="editor closed" id="HourID_' . $order->ID . '_0">' . $dstr . '<div class="table"><div><table><tr>' . $td . '</tr></table></div></div>' . $estr . $hstr . '</div>' ) : '' );
			}
			
			if ( $hours )
			{
				// Add heading if hours found
				$odstr .= $hdstr;
				
				$odstr .= ( $hours ? implode( $hours ) : '' );
				
				$odstr .= '</div>';
			}
			
			if ( $group )
			{
				foreach( $group as $key=>$val )
				{
					if ( $val && is_array( $val ) )
					{
						if ( !isset( $groups[$key] ) )
						{
							$groups[$key] = new stdClass();
						}
						
						foreach( $val as $k=>$v )
						{
							$groups[$key]->DateStart = ( !$groups[$key]->DateStart ? $v->DateStart : $groups[$key]->DateStart );
							$groups[$key]->DateEnd = ( $v->DateEnd ? $v->DateEnd : $groups[$key]->DateEnd );
							
							$groups[$key]->Hours = ( $groups[$key]->Hours ? ( $groups[$key]->Hours + $v->Hours ) : $v->Hours );
							$groups[$key]->Hours50 = ( $groups[$key]->Hours50 ? ( $groups[$key]->Hours50 + $v->Hours50 ) : $v->Hours50 );
							$groups[$key]->Hours100 = ( $groups[$key]->Hours100 ? ( $groups[$key]->Hours100 + $v->Hours100 ) : $v->Hours100 );
							
							$groups[$key]->GroupID = $v->GroupID;
							$groups[$key]->Value = ( isset( $groups[$key]->Value ) ? ( $groups[$key]->Value . $v->Value ) : $v->Value );
						}
						
						$groups[$key]->Value = ( isset( $groups[$key]->Value ) ? ( $groups[$key]->Value . '</div>' ) : '</div>' );
					}
				}
			}
		}
		
		// --- Events -------------------------------------------------------------------------------------
		
	}
	
	if ( $groups )
	{
		$iii = 1;
		
		$gcolor = ( isset( $_REQUEST['h'] ) && $_REQUEST['h'] == 2 ? 'green' : 'orange' );
		
		foreach( $groups as $key=>$val )
		{
			$url = 'window.open(\'' . BASE_URL . 'secure-files/files/' . $key . '/\'' . ',\'_blank\')';
			
			$odstr .= '<div style="border:2px dashed ' . $gcolor . ';margin-top:5px;margin-bottom:5px;" class="group closed" groupid="' . $key . '">';
			if ( 1==1 && ( $_REQUEST['h'] != 2 || $webuser->ContactID == 18 || $webuser->ContactID == 1 ) )
			{
				$odstr .= '<div style="float:right;padding:12px;padding-bottom:0;cursor:pointer;" onclick="OrderMemberExport(\'' . $key . '\',\'pdf\')"><img src="admin/gfx/icons/page_white_acrobat.png"></div>';
			}
			else if ( $url && $_REQUEST['h'] != 2 )
			{
				$odstr .= '<div style="float:right;padding:12px;padding-bottom:0;cursor:pointer;" onclick="' . $url . '"><img src="admin/gfx/icons/page_white_acrobat.png"></div>';
			}
			$odstr .= '<div class="description" onclick="openHourGroup(this)">Uke ' . ( $val->DateStart ? date( 'W, Y', strtotime( $val->DateStart ) ) : '' ) . ' (' . ( $val->Hours ? $val->Hours : 0 ) . 't)' . '</div>';
			$odstr .= '<div class="group_inner">' . $val->Value . '</div></div>';
		}
	}
	
	// Buttons to save order for admin etc
	
	if ( $admin && $_REQUEST['mode'] != 'members' )
	{
		$btn  = '<div class="toolbar"><div class="buttons">';
		
		if ( !isset( $_REQUEST['newsub'] ) && $_REQUEST['oid'] )
		{
			$btn .= '<button class="btn_delete" type="button" onclick="DeleteOrder(' . $_REQUEST['oid'] . ')" style="background:transparent; color: #444;">' . i18n( 'Delete' ) . '</button>';
		}
		
		if ( isset( $_REQUEST['newsub'] ) && $_REQUEST['oid'] )
		{
			$btn .= '<button class="btn_save" type="button" onclick="SaveOrder(\'' . $_REQUEST['oid'] . '\',true)">' . i18n( 'Save' ) . '</button>';
			$btn .= '<button class="btn_close" type="button" onclick="EditOrder(\'' . $_REQUEST['oid'] . '\',false,true)">' . i18n( 'Close' ) . '</button>';
		}
		else
		{
			$btn .= '<button class="btn_save" type="button" onclick="SaveOrder(\'' . ( $_REQUEST['oid'] ? $_REQUEST['oid'] : '0' ) . '\')">' . i18n( 'Save' ) . '</button>';
			$btn .= '<button class="btn_close" type="button" onclick="EditOrder(\'' . ( $_REQUEST['oid'] ? $_REQUEST['oid'] : '0' ) . '\')">' . i18n( 'Close' ) . '</button>';
		}
		
		$btn .= '</div></div>';
		
		if ( $btn )
		{
			$odstr .= $btn;
		}
	}
}

if ( isset( $_REQUEST['bajaxrand'] ) )
{
	if ( $odstr )
	{
		die( 'ok<!--separate-->' . $odstr );
	}

	die( 'fail' );
}

?>
