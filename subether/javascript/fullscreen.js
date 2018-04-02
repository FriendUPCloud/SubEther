
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

function toggleFullScreen( e )
{
	if ( !document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement )
	{
		// current working methods
		if ( document.documentElement.requestFullscreen )
		{
			document.documentElement.requestFullscreen();
		}
		else if ( document.documentElement.msRequestFullscreen )
		{
			document.documentElement.msRequestFullscreen();
		}
		else if ( document.documentElement.mozRequestFullScreen )
		{
			document.documentElement.mozRequestFullScreen();
		}
		else if ( document.documentElement.webkitRequestFullscreen )
		{
			document.documentElement.webkitRequestFullscreen( Element.ALLOW_KEYBOARD_INPUT );
		}
	}	
	else
	{
		if ( document.exitFullscreen )
		{
			document.exitFullscreen();
		}
		else if ( document.msExitFullscreen )
		{
			document.msExitFullscreen();
		}
		else if ( document.mozCancelFullScreen )
		{
			document.mozCancelFullScreen();
		}
		else if ( document.webkitExitFullscreen )
		{
			document.webkitExitFullscreen();
		}
	}
}



document.addEventListener( "keydown", function( e )
{
	if ( e.keyCode == 13 && document.body.className.indexOf( 'presentation' ) >= 0 )
	{
		toggleFullScreen();
	}
}, false );
