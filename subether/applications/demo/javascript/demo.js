
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

// GET -----------------------------------------------------------------------------------------------------------------

function loadDemo (  )
{
	xhr = new XMLHttpRequest();
	xhr.open( 'GET', 'api-json/v2/demo/', true );
	xhr.onreadystatechange = function (  )
	{ 
		if ( xhr.readyState === 4 && xhr.status === 200 )
		{
			// Get response data
			
			try
			{
				var d = JSON.parse( xhr.responseText );
				
				if( d && d.response == 'ok' )
				{
					// Insert data from api to html
					ge( 'DemoContent' ).innerHTML  = '<div id="DemoContentInner" contenteditable="true">' + d.items.data + '</div> ';
					ge( 'DemoContent' ).innerHTML += '<button onclick="saveDemo()">Save</button> ';
					ge( 'DemoContent' ).innerHTML += '<button onclick="deleteDemo()">Clear</button> ';
				}
				else
				{
					console.log( d );
				}
			}
			catch( e ) 
			{
				// Show raw response data in console
				
				console.log( { err: e, ret: xhr.responseText } );
			}
			
		}

	}; 
	xhr.setRequestHeader( 'Content-Type', 'application/json' );
	xhr.send(  );
}

loadDemo();

// PUT -----------------------------------------------------------------------------------------------------------------

function saveDemo (  )
{
	if( ge( 'DemoContentInner' ) )
	{
		var json = {
			'data': encodeURIComponent( ge( 'DemoContentInner' ).innerHTML )
		};
		
		xhr = new XMLHttpRequest();
		xhr.open( 'PUT', 'api-json/v2/demo/', true );
		xhr.onreadystatechange = function (  )
		{ 
			if ( xhr.readyState === 4 && xhr.status === 200 )
			{
				// Get response data
				
				try
				{
					var d = JSON.parse( xhr.responseText );
			
					if( d && d.response == 'ok' )
					{
						console.log( d.info );
					}
					else
					{
						console.log( d );
					}
				}
				catch( e ) 
				{
					// Show raw response data in console
			
					console.log( { err: e, ret: xhr.responseText } );
				}
			
			}
		
		}; 
		xhr.setRequestHeader( 'Content-Type', 'application/json' );
		xhr.send( JSON.stringify( json ) );
	}
}

// DELETE --------------------------------------------------------------------------------------------------------------

function deleteDemo (  )
{
	xhr = new XMLHttpRequest();
	xhr.open( 'DELETE', 'api-json/v2/demo/', true );
	xhr.onreadystatechange = function (  )
	{ 
		if ( xhr.readyState === 4 && xhr.status === 200 )
		{
			// Get response data
			
			try
			{
				var d = JSON.parse( xhr.responseText );
			
				if( d && d.response == 'ok' )
				{
					loadDemo();
					
					console.log( d.info );
				}
				else
				{
					console.log( d );
				}
			}
			catch( e ) 
			{
				// Show raw response data in console
			
				console.log( { err: e, ret: xhr.responseText } );
			}
			
		}

	}; 
	xhr.setRequestHeader( 'Content-Type', 'application/json' );
	xhr.send(  );
}

