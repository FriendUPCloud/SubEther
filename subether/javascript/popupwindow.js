
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

function openWindow ( query, parent, act, func, extra, e )
{
	closeWindow();
	if( !query || !act ) return false;
	var w = ge( 'PopupWindow__' );
	var i = ge( 'InnerPopupWindow__' );
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?global=true&function=openwindow', 'post', true );
	j.addVar ( 'pid', parent );
	j.addVar ( 'query', query );
	j.addVar ( 'act', act );
	j.addVar ( 'extra', extra );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( w && i ) 
			{
				i.innerHTML = r[1];
				w.className = 'open ' + act;
				if( func ) func ();
			}
		}
	}
	j.send ();
	
	if( e )
	{
		e.stopPropagation();
		e.preventDefault();
	}
}

function DialogWindow ( e, comp, act, vars, func, extra )
{
	closeWindow();
	
	if ( !comp ) return;
	
	var w = ge( 'PopupWindow__' );
	var i = ge( 'InnerPopupWindow__' );
	
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=' + comp + '&function=' + ( act ? act : 'openwindow' ), 'post', true );
	if ( vars ) j.addVar ( 'vars', ( typeof vars === 'object'/* || typeof vars === 'array'*/ ) ? JSON.stringify( vars ) : vars );
	if ( extra ) j.addVar ( 'extra', extra );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if ( w && i ) 
			{
				i.innerHTML = r[1];
				w.className = 'open ' + act;
				if ( func ) func ();
			}
		}
		else console.log( this.getResponseText() );
	}
	j.send ();
	
	if ( e )
	{
		e.stopPropagation();
		e.preventDefault();
	}
}

function openFullscreen ( query, parent, act, func, extra, e, path )
{
	if( !query || !parent || !act ) return;
	var w = ge( 'PopupWindow__' );
	var j = new bajax ();
	j.openUrl ( ( path ? getPath() : baseUrl() ) + '?global=true&function=openwindow&fullscreen=1', 'post', true );
	j.addVar ( 'pid', ( typeof parent === 'object' ? JSON.stringify( parent ) : parent ) );
	j.addVar ( 'query', query );
	j.addVar ( 'act', act );
	j.addVar ( 'res', ( window.innerWidth + 'x' + window.innerHeight ) );
	j.addVar ( 'extra', extra );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( w ) 
			{
				// Check if the file has scripts
				var scripts = '';
				var rt = r[1];
				var wholescript = [];
				while( scripts = rt.match( /\<script[^>]*?\>([\w\W]*?)\<\/script[^>]*?\>/i ) )
				{
					wholescript.push( scripts[1] );
					rt = rt.split( scripts[0] ).join ( '' );
				}
				// Run script
				if( wholescript.length )
				{
					eval( wholescript.join ( '' ) );
				}
				
				document.body.style.overflow = 'hidden';
				w.innerHTML = rt;
				w.className = 'open ' + act;
				if( func ) func ();
			}
		}
	}
	j.send ();
	
	if( e )
	{
		e.stopPropagation();
		e.preventDefault();
	}
}

function closeWindow ()
{
	if( ge( 'PopupWindow__' ) )
	{
		ge( 'PopupWindow__' ).className = '';
		if( ge( 'InnerPopupWindow__' ) )
		{
			ge( 'InnerPopupWindow__' ).innerHTML = '';
		}
		else ge( 'PopupWindow__' ).innerHTML = '<div id="InnerPopupWindow__"></div>';
		
		document.body.style.overflow = 'visible';
	}
}

function moreOptions( ele, component, button, fid, unique )
{
	if( !ele || !component || !button || !ge( 'OptionsBox' ) ) return;
	
	if( ge( 'OptionsBox' ).className.indexOf( 'open' ) >= 0 )
	{
		ele.className = '';
		ge( 'OptionsBox' ).className = '';
		ge( 'OptionsBox' ).getElementsByTagName( 'div' )[0].innerHTML = '';
	}
	else
	{
		var j = new bajax ();
		j.openUrl ( baseUrl() + '?component=' + component + '&function=' + button, 'post', true );
		if( fid ) j.addVar ( 'fid', fid );
		if( unique ) j.addVar ( 'unique', unique );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				ele.className = 'active';
				ge( 'OptionsBox' ).getElementsByTagName( 'div' )[0].innerHTML = r[1];
				ge( 'OptionsBox' ).className = 'open ' + button;
			}
		}
		j.send ();
	}
}

function getCurrentImage( uniqueid )
{
	var fid;
	var ele = ge( 'PopupWindow__' ).getElementsByTagName( 'div' );
	if( ele.length )
	{
		for( a = 0; a < ele.length; a++ )
		{
			if( ele[a].className.indexOf( 'ImageCurrent' ) >= 0 )
			{
				if ( uniqueid )
				{
					fid = ele[a].getElementsByTagName( 'div' )[0].getAttribute( 'unique' );
				}
				else
				{
					fid = ele[a].getElementsByTagName( 'div' )[0].getAttribute( 'fid' );
				}
			}
		}
	}
	return fid;
}

