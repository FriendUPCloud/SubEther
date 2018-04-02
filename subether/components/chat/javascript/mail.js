
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

function openMail( ele )
{
	if( !ele || !ge( 'Mail_inner' ) ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&action=mail', 'post', true );
	if( ele.getAttribute( 'accountid' ) )
	{
		j.addVar ( 'accid', ele.getAttribute( 'accountid' ) );
	}
	if( ele.getAttribute( 'folder' ) )
	{
		j.addVar ( 'fld', ele.getAttribute( 'folder' ) );
	}
	if( ele.getAttribute( 'messageid' ) )
	{
		j.addVar ( 'mid', ele.getAttribute( 'messageid' ) );
	}
	if( ele.getAttribute( 'rowid' ) )
	{
		j.addVar ( 'hid', ele.getAttribute( 'rowid' ) );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		if( r[0] == 'ok' )
		{
			ge( 'Mail_inner' ).className = 'open';
			ge( 'Mail_inner' ).parentNode.className = 'MailBox open';
			ge( 'Mail_inner' ).innerHTML = r[1];
			
			refreshAccount( ele.getAttribute( 'accountid' ), ele.getAttribute( 'folder' ) );
		}
		else if( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
	}
	j.send ();
}

function deleteMail()
{
	if( !ge( 'Mail_inner' ) ) return;
	
	var current = findCurrentFolder();
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&action=maildelete', 'post', true );
	
	var inp = ge( 'Mail_inner' ).getElementsByTagName( 'div' )[0].getElementsByTagName( '*' );
	
	if( inp.length > 0 )
	{
		for( a = 0; a < inp.length; a++ )
		{
			if( inp[a].name )
			{
				j.addVar ( inp[a].name, inp[a].value ? inp[a].value : inp[a].innerHTML );
			}
			else if( inp[a].getAttribute( 'var' ) )
			{
				j.addVar ( inp[a].getAttribute( 'var' ), inp[a].value ? inp[a].value : inp[a].innerHTML );
			}
		}
	}
	
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		if( r[0] == 'ok' )
		{
			refreshMail( current.getAttribute( 'accountid' ), current.getAttribute( 'folder' ), false, true );
		}
		else if( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
	}
	j.send ();
}

function moveMail( fld )
{
	if( !fld || !ge( 'Mail_inner' ) ) return;
	
	var current = findCurrentFolder();
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&action=mailmove', 'post', true );
	
	var inp = ge( 'Mail_inner' ).getElementsByTagName( 'div' )[0].getElementsByTagName( '*' );
	
	if( inp.length > 0 )
	{
		for( a = 0; a < inp.length; a++ )
		{
			if( inp[a].name )
			{
				j.addVar ( inp[a].name, inp[a].value ? inp[a].value : inp[a].innerHTML );
			}
			else if( inp[a].getAttribute( 'var' ) )
			{
				j.addVar ( inp[a].getAttribute( 'var' ), inp[a].value ? inp[a].value : inp[a].innerHTML );
			}
		}
	}
	
	j.addVar ( 'fld', fld );
	
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		if( r[0] == 'ok' )
		{
			refreshMail( current.getAttribute( 'accountid' ), current.getAttribute( 'folder' ), false, true );
		}
		else if( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
	}
	j.send ();
}

function sendMail()
{
	if( !ge( 'Mail_inner' ) ) return;
	
	var current = findCurrentFolder();
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&action=mailsend', 'post', true );
	
	var inp = ge( 'Mail_inner' ).getElementsByTagName( 'div' )[0].getElementsByTagName( '*' );
	
	if( inp.length > 0 )
	{
		for( a = 0; a < inp.length; a++ )
		{
			if( inp[a].name )
			{
				j.addVar ( inp[a].name, inp[a].value ? inp[a].value : inp[a].innerHTML );
			}
			else if( inp[a].getAttribute( 'var' ) )
			{
				j.addVar ( inp[a].getAttribute( 'var' ), inp[a].value ? inp[a].value : inp[a].innerHTML );
			}
		}
	}
	
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		if( r[0] == 'ok' )
		{
			alert( 'Mail sent ...' );
			refreshMail( current.getAttribute( 'accountid' ), current.getAttribute( 'folder' ), false, true );
		}
		else if( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
	}
	j.send ();
}

function writeMail()
{
	if( !ge( 'Mail_inner' ) ) return;
	
	var current = findCurrentFolder();
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&function=mailwrite', 'post', true );
	
	if( current )
	{
		j.addVar ( 'accid', current.getAttribute( 'accountid' ) );
	}
	
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		if( r[0] == 'ok' )
		{
			ge( 'Mail_inner' ).className = 'write';
			ge( 'Mail_inner' ).parentNode.className = 'write';
			ge( 'Mail_inner' ).innerHTML = r[1];
		}
		else if( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
	}
	j.send ();
}

function writeReply()
{
	if( !ge( 'Mail_inner' ) ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&function=mailwrite&reply=true', 'post', true );
	
	var inp = ge( 'Mail_inner' ).getElementsByTagName( '*' );
	
	if( inp.length > 0 )
	{
		for( a = 0; a < inp.length; a++ )
		{
			if( inp[a].getAttribute( 'var' ) )
			{
				j.addVar ( inp[a].getAttribute( 'var' ), inp[a].innerHTML );
			}
			if( inp[a].name )
			{
				j.addVar ( inp[a].name, inp[a].value );
			}
		}
	}
	
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		if( r[0] == 'ok' )
		{
			ge( 'Mail_inner' ).className = 'write';
			ge( 'Mail_inner' ).parentNode.className = 'write';
			ge( 'Mail_inner' ).innerHTML = r[1];
		}
		else if( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
	}
	j.send ();
}

function writeForward()
{
	if( !ge( 'Mail_inner' ) ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&function=mailwrite&forward=true', 'post', true );
	
	var inp = ge( 'Mail_inner' ).getElementsByTagName( '*' );
	
	if( inp.length > 0 )
	{
		for( a = 0; a < inp.length; a++ )
		{
			if( inp[a].getAttribute( 'var' ) )
			{
				j.addVar ( inp[a].getAttribute( 'var' ), inp[a].innerHTML );
			}
			if( inp[a].name )
			{
				j.addVar ( inp[a].name, inp[a].value );
			}
		}
	}
	
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		if( r[0] == 'ok' )
		{
			ge( 'Mail_inner' ).className = 'write';
			ge( 'Mail_inner' ).parentNode.className = 'write';
			ge( 'Mail_inner' ).innerHTML = r[1];
		}
		else if( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
	}
	j.send ();
}

function openFolder( ele )
{
	if( !ele || !ge( 'Accounts_inner' ) || !ge( 'Mail_inner' ) ) return;
	
	var li = ge( 'Accounts_inner' ).getElementsByTagName( 'li' );
	
	var current;
	
	if( li.length > 0 )
	{
		for( a = 0; a < li.length; a++ )
		{
			if( li[a] == ele )
			{
				li[a].className = li[a].className.split( 'current' ).join( '' ) + 'current';
				
				current = li[a];
			}
			else
			{
				li[a].className = li[a].className.split( 'current' ).join( '' );
			}
		}
	}
	
	refreshMail( current.getAttribute( 'accountid' ), current.getAttribute( 'folder' ), false, true );
	
	getHeaders( current.getAttribute( 'accountid' ), current.getAttribute( 'folder' ) );
}



var lineLimitMail = 1000;
var isRefreshingMail = false;

function refreshMail( accid, fld, current, newfolder )
{
	if( !ge( 'Mail_inner' ) ) return;
	
	var refresh = true;
	
	if( !accid && !fld && current )
	{
		accid = current.getAttribute( 'accountid' );
		fld = current.getAttribute( 'folder' );
		refresh = false;
	}
	
	if( !newfolder && ( ge( 'Mail_inner' ).className == 'open' || ge( 'Mail_inner' ).className == 'write' ) )
	{
		return;
	}
	
	//console.log( 'refreshMail running ...' );
	
	if( isRefreshingMail )
	{
		return;
	}
	this.isRefreshingMail = true;
	
	var mbox = ge( 'Mail_inner' );
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&function=mail', 'post', true );
	if( lineLimitMail )
	{
		j.addVar ( 'limit', lineLimitMail );
	}
	if( accid )
	{
		j.addVar ( 'accid', accid );
	}
	if( fld )
	{
		j.addVar ( 'fld', fld );
	}
	if( !accid && current )
	{
		j.addVar ( 'current', current.getAttribute( 'accountid' ) + '_' + current.getAttribute( 'folder' ) );
	}
	if( mbox.lastmessage && !newfolder )
	{
		j.addVar ( 'lastmessage', mbox.lastmessage );
	}
	if( mbox.lastdatetime && !newfolder )
	{
		j.addVar ( 'lastdatetime', mbox.lastdatetime );
	}
	j.onload = function ()
	{
		var safetyBufferMail = [];
		
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		// Ok we got a result..
		
		if( newfolder )
		{
			mbox.init = false;
			mbox.className = '';
			mbox.parentNode.className = 'MailBox';
			mbox.innerHTML = '';
		}
		
		if( r[0] == 'ok' && r[1] )
		{
			// If this is the first time and no last message
			if( !mbox.init )
			{
				mbox.pmsg = r[1];
				mbox.init = true;
				mbox.lastreq = false;
			}
			// Else use the buffer and add new lines to it
			else
			{
				if( r[3] && mbox.lastdatetime && r[3] < mbox.lastdatetime )
				{
					mbox.pmsg = mbox.pmsg + '<!--message-->' + r[1];
				}
				else
				{
					mbox.pmsg = r[1] + '<!--message-->' + mbox.pmsg;
				}
				
				mbox.lastreq = r[1];
			}
			
			mbox.lastmessage = r[2];
			mbox.lastdatetime = r[3];
		}
		else if( newfolder && r[0] == 'fail' )
		{
			mbox.innerHTML = '';
		}
		
		// If we have messages and it's a new message or a new notice
		
		if( mbox && mbox.pmsg && r[1] )
		{
			// Get messages
			var row = mbox.pmsg.split('<!--message-->');
			
			// Populate list im div
			var li = [];
			
			if( row.length > 0 )
			{
				//for( var l = row.length-2; l >= 0; l-- )
				for( var l = 0; l < row.length; l++ )
				{
					// If number is higher then limit then unset line in row array
					if( l > lineLimitMail )
					{
						row.splice( l, 1 );
						continue;
					}
					
					// Make some shortcuts and vars
					var m = row[l].match( /\ rowid\=\"([^"]*?)\"/i );
					
					var testFoundMsg = false;
					for( var test = 0; test < safetyBufferMail.length; test++ )
					{
						if( safetyBufferMail[test] == m[1] )
						{
							testFoundMsg = true;
							break;
						}
					}
					
					// Assign lines with html to li array
					if( !testFoundMsg )
					{
						li.push ( '<li class="line_' + l + '">' + row[l] + '</li>' );
						// Record item so we don't double show it later..
						safetyBufferMail.push( m[1] );
					}
				}
				
				// Join the buffer again
				mbox.pmsg = row.join('<!--message-->');
			}
			
			// Make a new ul
			var nul = document.createElement( 'ul' );
			nul.innerHTML = li.join('');
			
			// Try to do speedy node swap
			var ul = mbox.getElementsByTagName( 'ul' );
			if( ul.length && ul[0] )
			{
				ul[0].parentNode.replaceChild( nul, ul[0] );
			}
			else
			{
				mbox.innerHTML = '<ul>' + li.join('') + '</ul>';
			}
			
		}
		
		// Clear
		isRefreshingMail = false;
	}
	j.send ();
}

function findCurrentFolder()
{
	if( !ge( 'Accounts_inner' ) ) return false;
	
	var li = ge( 'Accounts_inner' ).getElementsByTagName( 'li' );
	
	if( li.length > 0 )
	{
		for( a = 0; a < li.length; a++ )
		{
			if( li[a].className.indexOf( 'current' ) >= 0 )
			{
				return li[a];
			}
		}
	}
	
	var h4 = ge( 'Accounts_inner' ).getElementsByTagName( 'h4' )[0];
	
	if( h4 )
	{
		return h4;
	}
	
	return false;
}

function refreshAccount( accid, fld, current )
{
	if( !ge( 'Accounts_inner' ) ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&function=accounts', 'post', true );
	if( accid )
	{
		j.addVar ( 'accid', accid );
	}
	if( fld )
	{
		j.addVar ( 'fld', fld );
	}
	if( current )
	{
		j.addVar ( 'current', current.getAttribute( 'accountid' ) + '_' + current.getAttribute( 'folder' ) );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		if( r[0] == 'ok' && r[1] )
		{
			ge( 'Accounts_inner' ).innerHTML = r[1];
		}
		else if( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
		else
		{
			ge( 'Accounts_inner' ).innerHTML = '';
		}
	}
	j.send ();
}

var checkMoreMail = false;

function getHeaders( accid, fld, pass )
{
	var current = findCurrentFolder();
	
	var q = new jaxqueue ( 'getHeaders' );
	q.addUrl ( '?component=chat&action=headers', ( ( accid || fld || pass ) ? true : false ) );
	if( accid )
	{
		q.addVar ( 'accid', accid );
	}
	if( fld )
	{
		q.addVar ( 'fld', fld );
	}
	if( current )
	{
		q.addVar ( 'current', current.getAttribute( 'accountid' ) + '_' + current.getAttribute( 'folder' ) );
	}
	if( checkMoreMail )
	{
		q.addVar ( 'moremail', true );
	}
	if( pass )
	{
		q.addVar ( 'pass', true );
	}
	q.onload ( function ( data )
	{
		var r = data.split ( '<!--separate-->' );
		
		if( r[0] == 'ok' && r[1] )
		{
			refreshAccount( accid, fld, current );
			refreshMail( accid, fld, current );
			
			checkMoreMail = false;
		}
		else if( r[0] == 'ok' )
		{
			checkMoreMail = true;
		}
		else
		{
			/*if( ge( 'Mail_inner' ) )
			{
				ge( 'Mail_inner' ).innerHTML = '';
			}*/
			
			refreshAccount();
		}
	} );
	q.save();
}

getHeaders();
getHeaders( false, false, true );

function createMailAccount()
{
	if( !ge( 'createmailaccount' ) ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&action=mailcreate', 'post', true );
	
	var inp = ge( 'createmailaccount' ).getElementsByTagName( '*' );
	
	if( inp.length > 0 )
	{
		for( a = 0; a < inp.length; a++ )
		{
			if( inp[a].name && inp[a].type == 'checkbox' )
			{
				j.addVar ( inp[a].name, inp[a].checked ? '1' : '-0' );
			}
			else if( inp[a].name )
			{
				j.addVar ( inp[a].name, inp[a].value );
			}
		}
	}
	
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		if( r[0] == 'ok' )
		{
			getHeaders( false, false, true );
			refreshAccount();
			closeWindow();
		}
		else if( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
	}
	j.send ();	
}

function editAccount()
{
	var current = findCurrentFolder();
	
	if( !current || !current.getAttribute( 'accountid' ) ) return;
	
	openWindow( 'Messages', current.getAttribute( 'accountid' ), 'mail' );
}

function deleteAccount()
{
	var current = findCurrentFolder();
	
	if( !current || !current.getAttribute( 'accountid' ) || !confirm( 'are you sure?' ) ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&action=mailaccountdelete', 'post', true );
	j.addVar ( 'accid', current.getAttribute( 'accountid' ) );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		if( r[0] == 'ok' )
		{
			refreshAccount();
			getHeaders( false, false, true );
		}
		else if( r[0] == 'fail' && r[1] )
		{
			alert( r[1] );
		}
	}
	j.send ();
}

function refreshMailFiles( fid )
{
	if( !fid || !ge( 'Hfiles' ) ) return;
	
	var inp = ge( 'Hfiles' ).getElementsByTagName( 'input' )[0];
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&function=mailfiles', 'post', true );
	j.addVar ( 'files', ( inp && inp.value ? ( inp.value + ',' + fid ) : fid ) );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		if( r[0] == 'ok' && r[1] )
		{
			ge( 'Hfiles' ).innerHTML = r[1];
		}
	}
	j.send ();	
}
