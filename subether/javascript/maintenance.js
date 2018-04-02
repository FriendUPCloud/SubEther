
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

var MaintenanceStarting = true;
var MaintenanceRunning = true;

// Adds a queue action to the queue
function maintenance ()
{
	var q = new jaxqueue ( 'maintenance' );
	q.addUrl ( '?global=true&function=maintenance' );
	q.onload ( function ( data )
	{
		var r = data.split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			if( ge( 'MaintenanceMode__' ) )
			{
				if( ge( 'MaintenanceMode__' ).style.visibility != 'hidden' )
				{
					ge( 'MaintenanceMode__' ).style.visibility = 'hidden';
					ge( 'MaintenanceMode__' ).style.zIndex = '-999';
				}
			}
			
			if( r[1] && MaintenanceStarting )
			{
				alert( r[1] );
				MaintenanceStarting = false;
			}
		}
		else if( r[0] == 'running' )
		{
			if( ge( 'MaintenanceMode__' ) )
			{
				if( ge( 'MaintenanceMode__' ).style.visibility != 'visible' )
				{
					ge( 'MaintenanceMode__' ).style.visibility = 'visible';
					ge( 'MaintenanceMode__' ).style.zIndex = '999';
				}
			}
			
			if( r[1] && MaintenanceRunning )
			{
				alert( r[1] );
				MaintenanceRunning = false;
			}
		}
		else
		{
			if( ge( 'MaintenanceMode__' ) )
			{
				if( ge( 'MaintenanceMode__' ).style.visibility != 'hidden' )
				{
					ge( 'MaintenanceMode__' ).style.visibility = 'hidden';
					ge( 'MaintenanceMode__' ).style.zIndex = '-999';
				}
			}
			
			MaintenanceStarting = true;
			MaintenanceRunning = true;
		}
	} );
	q.save();
}

maintenance();
