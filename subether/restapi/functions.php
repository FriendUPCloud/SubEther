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

function msgCodes ( $code, $reason = false )
{
	if( !$code ) return false;
	
	// List of all the possible errors
	$ers = array(
		'0000' => 'Missing protocol identifier',
		'0001' => 'No variables specified',
		'0002' => 'Nothing to list',
		'0003' => 'Wrong username / or password',
		'0004' => 'Session not found',
		'0005' => 'Session expired',
		'0006' => 'Account exists',
		'0007' => 'Registration failed',
		'0008' => 'Invitation not found',
		'0009' => 'Parameters allready exists',
		'0010' => 'Keygen failed',
		'0011' => 'Activation failed',
		'0012' => 'Access denied',
		'0013' => 'Wrong json format',
		'0014' => 'Parameters gave no result',
		'0015' => 'No new updates'
	);
	
	$rse = array(
		'0000' => 'You need to specify the protocol you wish to use in the URL.',
		'0001' => 'You have forgotten the parameters.',
		'0002' => 'There is nothing to list from the database.',
		'0003' => 'You need to provide the correct username / or password.',
		'0004' => 'You need to provide username and password, or session ID.',
		'0005' => 'You need to reauthenticate.',
		'0006' => 'Your account is allready registrated try authenticating instead.',
		'0007' => 'Account registration failed, contact node administrator.',
		'0008' => 'Your parameters doesn\'t match invited list.',
		'0009' => 'One or more of your parameters allready exists.',
		'0010' => 'Creation of key/token failed, contact support.',
		'0011' => 'Parameters didn\'t match or account is allready activated.',
		'0012' => 'Your request has been denied.',
		'0013' => 'Your json string is invalid, check that it is standard json format or that your parameters are allowed.',
		'0014' => 'Your parameters didn\t give any result from the database.',
		'0015' => 'There was no new updates, restart polling.'
	);
	
	return ( $reason ? $rse[ $code ] : $ers[ $code ] );
}

function setXmlHeader()
{
	if( !isset( $GLOBALS['headerset'] ) )
	{
		if ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' )
		{
			header ( 'Content-type: text/json; charset=utf-8' );
		}
		else
		{
			header ( 'Content-type: text/xml; charset=utf-8' );
		}
		
		$GLOBALS['headerset'] = true;
	}
}

function throwXmlError ( $err, $res = false, $func = false, $enc = false )
{
	global $database;
	
	// Close mysql socket ...
	$database->close();
	
	if( !$err ) return false;
	
	$enc = ( isset( $_REQUEST['Encoding'] ) ? $_REQUEST['Encoding'] : $enc );
	
	// Make sure this error exists.
	if( !msgCodes( $err ) || msgCodes( $err ) == null )
	{
		header( "HTTP/1.1 500 Internal Server Error" );
		die( '500 Internal Server Error: What error?');
	}
	
	$cod = $err;
	
	// Get the errors
	$res = !$res ? msgCodes( $err, true ) : $res;
	$err = msgCodes( $err );
	
	// Format the error string, if needed
	$arg = func_get_args();
	$err = vsprintf( $err, $arg );
	
	$timestamp = strtotime( date( 'Y-m-d H:i:s' ) );
	
	if ( $enc && strtolower( $enc ) == 'json' )
	{
		if( !isset( $GLOBALS['headerset'] ) )
		{
			header ( 'Content-type: text/json; charset=utf-8' );
		}
		
		$obj = new stdClass();
		$obj->response = 'failed';
		$obj->timestamp = $timestamp;
		$obj->code = $cod;
		$obj->reason = $err;
		$obj->info = $res;
		
		$msg = json_encode( $obj );
	}
	else
	{
		if( !isset( $GLOBALS['headerset'] ) )
		{
			header ( 'Content-type: text/xml; charset=utf-8' );
		}
		
		$msg = '<?xml version="1.0" encoding="utf-8"?>
<xml>
	<response>failed</response>
	<timestamp>' . $timestamp . '</timestamp>
	<code>' . $cod . '</code>
	<reason>' . $err . '</reason>
	<info>' . $res . '</info>
</xml>';
	}
	
	// Debug
	$debug = $_REQUEST;
	if( isset( $debug['Username'] ) && isset( $debug['Password'] ) )
	{
		$debug['Username'] = ( $debug['Username'] ? 'true' : 'false' );
		$debug['Password'] = ( $debug['Password'] ? 'true' : 'false' );
	}
	
	// Log api activity
	logActivity ( '[TRUE]', 'throwXmlError' . ( $func ? ' ( ' . $func . ' )' : '' ), $debug, 'delay.log' );
	logActivity ( $msg, 'throwXmlError' . ( $func ? ' ( ' . $func . ' )' : '' ), $debug, 'error.log' );
	
	die ( trim( $msg ) );
}

function throwXmlMsg ( $err, $res = false, $func = false, $enc = false )
{
	global $database;
	
	// Close mysql socket ...
	$database->close();
	
	if( !$err ) return false;
	
	$enc = ( isset( $_REQUEST['Encoding'] ) ? $_REQUEST['Encoding'] : $enc );
	
	// Make sure this error exists.
	if( !msgCodes( $err ) || msgCodes( $err ) == null )
	{
		header( "HTTP/1.1 500 Internal Server Error" );
		die( '500 Internal Server Error: What error?');
	}
	
	$cod = $err;
	
	// Get the errors
	$res = !$res ? msgCodes( $err, true ) : $res;
	$err = msgCodes( $err );
	
	// Format the error string, if needed
	$arg = func_get_args();
	$err = vsprintf( $err, $arg );
	
	$timestamp = strtotime( date( 'Y-m-d H:i:s' ) );
	
	if ( $enc && strtolower( $enc ) == 'json' )
	{
		if( !isset( $GLOBALS['headerset'] ) )
		{
			header ( 'Content-type: text/json; charset=utf-8' );
		}
		
		$obj = new stdClass();
		$obj->response = 'ok';
		$obj->timestamp = $timestamp;
		$obj->code = $cod;
		$obj->reason = $err;
		$obj->info = $res;
		
		$msg = json_encode( $obj );
	}
	else
	{
		if( !isset( $GLOBALS['headerset'] ) )
		{
			header ( 'Content-type: text/xml; charset=utf-8' );
		}
		
		$msg = '<?xml version="1.0" encoding="utf-8"?>
<xml>
	<response>ok</response>
	<timestamp>' . $timestamp . '</timestamp>
	<code>' . $cod . '</code>
	<reason>' . $err . '</reason>
	<info>' . $res . '</info>
</xml>';
	}
	
	// Log api activity
	logActivity ( '[TRUE]', 'throwXmlMsg' . ( $func ? ' ( ' . $func . ' )' : '' ), $_REQUEST, 'delay.log' );
	logActivity ( $msg, 'throwXmlMsg' . ( $func ? ' ( ' . $func . ' )' : '' ), $_REQUEST, ( $func ? ( $func . '.log' ) : false ) );
	
	die ( trim( $msg ) );
}

