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

function dotTrim ( $str, $max, $ext = false )
{
	$str = trim ( $str );
	if ( strlen ( $str ) > $max )
	{
		$parts = explode( '.', $str );
		//return mb_substr ( $str, 0, $max - 2, 'UTF-8' ) . '..' . ( $ext && strstr( $str, '.' ) ? ' ' . end( $parts ) : '.' );
		return substr ( $str, 0, $max - 2 ) . '..' . ( $ext && strstr( $str, '.' ) ? ' ' . end( $parts ) : '.' );
	}
	return $str;
}

function strLimit( $str, $limit )
{
	//return mb_substr ( trim( $str ), 0, $limit, 'UTF-8' );
	return substr ( trim( $str ), 0, $limit );
}
/*
function microtime_float()
{
    list( $usec, $sec ) = explode( " ", microtime() );
    return ( (float)$usec + (float)$sec );
}
*/
function str_mark( $str, $param = false )
{
	if( !$str ) return false;
	if( $param ) 
	{
		return str_ireplace( $param, '<em>' . $param . '</em>', $str );
	}
	else return $str;
}

function search_options( $str, $param = false )
{
	if( !$str ) return false;
	if( $param )
	{
		$out = '';
		$array = explode( ' ', mb_strtolower( $str ) );
		$index = array_flip( preg_split( '/\P{L}+/u', mb_strtolower( $param ), -1, PREG_SPLIT_NO_EMPTY ) );
		foreach( $array as $k=>$arr ) 
		{
			if( isset( $index[$arr] ) && $arr != ' ' ) 
			{
				$out = $param . ' ' . $array[$k+1] . ' ';
			}
		}
		return $out;
	}
	else return $str;
}

function in_object ( $needle, $haystack )
{
	if( $needle && $haystack )
	{
		foreach( $haystack as $object )
		{
			if( $object && is_object( $object ) )
			{
				foreach( $object as $obj )
				{
					if( $obj && is_object( $obj ) )
					{
						foreach( $obj as $o )
						{
							if( $o == $needle ) return $obj;
						}
					}
					else if( $obj == $needle ) return $object;
				}
			}
			else if( $object == $needle ) return $haystack;
		}
		return false;
	}
	else return false;
}

