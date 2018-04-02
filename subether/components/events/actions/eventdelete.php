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

if( $_POST['sid'] > 0 )
{
	// --- Delete this hourslot by ID
	
	$h = new dbObject( 'SBookHours' );
	if( $h->Load( $_POST['sid'] ) )
	{
		// Find and delete relations
		if( $rel = $database->fetchObjectRows( '
			SELECT 
				* 
			FROM 
				SBookNotification
			WHERE 
					ObjectID = \'' . $h->ID . '\'
				AND Type = "events" 
			ORDER BY 
				ID DESC 
		', false, 'components/events/actions/eventdelete.php' ) )
		{
			foreach( $rel as $re )
			{
				$r = new dbObject( 'SBookNotification' );
				if( $r->Load( $re->ID ) )
				{
					$r->Delete();
				}
			}
		}
		
		$h->IsDeleted = 1;
		$h->Save();
	}
	
	// --- Delete this event by ID if there is no hourslots connected to it
	
	if( $_POST['eid'] > 0 && !$database->fetchObjectRows( '
		SELECT 
			h.*
		FROM 
			SBookHours h
		WHERE 
				h.ProjectID = \'' . $_POST['eid'] . '\' 
			AND h.IsDeleted = "0" 
		ORDER BY 
			h.ID DESC 
	', false, 'components/events/actions/eventdelete.php' ) )
	{
		$e = new dbObject( 'SBookEvents' );
		if( $e->Load( $_POST['eid'] ) )
		{
			$e->IsDeleted = 1;
			$e->Save();
		}
	}
	
	die( 'ok<!--separate-->' );
}
else if( $_POST['eid'] > 0 )
{
	// --- Delete this event by ID
	
	$e = new dbObject( 'SBookEvents' );
	if( $e->Load( $_POST['eid'] ) )
	{
		if( $hours = $database->fetchObjectRows( '
			SELECT 
				h.*
			FROM 
				SBookHours h
			WHERE 
					h.ProjectID = \'' . $_POST['eid'] . '\' 
				AND h.IsDeleted = "0" 
			ORDER BY 
				h.ID DESC 
		', false, 'components/events/actions/eventdelete.php' ) )
		{
			foreach( $hours as $hr )
			{
				// --- Delete this hourslot by ID
				
				$h = new dbObject( 'SBookHours' );
				if( $h->Load( $hr->ID ) )
				{
					// Find and delete relations
					if( $rel = $database->fetchObjectRows( '
						SELECT 
							* 
						FROM 
							SBookNotification
						WHERE 
								ObjectID = \'' . $h->ID . '\'
							AND Type = "events" 
						ORDER BY 
							ID DESC 
					', false, 'components/events/actions/eventdelete.php' ) )
					{
						foreach( $rel as $re )
						{
							$r = new dbObject( 'SBookNotification' );
							if( $r->Load( $re->ID ) )
							{
								$r->Delete();
							}
						}
					}
					
					$h->IsDeleted = 1;
					$h->Save();
				}
			}
		}
		
		$e->IsDeleted = 1;
		$e->Save();
	}
	
	die( 'ok<!--separate-->' );
}

die( 'fail' );

?>