function showXmlData ( $dat = '', $fld = false, $func = false, $enc = false )
{
	global $database;
	
	// Close mysql socket ...
	$database->close();
	
	$fld = ( $fld ? $fld : 'data' );
	
	$enc = ( isset( $_REQUEST['Encoding'] ) ? $_REQUEST['Encoding'] : $enc );
	
	$timestamp = strtotime( date( 'Y-m-d H:i:s' ) );
	
	if ( $enc && strtolower( $enc ) == 'json' )
	{
		if( !isset( $GLOBALS['headerset'] ) )
		{
			header ( 'Content-type: text/json; charset=utf-8' );
		}
		
		$obj = new stdClass();
		$obj->response = 'ok';
		$obj->timestamp = $timestamp;
		$obj->{$fld} = $dat;
		
		$msg = json_encode( $obj );
	}
	else
	{
		if( !isset( $GLOBALS['headerset'] ) )
		{
			header ( 'Content-type: text/xml; charset=utf-8' );
		}
		
		$msg = '<?xml version="1.0" encoding="utf-8"?>
<xml>
	<response>ok</response>
	<timestamp>' . $timestamp . '</timestamp>
	' . ( $fld ? ( '<' . $fld . '>' . $dat . '</' . $fld . '>' ) : '' ) . '
</xml>';
	}
	
	// Log api activity
	logActivity ( '[TRUE]', 'showXmlData' . ( $func ? ' ( ' . $func . ' )' : '' ), $_REQUEST, 'delay.log' );
	logActivity ( $msg, 'showXmlData' . ( $func ? ' ( ' . $func . ' )' : '' ), $_REQUEST, ( $func ? ( $func . '.log' ) : false ) );
	
	die( trim( $msg ) );
}

function outputXML ( $dat = '', $sts = false, $func = false, $enc = false )
{
	global $database;
	
	// Close mysql socket ...
	$database->close();
	
	$sts = !$sts ? 'ok' : $sts;
	
	$enc = ( isset( $_REQUEST['Encoding'] ) ? $_REQUEST['Encoding'] : $enc );
	
	$timestamp = strtotime( date( 'Y-m-d H:i:s' ) );
	
	if ( $enc && strtolower( $enc ) == 'json' )
	{
		if( !isset( $GLOBALS['headerset'] ) )
		{
			header ( 'Content-type: text/json; charset=utf-8' );
		}
		
		$obj = new stdClass();
		$obj->response = $sts;
		$obj->timestamp = $timestamp;
		$obj->items = $dat;
		
		$msg = json_encode( $obj );
	}
	else
	{
		if( !isset( $GLOBALS['headerset'] ) )
		{
			header ( 'Content-type: text/xml; charset=utf-8' );
		}
		
		$msg = '<?xml version="1.0" encoding="utf-8"?>
<xml>
	<response>' . $sts . '</response>
	<timestamp>' . $timestamp . '</timestamp>
	<items>
		' . trim( $dat ) . '
	</items>
</xml>';
	}
	
	// Log api activity
	logActivity ( '[TRUE]', 'outputXML' . ( $func ? ' ( ' . $func . ' )' : '' ), $_REQUEST, 'delay.log' );
	logActivity ( $msg, 'outputXML' . ( $func ? ' ( ' . $func . ' )' : '' ), $_REQUEST, ( $func ? ( $func . '.log' ) : false ) );
	
	die( trim( $msg ) );
}

function allowAccess ()
{
	header ( 'Access-Control-Allow-Origin: *' );
	header ( 'Access-Control-Allow-Methods: GET, POST, OPTIONS' );
	header ( 'Access-Control-Allow-Headers: Method' );
}

function cronErrors( $id )
{
	global $database;
	
	$root = ( isset( $root ) && $root ? $root : BASE_DIR );
	
    $error = error_get_last();
	
	// TODO: clean file when certain size is reached
	
	$print = ''; $type = '';
	
	switch ( $error["type"] )
	{
		case E_ERROR:
			$print .= "--- " . ( $type = 'E_ERROR' ) . " --- ";
			break;
		
		case E_WARNING:
			$print .= "--- " . ( $type = 'E_WARNING' ) . " --- ";
			break;
		
		case E_DEPRECATED:
			$print .= "--- " . ( $type = 'E_DEPRECATED' ) . " --- ";
			return false;
			break;
		
		case E_STRICT:
			$print .= "--- " . ( $type = 'E_STRICT' ) . " --- ";
			break;
		
		case E_NOTICE:
			$print .= "--- " . ( $type = 'E_NOTICE' ) . " --- ";
			return false;
			break;
		
		case E_USER_ERROR:
			$print .= "--- " . ( $type = 'E_USER_ERROR' ) . " --- ";
			return false;
			break;
		
		case E_USER_WARNING:
			$print .= "--- " . ( $type = 'E_USER_WARNING' ) . " --- ";
			return false;
			break;
		
		case E_USER_NOTICE:
			$print .= "--- " . ( $type = 'E_USER_NOTICE' ) . " --- ";
			return false;
			break;
		
		case E_USER_DEPRECATED:
			$print .= "--- " . ( $type = 'E_USER_DEPRECATED' ) . " --- ";
			return false;
			break;
	}
	
	$print .= date( 'Y-m-d H:i:s' ) . "\r\n" . $error["message"] . ' in ' . $error["file"] . ' on ' . $error["line"] . "\r\n";
	
	// Set error file
	//$fp = fopen ( "$root/upload/error.txt", 'a+' );
	
	if ( /*$fp && */$print )
	{
		if ( isset( $id ) && $id > 0 && $error )
		{
			$err = new dbObject( 'SBookCronJobs' );
			$err->ID = $id;
			if ( $err->Load() )
			{
				$error["type"] = $type;
				
				$err->IsActive = 0;
				$err->IsRunning = 0;
				$err->Error = $err->Error . print_r( $error,1 );
				$err->Save();
			}
		}
		
		// Log api activity
		logActivity ( $print, false, false, 'phperror.log' );
		
		//fwrite ( $fp, $print );
	}
	
	//if ( $fp ) fclose ( $fp );
	
	return true;
}

function initErrorReporting( $id = false, $type = false )
{
	$root = ( isset( $root ) && $root ? $root : BASE_DIR );
	
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	ini_set( "display_errors", "off" );
	
	register_shutdown_function( "cronErrors", $id );
	//set_error_handler( "cronErrors" );
	//set_exception_handler( "cronErrors" );
	//ini_set( "display_errors", "off" );
	//ini_set( "display_errors", "on" );
	//error_reporting( E_ALL );
	//error_reporting( E_ALL & ~( E_STRICT | E_NOTICE | E_DEPRECATED ) );
}

