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

// Create user and contact relation -----------------------------------------
if ( isset( $_POST ) && isset( $_POST['AuthKey'] ) )
{
	$required = array(
		'Url', 'ID', 'SenderID', 'ReceiverID', 'AuthKey'
	);
	
	foreach( $_POST as $k=>$p )
	{
		if( !in_array( $k, $required ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	foreach( $required as $r )
	{
		if( !isset( $_POST[$r] ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	
	$node = new dbObject( 'SNodes' );
	$node->Url = $_POST['Url'];
	if( $node->Load() )
	{
		$co = new dbObject( 'SBookContact' );
		$co->NodeID = $node->ID;
		$co->NodeMainID = $_POST['SenderID'];
		if( $co->Load() )
		{
			$us = new dbObject( 'Users' );
			$us->Username = $co->Email;
			$us->Name = $co->Username;
			if( !$us->Load() )
			{
				$us->Password = md5( $_POST['AuthKey'] );
				$us->Name = $co->Username;
				$us->Email = $co->Email;
				$us->DateCreated = date( 'Y-m-d H:i:s' );
				$us->DateModified = date( 'Y-m-d H:i:s' );
				$us->IsTemplate = 0;
				$us->Save();
				
				$gr = new dbObject( 'Groups' );
				$gr->Name = 'NodeNetwork';
				if( !$gr->Load() )
				{
					$gr->Save();
				}
				
				$ug = new dbObject( 'UsersGroups' );
				$ug->GroupID = $gr->ID;
				$ug->UserID = $us->ID;
				$ug->Save();
				
				$co->UserID = $us->ID;
				$co->DateModified = date( 'Y-m-d H:i:s' );
				$co->Save();
			}
			
			$rc = new dbObject( 'SBookContact' );
			if( $rc->Load( $_POST['ReceiverID'] ) )
			{
				$re = new dbObject( 'SBookContactRelation' );
				$re->ContactID = $co->ID;
				$re->ObjectType = 'SBookContact';
				$re->ObjectID = $rc->ID;
				$re->NodeID = $node->ID;
				$re->NodeMainID = $_POST['ID'];
				if( !$re->Load() )
				{
					$re->Save();
					
					showXmlData ( $rc->AuthKey, 'authkey' );
				}
			}
		}
	}
	
	throwXmlError ( MISSING_PARAMETERS );
}
// Auth with user and allow or deny contact relation on both nodes ---------------
else if( isset( $_POST ) && isset( $_POST['Allow'] ) && verifySessionId() )
{
	$required = array(
		'Url', 'ID', 'Allow', 'SessionID' 
	);
	
	foreach( $_POST as $k=>$p )
	{
		if( !in_array( $k, $required ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	foreach( $required as $r )
	{
		if( !isset( $_POST[$r] ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	
	$node = new dbObject( 'SNodes' );
	$node->Url = $_POST['Url'];
	if( $node->Load() )
	{
		$re = new dbObject( 'SBookContactRelation' );
		if( $re->Load( $_POST['ID'] ) )
		{
			if( $_POST['Allow'] > 0 )
			{
				$re->IsApproved = 1;
				$re->IsNoticed = 1;
				$re->Save();
			}
			else
			{
				$re->Delete();
			}
			
			showXmlData ( 'ok', false );
		}
	}
	
	throwXmlError ( MISSING_PARAMETERS );
}
// Auth with user and create contact relation --------------------------------
else if( isset( $_POST ) && verifySessionId() )
{
	$required = array(
		'Url', 'ID', 'SenderID', 'ReceiverID', 'SessionID'
	);
	
	foreach( $_POST as $k=>$p )
	{
		if( !in_array( $k, $required ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	foreach( $required as $r )
	{
		if( !isset( $_POST[$r] ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	
	$node = new dbObject( 'SNodes' );
	$node->Url = $_POST['Url'];
	if( $node->Load() )
	{
		$co = new dbObject( 'SBookContact' );
		$co->NodeID = $node->ID;
		$co->NodeMainID = $_POST['SenderID'];
		if( $co->Load() )
		{
			$us = new dbObject( 'Users' );
			$us->ID = $co->UserID;
			if( $us->Load() )
			{
				$sess = new dbObject ( 'UserLogin' );
				$sess->Token = $_POST[ 'SessionID' ];
				$sess->UserID = $us->UserID;
				if ( !$sess->Load () )
				{
					throwXmlError ( SESSION_MISSING );
				}
				
				$rc = new dbObject( 'SBookContact' );
				if( $rc->Load( $_POST['ReceiverID'] ) )
				{
					$re = new dbObject( 'SBookContactRelation' );
					$re->ContactID = $co->ID;
					$re->ObjectType = 'SBookContact';
					$re->ObjectID = $rc->ID;
					$re->NodeID = $node->ID;
					$re->NodeMainID = $_POST['ID'];
					if( !$re->Load() )
					{
						$re->Save();
						
						showXmlData ( 'ok', false );
					}
				}
			}
		}
	}
	
	throwXmlError ( MISSING_PARAMETERS );
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
