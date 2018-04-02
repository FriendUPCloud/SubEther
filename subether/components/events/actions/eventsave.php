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

// --- Event -----------------------------------------------------------------------------------------------------------

if ( $_POST['datestart'] || $_POST['dateend'] )
{
	$_POST['datestart'] = ( $_POST['datestart'] && !strstr( $_POST['datestart'], '-' ) ? date( 'Y-m-d H:i:s', $_POST['datestart'] ) : $_POST['datestart'] );
	$_POST['dateend'] = ( $_POST['dateend'] && !strstr( $_POST['dateend'], '-' ) ? date( 'Y-m-d H:i:s', $_POST['dateend'] ) : $_POST['dateend'] );
}

if( $_POST['date'] && $_POST['start'] && $_POST['end'] )
{
	$_POST['start'] = ConvertToTime( $_POST['start'] );
	$_POST['end'] = ConvertToTime( $_POST['end'] );
	
	switch( $_POST['mode'] )
	{
		// TODO: Make support for from to events longer then one day, atm default is only one day per event
		
		case 'extended':
			$e = new dbObject( 'SBookEvents' );
			if( $_POST['eid'] > 0 )
			{
				$e->ID = $_POST['eid'];
				$e->Load();
			}
			else
			{
				if( $_POST['vid'] > 0 && ( $found = $database->fetchObjectRow( '
					SELECT 
						* 
					FROM 
						SBookEvents 
					WHERE 
						ID = \'' . $_POST['vid'] . '\' 
					ORDER BY 
						ID DESC 
				', false, 'components/events/actions/eventsave.php' ) ) )
				{
					$e->ImageID = $found->ImageID;
				}
				
				$e->UniqueID = UniqueKey();
				$e->DateCreated = date( 'Y-m-d H:i:s' );
				$e->UserID = $webuser->ContactID;
				if( $parent && strtolower( $parent->folder->MainName ) != 'profile' )
				{
					$e->CategoryID = $parent->folder->CategoryID;
				}
			}
			$e->Component = 'events';
			$e->Name = $_POST['event'];
			$e->Place = $_POST['place'];
			$e->Details = $_POST['details'];
			$e->Type = $_POST['type'];
			$e->DateStart = date( 'Y-m-d', strtotime( $_POST['datestart'] ? $_POST['datestart'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['start'] ) );
			$e->DateEnd = date( 'Y-m-d', strtotime( $_POST['datestart'] ? $_POST['datestart'] : $_POST['date'] ) )  . ' ' . date( 'H:i', strtotime( $_POST['end'] ) );
			//$e->DateEnd = date( 'Y-m-d', strtotime( $_POST['dateend'] ? $_POST['dateend'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['end'] ) );
			$e->DateModified = date( 'Y-m-d H:i:s' );
			$e->Access = $_POST['access'];
			$e->Save();
			
			$_POST['eid'] = $e->ID;
			break;
			
		default:
			if( !$database->fetchObjectRow( '
				SELECT 
					* 
				FROM 
					SBookEvents 
				WHERE 
					ID = \'' . $_POST['eid'] . '\' 
				ORDER BY 
					ID DESC 
			', false, 'components/events/actions/eventsave.php' ) )
			{
				$e = new dbObject( 'SBookEvents' );
				
				if( $_POST['vid'] > 0 && ( $found = $database->fetchObjectRow( '
					SELECT 
						* 
					FROM 
						SBookEvents 
					WHERE 
						ID = \'' . $_POST['vid'] . '\' 
					ORDER BY 
						ID DESC 
				', false, 'components/events/actions/eventsave.php' ) ) )
				{
					$e->ImageID = $found->ImageID;
				}
				
				$e->UniqueID = UniqueKey();
				$e->Component = 'events';
				$e->Name = $_POST['event'];
				$e->DateStart = date( 'Y-m-d', strtotime( $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['start'] ) );
				$e->DateEnd = date( 'Y-m-d', strtotime( $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['end'] ) );
				$e->UserID = $webuser->ContactID;
				if( $parent && strtolower( $parent->folder->MainName ) != 'profile' )
				{
					$e->CategoryID = $parent->folder->CategoryID;
				}
				$e->DateCreated = date( 'Y-m-d H:i:s' );
				$e->DateModified = date( 'Y-m-d H:i:s' );
				$e->Access = 1;
				$e->Save();
				
				$_POST['eid'] = $e->ID;
			}
			break;
	}
}

// --- HourSlot ---------------------------------------------------------------------------------------------------------

if( $_POST['eid'] > 0 && $_POST['date'] && $_POST['start'] && $_POST['end']  )
{
	// TODO: Add hour slots based on only one data not a start or end unless it's a different type.
	
	switch( $_POST['mode'] )
	{
		case 'extended':
			
			if( $hrs = $database->fetchObjectRows( '
				SELECT ID
				FROM SBookHours
				WHERE ProjectID = \'' . $_POST['eid'] . '\'
				AND Title = "events"
				AND IsDeleted = "0"
				AND
				(
						DateStart < \'' . date( 'Y-m-d 00:00:00.000000', strtotime( $_POST['datestart'] ? $_POST['datestart'] : $_POST['date'] ) ) . '\'
					OR  DateEnd > \'' . date( 'Y-m-d 23:59:59.000000', strtotime( $_POST['dateend'] ? $_POST['dateend'] : $_POST['date'] ) ) . '\' 
				)
				ORDER BY ID ASC 
			', false, 'components/events/actions/eventsave.php' ) )
			{
				foreach( $hrs as $hr )
				{
					$h = new dbObject( 'SBookHours' );
					$h->ID = $hr->ID;
					if( $h->Load() )
					{
						$h->DateStart = date( 'Y-m-d', strtotime( $_POST['datestart'] ? $_POST['datestart'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $h->DateStart ) );
						//$h->DateEnd = date( 'Y-m-d', strtotime( $_POST['dateend'] ? $_POST['dateend'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $h->DateEnd ) );
						$h->DateEnd = date( 'Y-m-d', strtotime( $_POST['datestart'] ? $_POST['datestart'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $h->DateEnd ) );
						$h->DateModified = date( 'Y-m-d H:i:s' );
						$h->Save();
					}
				}
			}
			
			$slots = $database->fetchObjectRows( 'SELECT ID FROM SBookHours WHERE ProjectID = \'' . $_POST['eid'] . '\' AND IsDeleted = "0" ORDER BY ID DESC', false, 'components/events/actions/eventsave.php' );
			
			if( isset( $_POST['users'] ) )
			{
				$usrs = $database->fetchObjectRows( '
					SELECT ID
					FROM SBookContact
					WHERE UserID IN (' . $_POST['users'] . ') 
					AND UserID > 0 
					ORDER BY ID ASC 
				', false, 'components/events/actions/eventsave.php' );
				
				if( $usrs && is_array( $usrs ) )
				{
					foreach( $usrs as $usr )
					{
						if( !$slots ) $slots = array();
						
						if( $usr->ID > 0 )
						{
							if( $hr = $database->fetchObjectRow( '
								SELECT ID
								FROM SBookHours
								WHERE ProjectID = \'' . $_POST['eid'] . '\'
								AND Title = "events" 
								AND UserID = "0"
								AND IsDeleted = "0" 
								ORDER BY ID ASC 
							', false, 'components/events/actions/eventsave.php' ) )
							{
								$h1 = new dbObject( 'SBookHours' );
								$h1->UserID = $usr->ID;
								$h1->Title = 'events';
								$h1->ProjectID = $_POST['eid'];
								//$h1->DateStart = date( 'Y-m-d', strtotime( $_POST['datestart'] ? $_POST['datestart'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['start'] ) );
								//$h1->DateEnd = date( 'Y-m-d', strtotime( $_POST['dateend'] ? $_POST['dateend'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['end'] ) );
								if( !$h1->Load() )
								{
									$h2 = new dbObject( 'SBookHours' );
									$h2->ID = $hr->ID;
									if( $h2->Load() )
									{
										$h2->UserID = $usr->ID;
										if ( $usr->ID == $webuser->ContactID )
										{
											$h2->IsAccepted = 1;
										}
										//$h2->DateStart = date( 'Y-m-d', strtotime( $_POST['datestart'] ? $_POST['datestart'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['start'] ) );
										//$h2->DateEnd = date( 'Y-m-d', strtotime( $_POST['dateend'] ? $_POST['dateend'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['end'] ) );
										//$h2->Access = $_POST['access'];
										$h2->DateModified = date( 'Y-m-d H:i:s' );
										$h2->Save();
										
										if( $h2->ID > 0 && $usr->ID != $webuser->ContactID )
										{
											// TODO: Find a way without relation complexity
											$r = new dbObject( 'SBookNotification' );
											$r->Type = 'events';
											$r->ObjectID = $h2->ID;
											$r->ReceiverID = $usr->ID;
											$r->Load();
											$r->Save();
										}
										
										$slots[] = $usr->ID;
									}
								}
							}
							else
							{
								$h = new dbObject( 'SBookHours' );
								$h->UserID = $usr->ID;
								$h->Title = 'events';
								$h->ProjectID = $_POST['eid'];
								if( !$h->Load() )
								{
									$h->DateStart = date( 'Y-m-d', strtotime( $_POST['datestart'] ? $_POST['datestart'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['start'] ) );
									//$h->DateEnd = date( 'Y-m-d', strtotime( $_POST['dateend'] ? $_POST['dateend'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['end'] ) );
									$h->DateEnd = date( 'Y-m-d', strtotime( $_POST['datestart'] ? $_POST['datestart'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['end'] ) );
									$h->Type = '';
									$h->Role = '';
									$h->Hours = 0;
									$h->IsNight = 0;
									if ( $usr->ID == $webuser->ContactID )
									{
										$h->IsAccepted = 1;
									}
									$h->DateCreated = date( 'Y-m-d H:i:s' );
									$h->DateModified = date( 'Y-m-d H:i:s' );
									$h->Access = $_POST['access'];
									$h->Save();
									
									if( $h->ID > 0 && $usr->ID != $webuser->ContactID )
									{
										// TODO: Find a way without relation complexity
										$r = new dbObject( 'SBookNotification' );
										$r->Type = 'events';
										$r->ObjectID = $h->ID;
										$r->ReceiverID = $usr->ID;
										$r->Load();
										$r->Save();
									}
									
									$slots[] = $usr->ID;
								}
							}
						}
					}
				}
			}
			
			$slots = ( $slots ? ( $_POST['slots'] - count( $slots ) ) : $_POST['slots'] );
			
			if( $slots > 0 )
			{
				for( $a = 0; $a < $slots; $a++ )
				{
					$h = new dbObject( 'SBookHours' );
					$h->UserID = 0;
					$h->Title = 'events';
					$h->Type = '';
					$h->Role = '';
					$h->ProjectID = $_POST['eid'];
					$h->DateStart = date( 'Y-m-d', strtotime( $_POST['datestart'] ? $_POST['datestart'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['start'] ) );
					//$h->DateEnd = date( 'Y-m-d', strtotime( $_POST['dateend'] ? $_POST['dateend'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['end'] ) );
					$h->DateEnd = date( 'Y-m-d', strtotime( $_POST['datestart'] ? $_POST['datestart'] : $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['end'] ) );
					$h->Hours = 0;
					$h->IsNight = 0;
					$h->DateCreated = date( 'Y-m-d H:i:s' );
					$h->DateModified = date( 'Y-m-d H:i:s' );
					$h->Access = $_POST['access'];
					$h->Save();
				}
			}
			break;
			
		default:
			$h = new dbObject( 'SBookHours' );
			if( isset( $_POST['sid'] ) && $_POST['sid'] > 0 )
			{
				$h->ID = $_POST['sid'];
				$h->Load();
			}
			$h->UserID = $_POST['attendee'];
			$h->Title = 'events';
			$h->Type = '';
			$h->Role = $_POST['role'];
			$h->ProjectID = $_POST['eid'];
			$h->DateStart = date( 'Y-m-d', strtotime( $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['start'] ) );
			$h->DateEnd = date( 'Y-m-d', strtotime( $_POST['date'] ) ) . ' ' . date( 'H:i', strtotime( $_POST['end'] ) );
			$h->Hours = 0;
			$h->IsNight = 0;
			if ( $_POST['attendee'] == $webuser->ContactID )
			{
				$h->IsAccepted = 1;
			}
			if ( !$_POST['attendee'] )
			{
				$h->IsAccepted = 0;
			}
			$h->DateCreated = date( 'Y-m-d H:i:s' );
			$h->DateModified = date( 'Y-m-d H:i:s' );
			$h->Access = 1;
			$h->Save();
			
			if( $h->ID > 0 && $_POST['attendee'] > 0 && $_POST['attendee'] != $webuser->ContactID )
			{
				// TODO: Find a way without relation complexity
				$r = new dbObject( 'SBookNotification' );
				$r->Type = 'events';
				$r->ObjectID = $h->ID;
				$r->ReceiverID = $_POST['attendee'];
				$r->Load();
				$r->Save();
			}			
			break;
	}
	
	die( 'ok<!--separate-->' . $h->ID . ' -- slots: ' . $slots );
}

die( 'fail' );

?>
