
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

if( typeof console == "undefined" ) 
{
    this.console = { log: function() {} };
    console.log( 'console.log init' );
}

function bottomFix ()
{
	if ( !document.getElementById ( 'Footer__' ) )
	{
		return setTimeout ( 'bottomFix ()', 5 );
	}
	
	var t = document.getElementById ( 'TopBox__' );
	var c = document.getElementById ( 'CenterBox__' );
	var f = document.getElementById( 'Footer__' );
	
	if( !t && !c && !f ) return;
	
	if( getDocumentHeight() < ( getElementHeight( t ) + getElementHeight( c ) + getElementHeight( f ) ) )
	{
		f.style.position = 'relative';
	}
	else f.style.position = '';
	
	// Fix table container
	if( ge( 'Table_Fields' ) )
	{
		ge( 'Table_Fields' ).style.minHeight = window.innerHeight - ( ge( 'TopBox__' ).offsetHeight + ge( 'Footer__' ).offsetHeight - 1 ) + 'px';
		ge( 'Table_Fields' ).style.marginBottom = '-1px';
	}
}
//bottomFix ();

var _events = new Array ();

function AddEvent ( type, func )
{
	if ( !_events[type] ) 
	{
		_events[type] = new Array ();
	}
	_events[type].push ( func );
	if ( window.attachEvent )
	{
		window.attachEvent ( type, func, false );
	}
	else 
	{
		window.addEventListener ( type.substr ( 2, type.length - 2 ), func, false );
	}
	return func;
}

/*AddEvent( 'onresize', bottomFix );
AddEvent( 'onscroll', bottomFix );
AddEvent( 'onclick', bottomFix );*/
