<? /*******************************************************************************
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
*******************************************************************************/ ?>
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="https://s3.amazonaws.com/plivosdk/web/plivo.min.js"></script>
<script type="text/javascript">

	function webrtcNotSupportedAlert()
	{
		alert( 'Your browser doesnt support WebRTC. You need Chrome 25 to use this demo' );
	}

	function getURLParameter(name)
	{
		return decodeURI((RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]);
	}

	function callUI()
	{
		//show outbound call UI
		/*ringbacktone.pause();*/
		/*dialpadHide();*/
		ge( 'status_txt' ).innerHTML = 'Ready';
		
		/*// Auto Join
		ge( 'status_txt' ).innerHTML = 'Calling..';
		var r = 'acezerox1337140605204926@phone.plivo.com';
		Plivo.conn.call( r );*/
	}

	function callAnsweredUI()
	{
		/*ringbacktone.pause();*/
		/*ge( 'status_txt' ).innerHTML = 'Call Answered....';*/
		ge( 'status_txt' ).innerHTML = 'Online';
		/*dialpadShow();*/
	}


	function onReady()
	{
		console.log( 'onReady...' );
		loginUser();
	}

	/*function login()
	{
		Plivo.conn.login( 'acezerox1337140605204926', '123456789' );
	}*/

	/*function logout()
	{
		Plivo.conn.logout();
	}*/

	function onLogin()
	{
		callUI();
	}
	
	/*function onMediaPermission()
	{
		// Auto Join
		ge( 'status_txt' ).innerHTML = 'Calling..';
		var r = 'acezerox1337140605204926@phone.plivo.com';
		Plivo.conn.call( r );
	}*/
	
	function onLoginFailed()
	{
		ge( 'status_txt' ).innerHTML = 'Auth Failed';
	}

	function onCalling()
	{
		console.log( 'onCalling' );
		ge( 'status_txt' ).innerHTML = 'Connecting....';
	}

	function onCallRemoteRinging()
	{
		ge( 'status_txt' ).innerHTML = 'Ringing..';
	}

	function onCallAnswered()
	{
		console.log( 'onCallAnswered' );
		callAnsweredUI();
	}

	function onCallTerminated()
	{
		console.log( 'onCallTerminated' );
		callUI();
	}

	function call()
	{
		if ( ge( 'make_call' ).innerHTML == 'Conference' )
		{
			ge( 'status_txt' ).innerHTML = 'Calling..';
			//var r = getURLParameter( 'vr' );
			var r = 'acezerox1337140605204926@phone.plivo.com';
			Plivo.conn.call( r );
			ge( 'make_call' ).innerHTML = 'End';
		}
		else if( ge( 'make_call' ).innerHTML == 'End' )
		{
			ge( 'status_txt' ).innerHTML = 'Ending..';
			Plivo.conn.hangup();
			ge( 'make_call' ).innerHTML == 'Conference';
			ge( 'status_txt' ).innerHTML = 'Ready';
		}
	}

	function hangup()
	{
		ge( 'status_txt' ).innerHTML = 'Hanging up..';
		Plivo.conn.hangup();
		callUI();
	}

	function dtmf( digit )
	{
		Plivo.conn.send_dtmf( digit );
	}
	function dialpadShow()
	{
		ge( 'btn-container' ).style.display = 'inline';
	}

	function dialpadHide()
	{
		ge( 'btn-container' ).style.display = 'none';
	}

	function mute()
	{
		Plivo.conn.mute();
		ge( 'linkUnmute' ).style.display = 'inline';
		ge( 'linkMute' ).style.display = 'none';
	}

	function unmute()
	{
		Plivo.conn.unmute();
		ge( 'linkUnmute' ).style.display = 'none';
        ge( 'linkMute' ).style.display = 'inline';
	}

	function voicechat()
	{
		Plivo.onWebrtcNotSupported = webrtcNotSupportedAlert;
		Plivo.onReady = onReady;
		Plivo.onLogin = onLogin;
		Plivo.onLoginFailed = onLoginFailed;
		Plivo.onMediaPermission = onMediaPermission;
		Plivo.onCalling = onCalling;
		Plivo.onCallRemoteRinging = onCallRemoteRinging;
		Plivo.onCallAnswered = onCallAnswered;
		Plivo.onCallTerminated = onCallTerminated;
		console.log( 'Initializing Plivo SDK' );
		Plivo.init();
	}
	
	// Assign Global Listeners
	if ( window.addEventListener )
	{
		//window.addEventListener ( 'onload', voicechat() );
	}
	else 
	{
		//window.addEventListener ( 'onload', voicechat() );
	}
	
</script>

<div id="Meeting">
	<div class="Box">
		
		<!--<div><?= $this->Teamspeak ?></div>-->
		
		<div>
			<!--<a href="javascript:void(0);" id="make_call" onclick="call();">Conference</a><br>-->
			<!--<span>Status: </span><span id="status_txt">Loading....</span><br><br>-->
			<!--<div id="btn-container">
				<a href="javascript:void(0);" id="hangup_call" onclick="hangup();">Hangup</a> 
				<a href="javascript:void(0);" id="linkMute" onclick="mute();">Mute</a> 
				<a href="javascript:void(0);" id="linkUnmute" onclick="unmute();">Unmute</a>
			</div>-->
		</div>
		
		<table>
			<tr>
				<td class="leftCol">
				
					<div id="ParticipantBox">
						<h4>Participants</h4>
						<ul><?= $this->Participants ?></ul>
					</div>
					
					<div id="ChatBox">
						<h4>Chat</h4>
						<div><?= $this->ChatMessages ?></div>
					</div>
					
					<div id="PostBox" class="post">
						<input onkeyup="if( event.keyCode == 13 ) { saveMeetingMessage( this ) }" placeholder="Type your message here">
					</div>
					
				</td>
				<td class="rightCol">
				
					<div id="VideoBox">
						<h4>Toolbar</h4>
						<div class="inner"><?= $this->WebCams ?></div>
					</div>
					
				</td>
			</tr>
		</table>
	</div>
</div>

<!--<audio id="ringbacktone" loop="" src="http://s3.amazonaws.com/plivowebrtc/audio/ringtone/ringbacktone.wav"/>-->
<embed id="WebRtc4npapi" type="application/w4a" height="1px" width="1px">
