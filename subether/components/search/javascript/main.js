
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

function initSearchField()
{
	if( !ge( 'SearchWrapper' ) ) return;
	
	if( ge( 'SearchWrapper' ).className == 'open' )
	{
		ge( 'SearchWrapper' ).className = '';
	}
	else
	{
		ge( 'SearchWrapper' ).className = 'open';
	}
}

function getUrl()
{
	var bref = document.location.href;
	if( bref.split( '#' )[1] ) return bref.split( '#' )[0];
	return bref.split( '?' )[0];
}

function getHash( param )
{
	hash = new Object();
	var bref = document.location.href;
	var res;
	res = bref.split( '#' );
	if( !res[1] ) return;
	res = res[1].split( '&' );
	if( !res ) return;
	if( res.length > 0 )
	{
		for( a = 0; a < res.length; a++ )
		{
			if( param && param == res[a].split('=')[0] )
			{
				return res[a].split('=')[1];
			}
			hash[res[a].split('=')[0]] = res[a].split('=')[1];
		}
		return hash;
	}
	return false;
}

function links()
{
	var c = ge( 'Content__' );
	var gsi = ge( 'GlobalSearch' ).getElementsByTagName( 'input' )[0];
	if( !c.getElementsByTagName( 'table' )[0] || c.className != 'results' ) return;
	var links = c.getElementsByTagName( 'table' )[0].getElementsByTagName( 'a' );
	if( links.length )
	{
		for ( var k = 0; k < links.length; k++ )
		{
			links[k].onclick = function( e )
			{
				var url = this.href.split( '?' )[1];
				this.href = '#' + url;
				search( gsi.value, url.split( '&p=' )[1] );
			}
		}
	}
}

function search( q, p, a, f )
{
	if( !q && ( !f || f == 0 ) ) return;
	
	var sf = f;
	var c = ge( 'Content__' );
	var se = ge( 'SearchEngine' );
	var gs = ge( 'GlobalSearch' );
	var sc = ge( 'SearchContent' );
	var so = ge( 'SearchOptions' );
	var gsi = ge( 'GlobalSearch' ).getElementsByTagName( 'input' )[0];
	
	/*if( !q ) 
	{
		c.innerHTML = '';
		so.innerHTML = '';
		so.style.visibility = 'hidden';
		return;
	}*/
	
	if( se )
	{
		c.innerHTML = '';
	}
	
	if( gs && gsi && c ) 
	{
		gs.style.visibility = 'visible';
		gsi.focus();
		gsi.value = q;
	}
	else return;
	
	ge( 'Logo' ).style.visibility = 'visible';

	var j = new bajax ();
	j.openUrl ( getUrl() + '?component=search&function=results', 'post', true );
	j.addVar ( 'q', q );
	if( p ) j.addVar ( 'p', p );
	if( a ) j.addVar ( 'a', a );
	if( sf && sf != '0' )
	{
		j.addVar ( 'sf', sf );
	}
	j.onload = function ()
	{
		var r = this.getResponseText().trim().split ( '<!--separate-->' );
		console.log( r );
		if ( r[0] == 'ok' && r[1] )
		{
			document.body.className = document.body.className.split( ' search' ).join( '' ) + ' search';
			c.className = 'search results';
			c.innerHTML = r[1];

			if( r[2] ) 
			{
				so.style.visibility = 'visible';
				so.innerHTML = r[2];
			}
			else
			{
				so.style.visibility = 'hidden';
				so.innerHTML = '';
			}
			links();
		}
		else if ( r[0] == 'route' && r[1] )
		{
			document.location = r[1];
		}
		else c.innerHTML = this.getResponseText();
	}
	j.send ();
}

function indexStatus()
{
	if( !ge( 'Navigation' ) ) return;
	var j = new bajax ();
	j.openUrl ( getUrl() + '?component=search&function=status', 'post', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			if( ge( 'Navigation' ) ) ge( 'Navigation' ).innerHTML = r[1];
		}
	}
	j.send ();
}
//setInterval ( 'indexStatus()', 10000 ); 
