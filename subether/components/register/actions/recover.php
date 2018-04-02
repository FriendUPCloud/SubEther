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

$new = false;

if ( $parent && $_REQUEST['UniqueID'] && $_REQUEST['RecoveryKey'] && $_REQUEST['PublicKey'] )
{
	if ( $usr = $database->fetchObjectRow( '
		SELECT
			*
		FROM
			`Users`
		WHERE
				IsDeleted = "0"
			AND IsDisabled = "1" 
			AND UniqueID = \'' . trim( $_REQUEST['UniqueID'] ) . '\'
			AND AuthKey = \'' . trim( $_REQUEST['RecoveryKey'] ) . '\' 
		ORDER BY
			ID DESC 
	' ) )
	{
		$us = new dbObject( 'Users' );
		$us->ID = $usr->ID;
		if ( $us->Load() )
		{
			// Save new password
			$us->InActive = 0;
            $us->IsDisabled = 0;
			$us->AuthKey = '';
			$us->Password = UniqueKey();
			$us->PublicKey = $_REQUEST['PublicKey'];
			$us->DateModified = date( 'Y-m-d H:i:s' );
			$us->Expires = date( '0000-00-00 00:00:00.000000' );
			$us->Save();
			
			// Create user or update user when activated
			$co = new dbObject( 'SBookContact' );
			$co->UserID = $usr->ID;
			if ( !$co->Load() )
            {
                $new = true;
                
                $co->Username = $usr->Name;
                $co->Email = $usr->Email;
                $co->DateCreated = date( 'Y-m-d H:i:s' );
            }
			$co->DateModified = date( 'Y-m-d H:i:s' );
			$co->Save();
			
            if ( $new )
            {
                $gr = new dbObject( 'Groups' );
                $gr->Name = 'SocialNetwork';
                if( !$gr->Load() )
                {
                    $gr->Save();
                }
                
                $ug = new dbObject( 'UsersGroups' );
                $ug->GroupID = $gr->ID;
                $ug->UserID = $us->ID;
                if( !$ug->Load() )
                {
                	$ug->Save();
                }	
                
                if( function_exists( 'assignToNewMembers' ) )
				{
                	// Assign to SubEther user/group
                	assignToNewMembers( $co->ID );
                }
            }
            
			// Login User
			$webuser = new dbUser();
			$webuser->setEncryptionMethod( 'plain' );
			$webuser->reauthenticate( $us->Username, $us->Password );
			if ( $webuser->is_authenticated )
			{
				if ( isset( $_REQUEST['bajaxrand'] ) )
				{
					die( 'ok<!--separate-->' . $parent->domain . $parent->path . 'home/' );
				}
				else
				{
					if ( isset( $_REQUEST['refresh'] ) )
					{
						header( 'Location: index.php' );
					}
					else
					{
						// Redirect to main view when logged in
						header( 'Location: ' . $parent->domain . $parent->path . 'home/' );
					}
					
					die( 'ok' );
				}
			}
		}
	}
	
	die( 'fail<!--separate-->Wrong RecoveryKey or Username, try again' );
}

die( 'fail<!--separate-->Something whent wrong contact support' );

?>
