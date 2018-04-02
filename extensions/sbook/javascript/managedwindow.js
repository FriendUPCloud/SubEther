
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

var _managedWindows = [];
window.managedMouseMode = 0;
window.currentMWindow = 0;
var mouseMoveWindowMove = 1;

function getMouseCoords( e )
{
	var m = new Object();
	m.x = e.pageX ? e.pageX : ( e.clientX + document.body.scrollLeft );
	m.y = e.pageY ? e.pageY : ( e.clientY + document.body.scrollTop );
	return m;
}
function mouseMListener( e )
{
	switch( window.managedMouseMode )
	{
		case mouseMoveWindowMove:
			if( window.currentMWindow.minimized ) return;
			var m = getMouseCoords( e );
			var px = m.x - window.managedMouseOX;
			var py = m.y - window.managedMouseOY;
			window.currentMWindow.updateCoords( px, py );
			document.getElementById('WindowOverlay').style.top = '0';
			break;
	}
}
function mouseMListenerUP( e )
{
	window.managedMouseMode = 0;
	document.getElementById('WindowOverlay').style.top = '';
}
if( window.addEventListener )
{
	window.addEventListener ( 'mousemove', mouseMListener, true );
	window.addEventListener ( 'mouseup', mouseMListenerUP, true );
}
else 
{
	window.attachEvent ( 'onmousemove', mouseMListener, true );
	window.attachEvent ( 'onmouseup', mouseMListenerUP, true );
}

