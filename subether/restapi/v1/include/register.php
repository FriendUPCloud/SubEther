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

// TODO: include dependencies and so on, and make error messages for the different things, and more details

throwXmlError ( MISSING_PARAMETERS );

// If it's a activate request
if( $_REQUEST['email'] && $_REQUEST['authkey'] )
{
	
	// Check if email and email match and that the account is inactive
	if( $usr = $database->fetchObjectRow( $q = '
		SELECT
			*
		FROM
			`Users`
		WHERE
				InActive = "1"
			AND Username = \'' . ( trim( $_REQUEST['email'] ) ? trim( $_REQUEST['email'] ) : trim( $_POST['email'] ) ) . '\'
			AND AuthKey = \'' . ( trim( $_REQUEST['authkey'] ) ? trim( $_REQUEST['authkey'] ) : trim( $_POST['authkey'] ) ) . '\'
		ORDER BY
			ID DESC
	' ) )
	{
		// Create user or update user when activated
		$co = new dbObject( 'SBookContact' );
		$co->UserID = $usr->ID;
		$co->Username = $usr->Name;
		$co->Email = $usr->Email;
		$co->AuthKey = $usr->AuthKey;
		$co->Load();
		$co->DateCreated = date( 'Y-m-d H:i:s' );
		$co->DateModified = date( 'Y-m-d H:i:s' );
		$co->Save();
		
		$us = new dbObject( 'Users' );
		$us->ID = $usr->ID;
		$us->Load();
		$us->InActive = 0;
		$us->Expires = date( '0000-00-00 00:00:00.000000' );
		$us->Save();
		
		$gr = new dbObject( 'Groups' );
		$gr->Name = 'SocialNetwork';
		if( !$gr->Load() )
		{
			$gr->Save();
		}
		
		$ug = new dbObject( 'UsersGroups' );
		$ug->GroupID = $gr->ID;
		$ug->UserID = $us->ID;
		$ug->Load();
		$ug->Save();	
		
		// Assign to SubEther user/group
		assignToNewMembers( $co->ID );
		
		// Login User
		$webuser =& new dbUser();
		$webuser->Username = $usr->Username;
		$webuser->Password = $usr->Password;
		$webuser->authenticate();
		if( $webuser->is_authenticated )
		{
			if( $_REQUEST['rdir'] )
			{
				
				
				header( 'Location: ' . $_REQUEST['rdir'] );
			}
			else
			{
				showXmlData ( $us->ID );
				//die( 'ok<!--separate-->' . $parent->domain . $parent->path . $usr->Name );
			}
		}
		
		throwXmlError ( AUTHENTICATION_ERROR );
		//die( 'failed, couldnt login, something went wrong contact support' );
	}
	
	throwXmlError ( AUTHENTICATION_ERROR );
	//die( 'failed, user account couldnt be activated because it either is allready activated or wasnt found or because email and authkey didnt match' );
	
}
// If it's a register request
else if( $_POST['Email'] && !$_POST['AuthKey'] )
{
	$iu = ( $_POST['Email'] ? explode( '@', $_POST['Email'] ) : '' );
	
	$username = UniqueName( SanitizeName( trim( $_POST['Username'] ? $_POST['Username'] : ( $iu ? $iu[0] : 'InvitedUser' ) ) ) );
	$password = ( $_POST['Password'] ? $_POST['Password'] : makePassword() );
	
	// Email is required
	if( !isset( $_POST['Email'] ) )
	{
		throwXmlError ( AUTHENTICATION_ERROR );
		//die( 'fail<!--separate-->Email is required' );
	}
	
	// Check invited list
	if( !checkBetaInvites( trim( $_POST['Email'] ) ) )
	{
		throwXmlError ( AUTHENTICATION_ERROR );
		//die( 'fail<!--separate-->Email didnt match invited only list' );
	}
	
	// Check if email exists
	if( $database->fetchObjectRow( 'SELECT InActive, Username FROM `Users` WHERE InActive = "0" AND Username = \'' . trim( $_POST['Email'] ) . '\' ' ) )
	{
		throwXmlError ( AUTHENTICATION_ERROR );
		//die( 'fail<!--separate-->This email allready exists choose another' );
	}
	
	// Check if user exists
	if( $database->fetchObjectRow( 'SELECT Email FROM `SBookContact` WHERE Email = \'' . trim( $_POST['Email'] ) . '\' ' ) )
	{
		throwXmlError ( AUTHENTICATION_ERROR );
		//die( 'fail<!--separate-->This user exists with this email on another node' );
	}
	
	$u = new dbObject( 'Users' );
	$u->Username = trim( $_POST['Email'] );
	if( !$u->Load() )
	{
		if( $u->AuthKey = makeAuthKey() )
		{
			$expiry = mktime( 0, 0, 0, date('m'), date('d')+3, date('Y') );
			
			// Save new user for activation
			
			$u->Name 			= mysql_real_escape_string( trim( $username ) );
			$u->Password 		= mysql_real_escape_string( md5( $password ) );
			$u->Email	 		= mysql_real_escape_string( trim( $_POST['Email'] ) );
			$u->DateCreated		= date( 'Y-m-d H:i:s' );
			$u->DateModified	= date( 'Y-m-d H:i:s' );
			$u->Expires 		= date( 'Y-m-d H:i:s', $expiry );
			$u->InActive		= 1;
			$u->Save();
		}
		else
		{
			die( 'fail<!--separate-->Couldnt make AuthKey, contact support' );	
		}
		
		if( $u->ID && $u->Email && $u->AuthKey )
		{
			$link = BASE_URL . 'register/?email=' . trim( $_POST['Email'] ) . '&authkey=' . $u->AuthKey . '&rdir=';
			
			// Send kode to email
			$cs  = 'Invitation';
			$cr  = $u->Email;
			$cm  = 'Username: ' . $u->Email . ' <br>';
			$cm .= 'Password: ' . $password . ' (temporary password should be changed after activation) <br>';
			$cm .= 'Click this link: <a href="' . $link . '">activation link</a> <br>';
			$cm .= 'to activate your account <br>';
			$ct  = 'html';
			mailNow_ ( $cs, $cm, $cr, $ct );
			
			showXmlData ( $u->ID );
			//die( 'ok<!--separate-->Invitation was sendt to ' . $u->Email );
		}
	}
	
	throwXmlError ( AUTHENTICATION_ERROR );
	//die( 'fail<!--separate-->This user has allready been invited' );
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );
//die( 'fail<!--separate-->Something went wrong contact support' );

?>
