
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

function getPath ()
{
	var http = location.protocol + '//';
	var host = location.hostname;
	var path = location.pathname
	return http + host + path;
}

function baseUrl ()
{
	var bases = document.getElementsByTagName('base');
	
	if ( bases.length > 0 )
	{
		return bases[0].href;
	}
	
	return false;
}

function tooltips( e, a )
{
	if( !e ) return;
	f = e.getElementsByTagName( '*' );
	if( f.length > 0 )
	{
		for( i = 0; i < f.length; i++ )
		{
			if( f[i].className.indexOf( 'tooltips' ) >= 0 )
			{
				if( a == 'close' || f[i].className.indexOf( 'open' ) >= 0 )
				{
					f[i].className = f[i].className.split( ' open' ).join( '' );
				}
				else
				{
					f[i].className = f[i].className + ' open';
					return;
				}
			}
		}
	}
	s = e.parentNode.getElementsByTagName( '*' );
	if( s.length > 0 )
	{
		for( i = 0; i < s.length; i++ )
		{
			if( s[i].className.indexOf( 'tooltips' ) >= 0 )
			{
				if( a == 'close' || s[i].className.indexOf( 'open' ) >= 0 )
				{
					s[i].className = s[i].className.split( ' open' ).join( '' );
				}
				else
				{
					s[i].className = s[i].className + ' open';
					return;
				}
			}
		}
	}
}

function ultoggle( ele, lvl )
{
	if ( lvl )
	{
		for ( a = 0; a < lvl; a++ )
		{
			ele = ele.parentNode;
		}
	}
	
	if ( ele )
	{
		if ( ele.className.indexOf( 'open' ) >= 0 )
		{
			ele.className = ele.className.split( 'open' ).join( '' );
		}
		else
		{
			ele.className = ele.className.split( 'open' ).join( '' ) + 'open';
		}
	}
}

function toggleCustomSelect( _this, e )
{
	if( _this.className.indexOf( 'open' ) >= 0 )
	{
		_this.className = _this.className.split( ' open' ).join( '' );
	}
	else
	{
		_this.className = _this.className.split( ' open' ).join( '' ) + ' open';
	}
	cancelBubble(e);
}

function setOptionCustomSelect( _this, e )
{
	var ul = _this.parentNode.getElementsByTagName( 'li' );
	
	if( ul.length > 0 )
	{
		for( a = 0; a < ul.length; a++ )
		{
			if( ul[a].className.indexOf( 'selected' ) >= 0 )
			{
				ul[a].className = ul[a].className.split( ' selected' ).join( '' );
			}
		}
		
		if( _this.getAttribute( 'value' ) )
		{
			_this.parentNode.parentNode.setAttribute( 'value', _this.getAttribute( 'value' ) );
			_this.parentNode.parentNode.getElementsByTagName( 'input' )[0].value = _this.getAttribute( 'value' );
		}
		_this.className = _this.className.split( ' selected' ).join( '' ) + ' selected';
	}
}

function getBrowserStorage ( Name, Unique )
{
	if ( !Name ) return false;
	if ( !Unique )
	{
		Unique = document.location + '';
		Unique = Unique.replace ( 'http://', '' );
		Unique = Unique.replace ( 'https://', '' );
		Unique = Unique.split ( '/' );
		Unique = '.' + Unique[ 0 ];
		Unique = Unique.split ( ':' )[0];
		while ( Unique.substr ( 0, 1 ) == '.' )
			Unique = Unique.substr ( 1, Unique.length - 1 );
		if ( Unique == 'localhost' || Unique == '127.0.0.1' )
			Unique = '';
	}
	
	Unique = ( Unique ? Unique.toLowerCase() : false );
	Name = Name.toLowerCase();
	
	//console.log( 'window.oStorage: ', window.oStorage );
	//console.log( 'document.cookie: ', document.cookie );
	
	//console.log( 'localStorage ', localStorage );
	
	if( !supports_html5_storage() )
	{
		console.log( 'this browser doesn\t support localStorage' );
		
		// Supports Internet Explorer < 8 etc
		//init_localstorage_alternative( 1 );
		init_localstorage_alternative( 2 );
	}
	
	// Retrieve Data from storage
	if( typeof localStorage.getItem == 'function' )
	{
		var Data = localStorage.getItem( ( ( Unique ? Unique + '<!-!>' : '' ) + Name ) );
	}
	else
	{
		if( typeof localStorage[( ( Unique ? Unique + '<!-!>' : '' ) + Name )] !== 'undefined' )
		{
			var Data = localStorage[( ( Unique ? Unique + '<!-!>' : '' ) + Name )];
		}
		else
		{
			var Data = false;
		}
	}
	
	if ( Data )
	{
		return Data;
		//return unescape ( Data );
	}
	
	return false;
}

