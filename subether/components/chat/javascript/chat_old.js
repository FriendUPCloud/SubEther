
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

var chatInterval = new Object ();
var scrollAllow = new Object ();
var chatSearch;

//Refresh chat window on an interval
function refreshChat ( q )
{
	if( q ) chatSearch = q.value;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&function=chat', 'post', true );
	if( chatSearch ) j.addVar ( 'q', chatSearch );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( r[1] && ge( 'Chat' ) && ge( 'Chat' ).className == 'open' ) 
			{
				var h = 'Chat (' + r[2] + ')';
				var p = ge( 'Chat_list' ).innerHTML;
				var n = r[1];
				if ( p == n ) return;
				ge( 'Chat_head' ).innerHTML = h;
				ge( 'Chat_list' ).innerHTML = n;
			}
			else if( r[2] && ge( 'Chat' ) && !ge( 'Chat' ).className )
			{
				ge( 'Chat_head' ).innerHTML = 'Chat (' + r[2] + ')';
			}
		}
	}
	j.send ();
}

// Open chat
function openChat ()
{
	if( ge( 'Chat' ) && !ge( 'Chat' ).className )
	{
		ge( 'Chat' ).className = 'open';
		ge( 'Chat' ).parentNode.className = 'open';
	}
	else if( ge( 'Chat' ) && ge( 'Chat' ).className == 'open' )
	{
		ge( 'Chat' ).className = '';
		ge( 'Chat' ).parentNode.className = '';
	}
	refreshChat();
}

// Close private chat
function closePrivChat ( u )
{
	if( u && ge( 'Chat_' + u ) ) 
	{
		clearInterval( chatInterval['Chat_' + u] );
		ge( 'Chat_' + u ).parentNode.removeChild( ge( 'Chat_' + u ) );
	}
	else return;
}

