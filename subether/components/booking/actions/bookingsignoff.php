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
			AND e.IsDeleted = "0" 
		ORDER BY
			e.ID DESC 
	' ) )
	{
		// If we find taken slots connected to the userid continue else fail
		if ( $hours = $database->fetchObjectRows( $q2 = '
			SELECT 
				h.* 
			FROM 
				SBookHours h  
			WHERE 
					h.ProjectID = \'' . $_POST['eid'] . '\'
				AND h.UserID = \'' . $webuser->ContactID . '\' 
				AND h.DateStart >= \'' . $_POST['fromdate'] . '\' 
				AND h.DateEnd <= \'' . $_POST['todate'] . '\' 
				AND h.IsDeleted = "0" 
			ORDER BY 
				h.ID ASC 
		' ) )
		{
			foreach( $hours as $hr )
			{
				$h = new dbObject( 'SBookHours' );
				$h->ID = $hr->ID;
				if( $h->Load() )
				{
					$h->IsDeleted = 1;
					$h->Save();
				}
			}
			
			die( 'ok<!--separate-->' . $_POST['eid'] );
		}
	}
}

die( 'fail' );

?>
