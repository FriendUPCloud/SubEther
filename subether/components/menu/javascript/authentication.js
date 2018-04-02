
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

function openLoginBox ()
{
	if( ge ( 'LoginBox' ).className == 'open' )
	{
		ge ( 'LoginBox' ).className = '';
		return;
	}
	else if ( ge ( 'LoginBox' ).className == '' )
	{
		ge ( 'LoginBox' ).className = 'open';
		ge ( 'webuser' ).focus ();
		return;
	}
}

function openSignupBox ()
{
	var c = ge( 'Content__' );
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=authentication&function=signup', 'post', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( c ) 
			{
				c.innerHTML = r[1];
				c.className = 'signup';
			}
		}
	}
	j.send ();
}

function logout ()
{
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=menu&action=logout', 'post', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			setBrowserStorage( 'uniqueid', '' );
			if( r[1] ) document.location = r[1];
		}
	}
	j.send ();
}

/* --- Global Authentication Event -------------------------------------------------------- */

// Check Global Keys
function checkAKeys( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var keycode = e.which ? e.which : e.keyCode;
	switch ( keycode )
	{
		case 27:
			if( ge( 'LoginBox' ) && ge( 'LoginBox' ).className.indexOf( 'open' ) >= 0 )
			{
				openLoginBox();
			}
			if( ge( 'MenuBox' ) && ge( 'MenuBox' ).className.indexOf( 'open' ) >= 0 )
			{
				openDropDownWindow( 'MenuBox' );	
			}
			break;
		default: break;
	}
}

// Check Global Cliks
function checkAClicks( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var button = e.which ? e.which : e.button;
	/*if( ge( 'LoginBox' ) && ge( 'LoginBox' ).className.indexOf( 'open' ) >= 0 )
	{
		var tags = ge( 'LoginBox' ).getElementsByTagName( '*' );
		if( tags.length > 0 )
		{
			for( a = 0; a < tags.length; a++ )
			{
				if( targ.id == 'LoginBox' || targ == tags[a] || targ.tagName == 'HTML' )
				{
					return;
				}
			}
			openLoginBox();
		}
	}*/
	if( ge( 'MenuBox' ) && ge( 'MenuBox' ).className.indexOf( 'open' ) >= 0 )
	{
		var tags = ge( 'Authentication' ).getElementsByTagName( '*' );
		if( tags.length > 0 )
		{
			for( a = 0; a < tags.length; a++ )
			{
				if( targ.id == 'MenuBox' || targ == tags[a] || targ.tagName == 'HTML' )
				{
					return;
				}
			}
			openDropDownWindow( 'MenuBox' );
		}
	}
}

// Assign Global Listeners
if ( window.addEventListener )
{
	window.addEventListener ( 'keydown', checkAKeys );
	window.addEventListener ( 'mousedown', checkAClicks );
}
else 
{
	window.attachEvent ( 'onkeydown', checkAKeys );
	window.attachEvent ( 'onmousedown', checkAClicks );
}
