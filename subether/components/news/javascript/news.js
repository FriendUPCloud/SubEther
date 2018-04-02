
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

function publishNews ( nid )
{
	var id = ( nid && nid != 'Current' ? '_' + nid : '' );
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=news&action=news', 'post', true );
	if( ge( 'Title' + id ) ) j.addVar ( 'Title', ge( 'Title' + id ).value );
	if( ge( 'Leadin' + id ) ) j.addVar ( 'Leadin', ge( 'Leadin' + id ).value );
	if( ge( 'Article' + id ) ) j.addVar ( 'Article', ge( 'Article' + id ).value );
	if( ge( 'Type' + id ) ) j.addVar ( 'Type', ge( 'Type' + id ).value );
	if( ge( 'Status' + id ) ) j.addVar ( 'Status', ge( 'Status' + id ).value );
	if( nid ) j.addVar ( 'nid', nid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			if( !nid )
			{
				ge( 'Title' ).value = '';
				ge( 'Leadin' ).value = '';
				ge( 'Article' ).value = '';
			}
			refreshNews( nid == 'Current' ? nid : '' );
		}
	}
	j.send ();
}

function refreshNews ( nid, edit )
{
	if( !ge( 'NewsContent' ) || !ge( 'NewsEditor' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=news&function=news', 'post', true );
	j.addVar ( 'nid', nid && nid != 'Current' ? nid : true );
	if( edit ) j.addVar ( 'edit', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] && r[2] )
		{
			ge( 'NewsContent' ).innerHTML = r[1];
			ge( 'NewsEditor' ).innerHTML = r[2];
			if( nid != 'Current' ) openNewsEditor( 'close' );
		}
	}
	j.send ();
}

function deleteNews( nid )
{
	if( !nid ) return;
	var r = confirm( 'Are you sure?' );
	if( r == true )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=news&action=news', 'post', true );
		j.addVar ( 'delete', nid );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				refreshNews();
			}
		}
		j.send ();
	}
}

function openNewsEditor( com )
{
	if( !ge( 'NewsEditor' ) || !com && ge( 'NewsEditor' ).getElementsByTagName( 'div' )[0].className.indexOf( 'Active' ) > 0 ) 
	{
		return;
	}
	else if( ge( 'NewsEditor' ) && ge( 'NewsEditor' ).getElementsByTagName( 'div' )[0].className.indexOf( 'Active' ) > 0 && com == 'close' )
	{
		ge( 'NewsEditor' ).getElementsByTagName( 'div' )[0].className = ge( 'NewsEditor' ).getElementsByTagName( 'div' )[0].className.split( ' Active' ).join( '' );
		return;
	}
	else if( !com )
	{
		ge( 'NewsEditor' ).getElementsByTagName( 'div' )[0].className = ge( 'NewsEditor' ).getElementsByTagName( 'div' )[0].className + ' Active';
		return;
	}
	return;
}