function setBrowserStorage ( Name, Data, Unique )
{
	if ( !Name ) return;
	if ( !Unique )
	{
		Unique = document.location + '';
		Unique = Unique.replace ( 'http://', '' );
		Unique = Unique.replace ( 'https://', '' );
		Unique = Unique.split ( '/' );
		Unique = '.' + Unique[ 0 ];
		Unique = Unique.split ( ':' )[0];
		while ( Unique.substr ( 0, 1 ) == '.' )
			Unique = Unique.substr ( 1, Unique.length - 1 );
		if ( Unique == 'localhost' || Unique == '127.0.0.1' )
			Unique = '';
	}
	
	Unique = ( Unique ? Unique.toLowerCase() : false );
	Name = Name.toLowerCase();
	
	if( !supports_html5_storage() )
	{
		console.log( 'this browser doesn\t support localStorage' );
		
		// Supports Internet Explorer < 8 etc
		//init_localstorage_alternative( 1 );
		init_localstorage_alternative( 2 );
	}
	
	// Put data into storage
	if( typeof localStorage.setItem == 'function' )
	{
		//localStorage.setItem( ( Unique + '<!-!>' + Name ), escape( Data ) );
		localStorage.setItem( ( ( Unique ? Unique + '<!-!>' : '' ) + Name ), Data );
	}
	else
	{
		localStorage[( ( Unique ? Unique + '<!-!>' : '' ) + Name )] = Data;
	}
}

var CookieCheck = false;

function supports_html5_storage()
{
	if( !CookieCheck && !document.cookie )
	{
		alert( 'Arena Enterprise (CMS) is using cookies to remember User authentication server side, to continue you have to enable cookies. This method will be removed soon.' );
		
		CookieCheck = true;
	}
	
	try
	{
		document.title = document.title.split( ' [unsecure]' ).join( '' ).split( ' [secure]' ).join( '' ) + ' [secure]';
		
		return 'localStorage' in window && window['localStorage'] !== null;
	}
	catch (e)
	{
		document.title = document.title.split( ' [unsecure]' ).join( '' ).split( ' [secure]' ).join( '' ) + ' [unsecure]';
		
		return false;
	}
}

