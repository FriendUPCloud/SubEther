
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

/*
function webrtcNotSupportedAlert()
{
	alert( "Your browser doesn't support WebRTC. You need Chrome 25 to use this demo" );
}

function onReady()
{
	console.log( "onReady..." );
	login();
}

function login()
{
	var username = 'acezerox1337';
	var pass = '123456789';
	Plivo.conn.login( username, pass );
}

function onLogin()
{
	var url = 'http://sub-ether.org/subether/components/meeting/include/conference.xml';
	Plivo.conn.call( url );
	//callUI();
}

function onLoginFailed()
{
    alert( "Auth Failed" );
}

function callUI()
{
	//show outbound call UI
	//ringbacktone.pause();
	//dialpadHide();
	//$('#callcontainer').show();
	//$('#status_txt').text('Ready');
	//$('#make_call').text('Conference');
}

function voicechat()
{
	Plivo.onWebrtcNotSupported = webrtcNotSupportedAlert;
	Plivo.onReady = onReady;
	Plivo.onLogin = onLogin;
	Plivo.onLoginFailed = onLoginFailed;
	Plivo.init();
}


//Plivo.onWebrtcNotSupported = webrtcNotSupportedAlert;
//Plivo.onReady = onReady;
//Plivo.onLogin = onLogin;
//Plivo.onLoginFailed = onLoginFailed;
//Plivo.onCalling = onCalling;
//Plivo.onCallRemoteRinging = onCallRemoteRinging;
//Plivo.onCallAnswered = onCallAnswered;
//Plivo.onCallTerminated = onCallTerminated;
//console.log( 'Initializing Plivo SDK' );
//Plivo.init();

// Assign Global Listeners
if ( window.addEventListener )
{
	window.addEventListener ( 'onload', voicechat() );
}
else 
{
	window.addEventListener ( 'onload', voicechat() );
}*/

function loginUser()
{
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=meeting&function=voicechat', 'post', true );
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

function onMediaPermission()
{
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=meeting&function=voicechat', 'post', true );
	j.addVar ( 'call', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{			
			// Auto Join
			ge( 'status_txt' ).innerHTML = 'Calling..';
			var dest = r[1];
			Plivo.conn.call( dest );
		}
	}
	j.send ();
}
