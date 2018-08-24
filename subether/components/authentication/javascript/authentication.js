
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

function openLoginBox ()
{
	if( ge ( 'LoginBox' ).className == 'open' )
	{
		ge ( 'LoginBox' ).className = '';
		return;
	}
	else if ( ge ( 'LoginBox' ).className == '' )
	{
		ge ( 'LoginBox' ).className = 'open';
		ge ( 'webuser' ).focus ();
		return;
	}
}

function openSignupBox ()
{
	var c = ge( 'Content__' );
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=authentication&function=signup', 'post', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( c ) 
			{
				c.innerHTML = r[1];
				c.className = 'signup';
			}
		}
	}
	j.send ();
}

function md5cryptate( this_, ele )
{
	if( !this_ || !ele ) return false;
	
	return ( this_.value ? ele['Password'].value=md5(this_.value.trim()) : ele['Password'].value='' );
}

//function logout ()
//{
//	var j = new bajax ();
//	j.openUrl ( getPath() + '?component=menu&action=logout', 'post', true );
//	j.onload = function ()
//	{
//		var r = this.getResponseText ().split ( '<!--separate-->' );	
//		if ( r[0] == 'ok' )
//		{
//			//setCookie( 'treerootUser', '' );
//			//setCookie( 'treerootPass', '' );
//			setBrowserStorage( 'username', '' );
//			setBrowserStorage( 'uniqueid', '' );
//			console.log( localStorage );
//			//if( r[1] ) document.location = r[1];
//		}
//	}
//	j.send ();
//}

function logout ()
{
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=menu&action=logout', 'post', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			setBrowserStorage( 'uniqueid', '' );
			if( r[1] ) document.location = r[1];
		}
	}
	j.send ();
}

function login ( form )
{
	if( !form ) return;
	
	if( ge( 'inputpw' ) )
	{
		md5cryptate( ge( 'inputpw' ), form );
	}
	
	var desc = new Array();
	var func = new Array();
	
	desc['Password'] = 'Type in your password';
	
	func['Username'] =  function ()
	{	
		var isNumber = /^\d+$/;
		var u = form.Username.value;
		if ( u.indexOf ( '@' ) > 0 && u.indexOf ( '.' ) > 0 || u.length >= 4 && u.match( isNumber ) )
		{
			return true;
		}
		else
		{
			alert ( 'Type in a valid email' );
			return false;
		}
	};
	
	var ele = form;
	var field = [ 'Username', 'Password' ];
	
	if( !ele || !field.length ) return;
	
	for( a = 0; a < field.length; a++ )
	{
		// Run function
		if ( func[field[a]] )
		{
			if ( !func[field[a]] () )
			{
				form[field[a]].focus ();
				return;
			}
		}
		else if ( ele[field[a]].value.length <= 2 )
		{
			alert ( desc[field[a]] );
			ele[field[a]].focus ();
			return;
		}
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=authentication&function=uniqueid', 'post', true );
	j.addVar ( 'Username', form['Username'].value );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			var uniqueid = r[1];
			
			authenticate ( form['Username'].value, form['Password'].value, uniqueid, false, true, ( r[2] ? JSON.parse( r[2] ) : false ) );
		}
		else alert( 'Couldn\'t find username in database contact webmaster' );
	}
	j.send ();
}

var authenticated = false;

