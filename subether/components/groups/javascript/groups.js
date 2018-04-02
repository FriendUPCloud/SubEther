
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

function createGroup ( parent )
{
	var i = ge( 'InnerPopupWindow__' );
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=groups&action=groupcreate', 'post', true );
	if ( parent )
	{
		j.addVar ( 'pid', parent );
	}
	var inputs = i.getElementsByTagName( 'input' );
	if( !inputs.length ) return false;
	for ( var a = 0; a < inputs.length; a++ )
	{
		if( inputs[a].type == 'radio' && inputs[a].name )
		{
			if( inputs[a].checked ) j.addVar ( inputs[a].name, inputs[a].value );
		}
		else if( inputs[a].name )
		{
			j.addVar ( inputs[a].name, inputs[a].value );
		}
	}
	var membs = ge( 'FindMembers' ).getElementsByTagName( 'input' );
	if( membs.length > 0 )
	{
		var m;
		for ( var a = 0; a < membs.length; a++ )
		{
			if( membs[a].id && membs[a].value )
			{
				m = ( m ? ( m + ',' + membs[a].value ) : membs[a].value );
			}
		}
		if( m )
		{
			j.addVar ( 'users', m );
		}
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			document.location = document.location.href.split( '?' )[0].split ( '#' )[0];
		}
	}
	j.send ();
}

function refreshCover( i )
{
	if( !i || !ge( 'MainImage' ) ) return;
	ge( 'MainImage' ).innerHTML = i;
}

function groupOptions()
{
	if( !ge( 'GroupOptionsBox' ) ) return;
	
	if( ge( 'GroupOptionsBox' ).className.indexOf( 'open' ) >= 0 )
	{
		ge( 'GroupOptionsBox' ).className = '';
		ge( 'GroupOptionsBox' ).getElementsByTagName( 'div' )[0].innerHTML = '';
	}
	else
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=groups&function=edits', 'get', true );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				ge( 'GroupOptionsBox' ).getElementsByTagName( 'div' )[1].innerHTML = r[1];
				ge( 'GroupOptionsBox' ).className = 'open';
			}
		}
		j.send ();
	}
}

function leaveGroup()
{
	if( confirm( 'Are you sure?' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=groups&action=leavegroup', 'post', true );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				document.location = 'en/home/';
			}
		}
		j.send ();
	}
}

function deleteGroup()
{
	if( confirm( 'Are you sure?' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=groups&action=deletegroup', 'post', true );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				document.location = 'en/home/';
			}
		}
		j.send ();
	}
}

function joinGroup ( groupid )
{
	if( !groupid ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=groups&action=joingroup', 'post', true );
	j.addVar ( 'groupid', groupid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			document.location = 'en/home/groups/' + groupid + '/';
		}
	}
	j.send ();
}

function editGroupSettings()
{
	if( !ge( 'About' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=groups&function=about', 'post', true );
	j.addVar ( 'edit', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'About' ).innerHTML = r[1];
		}
	}
	j.send ();
}

function closeGroupSettings( close )
{
	if( !ge( 'About' ) ) return;
	if( close )
	{
		ge( 'About' ).innerHTML = '';
	}
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=groups&function=about', 'post', true );
	j.addVar ( 'refresh', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'About' ).innerHTML = r[1];
		}
	}
	j.send ();
}

