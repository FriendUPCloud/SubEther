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

include_once ( 'subether/components/register/include/functions.php' );

$required = array(
	/*'SessionID', */'AuthKey'
);

$options = array(
	'Email', 'UniqueID', 'PublicKey', 
	'Firstname', 'Middlename', 'Lastname', 
	'Gender', 'Mobile', 'Image', 
	'UserType', 'Source', 'Encoding' 
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
	
	
	// Check if email and authkey match and that the account is inactive
	if( $usr = $database->fetchObjectRow( $q = '
		SELECT
			*
		FROM
			`Users`
		WHERE
				IsDeleted = "0" 
			AND InActive = "1" 
			' . ( isset( $_POST['UniqueID'] ) ? '
			AND UniqueID = \'' . trim( $_POST['UniqueID'] ) . '\'
			' : '
			AND Username = \'' . trim( $_POST['Email'] ) . '\'
			' ) . '
			AND AuthKey = \'' . trim( $_POST['AuthKey'] ) . '\' 
		ORDER BY 
			ID DESC 
	' ) )
	{
		$us = new dbObject( 'Users' );
		$us->ID = $usr->ID;
		if ( $us->Load() )
		{
			if( isset( $_REQUEST['PublicKey'] ) )
			{
				// Save new password
				$us->InActive = 0;
				$us->AuthKey = '';
				$us->Password = UniqueKey();
				$us->PublicKey = $_REQUEST['PublicKey'];
				$us->DateModified = date( 'Y-m-d H:i:s' );
				$us->Expires = date( '0000-00-00 00:00:00.000000' );
				$us->UserType = ( isset( $_POST['UserType'] ) ? $_POST['UserType'] : 1 );
				$us->Save();
			}
			else
			{
				$us->InActive = 0;
				$us->Expires = date( '0000-00-00 00:00:00.000000' );
				$us->UserType = ( isset( $_POST['UserType'] ) ? $_POST['UserType'] : 1 );
				$us->Save();
			}
			
			
			
			// Create user or update user when activated
			$co = new dbObject( 'SBookContact' );
			$co->UserID = $usr->ID;
			
			if( !$co->Load() )
			{
				$co->DateCreated = date( 'Y-m-d H:i:s' );
			}
			
			$co->Username = $usr->Name;
			$co->Email    = $usr->Email;
			$co->AuthKey  = $usr->AuthKey;
			
			$co->Firstname  = ( isset( $_POST['Firstname'] )  ? $_POST['Firstname']  : $co->Firstname );
			$co->Middlename = ( isset( $_POST['Middlename'] ) ? $_POST['Middlename'] : $co->Middlename );
			$co->Lastname   = ( isset( $_POST['Lastname'] )   ? $_POST['Lastname']   : $co->Lastname );
			$co->Gender     = ( isset( $_POST['Gender'] )     ? $_POST['Gender']     : $co->Gender );
			$co->Mobile     = ( isset( $_POST['Mobile'] )     ? $_POST['Mobile']     : $co->Mobile );
			
			if( !$co->Display && $co->Firstname )
			{
				$co->Display = 1;
			}
			
			$co->DateModified = date( 'Y-m-d H:i:s' );
			$co->Save();
			
			if( $co->ID > 0 && $us->ID > 0 )
			{
				$gr = new dbObject( 'Groups' );
				switch( $us->UserType )
				{
					case 2:
						$gr->Name = 'NodeNetwork';
						break;
					case 0:
						$gr->Name = 'SocialNetwork';
						break;
					default:
						$gr->Name = 'ApiUsers';
						break;
				}
				if( !$gr->Load() )
				{
					$gr->Save();
				}
				
				$ug = new dbObject( 'UsersGroups' );
				$ug->GroupID = $gr->ID;
				$ug->UserID = $us->ID;
				$ug->Load();
				$ug->Save();
				
				if( $us->UserType == 0 && function_exists( 'assignToNewMembers' ) )
				{
					// Assign to SubEther user/group
					assignToNewMembers( $co->ID );
				}
				
				$sess = new dbObject( 'UserLogin' );
				$sess->UserID = $us->ID;
				$sess->DataSource = ( isset( $_POST['Source'] ) ? $_POST['Source'] : 'api' );
				$sess->Token = md5(rand(0,9999).rand(0,9999).rand(0,9999).microtime());
				$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
				$sess->DateCreated = date( 'Y-m-d H:i:s' );
				$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
				$sess->Save ();
				
				showXmlData ( $sess->Token );
			}
		}
	}
	
	throwXmlError ( ACTIVATION_FAILED );
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
