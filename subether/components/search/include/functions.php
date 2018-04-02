<?php

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

function auto_decode ( $str )
{
	if( mb_detect_encoding( $str, 'auto' ) == 'UTF-8' )
	{
		$str = utf8_encode( $str );
	}
	else if( strstr( $str, 'Ã¥' ) || strstr( $str, 'Ã¸' ) || strstr( $str, 'Ã¦' ) )
	{
		$str = utf8_decode( $str );
	}
	return $str;
}
/*
function dot_trim( $str, $max, $find = false )
{
	//$str = trim( auto_decode( $str ) );
	if( $find ) 
	{
		$pos = strpos( strtolower( $str ), strtolower( $find ) );
	}
	if ( strlen ( $str ) > $max )
	{
		return substr ( $str, ( $find ? $pos : 0 ), $max ) . '...';
	}
	return $str;
}
*/
function TopLevelDomains ( $limited = false )
{
	if( $limited == '2' )
	{
		return array( 
			'biz', 'com', 'de', 'dk', 'eu', 'fi', 'gov', 'info',
			'mc', 'me', 'mil', 'no', 'net', 'org', 'pics', 'pl',
			'pro', 'tk', 'tv', 'uk', 'us', 'uz', 'ws' 
		);
	}
	else if( $limited == '1' )
	{
		return array( 
			'ac', 'ad', 'ae', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao',
			'aq', 'ar', 'as', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb',
			'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'biz', 'bj', 'bm', 'bn',
			'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by', 'bz', 'ca', 'cc',
			'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co',
			'com', 'cr', 'cu', 'cv', 'cw', 'cx', 'cy', 'cz', 'de', 'dj',
			'dk', 'dm', 'do', 'dz', 'ec', 'ee', 'eg', 'er', 'es', 'et',
			'eu', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gb', 'gd',
			'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gov', 'gp',
			'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk', 'hm', 'hn', 
			'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'info', 'int',
			'io', 'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo', 'jp', 'ke', 'kg',
			'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'lb',
			'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc',
			'md', 'me', 'mg', 'mh', 'mil', 'mk', 'ml', 'mm', 'mn', 'mo', 'mp',
			'mq', 'mr', 'ms', 'mt', 'mu', 'mv', 'mw', 'mx', 'my', 'mz', 'na',
			'nc', 'ne', 'net', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu',
			'nz', 'om', 'onl', 'org', 'pa', 'pe', 'pf', 'pg', 'ph', 'pics',
			'pk', 'pl', 'pm', 'pn', 'pr', 'pro', 'ps', 'pt', 'pw', 'py', 'qa',
			're', 'ro', 'rs', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg',
			'sh', 'si', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr', 'st', 'su',
			'sv', 'sx', 'sy', 'sz', 'tc', 'td', 'tel', 'tf', 'tg', 'th', 'tj',
			'tk', 'tl', 'tm', 'tn', 'to', 'tp', 'tr', 'tt', 'tv', 'tw', 'tz', 
			'ua', 'ug', 'uk', 'uno', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg',
			'vi', 'vn', 'vu', 'wed', 'wf', 'ws', 'ye', 'yt', 'za', 'zm', 'zw'
		);
	}
	else
	{
		return array( 
			'ac', 'academy', 'ad', 'ae', 'aero', 'af', 'ag', 'agency', 'ai', 'al', 'am', 
			'an', 'ao', 'aq', 'ar', 'arpa', 'as', 'asia', 'at', 'au', 'aw', 'ax', 'az', 
			'ba', 'bargains', 'bb', 'bd', 'be', 'berlin', 'bf', 'bg', 'bh', 'bi', 'bike', 
			'biz', 'bj', 'blue', 'bm', 'bn', 'bo', 'boutique', 'br', 'bs', 'bt', 'build', 
			'builders', 'buzz', 'bv', 'bw', 'by', 'bz', 'ca', 'cab', 'camera', 'camp', 
			'cards', 'careers', 'cat', 'catering', 'cc', 'cd', 'center', 'ceo', 'cf', 
			'cg', 'ch', 'cheap', 'ci', 'ck', 'cl', 'cleaning', 'clothing', 'club', 'cm', 
			'cn', 'co', 'codes', 'coffee', 'com', 'community', 'company', 'computer', 
			'condos', 'construction', 'contractors', 'cool', 'coop', 'cr', 'cruises', 
			'cu', 'cv', 'cw', 'cx', 'cy', 'cz', 'dance', 'dating', 'de', 'democrat', 
			'diamonds', 'directory', 'dj', 'dk', 'dm', 'do', 'domains', 'dz', 'ec', 
			'edu', 'education', 'ee', 'eg', 'email', 'enterprises', 'equipment', 'er', 
			'es', 'estate', 'et', 'eu', 'events', 'expert', 'exposed', 'farm', 'fi', 
			'fj', 'fk', 'flights', 'florist', 'fm', 'fo', 'foundation', 'fr', 'futbol', 
			'ga', 'gallery', 'gb', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gift', 'gl', 
			'glass', 'gm', 'gn', 'gov', 'gp', 'gq', 'gr', 'graphics', 'gs', 'gt', 'gu', 
			'guitars', 'guru', 'gw', 'gy', 'hk', 'hm', 'hn', 'holdings', 'holiday', 
			'house', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'immobilien', 'in', 
			'info', 'institute', 'int', 'international', 'io', 'iq', 'ir', 'is', 'it', 
			'je', 'jm', 'jo', 'jobs', 'jp', 'kaufen', 'ke', 'kg', 'kh', 'ki', 'kim', 
			'kitchen', 'kiwi', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'land', 
			'lb', 'lc', 'li', 'lighting', 'limo', 'link', 'lk', 'lr', 'ls', 'lt', 'lu', 
			'luxury', 'lv', 'ly', 'ma', 'maison', 'management', 'marketing', 'mc', 'md', 
			'me', 'menu', 'mg', 'mh', 'mil', 'mk', 'ml', 'mm', 'mn', 'mo', 'mobi', 'moda', 
			'monash', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'museum', 'mv', 'mw', 'mx', 
			'my', 'mz', 'na', 'nagoya', 'name', 'nc', 'ne', 'net', 'nf', 'ng', 'ni', 
			'ninja', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'onl', 'org', 'pa', 
			'partners', 'parts', 'pe', 'pf', 'pg', 'ph', 'photo', 'photography', 
			'photos', 'pics', 'pink', 'pk', 'pl', 'plumbing', 'pm', 'pn', 'post', 'pr', 
			'pro', 'productions', 'properties', 'ps', 'pt', 'pw', 'py', 'qa', 're', 
			'recipes', 'red', 'rentals', 'repair', 'report', 'reviews', 'rich', 'ro', 
			'rs', 'ru', 'ruhr', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sexy', 'sg', 'sh', 
			'shiksha', 'shoes', 'si', 'singles', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 
			'social', 'solar', 'solutions', 'sr', 'st', 'su', 'support', 'sv', 'sx', 
			'sy', 'systems', 'sz', 'tattoo', 'tc', 'td', 'technology', 'tel', 'tf', 
			'tg', 'th', 'tienda', 'tips', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'today', 
			'tokyo', 'tools', 'tp', 'tr', 'training', 'travel', 'tt', 'tv', 'tw', 'tz', 
			'ua', 'ug', 'uk', 'uno', 'us', 'uy', 'uz', 'va', 'vc', 've', 'ventures', 
			'vg', 'vi', 'viajes', 'villas', 'vision', 'vn', 'voting', 'voyage', 'vu', 
			'wang', 'watch', 'wed', 'wf', 'wien', 'works', 'ws', 'xn--3bst00m', 
			'xn--3ds443g', 'xn--3e0b707e', 'xn--45brj9c', 'xn--55qw42g', 'xn--55qx5d', 
			'xn--6frz82g', 'xn--6qq986b3xl', 'xn--80ao21a', 'xn--80asehdb', 'xn--80aswg', 
			'xn--90a3ac', 'xn--clchc0ea0b2g2a9gcd', 'xn--fiq228c5hs', 'xn--fiq64b', 
			'xn--fiqs8s', 'xn--fiqz9s', 'xn--fpcrj9c3d', 'xn--fzc2c9e2c', 'xn--gecrj9c', 
			'xn--h2brj9c', 'xn--io0a7i', 'xn--j1amh', 'xn--j6w193g', 'xn--kprw13d', 
			'xn--kpry57d', 'xn--l1acc', 'xn--lgbbat1ad8j', 'xn--mgb9awbf', 
			'xn--mgba3a4f16a', 'xn--mgbaam7a8h', 'xn--mgbayh7gpa', 'xn--mgbbh1a71e', 
			'xn--mgbc0a9azcg', 'xn--mgberp4a5d4ar', 'xn--mgbx4cd0ab', 'xn--ngbc5azd', 
			'xn--o3cw4h', 'xn--ogbpf8fl', 'xn--p1ai', 'xn--pgbs0dh', 'xn--q9jyb4c', 
			'xn--s9brj9c', 'xn--unup4y', 'xn--wgbh1c', 'xn--wgbl6a', 'xn--xkc2al3hye2a', 
			'xn--xkc2dl3a5ee0h', 'xn--yfro4i67o', 'xn--ygbi2ammx', 'xn--zfr164b', 'ye', 
			'yt', 'za', 'zm', 'zone', 'zw'
		);
	}
}