function saveGroupSettings()
{
	if( !ge( 'About' ) ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=groups&action=about', 'post', true );
	
	var special = ['accesslevelslist'];
	
	if ( special.length > 0 )
	{
		for ( a = 0; a < special.length; a++ )
		{
			if ( ge( special[a] ) )
			{
				var tr = ge( special[a] ).getElementsByTagName( 'div' );
				
				if ( tr.length > 0 )
				{
					var found = false;
					
					var lvls = new Array();
					
					for ( var b = 0; b < tr.length; b++ )
					{	
						var inp = tr[b].getElementsByTagName( '*' );
						
						if ( inp.length > 0 )
						{
							var obj = new Object();
							
							for ( var c = 0; c < inp.length; c++ )
							{
								if ( inp[c].name )
								{
									if ( inp[c].tagName == 'SELECT' && inp[c].name == 'Accounting' )
									{
										var opt = inp[c].getElementsByTagName( 'option' );
										
										if ( opt.length > 0 )
										{
											var opts = new Array();
											
											for ( var o = 0; o < opt.length; o++ )
											{
												if ( opt[o].value )
												{
													opts.push( opt[o].value );
												}
											}
											
											if ( opts.length > 0 )
											{
												obj[inp[c].name] = opts.join( ',' );
												
												found = true;
											}
										}
									}
									else if ( inp[c].type && inp[c].type == 'checkbox' )
									{
										obj[inp[c].name] = ( inp[c].checked ? inp[c].value : '0' );
										
										found = true;
									}
									else if ( inp[c].type )
									{
										obj[inp[c].name] = inp[c].value;
										
										found = true;
									}
								}
							}
							
							if ( obj && found )
							{
								lvls.push( obj );
							}
						}
					}
					
					if ( lvls && found )
					{
						j.addVar ( special[a], JSON.stringify( lvls ) );
					}
				}
			}
		}
	}
	
	var array = ['maingroup', 'GroupName', 'GroupDescription', 'GroupPrivacySettings'];
	
	if ( array.length > 0 )
	{
		for ( d = 0; d < array.length; d++ )
		{
			if ( ge( array[d] ) )
			{
				var inputs = ge( array[d] ).getElementsByTagName( '*' );
				
				if ( inputs.length > 0 )
				{
					for ( e = 0; e < inputs.length; e++ )
					{
						if ( inputs[e].getAttribute( 'name' ) )
						{
							if ( inputs[e].getAttribute( 'type' ) && inputs[e].getAttribute( 'type' ) == 'radio' )
							{
								if( inputs[e].checked )
								{
									j.addVar ( inputs[e].getAttribute( 'name' ), ( inputs[e].value ? inputs[e].value : inputs[e].innerHTML ) );
								}
							}
							else if ( inputs[e].getAttribute( 'type' ) && inputs[e].getAttribute( 'type' ) == 'checkbox' )
							{
								if( !inputs[e].checked )
								{
									j.addVar ( inputs[e].getAttribute( 'name' ), ( inputs[e].checked ? ( inputs[e].value ? inputs[e].value : inputs[e].innerHTML ) : '0' ) );
								}
							}
							else
							{
								j.addVar ( inputs[e].getAttribute( 'name' ), ( inputs[e].value ? inputs[e].value : inputs[e].innerHTML ) );
							}
						}
					}
				}
			}
		}
	}
	
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			closeGroupSettings(1);
			location.reload();
		}
		else
		{
			console.log( this.getResponseText() );
		}
	}
	j.send ();
}

function checkAccessInputs( ele )
{
	if ( ge( 'accesslevelslist' ) )
	{
		var rows = ge( 'accesslevelslist' ).getElementsByTagName( 'div' );
		
		if ( rows.length > 0 )
		{
			var last = 0;
			
			var delt = new Object();
			var keep = new Object();
			
			for ( a = 0; a < rows.length; a++ )
			{
				if ( a == 0 ) continue;
				
				var found = false;
				
				var inp = rows[a].getElementsByTagName( 'input' );
				
				if ( inp.length > 0 )
				{
					for ( b = 0; b < inp.length; b++ )
					{
						if ( inp[b].type == 'text' && inp[b].value )
						{
							found = rows[a];
						}
						if ( inp[b].type == 'checkbox' && inp[b].checked )
						{
							found = rows[a];
						}
					}
				}
				
				if ( found )
				{
					last = a;
					
					keep[a] = rows[a];
				}
				else
				{
					delt[a] = rows[a];
				}
			}
			
			if ( delt )
			{
				for ( var k in delt )
				{
					if ( k > (last+1) )
					{
						ge( 'accesslevelslist' ).removeChild( delt[k] );
					}
				}
			}
			
			if ( typeof delt[last+1] == 'undefined' && ge( 'accesslevelsedit' ) )
			{
				// TODO: Get the last empty one
				var div = document.createElement( 'div' );
				if ( ge( 'accesslevelsedit' ).className.indexOf( 'closed' ) >= 0 )
				{
					div.className = 'closed';
				}
				div.innerHTML = ge( 'accesslevelsedit' ).innerHTML;
				ge( 'accesslevelslist' ).appendChild( div );
			}
		}
	}
}

function toggleAccessOptions( ele )
{
	if ( !ele || !ele.parentNode.parentNode.parentNode.parentNode.parentNode ) return;
	
	var parent = ele.parentNode.parentNode.parentNode.parentNode.parentNode;
	
	parent.className = ( parent.className.indexOf( 'closed' ) >= 0 ? parent.className.split( 'closed' ).join( '' ) : parent.className.split( 'closed' ).join( '' ) + 'closed' );
}

