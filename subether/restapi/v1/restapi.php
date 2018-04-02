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

include_once ( 'subether/restapi/functions.php' );

// Logg all ...
logActivity ( false, '$_REQUEST', ( $_REQUEST ? json_encode( $_REQUEST ) : false ), 'all-api.log' );

// --- Last Check (27.12.2017) ---------------------------

// [x] /documentation/
// [x] /information/
// [-] /connect/nodes/
// [-] /connect/contacts/
// [-] /connect/groups/
// [x] /authenticate/
// [x] /secure-files/
// [x] /parse/
// [x] /components/wall/
// [x] /components/category/
// [x] /components/library/
// [x] /components/contacts/
// [x] /components/contacts/relations/
// [x] /components/contacts/requests/
// [x] /components/groups/
// [x] /components/chat/messages/
// [x] /components/chat/post/
// [-] /components/messages/
// [-] /components/irc/
// [x] /components/events/
// [x] /components/events/save/
// [x] /components/events/delete/
// [x] /components/register/
// [x] /components/register/recover/
// [x] /components/register/activate/
// [-] /components/register/invite/
// [-] /components/register/limited/
// [x] /components/statistics/
// [-] /components/notification/
// [-] /components/notification/contacts/
// [x] /components/notification/messages/
// [-] /components/notification/notices/

// [x] = Checked/ Documented / In use
// [-] = Not in use / Deprecated / Unfinished

// -------------------------------------------------------

include_once ( 'subether/classes/posthandler.class.php' );
include_once ( 'subether/classes/library.class.php' );
include_once ( 'subether/classes/fcrypto.class.php' );



// Default
define( 'MISSING_PROTOCOL_IDENTIFIER', '0000' );
define( 'MISSING_PARAMETERS', '0001' );
define( 'EMPTY_LIST', '0002' );
define( 'AUTHENTICATION_ERROR', '0003' );
define( 'SESSION_MISSING', '0004' );
define( 'SESSION_EXPIRED', '0005' );
define( 'ACCOUNT_EXISTS', '0006' );
define( 'REGISTRATION_FAILED', '0007' );
define( 'MISSING_INVITATION', '0008' );
define( 'PARAMETERS_EXISTS', '0009' );
define( 'KEYGEN_FAILED', '0010' );
define( 'ACTIVATION_FAILED', '0011' );
define( 'ACCESS_DENIED', '0012' );
define( 'WRONG_JSON_FORMAT', '0013' );
define( 'PARAMETERS_NORESULT', '0014' );
define( 'NO_NEW_UPDATES', '0015' );