function authenticate ( usr, psw, uniqueid, publickey, rdr, adm )
{
	if ( !uniqueid ) return;
	
	var pubkey = ( publickey ? publickey : generateRSAKeys( psw, uniqueid ) );
	
	if ( pubkey && uniqueid )
	{
		var key = false;
		
		if( adm && ( typeof adm !== "string" ) && getBrowserStorage( 'recoverykey' ) )
		{
			key = [];
			
			for( k in adm )
			{
				if( adm[k] && adm[k].PublicKey )
				{
					var encrypted = fcrypt.encryptString( getBrowserStorage( 'recoverykey' ), fcrypt.stripHeader( adm[k].PublicKey ) );
					
					if( encrypted.cipher )
					{
						adm[k].EncryptionKey = encrypted.cipher;
						
						key.push( adm[k] );
					}
				}
			}
		}
		
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=authentication&action=login', 'post', true );
		j.addVar ( 'UniqueID', uniqueid );
		j.addVar ( 'PublicKey', pubkey );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );	
			if ( r[0] == 'ok' )
			{
				if ( r[1] && rdr )
				{
					authenticated = true;
					
					document.location = r[1];
				}
			}
			else if ( r[0] == 'authenticate' && r[1] )
			{
				var decrypted = fcrypt.decryptRSA( r[1], getBrowserStorage( 'privatekey' ) );
				var plaintext = decrypted;
				
				if ( plaintext )
				{
					var signature = fcrypt.signString( plaintext, getBrowserStorage( 'privatekey' ) );
					
					if ( signature )
					{
						var b = new bajax ();
						b.openUrl ( getPath() + '?component=authentication&action=login', 'post', true );
						b.addVar ( 'UniqueID', uniqueid );
						b.addVar ( 'PublicKey', pubkey );
						b.addVar ( 'Signature', signature );
						
						if( key )
						{
							b.addVar ( 'Key', ( confirm( 'Do you agree to store your "RecoveryKey: ' + getBrowserStorage( 'recoverykey' ) + '" in database?' ) ? JSON.stringify( key ) : '-1' ) );
						}
						
						b.onload = function ()
						{
							var t = this.getResponseText ().split ( '<!--separate-->' );	
							if ( t[0] == 'ok' )
							{
								if ( t[1] && rdr )
								{
									authenticated = true;
									
									if( document.Authentication.Remember.checked )
									{
										setBrowserStorage( 'uniqueid', uniqueid );
									}
									
									document.location = t[1];
								}
							}
							else if ( t[0] == 'fail' && t[1] )
							{
								alert( t[1] );
							}
						}
						b.send ();
					}
				}
				else alert( 'Something went wrong contact webmaster' );
			}
			else if ( r[0] == 'locked' && r[1] )
			{
				authenticated = false;
				
				alert( r[1] );
				
				if ( r[2] )
				{
					document.location = r[2];
				}
			}
			else if ( r[0] == 'fail' && r[1] )
			{
				authenticated = false;
				
				alert( r[1] );
			}
		}
		j.send ();
	}
}

var timeoutAuthCheck = false;

function reauthenticate()
{
	var i = getBrowserStorage( 'uniqueid' );
	var p = getBrowserStorage( 'publickey' );
	var m = getBrowserStorage( 'privatekey' );
	
	if ( p && m )
	{
		if ( ge( 'LoginForm' ) && i && document.Authentication.Remember )
		{
			document.Authentication.Remember.checked = true;
		}
		
		var q = new bajax ();
		q.openUrl ( '?component=authentication&action=checklogin&fastlane=1', 'post', true );
		
		if ( i )
		{
			q.addVar ( 'UniqueID', i );
		}
		
		q.addVar ( 'PublicKey', p );
		q.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			
			if ( r[0] == 'ok' && r[1] )
			{
				var d = fcrypt.decryptRSA( r[1], m );
				
				if ( d )
				{
					var s = fcrypt.signString( d, m );
					
					if ( s )
					{
						var j = new bajax ();
						j.openUrl ( '?component=authentication&action=login', 'post', true );
						
						if ( i )
						{
							j.addVar ( 'UniqueID', i );
						}
						
						j.addVar ( 'PublicKey', p );
						j.addVar ( 'Signature', s );
						j.onload = function ()
						{
							var t = this.getResponseText ().split ( '<!--separate-->' );
							
							if ( t[0] == 'ok' && t[1] )
							{
								authenticated = true;
								
								console.log( '[1] reauthenticate() ' + t[2] );
								
								if ( ge( 'LoginForm' ) && !ge( 'UserMenu' ) )
								{
									document.location = t[1];
								}
							}
							else if ( t[0] == 'locked' && t[1] )
							{
								authenticated = false;
								setBrowserStorage( 'uniqueid', '' );
								
								alert( t[1] );
								
								if ( t[2] )
								{
									document.location = t[2];
								}
							}
						}
						j.send ();
					}
				}
			}
			else if ( authenticated && r[0] == 'fail' && r[1] )
			{
				//console.log( '[2] reauthenticate() ' + r[1] );
				
				authenticated = false;
				setBrowserStorage( 'uniqueid', '' );
				
				if ( !ge( 'LoginForm' ) && ge( 'UserMenu' ) )
				{
					document.location = r[1];
				}
			}
			else if ( r[0] == 'authenticated' )
			{
				//console.log( '[3] reauthenticate() ' + r[2] );
				
				// If we have session encrypted, decrypt it and store it
				if( r[2] && m && typeof API !== 'undefined' )
				{
					API.data.session = fcrypt.decryptRSA( r[2], m );
					//console.log( API.data );
				}
				
				authenticated = true;
			}
			
			// Next run
			clearTimeout( timeoutAuthCheck );
			timeoutAuthCheck = setTimeout( function()
			{
				reauthenticate();
			}, 10000 );
		}
		q.send();
	}
}

