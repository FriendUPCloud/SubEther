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

$required = array(
	'SessionID'
);

$options = array(
	'EventID', 'HourID'
);

// Temporary to view data i browser for development
if( !$_POST && $_REQUEST )
{
	$_POST = $_REQUEST;
	unset( $_POST['route'] );
}
else
{
	unset( $_POST['route'] );
}


if ( isset( $_POST ) )
{
	foreach( $_POST as $k=>$p )
	{
		if( !in_array( $k, $required ) && !in_array( $k, $options ) )
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
	
	// Get User data from sessionid
	$sess = new dbObject ( 'UserLogin' );
	$sess->Token = $_POST[ 'SessionID' ];
	if ( $sess->Load () )
	{
		$u = new dbObject ( 'SBookContact' );
		$u->UserID = $sess->UserID;
		if( !$u->Load () )
		{
			throwXmlError ( AUTHENTICATION_ERROR );
		}
	}
	
	// TODO: check if user has the right to delete
	if( $_POST['HourID'] > 0 )
	{
		// --- Delete this hourslot by ID
		
		$h = new dbObject( 'SBookHours' );
		if( $h->Load( $_POST['HourID'] ) )
		{
			$h->IsDeleted = 1;
			$h->Save();
		}
		
		// --- Delete this event by ID if there is no hourslots connected to it
		
		if( $_POST['EventID'] > 0 && !$database->fetchObjectRows( '
			SELECT 
				h.*
			FROM 
				SBookHours h
			WHERE 
					h.ProjectID = \'' . $_POST['EventID'] . '\' 
				AND h.IsDeleted = "0" 
			ORDER BY 
				h.ID DESC 
		' ) )
		{
			$e = new dbObject( 'SBookEvents' );
			if( $e->Load( $_POST['EventID'] ) )
			{
				$e->IsDeleted = 1;
				$e->Save();
			}
		}
		
		if( $h->ID > 0 )
		{
			showXmlData ( $h->ID );
		}
	}
	else if( $_POST['EventID'] > 0 )
	{
		// --- Delete this event by ID
		
		$e = new dbObject( 'SBookEvents' );
		if( $e->Load( $_POST['EventID'] ) )
		{
			if( $hours = $database->fetchObjectRows( '
				SELECT 
					h.*
				FROM 
					SBookHours h
				WHERE 
						h.ProjectID = \'' . $_POST['EventID'] . '\' 
					AND h.IsDeleted = "0" 
				ORDER BY 
					h.ID DESC 
			' ) )
			{
				foreach( $hours as $hr )
				{
					// --- Delete this hourslot by ID
					
					$h = new dbObject( 'SBookHours' );
					if( $h->Load( $hr->ID ) )
					{
						$h->IsDeleted = 1;
						$h->Save();
					}
				}
			}
			
			$e->IsDeleted = 1;
			$e->Save();
			
			showXmlData ( $e->ID );
		}
	}
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