// Add private chat (u = user id, n = username, o = online status, m = mode)
function addPrivChat ( u, n, o, m )
{
	if( ge( 'Chat_' + u ) || !u || !n ) return;
	
	var onclick;
	if( m == 'window' )
	{
		onclick = 'openWindow( \'Chat\', \'' + u + '\', \'chatwindow\', function(){ openPrivChat( \'' + u + '\', \'' + n + '\', \'window\' ); } );closePrivChat( \'' + u + '\' )';
	}
	else
	{
		onclick = 'openPrivChat( \'' + u + '\', \'' + n + '\' )';
	}
	
	var d = document.createElement ( 'div' );
	d.id = 'Chat_' + u;
	d.className = 'chattab';
	d.innerHTML = '<div class="chatpriv"><div id="Chat_head_' + u + '" class="head" onclick="' + onclick + '"><div class="status"><img src="' + ( o > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + '"/></div> <span class="name">' + n + '</span><div class="delete" onclick="closePrivChat( ' + u + ' );return false;">x</div></div><div id="Chat_inner_' + u + '" class="messages" onmouseup="checkScroll(this,\'' + u + '\')"></div><div class="post"><input onkeyup="if( event.keyCode == 13 ) { savePrivChat( \'' + u + '\', \'' + n + '\', this ) }"/></div></div>';
	ge( 'ChatTabs' ).appendChild( d );
	
	//chatInterval['Chat_' + u] = setInterval ( 'refreshPrivChat( \'' + u + '\', \'' + n + '\' );', 3000 );
	if( m && m == 'default' )
	{
		// if mode defined open priv chat
		openPrivChat( u, n, m );
	}
}

function checkScroll( ele, u )
{
	if( !ele || !u ) return;
	scrollAllow['Chat_' + u] = false;
	if( Math.round( ele.scrollHeight - ele.scrollTop ) < 470 )
	{
		scrollAllow['Chat_' + u] = true;
	}
}

// This is the chat box that opens when one clicks on a nick
function refreshPrivChat ( u, n, m )
{
	if( !u || !n || !ge( 'Chat_' + u ) ) return;
	
	var cbox = ge( 'Chat_' + u );
	var chbox = ge ( 'Chat_inner_' + u );
	var hbox = ge ( 'Chat_head_' + u );

	if ( chbox.pmsg && chbox.phead )
	{
		if( cbox.className == 'chattab open' ) 
		{
			if( m == 'window' )
			{
				chbox.innerHTML = chbox.pmsg;
			}
			else
			{
				//chbox.innerHTML = '<div class="head" onclick="openPrivChat( \'' + u + '\', \'' + n + '\' )"><div class="status"><img src="' + ( chbox.phead > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + '"/></div> <span class="name">' + n + '</span><div class="delete" onclick="closePrivChat( ' + u + ' )">x</div></div>' + chbox.pmsg;
				hbox.innerHTML = '<div class="status"><img src="' + ( chbox.phead > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + '"/></div> <span class="name">' + n + '</span><div class="delete" onclick="closePrivChat( ' + u + ' )">x</div>';
				chbox.innerHTML = chbox.pmsg;
			}
			if( ge( 'Chat_Messages_' + u ) ) ge( 'Chat_Messages_' + u ).scrollTop = ge( 'Chat_Messages_' + u ).scrollHeight;
		}
		else if( cbox.className != 'chattab open' )
		{	
			//chbox.innerHTML = '<div onclick="openPrivChat( \'' + u + '\', \'' + n + '\', \'' + m + '\' )"><div class="status"><img src="' + ( chbox.phead > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + '"/></div> <span class="name">' + n + '</span><div class="delete" onclick="closePrivChat( ' + u + ' )">x</div></div>';
			hbox.innerHTML = '<div class="status"><img src="' + ( chbox.phead > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + '"/></div> <span class="name">' + n + '</span><div class="delete" onclick="closePrivChat( ' + u + ' )">x</div>';
		}
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&function=chat', 'post', true );
	j.addVar ( 'u', u );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( cbox.className == 'chattab open' ) 
			{
				// Don't update on equal data
				if ( chbox.pmsg && chbox.phead )
				{
					if ( chbox.phead == r[2] && chbox.pmsg == r[1] )
					{
						return;
					}
				}
				chbox.pmsg  = r[1];
				chbox.phead = r[2];
				if( m == 'window' )
				{
					chbox.innerHTML = r[1];
				}
				else
				{
					//chbox.innerHTML = '<div class="head" onclick="openPrivChat( \'' + u + '\', \'' + n + '\', \'' + m + '\' )"><div class="status"><img src="' + ( r[2] > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + '"/></div> <span class="name">' + n + '</span><div class="delete" onclick="closePrivChat( ' + u + ' )">x</div></div>' + r[1];
					hbox.innerHTML = '<div class="status"><img src="' + ( r[2] > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + '"/></div> <span class="name">' + n + '</span><div class="delete" onclick="closePrivChat( ' + u + ' )">x</div>';
					chbox.innerHTML = r[1];
				}

				// Scroll down
				if( scrollAllow['Chat_' + u] )
				{
					chbox.scrollTop = chbox.scrollHeight;
				}
				if( !chatInterval['Chat_' + u] )
				{
					chatInterval['Chat_' + u] = setInterval ( 'refreshPrivChat( \'' + u + '\', \'' + n + '\', \'' + m + '\' );', 3000 );
				}
			}
			else if( cbox.className != 'chattab open' )
			{
				//chbox.innerHTML = '<div class="head" onclick="openPrivChat( \'' + u + '\', \'' + n + '\', \'' + m + '\' )"><div class="status"><img src="' + ( r[2] > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + '"/></div> <span class="name">' + n + '</span><div class="delete" onclick="closePrivChat( ' + u + ' )">x</div></div>';
				hbox.innerHTML = '<div class="status"><img src="' + ( r[2] > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + '"/></div> <span class="name">' + n + '</span><div class="delete" onclick="closePrivChat( ' + u + ' )">x</div>';
				clearInterval( chatInterval['Chat_' + u] );
				if( r[3] == '1' )
				{
					titleNotifications( false, n );
					cbox.getElementsByTagName( 'div' )[0].className = 'chatpriv notify';
				}
			}
		}
	}
	j.send ();
}

// Save private chat
function savePrivChat ( u, n, m, mode )
{
	if( !u && !n ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&action=chat', 'post', true );
	j.addVar ( 'u', u );
	j.addVar ( 'm', ( m.value ? m.value : m.innerHTML ) );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			refreshPrivChat( u, n, mode );
		}
	}
	j.send ();
	// Immediately clear!
	m.value = '';
	m.innerHTML = '';
}

// open private chat
function openPrivChat ( u, n, m )
{
	if( !u && !n ) return;
	if( ge( 'Chat_' + u ) && ge( 'Chat_' + u ).className != 'chattab open' )
	{
		ge( 'Chat_' + u ).getElementsByTagName( 'div' )[0].className = 'chatpriv open';
		ge( 'Chat_' + u ).className = 'chattab open';
		if( m && m == 'window' && ge( 'ChatWindow_' + u ) )
		{
			ge( 'ChatWindow_' + u ).focus();
		}
		else if( ge( 'Chat_' + u ).getElementsByTagName( 'input' ) )
		{
			ge( 'Chat_' + u ).getElementsByTagName( 'input' )[0].focus();
		}
		
		if( !scrollAllow['Chat_' + u] )
		{
			scrollAllow['Chat_' + u] = true;
		}
	}
	else if( ge( 'Chat_' + u ) && ge( 'Chat_' + u ).className == 'chattab open' )
	{
		ge( 'Chat_' + u ).getElementsByTagName( 'div' )[0].className = 'chatpriv';
		ge( 'Chat_' + u ).className = 'chattab';
	}
	notifications( u );
	refreshPrivChat( u, n, m );
}

// Set chatbox position
function chatBoxPos ()
{
	var margin = 5;
	if( !ge( 'Field_bottom' ) ) return false;
	var divs = ge( 'Field_bottom' ).getElementsByTagName( 'div' );
	if( divs.length )
	{
		var width;
		for( a = 0; a < divs.length; a++ )
		{
			if( divs[a].id == 'Chat' )
			{
				 width = Math.floor( divs[a].offsetWidth + margin );
			}
			else if( divs[a].className == 'chatpriv' && width )
			{
				divs[a].style.right = width + 'px';
				width = Math.floor( width + divs[a].offsetWidth + margin );
			}
		}
		if( width ) return width;
	}
	return false;
}

function chatSettings( ele, component, button )
{
	if( !ele || !component || !button || !ge( 'ChatSettings' ) ) return;
	
	if( ge( 'ChatSettings' ).className.indexOf( 'open' ) >= 0 )
	{
		//ele.className = '';
		ge( 'ChatSettings' ).className = '';
		ge( 'ChatSettings' ).getElementsByTagName( 'div' )[0].innerHTML = '';
	}
	else
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=chat&function=settings', 'post', true );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				//ele.className = 'active';
				ge( 'ChatSettings' ).getElementsByTagName( 'div' )[0].innerHTML = r[1];
				ge( 'ChatSettings' ).className = 'open ' + button;
			}
		}
		j.send ();
	}
}

function saveChatSettings( ele )
{
	if( !ele || ele.value == '' ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&action=settings', 'post', true );
	j.addVar ( 'mode', ele.value == '1' ? '1' : '0' );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			//
		}
	}
	j.send ();
}
