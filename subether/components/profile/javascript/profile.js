
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

function addContact( cid, ele )
{
	if( !cid || !ele ) return;
	
	getContactInfo( cid, function( ret )
	{ 
	
		if( ret )
		{
			console.log( 'ret: ', ret );
			
			if( ret.cuser && ret.cuser.NodeID > 0 && ret.cuser.NodeMainID > 0 )
			{
				if( ret.cuser.Url && ret.webuser )
				{
					var args1 = {
						'UniqueID' : ret.webuser.UniqueID, 
						'Email'    : ret.webuser.Email, 
						'Source'   : 'node' 
					};
					
					API.authenticate( ret.cuser.Url, args1, function( res, data )
					{ 
						
						console.log( { res : res, data : data } );
						
						if ( data.Token && getBrowserStorage( 'privatekey' ) )
						{
							var sessionid = fcrypt.decryptRSA( data.Token, getBrowserStorage( 'privatekey' ) );
							
							console.log( 'decrypted: ', sessionid );
							
							if( sessionid )
							{
								var args2 = {
									'SessionID' : sessionid, 
									'ContactID' : ret.cuser.NodeMainID 
								};
								
								API.contacts.requests( ret.cuser.Url, args2, function( res, data )
								{
									
									console.log( { res : res, data : data } );
									
									// TODO: Add storing of the data on this server
									
									// TODO: Only use API from now on ...
									
								} );
							}
						}
						
					} );
				}
			}
			else
			{
				var j = new bajax ();
				j.openUrl ( getPath() + '?component=profile&action=addcontact', 'post', true );
				j.addVar ( 'cid', cid );
				j.onload = function ()
				{
					var r = this.getResponseText ().split ( '<!--separate-->' );	
					if ( r[0] == 'ok' )
					{
						ele.parentNode.innerHTML = '<button><span>Pending</span></button>';
					}
				}
				j.send ();
			}
		}
	
	} );
}

function removeContact( ele )
{
	//if( !ele ) return;
	if( confirm( 'Are you sure?' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=profile&action=removecontact', 'post', true );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				//ele.parentNode.innerHTML = '<button onclick="addContact( \'' + r[1] + '\', this )"><span>Add Contact</span></button>';
				document.location = 'home/';
			}
		}
		j.send ();
	}
}

function getContactInfo( cid, callback )
{
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=profile&function=userinfo', 'post', true );
	
	if( cid ) 
	{
		j.addVar ( 'cid', cid );
	}
	
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		
		if ( r[0] == 'ok' && r[1] )
		{
			var json = JSON.parse( r[1] );
			
			if( json )
			{
				if( callback && typeof( callback ) == 'function' )
				{
					callback( json );
				}
				
				return json;
			}
			
			return r[1];
		}
	}
	j.send ();
}

function refreshAvatar( i )
{
	if( !i || !ge( 'Avatar' ) ) return;
	ge( 'Avatar' ).innerHTML = i;
}

function refreshCover( i )
{
	if( !i || !ge( 'MainImage' ) ) return;
	ge( 'MainImage' ).innerHTML = i;
}

function profileOptions()
{
	if( !ge( 'ProfileOptionsBox' ) ) return;
	
	if( ge( 'ProfileOptionsBox' ).className.indexOf( 'open' ) >= 0 )
	{
		ge( 'ProfileOptionsBox' ).className = '';
		ge( 'ProfileOptionsBox' ).getElementsByTagName( 'div' )[0].innerHTML = '';
	}
	else
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=profile&function=dropdown', 'get', true );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				ge( 'ProfileOptionsBox' ).getElementsByTagName( 'div' )[1].innerHTML = r[1];
				ge( 'ProfileOptionsBox' ).className = 'open';
			}
		}
		j.send ();
	}
}

function deleteUser()
{
	if( confirm( 'Are you sure you want to delete your account?' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=profile&action=deleteuser', 'post', true );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				document.location = 'en/home/';
			}
		}
		j.send ();
	}
}