function getCategoryID( $name, $userid = false )
{
	global $database;
	
	if( !$name ) return false;
	
	/*if( $userid )
	{
		$q = '
			SELECT 
				c.*,r.*, r.CategoryID, r.ID as ID, c2.ID as MainID, c2.Type as MainType, c2.Name as MainName  
			FROM 
				SBookCategory c, 
				SBookCategory c2, 
				SBookCategoryRelation r, 
				Users u 
			WHERE
					u.ID = \'' . $userid . '\' 
				AND r.ObjectType = "Users" 
				AND r.ObjectID = u.ID 
				AND c.ID = r.CategoryID 
				AND c2.ID = c.CategoryID 
			ORDER BY 
				r.SortOrder ASC, 
				c.ID ASC 
		';
	}
	else
	{*/
		$q = '
			SELECT 
				c.*, c.ID AS CategoryID, c2.ID as MainID, c2.Type as MainType, c2.Name as MainName 
			FROM 
				SBookCategory c,
				SBookCategory c2 
			WHERE 
					c.Type = "SubGroup" 
				AND c2.ID = c.CategoryID 
			ORDER BY 
				c.ID ASC 
		';
	/*}*/
	
	if( $cats = $database->fetchObjectRows ( $q, false, 'include/functions.php' ) )
	{
		$cts = array(); $rels = array(); $subs = array();
		
		foreach( $cats as $c )
		{
			if( $c->ParentID > 0 )
			{
				if( !isset( $subs[$c->ParentID] ) )
				{
					$subs[$c->ParentID] = array();
				}
				
				$subs[$c->ParentID][] = $c;
			}
			
			$cts[$c->ID] = $c->ID;
		}
		
		// TODO: Fix this ....., can't use this code
		//$m = getSBookGroupMembers( $cts );
		
		if( $userid && $cts && ( $rows = $database->fetchObjectRows ( '
			SELECT
				*
			FROM
				SBookCategoryRelation
			WHERE
					ObjectID = \'' . $userid . '\'
				AND CategoryID IN ( ' . implode( ',', $cts ) . ' ) 
				AND ObjectType = "Users" 
				GROUP BY 
					CategoryID 
				ORDER BY 
					ID DESC 
		', false, 'include/functions.php' ) ) )
		{
			foreach( $rows as $row )
			{
				$rels[$row->CategoryID] = $row;
			}
		}
		
		foreach( $cats as $c )
		{
			if( isset( $subs[$c->ID] ) )
			{
				$c->SubGroups = $subs[$c->ID];
			}
			if( isset( $rels[$c->ID] ) )
			{
				foreach( $rels[$c->ID] as $k=>$r )
				{
					$c->$k = $r;
				}
			}
			// TODO: Fix this code no need to list the whole freecking database to count members on a group.
			//if( isset( $m[$c->CategoryID] ) )
			//{
			//	$c->Users = count( $m[$c->CategoryID] );
			//}
			if( trim( strtolower( str_replace( ' ', '_', $c->Name ) ) ) == trim( strtolower( str_replace( ' ', '_', $name ) ) ) )
			{
				return $c;
			}
			else if( $c->CategoryID == $name )
			{
				return $c;
			}
		}
		//diebug( print_r( $cats,1 ) . ' .. ' . $q );
	}
	return false;
}

function getUserList( $key )
{
	global $database;
	
	if( !$key ) return false;
	$allowed = array( 'ID'=>'ID', 'Username'=>'Username', 'Name'=>'Name' );
	$out = array();
	if( $u = $database->fetchObjectRows( 'SELECT * FROM Users ORDER BY ID ASC', false, 'include/functions.php' ) )
	{
	//$u = new dbObject ( 'Users' );
	//if( $u = $u->find() )
	//{
		foreach( $u as $u )
		{
			$out[] = $u->$allowed[$key];
		}
	}
	else return false;
	return $out;
}

function getContacts( $type, $userid, $param = false, $search = false )
{
	global $database;
	
	if( !$type && !$userid ) return false;
	
	$q = '
		SELECT 
			c.ImageID,
			c.ID, 
			c.UserID,
			c.Username,
			c.Firstname,
			c.Middlename,
			c.Lastname,
			c.Display,
			r.IsApproved,
			r.IsNoticed   
		FROM 
			SBookContact c, 
			SBookContactRelation r, 
			Users u 
		WHERE
				u.ID = \'' . $userid . '\' 
			AND r.ObjectType = \'' . $type . '\' 
			AND r.ObjectID = u.ID 
			AND c.ID = r.ContactID 
			' . ( $param ? 'AND r.IsApproved = "0"' : 'AND r.IsApproved = "1"' ) . '
			' . ( $search ? 'AND c.Username LIKE "' . $search . '%" ' : '' ) . '
		ORDER BY 
			r.SortOrder ASC, 
			c.ID ASC 
	';

	if( $rows = $database->fetchObjectRows ( $q, false, 'include/functions.php' ) )
	{
		foreach( $rows as $k=>$v )
		{
			/*if( !ContactRelation( $v->UserID, $userid, true ) )
			{
				$rows[$k]->IsApproved = '0';
			}*/
			
			if( $v->Firstname ) $first = $v->Firstname . ' '; else $first = '';
			if( $v->Middlename ) $middle = $v->Middlename . ' '; else $middle = '';
			if( $v->Lastname ) $last = $v->Lastname . ' '; else $last = '';
			
			if( $v->Display == 1 )
			{
				$rows[$k]->DisplayName = trim( $first . $middle . $last );
			}
			else if( $v->Display == 2 )
			{
				$rows[$k]->DisplayName = trim( $first . $last );
			}
			else if( $v->Display == 3 )
			{
				$rows[$k]->DisplayName = trim( $last . $first );
			}
			else $rows[$k]->DisplayName = $v->Username;
		}
		return $rows;
	}
	return false;
}

function getCategories( $relation, $type = false, $cid = false, $user = false, $name = false )
{
	global $database, $webuser;
	
	if( !$relation ) return false;
	if( !$user ) $user =& $webuser;
	
	$q = '
		SELECT 
			c.*, 
			r.ObjectID as UserID,
			r.ID as RelationID 
		FROM 
			SBookCategory c, 
			SBookCategoryRelation r 
		WHERE
			r.ObjectType = \'' . $relation . '\' AND 
			r.ObjectID = \'' . $user->ID . '\' AND 
			' . ( $type ? 'c.Type = \'' . $type . '\' AND ' : '' ) . '
			' . ( 
					( $cid && is_array( $cid ) ) ? 
					( 'c.CategoryID IN ( ' . implode( ', ', $cid ) . ' ) AND ' ) :
					( $cid ? ( 'c.CategoryID = \'' . $cid . '\' AND ' ) : '' ) 
				) . '
			' . ( $name ? 'c.Name = \'' . $name . '\' AND ' : '' ) . '
			c.ID = r.CategoryID 
		ORDER BY 
			r.SortOrder ASC, 
			c.ID ASC 
	';
	
	// Mass support (many category ids)
	if( $cid && is_array( $cid ) )
	{
		if( $gc = $database->fetchObjectRows( $q, false, 'include/functions.php' ) )
		{
			$out = array();
			foreach( $gc as $g )
			{
				if( !isset( $out[$g->CategoryID] ) )
					$out[$g->CategoryID] = array();
				$out[$g->CategoryID][] = $g;
			}
			return $out;
		}
		return false;
	}
	else if( $name == false && $gc = $database->fetchObjectRows( $q, false, 'include/functions.php' ) )
	{
		/*foreach( $gc as $c )
		{
			$mr = new dbObject( 'SBookMessageRelation' );
			$mr->CategoryType = 'Users';
			$mr->CategoryID = $c->RelationID;
			if( $mr = $mr->Find() )
			{
				$i = 1;
				foreach( $mr as $r )
				{
					if( !$r->IsRead ) $c->IsRead += $i;
					if( !$r->IsNoticed ) $c->IsNoticed += $i;
				}
			}
		}*/
		return $gc;
	}
	else return $database->fetchObjectRow( $q );
}

