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

if( $webuser && isset( $_POST[ 'users' ] ) && isset( $_POST[ 'message' ] ) && ( isset( $_POST[ 'internal' ] ) || isset( $_POST[ 'email' ] ) ) )
{
	$sender = SBookContact( $webuser->ID );
	$users = explode( ',', $_POST[ 'users' ] );
	if( $users && $sender )
	{
		foreach( $users as $uid )
		{
			if( strstr( $uid, 'CustomMail:' ) && strstr( $uid, '@' ) && strstr( $uid, '.' ) )
			{
				$reciever = new stdClass();
				$reciever->Email = str_replace( 'CustomMail:', '', $uid );
			}
			else
			{
				$reciever = SBookContact( $uid );
			}
			
			if( $reciever && $reciever->UserID && isset( $_POST[ 'internal' ] ) )
			{
				$m = new dbObject( 'SBookMail' );
				$m->SenderID = $sender->UserID;
				$m->ReceiverID = $uid;
				$m->CategoryID = 0;
				$m->Message = $_POST[ 'message' ];
				$m->Date = date( 'Y-m-d H:i:s' );
				$m->Save();
			}
			if( $reciever && $reciever->Email && $sender->Email && isset( $_POST[ 'email' ] ) )
			{
				$us = 'Message From ' . $parent->folder->Name;
				$um = $_POST[ 'message' ];
				$ur = $reciever->Email;
				$ut = 'html';
				$uf = $sender->Email;
				mailNow_ ( $us, $um, $ur, $ut, $uf );
			}
		}
		
		/*$a = new cPTemplate ( 'subether/templates/standardemail.php' );
		$a->subject = $us;
		$a->body = $um;
		$article = $a->render ();*/
		
		die( 'ok<!--separate-->' );
	}
}
die( 'fail' );

?>
