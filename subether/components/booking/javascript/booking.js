
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

function RefreshBookingCalendar( id, inpid, date )
{
	if( !id || !ge( id ) || !date ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=booking&function=calendar', 'post', true );
	j.addVar ( 'id', id );
	j.addVar ( 'inpid', inpid );
	j.addVar ( 'date', date );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			ge( id ).innerHTML = r[1];
		}
	}
	j.send ();
}

function SetBookingDate( id, display, date )
{
	if( !id || !ge( id + '0' ) || !ge( id + '1' ) ) return;
	
	ge( id + '0' ).value = date;
	ge( id + '1' ).value = display;
	
	CloseBookingCalendar();
	FilterBooking();
}

function CloseBookingCalendar()
{
	if( ge( 'CalendarFromDate' ).className.indexOf( 'open' ) >= 0 )
	{
		ge( 'CalendarFromDate' ).className = ge( 'CalendarFromDate' ).className.split( ' open' ).join( '' );
	}
	if( ge( 'CalendarToDate' ).className.indexOf( 'open' ) >= 0 )
	{
		ge( 'CalendarToDate' ).className = ge( 'CalendarToDate' ).className.split( ' open' ).join( '' );
	}
}

function BookingCalendar( id, inpid, date )
{
	if( !id || !ge( id ) || !inpid || !date ) return;
	
	if( ge( id ).className.indexOf( 'open' ) >= 0 )
	{
		ge( id ).className = ge( id ).className.split( ' open' ).join( '' );
	}
	else
	{
		CloseBookingCalendar();
		
		RefreshBookingCalendar( id, inpid, date );
		
		ge( id ).className = ge( id ).className.split( ' open' ).join( '' ) + ' open';
	}
}

function EditBooking( ele, eid )
{
	if( !ele ) return;
	
	CloseBooking();
	
	ele.data = ele.innerHTML;
	ele.js = ele.getAttribute( 'onclick' );
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=booking&function=bookingedit', 'post', true );
	if( eid )
	{
		j.addVar ( 'eid', eid );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			if( eid )
			{
				ele.setAttribute( 'eventid', eid );
			}
			ele.id = 'EventEdit';
			ele.removeAttribute( 'onclick' );
			ele.innerHTML = r[1];
			ele.parentNode.className = ele.parentNode.className.split( ' closed' ).join( '' ) + ' open';
		}
	}
	j.send ();
}

function FilterBooking()
{
	if( !ge( 'BookingDate' ) || !ge( 'BookingContent' ) ) return;
	
	var inp = ge( 'BookingDate' ).getElementsByTagName( 'input' );
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=booking&function=booking', 'post', true );
	if( inp.length > 0 )
	{
		for( a = 0; a < inp.length; a++ )
		{
			if( inp[a].name )
			{
				j.addVar ( inp[a].name, inp[a].value );
			}
		}
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'BookingContent' ).innerHTML = r[1];
		}
	}
	j.send ();
}

function RefreshBooking( eid, img )
{
	if( !ge( 'BookingContent' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=booking&function=booking', 'post', true );
	if( eid )
	{
		j.addVar ( 'eid', eid );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			// Refresh only by id
			if( eid && ge( 'BookingID_' + eid ) && r[1] )
			{
				ge( 'BookingID_' + eid ).innerHTML = r[1];
				ge( 'BookingID_' + eid ).className = ge( 'BookingID_' + eid ).className.split( ' open' ).join( '' ) + ' closed';
				if( img )
				{
					ge( 'BookingID_' + eid ).className = ge( 'BookingID_' + eid ).className.split( ' edit' ).join( '' ) + '';
				}
			}
			// Remove by id
			else if( eid && ge( 'BookingID_' + eid ) && !r[1] )
			{
				ge( 'BookingID_' + eid ).parentNode.removeChild( ge( 'BookingID_' + eid ) );
			}
			// Refresh all
			else
			{
				ge( 'BookingContent' ).innerHTML = r[1];
			}
		}
	}
	j.send ();
}

function refreshEvent( sid, eid )
{
	RefreshBooking( eid, 'img' );
}

function SignupBooking( ele, eid, slots, amount, fromdate, todate )
{
	if( !ele || !eid || !slots || !amount || !fromdate || !todate ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=booking&action=bookingsignup', 'post', true );
	j.addVar ( 'eid', eid );
	j.addVar ( 'slots', slots );
	j.addVar ( 'amount', amount );
	j.addVar ( 'fromdate', fromdate );
	j.addVar ( 'todate', todate );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			//RefreshBooking( eid );
			//RefreshBooking();
			FilterBooking();
		}
	}
	j.send ();
}