function getMediaFolders( $relation, $tags = false, $param = false, $cid = false, $user = false )
{
	global $database, $webuser;
	
	if( !$relation ) return false;
	//if( !$user ) $user =& $webuser;
	
	$q = '
		SELECT 
			f.*  
		FROM  
			SBookFiles s, 
			Folder f, 
			SBookMediaRelation r 
		WHERE
			r.MediaType = \'' . $relation . '\' AND 
			' . ( $user ? 'r.UserID = \'' . ( $user->ID ? $user->ID : $user ) . '\' AND ' : '' ) . ' 
			' . ( $tags ? 's.Filename = \'' . $tags . '\' AND ' : '' ) . '
			' . ( $cid && $tags ? 'r.CategoryID = \'' . $cid . '\' AND ' : '' ) . '
			' . ( $cid && !$tags ? 'r.MediaID = \'' . $cid . '\' AND ' : '' ) . '
			s.FileFolder = r.MediaID AND 
			f.ID = s.MediaID 
		ORDER BY 
			r.SortOrder ASC, 
			f.ID ASC 
	';
	//die( $q . ' ..' );
	if( $param )
	{
		if( $c = $database->fetchObjectRow ( $q, false, 'include/functions.php' ) )
		{
			return $c;
		}
	}
	else
	{
		if( $c = $database->fetchObjectRows ( $q, false, 'include/functions.php' ) )
		{
			return $c;
		}
	}
}

