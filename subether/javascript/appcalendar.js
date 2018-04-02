
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

function getDocumentLocation ()
{
	var bref = document.location.href;
	bref = bref.split ( '?' )[0].split ( '#' )[0];
	return bref;
}

function changeDate ( month, year )
{
	var cal = ge ( 'Calendar' );
	if( !cal ) return;
	var j = new bajax ();
	j.openUrl ( getDocumentLocation () + '?module=extensions&extension=appointment&action=appcalendar&pid=<?= $this->page->MainID ?>', 'post', true );
	if( month )
	{
		var num = 1;
		if( month == '13' )
		{
			month = 1;
			year = parseFloat ( year ) + num;
		}
		else if( month == '0' )
		{
			month = 12;
			year = parseFloat ( year ) - num;
		}
		j.addVar ( 'month', month );
	}
	if( year ) j.addVar ( 'year', year );
	j.onload = function ( )
	{
		var date = ge ( 'dateValue' );
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{	
			cal.innerHTML = r[1];
			markDay ( date.className );
		}
		else alert( this.getResponseText () );
	}
	j.send ();
}

// pID is a date string DDMMYYYY
function markDay ( pID )
{
	var day = ge ( 'Day_' + pID );
	var el = ge ( 'Calendar' ).getElementsByTagName ( '*' );
	for ( var a = 0; a < el.length; a++ )
	{
		if( pID )
		{
			if( el[a].className.indexOf ( 'Marked' ) >= 0 )
			{
				el[a].className = el[a].className.split ( ' Marked' ).join ( '' );
			}
		}
		else
		{
			if( el[a].className.indexOf ( 'Selected' ) >= 0 )
			{
				el[a].className = el[a].className.split ( ' Marked' ).join ( '' );
				el[a].className = el[a].className + ' Marked';
				var pID = el[a].id.split ( 'Day_' )[1];
			}
		}
	}
	if( day ) day.className = day.className + ' Marked';
	var j = new bajax ( );
	j.openUrl ( getDocumentLocation () + '?module=extensions&extension=appointment&action=web_timebox&pid=<?= $this->page->MainID ?>', 'post', true );
	j.addVar ( 'pID', pID );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			var obox = ge ( 'TimeBox' );
			var topt = ge ( 'timeOptions' );
			obox.className = 'Open';
			topt.innerHTML = r[1];
			setDateTime();
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}
//markDay();
