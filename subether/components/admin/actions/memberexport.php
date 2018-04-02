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

if ( $_REQUEST['fromdate'] && $_REQUEST['todate'] )
{
	$fromdate 	= date( 'Y-m-d 00:00:00.000000', $_REQUEST['fromdate'] );
	$todate 	= date( 'Y-m-d 23:59:59.000000', $_REQUEST['todate'] );
}

// TODO: Output data based on GroupID with save option, and get saved file if saved request is set and the file or db info exists

// TODO: Check based on GroupID on categoryid info

if ( isset( $_REQUEST['groupid'] ) )
{
	$query = '
		SELECT 
			h.*, o.CategoryID 
		FROM
			SBookOrders o,
			SBookHours h
		WHERE
				h.ProjectID = o.ID
			AND h.GroupID = \'' . $_REQUEST['groupid'] . '\'
			AND h.IsDeleted = "0" 
		ORDER BY 
			h.ID ASC 
	';
}
else
{
	$query = '
		SELECT 
			c.*, r.CategoryID 
		FROM 
			SBookCategoryRelation r, 
			SBookContact c 
		WHERE 
				r.CategoryID = \'' . $parent->folder->CategoryID . '\' 
			AND r.ObjectType = "Users" 
			AND r.ObjectID > 0 
			AND c.UserID = r.ObjectID 
			AND c.NodeID = "0" 
			' . ( isset( $_REQUEST['mid'] ) ? ( '
			AND c.ID IN (' . mysql_real_escape_string( $_REQUEST['mid'] ) . ')
			' ) : '' ) . '
		GROUP BY
			c.ID 
		ORDER BY 
			c.ID ASC 
	';
}

