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

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

global $database, $webuser;

$str = '<div id="Orders" class="orders">';

// TODO: List some categories as open default, get everything by ajax in one go. Collapsed as option.
// TODO: Check of multiple before sending it further instead of it moving to the other list on single checked.
// TODO: Project checkoff checks all hours on project



// --- Orders ----------------------------------------------------------------

$oadmin = false;

if ( $parent->folder->MainName == 'Groups' )
{
	$cats = array( 'CategoryID' => $parent->folder->CategoryID );
	
	// If you have admin access to the order and this is a group
	if( isset( CategoryAccess( $webuser->ContactID, $parent->folder->CategoryID )->IsAdmin ) )
	{
		$oadmin = true;
	}
}
else
{
	$cats = CategoryAccess( $webuser->ContactID, false, -1, 'IsAdmin' );
}

$filterhours = '';

if ( isset( $_REQUEST['h'] ) )
{
	$hourstatus = array(
		0 => 'AND h.IsReady = "0" AND h.IsAccepted = "0"',
		1 => 'AND h.IsReady >= "1" AND h.IsAccepted = "0"',
		2 => 'AND h.IsReady >= "1" AND h.IsAccepted >= "1"'
	);
	
	$filterhours = $hourstatus[$_REQUEST['h']];
}

// Sort orders based on admin view on groups

if ( $oadmin )
{
	include ( $cbase . '/functions/admin.php' );
}

// Sort orders based on project view globally for all groups

else if ( !$oadmin && ( $_REQUEST['sort'] == 1 || $_REQUEST['h'] == 0 || !$_REQUEST['h'] ) )
{
	include ( $cbase . '/functions/projects.php' );
}

// Sort orders based on member hours view globally for all groups

else if ( !$oadmin && ( $_REQUEST['sort'] == 2 || $_REQUEST['h'] >= 1 ) )
{
	include ( $cbase . '/functions/members.php' );
}


// --- Output ---------------------------------------------------------------

$str .= ( $oadmin ? $nstr : $tstr ) . $hstr . '<div class="list' . ( $oadmin ? ' admin' : '' ) . '"><ul>' . ( $cstr ? $cstr : '<li><div>' . i18n( 'i18n_No orders in list' ) . '</div></li>' ) . '</ul></div>';

$str .= '</div>';

?>
