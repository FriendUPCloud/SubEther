
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

/*******************************************************************************
*                                                                              *
* Global chat object manages all chats!                                        *
*                                                                              *
* version 0.9                                                                  *
* Based on earlier code from chat.js                                           *
* Refactoring for the win!                                                     *
*                                                                              *
* BUGS:                                                                        *
* addPrivateChat() on notification.js generates double messages!               *
*                                                                              *
*******************************************************************************/

// Check Global Keys

// Check Global Cliks

var clicks = new Object();
var targ;
var button;

function cBubb( e )
{
	if( e.preventDefault ) e.preventDefault();
	e.cancelBubble = true;
	if( e.stopPropagation ) e.stopPropagation();
	return;
}

function ChatClicksDown( e )
{
	if ( !e ) e = window.event;
	targ = e.srcElement ? e.srcElement : e.target;
	button = e.which ? e.which : e.button;
	
	clicks[targ] = true;
}

function ChatClicksUp( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var button = e.which ? e.which : e.button;
	
	clicks[targ] = false;
}

// Assign Global Listeners

if ( window.addEventListener )
{
	window.addEventListener ( 'mousedown', ChatClicksDown );
	window.addEventListener ( 'mouseup', ChatClicksUp );
}
else 
{
	window.attachEvent ( 'onmousedown', ChatClicksDown );
	window.attachEvent ( 'onmouseup', ChatClicksUp );
}

// Chat object starts here

var folderIndex = '';
var lastMessageID = '';
var encryption = false;

var initContactList = true;

var denyScroll = new Object ();

function checkScroll( ele, u )
{
	// TODO: DenyScroll doesn't work when receiving chat messages
	
	if( !ele || !u ) return;
	denyScroll['Chat_' + u] = false;
	//console.log( ele.scrollHeight + ' -- ' + Math.round( ele.scrollHeight - ele.scrollTop ) + ' < ' + ele.scrollTop );
	if( Math.round( ele.scrollHeight - ele.scrollTop ) < 470 )
	//if( Math.round( ele.scrollHeight - ele.scrollTop ) < ele.scrollTop )
	{
		denyScroll['Chat_' + u] = true;
	}
}

