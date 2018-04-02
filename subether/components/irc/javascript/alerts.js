
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

function RunAlert( file )
{
	if( !file ) return;
	
	var root = 'upload/'; //'subether/components/irc/media/';
	
	var mp3 = file + '.mp3';
	var ogg = file + '.ogg';
	var wav = file + '.wav';
	
	var loopsong = 'no';
	var autostarts = 'yes';
	
	if( loopsong == 'yes' )
	{
		var looping5 = 'loop';
		var loopingE = 'true';
	}
	else
	{
		var looping5 = '';
		var loopingE = 'false';
	}
	
	if( autostarts == 'yes' )
	{
		var h5auto = 'autoplay';
		var h4auto = '1';
	}
	else
	{
		var h5auto = '';
		var h4auto = '0';
	}
	
	if( ge( 'AudioAlert' ) )
	{
		ge( 'AudioAlert' ).parentNode.removeChild( ge( 'AudioAlert' ) ); 
	}
	
	var div = document.createElement( 'div' );
	div.id = 'AudioAlert';
	div.style.visibility = 'hidden';
	div.style.position = 'absolute';
	div.style.top = '-999px';
	div.style.left = '-999px';
	div.innerHTML = '<audio '+h5auto+' controls '+looping5+'>' +
					'<source src="'+root+mp3+'" type="audio/mpeg">' +
					'<source src="'+root+ogg+'" type="audio/ogg">' +
					'<source src="'+root+wav+'" type="audio/wav">' +
					'<object classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" type="application/x-mplayer2">' +
					'<param name="filename" value="'+root+mp3+'">' +
					'<param name="autostart" value="'+h4auto+'">' +
					'<param name="loop" value="'+loopingE+'">' +
					'</object></audio>';
	document.body.appendChild( div );
	
	IsAlerted( file );
}

function IsAlerted( file )
{
	if( !file ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=irc&action=alerted', 'post', true );
	j.addVar ( 'command', file );
	j.send ();
}
