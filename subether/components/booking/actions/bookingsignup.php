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

if( $_POST['eid'] && $_POST['slots'] && $_POST['amount'] && $_POST['fromdate'] && $_POST['todate'] )
{
	$_POST['fromdate'] 	= date( 'Y-m-d 00:00:00.000000', $_POST['fromdate'] );
	$_POST['todate'] 	= date( 'Y-m-d 23:59:59.000000', $_POST['todate'] );
	
	// If we find event continue else fail
	if ( $ev = $database->fetchObjectRow( $q1 = '
		SELECT 
			e.* 
		FROM 
			SBookEvents e 
		WHERE 
				e.ID = \'' . $_POST['eid'] . '\' 
			AND e.Slots >= ' . $_POST['slots'] . ' 
			AND e.Limit >= ' . $_POST['amount'] . '
			AND e.IsDeleted = "0" 
		ORDER BY
			e.ID DESC 
	' ) )
	{
		// If we find a free slot continue else fail
		if ( !$hr = $database->fetchObjectRow( $q2 = '
			SELECT 
				h.* 
			FROM 
				SBookHours h  
			WHERE 
					h.ProjectID = \'' . $_POST['eid'] . '\' 
				AND h.DateStart >= \'' . $_POST['fromdate'] . '\' 
				AND h.DateEnd <= \'' . $_POST['todate'] . '\' 
				AND h.IsDeleted = "0" 
			ORDER BY 
				h.ID ASC 
		' ) )
		{
			for( $a = 0; $a < $_POST['slots']; $a++ )
			{
				$h = new dbObject( 'SBookHours' );
				$h->Title = 'booking';
				$h->ProjectID = $_POST['eid'];
				$h->UserID = $webuser->ContactID;
				$h->DateStart = date( 'Y-m-d', strtotime( $_POST['fromdate'] ) ) . ' ' . ( date( 'H:i:s', strtotime( $ev->DateStart ) ) > 0 ? date( 'H:i:s', strtotime( $ev->DateStart ) ) : '14:00:00' );
				$h->DateEnd = date( 'Y-m-d', strtotime( $_POST['todate'] ) ) . ' ' . ( date( 'H:i:s', strtotime( $ev->DateEnd ) ) > 0 ? date( 'H:i:s', strtotime( $ev->DateEnd ) ) : '12:00:00' );
				$h->DateCreated = date( 'Y-m-d H:i:s' );
				$h->DateModified = date( 'Y-m-d H:i:s' );
				$h->Access = $ev->Access;
				$h->Save();
			}
			
			die( 'ok<!--separate-->' . $_POST['eid'] );
		}
	}
}

die( 'fail' );

?>
