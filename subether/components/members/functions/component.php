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

	global $database, $webuser;	
	
	if ( $group = $database->fetchObjectRow( '
		SELECT 
			c.* 
		FROM 
			SBookCategory c 
		WHERE
			c.ID = \'' . $parent->folder->CategoryID . '\' 
	' ) )
	{
		$group->Settings = json_obj_decode( $group->Settings );
		
		// Parent Settings
		if ( $group->ParentID > 0 && ( $pset = $database->fetchObjectRow( '
			SELECT 
				*
			FROM 
				SBookCategory
			WHERE
					ID = \'' . $group->ParentID . '\' 
				AND Type = "SubGroup" 
				AND IsSystem = "0" 
				AND ParentID = "0" 
		', false, 'components/members/functions/component.php' ) ) )
		{
			$pset->Settings = json_obj_decode( $pset->Settings );
			
			if ( isset( $pset->Settings->AccessLevels ) )
			{
				$group->Settings->AccessLevels = $pset->Settings->AccessLevels;
			}
		}
		
		if ( is_object( $group->Settings ) && ( !isset( $group->Settings->AccessLevels[0] ) || !$group->Settings->AccessLevels[0]->ID && !$group->Settings->AccessLevels[0]->Name ) )
		{
			$group->Settings->AccessLevels = json_obj_decode( '[{"ID":"n","Name":"Owner","Display":"@","r":"1","w":"1","d":"1","a":"1","o":"1"},{"ID":"o","Name":"Admin","Display":"@","r":"1","w":"1","d":"1","a":"1","o":"0"},{"ID":"v","Name":"Moderator","Display":"+","r":"1","w":"1","d":"0","a":"1","o":"0"},{"ID":"i","Name":"Member","Display":"","r":"1","w":"1","d":"1","a":"0","o":"0"}]' );
		}
	}
	
	$members = false;
	
	// TODO: Look at this code when there is more time ...
	
	if( isset( $parent->folder->CategoryID ) && $parent->folder->CategoryID > 0 )
	{
		$members = $database->fetchObjectRows( '
			SELECT 
				r.*, u.ID AS ContactID, u.UserID, u.Username, u.Email, u.ImageID 
			FROM 
				SBookCategory c, 
				SBookCategoryRelation r,
				SBookContact u,
				Users u2 
			WHERE
					c.ID = \'' . $parent->folder->CategoryID . '\' 
				AND r.CategoryID = c.ID 
				AND r.ObjectType = "Users"
				AND u.UserID = r.ObjectID
				AND u.UserID != "0"
				AND u.NodeID = "0"
				AND u2.ID = u.UserID 
				AND u2.IsDeleted = "0" 
			ORDER BY 
				r.SortOrder ASC, 
				r.ID ASC 
			LIMIT 500 
		', false, 'components/members/functions/component.php' );
	}
	
	
	
	if ( $members/* = getSBookGroupMembers( $parent->folder->CategoryID )*/ )
	{
		$ids = array();
		
		foreach( $members as $m )
		{
			$u = new dbObject( 'SBookContact' );
			$u->UserID = $m->ObjectID;
			$u->Load();
			
			$m->ContactID = $u->ID;
			
			$ids[] = $u->ID;
		}
		
		$hasAccess = ( isset( $parent->access->IsAdmin ) || isset( $parent->access->IsOwner ) || isset( $parent->access->IsSystemAdmin ) ? true : false );
		
		$uAccess = CategoryAccess( $ids, $parent->folder->CategoryID );
		
		$unam = GetUserDisplayname( $ids );
		
		$td = 0;
		$str = '<div class="members">';
		//$str = '<table><tr>';
		foreach( $members as $m )
		{
			$td++;
			
			//$title = ( isset( $uAccess[$m->ContactID]->IsOwner ) || isset( $uAccess[$m->ContactID]->IsAdmin ) ? '@' : '' ) . ( isset( $uAccess[$m->ContactID]->IsModerator ) ? '+' : '' );
			
			$title = ( ( isset( $uAccess[$m->ContactID]->Settings->Display ) && $uAccess[$m->ContactID]->Settings->Display ) ? ( ' (' . $uAccess[$m->ContactID]->Settings->Display . ')' ) : '' );
			
			//$str .= '<td><table><tr>';
			$str .= '<div class="member"><table><tr>';
			$str .= '<td>';
			//$str .= '<td><div class="image"><a href="' . $parent->path . $m->Username . '">';
			$i = new dbImage ();
			/*if( $i->load( $m->ImageID ) )
			{
				$str .= $i->getImageHTML ( 50, 50, 'framed', false, 0xffffff );
			}*/
			
			if ( !FileExists( ( BASE_URL . 'secure-files/images/' . ( $i->UniqueID ? $i->UniqueID : $i->ID ) . '/' ) ) )
			{
				$m->ImageID = false;
			}
			
			if( $i->Load( $m->ImageID ) )
			{
				//$str = $i->getImageHTML ( 100, 100, 'framed', false, 0xffffff );
				//$str .= '<div class="image" style="background-image:url(\'' . $i->getImageURL ( 50, 50, 'framed', false, 0xffffff ) . '\');background-position:center center;background-size:cover;background-repeat:no-repeat;"><a href="' . $parent->path . $cs->Username . '"></a></div>';
				$str .= '<div class="image" style="background-image:url(\'' . ( BASE_URL . 'secure-files/images/' . ( $i->UniqueID ? $i->UniqueID : $i->ID ) . '/' ) . '\');background-position:center center;background-size:cover;background-repeat:no-repeat;"><a href="' . $parent->path . $cs->Username . '"></a></div>';
			}
			else
			{
				$str .= '<div class="image" style="background-image:url(\'admin/gfx/arenaicons/user_johndoe_128.png\');background-position:center center;background-size:cover;background-repeat:no-repeat;"><a href="' . $parent->path . $cs->Username . '"></a></div>';
			}
			//$str .= '</a></div></td>';
			$str .= '</td>';
			$str .= '<td><div><a href="'/* . $parent->path*/ . $m->Username . '">' . ( isset( $unam[$m->ContactID] ) ? $unam[$m->ContactID] : $m->Username ) . $title . '</a>';
			
			$str .= '<br>';
			
			$str .= '<div class="edit">';
			
			// New more dynamic way
			if( IsSystemAdmin() || ( $hasAccess && isset( $group->Settings->AccessLevels ) && ( !isset( $uAccess[$m->ContactID]->Settings->ID ) || $parent->access->Settings->Permission >= $uAccess[$m->ContactID]->Settings->Permission ) ) )
			{
				$str .= '<select name="Member_' . $m->ContactID . '" onchange="MemberPermission( \'' . $m->ContactID . '\', this.value )">';
				
				//$group->Settings->AccessLevels = array_reverse( $group->Settings->AccessLevels );
				
				$acclvl = array_reverse( $group->Settings->AccessLevels );
				
				//foreach( $group->Settings->AccessLevels as $lvl )
				foreach ( $acclvl as $lvl )
				{
					if ( !$lvl->Name ) continue;
					
					$sel = ( ( isset( $uAccess[$m->ContactID]->Settings->ID ) && $uAccess[$m->ContactID]->Settings->ID == $lvl->ID ) ? ' selected="selected"' : '' );
					$acc = ( $lvl->r ? 'r' : '-' ).( $lvl->w ? 'w' : '-' ).( $lvl->d ? 'd' : '-' ).( $lvl->a ? 'a' : '-' ).( $lvl->o ? 'o' : '-' ) . ( $lvl->ID ? '||'.$lvl->ID : '' );
					$per = ( $lvl->o . $lvl->a . $lvl->d . $lvl->w . $lvl->r );
					
					if ( IsSystemAdmin() || ( $parent->access->Settings->Permission >= $per ) )
					{
						$str .= '<option value="' . $acc . '"' . $sel . '>' . $lvl->Name . ( $lvl->Display ? ' (' . $lvl->Display . ')' : '' ) . '</option>';
					}
				}
				
				$str .= '</select>';
			}
			
			if( IsSystemAdmin() || ( $hasAccess && !isset( $uAccess[$m->ContactID]->IsOwner ) && $parent->access->Settings->Permission >= $uAccess[$m->ContactID]->Settings->Permission ) )
			{
				$str .= '<span onclick="kickMember( \'' . $m->ObjectID . '\' )"> [x] </span>';
			}
			
			$str .= '</div>';
			
			$str .= '</div></td>';
			$str .= '</tr></table></div>';
			/*$str .= '</tr></table></td>';
			if( $td == ( $hasAccess || isset( $parent->access->IsModerator ) ? 3 : 4 ) )
			{
				$td = 0;
				$str .= '</tr><tr>';
			}*/
		}
		//$str .= '</tr></table>';
		$str .= '<div class="clearboth" style="clear:both"></div>';
		$str .= '</div>';
		
		if( isset( $_REQUEST[ 'function' ] ) ) die( 'ok<!--separate-->' . $str );
	}

?>
