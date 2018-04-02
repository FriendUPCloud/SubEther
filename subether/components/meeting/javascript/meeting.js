
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

function saveMeetingMessage ( m )
{
	if( !m ) return false;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=meeting&action=meeting', 'post', true );
	j.addVar ( 'message', m.value );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			m.value = '';
			refreshMeetingMessage();
		}
	}
	j.send ();
}

function refreshMeetingMessage (  )
{
	//return;

	var j = new bajax ();
	j.openUrl ( getPath() + '?component=meeting&function=meeting', 'post', true );
	j.addVar ( 'getmessages', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( r[1] && ge( 'ChatBox' ) && ge( 'ChatBox' ).getElementsByTagName( 'div' )[0] ) 
			{
				ge( 'ChatBox' ).getElementsByTagName( 'div' )[0].innerHTML = r[1];
			}
			if( r[2] && ge( 'ParticipantBox' ) && ge( 'ParticipantBox' ).getElementsByTagName( 'ul' )[0] ) 
			{
				ge( 'ParticipantBox' ).getElementsByTagName( 'ul' )[0].innerHTML = r[2];
			}
			if( r[3] && ge( 'VideoBox' ) && ge( 'VideoBox' ).getElementsByTagName( 'div' )[0] ) 
			{
				ge( 'VideoBox' ).getElementsByTagName( 'div' )[0].innerHTML = r[3];
			}
		}
	}
	j.send ();
}

//setInterval ( 'refreshMeetingMessage();', 3000 );