function getContentFast ( $url )
{
	if( !$url ) return false;
	$ok = false;
	$c = curl_init( $url );
	curl_setopt( $c, CURLOPT_NOBODY, true );
	$r = curl_exec( $c );
	$k = curl_getinfo( $c, CURLINFO_HTTP_CODE );
	if( $r !== false && ( $k == 200 || $k == 301 ) )
	{
		$ok = true;
	}
	/*Æelse if( ( $url = LinkAssembler( $url ) ) )
	{
		$ok = true;
	}*/
	if( $ok === false ) return false;
	curl_close( $c );
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	return array( curl_exec( $ch ), $url );
}

function LinkAssembler ( $url )
{
	if( !$url ) return false;
	
	//$time_start = microtime_float();
	
	if( substr( $url, 0, 7 ) != 'http://' && substr( $url, 0, 8 ) != 'https://' )
	{
		$url = 'http://' . $url;
	}
	
	$s = explode( '/', $url );
	$p = explode( '.', $s[2] );
	
	if( count( $p ) >= 3 ) $d = $p[1];
	else $d = $p[0];
	
	$d = str_replace( ' ', '+', trim( $d ) );
	$keys = explode( '+', $d );
	
	if( $keys )
	{
		$new = '';
		foreach( $keys as $key )
		{
			$new .= $key;
			if( !in_array( $new, $keys ) ) 
			{
				$keys[] = $new;
			}
		}
		rsort( $keys );
		
		$nwurl = array();
		$tdom = TopLevelDomains( 2 );
		$i = 0;
		foreach( $keys as $key )
		{
			foreach( $tdom as $td )
			{
				//if( $i > 50 ) die( ' 50 curl requests: ' . print_r( $nwurl,1 ) . ' in ' . number_format( ( $time = microtime_float() - $time_start ), 2, '.', '' ) . ' seconds' );
				$nwurl[] = $curl = str_replace( implode( '.', $p ), ( ( ( count( $p ) >= 3 ? ( $p[0] . '.' ) : '' ) . $key . '.' . $td ) ), $url );
				$c = curl_init( $curl );
				curl_setopt( $c, CURLOPT_NOBODY, true );
				$r = curl_exec( $c );
				$k = curl_getinfo( $c, CURLINFO_HTTP_CODE );
				if ( $r !== false && ( $k == 200 || $k == 301 ) ) 
				{
					return $curl;
				}
				curl_close( $c );
				$i++;
			}
		}
	}
	
	//die( print_r( $p,1 ) . ' .. ' . print_r( $keys,1 ) . ' .. ' . print_r( $nwurl,1 ) . ' in ' . number_format( ( $time = microtime_float() - $time_start ), 2, '.', '' ) . ' seconds' );
}

