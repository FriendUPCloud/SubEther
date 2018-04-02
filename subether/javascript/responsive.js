
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

function initResponsive()
{
	if ( !document.body )
		return setTimeout ( 'initResponsive()', 5 );
	
	var dWidth = getDocumentWidth();
	var dHeight = getDocumentHeight();
	var agent = navigator.userAgent.toLowerCase();
	var type = 'browser';
	
	// If document width is the same and lower then 980 switch to mobile view
	if( dWidth <= 980 )
	{
		if( document.body.className.indexOf( 'mobile' ) < 0 && window.location.href.indexOf( 'mobileview' ) < 0 )
		{
			var j = new bajax ();
			j.openUrl ( getUrl() + '?global=true&function=responsive', 'post', true );
			j.addVar ( 'UserAgent', 'mobile' );
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );
				if ( r[0] == 'ok' )
				{
					document.body.className = document.body.className.split(' mobile').join('');
					document.body.className = document.body.className.split(' browser').join('');
					document.body.className = document.body.className.split(' bot').join('');
					document.body.className = document.body.className.split(' tablet').join('');
					
					document.body.className = document.body.className + ' ' + r[1];
					
					location.reload();
					//console.log( r[1] );
				}
			}
			j.send ();
		}
		
		type = 'mobile';
	}
	// Else if document width is higher then 980 switch to browser view
	else
	{
		if( document.body.className.indexOf( 'mobile' ) >= 0 && window.location.href.indexOf( 'mobileview' ) < 0 )
		{
			var j = new bajax ();
			j.openUrl ( getUrl() + '?global=true&function=responsive', 'post', true );
			j.addVar ( 'UserAgent', 'web' );
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );
				if ( r[0] == 'ok' )
				{
					document.body.className = document.body.className.split(' mobile').join('');
					document.body.className = document.body.className.split(' browser').join('');
					document.body.className = document.body.className.split(' bot').join('');
					document.body.className = document.body.className.split(' tablet').join('');
					
					document.body.className = document.body.className + ' browser';
					
					location.reload();
					//console.log( r[1] );
				}
			}
			j.send ();
		}
	}
	
	//document.title = dWidth + ' x ' + dHeight + ' | ' + type;
	
	return true;
}

function bottomFixer ()
{
	if ( !document.getElementById ( 'Footer__' ) )
	{
		return setTimeout ( 'bottomFixer ()', 50 );
	}
	
	var body = document.body;
	var html = document.documentElement;
	
	//var height = body.scrollHeight;
	var height = html.scrollHeight;
	
	var f = document.getElementById ( 'Footer__' );
	
	//console.log( getDocumentHeight() + ' < ' + height );
	
	//console.log( getDocumentHeight() + ' < ( ' + height + ' + ' + f.offsetHeight + ' = ' + ( height + f.offsetHeight ) + ' )' );
	
	//if( getDocumentHeight() < ( height + 80 ) )
	if( getDocumentHeight() < height )
	//if( getDocumentHeight() < ( height + f.offsetHeight ) )
	{
		f.style.position = 'relative';
	}
	else f.style.position = '';
	
	// Fix table container
	if( ge( 'Table_Fields' ) )
	{
		ge( 'Table_Fields' ).style.minHeight = window.innerHeight - ( f.offsetHeight - 1 ) + 'px';
		ge( 'Table_Fields' ).style.marginBottom = '-1px';
	}
	
	return true;
}

bottomFixer();

var DocumentHeight;

function onchangeListener()
{
	if ( document.body )
	{
		var body = document.body;
		var html = document.documentElement;
		
		//var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
		//var height = body.scrollHeight;
		var height = html.scrollHeight;
		
		//console.log( getDocumentHeight() + ' -- ' + DocumentHeight );
		if ( height != DocumentHeight )
		{
			//console.log( '-' );
			//console.log( 'ok run bottomfixer ... ' + height + ' != ' + DocumentHeight );
			// Run onchange functions
			bottomFixer();
			// Set current document height
			DocumentHeight = height;
		}
	}
	
	//setTimeout ( 'onchangeListener()', 50 );
}

setInterval( 'onchangeListener()', 50 );
onchangeListener();

function setVersion()
{
	if ( ge( 'nodeversion' ) && ge( 'NODE_VERSION' ) )
	{
		ge( 'NODE_VERSION' ).innerHTML = ge( 'nodeversion' ).getAttribute( 'content' );
	}
}

// Commented out because it crashes on mobiles
// Assign Global Listeners
if ( window.addEventListener )
{
	//window.addEventListener ( 'resize', initResponsive );
	//window.addEventListener ( 'resize', bottomFixer );
	window.addEventListener ( 'load', setVersion );
}
else 
{
	//window.attachEvent ( 'onresize', initResponsive );
	//window.attachEvent ( 'onresize', bottomFixer );
	window.attachEvent ( 'onload', setVersion );
}

//initResponsive();