// Create a managed window
function CreateManagedWindow( properties )
{	
	// Setup the popupwindow object
	var mwindow = {
		window: document.createElement( 'div' ),
		content: document.createElement( 'div' ),
		title: document.createElement( 'div' ),
		titleString: document.createElement( 'div' ),
		titleGadgets: document.createElement( 'div' ),
		width: 600,
		height: 150,
		dockable: false,
		docked: false,
		minimized: false,
		activated: false,
		events: [],
		gadgets: {
			close: false,
			minimize: false,
			maximize: false
		},
		// Draw window gadgets
		drawGadgets: function()
		{
			var existGads = this.titleGadgets.getElementsByTagName( 'div' );
			// Clear up existing gadgets
			var gads = {};
			for( var a = 0; a < existGads.length; a++ )
			{
				var ecn = existGads[a].className;
				switch ( ecn )
				{
					case 'Close':    gads.close    = existGads[a]; break;
					case 'Minimize': gads.minimize = existGads[a]; break;
					case 'Maximize': gads.maximize = existGads[a]; break;
				}
				if( !this.gadgets.close && gads.close )
					this.gadgets.removeChild( gads.close );
				if( !this.gadgets.minimize && gads.minimize )
					this.gadgets.removeChild( gads.minimize );
				if( !this.gadgets.maximize && gads.maximize )
					this.gadgets.removeChild( gads.maximize );
			}
			if( this.gadgets.close )
			{
				var c = document.createElement( 'div' );
				c.className = 'Close';
				this.titleGadgets.appendChild( c );
			}
			if( this.gadgets.minimize )
			{
				var c = document.createElement( 'div' );
				c.className = 'Minimize';
				this.titleGadgets.appendChild( c );
			}
			if( this.gadgets.maximize )
			{
				var c = document.createElement( 'div' );
				c.className = 'Maximize';
				this.titleGadgets.appendChild( c );
			}
		},
		setFlag: function( key, value )
		{
			// Reset optional onclick action
			this.title.onclick = null;
			
			switch( key )
			{
				case 'title':
					this.setTitle( value );
					break;
				case 'width':
					this.width = value;
					this.refresh();
					break;
				case 'height':
					this.height = value;
					this.refresh();
					break;
				case 'dockable':
					this.dockable = value == true ? true : false;
					break;
				case 'docked':
					this.docked = true;
					this.refresh();
					break;
				case 'minimized':
					this.minimized = value == true ? true : false;
					this.refresh();
					this.title.onclick = null;
					if( this.minimized )
					{
						var w = this;
						this.title.onclick = function()
						{
							var cn = w.window.className;
							if( w.dockable )
							{
								if( w.docked && w.minimized )
								{
									w.minimized = false;
									w.doEvents( 'maximize' );
								}
								else if ( !w.docked && w.minimized )
								{
									w.minimized = false;
									w.docked = true;
									w.doEvents( 'maximize' );
								}
								else if ( w.docked && !w.minimized )
								{
									w.minimized = true;
								}
							}
							// Maximize other non dockables
							else if ( w.minimized )
							{
								w.minimized = false;
								w.doEvents( 'maximize' );
							}
							w.refresh();
						}
					}
					break;
			}
		},
		setFlags: function( flags )
		{
			for ( var a in flags )
			{
				this.setFlag( a, flags[a] );
			}
		},
		setTitle: function( title )
		{
			this.titleString.innerHTML = title;
		},
		active: function( bol )
		{
			if( this.minimized || this.docked ) return;
			for( var a = 0; a < _managedWindows.length; a++ )
			{
				var mv = _managedWindows[a];
				if( mv != this )
				{
					mv.window.className = mv.window.className.split( ' Active' ).join ( '' );
					mv.activated = false;
				}
			}
			this.window.className = bol ? 'ManagedWindow Active' : 'ManagedWindow';
			this.activated = bol ? true : false;
			if( this.activated )
				window.currentMWindow = this;
		},
		reposition: function()
		{
			this.window.style.top = Math.floor ( window.innerHeight * 0.5 - ( this.window.offsetHeight * 0.5 ) ) + 'px';
			this.window.style.left = Math.floor ( window.innerWidth * 0.5 - ( this.window.offsetWidth * 0.5 ) ) + 'px';
		},
		updateCoords: function( px, py )
		{
			if( py + this.window.offsetHeight > window.innerHeight )
			{
				if( this.dockable && this.window.className.indexOf( ' Docked' ) < 0 )
				{
					this.docked = true;
				}
				py = window.innerHeight - this.window.offsetHeight;
			}
			if( px + this.window.offsetWidth > window.innerWidth )
				px = window.innerWidth - this.window.offsetWidth;
			if( px < 0 ) px = 0;
			if( py < 0 ) py = 0;
			this.x = px;
			this.y = py;
			this.window.style.top = py + 'px';
			this.window.style.left = px + 'px';
		},
		refresh: function()
		{
			if( this.minimized )
			{
				this.window.className = 'ManagedWindow Minimized';
				
				// Tray function
				Taskbar.redraw();
			}
			else if( this.docked )
			{
				this.window.className = 'ManagedWindow Docked';
				
				Taskbar.redraw();
			}
			else
			{
				this.window.style.width = this.width + 'px';
				this.window.style.height = this.height + 'px';
				this.updateCoords( this.x, this.y );
				this.drawGadgets();
			}
		},
		// Add an event to window
		addEvent: function( event, handler )
		{
			if( typeof( this.events[event] ) == 'undefined' )
				this.events[event] = [];
			this.events[event].push( handler );
		},
		// Execute events!
		doEvents: function( type )
		{
			if( typeof( this.events[type] ) != 'undefined' && this.events[type].length )
			{
				for( var a = 0; a < this.events[type].length; a++ )
				{
					this.events[type][a]();
				}
			}
		},
		appendChild: function( ele )
		{
			this.content.appendChild( ele );
		},
		setContent: function( cnt )
		{
			this.content.innerHTML = cnt;
		},
		loadContent: function( query )
		{
			var j = new bajax ();
			var w = this.window;
			var c = this.content;
			var o = this;
			j.openUrl ( query, 'get', true );
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );	
				if ( r[0] == 'ok' )
				{
					if( w && c ) 
					{
						t.innerHTML = act;
						c.innerHTML = '<div>' + r[1] + '</div>';
						var height = parseInt( c.getElementsByTagName( 'div' )[0].offsetHeight );
						height += parseInt( c.offsetTop ) + parseInt( t.offsetTop ) + 4;
						if( height > 500 ) 
						{
							height = 500;
							c.style.overflow = 'auto';
						}
						w.style.height = height + 'px';
						o.reposition();
						if( func ) func ();
					}
				}
			}
			j.send ();
		},
		init: function()
		{
			var o = this;
			this.window.className = 'ManagedWindow';
			this.content.className = 'WindowContent';
			this.title.className = 'WindowTitle';
			this.titleString.className = 'TitleString';
			this.titleGadgets.className = 'Gadgets';
			this.title.appendChild( this.titleString );
			this.title.appendChild( this.titleGadgets );
			this.window.appendChild( this.title );
			this.window.appendChild( this.content );
			document.getElementById( 'Windows' ).appendChild( this.window );
			var w = this.window;
			var c = this.content;
			var t = this.title;
			
			// Activate window on click
			w.onmousedown = function()
			{
				o.active( true );
			}
			
			t.onmousedown = function( e )
			{
				o.active( true );
				
				window.managedMouseMode = mouseMoveWindowMove;
				
				var m = getMouseCoords( e );
				var mx = m.x;
				var my = m.y;
				
				window.managedMouseOY = my - w.offsetTop;
				window.managedMouseOX = mx - w.offsetLeft;
				
				if( e.stopPropagation ) e.stopPropagation( e );
				if( e.preventDefault  ) e.preventDefault( e );
				e.cancelBubble = true;
				return false;
			}
			
			w.style.width = this.width + 'px';
			w.style.height = this.height + 'px';
			
			this.reposition();
			
			// Activate window
			this.active( true );
			
			// Can only init once
			this.init = function()
			{
				return false;
			}
		}
	};
	_managedWindows.push( mwindow );
	mwindow.init();
	if( properties )
		mwindow.setFlags( properties );
	return mwindow;
}

// A taskbar!
Taskbar = {
	redraw: function()
	{
		var x = 0;
		var i = 0;
		var x = 0;
		for ( var a = 0; a < _managedWindows.length; a++ )
		{
			var mw = _managedWindows[a];
			var mww = mw.window;
			var cn = mww.className;
			if( cn.indexOf( ' Minimized' ) > 0 || cn.indexOf( ' Docked' ) > 0 )
			{
				mww.style.right = x + 'px';
				x += mww.offsetWidth + 5;
			}
		}
	}
};

