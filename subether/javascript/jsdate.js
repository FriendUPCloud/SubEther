
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
Date.prototype.getWeek = function()
{
	var onejan = new Date( this.getFullYear(), 0, 1 );
	return Math.ceil( ( ( ( this - onejan ) / 86400000 ) + onejan.getDay()+1 ) / 7 );
}
*/
function strtotime( str )
{
	if( !str ) return false;
	
	if( str.split( '-' )[1] )
	{
		str = str.split( ' ' );
		
		// Convert 'Y-m-d H:i:s' to 'D, d M Y H:i:s'
		var Y = str[0].split( '-' )[0];
		var m = str[0].split( '-' )[1];
		var d = str[0].split( '-' )[2];
		var H = str[1].split( ':' )[0];
		var i = str[1].split( ':' )[1];
		var s = str[1].split( ':' )[2];
		
		var D = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ];
		var M = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
		
		var date = new Date( Y, (m-1), d, H, i, s );
		var Y = date.getFullYear();
		var m = str_pad( ( date.getMonth() + 1 ), 2, 'STR_PAD_LEFT' );
		var d = str_pad( date.getDate(), 2, 'STR_PAD_LEFT' );
		var H = str_pad( date.getHours(), 2, 'STR_PAD_LEFT' );
		var i = str_pad( date.getMinutes(), 2, 'STR_PAD_LEFT' );
		var s = str_pad( date.getSeconds(), 2, 'STR_PAD_LEFT' );
		var D = D[date.getDay()];
		var M = M[date.getMonth()];
		
		var pattern = '|D|, |d| |M| |Y| |H|:|i|:|s|';
		
		pattern = pattern.split( '|d|' ).join( d );
		pattern = pattern.split( '|Y|' ).join( Y );
		pattern = pattern.split( '|H|' ).join( H );
		pattern = pattern.split( '|i|' ).join( i );
		pattern = pattern.split( '|s|' ).join( s );
		pattern = pattern.split( '|D|' ).join( D );
		pattern = pattern.split( '|M|' ).join( M );
		
		str = pattern;
	}
	
	var timezone;
	
	// Set default server timezone
	//timezone = ' +0200';
	
	str = str + ( timezone ? timezone : '' );
	
	//var d = Date.parse( str ) / 1000;
	
	var d = str;
	
	return d;
}

function str_pad( str, num, type )
{
	var pad = '0'; var out = '';
	for( i = 0; i < num; i++ )
	{
		out = out + pad;
	}
	if( !type || type == 'STR_PAD_LEFT' )
	{
		return ( pad + str ).slice(-num);
	}
	return false;
}

function jsdate( pattern, str )
{	
	// Y-m-d H:i:s
	var date = ( str ? new Date( str ) : new Date() );
	var Y = date.getFullYear();
	var m = str_pad( ( date.getMonth() + 1 ), 2, 'STR_PAD_LEFT' );
	var d = str_pad( date.getDate(), 2, 'STR_PAD_LEFT' );
	var H = str_pad( date.getHours(), 2, 'STR_PAD_LEFT' );
	var i = str_pad( date.getMinutes(), 2, 'STR_PAD_LEFT' );
	var s = str_pad( date.getSeconds(), 2, 'STR_PAD_LEFT' );
	
	var w = date.getDay();
	//var W = date.getWeek();
	var n = date.getMonth();
	//var y = date.getFullYear().slice(-2);
	var g = date.getHours();
	var u = date.getMilliseconds();
	
	var l = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];
	var D = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ];
	var N = [ '7', '1', '2', '3', '4', '5', '6' ];
	var F = [ 'January', 'Febuary', 'Mars', 'April', 'May', 'June', 'July', 'August', 'September', 'Octover', 'November', 'December' ];
	var M = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
	
	var j = date.getDate();
	var l = l[date.getDay()];
	var L = l[date.getDay()].toLowerCase();
	var N = N[date.getDay()];
	var D = D[date.getDay()];
	var F = F[date.getMonth()];
	var M = M[date.getMonth()];
	
	pattern = pattern.split( 'Y' ).join( Y );
	pattern = pattern.split( 'm' ).join( m );
	pattern = pattern.split( 'd' ).join( d );
	pattern = pattern.split( 'H' ).join( H );
	pattern = pattern.split( 'i' ).join( i );
	pattern = pattern.split( 's' ).join( s );
	
	pattern = pattern.split( 'w' ).join( w );
	//pattern = pattern.split( 'W' ).join( W );
	pattern = pattern.split( 'n' ).join( n );
	//pattern = pattern.split( 'y' ).join( y );
	pattern = pattern.split( 'g' ).join( g );
	pattern = pattern.split( 'u' ).join( u );
	
	pattern = pattern.split( 'j' ).join( j );
	pattern = pattern.split( 'l' ).join( l );
	pattern = pattern.split( 'L' ).join( L );
	pattern = pattern.split( 'N' ).join( N );
	pattern = pattern.split( 'F' ).join( F );
	pattern = pattern.split( 'M' ).join( M );
	pattern = pattern.split( 'D' ).join( D );
	
	return pattern;
}

