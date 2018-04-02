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

if( $_POST['jid'] > 0 )
{
	$j = new dbObject( 'SBookCronJobs' );
	$j->ID = $_POST['jid'];
	if ( $j->Load() )
	{
		// Stop cronscript
		if ( $j->IsActive == 1 )
		{
			$j->IsRunning = 0;
			$j->IsActive = 0;
			$j->Save();
			
			die( 'ok<!--separate-->' . $j->ID );
		}
		
		// Start cronscript
		if ( $j->IsActive == 0 && $j->IsMaintenance == 0 )
		{
			$j->LastExec = '0000-00-00 00:00:00';
			$j->Error = '';
			$j->IsRunning = 0;
			$j->IsActive = 1;
			$j->Save();
			
			die( 'ok<!--separate-->' . $j->ID );
		}
	}
}

die( 'fail' );

?>
