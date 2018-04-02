
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
var inComingCall = new Object ();
var lineLimit = 50;
var chatSearch;

// A layer mindful element getter
function chatGet( str )
{
	if( parent && parent.hasLayer )
	{
		return parent.document.getElementById( str );
	}
	return document.getElementById( str );
}

//Refresh chat window on an interval
function refreshChat ( q, act )
{
	if( !chatGet( 'Chat' ) ) return;
	if( !chatGet( 'Chat_head' ) ) return;
	if( !chatGet( 'Chat_list' ) ) return;
	
	// Don't double run
	if( getRunning( 'refreshChat' ) ) return;
	setRunning( 'refreshChat', true );
	
	// If act request is to close
	if( act && act == 'close' )
	{
		chatGet( 'Chat' ).className = '';
		chatGet( 'Chat' ).parentNode.className = '';
	}
	
	// If act request is to open
	if( act && act == 'open' )
	{
		chatGet( 'Chat' ).className = 'open';
		chatGet( 'Chat' ).parentNode.className = 'open';
	}
	
	if( q ) chatSearch = ( q.value ? q.value : '-' );
	
	var x = new jaxqueue ( 'refreshChat' );
	x.addUrl ( '?component=chat&function=chat', ( q || act ? true : false ) );
	if( chatSearch )
	{
		x.addVar ( 'q', chatSearch );
		if( chatSearch == '-' )
		{
			chatSearch = '';
		}
	}
	if( chatGet( 'Chat_head' ).phead )
	{
		x.addVar ( 'online', chatGet( 'Chat_head' ).phead );
	}
	x.onload ( function ( data )
	{
		// Set to completed
		setRunning( 'refreshChat', false );
		
		if ( !chatGet( 'Chat_list' ) )
			return;
		
		var r = data.split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			// Check for Voicechat Notifications
			if( r[3] == '1' && !inComingCall[r[3]] )
			{
				joinConference();
				inComingCall[r[3]] = true;
			}
			else if( r[3] && !inComingCall[r[3]] )
			{
				openWindow( 'Chat', r[3], 'voicechat' );
				inComingCall[r[3]] = true;
				playAudio( 'skype_call', function(){ declineCall( r[3] );closeWindow(); }, 10000 );
			}
			
			var h = 'Chat (' + ( r[2] != 0 ? r[2].split( ',' ).length : '0' ) + ')';
			var n = r[1];
			chatGet( 'Chat_head' ).innerHTML = h;
			chatGet( 'Chat_list' ).innerHTML = n;
			
			chatGet( 'Chat_head' ).phead = r[2];
		}
		
		// Update time if found
		if( chatGet( 'Chat_list' ) )
		{
			var span = chatGet( 'Chat_list' ).getElementsByTagName( 'span' );
			if( span.length > 0 )
			{
				for( a = 0; a < span.length; a++ )
				{
					if( span[a].className == 'time' && span[a].innerHTML != '' )
					{
						var time = TimeToHuman( span[a].getAttribute( 'time' ), 'mini' );
					
						if( time && time != span[a].innerHTML )
						{
							span[a].innerHTML = time;
						}
					}
				}
			}
		}
		
		var ex = typeof( chatGet( 'Chat_list' ).str ) != 'undefined' ? chatGet( 'Chat_list' ).str : '';
		filterContactList( ex );
	} );
	x.save();
}

// Open chat
function openChat ()
{
	var act;
	if( !chatGet( 'Chat' ) ) return;
	
	if( chatGet( 'Chat' ) && !chatGet( 'Chat' ).className )
	{
		act = 'open';
	}
	else if( chatGet( 'Chat' ) && chatGet( 'Chat' ).className == 'open' )
	{
		act = 'close';
	}
	refreshChat( false, act );
}