function checkDefaultCategories ()
{
	$categories = array (
		1 => 'Profile',
		2 => 'Favorites',
		3 => 'Groups',
		4 => 'Contacts',
		5 => 'Pages',
		6 => 'Bank'
	);
	
	$subcategories = array (
		1 => array (
			1 => 'Wall',
			2 => 'About',
			3 => 'Contacts',
			4 => 'Events',
			6 => 'Groups',
			7 => 'Library',
			8 => 'Subscriptions'
		),
		2 => array (
			1 => 'Wall',
			2 => 'Messages',
			3 => 'Events',
			4 => 'Library',
			5 => 'Browse',
			6 => 'Maps',
			7 => 'Bookmarks',
			8 => 'Orders',
			9 => 'Crowdfunding'
		)
	);
	
	if( $categories )
	{
		global $webuser;
		
		foreach ( $categories as $key=>$cat )
		{
			// New method
			$cs = new dbObject( 'SBookCategory' );
			$cs->CategoryID = '0';
			$cs->Type = 'Group';
			$cs->Name = $cat;
			$cs->IsSystem = 1;
			if ( !$cs->Load() )
			{
				$cs->Owner = '0';
				$cs->Save();
			}
			
			$cr = new dbObject( 'SBookCategoryRelation' );
			$cr->CategoryID = $cs->ID;
			$cr->ObjectType = 'Users';
			$cr->ObjectID = $webuser->ID;
			if ( !$cr->Load() && $webuser->ID )
			{
				$cr->Save();
			}
			
			$ca = new dbObject( 'SBookCategoryAccess' );
			$ca->CategoryID = $cs->ID;
			$ca->UserID = $webuser->ContactID;
			$ca->ContactID = $webuser->ContactID;
			if( !$ca->Load() && $webuser->ContactID )
			{
				$ca->Read = 1;
				$ca->Write = 1;
				$ca->Delete = 1;
				$ca->Admin = 1;
				$ca->Owner = 1;
				$ca->Save();
			}
			
			/*// Old system - TODO: Remove when it's removed from the system
			$cs = new SBookCategory ( );
			$cs->webuser = $webuser;
			$cs->Name = $cat;
			$cs->Type = 'Group';
			$cs->CategoryID = '0';
			$cs->IsSystem = 1;
			if ( !$cs->Load () )
			{
				$cs->Save ();
			}*/
			
			if( $subcategories[$key] )
			{
				foreach( $subcategories[$key] as $scat )
				{
					// New method
					$sc = new dbObject( 'SBookCategory' );
					$sc->CategoryID = $cs->ID;
					$sc->Type = 'SubGroup';
					$sc->Name = $scat;
					$sc->IsSystem = 1;
					if ( !$sc->Load() )
					{
						$sc->Owner = '0';
						$sc->Save();
					}
					
					$sr = new dbObject( 'SBookCategoryRelation' );
					$sr->CategoryID = $sc->ID;
					$sr->ObjectType = 'Users';
					$sr->ObjectID = $webuser->ID;
					if ( !$sr->Load() && $webuser->ID )
					{
						$sr->Save();
					}
					
					$sa = new dbObject( 'SBookCategoryAccess' );
					$sa->CategoryID = $sc->ID;
					$sa->UserID = $webuser->ContactID;
					$sa->ContactID = $webuser->ContactID;
					if( !$sa->Load() && $webuser->ContactID )
					{
						$sa->Read = 1;
						$sa->Write = 1;
						$sa->Delete = 1;
						$sa->Admin = 1;
						$sa->Owner = 1;
						$sa->Save();
					}
					
					/*// Old system - TODO: Remove when it's removed from the system
					$sc = new SBookCategory ( );
					$sc->webuser = $webuser;
					$sc->Name = $scat;
					$sc->Type = 'SubGroup';
					$sc->CategoryID = $cs->ID;
					$sc->IsSystem = 1;
					if ( !$sc->Load () )
					{
						$sc->Save ();
					}*/
				}
			}
		}
	}
}

function getSharedPosts ( $id, $parentid = false, $userid = false, $param = false )
{
	global $database;
	
	if( !$id ) return false;
	
	if( $parentid )
	{
		$q = '
			SELECT 
				m.*, u.Name, u.Image 
			FROM 
				SBookMessage m, Users u
			WHERE
				m.ParentID = \'' . $id . '\' 
				AND u.ID = m.SenderID 
			ORDER BY  
				m.ID ASC 
		';
	}
	else if( $userid && $id == getCategoryID( 'News Feed', $userid )->ID )
	{
		$obj = getFeedFolders( $userid, 'Wall, Contacts, Groups' );
		
		$q = '
			SELECT 
				m.*, u.Name, u.Image, c2.Name as CategoryParentName, c.Name as CategoryName, cr.CategoryID as MainCategoryID, cr.ID as CategoryID, r.ID as RelationID, r.IsRead, r.IsNoticed 
			FROM 
				SBookCategory c, 
				SBookCategory c2, 
				SBookCategoryRelation cr, 
				SBookMessage m, 
				SBookMessageRelation r, 
				Users u 
			WHERE 
					r.CategoryID IN ( ' . $obj[0]->Categories . ' ) 
				AND m.ID = r.MessageID 
				AND m.ParentID = "0" 
				AND u.ID = m.SenderID 
				AND cr.ID = r.CategoryID 
				AND cr.ObjectType = "Users" 
				AND cr.ObjectID = u.ID 
				AND c.ID = cr.CategoryID 
				AND c2.ID = c.CategoryID 
				' . ( $param ? 'AND !r.' . $param : '' ) . '
			ORDER BY  
				m.ID DESC 
		';
		//die( print_r( $obj ) . ' ..' );
	}
	else if( $userid && $id == getCategoryID( $id, $userid )->CategoryID )
	{
		$q = '
			SELECT 
				m.*, u.Name, u.Image, r.ID as RelationID, r.IsRead, r.IsNoticed, r.CategoryID  
			FROM 
				SBookCategoryRelation c, 
				SBookMessage m, 
				SBookMessageRelation r, 
				Users u 
			WHERE
					c.CategoryID = \'' . $id . '\' 
				AND r.CategoryID = c.ID 
				AND m.ID = r.MessageID 
				AND m.ParentID = "0" 
				AND u.ID = m.SenderID 
				' . ( $param ? 'AND !r.' . $param : '' ) . '
			ORDER BY  
				m.ID DESC 
		';
		//die( $q );
	}
	else if( $userid )
	{
		$q = '
			SELECT 
				m.*, u.Name, u.Image, r.ID as RelationID, r.IsRead, r.IsNoticed, r.CategoryID  
			FROM 
				SBookMessage m, 
				SBookMessageRelation r, 
				Users u
			WHERE
				r.CategoryID = \'' . $id . '\' 
				AND r.CategoryType = "Users" 
				AND m.ID = r.MessageID 
				AND m.ParentID = "0" 
				AND u.ID = m.SenderID 
				' . ( $param ? 'AND !r.' . $param : '' ) . '
			ORDER BY  
				m.ID DESC 
		';	
	}
	else
	{	
		$q = '
			SELECT 
				m.*, u.Name, u.Image, r.ID as RelationID, r.IsRead, r.IsNoticed, r.CategoryID  
			FROM 
				SBookMessage m, 
				SBookMessageRelation r, 
				Users u 
			WHERE
				r.CategoryID = \'' . $id . '\' 
				AND r.CategoryType = "SubGroup" 
				AND m.ID = r.MessageID 
				AND m.ParentID = "0" 
				AND u.ID = m.SenderID 
				' . ( $param ? 'AND !r.' . $param : '' ) . '
			ORDER BY  
				m.ID DESC 
		';	
	}
	
	if( $rows = $database->fetchObjectRows( $q, false, 'include/functions.php' ) )
	{
		return $rows;
	}
	return false;
}

