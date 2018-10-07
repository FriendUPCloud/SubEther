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

// User cache
$GLOBALS['cachedUsers'] = array ();
$GLOBALS['cachedUserStatus'] = array ();
$GLOBALS['cachedUserData'] = array();
$GLOBALS['cachedUserContactIDs'] = array();

// Makes sure the username is not the name of a component!
function SanitizeUsername ( $usr, $path = false )
{
	global $webuser;
	
	if ( $path && $usr )
	{
		if ( $len = strlen( $path ) )
		{
			if ( substr( $usr, 0, $len ) == $path )
			{
				$usr = substr( $usr, $len );
			}
		}
	}
	
	$str = explode( '/', trim( $usr ) );
	//if( $webuser->ID == 113 ) die( $usr . ' -- ' . $path . ' -- ' . print_r( $str,1 ) );
	return $str[0];
}

function LoadSBookUserByUsername ( $nam )
{
	global $database, $cachedUsers;
	
	if( !trim( $nam ) ) return false;
	
	foreach( $cachedUsers as $cus )
		if( $cus->Username == $nam )
			return $cus;
	
	if( isset( $cachedUsers['_failed_'][$nam] ) )
	{
		return false;
	}
	
	if ( $u = $database->fetchObjectRow ( '
		SELECT
			c.*
		FROM
			Users u,
			SBookContact c 
		WHERE
				c.Username = "' . $nam . '"
			AND u.ID = c.UserID
			AND u.IsDeleted = "0" 
	', false, 'functions/userfuncs.php' ) )
	{
		return $u;
	}
	else
	{
		if( !isset( $cachedUsers['_failed_'] ) )
		{
			$cachedUsers['_failed_'] = array();
		}
		
		$cachedUsers['_failed_'][$nam] = $nam;
	}
	return false;
}

function ThemeData( $tid = '' )
{
	include_once ( 'subether/components/library/include/functions.php' );
	
    $themes = openFolder( 'subether/themes' );
    
    if( $themes && is_array( $themes ) )
    {
        $i = 1; $folders = array();
        
        $obj = new stdClass();
        $obj->ID = 0;
        $obj->Name = 'Default';
        
        $folders[0] = $obj;
        
        foreach( $themes as $fld )
        {
            $obj = new stdClass();
            $obj->ID = $i++;
            $obj->Dir = $fld->name;
            $obj->Path = $fld->path;
            
            if( $content = openFile( $fld->path, 'info' ) )
            {
                $data = explode( ',', $content );
                
                if( isset( $data[0] ) )
                {
                    $obj->Name = trim( $data[0] );
                }
                if( isset( $data[1] ) )
                {
                    $obj->Thumb = $obj->Path . '/' . trim( $data[1] );
                }
            }
            
            $folders[$obj->ID] = $obj;
        }
        
        return ( $tid != '' && isset( $folders[$tid] ) ? $folders[$tid] : $folders );
    }
    
    return false;
}

function UserLanguage()
{
	global $Session, $webuser;
	
	if ( $webuser->ID > 0 && !isset( $_REQUEST['setlang'] ) && !$_SESSION['UserLanguage'] )
	{
		$c = new dbObject( 'SBookContact' );
		$c->UserID = $webuser->ID;
		if ( $c->Load() )
		{
			$c->Data = json_obj_decode( $c->Data );
			
			if ( isset( $c->Data->LanguageCode ) && $c->Data->LanguageCode )
			{
				$Session->Set( 'CurrentLanguage', $c->Data->CurrentLanguage );
				$Session->Set( 'LanguageCode', $c->Data->LanguageCode );
				
				$_SESSION['UserLanguage'] = $c->Data->LanguageCode;
			}
		}
	}
}

function UserAgent()
{
	global $webuser;
	
	if ( $_REQUEST['displaymode'] == '0' )
	{
		if( !isset( $_REQUEST['save'] ) || $_REQUEST['save'] )
		{
			$_SESSION['UserAgent'] = 'web';
		}
		return 'web';
	}
	if ( $_REQUEST['displaymode'] == '1' || $_SESSION['UserAgent'] == 'mobile' )
	{
		if( !isset( $_REQUEST['save'] ) || $_REQUEST['save'] )
		{
			$_SESSION['UserAgent'] = 'mobile';
		}
		return 'mobile';
	}
	if ( $_REQUEST['displaymode'] == '2' || $_SESSION['UserAgent'] == 'presentation' )
	{
		if( !isset( $_REQUEST['save'] ) || $_REQUEST['save'] )
		{
			$_SESSION['UserAgent'] = 'presentation';
		}
		return 'mobile presentation';
	}
	if ( $_REQUEST['displaymode'] == '3' || $_SESSION['UserAgent'] == 'tablet' )
	{
		if( !isset( $_REQUEST['save'] ) || $_REQUEST['save'] )
		{
			$_SESSION['UserAgent'] = 'tablet';
		}
		return 'tablet';
	}
	if ( strstr( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'iphone' ) )
	{
		return 'mobile iphone';
	}
	if ( strstr( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'ipad' ) )
	{
		return 'tablet ipad';
	}
	if ( strstr( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'android' ) )
	{
		return 'mobile android';
	}
	
	if ( $webuser->ID > 0 && !isset( $_REQUEST['displaymode'] ) && !isset( $_SESSION['UserAgent'] ) )
	{
		$c = new dbObject( 'SBookContact' );
		$c->UserID = $webuser->ID;
		if ( $c->Load() )
		{
			$c->Data = json_obj_decode( $c->Data );
			
			if ( isset( $c->Data->Display ) )
			{
				if ( $c->Data->Display == 0 )
				{
					$_SESSION['UserAgent'] = 'web';
				}
				else if ( $c->Data->Display == 1 )
				{
					$_SESSION['UserAgent'] = 'mobile';
				}
				else if ( $c->Data->Display == 2 )
				{
					$_SESSION['UserAgent'] = 'presentation';
				}
				else if ( $c->Data->Display == 3 )
				{
					$_SESSION['UserAgent'] = 'tablet';
				}
			}
		}
	}
	
	return 'web';
}

// Sets up user data with linking the Users and SBookContact tables
function setupUserData ( $cuser = false, $overwrite = false )
{
	global $database, $webuser;
	
	if( ( $cuser && isset( $cuser->ID ) ) || ( $overwrite && $webuser->ContactID ) )
	{
		//die( $webuser->ContactID . ' --' );
		if( $u = CacheUser( isset( $cuser->ID ) ? $cuser->ID : $webuser->ContactID ) )
		{
			$u = $u->ContactInfo;
			
			if( $u->Firstname ) $first = $u->Firstname . ' ';
			if( $u->Middlename ) $middle = $u->Middlename . ' ';
			if( $u->Lastname ) $last = $u->Lastname . ' ';
			
			if( $u->Display == 1 )
			{
				$u->DisplayName = trim( $first . $middle . $last );
			}
			else if( $u->Display == 2 )
			{
				$u->DisplayName = trim( $first . $last );
			}
			else if( $u->Display == 3 )
			{
				$u->DisplayName = trim( $last . $first );
			}
			else $u->DisplayName = $u->Username;
			
			//$u->ContactID = $u->ID;
			if ( $cuser )
			{
				$u->ID = $cuser->UserID;
				$u->Image = $cuser->ImageID;
			}
			//$u->AuthKey = 0;
			//$u->Groups =& $cuser->Groups;
			//$u->Contacts =& $cuser->Contacts;
			
			$cuser = $u;
		}
		//else $cuser =& $user;
	}
	else if( $webuser )
	{
		//$user = $webuser;
		
		if( $c = CacheUser( false, $webuser->ID ) )
		{
			
			$c = $c->ContactInfo;
			
			if( $c->Firstname ) $first = $c->Firstname . ' ';
			if( $c->Middlename ) $middle = $c->Middlename . ' ';
			if( $c->Lastname ) $last = $c->Lastname . ' ';
			
			if( $c->Display == 1 )
			{
				$c->DisplayName = trim( $first . $middle . $last );
			}
			else if( $c->Display == 2 )
			{
				$c->DisplayName = trim( $first . $last );
			}
			else if( $c->Display == 3 )
			{
				$c->DisplayName = trim( $last . $first );
			}
			else $c->DisplayName = $c->Username;
			
			//$c->ContactID = $c->ID;
			//$c->Username = $c->Username;
			//$c->Image = $c->ImageID;
			//$c->ImageID = $c->ImageID;
			//$c->Password = 0;
			
			$webuser->ContactID = $c->ContactID;
			
			$cuser = $c;
		}
	}
	/*else if( !$cuser->UserID && $cuser->ID )
	{
		if( $u = $database->fetchObjectRow ( 'SELECT * FROM SBookContact WHERE UserID = \'' . $cuser->ID . '\' ' ) )
		{
			if( $u->Firstname ) $first = $u->Firstname . ' '; else $first = '';
			if( $u->Middlename ) $middle = $u->Middlename . ' '; else $middle = '';
			if( $u->Lastname ) $last = $u->Lastname . ' '; else $last = '';
			
			if( $u->Display == 1 )
			{
				$u->DisplayName = trim( $first . $middle . $last );
			}
			else if( $u->Display == 2 )
			{
				$u->DisplayName = trim( $first . $last );
			}
			else if( $u->Display == 3 )
			{
				$u->DisplayName = trim( $last . $first );
			}
			else $u->DisplayName = $u->Username;

			$u->ContactID = $u->ID;
			$u->ID = $cuser->ID;
			$u->Image = $cuser->ImageID;
			$u->AuthKey = 0;
			$cuser =& $u;
		}
		else $cuser = $user;
	}*/
	
	//if( !$cuser ) $cuser =& $user;
	if( !$cuser ) $cuser = $webuser;
	
	return $cuser;
}

function FindIdByRoute ( $path )
{
	//if( !$path ) return false;
	//$url = explode( '/', str_replace( $path, '', $_REQUEST['route'] ) );
	//if( $url[0] && is_numeric( $url[0] ) )
	//{	
	//	return $url[0];
	//}
	
	$url = explode( '/', $_REQUEST['route'] );
	
	if ( $url && is_array( $url ) )
	{
		//rsort ( $url );
		if( isset( $_REQUEST['testchris2'] ) ) die( $path . ' -- ' . print_r( $url,1 ) );
		foreach ( $url as $u )
		{
			if ( trim( $u ) != '' )
			{
				$vars = explode( '?', $u );
				
				if ( $vars[0] && !strstr( $vars[0], '?' ) && is_numeric( $vars[0] ) )
				{
					return trim( $vars[0] );
				}
			}
		}
	}
	
	return false;
}

function IsSystemAdmin ( $uid = false )
{
	global $webuser, $database;
	
	$user = ( $webuser->ID > 0 ? $webuser->ID : $uid );
	
	if( isset( $GLOBALS['WebuserSystemAdmin'] ) )
	{
		return $GLOBALS['WebuserSystemAdmin'];
	}
	else if( $webuser->ID > 0 && method_exists( $webuser, 'isSuperUser' ) && $webuser->isSuperUser() )
	{
		$GLOBALS['WebuserSystemAdmin'] = true;
		return true;
	}
	else if( $user )
	{
		$q = '
			SELECT ul.UserID, ul.DataSource, us.ID, us.Username, us.InGroups, us.IsAdmin, gr.SuperAdmin, gr.Name 
			FROM UserLogin ul, Users us, UsersGroups ug, Groups gr 
			WHERE 
				ul.UserID = \'' . $user . '\' 
				AND ul.DataSource = "site" 
				AND us.ID = ul.UserID 
				AND us.IsAdmin = "1" 
				AND us.InGroups = "1" 
				AND us.IsDisabled = "0" 
				AND ug.UserID = us.ID 
				AND gr.ID = ug.GroupID 
				AND gr.SuperAdmin = "1" 
			ORDER BY ul.ID DESC 
		';
		
		if( $row = $database->fetchObjectRow( $q, false, 'functions/userfuncs.php' ) )
		{
			$GLOBALS['WebuserSystemAdmin'] = true;
			return true;
		}
	}
	$GLOBALS['WebuserSystemAdmin'] = false;
	return false;
}

function CategoryAccess( $cid, $catid = false, $system = false, $status = false, $overwrite = false )
{
	global $database, $webuser;
	
	if( !$cid ) return false;
	
	$cats = array(); $cids = array(); $q = false;
	
	if( IsSystemAdmin() )
	{
		$cats['IsSystemAdmin'] = true;
	}
	
	if( is_array( $cid ) && $catid > 0 )
	{
		if ( isset( $webuser->ContactID ) )
		{
			$cid[] = $webuser->ContactID;
		}
		
		$q = '
			SELECT 
				c.Name AS CategoryName, 
				c.Owner AS CategoryOwnerID, 
				c.IsSystem, 
				c.Settings,
				c.ParentID, 
				p.Settings AS ParentSettings, 
				a.* 
			FROM 
				SBookCategoryAccess a,
				SBookCategory c 
					LEFT JOIN SBookCategory p ON
					(
							c.ParentID > 0
						AND p.ID = c.ParentID
					) 
			WHERE 
					a.UserID IN ( ' . implode( ',', $cid ) . ' ) 
				AND a.CategoryID = \'' . $catid . '\' 
				AND c.ID = a.CategoryID 
				AND
				(
					(
						c.Owner > 0
					)
					OR
					(
							c.Owner = "0"
						AND a.ContactID IN ( ' . implode( ',', $cid ) . ' )
					)
				)'
				. ( $system ? '
				AND c.IsSystem = \'' . ( $system > 0 ? 1 : 0 ) . '\' '
				: '' ) . '
			ORDER BY 
				a.ID ASC 
		';
	}
	else if ( !is_array( $cid ) )
	{
		$q = '
			SELECT 
				c.Name AS CategoryName, 
				c.Owner AS CategoryOwnerID, 
				c.IsSystem, 
				c.Settings, 
				c.ParentID, 
				p.Settings AS ParentSettings, 
				a.* 
			FROM 
				SBookCategoryAccess a, 
				SBookCategory c 
					LEFT JOIN SBookCategory p ON
					(
							c.ParentID > 0
						AND p.ID = c.ParentID
					) 
			WHERE 
					a.UserID = \'' . ( isset( $webuser->ContactID ) ? $webuser->ContactID : $cid ) . '\' 
				AND c.ID = a.CategoryID 
				AND
				(
					(
						c.Owner > 0
					)
					OR
					(
							c.Owner = "0"
						AND a.ContactID = \'' . $cid . '\'
					)
				)'
				. ( $system ? '
				AND c.IsSystem = \'' . ( $system > 0 ? 1 : 0 ) . '\' '
				: '' ) . '
			ORDER BY 
				a.ID ASC 
		';
	}
	
	if( $q && ( $cts = $database->fetchObjectRows( $q, false, 'functions/userfuncs.php' ) ) )
	{
		foreach( $cts as $c )
		{
			$c->Settings = ( is_object( $c->Settings ) ? $c->Settings : json_obj_decode( $c->Settings ) );
			
			if ( $c->ParentID > 0 && isset( $c->ParentSettings ) )
			{
				$c->ParentSettings = ( is_object( $c->ParentSettings ) ? $c->ParentSettings : json_obj_decode( $c->ParentSettings ) );
				
				if ( isset( $c->ParentSettings->AccessLevels ) )
				{
					$c->Settings->AccessLevels = $c->ParentSettings->AccessLevels;
				}
			}
			
			if ( is_object( $c->Settings ) && ( !isset( $c->Settings->AccessLevels[0] ) || !$c->Settings->AccessLevels[0]->ID && !$c->Settings->AccessLevels[0]->Name ) )
			{
				$c->Settings->AccessLevels = json_obj_decode( '[{"ID":"n","Name":"Owner","Display":"@","r":"1","w":"1","d":"1","a":"1","o":"1"},{"ID":"o","Name":"Admin","Display":"@","r":"1","w":"1","d":"1","a":"1","o":"0"},{"ID":"v","Name":"Moderator","Display":"+","r":"1","w":"1","d":"0","a":"1","o":"0"},{"ID":"i","Name":"Member","Display":"","r":"1","w":"1","d":"1","a":"0","o":"0"}]' );
			}
			
			$NewSettings = '';
			//if ( $c->CategoryID == 22 && $catid == 22 && $webuser->ContactID == 19 ) die( print_r( $c,1 ) . ' -- ' . $overwrite );
			
			// --- Display based on groups access levels -----------------------------
			if ( isset( $c->Settings->AccessLevels ) )
			{
				foreach( $c->Settings->AccessLevels as $l )
				{
					$obj = new stdClass();
					$obj->ID = $l->ID;
					$obj->Name = $l->Name;
					$obj->Display = $l->Display;
					$obj->Permission = ( $l->o . $l->a . $l->d . $l->w . $l->r );
					$obj->Accounting = $l->Accounting;
					$obj->r = $l->r;
					$obj->w = $l->w;
					$obj->d = $l->d;
					$obj->a = $l->a;
					$obj->o = $l->o;
					
					
					/*if ( !$NewSettings && $overwrite && $catid )
					{
						$Access = false;
						
						if ( is_string( $overwrite ) )
						{
							$overwrite = json_obj_decode( $overwrite );
						}
						
						if ( ( is_object( $overwrite ) && isset( $overwrite->{$l->ID} ) ) || ( is_array( $overwrite ) && isset( $overwrite[$l->ID] ) ) )
						{
							$Access = ( is_array( $overwrite ) && isset( $overwrite[$l->ID] ) ? $overwrite[$l->ID] : $overwrite->{$l->ID} );
						}
						
						if ( $Access && strstr( ( ','.$Access.',' ), ( ','.(string)$c->UserID.',' ) ) )
						{
							$c->Access = $l->ID;
							$c->Read = $l->r;
							$c->Write = $l->w;
							$c->Delete = $l->d;
							$c->Admin = $l->a;
							$c->Owner = $l->o;
							
							$NewSettings = $obj;
							//$c->Settings = $obj;
						}
					}
					else */if ( !$NewSettings && !$c->Access && ( $l->r == $c->Read && $l->w == $c->Write && $l->d == $c->Delete && $l->a == $c->Admin && $l->o == $c->Owner ) )
					{
						$NewSettings = $obj;
						//$c->Settings = $obj;
					}
					else if ( !$NewSettings && $c->Access && $c->Access == $l->ID && ( $l->r == $c->Read && $l->w == $c->Write && $l->d == $c->Delete && $l->a == $c->Admin && $l->o == $c->Owner ) )
					{
						$NewSettings = $obj;
						//$c->Settings = $obj;
					}
				}
			}
			
			//if ( $c->CategoryID == 22 && $catid == 22 && $webuser->ContactID == 19 ) die( print_r( $c,1 ) . ' -- ' . print_r( $NewSettings,1 ) );
			
			// --- SystemAdmin level --------------------------------------------------
			if( IsSystemAdmin() && $c->UserID == $webuser->ContactID )
			{
				$c->Read = 1;
				$c->Write = 1;
				$c->Delete = 1;
				$c->Admin = 1;
				
				$c->IsSystemAdmin = true;
				$c->IsAdmin = true;
			}
			
			// --- Owner level -------------------------------------------------------
			if( $c->Owner && $c->Admin && $c->Read && $c->Write && $c->Delete )
			{
				$c->IsOwner = true;
			}
			
			// --- Admin level -------------------------------------------------------
			if( $c->Admin && $c->Read && $c->Write && $c->Delete )
			{
				$c->IsAdmin = true;
			}
			// --- Moderator level ---------------------------------------------------
			else if( $c->Admin && $c->Read && $c->Write && !$c->Delete )
			{
				$c->IsModerator = true;
			}
			// --- User level --------------------------------------------------------
			else if( !$c->Admin && $c->Read && $c->Write && $c->Delete )
			{
				$c->IsUser = true;
			}
			
			
			
			if( $status && !$c->{$status} )
			{
				continue;
			}
			
			if( !isset( $cats[$c->CategoryID] ) )
			{
				$cats[$c->CategoryID] = array();
			}
			
			if ( !isset( $cats[$c->CategoryID][$c->UserID] ) )
			{
				$cats[$c->CategoryID][$c->UserID] = new stdClass();
			}
			
			foreach( $c as $k=>$v )
			{
				$cats[$c->CategoryID][$c->UserID]->{$k} = $v;
			}
			
			//$cats[$c->CategoryID][$c->UserID] = $c;
			$cats[$c->CategoryID][$c->UserID]->Settings = $NewSettings;
			
			$cids[$c->CategoryID] = $c->CategoryID;
			
			//if ( $c->CategoryID == 22 && $catid == 22 && $webuser->ContactID == 19 ) die( print_r( $cats[$catid][$webuser->ContactID],1 ) . ' -- ' . print_r( $NewSettings,1 ) );
			//if ( $c->CategoryID == 22 ) die( print_r( $cats[$c->CategoryID],1 ) . ' -- ' . print_r( $NewSettings,1 ) . ' -- ' . $c->UserID );
		}
	}
	
	if( $catid )
	{
		if( !isset( $cats[$catid][$webuser->ContactID] ) && IsSystemAdmin() )
		{
			if( !isset( $cats[$catid] ) )
			{
				$cats[$catid] = array();
			}
			
			$c2 = new stdClass();
			$c2->Read = 1;
			$c2->Write = 1;
			$c2->Delete = 1;
			$c2->Admin = 1;
			
			$c2->IsSystemAdmin = true;
			$c2->IsAdmin = true;
			
			$cats[$catid][$webuser->ContactID] = $c2;
		}
		
		if( isset( $cats[$catid] ) )
		{
			if( is_array( $cid ) )
			{
				return $cats[$catid];
			}
			else
			{
				return $cats[$catid][$webuser->ContactID];
			}
		}
		return false;
	}
	
	if( $cids )
	{
		$cats['CategoryID'] = implode( ',', $cids );
	}
	
	return ( $cats ? $cats : false );
}

// TODO: Make sure we don't have security holes for changing access, check $webuser, $database, IsSystemAdmin() and dbObject

function UpdateCategoryAccess( $folderid, $userid, $perm, $contactid = false )
{
	global $database, $webuser;
	
	if( !$folderid || !$userid || !$perm ) return false;
	
	$perm = explode( '||', $perm );
	
	$rwdao = ( isset( $perm[0] ) ? $perm[0] : false );
	$access = ( isset( $perm[1] ) ? $perm[1] : false );
	
	$acc = new stdClass();
	
	// Access flags [rwdao] ( r = Read, w = Write, d = Delete, a = Admin, o = Owner )
	
	$acc->Read   = ( isset( $rwdao{0} ) && $rwdao{0} == 'r' ? 1 : 0 );
	$acc->Write  = ( isset( $rwdao{1} ) && $rwdao{1} == 'w' ? 1 : 0 );
	$acc->Delete = ( isset( $rwdao{2} ) && $rwdao{2} == 'd' ? 1 : 0 );
	$acc->Admin  = ( isset( $rwdao{3} ) && $rwdao{3} == 'a' ? 1 : 0 );
	$acc->Owner  = ( isset( $rwdao{4} ) && $rwdao{4} == 'o' ? 1 : 0 );
	
	$acc->Permission = ( $acc->Owner . $acc->Admin . $acc->Delete . $acc->Write . $acc->Read );
	
	$hasRead = false; $hasWrite = false; $hasDelete = false; $hasAdmin = false; $hasOwner = false;
	
	$userAdmin = false; $userOwner = false; $adminAccess = false; $moderatorAccess = false; $userAccess = false; $adminOwner = false;
	
	if( $adm = $database->fetchObjectRow( $q1 = '
		SELECT 
			c.Name AS CategoryName, 
			c.Owner AS CategoryOwnerID, 
			a.* 
		FROM
			SBookCategory c, 
			SBookCategoryAccess a 
		WHERE 
				a.UserID = \'' . $webuser->ContactID . '\' 
			AND a.CategoryID = \'' . $folderid . '\' 
			AND c.ID = a.CategoryID 
			AND ( c.Owner > 0 OR ( c.Owner = "0" AND a.ContactID = \'' . ( $contactid ? $contactid : $webuser->ContactID ) . '\' ) ) 
		ORDER BY 
			a.ID ASC 
	', false, 'functions/userfuncs.php' ) )
	{
		$adm->Permission = ( $adm->Owner . $adm->Admin . $adm->Delete . $adm->Write . $adm->Read );
	}
	
	if( $usr = $database->fetchObjectRow( $q2 = '
		SELECT 
			c.Name AS CategoryName, 
			c.Owner AS CategoryOwnerID, 
			a.* 
		FROM
			SBookCategory c, 
			SBookCategoryAccess a 
		WHERE 
				a.UserID = \'' . $userid . '\' 
			AND a.CategoryID = \'' . $folderid . '\' 
			AND c.ID = a.CategoryID 
			AND ( c.Owner > 0 OR ( c.Owner = "0" AND a.ContactID = \'' . ( $contactid ? $contactid : $userid ) . '\' ) ) 
		ORDER BY 
			a.ID ASC 
	', false, 'functions/userfuncs.php' ) )
	{
		$usr->Permission = ( $usr->Owner . $usr->Admin . $usr->Delete . $usr->Write . $usr->Read );
	}
	
	// --- Has Read --- 
	
	if( $adm && ( $adm->Read >= $acc->Read ) && ( !$usr || ( $adm->Read >= $usr->Read ) ) )
	{
		$hasRead = true;
	}
	
	// --- Has Write --- 
	
	if( $adm && ( $adm->Write >= $acc->Write ) && ( !$usr || ( $adm->Write >= $usr->Write ) ) )
	{
		$hasWrite = true;
	}
	
	// --- Has Delete --- 
	
	if( $adm && ( $adm->Delete >= $acc->Delete ) && ( !$usr || ( $adm->Delete >= $usr->Delete ) ) )
	{
		$hasDelete = true;
	}
	
	// --- Has Admin --- 
	
	if( $adm && ( $adm->Admin >= $acc->Admin ) && ( !$usr || ( $adm->Admin >= $usr->Admin ) ) )
	{
		$hasAdmin = true;
	}
	
	// --- Has Owner ---
	
	if( $adm && $adm->CategoryOwnerID == $adm->ContactID && ( $adm->Owner >= $acc->Owner ) && ( !$usr || ( $adm->Owner >= $usr->Owner ) ) )
	{
		$hasOwner = true;
	}
	
	// --- User Admin --- ( Read, Write, Delete, Admin ) 
	
	if( $usr && $usr->Read == 1 && $usr->Write == 1 && $usr->Delete == 1 && $usr->Admin == 1 ) 
	{
		$userAdmin = true;
	}
	
	// --- User Admin --- ( Read, Write, Delete, Admin ) 
	
	if( $usr && $usr->Owner == 1 ) 
	{
		$userOwner = true;
	}
	
	// --- Is Owner --- ( Read, Write, Delete, Admin, Owner ) 
	
	if( !$userOwner && $hasOwner && $hasAdmin && $hasDelete && $hasWrite && $hasRead )
	{
		$adminOwner = true;
	}
	
	// --- Is Admin --- ( Read, Write, Delete, Admin ) 
	
	if( !$userOwner && !$hasOwner && $hasAdmin && $hasDelete && $hasWrite && $hasRead )
	{
		$adminAccess = true;
	}
	
	// --- Is User Admin --- ( Read, Write, Delete, Admin ) 
	
	if( !$userOwner && !$hasOwner && $hasAdmin && $hasDelete && $hasWrite && $hasRead )
	{
		$UserAccess = true;
	}
	
	// --- Is Moderator --- ( Read, Write, Admin ) 
	
	if( !$userOwner && !$hasOwner && !$userAdmin && $hasAdmin && !$hasDelete && $hasWrite && $hasRead )
	{
		$moderatorAccess = true;
	}
	
	// TODO: Fix bug about owner ...
	
	// If both admin and user check is approved do the access update
	
	//if( IsSystemAdmin() || $adminOwner || $adminAccess || $moderatorAccess )
	if( IsSystemAdmin() || ( $adm && $usr && $adm->Permission >= $usr->Permission && $adm->Permission >= $acc->Permission ) )
	{
		$a = new dbObject( 'SBookCategoryAccess' );
		$a->CategoryID = $folderid;
		$a->UserID = $userid;
		$a->ContactID = $userid;
		$a->Load();
		$a->Access = $access;
		$a->Read = $acc->Read;
		$a->Write = $acc->Write;
		$a->Delete = $acc->Delete;
		$a->Admin = $acc->Admin;
		
		// If admin is owner and user is not and request is to change owner go ahead, can't change owner of a profile or a system folder
		
		if( ( IsSystemAdmin() || $adminOwner ) && $acc->Owner > 0 && ( $own = $database->fetchObjectRow( $q3 = '
			SELECT 
				c.Name AS CategoryName, 
				c.Owner AS CategoryOwnerID, 
				a.* 
			FROM 
				SBookCategory c, 
				SBookCategoryAccess a 
			WHERE 
					c.ID = \'' . $folderid . '\' 
				' . ( !IsSystemAdmin() ? ( 'AND c.Owner = \'' . $adm->UserID . '\' ' ) : '' ) . ' 
				AND c.Owner > 0 
				AND a.CategoryID = c.ID 
				AND a.UserID = c.Owner 
			ORDER BY 
				a.ID ASC 
		', false, 'functions/userfuncs.php' ) ) )
		{
			$c = new dbObject( 'SBookCategory' );
			$c->ID = $own->CategoryID;
			if( $c->Load() )
			{
				$o = new dbObject( 'SBookCategoryAccess' );
				$o->ID = $own->ID;
				if( $o->Load() )
				{
					// Remove owner from admin
					$o->Owner = 0;
					$o->Save();
					
					// Set new owner on category
					$c->Owner = $a->UserID;
					$c->Save();
					
					// Set owner on user
					$a->Owner = $acc->Owner;
				}
			}
		}
		// Else if there is no owner set and admin is system admin overwrite access
		else if( IsSystemAdmin() && $acc->Owner > 0 )
		{
			$c = new dbObject( 'SBookCategory' );
			$c->ID = $folderid;
			if( $c->Load() )
			{
				// Set new owner on category
				$c->Owner = $a->UserID;
				$c->Save();
				
				// Set owner on user
				$a->Owner = $acc->Owner;
			}
		}
		
		$a->Save();
		
		return true;
	}
	
	return false;
}

// Set SubEther Book Session data
function SessionSet ( $module, $component = false, $categoryid = false, $status = false )
{
	if ( !isset ( $_SESSION['sb'] ) || !is_array ( $_SESSION['sb'] ) )
		$_SESSION['sb'] = array ();
	if ( !isset ( $_SESSION['sb'][$module] ) )
		$_SESSION['sb'][$module] = array ();
	// Component specific info, set it under the component name
	if ( isset ( $component ) )
	{
		$_SESSION['sb'][$module][$component] = array ( 'Status'=>$status, 'CategoryID'=>$categoryid, 'LastActivity'=>time() );
	}
	// Module general info, set in module_status
	else
	{
		$_SESSION['sb'][$module]['module_status'] = array ( 'Status'=>$status, 'CategoryID'=>$categoryid, 'LastActivity'=>time() );
	}
}

// uid = User ID and pid = Category ID
function IsUserOnline ( $uid, $pid = false, $module = false, $component = false )
{
	global $database, $cachedUserStatus;
	
	if( !$uid ) return false;
	
	$kkey = $uid.'.'.$pid.'.'.$module.'.'.$component;
	
	if( is_array( $uid ) )
	{
		// Module specific check
		if ( $module )
		{
			// Component specific check or not?
			if ( !$component ) $component = 'module_status';
		
			$q = '
				SELECT * FROM SBookStatus 
				WHERE 
					UserID IN ( ' . implode( ',', $uid ) . ' ) AND
					CategoryID=\'' . $pid . '\' AND
					`Module`=\'' . $module . '\' AND
					`Component`=\'' . $component . '\'
				ORDER BY ID DESC, LastActivity DESC 
			';
		
			if ( $rows = $database->fetchObjectRows( $q, false, 'functions/userfuncs.php' ) )
			{
				$out = array();
				foreach( $rows as $row )
				{
					$out[$row->UserID] = $row;
				}
				return $out;
			}
		}
		else
		{
			$q = '
				SELECT * FROM SBookStatus 
				WHERE 
					UserID IN ( ' . implode( ',', $uid ) . ' ) ' . ( $pid ? ' AND CategoryID=\'' . $pid . '\'' : '' ) . '
				ORDER BY ID DESC, LastActivity DESC 
			';
			
			if ( $rows = $database->fetchObjectRows( $q, false, 'functions/userfuncs.php' ) )
			{
				$out = array();
				foreach( $rows as $row )
				{
					$out[$row->UserID] = $row;
				}
				return $out;
			}
		}
	}
	else
	{
		if( isset( $cachedUserStatus[$kkey] ) )
			return $cachedUserStatus[$kkey];
		// Module specific check
		if ( $module )
		{
			// Component specific check or not?
			if ( !$component ) $component = 'module_status';
		
			if ( $row = $database->fetchObjectRow ( '
				SELECT * FROM SBookStatus 
				WHERE 
					UserID=\'' . $uid . '\' AND 
					CategoryID=\'' . $pid . '\' AND
					`Module`=\'' . $module . '\' AND
					`Component`=\'' . $component . '\'
				ORDER BY ID DESC, LastActivity DESC 
			', false, 'functions/userfuncs.php' ) )
			{
				$cachedUserStatus[$kkey] = $row;
				return $row;
			}
		}
		else
		{
			// Global check (with illogical category check)
			if ( $row = $database->fetchObjectRow ( '
				SELECT * FROM SBookStatus 
				WHERE 
					UserID=\'' . $uid . '\'' . ( $pid ? ' AND CategoryID=\'' . $pid . '\'' : '' ) . '
				ORDER BY ID DESC, LastActivity DESC 
			', false, 'functions/userfuncs.php' ) )
			{
				$cachedUserStatus[$kkey] = $row;
				return $row;
			}
		}
	}
	$cachedUserStatus[$kkey] = false;
	return false;
}

function IsUserAFK ( $uid, $component, $cid )
{
	global $database;
	
	// Make number
	$uid = intval( $uid, 10 );
	$cid = intval( $cid, 10 );
	
	if( $uid > 0 && $cid > 0 )
	{
		switch( $component )
		{
			case 'wall':
				return false;
				break;
			case 'irc':
				$q = 'SELECT Date FROM SBookChat
					  WHERE SenderID = \'' . $uid . '\'
					  AND CategoryID = \'' . $cid . '\'
					  AND Date >= \'' . date ( 'Y-m-d H:i:s', time () - 1800 ) . '\'
					  ORDER BY Date DESC LIMIT 1';
				break;
			default:
				return true;
				break;
		}
		if( $afk = $database->fetchObjectRow ( $q, false, 'functions/userfuncs.php' ) )
		{
			//die( 'ok ' . print_r( $afk,1 ) );
			return false;
		}
	}
	return true;
}

function SaveUserVote ( $oid, $type )
{
	global $database, $webuser;
	
	if( !$oid || !$type ) return false;
	
	// TODO: Remove this, it's obselete, but check where it's added.
	return true;
	
	$votes = $database->fetchObjectRow( '
		SELECT * FROM SBookVotes 
		WHERE ObjectID = \'' . $oid . '\' AND Type = \'' . $type . '\' 
		ORDER BY ID DESC LIMIT 1
	', false, 'functions/userfuncs.php' );

	$v = new dbObject( 'SBookVotes' );
	$v->ObjectID = $oid;
	$v->Type = $type;
	if( !$votes->ID )
	{
		$v->VoteID = $webuser->ID . ',';
		$v->Save();
		return true;
	}
	else if( $v->Load( $votes->ID ) && !strstr( $votes->VoteID, $webuser->ID ) )
	{
		$v->VoteID = $votes->VoteID . $webuser->ID . ',';
		$v->Save();
		return true;
	}
	return false;
}

function ContactGroups( $ids = false )
{
	global $database;
	
	$q = '
		SELECT 
			c.*, 
			u.ID AS ContactID, 
			u.UserID, 
			u.Username 
		FROM 
			SBookContact u, 
			SBookCategoryRelation r, 
			SBookCategory c 
		WHERE 
			' . ( $ids && is_array( $ids ) ? 'u.ID IN (' . implode( ',', $ids ) . ') ' : '' ) . '
			' . ( $ids && !is_array( $ids ) ? 'u.ID = \'' . $ids . '\' ' : '' ) . '
			AND u.UserID > 0 
			AND r.ObjectID = u.UserID 
			AND r.ObjectType = "Users" 
			AND c.ID = r.CategoryID 
		ORDER BY u.ID 
	';
	
	if( $groups = $database->fetchObjectRows( $q, false, 'functions/userfuncs.php' ) )
	{
		$uid = array();
		
		foreach( $groups as $gr )
		{
			if( !$uid[$gr->ContactID] )
			{
				$uid[$gr->ContactID] = array();
			}
			if( !$uid[$gr->ContactID][$gr->ID] )
			{
				$uid[$gr->ContactID][$gr->ID] = array();
			}
			$uid[$gr->ContactID][$gr->ID] = $gr;
		}
		
		return $uid;
	}
}

function ContactRelations( $cid = false, $status = false, $list = false, $search = false )
{
	global $database, $webuser;
	
	// Make a number
	$cid = intval( $cid, 10 );
	
	// TODO remove old way
	$q = '
		SELECT 
			r.ObjectID, 
			r.IsNoticed AS SecondIsNoticed, 
			r.IsApproved AS SecondIsApproved, 
			r2.IsNoticed AS FirstIsNoticed, 
			r2.IsApproved AS FirstIsApproved,
			c2.ID, 
			c2.ImageID, 
			c2.UserID, 
			c2.Firstname, 
			c2.Middlename, 
			c2.Lastname, 
			c2.Display, 
			c2.Username,
			c2.NodeID,
			c2.NodeMainID,
			u.UniqueID,
			u.PublicKey
		FROM 
			SBookContact c, 
			SBookContactRelation r, 
			SBookContact c2
				LEFT JOIN Users u ON
					(
							c2.UserID > 0
						AND c2.UserID = u.ID
					),
			SBookContactRelation r2 
		WHERE 
			' . ( $cid ? ( '
			c.ID = \'' . $cid . '\'
			' ) : ( '
			c.UserID = \'' . $webuser->ID . '\'
			' ) ) . ' 
			AND r.ContactID = c.ID 
			AND r.ObjectType = "Users" 
			AND c2.UserID = r.ObjectID 
			AND r2.ContactID = c2.ID 
			AND r2.ObjectType = "Users"
			AND r2.ObjectID = c.UserID
			AND u.IsDeleted = "0" 
			' . ( $search ? '
			AND c2.Username LIKE "' . $search . '%"
			' : '' ) . '
		ORDER BY
			c2.Firstname ASC,
			c2.Username ASC 
	'; 
	
	$q2 = '
		SELECT 
			r.*, 
			r.ContactID AS SenderID, 
			r.ObjectID AS ReceiverID, 
			c2.ID, 
			c2.ImageID, 
			c2.UserID, 
			c2.Firstname, 
			c2.Middlename, 
			c2.Lastname, 
			c2.Display, 
			c2.Username,
			c2.NodeID,
			c2.NodeMainID,
			u.UniqueID,
			u.PublicKey
		FROM 
			SBookContact c, 
			SBookContactRelation r, 
			SBookContact c2
				LEFT JOIN Users u ON
				(
						c2.UserID > 0 
					AND c2.UserID = u.ID 
				)
		WHERE 
			(
				( ' . ( $cid ? ( '
					c.ID = \'' . $cid . '\'
					' ) : ( '
					c.ID = \'' . $webuser->ContactID . '\'
					' ) ) . ' 
					AND r.ContactID = c.ID 
					AND r.ObjectType = "SBookContact" 
					AND c2.ID = r.ObjectID
					AND u.IsDeleted = "0" 
				) 
				OR
				( ' . ( $cid ? ( '
					c.ID = \'' . $cid . '\'
					' ) : ( '
					c.ID = \'' . $webuser->ContactID . '\'
					' ) ) . ' 
					AND r.ObjectID = c.ID 
					AND r.ObjectType = "SBookContact" 
					AND c2.ID = r.ContactID
					AND u.IsDeleted = "0" 
				)
			) 
			' . ( $search ? '
			AND c2.Username LIKE "' . $search . '%" 
			' : '' ) . '
		ORDER BY
			c2.Firstname ASC,
			c2.Username ASC 
	';
	
	$rows = array();
	
	// TODO remove old way
	if( $data = $database->fetchObjectRows( $q, false, 'functions/userfuncs.php' ) )
	{
		foreach( $data as $d )
		{
			$rows[] = $d;
		}
	}
	if( $data = $database->fetchObjectRows( $q2, false, 'functions/userfuncs.php' ) )
	{
		foreach( $data as $d )
		{
			$rows[] = $d;
		}
	}
	
	if( $rows )
	{
		$uids = array(); $ids = array();
		
		foreach( $rows as $row )
		{
			if( $row->ID )
			{
				$ids[$row->ID] = $row->ID;
			}
			if( $row->UserID )
			{
				$uids[$row->UserID] = $row->UserID;
			}
		}
		
		$uonl = IsUserOnline( $uids );
		$dnam = GetUserDisplayname( $ids );
		
		$out = array();
		
		foreach( $rows as $row )
		{
			//$act = IsUserOnline( $row->UserID );
			$act = $uonl[$row->UserID];
			$nme = $dnam[$row->ID];
			
			if( isset( $row->ObjectType ) && $row->ObjectType == 'SBookContact' )
			{
				$obj = new stdClass();
				$obj->Username = $row->Username;
				$obj->Name = ( $nme ? $nme : $row->Username ); 
				$obj->ID = $row->ID;
				$obj->UniqueID = $row->UniqueID;
				$obj->PublicKey = $row->PublicKey;
				$obj->ImageID = $row->ImageID;
				$obj->UserID = $row->UserID;
				$obj->NodeID = $row->NodeID;
				$obj->NodeMainID = $row->NodeMainID;
				$obj->SenderID = $row->SenderID;
				$obj->ReceiverID = $row->ReceiverID;
				$obj->IsNoticed = $row->IsNoticed;
				$obj->IsApproved = $row->IsApproved;
				$obj->LastActivity = $act->LastActivity;
				$obj->DataSource = $act->DataSource;
				$obj->UserAgent = $act->UserAgent;
				$obj->OnlineStatus = $act;
				$obj->Status = $row->IsApproved ? 'Contact' : 'Pending';
				
				if( $status && $status != $obj->Status )
				{
					continue;
				}
				if( $list && $cid && $obj->{$list} != $cid )
				{
					continue;
				}
				
				$out[$row->ID] = $obj;
			}
			// TODO remove old way
			else if( !$list )
			{
				$obj = new stdClass();
				$obj->Username = $row->Username;
				$obj->Name = ( $nme ? $nme : $row->Username ); 
				$obj->ID = $row->ID;
				$obj->UniqueID = $row->UniqueID;
				$obj->PublicKey = $row->PublicKey;
				$obj->ImageID = $row->ImageID;
				$obj->UserID = $row->ObjectID;
				$obj->NodeID = $row->NodeID;
				$obj->NodeMainID = $row->NodeMainID;
				$obj->FirstIsNoticed = $row->FirstIsNoticed;
				$obj->FirstIsApproved = $row->FirstIsApproved;
				$obj->SecondIsNoticed = $row->SecondIsNoticed;
				$obj->SecondIsApproved = $row->SecondIsApproved;
				$obj->LastActivity = $act->LastActivity;
				$obj->DataSource = $act->DataSource;
				$obj->UserAgent = $act->UserAgent;
				$obj->OnlineStatus = $act;
				$obj->Status = $row->SecondIsApproved ? 'Contact' : 'Pending';
				
				if( $status && $status != $obj->Status )
				{
					continue;
				}
				
				$out[$row->ID] = $obj;
			}
		}
		return $out;
	}
}

/** 
 * Make human readable format
 *
 * $date     = input date
 * $mode     = mini / medium / day
 * $current  = the date right now
 * $timezone = where in the world?
**/
function TimeToHuman ( $date, $mode = false, $current = false, $timezone = false )
{
	if( !$date ) return;
	
	// Get date in unix timestamp
	$unix = strtotime( $date );
	
	// Get now! in unix timestamp
	if( $current ) $current = strtotime( $current );
	else $current = strtotime( date( 'Y-m-d H:i:s' ) );
	
	// Get out date
	list( $year, $month1, $month2, $week, $name, $day, $time, $date ) = 
		explode( ',', date( 'Y,F,j,W,D,j/n,H:i,Y-m-d H:i:s', $unix ) );
	
	// Time differing
	$difference = $current - $unix;
	
	// Is there a leap year?
	$leapyear = ((($year % 4) == 0) && ((($year % 100) != 0) || (($year %400) == 0)));
	$daydiff = $difference / 60 / 60 / 24;
	
	// Set differences in years and months etc
	$y = floor( $daydiff / ( $leapyear ? 366 : 365 ) );
	$m = floor( $daydiff / 31 );
	$w = floor( $daydiff / 7 );
	$d = floor( $daydiff );
	$i = floor( $difference / 60 );
	$h = floor( $i / 60 );
	$s = $difference;
	
	if( !$mode )
	{
		// Years ago
		if( $y >= 2 ) return ( i18n( 'i18n_' . $month1 ) . ' ' . $month2 . ', ' . $year );
		if( $y == 1 ) return ( i18n( 'i18n_' . $month1 ) . ' ' . $month2 . ', ' . $year );
		// Months ago
		if( $m >= 2 ) return ( i18n( 'i18n_' . $month1 ) . ' ' . $month2 . ' ' . i18n( 'i18n_at' ) . ' ' . $time );
		if( $m == 1 ) return ( i18n( 'i18n_' . $month1 ) . ' ' . $month2 . ' ' . i18n( 'i18n_at' ) . ' ' . $time );
		// Days ago
		if( $d >= 2 ) return ( i18n( 'i18n_' . $month1 ) . ' ' . $month2 . ' ' . i18n( 'i18n_at' ) . ' ' . $time );
		if( $d == 1 ) return ( i18n( 'i18n_Yesterday at' ) . ' ' . $time );
		// Hours ago
		if( $h >= 2 ) return ( $h . ' ' . i18n( 'i18n_hours ago' ) );
		if( $h == 1 ) return ( i18n( 'i18n_about an hour ago' ) );
		// Minutes ago
		if( $i >= 2 ) return ( $i . ' ' . i18n( 'i18n_minutes ago' ) );
		if( $i == 1 ) return ( i18n( 'i18n_about a minute ago' ) );
		// Seconds ago
		if( $s >= 9 ) return ( $s . ' ' . i18n( 'i18n_seconds ago' ) );
		if( $s >= 0 ) return ( i18n( 'i18n_a few seconds ago' ) );
	}
	else if( $mode == 'mini' )
	{
		// Years ago
		if( $y > 0 ) return ( $y . 'y' );
		// Months ago
		if( $m > 0 ) return ( $m . 'm' );
		// Days ago
		if( $d > 0 ) return ( $d . 'd' );
		// Hours ago
		if( $h > 0 ) return ( $h . 'h' );
		// Minutes ago
		if( $i > 0 ) return ( $i . 'm' );
		// Seconds ago
		if( $s > 0 ) return ( $s . 's' );
	}
	else if( $mode == 'medium' )
	{
		// Years ago
		if( $y > 0 ) return ( date( 'm/d/y H:i', $unix ) );
		// Months ago
		if( $m > 0 ) return ( $day . ', ' . $time );
		// Days ago
		if( $d > 0 ) return ( $day . ', ' . $time );
		// Hours ago
		if( $h > 0 ) return ( $time );
		// Minutes ago
		if( $i > 0 ) return ( $time );
		// Seconds ago
		if( $s > 0 ) return ( $time );
	}
	else if( $mode == 'day' )
	{
		// Years ago
		if( $y > 0 ) return ( date( 'm/d/y', $unix ) );
		// Months ago
		if( $m > 0 ) return ( i18n( 'i18n_' . $month1 ) . ' ' . $month2 );
		// Weeks ago
		if( $w > 0 ) return ( $name );
		// Days ago
		if( $d > 0 ) return ( $name );
		// Hours ago
		if( $h > 0 ) return ( $name );
		// Minutes ago
		if( $i > 0 ) return ( $name );
		// Seconds ago
		if( $s > 0 ) return ( $name );
	}
}

function LogUserActivity ( $uid = false, $subject, $message = false, $type, $table, $rowid )
{
	global $webuser;
	
	if ( $subject && $type && $table && $rowid )
	{
		$l = new dbObject( 'Log' );
		$l->UserID = $webuser->ID;
		$l->Type = $type;
		$l->Subject = $subject;
		$l->Message = $message;
		$l->ObjectType = $table;
		$l->ObjectID = $rowid;
		$l->ConnectedType = 'Users';
		$l->ConnectedID = ( $uid ? $uid : $webuser->ID );
		$l->DateCreated = date( 'Y-m-d H:i:s' );
		$l->Save();
		
		if ( $l->ID > 0 )
		{
			return true;
		}
	}
	
	return false;
}

function GetUserActivity ( $uid = false, $subject = false, $type = false, $table, $rowid, $arr = false )
{
	global $database, $webuser;
	
	if ( $table && $rowid && ( $rows = $database->fetchObjectRows ( $q = '
		SELECT * FROM Log 
		WHERE ObjectType = \'' . $table . '\' 
		AND ObjectID IN (' . $rowid . ') 
		AND ConnectedType = "Users" 
		AND ConnectedID = UserID
		' . ( $uid != '*' ? '
		AND UserID = \'' . ( $uid ? $uid : $webuser->ID ) . '\'
		' : '' ) . ( $subject ? '
		AND Subject = \'' . $subject . '\' 
		' : '' ) . ( $type ? '
		AND Type = \'' . $type . '\'
		' : '' ) . '
		ORDER BY DateCreated DESC 
	', false, 'functions/userfuncs.php' ) ) )
	{
		$new = array(); $uids = array();
		
		if ( $arr && is_array( $arr ) )
		{
			$new = $arr;
		}
		
		foreach( $rows as $row )
		{
			$uids[$row->UserID] = $row->UserID;
			$uids[$row->ConnectedID] = $row->ConnectedID;
		}
		
		$usrs = GetUserDisplayname( false, $uids );
		
		foreach( $rows as $row )
		{
			$row->UserName = ( isset( $usrs[$row->UserID] ) ? $usrs[$row->UserID] : $row->UserID );
			$row->ConnectedName = ( isset( $usrs[$row->ConnectedID] ) ? $usrs[$row->ConnectedID] : $row->ConnectedID );
			
			$new[strtotime($row->DateCreated)] = $row;
		}
		
		ksort( $new );
		
		return $new;
	}
	
	return false;
}

function LogUser2 ( $status, $component, $parent, $lastactivity = false )
{
	global $database, $webuser;
	
	if( !$status || !$component || !$parent ) return false;
	
	if( $status == 'login' && $rows = $database->fetchObjectRows ( '
		SELECT * FROM SBookStatus
		WHERE UserID = \'' . $webuser->ID . '\'
		ORDER BY ID DESC 
	', false, 'functions/userfuncs.php' ) )
	{
		foreach( $rows as $row )
		{
			// Delete old log
			$l = new dbObject( 'SBookStatus' );
			if( $l->Load( $row->ID ) )
			{
				$l->Delete();
			}
		}
	}
	
	$l = new dbObject( 'SBookStatus' );
	$l->UserID = $webuser->ID;
	$l->Status = $status;
	$l->Component = $component;
	$l->Module = $parent->module;
	$l->CategoryID = $parent->folder->CategoryID;
	if( $l->Load() )
	{
		$l->InActive = '0';
		$l->LastActivity = ( $lastactivity ? date( 'Y-m-d H:i:s', strtotime( $lastactivity ) ) : date( 'Y-m-d H:i:s' ) );
		$l->DateCreated = date( 'Y-m-d H:i:s' );
		$l->Save();
	}
}


// Component, Action, Type, SenderID, ReceiverID, CategoryID, DataSource
function LogStats( $component, $action, $type, $sid = false, $rid = false, $catid = false, $source = 'site' )
{
	global $webuser;
	
	if( $component && $type && $action )
	{
		$l = new dbObject( 'SBookStats' );
		
		$l->SenderID   = ( $sid ? $sid : $webuser->ContactID );
		$l->ReceiverID = ( $rid ? $rid : 0 );
		$l->CategoryID = ( $catid ? $catid : 0 );
		$l->Component  = strtolower( $component );
		$l->Type       = strtolower( $type );
		$l->Action     = strtolower( $action );
		$l->DataSource = strtolower( $source );
		
		if( !$l->Load() )
		{
			$l->DateCreated = date( 'Y-m-d H:i:s' );
		}
		
		$l->DateModified = date( 'Y-m-d H:i:s' );
		$l->Counter      = ( $l->Counter + 1 );
		$l->Save();
		
		return $l;
	}
	
	return false;
}



// Load user info into cache
// cid = contact ID, pid = UserID
function CacheUser ( $cid = false, $pid = false )
{
	global $database, $cachedUsers;
	//if( $cid ) die( $cid . ' --' );
	// If ContactID is defined and it's an array
	if ( $cid && is_array( $cid ) )
	{
		// For return
		$users = array(); $ids = array();
		
		foreach ( $cid as $id )
		{
			if ( !isset( $cachedUsers[$id] ) )
			{
				$ids[] = $id;
			}
			else
			{
				$users[$id] = $cachedUsers[$id];
			}
		}
		
		// Search by ContactID?
		$q = '
			SELECT 
				c.*, u.UniqueID, u.PublicKey 
			FROM 
				SBookContact c, 
				Users u 
			WHERE 
					c.ID IN ( ' . ( $ids && is_array( $ids ) ? implode( ',', $ids ) : '' ) . ' ) 
				AND u.ID = c.UserID 
				AND u.IsDeleted = "0" 
			ORDER 
				BY c.ID ASC 
		';
		//die( $q );
		if ( $ids && is_array( $ids ) && ( $rows = $database->fetchObjectRows ( $q, false, 'functions/userfuncs.php' ) ) )
		{
			
			foreach( $rows as $us )
			{
				$us->ContactID = $us->ID;
				$us->Image = $us->ImageID;
				
				$users[$us->ID] = new stdclass ();
				$users[$us->ID]->ContactInfo = $us;
				// Put in cache
				//$users[$us->ID]->ContactInfo->ID = $us->UserID;
				$cachedUsers[$us->ID] = $users[$us->ID];
			}
		}
		
		return $users;
	}
	// If UserID is defined and it's an array
	else if ( $pid && is_array( $pid ) )
	{
		// For return
		$users = array(); $ids = array();
		
		foreach ( $pid as $id )
		{
			if ( !isset( $cachedUsers['pid'.$id] ) )
			{
				$ids[] = $id;
			}
			else
			{
				$users[$id] = $cachedUsers['pid'.$id];
			}
		}
		
		// Search by UserID?
		$q = '
			SELECT 
				c.*, u.UniqueID, u.PublicKey 
			FROM 
				SBookContact c, 
				Users u 
			WHERE 
					c.UserID IN ( ' . ( $ids && is_array( $ids ) ? implode( ',', $ids ) : '' ) . ' ) 
				AND u.ID = c.UserID 
				AND u.IsDeleted = "0" 
			ORDER 
				BY c.ID ASC 
		';
		
		if ( $ids && is_array( $ids ) && ( $rows = $database->fetchObjectRows ( $q, false, 'functions/userfuncs.php' ) ) )
		{
			
			foreach( $rows as $us )
			{
				$us->ContactID = $us->ID;
				$us->Image = $us->ImageID;
				
				$users[$us->ID] = new stdclass ();
				$users[$us->ID]->ContactInfo = $us;
				// Put in cache
				$users[$us->ID]->ContactInfo->ID = $us->UserID;
				$cachedUsers['pid'.$us->UserID] = $users[$us->ID];
			}
		}
		
		return $users;
	}
	// If ContactID is defined and it's not an array
	else if ( $cid && !is_array( $cid ) && !isset( $cachedUsers[$cid] ) )
	{
		//$q = 'SELECT * FROM SBookContact WHERE ID=\'' . $cid . '\'';
		
		$q = '
			SELECT 
				c.*, u.UniqueID, u.PublicKey 
			FROM 
				SBookContact c, 
				Users u 
			WHERE 
					c.ID = \'' . $cid . '\' 
				AND u.ID = c.UserID 
				AND u.IsDeleted = "0" 
			ORDER 
				BY c.ID ASC 
		';
		
		if( $us = $database->fetchObjectRow( $q, false, 'functions/userfuncs.php' ) )
		{
			$us->ContactID = $us->ID;
			$us->Image = $us->ImageID;
			
			$cachedUsers[$us->ID] = new stdclass ();
			$cachedUsers[$us->ID]->ContactInfo = $us;
			
			//$cachedUsers[$us->ID]->ContactInfo->ID = $us->UserID;
		}
		//else $cachedUsers[$cid] = false;
	}
	// If UserID is defined and it's not an array
	else if ( $pid && !is_array( $pid ) && !isset( $cachedUsers['pid'.$pid] ) )
	{
		//$q = 'SELECT * FROM SBookContact WHERE UserID=\'' . $pid . '\'';
		
		$q = '
			SELECT 
				c.*, u.UniqueID, u.PublicKey 
			FROM 
				SBookContact c, 
				Users u 
			WHERE 
					c.UserID = \'' . $pid . '\' 
				AND u.ID = c.UserID 
				AND u.IsDeleted = "0" 
			ORDER 
				BY c.ID ASC 
		';
		
		if( $us = $database->fetchObjectRow( $q, false, 'functions/userfuncs.php' ) )
		{
			$us->ContactID = $us->ID;
			$us->ID = $us->UserID;
			$us->Image = $us->ImageID;
			
			$cachedUsers['pid'.$us->UserID] = new stdclass ();
			$cachedUsers['pid'.$us->UserID]->ContactInfo = $us;
		}
		//else $cachedUsers['pid'.$pid] = false;
	}
	
	// Return cache
	if ( isset( $cachedUsers[($cid?$cid:('pid'.$pid))] ) )
	{
		return $cachedUsers[($cid?$cid:('pid'.$pid))];
	}
	
	return false;
	
	
	// TODO: Delete this code ---
	/*// Check for more users in array
	if ( ( $cid && is_array( $cid ) ) || ( !$cid && $pid && is_array( $pid ) ) )
	{
		// For return
		$users = array();
		
		// CID could be false!
		if( $cid )
		{
			$ids = $cid;
			$cid = array();
			foreach( $ids as $id )
			{
				if( !isset( $cachedUsers[$id] ) )
				{
					$cid[] = $id;
				}
				else
				{
					$users[$id] = $cachedUsers[$id];
				}
			}
		}
		
		if( !$cid && !$pid && !$users )
		{
			return false;
		}
		
		// Search by contact ID or UserID?
		$q = ( $cid ? '
			SELECT 
				* 
			FROM 
				SBookContact
			WHERE 
				ID IN ( ' . ( $cid && is_array( $cid ) ? implode( ',', $cid ) : '' ) . ' ) 
			ORDER 
				BY ID ASC 
		' : '
			SELECT 
				* 
			FROM 
				SBookContact
			WHERE 
				UserID IN ( ' . ( $pid && is_array( $pid ) ? implode( ',', $pid ) : '' ) . ' ) 
			ORDER 
				BY ID ASC 
		' );
		
		if ( ( $cid || $pid ) && ( $rows = $database->fetchObjectRows ( $q, false, 'functions/userfuncs.php' ) ) )
		{
			
			foreach( $rows as $us )
			{
				$us->ContactID = $us->ID;
				
				$users[$us->ID] = new stdclass ();
				$users[$us->ID]->ContactInfo = $us;
				// Put in cache
				$cachedUsers[$us->ID] = $users[$us->ID];
				
				$users[$us->ID]->ContactInfo->ID = $us->UserID;
				
				$cachedUsers['pid'.$us->UserID] = $users[$us->ID];
			}
		}
		return $users;
	}
	// Check for a single user by contact id
	if( $cid && !isset( $cachedUsers[$cid] ) )
	{
		$q = 'SELECT * FROM SBookContact WHERE ID=\'' . $cid . '\'';
		if( $us = $database->fetchObjectRow( $q, false, 'functions/userfuncs.php' ) )
		{
			$us->ContactID = $us->ID;
			
			$cachedUsers[$us->ID] = new stdclass ();
			$cachedUsers[$us->ID]->ContactInfo = $us;
		}
		else $cachedUsers[$cid] = false;
	}
	// Check for single user by userid
	else if( $pid && !isset( $cachedUsers['pid'.$pid] ) )
	{
		$q = 'SELECT * FROM SBookContact WHERE UserID=\'' . $pid . '\'';
		if( $us = $database->fetchObjectRow( $q, false, 'functions/userfuncs.php' ) )
		{
			$us->ContactID = $us->ID;
			$us->ID = $us->UserID;
			
			$cachedUsers['pid'.$us->UserID] = new stdclass ();
			$cachedUsers['pid'.$us->UserID]->ContactInfo = $us;
		}
		else $cachedUsers['pid'.$pid] = false;
	}
	// Return cache
	return $cachedUsers[($cid?$cid:('pid'.$pid))];*/
}

// Get a proper user name
function GetUserDisplayname ( $cid = false, $uid = false )
{
	// If cid or uid is array
	if ( ( $cid && is_array( $cid ) || $uid && is_array( $uid ) ) && ( $usr = CacheUser( $cid, $uid ) ) )
	{
		//die( print_r( $usr,1 ) . ' -- ' . print_r( $cid,1 ) );
		
		$users = array();
		foreach ( $usr as $k=>$u )
		{
			$dn = $u->ContactInfo->Username;
			switch ( $u->ContactInfo->Display )
			{
				case 1:
					$dn = $u->ContactInfo->Firstname . ' ' . 
						  $u->ContactInfo->Middlename . ' ' .
						  $u->ContactInfo->Lastname;
					break;
				case 2:
					$dn = $u->ContactInfo->Firstname . ' ' . 
						  $u->ContactInfo->Lastname;
					break;
				case 3:
					$dn = $u->ContactInfo->Lastname . ' ' . 
						  $u->ContactInfo->Firstname;
					break;
			}
			if ( is_string( $dn ) )
			{
				//$users[$u->ContactInfo->ID] = trim( $dn );
				$users[$k] = trim( $dn );
			}
		}
		return $users;
	}
	// If cid or uid is single
	if ( ( $cid || $uid ) && ( $u = CacheUser( $cid, $uid ) ) )
	{
		$dn = $u->ContactInfo->Username;
		switch ( $u->ContactInfo->Display )
		{
			case 1:
				$dn = $u->ContactInfo->Firstname . ' ' . 
				      $u->ContactInfo->Middlename . ' ' .
				      $u->ContactInfo->Lastname;
				break;
			case 2:
				$dn = $u->ContactInfo->Firstname . ' ' . 
				      $u->ContactInfo->Lastname;
				break;
			case 3:
				$dn = $u->ContactInfo->Lastname . ' ' . 
				      $u->ContactInfo->Firstname;
				break;
		}
		return trim ( $dn );
	}
	return false;
}

function getUserGroupsID ( $userid, $system = null ) 
{
	global $database;
	
	if ( $userid > 0 )
	{
		if( $rows = $database->fetchObjectRows ( '
            SELECT 
                c.ID 
            FROM 
                SBookCategoryRelation r, 
                SBookCategory c 
            WHERE 
                	r.ObjectType = "Users" 
                AND r.ObjectID = \'' . $userid . '\' 
                AND c.ID = r.CategoryID
				AND c.Name != "Wall" 
				' . ( $system != null ? '
				AND c.IsSystem = \'' . $system . '\' 
				' : '' ) . '
            ORDER BY 
                c.ID ASC 
        ', false, 'functions/userfuncs.php' ) )
		{
			$out = array();
			
			foreach ( $rows as $row )
			{
				$out[] = $row->ID;
			}
			
			return $out;
		}
	}
	
	return false;
}

function getUserContactsID ( $cid, $userid = false )
{
	global $database, $cachedUserContactIDs, $cachedUsers;

	// Cache
	$key = $userid ? $cid . $userid : $cid;
	if( isset( $cachedUserContactIDs[$key] ) )
		return $cachedUserContactIDs[$key];

	if ( $cid > 0 )
	{
		$rows = array();
		
		// TODO remove old way
		$q = '
            SELECT 
                c.* 
            FROM
				SBookContact c2, 
                SBookContactRelation r, 
                SBookContact c 
            WHERE
					c2.ID = \'' . $cid . '\' 
				AND r.ObjectID = c2.UserID 
                AND r.ObjectType = "Users" 
                AND r.IsApproved = "1" 
                AND c.ID = r.ContactID 
            ORDER BY 
                c.ID ASC 
        ';
		
		$q2 = '
			SELECT 
				c.* 
			FROM 
				SBookContactRelation r, 
				SBookContact c 
			WHERE 
				( ( r.ContactID = \'' . $cid . '\' 
				AND r.ObjectType = "SBookContact"
				AND r.IsApproved = "1" 
				AND c.ID = r.ObjectID ) 
				OR ( r.ObjectID = \'' . $cid . '\' 
				AND r.ObjectType = "SBookContact"
				AND IsApproved = "1" 
				AND c.ID = r.ContactID ) ) 
			ORDER BY 
				c.ID ASC 
		';

		if( $data = $database->fetchObjectRows( $q, false, 'functions/userfuncs.php' ) )
		{
			foreach( $data as $d )
			{
				// Cache
				if( !isset( $cachedUsers[$d->ID] ) )
				{
					$cachedUsers[$d->ID] = new stdClass();
					$cachedUsers[$d->ID]->ContactInfo = $d;
				}
				
				$rows[] = $d;
			}
		}
		if( $data = $database->fetchObjectRows( $q2, false, 'functions/userfuncs.php' ) )
		{
			foreach( $data as $d )
			{
				// Cache
				if( !isset( $cachedUsers[$d->ID] ) )
				{
					$cachedUsers[$d->ID] = new stdClass();
					$cachedUsers[$d->ID]->ContactInfo = $d;
				}
				$rows[] = $d;
			}
		}
		
		if( $rows )
		{
			$sc = $database->fetchObjectRow( '
				SELECT
					*
				FROM
					SBookContact
				WHERE
					ID = \'' . $cid . '\'
				ORDER BY
					ID DESC
			', false, 'functions/userfuncs.php' );
			
			//$sc = new dbObject( 'SBookContact' );
			//$sc->Load( $cid );
			
			$field = $userid ? 'UserID' : 'ID';
			
			$out = array( $sc->{$field} );
			
			foreach ( $rows as $row )
			{
				$out[] = $row->{$field};
			}
			
			$cachedUserContactIDs[$key] = $out;
			
			return $out;
		}
	}
	
	return false;
}

function getWallID ()
{
	global $database;
	
	if( $row = $database->fetchObjectRow ( '
		SELECT 
			c.* 
		FROM 
			SBookCategory c, 
			SBookCategory c2 
		WHERE 
				c.Type = "SubGroup" 
			AND c.Name = "Wall" 
			AND c2.ID = c.CategoryID 
			AND c2.Type = "Group"
			AND c2.Name = "Profile" 
		ORDER BY 
			c.ID ASC 
	' ) )
	{
		return $row->ID;
	}
	
	return false;
}

// TODO: Find out if we use it, if not delete it!
function SBookContact( $userid )
{
	global $database;
	
	if( !$userid ) return false;

	if( $row = CacheUser( false, $userid ) )
	{
		$row->ContactInfo->AuthKey = '0';
		return $row->ContactInfo;
	}
	return false;
}

// Does the same as SBookContact (but doesn't care about AuthKey)
function ContactID( $uid )
{
	if( $row = CacheUser( false, $uid ) )
	{
		return $row->ContactInfo->ID;
	}
	return false;
}

function Initials( $name )
{
	if( !$name ) return false;
	
	$name = explode( ' ', $name );
	
	foreach( $name as $k=>$n )
	{
		$name[$k] = strtoupper( substr( $n, 0, 1 ) );
	}
	
	return ( implode( '.', $name ) . '.' );
}

// cache supported user data function
function UserData( $uid )
{
	global $database, $cachedUserData;
	
	if( !$uid ) return false;
	
	// Get a number of IDs and cache (return the array)
	if( is_array( $uid ) )
	{
		if( $rows = $database->fetchObjectRows( 'SELECT * FROM `SBookContact` WHERE UserID IN ( ' . implode( ',', $uid ) . ' )', false, 'functions/userfuncs.php' ) )
		{
			$out = array();
			foreach( $rows as $row )
			{
				$cachedUserData[$row->UserID] = json_obj_decode( $row->Data );
				$out[] = $cachedUserData[$row->UserID];
			}
			return $out;
		}
	}
	
	// Found in cache
	if( isset( $cachedUserData[$uid] ) )
		return $cachedUserData[$uid];
		
	// Chat Settings
	if( $c = CacheUser( false, $uid ) )
	{
		$c = $c->ContactInfo;
		if( is_string( $c->Data ) )
		{
			$c->Data = json_obj_decode( $c->Data );
			$cachedUserData[$uid] = $c->Data;
			return $c->Data;
		}
	}
	
	return false;
}

function UniqueName ( $name )
{
	global $database;
	
	if( $database->fetchObjectRow ( '
		SELECT ID FROM SBookContact
		WHERE Username != "" AND Username = \'' . $name . '\'
		ORDER BY ID DESC
	', false, 'functions/userfuncs.php' ) )
	{
		for ( $a = 1; $a < 100; $a++ )
		{
			if( !$database->fetchObjectRow ( '
				SELECT ID FROM SBookContact
				WHERE Username != "" AND Username = \'' . ( $name.'.'.$a ) . '\'
				ORDER BY ID DESC
			', false, 'functions/userfuncs.php' ) )
			{
				return ( $name.'.'.$a );
			}
		}
	}
	
	return $name;
}

function UniqueID ( $username = false )
{
	global $database, $webuser;
	
	if ( $usr = $database->fetchObjectRow( '
		SELECT 
			UniqueID 
		FROM 
			`Users` 
		WHERE 
				IsDeleted = "0" 
			AND ' . ( $username ? ( 'Username = \'' . trim( $username ) . '\' ' ) : ( 'ID = \'' . $webuser->ID . '\' ' ) ) . '
		ORDER BY 
			ID DESC 
	' ) )
	{
		// If user doesn't have a uniqueid make one
		if( !$usr->UniqueID )
		{
			$usu = new dbObject( 'Users' );
			$usu->ID = $usr->ID;
			if( $usu->Load() )
			{
				$usu->UniqueID = UniqueKey( $usr->Username );
				$usu->Save();
			}
		}
		
		return ( $usr->UniqueID ? $usr->UniqueID : $usu->UniqueID );
	}
	
	return false;
}

function CheckNodeUser ( $cid )
{
	global $database;
	
	// Check cache
	if( $cu = CacheUser( $cid ) )
	{
		if( $cu->NodeID > 0 && $cu->NodeMainID > 0 )
			return $cu->ContactInfo;
	}
	
	return false;
}

if( !function_exists( 'ReadUserAgent' ) )
{
	function ReadUserAgent( $ua )
	{
		$str = '';
		
		if( $ua )
		{
			include_once ( 'subether/thirdparty/php/UserAgentParser.php' );
			
			$str .= $ua;
			
			if( function_exists( 'parse_user_agent' ) && ( $array = parse_user_agent( $ua ) ) )
			{
				$str .= ( ' | ' . $array['browser'] . ' ' . $array['version'] . ' on ' . $array['platform'] );
			}
		}
		
		return $str;
	}
}

?>
