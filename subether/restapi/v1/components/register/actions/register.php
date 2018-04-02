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
	/*'SessionID', */'Email' 
);

$options = array(
	'Firstname', 'Middlename', 
	'Lastname', 'Gender', 
	'Mobile', 'Image', 
	'Username', 'Password',
	'Encoding' 
);

// Temporary to view data in browser for development
if( !$_POST && $_REQUEST )
{
	$_POST = $_REQUEST;
}
unset( $_POST['route'] );


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
	
	
	//$_POST['Username'] = UniqueUsername( CleanUsername( trim( $_POST['Username'] ) ) );
	
	// Check invited list
	if( !BetaInvites( trim( $_POST['Email'] ) ) )
	{
		throwXmlError ( MISSING_INVITATION );
	}
	
	// Check if email exists
	if( $database->fetchObjectRow( '
		SELECT
			InActive, 
			Username 
		FROM 
			`Users` 
		WHERE 
				InActive = "0" 
			AND Username = \'' . trim( $_POST['Email'] ) . '\' 
	' ) )
	{
		throwXmlError ( PARAMETERS_EXISTS );
	}
	
	// Check if user exists
	if( $database->fetchObjectRow( '
		SELECT
			Email
		FROM
			`SBookContact`
		WHERE
			Email = \'' . trim( $_POST['Email'] ) . '\'
	' ) )
	{
		// TODO: Make support for migration between nodes here and in the api
		//die( 'migrate<!--separate-->This user exists with this email on another node, do you wish to migrate?' );
	}
	
	if ( isset( $_POST['Username'] ) )
	{
		$username = UniqueUsername( CleanUsername( trim( $_POST['Username'] ) ) );
	}
	else
	{
		$iu = explode( '@', $_POST['Email'] );
		$username = UniqueUsername( CleanUsername( trim( $iu[0] ) ) );
	}
	
	$u = new dbObject( 'Users' );
	$u->Username = trim( $_POST['Email'] );
	if( !$u->Load() )
	{
		if( $authkey = makeHumanPassword() )
		{
			$expiry = mktime( 0, 0, 0, date('m'), date('d')+3, date('Y') );
			
			// Save new user for activation
			
			$u->AuthKey		    = md5( trim( $authkey ) );
			$u->UniqueID 		= UniqueKey( $u->Username );
			$u->Name 			= trim( $username );
			
			if( isset( $_POST['Password'] ) )
			{
				$u->Password = ( isValidMd5( $_POST['Password'] ) ? trim( $_POST['Password'] ) : md5( $_POST['Password'] ) );
			}
			
			$u->Email	 		= trim( $_POST['Email'] );
			$u->DateCreated		= date( 'Y-m-d H:i:s' );
			$u->DateModified	= date( 'Y-m-d H:i:s' );
			$u->Expires 		= date( 'Y-m-d H:i:s', $expiry );
			$u->InActive		= 1;
			$u->Save();
			
			if( $u->ID > 0 )
			{
				// Create user or update user when activated
				$c = new dbObject( 'SBookContact' );
				$c->UserID = $u->ID;
				
				if( !$c->Load() )
				{
					$c->DateCreated = date( 'Y-m-d H:i:s' );
				}
				
				$c->Username = $u->Name;
				$c->Email    = $u->Email;
				$c->AuthKey  = $u->AuthKey;
				
				$c->Firstname  = ( isset( $_POST['Firstname'] )  ? $_POST['Firstname']  : $c->Firstname );
				$c->Middlename = ( isset( $_POST['Middlename'] ) ? $_POST['Middlename'] : $c->Middlename );
				$c->Lastname   = ( isset( $_POST['Lastname'] )   ? $_POST['Lastname']   : $c->Lastname );
				$c->Gender     = ( isset( $_POST['Gender'] )     ? $_POST['Gender']     : $c->Gender );
				$c->Mobile     = ( isset( $_POST['Mobile'] )     ? $_POST['Mobile']     : $c->Mobile );
				
				if( !$c->Display && $c->Firstname )
				{
					$c->Display = 1;
				}
				
				$c->DateModified = date( 'Y-m-d H:i:s' );
				$c->Save();
			}
		}
		else
		{
			throwXmlError ( KEYGEN_FAILED );
		}
	}
	
	
	if( $u->ID > 0 && $u->Email && $u->AuthKey )
	{
		showXmlData ( $u->AuthKey );
	}
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
