
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

function findMembers ( groupid, search )
{
	var lfm = ge( 'ListFoundMembers' );
	lfm.innerHTML = '';
	if( !search ) return false;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=members&function=findmembers', 'post', true );
	if ( groupid ) j.addVar ( 'groupid', groupid );
	j.addVar ( 'search', search.value );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( r[1] ) lfm.innerHTML = r[1];
		}
	}
	j.send ();
}

function removeMember ( userid )
{
	if( !userid ) return false;
	if( ge( 'uid_' + userid ) ) 
	{
		ge( 'uid_' + userid ).parentNode.parentNode.removeChild( ge( 'uid_' + userid ).parentNode );
	}
}

function selectMember ( userid, name )
{
	if( !userid || !name || ge( 'uid_' + userid ) ) return;
	var s = document.createElement ( 'span' );
	s.className = 'member';
	s.innerHTML = name + '<input type="hidden" id="uid_' + userid + '" value="' + userid + '"/><a href="javascript:void(0);" onclick="removeMember( ' + userid + ' )">x</a>';
	ge( 'FindMembers' ).appendChild( s );
	ge( 'ListFoundMembers' ).innerHTML = '';
	if ( ge( 'MemberSearch' ) )
	{
		ge( 'MemberSearch' ).value = '';
		ge( 'MemberSearch' ).focus();
	}
}

function inviteMembers ( groupid )
{
	if( !groupid ) return false;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=members&action=invitemembers', 'post', true );
	var inputs = ge( 'FindMembers' ).getElementsByTagName( 'input' );
	if( !inputs.length ) return false;
	for ( var a = 0; a < inputs.length; a++ )
	{
		if( inputs[a].id ) j.addVar ( inputs[a].id, inputs[a].value );
	}
	j.addVar ( 'groupid', groupid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			document.location = document.location.href.split( '?' )[0].split ( '#' )[0];
			//refreshMembers();
		}
	}
	j.send ();
}

function refreshMembers ()
{
	if( !ge( 'MembersContent' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=members&function=component', 'get', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'MembersContent' ).innerHTML = r[1];
		}
	}
	j.send ();
}

function kickMember ( uid )
{
	if( !uid ) return;
	if( confirm( 'Are you sure?' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=members&action=kickmember', 'post', true );
		j.addVar ( 'uid', uid );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				refreshMembers();
			}
		}
		j.send ();
	}
}

function MemberPermission( uid, acc )
{
	if( !uid || !acc ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=members&action=updateaccess', 'post', true );
	j.addVar ( 'uid', uid );
	j.addVar ( 'acc', acc );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			refreshMembers();
		}
	}
	j.send ();
}