// Close private chat
function closePrivChat ( u )
{
	if( !chatGet( 'Chat_' + u ) ) return;
	
	if( u && chatGet( 'Chat_' + u ) ) 
	{
		// If interval exists clear it
		if( chatInterval['Chat_' + u] )
		{
			clearInterval( chatInterval['Chat_' + u] );
		}
		chatGet( 'Chat_' + u ).parentNode.removeChild( chatGet( 'Chat_' + u ) );
		
		// So, tell we're completed
		running( 'chat_' + u, false );
	}
	else return;
}

// Add private chat (u = user id, n = username, o = online status, m = mode)
function addPrivChat ( u, n, o, m )
{
	if( ( window.innerWidth <= 1024 || !ge( 'ChatBox' ) ) && u )
	{
		document.location = 'en/home/messages/' + u;
	}
	
	if( chatGet( 'Chat_' + u ) || !u || !n ) return;
	if( !chatGet( 'ChatTabs' ) ) return;
	
	/*if( !scrollAllow['Chat_' + u] )
	{
		scrollAllow['Chat_' + u] = true;
	}*/
	
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
	chatGet( 'ChatTabs' ).appendChild( d );
	
	refreshPrivChat( u, n, m, true );
	
	if( m && m == 'default' )
	{
		// if mode defined open priv chat
		openPrivChat( u, n, m );
	}
}

/*function checkScroll( ele, u )
{
	if( !ele || !u ) return;
	scrollAllow['Chat_' + u] = false;
	if( Math.round( ele.scrollHeight - ele.scrollTop ) < 470 )
	{
		scrollAllow['Chat_' + u] = true;
	}
}*/

