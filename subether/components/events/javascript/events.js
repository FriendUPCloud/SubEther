
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

function SetEventDate( date )
{
	if( !ge( 'EventDate' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=events&function=eventdate&js=true', 'post', true );
	j.addVar ( 'date', date );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'EventDate' ).innerHTML = r[1];
			RefreshEvent();
		}
	}
	j.send ();
}

function CreateEvent( ele, e )
{
	ele.data = ele.innerHTML;
	
	ele.innerHTML = '<div id="EditEvent" Date="' + ele.id.split('_')[0] + '" EventID="' + ele.id.split('_')[1] + '" EventSlotID="' + ele.id.split('_')[2] + '">' +
	'<div><input id="EventAttendee" type="text" style="width:94%;" placeholder="Attendee"/></div>' +
	'<div><input id="EventRole" type="text" style="width:94%;" placeholder="Role"/></div>' +
	'<div><input id="EventName" type="text" style="width:94%" placeholder="EventName"/></div>' +
	'<div><input id="EventStart" type="text" style="width:37.5%;" placeholder="TimeStart"/>' +
	'<span style="width:10%;"> - </span>' +
	'<input id="EventEnd" type="text" style="width:37.5%;" placeholder="TimeStart"/></div>' + 
	'</div>';
}

function SearchEvent( ele, type, e, all )
{
	if ( !e ) e = window.event;
	
	var keycode = e.which ? e.which : e.keyCode;
	
	CloseEventSearch();
	
	if( !ele || !type || keycode == 27 ) return;
	
	var div = document.createElement( 'div' );
	div.id = 'FindEvents';
	ele.parentNode.appendChild( div );
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=events&function=eventsearch', 'post', true );
	j.addVar ( 'type', type );
	if( ele.value && !all ) j.addVar ( 'keyword', ele.value );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'FindEvents' ).className = 'open';
			ge( 'FindEvents' ).innerHTML = r[1];
		}
	}
	j.send ();
}

function SelectEventInput( ele, type, e )
{
	if( !ele ) return;
	
	if( ge( 'FindEvents' ) && ge( 'FindEvents' ).className == 'open' )
	{
		CloseEventSearch();
	}
	else
	{
		ele.focus();
		ele.select();
		
		if( type ) SearchEvent( ele, type, e, 'all' );
	}
}

function CloseEventSearch()
{
	if( ge( 'FindEvents' ) )
	{
		ge( 'FindEvents' ).parentNode.removeChild( ge( 'FindEvents' ) );
	}
}

function SetSearchValue( ele, id )
{
	if( !ele || !ele.getAttribute('value') || !id ) return;
	
	ge( id ).value = ele.innerHTML;
	ge( id ).setAttribute( 'vid', ele.getAttribute('value') );
	ge( id ).focus();
	ge( id ).select();
	
	CloseEventSearch();
}

function EditEvent( ele, e, date, eid, sid, mode )
{
	if ( !ele || !date ) return;
	
	if ( ele.parentNode.className.indexOf( 'open' ) >= 0 )
	{
		CloseEvent();
		return;
	}
	
	ele.data = ele.innerHTML;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=events&function=eventedit', 'post', true );
	j.addVar ( 'date', date );
	if ( eid > 0 ) j.addVar ( 'eid', eid );
	if ( sid > 0 ) j.addVar ( 'sid', sid );
	if ( mode ) j.addVar ( 'mode', mode );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			CloseEvent();
			ele.innerHTML = r[1];
			
			ele.parentNode.className = ele.parentNode.className.split( ' closed' ).join( '' ).split( ' open' ).join( '' ) + ' open';
			
			var inp = ele.getElementsByTagName( 'input' );
			
			if ( inp.length > 0 )
			{
				inp[0].focus();
			}
		}
	}
	j.send ();
}

function InviteByICS( eid )
{
	if ( eid && confirm( 'Vil du sende email invitasjon til deltakerene?' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=events&action=eventinvite&eid=' + eid, 'post', true );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				if ( r[1] )
				{
					alert( r[1] );
				}
			}
			else alert( this.getResponseText() );
		}
		j.send ();
	}
}

function RefreshEvent( sid, eid )
{
	if( !ge( 'CalendarContent' ) ) return;
	var mode = window.location.href.split('?r=')[1];
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=events&function=events' + ( mode ? '&r=' + mode : '' ) + '&js=true', 'post', true );
	if( eid > 0 ) j.addVar ( 'eid', eid );
	if( sid > 0 ) j.addVar ( 'sid', sid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'CalendarContent' ).innerHTML = r[1];
		}
	}
	j.send ();
}

function RefreshPanelEvent()
{
	if( !ge( 'EventPanel' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=events&function=panel&refresh=true', 'post', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'EventPanel' ).innerHTML = r[1];
		}
	}
	j.send ();
}

function ChangeCalendar( mode, date )
{
	document.location = getPath() + '?r=' + mode + ( date ? ( '&basetime=' + date ) : '' );
}