function getUserFolders ( $folders, $userids )
{
	global $database;
	
	if( !$folders && !$userids ) return false;
	
	if( $folders = explode( ',', trim( $folders ) ) )
	{
		foreach( $folders as $k=>$f )
		{
			$folders[$k] = strtolower( trim( $f ) );
		}
	}
	
	if( $userids = explode( ',', trim( $userids ) ) )
	{
		foreach( $userids as $k=>$u )
		{
			$userids[$k] = strtolower( trim( $u ) );
		}
	}
	
	
}

function renderTemplates ( $name, $path, $folder = false, $url = false, $tabs = false, $cuser = false, $mode = false, $switch = false )
{
	global $page, $webuser;
	
	if( !$name || !$path ) return false;
	
	$cuser = setupUserData ( $cuser );
	
	if( !$cuser ) $cuser =& $webuser;
	
	if( $folder ) $nav = $name . '/' . $folder->CategoryID . '/';
	
	if( $tabs )
	{
		$i = 0;
		foreach( $tabs as $key=>$val )
		{
			if( $i == 0 && !$mode && file_exists ( 'extensions/sbook/templates/' . $name . '_' . $key . '.php' ) )
			{
				$mode = $key;
			}
			else if ( $key == $mode && file_exists ( 'extensions/sbook/templates/' . $name . '_' . $key . '.php' ) )
			{
				$mode = $key;
			}
			$i++;
		}
	}
	
	// --- MainPage --- //
	if( $mainpage = new cPTemplate ( 'extensions/sbook/templates/' . $name . '.php' ) )
	{
		$mainpage->tabs = $tabs;
		$mainpage->folder =& $folder;
		$mainpage->cuser =& $cuser;
		$mainpage->webuser =& $webuser;
		$mainpage->mode = $mode;
		$mainpage->switch = $switch;
		$mainpage->url = $url;
		$mainpage->page =& $page;
		$mainpage->path = $path;
		$mainpage->nav = $path . $nav;
	}
	// --- SubPage --- //
	if( $subpage = new cPTemplate ( 'extensions/sbook/templates/' . $name . '_' . $mode . '.php' ) )
	{
		$subpage->tabs = $tabs;
		$subpage->folder =& $folder;
		$subpage->cuser =& $cuser;
		$subpage->webuser =& $webuser;
		$subpage->mode = $mode;
		$subpage->switch = $switch;
		$subpage->url = $url;
		$subpage->page =& $page;
		$subpage->path = $path;
		$subpage->nav = $path . $nav;
	}
	
	// --- Output --- //
	if( $subpage ) $mainpage->subpage = $subpage->render();
	if( $mainpage ) return $mainpage->render();
}

