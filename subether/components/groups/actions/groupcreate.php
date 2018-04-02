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

//include ( 'subether/components/meeting/include/voicechat.php' );

function createSBookGroup ( $parent = 0, $name, $users, $privacy )
{
	global $database, $webuser;
	
	if ( !$name || !$privacy ) return false;
	
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
	
	if ( !$database->fetchObjectRow( $q ) )
	{
		if ( $p = $database->fetchObjectRow( '
			SELECT
				*
			FROM
				SBookCategory
			WHERE
					Type = "Group"
				AND Name = "Groups"
		', false, 'components/groups/actions/groupcreate.php' ) )
		{
			$uset = array();
			
			// Parent Settings
			if ( $parent > 0 && ( $pset = $database->fetchObjectRows( '
				SELECT 
					a.*, c.Settings AS GroupSettings 
				FROM 
					SBookCategory c,
					SBookCategoryAccess a 
				WHERE
						c.ID = \'' . $parent . '\' 
					AND c.ID > 0 
					AND c.Type = "SubGroup" 
					AND c.IsSystem = "0" 
					AND c.ParentID = "0"
					AND a.CategoryID = c.ID 
			', false, 'components/groups/actions/groupcreate.php' ) ) )
			{
				foreach( $pset as $set )
				{
					$uset[$set->ContactID] = $set;
				}
			}
			
			$g = new dbObject( 'SBookCategory' );
			$g->UniqueID  = UniqueKey();
			$g->CategoryID = $p->ID;
			$g->ParentID = $parent;
			$g->Type = 'SubGroup';
			$g->Name = $name;
			$g->Privacy = $privacy;
			$g->Owner = $webuser->ContactID;
			$g->Save();
			
			// Old method - TODO: Remove when every trace of this is deleted in the system
			$r = new dbObject( 'SBookCategoryRelation' );
			$r->CategoryID = $g->ID;
			$r->ObjectType = 'Users';
			$r->ObjectID = $webuser->ID;
			$r->Load();
			$r->Permission = 'owner';
			$r->Save();
			
			// New method
			$a = new dbObject( 'SBookCategoryAccess' );
			$a->CategoryID = $g->ID;
			$a->UserID = $webuser->ContactID;
			$a->ContactID = $webuser->ContactID;
			if ( !$a->Load() )
			{
				$a->Read = 1;
				$a->Write = 1;
				$a->Delete = 1;
				$a->Admin = 1;
				$a->Owner = 1;
				$a->Save();
			}
			
			if ( $users = explode( ',', $users ) )
			{
				foreach( $users as $usr )
				{
					$c = new dbObject( 'SBookContact' );
					$c->UserID = $usr;
					$c->NodeID = 0;
					$c->NodeMainID = 0;
					if ( $c->Load() )
					{
						// Old method - TODO: Remove when every trace of this is deleted in the system
						$r = new dbObject( 'SBookCategoryRelation' );
						$r->CategoryID = $g->ID;
						$r->ObjectType = 'Users';
						$r->ObjectID = $c->UserID;
						$r->Load();
						$r->Save();
						
						// New method
						$a = new dbObject( 'SBookCategoryAccess' );
						$a->CategoryID = $g->ID;
						$a->UserID = $c->ID;
						$a->ContactID = $c->ID;
						if ( !$a->Load() )
						{
							if ( isset( $uset[$c->ID] ) && $uset[$c->ID] )
							{
								$a->Read = $uset[$c->ID]->Read;
								$a->Write = $uset[$c->ID]->Write;
								$a->Delete = $uset[$c->ID]->Delete;
								$a->Admin = $uset[$c->ID]->Admin;
								$a->Save();
							}
							else
							{
								$a->Read = 1;
								$a->Write = 1;
								$a->Delete = 1;
								$a->Save();
							}
						}
					}
				}
			}
			
			return $g->ID;
		}
		return die( 'couldnt find parent folder' );
	}
	return die( 'choose another group name' );
}

if ( $_POST )
{
	$cg = createSBookGroup ( $_POST[ 'pid' ], $_POST[ 'name' ], $_POST[ 'users' ], $_POST[ 'privacy' ] );
	
	if ( isset( $_REQUEST[ 'bajaxrand' ] ) )
	{
		if ( $cg ) die ( 'ok<!--separate-->' . $cg ); else die ( 'fail' );
	}
}

?>
