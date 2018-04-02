
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

var checked = new Array();

function checkform( form, ajax )
{	
	var input = form.getElementsByTagName( 'input' );
	
	if( !form || !input.length ) return;
	
	var desc = new Array();
	var func = new Array();
		
	desc['Firstname'] = 'Type in your firstname';
	desc['Lastname'] = 'Type in your lastname';
	desc['Password'] = 'Type in your password';
	desc['webPassword'] = 'Type in your password';
	desc['AuthKey'] = 'Type in your authkey';
	
	func['Username'] =  function ()
	{	
		var u = form.Username.value;
		if ( u.length >= 2 )
		{
			var j = new bajax ();
			j.openUrl ( getPath() + ajax.split('&')[0] + '&function=usercheck', 'post', true );
			j.addVar ( 'username', u );
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );	
				if ( r[0] == 'exists' )
				{
					alert ( 'Username exists choose another one' );
					return false;
				}
				else if( r[0] == 'checked' )
				{
					checked['Username'] = true;
					checkform( form, ajax );
				}
			}
			j.send ();
		}
		else
		{
			alert ( 'Type in your username' );
			return false;
		}
	};
		
	func['Email'] = function ()
	{
		var e = form.Email.value;
		if ( e.indexOf ( '@' ) > 0 && e.indexOf ( '.' ) > 0 )
		{
			var j = new bajax ();
			j.openUrl ( getPath() + ajax.split('&')[0] + '&function=usercheck', 'post', true );
			j.addVar ( 'email', e );
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );	
				if ( r[0] == 'exists' )
				{
					alert ( 'Email exists choose another one' );
					return false;
				}
				else if ( r[0] == 'checked' )
				{
					checked['Email'] = true;
					checkform( form, ajax );
				}
			}
			j.send ();
		}
		else
		{
			alert ( 'Type in a valid email' );
			return false;
		}
	};
	
	func['ConfirmEmail'] =  function ()
	{
		var e = form.Email.value;
		var c = form.ConfirmEmail.value;
		if ( e.length > 0 && c.length > 0 && e == c )
		{
			return true;
		}
		else
		{
			alert ( 'Email confirmation didnt match' );
			return false;
		}
	};
	
	func['webUsername'] =  function ()
	{	
		var isNumber = /^\d+$/;
		var u = form.webUsername.value;
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
	
	for ( var a = 0; a < input.length; a++ )
	{
		if( input[a].className == 'obl' && !checked[input[a].name] )
		{
			// Run function
			if ( func[input[a].name] )
			{
				if ( !func[input[a].name] () )
				{
					form[input[a].name].focus ();
					return false;
				}
			}
			// Run default alert
			else if ( form[input[a].name].value.length <= 2 )
			{
				alert ( desc[input[a].name] );
				form[input[a].name].focus ();
				return false;
			}
		}
		if( input[a].className == 'obl' && checked[input[a].name] )
		{
			checked[input[a].name] = false;
		}
	}
	
	if( !ajax ) form.submit ();
	else ajaxsubmit( form, ajax );
}

function ajaxsubmit( form, ajax )
{
	var input = form.getElementsByTagName( 'input' );
	
	if( !form && !ajax && !input.length ) return false;
	
	var j = new bajax ();
	/*j.openUrl ( document.location.href.split( '?' )[0].split ( '#' )[0] + ajax, 'post', true );*/
	j.openUrl ( getPath() + ajax, 'post', true );
	for ( var a = 0; a < input.length; a++ )
	{
		if( input[a].name && input[a].className != 'disabled' )
		{
			j.addVar ( input[a].name, input[a].value );
			if( input[a].name == 'Email' ) j.addVar ( 'webUsername', input[a].value );
			else if( input[a].name == 'Password' ) j.addVar ( 'webPassword', input[a].value );
		}
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			document.location = r[1];
		}
		else if ( r[0] == 'authenticate' )
		{
			if( r[1] )
			{
				if( ge( 'SignUpButton' ) && ge( 'SignUpButton' ).className == 'disabled' )
				{
					ge( 'SignUpButton' ).className = '';
				}
				if( ge( 'AuthKeyButton' ) && !ge( 'AuthKeyButton' ).className )
				{
					ge( 'AuthKeyButton' ).className = 'disabled';
				}
				ge( r[1] ).parentNode.parentNode.className = '';
				ge( r[1] ).className = 'obl';
				ge( r[1] ).focus();
				alert( 'The AuthKey was sendt to your email' );
			}
			return;
		}
		else if ( r[0] == 'send' )
		{
			ajaxsubmit( form, r[1] );
		}
		else if ( r[0] == 'fail' )
		{
			alert( r[1] );
		}
	}
	j.send ();
}

var validated = new Array();

function validateForm ( form, obl )
{
	if( !form ) return false;
	
	var fields = form.getElementsByTagName( '*' );
	
	var desc = new Array();
	var func = new Array();
		
	desc['Firstname'] = 'Type in your firstname';
	desc['Lastname'] = 'Type in your lastname';
	desc['Password'] = 'Type in your password';
	desc['AuthKey'] = 'Type in your authkey';
	
	/*func['Username'] =  function ()
	{	
		var u = form.Username.value;
		if ( u.length >= 2 )
		{
			var j = new bajax ();
			j.openUrl ( getPath() + '?component=authentication&function=usercheck', 'post', true );
			j.addVar ( 'username', u );
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );	
				if ( r[0] == 'exists' )
				{
					alert ( 'Username exists choose another one' );
					return false;
				}
				else if( r[0] == 'checked' )
				{
					checked['Username'] = true;
					checkform( form, ajax );
				}
			}
			j.send ();
		}
		else
		{
			alert ( 'Type in your username' );
			return false;
		}
	};*/
		
	func['Email'] = function ()
	{
		var e = form.Email.value;
		if ( e.indexOf ( '@' ) > 0 && e.indexOf ( '.' ) > 0 )
		{
			var j = new bajax ();
			j.openUrl ( getPath() + '?component=authentication&function=usercheck', 'post', true );
			j.addVar ( 'email', e );
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );	
				if ( r[0] == 'exists' )
				{
					alert ( 'Email exists choose another one' );
					return false;
				}
				else if ( r[0] == 'checked' )
				{
					checked['Email'] = true;
					checkform( form, ajax );
				}
			}
			j.send ();
		}
		else
		{
			alert ( 'Type in a valid email' );
			return false;
		}
	};
	
	func['ConfirmEmail'] =  function ()
	{
		var e = form.Email.value;
		var c = form.ConfirmEmail.value;
		if ( e.length > 0 && c.length > 0 && e == c )
		{
			return true;
		}
		else
		{
			alert ( 'Email confirmation didnt match' );
			return false;
		}
	};
	
	func['Username'] =  function ()
	{	
		var isNumber = /^\d+$/;
		var u = form.webUsername.value;
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
	
	for ( var a = 0; a < fields.length; a++ )
	{
		if( inArray( fields[a].name, obl ) && !validated[fields[a].name] )
		{
			// Run function
			if ( func[fields[a].name] )
			{
				if ( !func[fields[a].name] () )
				{
					form[fields[a].name].focus ();
					return false;
				}
			}
			// Run default alert
			else if ( form[fields[a].name].value.length <= 2 )
			{
				alert ( desc[fields[a].name] );
				form[fields[a].name].focus ();
				return false;
			}
		}
		if( inArray( fields[a].name, obl ) && validated[fields[a].name] )
		{
			validated[fields[a].name] = false;
		}
	}
	
	return true;
}
