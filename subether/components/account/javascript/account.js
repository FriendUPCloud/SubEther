
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

function getAccount ( tmp, param )
{
	if( !tmp ) return;
	
	if( ge( tmp ).className == 'active' && param != 'refresh' )
	{
		closeAccount();
		return;
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=account&function=account', 'post', true );
	j.addVar ( 'tmp', tmp );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( r[1] && ge( tmp ) )
			{
				closeAccount();
				if( !ge( tmp ).getAttribute( 'content' ) )
				{
					ge( tmp ).setAttribute( 'content', ge( tmp ).innerHTML );
				}
				ge( tmp ).innerHTML = r[1];
				ge( tmp ).className = 'active';
				
				// Check if the file has scripts
				var scripts = '';
				var rt = r[1];
				var wholescript = [];
				while( scripts = rt.match( /\<script[^>]*?\>([\w\W]*?)\<\/script[^>]*?\>/i ) )
				{
					wholescript.push( scripts[1] );
					rt = rt.split( scripts[0] ).join ( '' );
				}
				// Run script
				if( wholescript.length )
				{
					eval( wholescript.join ( '' ) );
				}
			}
		}
	}
	j.send ();
}

function deleteAccount()
{
	if( confirm( 'Are you sure you want to delete your account?' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=account&action=deleteuser', 'post', true );
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

function closeAccount ()
{
	if( !ge( 'Tabs' ) ) return;
	var divs = ge( 'Tabs' ).getElementsByTagName( 'div' );
	if( !divs.length ) return;
	for ( var a = 0; a < divs.length; a++ )
	{
		if( divs[a].className == 'active' )
		{
			if( divs[a].getAttribute( 'content' ) )
			{
				divs[a].innerHTML = divs[a].getAttribute( 'content' );
			}
			else
			{
				divs[a].innerHTML = '';
			}
			divs[a].className = '';
		}
	}
}

function saveAccount ( tmp )
{
	if( !tmp ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=account&action=account', 'post', true );
	var inputs = ge( tmp ).getElementsByTagName( '*' );
	if( !inputs.length ) return;
	for ( var a = 0; a < inputs.length; a++ )
	{
		if( !inputs[a].name ) continue;
		else if( inputs[a].type == 'checkbox' )
		{
			j.addVar ( inputs[a].name, inputs[a].checked ? 1 : 0 );
		}
		else if( inputs[a].type == 'radio' )
		{
			if( inputs[a].checked ) j.addVar ( inputs[a].name, inputs[a].value );
		}
		else
		{
			j.addVar ( inputs[a].name, inputs[a].value );
		}
	}
	if( ge( 'Validation' ) && ge( 'Validation' ).value.length > 0 )
	{
		j.addVar ( 'validation', ge( 'Validation' ).value );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			//if( ge( tmp ) ) getAccount( tmp, 'refresh' );
			location.reload();
		}
		else if ( r[0] == 'changed' && r[1] )
		{
			alert( r[1] );
			//closeAccount();
			refreshAccount( 'general' );
		}
		else if ( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
		else
		{
			console.log( this.getResponseText() );
		}
	}
	j.send ();
}

function changeLanguage()
{
	if ( ge( 'LanguageCode' ) && ge( 'LanguageCode' ).value )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=account&action=language', 'post', true );
		j.addVar ( 'Language', ge( 'LanguageCode' ).value );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );	
			if ( r[0] == 'ok' )
			{
				location.reload();
			}
			else console.log( this.getResponseText() );
		}
		j.send ();
	}
}

function changeDisplayMode( e )
{
	if ( ge( 'DisplayCode' ) && ge( 'DisplayCode' ).value )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=account&action=display', 'post', true );
		j.addVar ( 'Display', ge( 'DisplayCode' ).value );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );	
			if ( r[0] == 'ok' )
			{
				location.reload();
			}
		}
		j.send ();
	}
}

function removeEmail( email )
{
	if ( email )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=account&action=email', 'post', true );
		j.addVar ( 'RemoveEmail', email );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );	
			if ( r[0] == 'ok' )
			{
				//getAccount( 'account_email', 'refresh' );
				refreshAccount( 'general' );
			}
			else if( r[0] == 'fail' && r[1] )
			{
				alert( r[1] );
			}
			else console.log( this.getResponseText() );
		}
		j.send ();
	}
}

