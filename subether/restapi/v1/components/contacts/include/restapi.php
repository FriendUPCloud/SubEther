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

global $database;

$xml = array(); $json = new stdClass(); $fid = array();

$required = array(
	//'SessionID' 
);

$options = array(
	'ContactID', 'Limit', 'Encoding' 
);

// TODO: Adding support for getting basic info abiut contacts with an anonymous account.

$u = new stdClass();

// Get User data from sessionid
/*$sess = new dbObject ( 'UserLogin' );
$sess->Token = $_POST['SessionID'];
if ( $sess->Load () )
{
	if( $sess->UserID > 0 )
	{
		$u = new dbObject ( 'SBookContact' );
		$u->UserID = $sess->UserID;
		if( !$u->Load () )
		{
			throwXmlError ( AUTHENTICATION_ERROR );
		}
		
		if( !$u->UniqueID )
		{
			$c = new dbObject ( 'Users' );
			if( $c->Load( $sess->UserID ) )
			{
				$u->UniqueID = $c->UniqueID;
			}
		}
	}
	else if ( $sess->NodeID > 0 )
	{
		$u = new dbObject ( 'SNodes' );
		$u->ID = $sess->NodeID;
		if( !$u->Load () )
		{
			throwXmlError ( AUTHENTICATION_ERROR );
		}
	}
}*/

