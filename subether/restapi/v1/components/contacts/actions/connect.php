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

include_once ( 'subether/classes/posthandler.class.php' );

$co = new dbObject( 'SBookContact' );
$co->ID = isset( $userid ) ? $userid : $_POST['cid'];
if( $co->Load() )
{
	$node = new dbObject( 'SNodes' );
	if( $node->Load( $co->NodeID ) )
	{
		$sc = new dbObject( 'SBookContact' );
		$sc->Load( $webuser->ContactID );
		
		// Creating new users and relation on both nodes --------------------------------------
		$us = new dbObject( 'Users' );
		$us->Username = $co->Email;
		$us->Name = $co->Username;
		if( !$us->Load() )
		{
			$re = new dbObject( 'SBookContactRelation' );
			$re->ContactID = $webuser->ContactID;
			$re->ObjectType = 'SBookContact';
			$re->ObjectID = $co->ID;
			if( $re->Load() )
			{
				die( 'relation exists' );
			}
			$re->Save();
			
			$ph = new PostHandler ( $node->Url . 'connect/contacts/' );
			$ph->AddVar ( 'Url', NODE_URL );
			$ph->AddVar ( 'ID', $re->ID );
			$ph->AddVar ( 'SenderID', $webuser->ContactID );
			$ph->AddVar ( 'ReceiverID', $co->NodeMainID );
			$ph->AddVar ( 'AuthKey', $sc->AuthKey );
			$res = $ph->send();
			
			if( $res && substr( $res, 0, 5 ) == "<?xml" )
			{
				$xml = simplexml_load_string ( trim( $res ) );
				
				if( $xml->response == 'ok' && isset( $xml->authkey ) )
				{
					$us->Password = md5( (string)$xml->authkey );
					$us->Name = $co->Username;
					$us->Email = $co->Email;
					$us->DateCreated = date( 'Y-m-d H:i:s' );
					$us->DateModified = date( 'Y-m-d H:i:s' );
					$us->IsTemplate = 0;
					$us->Save();
					
					$gr = new dbObject( 'Groups' );
					$gr->Name = 'NodeNetwork';
					if( !$gr->Load() )
					{
						$gr->Save();
					}
					
					$ug = new dbObject( 'UsersGroups' );
					$ug->GroupID = $gr->ID;
					$ug->UserID = $us->ID;
					$ug->Save();
					
					$co->UserID = $us->ID;
					$co->DateModified = date( 'Y-m-d H:i:s' );
					$co->Save();
					
					die( 'ok<!--separate-->' );
				}
				else if( $xml->response == 'failed' )
				{
					$scr = new dbObject( 'SBookContactRelation' );
					if( $scr->Load( $re->ID ) )
					{
						$scr->Delete();
					}
					
					die( print_r( $xml,1 ) . ' .. 1 ' . $node->Url . 'connect/contacts/' . ' .. ' . NODE_URL . ' .. ' . $re->ID . ' .. ' . $webuser->ContactID . ' .. ' . $co->NodeMainID . ' .. ' . $sc->AuthKey );
				}
			}
		}
		// Auth with user and create relation on both nodes ----------------------------------
		else if( !isset( $_POST[ 'allow' ] ) && !isset( $_POST[ 'deny' ] ) )
		{
			if( !$sess = $database->fetchObjectRow ( 'SELECT * FROM UserLogin WHERE UserID = \'' . $us->ID . '\' AND DateExpired > NOW() ORDER BY ID DESC LIMIT 1' ) )
			{
				require ( 'subether/restapi/components/contacts/actions/authenticate.php' );
			}
			
			if( $sess->ID > 0 )
			{
				$re = new dbObject( 'SBookContactRelation' );
				$re->ContactID = $webuser->ContactID;
				$re->ObjectType = 'SBookContact';
				$re->ObjectID = $co->ID;
				if( $re->Load() )
				{
					die( 'relation exists' );
				}
				$re->Save();
				
				$ph = new PostHandler ( $node->Url . 'connect/contacts/' );
				$ph->AddVar ( 'Url', NODE_URL );
				$ph->AddVar ( 'ID', $re->ID );
				$ph->AddVar ( 'SenderID', $webuser->ContactID );
				$ph->AddVar ( 'ReceiverID', $co->NodeMainID );
				$ph->AddVar ( 'SessionID', $sess->Token );
				$res = $ph->send();
				
				if( $res && substr( $res, 0, 5 ) == "<?xml" )
				{
					$xml = simplexml_load_string ( trim( $res ) );
					
					if( $xml->response == 'ok' )
					{
						die( 'ok<!--separate-->' );
					}
					if( $xml->response == 'failed' )
					{
						$scr = new dbObject( 'SBookContactRelation' );
						if( $scr->Load( $re->ID ) )
						{
							$scr->Delete();
						}
						
						die( print_r( $xml,1 ) . ' .. 2' );
					}
				}
			}
		}
		// Auth with user and allow or deny relation on both nodes -----------------------------
		else if( isset( $_POST[ 'allow' ] ) || isset( $_POST[ 'deny' ] ) )
		{
			if( !$sess = $database->fetchObjectRow ( 'SELECT * FROM UserLogin WHERE UserID = \'' . $co->UserID . '\' AND DateExpired > NOW() ORDER BY ID DESC LIMIT 1' ) )
			{
				require ( 'subether/restapi/components/contacts/actions/authenticate.php' );
			}
			
			if( $sess->ID > 0 )
			{
				$re = new dbObject( 'SBookContactRelation' );
				$re->ContactID = $co->ID;
				$re->ObjectType = 'SBookContact';
				$re->ObjectID = $webuser->ContactID;
				if( !$re->Load() )
				{
					die( 'relation doesnt exists' );
				}
				
				$ph = new PostHandler ( $node->Url . 'connect/contacts/' );
				$ph->AddVar ( 'Url', NODE_URL );
				$ph->AddVar ( 'ID', $re->NodeMainID );
				$ph->AddVar ( 'Allow', ( $_POST[ 'allow' ] ? '1' : '0' ) );
				$ph->AddVar ( 'SessionID', $sess->Token );
				$res = $ph->send();
				
				if( $res && substr( $res, 0, 5 ) == "<?xml" )
				{
					$xml = simplexml_load_string ( trim( $res ) );
					
					if( $xml->response == 'ok' )
					{
						$scr = new dbObject( 'SBookContactRelation' );
						if( $scr->Load( $re->ID ) )
						{
							if( isset( $_POST[ 'allow' ] ) )
							{
								$scr->IsApproved = 1;
								$scr->Save();
							}
							else if( isset( $_POST[ 'deny' ] ) )
							{
								$scr->Delete();
							}
						}
						
						// Clear chatcache
						unset ( $_SESSION['ChatCache'] );
						
						die( 'ok<!--separate-->' );
					}
					if( $xml->response == 'failed' )
					{
						die( print_r( $xml,1 ) . ' .. 3' );
					}
				}
			}
		}
		die( 'couldnt find user' );
	}
	die( 'couldnt find node' );
}
die( 'couldnt find contact' );

?>
