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
include_once ( ( $root ? ( $root.'/' ) : '' ) . 'subether/functions/userfuncs.php' );
include_once ( ( $root ? ( $root.'/' ) : '' ) . 'subether/functions/globalfuncs.php' );

// Get root folder
$rfld = new dbFolder();
$rfld = $rfld->getRootFolder();

$nodes = $database->fetchObjectRows( 'SELECT ID, Url, SessionID FROM SNodes WHERE SessionID != "" AND IsConnected = "1" AND IsDenied = "0" ORDER BY ID ASC' );

if ( $nodes )
{
	foreach ( $nodes as $node )
	{
		$ph = new PostHandler ( $node->Url . 'components/contacts/' );
		$ph->AddVar ( 'Url', $node->Url );
		$ph->AddVar ( 'SessionID', $node->SessionID );
		$xml = simplexml_load_string ( ( $res = $ph->send () ) );
		
		if( isset( $_REQUEST['apiaction'] ) && $_REQUEST['apiaction'] == 'components' && $node->ID == 25 )
		{
			//die( print_r( $xml,1 ) . ' ..123' );
		}
		if ( $xml->response == 'ok' && $xml->items->Contacts )
		{
			// --- Library -----------------------------------------------------------------
			
			if ( isset( $xml->items->Images ) )
			{
				$ph = new PostHandler ( $node->Url . 'components/library/' );
				$ph->AddVar ( 'Url', $node->Url );
				$ph->AddVar ( 'SessionID', $node->SessionID );
				$ph->AddVar ( 'Images', $xml->items->Images );
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
			
			// --- Contacts -------------------------------------------------------------------
			
			if ( $xml->items->Contacts )
			{
				foreach ( $xml->items->Contacts as $gr )
				{
					$rname = getNodeRelation( (string)$gr->Username, 'Username', $node->ID, 'SBookContact', false, true );
					$uname = UniqueName( (string)$gr->Username );
					
					$c = new dbObject( 'SBookContact' );
					$c->NodeID = $node->ID;
					$c->NodeMainID = (string)$gr->ID;
					$c->Load();
					$c->ImageID = (string)$gr->ImageID;
					//$c->UserID = $c->ID;
					$c->Username = ( $rname ? $rname : $uname );
					$c->Firstname = (string)$gr->Firstname;
					$c->Middlename = (string)$gr->Middlename;
					$c->Lastname = (string)$gr->Lastname;
					$c->Gender = (string)$gr->Gender;
					$c->Languages = (string)$gr->Languages;
					$c->Alternate = (string)$gr->Alternate;
					$c->ScreenName = (string)$gr->ScreenName;
					$c->Website = (string)$gr->Website;
					$c->Address = (string)$gr->Address;
					$c->Country = (string)$gr->Country;
					$c->City = (string)$gr->City;
					$c->PostCode = (string)$gr->PostCode;
					$c->Telephone = (string)$gr->Telephone;
					$c->Mobile = (string)$gr->Mobile;
					$c->Email = (string)$gr->Email;
					$c->Work = (string)$gr->Work;
					$c->College = (string)$gr->College;
					$c->HighSchool = (string)$gr->HighSchool;
					$c->Interests = (string)$gr->Interests;
					$c->Philosophy = (string)$gr->Philosophy;
					$c->Religion = (string)$gr->Religion;
					$c->Political = (string)$gr->Political;
					$c->About = (string)$gr->About;
					$c->Quotations = (string)$gr->Quotations;
					$c->Data = (string)$gr->Data;
					$c->DateCreated = (string)$gr->DateCreated;
					$c->ShowAlternate = (string)$gr->ShowAlternate;
					$c->Display = (string)$gr->Display;
					$c->Save();
					
					// --- Relations --- //
					
					if ( $c->ID > 0 )
					{
						$r = new dbObject( 'SNodesRelation' );
						$r->Field = 'ID';
						$r->NodeID = $node->ID;
						$r->NodeType = 'SNodes';
						$r->ConnectedID = $c->ID;
						$r->ConnectedType = 'SBookContact';
						$r->Load();
						$r->NodeValue = (string)$gr->ID;
						$r->ConnectedValue = $c->ID;
						$r->Save();
						
						$r = new dbObject( 'SNodesRelation' );
						$r->Field = 'Username';
						$r->NodeID = $node->ID;
						$r->NodeType = 'SNodes';
						$r->ConnectedID = $c->ID;
						$r->ConnectedType = 'SBookContact';
						$r->Load();
						$r->NodeValue = (string)$gr->Username;
						$r->ConnectedValue = ( $rname ? $rname : $uname );
						$r->Save();
						
						$r = new dbObject( 'SNodesRelation' );
						$r->Field = 'UserID';
						$r->NodeID = $node->ID;
						$r->NodeType = 'SNodes';
						$r->ConnectedID = $c->ID;
						$r->ConnectedType = 'SBookContact';
						$r->Load();
						$r->NodeValue = (string)$gr->UserID;
						$r->ConnectedValue = $c->UserID;
						$r->Save();
					}
				}
			}
		}
		else 
		{
			//die ( 'Must reauthenticate!' );
		}
	}
}

?>
