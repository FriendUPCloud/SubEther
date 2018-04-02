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

$u = new dbObject( 'SBookContact' );
$u->UserID = $webuser->ID;
$u->Load();

$w = new dbObject( 'Users' );
$w->Load( $webuser->ID );

if ( $u->ID > 0 && $w->ID > 0 )
{
	$changed = false;
	
	if ( isset( $_POST['NewEmail'] ) && $_POST['NewEmail'] != '' )
	{
		if ( !strstr( $_POST['NewEmail'], '@' ) || !strstr( $_POST['NewEmail'], '.' ) )
		{
			die( 'fail<!--separate-->Your email is not valid, example@domain.com' );
		}
		
		$obj = json_obj_decode( $u->Data );
		
		if ( !isset( $obj->Emails ) )
		{
			$obj->Emails = new stdClass();
		}
		
		if ( is_object( $obj->Emails ) )
		{
			$arr = new stdClass();
			
			$ii = 0; $found = false;
			
			foreach ( $obj->Emails as $key=>$val )
			{
				if ( trim( $val ) == trim( $_POST['NewEmail'] ) )
				{
					$found = true;
				}
				
				$arr->{$ii} = $val;
				
				$ii++;
			}
			
			if ( !$found )
			{
				$arr->{$ii} = trim( $_POST['NewEmail'] );
			}
			
			$obj->Emails = $arr;
		}
		
		$u->Data = json_obj_encode( $obj );
	}
	
	if ( isset( $_POST['PrimaryEmail'] ) )
	{
		if ( $usr = $database->fetchObjectRow( '
			SELECT
				ID, Username
			FROM
				Users
			WHERE
				Username = \'' . trim( $_POST['PrimaryEmail'] ) . '\' 
			ORDER BY
				ID DESC
		' ) )
		{
			if ( $usr->ID != $webuser->ID )
			{
				die( 'fail<!--separate-->email allready exists, choose another unique email' );
			}
		}
		else
		{
			$obj = json_obj_decode( $u->Data );
			
			if ( $obj->Emails && is_object( $obj->Emails ) )
			{
				$changed = true;
				
				$arr = new stdClass();
				
				$arr->{0} = trim( $_POST['PrimaryEmail'] );
				
				$i = 1;
				
				foreach ( $obj->Emails as $key=>$email )
				{
					if ( trim( $email ) != trim( $_POST['PrimaryEmail'] ) )
					{
						$arr->{$i} = $email;
						$i++;
					}
				}
				
				$obj->Emails = $arr;
			}
			
			$u->Data = json_obj_encode( $obj );
			$u->Email = trim( $_POST['PrimaryEmail'] );
			
			$w->Username = trim( $_POST['PrimaryEmail'] );
		}
	}
	
	if ( isset( $_POST['RemoveEmail'] ) )
	{
		$obj = json_obj_decode( $u->Data );
		
		if ( $obj->Emails && is_object( $obj->Emails ) )
		{
			$arr = new stdClass();
			
			foreach ( $obj->Emails as $key=>$email )
			{
				if ( trim( $email ) == trim( $_POST['RemoveEmail'] ) )
				{
					continue;
				}
				
				$arr->{$key} = $email;
			}
			
			$obj->Emails = $arr;
		}
		
		$u->Data = json_obj_encode( $obj );
	}
	
	if ( $u->ID > 0 && $w->ID > 0 )
	{
		$u->Save();
		$w->Save();
		
		if ( $changed )
		{
			// Login User
			$webuser =& new dbUser();
			$webuser->setEncryptionMethod( 'plain' );
			$webuser->reauthenticate( $w->Username, $w->Password );
		}
		
		die( 'ok<!--separate-->' );
	}
}

die( 'fail' );

?>
