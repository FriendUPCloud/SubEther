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

function checkUser( $post, $key )
{
	if( !$post || !$key ) return false;
	
	$a = array( 'username'=>'Name', 'email'=>'Email' );
	
	$u = new dbObject ( 'Users' );
	if( !$a[$key] ) return false;
	else
	{
		$u->$a[$key] = $post;
	}
	if( $u->load() )
	{
		return true;
	}
	else return false;
	
}

function makeAuthKey ()
{
	$numbs = '0123456789';
	$final = '';
	for ( $a = 0; $a < 5; $a++ )
	{
		$final .= substr ( $numbs, rand ( 0, strlen ( $numbs )-1 ), 1 );
	}
	return $final;
}

function makePassword ( $length = 10 )
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



if( !function_exists( 'makeHumanPassword' ) )
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



/*
function checkForAuthKey( $post )
{
	$out = array();
	
	if( !$post[ 'Email' ] ) 
	{
		$out[ 'reason' ] = 'Email is required'; 
		return $out;
	}
	
	if( !checkBetaInvites( $post[ 'Email' ] ) )
	{
		$out[ 'reason' ] = 'Email didnt match invited only list'; 
		return $out;
	}
	
	$c = new dbObject( 'SBookContact' );
	$c->Email = trim( $post[ 'Email' ] );
	$c->IsMail = 1;
	if( $c->load() )
	{
		if( $c->AuthKey && !$post[ 'AuthKey' ] )
		{
			$out[ 'response' ] = 'authenticate';
		}
		else if( $c->AuthKey && trim( strtolower( $c->AuthKey ) ) == trim( strtolower( $post[ 'AuthKey' ] ) ) )
		{
			$out[ 'response' ] = 'ok';
		}
		else
		{
			$out[ 'reason' ] = 'AuthKey didnt match';
		}
	}
	else
	{
		if( $c->AuthKey = makeAuthKey() )
		{
			// Send kode to email
			$cs = 'AuthKey';
			$cr = $c->Email;
			$cm = 'AuthKey: ' . $c->AuthKey;
			$ct = 'html';
			mailNow_ ( $cs, $cm, $cr, $ct );
			
			$c->save();
			
			$out[ 'response' ] = 'authenticate';
		}
		else
		{
			$out[ 'reason' ] = 'Couldnt make AuthKey, contact web administrator';
		}
	}
	
	return $out;
}*/