function SaveEvent()
{
	var ele;
	
	if( ge( 'EventEditor' ) )
	{
		ele = ge( 'EventEditor' );
	}
	else if( ge( 'EditEvent' ) )
	{
		ele = ge( 'EditEvent' );
	}
	
	if( !ele ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=events&action=eventsave', 'post', true );
	// --- EventMode -----------------------------------------------------------------------------------
	if( ele.className.indexOf( 'extended' ) >= 0 )
	{
		//j.addVar ( 'mode', ele.className );
		j.addVar ( 'mode', 'extended' );
	}
	// --- EventID -------------------------------------------------------------------------------------
	if( ge( 'EventName' ).getAttribute( 'vid' ) > 0 )
	{
		j.addVar ( 'vid', ge( 'EventName' ).getAttribute( 'vid' ) );
	}
	if( ele.getAttribute( 'EventID' ) > 0 )
	{
		j.addVar ( 'eid', ele.getAttribute( 'EventID' ) );
	}
	// --- HourSlotID ----------------------------------------------------------------------------------
	if( ele.getAttribute( 'EventSlotID' ) > 0 )
	{
		j.addVar ( 'sid', ele.getAttribute( 'EventSlotID' ) );
	}
	// --- Date ----------------------------------------------------------------------------------------
	if( ele.getAttribute( 'Date' ) )
	{
		j.addVar ( 'date', ele.getAttribute( 'Date' ) );
	}
	// --- UserID --------------------------------------------------------------------------------------
	if( ge( 'EventAttendee' ) )
	{
		j.addVar ( 'attendee', ge( 'EventAttendee' ).getAttribute( 'vid' ) && ge( 'EventAttendee' ).value.length > 0 ? ge( 'EventAttendee' ).getAttribute( 'vid' ) : ge( 'EventAttendee' ).value );
	}
	// --- Role ----------------------------------------------------------------------------------------
	if( ge( 'EventRole' ) )
	{
		j.addVar ( 'role', ge( 'EventRole' ).value );
	}
	// --- Details -------------------------------------------------------------------------------------
	if( ge( 'EventDetails' ) )
	{
		j.addVar ( 'details', ge( 'EventDetails' ).value );
	}
	// --- Type -------------------------------------------------------------------------------------
	if( ge( 'EventType' ) )
	{
		j.addVar ( 'type', ge( 'EventType' ).value );
	}
	// --- Place ---------------------------------------------------------------------------------------
	if( ge( 'EventPlace' ) )
	{
		j.addVar ( 'place', ge( 'EventPlace' ).value );
	}
	// --- Access --------------------------------------------------------------------------------------
	if( ge( 'EventAccess' ) )
	{
		j.addVar ( 'access', ge( 'EventAccess' ).value );
	}
	// --- Slots ---------------------------------------------------------------------------------------
	if( ge( 'EventSlots' ) )
	{
		j.addVar ( 'slots', ge( 'EventSlots' ).value );
	}
	// --- Start date ----------------------------------------------------------------------------------
	if( ge( 'EventStartDate' ) )
	{
		j.addVar ( 'datestart', ge( 'EventStartDate' ).value );
	}
	// --- Start date ----------------------------------------------------------------------------------
	if( ge( 'EventEndDate' ) )
	{
		j.addVar ( 'dateend', ge( 'EventEndDate' ).value );
	}
	// --- Event name if new ---------------------------------------------------------------------------
	if( ge( 'EventName' ) )
	{
		j.addVar ( 'event', ge( 'EventName' ).value );
	}
	// --- Start time ----------------------------------------------------------------------------------
	if( ge( 'EventStart' ) )
	{
		j.addVar ( 'start', ge( 'EventStart' ).value );
	}
	// --- End time ------------------------------------------------------------------------------------
	if( ge( 'EventEnd' ) )
	{
		j.addVar ( 'end', ge( 'EventEnd' ).value );
	}
	// --- Members -------------------------------------------------------------------------------------
	if( ge( 'FindMembers' ) )
	{
		var mem = ge( 'FindMembers' ).getElementsByTagName( 'input' );
		
		if( mem.length > 0 )
		{
			var usr;
			
			for( var a = 0; a < mem.length; a++ )
			{
				if( mem[a] && mem[a].value )
				{
					usr = ( usr ? ( usr + ',' + mem[a].value ) : mem[a].value );
				}
			}
			
			if( usr )
			{
				j.addVar ( 'users', usr );
			}
		}
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			RefreshEvent( r[1] );
			RefreshPanelEvent();
			
			if ( typeof loadShareMessages === 'function' )
			{
				loadShareMessages( false, true );
			}
		}
	}
	j.send ();
}

function SignupEvent( sid, ele )
{
	if( !sid ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=events&action=eventsignup', 'post', true );
	j.addVar ( 'sid', sid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( ele && ele.parentNode )
			{
				ele.parentNode.innerHTML = '<button onclick="SignoffEvent(\'' + sid + '\',this)">Sign off</button>';
			}
			
			RefreshPanelEvent();
		}
	}
	j.send ();
}

