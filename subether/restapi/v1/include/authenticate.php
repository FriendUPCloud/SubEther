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

allowAccess();

include_once ( BASE_DIR . '/subether/classes/fcrypto.class.php' );



// Temporary
if( !$_POST && $_REQUEST )
{
	if( $_REQUEST['route'] ) unset( $_REQUEST['route'] );
	$_POST = $_REQUEST;
}

if ( isset ( $_POST[ 'AuthData' ] ) )
{
	$auth = array();
	
	$json = json_decode( stripslashes( $_POST[ 'AuthData' ] ) );
	
	if ( isset( $json->New ) )
	{
		$required = array(
			'ID', 'Username', 'Password' 
		);
		
		// Delete tokens that has expired since 3 days or 1 hour ago
		$database->query ( '
			DELETE FROM UserLogin 
			WHERE `DateExpired` <= \'' . date( 'Y-m-d H:i:s' ) . '\' 
		' );
		
		foreach( $json->New as $key=>$usr )
		{
			$obj = new stdClass();
			
			foreach( $usr as $k=>$p )
			{
				if( !in_array( $k, $required ) )
				{
					throwXmlError ( MISSING_PARAMETERS, false, 'authenticate' );
				}
			}
			foreach( $required as $r )
			{
				if( !isset( $usr->{$r} ) )
				{
					throwXmlError ( MISSING_PARAMETERS, false, 'authenticate' );
				}
			}
			
			$c = new dbObject( 'SBookContact' );
			$c->NodeMainID = $usr->ID;
			if( $c->Load() )
			{
				// Username and Password Authenticate
				$p = new dbObject ( 'Users' );
				$p->ID = $c->UserID;
				$p->Username = $usr->Username;
				$p->Password = md5($usr->Password);
				// Ok, we found login
				if ( $p = $p->findSingle () )
				{
					$obj->ID = $usr->ID;
					
					// Try to get existing session id!
					$sess = new dbObject ( 'UserLogin' );
					$sess->UserID = $p->ID;
					
					// If we found the token and we are inside the expiry date update token and return found token
					if ( $sess->Load() && $sess->Token && strtotime( $sess->DateExpired ) > time () )
					{
						$sess->IP = $_SERVER['REMOTE_ADDR'];
						$sess->Data = 'api-authenticate-v1-updated';
						$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
						$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
						$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
						$sess->Save ();
						
						// Update login
						$p->DateLogin = date( 'Y-m-d H:i:s' );
						$p->save();
					}
					// Regenerate session id token
					else
					{
						$sess->IP = $_SERVER['REMOTE_ADDR'];
						$sess->DataSource = strtolower( $_POST['Source'] );
						$sess->Data = 'api-authenticate-v1-new';
						$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
						$sess->Token = md5(rand(0,9999).rand(0,9999).rand(0,9999).microtime());
						$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
						$sess->DateCreated = date( 'Y-m-d H:i:s' );
						$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
						$sess->Save ();
						
						// Update login
						$p->DateLogin = date ( 'Y-m-d H:i:s' );
						$p->Save ();
					}
					
					$obj->SessionID = $sess->Token;
				}
			}
			
			if ( isset( $obj->ID ) )
			{
				$auth[] = $obj;
			}
		}
	}
	
	if ( isset( $json->Refresh ) )
	{
		$required = array(
			'ID', 'SessionID' 
		);
		
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
		
		foreach( $json->Refresh as $key=>$usr )
		{
			$obj = new stdClass();
			$obj->ID = $usr->ID;
			
			foreach( $usr as $k=>$p )
			{
				if( !in_array( $k, $required ) )
				{
					throwXmlError ( MISSING_PARAMETERS, false, 'authenticate' );
				}
			}
			foreach( $required as $r )
			{
				if( !isset( $usr->{$r} ) )
				{
					throwXmlError ( MISSING_PARAMETERS, false, 'authenticate' );
				}
			}
			
			// Refresh Session
			$sess = new dbObject ( 'UserLogin' );
			$sess->Token = $usr->SessionID;
			
			if( $sess->Load() )
			{
				// Check if the session has expired (takes 1 hour)
				if( time () > strtotime( $sess->DateExpired ) )
				{
					$sess->Token = '';
					$sess->Save ();
					
					$obj->SessionID = 'expired';
				}
				// Check if the session has expired (takes 10 min)
				else if( strtotime( $sess->LastHeartbeat ) < ( time () - ( 10 * 10 ) ) )
				{
					$sess->Token = '';
					$sess->Save ();
					
					$obj->SessionID = 'expired';
				}
				else
				{
					$sess->IP = $_SERVER['REMOTE_ADDR'];
					$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
					$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
					$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
					$sess->Save ();
					
					// Update login
					//$usr->DateLogin = date( 'Y-m-d H:i:s' );
					//$usr->save();
					
					$obj->SessionID = $sess->Token;
				}
			}
			else
			{
				$obj->SessionID = 'expired';
			}
			
			$auth[] = $obj;
		}
	}
	
	if( $auth )
	{
		showXmlData ( json_encode( $auth ), false, 'authenticate' );
	}
	
	throwXmlError ( AUTHENTICATION_ERROR, false, 'authenticate' );
}
// Username and Password Authenticate
else if ( isset ( $_POST[ 'Username' ] ) && isset ( $_POST[ 'Password' ] ) && isset( $_POST[ 'Source' ] ) )
{
	$p = new dbObject ( 'Users' );
	$p->Username = $_POST['Username'];
	$p->Password = ( isValidMd5( $_POST['Password'] ) ? trim( $_POST['Password'] ) : md5( $_POST['Password'] ) );
	// Ok, we found login
	if ( $p = $p->findSingle () )
	{
		// Delete tokens that has expired since 3 days or 1 hour ago
		$database->query ( '
			DELETE FROM UserLogin 
			WHERE `DateExpired` <= \'' . date( 'Y-m-d H:i:s' ) . '\' 
		' );
		
		// Try to get existing session id!
		$sess = new dbObject ( 'UserLogin' );
		$sess->UserID = $p->ID;
		$sess->DataSource = $_POST['Source'];
		
		// If we found the token and we are inside the expiry date update token and return found token
		if ( $sess->Load() && $sess->Token && strtotime( $sess->DateExpired ) > time () )
		{
			$sess->IP = $_SERVER['REMOTE_ADDR'];
			$sess->Data = 'api-authenticate-v1-updated';
			$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
			$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
			$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
			$sess->Save ();
			
			// Update login
			$p->DateLogin = date( 'Y-m-d H:i:s' );
			$p->save();
		}
		// Regenerate session id token
		else
		{
			$sess->IP = $_SERVER['REMOTE_ADDR'];
			$sess->DataSource = strtolower( $_POST['Source'] );
			$sess->Data = 'api-authenticate-v1-new';
			$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
			$sess->Token = md5(rand(0,9999).rand(0,9999).rand(0,9999).microtime());
			$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
			$sess->DateCreated = date( 'Y-m-d H:i:s' );
			$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
			$sess->Save ();
			
			// Update login
			$p->DateLogin = date ( 'Y-m-d H:i:s' );
			$p->Save ();
		}
		
		showXmlData ( $sess->Token, 'sessionid', 'authenticate' );
	}
	
	throwXmlError ( AUTHENTICATION_ERROR, false, 'authenticate' );
}
// UniqueID authentication based on private and public key with a signed random generated string to verify
else if ( ( isset ( $_POST['Username'] ) || isset ( $_POST['UniqueID'] ) || isset ( $_POST['PublicKey'] ) || isset( $_POST['RecoveryKey'] ) ) && isset ( $_POST['Source'] ) && !isset( $_POST['Email'] ) )
{
	switch ( strtolower( $_POST['Source'] ) )
	{
		case 'node':
			$u = new dbObject( 'SNodes' );
			$u->UniqueID = $_POST['UniqueID'];
			if ( $u->Load() && $u->PublicKey )
			{
				$fcrypt = new fcrypto();
				
				if ( !isset( $_POST['Signature'] ) )
				{
					$u->AuthKey = UniqueKey();
					$u->Save();
					
					$encrypted = $fcrypt->encryptRSA( $u->AuthKey, $u->PublicKey );
					$ciphertext = $encrypted;
					
					if ( $u->ID > 0 && $ciphertext )
					{
						showXmlData ( $ciphertext, 'authkey', 'authenticate' );
					}
				}
				else if ( $u->AuthKey && $fcrypt->verifyString( $u->AuthKey, $_POST['Signature'], $u->PublicKey ) )
				{
					// Delete tokens that has expired since 3 days or 1 hour ago
					$database->query ( '
						DELETE FROM UserLogin 
						WHERE `DateExpired` <= \'' . date( 'Y-m-d H:i:s' ) . '\' 
					' );
					
					// Try to get existing session id!
					$sess = new dbObject ( 'UserLogin' );
					$sess->NodeID = $u->ID;
					$sess->DataSource = strtolower( $_POST['Source'] );
					
					// If we found the token and we are inside the expiry date update token and return found token
					if ( $sess->Load() && $sess->Token )
					{
						$sess->IP = $_SERVER['REMOTE_ADDR'];
						$sess->Data = 'api-authenticate-v2-updated';
						$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
						$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
						$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
						$sess->Save ();
						
						// Update login
						$u->DateLogin = date( 'Y-m-d H:i:s' );
						$u->save();
					}
					// Regenerate session id token
					else
					{
						$sess->IP = $_SERVER['REMOTE_ADDR'];
						$sess->Data = 'api-authenticate-v2-new';
						$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
						$sess->Token = md5(rand(0,9999).rand(0,9999).rand(0,9999).microtime());
						$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
						$sess->DateCreated = date( 'Y-m-d H:i:s' );
						$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
						$sess->Save ();
						
						// Update login
						$u->DateLogin = date ( 'Y-m-d H:i:s' );
						$u->Save ();
					}
					
					if ( $sess->Token )
					{
						$encrypted = $fcrypt->encryptRSA( $sess->Token, $u->PublicKey );
						
						showXmlData ( ( $encrypted ? $encrypted : $sess->Token ), 'sessionid', 'authenticate' );
					}
				}
			}
			
			throwXmlError ( AUTHENTICATION_ERROR, false, 'authenticate' );
			
			break;
		
		// Default auth for users
		
		default:
			
			if ( isset( $_POST['RecoveryKey'] ) && isset( $_POST['UniqueID'] ) && isset( $_POST['PublicKey'] ) )
			{
				$u = new dbObject( 'Users' );
				$u->IsDeleted = 0;
				$u->IsDisabled = 1;
				$u->UniqueID = trim( $_POST['UniqueID'] );
				$u->AuthKey = md5( trim( $_POST['RecoveryKey'] ) );
				if ( $u->Load() )
				{
					// Save new password
					$u->IsDisabled = 0;
					$u->AuthKey = '';
					//$u->Password = UniqueKey();
					//$u->Password = md5(rand(0,9999).rand(0,9999).rand(0,9999).microtime());
					$u->Password = hash( 'sha1', rand(0,9999).rand(0,9999).rand(0,9999).microtime() );
					$u->PublicKey = $_POST['PublicKey'];
					$u->DateModified = date( 'Y-m-d H:i:s' );
					$u->Save();
				}
			}
			
			if ( isset( $_POST['Username'] ) )
			{
				$u = new dbObject( 'Users' );
				$u->IsDeleted = 0;
				$u->Username = trim( $_POST['Username'] );
				if ( $u->Load() )
				{
					// If user doesn't have a uniqueid make one
					if ( !$u->UniqueID )
					{
						$u->UniqueID = UniqueKey( $u->Username );
						$u->Save();
					}
					
					if ( $u->ID > 0 && $u->UniqueID )
					{
						showXmlData ( $u->UniqueID, 'uniqueid', 'authenticate' );
					}
				}
			}
			else
			{
				$u = new dbObject( 'Users' );
				$u->UniqueID = trim( $_POST['UniqueID'] );
				if ( $u->Load() )
				{
					$fcrypt = new fcrypto();
					
					// Just for logging ...
					$_REQUEST['Password'] = $u->Password;
					
					if( $u->PublicKey )
					{
						$_REQUEST['PublicKey'] = $u->PublicKey;
					}
					
					if ( !isset( $_POST['Signature'] ) )
					{
						if ( !$u->PublicKey && $_POST['PublicKey'] )
						{
							$u->PublicKey = $_POST['PublicKey'];
						}
						
						//$u->Password = UniqueKey();
						//$u->Password = md5(rand(0,9999).rand(0,9999).rand(0,9999).microtime());
						$u->Password = hash( 'sha1', rand(0,9999).rand(0,9999).rand(0,9999).microtime() );
						$u->InActive = 0;
						$u->IsDisabled = 0;
						$u->AuthKey = '';
						$u->Save();
						
						if ( $u->PublicKey )
						{
							$ciphertext = $fcrypt->encryptRSA( $u->Password, $u->PublicKey );
							
							if ( $u->ID > 0 && $ciphertext )
							{
								// Just for logging ...
								$_REQUEST['Password'] = $u->Password;
								$_REQUEST['PublicKey'] = $u->PublicKey;
								
								showXmlData ( $ciphertext, 'password', 'authenticate' );
							}
						}
					}
					else if ( $u->Password && $fcrypt->verifyString( $u->Password, $_POST['Signature'], $u->PublicKey ) )
					{
						$test = array();
						
						// Delete tokens that has expired since 3 days or 1 hour ago
						$database->query ( '
							DELETE FROM UserLogin 
							WHERE `DateExpired` <= \'' . date( 'Y-m-d H:i:s' ) . '\' 
						' );
						
						// Try to get existing session id!
						$sess = new dbObject ( 'UserLogin' );
						$sess->UserID = $u->ID;
						$sess->DataSource = strtolower( $_POST['Source'] );
						
						// If we found the token and we are inside the expiry date update token and return found token
						if ( $sess->Load() && $sess->Token && strtotime( $sess->DateExpired ) > time () )
						{
							$sess->IP = $_SERVER['REMOTE_ADDR'];
							$sess->Data = 'api-authenticate-v2-updated';
							$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
							$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
							$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
							$sess->Save ();
							
							// Update login
							$u->DateLogin = date( 'Y-m-d H:i:s' );
							$u->save();
						}
						// Regenerate session id token
						else
						{
							$sess->IP = $_SERVER['REMOTE_ADDR'];
							$sess->DataSource = strtolower( $_POST['Source'] );
							$sess->Data = 'api-authenticate-v2-new';
							$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
							$sess->Token = md5(rand(0,9999).rand(0,9999).rand(0,9999).microtime());
							$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
							$sess->DateCreated = date( 'Y-m-d H:i:s' );
							$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
							$sess->Save ();
							
							// Update login
							$u->DateLogin = date ( 'Y-m-d H:i:s' );
							$u->Save ();
						}
						
						$test['UserID'] = $sess->UserID;
						$test['ID'] = $sess->ID;
						$test['IP'] = $sess->IP;
						$test['DataSource'] = $sess->DataSource;
						$test['UserAgent'] = $sess->UserAgent;
						$test['Token'] = $sess->Token;
						
						if ( $u->ID > 0 && $sess->Token )
						{
							showXmlData ( $sess->Token, 'sessionid', 'authenticate' );
						}
					}
				}
			}
			
			throwXmlError ( AUTHENTICATION_ERROR, false, 'authenticate' );
			
			break;
	}
	
	throwXmlError ( AUTHENTICATION_ERROR, false, 'authenticate' );
}
// SessionID Authenticate
else if ( isset ( $_POST['SessionID'] ) )
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
	$sess->Token = $_POST['SessionID'];
	if ( $sess->Load () )
	{
		$u = new dbObject ( 'Users' );
		$u->Load ( $sess->UserID );
		
		$c = new dbObject ( 'SBookContact' );
		$c->UserID = $sess->UserID;
		$c->Load ();
		
		// Check if the session has expired (takes 1 hour)
		if( time () > strtotime( $sess->DateExpired ) )
		{
			$sess->Token = '';
			$sess->Save ();
			
			throwXmlError ( SESSION_EXPIRED, false, 'authenticate' );
		}
		// Check if the session has expired (takes 10 min)
		else if( strtotime( $sess->LastHeartbeat ) < ( time () - ( 10 * 10 ) ) )
		{
			$sess->Token = '';
			$sess->Save ();
			
			throwXmlError ( SESSION_EXPIRED, false, 'authenticate' );
		}
		else if( $u->ID > 0 && $c->ID > 0 )
		{
			// Signal our presense
			$stat = new dbObject ( 'SBookStatus' );
			$stat->UserID = $u->ID;
			$stat->Module = 'presense';
			$stat->Status = 'online';
			$stat->Load ();
			$stat->Token = $sess->Token;
			$stat->DataSource = 'node-session' . ( $sess->DataSource ? '(' . $sess->DataSource . ')' : '' );
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
			
			showXmlData ( $c->ID, false, 'authenticate' );
		}
	}
	
	throwXmlError ( SESSION_MISSING, false, 'authenticate' );
}
// New method of authentication more secure and easier
else if( isset( $_REQUEST['Email'] ) && isset( $_REQUEST['Source'] ) )
{
	$usr = new dbObject( 'Users' );
	$usr->Username = trim( $_REQUEST['Email'] );
	if( isset( $_REQUEST['UniqueID'] ) )
	{
		$usr->UniqueID = trim( $_REQUEST['UniqueID'] );
	}
	if ( $usr->Load() )
	{
		$fcrypt = new fcrypto();
		
		if ( $usr->PublicKey )
		{
			// Delete tokens that has expired since 3 days or 1 hour ago
			$database->query ( '
				DELETE FROM UserLogin 
				WHERE `DateExpired` <= \'' . date( 'Y-m-d H:i:s' ) . '\' 
			' );
			
			// Try to get existing session id!
			$sess = new dbObject ( 'UserLogin' );
			$sess->UserID = $usr->ID;
			$sess->DataSource = strtolower( $_REQUEST['Source'] );
			
			// If we found the token and we are inside the expiry date update token and return found token
			if ( $sess->Load() && $sess->Token )
			{
				$sess->IP = $_SERVER['REMOTE_ADDR'];
				$sess->Data = 'api-v1-authenticate-updated';
				$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
				$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
				$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
				$sess->Save ();
			}
			// Regenerate session id token
			else
			{
				$sess->IP = $_SERVER['REMOTE_ADDR'];
				$sess->Data = 'api-v1-authenticate-new';
				$sess->UserAgent = ( function_exists( 'ReadUserAgent' ) ? ReadUserAgent( $_SERVER['HTTP_USER_AGENT'] ) : '' );
				$sess->Token = md5(rand(0,9999).rand(0,9999).rand(0,9999).microtime());
				$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
				$sess->DateCreated = date( 'Y-m-d H:i:s' );
				$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) );
				$sess->Save ();
			}
			
			if ( $sess->Token && ( $token = $fcrypt->encryptRSA( $sess->Token, $usr->PublicKey ) ) )
			{
				$xml  = "\t\t<UniqueID>" . $usr->UniqueID . "</UniqueID>\n";
				$xml .= "\t\t<Token>" . $token . "</Token>\n";
				
				$json = new stdClass();
				$json->UniqueID = $usr->UniqueID;
				$json->Token = $token;
				
				outputXML ( ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' ? $json : $xml ), false, 'authenticate' );
				
				//showXmlData ( $token, 'Token', 'authenticate' );
			}
		}
	}
	
	throwXmlError ( AUTHENTICATION_ERROR, false, 'authenticate' );
}

// Give default error
throwXmlError ( MISSING_PARAMETERS, false, 'authenticate' );

?>
