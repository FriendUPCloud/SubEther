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

//include_once ( ( $root ? ( $root.'/' ) : '' ) . 'subether/classes/posthandler.class.php' );
include_once ( 'subether/classes/posthandler.class.php' );

$node = new dbObject( 'SNodes' );
if( $node->Load( $_POST[ 'allow' ] ) )
{
	if( $main = getNodeData() )
	{
		$ph = new PostHandler ( $node->Url . 'connect/nodes/' );
		$ph->AddVar ( 'UniqueID', $main->UniqueID );
		$ph->AddVar ( 'PublicKey', $main->PublicKey );
		$ph->AddVar ( 'Url', $main->Url );
		$ph->AddVar ( 'Name', $main->Name );
		$ph->AddVar ( 'Version', $main->Version );
		$ph->AddVar ( 'Owner', $main->Owner );
		$ph->AddVar ( 'Email', $main->Email );
		$ph->AddVar ( 'Location', $main->Location );
		$ph->AddVar ( 'Users', $main->Users );
		$ph->AddVar ( 'Open', $main->Open );
		$ph->AddVar ( 'Created', $main->DateCreated );
		$res = $ph->send();
		
		// Log api activity
		logActivity ( $node->Url . 'connect/nodes/', $ph->vars, $res );
		
		if( $res && substr( $res, 0, 5 ) == "<?xml" )
		{
			$xml = simplexml_load_string ( trim( $res ) );
			
			if( $xml->response == 'ok' )
			{
				if( isset( $xml->publickey ) )
				{
					$node->PublicKey = (string)$xml->publickey;
				}
				$node->IsPending = 1;
			}
			else if( $xml->response == 'failed' )
			{
				$node->IsDenied = 1;
			}
		}
		
		$node->DateModified = date( 'Y-m-d H:i:s' );
		$node->Save();
	}
}

?>
