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

if ( isset( $_POST ) && $_POST[ 'groupid' ] )
{
	$ok = false;
	
	foreach( $_POST as $key=>$value )
	{
		if ( $key == 'groupid' ) 
		{
			continue;
		}
		else if ( $value )
		{
			$u = new dbObject( 'SBookContact' );
			$u->UserID = $value;
			$u->NodeID = 0;
			$u->NodeMainID = 0;
			if ( $u->Load() )
			{
				if ( $p = $database->fetchObjectRow( '
					SELECT
						*
					FROM
						SBookCategory
					WHERE
							ID = \'' . $_POST['groupid'] . '\' 
						AND ID > 0 
						AND Type = "SubGroup" 
						AND IsSystem = "0" 
				', false, 'components/groups/actions/groupcreate.php' ) )
				{
					$uset = array();
					
					// Parent Settings
					if ( $p->ParentID > 0 && ( $pset = $database->fetchObjectRows( '
						SELECT 
							a.*, c.Settings AS GroupSettings 
						FROM 
							SBookCategory c,
							SBookCategoryAccess a 
						WHERE
								c.ID = \'' . $p->ParentID . '\' 
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
					
					// Old method - TODO: Remove when every trace of this is deleted in the system
					$c = new dbObject( 'SBookCategoryRelation' );
					$c->ObjectType = 'Users';
					$c->CategoryID = $p->ID;
					$c->ObjectID = $u->UserID;
					$c->Load();
					$c->Save();
					
					// New method
					$a = new dbObject( 'SBookCategoryAccess' );
					$a->CategoryID = $p->ID;
					$a->UserID = $u->ID;
					$a->ContactID = $u->ID;
					if ( !$a->Load() )
					{
						if ( isset( $uset[$u->ID] ) && $uset[$u->ID] )
						{
							$a->Read = $uset[$u->ID]->Read;
							$a->Write = $uset[$u->ID]->Write;
							$a->Delete = $uset[$u->ID]->Delete;
							$a->Admin = $uset[$u->ID]->Admin;
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
					
					$ok = true;
				}
			}
		}
	}
	
	if ( $ok ) die( 'ok<!--separate-->' );
}
die( 'fail' );

?>
