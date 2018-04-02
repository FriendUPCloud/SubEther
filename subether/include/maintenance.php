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

$root = ( file_exists( "config.php" ) ? '.' : '../..' );

include_once ( "$root/subether/restapi/functions.php" );

$start = new dbObject( 'SBookCronJobs' );
$start->Filename = 'maintenance_cron.php';
if( !$start->Load() )
{
	$start->MinDelay = 30;
	$start->IsMaintenance = 1;
}
if( isset( $_POST['init'] ) && $start->IsActive == 0 )
{
	if( $_POST['type'] )
	{
		$start->Type = $_POST['type'];
	}
	else
	{
		$start->Type = 'treeroot';
	}
	
	$start->LastExec = date( 'Y-m-d H:i:s' );
	$start->IsActive = 1;
	$start->Save();
}

if( $start->ID > 0 && $start->IsActive == 1 && $start->IsRunning == 0 )
{
	if( $_SESSION['MaintenanceStarting'] )
	{
		output( 'ok<!--separate-->' );
	}
	else
	{
		output( 'ok<!--separate-->Maintenance will start in ' . ( $start->MinDelay - ( date( 'YmHi' ) - date( 'YmHi', strtotime( $start->LastExec ) ) ) ) . ' minutes.' );
		$_SESSION['MaintenanceStarting'] = true;
	}
}
else if( $start->ID > 0 && $start->IsActive == 1 && $start->IsRunning == 1 )
{
	if( $_SESSION['MaintenanceRunning'] )
	{
		output( 'running<!--separate-->' );
	}
	else
	{
		output( 'running<!--separate-->Maintenance is running, As soon as the updates is complete, the site will return to normal.' );
		$_SESSION['MaintenanceRunning'] = true;
	}
}
else
{
	$_SESSION['MaintenanceStarting'] = false;
	$_SESSION['MaintenanceRunning'] = false;
}

output( 'none' );

?>