function renderTabs ( $name, $obj = false, $module = false, $access = false )
{
	switch( $name )
	{
		/*case 'groups':
			return array( 
				'wall'=>'<strong>' . ( $obj->Name != '' ? $obj->Name : 'Wall' ) . '</strong>',
				'irc'=>'Chat', 
				'meeting'=>'Meeting', 
				'news'=>'News',
				'members'=>'Members', 
				'events'=>'Events', 
				'library'=>'Library', 
				'admin'=>'Admin'
			);
			break;*/
		case 'account_panel':
			return array( 
				'general'=>'General', 
				'security'=>'Security', 
				'divider'=>'', 
				'privacy'=>'Privacy', 
				'tagging'=>'Tagging', 
				'blocking'=>'Blocking',
				'divider'=>'',
				'notifications'=>'Notifications',
				'mobile'=>'Mobile',
				'followers'=>'Followers',
				'divider'=>'',
				'apps'=>'Apps',
				'ads'=>'Ads',
				'payments'=>'Payments',
				'gifts'=>'Gifts',
				'support'=>'Support'
			);
			break;
		case 'account_general':
			return array( 
				'name'=>'Name', 
				'username'=>'Username', 
				'email'=>'Email', 
				'password'=>'Password', 
				/*'networks'=>'Networks',*/ 
				'language'=>'Language',
				'themes'=>'Theme',
				'display'=>'Display'
			);
			break;
		case 'global_panel':
			return array( 
				'settings'=>'Settings'
			);
			break;
		case 'global_settings':
			return array( 
				'content'=>'Content'
			);
			break;
		default:
			return ListComponentTabs( $name, $module, false, $obj, $access );
			break;
	}
	return '';
}

/* Groups ------------------------------------------------------------------- */

function listSBookContacts ( $userid = false, $search = false )
{
	global $database, $webuser;
	
	$q = '';
	
	if( $rows = $database->fetchObjectRows( $q ) )
	{
		return $rows;
	}
	return false;
}

function inviteSBookGroupMembers ( $groupid, $userid )
{
	global $database, $webuser;
	
	if( $groupid || $userid ) return false;
	
	$q = '';
	
	if( $row = $database->fetchObjectRow( $q ) )
	{
		$g = new dbObject( 'SBookCategory' );
		if( $g->load( $groupid ) )
		{	
			foreach( $userid as $uid )
			{
				$r = new dbObject( 'SBookCategoryRelations' );
				$r->ObjectID = $g->ID;
				$r->ObjectConnection = 'Users';
				$r->UserID = $uid->ID;
				$r->save();
			}
		}
		return true;
	}
	return false;
}
/*
// Check all group members and return SBookCategoryRelation for each user
function getSBookGroupMembers ( $groupid )
{
	global $database;
	
	if( !$groupid ) return false;
	
	$q = '
		SELECT 
			r.* 
		FROM 
			SBookCategory c, 
			SBookCategoryRelation r 
		WHERE
				c.ID = \'' . $groupid . '\' 
			AND r.CategoryID = c.ID 
			AND r.ObjectType = "Users" 
		ORDER BY  
			r.SortOrder ASC, 
			r.ID ASC 
	';
	
	if( $rows = $database->fetchObjectRows( $q ) )
	{
		return $rows;
	}
	return false;
}
*//*
function createSBookGroup ( $parent, $name, $users, $privacy )
{
	global $database, $webuser;
	
	if( !$parent || !$name || !$privacy ) return false;
	
	$q = '
		SELECT 
			c.* 
		FROM 
			SBookCategory c
		WHERE
			c.Name = \'' . $name . '\' 
		ORDER BY  
			c.ID ASC 
	';
	
	if( !$database->fetchObjectRow( $q ) )
	{
		$p = new dbObject( 'SBookCategory' );
		if( $p->load( $parent ) )
		{
			$g = new dbObject( 'SBookCategory' );
			$g->CategoryID = $p->ID;
			$g->Type = 'SubGroup';
			$g->Name = $name;
			$g->Privacy = $privacy;
			$g->save();
			
			$r = new dbObject( 'SBookCategoryRelation' );
			$r->CategoryID = $g->ID;
			$r->ObjectType = 'Users';
			$r->ObjectID = $webuser->ID;
			$r->Permission = 'admin';
			$r->save();
			return true;
		}
		return die( 'couldnt find parent folder' );
	}
	return die( 'choose another group name' );
}*/

