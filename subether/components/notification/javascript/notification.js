
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

var titleInfo;

var alertedlist = {};

// Notification icons
function notifications ( n, t )
{
	var q = new jaxqueue ( 'notifications' );
	q.addUrl ( baseUrl() + '?component=notification&function=notification', ( n || t ? true : false ) );
	if( n )
	{
		if( t == 'contacts' ) q.addVar ( 'contacts', n );
		else if( t == 'notices' ) q.addVar ( 'notices', n );
		else if( t == 'connects' ) q.addVar ( 'connects', n );
		else q.addVar ( 'messages', n );
	}
	q.onload ( function ( data )
	{
		var contacts = false;
		var total = 0;
		var r = data.split ( '<!--split-->' );
		// Notifications for messages
		var mess = ge ( 'Messageu' );
		if( r[0] ) 
		{
			var s = r[0].split ( '<!--separate-->' );
			if( s.length && s[0] == 'messages' )
			{
				if( s.length >= 3 )
				{
					var u = s[1].split( ',' );
					var n = s[2].split( ',' );
					var m = s[4].split( ',' );
					var i = s[5].split( ',' );
					var t = s[6].split( ',' );
					var b = s[7].split( ',' );
					//var h = s[8].split( ',' );
					var h = s[8];
					if( u.length && n.length )
					{
						for( var a = 0; a < u.length; a++ )
						{
							if( !ge( 'Chat_' + u[a] ) )
							{
								if( typeof( addPrivChat ) != 'undefined' )
								{
									addPrivChat( u[a], n[a], 1, m[a] );
								}
								else if( !t[a] || t[a] == '0' ) 
								{
									if ( ge( 'Chat' ) )
									{
										chatObject.addPrivateChat( u[a], n[a], i[a] );
										chatObject.minimizePrivateChat( u[a] );
									}
									else
									{
										// Commented out for now if chat is disabled ...
										
										// Check if this is a live invite and send a call request
										if ( h )
										{
											//chatObject.receiveCall( u[a], h, n[a], i[a] );
										}
										else
										{
											/*// TODO: Add messageid check to make sure it doesn't renotify the user
											
											// Desktop notify
											titleNotifications( false, n[a], 'desktop', b[a], i[a] );
											
											// Play audio on alert notify
											playAudio( 'pling', function(){ removeAudio(); }, 10, true );*/
											
											// Reset notification so that the audio notification stops
											resetMessageNotify( u[a] );
										}
									}
								}
							}
						}
					}
				}
				if( s.length >= 4 )
				{
					// Create ncount div or fill existing one
					var messdivs = false;
					if ( mess ) messdivs = mess.getElementsByTagName ( 'div' );
					if( !messdivs.length )
					{
						var d = document.createElement ( 'div' );
						d.className = 'ncount hmm';
						d.innerHTML = s[3];
						mess.appendChild( d );
						mess.className = 'active';
					}
					else
					{
						messdivs[0].innerHTML = s[3];
					}
					
					total += Number( s[3] );
					
					// If window is open then reset notification
					if ( ge( 'NotificationBox' ) && ge( 'NotificationBox' ).className.indexOf( 'open messages' ) >= 0 )
					{
						resetMessageNotify( false, 'notified' );
					}
				}
			}
			else
			{
				if( mess.getElementsByTagName( 'div' ).length )
				{
					mess.innerHTML = '';
					if( !ge( 'DropDownWindow' ) ) mess.className = '';
				}
			}
		}
		else
		{
			if( mess.getElementsByTagName( 'div' ).length )
			{
				mess.innerHTML = '';
				if( !ge( 'DropDownWindow' ) ) mess.className = '';
			}
		}
		// Notifications for contacts
		if( r[1] ) 
		{
			var s = r[1].split ( '<!--separate-->' );
			if( s && s[0] == 'contacts' )
			{	
				if( s[1] )
				{
					if( !ge( 'Frequ' ).getElementsByTagName( 'div' )[0] )
					{
						var d = document.createElement ( 'div' );
						d.className = 'ncount';
						d.innerHTML = s[1];
						ge( 'Frequ' ).appendChild( d );
						ge( 'Frequ' ).className = 'active';
					}
					else
					{
						var Frequ = ge( 'Frequ' ).getElementsByTagName( 'div' )[0];
						Frequ.innerHTML = s[1];
					}
				}
				contacts = s[1];
				total += Number( s[1] );
			}
			else
			{
				if( ge( 'Frequ' ).getElementsByTagName( 'div' )[0] )
				{
					ge( 'Frequ' ).innerHTML = '';
					if( !ge( 'DropDownWindow' ) ) ge( 'Frequ' ).className = '';
				}
			}
		}
		else
		{
			if( ge( 'Frequ' ).getElementsByTagName( 'div' )[0] )
			{
				ge( 'Frequ' ).innerHTML = '';
				if( !ge( 'DropDownWindow' ) ) ge( 'Frequ' ).className = '';
			}
		}
		// Notifications globaly
		if( r[2] ) 
		{
			var s = r[2].split ( '<!--separate-->' );
			if( s && s[0] == 'notices' )
			{	
				if( s[1] )
				{
					if( !ge( 'Notiu' ).getElementsByTagName( 'div' )[0] )
					{
						var d = document.createElement ( 'div' );
						d.className = 'ncount';
						d.innerHTML = s[1];
						ge( 'Notiu' ).appendChild( d );
						ge( 'Notiu' ).className = 'active';
					}
					else
					{
						ge( 'Notiu' ).getElementsByTagName( 'div' )[0].innerHTML = s[1];
					}
				}
				total += Number( s[1] );
			}
			else
			{
				if( ge( 'Notiu' ).getElementsByTagName( 'div' )[0] )
				{
					ge( 'Notiu' ).innerHTML = '';
					if( !ge( 'DropDownWindow' ) ) ge( 'Notiu' ).className = '';
				}
			}
		}
		// Notifications for connects
		if( r[3] )
		{
			var s = r[3].split ( '<!--separate-->' );
			if( s && s[0] == 'connects' )
			{	
				if( s[1] )
				{
					if( !ge( 'Frequ' ).getElementsByTagName( 'div' )[0] )
					{
						var d = document.createElement ( 'div' );
						d.className = 'ncount';
						d.innerHTML = s[1];
						ge( 'Frequ' ).appendChild( d );
						ge( 'Frequ' ).className = 'active';
					}
					else
					{
						var Frequ = ge( 'Frequ' ).getElementsByTagName( 'div' )[0];
						Frequ.innerHTML = ( contacts + s[1] );
					}
				}
				total += Number( s[1] );
			}
			else if( !contacts )
			{
				if( ge( 'Frequ' ).getElementsByTagName( 'div' )[0] )
				{
					ge( 'Frequ' ).innerHTML = '';
					if( !ge( 'DropDownWindow' ) ) ge( 'Frequ' ).className = '';
				}
			}
		}
		// Notifications for cart content
		if( r[4] )
		{
			var s = r[4].split ( '<!--separate-->' );
			if( s && s[0] == 'cart' )
			{	
				if( s[1] )
				{
					if( !ge( 'Cart' ).getElementsByTagName( 'div' )[0] )
					{
						var d = document.createElement ( 'div' );
						d.className = 'ncount';
						d.innerHTML = s[1];
						ge( 'Cart' ).appendChild( d );
						ge( 'Cart' ).className = 'active';
					}
					else
					{
						ge( 'Cart' ).getElementsByTagName( 'div' )[0].innerHTML = s[1];
					}
				}
			}
			else
			{
				if( ge( 'Cart' ).getElementsByTagName( 'div' )[0] )
				{
					ge( 'Cart' ).innerHTML = '';
					if( !ge( 'DropDownWindow' ) ) ge( 'Cart' ).className = '';
				}
			}
		}
		else if( !contacts )
		{
			if( ge( 'Notiu' ).getElementsByTagName( 'div' )[0] )
			{
				ge( 'Notiu' ).innerHTML = '';
				if( !ge( 'DropDownWindow' ) ) ge( 'Notiu' ).className = '';
			}
		}
		
		// Notifications in title
		titleNotifications( total );
	} );
	q.save();
}

