
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

// Plivo Config
var pConfig = new Object ();
pConfig.perm_on_click = true;
pConfig.debug = true;

var initConf = false;

function webrtcNotSupportedAlert()
{
	//alert( 'Your browser doesnt support WebRTC. You need Chrome 25 to use this demo' );
	console.log( 'Your browser doesnt support WebRTC. You need Chrome 25 to use this demo' );
}

function onReady()
{
	console.log( 'onReady...' );
	loginUser();
}

function loginUser()
{
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&function=voicechat', 'post', true );
	j.addVar ( 'login', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] && r[2] )
		{
			// Login
			var u = r[1];
			var p = r[2];
			Plivo.conn.login( u, p );
		}
		else
		{
			// Login Failed
			onLoginFailed();
		}
	}
	j.send ();
}

function callUser_old( uid, e )
{
	if( !uid ) return;

	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&action=voicechat', 'post', true );
	j.addVar ( 'call', uid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			
			if( inComingCall[1] )
			{
				inComingCall[1] = false;
			}
			
			if( r[1] )
			{
				openWindow( 'Chat', uid, 'chatwindow', function(){ openPrivChat( uid, r[1], 'window' ); } );
				openChat();
				playAudio( 'skype_dialing' );
			}
		}
	}
	j.send ();
	
	if( e )
	{
		return cancelBubble( e );
	}
}

function callUser( cid, user, img, e )
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
			console.log( r[1] );
			
			popupCenter( r[1], cid, 600, 600 );
			
			chatObject.addPrivateChat( cid, user, img );
			
			// Receiving some messages..
			addMessageHandler( 'default', function( msg )
			{
				//console.log( msg );
				
				console.log( msg.data.data );
				
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
	
	return cancelBubble( e );
}

var popupWindow;

function popupCenter ( url, title, w, h )
{
	var left = ( screen.width/2 ) - ( w/2 );
	var top = ( screen.height/2 ) - ( h/2 );
	popupWindow = window.open( url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left );
} 

function popupClose()
{
	if ( popupWindow )
	{
		popupWindow.close();
	}
}

function declineCall( uid )
{
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&action=voicechat', 'post', true );
	j.addVar ( 'decline', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( uid && inComingCall[uid] )
			{
				inComingCall[uid] = false;
			}
			
			hangup();
		}
	}
	j.send ();
}

function acceptCall()
{
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&action=voicechat', 'post', true );
	j.addVar ( 'accept', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			//removeAudio();
		}
	}
	j.send ();
}

function joinConference( uid )
{
	//if( initConf ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&function=voicechat', 'post', true );
	j.addVar ( 'call', uid ? uid : true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{			
			console.log( 'Joining..' );
			var dest = r[1];
			Plivo.conn.call( dest );
			
			if( uid )
			{
				acceptCall();
			}
			
			removeAudio();
		}
	}
	j.send ();
}

function onLogin()
{
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&action=voicechat', 'post', true );
	j.addVar ( 'login', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			callUI();
		}
	}
	j.send ();
}

function callUI()
{
	//show outbound call UI
	/*ringbacktone.pause();*/
	/*dialpadHide();*/
	//ge( 'status_txt' ).innerHTML = 'Ready';
	console.log( 'Ready' );
	
	/*// Auto Join
	ge( 'status_txt' ).innerHTML = 'Calling..';
	var r = 'acezerox1337140605204926@phone.plivo.com';
	Plivo.conn.call( r );*/
}

function onLoginFailed()
{
	//ge( 'status_txt' ).innerHTML = 'Auth Failed';
	//alert( 'Auth Failed' );
	console.log( 'Auth Failed' );
}

function call( dest )
{
	if( !dest ) return;
	
	/*if ( ge( 'make_call' ).innerHTML == 'Conference' )
	{*/
		Plivo.conn.call( dest );
	/*}*/
	/*else if( ge( 'make_call' ).innerHTML == 'End' )
	{
		Plivo.conn.hangup();
	}*/
}

function hangup()
{
	//ge( 'status_txt' ).innerHTML = 'Hanging up..';
	console.log( 'Hanging up..' );
	Plivo.conn.hangup();
	//callUI();
	removeAudio();
	loginUser();
}

