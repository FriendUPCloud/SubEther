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

// Heartbeat function to control Sub-Ether

global $document, $database, $webuser;

include_once ( 'subether/functions/userfuncs.php' );

if( $webuser->ID > 0 )
{
	$document->addResource ( 'javascript', 'lib/javascript/bajax.js' );
	$document->addResource ( 'javascript', 'extensions/session/heartbeat.js' );

	// Make sure we have subether book session storage
	if ( !isset ( $_SESSION['sb'] ) || !is_array ( $_SESSION['sb'] ) )
	{
		$_SESSION['sb'] = array ();
	}
	
	// Signal our presense
	$status = new dbObject ( 'SBookStatus' );
	$status->UserID = $webuser->ID;
	$status->Module = 'presense';
	$status->Status = 'online';
	$status->Load ();
	$status->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
	$status->LastActivity = date ( 'Y-m-d H:i:s' );
	$status->Save ();
	
	// Delete online status where activiti was 60 mins ago
	$database->query ( '
		DELETE FROM SBookStatus
		WHERE `Status`=\'online\'
		AND `LastActivity` <= \'' . date ( 'Y-m-d H:i:s', time () - 3200 ) . '\'
	' );
	
	// Delete tokens that has expired since 3 days ago
	//$database->query ( '
	//	DELETE FROM UserLogin 
	//	WHERE `DateExpired` <= \'' . date ( 'Y-m-d H:i:s' ) . '\' 
	//' );
	
	// Go through session bank	
	foreach ( $_SESSION['sb'] as $mod=>$modval )
	{
		if ( !isset ( $mod ) || !trim ( $mod ) ) continue;
		if ( file_exists ( 'subether/modules/' . $mod ) )
		{
			foreach ( $modval as $key=>$array )
			{
				// Defunct session data is skipped
				if ( !is_array ( $array ) && $array == false ) continue;
				// Old data is set to defunct
				if ( $array['LastActivity'] < time()-120 )
				{
					$_SESSION['sb'][$mod][$key] = false;
				}
				// Good data is processed and renewed
				else
				{
					$status = new dbObject ( 'SBookStatus' );
					$status->UserID = $webuser->ID;
					$status->Module = $mod;
					$status->Component = $key;
					$status->Load ();
					$status->Status = $array['Status'];
					$status->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
					$status->LastActivity = date ( 'Y-m-d H:i:s', $array['LastActivity'] );
					$status->CategoryID = $array['CategoryID'];
					$status->Save ();
				}
				// Delete old stuff (for all users)
				$database->query ( '
					DELETE FROM SBookStatus 
					WHERE 
						`Module` = \'' . $mod . '\' AND 
						`Component`=\'' . $key . '\' AND 
						`LastActivity` <= \'' . date ( 'Y-m-d H:i:s', time () - 120 ) . '\'
				' );
			}
		}
	}

	// Get a refresh session directive
	if( $_REQUEST['session'] == 'refresh' )
	{
		if( $sess = $database->fetchObjectRow ( 'SELECT * FROM UserLogin WHERE UserID = \'' . $webuser->ID . '\' ORDER BY ID DESC LIMIT 1' ) )
		{
			$s = new dbObject( 'UserLogin' );
			if( $s->Load( $sess->ID ) )
			{
				// Store IP Address too
				$remote = $_SERVER['HTTP_X_FORWARDED_FOR'];
				if ( !$remote ) $remote = $_SERVER['REMOTE_ADDR'];
				
				$s->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
				$s->LastHeartbeat = date( 'Y-m-d H:i:s' );
				$s->IP = $remote;
				$s->Save();
				
				output( 'LastHeartbeat: ' . $s->LastHeartbeat . ' ID: ' . $s->ID );
			}
		}
	}
}
output( 'fail' );

?>