chatObject = {
	
	// Public variables: _______________________________________________________
	
	currentFilter: '',             // Filter on contact list
	timeoutContactlist: false,     // Refreshes contact list
	timeoutContactlistTime: 10000, // Every N seconds
	timeoutFolderlist: false,      // Refreshes folder list
	timeoutFolderlistTime: 10000,  // Every N seconds
	timeoutChatlogs: false,        // Refreshes all the private chats
	timeoutChatlogsTime: 3000,     // Every N seconds
	openChats: [],                  // Every userid of open chats
	lineLimit: 50,                  // How many lines to list in the chat
	
	// Public functions: _______________________________________________________
	
	// Initialize
	init: function()
	{
		var o = this;
		
		// Setup refreshing!
		this.refreshContactlist();
		this.refreshFolderlist();
		this.refreshPrivateChats();
		
		// Things to do
		this.windowonload();
		
		// Safetybuffer for received and sent messages
		this.safetyBuffer = [];
	},
	windowonload: function()
	{
		// Quickly scramble to run!
		if( !this.get( 'Chat' ) )
			return setTimeout( 'chatObject.windowonload()', 10 );
			
		// Find earlier states
		if( getCookie( 'ChatContactlistOpen' ) == '1' )
		{
			chatObject.openContactlist();
		}
		// If we have open chat windows
		var eles = getCookie( 'ChatUsersOpen' );
		if( eles )
		{
			eles = eles.split( "\n" );
			for( var a = 0; a < eles.length; a++ )
			{
				if( getCookie( 'ChatOpen_' + eles[a].split( "\t" )[0] ) == '1' )
				{
					chatObject.addPrivateChat( eles[a].split( "\t" )[0], eles[a].split( "\t" )[1], eles[a].split( "\t" )[2] );
					chatObject.maximizePrivateChat( eles[a].split( "\t" )[0] );
				}
				else if( getCookie( 'ChatOpen_' + eles[a].split( "\t" )[0] ) == '2' )
				{
					chatObject.addPrivateChat( eles[a].split( "\t" )[0], eles[a].split( "\t" )[1], eles[a].split( "\t" )[2] );
					chatObject.minimizePrivateChat( eles[a].split( "\t" )[0] );
				}
			}
		}
	},
	
	// Get an element from the layer or from the document
	get: function( ele )
	{
		//if( parent && parent.hasLayer )
		//	return parent.document.getElementById( ele );
		return document.getElementById( ele );
	},
	
	// Opens or closes the contact list
	toggleContactlist: function()
	{
		var chat = this.get( 'Chat' );
		if( !chat ) return;
		
		// We have a closed contact list, make open
		if( !chat.className )
		{
			this.openContactlist();
		}
		// Else, close it
		else if( chat.className == 'open' )
		{
			this.closeContactlist();
		}
	},
	
	// Open the contact list
	openContactlist: function()
	{
		// Open a connection to load the contact list
		if( this.get( 'Chat' ) )
		{
			this.get( 'Chat' ).className = 'open';
			this.get( 'Chat' ).parentNode.className = 'open';
		}
		setCookie( 'ChatContactlistOpen', '1' );
	},
	
	// Close contact list and remove interval so it stops refreshing!
	closeContactlist: function()
	{
		if( this.get( 'Chat' ) )
		{
			this.get( 'Chat' ).className = '';
			this.get( 'Chat' ).parentNode.className = '';
		}
		setCookie( 'ChatContactlistOpen', '0' );
	},
	
	// Refresh the contact list. Yes!
	refreshContactlist: function()
	{
		var o = this;
		var j = new bajax();
		j.openUrl( '?component=chat&function=chat&fastlane=1', 'post', true );
		
		if( initContactList )
		{
			j.addVar ( 'init', true );
		}
		
		// TODO: Use this one in the future!
		//j.openUrl( '?component=chat&function=contacts', 'post', true );
		
		// Add current online status..
		if( this.get( 'Chat_head' ) && this.get( 'Chat_head' ).phead )
		{
			j.addVar ( 'online', this.get( 'Chat_head' ).phead );
		}
		else if( this.get( 'Chat_list' ) && this.get( 'Chat_list' ).online )
		{
			j.addVar ( 'online', this.get( 'Chat_list' ).online );
		}
		
		// Onload action
		j.retries = 5;
		j.onload = function()
		{
			var ch = o.get( 'Chat' ); 
			if( !ch ) 
			{
				clearTimeout( o.timeoutContactlist );
				if( this.retries > 0 )
				{
					// Wait until we are out of retries!
					// DOM node might not be loaded yet.
					return setTimeout( function()
					{
						j.retries--;
						j.onload();
					}, 100 );
				}
				// Next run
				o.timeoutContactlist = setTimeout( function(){ o.refreshContactlist() }, o.timeoutContactlistTime );
				return;
			}
			
			var chatList = o.get( 'Chat_list' );
			var chatHead = o.get( 'Chat_head' );
			
			// Get response data
			var r = this.getResponseText().split( '<!--separate-->' );
			
			// Control values
			if( r.length >= 2 )
			{
				// Assign values				
				var h = 'Chat (' + ( r[2] != 0 ? r[2].split( ',' ).length : '0' ) + ')';
				
				// If we have chat encryption set or not
				encryption = ( r[4] && r[4] > 0 ? true : false );
				
				var n = r[1];
				if( chatHead && h != chatHead.innerHTML )
				{
					chatHead.innerHTML = h; // Chat head / title
				}
				if( !chatHead || ( r[2] != chatHead.phead ) )
				{
					var openLi = false;
					var oli = false;
					
					if( chatList.innerHTML )
					{
						oli = chatList.getElementsByTagName( 'li' );
						
						if( oli.length > 0 )
						{
							for( a = 0; a < oli.length; a++ )
							{
								if( oli[a].id && oli[a].id.split('ChatContact_')[1] && oli[a].className && oli[a].className.indexOf( 'active' ) >= 0 )
								{
									openLi = oli[a].id.split('ChatContact_')[1];
								}
							}
						}
					}
					
					if( n )
					{
						if( openLi && oli )
						{
							var mli = new Object();
							
							var nli = n.split( '<!--contacts-->' );
							
							if( nli.length > 0 )
							{
								for( b = 0; b < nli.length; b++ )
								{
									if( nli[b].split('<!--var-->')[0] && nli[b].split('<!--var-->')[0] != openLi )
									{
										mli[nli[b].split('<!--var-->')[0]] = nli[b].split('<!--var-->')[1];
									}
								}
							}
							
							if( mli )
							{
								for( c = 0; c < oli.length; c++ )
								{
									if( oli[c].id && oli[c].id.split('ChatContact_')[1] && mli[oli[c].id.split('ChatContact_')[1]] )
									{
										oli[c].innerHTML = mli[oli[c].id.split('ChatContact_')[1]];
									}
								}
							}
						}
						else
						{
							var nli = n.split( '<!--contacts-->' );
							
							if( nli.length > 0 )
							{
								for( d = 0; d < nli.length; d++ )
								{
									nli[d] = '<li id="ChatContact_' + nli[d].split('<!--var-->')[0] + '">' + nli[d].split('<!--var-->')[1] + '</li>';
								}
							}
							
							var ul = '<div class="inner"><ul>' + nli.join('') + '</ul></div>';
							
							chatList.innerHTML = ul; // Chat contact list
						}
					}
				}
				if( chatHead )
				{
					o.get( 'Chat_head' ).phead = r[2];
				}
				if( chatList )
				{
					o.get( 'Chat_list' ).online = r[2];
				}
				
				if( chatList )
				{
					// Update time if found on contacts
					var span = chatList.getElementsByTagName( 'span' );
					if( span && span.length > 0 )
					{
						for( a = 0; a < span.length; a++ )
						{
							if( span[a].className == 'time' && span[a].innerHTML != '' )
							{
								var time = TimeToHuman( span[a].getAttribute( 'time' ), 'mini' );
								
								if( time && time != span[a].innerHTML )
								{
									span[a].innerHTML = time;
								}
							}
						}
					}
				}
				
				initContactList = false;
				
				// Update filter
				o.filterContactlist( o.currentFilter );
			}
			// Next run
			clearTimeout( o.timeoutContactlist );
			o.timeoutContactlist = setTimeout( function(){ o.refreshContactlist() }, o.timeoutContactlistTime );
		}
		// Do loading
		j.send();
	},
	
	// Refresh the folder list
	refreshFolderlist: function( cid )
	{
		var o = this;
		
		var fld = this.get( 'ListIM_inner' );
		
		if( !fld ) return;
		
		var ele = fld.getElementsByTagName( 'li' );
		
		if( ele.length > 0 )
		{
			for( a = 0; a < ele.length; a++ )
			{
				// Remove current if cid is a new id
				if( cid && ele[a].className.indexOf( 'current' ) >= 0 && ele[a].id && cid != ele[a].id.split( '_' )[1] )
				{
					ele[a].className = ele[a].className.split( 'current ' ).join( '' );
				}
				// Add a new current if cid id matches
				else if( cid && ele[a].className.indexOf( 'current' ) < 0 && ele[a].id && cid == ele[a].id.split( '_' )[1] )
				{
					ele[a].className = 'current ' + ele[a].className.split( 'current ' ).join( '' );
				}
				// Find cid id if cid is not defined
				else if( !cid && ele[a].className.indexOf( 'current' ) >= 0 && ele[a].id )
				{
					cid = ele[a].id.split( '_' )[1];
				}
			}
		}
		
		var j = new bajax();
		j.openUrl( '?component=chat&function=folders', 'post', true );
		j.addVar ( 'fld', folderIndex );
		j.addVar ( 'cid', cid );
		j.addVar ( 'lastmessage', lastMessageID );
		j.onload = function()
		{
			// Get response data
			var r = this.getResponseText().split( '<!--separate-->' );
			
			if( r[0] == 'ok' && r[1] )
			{
				if( r[1] != fld.innerHTML )
					fld.innerHTML = r[1];
				
				if( r[2] )
				{
					lastMessageID = r[2];
				}
			}
			
			// Next run
			clearTimeout( o.timeoutFolderlist );
			o.timeoutFolderlist = setTimeout( function()
			{
				o.refreshFolderlist();
			}, o.timeoutFolderlistTime );
		}
		j.send();
	},
	
	// Open message
	openPrivateMessage: function( userid )
	{
		if( !userid ) 
		{
			userid = '0';
			//return;
		}
		
		// Remove chatwindow if there is any
		this.removePrivateChat( userid );
		
		var im = this.get( 'RightIM_inner' );
		var userDiv = this.get( 'Chat_' + userid );
		
		if( !im ) return;
		
		if( !userDiv )
		{
			var cur = '';
			var ele = im.getElementsByTagName( 'div' );
			if( ele.length > 0 )
			{
				for( a = 0; a < ele.length; a++ )
				{
					if( ele[a].className == 'messages' )
					{
						cur = ele[a].innerHTML;
						break;
					}
				}
			}
			
			var d = document.createElement( 'div' );
			d.id = 'Chat_' + userid;
			im.innerHTML = '';
			im.appendChild( d );
			this.openChats.push ( { 
				userId:      userid, 
				lastMessage: 0, 
				lastSeen:    0,
				keys: { publicKey: 0 }
			} );
			// Add html
			d.innerHTML = '<div id="Chat_inner_' + userid + '" class="messages">' + cur + '</div>' +
			'<div class="post"><textarea onkeydown="return chatObject.sendMessage(\'' + userid + '\',this,event)" onclick="return chatObject.scrollDownMessages(\'' + userid + '\')" placeholder="Ny melding..."></textarea></div>' +
			'<div class="toolbar"><input name="crypto" type="checkbox" value="1"' + ( encryption ? 'checked="checked"' : '' ) + ' onclick="chatObject.saveChatSettings(this)"><span> crypto</span></div>';
		}
		// Only create if not existing
		else if( userDiv && im.className.indexOf( 'active' ) < 0 ) 
		{
			this.openChats.push ( { 
				userId:      userid, 
				lastMessage: 0, 
				lastSeen:    0,
				keys: { publicKey: 0 }
			} );
		}
		
		im.className = im.className.split( 'active' ).join( '' ) + 'active';
		
		// Refresh folderlist
		this.refreshFolderlist( userid );
		
		// TODO: only this one!
		this.refreshPrivateChats();
	},
	
	focusOnTextArea: function( userid )
	{
		// Focus on text area
		var d = ge( 'Chat_' + userid );
		var tx = d.getElementsByTagName( 'textarea' );
		if( tx.length && tx[0] )
		{
			tx[0].focus();
		}
	},
	
	scrollDownMessages: function( userid )
	{
		if( userid && ge( 'Chat_inner_' + userid ) )
		{
			// Scroll to bottom
			//ge( 'Chat_inner_' + userid ).scrollTop = 9999999999999;
			ge( 'Chat_inner_' + userid ).scrollTop = ge( 'Chat_inner_' + userid ).scrollHeight;
		}
	},
	
	// Open contact message in a dropdown
	openContactMessage: function( userid )
	{
		if( !userid ) return;
		
		var cl = this.get( 'Chat_list' );
		var im = this.get( 'ChatContact_' + userid );
		var userDiv = this.get( 'Chat_' + userid );
		
		if( !im || !cl ) return;
		
		// Close
		if( im.className && im.className.indexOf( 'open' ) >= 0 )
		{
			//im.style.background = 'red';
			//this.filterContactsByID();
			if( document.body )
			{
				document.body.className = document.body.className.split( ' openprivchat' ).join( '' );
			}
			if( ge( 'Footer__' ) )
			{
				ge( 'Footer__' ).style.position = 'relative';
			}
			cl.className = cl.className.split( 'open' ).join( '' );
			im.className = im.className.split( 'open ' ).join( '' );
			//location.hash = '';
			return;
		}
		// Open
		else if( im.className && im.className.indexOf( 'active' ) >= 0 )
		{
			//im.style.background = '';
			//this.filterContactsByID( userid );
			if( document.body )
			{
				document.body.className = document.body.className.split( ' openprivchat' ).join( '' ) + ' openprivchat';
			}
			cl.className = cl.className.split( 'open' ).join( '' ) + 'open';
			im.className = im.className.split( 'open ' ).join( '' ) + 'open ';
			//location.hash = 'c' + userid;
			this.focusOnTextArea( userid );
			return;
		}
		
		var li = cl.getElementsByTagName( 'li' );
		
		if( li.length > 0 )
		{
			for( a = 0; a < li.length; a++ )
			{
				if( li[a].className && li[a].className.indexOf( 'open' ) >= 0 )
				{
					li[a].className = li[a].className.split( 'open ' ).join( '' );
				}
				if( li[a].className && li[a].className.indexOf( 'active' ) >= 0 )
				{
					li[a].className = li[a].className.split( 'active ' ).join( '' );
				}
			}
		}
		
		if( document.body )
		{
			document.body.className = document.body.className.split( ' openprivchat' ).join( '' ) + ' openprivchat';
		}
		cl.className = cl.className.split( 'open' ).join( '' ) + 'open';
		im.className = im.className.split( 'open ' ).join( '' ) + 'open ';
		im.className = im.className.split( 'active ' ).join( '' ) + 'active ';
		
		// Remove chatwindow if there is any
		this.removePrivateChat();
		
		if( !userDiv )
		{
			var cur = '';
			var ele = im.getElementsByTagName( 'div' );
			if( ele.length > 0 )
			{
				for( a = 0; a < ele.length; a++ )
				{
					if( ele[a].className == 'messages' )
					{
						cur = ele[a].innerHTML;
						break;
					}
				}
			}
			
			var d = document.createElement( 'div' );
			d.id = 'Chat_' + userid;
			d.className = 'open';
			im.appendChild( d );
			this.openChats.push ( { 
				userId:      userid, 
				lastMessage: 0, 
				lastSeen:    0,
				keys: { publicKey: 0 }
			} );
			// Add html
			d.innerHTML = '<div id="Chat_inner_' + userid + '" class="messages">' + cur + '</div>' +
			'<div class="post"><textarea onkeydown="return chatObject.sendMessage(\'' + userid + '\',this,event)" onclick="return chatObject.scrollDownMessages(\'' + userid + '\')" placeholder="Ny melding..."></textarea></div>' +
			'<div class="toolbar"><input name="crypto" type="checkbox" value="1"' + ( encryption ? 'checked="checked"' : '' ) + ' onclick="chatObject.saveChatSettings(this)"><span> crypto</span></div>';
			
			
		}
		// Only create if not existing
		else if( userDiv && im.className.indexOf( 'active' ) < 0 ) 
		{
			this.openChats.push ( { 
				userId:      userid, 
				lastMessage: 0, 
				lastSeen:    0,
				keys: { publicKey: 0 }
			} );
		}
		
		//this.filterContactsByID( userid );
		
		// Set focus
		this.focusOnTextArea( userid );
		
		//location.hash = 'c' + userid;
		
		// TODO: only this one!
		this.refreshPrivateChats();
		
		// Add eventlistener on resize
		if ( this.get( 'Chat_inner_' + userid ) && window.addEventListener )
		{
			window.addEventListener( 'resize', function(){ chatObject.scrollDownMessages( userid ) } );
		}
		else 
		{
			window.attachEvent( 'onresize', function(){ chatObject.scrollDownMessages( userid ) } );
		}
	},
	
	// Add a new private chat window
	addPrivateChat: function( userid, displayName, img )
	{
		// Get content
		if( !displayName ) displayName = '...';
		if( !img ) img = false;
		var userDiv = this.get( 'Chat_' + userid );
		var tabsDiv = this.get( 'ChatTabs' );
		if( !tabsDiv ) return;
		
		var online = false; // Online status
		
		// Only create if not existing
		if( !userDiv ) 
		{
			var d = document.createElement( 'div' );
			d.id = 'Chat_' + userid;
			d.className = 'chattab';
			this.get( 'ChatTabs' ).appendChild( d );
			this.openChats.push ( { 
				userId:      userid, 
				lastMessage: 0, 
				lastSeen:    0,
				keys: { publicKey: 0 },
				displayName: displayName,
				img:		 img
			} );
			
			// Add html
			d.innerHTML = '<div class="chatpriv">' +
			'<div id="Chat_head_' + userid + '" class="head" onclick="chatObject.togglePrivateChat(' + userid + ')">' +
			'<div class="status"><img src="' + ( online > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + '"/></div> ' +
			( img ? '<span class="image" style="background-image:url(\'' + img + '\')" onmouseout="tooltips(this,\'close\')" onmouseover="tooltips(this,\'open\')"></span> ' : '' ) +
			'<span class="name">' + displayName + '</span>' +
			'<div class="delete" onclick="chatObject.removePrivateChat(' + userid + ')">x</div>' +
			( img ? renderTooltips( displayName ) : '' ) + 
			'</div>' +
			'<div id="Chat_inner_' + userid + '" class="messages" onmouseup="checkScroll(this,\'' + userid + '\')"></div>' +
			//'<div class="post"><input onkeyup="if( event.keyCode == 13 ) { chatObject.sendMessage(' + userid + ', this) }"/></div>' +
			'<div class="post"><textarea id="Chat_send_' + userid + '" onkeydown="return chatObject.sendMessage(\'' + userid + '\',this,event)" onclick="return chatObject.scrollDownMessages(\'' + userid + '\')" placeholder="Ny melding..."></textarea></div>' +
			'<div class="toolbar"><input name="crypto" type="checkbox" value="1"' + ( encryption ? 'checked="checked"' : '' ) + ' onclick="chatObject.saveChatSettings(this)"><span> crypto</span></div>' + 
			'</div>';
			userDiv = d;
		}
		
		// Focus on chat input
		if ( userDiv.getElementsByTagName( 'textarea' ).length )
		{
			userDiv.getElementsByTagName( 'textarea' )[0].focus();
		}
		
		// Maximize that tab
		this.maximizePrivateChat( userid );
		
		// TODO: only this one!
		this.refreshPrivateChats();
		
		// If we have open chat windows, register this userid in it!
		var eles = getCookie( 'ChatUsersOpen' );
		if( eles )
		{
			eles = eles.split( "\n" );
			var found = false;
			var newe = [];
			for( var a = 0; a < eles.length; a++ )
			{
				if( eles[a].split( "\t" )[0] == userid )
				{
					found = true;
				}
				else
				{
					newe.push( eles[a] );
				}
			}
			setCookie( 'ChatUsersOpen', newe.join( "\n" ) + "\n" + userid + "\t" + displayName + "\t" + img );
		}
		else setCookie( 'ChatUsersOpen', userid + "\t" + displayName + "\t" + img );
	},
	
	// Remove a private chat window
	removePrivateChat: function( userid )
	{
		var tabsDiv = this.get( 'ChatTabs' );
		if( tabsDiv )
		{
			var userDiv = this.get( 'Chat_' + userid );
			if( userDiv && userDiv.parentNode.id == 'ChatTabs' )
			{
				tabsDiv.removeChild( userDiv );
			}
		}
		
		if( userid )
		{
			// Remove from refresh queue
			var out = [];
			for( var a = 0; a < this.openChats.length; a++ )
			{
				if( this.openChats[a].userId != userid )
				{
					out.push( this.openChats[a] );
				}
			}
			this.openChats = out;
			setCookie( 'ChatOpen_' + userid, '0' );
		}
		else
		{
			// Remove from refresh queue
			var out = [];
			for( var a = 0; a < this.openChats.length; a++ )
			{
				if( this.openChats[a].userId )
				{
					var userDiv = this.get( 'Chat_' + this.openChats[a].userId );
					if( userDiv && userDiv.parentNode )
					{
						userDiv.parentNode.removeChild( userDiv );
					}
					
					setCookie( 'ChatOpen_' + this.openChats[a].userId, '0' );
				}
			}
			this.openChats = out;
		}
	},
	
	// Toggles maximized / minimized state
	togglePrivateChat: function( userid )
	{
		var userDiv = this.get( 'Chat_' + userid );
		if( !userDiv ) return;
		if( userDiv.className == 'chattab' )
			return this.maximizePrivateChat( userid );
		else return this.minimizePrivateChat( userid );
	},
	
	// Maximize a private chat window
	maximizePrivateChat: function( userid )
	{
		var userDiv = this.get( 'Chat_' + userid );
		if( !userDiv ) return;
		userDiv.className = 'chattab open';
		userDiv.getElementsByTagName( 'div' )[0].className = 'chatpriv open';
		setCookie( 'ChatOpen_' + userid, '1' );
		
		// Set focus
		this.focusOnTextArea( userid );
	},
	
	// Minimize a private chat window
	minimizePrivateChat: function( userid )
	{
		var userDiv = this.get( 'Chat_' + userid );
		if( !userDiv ) return;
		userDiv.className = 'chattab';
		userDiv.getElementsByTagName( 'div' )[0].className = 'chatpriv';
		setCookie( 'ChatOpen_' + userid, '2' );
	},
	
	// Put a notification on a private chat window
	notifyPrivateChat: function( userid, displayName )
	{
		var userDiv = this.get( 'Chat_' + userid );
		if( !userDiv ) return;
		
		if( !displayName ) displayName = '...';
	
		var notifyDiv = userDiv.getElementsByTagName( 'div' )[0];
		if( !notifyDiv ) return;
		notifyDiv.className = 'chatpriv notify';
		if( typeof( titleNotifications ) != 'undefined' )
			titleNotifications( false, displayName );
	},
	
	// Refreshes all private chats and populates their logs - also check's 
	// notification events etc.
	refreshPrivateChats: function()
	{
		if( !this.openChats.length )
			return;
		
		// Make sure this doesn't clog up when network is bad
		if( this.isRefreshing ) return;
		this.isRefreshing = true;
		
		var list = [];
		var o = this;
		var j = new bajax();
		j.openUrl( '?function=checkqueue&global=true&type=privchat', 'post', true );
		
		// Get some information about all open chats
		var removalQueue = [];
		for( var a = 0; a < this.openChats.length; a++ )
		{
			// Make sure this chat actually is open
			var userDiv = this.get( 'Chat_' + this.openChats[a].userId );
			if( !userDiv )
			{
				// Remove from refresh queue
				removalQueue.push( function() {
					o.removePrivateChat( o.openChats[a].userId );
				} );
				continue;
			}
			
			var oc = this.openChats[a].userId;
			var nd = this.openChats[a].notify;
			var rd = this.openChats[a].read;
			var lm = this.openChats[a].lastMessage;
			var ls = this.openChats[a].lastSeen;
			var row = {
				Url: '?component=chat&function=chat',
				//Url: '?component=chat&function=messages',
				postVars: {
					u: oc,
					notify: nd,
					read: rd,
					lastmessage: lm,
					lastseen: ls,
					Name: 'refreshPrivChat' // This one is not needed..
				}
			};
			list.push( row );
		}
		
		// Delayed removal
		if( removalQueue.length )
		{
			for( var a = 0; a < removalQueue.length; a++ )
				removalQueue[a]();
		}
		
		j.addVar( 'jaxqueue', JSON.stringify( list ) );
		j.onload = function()
		{
			// Clear safetybuffer
			o.safetyBuffer = [];
			
			var res = this.getResponseText ();
			if( res ) res = res.split ( '<!--jaxqueue-->' );
			
			for ( var b = 0; b < res.length; b++ )
			{
				// Skip empty entries
				if( res[b].length <= 0 ) continue;
				
				// get type from result list
				var data = res[b].split ( '<!--jsfunction-->' );
				var rnam = data[0]; var rdat = data[1];
				
				if( !rdat ) continue;
				
				var r = rdat.split( '<!--separate-->' );
				if( r.length < 2 ) continue;
				
				var u = r[7];
				var cbox = o.get( 'Chat_' + u );
				if( !cbox ) continue;
				var copen = ( ( cbox.className == 'chattab open' || cbox.className == 'open' ) ? 1 : 0 );
				var cclas = ( cbox.getElementsByTagName( 'div' ).length > 0 ? cbox.getElementsByTagName( 'div' )[0] : false );
				var chbox = o.get( 'Chat_inner_' + u );
				var hbox = o.get( 'Chat_head_' + u );
				var displayName = '...';
				var img = r[8];
				//var key = r[11];
				var pendingUpdate = false;
				
				// Get/set some data
				for( var c = 0; c < o.openChats.length; c++ )
				{
					if( o.openChats[c].userId == u )
					{
						displayName = o.openChats[c].displayName;
						if( r[3] != '' ) o.openChats[c].notify = r[3];
						if( r[4] != '' )
						{
							if( r[4] != o.openChats[c].lastMessage )
							{
								pendingUpdate = true;
							}
							o.openChats[c].lastMessage = r[4];
						}
						if( r[6] != '' )
						{
							if( !pendingUpdate && r[6] != o.openChats[c].lastSeen )
							{
								pendingUpdate = true;
							}
							o.openChats[c].lastSeen = r[6];
						}
						if( r[9] != '' ) o.openChats[c].read = r[9];
						
						// If found notification and tab is closed notify user
						if( !copen && r[3] == '1' )
						{
							titleNotifications( false, displayName );
							cclas.className = 'chatpriv notify';
						}
						// If found notification and tab is open reset and set isread on notify
						if( copen && ( r[9] == '0' || !r[9] ) )
						{
							if( typeof resetMessageNotify == 'function' ) resetMessageNotify( u, 'read' );
							o.openChats[c].notify = 0;
							o.openChats[c].read = 1;
						}
						// If we have publickey for this user store it
						o.openChats[c].keys.publicKey = ( r[10] ? r[10] : '' );
						// If we have encryptionkey for this user store it
						//o.openChats[c].keys.encryptionKey = ( r[11] ? r[11] : '' );
						if( r[11] )
						{
							o.openChats[c].keys.encryptionKey = r[11].split( '<!--encryptionid-->' )[0];
							
							if( r[11].split( '<!--encryptionid-->' )[1] )
							{
								o.openChats[c].keys.encryptionId = r[11].split( '<!--encryptionid-->' )[1];
							}
						}
						break;
					}
				}
				
				// Remove the loader
				if( cbox.getElementsByTagName( 'div' )[0].className.indexOf( 'loading' ) >= 0 )
				{
					var loading = cbox.getElementsByTagName( 'div' )[0];
					if( loading && loading.className )
						loading.className = loading.className.split(' loading').join('');
				}
				
				// Ok we got a result..
				if ( r[0] == 'ok' && r[1] )
				{
					// If we have audio notification run it
					if( r[5] )
					{
						// Check if this is a live invite and send a call request
						if ( r[12] )
						{
							o.receiveCall( u, r[12], displayName, img );
						}
						// Run default notifications on messages
						else
						{
							if( !copen && r[3] != '' && r[3] != '0' )
							{
								var msg = r[3];
								
								// TODO: Find a way to see if it's encrypted or a normal message ...
								
								// Decrypt encrypted messages before sending desktop notification ...
								if( r[11] )
								{
									var dmsg = o.decryptMessage( msg, r[11].split( '<!--encryptionid-->' )[0] );
								
									if( dmsg )
									{
										dmsg = nl2br( dmsg );
										dmsg = makeLinks( dmsg );
										dmsg = stripSlashes( dmsg );
										dmsg = renderSmileys( dmsg );
									
										msg = dmsg;
									}
								}
								
								// Desktop notify
								titleNotifications( false, displayName, 'desktop', msg, img );
							}
							
							// Play audio on alert notify
							playAudio( r[5], function(){ removeAudio(); }, 10, true );
							
							// Reset notification so that the audio notification stops
							if( typeof resetMessageNotify == 'function' ) resetMessageNotify( u );
						}
					}
					
					// Don't update on equal data
					if ( chbox.innerHTML != '' && chbox.pmsg && chbox.phead )
					{
						if ( chbox.phead == r[2] && ( chbox.pmsg == r[1] || chbox.lastreq != '' && chbox.lastreq == r[1] ) )
						{
							return;
						}
					}
					
					// If this is the first time and no last message
					if( !chbox.init )
					{
						chbox.pmsg = r[1];
						chbox.init = true;
						chbox.lastreq = false;
					}
					// Else use the buffer and add new lines to it
					else
					{
						chbox.pmsg = r[1] + chbox.pmsg;
						chbox.lastreq = r[1];
					}
					
					chbox.lastmessage = r[4];
					chbox.read = r[9];
				}
				
				// Update header
				if( chbox && chbox.phead != r[2] && hbox )
				{
					chbox.phead = r[2];
					
					var boxHTML =  '<div class="status"><img src="' + 
						( r[2] > 0 ? 'admin/gfx/icons/bullet_green.png' : 'admin/gfx/icons/bullet_white.png' ) + 
						'"/></div> ' + ( img ? '<span class="image" style="background-image:url(\'' + img + '\')" onmouseout="tooltips(this,\'close\')" onmouseover="tooltips(this,\'open\')"></span> ' : '' ) +
						'<span class="name">' + displayName + '</span>' +
						'<div class="delete" onclick="chatObject.removePrivateChat( ' + u + ' )">x</div>' + ( img ? renderTooltips( displayName ) : '' );
						
					if( hbox.innerHTML != boxHTML )
						hbox.innerHTML = boxHTML;
				}
				
				// If receiver has accepted or declined call give the sender info about it and stop the audio
				if( r[13] != '' )
				{
					removeAudio();
					if( typeof resetMessageNotify == 'function' ) resetMessageNotify( u, 'connected', true );
					
					if( r[13] == '-1' )
					{
						popupClose();
					}
				}
				
				
				// If we have messages and it's a new message or a new notice
				if( chbox && chbox.pmsg && ( r[1] || r[6] ) )
				{
					// Get messages
					var row = chbox.pmsg.split('<!--message-->');
					
					// Populate list im div
					var li = [];
					
					if( row.length > 0 )
					{
						for( var l = row.length-2; l >= 0; l-- )
						{
							// If number is higher then limit then unset line in row array
							if( l > o.lineLimit )
							{
								row.splice( l, 1 );
								continue;
							}
							
							// Get more data for every message
							row[l] = row[l].split('<!--data-->');
							
							var mskey = ( row[l][1] ? row[l][1] : false );
							var msgid = ( row[l][2] ? row[l][2] : false );
							
							// Make some shortcuts and vars
							var m = row[l][0].match( /\ rowid\=\"([^"]*?)\"/i );
							
							var testFoundMsg = false;
							for( var test = 0; test < o.safetyBuffer.length; test++ )
							{
								if( o.safetyBuffer[test] == m[1] )
								{
									testFoundMsg = true;
									break;
								}
							}
							
							// If we find trace of encryption, try to decrypt it
							var crypto = row[l][0].match( /-----ENCRYPTION-----([^"]*?)\-----ENCRYPTION-----/i );
							
							if( crypto && trim( crypto[1] ) )
							{
								var estr = trim( crypto[1] );
								
								var dmsg = o.decryptMessage( estr, mskey );
								
								if( dmsg )
								{
									dmsg = nl2br( dmsg );
									dmsg = makeLinks( dmsg );
									dmsg = stripSlashes( dmsg );
									dmsg = renderSmileys( dmsg );
								}
								else
								{
									dmsg = estr;
									
									//dmsg = dmsg.split( "\r\n" ).join( "" );
									//dmsg = dmsg.split( "\n" ).join( "" );
								}
								
								row[l][0] = row[l][0].split( crypto[0] ).join ( dmsg );
								
								var cmsg = row[l][0];
								
								//console.log( crypto[0] + ' || ' + cmsg );
							}
							else
							{
								var cmsg = row[l][0];
								
								cmsg = nl2br( cmsg );
								cmsg = makeLinks( cmsg );
								cmsg = stripSlashes( cmsg );
								cmsg = renderSmileys( cmsg );
							}
							
							// Assign lines with html to li array
							if( !testFoundMsg )
							{
								li.push ( '<li class="line_' + l + '">' + cmsg + '</li>' );
								// Record item so we don't double show it later..
								o.safetyBuffer.push( m[1] );
							}
							
							// Join the message with the others data again for buffer storage
							row[l] = row[l].join('<!--data-->');
						}
						
						// If last message is seen by the contact and it was more then 2min set last seen date
						if( r[6] )
						{
							// Set seen
							chbox.lastseen = r[6];
							
							li.push ( '<li class="line_info"><span class="icon"></span><span class="info">' + r[6] + '</span></li>' );
						}
						
						// Join the buffer again
						chbox.pmsg = row.join('<!--message-->');
					}
					
					// Only update if there's anything to update
					if( pendingUpdate )
					{
						// Make a new ul
						var nul = document.createElement( 'ul' );
						var lij = li.join('');
						
						// Try to do speedy node swap
						var ul = chbox.getElementsByTagName( 'ul' );
						if( ul.length && ul[0] )
						{
							nul.innerHTML = lij;
							ul[0].parentNode.replaceChild( nul, ul[0] );
						}
						else 
						{
							var chml = '<div id="Chat_Messages_' + u + '" class="inner"><ul>' + lij + '</ul></div>';
							
							chbox.innerHTML = chml;
						}
						
						// Scroll down
						// TODO: Not while forcibly scrolling up!
						if( !denyScroll['Chat_' + u] )
						{
							//chbox.scrollTop = chbox.scrollHeight;
							//chbox.scrollTop = 9999999999999;
							o.scrollDownMessages( u );
						}
					}
				}
			}
			// Clear
			o.isRefreshing = false;
			clearTimeout( o.timeoutChatlogs );
			o.timeoutChatlogs = setTimeout( function()
			{
				o.refreshPrivateChats();
			}, o.timeoutChatlogsTime );
		}
		j.send();
	},
	
	// Sends a message to a user
	sendMessage: function( userid, mess, e, msg )
	{
		if( !userid || !mess ) return;
		var o = this;
		
		var kc = e.which ? e.which : e.keyCode;
		var sh = e.shiftKey;
		
		if( ( !sh && kc == 13 ) || msg )
		{
			var tmsg = ( mess.value ? mess.value : mess.innerHTML );
			
			if ( msg )
			{
				tmsg = msg;
			}
			
			if( !tmsg.length || !tmsg.split( /\s+/ ).join ( '' ).length ) return cBubb( e );
			
			var encrypted = false;
			
			if( o.openChats.length )
			{
				for( var c = 0; c < o.openChats.length; c++ )
				{
					// If we find user and message, users public key and your public key is defined do crypto
					if( o.openChats[c].userId == userid && tmsg && o.openChats[c].keys.publicKey && encryption/* && getBrowserStorage( 'publickey' )*/ )
					{
						var cmsg = htmlentities( tmsg );
						//var cmsg = tmsg;
						
						var encrypted = o.encryptMessage( cmsg, userid );
						
						//console.log( encrypted + ' -- ' + cmsg + ' -- ' + userid );
					}
				}
			}
			
			
			
			/*// Send message
			var j = new bajax ();
			j.openUrl ( '?component=chat&action=chat&fastlane=1', 'post', true );
			j.addVar ( 'u', userid );
			if( encrypted )
			{
				j.addVar ( 'encryption', '1024 bit RSA' );
				j.addVar ( 'data', JSON.stringify( encrypted ) );
			}
			else
			{
				j.addVar ( 'm', tmsg );
			}
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );	
				if ( r[0] == 'ok' )
				{
					// ...
				}
			}
			j.send ();*/
			
			
			if( encrypted )
			{
				var vars = {
					'ContactID'  : userid, 
					'Message'    : encrypted['message'], 
					'CryptoID'	 : encrypted['encryptionid'], 
					'CryptoKeys' : JSON.stringify( encrypted ), 
					'Encryption' : '1024 bit RSA', 
					'Date'		 : 'removethis'
				};
			}
			else
			{
				var vars = {
					'ContactID'  : userid,
					'Message'    : tmsg, 
					'Date'		 : 'removethis'
				};
			}
			
			API.messages.put( vars, function( res, data )
			{
				
				console.log( { res : res, data : data } );
				
				if ( res == 'ok' )
				{
					// ...
				}
				
			} );
			
			
			// Immediately clear!
			if( mess.innerHTML ) mess.innerHTML = '';
			if( mess.value ) mess.value = '';
			
			// Blur on submit for mobiles
			var uel = navigator.userAgent.toLowerCase();
			if( uel.indexOf( 'android' ) >= 0 || uel.indexOf( 'ios' ) >= 0 )
				mess.blur();
			
			// Instantly refresh
			var chbox = o.get( 'Chat_inner_' + userid );
			if( !chbox ) return cBubb( e );
			
			var ul = chbox.getElementsByTagName( 'ul' );
			if( !ul.length )
			{
				chbox.innerHTML = '<div id="Chat_Messages_' + userid + '" class="inner"><ul></ul></div>';
				ul = chbox.getElementsByTagName( 'ul' );
				//return cBubb( e );
			}
			
			// Remove "last seen"
			var lis = ul[0].getElementsByTagName( 'li' );
			for( var za = 0; za < lis.length; za++ )
			{
				if( lis[za].className == 'line_info' )
					lis[za].parentNode.removeChild( lis[za] );
			}
			
			var li = document.createElement( 'li' );
			li.className = 'newline';
			li.innerHTML = '<div class="ChatRow" rowid="0"><div class="ChatInfo"><div class="Image">&nbsp;</div><div class="Nick">(Sending message..)</div><div class="Time">&nbsp;</div></div><div class="ChatMessage">' + tmsg + '</div></div>';
			ul[0].appendChild( li );
			
			// Scroll to bottom
			//chbox.scrollTop = 9999999999999;
			o.scrollDownMessages( userid );
			
			return cBubb( e );
		}
	},
	
	// Call user
	callUser: function( cid, user, img, e )
	{
		if ( !cid || !e ) return false;
		
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=chat&function=voicechat', 'post', true );
		j.addVar ( 'call', cid );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );	
			if ( r[0] == 'ok' && r[1] )
			{
				//console.log( r[1] );
				
				//popupCenter( r[1], cid, 1000, 600 );
				
				// Open in new tab instead of popup
				var win = window.open( r[1], '_blank' );
				
				if ( win )
				{
					win.focus();
				}
				
				chatObject.addPrivateChat( cid, user, img );
				
				// Receiving some messages..
				addMessageHandler( 'default', function( msg )
				{
					//console.log( msg );
					
					//console.log( msg.data.data );
					
					var url = msg.data.data;
					
					//console.log( cid + ' | ' + mess );
					
					if ( cid && url )
					{
						chatObject.videoMessage( cid, url );
					}
				} );
				
				//if( inComingCall[1] )
				//{
				//	inComingCall[1] = false;
				//}
				
				//if( r[1] )
				//{
				//	openWindow( 'Chat', uid, 'chatwindow', function(){ openPrivChat( uid, r[1], 'window' ); } );
				//	openChat();
				//	playAudio( 'skype_dialing' );
				//}
			}
		}
		j.send ();
		
		return cBubb( e );
	},
	
	// Incomming video call
	receiveCall: function( cid, url, user, img )
	{
		if ( !cid || !url ) return;
		
		// Play audio on dailing
		//playAudio( 'skype_call', function(){ removeAudio(); }, 50, true );
		playAudio( 'call_in', function(){ removeAudio(); }, 50, true );
		//playAudio( 'skype_call' );
		
		DialogWindow( false, 'chat', 'voicechat_incoming', { 'cid' : cid, 'url' : url, 'user' : user, 'img' : img } );
		
		/*// Accept or Decline call
		if ( confirm( 'Accept Call?' ) )
		{
			removeAudio();
			
			popupCenter( url, cid, 600, 600 );
			
			chatObject.addPrivateChat( cid, user, img );
			
			//openWindow( 'Chat', uid, 'voicechat' );
			//call( r[3] );
			//chatObject.acceptCall();
			
			if( typeof resetMessageNotify == 'function' ) resetMessageNotify( cid, 'accepted' );
		}
		else
		{
			//chatObject.declineCall();
			
			removeAudio();
			
			if( typeof resetMessageNotify == 'function' ) resetMessageNotify( cid, 'declined' );
		}*/
	},
	
	// Accept video call
	acceptCall: function( cid, url, user, img )
	{
		if ( !cid || !url ) return;
		removeAudio();
		// TODO: Open window if useragent is desktop
		// TODO: Open window in iframe as a top or bottom layer
		//popupCenter( url, cid, 1000, 600 );
		chatObject.addPrivateChat( cid, user, img );
		if( typeof resetMessageNotify == 'function' ) resetMessageNotify( cid, 'accepted' );
		closeWindow();
	},
	
	// Decline video call
	declineCall: function( cid, url, user, img )
	{
		if ( !cid || !url ) return;
		
		removeAudio();
		
		if( typeof resetMessageNotify == 'function' ) resetMessageNotify( cid, 'declined' );
		
		closeWindow();
	},
	
	// TODO: Find a way to remove audio when accepted or declined message back to host
	
	// Sends a video message invite to a user
	videoMessage: function( userid, url )
	{
		if ( !userid || !url ) return;
		var o = this;
		
		// Send invite
		var j = new bajax ();
		j.openUrl ( '?component=chat&action=voicechat&fastlane=1', 'post', true );
		j.addVar ( 'u', userid );
		j.addVar ( 'm', url );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );	
			if ( r[0] == 'ok' )
			{
				// Play audio on dailing
				//playAudio( 'skype_dialing', function(){ removeAudio(); }, 50, true );
				playAudio( 'call_out', function(){ removeAudio(); }, 50, true );
				
				o.scrollDownMessages( userid );
			}
		}
		j.send ();
	},
	
	// Create an object of invitations where random privkey is encrypted with receivers pubkey and message is encrypted with random pubkey
	encryptMessage: function( msg, inv )
	{
		if ( !msg || !inv ) return false;
		
		var o = this;
		var host = false;
		var obj = new Object();
		var invited = inv.split( ',' );
		
		obj['receivers'] = new Object();
		
		if ( o.openChats.length && invited.length > 0 )
		{
			for ( var c = 0; c < o.openChats.length; c++ )
			{
				if ( o.openChats[c].userId == invited[0] )
				{
					host = o.openChats[c];
				}
			}
			
			if ( host )
			{
				var encryptionid = false;
				
				var privkey = false;
				var pubkey = false;
				var keys = false;
				
				var sender = false;
				
				if ( host.keys.encryptedKey && host.keys.plaintext && ( host.keys.encryptionKey == host.keys.encryptedKey ) )
				{
					// Get keys from memory
					keys = host.keys.plaintext;
					
					privkey = keys.split( '<!--split-->' )[0];
					pubkey = keys.split( '<!--split-->' )[1];
					
					if ( keys.split( '<!--split-->' )[2] )
					{
						encryptionid = keys.split( '<!--split-->' )[2];
					}
					
					// Encrypt the message private and public keys with the users public key
					sender = host.keys.encryptionKey;
				}
				else if ( host.keys.encryptionKey )
				{
					// Decrypt the message private and public keys from storage to be unlocked by user
					var keypairs = o.decryptCryptoKeys( host.keys.encryptionKey );
					
					if ( keypairs )
					{
						privkey = keypairs.privateKey;
						pubkey = keypairs.publicKey;
						
						if ( host.keys.encryptionId )
						{
							encryptionid = host.keys.encryptionId;
						}
						
						keys = ( privkey + '<!--split-->' + pubkey + ( encryptionid ? ( '<!--split-->' + encryptionid ) : '' ) );
						
						// Encrypt the message private and public keys with the users public key
						sender = host.keys.encryptionKey;
					}
					else
					{
						console.log( 'Something wrong with the keys ...' );
						console.log( getBrowserStorage( 'privatekey' ) );
						console.log( getBrowserStorage( 'publickey' ) );
						
						console.log( host.keys.encryptionKey );
					}
				}
				
				// If all else fail generate new keys
				if ( !privkey || !pubkey || !keys || !sender )
				{
					// Generate new private and public key pairs if we didn't find any in storage
					var keypairs = o.generateCryptoKeys();
					
					if ( keypairs )
					{
						privkey = keypairs.privateKey;
						pubkey = keypairs.publicKey;
						
						keys = ( privkey + '<!--split-->' + pubkey );
						
						// Encrypt the message private and public keys with the users public key
						sender = o.encryptData( privkey );
					}
				}
				
				if ( privkey && pubkey && keys && sender )
				{
					for ( var a = 0; a < o.openChats.length; a++ )
					{
						for ( var b = 0; b < invited.length; b++ )
						{
							if ( o.openChats[a].userId == invited[b] )
							{
								if ( o.openChats[a].keys.publicKey )
								{
									// Encrypt the message private and public keys with contacts public key
									var encrypted = o.encryptData( privkey, o.openChats[a].keys.publicKey );
									
									if ( encrypted )
									{
										obj['receivers'][invited[b]] = encrypted;
										o.openChats[a].keys.encryptedKey = sender;
										o.openChats[a].keys.plaintext = keys;
									}
								}
							}
						}
					}
					
					var encryptmsg = o.encryptData( msg, pubkey );
					
					if ( obj && sender && encryptmsg )
					{
						obj['receivers']['sender'] = sender;
						obj['message'] = encryptmsg;
						obj['encryptionid'] = ( encryptionid ? encryptionid : false );
						
						return obj;
					}
				}
			}
		}
		
		return false;
	},
	
	// Decrypt message based on encrypted key opened with your private key linked to encrypted message
	decryptMessage: function( data, encryptionkey, privkey )
	{
		if ( !privkey && getBrowserStorage( 'privatekey' ) )
		{
			privkey = getBrowserStorage( 'privatekey' );
		}
		
		if( !data || !encryptionkey || !privkey ) return false;
		
		var o = this;
		
		var keypairs = o.decryptCryptoKeys( encryptionkey, privkey )
		
		if( keypairs && keypairs.privateKey )
		{
			var msg = o.decryptData( data, keypairs.privateKey );
			
			if( msg && msg.plaintext )
			{
				return msg.plaintext;
			}
		}
		
		return false;
	},
	
	// Generate new random crypto keys
	generateCryptoKeys: function()
	{
		var o = this;
		
		fcrypt.generateKeys();
		
		var privkey = fcrypt.getPrivateKey();
		var pubkey  = fcrypt.getPublicKey();
		
		if( privkey && pubkey )
		{
			var keys = new Object();
			keys.privateKey = fcrypt.stripHeader( privkey );
			keys.publicKey = fcrypt.stripHeader( pubkey );
			
			return keys;
		}
		
		return false;
	},
	
	// Decrypt encrypted string with keys with your private key
	decryptCryptoKeys: function( data, privkey )
	{
		if ( !privkey && getBrowserStorage( 'privatekey' ) )
		{
			privkey = getBrowserStorage( 'privatekey' );
		}
		
		if( !data || !privkey ) return false;
		
		var o = this;
		
		data = fcrypt.stripHeader( data );
		
		var decrypted = o.decryptData( data, privkey );
		
		if( decrypted && decrypted.plaintext )
		{
			decrypted = decrypted.plaintext.split( '<!--split-->' );
			
			var privKeyObject = fcrypt.setPrivateKeyRSA( decrypted[0] );
			
			var PublicKey = fcrypt.getPublicKey( privKeyObject );
			
			if ( decrypted[0] && PublicKey )
			{
				var keys = new Object();
				keys.privateKey = fcrypt.stripHeader( decrypted[0] );
				keys.publicKey = fcrypt.stripHeader( PublicKey );
			}
			else
			{
				var keys = new Object();
				keys.privateKey = fcrypt.stripHeader( decrypted[0] );
				keys.publicKey = fcrypt.stripHeader( decrypted[1] );
			}
			
			return keys;
		}
		
		return false;
	},
	
	// Encrypt data with your publickey or the receivers public key
	encryptData: function( data, pubkey )
	{
		if ( !pubkey && getBrowserStorage( 'privatekey' ) )
		{
			var privKeyObject = fcrypt.setPrivateKeyRSA( getBrowserStorage( 'privatekey' ) );
			
			pubkey = fcrypt.getPublicKey( privKeyObject );
			
			pubkey = fcrypt.stripHeader( pubkey );
		}
		
		if( !data || !pubkey ) return '';
		
		var encrypted = fcrypt.encryptString( data, pubkey );
		
		if( encrypted && encrypted.cipher )
		{
			return fcrypt.stripHeader( encrypted.cipher );
		}
		
		return false;
	},
	
	// Decrypt data with your privatekey
	decryptData: function( data, privkey )
	{
		if ( !privkey && getBrowserStorage( 'privatekey' ) )
		{
			privkey = getBrowserStorage( 'privatekey' );
		}
		
		if( !data || !privkey ) return '';
		
		data = fcrypt.stripHeader( data );
		
		var decrypted = fcrypt.decryptString( data, privkey );
		
		if( decrypted.plaintext )
		{
			return decrypted;
		}
		
		return false;
	},
	
	// Filter contactlist users by userid
	filterContactsByID: function( userid )
	{
		var c = ge( 'Chat_list' );
		if( !c ) return;
		var li = c.getElementsByTagName( 'li' );
		if( li.length > 0 )
		{
			for( var a = 0; a < li.length; a++ )
			{
				if( li[a].id )
				{
					var uid = li[a].id.split( 'ChatContact_' );
					if( !userid || userid == '' )
					{
						li[a].style.display = '';
					}
					else if( uid[1] && userid && uid[1] == userid )
					{
						li[a].style.display = '';
					}
					else
					{
						li[a].style.display = 'none';
					}
				}
			}
		}
	},
	
	// Filter contactlist users by keyword against displayname
	filterContactlist: function( val )
	{
		//if( !val ) val = this.currentFilter;
		var c = ge( 'Chat_list' );
		var eles = c.getElementsByTagName( 'li' );
		var i;
		if( val )
		{
			i = val.toLowerCase()
		}
		for( var a = 0; a < eles.length; a++ )
		{
			var elsp = eles[a].getElementsByTagName( 'span' );
			if( elsp[1] )
			{
				var span = elsp[1];
				var sinner = span.innerHTML;
				if( sinner.length )
				{
					if( !val || val == '' )
					{
						eles[a].style.display = '';
					}
					else if( sinner.toLowerCase().split( i ).join ( '' ).length < sinner.length )
					{
						eles[a].style.display = '';
					}
					else
					{
						eles[a].style.display = 'none';
					}
				}
			}
		}
		this.currentFilter = val;
	},
	
	// Manage chat settings
	chatSettings: function( ele, component, button )
	{
		if( !ele || !component || !button || !ge( 'ChatSettings' ) ) return;
		
		if( ge( 'ChatSettings' ).className.indexOf( 'open' ) >= 0 )
		{
			//ele.className = '';
			ge( 'ChatSettings' ).className = '';
			ge( 'ChatSettings' ).getElementsByTagName( 'div' )[0].innerHTML = '';
		}
		else
		{
			var j = new bajax ();
			j.openUrl ( '?component=chat&function=settings', 'post', true );
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );
				if ( r[0] == 'ok' && r[1] )
				{
					//ele.className = 'active';
					ge( 'ChatSettings' ).getElementsByTagName( 'div' )[0].innerHTML = r[1];
					ge( 'ChatSettings' ).className = 'open ' + button;
				}
			}
			j.send ();
		}
	},
	
	// Save chat settings
	saveChatSettings: function( ele )
	{
		if( !ele || ele.value == '' ) return;
		var j = new bajax ();
		j.openUrl ( '?component=chat&action=settings', 'post', true );
		if( ele.name == 'mode' )
		{
			j.addVar ( 'mode', ele.value == '1' ? '1' : '0' );
		}
		if( ele.name == 'crypto' )
		{
			j.addVar ( 'crypto', ele.checked ? '1' : '0' );
			encryption = ( ele.checked ? true : false );
		}
		if( ele.name == 'sound' )
		{
			j.addVar ( 'sound', ele.checked ? '1' : '0' );
		}
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				//
			}
		}
		j.send ();
	}
};

// Initialize the chat object!
chatObject.init();