function SignoffBooking( ele, eid, slots, amount, fromdate, todate )
{
	if( !ele || !eid || !slots || !amount || !fromdate || !todate ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=booking&action=bookingsignoff', 'post', true );
	j.addVar ( 'eid', eid );
	j.addVar ( 'slots', slots );
	j.addVar ( 'amount', amount );
	j.addVar ( 'fromdate', fromdate );
	j.addVar ( 'todate', todate );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			//RefreshBooking( eid );
			//RefreshBooking();
			FilterBooking();
		}
	}
	j.send ();
}

function SaveBooking()
{
	if( !ge( 'EventEdit' ) ) return;
	var evt = ge( 'EventEdit' );
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=booking&action=bookingsave', 'post', true );
	if( evt.getAttribute( 'eventid' ) )
	{
		j.addVar ( 'eid', evt.getAttribute( 'eventid' ) );
	}
	if( ge( 'EventName' ) )
	{
		j.addVar ( 'name', ge( 'EventName' ).value );
	}
	if( ge( 'EventPlace' ) )
	{
		j.addVar ( 'place', ge( 'EventPlace' ).value );
	}
	if( ge( 'EventDescription' ) )
	{
		j.addVar ( 'description', ge( 'EventDescription' ).value );
	}
	if( ge( 'EventPrice' ) )
	{
		j.addVar ( 'price', ge( 'EventPrice' ).value );
	}
	if( ge( 'EventSlots' ) )
	{
		j.addVar ( 'slots', ge( 'EventSlots' ).value );
	}
	if( ge( 'EventLimit' ) )
	{
		j.addVar ( 'limit', ge( 'EventLimit' ).value );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			RefreshBooking( evt.getAttribute( 'eventid' ) ? r[1] : '' );
		}
	}
	j.send ();
}

function CloseBooking()
{
	if( ge( 'EventEdit' ) )
	{
		ge( 'EventEdit' ).innerHTML = ( ge( 'EventEdit' ).data ? ge( 'EventEdit' ).data : '' );
		ge( 'EventEdit' ).setAttribute( 'onclick', ge( 'EventEdit' ).js );
		ge( 'EventEdit' ).parentNode.className = ge( 'EventEdit' ).parentNode.className.split( ' open' ).join( '' ) + ' closed';
		ge( 'EventEdit' ).removeAttribute( 'eventid' );
		ge( 'EventEdit' ).removeAttribute( 'id' );
	}
}

function DeleteBooking()
{
	if( ge( 'EventEdit' ) && ge( 'EventEdit' ).getAttribute( 'eventid' ) && confirm( 'Are you sure?' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=booking&action=bookingdelete', 'post', true );
		j.addVar ( 'eid', ge( 'EventEdit' ).getAttribute( 'eventid' ) );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );	
			if ( r[0] == 'ok' )
			{
				RefreshBooking( r[1] );
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
			if( targ.id == 'EventLimit' )
			{
				SaveBooking();
			}
			break;
		// Esc key
		case 27:
			CloseBooking();
			break;
		// Enter key
		case 13:
			SaveBooking();
			break;
		// Delete key
		case 46:
			if( targ.tagName != 'INPUT' )
			{
				DeleteBooking();
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
	if( ge( 'EventEdit' ) && targ.tagName != 'SELECT' && targ.tagName != 'INPUT' && targ.tagName != 'OPTION' )
	{
		CloseBooking();
	}
	else if( ( ge( 'CalendarFromDate' ) || ge( 'CalendarToDate' ) ) && targ.tagName != 'DIV' && targ.tagName != 'A' && targ.tagName != 'BUTTON' )
	{
		CloseBookingCalendar();
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
