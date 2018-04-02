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
include_once ( ( $root ? ( $root.'/' ) : '' ) . 'subether/functions/globalfuncs.php' );

$nodes = $database->fetchObjectRows( 'SELECT ID, Url, SessionID FROM SNodes WHERE SessionID != "" AND IsConnected = "1" AND IsDenied = "0" ORDER BY ID ASC' );

if ( $nodes )
{
	foreach ( $nodes as $node )
	{
		$ph = new PostHandler ( $node->Url . 'components/wall/' );
		$ph->AddVar ( 'Url', $node->Url );
		$ph->AddVar ( 'SessionID', $node->SessionID );
		$wall = simplexml_load_string ( ( $res = $ph->send () ) );
		
		if ( $wall->response == 'ok' )
		{
			// --- Categories --------------------------------------------------------------
			
			if ( isset( $wall->items->Categories ) )
			{
				$ph = new PostHandler ( $node->Url . 'components/category/' );
				$ph->AddVar ( 'Url', $node->Url );
				$ph->AddVar ( 'SessionID', $node->SessionID );
				$ph->AddVar ( 'Categories', $wall->items->Categories );
				$cat = simplexml_load_string ( ( $res = $ph->send () ) );
				
				if ( $cat->response == 'ok' )
				{
					if ( $cat->items->Categories )
					{
						foreach ( $cat->items->Categories as $row )
						{
							$relid = false;
							
							switch ( (string)$row->IsSystem )
							{
								// --- System Category -------------------------------------
								case 1:
									if ( (string)$row->CategoryID == 0 )
									{
										$p = new dbObject( 'SBookCategory' );
										$p->CategoryID = 0;
										$p->Type = (string)$row->Type;
										$p->Name = (string)$row->Name;
										$p->Load();
										
										$relid = $p->ID;
									}
									else
									{
										$c = new dbObject( 'SBookCategory' );
										$c->CategoryID = ( $p->ID > 0 ? $p->ID : 0 );
										$c->Type = (string)$cat->Type;
										$c->Name = (string)$cat->Name;
										$c->Load();
										
										$relid = $c->ID;
									}
									if ( $relid )
									{
										$r = new dbObject( 'SNodesRelation' );
										$r->Field = 'ID';
										$r->NodeID = $node->ID;
										$r->NodeType = 'SNodes';
										$r->ConnectedID = $relid;
										$r->ConnectedType = 'SBookCategory';
										$r->Load();
										$r->NodeValue = (string)$row->ID;
										$r->ConnectedValue = $relid;
										$r->Save();
									}
									break;
								// --- Default ----------------------------------------------
								default:
									$c = new dbObject( 'SBookCategory' );
									$c->NodeID = $node->ID;
									$c->NodeMainID = (string)$row->ID;
									$c->Load();
									$c->CategoryID = (string)$row->CategoryID;
									$c->Type = (string)$row->Type;
									$c->Name = (string)$row->Name;
									$c->Privacy = (string)$row->Privacy;
									$c->Settings = (string)$row->Settings;
									$c->Description = (string)$row->Description;
									$c->Save();
									break;
							}
						}
					}
					
					//die( print_r( $cat->items,1 ) . ' ..' );
				}
			}
			
			// --- Library -----------------------------------------------------------------
			
			if ( isset( $wall->items->Images ) )
			{
				$ph = new PostHandler ( $node->Url . 'components/library/' );
				$ph->AddVar ( 'Url', $node->Url );
				$ph->AddVar ( 'SessionID', $node->SessionID );
				$ph->AddVar ( 'Images', $wall->items->Images );
				$lib = simplexml_load_string ( ( $res = $ph->send () ) );
				
				if ( $lib->response == 'ok' )
				{
					// --- Folders --------------------------------------------------------
					if ( $lib->items->Folders )
					{
						foreach ( $lib->items->Folders as $row )
						{
							$f = new dbObject( 'Folder' );
							$f->NodeID = $node->ID;
							$f->NodeMainID = (string)$row->ID;
							$f->Load();
							$f->Name = (string)$row->Name;
							$f->Parent = (string)$row->Parent;
							$f->Description = (string)$row->Description;
							$f->DiskPath = ( $node->Url . (string)$row->DiskPath );
							$f->Notes = (string)$row->Notes;
							$f->UserID = (string)$row->UserID;
							$f->CategoryID = (string)$row->CategoryID;
							$f->Save();
						}
					}
					
					// --- Images ---------------------------------------------------------
					if ( $lib->items->Images )
					{
						foreach ( $lib->items->Images as $row )
						{
							$folder = new dbObject( 'Folder' );
							$folder->NodeID = $node->ID;
							$folder->NodeMainID = (string)$row->ImageFolder;
							$folder->Load();
							
							$i = new dbObject( 'Image' );
							$i->NodeID = $node->ID;
							$i->NodeMainID = (string)$row->ID;
							$i->Load();
							$i->Title = (string)$row->Title;
							$i->Filename = (string)$row->Filename;
							$i->Tags = (string)$row->Tags;
							$i->ColorSpace = (string)$row->ColorSpace;
							$i->ImageFolder = $folder->ID;
							$i->Filesize = (string)$row->Filesize;
							$i->Width = (string)$row->Width;
							$i->Height = (string)$row->Height;
							$i->SortOrder = (string)$row->SortOrder;
							$i->Filetype = (string)$row->Filetype;
							$i->Save();
						}
					}
					
					//die( print_r( $lib->items,1 ) . ' ..' );
				}
			}
			
			// --- Wall -------------------------------------------------------------------
			
			if ( $wall->items->Posts )
			{
				foreach ( $wall->items->Posts as $row )
				{
					$sender = new dbObject( 'SBookContact' );
					$sender->NodeID = $node->ID;
					$sender->NodeMainID = (string)$row->SenderID;
					$sender->Load();
					
					$receiver = new dbObject( 'SBookContact' );
					$receiver->NodeID = $node->ID;
					$receiver->NodeMainID = (string)$row->ReceiverID;
					$receiver->Load();
					
					$m = new dbObject( 'SBookMessage' );
					$m->NodeID = $node->ID;
					$m->NodeMainID = (string)$row->ID;
					$m->Load();
					$m->CategoryID = getNodeRelation( (string)$row->CategoryID, 'ID', $node->ID, 'SBookCategory' );
					$m->SenderID = $sender->ID;
					$m->ReceiverID = $receiver->ID;
					$m->Message = (string)$row->Message;
					$m->Data = (string)$row->Data;
					$m->Date = (string)$row->Date;
					$m->DateModified = (string)$row->DateModified;
					$m->Type = (string)$row->Type;
					$m->ParentID = (string)$row->ParentID;
					$m->SeenBy = (string)$row->SeenBy;
					$m->Tags = (string)$row->Tags;
					$m->Rating = (string)$row->Rating;
					$m->Save();
				}
			}
			
			//die( print_r( $wall->items,1 ) . ' ..' );
		}
		else 
		{
			//die ( 'Must reauthenticate!' );
		}
	}
}

?>
