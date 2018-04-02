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
	/*'SessionID', */
);

$options = array(
	'Email', 'Username', 'UniqueID', 'Encoding' 
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
	
	
	
	$u = new dbObject( 'Users' );
	if ( isset( $_POST['Email'] ) )
	{
		$u->Username = trim( $_POST['Email'] );
	}
	if ( isset( $_POST['Username'] ) )
	{
		$u->Name = trim( $_POST['Username'] );
	}
	if ( isset( $_POST['UniqueID'] ) )
	{
		$u->UniqueID = trim( $_POST['UniqueID'] );
	}
	if ( $u->Load() )
	{
		if ( $newkey = trim( makeHumanPassword() ) )
		{
			// If user doesn't have a uniqueid make one
			if ( !$u->UniqueID )
			{
				$usu = new dbObject( 'Users' );
				$usu->ID = $u->ID;
				if ( $usu->Load() )
				{
					$usu->UniqueID = UniqueKey( $u->Username );
					$usu->Save();
					
					$u->UniqueID = $usu->UniqueID;
				}
			}
			
			// Send account info to email
			$cs  = 'Account Recovery';
			$cr  = ( strstr( $u->Username, '@' ) ? $u->Username : $u->Email );
			$cm  = 'Username: ' . $u->Username . ' <br>';
			$cm .= 'RecoveryKey: ' . $newkey . ' <br>';
			mailNow_ ( $cs, $cm, $cr, 'html' );
			
			// Save new password
			$u->IsDisabled = 1;
			$u->AuthKey = md5( $newkey );
			$u->DateModified = date( 'Y-m-d H:i:s' );
			$u->Save();
			
			if ( $u->ID > 0 && $u->AuthKey )
			{
				showXmlData ( $cr );
			}
		}
		
		throwXmlError ( KEYGEN_FAILED );
	}
	
	throwXmlError ( PARAMETERS_NORESULT );
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
