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
	'SessionID', 'EventName', 
	'DateStart', 'DateEnd'
);

$options = array(
	'EventID', 'HourID',
	'CategoryID', 'ContactID',
	'Access', 'EventPlace',
	'EventDetails', 'HourRole',
	'HourSlots' 
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
	
	
	// Load event ---------------------------------------------------------------------------------------------------------
	if( $_POST['EventID'] > 0 && isset( $_POST['ContactID'] ) )
	{
		$e = new dbObject( 'SBookEvents' );
		$e->ID = $_POST['EventID'];
		$e->Load();
	}
	// Update event -------------------------------------------------------------------------------------------------------
	else if( $_POST['EventID'] > 0 )
	{
		$e = new dbObject( 'SBookEvents' );
		$e->ID = $_POST['EventID'];
		if( $e->Load() )
		{
			$e->Name = ( $_POST['EventName'] ? $_POST['EventName'] : $e->Name );
			$e->Place = ( $_POST['EventPlace'] ? $_POST['EventPlace'] : $e->Place );
			$e->Details = ( $_POST['EventDetails'] ? $_POST['EventDetails'] : $e->Details );
			$e->DateStart = ( $_POST['DateStart'] ? date( 'Y-m-d H:i:s', strtotime( $_POST['DateStart'] ) ) : $e->DateStart );
			$e->DateEnd = ( $_POST['DateEnd'] ? date( 'Y-m-d H:i:s', strtotime( $_POST['DateEnd'] ) ) : $e->DateEnd );
			$e->Access = ( $_POST['Access'] ? $_POST['Access'] : $e->Access );
			$e->DateModified = date( 'Y-m-d H:i:s' );
			$e->Save();
		}
	}
	// Create event ------------------------------------------------------------------------------------------------------
	else
	{
		$e = new dbObject( 'SBookEvents' );
		$e->Component = 'events';
		$e->UserID = $u->ID;
		$e->CategoryID = ( $_POST['CategoryID'] ? $_POST['CategoryID'] : 0 );
		$e->Name = ( $_POST['EventName'] ? $_POST['EventName'] : '' );
		$e->Place = ( $_POST['EventPlace'] ? $_POST['EventPlace'] : '' );
		$e->Details = ( $_POST['EventDetails'] ? $_POST['EventDetails'] : '' );
		$e->DateStart = ( $_POST['DateStart'] ? date( 'Y-m-d H:i:s', strtotime( $_POST['DateStart'] ) ) : '' );
		$e->DateEnd = ( $_POST['DateEnd'] ? date( 'Y-m-d H:i:s', strtotime( $_POST['DateEnd'] ) ) : '' );
		$e->Access = ( $_POST['Access'] ? $_POST['Access'] : 1 );
		$e->DateCreated = date( 'Y-m-d H:i:s' );
		$e->Save();
	}
	
	
	if( $e->ID > 0 )
	{
		// Update hour ---------------------------------------------------------------------------------------------------------
		if( $_POST['HourID'] > 0 )
		{
			$h = new dbObject( 'SBookHours' );
			$h->ID = $_POST['HourID'];
			if( $h->Load() )
			{
				$h->UserID = ( $_POST['ContactID'] ? $_POST['ContactID'] : $h->UserID );
				$h->Role = ( $_POST['HourRole'] ? $_POST['HourRole'] : $h->Role );
				$h->DateStart = ( $_POST['DateStart'] ? date( 'Y-m-d H:i:s', strtotime( $_POST['DateStart'] ) ) : $h->DateStart );
				$h->DateEnd = ( $_POST['DateEnd'] ? date( 'Y-m-d H:i:s', strtotime( $_POST['DateEnd'] ) ) : $h->DateEnd );
				$h->Access = ( $_POST['Access'] ? $_POST['Access'] : $h->Access );
				$h->DateModified = date( 'Y-m-d H:i:s' );
				$h->Save();
			}
		}
		// Create hour(s) ------------------------------------------------------------------------------------------------------
		else
		{
			$slots = $database->fetchObjectRows( '
				SELECT
					ID
				FROM
					SBookHours
				WHERE
						ProjectID = \'' . $e->ID . '\'
					AND IsDeleted = "0"
				ORDER BY
					ID DESC
			' );
			
			$slots = ( $slots ? ( ( $_POST['HourSlots'] ? $_POST['HourSlots'] : 1 ) - count( $slots ) ) : ( $_POST['HourSlots'] ? $_POST['HourSlots'] : 1 ) );
			
			if( $slots > 0 )
			{
				for( $a = 0; $a < $slots; $a++ )
				{
					$h = new dbObject( 'SBookHours' );
					$h->Title = 'events';
					$h->ProjectID = $e->ID;
					$h->UserID = ( $_POST['ContactID'] ? $_POST['ContactID'] : 0 );
					$h->Type = '';
					$h->Role = ( $_POST['HourRole'] ? $_POST['HourRole'] : '' );
					$h->DateStart = ( $_POST['DateStart'] ? date( 'Y-m-d H:i:s', strtotime( $_POST['DateStart'] ) ) : '' );
					$h->DateEnd = ( $_POST['DateEnd'] ? date( 'Y-m-d H:i:s', strtotime( $_POST['DateEnd'] ) ) : '' );
					$h->Hours = 0;
					$h->IsNight = 0;
					$h->DateCreated = date( 'Y-m-d H:i:s' );
					$h->DateModified = date( 'Y-m-d H:i:s' );
					$h->Access = ( $_POST['Access'] ? $_POST['Access'] : 1 );
					$h->Save();
					
					if( $a > 1000 ) break;
				}
			}
		}
	}
	
	
	if( $e->ID > 0 && $h->ID > 0 )
	{
		showXmlData ( $e->ID );
	}
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