function SignoffEvent( sid, ele )
{
	if( !sid ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=events&action=eventsignoff', 'post', true );
	j.addVar ( 'sid', sid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( ele && ele.parentNode )
			{
				ele.parentNode.innerHTML = '<button onclick="SignupEvent(\'' + sid + '\',this)">Sign up</button>';
			}
			
			RefreshPanelEvent();
		}
	}
	j.send ();
}

function ViewEvent( eid, cid, time )
{
	if( eid )
	{
		document.location = getPath() + '?r=day' + ( cid ? ( '&categoryid=' + cid ) : '' ) + '&event=' + eid + ( time ? ( '&basetime=' + time ) : '' );
	}
}

function CloseEvent()
{
	if( ge( 'EditEvent' ) && ge( 'EditEvent' ).parentNode.data )
	{
		if ( ge( 'EditEvent' ).parentNode.className.indexOf( 'open' ) >= 0 || ge( 'EditEvent' ).parentNode.className.indexOf( 'closed' ) >= 0 )
		{
			ge( 'EditEvent' ).parentNode.className = ge( 'EditEvent' ).parentNode.className.split( ' open' ).join( '' ).split( ' closed' ).join( '' ) + '';
		}
		else if ( ge( 'EditEvent' ).parentNode.parentNode.className.indexOf( 'open' ) >= 0 || ge( 'EditEvent' ).parentNode.parentNode.className.indexOf( 'closed' ) >= 0 )
		{
			ge( 'EditEvent' ).parentNode.parentNode.className = ge( 'EditEvent' ).parentNode.parentNode.className.split( ' open' ).join( '' ).split( ' closed' ).join( '' ) + '';
		}
		
		ge( 'EditEvent' ).parentNode.innerHTML = ge( 'EditEvent' ).parentNode.data;
	}
	else if( ge( 'EventEditor' ) && ge( 'EventEditor' ).parentNode )
	{
		if ( ge( 'EventEditor' ).parentNode.className.indexOf( 'open' ) >= 0 || ge( 'EventEditor' ).parentNode.className.indexOf( 'closed' ) >= 0 )
		{
			ge( 'EventEditor' ).parentNode.className = ge( 'EventEditor' ).parentNode.className.split( ' open' ).join( '' ).split( ' closed' ).join( '' ) + ' closed';
		}
		else if ( ge( 'EventEditor' ).parentNode.parentNode.className.indexOf( 'open' ) >= 0 || ge( 'EventEditor' ).parentNode.parentNode.className.indexOf( 'closed' ) >= 0 )
		{
			ge( 'EventEditor' ).parentNode.parentNode.className = ge( 'EventEditor' ).parentNode.parentNode.className.split( ' open' ).join( '' ).split( ' closed' ).join( '' ) + ' closed';
		}
		
		ge( 'EventEditor' ).parentNode.innerHTML = ge( 'EventEditor' ).parentNode.data;
	}
}

function DeleteEvent( id )
{
	if( ( id > 0 || ( ge( 'EditEvent' ) && ge( 'EditEvent' ).getAttribute( 'EventID' ) > 0 ) ) && confirm( 'Are you sure?' ) )
	{
		var eid = ( id ? id : ge( 'EditEvent' ).getAttribute( 'EventID' ) );
		var sid = ( ge( 'EditEvent' ) ? ge( 'EditEvent' ).getAttribute( 'EventSlotID' ) : false );
		
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=events&action=eventdelete', 'post', true );
		if( eid ) j.addVar ( 'eid', eid );
		if( sid ) j.addVar ( 'sid', sid );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				RefreshEvent();
				RefreshPanelEvent();
			}
		}
		j.send ();
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
			if ( ge( 'EditEvent' ) && !ge( 'EditEvent' ).className && targ.id == 'EventEnd' )
			{
				SaveEvent();
			}
			CloseEventSearch();
			break;
		// Esc key
		case 27:
			if ( ge( 'FindEvents' ) )
			{
				CloseEventSearch();
			}
			else
			{
				CloseEvent();
			}
			break;
		// Enter key
		case 13:
			SaveEvent();
			break;
		// Delete key
		case 46:
			if ( ge( 'EditEvent' ) && !ge( 'EditEvent' ).className )
			{
				DeleteEvent();
			}
			break;
		default: break;
	}
}

// Check Global Cliks
function checkClicks( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	
	if ( ge( 'FindEvents' ) )
	{
		var tags = ge( 'FindEvents' ).getElementsByTagName( '*' );
		if ( tags.length > 0 )
		{
			for ( a = 0; a < tags.length; a++ )
			{
				if ( targ.id == 'FindEvents' || targ.id == 'EventName' || targ.id == 'EventAttendee' || targ.id == 'EventRole' || targ == tags[a] )
				{
					return;
				}
			}
			
			CloseEventSearch();
		}
	}
	else if ( ge( 'EditEvent' ) )
	{
		var tags = ge( 'EditEvent' ).getElementsByTagName( '*' );
		if ( tags.length > 0 )
		{
			for ( a = 0; a < tags.length; a++ )
			{
				if ( targ.id == 'EditEvent' || targ == tags[a] )
				{
					return;
				}
			}
			CloseEvent();
		}
	}
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
