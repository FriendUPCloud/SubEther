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

$required = array(
	'SessionID' 
);

$options = array(
	'ContactID', 'ContactEmail', 'ContactNumber', 'ContactUsername', 'AllowID', 'DenyID', 'DeleteID', 'CancelID', 'Encoding' 
);



if ( isset( $_POST ) )
{
	// --- Check for allowed parameters ----------------------------------------------
	
	// Temporary to view data i browser for development
	if( !$_POST )
	{
		if( isset( $_REQUEST['route'] ) )
		{
			unset( $_REQUEST['route'] );
		}
		
		$_POST = $_REQUEST;
	}
	
	foreach ( $_POST as $k=>$p )
	{
		if( !in_array( $k, $required ) && !in_array( $k, $options ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	foreach ( $required as $r )
	{
		if( !isset( $_POST[$r] ) || !$_POST[$r] )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	
	// --- Get User data from SessionID ---------------------------------------------
	
	if ( !$sess = $database->fetchObjectRow ( '
		SELECT
			c.*, l.Token 
		FROM
			UserLogin l,
			SBookContact c 
		WHERE 
				l.Token = \'' . $_POST['SessionID'] . '\'
			AND c.UserID = l.UserID
		ORDER BY 
			l.ID DESC 
	' ) )
	{
		throwXmlError ( AUTHENTICATION_ERROR );
	}
	
	// --- OPTION 1 : Add contact request by ID, Email, Number or Username ---------------------
	
	if ( isset( $_POST['ContactID'] ) || isset( $_POST['ContactEmail'] ) || isset( $_POST['ContactNumber'] ) || isset( $_POST['ContactUsername'] ) )
	{
		if ( $contact = $database->fetchObjectRow ( $q = '
			SELECT 
				c.*, r.ID AS RequestID 
			FROM 
				SBookContact c
					LEFT JOIN SBookContactRelation r ON ( (
					( r.ObjectID = c.ID AND r.ContactID = \'' . $sess->ID . '\' ) OR
					( r.ContactID = c.ID AND r.ObjectID = \'' . $sess->ID . '\' ) ) AND
					r.ObjectType = "SBookContact" ) 
			WHERE 
				c.ID > 0 '
				. ( isset( $_POST['ContactID'] ) ? 'AND c.ID = \'' . $_POST['ContactID'] . '\' ' : '' ) 
				. ( isset( $_POST['ContactEmail'] ) ? 'AND c.Email = \'' . $_POST['ContactEmail'] . '\' ' : '' )
				. ( isset( $_POST['ContactNumber'] ) ? 'AND ( c.Telephone = \'' . $_POST['ContactNumber'] . '\' OR c.Mobile = \'' . $_POST['ContactNumber'] . '\' ) ' : '' )
				. ( isset( $_POST['ContactUsername'] ) ? 'AND c.Username = \'' . $_POST['ContactUsername'] . '\' ' : '' ) . '
			ORDER BY 
				c.ID DESC 
		' ) )
		{
			$r = new dbObject( 'SBookContactRelation' );
			if( !$r->Load( $contact->RequestID ) )
			{
				$r->ContactID = $sess->ID;
				$r->ObjectType = 'SBookContact';
				$r->ObjectID = $contact->ID;
				$r->DateCreated = date( 'Y-m-d H:i:s' );
				$r->DateModified = date( 'Y-m-d H:i:s' );
				$r->Save();
				
				UserActivity( 'contacts', 'relations', $r->ContactID, $r->ObjectID, $r->ID, 'new' );
				
				showXmlData ( $contact->ID, 'ContactID' );
			}
		}
		
		throwXmlMsg ( EMPTY_LIST );
	}
	
	// --- OPTION 2 : Allow or Deny request ----------------------------------------------------
	
	if ( isset( $_POST['AllowID'] ) || isset( $_POST['DenyID'] ) )
	{
		if ( $contact = $database->fetchObjectRow ( '
			SELECT 
				c.*, c.ID AS ContactID, r.ID 
			FROM 
				SBookContactRelation r, 
				SBookContact c 
			WHERE 
					r.ObjectID = \'' . $sess->ID . '\'
				AND r.ContactID = \'' . ( $_POST['DenyID'] ? $_POST['DenyID'] : $_POST['AllowID'] ) . '\' 
				AND c.ID = r.ContactID 
				AND r.ObjectType = "SBookContact" 
			ORDER BY 
				r.ID ASC 
		' ) )
		{
			$r = new dbObject( 'SBookContactRelation' );
			if( $r->Load( $contact->ID ) )
			{
				$rid = $r->ID;
				
				if( $_POST['AllowID'] )
				{
					UserActivity( 'contacts', 'relations', $r->ContactID, $r->ObjectID, $r->ID, 'approved' );
					
					$r->IsApproved = 1;
					$r->DateModified = date( 'Y-m-d H:i:s' );
					$r->Save();
					
					showXmlData ( $rid, 'RequestID' );
				}
				if( $_POST['DenyID'] )
				{
					UserActivity( 'contacts', 'relations', $r->ContactID, $r->ObjectID, $r->ID, 'denied' );
					
					$r->Delete();
					
					showXmlData ( $rid, 'RequestID' );
				}
			}
		}
		
		throwXmlMsg ( EMPTY_LIST );
	}
	
	// --- OPTION 3 : Delete contact by ID -----------------------------------------------------
	
	if ( isset( $_POST['DeleteID'] ) || isset( $_POST['CancelID'] ) )
	{
		if ( $contact = $database->fetchObjectRow ( $q = '
			SELECT 
				c.*, c.ID AS ContactID, r.ID 
			FROM 
				SBookContactRelation r, 
				SBookContact c 
			WHERE 
					( ( r.ContactID = \'' . $sess->ID . '\' AND r.ObjectID = \'' . ( $_POST['CancelID'] ? $_POST['CancelID'] : $_POST['DeleteID'] ) . '\' AND r.ObjectID = c.ID ) 
				OR  ( r.ContactID = \'' . ( $_POST['CancelID'] ? $_POST['CancelID'] : $_POST['DeleteID'] ) . '\' AND r.ObjectID = \'' . $sess->ID . '\' AND r.ContactID = c.ID ) ) 
				AND r.ObjectType = "SBookContact" 
			ORDER BY 
				r.ID ASC 
		' ) )
		{
			$r = new dbObject( 'SBookContactRelation' );
			if( $r->Load( $contact->ID ) )
			{
				$rid = $r->ID;
				
				UserActivity( 'contacts', 'relations', $r->ContactID, $r->ObjectID, $r->ID, 'removed' );
				
				$r->Delete();
				
				showXmlData ( $rid, 'RequestID' );
			}
		}
		
		throwXmlMsg ( EMPTY_LIST );
	}
	
	// --- DEFAULT : Get contact request list --------------------------------------------------
	
	if ( $requests = $database->fetchObjectRows ( '
		SELECT 
			c.*, c.ID AS ContactID, r.ID 
		FROM 
			SBookContactRelation r, 
			SBookContact c 
		WHERE 
				( ( r.ContactID = \'' . $sess->ID . '\' AND r.ObjectID = c.ID ) 
			OR  ( r.ObjectID = \'' . $sess->ID . '\' AND r.ContactID = c.ID ) ) 
			AND r.ObjectType = "SBookContact" 
			AND r.IsApproved = "0" 
		ORDER BY 
			r.ID ASC 
	' ) )
	{
		$xml = array(); $json = new stdClass(); $fid = array();
		
		$json->Requests = [];
		
		foreach ( $requests as $row )
		{
			$str  = '<Requests>';
			$str .= '<ID>' . $row->ID . '</ID>';
			$str .= '<ContactID>' . $row->ContactID . '</ContactID>';
			$str .= '<ImageID>' . $row->ImageID . '</ImageID>';
			$str .= '<UserID>' . $row->UserID . '</UserID>';
			$str .= '<Username><![CDATA[' . $row->Username . ']]></Username>';
			$str .= '<Firstname><![CDATA[' . $row->Firstname . ']]></Firstname>';
			$str .= '<Middlename><![CDATA[' . $row->Middlename . ']]></Middlename>';
			$str .= '<Lastname><![CDATA[' . $row->Lastname . ']]></Lastname>';
			$str .= '<ShowAlternate><![CDATA[' . $row->ShowAlternate . ']]></ShowAlternate>';
			$str .= '<Display>' . $row->Display . '</Display>';
			$str .= '</Requests>';
			
			$obj = new stdClass();
			$obj->ID            = $row->ID;
			$obj->ContactID     = $row->ContactID;
			$obj->ImageID       = $row->ImageID;
			$obj->UserID        = $row->UserID;
			$obj->Username      = $row->Username;
			$obj->Firstname     = $row->Firstname;
			$obj->Middlename    = $row->Middlename;
			$obj->Lastname      = $row->Lastname;
			$obj->ShowAlternate = $row->ShowAlternate;
			$obj->Display       = $row->Display;
			
			$json->Requests[] = $obj;
			
			if ( $row->ImageID > 0 && !in_array( $row->ImageID, $fid ) )
			{
				$fid[] = $row->ImageID;
			}
			
			$xml[] = $str;
		}
		
		if ( count( $xml ) )
		{
			$xml[] = '<Listed>' . count( $xml ) . '</Listed>';
			
			$json->Listed = count( $xml );
			
			if ( count( $fid ) )
			{
				$xml[] = '<Images>' . implode( ',', $fid ) . '</Images>';
				
				$json->Images = implode( ',', $fid );
			}
			
			$xml[] = '<ID>' . $sess->ID . '</ID>';
			
			$json->ID = $sess->ID;
			
			outputXML ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' ? $json : implode( $xml ) );
		}
	}
	
	throwXmlMsg ( EMPTY_LIST );
}

throwXmlError ( MISSING_PARAMETERS );

?>