function titleNotifications( n, u, type, message, icon )
{
	msg = ( u ? ( u + ' writes you...' ) : false );
	
	icon = ( icon ? icon : 'upload/images-master/logo_symbol_white.png' );
	
	if( u && document.title.indexOf( u ) < 0 )
	{
		if( !titleInfo ) titleInfo = document.title;
		document.title = msg;
	}
	else if( titleInfo )
	{
		document.title = titleInfo;
	}
	
	if( u && type && document.title )
	{
		desktopNotify( ( u + ' says' ), message, icon );
	}
	
	ts = document.title.split( ') ' );
	
	if( n && n > 0 && ts[1] )
	{
		document.title = '(' + n + ') ' + ts[1];
		titleInfo = document.title;
	}
	else if( n && n > 0 )
	{
		document.title = '(' + n + ') ' + document.title;
		titleInfo = document.title;
	}
	else if( !u && ts[1] )
	{
		document.title = ts[1];
		titleInfo = document.title;
	}
}

function resetMessageNotify( uid, type, reverse )
{
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=notification&action=resetnotify&fastlane=1', 'post', true );
	if( uid ) j.addVar ( 'uid', uid );
	if( type ) j.addVar ( 'type', type );
	if( reverse ) j.addVar ( 'reverse', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if( r[0] == 'ok' )
		{
			//
		}
	}
	j.send ();
}

