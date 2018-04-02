
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

function SaveApiAccount( ele, aid )
{
	if ( !ele || !ele.parentNode.parentNode ) return;
	
	var vars = new Object();
	
	var parent = ele.parentNode.parentNode;
	
	var inp = parent.getElementsByTagName( '*' );
	
	if ( inp.length > 0 )
	{
		for ( a = 0; a < inp.length; a++ )
		{
			if ( inp[a].tagName == 'INPUT' && inp[a].name )
			{
				vars[inp[a].name] = inp[a].value;
			}
		}
	}
	
	if ( vars )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=global&action=updateapiaccount', 'post', true );
		
		if ( aid > 0 )
		{
			j.addVar ( 'aid', aid );
		}
		
		for ( var key in vars )
		{
			j.addVar ( key, vars[key] );
		}
		
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			
			if ( r[0] == 'ok' && r[1] )
			{
				console.log( 'saved --- ' + r[1] );
				location.reload();
			}
			else if ( r[0] == 'fail' && r[1] )
			{
				alert( r[1] );
			}
		}
		j.send ();
	}
}

function DeleteApiAccount( aid )
{
	if ( !aid ) return;
	
	if ( confirm( 'are you sure?' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=global&action=deleteapiaccount', 'post', true );
		j.addVar ( 'aid', aid );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			
			if ( r[0] == 'ok' && r[1] )
			{
				console.log( 'deleted --- ' + r[1] );
				location.reload();
			}
			else if ( r[0] == 'fail' && r[1] )
			{
				alert( r[1] );
			}
		}
		j.send ();
	}
}
