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

if( $_REQUEST['action'] = 'addcontact' && $_POST['cid'] && $webuser )
{
	$c = new dbObject( 'SBookContact' );
	$c->ID = $_POST['cid'];
	if( $c->Load() )
	{
		if( $c->NodeID > 0 && $c->NodeMainID > 0 )
		{
			//require ( 'subether/restapi/components/contacts/actions/connect.php' );
			
			die( 'fail' );
		}
		
		// TODO remove old way
		/*$r = new dbObject( 'SBookContactRelation' );
		$r->ObjectID = $webuser->ID;
		$r->ObjectType = 'Users';
		$r->ContactID = $c->ID;
		$r->IsNoticed = 1;
		$r->IsApproved = 1;
		if( !$r->load() )
		{
			$r->save();
			
			$s = new dbObject( 'SBookContact' );
			$s->UserID = $webuser->ID;
			if( $s->load() )
			{
				$u = new dbObject( 'SBookContactRelation' );
				$u->ObjectID = $_POST['cid'];
				$u->ObjectType = 'Users';
				$u->ContactID = $s->ID;
				if( !$u->load() )
				{
					$u->save();
				}
			}
			
			die( 'ok<!--separate-->' );
		}*/
		$r = new dbObject( 'SBookContactRelation' );
		$r->ContactID = $webuser->ContactID;
		$r->ObjectType = 'SBookContact';
		$r->ObjectID = $c->ID;
		if( !$r->Load() )
		{
			$r->DateCreated = date( 'Y-m-d H:i:s' );
			$r->DateModified = date( 'Y-m-d H:i:s' );
			$r->Save();
			
			UserActivity( 'contacts', 'relations', $r->ContactID, $r->ObjectID, $r->ID, 'new' );
			
			die( 'ok<!--separate-->' );
		}
		die( 'relation exists' );
	}
	die( 'couldnt find contact' );
}
die( 'fail' );

?>