if( !function_exists ( 'checkBetaInvites' ) )
{
	function checkBetaInvites ( $email )
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

/*
function signUp( $post )
{
	if( !$post[ 'Email' ] && !$post[ 'Password' ] ) return false;
	
	$co = new dbObject( 'SBookContact' );
	$co->Email = $post[ 'Email' ];
	$co->AuthKey = $post[ 'AuthKey' ];
	if( !$co->load() )
	{
		return false;
	}
	
	$us = new dbObject( 'Users' );
	$us->Username = $post[ 'Email' ];
	if( $us->load() )
	{
		return false;
	}
	$us->Password = md5( $post[ 'Password' ] );
	$us->Name = $post[ 'Username' ];
	$us->Email = $post[ 'Email' ];
	$us->DateCreated = date( 'Y-m-d H:i:s' );
	$us->DateModified = date( 'Y-m-d H:i:s' );
	$us->IsTemplate = 0;
	$us->save();
	
	$co->UserID = $us->ID;
	$co->Username = $post[ 'Username' ];
	$co->Firstname = $post[ 'Firstname' ];
	$co->Lastname = $post[ 'Lastname' ];
	$co->DateCreated = date( 'Y-m-d H:i:s' );
	$co->DateModified = date( 'Y-m-d H:i:s' );
	$co->save();
	
	$gr = new dbObject( 'Groups' );
	$gr->Name = 'SocialNetwork';
	if( !$gr->load() )
	{
		$gr->save();
	}
	
	$ug = new dbObject( 'UsersGroups' );
	$ug->GroupID = $gr->ID;
	$ug->UserID = $us->ID;
	$ug->save();
	
	// LogUser: signup
	logUser( 'signup' );	
	
	// Assign to SubEther user/group
	assignToNewMembers( $us->ID );
	
	return true;
}*/

function SanitizeName( $username )
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

function assignToNewMembers ( $cid, $gname = false, $uname = false, $sendmail = true )
{
	global $database;
	
	if( !$cid ) return false;
	
	include_once ( 'subether/functions/globalfuncs.php' );
	
	$n = new dbObject( 'SBookContact' );
	$n->Load( $cid );
	
	if ( $n->ID > 0 && ( $p = $database->fetchObjectRow( '
		SELECT
			*
		FROM
			SBookCategory
		WHERE
				Type = "Group"
			AND Name = "Groups"
	' ) ) )
	{
		// Assign to SubEther group
		$g = new dbObject( 'SBookCategory' );
		$g->Type = 'SubGroup';
		$g->Name = ( $gname ? $gname : ( defined( 'NODE_DEFAULT_GROUP' ) && NODE_DEFAULT_GROUP ? NODE_DEFAULT_GROUP : 'SubEther' ) );
		if( !$g->Load() )
		{
			$g->CategoryID = $p->ID;
			$g->UniqueID  = UniqueKey();
			$g->Privacy = 'OpenGroup';
			$g->Save();
		}
		
		// Old method - TODO: Remove when every trace of this is deleted in the system
		$r = new dbObject( 'SBookCategoryRelation' );
		$r->ObjectType = 'Users';
		$r->ObjectID = $n->UserID;
		if( !$r->Load() )
		{
			$r->CategoryID = $g->ID;
			$r->Save();
		}
		
		// New method
		$a = new dbObject( 'SBookCategoryAccess' );
		$a->UserID = $n->ID;
		$a->ContactID = $n->ID;
		if( !$a->Load() )
		{
			$a->CategoryID = $g->ID;
			$a->Read = 1;
			$a->Write = 1;
			$a->Delete = 1;
			$a->Save();
		}
		
		$admin = 'SubEther';
		
		if ( $adm = $database->fetchObjectRow( '
			SELECT c.* FROM Users u, SBookContact c 
			WHERE u.IsAdmin = "1" AND c.UserID = u.ID 
			ORDER BY u.ID ASC 
			LIMIT 1
		' ) )
		{
			$admin = $adm->Username;
		}
		
		$c = new dbObject( 'SBookContact' );
		$c->Username = ( $uname ? $uname : ( defined( 'NODE_DEFAULT_ADMIN' ) && NODE_DEFAULT_ADMIN ? NODE_DEFAULT_ADMIN : $admin ) );
		if( $c->Load() )
		{
			// Assign to SubEtherAdmin contact
			$r = new dbObject( 'SBookContactRelation' );
			$r->ObjectType = 'SBookContact';
			$r->ObjectID = $cid;
			if( !$r->Load() )
			{
				$r->ContactID = $c->ID;
				$r->Save();
			}
			
			// TODO: Send mail to the one that invited if not just the default user ...
			
			if( $sendmail && $c->Email && function_exists( 'mailNow_' ) )
			{
				// Get admin emails from administration group
				$admins = array( $c->Email );
				
				foreach( $admins as $email )
				{
					// Send email to admins
					$cs  = $n->Username . ' Just Signed Up On ' . ( BASE_URL ? BASE_URL : 'http://sub-ether.org/' );
					$cr  = $email;
					$cm  = 'Username: ' . $n->Username . '<br>';
					$cm .= 'Email: ' . $n->Email . '<br>';
					$cm .= 'URL: <a href="' . ( BASE_URL ? BASE_URL : 'http://sub-ether.org/' ) . $n->Username . '">' . ( BASE_URL ? BASE_URL : 'http://sub-ether.org/' ) . $n->Username . '</a>';
					$ct  = 'html';
					
					$res = mailNow_ ( $cs, $cm, $cr, $ct );
				}
			}
		}
	}
}

?>
