
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

var mInterval = new Object ();
var lineLimit = 300;

function refreshMessageList ( uid )
{
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=messages&function=getmessages&rl=true', 'post', true );
	j.addVar ( 'init', 1 );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( r[1] ) 
			{
				if( ge( 'ListIM_inner' ) )
				{
					ge( 'ListIM_inner' ).innerHTML = r[1];
				}
				getCurrentMessage( uid );
			}
		}
	}
	j.send ();
}

function getCurrentMessage ( uid )
{
	var li;
	
	if( ge( 'ListIM' ) )
	{
		li = ge( 'ListIM' ).getElementsByTagName( 'li' );
	}
	if( uid && ge( 'Message_' + uid ) ) 
	{
		ge( 'Message_' + uid ).className = 'current';
	}
	else if( !uid && li && li.length ) 
	{
		li[0].className = 'current';
		li[0].getElementsByTagName( 'a' )[0].click();
	}
	
	// If in mobile view
	if( uid && window.innerWidth <= 1024 )
	{
		openMessage( uid );
	}
}

function openMessage ( uid )
{
	if( !uid || !ge( 'Message_Post' ) ) return false;
	if( imbox )
	{
		var imbox = false;
	}
	if( imbox && imbox.lastmessage ) alert( uid + ' .. ' + imbox.lastmessage );
	clearInterval( mInterval.list );
	clearInterval( mInterval.message );
	if( ge( 'ListIM' ) )
	{
		mInterval.list = setInterval ( 'refreshMessageList( \'' + uid + '\' );', 10000 );
	}
	mInterval.message = setInterval ( 'refreshMessage( \'' + uid + '\' );', 3000 );
	ge( 'Message_Post' ).innerHTML = '<input placeholder="Write a reply" onkeyup="if( event.keyCode == 13 ) { saveMessage( this, \'' + uid + '\' ) }">';
	if( ge( 'ListIM' ) )
	{
		refreshMessageList( uid );
	}
	refreshMessage( uid, 1 );
}

var scrollpos = true;

function denyScroll( ele )
{
	if( !ele ) return;
	scrollpos = false;
	if( Math.round( ele.scrollHeight - ele.scrollTop ) < 470 )
	{
		scrollpos = true;
	}
}

function refreshMessage ( uid, reset )
{
	if( !uid || !ge( 'RightIM_inner' ) ) return;
	
	var imbox = ge( 'RightIM_inner' );
	
	if( reset )
	{
		imbox.pmsg = false;
		imbox.init = false;
		imbox.lastreq = false;
		imbox.lastmessage = false;
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=messages&function=getmessages&rm=true', 'post', true );
	j.addVar ( 'limit', lineLimit );
	j.addVar ( 'userid', uid );
	if( imbox.lastmessage > 0 )
	{
		j.addVar ( 'lastmessage', imbox.lastmessage );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			// Don't update on equal data
			if ( imbox.innerHTML != '' && imbox.pmsg )
			{
				if ( imbox.pmsg == r[1] || ( imbox.lastreq != '' && imbox.lastreq == r[1] ) )
				{
					return;
				}
			}
			
			// If this is the first time and no last message
			if( !imbox.init )
			{
				imbox.pmsg = r[1];
				imbox.init = true;
				imbox.lastreq = false;
			}
			// Else use the buffer and add new lines to it
			else
			{
				imbox.pmsg = r[1] + imbox.pmsg;
				imbox.lastreq = r[1];
			}
			
			// Get messages
			var row = imbox.pmsg.split('<!--message-->');
			
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
				
				// Join the buffer again
				imbox.pmsg = row.join('<!--message-->');
			}
			
			imbox.innerHTML = '<div class="inner"><ul>' + li.join('') + '</ul></div>';
			
			// Scroll down
			if( scrollpos )
			{
				ge( 'RightIM' ).scrollTop = ge( 'RightIM' ).scrollHeight;
			}
			
			imbox.lastmessage = r[2];
		}
	}
	j.send ();
}

function saveMessage ( m, uid )
{
	if( !m || !uid ) return false;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=messages&action=savemessage', 'post', true );
	j.addVar ( 'userid', uid );
	j.addVar ( 'message', m.value );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			m.value = '';
			refreshMessage( uid );
		}
	}
	j.send ();
}