function deleteImage()
{
	var fid = getCurrentImage();
	/*var ele = ge( 'InnerPopupWindow__' ).getElementsByTagName( 'div' );
	if( ele.length )
	{
		for( a = 0; a < ele.length; a++ )
		{
			if( ele[a].className.indexOf( 'ImageCurrent' ) >= 0 )
			{
				fid = ele[a].getElementsByTagName( 'img' )[0].getAttribute( 'fid' );
			}
		}
	}*/
	if( !fid ) return;
	var r = confirm( 'Are you sure?' );
	if( r == true )
	{
		var j = new bajax ();
		j.openUrl ( baseUrl() + '?component=library&action=files&option=deletefile', 'post', true );
		j.addVar ( 'deletefile', fid );
		j.addVar ( 'filetype', 'image' );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				// TODO: Make support for deleting images in showroom object and refreshing of library or wherever the image is shown
				clearInterval ( Showroom.interval );
				Showroom.changePage ( 'next', false, Showroom.current, true );
			}
			else alert( this.getResponseText() );
		}
		j.send ();
	}
}

var wappear1;
var wappear2;

// TODO: Create a more smooth fade in and out

function confirmCustom( msg, call )
{
	if ( msg && call )
	{
		return alertWindow( msg, 2, false, call );
	}
	
	return false;
}

function alertWindow( msg, btn, time, call )
{
	if ( msg && ge( 'PopupWindow__' ) && ge( 'InnerPopupWindow__' ) )
	{
		closeAlertWindow(1);
		
		var div = document.createElement( 'div' );
		div.id = 'AlertWindow';
		div.style.textAlign = 'center';
		div.innerHTML = '<span onclick="closeAlertWindow(1)" style="position:relative;display:inline-block;padding:20px;padding-left:40px;padding-right:40px;margin:30% 10%;background:white;cursor:pointer;border:1px solid #cacaca;box-shadow:0 0 4px -1px rgba(0, 0, 0, 0.8);color:black;"><span>' + msg + '</span>' +
		( ( btn == 1 || !btn ) ? '<div style="padding-top:20px;"><button onclick="closeAlertWindow(1)">OK</button></div>' : '' ) + 
		( btn && btn == 2 ? '<div style="padding-top:20px;"><button class="OkButton">OK</button><button onclick="closeAlertWindow(1)">Cancel</button></div>' : '' ) + '<span>';
		
		var a = 1;
		
		ge( 'PopupWindow__' ).style.opacity = '0.' + a;
		ge( 'PopupWindow__' ).className = 'open';
		ge( 'InnerPopupWindow__' ).className = 'transparent';
		ge( 'InnerPopupWindow__' ).appendChild( div );
		
		// Set callback function on click after setting HTML
		var buttons = div.getElementsByTagName( 'button' );
		for( var a = 0; a < buttons.length; a++ )
		{
			if( buttons[a].className == 'OkButton' )
			{
				buttons[a].onclick = function()
				{
					closeAlertWindow(1, call );
				}
			}
		}
		
		var wappear1 = setInterval( function()
		{
			if ( !ge( 'AlertWindow' ) || a >= 9 )
			{
				ge( 'PopupWindow__' ).style.opacity = 1;
				clearInterval( wappear1 );
			}
			else
			{
				ge( 'PopupWindow__' ).style.opacity = '0.' + a;
			}
			
			a = Math.round( a + 1 );
			
			console.log( ge( 'PopupWindow__' ).style.opacity + ' --' );
		}, 100 );
		
		if ( time )
		{
			setTimeout( 'closeAlertWindow()', time );
		}
	}
}

function closeAlertWindow( force, cfm )
{
	if ( ge( 'AlertWindow' ) && ge( 'PopupWindow__' ) && ge( 'InnerPopupWindow__' ) )
	{
		var a = 9;
		
		wappear2 = setInterval( function()
		{
			if ( !ge( 'AlertWindow' ) || a <= 0 )
			{
				ge( 'PopupWindow__' ).className = '';
				ge( 'InnerPopupWindow__' ).className = '';
				
				if ( ge( 'AlertWindow' ) )
				{
					ge( 'AlertWindow' ).parentNode.removeChild( ge( 'AlertWindow' ) );
				}
				
				ge( 'PopupWindow__' ).style.opacity = 1;
				clearInterval( wappear2 );
			}
			else
			{
				ge( 'PopupWindow__' ).style.opacity = '0.' + a;
			}
			
			a = Math.round( a - 1 );
			
			console.log( ge( 'PopupWindow__' ).style.opacity + ' --' );
		}, 100 );
		
		if ( force )
		{
			ge( 'PopupWindow__' ).className = '';
			ge( 'InnerPopupWindow__' ).className = '';
			
			if ( ge( 'AlertWindow' ) )
			{
				ge( 'AlertWindow' ).parentNode.removeChild( ge( 'AlertWindow' ) );
			}
			
			ge( 'PopupWindow__' ).style.opacity = 1;
			clearInterval( wappear2 );
		}
		
		if ( cfm )
		{
			if ( typeof( cfm ) == 'string' )
			{
				console.log( 'Evaluating.' );
				eval( cfm );
			}
			else
			{
				console.log( 'Running callback.' );
				cfm();
			}
			
			return true;
		}
		
	}
	
	return false;
}



/* --- Global Popupwindow Event -------------------------------------------------------- */

// Check Global Keys
function checkWindowKeys( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var keycode = e.which ? e.which : e.keyCode;
	switch ( keycode )
	{
		case 27:
			closeWindow();
			break;
		default: break;
	}
}

// Check Global Cliks
function checkWindowClicks( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	//
}

// Assign Global Listeners
if ( window.addEventListener )
{
	window.addEventListener ( 'keydown', checkWindowKeys );
	window.addEventListener ( 'mousedown', checkWindowClicks );
}
else 
{
	window.attachEvent ( 'onkeydown', checkWindowKeys );
	window.attachEvent ( 'onmousedown', checkWindowClicks );
}