function getProfileByUrl ( $userinfo )
{
	global $database;
	
	if( !$userinfo ) return false;
	
	$c = '
		SELECT 
			c.* 
		FROM 
			SBookContact c 
		ORDER BY 
			c.ID ASC 
	';
	
	$userinfo = strtolower( trim( $userinfo ) );
	$userinfo = str_replace( ' ', '_', $userinfo );
	
	if ( $rows = $database->fetchObjectRows ( $c, false, 'include/functions.php' ) )
	{
		$q = '';
		foreach( $rows as $co )
		{
			$co->UserID = strtolower( trim( $co->UserID ) );
			$co->Username = strtolower( trim( $co->Username ) );
			$co->Username = str_replace( ' ', '_', $co->Username );
				
			if( $userinfo == $co->UserID || $userinfo == $co->Username )
			{
				$g = '
					SELECT 
						c.*, r.ID as FolderID  
					FROM 
						SBookCategory c, 
						SBookCategoryRelation r 
					WHERE 
							r.ObjectID = \'' . $co->UserID . '\' 
						AND r.ObjectType = "Users" 
						AND c.ID = r.CategoryID 
					ORDER BY 
						c.ID ASC 
				';
				
				if ( $rows = $database->fetchObjectRows ( $g, false, 'include/functions.php' ) )
				{
					$co->Groups = $rows;
				}
		
				$c = '
					SELECT 
						c.*, r.IsApproved, r.IsNoticed  
					FROM 
						SBookContact c, 
						SBookContactRelation r 
					WHERE 
							r.ObjectID = \'' . $co->UserID . '\' 
						AND r.ObjectType = "Users" 
						AND c.ID = r.ContactID 
					ORDER BY 
						c.ID ASC 
				';
		
				if ( $rows = $database->fetchObjectRows ( $c, false, 'include/functions.php' ) )
				{
					$co->Contacts = $rows;
				}
		
				return $co;
			}
		}
	}
}

function logUser ( $status, $otype = false, $oid = false, $ctype = false, $cid = false, $uid = false )
{
	global $database, $webuser;
	
	if( !$webuser || !$status ) return false;
	
	$ls = $database->fetchObjectRow ( '
		SELECT 
			* 
		FROM
			Log 
		WHERE
			UserID = \'' . ( $uid ? $uid : $webuser->ID ) . '\' 
		ORDER BY 
			DateCreated DESC 
		LIMIT 1 
	', false, 'include/functions.php' );
	
	if( $ls && $otype && $otype == $ls->ObjectType && $status == $ls->Type || $ls && !$otype && $status == $ls->Type ) return false;

	$l = new dbObject( 'Log' );
	$l->UserID = ( $uid ? $uid : $webuser->ID );
	$l->Type = $status;
	$l->ObjectType = $otype;
	$l->ObjectID = $oid;
	$l->ConnectedType = $ctype;
	$l->ConnectedID = $cid;
	$l->DateCreated = date( 'Y-m-d H:i:s' );
	$l->Save();

	if( $l->ID > 0 ) return true;
}

function checkUserStatus ( $uid, $status = false, $otype = false, $oid = false )
{
	global $database, $webuser;
	
	if( !$uid ) return false;
	
	$q = '
		SELECT 
			l.*, s.LastHeartbeat 
		FROM 
			Log l, 
			UserLogin s 
		WHERE 
				l.UserID = \'' . $uid . '\' 
			' . ( $status ? 'AND l.Type = \'' . $status . '\'' : '' ) . ' 
			' . ( $otype ? 'AND l.ObjectType = \'' . $otype . '\'' : '' ) . ' 
			' . ( $oid ? 'AND l.ObjectID = \'' . $oid . '\'' : '' ) . ' 
			AND s.UserID = l.UserID 
		ORDER BY 
			 l.DateCreated DESC, 
			 s.LastHeartbeat DESC 
		LIMIT 1 
	';
	$stats = array();
	$stats[0] = 'offline';
	
	if( $log = $database->fetchObjectRow ( $q, false, 'include/functions.php' ) )
	{
		if ( $log->Type == 'logout' || ( date( 'i' ) - date( 'i', strtotime( $log->LastHeartbeat ) ) ) >= 5 )
		{
			$stats[0] = 'offline';
		}
		else if( $log->Type == 'login' || $log->Type != 'logout' )
		{
			$stats[0] = 'online';
		}
	}
	
	$stats[1] = $log;
	return $stats;
}