function TimeToHuman( date, mode, current, timezone )
{
	if( !date ) return false;
	
	var unix = strtotime( date );
	
	if( current ) current = strtotime( current );
	else current = strtotime( jsdate( 'Y-m-d H:i:s' ) );
	
	date = jsdate( 'Y-m-d H:i:s', unix );
	
	var year = jsdate( 'Y', unix );
	var month = jsdate( 'F j', unix );
	//var week = jsdate( 'W', unix );
	var name = jsdate( 'D', unix );
	//var day = jsdate( 'n/j', unix );
	//var time = jsdate( 'g:ia', unix );
	var day = jsdate( 'j/n', unix );
	var time = jsdate( 'H:i', unix );
	
	var y = ( jsdate( 'Y', current ) - jsdate( 'Y', unix ) );
	var m = ( jsdate( 'm', current ) - jsdate( 'm', unix ) );
	var w = ( jsdate( 'W', current ) - jsdate( 'W', unix ) );
	var d = ( jsdate( 'd', current ) - jsdate( 'd', unix ) );
	var h = ( jsdate( 'H', current ) - jsdate( 'H', unix ) );
	var i = ( jsdate( 'i', current ) - jsdate( 'i', unix ) );
	var s = ( jsdate( 's', current ) - jsdate( 's', unix ) );
	
	if( !mode )
	{
		// Years ago
		if( y >= 2 ) return ( month + ', ' + year );
		if( y == 1 ) return ( month + ', ' + year );
		// Months ago
		if( m >= 2 ) return ( month + ' at ' + time );
		if( m == 1 ) return ( month + ' at ' + time );
		// Days ago
		if( d >= 2 ) return ( month + ' at ' + time );
		if( d == 1 ) return ( 'Yesterday at ' + time );
		// Hours ago
		if( h >= 2 ) return ( h + ' hours ago' );
		if( h == 1 ) return ( 'about an hour ago' );
		// Minutes ago
		if( i >= 2 ) return ( i + ' minutes ago' );
		if( i == 1 ) return ( 'about a minute ago' );
		// Seconds ago
		if( s >= 9 ) return ( s + ' seconds ago' );
		if( s >= 0 ) return ( 'a few seconds ago' );
	}
	if( mode == 'mini' )
	{
		// Years ago
		if( y > 0 ) return ( y + 'y' );
		// Months ago
		if( m > 0 ) return ( m + 'm' );
		// Days ago
		if( d > 0 ) return ( d + 'd' );
		// Hours ago
		if( h > 0 ) return ( h + 'h' );
		// Minutes ago
		if( i > 0 ) return ( i + 'm' );
		// Seconds ago
		if( s > 0 ) return ( s + 's' );
	}
	if( mode == 'medium' )
	{
		// Years ago
		if( y > 0 ) return ( jsdate( 'm/d/y H:i', unix ) );
		// Months ago
		if( m > 0 ) return ( day + ', ' + time );
		// Days ago
		if( d > 0 ) return ( day + ', ' + time );
		// Hours ago
		if( h > 0 ) return ( time );
		// Minutes ago
		if( i > 0 ) return ( time );
		// Seconds ago
		if( s > 0 ) return ( time );
	}
	if( mode == 'day' )
	{
		// Years ago
		if( y > 0 ) return ( jsdate( 'm/d/y', unix ) );
		// Months ago
		if( m > 0 ) return ( month );
		// Weeks ago
		if( w > 0 ) return ( name );
		// Days ago
		if( d > 0 ) return ( name );
		// Hours ago
		if( h > 0 ) return ( name );
		// Minutes ago
		if( i > 0 ) return ( name );
		// Seconds ago
		if( s > 0 ) return ( name );
	}
	
	return date;
}
/*
console.log( TimeToHuman( '2014-06-26 07:05:49', 'mini' ) + ' ..' );

console.log( strtotime( 'Wed, 26 Jan 2011 13:51:50' ) + ' one strtotime' );
console.log( strtotime( '2014-06-25 21:56:41' ) + ' two strtotime' );

console.log( jsdate( 'Y-m-d H:i:s', strtotime( 'Wed, 26 Jan 2011 13:51:50' ) ) + ' ..' );
console.log( jsdate( 'Y-m-d H:i:s', strtotime( '2014-06-26 06:56:31' ) ) + ' ||' );
console.log( jsdate( 'D, d M Y H:i:s' ) + ' --' );
console.log( jsdate( 'Y-m-d H:i:s' ) + ' ||' );*/
