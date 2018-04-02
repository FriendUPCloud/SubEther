
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

// TODO: Add check so it doesn't do the same over again, only unique notifications ...

//TODO: Beautify the notify window...
function desktopNotify( title, msg, ic )
{
	// Let's check if the browser supports notifications
	if ( !( 'Notification' in window ) || !title )
	{
		return;
	}
	
	if ( typeof( Notification ) != "undefined" && Notification.hasOwnProperty( 'requestPermission' ) )
	{
		var options = {
			body: msg,
			icon: ic
		}
		
		// Let's check whether notification permissions have already been granted
		if ( Notification && Notification.permission === 'granted' )
		{
			// If it's okay let's create a notification
			//var notification = new Notification( title, options );
			standardNotification( title, options );
		}
		
		// Otherwise, we need to ask the user for permission
		else if ( Notification && Notification.permission !== 'denied' )
		{
			Notification.requestPermission( function( status )
			{
				// Change based on user's decision
				if ( Notification.permission !== status )
				{
					Notification.permission = status;
				}
				
				// If the user accepts, let's create a notification
				if ( status === 'granted' )
				{
					//var notification = new Notification( title, options );
					standardNotification( title, options );
				}
				else
				{
					chromeNotification( title, options );
				}
			});
		}
		else
		{
			chromeNotification( title, options );
		}
	}
	else console.log( 'desktopNotify is disabled' );
	
	return;
}

function standardNotification( title, options )
{
	if ( title && options )
	{
		var notification = new Notification( title, options );
	}
}

function chromeNotification( title, options )
{
	console.log( 'chromeNotification needs compatible code' );
	
	return;
	
	if ( title && options )
	{
		// TODO: Add service worker for this.
		
		navigator.serviceWorker.register( 'sw.js' );
		
		navigator.serviceWorker.ready.then( function( registration )
		{
			registration.showNotification( title,
			{
				body: options.body,
				icon: options.icon,
				vibrate: [200, 100, 200, 100, 200, 100, 200],
				tag: 'vibration-sample'
			});
		});
	}
}