function chatStatusMessage ( $uid, $cid, $status )
{
	global $webuser;
	
	if( !$webuser || !$uid || !$cid ) return false;
	
	// get user last chat activity
	$activity = getLastChatActivity( $uid );
	
	if( 
		( $status == 'joins' && $activity && $activity->CategoryID != $cid ) || 
		( $status == 'joins' && $activity && $activity->CategoryID == $cid && $activity->Status == 'quits' ) || 
		( $status == 'joins' && !$activity  )
	)
	{
		// If has activity, and activity cat is not this cat, and we're not quitting
		if( $activity && $activity->CategoryID != $cid && $activity->Status != 'quits' )
		{
			// save quit status as a message
			$sm = new dbObject( 'SBookChat' );
			$sm->Type = 'im';
			$sm->Status = 'quits';
			$sm->SenderID = $uid;
			$sm->CategoryID = $activity->CategoryID;
			$sm->Message = 'has quit';
			$sm->Date = date( 'Y-m-d H:i:s' );
			$sm->save();
		}
		
		if( $activity && $activity->CategoryID == $cid && $activity->Status == 'joins' ) return false;
		
		// save join status as message
		$sm = new dbObject( 'SBookChat' );
		$sm->Type = 'im';
		$sm->Status = 'joins';
		$sm->SenderID = $uid;
		$sm->CategoryID = $cid;
		$sm->Message = 'has joined';
		$sm->Date = date( 'Y-m-d H:i:s' );
		$sm->save();
	}
	else if ( $status == 'quits' && $activity && $activity->CategoryID == $cid && $activity->Status != 'quits' )
	{
		// save quit status as a message
		$sm = new dbObject( 'SBookChat' );
		$sm->Type = 'im';
		$sm->Status = 'quits';
		$sm->SenderID = $uid;
		$sm->CategoryID = $cid;
		$sm->Message = 'has quit';
		$sm->Date = date( 'Y-m-d H:i:s' );
		$sm->save();
	}
}

function getLastChatActivity ( $uid )
{
	global $database;
	
	if( !$uid ) return false;
	
	$q = '
		SELECT 
			s.* 
		FROM 
			SBookChat s 
		WHERE 
			s.SenderID = \'' . $uid . '\' 
		ORDER BY 
			s.ID DESC 
		LIMIT 1 
	';
	
	if( $activity = $database->fetchObjectRow ( $q, false, 'include/functions.php' ) )
	{
		return $activity;
	}
	return false;
}

function checkContactStatus ( $UserID, $CategoryID = false, $Status = false )
{
	if( !$UserID ) return false;
	
	$s = new dbObject( 'SBookContactStatus' );
	$s->UserID = $UserID;
	if( $s->Load() )
	{
		$s->CategoryID = $CategoryID ? $CategoryID : 0;
		$s->Status = $Status ? $Status : 'online';
		$s->Save();
	}
}
/*
function mailNow_ ( $subject, $message, $receiver, $type = 'html', $from = MAIL_REPLYTO, $attachments = false )
{
	$email = new eMail ();
	$email->setHostInfo ( MAIL_SMTP_HOST, MAIL_USERNAME, MAIL_PASSWORD );
	$email->setSubject ( utf8_decode ( $subject ) );
	$email->setPort ( defined ( 'MAIL_SMTP_PORT' ) ? MAIL_SMTP_PORT : '25' );
	$email->setFrom ( $from );
	$email->addRecipient ( $receiver );
	$email->addHeader ( "Content-type", "text/" . $type . "; charset=iso-8859-1" );
	$Article = utf8_decode ( $message );
	
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
	
	$email->send ();
}
*/
function smsNow_ ( $receiver, $message )
{
	$sms = new cSMS ( SMS_PROVIDER );
	$sms->setHostname ( SMS_HOSTNAME );
	$sms->setUsername ( SMS_USERNAME );
	$sms->setPassword ( SMS_PASSWORD );
	$sms->setFromName ( SMS_FROMNAME );
	$sms->setApiID ( SMS_API_ID );
	$sms->addReceiver ( $receiver );
	$sms->setMessage ( utf8_decode ( $message ) );
	$sms->send ();
}

function dbObjectClean ( $object )
{
	if( !$object ) return false;
	
	$excluded = array( 
		'_tableName', 
		'_primaryKey', 
		'_isLoaded', 
		'_loadState', 
		'_doHooks', 
		'_clauses', 
		'_autoLoad', 
		'_dbOverride', 
		'loadState', 
		'customQuery', 
		'_contentGroups', 
		'_loadingExtrafields', 
		'_cache', 
		'_lastQuery', 
		'table', 
		'fields', 
		'keys', 
		'exists', 
		'_fieldNames', 
		'_table'
	);
	
	$out = array();
	$obj = new stdClass();
	
	if( $object )
	{
		foreach( $object as $key=>$ob )
		{
			if( is_object( $ob ) && $key != '_table' )
			{
				foreach( $ob as $k=>$o )
				{
					if( !is_object( $o ) && !in_array( $k, $excluded ) )
					{
						$obj->$k = $o;
					}
				}
				$out[$key] = $obj;
				$multiple = true;
			}
			else
			{
				if( !is_object( $ob ) && !in_array( $key, $excluded ) )
				{
					$obj->$key = $ob;
				}
				$multiple = false;
			}
			
		}
		if( !$multiple ) 
		{
			$out[] = $obj;
		}
		return $out;
	}
	return false;
}

?>