function changeEmail()
{
	if ( ge( 'account_email' ) )
	{
		var obj = new Object();
		
		var inp = ge( 'account_email' ).getElementsByTagName( 'input' );
		
		if ( inp.length > 0 )
		{
			for ( var a = 0; a < inp.length; a++ )
			{
				if ( inp[a].name && inp[a].type == 'radio' && inp[a].value )
				{
					if ( inp[a].checked )
					{
						obj[inp[a].name] = inp[a].value;
					}
				}
				else if ( inp[a].name && inp[a].value )
				{
					obj[inp[a].name] = inp[a].value;
				}
			}
			
			if ( obj )
			{
				var j = new bajax ();
				j.openUrl ( getPath() + '?component=account&action=email', 'post', true );
				
				for ( var key in obj )
				{
					j.addVar ( key, obj[key] );
				}
				
				j.onload = function ()
				{
					var r = this.getResponseText ().split ( '<!--separate-->' );	
					if ( r[0] == 'ok' )
					{
						//getAccount( 'account_email', 'refresh' );
						refreshAccount( 'general' );
					}
					else if( r[0] == 'fail' && r[1] )
					{
						alert( r[1] );
					}
					else console.log( this.getResponseText() );
				}
				j.send ();
			}
		}
	}
}

function refreshAccount( cat )
{
	if ( ge( 'Tabs' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=account&function=tabs', 'post', true );
		j.addVar ( 'cat', cat );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );	
			if ( r[0] == 'ok' && r[1] )
			{
				ge( 'Tabs' ).innerHTML = r[1];
			}
		}
		j.send ();
	}
}

function changePassword()
{
	var desc = new Array();
	var func = new Array();
	
	//desc['Validation'] = 'Type in your current password';
	desc['Password'] = 'Type in your new password';
	desc['Confirmed'] = 'Confirm your new password';
	
	var ele = ge( 'account_password' );
	//var field = [ /*'Validation', */'Password', 'Confirmed' ];
	var field = [ 'Password', 'Confirmed' ];
	
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
	
	if ( ge('Password').value == ge('Confirmed').value && ge('Username').value && ge('UniqueID').value )
	{
		var UniqueID = ge('UniqueID').value;
		var Username = ge('Username').value;
		//var Validation = md5( ge('Validation').value );
		var Password = md5( ge('Password').value.trim() );
		
		/*if ( validateRSAKeys( Validation, UniqueID ) )
		{*/
			var keys = generateNewRSAKeys( Password, UniqueID );
			
			if ( keys && keys.privatekey && keys.publickey )
			{
				var j = new bajax ();
				j.openUrl ( getPath() + '?component=account&action=password', 'post', true );
				j.addVar ( 'UniqueID', UniqueID );
				j.addVar ( 'PublicKey', keys.publickey );
				j.onload = function ()
				{
					var r = this.getResponseText ().split ( '<!--separate-->' );	
					if ( r[0] == 'ok' )
					{
						// TODO: Find out why privatekey is generated different from authentication key, crypto chat stops working after pw change, because privkey is wrong in storage
						setBrowserStorage( 'privatekey', fcrypt.stripHeader( keys.privatekey ) );
						setBrowserStorage( 'publickey', fcrypt.stripHeader( keys.publickey ) );
						
						console.log( getBrowserStorage( 'privatekey' ) );
						console.log( getBrowserStorage( 'publickey' ) );
						
						if ( r[1] )
						{
							alert( r[1] );
							//closeAccount();
							refreshAccount( 'general' );
						}
					}
					else if ( r[0] == 'keys' && r[1] )
					{
						var data = JSON.parse( r[1] );
						
						if ( data.length > 0 )
						{
							var out = new Array();
							
							for ( a = 0; a < data.length; a++ ) 
							{
								if ( data[a].EncryptionKey )
								{
									// Decrypt key string with current privatekey
									var decrypted = fcrypt.decryptString( data[a].EncryptionKey, getBrowserStorage( 'privatekey' ) );
									
									if ( decrypted && decrypted.plaintext )
									{
										// Encrypt keys in key string with new publickey
										var encrypted = fcrypt.encryptString( decrypted.plaintext, keys.publickey );
										
										if ( encrypted && encrypted.cipher )
										{
											var obj = new Object();
											obj.ID = data[a].ID;
											obj.EncryptionKey = encrypted.cipher;
											//obj.PublicKey = keys.publickey;
											
											// Store object with new data to out array
											out.push( obj );
										}
									}
								}
							}
							
							var b = new bajax ();
							b.openUrl ( getPath() + '?component=account&action=password', 'post', true );
							b.addVar ( 'UniqueID', UniqueID );
							b.addVar ( 'PublicKey', keys.publickey );
							b.addVar ( 'Keys', ( out.length > 0 ? JSON.stringify( out ) : '' ) );
							b.onload = function ()
							{
								var t = this.getResponseText ().split ( '<!--separate-->' );	
								if ( t[0] == 'ok' )
								{
									// TODO: Find out why privatekey is generated different from authentication key, crypto chat stops working after pw change, because privkey is wrong in storage
									setBrowserStorage( 'privatekey', fcrypt.stripHeader( keys.privatekey ) );
									setBrowserStorage( 'publickey', fcrypt.stripHeader( keys.publickey ) );
									
									console.log( getBrowserStorage( 'privatekey' ) );
									console.log( getBrowserStorage( 'publickey' ) );
									
									if ( t[1] )
									{
										alert( t[1] );
										//closeAccount();
										refreshAccount( 'general' );
									}
								}
								else
								{
									console.log( this.getResponseText() );
								}
							}
							b.send ();
						}
					}
					else
					{
						console.log( this.getResponseText() );
					}
				}
				j.send ();
			}
			else alert( 'Something when\'t wrong contact webmaster' );
		/*}
		else
		{
			alert ( 'What you typed in didn\'t match your current password try again' );
			ge('Validation').focus ();
		}*/
	}
	else
	{
		alert( 'Password didn\'t match confirmation' );
		ge('Confirmed').focus ();
	}
}

