
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

function CreateBroadcast( ele, e )
{
	ele.data = ele.innerHTML;
	
	ele.innerHTML = '<div id="EditEvent" Date="' + ele.id.split('_')[0] + '">' +
	'<div><input id="EventAttendee" type="text" style="width:94%;" /></div>' +
	'<div><input id="EventRole" type="text" style="width:94%;" /></div>' +
	'<div><input id="EventName" type="text" style="width:94%" /></div>' +
	'<div><input id="EventStart" type="text" style="width:37.5%;" />' +
	'<span style="width:10%;"> - </span>' +
	'<input id="EventEnd" type="text" style="width:37.5%;" /></div>' + 
	'</div>';
}

function RefreshBroadcast( id )
{
	alert( 'Event Saved. Refreshing SlotID:' + id );
}

function SaveBroadcast()
{
	if( !ge( 'EventEdit' ) && !ge( 'EventWrapper' ) ) return;
	var evt = ge( 'EventWrapper' ) ? ge( 'EventWrapper' ) : ge( 'EventEdit' );
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=broadcast&action=eventsave', 'post', true );
	if( evt.getAttribute( 'EventID' ) )
	{
		j.addVar ( 'eid', evt.getAttribute( 'EventID' ) );
	}
	if( ge( 'EventName' ) ) j.addVar ( 'name', ge( 'EventName' ).value );
	if( ge( 'EventType' ) ) j.addVar ( 'type', ge( 'EventType' ).value );
	if( ge( 'EventTime' ) ) j.addVar ( 'start', ge( 'EventTime' ).value );
	if( ge( 'EventUrl' ) ) j.addVar ( 'url', ge( 'EventUrl' ).value );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			RefreshBroadcast( r[1] );
		}
	}
	j.send ();
}

function CloseBroadcast()
{
	if( ge( 'EventEdit' ) )
	{
		ge( 'EventEdit' ).className = 'event admin closed';
		//ge( 'EventEdit' ).parentNode.innerHTML = ge( 'EventEdit' ).parentNode.data;
	}
}

function DeleteBroadcast()
{
	if( ge( 'EventEdit' ) && confirm( 'Are you sure?' ) )
	{
		alert( 'Deleting' );
	}
}

/* --- Global Events -------------------------------------------------------- */

// Check Global Keys
function checkKeys( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var keycode = e.which ? e.which : e.keyCode;
	switch ( keycode )
	{
		// Tab key
		case 9:
			if( targ.id == 'Eventime' )
			{
				SaveBroadcast();
			}
			break;
		// Esc key
		case 27:
			CloseBroadcast();
			break;
		// Enter key
		case 13:
			SaveBroadcast();
			break;
		// Delete key
		case 46:
			DeleteBroadcast();
			break;
		default: break;
	}
}

// Check Global Cliks
function checkClicks( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	if( ge( 'EventEdit' ) && targ.tagName != 'SELECT' && targ.tagName != 'INPUT' && targ.tagName != 'OPTION' )
	{
		CloseBroadcast();
	}
	else if( !targ.parentNode.id ) return;
}

// Global Events
if ( window.addEventListener )
{
	window.addEventListener ( 'keydown', checkKeys );
	window.addEventListener ( 'mousedown', checkClicks );
}
else 
{
	window.attachEvent ( 'onkeydown', checkKeys );
	window.attachEvent ( 'onmousedown', checkClicks );
}
