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

if( $_POST['hid'] > 0 && ( $hour = $database->fetchObjectRow( '
	SELECT 
		h.* 
	FROM 
		SBookHours h 
	WHERE 
			h.ID = \'' . $_POST['hid'] . '\' 
	ORDER BY 
		h.ID ASC 
' ) ) )
{
	$h = new dbObject( 'SBookHours' );
	$h->ID = $hour->ID;
	if ( $h->Load() )
	{
		$del = $h->ID;
		$usr = $h->UserID;
		
		//$h->IsDeleted = 1;
		//$h->Save();
		$h->Delete();
		
		LogUserActivity( $usr, 'delete hour', false, 'delete', 'SBookHours', $del );
	}
	
	die( 'ok<!--separate-->' . $del );
}

die( 'fail' );

?>
