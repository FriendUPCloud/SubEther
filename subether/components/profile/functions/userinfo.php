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
	
if ( isset( $_REQUEST['cid'] ) && ( $csr = $database->fetchObjectRow( '
	SELECT 
		u.UniqueID, u.PublicKey, 
		c.ID, c.UserID, c.ImageID, 
		c.Username, c.Firstname, 
		c.Middlename, c.Lastname, 
		c.Gender, c.Languages, 
		c.Alternate, c.ScreenName, 
		c.Website, c.Address, 
		c.Country, c.City, 
		c.Postcode, c.Telephone, 
		c.Mobile, c.Email, 
		c.Work, c.College, 
		c.HighSchool, c.Interests, 
		c.Philosophy, c.Religion, 
		c.Political, c.About, 
		c.Quotations, c.Birthdate, 
		c.DateCreated, c.DateModified, 
		c.ShowAlternate, c.Display, 
		c.NodeID, c.NodeMainID, 
		n.Url 
	FROM 
		SBookContact c, 
		Users u LEFT JOIN SNodes n ON ( u.NodeID > 0 AND n.ID = u.NodeID ) 
	WHERE 
			c.ID = \'' . $_REQUEST['cid'] . '\' 
		AND u.ID = c.UserID 
		AND u.IsDeleted = "0" 
' ) ) )
{
	//die( print_r( $parent,1 ) . ' --' );	
	
	$wsr = new stdClass();
	
	if( $parent && $parent->webuser )
	{
		$wsr->UniqueID  = $parent->webuser->UniqueID;
		$wsr->PublicKey = $parent->webuser->PublicKey;
		$wsr->ID        = $parent->webuser->ContactID;
		$wsr->UserID    = $parent->webuser->UserID;
		$wsr->Email     = $parent->webuser->Email;
	}
	
	$obj = new stdClass();
	$obj->webuser = $wsr;
	$obj->cuser = $csr;
	
	die( 'ok<!--separate-->' . json_encode( $obj ) );
}
else if( $parent )
{
	$wsr = new stdClass();
	
	if( $parent && $parent->webuser )
	{
		$wsr->UniqueID  = $parent->webuser->UniqueID;
		$wsr->PublicKey = $parent->webuser->PublicKey;
		$wsr->ID        = $parent->webuser->ContactID;
		$wsr->UserID    = $parent->webuser->UserID;
		$wsr->Email     = $parent->webuser->Email;
	}
	
	$obj = new stdClass();
	$obj->webuser = $wsr;
	
	die( 'ok<!--separate-->' . json_encode( $obj ) );
}

die( 'fail' );

?>