function LinkFilter ( $url )
{
	if( !$url ) return false;
	
	return $url;
	
	$url = str_replace( ' ', '', trim( strip_tags( $url ) ) );
	
	if( substr ( $url, 0, 7 ) != 'http://' && substr ( $url, 0, 8 ) != 'https://' )
	{
		$url = 'http://' . $url;
	}

	$s = explode( '/', $url );
	$p = explode( '.', $s[2] );
	
	if( !in_array( end( $p ), TopLevelDomains() ) )
	{
		$url = str_replace( implode( '.', $p ), ( idn_to_ascii(implode( '.', $p )) . '.no' ), $url );
		$p[] = 'no';
	}
	
	/*if( count( $p ) < 3 && !in_array( 'www', $p ) )
	{
		$url = str_replace( implode( '.', $p ), ( 'www.' . implode( '.', $p ) ), $url );
	}*/
	
	return $url;
}

function StringFilter ( $str )
{
	if( !$str[0] ) return;
	
	// Parse the document -----------------------------------------------------------
	$doc = new DOMDocument();
	@$doc->loadHTML( $str[0] );
	
	if( $doc )
	{
		$data = new stdClass();
		
		// Link ----------------------------------------------------------------
		$data->Link = $str[1];
		
		// Meta ----------------------------------------------------------------
		$content = ''; $key = ''; $data->Meta = array();
		foreach( $doc->getElementsByTagName( 'meta' ) as $meta )
		{
			$key = trim( $meta->getAttribute( 'name' ) );
			$key = mysql_real_escape_string( $key );
			$content = trim( $meta->getAttribute( 'content' ) );
			$content = mysql_real_escape_string( $content );
			if( $content != '' && $content != '0' && $key != '' )
			{
				$data->Meta[$key] = $content;
			}
		}
		
		// Title ---------------------------------------------------------------
		$value = '';
		foreach( $doc->getElementsByTagName( 'title' ) as $title )
		{
			$value = trim( $title->nodeValue );
			$value = mysql_real_escape_string( $value );
			if( $value != '' && $value != '0' )
			{
				$data->Title = $value;
			}
		}
		
		// Script -------------------------------------------------------------
		foreach( $doc->getElementsByTagName( 'script' ) as $script )
		{
			$script->nodeValue = '';
		}
		
		// KeyWords -------------------------------------------------------------
		$value = ''; $data->KeyWords = array();
		foreach( $doc->getElementsByTagName( 'h1' ) as $h1 )
		{
			$value = trim( $h1->nodeValue );
			$value = preg_replace( '/^\s+|\n|\r|\s+$/m', ' ', $value );
			$value = preg_replace( '/\s*$^\s*/m', ' ', $value );
			$value = preg_replace( '/[ \t]+/', ' ', $value );
			$value = mysql_real_escape_string( $value );
			if( !in_array( $value, $data->KeyWords ) && $value != '' && $value != '0' )
			{
				$data->KeyWords[] = $value;
			}
			else $h1->nodeValue = '';
		}
		
		$value = '';
		foreach( $doc->getElementsByTagName( 'h2' ) as $h2 )
		{
			$value = trim( $h2->nodeValue );
			$value = preg_replace( '/^\s+|\n|\r|\s+$/m', ' ', $value );
			$value = preg_replace( '/\s*$^\s*/m', ' ', $value );
			$value = preg_replace( '/[ \t]+/', ' ', $value );
			$value = mysql_real_escape_string( $value );
			if( !in_array( $value, $data->KeyWords ) && $value != '' && $value != '0' )
			{
				$data->KeyWords[] = $value;
			}
			else $h2->nodeValue = '';
		}
		
		$value = '';
		foreach( $doc->getElementsByTagName( 'h3' ) as $h3 )
		{
			$value = trim( $h3->nodeValue );
			$value = preg_replace( '/^\s+|\n|\r|\s+$/m', ' ', $value );
			$value = preg_replace( '/\s*$^\s*/m', ' ', $value );
			$value = preg_replace( '/[ \t]+/', ' ', $value );
			$value = mysql_real_escape_string( $value );
			if( !in_array( $value, $data->KeyWords ) && $value != '' && $value != '0' )
			{
				$data->KeyWords[] = $value;
			}
			else $h3->nodeValue = '';
		}
		
		$value = '';
		foreach( $doc->getElementsByTagName( 'h4' ) as $h4 )
		{
			$value = trim( $h4->nodeValue );
			$value = preg_replace( '/^\s+|\n|\r|\s+$/m', ' ', $value );
			$value = preg_replace( '/\s*$^\s*/m', ' ', $value );
			$value = preg_replace( '/[ \t]+/', ' ', $value );
			$value = mysql_real_escape_string( $value );
			if( !in_array( $value, $data->KeyWords ) && $value != '' && $value != '0' )
			{
				$data->KeyWords[] = $value;
			}
			else $h4->nodeValue = '';
		}
		
		$value = '';
		foreach( $doc->getElementsByTagName( 'h5' ) as $h5 )
		{
			$value = trim( $h5->nodeValue );
			$value = preg_replace( '/^\s+|\n|\r|\s+$/m', ' ', $value );
			$value = preg_replace( '/\s*$^\s*/m', ' ', $value );
			$value = preg_replace( '/[ \t]+/', ' ', $value );
			$value = mysql_real_escape_string( $value );
			if( !in_array( $value, $data->KeyWords ) && $value != '' && $value != '0' )
			{
				$data->KeyWords[] = $value;
			}
			else $h5->nodeValue = '';
		}
		
		$value = '';
		foreach( $doc->getElementsByTagName( 'h6' ) as $h6 )
		{
			$value = trim( $h6->nodeValue );
			$value = preg_replace( '/^\s+|\n|\r|\s+$/m', ' ', $value );
			$value = preg_replace( '/\s*$^\s*/m', ' ', $value );
			$value = preg_replace( '/[ \t]+/', ' ', $value );
			$value = mysql_real_escape_string( $value );
			if( !in_array( $value, $data->KeyWords ) && $value != '' && $value != '0' )
			{
				$data->KeyWords[] = $value;
			}
			else $h6->nodeValue = '';
		}
		
		// Links --------------------------------------------------------------
		$href = ''; $value = ''; $data->Links = array();
		foreach( $doc->getElementsByTagName( 'a' ) as $link )
		{
			$href = trim( $link->getAttribute( 'href' ) );
			if( !in_array( $href, $data->Links ) && $href != '' && substr( $href, 0, 4 ) == 'http' )
			{
				$data->Links[] = $href;
			}
			
			$value = trim( $link->nodeValue );
			$value = mysql_real_escape_string( $value );
			if( !in_array( $value, $data->KeyWords ) && $value != '' && $value != '0' )
			{
				$data->KeyWords[] = $value;
			}
			else $link->nodeValue = '';
		}
		
		// Leadin -------------------------------------------------------------
		$content = '';
		foreach( $doc->getElementsByTagName( 'body' ) as $body )
		{
			$content = trim( $body->nodeValue );
			$content = preg_replace( '/^\s+|\n|\r|\s+$/m', ' ', $content );
			$content = preg_replace( '/\s*$^\s*/m', ' ', $content );
			$content = preg_replace( '/[ \t]+/', ' ', $content );
			$content = mysql_real_escape_string( $content );
			$data->Leadin = $content;
		}
		
		//die( print_r( $data,1 ) . ' ..' );
		
		$data->KeyWords = implode( ' , ', $data->KeyWords );
		$data->Links = implode( ' , ', $data->Links );
		
		//die( print_r( $data,1 ) . ' ..' );
		
		return $data;
	}
	return false;
}

