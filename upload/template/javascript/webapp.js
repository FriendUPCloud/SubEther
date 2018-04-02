
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

// Mobile Safari in standalone mode

if ( ( "standalone" in window.navigator ) && window.navigator.standalone )
{
	// If you want to prevent remote links in standalone web apps opening Mobile Safari, change 'remotes' to true
	
	var noddy, remotes = false;
	
	document.addEventListener('click', function(event) 
	{
		noddy = event.target;
		
		// Bubble up until we hit link or top HTML element. Warning: BODY element is not compulsory so better to stop on HTML
		
		while( noddy.nodeName !== "A" && noddy.nodeName !== "HTML" ) 
		{
			noddy = noddy.parentNode;
		}
		
		if( 'href' in noddy && noddy.href.indexOf('http') !== -1 && ( noddy.href.indexOf( document.location.host ) !== -1 || remotes ) )
		{
			event.preventDefault();
			document.location.href = noddy.href;
		}
	
	}, false );
}

