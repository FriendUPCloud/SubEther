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

// --- HourSlot ---------------------------------------------------------------------------------------------------------

if( $_POST['sid'] > 0 && $webuser->ContactID )
{
	$h = new dbObject( 'SBookHours' );
	$h->ID = $_POST['sid'];
	if( $h->Load() )
	{
		$h->UserID = 0;
		$h->IsAccepted = 0;
		$h->Save();
		
		// Update relation
		if( $h->ID > 0 )
		{
			$r = new dbObject( 'SBookNotification' );
			$r->Type = 'events';
			$r->ObjectID = $h->ID;
			$r->ReceiverID = $webuser->ContactID;
			if( $r->Load() )
			{
				$r->IsAccepted = -1;
				$r->Save();
			}
		}
	}
	
	die( 'ok<!--separate-->' . $h->ID );
}

die( 'fail' );

?>
