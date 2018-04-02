
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

var approved = new Array();

function signUp()
{
	var desc = new Array();
	
	desc['Email'] = 'Email is required';
	
	var ele = ge( 'SignupForm' );
	var field = [ 'Email' ];
	
	if( !ele || !ele.action || !field.length ) return;
	
	for( a = 0; a < field.length; a++ )
	{
		if ( ele[field[a]].value.length <= 2 )
		{
			alert ( desc[field[a]] );
			ele[field[a]].focus ();
			return;
		}
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?' + ele.action.split('?')[1], 'post', true );
	j.addVar ( 'Email', ele['Email'].value );
	/*if ( ele['Firstname'] )
	{
		j.addVar ( 'Firstname', ele['Firstname'].value );
	}
	if ( ele['Lastname'] )
	{
		j.addVar ( 'Lastname', ele['Lastname'].value );
	}*/
	if ( ele['Username'] )
	{
		j.addVar ( 'Username', ele['Username'].value );
	}
	if( ele['StoreKey'] && ele['StoreKey'].checked )
	{
		j.addVar ( 'StoreKey', '1' );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] && r[2] )
		{
			alert( r[1] );
			document.location = r[2];
		}
		else if ( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
	}
	j.send ();
}

function activate( btn )
{
	var desc = new Array();
	
	desc['AuthKey'] = 'Type in the AuthKey you received';
	desc['Email'] = 'Type in your email';
	desc['Password'] = 'Type in your password';
	desc['Confirmed'] = 'Confirm your password';
	
	var ele = ge( 'ActivateForm' );
	var field = [ 'AuthKey', 'Email', 'Password', 'Confirmed' ];
	
	if( !ele || !ele.action || !field.length ) return;
	
	for( a = 0; a < field.length; a++ )
	{
		if ( ele[field[a]].value.length <= 2 )
		{
			alert ( desc[field[a]] );
			ele[field[a]].focus ();
			return;
		}
	}
	
	if ( ele['Password'].value == ele['Confirmed'].value && ele['Email'].value && ele['AuthKey'].value )
	{
		var AuthKey = md5( ele['AuthKey'].value.trim() );
		var Username = ele['Email'].value.trim();
		var Password = md5( ele['Password'].value.trim() );
		
		// TODO: First get uniqueid somehow do not use username in keygens, simply because users change emails often ...
		
		buttonLoading( btn, true );
		
		var x = new bajax ();
		x.openUrl ( getPath() + '?component=register&function=uniqueid', 'post', true );
		x.addVar ( 'Username', Username );
		x.onload = function ()
		{
			var s = this.getResponseText ().split ( '<!--separate-->' );	
			if ( s[0] == 'ok' && s[1] )
			{
				var uniqueid = s[1];
				
				var keys = generateNewKeys( Password, uniqueid );
				
				if ( keys && keys.privatekey && keys.publickey )
				{
					var j = new bajax ();
					j.openUrl ( getPath() + '?component=register&action=activate', 'post', true );
					j.addVar ( 'UniqueID', uniqueid );
					j.addVar ( 'AuthKey', AuthKey );
					j.addVar ( 'PublicKey', keys.publickey );
					j.onload = function ()
					{
						var r = this.getResponseText ().split ( '<!--separate-->' );	
						if ( r[0] == 'ok' )
						{
							setBrowserStorage( 'privatekey', fcrypt.stripHeader( keys.privatekey ) );
							setBrowserStorage( 'publickey', fcrypt.stripHeader( keys.publickey ) );
							setBrowserStorage( 'uniqueid', uniqueid );
							if ( r[1] )
							{
								document.location = r[1];
							}
						}
						else if ( r[0] == 'fail' && r[1] )
						{
							alert( r[1] );
						}
						else
						{
							alert( this.getResponseText() );
						}
						
						buttonLoading( btn, false );
					}
					j.send ();
				}
				else
				{
					alert( 'Something when\'t wrong contact webmaster' );
					
					buttonLoading( btn, false );
				}
				
			}
		}
		x.send ();
	}
	else
	{
		alert( 'Password didn\'t match confirmation' );
		ele['Confirmed'].focus ();
		
		buttonLoading( btn, false );
	}
}

