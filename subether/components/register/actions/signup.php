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

if ( $_POST && ( $webuser->ID > 0 || $database->fetchObjectRow( 'SELECT * FROM SNodes WHERE IsMain = "1" AND Open = "1"' ) || !$database->fetchObjectRow( 'SELECT * FROM SNodes' ) ) )
{
	// Email is required
	if ( !isset( $_POST['Email'] ) )
	{
		die( 'fail<!--separate-->Email is required' );
	}
	
	// Check invited list
	if ( !checkBetaInvites( trim( $_POST['Email'] ) ) )
	{
		die( 'fail<!--separate-->Email didnt match invited only list' );
	}
	
	// Check if email exists
	if( $database->fetchObjectRow( 'SELECT Username FROM `Users` WHERE Username = \'' . trim( $_POST['Email'] ) . '\' ' ) )
	{
		die( 'fail<!--separate-->This email allready exists choose another' );
	}
	
	// Check if user exists
	if ( $database->fetchObjectRow( 'SELECT Email FROM `SBookContact` WHERE Email = \'' . trim( $_POST['Email'] ) . '\' ' ) )
	{
		// TODO: Make support for migration between nodes here and in the api
		//die( 'migrate<!--separate-->This user exists with this email on another node, do you wish to migrate?' );
	}
	
	if ( $_POST['Username'] )
	{
		$username = UniqueName( SanitizeName( trim( $_POST['Username'] ) ) );
	}
	else
	{
		$iu = explode( '@', $_POST['Email'] );
		$username = UniqueName( SanitizeName( trim( $iu[0] ) ) );
	}
	
	$u = new dbObject( 'Users' );
	$u->Username = trim( $_POST['Email'] );
	if ( !$u->Load() )
	{
		if( $authkey = makeHumanPassword() )
		{
			$expiry = mktime( 0, 0, 0, date('m'), date('d')+3, date('Y') );
			
			// Save new user for activation
			
			$u->UniqueID 		= UniqueKey( $u->Username );
			$u->Name 			= trim( $username );
			$u->AuthKey 		= md5( trim( $authkey ) );
			$u->Email	 		= trim( $_POST['Email'] );
			$u->DateCreated		= date( 'Y-m-d H:i:s' );
			$u->DateModified	= date( 'Y-m-d H:i:s' );
			$u->Expires 		= date( 'Y-m-d H:i:s', $expiry );
			$u->InActive		= 1;
			
			if( isset( $_POST['StoreKey'] ) && $_POST['StoreKey'] )
			{
				$u->StoreKey = 1;
			}
			
			$u->Save();
		}
		else
		{
			die( 'fail<!--separate-->Couldn\'t make AuthKey, contact support' );	
		}
	}
	
	if ( $u->ID && $u->Email && $u->AuthKey )
	{
		//$link  = BASE_URL . 'register/?activate=' . $u->UniqueID . '&email=' . $u->Username . '&authkey=' . $authkey . '&auto=login';
		$link  = BASE_URL . 'register/?activate=' . $u->UniqueID . '&email=' . $u->Username . '&authkey=' . $authkey;
		$link2 = BASE_URL . 'register/?activate=' . $u->UniqueID . '&email=' . $u->Username;
		
		// Send kode to email
		$cs  = 'AuthKey';
		$cr  = $u->Email;
		$cm  = 'Use this AuthKey: ' . $authkey . ' <br>';
		$cm .= 'With this Password(should be changed): ' . $authkey . ' <br>';
		$cm .= 'or ';
		$cm .= 'click this link: <a href="' . $link . '">activation link</a> ';
		$cm .= 'to activate your account <br>';
		$ct  = 'html';
		
		$res = mailNow_ ( $cs, $cm, $cr, $ct );
		
		if( $res && $res['ok'] )
		{
			die( 'ok<!--separate-->The AuthKey was sendt to ' . $u->Email . '<!--separate-->' . $link2 );
		}
		else if( $res && !$res['ok'] )
		{
			die( 'ok<!--separate-->There was an error trying to send mail: ' . $res['error'] . '<!--separate-->' . $link );
		}
	}
}

die( 'fail<!--separate-->Something went wrong contact support' );

?>
