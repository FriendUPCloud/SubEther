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

global $webuser;

if ( $_REQUEST['action'] = 'removecontact' && $webuser && $parent && $parent->cuser->ContactID > 0 )
{
	$c = new dbObject( 'SBookContact' );
	$c->ID = $parent->cuser->ContactID;
	if ( $c->Load() )
	{
		$deleted = false;
		
		// TODO: Remove old way all over the system
		$r = new dbObject( 'SBookContactRelation' );
		$r->ObjectID = $webuser->ID;
		$r->ObjectType = 'Users';
		$r->ContactID = $c->ID;
		if( $r->Load() )
		{
			$r->Delete();
			
			$s = new dbObject( 'SBookContact' );
			$s->UserID = $webuser->ID;
			if( $s->load() )
			{
				$u = new dbObject( 'SBookContactRelation' );
				$u->ObjectID = $c->UserID;
				$u->ObjectType = 'Users';
				$u->ContactID = $s->ID;
				if( $u->Load() )
				{
					$u->Delete();
				}
			}
		}
		
		$r1 = new dbObject( 'SBookContactRelation' );
		$r1->ContactID = $webuser->ContactID;
		$r1->ObjectType = 'SBookContact';
		$r1->ObjectID = $c->ID;
		if ( $r1->Load() )
		{
			UserActivity( 'contacts', 'relations', $r1->ContactID, $r1->ObjectID, $r1->ID, 'removed' );
			
			$deleted = true;
			$r1->Delete();
		}
		
		$r2 = new dbObject( 'SBookContactRelation' );
		$r2->ContactID = $c->ID;
		$r2->ObjectType = 'SBookContact';
		$r2->ObjectID = $webuser->ContactID;
		if ( $r2->Load() )
		{
			UserActivity( 'contacts', 'relations', $r2->ContactID, $r2->ObjectID, $r2->ID, 'removed' );
			
			$deleted = true;
			$r2->Delete();
		}
		
		if ( $deleted )
		{
			die( 'ok<!--separate-->' . $c->ID );
		}
	}
	die( 'couldnt find contact' );
}
die( 'fail' );

?>
