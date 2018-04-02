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

// Check upload -------------------------------------------------------------------------------------------------------------

if ( !file_exists ( "$root/subether/upload" ) )
{
	@mkdir( "$root/subether/upload", 0777, true );
	@chmod( "$root/subether/upload", 0777 );
}
if ( !file_exists ( "$root/subether/upload" ) )
{
	$errors[] = 'No "subether/upload" directory exists!';	
}
else if ( !( $fp = fopen ( "$root/subether/upload/test", 'w' ) ) )
{
	$errors[] = 'The "subether/upload" folder is not writable. Please change the permissions on it to "777".';
}
if ( file_exists( "$root/subether/upload/test" ) )
{
	@unlink( "$root/subether/upload/test" );
}
if ( $fp )
{
	fclose ( $fp );
	$fp = false;
}

// Check backup ------------------------------------------------------------------------------------------------------------

if ( !file_exists ( "$root/subether/upload/backup" ) )
{
	@mkdir( "$root/subether/upload/backup", 0777, true );
	@chmod( "$root/subether/upload/backup", 0777 );
}
if ( !file_exists ( "$root/subether/upload/backup" ) )
{
	$errors[] = 'No "subether/upload/backup" directory exists!';	
}
else if ( !( $fp = fopen ( "$root/subether/upload/backup/test", 'w' ) ) )
{
	$errors[] = 'The "subether/upload/backup" folder is not writable. Please change the permissions on it to "777".';
}
if ( file_exists( "$root/subether/upload/backup/test" ) )
{
	@unlink( "$root/subether/upload/backup/test" );
}
if ( $fp )
{
	fclose ( $fp );
	$fp = false;
}

// Check private -------------------------------------------------------------------------------------------------------------

if ( !file_exists ( "$root/subether/upload/private" ) )
{
	@mkdir( "$root/subether/upload/private", 0777, true );
	@chmod( "$root/subether/upload/private", 0777 );
}
if ( !file_exists ( "$root/subether/upload/private" ) )
{
	$errors[] = 'No "subether/upload/private" directory exists!';	
}
else if ( !( $fp = fopen ( "$root/subether/upload/private/test", 'w' ) ) )
{
	$errors[] = 'The "subether/upload/private" folder is not writable. Please change the permissions on it to "777".';
}
if ( file_exists( "$root/subether/upload/private/test" ) )
{
	@unlink( "$root/subether/upload/private/test" );
}
if ( $fp )
{
	fclose ( $fp );
	$fp = false;
}

// Check releases ------------------------------------------------------------------------------------------------------------

if ( !file_exists ( "$root/subether/upload/releases" ) )
{
	@mkdir( "$root/subether/upload/releases", 0777, true );
	@chmod( "$root/subether/upload/releases", 0777 );
}
if ( !file_exists ( "$root/subether/upload/releases" ) )
{
	$errors[] = 'No "subether/upload/releases" directory exists!';	
}
else if ( !( $fp = fopen ( "$root/subether/upload/releases/test", 'w' ) ) )
{
	$errors[] = 'The "subether/upload/releases" folder is not writable. Please change the permissions on it to "777".';
}
if ( file_exists( "$root/subether/upload/releases/test" ) )
{
	@unlink( "$root/subether/upload/releases/test" );
}
if ( $fp )
{
	fclose ( $fp );
	$fp = false;
}

// Check temp ------------------------------------------------------------------------------------------------------------------

if ( !file_exists ( "$root/subether/upload/temp" ) )
{
	@mkdir( "$root/subether/upload/temp", 0777, true );
	@chmod( "$root/subether/upload/temp", 0777 );
}
if ( !file_exists ( "$root/subether/upload/temp/" ) )
{
	$errors[] = 'No "subether/upload/temp" directory exists!';	
}
else if ( !( $fp = fopen ( "$root/subether/upload/temp/test", 'w' ) ) )
{
	$errors[] = 'The "subether/upload/temp" folder is not writable. Please change the permissions on it to "777".';
}
if ( file_exists( "$root/subether/upload/temp/test" ) )
{
	@unlink( "$root/subether/upload/temp/test" );
}
if ( $fp )
{
	fclose ( $fp );
	$fp = false;
}

