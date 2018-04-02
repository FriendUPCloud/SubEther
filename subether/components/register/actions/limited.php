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

// TODO: Create passwords in client before sending it here, also make it possible to create accounts for people that isn't limited as superadmin

if( $webuser->ID > 0 && $_POST['Accounts'] > 0 )
{
	$_POST['Keys'] = json_decode( $_POST['Keys'] );
	
	die( 'TODO: Create this in two turns, first get uniqueid\'s and created usernames on all accounts sent to javascript and then after that create keys and send final. ' . print_r( $_POST,1 ) );
	
	// Email is required
	if( !isset( $_POST['Email'] ) )
	{
		die( 'fail<!--separate-->Email is required' );
	}
	
	// Expiry date is required
	if( !isset( $_POST['Expiry'] ) )
	{
		die( 'fail<!--separate-->Expiry date is requires' );
	}
	
	// Check if email is valid
	if( !strstr( $_POST['Email'], '@' ) || !strstr( $_POST['Email'], '.' ) )
	{
		die( 'fail<!--separate-->Email is not valid' );
	}
	
	$accounts = array();
	
	for( $i = 0; $i < $_POST['Accounts']; $i++ )
	{
		$username = UniqueName( SanitizeName( trim( 'LimitedUser' ) ) );
		$password = makePassword();
		
		$u = new dbObject( 'Users' );
		$u->Username = $username;
		if( !$u->Load() )
		{
			// Save new limited users
			
			$u->Name 			= mysql_real_escape_string( trim( $username ) );
			$u->Password 		= mysql_real_escape_string( md5( $password ) );
			$u->Email	 		= mysql_real_escape_string( trim( $_POST['Email'] ) );
			$u->DateCreated		= date( 'Y-m-d H:i:s' );
			$u->DateModified	= date( 'Y-m-d H:i:s' );
			$u->Expires 		= date( 'Y-m-d H:i:s', strtotime( $_POST['Expiry'] ) );
			$u->IsLimited 		= 1;
			
			if( isset( $_POST['StoreKey'] ) && $_POST['StoreKey'] )
			{
				$u->StoreKey = 1;
			}
			
			$u->Save();
			
			// Create user or update user when activated
			$co = new dbObject( 'SBookContact' );
			$co->UserID = $u->ID;
			if( !$co->Load() )
			{
				$co->DateCreated = date( 'Y-m-d H:i:s' );
			}
			$co->Username = $u->Name;
			$co->DateModified = date( 'Y-m-d H:i:s' );
			$co->Save();
			
			$gr = new dbObject( 'Groups' );
			$gr->Name = 'SocialNetwork';
			if( !$gr->Load() )
			{
				$gr->Save();
			}
			
			$ug = new dbObject( 'UsersGroups' );
			$ug->GroupID = $gr->ID;
			$ug->UserID = $u->ID;
			if( !$ug->Load() )
			{
				$ug->Save();
			}
			
			$obj = new stdClass();
			$obj->Username = $u->Name;
			$obj->Password = $password;
			$obj->Expires = $u->Expires;
			
			// TODO: Implement default group reg ...
			
			$accounts[] = $obj;
		}
		
		if( $i > 1000 )
		{
			die( 'fail<!--separate-->Something wrong with for loop contact support' );
		}
	}
	
	if( $accounts && count( $accounts ) > 0 )
	{
		// Send kode to email
		$cs  = 'Limited Accounts';
		$cr  = trim( $_POST['Email'] );
		$cm  = '';
		
		foreach( $accounts as $a )
		{
			$cm .= 'Username: ' . $a->Username . ' <br>';
			$cm .= 'Password: ' . $a->Password . ' <br>';
			$cm .= 'Expires: ' . $a->Expires . ' <br>';
			$cm .= '<br>';
		}
		
		$ct  = 'html';
		
		$res = mailNow_ ( $cs, $cm, $cr, $ct );
		
		if( $res && $res['ok'] )
		{
			die( 'ok<!--separate-->Limited accounts where sendt to ' . $_POST['Email'] );
		}
		else if( $res && !$res['ok'] )
		{
			die( 'ok<!--separate-->There was an error trying to send mail: ' . $res['error'] . ' [' . $cm . ']' );
		}
	}
}

die( 'fail<!--separate-->Something went wrong contact support' );

?>