function MoveSelectOption( ele, reverse )
{
	if ( !ele || !ele.parentNode.parentNode.parentNode ) return;
	
	var parent = ele.parentNode.parentNode.parentNode;
	
	var sel1 = parent.getElementsByTagName( 'select' )[0];
	var sel2 = parent.getElementsByTagName( 'select' )[1];
	
	var opt = new Array();
	var cur = new Object();
	var rep = new Array();
	
	if ( sel1 && sel2 )
	{
		if ( reverse )
		{
			var opt1 = sel1.getElementsByTagName( 'option' );
			
			if ( opt1.length > 0 )
			{
				for ( a = 0; a < opt1.length; a++ )
				{
					if ( opt1[a].selected )
					{
						opt.push( opt1[a] );
					}
					else
					{
						cur[opt1[a].value] = opt1[a].value;
					}
				}
			}
			
			var opt2 = sel2.getElementsByTagName( 'option' );
			
			if ( opt2.length > 0 )
			{
				for ( b = 0; b < opt2.length; b++ )
				{
					if ( opt2[b].value && !cur[opt2[b].value] )
					{
						opt2[b].selected = false;
					}
					else if ( opt2[b].selected )
					{
						rep.push( opt2[b].value );
					}
				}
			}
			
			if ( opt.length > 0 )
			{
				for ( c = 0; c < opt.length; c++ )
				{
					sel1.removeChild( opt[c] );
				}
			}
			
			if ( rep.length > 0 )
			{
				sel2.setAttribute( 'selected', rep.join( ',' ) );
			}
			else
			{
				sel2.removeAttribute( 'selected' );
			}
		}
		else
		{
			var opt1 = sel1.getElementsByTagName( 'option' );
			
			if ( opt1.length > 0 )
			{
				for ( a = 0; a < opt1.length; a++ )
				{
					if ( opt1[a] )
					{
						cur[opt1[a].value] = opt1[a].value;
					}
					if ( opt1[a].selected )
					{
						rep.push( opt1[a].value );
					}
				}
			}
			
			var opt2 = sel2.getElementsByTagName( 'option' );
			
			if ( opt2.length > 0 )
			{
				for ( b = 0; b < opt2.length; b++ )
				{
					if ( opt2[b].value && ( opt2[b].selected || cur[opt2[b].value] ) )
					{
						var option = document.createElement( 'option' );
						option.value = opt2[b].value;
						option.innerHTML = opt2[b].innerHTML;
						option.setAttribute( 'onclick', 'MarkSelectOption(this)' );
						
						if ( rep && rep.indexOf( opt2[b].value ) >= 0 )
						{
							option.selected = true;
						}
						
						opt.push( option );
						
						opt2[b].selected = true;
						
						
					}
				}
			}
			
			if ( opt.length > 0 )
			{
				sel1.innerHTML = '';
				
				for ( c = 0; c < opt.length; c++ )
				{
					sel1.appendChild( opt[c] );
				}
			}
			
			if ( rep.length > 0 )
			{
				sel1.setAttribute( 'selected', rep.join( ',' ) );
			}
			else
			{
				sel1.removeAttribute( 'selected' );
			}
		}
	}
}

function MarkSelectOption( ele )
{
	if ( !ele || !ele.parentNode ) return;
	
	var sel = ele.parentNode;
	
	var cur = ( sel.getAttribute( 'selected' ) ? sel.getAttribute( 'selected' ).split( ',' ) : false );
	var rep = new Array();
	
	var opt = sel.parentNode.getElementsByTagName( 'option' );
	
	if ( opt && opt.length > 0 )
	{
		for ( a = 0; a < opt.length; a++ )
		{
			if ( ele == opt[a] && cur && cur.indexOf( opt[a].value ) >= 0 )
			{
				opt[a].selected = false;
			}
			else if ( opt[a].value && cur && cur.indexOf( opt[a].value ) >= 0 )
			{
				opt[a].selected = true;
				rep.push( opt[a].value );
			}
			else if ( opt[a].value && opt[a].selected )
			{
				opt[a].selected = true;
				rep.push( opt[a].value );
			}
		}
	}
	
	if ( rep.length > 0 )
	{
		sel.setAttribute( 'selected', rep.join( ',' ) );
	}
	else
	{
		sel.removeAttribute( 'selected' );
	}
}

/*
function findMembers ( groupid, search )
{
	var lfm = ge( 'ListFoundMembers' );
	lfm.innerHTML = '';
	if( !groupid || !search ) return false;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=groups&function=findmembers', 'post', true );
	j.addVar ( 'groupid', groupid );
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
	if( !userid || !name ) return false;
	var s = document.createElement ( 'span' );
	s.className = 'member';
	s.innerHTML = name + '<input type="hidden" id="uid_' + userid + '" value="' + userid + '"/><a href="javascript:void(0);" onclick="removeMember( ' + userid + ' )">x</a>';
	ge( 'FindMembers' ).appendChild( s );
}

function inviteMembers ( groupid )
{
	if( !groupid ) return false;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=groups&action=invitemembers', 'post', true );
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
		}
	}
	j.send ();
}*/