function logActivity ( $result = '', $query = false, $vars = false, $log = false, $limit = false, $storage = false, $mode = false )
{
	//if ( !$result ) return false;
	
	$log = $log ? $log : 'api.log';
	$limit = $limit ? $limit : 1000000;
	$storage = $storage ? $storage : ( defined( 'BASE_DIR' ) ? ( BASE_DIR . '/' ) : '' ) . 'subether/upload/log/';
	$mode = $mode ? $mode : 'a+';
	
	if ( !file_exists( $storage ) )
	{
		@mkdir( $storage, 0777, true );
	}
	
	if ( file_exists( $storage ) )
	{
		$arr = array();
		
		$fname = explode( '.', $log );
		
		if ( $dir = opendir ( $storage ) )
		{
			while ( $file = readdir ( $dir ) )
			{
				if ( $file{0} == '.' ) continue;
				
				if ( strstr( $file, $fname[0] ) && $key = preg_replace( '/[^0-9]/', '', $file ) )
				{
					$arr[$key] = $file;
				}
			}
			
			if ( $arr )
			{
				krsort( $arr );
				
				if ( count( $arr ) > 3 )
				{
					$i = 1;
					
					foreach( $arr as $a )
					{
						if ( $i > 3 )
						{
							@unlink( $storage . $a );
						}
						
						$i++;
					}
				}
			}
		}
		
		$fn = explode( '.', $log );
		$log = $fn[0] . strtotime( date( 'Y-m-d' ) ) . '.' . end( $fn );
		
		// Set log file
		if ( defined( 'NODE_LOG' ) && NODE_LOG && ( $fp = @fopen ( $storage . $log, $mode ) ) )
		{
			$apidelay = ( isset( $GLOBALS['LoadTime'] ) ? round( ( microtime(true) - $GLOBALS['LoadTime'] ), 3 ) : '' );
			
			if ( $query )
			{
				$data  = "\r\n\r\n" . date( 'Y-m-d H:i:s' ) . ' | ' . strtotime( date( 'Y-m-d H:i:s' ) ) . ( $apidelay ? ' (' . $apidelay . 's)' : '' );
				$data .= ' --------------------------------------------------' . "\r\n\r\n" . 'From: ';
				$data .= $_SERVER['REMOTE_ADDR'];
				$data .= ' Query: ';
				$data .= $query . ( is_array( $vars ) ? ( "\r\n\r\n" . print_r( $vars, 1 ) ) : ( "\r\n\r\n" . $vars . ' ' ) );
				
				if( $result )
				{
					$data .= "\r\n" . 'Result: ';
					$data .= $result;
				}
			}
			else
			{
				$data = $result;
			}
			
			fwrite ( $fp, $data );
			fclose ( $fp );
			
			return true;
		}
	}
	
	return false;
}

function heartbeat( $token, $loop = 0, $delay = 0 )
{
	if( !$token ) return false;
	
	// If delay is defined only run the heartbeat every 30sec f.eks
	
	if( $delay > 0 && $delay > $loop )
	{
		return ++$loop;
	}
	
	$sess = new dbObject ( 'UserLogin' );
	$sess->Token = $token;
	if ( $sess->Load () )
	{
		$sess->LastHeartbeat = date( 'Y-m-d H:i:s' );
		$sess->DateExpired = date( 'Y-m-d H:i:s', ( time () + ( 60 * 60 ) ) ); // Expires after 1hour 
		$sess->Save();
		
		// Log api activity
		//logActivity ( $sess->LastHeartbeat . "\r\n" . ( $loop . ' - ' . $delay ) . "\r\n", false, false, 'heartbeat.log' );
		
		return 1;
	}
	else
	{
		throwXmlError ( SESSION_EXPIRED, false, 'heartbeat' );
		
		return false;
	}
	
	return false;
}

