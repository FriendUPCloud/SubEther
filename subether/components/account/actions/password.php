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

include_once ( 'subether/classes/fcrypto.class.php' );

if ( $webuser->ID > 0 && $_POST['UniqueID'] )
{
	// Check for keys in storage and send them to client for encryption with new keys
	
	// TODO: Fix this, sometime ...
	
	/*if ( !isset( $_POST['Keys'] ) && ( $keys = $database->fetchObjectRows ( '
			SELECT 
				s.* 
			FROM 
				Users u, 
				SBookStorage s 
			WHERE 
					u.ID = \'' . $webuser->ID . '\'
				AND u.UniqueID = \'' . $_POST['UniqueID'] . '\' 
				AND s.UserID = u.ID 
				AND s.PublicKey = u.PublicKey 
				AND s.IsDeleted = "0"
			ORDER BY s.ID DESC
			LIMIT 50 
		', false, 'components/account/actions/password.php' ) ) )
	{*/
	if ( !isset( $_POST['Keys'] ) && ( $keys = $database->fetchObjectRows ( '
			SELECT 
				m.* 
			FROM 
				Users u, 
				SBookContact c, 
				SBookMail m 
			WHERE 
					u.ID = \'' . $webuser->ID . '\' 
				AND u.IsDeleted = "0" 
				AND c.UserID = u.ID 
				AND m.ReceiverID = c.ID 
				AND m.Type = "cm" 
				AND m.EncryptionKey != "" 
				AND m.PublicKey = u.PublicKey 
			ORDER BY 
				m.ID DESC 
			LIMIT 50 
		', false, 'components/account/actions/password.php' ) ) )
	{
		$out = array();
		
		foreach ( $keys as $key )
		{
			$obj = new stdClass();
			$obj->ID = $key->ID;
			//$obj->PublicKey = fcrypto::stripHeader( $key->PublicKey );
			$obj->EncryptionKey = fcrypto::stripHeader( $key->EncryptionKey );
			
			$out[] = $obj;
		}
		
		if ( $out )
		{
			die( 'keys<!--separate-->' . json_encode( $out ) );
		}
	}
	
	// Update user with the new public key and update storage with new keys
	
	if ( $_POST['PublicKey'] && ( isset( $_POST['Keys'] ) || !$keys ) )
	{
		$u = new dbObject( 'Users' );
		$u->ID = $webuser->ID;
		$u->UniqueID = $_POST['UniqueID'];
		if ( $u->Load() )
		{
			$u->Password = UniqueKey();
			$u->PublicKey = $_POST['PublicKey'];
			$u->DateModified = date( 'Y-m-d H:i:s' );
			$u->Save();
			
			if ( $_POST['Keys'] )
			{
				$keys = json_decode( $_POST['Keys'] );
				
				if ( $keys && is_array( $keys ) )
				{
					foreach ( $keys as $k )
					{
						if ( $k->ID > 0 )
						{
							// TODO: Maybe this should be a cronscript or it will at least have to wait until chat is properly fixed because there is problems when changing password to new then changing back and creating a new crypto message, it doesn't do type = cm.
							
							/*$m = new dbObject( 'SBookMail' );
							$m->ID = $k->ID;
							if ( $m->Load() )
							{
								$m->EncryptionKey = $k->EncryptionKey;
								$m->PublicKey = $_POST['PublicKey'];
								$m->Save();
							}*/
							
							// TODO: This doesn't seem work ... find a way to update alot of keys on the client without loosing anything
							
							/*$s = new dbObject( 'SBookStorage' );
							$s->ID = $k->ID;
							if ( $s->Load() )
							{
								$s->EncryptionKey = $k->EncryptionKey;
								$s->PublicKey = $u->PublicKey;
								$s->DateModified = date( 'Y-m-d H:i:s' );
								$s->Save();
							}*/
						}
					}
				}
			}
			
			// Login User
			$webuser = new dbUser();
			$webuser->setEncryptionMethod( 'plain' );
			$webuser->reauthenticate( $u->Username, $u->Password );
			if ( $webuser->is_authenticated )
			{
				die( 'ok<!--separate-->Your password was successfully changed' );
			}
			
			die( 'ok<!--separate-->Your password was successfully changed' );
		}
	}
}

die( 'fail' );

?>
