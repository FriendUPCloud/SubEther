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

if ( isset( $_REQUEST[ 'recover' ] ) )
{
    $fields = array( 'Username'/*, 'Email', 'Telephone', 'Mobile', 'Name'*/ );
    
    foreach ( $fields as $fld )
    {
        if ( $c = $database->fetchObjectRow( 'SELECT * FROM `Users` WHERE `' . $fld . '` = \'' . trim( $_REQUEST['recover'] ) . '\' ' ) )
        {
            $u = new dbObject( 'Users' );
            $u->ID = $c->ID;
            if ( $u->Load() && ( $newkey = trim( generateHumanPassword() ) ) )
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
                    }
                }
                
                //$link  = BASE_URL . 'register/?recover=' . ( $u->UniqueID ? $u->UniqueID : $usu->UniqueID ) . '&user=' . $u->Username . '&key=' . $newkey . '&auto=login';
                $link  = BASE_URL . 'register/?recover=' . ( $u->UniqueID ? $u->UniqueID : $usu->UniqueID ) . '&user=' . $u->Username . '&key=' . $newkey;
                $link2 = BASE_URL . 'register/?recover=' . ( $u->UniqueID ? $u->UniqueID : $usu->UniqueID ) . '&user=' . $u->Username;
                
                // Send account info to email
                $cs  = 'Account Recovery';
                $cr  = ( strstr( $u->Username, '@' ) ? $u->Username : $u->Email );
                $cm  = 'Username: ' . $u->Username . ' <br>';
                $cm .= 'RecoveryKey: ' . $newkey . ' <br>';
                $cm .= 'Password(should be changed): ' . $newkey . ' <br>';
                $cm .= 'Click this link: <a href="' . $link . '">recovery link</a> ';
                $cm .= 'to recover your account <br>';
                $ct  = 'html';
                
                $res = mailNow_ ( $cs, $cm, $cr, $ct );
                
                if( $res && $res['ok'] )
				{
		            // Save new password
		            $u->IsDisabled = 1;
		            $u->AuthKey = md5( $newkey );
		            $u->DateModified = date( 'Y-m-d H:i:s' );
		            $u->Save();
		            
		            die( 'ok<!--separate-->Your account info is sendt to: ' . $cr . '<!--separate-->' . $link2 );
                }
                else if( $res && !$res['ok'] )
				{
					die( 'ok<!--separate-->There was an error trying to send mail, contact the site webmaster: ' . $res['error'] );
				}
            }
            
            die( 'fail<!--separate-->There was an error with creating a new password, contact the site webmaster.' );
        }
    }
    
    die( 'fail<!--separate-->Couldn\'t find your account info, try again.' );
}

die( 'fail' );

?>