if( !function_exists( 'UserActivity' ) )
{
	function UserActivity( $component, $type, $userid, $contactid = null, $typeid = null, $data = '' )
	{
		global $database;
		
		// 
	
		if( $component && $type && $userid )
		{
			// Store every activity ...
			
			$ua = new dbObject ( 'UserActivity' );
			$ua->Component  = $component;
			$ua->Type       = $type;
			$ua->UserID     = $userid;
			$ua->ContactID  = $contactid;
			//$ua->Load();
			$ua->TypeID     = $typeid;
			$ua->Data       = $data;
			$ua->LastUpdate = time();
			$ua->Save();
			
			// Cleanup old activity that is 24 hours old ...
			
			if( $rows = $database->fetchObjectRow ( $q = '
				SELECT ID 
				FROM UserActivity 
				WHERE UserID = \'' . $userid . '\' AND LastUpdate <= ' . ( time() - ( 60 * 60 * 24 ) ) . '
				ORDER BY ID ASC 
			', false, 'restapi/functions.php' ) ) 
			{
				// Delete code ... 
				
				$database->query ( '
					DELETE FROM UserActivity 
					WHERE UserID = \'' . $userid . '\' AND LastUpdate <= ' . ( time() - ( 60 * 60 * 24 ) ) . ' 
					ORDER BY ID ASC 
					LIMIT 50 
				' );
			}
			
			return true;
		}
	
		return false;
	}
}

function getPathDir( $back = '', $type = 'config' )
{
	// TODO: Make this more dynamic, now just made as a quickfix for the symlink issue
	
	if ( $back && isset( $_SERVER['SCRIPT_FILENAME'] ) && $type == 'config' )
	{
		if ( $back == '../../..' )
		{
			$dirname = dirname( $_SERVER['SCRIPT_FILENAME'] );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			
			return $dirname;
		}
		else if ( $back == '../..' )
		{
			$dirname = dirname( $_SERVER['SCRIPT_FILENAME'] );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			
			return $dirname;
		}
		else if ( $back == '..' )
		{
			$dirname = dirname( $_SERVER['SCRIPT_FILENAME'] );
			$dirname = dirname( $dirname );
			
			return $dirname;
		}
	}
	else if ( $back && __FILE__ && $type == 'subether' )
	{
		if ( $back == '../../..' )
		{
			$dirname = dirname( __FILE__ );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			//$dirname = dirname( $dirname );
			
			if ( file_exists( "$dirname/SubEther" ) )
			{
				return "$dirname/SubEther";
			}
			if ( file_exists( "$dirname/treeroot" ) )
			{
				return "$dirname/treeroot";
			}
		}
		else if ( $back == '../..' )
		{
			$dirname = dirname( __FILE__ );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			
			if ( file_exists( "$dirname/SubEther" ) )
			{
				return "$dirname/SubEther";
			}
			if ( file_exists( "$dirname/treeroot" ) )
			{
				return "$dirname/treeroot";
			}
		}
		else if ( $back == '..' )
		{
			$dirname = dirname( __FILE__ );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			
			if ( file_exists( "$dirname/SubEther" ) )
			{
				return "$dirname/SubEther";
			}
			if ( file_exists( "$dirname/treeroot" ) )
			{
				return "$dirname/treeroot";
			}
		}
	}
	else if ( $back && __FILE__ && $type == 'arenacm' )
	{
		if ( $back == '../../..' )
		{
			$dirname = dirname( __FILE__ );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			//$dirname = dirname( $dirname );
			
			if ( file_exists( "$dirname/ArenaCM" ) )
			{
				return "$dirname/ArenaCM";
			}
			if ( file_exists( "$dirname/arena2" ) )
			{
				return "$dirname/arena2";
			}
		}
		else if ( $back == '../..' )
		{
			$dirname = dirname( __FILE__ );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			
			if ( file_exists( "$dirname/ArenaCM" ) )
			{
				return "$dirname/ArenaCM";
			}
			if ( file_exists( "$dirname/arena2" ) )
			{
				return "$dirname/arena2";
			}
		}
		else if ( $back == '..' )
		{
			$dirname = dirname( __FILE__ );
			$dirname = dirname( $dirname );
			$dirname = dirname( $dirname );
			
			if ( file_exists( "$dirname/ArenaCM" ) )
			{
				return "$dirname/ArenaCM";
			}
			if ( file_exists( "$dirname/arena2" ) )
			{
				return "$dirname/arena2";
			}
		}
	}
	
	return $back;
}

function getBasicResources( $root = false )
{
	if ( !file_exists( "config.php" ) )
	{
		$path1 = getPathDir( ( $root ? $root : '../..' ), 'config' );
		$path2 = getPathDir( ( $root ? $root : '../..' ), 'arenacm' );
		$path3 = getPathDir( ( $root ? $root : '../..' ), 'subether' );
	}
	else
	{
		$path1 = $path2 = $path3 = '.';
	}
	
	include_once ( "$path1/config.php" );
	
	if( !isset( $GLOBALS['database'] ) )
	{
		include_once ( "$path2/lib/functions/functions.php" );
		include_once ( "$path2/lib/classes/database/cdatabase.php" );
		include_once ( "$path2/lib/classes/dbObjects/dbObject.php" );
		include_once ( "$path2/lib/classes/dbObjects/dbFolder.php" );
		include_once ( "$path2/lib/classes/dbObjects/dbImage.php" );
		include_once ( "$path2/lib/classes/dbObjects/dbUser.php" );
		include_once ( "$path2/lib/lib.php" );
		
		if( file_exists( "$path1/lib/core_config.php" ) )
		{
			$corebase = new cDatabase ();
			require ( "$path1/lib/core_config.php" );
			$corebase->open () or die ( 'Failed to connect' );
			
			if( $_site_ = $corebase->fetchObjectRow( '
				SELECT * FROM Sites 
				ORDER BY ID ASC 
				LIMIT 1 
			' ) )
			{
				// TODO: Make this more intelligent, find the config file and set the BASE_URL and BASE_DIR based on that, not from the db
				
				if( $_site_->BaseUrl && !strstr( $_site_->BaseUrl, 'http' ) )
				{
					$_site_->BaseUrl = ( stripos( $_SERVER['SERVER_PROTOCOL'], 'https' ) === true ? 'https://' : 'http://' ) . $_site_->BaseUrl;
				}
				
				define( 'BASE_URL', $_site_->BaseUrl );
				define( 'BASE_DIR', $_site_->BaseDir );
				
				$database = new cDatabase ();
				$database->SetUsername ( $_site_->SqlUser );
				$database->SetPassword ( $_site_->SqlPass );
				$database->SetHostname ( $_site_->SqlHost );
				$database->SetDb ( $_site_->SqlDatabase );
				
				$database->open () or die ( 'Failed to connect' );
				
				dbObject::globalValueSet ( 'database', $database );
				
				$GLOBALS['database'] = $database;
			}
		}
	}
}

getBasicResources( $root );

function isValidMd5( $md5 )
{
    return !empty( $md5 ) && preg_match( '/^[a-f0-9]{32}$/', $md5 );
}

if( !function_exists( 'ReadUserAgent' ) )
{
	function ReadUserAgent( $ua )
	{
		$str = '';
		
		if( $ua )
		{
			include_once ( BASE_DIR . '/subether/thirdparty/php/UserAgentParser.php' );
			
			$str .= $ua;
			
			if( function_exists( 'parse_user_agent' ) && ( $array = parse_user_agent( $ua ) ) )
			{
				$str .= ( ' | ' . $array['browser'] . ' ' . $array['version'] . ' on ' . $array['platform'] );
			}
		}
		
		return $str;
	}
}

if( !function_exists( 'hex_sha256' ) )
{
	function hex_sha256( $str )
	{
		return hash( 'sha256', $str );
	}
}

if( !function_exists( 'UniqueKey' ) )
{
	function UniqueKey( $option1 = false, $option2 = false, $option3 = false, $option4 = false )
	{
		$host = ( BASE_URL ? ( BASE_URL . '|' ) : '' );
		$option1 = ( $option1 ? ( $option1 . '|' ) : '' );
		$option2 = ( $option2 ? ( $option2 . '|' ) : '' );
		$option3 = ( $option3 ? ( $option3 . '|' ) : '' );
		$option4 = ( $option4 ? ( $option4 . '|' ) : '' );
		$current = ( time() . '|' );
		$random = str_replace( ' ', '', rand(0,999).rand(0,999).rand(0,999).microtime() );
		$hexkey = hex_sha256( $host.$option1.$option2.$option3.$option4.$current.$random );
		
		return $hexkey;
	}
}

if( !function_exists ( 'randomKey' ) )
{
	function randomKey ()
	{
		$numbs = '0123456789';
		$final = '';
		for ( $a = 0; $a < 5; $a++ )
		{
			$final .= substr ( $numbs, rand ( 0, strlen ( $numbs )-1 ), 1 );
		}
		return $final;
	}
}

if( !function_exists ( 'randomPassword' ) )
{
	function randomPassword ( $n = 10 )
	{
		$p = array ();
		$a = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';
		for ( $i = 0; $i < $n; $i++ )
		{
			$p[] = $a[rand(0,strlen($a)-1)];
		}
		return implode( $p );
	}
}

if( !function_exists ( 'makeHumanPassword' ) )
{
	function makeHumanPassword ()
	{
		$words01 = array( 'Friend', 'Tree', 'Amiga', 'Rock', 'Stone', 'Sun', 'Winter' );
		$words02 = array( 'Liquid', 'Easy', 'Friendly', 'Up', 'Forward', 'Wall' );
		$words03 = array( 'Green', 'Blue', 'Red', 'Yellow', 'Orange', 'Free' );
		
		$pass = '';
		$rn = rand(0,2);
		
		$goon = true;
		$rounds = 0;
		$first = $second = $third = $fourth = false;
		
		while ( $goon )
		{
			
			switch ( $rn )
			{
				case 0:
					if ( !$first ) $pass .= $words01[ rand(0,6)  ] . '';
					$first = true;
					break;
				case 1:
					if ( !$second ) $pass .= $words02[ rand(0,5)  ] . '';
					$second = true;
					break;
				case 2:
					if ( !$third ) $pass .= $words03[ rand(0,5)  ] . '';
					$third = true;
					break;
				case 3:
					if ( !$fourth ) $pass .= rand(10,99) . '';
					$fourth = true;
					break;
				default:
					$rn = rand(0,3);
					continue;
					break;
			}
			
			$rn = rand(0,3);
			
			if	( $first && $second && $third )
			{
				$goon = false;
				if ( !$fourth ) $pass .= rand(10,99) . '';
			}
			
			$rounds++;
			
			if	(  $rounds > 100 ) $goon = false;
		}
		
		if ( $pass )
		{
			return $pass;
		}
		
		return false;
	}
}

if( !function_exists ( 'BetaInvites' ) )
{
	function BetaInvites ( $email )
	{
		if( file_exists( BASE_DIR . '/subether/upload/private/invited_members_only' ) )
		{
			$invited = explode ( "\n", @file_get_contents ( BASE_DIR . '/subether/upload/private/invited_members_only' ) );
			
			if( !$email ) return false;
			
			if( in_array( trim( strtolower( $email ) ), $invited ) )
			{
				return true;
			}
			return false;
		}
		return true;
	}
}

if( !function_exists ( 'UniqueUsername' ) )
{
	function UniqueUsername ( $name, $uid = false )
	{
		global $database;
		
		if( $database->fetchObjectRow ( $uid && $uid != '' ? '
			SELECT 
				ID 
			FROM 
				Users 
			WHERE 
					Name != "" 
				AND Name = \'' . trim( $name ) . '\' 
				AND UniqueID != \'' . $uid . '\' 
			ORDER BY 
				ID DESC 
			' : '
			SELECT 
				ID 
			FROM 
				SBookContact 
			WHERE 
					Username != "" 
				AND Username = \'' . trim( $name ) . '\' 
			ORDER BY 
				ID DESC 
		', false, 'restapi/functions.php' ) ) 
		{
			for ( $a = 1; $a < 100; $a++ )
			{
				if( !$database->fetchObjectRow ( '
					SELECT 
						ID 
					FROM 
						SBookContact 
					WHERE 
							Username != "" 
						AND Username = \'' . trim( $name.'.'.$a ) . '\' 
					ORDER BY 
						ID DESC 
				', false, 'restapi/functions.php' ) )
				{
					return trim( $name.'.'.$a );
				}
			}
		}
		
		return trim( $name );
	}
}

if( !function_exists ( 'CleanUsername' ) )
{
	function CleanUsername ( $username )
	{
		if( !$username ) return false;
		
		$clean_name = strtr( $username, array( '' => 'S','' => 'Z','' => 's','' => 'z','' => 'Y','À' => 'A','Á' => 'A','Â' => 'A','Ã' => 'A','Ä' => 'A','Å' => 'A','Ç' => 'C','È' => 'E','É' => 'E','Ê' => 'E','Ë' => 'E','Ì' => 'I','Í' => 'I','Î' => 'I','Ï' => 'I','Ñ' => 'N','Ò' => 'O','Ó' => 'O','Ô' => 'O','Õ' => 'O','Ö' => 'O','Ø' => 'O','Ù' => 'U','Ú' => 'U','Û' => 'U','Ü' => 'U','Ý' => 'Y','à' => 'a','á' => 'a','â' => 'a','ã' => 'a','ä' => 'a','å' => 'a','ç' => 'c','è' => 'e','é' => 'e','ê' => 'e','ë' => 'e','ì' => 'i','í' => 'i','î' => 'i','ï' => 'i','ñ' => 'n','ò' => 'o','ó' => 'o','ô' => 'o','õ' => 'o','ö' => 'o','ø' => 'o','ù' => 'u','ú' => 'u','û' => 'u','ü' => 'u','ý' => 'y','ÿ' => 'y' ) );
		$clean_name = strtr( $clean_name, array( 'Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', '' => 'OE', '' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u' ) );
		$clean_name = preg_replace( array( '/\s/', '/\.[\.]+/', '/[^\w_\.\-]/' ), array( '.', '.', '' ), $clean_name );
		$clean_name = str_replace( ' ', '.', $clean_name );
		$clean_name = str_replace( array( 'æ', 'ø', 'å', 'Æ', 'Ø', 'Å' ), array( 'ae', 'o', 'aa', 'Ae', 'O', 'Aa' ), $clean_name );
		$clean_name = strtolower( $clean_name );
		
		return $clean_name;
	}
}

if( !function_exists( 'RunVirtualCronJobs' ) )
{
	function RunVirtualCronJobs( $root = false )
	{
		global $database;
		
		$path = "$root/subether/restapi/v1/cron/";
		
		include_once ( "$root/subether/classes/library.class.php" );
		
		$lib = new Library(); $folder = $lib->OpenFolder( "$root/subether/restapi/v1/", "cron/" );
		
		if( $folder )
		{
			foreach( $folder as $fld )
			{
				$job = new dbObject( 'SBookCronJobs' );
				$job->Filename = $fld->name;
				if( !$job->Load() && strstr( $job->Filename, '_cron.php' ) )
				{
					$job->IsActive = 1;
					$job->MinDelay = 30;
					$job->Save();
				}
			}
		}
		
		if( $jobs = $database->fetchObjectRows( '
			SELECT * FROM SBookCronJobs 
			WHERE IsActive = "1" AND IsRunning = "0" 
			ORDER BY SortOrder, ID ASC 
		' ) )
		{
			foreach( $jobs as $j )
			{
				if( $j->MinDelay && $j->MinDelay >= 1 )
				{
					if( str_replace( '-', '', date( 'YmHi' ) - date( 'YmHi', strtotime( $j->LastExec ) ) ) >= $j->MinDelay )
					{
						if( file_exists( $path . $j->Filename ) )
						{
							$res = new dbObject( 'SBookCronJobs' );
							$res->ID = $j->ID;
							if( $res->Load() )
							{
								$res->IsRunning = 1;
								$res->Save();
								
								initErrorReporting( $j->ID, 'cronjobs' );
								
								include_once( $path . $j->Filename );
								
								$done = new dbObject( 'SBookCronJobs' );
								$done->ID = $j->ID;
								if( $done->Load() )
								{
									$done->IsRunning = 0;
									$done->LastExec = date( 'Y-m-d H:i:s' );
									$done->Save();
								}
								
								return true;
							}
						}
					}
				}
			}
		}
		
		return false;
	}
}

function getServerUniqueID( $root = false, $uniqueid = false )
{
	// TODO: Get from database if file not found, then if both is not found create new.
	
	$uniqueid = ( $uniqueid ? $uniqueid : UniqueKey() );
	
	if ( defined( 'BASE_DIR' ) )
	{
		// If file exists get content
		if( file_exists( BASE_DIR . '/subether/upload/private/node.id' ) )
		{
			if( $cnt = file_get_contents( BASE_DIR . '/subether/upload/private/node.id' ) )
			{
				if( trim( $cnt ) )
				{
					return trim( $cnt );
				}
			}
		}
		// Else if file doesn't exist create one
		else if( !file_exists( BASE_DIR . '/subether/upload/private/node.id' ) )
		{
			if( $uniqueid )
			{
				if( $fp = fopen( ( BASE_DIR . '/subether/upload/private/node.id' ), "w+" ) )
				{
					fwrite( $fp, trim( $uniqueid ) );
					fclose( $fp );
					
					//chmod( ( BASE_DIR . '/subether/node.id' ), 0755 );
					
					if( trim( $uniqueid ) )
					{
						return trim( $uniqueid );
					}
				}
			}
		}
	}
	
	return $uniqueid;
}

function getServerKeys( $root = false, $type = false )
{
	if ( defined( 'BASE_DIR' ) )
	{
		include_once ( BASE_DIR . '/subether/classes/fcrypto.class.php' );
		
		$fcrypt = new fcrypto();
		
		// If file exists get content
		if( file_exists( BASE_DIR . '/subether/upload/private/node.key' ) )
		{
			$keys = array();
			
			if( $cnt = file_get_contents( BASE_DIR . '/subether/upload/private/node.key' ) )
			{
				//$cnt = explode( "\r\n\r\n", $cnt );
				$cnt = explode( "-----", $cnt );
				
				if( $cnt && count( $cnt ) > 0 )
				{
					$prvkey = $cnt[0]."-----".$cnt[1]."-----".$cnt[2]."-----".$cnt[3]."-----";
					$pubkey = $cnt[4]."-----".$cnt[5]."-----".$cnt[6]."-----".$cnt[7]."-----";
					
					// TODO: Fix this getKeys to accept privatekey or to setRSAprivatekey to get keys
					//$keys = $fcrypt->getKeys( $cnt );
					
					$keys['privatekey'] = trim( $prvkey );
					$keys['publickey'] = trim( $pubkey );
				
					if( count( $keys ) >= 2 )
					{
						if( $type )
						{
							return $keys[$type];
						}
						else
						{
							return $keys;
						}
					}
				}
			}
		}
		// Else if file doesn't exist create one
		else if( !file_exists( BASE_DIR . '/subether/upload/private/node.key' ) )
		{
			if( $keys = $fcrypt->generateKeys() )
			{
				if( $fp = fopen( ( BASE_DIR . '/subether/upload/private/node.key' ), "w+" ) )
				{
					fwrite( $fp, implode( "\r\n\r\n", $keys ) );
					//fwrite( $fp, $keys['privatekey'] );
					fclose( $fp );
					
					//@chmod( ( BASE_DIR . '/subether/node.key' ), 0755 );
					
					if( count( $keys ) >= 2 )
					{
						if( $type )
						{
							return $keys[$type];
						}
						else
						{
							return $keys;
						}
					}
				}
			}
		}
	}
	
	return false;
}

function getNodeInfo( $type = false )
{
	$info = ( file_exists( BASE_DIR . '/subether/info.txt' ) ? @file_get_contents( BASE_DIR . '/subether/info.txt' ) : false );
	
	if ( $info )
	{
		$info = explode( ',', $info );
	}
	
	switch ( $type )
	{
		case 'index':
			return trim( isset( $info[1] ) ? $info[1] : 'http://sub-ether.org/' );
			break;
		
		case 'name':
		default:
			return trim( isset( $info[0] ) ? $info[0] : 'Subether' );
			break;
	}
	
	return false;
}

function getNodeVersion()
{
	return trim( file_exists( BASE_DIR . '/subether/version.txt' ) ? @file_get_contents( BASE_DIR . '/subether/version.txt' ) : '1.0.00' );
}

function getNodeVerification()
{
	return trim( file_exists( BASE_DIR . '/subether/verification.txt' ) ? @file_get_contents( BASE_DIR . '/subether/verification.txt' ) : '{DC65D301-0301-4CA7-B2E1-E6490BAB7F5E}' );
}

function getNodeLocation()
{
	if ( $_SERVER['SERVER_ADDR'] )
	{
		if ( $data = @file_get_contents( $q = "http://ipinfo.io/{$_SERVER['SERVER_ADDR']}" ) )
		{
			$details = json_decode( $data );
			
			if ( isset( $details->country ) )
			{
				return $details->country;
			}
			else if ( $_SERVER['HTTP_HOST'] || $_SERVER['SERVER_NAME'] )
			{
				$ip = gethostbyname( $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'] );
				
				$data = @file_get_contents( $q = "http://ipinfo.io/{$ip}" );
				
				if( $data )
				{
					$details = json_decode( $data );
					
					if ( isset( $details->country ) )
					{
						return $details->country;
					}
					else if ( $data )
					{
						include_once ( BASE_DIR . '/subether/classes/htmlparser.class.php' );
						
						$doc = new SimpleHTML( $data );
						
						foreach( $doc->getElementsByTagName( 'a' ) as $link )
						{
							if ( $link->tagAttributes && strstr( $link->tagAttributes['class'], 'flag flag-' ) && $link->tagAttributes['href'] )
							{
								$country = explode( '/', $link->tagAttributes['href'] );
								return strtoupper( end( $country ) );
							}
						}
					}
				}
			}
		}
	}
	
	return false;
}

function getNodeData( $root = false )
{
	global $database;
	
	include_once ( ( $root ? ( $root.'/' ) : '' ) . 'subether/classes/library.class.php' );
	
	$usr = $database->fetchObjectRows( 'SELECT Name, Email FROM Users WHERE UserType = "0" AND IsDeleted = "0" AND NodeID = "0" ORDER BY IsAdmin DESC, ID ASC' );
	$cps = $database->fetchObjectRows( 'SELECT Name FROM SComponents GROUP BY Name ORDER BY ID ASC' );
	$mds = $database->fetchObjectRows( 'SELECT Name FROM SModules GROUP BY Name ORDER BY ID ASC' );
	$nds = $database->fetchObjectRows( 'SELECT ID, Url, UniqueID, IsIndex, IsConnected, Open, SessionID FROM SNodes WHERE IsMain = "0" AND IsDenied = "0" AND IsAllowed = "1" AND ( IsPending = "1" OR IsConnected = "1" ) ORDER BY ID ASC' );
	$lib = new Library(); $folder = $lib->OpenFolder( 'subether/upload/', 'releases/' );
	
	// --- Set Index Node if there is no nodes -----------------------------------------------------------------
	
	$nin = array();
	$nin[0] = new stdClass();
	$nin[0]->Url = getNodeInfo( 'index' );
	$nin[0]->IsIndex = 1;
	
	$nds = ( $nds ? $nds : $nin );
	
	// --- Update main node ------------------------------------------------------------------------------------
	
	$main = new dbObject( 'SNodes' );
	$main->IsMain = 1;
	if( !$main->Load() )
	{
		$main->DateCreated = ( defined( 'NODE_CREATED' ) ? NODE_CREATED : date( 'Y-m-d H:i:s' ) );
		$main->Location = getNodeLocation();
		$main->IsConnected = 1;
		$main->Open = ( defined( 'NODE_OPEN' ) ? NODE_OPEN : 1 );
	}
	$main->UniqueID = getServerUniqueID( $root, $main->UniqueID );
	$main->PublicKey = getServerKeys( $root, 'publickey' );
	$main->Url = getNodeHost( BASE_URL );
	$main->Name = getNodeInfo( 'name' );
	$main->Version = getNodeVersion();
	$main->Owner = $usr[0]->Name;
	$main->Email = $usr[0]->Email;
	$main->Users = count( $usr );
	if( $cps )
	{
		$cp = array();
		foreach( $cps as $c )
		{
			$cp[] = $c->Name;
		}
	}
	if( $mds )
	{
		$md = array();
		foreach( $mds as $m )
		{
			$md[] = $m->Name;
		}
	}
	$main->Components = ( isset( $cp ) ? json_encode( $cp ) : '0' );
	$main->Modules = ( isset( $md ) ? json_encode( $md ) : '0' );
	$main->DateModified = date( 'Y-m-d H:i:s' );
	$main->Save();
	
	// --- output object ----------------------------------------------------------------------------------------
	
	$obj = new stdClass();
	$obj->ID = $main->ID;
	$obj->Verification = getNodeVerification();
	$obj->UniqueID = $main->UniqueID;
	$obj->PublicKey = $main->PublicKey;
	$obj->Url = $main->Url;
	$obj->Name = $main->Name;
	$obj->Version = $main->Version;
	$obj->Owner = $main->Owner;
	$obj->Email = $main->Email;
	$obj->Location = $main->Location;
	$obj->Users = $main->Users;
	$obj->Open = $main->Open;
	$obj->DateCreated = $main->DateCreated;
	
	// --- Modules -----------------------------------------------------------------------------------------------
	
	if( $mds )
	{
		$obj->Modules = array();
		
		foreach( $mds as $m )
		{
			$mod = new stdClass();
			$mod->Name = $m->Name;
			
			$obj->Modules[] = $mod;
		}
	}
	
	// --- Components --------------------------------------------------------------------------------------------
	
	if( $cps )
	{
		$obj->Components = array();
		
		foreach( $cps as $c )
		{
			$com = new stdClass();
			$com->Name = $c->Name;
			
			$obj->Components[] = $com;
		}
	}
	
	// --- Plugins -----------------------------------------------------------------------------------------------
	
	// TODO: Create support for plugins
	/*if( $pps )
	{
		$obj->Plugins = array();
		
		foreach( $pps as $p )
		{
			$plg = new stdClass();
			$plg->Name = $p->Name;
			
			$obj->Plugins[] = $plg;
		}
	}*/
	
	// --- Themes ------------------------------------------------------------------------------------------------
	
	// TODO: Create support for themes
	/*if( $tms )
	{
		$obj->Themes = array();
		
		foreach( $tms as $t )
		{
			$tmp = new stdClass();
			$tmp->Name = $t->Name;
			
			$obj->Themes[] = $tmp;
		}
	}*/
	
	// --- Nodes -------------------------------------------------------------------------------------------------
	
	if( $nds )
	{
		$obj->Nodes = array(); $found = false;
		
		foreach( $nds as $n )
		{
			$nod = new stdClass();
			$nod->ID = $n->ID;
			$nod->UniqueID = $n->UniqueID;
			$nod->Url = $n->Url;
			$nod->IsIndex = $n->IsIndex;
			$nod->IsConnected = $n->IsConnected;
			$nod->Open = $n->Open;
			$nod->SessionID = $n->SessionID;
			
			$obj->Nodes[] = $nod;
			
			if ( $nod->IsIndex )
			{
				$found = true;
			}
		}
		
		if ( !$found )
		{
			$nod = new stdClass();
			$nod->Url = getNodeInfo( 'index' );
			$nod->IsIndex = 1;
			
			$obj->Nodes[] = $nod;
		}
	}
	
	// --- Releases ----------------------------------------------------------------------------------------------
	
	if( $folder )
	{
		$folder = array_reverse( $folder );
		
		$obj->Releases = array();
		
		foreach( $folder as $f )
		{
			$rel = new stdClass();
			$rel->Name = $f->name;
			$rel->Title = $f->title;
			$rel->Path = $f->path;
			$rel->Type = $f->type;
			$rel->Size = $f->size;
			if( $f->modified )
			{
				$rel->Modified = date( 'Y-m-d H:i:s', $f->modified );
			}
			if( $f->version )
			{
				$rel->Version = $f->version;
			}
			
			$obj->Releases[] = $rel;
		}
	}
	
	if ( $obj )
	{
		return $obj;
	}
	
	return false;
}

function getNodeHost( $url )
{
	if ( !$url ) return false;
	
	if ( isset( $_SERVER['REMOTE_ADDR'] ) && isset( $_SERVER['SERVER_ADDR'] ) )
	{
		$newurl = false;
		
		if( ( strstr( $url, '127.0.0.1' ) && strstr( BASE_URL, '127.0.0.1' ) ) || ( strstr( $url, 'localhost' ) && strstr( BASE_URL, 'localhost' ) ) )
		{
			return $url;
		}
		// If the url is localhost find remoteip since it doesn't have a domain
		else if ( strstr( $url, '127.0.0.1' ) || strstr( $url, 'localhost' ) )
		{
			$newurl = str_replace( 'NØFF', '', preg_replace( '/(http[s]{0,1}\:\/\/)([^\/]+)([\/]{0,1}.+)/i', "$1NØFF{$_SERVER['REMOTE_ADDR']}$3", $url ) );
		}
		// Else if server has a local ip and the url might be something else force addresse to be the correct server ip
		else if ( strstr( $_SERVER['SERVER_ADDR'], '127.0.0.1' ) || substr( $_SERVER['SERVER_ADDR'], 0, 4 ) == '192.' || substr( $_SERVER['SERVER_ADDR'], 0, 3 ) == '10.' )
		{
			$newurl = str_replace( 'NØFF', '', preg_replace( '/(http[s]{0,1}\:\/\/)([^\/]+)([\/]{0,1}.+)/i', "$1NØFF{$_SERVER['SERVER_ADDR']}$3", $url ) );
		}
		
		if ( $newurl )
		{
			return $newurl;
		}
		//return str_replace( array( '127.0.0.1', 'localhost' ), $_SERVER['REMOTE_ADDR'], $url );
	}
	
	return $url;
}


// To work around caching in cdatabase.php

if( !function_exists( 'fetchObjectRow' ) )
{
	function fetchObjectRow( $query )
	{
		global $database;
	
		$rs = mysqli_query( $database->resource, $query );
	
		if ( $row = mysqli_fetch_assoc( $rs ) )
		{
			$obj = new stdclass ( );
			foreach ( $row as $k=>$v )
			{
				$obj->$k = $v;
			}
		
			mysqli_free_result( $rs );
			return $obj;
		}
	
		return false;
	}
}

if( !function_exists( 'fetchObjectRows' ) )
{
	function fetchObjectRows( $query, $mode = MYSQLI_ASSOC )
	{
		global $database;
	
		if ( $qr = mysqli_query ( $database->resource, $query ) )
		{
			while ( $rows[] = mysqli_fetch_assoc ( $qr ) ) {}
			array_pop ( $rows );
			if ( ( $count = count ( $rows ) ) )
			{
				$output = Array ( );
				foreach ( $rows as $row )
				{
					$obj = new stdclass ( );
					foreach ( $row as $k=>$v )
					{
						if ( is_string ( $v ) || is_numeric ( $v ) )
							$obj->$k = $v;
					}
					$output[] = $obj;
				}
			
				mysqli_free_result ( $qr );
				return $output;
			}
		}
	
		return false;
	}
}


if( !function_exists( 'mailNow_' ) )
{
	function mailNow_ ( $subject, $message, $receiver, $type = 'html', $from = MAIL_REPLYTO, $attachments = false, $template = false, $rawdata = false )
	{
		include_once ( 'subether/classes/mail.class.php' );
		
		$email = new eMail ();
		$email->setHostInfo ( MAIL_SMTP_HOST, MAIL_USERNAME, MAIL_PASSWORD );
		$email->setSubject ( ( strstr( $subject, '=?UTF-8?B?' ) ? $subject : ( '=?UTF-8?B?' . base64_encode ( $subject ) . '?=' ) ) );
		$email->setPort ( defined ( 'MAIL_SMTP_PORT' ) ? MAIL_SMTP_PORT : '25' );
		$email->setFrom ( $from );
		$email->_error_report = true;
		$email->_recipients = array ( $receiver );
		$email->addHeader ( "Content-type", "text/" . $type . "; charset=iso-8859-1" );
		
		$Article = utf8_decode ( $message );
		
		if ( $attachments )
		{
			if ( is_object( $attachments ) && isset( $attachments->Data ) && isset( $attachments->Filename ) )
			{
				$email->addRawFile ( $attachments->Data, $attachments->Filename, $attachments->Type, $attachments->Encoding );
			}
			else if ( is_string( $attachments ) )
			{
				foreach ( $attachments as $att )
				{
					$email->addAttachment ( $att );
				}
			}
			else if ( is_array( $attachments ) )
			{
				foreach ( $attachments as $att )
				{
					if ( is_object( $att ) )
					{
						if ( isset( $att->Data ) && isset( $att->Filename ) )
						{
							$email->addRawFile ( $att->Data, $att->Filename, $att->Type, $att->Encoding );
						}
					}
					else
					{
						$email->addAttachment ( $att );
					}
				}
			}
		}
		
		// Use template
		if ( !$template && file_exists ( 'subether/templates/standardemail.php' ) )
		{
			$a = new cPTemplate ( 'subether/templates/standardemail.php' );
			$a->subject = $subject;
			$a->body = $Article;
			$Article = $a->render ();
		}
		
		// Extract all images and add to mail data
		$embedImages = array ();
		$cid = 1;
		preg_match_all ( '/\<img[^>]*?\>/i', $Article, $matches );
		foreach ( $matches[0] as $match )
		{
			preg_match ( '/src\=\"([^"]*?)\"/i', $match, $src );
			preg_match ( '/style\=\"([^"]*?)\"/i', $match, $style );
			preg_match ( '/border\=\"([^"]*?)\"/i', $match, $border );
			if ( $style ) $style = ' style="' . $style[1] . '"'; else $style = '';
			if ( $border ) $border = ' border="' . $border[1] . '"'; else $border = '';
			$embedImages[] = array ( $match, '<img' . $style . $border . ' src="cid:image_' . 
			$mail->ID . '_' . $cid . '"/>', $src[1], 'image_' . $mail->ID . '_' . $cid );
			$cid++;
		}
		if ( count ( $embedImages ) && is_array ( $embedImages ) )
		{
			foreach ( $embedImages as $row )
			{
				list ( $original, $replace, $file, $tempName ) = $row;
				$Article = str_replace ( $original, $replace, $Article );
				$email->embedImage ( $file, false, false, $tempName );
			}
		}
		
		$email->setMessage ( $Article );
		
		if( $email->send ( $rawdata ) )
		{
			return array( 'ok' => true, 'error' => '' );
		}
		else
		{
			return array( 'ok' => false, 'error' => $email->_error_reponse );
		}
	}
}



function AIbot ( $string, $user )
{
	if ( $string )
	{
		if ( $string{0} == '/' )
		{
			$frg = explode( ' ', $string );
			$key = $frg[0];
			$key = strtolower( substr( $key, 1 ) );
			
			$arg = ( isset( $frg[1] ) ? $frg[1] : false );
			
			if ( $creativebrain = AIresources( $key ) )
			{
				if( $arg && isset( $creativebrain[$arg] ) && is_callable( $creativebrain[$arg] ) )
				{
					return $creativebrain[$arg]( $frg, $user );
				}
				else if( $arg && isset( $creativebrain[$arg] ) && !is_callable( $creativebrain[$arg] ) )
				{
					return $creativebrain[$arg];
				}
				else if ( isset( $creativebrain[$key] ) )
				{
					return $creativebrain[$key];
				}
			}
		}
	}
	
	return false;
}

function AIresources ( $key )
{
	switch( $key )
	{
		case 'help':
			return array(
				'help' => 'Commands:<br>/help<br>/hello<br>/me<br>/you<br>/whois<br>/bug',
				/*'bug' => 'Commands:<br>/bug add [name] [description]<br>/bug delete [id]'*/
				'bug' => 'Commands:<br>/bug add [name] [description]'
			);
			break;
		case 'bug':
			return array(
				'add' => function( $args, $email )
				{
					include_once ( 'subether/classes/mantis.class.php' );
					
					$bug = new Mantis( false, false, $email );
					$bug->Summery = $args[2];
					$bug->Description = $args[3];
					$id = $bug->Save();
					
					return ( 'BugID: ' . $id . ' saved to mantis.' );
				}/*,
				'delete' => function( $args ){ return 'Bug with ID: ' . $args[2] . ' is deleted'; }*/
			);
			break;
		case 'hello':
			return array( 'hello' => 'Hello there :)' );
			break;
		case 'me':
			return array( 'me' => 'You are you and so on ...' );
			break;
		case 'you':
			return array( 'you' => 'I\'m a bot' );
			break;
		case 'whois':
			return array( 'whois' => 'I\'m a bot' );
			break;
	}
	
	/*return array(
		'help' => 'Commands:<br>/help<br>/hello<br>/me<br>/you<br>/whois',
		'hello' => 'Hello there :)',
		'me' => 'You are you and so on ...',
		'you' => 'I\'m a bot',
		'whois' => 'I\'m a bot'
	);*/
}

?>