function onMediaPermission()
{
	//removeAudio();
	/*var j = new bajax ();
	j.openUrl ( getPath() + '?component=chat&function=voicechat', 'post', true );
	j.addVar ( 'call', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{			
			// Auto Join
			//ge( 'status_txt' ).innerHTML = 'Calling..';
			console.log( 'Calling..' );
			var dest = r[1];
			//Plivo.conn.call( dest );
		}
	}
	j.send ();*/
}

function inComingCall( uid )
{
	// Ringtone
	playAudio( 'skype_call' );
	// Accept or Decline call
	if( confirm( 'Accept Call?' ) )
	{
		openWindow( 'Chat', uid, 'voicechat' );
		call( r[3] );
		acceptCall();
	}
	else
	{
		declineCall();
	}
}

function onCalling()
{
	console.log( 'onCalling' );
	//ge( 'status_txt' ).innerHTML = 'Connecting....';
	console.log( 'Connecting....' );
}

function onCallRemoteRinging()
{
	//ge( 'status_txt' ).innerHTML = 'Ringing..';
	console.log( 'Ringing..' );
}

function onCallAnswered()
{
	console.log( 'onCallAnswered' );
	callAnsweredUI();
}

function callAnsweredUI()
{
	/*ringbacktone.pause();*/
	/*ge( 'status_txt' ).innerHTML = 'Call Answered....';*/
	//ge( 'status_txt' ).innerHTML = 'Online';
	console.log( 'Online' );
	/*dialpadShow();*/
}

function onCallTerminated()
{
	console.log( 'onCallTerminated' );
	callUI();
}

function onIncomingCall( account_name, extraHeaders )
{
	console.log("onIncomingCall:"+account_name);
	console.log("extraHeaders=");
	for (var key in extraHeaders) {
		console.log("key="+key+".val="+extraHeaders[key]);
	}
	IncomingCallUI();
}

function answer()
{
	console.log("answering")
	//$('#status_txt').text('Answering....');
	Plivo.conn.answer();
	callAnsweredUI()
}

function reject()
{
	callUI();
	Plivo.conn.reject();
}

function IncomingCallUI()
{
	//show incoming call UI
	console.log("Incoming Call");
	if( confirm( 'Accept Call?' ) )
	{
		answer();
	}
	else
	{
		reject();
	}
}

function onIncomingCallCancelled()
{
	callUI();
}

function onCallFailed( cause )
{
	console.log("onCallFailed:"+cause);
	callUI();
	//$('#status_txt').text("Call Failed:"+cause);
}

function voicechat()
{
	/*Plivo.onWebrtcNotSupported = webrtcNotSupportedAlert;
	Plivo.onReady = onReady;
	Plivo.onLogin = onLogin;
	Plivo.onLoginFailed = onLoginFailed;
	//Plivo.onMediaPermission = onMediaPermission;
	Plivo.onCalling = onCalling;
	Plivo.onCallRemoteRinging = onCallRemoteRinging;
	Plivo.onCallAnswered = onCallAnswered;
	Plivo.onCallTerminated = onCallTerminated;
	Plivo.onIncomingCall = onIncomingCall;
	console.log( 'Initializing Plivo SDK' );
	Plivo.setDebug( true );
	Plivo.init( pConfig );*/
	
	Plivo.onWebrtcNotSupported = webrtcNotSupportedAlert;
	Plivo.onReady = onReady;
	Plivo.onLogin = onLogin;
	Plivo.onLoginFailed = onLoginFailed;
	//Plivo.onLogout = onLogout;
	Plivo.onCalling = onCalling;
	Plivo.onCallRemoteRinging = onCallRemoteRinging;
	Plivo.onCallAnswered = onCallAnswered;
	Plivo.onCallTerminated = onCallTerminated;
	Plivo.onCallFailed = onCallFailed;
	Plivo.onMediaPermission = onMediaPermission;
	Plivo.onIncomingCall = onIncomingCall;
	Plivo.onIncomingCallCancelled = onIncomingCallCancelled;
	//Plivo.setDebug( true ); 
	Plivo.init( pConfig );
}
/*
// Assign Global Listeners
if ( window.addEventListener )
{
	window.addEventListener ( 'onload', voicechat() );
}
else 
{
	window.addEventListener ( 'onload', voicechat() );
}*/