// Run Query	
if ( $contacts = $database->fetchObjectRows ( '
	SELECT 
		c.*, u.UniqueID, u.PublicKey, n.Url AS NodeUrl 
	FROM 
		Users u, 
		SBookContact c 
			LEFT JOIN SNodes n ON ( n.ID = c.NodeID )
	WHERE 
		' . ( $_POST['ContactID'] > 0 ? 'c.ID = \'' . $_POST['ContactID'] . '\' AND ' : 'c.NodeID = "0" AND u.NodeID = "0" AND ' ) . '
		u.ID = c.UserID 
	ORDER BY 
		c.Firstname ASC, 
		c.Username ASC 
	' . ( isset( $_POST['Limit'] ) ? 'LIMIT ' . $_POST['Limit'] : '' ) . '
' ) )
{
	$json->Contacts = [];
	
	foreach ( $contacts as $row )
	{
		switch ( $row->Display )
		{
			case 1:
				$row->DisplayName = trim( $row->Firstname . ' ' . $row->Middlename . ' ' . $row->Lastname );
				break;
			case 2:
				$row->DisplayName = trim( $row->Firstname . ' ' . $row->Lastname );
				break;
			case 3:
				$row->DisplayName = trim( $row->Lastname . ' ' . $row->Firstname );
				break;
			default:
				$row->DisplayName = $row->Username;
				break;
		}
		
		$str  = "\t\t".'<Contacts>'."\n";
		$str .= "\t\t".'	<ID>' . $row->ID . '</ID>'."\n";
		$str .= "\t\t".'	<UniqueID>' . $row->UniqueID . '</UniqueID>'."\n";
		$str .= "\t\t".'	<PublicKey>' . $row->PublicKey . '</PublicKey>'."\n";
		$str .= "\t\t".'	<ImageID>' . $row->ImageID . '</ImageID>'."\n";
		$str .= "\t\t".'	<UserID>' . $row->UserID . '</UserID>'."\n";
		$str .= "\t\t".'	<Username><![CDATA[' . $row->Username . ']]></Username>'."\n";
		$str .= "\t\t".'	<Firstname><![CDATA[' . $row->Firstname . ']]></Firstname>'."\n";
		$str .= "\t\t".'	<Middlename><![CDATA[' . $row->Middlename . ']]></Middlename>'."\n";
		$str .= "\t\t".'	<Lastname><![CDATA[' . $row->Lastname . ']]></Lastname>'."\n";
		$str .= "\t\t".'	<Gender>' . $row->Gender . '</Gender>'."\n";
		$str .= "\t\t".'	<Languages>' . $row->Languages . '</Languages>'."\n";
		$str .= "\t\t".'	<Alternate><![CDATA[' . $row->Alternate . ']]></Alternate>'."\n";
		$str .= "\t\t".'	<ScreenName><![CDATA[' . $row->ScreenName . ']]></ScreenName>'."\n";
		$str .= "\t\t".'	<Website>' . $row->Website . '</Website>'."\n";
		$str .= "\t\t".'	<Address><![CDATA[' . $row->Address . ']]></Address>'."\n";
		$str .= "\t\t".'	<Country><![CDATA[' . $row->Country . ']]></Country>'."\n";
		$str .= "\t\t".'	<City><![CDATA[' . $row->City . ']]></City>'."\n";
		$str .= "\t\t".'	<Postcode>' . $row->Postcode . '</Postcode>'."\n";
		$str .= "\t\t".'	<Telephone>' . $row->Telephone . '</Telephone>'."\n";
		$str .= "\t\t".'	<Mobile>' . $row->Mobile . '</Mobile>'."\n";
		$str .= "\t\t".'	<Email>' . $row->Email . '</Email>'."\n";
		$str .= "\t\t".'	<Work><![CDATA[' . $row->Work . ']]></Work>'."\n";
		$str .= "\t\t".'	<College><![CDATA[' . $row->College . ']]></College>'."\n";
		$str .= "\t\t".'	<HighSchool><![CDATA[' . $row->HighSchool . ']]></HighSchool>'."\n";
		$str .= "\t\t".'	<Interests><![CDATA[' . $row->Interests . ']]></Interests>'."\n";
		$str .= "\t\t".'	<Philosophy><![CDATA[' . $row->Philosophy . ']]></Philosophy>'."\n";
		$str .= "\t\t".'	<Religion><![CDATA[' . $row->Religion . ']]></Religion>'."\n";
		$str .= "\t\t".'	<Political><![CDATA[' . $row->Political . ']]></Political>'."\n";
		$str .= "\t\t".'	<About><![CDATA[' . $row->About . ']]></About>'."\n";
		$str .= "\t\t".'	<Quotations><![CDATA[' . $row->Quotations . ']]></Quotations>'."\n";
		$str .= "\t\t".'	<Data><![CDATA[' . $row->Data . ']]></Data>'."\n";
		$str .= "\t\t".'	<ShowAlternate><![CDATA[' . $row->ShowAlternate . ']]></ShowAlternate>'."\n";
		$str .= "\t\t".'	<Display>' . $row->Display . '</Display>'."\n";
		$str .= "\t\t".'	<DisplayName>' . $row->DisplayName . '</DisplayName>'."\n";
		$str .= "\t\t".'	<NodeID>' . $row->NodeID . '</NodeID>'."\n";
		$str .= "\t\t".'	<NodeMainID>' . $row->NodeMainID . '</NodeMainID>'."\n";
		$str .= "\t\t".'	<NodeUrl>' . $row->NodeUrl . '</NodeUrl>'."\n";
		$str .= "\t\t".'</Contacts>'."\n";
		
		$obj = new stdClass();
		$obj->ID            = $row->ID;
		$obj->UniqueID      = $row->UniqueID;
		$obj->PublicKey     = $row->PublicKey;
		$obj->ImageID       = $row->ImageID;
		$obj->UserID        = $row->UserID;
		$obj->Username      = $row->Username;
		$obj->Firstname     = $row->Firstname;
		$obj->Middlename    = $row->Middlename;
		$obj->Lastname      = $row->Lastname;
		$obj->Gender        = $row->Gender;
		$obj->Languages     = $row->Languages;
		$obj->Alternate     = $row->Alternate;
		$obj->ScreenName    = $row->ScreenName;
		$obj->Website       = $row->Website;
		$obj->Address       = $row->Address;
		$obj->Country       = $row->Country;
		$obj->City          = $row->City;
		$obj->Postcode      = $row->Postcode;
		$obj->Telephone     = $row->Telephone;
		$obj->Mobile        = $row->Mobile;
		$obj->Email         = $row->Email;
		$obj->Work          = $row->Work;
		$obj->College       = $row->College;
		$obj->HighSchool    = $row->HighSchool;
		$obj->Interests     = $row->Interests;
		$obj->Philosophy    = $row->Philosophy;
		$obj->Religion      = $row->Religion;
		$obj->Political     = $row->Political;
		$obj->About         = $row->About;
		$obj->Quotations    = $row->Quotations;
		$obj->Data          = $row->Data;
		$obj->ShowAlternate = $row->ShowAlternate;
		$obj->Display       = $row->Display;
		$obj->DisplayName   = $row->DisplayName;
		$obj->NodeID        = $row->NodeID;
		$obj->NodeMainID    = $row->NodeMainID;
		$obj->NodeUrl       = $row->NodeUrl;
		
		$json->Contacts[] = $obj;
		
		if ( $row->ImageID > 0 && !in_array( $row->ImageID, $fid ) )
		{
			$fid[] = $row->ImageID;
		}
		
		$xml[] = $str;
	}
}

if ( count( $xml ) )
{
	$xml = array_reverse( $xml );
	
	$xml[] = "\t\t".'<Listed>' . count( $xml ) . '</Listed>'."\n";
	
	if ( count( $fid ) )
	{
		$xml[] = "\t\t".'<Images>' . implode( ',', $fid ) . '</Images>'."\n";
	}
	
	$xml[] = "\t\t".'<ID>' . $u->ID . '</ID>'."\n";
	
	$json->Contacts = array_reverse( $json->Contacts );
	
	$json->Listed = count( $json->Contacts );
	
	if ( count( $fid ) )
	{
		$json->Images = implode( ',', $fid );
	}
	
	$json->ID       = $u->ID;
	$json->UniqueID = $u->UniqueID;
	$json->Email    = $u->Email;
	
	outputXML ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' ? $json : implode( $xml ) );
}

throwXmlError ( EMPTY_LIST );

?>
