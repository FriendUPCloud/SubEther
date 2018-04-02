
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

var CheckedUrls = []; var FoundNode = false;

function updateNode( nid, type )
{
	if( !nid || !type ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=global&action=updatenode', 'post', true );
	if( type == 'allow' )
	{
		j.addVar ( 'allow', nid );
	}
	if( type == 'deny' )
	{
		j.addVar ( 'deny', nid );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			location.reload();
		}
	}
	j.send ();
}

function updatePrivacy( pid )
{
	if( !pid ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=global&action=updatenodeprivacy', 'post', true );
	j.addVar ( 'open', pid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			console.log( r[1] );
		}
	}
	j.send ();
}

function deleteNode( nid )
{
	if( !nid || !confirm( 'are you sure?' ) ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=global&action=deletenode', 'post', true );
	j.addVar ( 'nid', nid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			location.reload();
		}
	}
	j.send ();
}

function searchAfterNodes( query, page )
{
	query = ( query ? query : ge( 'NodeVerify' ).value + ' || Treeroot || SubEther' );
	
	page = ( page ? page : 0 );
	
	console.log( query + ' [] ' + page );
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=search&function=results&encoding=json', 'post', true );
	j.addVar ( 'q', query );
	
	if( page > 0 )
	{
		j.addVar ( 'p', page );
	}
	
	j.addVar ( 'a', 'submit' );
	j.onload = function ()
	{
		var r = this.getResponseText().trim().split ( '<!--separate-->' );
		
		if( r[0] == 'ok' )
		{
			if( r[1] )
			{
				var obj = JSON.parse( r[1] );
				
				if( obj && obj.results )
				{
					var str = '';
					
					for( key in obj.results )
					{
						str += '<div>';
						str += '<div><a href="' + obj.results[key].Link + '">' + obj.results[key].Title + '</a><div>';
						str += '<div>' + obj.results[key].Link + '<div>';
						str += '<div>' + obj.results[key].Leadin + '<div>';
						str += '</div><br/>';
						
						if( obj.results[key].Link )
						{
							checkNode( obj.results[key].Link );
						}
					}
					
					ge( 'SearchResults' ).innerHTML = ( str + ge( 'SearchResults' ).innerHTML );
					
					if( page < 30 )
					{
						searchAfterNodes( query, ++page );
					}
					else if( FoundNode )
					{
						document.location.reload();
					}
				}
			}
			else
			{
				ge( 'SearchResults' ).innerHTML = '';
				
				if( FoundNode )
				{
					document.location.reload();
				}
			}
		}
		else if( r[0] && r[1] )
		{
			console.log( r );
		}
	}
	j.send ();
}

function checkNode( url )
{
	var base_url = '';
	
	var bases = document.getElementsByTagName('base');
	
	if ( bases.length > 0 )
	{
		base_url = bases[0].href;
	}
	
	var http = url.split( '://' );
	var domain = ( http[1] ? http[1].split( '/' ) : '' ); 
	
	if( domain && domain[0] && http[0] )
	{
		domain = ( http[0] + '://' + domain[0] );
	}
	
	if( base_url && url && CheckedUrls.indexOf( domain ) < 0 )
	{
		ge( 'SearchCheck' ).innerHTML = '(' + domain + ') Checking ...';
		
		var j = new bajax ();
		j.openUrl ( base_url + 'subether/include/checknode.php', 'post', true );
		j.addVar ( 'url', url );
		j.onload = function ()
		{
			var r = this.getResponseText().trim().split ( '<!--separate-->' );
			
			if( r[0] == 'ok' && r[1] )
			{
				console.log( r[1] );
				
				FoundNode = true;
			}
			else if( r[0] && r[1] )
			{
				console.log( r );
			}
			
			if( domain )
			{
				CheckedUrls.push( domain );
				
				console.log( domain );
			}
			
			ge( 'SearchCheck' ).innerHTML = '';
		}
		j.send ();
	}
}