if ( !$errors )
{
	if ( $sdb && !$sdb->fetchObjectRows( 'SELECT ID FROM ContentElement ORDER BY ID ASC' ) )
	{
		if ( file_exists( $basedir . '/subether/arenadefault.sql' ) )
		{
			$test = [];
			// Import structure
			$sql = file_get_contents ( $basedir . '/subether/arenadefault.sql' );
			$sql = explode ( ');', $sql );
			foreach ( $sql as $s )
			{
				if ( $s{0} == '-' ) continue;
				if ( !trim ( $s ) ) continue;
				$qry = explode( 'INSERT INTO', $s );
				if( $qry && isset( $qry[1] ) )
				{
					$sdb->query ( $test[] = trim( "INSERT INTO".$qry[1].");" ) );
				}
			}
		}
		
		if( !$sdb->fetchObjectRows( 'SELECT ID FROM ContentElement ORDER BY ID ASC' ) )
		{
			die( $basedir . '/subether/arenadefault.sql didn\'t install correctly, import this into mysql manually ... '."\r\n".'<pre>' . print_r( $test,1 ) . '</pre> ---' . mysql_error() );
		}
	}
	
	$debug   = 'error_reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED );'."\n\t";
	$debug  .= 'ini_set( \'display_errors\', true );'."\n\t";
	
	// Additional config data
	$config  = $debug . ( isset( $config ) ? $config : '' );
	
	$config .= '// Use only one language page structure'."\n\t";
	$config .= 'define( \'LANGUAGES_ONE_PAGE_STRUCTURE\', true );'."\n\t";
	$config .= 'define( \'COMBINE_RESOURCES\', true );'."\n\t";
	$config .= '// API ---------------------------------------------------------------------'."\n\t";
	$config .= 'define( \'API\', \'subether/restapi/websnippet.php\' );'."\n\t";
	$config .= '// Node setup --------------------------------------------------------------'."\n\t";
	$config .= 'define( \'NODE_LOG\', false );'."\n\t";
	$config .= 'define( \'NODE_OPEN\', 1 );'."\n\t";
	$config .= 'define( \'NODE_CREATED\', \'' . date( 'Y-m-d H:i:s' ) . '\' );'."\n\t";
	$config .= 'define( \'NODE_THEME\', \'' . 'subether' . '\' );'."\n\t";
	$config .= 'define( \'NODE_DEFAULT_GROUP\', \'\' );'."\n\t";
	$config .= 'define( \'NODE_DEFAULT_ADMIN\', \'\' );'."\n\t";
	$config .= '// Module settings ---------------------------------------------------------'."\n\t";
	$config .= 'define( \'MODULE_ENGINE\', \'bing\' );'."\n\t";
	$config .= 'define( \'HIDE_LOGIN_SCREEN\', true );'."\n\t";
	$config .= '// Component settings ------------------------------------------------------'."\n\t";
	$config .= 'define( \'HIDE_EXTERNAL_VIDEOS\', false );'."\n\t";
	$config .= 'define( \'WALL_DEFAULT_CATEGORYID\', 0 );'."\n\t";
	$config .= 'define( \'WALL_DEFAULT_ACCESS\', 0 );'."\n\t";
	$config .= '// General settings --------------------------------------------------------'."\n\t";
	$config .= 'define( \'ADMIN_LANGUAGE\', \'en\' );'."\n\t";
}
else
{
	$tpl = new cPTemplate ( "$root/lib/templates/installer/error.php" );
	foreach ( $errors as $er )
	{
		$tpl->error .= '<p>' . $er . '</p>';
	}
}

?>