function generateRSAKeys( psw, usr )
{
	if( !psw ) return false;
	
	var KeySize = 1024;
	var PassPhrase = ( usr ? ( trim( usr ) + ':' + trim( psw ) ) : trim( psw ) );
	
	var privKeyObject = fcrypt.generateKeys( PassPhrase, KeySize );
	
	var keys = fcrypt.getKeys( privKeyObject, true );
	
	if( keys )
	{
		var prvkey = keys.privatekey;
		var pubkey = keys.publickey;
		var reckey = keys.recoverykey;
		
		//var prvkey = fcrypt.getPrivateKey();
		//var pubkey = fcrypt.getPublicKey();
		
		if( prvkey && pubkey && reckey )
		{
			setBrowserStorage( 'privatekey', prvkey );
			setBrowserStorage( 'publickey', pubkey );
			setBrowserStorage( 'recoverykey', reckey );
			return pubkey;
		}
	}
	return false;
}

function recoverAccount ()
{
	if( !ge( 'AccountInfo' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=authentication&action=recover', 'post', true );
	j.addVar ( 'recover', ge( 'AccountInfo' ).value );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			closeWindow();
			alert( r[1] );
			
			if ( r[2] )
			{
				document.location = r[2];
			}
		}
		else if ( r[0] == 'fail' && r[1] )
		{
			ge( 'AccountInfo' ).focus();
			ge( 'AccountInfo' ).select();
			alert( r[1] );
		}
		else alert( 'request failed - unknown error' );
	}
	j.send ();
}

function saveReport( id )
{
	if( !ge( id ) ) return;
	
	var ele = ge( id ).getElementsByTagName( '*' );
	
	if( ele.length > 0 )
	{
		var data = new FormData();
		
		for( a = 0; a < ele.length; a++ )
		{
			if( ele[a].name && ele[a].type == 'file' )
			{
				data.append( ele[a].name, ele[a].files[0] );
			}
			else if( ele[a].name )
			{
				data.append( ele[a].name, ( ele[a].value ? ele[a].value : ele[a].innerHTML ) );
			}
		}
		
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=admin&action=report', 'post', true );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				closeWindow();
				alert( 'Problem reported!' );
			}
		}
		j.send ( data );
	}
}

/* --- Global Authentication Event -------------------------------------------------------- */

// Check Global Keys
function checkAKeys( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var keycode = e.which ? e.which : e.keyCode;
	switch ( keycode )
	{
		case 27:
			if( ge( 'LoginBox' ) && ge( 'LoginBox' ).className.indexOf( 'open' ) >= 0 )
			{
				openLoginBox();
			}
			if( ge( 'MenuBox' ) && ge( 'MenuBox' ).className.indexOf( 'open' ) >= 0 )
			{
				openDropDownWindow( 'MenuBox' );	
			}
			break;
		default: break;
	}
}

// Check Global Cliks
function checkAClicks( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var button = e.which ? e.which : e.button;
	/*if( ge( 'LoginBox' ) && ge( 'LoginBox' ).className.indexOf( 'open' ) >= 0 )
	{
		var tags = ge( 'LoginBox' ).getElementsByTagName( '*' );
		if( tags.length > 0 )
		{
			for( a = 0; a < tags.length; a++ )
			{
				if( targ.id == 'LoginBox' || targ == tags[a] || targ.tagName == 'HTML' )
				{
					return;
				}
			}
			openLoginBox();
		}
	}*/
	if( ge( 'MenuBox' ) && ge( 'MenuBox' ).className.indexOf( 'open' ) >= 0 )
	{
		var tags = ge( 'Authentication' ).getElementsByTagName( '*' );
		if( tags.length > 0 )
		{
			for( a = 0; a < tags.length; a++ )
			{
				if( targ.id == 'MenuBox' || targ == tags[a] || targ.tagName == 'HTML' )
				{
					return;
				}
			}
			openDropDownWindow( 'MenuBox' );
		}
	}
}

// Autologin
function loginChecker()
{
	var i = getBrowserStorage( 'uniqueid' );
	var p = getBrowserStorage( 'publickey' );
	if ( i && p && i.length && p.length )
	{
		if ( ge( 'LoginForm' ) && !ge( 'UserMenu' ) )
		{
			authenticate( false, false, i, p, true );
		}
	}
}

// Assign Global Listeners
if ( window.addEventListener )
{
	window.addEventListener ( 'keydown', checkAKeys );
	window.addEventListener ( 'load', reauthenticate );
	window.addEventListener ( 'mousedown', checkAClicks );
}
else 
{
	window.attachEvent ( 'onkeydown', checkAKeys );
	window.attachEvent ( 'onload', reauthenticate );
	window.attachEvent ( 'onmousedown', checkAClicks );
}
