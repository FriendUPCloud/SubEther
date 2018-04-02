
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

function getGlobals ( tmp, param )
{
	if( !tmp ) return;
	
	if( ge( tmp ).className == 'active' && param != 'refresh' )
	{
		closeGlobals();
		return;
	}
	
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=global&function=component', 'post', true );
	j.addVar ( 'tmp', tmp );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( r[1] && ge( tmp ) )
			{
				closeGlobals();
				if( !ge( tmp ).getAttribute( 'content' ) )
				{
					ge( tmp ).setAttribute( 'content', ge( tmp ).innerHTML );
				}
				ge( tmp ).innerHTML = r[1];
				ge( tmp ).className = 'active';
			}
		}
	}
	j.send ();
}

function closeGlobals ()
{
	if( !ge( 'Tabs' ) ) return;
	var divs = ge( 'Tabs' ).getElementsByTagName( 'div' );
	if( !divs.length ) return;
	for ( var a = 0; a < divs.length; a++ )
	{
		if( divs[a].className == 'active' )
		{
			if( divs[a].getAttribute( 'content' ) )
			{
				divs[a].innerHTML = divs[a].getAttribute( 'content' );
			}
			else
			{
				divs[a].innerHTML = '';
			}
			divs[a].className = '';
		}
	}
}

function updateGlobalSettings ( ele )
{
	if( !ele || !ele.getAttribute( 'component' ) || !ele.getAttribute( 'module' ) || !ele.getAttribute( 'position' ) ) return;
	
	var sel = ele.parentNode.getElementsByTagName( 'select' )[0];
	
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=global&action=updatesettings', 'post', true );
	j.addVar ( 'cname', ele.getAttribute( 'component' ) );
	if( ele.getAttribute( 'view' ) )
	{
		j.addVar ( 'mtype', ele.getAttribute( 'view' ) );
	}
	j.addVar ( 'mname', ele.getAttribute( 'module' ) );
	j.addVar ( 'access', ( sel.value ? sel.value : false ) );
	j.addVar ( 'position', ele.getAttribute( 'position' ) );
	j.addVar ( 'checked', ( ele.checked ? '1' : '0' ) );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			//
		}
	}
	j.send ();
}

function updateGlobalTabSettings ( ele )
{
	if( !ele || !ele.getAttribute( 'component' ) || !ele.getAttribute( 'module' ) || !ele.getAttribute( 'position' ) || !ele.getAttribute( 'tab' ) ) return;
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=global&action=updatetabs', 'post', true );
	j.addVar ( 'cname', ele.getAttribute( 'component' ) );
	if( ele.getAttribute( 'view' ) )
	{
		j.addVar ( 'mtype', ele.getAttribute( 'view' ) );
	}
	j.addVar ( 'mname', ele.getAttribute( 'module' ) );
	j.addVar ( 'position', ele.getAttribute( 'position' ) );
	j.addVar ( 'tab', ele.getAttribute( 'tab' ) );
	j.addVar ( 'checked', ( ele.checked ? '1' : '0' ) );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			//
		}
	}
	j.send ();
}

function updateGlobalVisibility ( ele )
{
	if( !ele || !ele.name || !ele.value ) return;
	
	var sel = ele.parentNode.getElementsByTagName( 'select' )[0];
	
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=global&action=updatevisibility', 'post', true );
	if( ele.getAttribute( 'view' ) )
	{
		j.addVar ( 'mtype', ele.getAttribute( 'view' ) );
	}
	j.addVar ( 'mname', ele.name.split('_')[0] );
	j.addVar ( 'access', ( sel.value ? sel.value : false ) );
	j.addVar ( 'value', ele.value );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			//
		}
	}
	j.send ();
}

function updateGlobalAccess ( ele )
{
	if( !ele || !ele.getAttribute( 'module' ) ) return;
	
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=global&action=updateaccess', 'post', true );
	if( ele.getAttribute( 'view' ) )
	{
		j.addVar ( 'type', ele.getAttribute( 'view' ) );
	}
	if( ele.getAttribute( 'position' ) )
	{
		j.addVar ( 'position', ele.getAttribute( 'position' ) );
	}
	if( ele.getAttribute( 'component' ) )
	{
		j.addVar ( 'cname', ele.getAttribute( 'component' ) );
	}
	j.addVar ( 'mname', ele.getAttribute( 'module' ) );
	j.addVar ( 'access', ele.value );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			//
		}
	}
	j.send ();
}

function updateGlobalRoute ( ele )
{
	if( !ele || !ele.getAttribute( 'module' ) || !ele.getAttribute( 'position' ) ) return;
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=global&action=updateroute', 'post', true );
	if( ele.getAttribute( 'view' ) )
	{
		j.addVar ( 'mtype', ele.getAttribute( 'view' ) );
	}
	j.addVar ( 'mname', ele.getAttribute( 'module' ) );
	j.addVar ( 'position', ele.getAttribute( 'position' ) );
	j.addVar ( 'checked', ( ele.checked ? '1' : '0' ) );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			//
		}
	}
	j.send ();
}

function openRenderList( ele )
{
	if( !ele ) return;
	
	var parent = ele.parentNode.parentNode.parentNode.parentNode.parentNode;
	
	if( parent.className == 'open' )
	{
		parent.className = 'closed';
		ele.innerHTML = '[+]';
	}
	else
	{
		parent.className = 'open';
		ele.innerHTML = '[-]';
	}
}

function openTabList ( ele )
{	
	if( !ele || !ele.parentNode ) return;
	
	if( ele.className == 'open' )
	{
		closeTabList( ele );
		return;
	}
	if( ge( 'TabList' ) )
	{
		closeTabList( ge( 'TabList' ).parentNode.getElementsByTagName( 'span' )[0] );
	}
	
	var elem = ele;
	var parent = ele.parentNode;
	var input = parent.getElementsByTagName( 'input' )[0];
	if( !input.getAttribute( 'component' ) || !input.getAttribute( 'module' ) || !input.getAttribute( 'position' ) ) return;
	
	var j = new bajax ();
	j.openUrl ( baseUrl() + '?component=global&function=tabsettings', 'post', true );
	j.addVar ( 'cname', input.getAttribute( 'component' ) );
	j.addVar ( 'mname', input.getAttribute( 'module' ) );
	j.addVar ( 'position', input.getAttribute( 'position' ) );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			elem.innerHTML = '[-]';
			elem.className = 'open';
			var div = document.createElement( 'div' );
			div.id = 'TabList';
			div.innerHTML = r[1];
			parent.appendChild( div );
		}
	}
	j.send ();
}

function closeTabList ( ele )
{
	if( !ele || !ele.parentNode || !ge( 'TabList' ) ) return;
	var elem = ele;
	var parent = ele.parentNode;
	elem.innerHTML = '[+]';
	elem.className = '';
	parent.removeChild( ge( 'TabList' ) );	
}
