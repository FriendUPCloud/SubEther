
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

// IRC Internet Relay Chat clone! ----------------------------------------------

var chatBufLimit = 2500;

var chatBuffer = new Object ();
chatBuffer.initialized = false;
chatBuffer.rows = new Array ();
chatBuffer.lastMessage = '0';
chatBuffer.netActivity = false;
chatBuffer.loadingIntr = false;
chatBuffer.refreshIntr = false;
chatBuffer.saveIM = function ( pid )
{	
	if( !pid && !ge( 'InstantMessage' ) ) return false;
	
	// Stop all net activity if possible
	if ( !this.netActivity )
	{
		this.netActivity = true;
	}

	var uri = document.location.href.split ( '?' )[0].split ( '#' )[0];
	
	var chan = ge ( 'ChatCategoryID' ).value;
	var m = ge ( 'InstantMessage' ).value;
	var mes = m;
	
	// Encrypt
	mes = Encrypt ( mes, chan );
	
	var j = new bajax ();	
	j.c = this;
	j.openUrl ( uri + '?component=irc&action=saveimessage', 'post', true );
	j.addVar ( 'pid', pid );
	j.addVar ( 'message', mes );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			if( r[1] && ge( 'ListIM' ) )
			{
				var div = document.createElement( 'div' );
				div.innerHTML = r[1];
				ge( 'ListIM' ).appendChild( div );
				// Scroll down
				ChatListScrollDown ();
			}
		}
		
		// Say all is clear!
		//checkSession ();
		this.c.netActivity = false;
		this.c.refreshIM ( pid, true );
	}
	j.send ();
	
	ge ( 'InstantMessage' ).value = '';
}
chatBuffer.loading = function ()
{
	ge ( 'ListIM' ).innerHTML += '.';
}
chatBuffer.refreshIM = function ( pid, init )
{
	if ( this.netActivity || running( 'irc_' + pid, true ) ) 
	{
		return;
	}
	
	if ( typeof ( ge ) == 'undefined' || typeof ( bajax ) == 'undefined' ) return;
	if( !pid && !ge( 'ListIM' ) ) return false;
	
	this.netActivity = true;
	
	if ( !init )
	{
		if ( !chatBuffer.loadingIntr )
		{
			chatBuffer.loadingIntr = setInterval ( 'chatBuffer.loading()', 200 );
		}
		if ( !this.refreshIntr )
		{
			this.refreshIntr = setInterval ( 'chatBuffer.refreshIM ( ' + pid + ', true );', 1000 );
		}
		init = 'yes';
	}
	else 
	{
		init = '';
	}
	
	var j = new bajax ();
	
	
	// Load messages
	// Tell if we're initing or not
	// Tell pid
	// Tell last message
	j.c = this;
	
	var uri = document.location.href.split ( '?' )[0].split ( '#' )[0];
	
	j.openUrl ( uri + '?component=irc&function=getimessages&init='+init, 'post', true );
	j.addVar ( 'pid', pid );
	j.addVar ( 'lastmessage', this.lastMessage );
	j.onload = function ()
	{
		if ( this.c.loadingIntr )
		{
			clearInterval ( this.c.loadingIntr );
			this.c.loadingIntr = false;
		}
		var r = this.getResponseText ().split ( '<!--separate-->' );
		var chan = ge ( 'ChatCategoryID' ).value;
		if ( r[0] == 'ok' )
		{
			var re = r[1].split('<!--message-->');
			
			// First time (we're initializing)
			if ( !this.c.initialized )
			{
				if ( re )
				{
					// Populate array
					for ( var d = 0; d < chatBufLimit; d++ )
					{
						if ( d >= re.length )
						{
							this.c.rows[d] = '&nbsp;';
						}
						else this.c.rows[d] = re[d];
					}
				}
				// Mark it
				this.c.initialized = true;
				ge( 'ListIM' ).innerHTML = '';
				
				// Populate list im div
				var outDivs = new Array ();
				for ( var u = chatBufLimit - 1; u >= 0; u-- )
				{
					var mesg = this.c.rows[u];
					if ( typeof ( Decrypt ) != 'undefined' )
						mesg = Decrypt ( mesg, chan );
					outDivs.push ( '<div id="Line_' + u + '">' + mesg + '</div>' );
				}
				ge ( 'ListIM' ).innerHTML = outDivs.join ( '');
				this.c.lastMessage = r[2]>0?r[2]:this.c.lastMessage;
			}
			// Reoccupy chat list
			else if ( typeof ( r[2] ) != 'undefined' && this.c.lastMessage != r[2] )
			{
				this.c.lastMessage = r[2];
				// Shift whole list up
				for ( var p = chatBufLimit-1-re.length; p >= 0; p-- )
				{
					if ( p < 0 ) p = 0;
					ge ( 'Line_' + (p+re.length) ).innerHTML = ge ( 'Line_' + (p+"") ).innerHTML;
				}
				// New entries
				for ( var p = 0; p < re.length; p++ )
				{
					var mesg = re[p];
					if ( typeof ( Decrypt ) != 'undefined' )
						mesg = Decrypt ( mesg, chan );
					ge ( 'Line_' + (p+"") ).innerHTML = mesg;
				}
				this.c.lastMessage = r[2]>0?r[2]:this.c.lastMessage;
			}
			// Scroll down
			ChatListScrollDown ();
			
			// Online Users
			if( ge ( 'RightIM' ) && r[3] ) ge ( 'RightIM' ).innerHTML = r[3];
			
			// Audio Notifications
			if( r[4] ) RunAlert( r[4] );
			
		}
		// So, tell we're completed
		this.c.netActivity = false;
		running( 'irc_' + pid, false );
	}
	j.send ();
}

