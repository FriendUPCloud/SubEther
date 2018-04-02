
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

function sendAdminMail ()
{
	var users;
	var internal;
	var email;
	
	if( !ge( 'MailList' ) || !ge( 'PostBox' ) || !ge( 'MessageContent' ) ) return;
	
	var list = ge( 'MailList' ).getElementsByTagName( 'input' );
	if( list.length > 0 )
	{
		for( a = 0; a < list.length; a++ )
		{
			if( list[a].type == 'checkbox' && list[a].checked )
			{
				users = ( users ? users + ',' + list[a].value : list[a].value );
			}
		}
	}
	
	var type = ge( 'PostBox' ).getElementsByTagName( 'input' );
	if( type.length > 0 )
	{
		for( a = 0; a < type.length; a++ )
		{
			if( type[a].type == 'checkbox' && type[a].checked )
			{
				if( type[a].name == 'Message' )
				{
					internal = 1;
				}
				else if( type[a].name == 'Email' )
				{
					email = 1;
				}
			}
		}
	}
	
	if( users && ge( 'MessageContent' ).innerHTML.length > 0 && ( internal || email ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=admin&action=mailsend', 'post', true );
		if( internal ) j.addVar ( 'internal', internal );
		if( email ) j.addVar ( 'email', email );
		j.addVar ( 'users', users );
		j.addVar ( 'message', ge( 'MessageContent' ).innerHTML );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				alert( 'mail sendt' );
				if( r[1] ) ge( 'AdminContent' ).innerHTML = r[1];
			}
			else alert( 'failed' );
		}
		j.send ();
	}
}