function validateRSAKeys( psw, usr )
{
	if( !psw ) return false;
	
	var keys = generateNewRSAKeys( psw, usr );
	
	if ( keys && fcrypt.stripHeader( keys.privatekey ) == getBrowserStorage( 'privatekey' ) )
	{
		return true;
	}
	
	return false;
}

function generateNewRSAKeys( psw, usr )
{
	if( !psw ) return false;
	
	var KeySize = 1024;
	var PassPhrase = ( usr ? ( trim( usr ) + ':' + trim( psw ) ) : trim( psw ) );
	
	/*fcrypt.generateKeys( PassPhrase, KeySize );
	
	var prvkey = fcrypt.getPrivateKey();
	var pubkey  = fcrypt.getPublicKey();
	
	if ( prvkey && pubkey )
	{
		var keys = new Object();
		keys.privatekey = prvkey;
		keys.publickey = pubkey;
		
		return keys;
	}*/
	
	var privKeyObject = fcrypt.generateKeys( PassPhrase, KeySize );
	
	var keys = fcrypt.getKeys( privKeyObject );
	
	if( keys )
	{
		var prvkey = keys.privatekey;
		var pubkey = keys.publickey;
		var reckey = keys.recoverykey;
		
		if( prvkey && pubkey && reckey )
		{
			setBrowserStorage( 'privatekey', prvkey );
			setBrowserStorage( 'publickey', pubkey );
			setBrowserStorage( 'recoverykey', reckey );
			
			return keys;
		}
	}
	return false;
}

function validateAccount ( ele )
{
	/*if( !ele.value || !ge( 'Submit' ) ) return;
	else
	{
		var button = ge( 'Submit' );
		var name = ge( 'Submit' ).getAttribute( 'name' );
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=account&function=validation', 'post', true );
	j.addVar ( 'validation', ele.value );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			button.setAttribute( 'onclick', 'saveAccount( \'' + name + '\', \'' + ele + '\' )' );
			button.className = '';
		}
		else
		{
			button.setAttribute( 'onclick', '' );
			button.className = 'disabled';
		}
	}
	j.send ();*/
}

function updateDisplay ()
{
	if( !ge( 'Firstname' ) || !ge( 'Middlename' ) || !ge( 'Lastname' ) || !ge( 'Display' ) ) return;
	else
	{
		var first = ge( 'Firstname' ).value;
		var middle = ge( 'Middlename' ).value;
		var last = ge( 'Lastname' ).value;
		var display = ge( 'Display' );
	}
	
	var options = display.getElementsByTagName( 'option' );
}