function ChatListScrollDown ( )
{
	ge ( 'ListIM' ).scrollTop = ge ( 'ListIM' ).scrollHeight;
}

// First init
if ( window.id == 'ChatWindow' )
{
	chatBuffer.refreshIM ( window.channel );
	ge ( 'InstantMessage' ).onkeydown = function ( e )
	{
		if ( !e ) e = window.event;
		var kc = e.which ? e.which : e.keyCode;
		if ( kc == 13 )
		{
			chatBuffer.saveIM ( window.channel );
		}
	}
}

/* Take the chat and pop it out --------------------------------------------- */

function PopoutChat ( chan )
{
	var win = window.open ( '', 'IRC-like Chat', 'width=720,height=600,topbar=no,scrolling=no,resize=no' );
	win.id = 'ChatWindow';
	win.channel = chan;
	
	var bref = document.getElementsByTagName ( 'base' )[0].href;
	
	win.document.write ( '<html><head><title>IRC-Like Chat</title><base href="' + bref + '"/></head><body><input type="hidden" id="ChatCategoryID" value="' + ge ( 'ChatCategoryID' ).value  + '"/></body></html>' );
	
	var c = win.document.createElement ( 'div' );
	c.id = 'ChatPopout';
	
	var d = win.document.createElement ( 'div' );
	d.id = 'Content';
	d.innerHTML = '<div id="MainIM"><div id="ListIM"></div></div>' + 
				'<div id="UserIM"><div id="RightIM"></div></div>';
	c.appendChild ( d );
	
	var g = win.document.createElement ( 'div' );
	var inner = win.document.createElement ( 'div' );
	var inp = win.document.createElement ( 'input' );
	inp.id = 'InstantMessage';
	inner.appendChild ( inp );
	g.appendChild ( inner ); 
	g.id = 'InputGui';
	c.appendChild ( g );
	
	var stl = win.document.createElement ( 'link' );
	stl.rel = 'stylesheet';
	stl.href = 'subether/components/irc/css/irc.css';
	
	var wic = win.document.createElement ( 'link' );
	wic.rel = 'stylesheet';
	wic.href = 'subether/components/irc/css/popupwindow.css';
	
	var scripts = [
		'lib/javascript/arena-lib.js',
		'lib/javascript/bajax.js',
		'subether/components/irc/javascript/irc.js',
		'subether/components/irc/javascript/alerts.js',
		'extensions/session/heartbeat.js'
	];
	
	for ( var a = 0; a < scripts.length; a++ )
	{
		var scr = win.document.createElement ( 'script' );
		scr.src = scripts[a];
		win.document.getElementsByTagName ( 'head' )[0].appendChild ( scr );
	}
	
	win.document.body.appendChild ( stl );
	win.document.body.appendChild ( wic );
	win.document.body.appendChild ( c );
}
