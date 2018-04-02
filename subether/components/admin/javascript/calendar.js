
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

function RefreshAdminCalendar( date, ele )
{
	if( !date || !ele ) return;
	
	var parent = ele.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
	
	if( !parent ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=admin&function=calendar', 'post', true );
	j.addVar ( 'date', date );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			parent.innerHTML = r[1];
		}
	}
	j.send ();
}

function SetAdminDate( date, display, show, ele )
{
	if( !date || !display || !ele ) return;
	
	var parent = ele.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
	var input = parent.getElementsByTagName( 'input' )[0];
	var visible = parent.getElementsByTagName( 'input' )[1];
	
	if( !parent || !input ) return;
	
	var classes = parent.className.split( ' ' );
	
	parent.className = display + ' ' + classes[1] + ' closed';
	
	input.value = date;
	
	if ( visible )
	{
		visible.value = show;
	}
	
	FilterMemberHours();
}

function CloseAdminCalendar( ele )
{
	if( !ele ) return;
	
	if( ele.className.indexOf( 'open' ) >= 0 )
	{
		ele.className = ele.className.split( ' open' ).join( '' ) + ' closed';
	}
}

function AdminCalendar( ele )
{
	if( !ele || !ele.parentNode ) return;
	
	var parent = ele.parentNode;
	
	if( parent.className.indexOf( 'open' ) >= 0 )
	{
		CloseAdminCalendar( parent );
	}
	else
	{
		parent.className = parent.className.split( ' closed' ).join( '' ) + ' open';
	}
}
