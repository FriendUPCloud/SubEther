
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

// All handlers
var treerootMessageHandlers = {};

// Add to handler list
function addMessageHandler( command, callback )
{
	if( typeof( treerootMessageHandlers[command] ) == 'undefined' )
	{
		treerootMessageHandlers[command] = [];
	}
	treerootMessageHandlers[command].push( callback );
	
	// The function is returned for tracking it later
	return callback;
}

// Remove it by command and function
function removeMessageHandler( command, func )
{
	if( !treerootMessageHandlers[command] ) return;
	var o = [];
	for( var a = 0; a < treerootMessageHandlers[command].length; a++ )
	{
		// Execute handler with data
		if( treerootMessageHandlers[command] != func )
			o.push( treerootMessageHandlers[command] );
	}
	treerootMessageHandlers[command] = o;
}

function sendMessage( msg )
{
	if( window.parent && msg )
	{
		//window.parent.postMessage( JSON.stringify( msg ), '*' );
		window.parent.postMessage( msg, '*' );
	}
}

function isLoaded()
{
	sendMessage( { 'derp': 'localstorage', 'data': { 'type': 'treeroot', 'loaded' : 'true' } } );
}

// Handle messages
window.addEventListener( 'message', function( msg )
{
	//console.log( '// Handle messages' );
	//console.log( msg );
	
	//console.log( msg.data );
	if( !msg.data ) return;
	
	if( msg.data.command )
	{
		switch( msg.data.command )
		{
			case 'account_edit_profile':
				document.location.href = '/profile/';
				break;
			case 'account_settings':
				document.location.href = '/account/';
				break;
			case 'global_settings':
				document.location.href = '/global/';
				break;
			case 'nav_newsfeed':
				document.location.href = '/wall/';
				break;
			case 'nav_messages':
				document.location.href = '/messages/';
				break;
			case 'nav_calendar':
				document.location.href = '/events/';
				break;
			case 'nav_library':
				document.location.href = '/library/';
				break;
			case 'nav_browse':
				document.location.href = '/browse/';
				break;
			case 'nav_bookmarks':
				document.location.href = '/bookmarks/';
				break;
		}
	}
	
	if ( 'localstorage' === msg.data.type )
	{
		console.log( 'set localstorage values' );
		console.log( msg.data );
		
		if( msg.data.keys )
		{
			setBrowserStorage( 'privatekey', decodeURIComponent( msg.data.keys.privatekey ) );
			setBrowserStorage( 'publickey', decodeURIComponent( msg.data.keys.publickey ) );
			setBrowserStorage( 'uniqueid', decodeURIComponent( msg.data.keys.uniqueid ) );
			
			//console.log( decodeURIComponent( msg.data.keys.privatekey ) );
			//console.log( decodeURIComponent( msg.data.keys.publickey ) );
			//console.log( decodeURIComponent( msg.data.keys.uniqueid ) );
			
			console.log( localStorage );
		}
	}
	
	// Temporary stuff ....
	
	if( !msg.data.type ) return;
	
	//var l = msg.data.indexOf( '{' ) >= 0 ? JSON.parse( msg.data ) : false;
	var l = false;
	if( !l || !l.command )
	{
		l = { command: 'default', data: msg.data };
	}
	if( !treerootMessageHandlers[l.command] ) return;
	// Go through all added message handlers for this command!
	for( var a = 0; a < treerootMessageHandlers[l.command].length; a++ )
	{
		// Execute handler with data
		treerootMessageHandlers[l.command][a]( l );
	}
}, false );



// Assign Global Listeners
if ( window.addEventListener )
{
	window.addEventListener ( 'load', isLoaded );
}
else 
{
	window.attachEvent ( 'onload', isLoaded );
}
