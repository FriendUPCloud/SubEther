
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

function editAbout( id )
{
	if( id && !ge( id ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=about&function=about', 'post', true );
	j.addVar ( 'edit', id );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( id ).innerHTML = r[1];
		}
	}
	j.send ();
}

function closeEdit( id )
{
	if( id && !ge( id ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=about&function=about', 'post', true );
	j.addVar ( 'refresh', id );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( id ).innerHTML = r[1];
		}
	}
	j.send ();
}

function saveAbout( id )
{
	if( !id || !ge( id ) || !ge( 'AboutContent' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=about&action=about', 'post', true );
	var inputs = ge( 'AboutContent' ).getElementsByTagName( '*' );
	if( inputs.length > 0 )
	{
		for( a = 0; a < inputs.length; a++ )
		{
			if( inputs[a].type == 'checkbox' )
			{
				j.addVar ( inputs[a].name, inputs[a].checked ? '1' : '0' );
			}
			else
			{
				j.addVar ( inputs[a].name, inputs[a].value );
			}
		}
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			closeEdit( id );
		}
	}
	j.send ();
}

function SetBirthday()
{
	if ( ge( 'Byear' ) && ge( 'Bmonth' ) && ge( 'Bday' ) && ge( 'Birthdate' ) )
	{
		ge( 'Birthdate' ).value = ( ge( 'Byear' ).value + '-' + ge( 'Bmonth' ).value + '-' + ge( 'Bday' ).value );
	}
}