function invite()
{
	var desc = new Array();
	
	desc['Email'] = 'Email is required';
	
	var ele = ge( 'InviteForm' );
	var field = [ 'Email' ];
	
	if( !ele || !ele.action || !field.length ) return;
	
	for( a = 0; a < field.length; a++ )
	{
		if ( ele[field[a]].value.length <= 2 )
		{
			alert ( desc[field[a]] );
			ele[field[a]].focus ();
			return;
		}
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?' + ele.action.split('?')[1], 'post', true );
	j.addVar ( 'Email', ele['Email'].value );
	if( ele['Group'] )
	{
		j.addVar ( 'Group', ele['Group'].value );
	}
	if( ele['StoreKey'] && ele['StoreKey'].checked )
	{
		j.addVar ( 'StoreKey', '1' );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if( r[0] == 'ok' && r[1] )
		{
			alert( r[1] );
		}
		else if( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
		else console.log( this.getResponseText() );
	}
	j.send ();
}

function limited()
{
	// TODO: Create this in two turns, first get uniqueid's and created usernames on all accounts sent to javascript and then after that create keys and send final
	
	var desc = new Array();
	
	desc['Email'] = 'Email is missing';
	desc['Expiry'] = 'Expiry date is missing';
	
	var ele = ge( 'LimitedForm' );
	var field = [ 'Email', 'Expiry' ];
	
	if( !ele || !ele.action ) return;
	
	for( a = 0; a < field.length; a++ )
	{
		if ( ele[field[a]].value.length <= 2 )
		{
			alert ( desc[field[a]] );
			ele[field[a]].focus ();
			return;
		}
	}
	
	var out = new Array();
	
	if ( ele['Accounts'].value > 0 )
	{
		var aclen = ele['Accounts'].value;
		
		for ( a = 0; a < aclen; a++ ) 
		{
			var Password = randomPassword();
			var Username = ele['Email'].value;
			// TODO: Uniqueid's not Username
			var keys = generateNewKeys( Password, Username );
			
			if ( keys )
			{
				var obj = new Object();
				obj.Key = Password;
				obj.PublicKey = keys.publickey;
				
				out.push( obj );
			}
		}
	}
	
	if ( out )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?' + ele.action.split('?')[1], 'post', true );
		j.addVar ( 'Email', ele['Email'].value );
		j.addVar ( 'Accounts', ele['Accounts'].value );
		j.addVar ( 'Expiry', ele['Expiry'].value );
		j.addVar ( 'Keys', JSON.stringify( out ) );
		if( ele['Group'] )
		{
			j.addVar ( 'Group', ele['Group'].value );
		}
		if( ele['StoreKey'] && ele['StoreKey'].checked )
		{
			j.addVar ( 'StoreKey', '1' );
		}
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );	
			if( r[0] == 'ok' && r[1] )
			{
				alert( r[1] );
			}
			else if( r[0] == 'fail' && r[1] )
			{
				alert( r[1] );
			}
			else alert( this.getResponseText () );
		}
		j.send ();
	}
}

function checkField( form, name )
{
	if( !form || !name ) return false;
	
	var desc = new Array();
	var func = new Array();
	
	desc['Username'] = 'Type in your username';
	desc['Password'] = 'Type in a password';
	
	func['Email'] = function ()
	{
		var email = form.Email.value;
		if ( email.indexOf ( '@' ) > 0 && email.indexOf ( '.' ) > 0 )
		{
			var j = new bajax ();
			j.openUrl ( getPath() + '?component=register&function=emailcheck', 'post', true );
			j.addVar ( 'Email', email );
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );	
				if ( r[0] == 'ok' )
				{
					approved['Email'] = true;
					return true;
				}
				else if ( r[0] == 'fail' && r[1] )
				{
					alert( r[1] );
					approved['Email'] = false;
					return false;
				}
				
				approved['Email'] = false;
				return false;
			}
			j.send ();
		}
		else
		{
			alert ( 'Type in a valid email' );
			approved['Email'] = false;
			return false;
		}
		
		approved['Email'] = false;
		return false;
	};
	
	// Run function
	if ( func[name] && !approved[name] )
	{
		if ( !func[name] () )
		{
			form[name].focus ();
			form[name].select ();
			return false;
		}
	}
	// Run default alert
	else if ( form[name].value.length <= 2 && !approved[name] )
	{
		alert ( desc[name] );
		form[name].focus ();
		return false;
	}
	else
	{
		approved[name] = true;
		return true;
	}
	
	return false;
}

