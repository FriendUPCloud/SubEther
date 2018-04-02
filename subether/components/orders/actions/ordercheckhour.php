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

if ( $_POST['hid'] > 0 && ( $hours = $database->fetchObjectRows( '
	SELECT 
		h.* 
	FROM 
		SBookHours h 
	WHERE 
			h.ID IN (' . $_POST['hid'] . ') 
	ORDER BY 
		h.ID ASC 
' ) ) )
{
	$groupid = UniqueKey();
	
	foreach( $hours as $hour )
	{
		$h = new dbObject( 'SBookHours' );
		$h->ID = $hour->ID;
		if ( $h->Load() )
		{
			if ( $parent->folder->MainName == 'Groups' )
			{
				if ( !$h->IsReady )
				{
					$h->IsReady = $webuser->ContactID;
				}
				
				$h->IsAccepted = ( isset( $_POST['checked'] ) && $_POST['checked'] ? $webuser->ContactID : 0 );
			}
			else
			{
				switch( $_REQUEST['h'] )
				{
					case '1':
						$h->IsAccepted = ( isset( $_POST['checked'] ) && $_POST['checked'] ? $webuser->ContactID : 0 );
						break;
					
					case '2':
						$h->IsAccepted = ( isset( $_POST['checked'] ) && $_POST['checked'] ? $webuser->ContactID : 0 );
						//$h->IsFinished = ( isset( $_POST['checked'] ) && $_POST['checked'] ? 1 : 0 );
						break;
					
					default:
						$h->IsReady = ( isset( $_POST['checked'] ) && $_POST['checked'] ? $webuser->ContactID : 0 );
						break;
				}
			}
			
			if ( isset( $_REQUEST['group'] ) && !$h->GroupID )
			{
				$h->GroupID = $groupid;
			}
			
			$h->DateModified = date( 'Y-m-d H:i:s' );
			$h->Save();
			
			LogUserActivity( $h->UserID, ( $h->IsAccepted ? 'accepted hour' : 'pending hour' ), false, ( $h->IsAccepted ? 'accepted' : 'pending' ), 'SBookHours', $h->ID );
		}
	}
	
	die( 'ok<!--separate-->' . $groupid );
}

die( 'fail' );

?>
