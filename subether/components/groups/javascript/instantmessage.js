
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

var chatBuffer = new Object ();
chatBuffer.initialized = false;
chatBuffer.rows = new Array ();
chatBuffer.lastMessage = '0';
chatBuffer.netActivity = false;
chatBuffer.saveIM = function ( pid )
{	
	if( !pid && !ge( 'InstantMessage' ) ) return false;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=groups&action=saveimessage', 'post', true );
	j.addVar ( 'pid', pid );
	j.addVar ( 'message', ge ( 'InstantMessage' ).value );
	ge ( 'InstantMessage' ).value = '';
	j.c = this;
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			this.c.refreshIM ( pid, true );
		}
	}
	j.send ();
	
	checkSession ();
}
chatBuffer.refreshIM = function ( pid, init )
{
	if ( typeof ( ge ) == 'undefined' || typeof ( bajax ) == 'undefined' ) return;
	if( !pid && !ge( 'ListIM' ) ) return false;
	if ( this.netActivity == true ) return;
	this.netActivity = true;
	var j = new bajax ();
	if ( !init )
	{
		setInterval ( 'chatBuffer.refreshIM ( ' + pid + ', true );', 500 );
		init = 'yes';
	}
	else init = '';
	
	// Load messages
	// Tell if we're initing or not
	// Tell pid
	// Tell last message
	j.c = this;
	j.openUrl ( getPath() + '?component=groups&function=getimessages&init='+init, 'post', true );
	j.addVar ( 'pid', pid );
	j.addVar ( 'lastmessage', this.lastMessage );
	j.onload = function ()
	{
		this.c.netActivity = false;
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			//alert( this.getResponseText () );
			var re = r[1].split('<!--message-->');
			// First time (we're initializing)
			if ( !this.c.initialized )
			{
				if ( re )
				{
					// Populate array
					for ( var d = 0; d < 50; d++ )
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
				for ( var u = 50; u >= 0; u-- )
				{
					var d = document.createElement ( 'div' );
					d.id = 'Line_' + u;
					d.innerHTML = typeof ( this.c.rows[u] ) != 'undefined' ? this.c.rows[u] : '&nbsp;';
					ge( 'ListIM' ).appendChild(d);
				}
				this.c.lastMessage = r[2]>0?r[2]:this.c.lastMessage;
			}
			// Reoccupy chat list
			else if ( typeof ( r[2] ) != 'undefined' && this.c.lastMessage != r[2] )
			{
				
				this.c.lastMessage = r[2];
				
				// Shift whole list up
				for ( var p = 50-re.length; p >= 0; p-- )
				{
					if ( p < 0 ) p = 0;
					ge ( 'Line_' + (p+re.length) ).innerHTML = ge ( 'Line_' + (p+"") ).innerHTML;
				}
				// New entries
				for ( var p = 0; p < re.length; p++ )
				{
					ge ( 'Line_' + (p+"") ).innerHTML = re[p];
				}
				
				this.c.lastMessage = r[2]>0?r[2]:this.c.lastMessage;
			}
			// Scroll down
			ge ( 'ListIM' ).scrollTop = ge ( 'ListIM' ).scrollHeight;
			
			// Online Users
			if( ge ( 'RightIM' ) && r[3] ) ge ( 'RightIM' ).innerHTML = r[3];
		}
	}
	j.send ();
}
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