function strip_html_tags( $string )
{
    $string = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $string );
    return strip_tags( $string );
}


function ConvertToBingPage ( $page, $limit, $binglimit )
{
	if( !$page || !$limit || !$binglimit ) return;
	
	$out = array();
	
	//$page = '2';
	//$binglimit = '10';
	//$limit = '20';
	
	if( $limit > $binglimit )
	{
		$loop = ( $limit / $binglimit );
		for( $a = 0; $a < $loop; $a++ )
		{
			$out[] = ( ( $limit * ( $page - 1 ) ) + 1 );
		}
	}
	else
	{
		$out[] = ( ( $limit * ( $page - 1 ) ) + 1 );
	}
	//die( print_r( $out,1 ) . ' --' );
	return $out;
}

function ConvertFromBingPages ( $page, $limit, $bingpage, $bingpages )
{
	if( !$page || !$limit || !$bingpage || !$bingpages ) return;

	$pagespan = 4;
	
	$out = array();
	
	$start = ( ( $page - $pagespan ) > 1 ? ( $page - $pagespan ) : 1 );
	$end = ( $bingpage <= end( $bingpages ) ? ( $page + $pagespan ) : $page );
	
	for( $a = $start; $a < $page; $a++ )
	{
		$out[$a] = ( ( $limit * ( $a - 1 ) ) + 1 );
		if( $a > 100 ) return;
	}
	
	$out[$page] = $bingpage;
	
	for( $a = ($page+1); $a <= $end; $a++ )
	{
		$out[$a] = ( ( $limit * ( $a - 1 ) ) + 1 );
		if( $a > 100 ) return;
	}
	
	//die( print_r( $out,1 ) . ' ..' );
	return $out;
}
/*
function GetCurlContentByUrl ( $url )
{
	if( !$url ) return;
	
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	return curl_exec( $ch );
}
*/
function BingParser ( $url, $limit = false, $page = false, $counter = false )
{
	if( !$url ) return;
	
	$bingurl = 'http://www.bing.com/search?q=' . $url;
	
	$str = GetCurlContentByUrl( $bingurl );
	
	//die( $str );
	
	// Parse the document -----------------------------------------------------------
	$doc = new DOMDocument();
	@$doc->loadHTML( $str );
	
	if( $doc )
	{
		$data = array();
		
		$data['count'] = preg_replace( '/\D/', '', $doc->getElementById( 'b_tween' )->nodeValue );
		
		$data['pages'] = array();
		
		// Test --------------------------------------------------------------------
		
		$res = $doc->getElementById( 'b_results' );
		
		$i = 1;
		
		foreach( $res->getElementsByTagName( 'li' ) as $li )
		{
			// Result --------------------------------------------------------------
			
			if( strstr( $li->getAttribute( 'class' ), 'algo' ) )
			{
				$obj = new stdClass();
				
				foreach( $li->getElementsByTagName( 'h2' ) as $h2 )
				{
					foreach( $h2->getElementsByTagName( 'a' ) as $link )
					{
						$obj->Title = $link->nodeValue;
						$obj->Link = $link->getAttribute( 'href' );
					}
				}
				
				
				foreach( $li->getElementsByTagName( 'p' ) as $leadin )
				{
					$obj->Leadin = $leadin->nodeValue;
				}
				
				if( $obj->Title && ( !$limit || $limit >= $i ) )
				{
					$data['results'][] = $obj;
					$i++;
				}
				
				
			}
			
			// Pages -------------------------------------------------------------
				
			if( strstr( $li->getAttribute( 'class' ), 'pag' ) )
			{
				$ii = 1;
				foreach( $li->getElementsByTagName( 'a' ) as $pge )
				{
					if( intval( $pge->nodeValue ) )
					{
						if( $href = explode( '&', $pge->getAttribute( 'href' ) ) )
						{
							$vals = array();
							foreach( $href as $val )
							{
								$val = explode( '=', $val );
								$vals[$val[0]] = $val[1];
							}
							
							$data['pages'][$pge->nodeValue] = $vals['first'];
						}
					}
					$ii++;
				}
			}
		}
		
		//die( print_r( $data,1 ) . ' ..' );
		
		if( $page > 1 )
		{
			$binglimit = ( $data['pages'][2] - 1 );
			
			$pdata = array();
			
			if( $bingpages = ConvertToBingPage( $page, $limit, $binglimit ) )
			{
				$pdata['count'] = $data['count'];
				
				foreach( $bingpages as $bp )
				{
					$purl = ( $url . '&first=' . $bp );
					$arr = BingParser( $purl, $limit );
					
					$pdata['pages'] = ConvertFromBingPages( $page, $limit, $bp, $arr['pages'] );
					
					foreach( $arr['results'] as $ar )
					{
						$pdata['results'][] = $ar;
					}
				}
			}
			
			//die( print_r( $pdata,1 ) . ' ..' );
			return $pdata;
		}
		else
		{
			return $data;
		}
	}
	return false;
}

?>