function init_localstorage_alternative( alt )
{
	// TODO: This code probably needs more work, but for now it works...
	
	switch( alt )
	{
		case 1:
			
			try
			{
				return 'localStorage' in window && window['localStorage'] !== null;
			}
			catch (e)
			{
				window.localStorage =
				{
					getItem: function ( sKey )
					{
						if ( !sKey || !this.hasOwnProperty(sKey) )
						{
							return null;
						}
						
						return unescape(document.cookie.replace(new RegExp("(?:^|.*;\\s*)" + escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*((?:[^;](?!;))*[^;]?).*"), "$1"));
					},
					
					key: function ( nKeyId )
					{
						return unescape(document.cookie.replace(/\s*\=(?:.(?!;))*$/, "").split(/\s*\=(?:[^;](?!;))*[^;]?;\s*/)[nKeyId]);
					},
					
					setItem: function ( sKey, sValue )
					{
						if( !sKey )
						{
							return;
						}
						document.cookie = escape(sKey) + "=" + escape(sValue) + "; expires=Tue, 19 Jan 2038 03:14:07 GMT; path=/";
						this.length = document.cookie.match(/\=/g).length;
					},
					
					length: 0,
					
					removeItem: function ( sKey )
					{
						if ( !sKey || !this.hasOwnProperty(sKey) )
						{
							return;
						}
						document.cookie = escape(sKey) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
						this.length--;
					},
					
					hasOwnProperty: function ( sKey )
					{
						return (new RegExp("(?:^|;\\s*)" + escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
					}
				};
				
				window.localStorage.length = (document.cookie.match(/\=/g) || window.localStorage).length;
			}
			
			break;
			
		default:
			
			try
			{
				return 'localStorage' in window && window['localStorage'] !== null;
			}
			catch (e)
			{
				Object.defineProperty( window, "localStorage", new ( function ()
				{
					var aKeys = [], oStorage = {};
					
					Object.defineProperty( oStorage, "getItem",
					{
						value: function ( sKey )
						{
							var storage = {};
							
							if( window.oStorage )
							{
								if( window.oStorage.split(/\s*;\s*/)[0] && window.oStorage.split(/\s*;\s*/)[0].split("Storage=")[1] )
								{
									storage = JSON.parse( window.oStorage.split(/\s*;\s*/)[0].split("Storage=")[1] );
								}
							}
							else if( document.cookie )
							{
								if( document.cookie.split(/\s*;\s*/)[0] && document.cookie.split(/\s*;\s*/)[0].split("Storage=")[1] )
								{
									storage = JSON.parse( document.cookie.split(/\s*;\s*/)[0].split("Storage=")[1] );
								}
							}
							
							return sKey && storage[escape(sKey)] ? unescape( storage[escape(sKey)] ) : null;
							//return sKey ? this[sKey] : null;
						},
						writable: false,
						configurable: false,
						enumerable: false
					});
					
					Object.defineProperty( oStorage, "key",
					{
						value: function ( nKeyId )
						{
							return aKeys[nKeyId];
						},
						writable: false,
						configurable: false,
						enumerable: false
					});
					
					Object.defineProperty( oStorage, "setItem",
					{
						value: function ( sKey, sValue )
						{
							if( !sKey )
							{
								return;
							}
							//document.cookie = escape(sKey) + "=" + escape(sValue) + "; expires=Tue, 19 Jan 2038 03:14:07 GMT; path=/";
							//window.oStorage = escape(sKey) + "=" + escape(sValue) + "; expires=Tue, 19 Jan 2038 03:14:07 GMT; path=/";
							
							var storage = {};
							
							if( window.oStorage )
							{
								if( window.oStorage.split(/\s*;\s*/)[0] && window.oStorage.split(/\s*;\s*/)[0].split("Storage=")[1] )
								{
									storage = JSON.parse( window.oStorage.split(/\s*;\s*/)[0].split("Storage=")[1] );
								}
							}
							else if( document.cookie )
							{
								if( document.cookie.split(/\s*;\s*/)[0] && document.cookie.split(/\s*;\s*/)[0].split("Storage=")[1] )
								{
									storage = JSON.parse( document.cookie.split(/\s*;\s*/)[0].split("Storage=")[1] );
								}
							}
							
							storage[escape(sKey)] = escape(sValue);
							
							document.cookie = "Storage=" + JSON.stringify( storage ) + "; expires=Tue, 19 Jan 2038 03:14:07 GMT; path=/";
							window.oStorage = "Storage=" + JSON.stringify( storage ) + "; expires=Tue, 19 Jan 2038 03:14:07 GMT; path=/";
						},
						writable: false,
						configurable: false,
						enumerable: false
					});
					
					Object.defineProperty( oStorage, "length",
					{
						get: function ()
						{
							return aKeys.length;
						},
						configurable: false,
						enumerable: false
					});
					
					Object.defineProperty( oStorage, "removeItem",
					{
						value: function ( sKey )
						{
							if ( !sKey )
							{
								return;
							}
							//document.cookie = escape(sKey) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
							//window.oStorage = escape(sKey) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
							
							var storage = {};
							
							if( window.oStorage )
							{
								if( window.oStorage.split(/\s*;\s*/)[0] && window.oStorage.split(/\s*;\s*/)[0].split("Storage=")[1] )
								{
									storage = JSON.parse( window.oStorage.split(/\s*;\s*/)[0].split("Storage=")[1] );
								}
							}
							else if( document.cookie )
							{
								if( document.cookie.split(/\s*;\s*/)[0] && document.cookie.split(/\s*;\s*/)[0].split("Storage=")[1] )
								{
									storage = JSON.parse( document.cookie.split(/\s*;\s*/)[0].split("Storage=")[1] );
								}
							}
							
							storage[escape(sKey)] = '';
							
							document.cookie = "Storage=" + JSON.stringify( storage ) + "; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
							window.oStorage = "Storage=" + JSON.stringify( storage ) + "; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
						},
						writable: false,
						configurable: false,
						enumerable: false
					});
					
					this.get = function ()
					{
						var iThisIndx;
						
						for ( var sKey in oStorage )
						{
							iThisIndx = aKeys.indexOf(sKey);
							
							if ( iThisIndx === -1 )
							{
								oStorage.setItem( sKey, oStorage[sKey] );
							}
							else
							{
								aKeys.splice(iThisIndx, 1);
							}
							
							delete oStorage[sKey];
						}
						
						for ( aKeys; aKeys.length > 0; aKeys.splice(0, 1) )
						{
							oStorage.removeItem( aKeys[0] );
						}
						
						for ( var aCouple, iKey, nIdx = 0, aCouples = document.cookie.split(/\s*;\s*/); nIdx < aCouples.length; nIdx++ )
						{
							aCouple = aCouples[nIdx].split(/\s*=\s*/);
							
							if ( aCouple.length > 1 )
							{
								oStorage[iKey = unescape(aCouple[0])] = unescape(aCouple[1]);
								aKeys.push(iKey);
							}
						}
						
						return oStorage;
					};
					
					this.configurable = false;
					this.enumerable = true;
					
				})());
			}
			
			break;
	}
}

function renderTooltips( content )
{
	if( !content ) return false;
	
	return str = '<div class="tooltips"><div class="bottomarrow"></div>' +
	'<div class="inner">' + content + '</div></div>';
}

function getPathVar( ele )
{
	var bref = document.location.href;
	
	if( bref.split( '?' )[1] )
	{
		var href = bref.split( '?' )[1].split( '#' )[0];
		var vars = href.split( '&' );
		
		if( vars.length > 0 )
		{
			for( a = 0; a < vars.length; a++ )
			{
				if( vars[a].split( '=' )[0] && vars[a].split( '=' )[1] && vars[a].split( '=' )[0] == ele )
				{
					return vars[a].split( '=' )[1];
				}
			}
		}
		else if( href.split( '=' )[0] && href.split( '=' )[1] && href.split( '=' )[0] == ele )
		{
			return href.split( '=' )[1];
		}
	}
	
	return false;
}

function renderSmileys( str )
{
	var smileys = {
		":)":"smile", ":(":"frown", ":p":"tongue", ":D":"grin", ":o":"gasp", ";)":"wink",
		":v":"pacman", ">:(":"grumpy", ":/":"unsure", ":'(":"cry", "^_^":"kiki", "8-)":"glasses",
		"B|":"sunglasses", "<3":"heart", "3:)":"devil", "O:)":"angel", "-_-":"squint",
		"o.O":"confused", ">:o":"upset", ":3":"colonthree", "(y)":"like"
	};
	
	// Fix http
	if( strstr( str, 'https://' ) )
	{
		str = str.replace( 'https://', '<!--replacehttps-->' );
	}
	else
	{
		str = str.replace( 'http://', '<!--replacehttp-->' );
	}
	
	// TODO: Fix object merging in javascript, commented out until this is fixed
	// Add support for smilies with a nose
	/*var WithLines = new Object();
	var key = false;
	for ( var k in smileys )
	{
		if ( !strstr( k, '-' ) && k.length == 2 && k.charAt(0) == ':' )
		{
			key = k.charAt(0) + '-' + k.charAt(1);
			WithLines[key] = smileys[k];
		}
	}
	if ( key ) smileys = array_merge ( smileys, WithLines );*/
	
	// Make smilies
	for ( var k in smileys )
	{
		if( str.length < 4 )
		{
			str = str.replace( k, ( '<i class="emoticon emoticon_' + smileys[k] + '"></i>' ) );
		}
		else
		{
			str = str.replace( ( ' ' + k ), ( ' <i class="emoticon emoticon_' + smileys[k] + '"></i>' ) );
		}
	}
	
	// Fix back http
	if( strstr( str, '<!--replacehttps-->' ) )
	{
		str = str.replace( '<!--replacehttps-->', 'https://' );
	}
	else
	{
		str = str.replace( '<!--replacehttp-->', 'http://' );
	}
	
	return str;
}

function randomPassword ( n )
{
	n = ( n ? n : 10 );
	var text = '';
    var possible = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';

    for ( var i = 0; i < n; i++ )
	{
        text += possible.charAt( Math.floor( Math.random() * possible.length ) );
	}

    return text;
}

function makeLinks( str )
{
	// Convert x:// a href linkes into just x:// text
	str = str.replace(/\<a[^>]*?\>[a-zA-Z]+\:\/\/(.*?)\<\/a\>/i, "$1");
	// Convert www. text into http:// text
    str = str.replace(/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i, "$1http://$2");
	// Convert some x:// text that doesn't start with >http:// (ex) into <a href> links
	str = str.replace(/[^>=\"a-z]{0,1}([a-zA-Z]*\:\/\/[\w-?&;\!\:\%\+#~=\.\/\@\(\)]+[\w-?&;\!\:\%\+#~=\.\/\@\(\)])/i, " <a target=\"_blank\" href=\"$1\">$1</a>");
	// Convert emails into links
    str = str.replace(/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i, " <a href=\"mailto:$1\">$1</a>");
    return str;
}

function inArray ( needle, haystack ) 
{
    var length = haystack.length;
    for( var i = 0; i < length; i++ ) 
	{
        if( haystack[i] == needle ) return true;
    }
    return false;
}

function objectKeys( obj )
{
	if( !obj ) return false;
	
	var keys = new Array();
	
	for( var key in obj )
	{
		if( obj.hasOwnProperty( key ) )
		{
			keys.push( key );
		}
	}
	
	return keys;
}

function strstr ( haystack, needle )
{
	if( haystack.toLowerCase().indexOf( needle.toLowerCase() ) >= 0 )
	{
		return true;
	}
	return false;
}

function trim( str ) 
{
	return str.replace(/^\s+|\s+$/gm, '');
}

function stripSlashes( str )
{
    return str.replace(/\\(.)/mg, "$1");
}

function cancelBubble ( ev )
{
	if ( !ev ) ev = window.event;
	if ( !ev ) return false;
	if ( ev.cancelBubble ) ev.cancelBubble ();
	if ( ev.stopPropagation ) ev.stopPropagation ();
	return false;
}

function selectText( id )
{
	if( document.selection )
	{
		var range = document.body.createTextRange();
		range.moveToElementText( document.getElementById( id ) );
		range.select();
	}
	else if( window.getSelection )
	{
		var range = document.createRange();
		range.selectNode( document.getElementById( id ) );
		window.getSelection().addRange( range );
	}
}

var JaxActivity = new Object ();

// func = check key
// run  == true,  try to set to true  - if already true - return true, 
//                otherwise, return false and set activity
// run  == false, set to false - return false if existing, true if not
function running( func, run )
{
	if( !func ) return false;
	
	// Run is true
	if( run && ( !JaxActivity[ func ] || JaxActivity[ func ] == false ) )
	{
		JaxActivity[ func ] = ( new Date () ).getTime(); //jsdate( 'Hisu' );
		return false;
	}
	
	// Run is false AND we have existing activity
	if( !run && JaxActivity[ func ] )
	{
		var runtime = ( new Date () ).getTime() - JaxActivity[ func ];
		
		/*if( runtime > 1000 )
		{
			runtime = ( runtime / 1000 ) + 's';
		}
		else
		{*/
			runtime = runtime + 'ms';
		/*}*/
		
		//console.log( func + ' : ' + runtime );
		
		JaxActivity[ func ] = false;
		return false;
	}
	
	return true;
}

// Set running status
function setRunning( func, val )
{
	// Set to running
	if( val == true )
	{
		// Log start
		JaxActivity[func] = ( new Date () ).getTime();
	}
	// Set to stopped
	else
	{
		// Log stop
		if( typeof( JaxActivity[func] ) != 'undefined' && JaxActivity[func] )
		{
			//var runtime = ( new Date () ).getTime() - JaxActivity[func];
		}
		delete JaxActivity[func];
	}
	return true;
}

// Get running status
function getRunning( func )
{
	if( typeof( JaxActivity[func] ) == 'undefined' )
	{
		return false;
	}
	if( JaxActivity[func] )
	{
		return true;
	}
	return false;
}

// arr = array to sort, fld = field in object to sort by, pri = array of priority list
function sortByPriority( arr, fld, pri )
{
	var out = new Array();
	
	if( pri.length > 0 )
	{
		// Assign the matched objects to the prio array
		for( var a = 0; a < pri.length; a++ )
		{
			var val = pri[a];
			pri[a] = new Array();
			if( arr.length > 0 )
			{
				for( var b = 0; b < arr.length; b++ )
				{
					if( arr[b][fld] == val )
					{
						pri[a].push( arr[b] );
					}
					if( b > 1000 )
					{
						//console.log( 'Assign the matched objects to the prio array [b]' );
						return false;
					}
				}
			}
			if( a > 1000 )
			{
				//console.log( 'Assign the matched objects to the prio array [a]' );
				return false;
			}
		}
		// Loop through the prio array and put everyting into an output array
		for( var a = 0; a < pri.length; a++ )
		{
			if( pri[a].length > 0 )
			{
				for( var b = 0; b < pri[a].length; b++ )
				{
					if( pri[a][b] )
					{
						out.push( pri[a][b] );
					}
					if( b > 1000 )
					{
						//console.log( 'Loop through the prio array and put everyting into an output array [b]' );
						return false;
					}
				}
			}
			if( a > 1000 )
			{
				//console.log( 'Loop through the prio array and put everyting into an output array [a]' );
				return false;
			}
		}
		// Output the result
		return out;
	}
	
	return false;
}

function strShow( ele )
{
	if( !ele || !ele.parentNode.parentNode ) return;
	
	var span = ele.parentNode.parentNode.getElementsByTagName( 'span' );
	
	if( span.length > 0 )
	{
		for( a = 0; a < span.length; a++ )
		{
			if( span[a].className == 'str show' )
			{
				span[a].className = 'str hide';
				ele.innerHTML = 'See More';
			}
			else if( span[a].className == 'str hide' )
			{
				span[a].className = 'str show';
				ele.innerHTML = 'Hide';
			}
		}
	}
}

function asciiEncode( data )
{
	var str = "";
	for(var i = 0; i < data.length; i+=2)
	{
		var rleValue = data[i];
		var rleSequenceLength = data[i+1];
		
		if(rleValue < 3) // Sequences are captured for blanks, walls or grains
			var asciiCode = 36 + (rleValue * 30) + (rleSequenceLength - 1);
		else // remaining cell types (3 and 4) are treated as one-offs
			var asciiCode = 33 + (rleValue - 3);
			
		// Special case for eliminated semi-colons
		if(asciiCode == 59)
			asciiCode = 35;
			
		str += String.fromCharCode(asciiCode);
	}
	return str;
}

function asciiDecode( str )
{
	var result = new Array;
	for(var i = 0; i < str.length; ++i)
	{
		var asciiCode = str.charCodeAt(i);
		
		// Special case handling for semi-colons.
		if(asciiCode == 35)
			asciiCode = 59;
		
		if(asciiCode > 35)
		{
			// It is a blank, wall or grain cell.
			var rleValue = Math.floor((asciiCode - 36) / 30);
			var rleSequenceLength = ((asciiCode - 36) % 30) + 1;
		}
		else
		{
			// It is a 'one-off' cell such as egg, ladder, lift etc.
			var rleValue = (asciiCode - 33) + 3;
			var rleSequenceLength = 1;
		}
		result[i*2] = rleValue;	
		result[i*2 + 1] = rleSequenceLength;
	}
	return result;
}


function openDropDownWindow ( id )
{
	if( !id ) return; 
	else var el = document.getElementById( id );
	
	if( el.className == 'open' )
	{
		el.className = '';
		
		document.body.className = document.body.className.split(' dropdownwindow').join('');
	}
	else if ( el.className == '' )
	{
		el.className = 'open';
		
		document.body.className = document.body.className.split(' dropdownwindow').join('') + ' dropdownwindow';
	}
}

function dropDownWindow ( id, comp, targ, ele, close )
{
	if( !id || !comp || !targ || !ele ) return; 
	
	if( ge( 'DropDownWindow' ) && !close && ge( id ).className != 'open ' + targ )
	{
		closeNotificationBox();
	}
	
	if( ge( id ).className == 'open ' + targ )
	{
		ge( id ).className = '';
		ele.className = '';
		
		document.body.className = document.body.className.split(' dropdownwindow').join('');
	}
	else if( ge( id ).className == '' )
	{
		if( ge( 'DropDownWindow' ) )
		{
			ge( 'DropDownWindow' ).parentNode.className = '';
			ge( 'DropDownWindow' ).parentNode.removeChild( ge( 'DropDownWindow' ) );
		}
		var j = new bajax ();
		j.openUrl ( baseUrl() + '?global=true&function=dropdownwindow', 'post', true );
		j.addVar ( 'component', comp );
		j.addVar ( 'target', targ );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );	
			if( r[0] == 'ok' && !ge( 'DropDownWindow' ) )
			{
				document.body.className = document.body.className.split(' dropdownwindow').join('') + ' dropdownwindow';
				
				var d = document.createElement ( 'div' );
				d.id = 'DropDownWindow';
				d.innerHTML = r[1];
				ge( id ).appendChild( d );
				ge( id ).className = 'open ' + targ;
				ele.className = 'active';
				if( r[2] ) notifications( r[2], targ );
			}
		}
		j.send ();
	}
}

function initTabs( id )
{
	if( !id || !ge( id ) ) return;
	
	tabs = new Array();
	pages = new Array();
	
	var t = 0;
	var p = 0;
	
	var els = ge( id ).getElementsByTagName( '*' );
	if( !els.length ) return;
	for( a = 0; a < els.length; a++ )
	{
		if( els[a].className != 'tabs' && els[a].className.indexOf( 'tab' ) >= 0 )
		{
			t++;
			
			els[a].onclick = function()
			{
				els[a].className = els[a].className.split( ' current' ).join( '' ) + ' current';
			}
		}
		if( els[a].className != 'pages' && els[a].className.indexOf( 'page' ) >= 0 )
		{
			p++;
		}
	}
}

function testTabSystem ( element, vartype, key )
{
	var ele, varID;
	if ( !element ) element = false;
	if ( typeof ( element ) == 'object' ) ele = element;
	else ele = document.getElementById ( element );
	if ( !ele ) return;
	else if ( ele.initialized ) return;
	else ele.initialized = true;
	varID = ele.id;
	if ( !vartype ) vartype = 'normal';
	if ( !key ) key = varID;
	var Match = 'tab';
	if ( vartype == 'subtabs' ) Match = 'subTab';
	var tabs = ele.getElementsByTagName ( 'div' );
	var activetab = false;
	var lasttab = false;
	for ( var n = 0; n < tabs.length; n++ )
	{
		if ( hasClass ( tabs[ n ], Match ) )
		{
			if ( tabs[ n ].id == getCookie ( key + 'activeTab' ) || !getCookie ( key + 'activeTab' ) )
			{
				tabs[ n ].className = Match + 'Active';
				activatePage ( tabs[ n ].id.replace ( Match, 'page' ), tabs[ n ].parentNode );
				setCookie ( key + 'activeTab', tabs[ n ].id );
				activetab = tabs[n];
			}
			tabs[ n ].onclick = function ( )
			{
				var pid = this.id.replace ( Match, 'page' );
				activateTab ( this, this.parentNode, vartype );
				setCookie ( key + 'activeTab', this.id );
			}
			lasttab = tabs[ n ];
		}
	}
	if ( !activetab && lasttab )
	{
		lasttab.className = Match + 'Active';
		activatePage ( lasttab.id.replace ( Match, 'page' ), lasttab.parentNode );
		setCookie ( key + 'activeTab', lasttab.id );
	}
}

function testactivateTab ( element, container, vartype )
{
var ele;
var Match = "tab";
if ( vartype == "subtabs" )
Match = "subTab";
if ( typeof ( element ) == "object" ) ele = element;
else ele = document.getElementById ( element );
if ( typeof ( container ) != "object" )
{
if ( element && typeof ( element ) == "object" )
container = element.parentNode;
else if ( ele && typeof ( ele ) == "object" )
container = ele.parentNode;
else
container = document.getElementById ( container );
}
if ( !ele ) return;
var tabs = container.getElementsByTagName ( "DIV" );
ele.className = Match + "Active";
for ( var n = 0; n < tabs.length; n++ )
{
if ( tabs[ n ].parentNode != container ) continue;
if ( tabs[ n ].id != ele.id && tabs[ n ].className.substr ( 0, Match.length ) == Match )
tabs[ n ].className = Match;
}
activatePage ( getDivById ( ele.id.replace ( Match, 'page' ), container ), container );
}
/**
* This activates a page, used bu activateTab()
**/
function testactivatePage ( element, container )
{
var ele;
if ( typeof ( container ) != "object" )
{
if ( typeof ( element ) == "object" )
container = element.parentNode;
else
container = document.getElementById ( container );
}
if ( !container ) return;
if ( typeof ( element ) == "object" ) ele = element;
else ele = getDivById ( element, container );
if ( !ele ) return false;
ele.className = "pageActive";
ele.style.visibility = 'visible';
if ( isIE )
{
ele.style.position = 'static';
}
else
{
ele.style.position = 'relative';
ele.style.display = '';
}
var pages = container.getElementsByTagName ( "div" )
for ( var n = 0; n < pages.length; n++ )
{
if ( pages[ n ].parentNode != container ) continue;
if ( pages[ n ].id != ele.id && pages[ n ].className.substr ( 0, 4 ) == "page" )
{
pages[ n ].className = "page";
if ( isIE )
{
pages[ n ].style.visibility = 'hidden';
pages[ n ].style.position = 'absolute';
}
else pages[ n ].style.display = 'none';
}
}
} 
