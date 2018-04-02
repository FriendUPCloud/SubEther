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
	
	foreach( $main->Nodes as $nod )
	{
		if( $nod->Url && $nod->IsIndex == 0 && $nod->IsConnected > 0 && $nod->Open >= 0 && $nod->SessionID )
		{
			$ph = new PostHandler ( $nod->Url . 'components/contacts/' );
			$ph->AddVar ( 'SessionID', $nod->SessionID );
			//$ph->AddVar ( 'Limit', '100' );
			$ph->AddVar ( 'Limit', '2000' );
			$res = $ph->Send();
			
			// Log api activity
			logActivity ( $res, $nod->Url . 'components/contacts/', $ph->vars, 'contacts.log' );
			
			if( $res && substr( $res, 0, 5 ) == "<?xml" )
			{
				$xml = simplexml_load_string ( trim( $res ) );
				$dat = ( isset( $xml->items[0] ) ? $xml->items[0] : false );
				
				if( $xml->response == 'ok' )
				{
					if( isset( $dat->Contacts ) )
					{
						foreach( $dat->Contacts as $c )
						{
							if( (string)$c->UniqueID != '' )
							{
								// TODO: There is possibly a way to manipulate users here by manipulating the uniqueid and publickey.
								// TODO: Implement check for more pages based on limits to check everything possible over a span of time.
								$usr = new dbObject( 'Users' );
								$usr->UniqueID   = (string)$c->UniqueID;
								$usr->Load();
								
								$username        = UniqueUsername( (string)$c->Username, (string)$c->UniqueID );
								
								$usr->Username   = (string)$c->Email;
								$usr->PublicKey  = (string)$c->PublicKey;
								$usr->Name       = $username;
								$usr->Email      = (string)$c->Email;
								$usr->NodeID     = $nod->ID;
								$usr->NodeUserID = (string)$c->UserID;
								$usr->Save();
								
								if( $usr->ID > 0 )
								{
									$con = new dbObject( 'SBookContact' );
									$con->UniqueID   = (string)$c->UniqueID;
									$con->Load();
									$con->UserID        = $usr->ID;
									$con->Username      = $username;
									$con->Email         = (string)$c->Email;
									$con->Firstname     = (string)$c->Firstname;
									$con->Middlename    = (string)$c->Middlename;
									$con->Lastname      = (string)$c->Lastname;
									$con->Gender        = (string)$c->Gender;
									$con->Languages     = (string)$c->Languages;
									$con->Alternate     = (string)$c->Alternate;
									$con->ScreenName    = (string)$c->ScreenName;
									$con->Website       = (string)$c->Website;
									$con->Address       = (string)$c->Address;
									$con->Country       = (string)$c->Country;
									$con->City          = (string)$c->City;
									$con->Postcode      = (string)$c->Postcode;
									$con->Telephone     = (string)$c->Telephone;
									$con->Mobile        = (string)$c->Mobile;
									$con->Email         = (string)$c->Email;
									$con->Work          = (string)$c->Work;
									$con->College       = (string)$c->College;
									$con->HighSchool    = (string)$c->HighSchool;
									$con->Interests     = (string)$c->Interests;
									$con->Philosophy    = (string)$c->Philosophy;
									$con->Religion      = (string)$c->Religion;
									$con->Political     = (string)$c->Political;
									$con->About         = (string)$c->About;
									$con->Quotations    = (string)$c->Quotations;
									$con->Data          = (string)$c->Data;
									$con->ShowAlternate = (string)$c->ShowAlternate;
									$con->Display       = (string)$c->Display;
									$con->NodeID        = $nod->ID;
									$con->NodeMainID    = (string)$c->ID;
									$con->DateModified  = date( 'Y-m-d H:i:s' );
									$con->Save();
								}
							}
						}
					}
				}
			}
		}
	}
}

?>