if ( $rows = $database->fetchObjectRows( $query ) )
{
	$usrs = array(); $catids = ''; $hrs = array();
	
	if ( isset( $_REQUEST['groupid'] ) )
	{
		foreach ( $rows as $row )
		{
			$usrs[$row->UserID] = $row->UserID;
			$catids = ( $catids && !strstr( $catids, $row->CategoryID ) ? ( $catids . ',' . $row->CategoryID ) : $row->CategoryID );
		}
	}
	else
	{
		foreach ( $rows as $row )
		{
			$usrs[$row->ID] = $row->ID;
			$catids = $row->CategoryID;
		}
	}
	
	//$cats = CategoryAccess( $usrs, $parent->folder->CategoryID );
	$cats = CategoryAccess( $usrs, $catids );
	
	// --- BE AWARE DESPO CODE, TOO LITTLE TIME .............................................................
	
	if ( $usrs && ( $events = $database->fetchObjectRows( $q = '
		SELECT 
			h.*, a.MemberID, o.OrderID, o.JobID, o.CustomerID, o.F6 AS ProjectName, o.Data 
		FROM 
			SBookOrders o,
			SBookCategoryAccess a, 
			SBookHours h 
		WHERE
				h.ProjectID = o.ID
			' . ( isset( $_REQUEST['groupid'] ) ? '
			AND h.GroupID = \'' . $_REQUEST['groupid'] . '\' 
			' : '
			AND h.IsAccepted >= "1"
			AND o.CategoryID IN (' . $catids . ') 
			' ) . '
			AND h.UserID IN (' . implode( ',', $usrs ) . ')
			' . ( isset( $_REQUEST['hid'] ) ? '
			AND h.ID IN (' . $_REQUEST['hid'] . ')
			' : '' ) . '
			AND a.UserID = h.UserID 
			AND a.CategoryID = o.CategoryID 
			' . ( $fromdate ? '
			AND h.DateStart >= \'' . $fromdate . '\'
			' : '' ) . '
			' . ( $todate ? '
			AND h.DateEnd <= \'' . $todate . '\'
			' : '' ) . '
		ORDER BY 
			h.DateStart ASC, o.ID ASC 
	' ) ) )
	{
		$account = array(); $pts = array(); $prn = array();
		
		// Parent Settings
		if ( $pset = $database->fetchObjectRows( /*'
			SELECT 
				a.*,
				c.ID AS GroupID,
				c.ParentID,
				p.Name AS ParentName,
				p.Settings AS ParentSettings
			FROM 
				SBookCategory c, 
				SBookCategory p,
				SBookCategoryAccess a 
			WHERE
					c.ID IN (' . $catids . ') 
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
		'*/'
			SELECT 
				c.*,
				c.ID AS GroupID,
				c.ParentID,
				p.Name AS ParentName,
				p.Settings AS ParentSettings
			FROM 
				SBookCategory c
					LEFT JOIN SBookCategory p ON
					(
							c.ParentID > 0 
						AND p.ID = c.ParentID 
						AND p.Type = "SubGroup" 
						AND p.IsSystem = "0" 
						AND p.ParentID = "0" 
					)
			WHERE
					c.ID IN (' . $catids . ') 
				AND c.Type = "SubGroup"
				AND c.IsSystem = "0" 
			ORDER BY
				c.ID ASC 
		' ) )
		{
			//die( print_r( $pset,1 ) . ' --' );
			foreach ( $pset as $ps )
			{
				if ( $ps->ParentSettings && is_string( $ps->ParentSettings ) )
				{
					$ps->ParentSettings = json_decode( $ps->ParentSettings );
				}
				
				//$prn[$ps->GroupID] = $ps;
				$prn[$ps->GroupID] = $ps;
				
				if ( $ps->GroupID )
				{
					if ( $sbacc = $database->fetchObjectRows( '
						SELECT 
							*
						FROM 
							SBookCategoryAccess
						WHERE
								CategoryID = \'' . ( $ps->ParentID > 0 ? $ps->ParentID : $ps->GroupID ) . '\'
							AND CategoryID > 0 
						ORDER BY
							ID ASC
					' ) )
					{
						//die( print_r( $sbacc,1 ) . ' --' );
						
						foreach( $sbacc as $sb )
						{
							if ( $sb->ContactID > 0 )
							{
								//die( print_r( $ps,1 ) . ' --' );
								
								if ( $ps->Settings && is_string( $ps->Settings ) )
								{
									$sb->Settings = json_decode( $ps->Settings );
								}
								
								if ( $ps->ParentSettings )
								{
									$sb->ParentSettings = $ps->ParentSettings;
								}
								
								//$pts[$ps->ContactID] = $ps->MemberID;
								$pts[$sb->ContactID] = $sb;
							}
						}
					}
				}
				
				// TODO: Add listout of accounting types to the output of the files
				if ( $accset = $database->fetchObjectRows( '
					SELECT 
						*
					FROM 
						SBookAccountingSettings
					WHERE
							CategoryID = \'' . ( $ps->ParentID > 0 ? $ps->ParentID : $ps->GroupID ) . '\'
						AND CategoryID > 0 
					ORDER BY
						ID ASC
				' ) )
				{
					//die( print_r( $accset,1 ) . ' -- ' );
					foreach( $accset as $acc )
					{
						$account[$acc->ID] = $acc;
					}
				}
			}
		}
		/*else
		{
			// TODO: There is no ParentID .... fix when time ...
			// TODO: Add listout of accounting types to the output of the files
			if ( $accset = $database->fetchObjectRows( '
				SELECT 
					*
				FROM 
					SBookAccountingSettings
				WHERE
						' . ( $row->ParentID > 0 ? '
						CategoryID = \'' . $row->ParentID . '\'
						' : '
						CategoryID IN (' . $catids . ') 
						' ) . '
					AND CategoryID > 0 
				ORDER BY
					ID ASC
			' ) )
			{
				foreach( $accset as $acc )
				{
					$account[$acc->ID] = $acc;
				}
			}
		}*/
		//die( $catids . ' [] ' . print_r( $prn,1 ) . ' -- ' . print_r( $account,1 ) . ' -- ' . print_r( $pts,1 ) );
		// TODO: Connect field to accounting one per field (KM, Parking, Toll)
		
		// TODO: Figure out this dynamic accounting types matched to output fields ...
		
		// TODO: Make this dynamic, temporary static
		
		$options = array(
			'Hours'=>'Hours-0-T-1',
			'Hours-50'=>'Hours50-0-T-1',
			'Hours-100'=>'Hours100-0-T-1',
			'Hours-Addition'=>'Addition-0-T-1',
			'Product-Toll'=>'F2-0-V-2',
			'Product-Parking'=>'F3-0-V-2',
			'Product-KM'=>'F4-0-V-2'
		);
		
		foreach ( $events as $evt )
		{
			if ( $evt->UserID > 0 )
			{
				//die( print_r( $evt,1 ) . ' --' );
				
				if ( !isset( $hrs[$evt->UserID] ) )
				{
					$obj = new stdClass();
					$obj->UserID = $evt->UserID;
					$obj->MemberID = ( isset( $pts[$evt->UserID]->MemberID ) ? $pts[$evt->UserID]->MemberID : $evt->MemberID );
					$obj->IsReady = ( $evt->IsReady >= 1 ? GetUserDisplayname( $evt->IsReady ) : $evt->IsReady );
					$obj->IsAccepted = ( $evt->IsAccepted >= 1 ? GetUserDisplayname( $evt->IsAccepted ) : $evt->IsAccepted );
					$obj->IsFinished = ( $evt->IsFinished >= 1 ? GetUserDisplayname( $evt->IsFinished ) : $evt->IsFinished );
					
					if ( isset( $cats[$evt->UserID]->Settings ) )
					{
						$overwrite = new stdClass();
						
						$sts = $cats[$evt->UserID]->Settings;
						
						// --- Overwrite access based on project ------------------------------------------------
						
						if ( $evt->Data )
						{
							$dta = json_decode( $evt->Data );
							
							//die( print_r( $data,1 ) . ' -- ' . print_r( $cats[$evt->UserID],1 ) );
							
							$access = array();
							
							if ( isset( $pts[$evt->UserID]->ParentSettings->AccessLevels ) && is_array( $pts[$evt->UserID]->ParentSettings->AccessLevels ) )
							{
								foreach( $pts[$evt->UserID]->ParentSettings->AccessLevels as $lvl )
								{
									if ( $lvl->ID )
									{
										$access[$lvl->ID] = $lvl;
									}
								}
							}
							else if ( isset( $pts[$evt->UserID]->Settings->AccessLevels ) && is_array( $pts[$evt->UserID]->Settings->AccessLevels ) )
							{
								foreach( $pts[$evt->UserID]->Settings->AccessLevels as $lvl )
								{
									if ( $lvl->ID )
									{
										$access[$lvl->ID] = $lvl;
									}
								}
							}
							
							/*if ( isset( $cats[$evt->UserID]->ParentSettings->AccessLevels ) && is_array( $cats[$evt->UserID]->ParentSettings->AccessLevels ) )
							{
								foreach( $cats[$evt->UserID]->ParentSettings->AccessLevels as $lvl )
								{
									if ( $lvl->ID )
									{
										$access[$lvl->ID] = $lvl;
									}
								}
							}
							else if ( isset( $cats[$evt->UserID]->Settings->AccessLevels ) && is_array( $cats[$evt->UserID]->Settings->AccessLevels ) )
							{
								foreach( $cats[$evt->UserID]->Settings->AccessLevels as $lvl )
								{
									if ( $lvl->ID )
									{
										$access[$lvl->ID] = $lvl;
									}
								}
							}*/
							
							if ( is_object( $dta ) && $access )
							{
								foreach( $dta as $k=>$v )
								{
									if ( $v && strstr( ( ','.(string)$v.',' ), ( ','.(string)$evt->UserID.',' ) ) && isset( $access[$k] ) )
									{
										$overwrite = $access[$k];
									}
								}
							}
						}
						//die( print_r( $cats,1 ) . ' -- ' . $evt->UserID . ' [] ' . $evt->CategoryID . ' [] ' . $evt->Data . ' [] ' . print_r( $prn,1 ) . ' || ' . print_r( $pts,1 ) );
						//die( print_r( $overwrite,1 ) . ' --' );
						$obj->Code = ( isset( $overwrite->ID ) ? $overwrite->ID : $sts->ID );
						$obj->Display = ( isset( $overwrite->Display ) ? $overwrite->Display : $sts->Display );
						$obj->Adittion50 = $sts->Adittion50;
						$obj->Adittion100 = $sts->Adittion100;
						
						if ( $overwrite && isset( $overwrite->ID ) )
						{
							if ( $overwrite->Accounting )
							{
								$obj->Accounting = explode( ',', $overwrite->Accounting );
							}
							
						}
						else if ( isset( $sts->Accounting ) )
						{
							$obj->Accounting = explode( ',', $sts->Accounting );
						}
					}
					
					$obj->Date = strtotime( date( 'Y-m-d H:i:s' ) );
					$obj->Projects = array();
					$hrs[$evt->UserID] = $obj;
				}
				
				//$evt->Name = ( $evt->ProjectName ? $evt->ProjectName : $evt->Name );
				$evt->Name = ( $evt->CustomerID ? $evt->CustomerID : $evt->Name );
				
				if ( $evt->DateEnd != '0000-00-00 00:00:00' && $evt->DateStart != '0000-00-00 00:00:00' )
				{
					// Time difference in hours
					$diffInHours = ( ( strtotime( $evt->DateEnd ) - strtotime( $evt->DateStart ) ) / 3600 );
					
					$access = array(); $overwrite = new stdClass();
					
					$dta = json_decode( $evt->Data );
					
					if ( isset( $pts[$evt->UserID]->ParentSettings->AccessLevels ) && is_array( $pts[$evt->UserID]->ParentSettings->AccessLevels ) )
					{
						foreach( $pts[$evt->UserID]->ParentSettings->AccessLevels as $lvl )
						{
							if ( $lvl->ID )
							{
								$access[$lvl->ID] = $lvl;
							}
						}
					}
					else if ( isset( $pts[$evt->UserID]->Settings->AccessLevels ) && is_array( $pts[$evt->UserID]->Settings->AccessLevels ) )
					{
						foreach( $pts[$evt->UserID]->Settings->AccessLevels as $lvl )
						{
							if ( $lvl->ID )
							{
								$access[$lvl->ID] = $lvl;
							}
						}
					}
					
					if ( is_object( $dta ) && $access )
					{
						foreach( $dta as $k=>$v )
						{
							if ( $v && strstr( ( ','.(string)$v.',' ), ( ','.(string)$evt->UserID.',' ) ) && isset( $access[$k] ) )
							{
								$overwrite = $access[$k];
							}
						}
					}
					
					$obj = new stdClass();
					$obj->ID = ( $evt->OrderID ? $evt->OrderID : $evt->ProjectID );
					$obj->JobID = $evt->JobID;
					$obj->Type = $evt->Type;
					$obj->Name = $evt->Name;
					$obj->Project = $evt->ProjectName;
					$obj->Details = $evt->Details;
					$obj->Data = $dta;
					$obj->Code = $overwrite->ID;
					$obj->Display = $overwrite->Display;
					$obj->F1 = $evt->F1;
					$obj->F2 = $evt->F2;
					$obj->F3 = $evt->F3;
					$obj->F4 = $evt->F4;
					$obj->Date = strtotime( $evt->DateStart );
					$obj->Hours = $diffInHours;
					$obj->Hours50 = $evt->Hours50;
					$obj->Hours100 = $evt->Hours100;
					
					// Start: Extended information ----------------------------------------------------------------
					
					if ( isset( $hrs[$evt->UserID]->Accounting ) && is_array( $hrs[$evt->UserID]->Accounting ) )
					{
						foreach( $hrs[$evt->UserID]->Accounting as $uac )
						{
							if ( isset( $account[$uac] ) && isset( $options[$account[$uac]->Type] ) )
							{
								$data = $account[$uac];
								$type = explode( '-', $options[$account[$uac]->Type] );
								
								$ext = new stdClass();
								$ext->Code       = ( $data->VisualID ? $data->VisualID : $data->ID );
								$ext->Type       = $type[2];
								$ext->ID         = ( $ext->Type == 'T' && $evt->JobID ? $evt->JobID : $type[3] );
								//$ext->ID         = $type[3];
								$ext->Amount     = $evt->{$type[0]};
								
								if ( $ext->Amount > 0 && ( !$evt->Type && ( !$type[1] || $evt->Type == $type[1] ) ) )
								{
									$obj->Extended[] = $ext;
								}
							}
						}
					}
					// TODO: Do this dynamic
					// TODO: Fallback now is Hours, do we want that?
					else
					{
						$ext = new stdClass();
						$ext->Code = 0;
						$ext->Type = 'T';
						$ext->ID = $evt->JobID;
						$ext->Amount = $evt->Hours;
						$obj->Extended[] = $ext;
					}
					
					
					// End: Extended information ------------------------------------------------------------------
					
					$hrs[$evt->UserID]->Projects[] = $obj;
					
					$hrs[$evt->UserID]->FromDate = ( isset( $hrs[$evt->UserID]->FromDate ) && ( strtotime( $hrs[$evt->UserID]->FromDate ) < strtotime( $evt->DateStart ) ) ? $hrs[$evt->UserID]->FromDate : $evt->DateStart );
					$hrs[$evt->UserID]->ToDate = ( isset( $hrs[$evt->UserID]->ToDate ) && ( strtotime( $hrs[$evt->UserID]->ToDate ) > strtotime( $evt->DateEnd ) ) ? $hrs[$evt->UserID]->ToDate : $evt->DateEnd );
					$hrs[$evt->UserID]->Total = ( isset( $hrs[$evt->UserID]->Total ) ? ( $hrs[$evt->UserID]->Total + $diffInHours ) : $diffInHours );
					
					$hrs[$evt->UserID]->Total50 = ( isset( $hrs[$evt->UserID]->Total50 ) ? ( $hrs[$evt->UserID]->Total50 + $evt->Hours50 ) : $evt->Hours50 );
					$hrs[$evt->UserID]->Total100 = ( isset( $hrs[$evt->UserID]->Total100 ) ? ( $hrs[$evt->UserID]->Total100 + $evt->Hours100 ) : $evt->Hours100 );
					
					$hrs[$evt->UserID]->TotalF2 = ( isset( $hrs[$evt->UserID]->TotalF2 ) ? ( $hrs[$evt->UserID]->TotalF2 + $evt->F2 ) : $evt->F2 );
					$hrs[$evt->UserID]->TotalF3 = ( isset( $hrs[$evt->UserID]->TotalF3 ) ? ( $hrs[$evt->UserID]->TotalF3 + $evt->F3 ) : $evt->F3 );
					$hrs[$evt->UserID]->TotalF4 = ( isset( $hrs[$evt->UserID]->TotalF4 ) ? ( $hrs[$evt->UserID]->TotalF4 + $evt->F4 ) : $evt->F4 );
				}
				else if ( $evt->Hours && $evt->DateStart != '0000-00-00 00:00:00' )
				{
					// Time difference in hours
					//$diffInHours = ( ( strtotime( $evt->DateEnd ) - strtotime( $evt->DateStart ) ) / 3600 );
					
					$access = array(); $overwrite = new stdClass();
					
					$dta = json_decode( $evt->Data );
					
					if ( isset( $pts[$evt->UserID]->ParentSettings->AccessLevels ) && is_array( $pts[$evt->UserID]->ParentSettings->AccessLevels ) )
					{
						foreach( $pts[$evt->UserID]->ParentSettings->AccessLevels as $lvl )
						{
							if ( $lvl->ID )
							{
								$access[$lvl->ID] = $lvl;
							}
						}
					}
					else if ( isset( $pts[$evt->UserID]->Settings->AccessLevels ) && is_array( $pts[$evt->UserID]->Settings->AccessLevels ) )
					{
						foreach( $pts[$evt->UserID]->Settings->AccessLevels as $lvl )
						{
							if ( $lvl->ID )
							{
								$access[$lvl->ID] = $lvl;
							}
						}
					}
					
					if ( is_object( $dta ) && $access )
					{
						foreach( $dta as $k=>$v )
						{
							if ( $v && strstr( ( ','.(string)$v.',' ), ( ','.(string)$evt->UserID.',' ) ) && isset( $access[$k] ) )
							{
								$overwrite = $access[$k];
							}
						}
					}
					
					$obj = new stdClass();
					$obj->ID = ( $evt->OrderID ? $evt->OrderID : $evt->ProjectID );
					$obj->JobID = $evt->JobID;
					$obj->Type = $evt->Type;
					$obj->Name = $evt->Name;
					$obj->Project = $evt->ProjectName;
					$obj->Details = $evt->Details;
					$obj->Data = $dta;
					$obj->Code = $overwrite->ID;
					$obj->Display = $overwrite->Display;
					$obj->F1 = $evt->F1;
					$obj->F2 = $evt->F2;
					$obj->F3 = $evt->F3;
					$obj->F4 = $evt->F4;
					$obj->Date = strtotime( $evt->DateStart );
					$obj->Hours = $evt->Hours;
					$obj->Hours50 = $evt->Hours50;
					$obj->Hours100 = $evt->Hours100;
					
					// Start: Extended information ----------------------------------------------------------------
					
					if ( isset( $hrs[$evt->UserID]->Accounting ) && is_array( $hrs[$evt->UserID]->Accounting ) )
					{
						foreach( $hrs[$evt->UserID]->Accounting as $uac )
						{
							
							//die( print_r( $account,1 ) . ' -- ' . print_r( $hrs[$evt->UserID]->Accounting,1 ) );
							
							if ( isset( $account[$uac] ) && isset( $options[$account[$uac]->Type] ) )
							{
								$data = $account[$uac];
								$type = explode( '-', $options[$account[$uac]->Type] );
								
								$ext = new stdClass();
								$ext->Code       = ( $data->VisualID ? $data->VisualID : $data->ID );
								$ext->Type       = $type[2];
								$ext->ID         = ( $ext->Type == 'T' && $evt->JobID ? $evt->JobID : $type[3] );
								//$ext->ID         = $type[3];
								$ext->Amount     = $evt->{$type[0]};
								
								if ( $ext->Amount > 0 && ( !$evt->Type && ( !$type[1] || $evt->Type == $type[1] ) ) )
								{
									$obj->Extended[] = $ext;
								}
							}
						}
					}
					// TODO: Do this dynamic
					// TODO: Fallback now is Hours, do we want that?
					else
					{
						$ext = new stdClass();
						$ext->Code = 0;
						$ext->Type = 'T';
						$ext->ID = $evt->JobID;
						$ext->Amount = $evt->Hours;
						$obj->Extended[] = $ext;
					}
					
					// End: Extended information ------------------------------------------------------------------
					
					$hrs[$evt->UserID]->Projects[] = $obj;
					
					$hrs[$evt->UserID]->FromDate = ( isset( $hrs[$evt->UserID]->FromDate ) && ( strtotime( $hrs[$evt->UserID]->FromDate ) < strtotime( $evt->DateStart ) ) ? $hrs[$evt->UserID]->FromDate : $evt->DateStart );
					$hrs[$evt->UserID]->ToDate = ( isset( $hrs[$evt->UserID]->ToDate ) && ( strtotime( $hrs[$evt->UserID]->ToDate ) > strtotime( $evt->DateStart ) ) ? $hrs[$evt->UserID]->ToDate : $evt->DateStart );
					$hrs[$evt->UserID]->Total = ( isset( $hrs[$evt->UserID]->Total ) ? ( $hrs[$evt->UserID]->Total + $evt->Hours ) : $evt->Hours );
					
					$hrs[$evt->UserID]->Total50 = ( isset( $hrs[$evt->UserID]->Total50 ) ? ( $hrs[$evt->UserID]->Total50 + $evt->Hours50 ) : $evt->Hours50 );
					$hrs[$evt->UserID]->Total100 = ( isset( $hrs[$evt->UserID]->Total100 ) ? ( $hrs[$evt->UserID]->Total100 + $evt->Hours100 ) : $evt->Hours100 );
					
					$hrs[$evt->UserID]->TotalF2 = ( isset( $hrs[$evt->UserID]->TotalF2 ) ? ( $hrs[$evt->UserID]->TotalF2 + $evt->F2 ) : $evt->F2 );
					$hrs[$evt->UserID]->TotalF3 = ( isset( $hrs[$evt->UserID]->TotalF3 ) ? ( $hrs[$evt->UserID]->TotalF3 + $evt->F3 ) : $evt->F3 );
					$hrs[$evt->UserID]->TotalF4 = ( isset( $hrs[$evt->UserID]->TotalF4 ) ? ( $hrs[$evt->UserID]->TotalF4 + $evt->F4 ) : $evt->F4 );
				}
			}
		}
	}
	//die( $q . ' -- ' . print_r( $events,1 ) . ' [] ' . print_r( $hrs,1 ) );
	//die( print_r( $hrs,1 ) . ' -- ' . print_r( $cats,1 ) . ' [] ' . print_r( $account,1 ) );
}

$settings = false;

$img = ( BASE_DIR . '/upload/images-master/re_logo_export.png' );
//die( print_r( $prn,1 ) . ' --' );
/*if ( isset( $prn[$parent->folder->CategoryID]->ParentSettings->AccessLevels ) )
{
	$settings = $prn[$parent->folder->CategoryID]->ParentSettings;
}
else */if ( $prn && is_array( $prn ) )
{
	foreach( $prn as $p )
	{
		if ( !$settings && ( $p->ParentSettings || $p->Settings ) )
		{
			if ( $p->ParentSettings )
			{
				$settings = ( is_string( $p->ParentSettings ) ? json_obj_decode( $p->ParentSettings ) : $p->ParentSettings );
			}
			else if ( $p->ParentSettings )
			{
				$settings = ( is_string( $p->Settings ) ? json_obj_decode( $p->Settings ) : $p->Settings );
			}
		}
	}
}
else if ( isset( $parent->folder->Settings ) && $parent->folder->Settings )
{
	$settings = ( is_string( $parent->folder->Settings ) ? json_obj_decode( $parent->folder->Settings ) : $parent->folder->Settings );
}

//die( print_r( $settings->AccessLevels,1 ) . ' -- ' . print_r( $prn,1 ) );

if ( $hrs )
{
	// Set hours as finished if this is a finished request
	
	if ( $events && isset( $_REQUEST['finished'] ) )
	{
		foreach ( $events as $evt )
		{
			$hr = new dbObject( 'SBookHours' );
			$hr->ID = $evt->ID;
			if ( $evt->ID > 0 && $hr->Load() && !$hr->IsFinished )
			{
				$hr->IsFinished = $webuser->ContactID;
				$hr->Save();
				
				if ( isset( $hrs[$evt->UserID]->IsFinished ) )
				{
					$hrs[$evt->UserID]->IsFinished = ( $hr->IsFinished >= 1 ? GetUserDisplayname( $hr->IsFinished ) : $hr->IsFinished );
				}
				
				LogUserActivity( 'close hour', false, 'close', 'SBookHours', $hr->ID );
			}
		}
	}
	
	switch ( $_REQUEST['type'] )
	{
		// --- PDF ---------------------------------------------------------------------------------
		
		case 'pdf':
			
			// Temp files
			define ('K_PATH_CACHE', BASE_DIR . '/upload/images-cache/' );
			// 
			define ('K_PATH_URL_CACHE', BASE_DIR . '/upload/images-cache/' );
			
			include_once ( 'subether/thirdparty/php/tcpdf/tcpdf.php' );
			
			ob_clean();
			
			// Setup PDF document
			$pdf = new TCPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
			
			// set document information
			$pdf->SetCreator( 'Friend Studios AS' );
			$pdf->SetAuthor( 'Treeroot' );
			$pdf->SetTitle( 'Hourlist' );
			$pdf->SetSubject( 'Hourlist' );
			$pdf->SetKeywords( 'Hourlist' );
			
			// set default monospaced font
			$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );
			
			//set margins
			//$pdf->SetMargins( 0, 5, 2 );
			$pdf->SetMargins( 2, 5, 4 );
			//$pdf->SetMargins( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
			$pdf->SetHeaderMargin( 0 );
			$pdf->SetFooterMargin( 0 );
			$pdf->SetHeaderData( '', 0, '', '' );
			$pdf->SetPageBoxes ( 0, 'CropBox', 0, 0, 220, 295, false );
			
			//set auto page breaks
			$pdf->SetAutoPageBreak( TRUE, 5 );
			//$pdf->SetAutoPageBreak( TRUE, PDF_MARGIN_BOTTOM );
			
			//die( print_r( $hrs,1 ) . ' --' );
			
			foreach ( $hrs as $h )
			{
				$temp = new cPTemplate ( 'subether/components/admin/templates/hourlist_template.php' );
				
				$temp->logo = $img;
				
				$temp->d = $h;
				
				if ( isset( $settings->AccessLevels ) )
				{
					$temp->s = $settings;
				}
				
				$str = $temp->Render();
				
				//die( $str . ' --' );
				
				$pdf->AddPage ();
				
				@$pdf->WriteHTML ( $str, true, 0, true, true );
			}
			
			// TODO: Save based on if it's going to be saved for a user or for a group
			
			// If we are to save file to disk
			if ( $_REQUEST['groupid'] && isset( $_REQUEST['save'] ) )
			{
				$lib = new Library ( 'File' );
				$lib->UserID = $parent->cuser->UserID;
				$lib->ParentFolder = 'Library';
				$lib->FolderName = 'Hours';
				$lib->FolderAccess = 2;
				$lib->FileUniqueID = $_REQUEST['groupid'];
				$lib->Filename = ( 'Hourlist_' . date( 'Ymd' ) . '.pdf' );
				$lib->FileAccess = 2;
				$lib->Save();
				
				if ( $lib->FilePath && $lib->FolderPath && $lib->Filename )
				{
					if ( file_exists( BASE_DIR . '/' . $lib->FolderPath . $lib->Filename ) )
					{
						unlink( BASE_DIR . '/' . $lib->FolderPath . $lib->Filename );
					}
					
					@$pdf->Output ( BASE_DIR . '/' . $lib->FolderPath . $lib->Filename, 'F' );
				}
				
				if ( isset( $_REQUEST['bajaxrand'] ) )
				{
					die( 'ok<!--separate-->' );
				}
			}
			//die( print_r( $hrs,1 ) . ' --' );
			// close and output PDF document
			die ( @$pdf->Output( date( 'd-m-Y' ) . '_Hourlist_.pdf', 'I' ) );
			
			break;
			
		// --- XLS ----------------------------------------------------------------------------------------	
			
		case 'xls':
			
			include_once( 'subether/thirdparty/php/PHPExcel/Classes/PHPExcel.php' );
			
			// Create new PHPExcel object
			$sheet = new PHPExcel();
			
			// Set document properties
			$sheet->getProperties()->setCreator( 'Friend Studios' );
			$sheet->getProperties()->setLastModifiedBy( 'Friend Studios' );
			$sheet->getProperties()->setTitle( 'Hourlist' );
			$sheet->getProperties()->setSubject( 'Hourlist' );
			$sheet->getProperties()->setDescription( 'Hourlist' );
			$sheet->getProperties()->setKeywords( 'Hourlist' );
			$sheet->getProperties()->setCategory( 'Hourlist' );
			
			$xrow = 1;
			
			$sheet->setActiveSheetIndex(0);
			$str = $sheet->getActiveSheet();
			
			$str->getColumndimension('A')->setWidth(15); $str->getStyle('A1')->getFont()->setBold(true);
			$str->getColumndimension('B')->setWidth(10); $str->getStyle('B1')->getFont()->setBold(true);
			$str->getColumndimension('C')->setWidth(10); $str->getStyle('C1')->getFont()->setBold(true);
			$str->getColumndimension('D')->setWidth(15); $str->getStyle('D1')->getFont()->setBold(true);
			$str->getColumndimension('E')->setWidth(10); $str->getStyle('E1')->getFont()->setBold(true);
			$str->getColumndimension('F')->setWidth(10); $str->getStyle('F1')->getFont()->setBold(true);
			$str->getColumndimension('G')->setWidth(15);  $str->getStyle('G1')->getFont()->setBold(true);
			//$str->getColumndimension('H')->setWidth(13); $str->getStyle('H1')->getFont()->setBold(true);
			//$str->getColumndimension('I')->setWidth(6);  $str->getStyle('I1')->getFont()->setBold(true);
			//$str->getColumndimension('J')->setWidth(13); $str->getStyle('J1')->getFont()->setBold(true);
			//$str->getColumndimension('K')->setWidth(6);  $str->getStyle('K1')->getFont()->setBold(true);
			//$str->getColumndimension('L')->setWidth(13); $str->getStyle('L1')->getFont()->setBold(true);
			//$str->getColumndimension('M')->setWidth(6);  $str->getStyle('M1')->getFont()->setBold(true);
			//$str->getColumndimension('N')->setWidth(10); $str->getStyle('N1')->getFont()->setBold(true);
			//$str->getColumndimension('O')->setWidth(13); $str->getStyle('O1')->getFont()->setBold(true);
			
			//$cols = array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M' );
			$cols = array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H' );
			
			// Set head columns		
			//$head = array( 'ID', 'Ordrenr', 'Kunde', 'Dato', 'Timer dag tid', 'Timer avspasering', 'Kode', 'Tilleg 50%', 'Tilleg 100%', 'Tillegstekst', 'Bom', 'Parkering', 'Km' );
			$head = array( 'dato', 'ordre', 'ansatt', 'vare/timekode', 'antall', 'posttype', 'operasjonsnr'/*, 'maskinnr'*/ );
			
			//for ( $c = 0; $c < 13; $c++ )
			for ( $c = 0; $c < 7; $c++ )
			{
				$str->setCellValue( $cols[$c].$xrow, $head[$c] );
			}
			
			$xrow = 2;
			
			$total = array();
			$tc = 0;
			//die( print_r( $hrs,1 ) . ' --' );
			foreach ( $hrs as $h )
			{			
				/*if( ++$tc == 2 )
				{
					die( print_r( $h, 1 ) );
				}*/
				if ( $h->Projects )
				{
					foreach ( $h->Projects as $p )
					{
						if ( $p->Extended )
						{
							foreach ( $p->Extended as $e )
							{
								if ( $e->Amount > 0 )
								{
									// Set content rows
									
									$data = array (
										trim( date( 'dmY', $p->Date ) ),
										trim( $p->ID ),
										trim( $h->MemberID ? $h->MemberID : $h->UserID ),
										trim( $e->Code ),
										trim( $e->Amount ),
										trim( $e->Type ),
										trim( $e->ID )/*,
										''*/
									);
									
									//for ( $c = 0; $c < 13; $c++ )
									for ( $c = 0; $c < 7; $c++ )
									{
										$str->setCellValue( $cols[$c].$xrow, $data[$c] );
										
										if ( in_array( $c, array( 0 ) ) )
										{
											$str->getCell( $cols[$c].$xrow )->setValueExplicit( $data[$c], PHPExcel_Cell_DataType::TYPE_STRING );
											$str->getStyle( $cols[$c].$xrow )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_LEFT );
										}
										
										/*if ( in_array( $c, array( 4 ) ) )
										{
											$total[$c] += (int)$data[$c];
										}*/
									}
									
									$xrow++;
								}
							}
						}
					}
				}
			}
			
			/*if ( $total )
			{
				foreach( $total as $k=>$v )
				{
					$str->setCellValue( $cols[$k].($xrow+1), $v );
				}
			}*/
			
			header ( 'Content-type: application/vnd.ms-excel; charset=utf-8' );
			header ( 'Content-Disposition: download; filename="Hourlist_' . date( 'Ymd' ) . '.xls"' );
			
			// Save Excel 2007 file
			$objWriter = PHPExcel_IOFactory::createWriter( $sheet, 'Excel2007' );
			
			die ( $objWriter->save( 'php://output' ) );
			
			break;
		
		// --- CSV ----------------------------------------------------------------------------------------	
		
		case 'csv':
			
			// Set head columns		
			//$head = array( 'ID', 'Ordrenr', 'Kunde', 'Dato', 'Timer dag tid', 'Timer avspasering', 'Kode', 'Tilleg 50%', 'Tilleg 100%', 'Tillegstekst', 'Bom', 'Parkering', 'Km' );
			//$head = array( 'dato', 'ordre', 'ansatt', 'vare/timekode', 'antall', 'posttype', 'operasjonsnr', 'maskinnr' );
			
			$total = array();
			
			$str = ''; $ii = 0;
			
			//$str = implode( "\t", $head );
			//$str = implode( ";", $head );
			
			foreach ( $hrs as $h )
			{	
				if ( $h->Projects )
				{
					foreach ( $h->Projects as $p )
					{
						if ( $p->Extended )
						{
							foreach ( $p->Extended as $e )
							{
								if ( $e->Amount > 0 )
								{
									// Set content rows
									
									$data = array (
										trim( date( 'dmY', $p->Date ) ),
										trim( $p->ID ),
										trim( $h->MemberID ? $h->MemberID : $h->UserID ),
										trim( $e->Code ),
										trim( $e->Amount ),
										trim( $e->Type ),
										trim( $e->ID )/*,
										''*/
									);
									
									/*foreach ( $data as $k=>$v )
									{
										if ( in_array( $k, array( 5, 6, 7, 8, 9, 10, 11, 12, 14 ) ) )
										{
											$total[$k] += (int)$data[$k];
										}
									}*/
									
									//$str .= "\n" . implode( "\t", $data );
									$str .= ( $ii++ > 0 ? "\n" : '' ) . implode( ";", $data );
								}
							}
						}
					}
				}
			}
			
			/*if ( $total && $data )
			{
				//$str .= "\n\n";
				
				$ii = 0;
				
				foreach ( $data as $k=>$v )
				{
					//if ( $ii++ > 0 ) $str .= "\t";
					//$str .= $total[$k];
				}
			}*/
			
			header ( 'Content-type: application/octet-stream; charset=utf-8' );
			//header ( 'Content-type: text/csv; charset=utf-8' );
			//header ( 'Content-type: application/vnd.ms-excel; charset=utf-8' );
			header ( 'Content-Disposition: download; filename="Hourlist_' . date( 'Ymd' ) . '.csv"' );
			
			die ( $str );
			
			break;
	}
}

die( 'no data' );

?>