function verifySessionId ( $sessionid = false, $internal = false )
{
	global $database, $webuser;
	
	$sessionid = ( $sessionid ? $sessionid : $_POST['SessionID'] );
	
	// Temporary to view data i browser for development
	if( !isset( $_POST['SessionID'] ) && isset( $_REQUEST['SessionID'] ) )
	{
		$sessionid = $_REQUEST['SessionID'];
	}
	
	if ( !$internal && $sessionid )
	{
		$check = new dbObject ( 'UserLogin' );
		$check->Token = $sessionid;
		if ( $check->Load () )
		{
			// Delete tokens that has expired since 3 days or 1 hour ago
			$database->query ( '
				DELETE FROM UserLogin 
				WHERE `DateExpired` <= \'' . date( 'Y-m-d H:i:s' ) . '\' 
			' );
	
			// Delete online status where activiti was 60 mins ago
			$database->query ( '
				DELETE FROM SBookStatus
				WHERE `Status`=\'online\'
				AND `LastActivity` <= \'' . date ( 'Y-m-d H:i:s', time () - 3200 ) . '\'
			' );
			
			$sess = new dbObject ( 'UserLogin' );
			$sess->Token = $sessionid;
			if ( $sess->Load () )
			{
				if( $sess->UserID > 0 )
				{
					$u = new dbObject ( 'Users' );
					$u->Load( $sess->UserID );
			
					if ( !isset( $u->ContactID ) && $u->ID > 0 )
					{
						$c = new dbObject ( 'SBookContact' );
						$c->UserID = $u->ID;
						if ( $c->Load() )
						{
							$u->ContactID = $c->ID;
						}
					}
				}
				else if( $sess->NodeID > 0 )
				{
					$u = new dbObject ( 'SNodes' );
					$u->Load( $sess->NodeID );
				}
			
				if( $sess->UserID > 0 )
				{
					// Signal our presense
					$stat = new dbObject ( 'SBookStatus' );
					$stat->UserID = $u->ID;
					$stat->Module = 'presense';
					$stat->Status = 'online';
					$stat->Load ();
					$stat->Token = $sess->Token;
					$stat->DataSource = 'node-verify' . ( $sess->DataSource ? '(' . $sess->DataSource . ')' : '' );
					$stat->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
					$stat->LastActivity = date ( 'Y-m-d H:i:s' );
					$stat->DateExpired = $sess->DateExpired;
					$stat->Save ();
				
					$sess->IP = $_SERVER['REMOTE_ADDR'];
					$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
					$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
					$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
					$sess->Save ();
				
					// Update login
					$u->DateLogin = date( 'Y-m-d H:i:s' );
					$u->save();
				}
				
				// Just show session token which isn't expired
				return $u;
			}
			else
			{
				throwXmlError ( SESSION_EXPIRED, false, 'authenticate' );
			}
		}
	}
	// TODO: Make this more intuitive and combine it with the one over as an alternative to post sessionid
	else if ( $internal && $sessionid )
	{
		$sess = new dbObject ( 'UserLogin' );
		$sess->Token = $sessionid;
		if ( $sess->Load () )
		{
			if( $sess->UserID > 0 )
			{
				$u = new dbObject ( 'Users' );
				$u->Load( $sess->UserID );
			
				if ( !isset( $u->ContactID ) && $u->ID > 0 )
				{
					$c = new dbObject ( 'SBookContact' );
					$c->UserID = $u->ID;
					if ( $c->Load() )
					{
						$u->ContactID = $c->ID;
					}
				}
			
				// Signal our presense
				$stat = new dbObject ( 'SBookStatus' );
				$stat->UserID = $u->ID;
				$stat->Module = 'presense';
				$stat->Status = 'online';
				$stat->Load ();
				$stat->Token = $sess->Token;
				$stat->DataSource = 'node-verify' . ( $sess->DataSource ? '(' . $sess->DataSource . ')' : '' );
				$stat->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
				$stat->LastActivity = date ( 'Y-m-d H:i:s' );
				$stat->DateExpired = $sess->DateExpired;
				$stat->Save ();
			
				$sess->IP = $_SERVER['REMOTE_ADDR'];
				$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
				$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
				$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
				$sess->Save ();
			
				// Update login
				$u->DateLogin = date( 'Y-m-d H:i:s' );
				$u->save();
			}
			else if( $sess->NodeID > 0 )
			{
				$u = new dbObject ( 'SNodes' );
				$u->Load( $sess->NodeID );
			}
			
			return $u;
		}
	}
	else if( !$internal && !$sessionid && $webuser->ID > 0 )
	{
		if( $webuser->GetToken() )
		{
			$sess = new dbObject ( 'UserLogin' );
			$sess->Token = $webuser->GetToken();
			if ( $sess->Load () )
			{
				$u = new dbObject ( 'Users' );
				$u->Load( $sess->UserID );
				
				if ( !isset( $u->ContactID ) && $u->ID > 0 )
				{
					$c = new dbObject ( 'SBookContact' );
					$c->UserID = $u->ID;
					if ( $c->Load() )
					{
						$u->ContactID = $c->ID;
					}
				}
				
				$_POST['SessionID'] = $webuser->GetToken();
				
				return $u;
			}
		}
	}
	else if( $internal && !$sessionid && isset( $_COOKIE["arenaweb_UserToken"] ) )
	{
		
		$webuser = new dbUser ( );
		$webuser->authenticate ( 'web' );
		if ( $webuser->is_authenticated )
		{
			$u = new dbObject ( 'Users' );
			if( $u->Load( $webuser->ID ) )
			{
				if ( !isset( $u->ContactID ) && $u->ID > 0 )
				{
					$c = new dbObject ( 'SBookContact' );
					$c->UserID = $u->ID;
					if ( $c->Load() )
					{
						$u->ContactID = $c->ID;
					}
				}
				
				dbObject::globalValueSet ( 'webuser', $webuser );
			
				$_POST['SessionID'] = $webuser->GetToken();
			
				return $u;
			}
		}
		
		$webuser = false;
		
		return false;
	}
	
	if( !$internal )
	{
		throwXmlError ( SESSION_MISSING, false, 'authenticate' );
	}
}



// Sitemap: --------------------------------------------------------------------
if ( preg_match ( '/\/sitemap.xml/i', $_SERVER['REQUEST_URI'], $matches ) )
{
	require ( 'subether/restapi/v1/sitemap/sitemap.php' );
}

// Encoding: json: -------------------------------------------------------------
if ( preg_match ( '/api-json\//i', $_REQUEST['route'], $matches ) )
{
	$_REQUEST['Encoding'] = 'json';
}

// Command: plugins: -----------------------------------------------------------
if ( preg_match ( '/plugins\//i', $_REQUEST['route'], $matches ) )
{
	require ( 'subether/restapi/v1/plugins/restapi.php' );
}

// Command: documentation: -------------------------------------------------------
if ( preg_match ( '/documentation\//i', $_REQUEST['route'], $matches ) )
{
	require ( 'subether/restapi/v1/include/documentation.php' );
}

// Command: information: -------------------------------------------------------
if ( preg_match ( '/information\//i', $_REQUEST['route'], $matches ) )
{
	require ( 'subether/restapi/v1/include/information.php' );
}

// Command: connect: -----------------------------------------------------------
//if ( preg_match ( '/connect\//i', $_REQUEST['route'], $matches ) )
//{
//	require ( 'subether/restapi/v1/include/connect.php' );
//}

// Command: authenticate: ------------------------------------------------------
if ( preg_match ( '/authenticate\//i', $_REQUEST['route'], $matches ) )
{
	require ( 'subether/restapi/v1/include/authenticate.php' );
}

// Command: secure-files: ------------------------------------------------------
if ( preg_match ( '/secure-files\//i', $_REQUEST['route'], $matches ) )
{
	require ( 'subether/restapi/v1/components/library/include/secure.php' );
}

// Command: parse: -------------------------------------------------------------
if ( preg_match ( '/parse\//i', $_REQUEST['route'], $matches ) )
{
	require ( 'subether/restapi/v1/components/wall/include/parse.php' );
}

// Command: register: ----------------------------------------------------------
if ( preg_match ( '/components\/register\//i', $_REQUEST['route'], $matches ) )
{
	require ( 'subether/restapi/v1/components/register/restapi.php' );
}

// Command: contacts: ----------------------------------------------------------
if ( preg_match ( '/components\/contacts\//i', $_REQUEST['route'], $matches ) )
{
	require ( 'subether/restapi/v1/components/contacts/restapi.php' );
}

// Command: post: --------------------------------------------------
if ( preg_match ( '/components\/chat\/post\//i', $_REQUEST[ 'route' ], $matches ) )
{
	require ( 'subether/restapi/v1/components/chat/actions/messages.php' );
}

// Command: components: --------------------------------------------------------
if ( preg_match ( '/components\//i', $_REQUEST['route'], $matches ) && verifySessionId() )
{
	require ( 'subether/restapi/v1/include/components.php' );
}

?>
