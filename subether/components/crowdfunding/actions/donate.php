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

if ( $_POST['pid'] > 0 && $_POST['currency'] && $_POST['donation'] && ( $row = $database->fetchObjectRow( $q = '
	SELECT 
		* 
	FROM 
		SBookCrowdfunding 
	WHERE 
			`ID` = \'' . $_POST['pid'] . '\'
		AND `CategoryID` = \'' . $parent->folder->CategoryID . '\' 
		AND `IsDeleted` = "0" 
	ORDER BY 
		ID DESC 
' ) ) )
{
	$o = new dbObject( 'SBookDonations' );
	$o->UniqueID = '';
	$o->Component = 'crowdfunding';
	$o->ComponentID = $_POST['pid'];
	$o->UserID = ( $webuser ? $webuser->ContactID : 0 );
	$o->Name = 'true';
	$o->CategoryID = $parent->folder->CategoryID;
	$o->PaymentType = 'undefined';
	$o->PaymentID = '0';
	$o->Donation = $_POST['donation'];
	$o->Currency = $_POST['currency'];
	$o->DateCreated = date( 'Y-m-d H:i:s' );
	$o->DateModified = date( 'Y-m-d H:i:s' );
	$o->Save();
	
	die( 'ok<!--separate-->' . $o->Donation );
}

die( 'fail ' );

?>