function refreshContactRequests()
{
	if( !ge( 'DropDownWindow' ) ) return false;
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=notification&function=contacts', 'get', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if( r[0] == 'ok' )
		{
			ge( 'DropDownWindow' ).innerHTML = r[1];
		}
		else
		{
			ge( 'Frequ' ).className = '';
			ge( 'NotificationBox' ).className = '';
			ge( 'DropDownWindow' ).innerHTML = '';
		}
	}
	j.send ();
}

function refreshConnectRequests()
{
	if( !ge( 'DropDownWindow' ) ) return false;
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=notification&function=connects', 'get', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if( r[0] == 'ok' )
		{
			ge( 'DropDownWindow' ).innerHTML = r[1];
		}
		else
		{
			ge( 'Frequ' ).className = '';
			ge( 'NotificationBox' ).className = '';
			ge( 'DropDownWindow' ).innerHTML = '';
		}
	}
	j.send ();
}

function refreshNotices()
{
	if( !ge( 'DropDownWindow' ) ) return;
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=notification&function=notices', 'get', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if( r[0] == 'ok' )
		{
			ge( 'DropDownWindow' ).innerHTML = r[1];
		}
		else
		{
			ge( 'Notiu' ).className = '';
			ge( 'NotificationBox' ).className = '';
			ge( 'DropDownWindow' ).innerHTML = '';
		}
	}
	j.send ();
}

function allowContact( uid )
{
	if( !uid ) return false;
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=notification&action=contacts', 'post', true );
	j.addVar ( 'allow', uid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if( r[0] == 'ok' ) refreshContactRequests();
		else alert( this.getResponseText() );
	}
	j.send ();
}

function denyContact( uid )
{
	if( !uid ) return false;
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=notification&action=contacts', 'post', true );
	j.addVar ( 'deny', uid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if( r[0] == 'ok' ) refreshContactRequests();
		else alert( this.getResponseText() );
	}
	j.send ();
}

function allowConnect( id )
{
	if( !id ) return false;
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=notification&action=connects', 'post', true );
	j.addVar ( 'allow', id );
	j.onload = function ()
	{
		refreshConnectRequests()
	}
	j.send ();
}

function denyConnect( id )
{
	if( !id ) return false;
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=notification&action=connects', 'post', true );
	j.addVar ( 'deny', id );
	j.onload = function ()
	{
		refreshConnectRequests()
	}
	j.send ();
}

function closeNotificationBox()
{
	if( ge( 'NotificationBox' ) && ge( 'NotificationBox' ).className.indexOf( 'open' ) >= 0 )
	{
		switch ( ge( 'NotificationBox' ).className.split( ' ' )[1] )
		{
			case 'contacts':
				dropDownWindow( 'NotificationBox', 'notification', 'contacts', ge( 'Frequ' ), true );
				break;
			case 'messages':
				dropDownWindow( 'NotificationBox', 'notification', 'messages', ge( 'Messageu' ), true );
				break;
			case 'notices':
				dropDownWindow( 'NotificationBox', 'notification', 'notices', ge( 'Notiu' ), true );
				break;
			default: break;
		}
	}
}

/* --- Global Notification Event -------------------------------------------------------- */

// Check Global Keys
function checkNKeys( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var keycode = e.which ? e.which : e.keyCode;
	switch ( keycode )
	{
		case 27:
			if( ge( 'NotificationBox' ) && ge( 'NotificationBox' ).className.indexOf( 'open' ) >= 0 )
			{
				closeNotificationBox();
			}
			break;
		default: break;
	}
}

// Check Global Cliks
function checkNClicks( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var button = e.which ? e.which : e.button;
	if( ge( 'NotificationBox' ) && ge( 'NotificationBox' ).className.indexOf( 'open' ) >= 0 )
	{
		var tags = ge( 'Notification' ).getElementsByTagName( '*' );
		if( tags.length > 0 )
		{
			for( a = 0; a < tags.length; a++ )
			{
				if( targ.id == 'NotificationBox' || targ == tags[a] || targ.tagName == 'HTML' )
				{
					return;
				}
			}
			closeNotificationBox();
		}
	}
}

// Assign Global Listeners
if ( window.addEventListener )
{
	window.addEventListener ( 'keydown', checkNKeys );
	window.addEventListener ( 'mousedown', checkNClicks );
}
else 
{
	window.attachEvent ( 'onkeydown', checkNKeys );
	window.attachEvent ( 'onmousedown', checkNClicks );
}
