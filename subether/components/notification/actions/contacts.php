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

if( ( isset( $_POST[ 'allow' ] ) || isset( $_POST[ 'deny' ] ) ) && $webuser->ID > 0 )
{
	$_POST[ 'allow' ] ? $userid = $_POST[ 'allow' ] : '';
	$_POST[ 'deny' ] ? $userid = $_POST[ 'deny' ] : '';
	
	$c = new dbObject( 'SBookContact' );
	$c->ID = $userid;
	if( $c->Load() )
	{
		if( $c->NodeID > 0 && $c->NodeMainID > 0 )
		{
			require ( 'subether/restapi/v1/components/contacts/actions/connect.php' );
			
			die( 'fail' );
		}
		
		/*// TODO remove old way
		$r = new dbObject( 'SBookContactRelation' );
		$r->ObjectID = $webuser->ID;
		$r->ObjectType = 'Users';
		$r->ContactID = $c->ID;
		if( $r->Load() )
		{
			if( isset( $_POST[ 'allow' ] ) )
			{
				$r->DateModified = date( 'Y-m-d H:i:s' );
				$r->IsApproved = 1;
				$r->Save();
			}
			else if( isset( $_POST[ 'deny' ] ) )
			{
				$r->Delete();
			}
			
			$s = new dbObject( 'SBookContact' );
			$s->UserID = $webuser->ID;
			if( $s->Load() )
			{
				$u = new dbObject( 'SBookContactRelation' );
				$u->ObjectID = $userid;
				$u->ObjectType = 'Users';
				$u->ContactID = $s->ID;
				if( $u->Load() )
				{
					if( isset( $_POST[ 'allow' ] ) )
					{
						$u->DateModified = date( 'Y-m-d H:i:s' );
						$u->IsApproved = 1;
						$u->Save();
					}
					else if( isset( $_POST[ 'deny' ] ) )
					{
						$u->Delete();
					}
				}
			}
		}*/
		
		$r = new dbObject( 'SBookContactRelation' );
		$r->ContactID = $c->ID;
		$r->ObjectType = 'SBookContact';
		$r->ObjectID = $webuser->ContactID;
		if( $r->Load() )
		{
			if( isset( $_POST[ 'allow' ] ) )
			{
				$r->DateModified = date( 'Y-m-d H:i:s' );
				$r->IsApproved = 1;
				$r->Save();
				
				UserActivity( 'contacts', 'relations', $r->ContactID, $r->ObjectID, $r->ID, 'approved' );
			}
			else if( isset( $_POST[ 'deny' ] ) )
			{
				UserActivity( 'contacts', 'relations', $r->ContactID, $r->ObjectID, $r->ID, 'denied' );
				
				$r->Delete();
			}
		}
		
		// Clear chatcache
		unset ( $_SESSION['ChatCache'] );
		
		die( 'ok<!--separate-->' );
	}
	die( 'fail' );
}
else die( 'fail' );

?>
