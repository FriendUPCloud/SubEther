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

include_once ( ( $root ? ( $root.'/' ) : '' ) . 'subether/classes/posthandler.class.php' );

$main = getNodeData( $root );

if( isset( $main->Nodes ) )
{
	
	// --- Send your node information to the nodes in the index list and store the results back in the database ---
	
	foreach( $main->Nodes as $nod )
	{
		if( $nod->Url && $main->UniqueID )
		{
			$ph = new PostHandler ( $nod->Url . 'information/' );
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
			$res = $ph->Send();
			
			// Log api activity
			logActivity ( $res, $nod->Url . 'information/', $ph->vars, 'information.log' );
			
			if( $res && substr( $res, 0, 5 ) == "<?xml" )
			{
				$xml = simplexml_load_string ( trim( $res ) );
				$dat = ( isset( $xml->items[0]->Information ) ? $xml->items[0]->Information : false );
				
				if( $xml->response == 'ok' && isset( $dat->UniqueID ) && $dat->UniqueID )
				{
					if( isset( $dat->Modules->Module ) )
					{
						$mods = array();
						foreach( $dat->Modules->Module as $v )
						{
							$mods[] = (string)$v;
						}
					}
					
					if( isset( $dat->Components->Component ) )
					{
						$comp = array();
						foreach( $dat->Components->Component as $v )
						{
							$comp[] = (string)$v;
						}
					}
					
					if( isset( $dat->Plugins->Plugin ) )
					{
						$plgs = array();
						foreach( $dat->Plugins->Plugin as $v )
						{
							$plgs[] = (string)$v;
						}
					}
					
					if( isset( $dat->Themes->Theme ) )
					{
						$thms = array();
						foreach( $dat->Themes->Theme as $v )
						{
							$thms[] = (string)$v;
						}
					}
					
					if( isset( $dat->Releases->Release ) )
					{
						$rels = array();
						foreach( $dat->Releases->Release as $v )
						{
							$rels[] = $v;
						}
					}
					
					$node = new dbObject( 'SNodes' );
					$node->UniqueID = (string)$dat->UniqueID;
					if( !$node->Load() || $node->DateCreated == '0000-00-00 00:00:00' )
					{
						$node->DateCreated = (string)$dat->Created;
					}
					$node->IsIndex = ( getNodeInfo( 'index' ) == (string)$dat->Url ? 1 : 0 );
					$node->PublicKey = (string)$dat->PublicKey;
					$node->Url = (string)$dat->Url;
					$node->Name = (string)$dat->Name;
					$node->Version = (string)$dat->Version;
					$node->Owner = (string)$dat->Owner;
					$node->Email = (string)$dat->Email;
					$node->Location = (string)$dat->Location;
					$node->Users = (string)$dat->Users;
					$node->Modules = ( isset( $mods ) ? json_encode( $mods ) : '' );
					$node->Components = ( isset( $comp ) ? json_encode( $comp ) : '' );
					$node->Plugins = ( isset( $plgs ) ? json_encode( $plgs ) : '' );
					$node->Themes = ( isset( $thms ) ? json_encode( $thms ) : '' );
					$node->Releases = ( isset( $rels ) ? json_encode( $rels ) : '' );
					$node->Open = (string)$dat->Open;
					$node->DateModified = date( 'Y-m-d H:i:s' );
					$node->IsConnected = 1;
					$node->IsPending = 0;
					$node->Save();
					
					if( isset( $dat->Nodes->Node ) )
					{
						foreach( $dat->Nodes->Node as $n )
						{
							$nodes = new dbObject( 'SNodes' );
							$nodes->UniqueID = (string)$n->UniqueID;
							if( !$nodes->Load() && $nodes->UniqueID != '' )
							{
								$nodes->Url = (string)$n->Url;
								$nodes->Save();
							}
						}
					}
				}
				else if( $xml->response == 'failed' && $xml->code == '0012' && $nod->ID )
				{
					$node = new dbObject( 'SNodes' );
					$node->ID = $nod->ID;
					if( $node->Load() )
					{
						$node->IsPending = 0;
						$node->IsConnected = 0;
						$node->Save();
					}
				}
			}
		}
	}
}

?>
