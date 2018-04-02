
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

// TODO: Create Automagic with requests, get information about sender and receiver(s), authenticate, then do request and save remote and local

var API = {
	
	// Method defined ( GET, PUT, DELETE ) and ( External, External & Local, Local )
	
	data: { 
		ciduser: false,
		webuser: false,
		session: false
	},
	
	query: function ( url, vars, callback )
	{
		if( url )
		{
			var j = new bajax ();
			j.openUrl ( url, 'post', true );
			
			//console.log( { vars: vars } );
			
			if( vars && typeof( vars ) != 'string' )
			{
				for( var key in vars )
				{
					j.addVar ( key, vars[key] );
				}
			}
			
			j.onload = function ()
			{
				var data = this.getResponseText ();	
			
				if( data )
				{
					try
					{
						var json = JSON.parse( data );
					}
					catch( e )
					{
						var json = false;
					}
					
					if( json )
					{
						if( callback && typeof( callback ) == 'function' )
						{
							callback( json.response, json );
						}
					
						return json;
					}
				}
			
				if( callback && typeof( callback ) == 'function' )
				{
					callback( false, data );
				}
			
				return data;
			}
			j.send ();
		}
		
		return false;
	},
	
	
	
	authenticate: function ( destination, vars, callback )
	{
		// TODO: If you have sessionid no need to reauth unless there is a failed message
		
		if( destination && destination.indexOf( 'http' ) >= 0 )
		{
			API.query( destination + /*( destination.substr( 0, -1 ) != '/' ? '/' : '' ) + */'api-json/v1/authenticate/', vars, callback );
		}
		else
		{
			API.contacts.get( 'localhost', { 'ContactID': destination }, function( res, data )
			{
				console.log( { res: res, data: data } );
				
				if( res == 'ok' )
				{
					if( data.Contacts.NodeUrl )
					{
						var auth = { 
							'UniqueID': data.UniqueID, 
							'Email': data.Email, 
							'Source': 'node' 
						};
						
						API.query( data.Contacts.NodeUrl + 'api-json/v1/authenticate/', auth, callback );
					}
					else
					{
						if( callback && typeof( callback ) == 'function' )
						{
							callback( true );
						}
						
						return true;
					}
				}
				
			} );
		}
	},
	
	contacts: {
		
		// Relations GET, PUT, DELETE
		// Requests  GET, PUT, DELETE
		// Contacts  GET, PUT, DELETE
		
		requests: { 
			
			// Predefined variabled for the various functions, inlcuding type of method ( external, external & local, local )
			
			get: function ( destination, vars, callback )
			{
				if( destination && destination.indexOf( 'http' ) >= 0 )
				{
					// External and local request, require authentication.
					API.query( destination + 'api-json/v1/components/contacts/requests/', vars, callback );
				}
				else
				{
					// Local request
					API.query( '/api-json/v1/components/contacts/requests/', vars, callback );
				}
			},
			
			// Add Allow/Deny and Add Request ...
			
			put: function ( destination, vars, callback )
			{
				if( destination && destination.indexOf( 'http' ) >= 0 )
				{
					// External and local request, require authentication. Only for save requests no need for get and delete requests.
					API.query( destination + 'api-json/v1/components/contacts/requests/', vars );
					API.query( '/api-json/v1/components/contacts/requests/', vars, callback );
				}
				// Third option for local and external save ???
				else
				{
					// Local request
					API.query( '/api-json/v1/components/contacts/requests/', vars, callback );
				}
			},
			
			// Add Delete/Cancel ...
			
			del: function ( destination, vars, callback )
			{
				if( destination && destination.indexOf( 'http' ) >= 0 )
				{
					// External and local request, require authentication. Only for save requests no need for get and delete requests.
					API.query( destination + 'api-json/v1/components/contacts/requests/', vars );
					API.query( '/api-json/v1/components/contacts/requests/', vars, callback );
				}
				// Third option for local and external save ???
				else
				{
					// Local request
					API.query( '/api-json/v1/components/contacts/requests/', vars, callback );
				}
			}
		},
		
		get: function ( destination, vars, callback )
		{
			//destination = ( destination && destination.indexOf( 'http' ) >= 0 ? destination : '/' );
			
			if( destination && destination.indexOf( 'http' ) >= 0 )
			{
				// TODO: Do we need to get contact information from another node ??? if the node syncs it ... maybe, maybe not.
				
				API.query( destination + 'api-json/v1/components/contacts/', vars );
				API.query( '/api-json/v1/components/contacts/', vars, callback );
			}
			else
			{
				// Local request
				API.query( destination + 'api-json/v1/components/contacts/', vars, callback );
			}
		},
		
		put: function (  )
		{
			
		},
		
		del: function (  )
		{
			
		}
		
		// Relations LOAD, SAVE, DELETE
		// Requests  LOAD, SAVE, DELETE
		// Contacts  LOAD, SAVE, DELETE
		
	},
	
	messages: { 
		
		// Messages LOAD, SAVE
		
		// /api-json/v1/messages/
		
		get: function ( vars, callback )
		{
			
			
			destination = ( destination && destination.indexOf( 'http' ) >= 0 ? destination : '/' );
			
			
			
			API.query( destination + 'api-json/v1/components/chat/messages/', vars, callback );
		},
		
		put: function ( vars, callback )
		{
			vars['SessionID'] = ( API.data.session ? API.data.session : '' );
			
			API.query( 'api-json/v1/components/contacts/', { 'SessionID': vars.SessionID, 'ContactID': vars.ContactID }, function( res1, data1 )
			{
				console.log( { res1: res1, data1: data1, obj1: { 'SessionID': vars.SessionID, 'ContactID': vars.ContactID } } );
				
				if( res1 == 'ok' )
				{
					if( data1 && data1.items.Contacts[0].NodeUrl && data1.items.Contacts[0].NodeMainID )
					{
						API.query( 'api-json/v1/components/chat/post/', vars );
						
						API.query( data1.items.Contacts[0].NodeUrl + 'api-json/v1/authenticate/', 
						{ 
							'UniqueID': data1.items.UniqueID, 
							'Email': data1.items.Email, 
							'Source': 'node' 
						}, 
						function( res2, data2 )
						{
							console.log( { res2 : res2, data2 : data2, obj2: { 
								'UniqueID': data1.items.UniqueID, 
								'Email': data1.items.Email, 
								'Source': 'node' 
							} } );
							
							if( data2.Token && getBrowserStorage( 'privatekey' ) )
							{
								var sessionid = fcrypt.decryptRSA( data2.Token, getBrowserStorage( 'privatekey' ) );
								
								console.log( 'decrypted: ', sessionid );
								
								if( sessionid )
								{
									vars['SessionID'] = sessionid;
									vars['ContactID'] = data1.items.Contacts[0].NodeMainID;
									
									API.query( data1.items.Contacts[0].NodeUrl + 'api-json/v1/components/chat/post/', vars, callback );
								}
							}
							
						} );
						
					}
					else
					{
						API.query( 'api-json/v1/components/chat/post/', vars, callback );
					}
				}
				else
				{
					if( callback && typeof( callback ) == 'function' )
					{
						callback( false );
					}
				}
				
			} );
		}
		
	}
	
}



// Test ....
if( 1!=1 && confirm( 'Do you want to start long-polling test?' ) )
{
	var vars = {
		'SessionID'   : ( API.data.session ? API.data.session : 'b3fbc6b56408b135a9e41707afdd71bc' ),
		'ContactID'   : '2',
		'LastActivity': ''
	};
	
	API.query( 'api-json/v1/components/chat/messages/', vars, function( res, data )
	{
		
		console.log( { res: res, data: data, vars: vars } );
		alert( 'bla bla bla' );
		
	} );
}