function recover( btn )
{
	var desc = new Array();
	var func = new Array();
	
	desc['Key'] = 'Type in the RecoveryKey you received';
	desc['Username'] = 'Type in your email/username';
	desc['Password'] = 'Type in your new password';
	desc['Confirmed'] = 'Confirm your new password';
	
	var ele = ge( 'RecoverForm' );
	var field = [ 'Key', 'Username', 'Password', 'Confirmed' ];
	
	if ( !ele || !field.length ) return;
	
	for ( a = 0; a < field.length; a++ )
	{
		if ( ge(field[a]).value.length <= 2 )
		{
			alert ( desc[field[a]] );
			ge(field[a]).focus ();
			return;
		}
	}
	
	if ( ge('Password').value == ge('Confirmed').value && ge('Username').value && ge('Key').value )
	{
		var Key = md5( ge('Key').value.trim() );
		var Username = ge('Username').value.trim();
		var Password = md5( ge('Password').value.trim() );
		
		// TODO: First get uniqueid somehow do not use username in keygens, simply because users change emails often ...
		
		buttonLoading( btn, true );
		
		var x = new bajax ();
		x.openUrl ( getPath() + '?component=register&function=uniqueid', 'post', true );
		x.addVar ( 'Username', Username );
		x.onload = function ()
		{
			var s = this.getResponseText ().split ( '<!--separate-->' );	
			if ( s[0] == 'ok' && s[1] )
			{
				var uniqueid = s[1];
				
				var keys = generateNewKeys( Password, uniqueid );
				
				if ( keys && keys.privatekey && keys.publickey )
				{
					var j = new bajax ();
					j.openUrl ( getPath() + '?component=register&action=recover', 'post', true );
					j.addVar ( 'UniqueID', uniqueid );
					j.addVar ( 'RecoveryKey', Key );
					j.addVar ( 'PublicKey', keys.publickey );
					j.onload = function ()
					{
						var r = this.getResponseText ().split ( '<!--separate-->' );	
						if ( r[0] == 'ok' )
						{
							setBrowserStorage( 'privatekey', fcrypt.stripHeader( keys.privatekey ) );
							setBrowserStorage( 'publickey',fcrypt.stripHeader( keys.publickey ) );
							setBrowserStorage( 'uniqueid', uniqueid );
							if ( r[1] )
							{
								document.location = r[1];
							}
						}
						else if ( r[0] == 'fail' && r[1] )
						{
							alert( r[1] );
						}
						else
						{
							alert( this.getResponseText() );
						}
						
						buttonLoading( btn, false );
					}
					j.send ();
				}
				else
				{
					alert( 'Something when\'t wrong contact webmaster' );
					
					buttonLoading( btn, false );
				}
				
			}
		}
		x.send ();
	}
	else
	{
		alert( 'Password didn\'t match confirmation' );
		ge('Confirmed').focus ();
		
		buttonLoading( btn, false );
	}
}

function generateNewKeys( psw, usr )
{
	if( !psw ) return false;
	
	var KeySize = 1024;
	var PassPhrase = ( usr ? ( trim( usr ) + ':' + trim( psw ) ) : trim( psw ) );
	
	fcrypt.generateKeys( PassPhrase, KeySize );
	
	var prvkey = fcrypt.getPrivateKey();
	var pubkey  = fcrypt.getPublicKey();
	
	if ( prvkey && pubkey )
	{
		var keys = new Object();
		keys.privatekey = prvkey;
		keys.publickey = pubkey;
		
		return keys;
	}
	return false;
}

function initSignUp()
{
	var ele = ge( 'SignupForm' );
	
	if( !ele ) return;
	
	var btn = ele.getElementsByTagName( 'button' )[0];
	
	if( btn )
	{		
		btn.onclick = function(){ signUp() };
	}
}

function initActivate( auto )
{
	var ele = ge( 'ActivateForm' );
	
	if( !ele ) return;
	
	var btn = ele.getElementsByTagName( 'button' )[0];
	
	if( btn )
	{		
		btn.onclick = function(){ activate( btn ) };
	}
	
	if( auto && auto == 'login' )
	{
		activate( btn );
	}
}

function initInvite()
{
	var ele = ge( 'InviteForm' );
	
	if( !ele ) return;
	
	var btn = ele.getElementsByTagName( 'button' )[0];
	
	if( btn )
	{		
		btn.onclick = function(){ invite() };
	}
}

function initLimited()
{
	var ele = ge( 'LimitedForm' );
	
	if( !ele ) return;
	
	var btn = ele.getElementsByTagName( 'button' )[0];
	
	if( btn )
	{		
		btn.onclick = function(){ limited() };
	}
}

function initRecover( auto )
{
	var ele = ge( 'RecoverForm' );
	
	if( !ele ) return;
	
	var btn = ele.getElementsByTagName( 'button' )[0];
	
	if( btn )
	{		
		btn.onclick = function(){ recover( btn ) };
	}
	
	if( auto && auto == 'login' )
	{
		recover( btn );
	}
}

//var load;

function buttonLoading( btn, act )
{
	if ( act )
	{
		btn.disabled = true;
		
		btn.remember = btn.innerHTML;
		
		btn.innerHTML = 'loading';
		
		/*load = setInterval( function ()
		{
			if ( btn.innerHTML.indexOf( '...' ) >= 0 )
			{
				var text = btn.innerHTML.split( '.' ).join( '' );
			}
			else
			{
				var text = btn.innerHTML;
			}
			btn.innerHTML = text + '.';
		}, 500 );*/
		
		setTimeout( function ()
		{
			btn.disabled = false;
			//clearInterval( load );
			btn.innerHTML = btn.remember;
		}, 12000 )
	}
	else
	{
		btn.disabled = false;
		//clearInterval( load );
		btn.innerHTML = btn.remember;
	}
}
