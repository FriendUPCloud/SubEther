
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

function createAccount( pid )
{
	var inp = ge( 'createaccount' ).getElementsByTagName( 'input' );
	
	if( !inp.length ) return;
    
    var j = new bajax ();
    j.openUrl ( getPath() + '?component=bank&action=create', 'post', true );
    for( a = 0; a < inp.length; a++ )
	{
		if( inp[a].name )
		{
			j.addVar ( inp[a].name, inp[a].value );
		}
	}
    j.onload = function ()
    {
        var r = this.getResponseText ().split ( '<!--separate-->' );
        if ( r[0] == 'ok' )
        {
            alert( 'saved' );
			closeWindow();
        }
		else alert( this.getResponseText() );
    }
    j.send ();
}

function transfer( accid )
{
	if( !accid || !ge( 'transfer' ) || !ge( 'Bank' ) ) return;
	
	var inp = ge( 'transfer' ).getElementsByTagName( 'input' );
	var sel = ge( 'transfer' ).getElementsByTagName( 'select' );
	
	if( !inp.length || !sel.length ) return;
	
	var j = new bajax ();
    j.openUrl ( getPath() + '?component=bank&action=transfer&accid=' + accid, 'post', true );
    for( a = 0; a < inp.length; a++ )
	{
		if( inp[a].name )
		{
			j.addVar ( inp[a].name, inp[a].value );
		}
	}
	for( b = 0; b < sel.length; b++ )
	{
		if( sel[b].name )
		{
			j.addVar ( sel[b].name, sel[b].value );
		}
	}
    j.onload = function ()
    {
        var r = this.getResponseText ().split ( '<!--separate-->' );
        if ( r[0] == 'ok' && r[1] )
        {
			ge( 'Bank' ).innerHTML = r[1];
			closeWindow();
        }
		else alert( this.getResponseText() );
    }
    j.send ();
}

function payment( accid )
{
	if( !accid || !ge( 'payment' ) || !ge( 'Bank' ) ) return;
	
	var inp = ge( 'payment' ).getElementsByTagName( 'input' );
	var sel = ge( 'payment' ).getElementsByTagName( 'select' );
	
	if( !inp.length || !sel.length ) return;
	
	var j = new bajax ();
    j.openUrl ( getPath() + '?component=bank&action=payment&accid=' + accid, 'post', true );
    for( a = 0; a < inp.length; a++ )
	{
		if( inp[a].name )
		{
			j.addVar ( inp[a].name, inp[a].value );
		}
	}
	for( b = 0; b < sel.length; b++ )
	{
		if( sel[b].name )
		{
			j.addVar ( sel[b].name, sel[b].value );
		}
	}
    j.onload = function ()
    {
        var r = this.getResponseText ().split ( '<!--separate-->' );
        if ( r[0] == 'ok' && r[1] )
        {
			ge( 'Bank' ).innerHTML = r[1];
			closeWindow();
        }
		else alert( this.getResponseText() );
    }
    j.send ();
}
