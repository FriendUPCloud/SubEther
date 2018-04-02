
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

function ShowCryptoWindow ( channel )
{
	if ( typeof ( window.localStorage ) == 'undefined' )
	{
		alert ( 'Du har ikke støtte for kryptert chat.' );
		return;
	}
	var cw = window.open ( '', '', 'width=640, height=480, topbar=no' );
	cw.document.write ( '<html>\
	<head>\
		<title>\
			Crypto settings\
		</title>\
		<base href="http://sub-ether.org"/>\
		<style> body { padding: 10px !important; margin: 0; } </style>\
		<link rel="stylesheet" href="template-css/main.css"/>\
		<script src="subether/javascript/crypto.js"></script>\
	</head>\
	<body>\
	<div id="">\
		<h2>Skriv inn din krypto nøkkel (' + channel + '):</h2>\
		<textarea style="width: 100%; height: 300px; box-sizing: border-box" onkeyup="SetCryptoKey(this.value,\'' + channel + '\')">' + 
			window.localStorage.getItem ( 'crypto_' + channel ) + '</textarea>\
		<hr/>\
		<button type="button" onclick="GenerateCrypto(this,\'' + channel + '\')">Generér</button>\
	</div>\
	</body>\
</html>' );
}

function SetCryptoKey ( val, channel )
{
	if ( typeof ( window.localStorage ) == 'undefined' )
	{
		return;
	}
	if ( !val.length ) val = null;
	window.localStorage.setItem ( 'crypto_' + channel, val );
}

function GenerateCrypto ( i, channel )
{
	var ta = i.parentNode.getElementsByTagName ( 'textarea' )[0];
	
	var string = 'skldfuyt92784ngqpowiu+89jghq3*&HWD%)UEGNuq34t4wsg5%H&"M/I%)PMI¤W)!G#&%';
	var t = '';
	for ( var a = 0; a < 255; a++ )
	{
		t += string.substr(Math.random()*string.length,1);
	}
	ta.value = t;
	window.localStorage.setItem ( 'crypto_' + channel, t );
}

function Decrypt ( string, channel )
{
	var mes = string;
	if ( typeof ( window.localStorage ) != 'undefined' && typeof ( ShowCryptoWindow ) != 'undefined' )
	{
		var o = window.localStorage.getItem ( 'crypto_' + channel );
		if ( o && o != 'null' && o.length )
		{
			mes = '';
			for ( var a = 0; a < string.length; a++ )
			{
				// Reverse Encrypt :)
				mes += String.fromCharCode ( string.charCodeAt ( a ) - o.charCodeAt ( a % o.length ) );
			}
		}
	}
	return mes;
}

function Encrypt ( m, channel )
{
	var mes = m;
	if ( typeof ( window.localStorage ) != 'undefined' && typeof ( ShowCryptoWindow ) != 'undefined' )
	{
		var o = window.localStorage.getItem ( 'crypto_' + channel );
		if ( o && o != 'null' && o.length )
		{
			mes = '';
			for ( var a = 0; a < m.length; a++ )
			{
				// Do a nice encrypt
				var oa = a % o.length;
				mes += String.fromCharCode ( ( m.charCodeAt ( a ) + o.charCodeAt ( oa ) ) );
			}
		}
	}
	return mes;
}

