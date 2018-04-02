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

global $webuser, $database;

if ( $_POST )
{
	$found = false;
	
	// Find out what takes to much time ........
	
	// Update Notify to IsNoticed
	if( $nms = $database->fetchObjectRows( $q = '
		SELECT 
			ID 
		FROM 
			SBookMail 
		WHERE
			' . ( isset( $_POST['reverse'] ) ? '
			SenderID = \'' . $webuser->ContactID . '\'
			' . ( $_POST['uid'] > 0 ? '
			AND ReceiverID = \'' . $_POST['uid'] . '\'
			' : '' ) : '
			ReceiverID = \'' . $webuser->ContactID . '\'
			' . ( $_POST['uid'] > 0 ? '
			AND SenderID = \'' . $_POST['uid'] . '\'
			' : '' ) ) . '
			AND ( IsRead = "0" OR IsNoticed = "0" OR IsAlerted = "0" OR IsAccepted = "0" OR IsConnected = "0" ) 
		ORDER BY
			ID DESC
	', false, 'notification/actions/resetnotify.php' ) )
	{
		foreach( $nms as $nm )
		{
			$m = new dbObject( 'SBookMail' );
			if( $m->Load( $nm->ID ) )
			{
				switch( $_POST['type'] )
				{
					case 'read':
						if( $m->IsRead == 0 )
						{
							$found = ( !$found ? 'read [' . $m->ID . ']' : $found );
							$m->IsRead = 1;
							$m->IsNoticed = 1;
							
							$m->IsAlerted = 1;
							$m->DateModified = date( 'Y-m-d H:i:s' );
							$m->Save();
						}
						break;
					
					case 'notified':
						if( $m->IsNoticed == 0 )
						{
							$found = ( !$found ? 'notified [' . $m->ID . ']' : $found );
							$m->IsNoticed = 1;
							
							$m->IsAlerted = 1;
							$m->DateModified = date( 'Y-m-d H:i:s' );
							$m->Save();
						}
						break;
					
					case 'accepted':
						if ( $m->Type == 'vm' && $m->IsAccepted == 0 )
						{
							$found = ( !$found ? 'accepted [' . $m->ID . ']' : $found );
							$m->IsAccepted = 1;
							
							$m->IsAlerted = 1;
							$m->DateModified = date( 'Y-m-d H:i:s' );
							$m->Save();
						}
						break;
					
					case 'declined':
						if ( $m->Type == 'vm' && $m->IsAccepted == 0 )
						{
							$found = ( !$found ? 'declined [' . $m->ID . ']' : $found );
							$m->IsAccepted = -1;
							
							$m->IsAlerted = 1;
							$m->DateModified = date( 'Y-m-d H:i:s' );
							$m->Save();
						}
						break;
					
					case 'connected':
						if ( $m->Type == 'vm' && $m->IsConnected == 0 )
						{
							$found = ( !$found ? 'connected [' . $m->ID . ']' : $found );
							$m->IsConnected = 1;
							
							$m->IsAlerted = 1;
							$m->DateModified = date( 'Y-m-d H:i:s' );
							$m->Save();
						}
						break;
				}
				
				//$m->IsAlerted = 1;
				//$m->DateModified = date( 'Y-m-d H:i:s' );
				//$m->Save();
			}
		}
	}
	
	die( 'ok<!--separate-->' . ( $found ? $found : 'false' ) );
}

die( 'fail' );

?>
