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

function generatePassword ( $length = 10 )
{
	$chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$str = '';
	$max = strlen( $chars ) - 1;
	for ( $i = 0; $i < $length; $i++ )
	{
	  $str .= $chars[ mt_rand( 0, $max ) ];
	}
	return $str;
}

function generateHumanPassword ()
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

function checkAuth( $post )
{
	$out = array();
	
	if( !$post[ 'webUsername' ] && !$post[ 'webPassword' ] ) 
	{
		$out[ 'error' ] = 'Username or Password is empty'; return $out;
	}
	
	$u = new dbObject( 'Users' );
	$u->Username = trim( $post[ 'webUsername' ] );
	$u->Password = md5( trim( $post[ 'webPassword' ] ) );
	if( $u = $u->findSingle() )
	{
		$out[ 'error' ] = false;
		// LogUser: login
		//logUser( 'login' );		
	}
	else
	{
		$out[ 'error' ] = 'Username and Password didnt match ( user@email.com / yourpassword )';
	}
	
	return $out;
}

function renderDropDownList( $pos )
{
	global $database;
	
	$type = ( UserAgent() == 'web' ? 'global' : 'mobile' );
	
	$q = '
		SELECT *
		FROM SModules
		WHERE Type = \'' . $type . '\' AND Position = \'' . $pos . '\' AND Visible = "3" 
		' . ( IsSystemAdmin() ? 'AND ( UserLevels = ",99,1," OR UserLevels = ",99," OR UserLevels = ",0," ) ' : 'AND ( UserLevels = ",99,1," OR UserLevels = ",0," ) ' ) . '
		GROUP BY Name 
		ORDER BY SortOrder ASC, Name ASC 
	';

	if( $rows = $database->fetchObjectRows( $q, false, 'components/authentication/include/functions.php' ) )
	{
		foreach( $rows as $row )
		{
			$row->Components = $database->fetchObjectRows( $q2 = '
				SELECT * 
				FROM SComponents 
				WHERE Module = \'' . $row->Name . '\' AND Type = \'' . $type . '\' AND Position = \'' . $pos . '\' 
				' . ( IsSystemAdmin() ? 'AND ( UserLevels = ",99,1," OR UserLevels = ",99," OR UserLevels = ",0," ) ' : 'AND ( UserLevels = ",99,1," OR UserLevels = ",0," ) ' ) . '
				GROUP BY Name 
				ORDER BY SortOrder ASC, Name ASC 
			', false, 'components/authentication/include/functions.php' );
		}
		
		return $rows;
	}
	return false;
}


?>