function refreshPrivChat ( u, n, m, act )
{
	if( !u || !n || !chatGet( 'Chat_' + u ) ) return;
	
	var cbox = chatGet( 'Chat_' + u );
	var chbox = chatGet( 'Chat_inner_' + u );
	var hbox = chatGet( 'Chat_head_' + u );
	
	if( !cbox ) return;
	if( !chbox ) return;
	if( !hbox ) return;
	
	// If act request is to close
	if( act && act == 'close' )
	{
		cbox.getElementsByTagName( 'div' )[0].className = 'chatpriv';
		cbox.className = 'chattab';
	}
	
	// If act request is to open
	if( act && act == 'open' )
	{
		var cmsg = chatGet( 'Chat_Messages_' + u );
		
		cbox.getElementsByTagName( 'div' )[0].className = 'chatpriv open' + ( cmsg ? '' : ' loading' );
		cbox.className = 'chattab open';
		if( m && m == 'window' && chatGet( 'ChatWindow_' + u ) )
		{
			chatGet( 'ChatWindow_' + u ).focus();
		}
		else if( cbox.getElementsByTagName( 'input' ) )
		{
			cbox.getElementsByTagName( 'input' )[0].focus();
		}
	}
	
	// Don't double run
	if( getRunning( 'Chat_' + u ) ) return;
	setRunning( 'Chat_' + u, true );
	
	var x = new jaxqueue ( 'refreshPrivChat', 3000, u );
	x.addUrl ( '?component=chat&function=chat', ( act ? true : false ) );
	x.addVar ( 'limit', lineLimit );
	x.addVar ( 'u', u );
	x.addVar ( 'lastmessage', chbox.lastmessage );
	x.fncVar ( 'u', u );
	x.fncVar ( 'n', n );
	x.fncVar ( 'm', m );
	// If lastseen is set send this with the request
	if( chbox.lastseen != '' )
	{
		x.addVar ( 'lastseen', chbox.lastseen );
	}
	x.onload ( function ( data )
	{		
		if( !chatGet( 'Chat_' + u ) ) return;
		
		// Set to completed
		setRunning( 'Chat_' + u, false );
		
		// Remove the loader
		if( cbox.getElementsByTagName( 'div' )[0].className.indexOf( 'loading' ) >= 0 )
		{
			var loading = cbox.getElementsByTagName( 'div' )[0];
			loading.className = loading.className.split(' loading').join('');
		}
		
		//var r = this.getResponseText ().split ( '<!--separate-->' );
		var r = data.split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{	
			if( cbox.className != 'chattab open' )
			{
				// If notification is found notify user
				if( r[3] == '1' )
				{
					titleNotifications( false, n );
					cbox.getElementsByTagName( 'div' )[0].className = 'chatpriv notify';
				}
			}
			
			// If we have audio notification run it
			if( r[5] )
			{
				playAudio( r[5], function(){ removeAudio(); }, 10, true );
				// Reset notification so that the audio notification stops
				resetMessageNotify( u );
			}
			
			// Don't update on equal data
			if ( chbox.innerHTML != '' && chbox.pmsg && chbox.phead )
			{
				if ( chbox.phead == r[2] && ( chbox.pmsg == r[1] || chbox.lastreq != '' && chbox.lastreq == r[1] ) )
				{
					return;
				}
			}
			
			// If this is the first time and no last message
			if( !chbox.init )
			{
				chbox.pmsg = r[1];
				chbox.init = true;
				chbox.lastreq = false;
			}
			// Else use the buffer and add new lines to it
			else
			{
				chbox.pmsg = r[1] + chbox.pmsg;
				chbox.lastreq = r[1];
			}
			
			chbox.lastmessage = r[4];
		}
		
		// Update header
		if( chbox && chbox.phead != r[2] && hbox && m != 'window' )
		{
			chbox.phead = r[2];
			
			hbox.innerHTML = '<div class="status"><img src="' + ( r[2] > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + '"/></div> <span class="name">' + n + '</span><div class="delete" onclick="closePrivChat( ' + u + ' )">x</div>';
		}
		
		// If we have messages and it's a new message or a new notice
		if( chbox && chbox.pmsg && ( r[1] || r[6] ) )
		{
			// If lastmessage is set send this with the request prevent double post
			var runUpdate = true;
			if( !chbox.prevmessages )
				chbox.prevmessages = [];
			if( chbox.lastmessage > 0 )
			{
				if( chbox.prevmessages )
				{
					for( var a = 0; a < chbox.prevmessages.length; a++ )
					{
						// We already have this..
						if( chbox.prevmessages[a] == chbox.lastmessage )
						{
							runUpdate = false;
							break;
						}
					}
				}
				if( runUpdate )
				{
					chbox.prevmessages.push( chbox.lastmessage );
				}
			}
			
			// Only populate chat list if we have a go!
			if( runUpdate )
			{
				// Get messages
				var row = chbox.pmsg.split('<!--message-->');
			
				// Populate list im div
				var li = new Array ();
			
				if( row.length > 0 )
				{
					for( var l = row.length-2; l >= 0; l-- )
					{
						// If number is higher then limit then unset line in row array
						if( l > lineLimit )
						{
							row.splice( l, 1 );
							continue;
						}
						// Assign lines with html to li array
						li.push ( '<li class="line_' + l + '">' + row[l] + '</li>' );
					}
				
					// If last message is seen by the contact and it was more then 2min set last seen date
					if( r[6] )
					{
						// Set seen
						chbox.lastseen = r[6];
					
						li.push ( '<li class="line_info"><span class="icon"></span><span class="info">' + r[6] + '</span></li>' );
					}
				
					// Join the buffer again
					chbox.pmsg = row.join('<!--message-->');
				}
			
				chbox.innerHTML = '<div id="Chat_Messages_' + u + '" class="inner"><ul>' + li.join('') + '</ul></div>';
			}
			
			/*// Scroll down
			if( scrollAllow['Chat_' + u] )
			{
				chbox.scrollTop = chbox.scrollHeight;
			}*/
		}
	} );
	x.save();
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
			refreshPrivChat( u, n, mode, 'refresh' );
			//playAudio( 'pling', function(){ removeAudio(); }, 10, true );
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
	
	var act;
	
	if( chatGet( 'Chat_' + u ) && chatGet( 'Chat_' + u ).className != 'chattab open' )
	{
		act = 'open';
		
		/*if( !scrollAllow['Chat_' + u] )
		{
			scrollAllow['Chat_' + u] = true;
		}*/
	}
	else if( chatGet( 'Chat_' + u ) && chatGet( 'Chat_' + u ).className == 'chattab open' )
	{
		act = 'close';
	}
	notifications( u );
	refreshPrivChat( u, n, m, act );
}

function chatSettings( ele, component, button )
{
	if( !ele || !component || !button || !chatGet( 'ChatSettings' ) ) return;
	
	if( chatGet( 'ChatSettings' ).className.indexOf( 'open' ) >= 0 )
	{
		chatGet( 'ChatSettings' ).className = '';
		chatGet( 'ChatSettings' ).getElementsByTagName( 'div' )[0].innerHTML = '';
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
				chatGet( 'ChatSettings' ).getElementsByTagName( 'div' )[0].innerHTML = r[1];
				chatGet( 'ChatSettings' ).className = 'open ' + button;
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

// Filter contact list by list
function filterContactList( str )
{
	var c = ge( 'Chat_list' ); c.str = str;
	var eles = c.getElementsByTagName( 'li' );
	var i = str.toLowerCase();
	for( var a = 0; a < eles.length; a++ )
	{
		var span = eles[a].getElementsByTagName( 'span' )[1];
		var sinner = span.innerHTML;
		if( str == '' )
			eles[a].style.display = '';
		else if( sinner.toLowerCase().split( i ).join ( '' ).length < sinner.length )
		{
			eles[a].style.display = '';
		}
		else
		{
			eles[a].style.display = 'none';
		}
	}
}


// Working with a layer, setup chat in parent ----------------------------------

function initLayeredChat()
{
	if( !( parent && parent.hasLayer ) )
		return;
	
	if( !parent.document.getElementById( 'ChatBox' ) )
	{
		var w = parent.CreateManagedWindow( {
			title: 'Chat',
			width: 180,
			height: 435,
			dockable: true,
			minimized: true
		} );
		
		w.addEvent( 'maximize', function(){ 
			refreshChat( false, 'open' ); 
		} );
		
		var cb = parent.document.createElement( 'div' );
		cb.id = 'ChatBox';
		cb.innerHTML = ge( 'ChatBox' ).innerHTML;
		w.appendChild( cb );
		ge( 'ChatBox' ).parentNode.removeChild( ge( 'ChatBox' ) );
	}
	
	parent.checkScroll = function()
	{
		checkScroll();
	}
	
	parent.savePrivChat = function()
	{
		savePrivChat();
	}
	
	// Overwrite open a private chat
	openPrivChat = function ( u, n, m, o )
	{
		if( !u && !n ) return;
		if( !o ) o = false;
	
		var nw = parent.CreateManagedWindow( {
			title: 'Chat with ' + n,
			width: 400,
			height: 300,
			dockable: true
		} );
		
		nw.setContent( '<div id="Chat_' + u + '"><div class="chatpriv"><div id="Chat_head_' + u + '" class="head"><div class="status"><img src="' + ( o > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + '"/></div> <span class="name">' + n + '</span><div class="delete" onclick="closePrivChat( ' + u + ' );return false;">x</div></div><div id="Chat_inner_' + u + '" class="messages" onmouseup="checkScroll(this,\'' + u + '\')"></div><div class="post"><input onkeyup="if( event.keyCode == 13 ) { savePrivChat( \'' + u + '\', \'' + n + '\', this ) }"/></div></div></div>' );
		
		refreshPrivChat( u, n, m, 'open' );
	}
	
	// Initialize private chat
	parent.addPrivChat = function ( u, n, o, m )
	{
		// TODO: Activate connect window
		openPrivChat( u, n, m, o );
	}
}
if( window.addEventListener )
	window.addEventListener( 'load', initLayeredChat );
else window.attachEvent( 'onload', initLayeredChat );

