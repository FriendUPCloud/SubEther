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

global $webuser;

if( $_REQUEST[ 'action' ] == 'account' )
{
	/*if( isset( $_POST[ 'validation' ] ) )
	{
		if( !Validation( $_POST[ 'validation' ] ) )
		{
			die( 'Your current password was entered wrong.' );
		} 
	}*/
	
	$u = new dbObject( 'SBookContact' );
	$u->UserID = $webuser->ID;
	if( $u->Load() && $_POST )
	{
		foreach( $_POST as $k=>$v )
		{
			$u->$k = $v;
		}
		
		$w = new dbObject( 'Users' );
		if( $w->Load( $webuser->ID ) && $_POST )
		{
			/*if( isset( $_POST[ 'Password' ] ) )
			{
				if( trim( $_POST[ 'Password' ] ) == trim( $_POST[ 'Confirmed' ] ) )
				{
					$w->Password = md5( trim( $_POST[ 'Password' ] ) );
					$w->DateModified = date( 'Y-m-d H:i:s' );
					$w->Save();
					
					// Login User
					$webuser =& new dbUser();
					$webuser->reauthenticate( $w->Username, trim( $_POST[ 'Password' ] ) );
					if( $webuser->is_authenticated )
					{
						die( 'changed<!--separate-->Password was successfully changed.' );
					}
					
					die( 'something went wrong' );
				}
				else die( 'Password didn\'t match.' );
			}*/
			if( isset( $_POST[ 'Username' ] ) )
			{
				if( FindUserName( $_POST[ 'Username' ] ) )
				{
					die( 'fail<!--separate-->Username exists choose another one.' );
				}
				$w->Name = $_POST[ 'Username' ];
				$w->Save();
			}
			/*if( isset( $_POST[ 'NewEmail' ] ) && $_POST[ 'NewEmail' ] != '' )
			{
				if( !strstr( $_POST[ 'NewEmail' ], '@' ) || !strstr( $_POST[ 'NewEmail' ], '.' ) )
				{
					die( 'Your email is not valid, example@domain.com' );
				}
				$obj = StringToObject( $u->Data );
				$obj->Emails[] = trim( $_POST[ 'NewEmail' ] );
				$u->Data = ObjectToString( $obj );
			}
			if( isset( $_POST[ 'PrimaryEmail' ] ) )
			{
				die( 'Doest work atm, contact support' );
				
				$obj = StringToObject( $u->Data );
				if( $obj->Emails )
				{
					$i = 0;
					foreach( $obj->Emails as $key=>$email )
					{
						if( $key == '0' )
						{
							$obj->Emails[$i] = trim( $_POST[ 'PrimaryEmail' ] );
							$i++;
						}
						if( trim( $email ) == trim( $_POST[ 'PrimaryEmail' ] ) )
						{
							continue;
						}
						$obj->Emails[$i] = $email;
						$i++;
					}
				}
				$u->Data = ObjectToString( $obj );
				$u->Email = trim( $_POST[ 'PrimaryEmail' ] );
				
				$w->Username = trim( $_POST[ 'PrimaryEmail' ] );
				$w->Save();
				
				// TODO: Need support for password, this function should support a md5 encrypted password when reauthenticating, either that or private key + public key
				// Login User
				$webuser =& new dbUser();
				$webuser->reauthenticate( $w->Username, trim( $_POST[ 'Password' ] ) );
				if( $webuser->is_authenticated )
				{
					die( 'changed<!--separate-->Primary email was successfully changed.' );
				}
			}*/
		}
		
		UserActivity( 'contacts', 'contact', $u->ID, null, $u->ID, 'updated' );
		
		$u->Save();
		die( 'ok<!--separate-->' );
	}
	die( 'fail' );
}
die( 'fail' );

?>
