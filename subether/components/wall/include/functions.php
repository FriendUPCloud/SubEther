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

function sanitizeHTML ( $html )
{	
	// Load the post as a DOM tree
	/*$doc = new DOMDocument();
	$doc->loadHTML( $html );
	
	// Allowed tags and attributes
	$tagWhitelist = array(
		//array( 'name' => 'a', 'attributes' => 'href' ),
		array( 'name' => 'br' ) 
	);
	
	if( $doc )
	{
		// Loop through ALL the tags in the post
		$tags = $doc->getElementsByTagName( '*' );
		if( $tags )
		{
			foreach( $tags as $tag )
			{
				// Check if this tag is in the whitelist
				$found = false;
				foreach( $tagWhitelist as $allowedTag )
				{
					if( $tag->nodeName == $allowedTag['name'] )
					{
						$tagfound = true;
						break;
					}
					if( $tag->getAttribute( $allowedTag['attributes'] ) )
					{
						$attfound = true;
						break;
					}
				}
				
				// If this tag wasn't found in the whitelist, remove it from the document
				if( !$tagfound )
				{
					$tag->parentNode->removeChild( $tag );
				}  
				
				// If it is allowed, check if it contains any illegal attributes, and remove those as well
				if( !$attfound )
				{
					$tag->parentNode->removeAttribute( $tag );
				}
			}
		}
		
		// Finally, save the new post as HTML and load into the database
		$html = $doc->saveHTML();
	}
	die( $html . ' ..' );*/
	return mysql_real_escape_string ( $html );
}

// Clears input text...
function formatText( $str )
{
	if( !$str ) return;
	
	$br = array( '<br />', '<br>', '<br/>', '&lt;br /&gt;', '&lt;br/&gt;', '&lt;br&gt;' );
	
	// Convert divs to newlines.. (google chrome)
	$str = preg_replace( '/\<div[^>]*?\>/i', '', $str );
	$str = preg_replace( '/\<\/div[^>]*?\>/i', "\r\n", $str );
	
	$str = str_ireplace ( $br, "\r\n", $str );
	
	$str = preg_replace( '/<[^>]*>/', '', $str );
	
	$str = nl2br( $str );
	
	$str = preg_replace_hashtags( $str );
	
	$str = makeLinks( $str );
	
	$str = stripslashes( $str );
	
	$str = convertToTags( $str );
	
	return $str;
}

function convertToTags( $str )
{
	// Match
	//$str = html_entity_decode( $str );
	if( preg_match_all( '/(\<code\>)([\w\W]*?)(\<\/code\>)/i', $str, $matches ) )
	{
		// Replace
		$str = preg_replace_callback( '/\<code\>([\w\W]*?)\<\/code\>/i', function( $m ){ return '<div class="Code"><pre>' . htmlentities( str_replace( array( '<br>', '<p>', '</p>' ), array( "\n", "\n", '' ), $m[1] ) ) . '</pre></div>'; }, $str );
	}
	
	return $str;
}

// Blacklist executed!
function sanitizeText( $str )
{
	$str = preg_replace( array( '/\<\/p\>/i', '/\<div\>\<br[^>]*?\>/i', '/\<div\>/i', '/\<br[^>]*?\>/i' ), "\r\n", $str );
	$str = strip_tags( $str );
	$str = str_replace( "\r\n", "<br>", $str );
	$str = preg_replace_hashtags( $str );
	$str = makeLinks( $str );
	return $str;
}

function preg_replace_hashtags( $str )
{
	if( $hashtags = gethashtags( $str ) )
	{
		foreach( $hashtags as $tag )
		{
			$str = str_replace( trim( $tag ), ( ' <a class="hashtag" href="en/home/wall/?hashtag=' . strtolower( str_replace( '#', '', trim( $tag ) ) ) . '"><span>' . trim( $tag ) . '</span></a>' ), $str );
		}
	}
	return $str;
}

function gethashtags( $str )
{
	if( !$str ) return false;
	if( $str{0} == '#' )
	{
		$str = ( ' ' . $str );
	}
	preg_match_all( '/(\s#\w+)/', $str, $matches );
	return $matches[1];
}

function getUrls( $str )
{
	$str = strip_tags( trim( $str ) );
	
	$str = preg_replace( '/^\s+|\n|\r|\s+$/m', ' ', $str );
	$str = preg_replace( '/\s*$^\s*/m', ' ', $str );
	$str = preg_replace( '/[ \t]+/', ' ', $str );
	
	$out = array();
	
	$regex = array( '/https?\:\/\/[^\" ]+/i', '/http?\:\/\/[^\" ]+/i' );
	
	foreach( $regex as $rgx )
	{
		preg_match_all( $rgx, $str, $matches );
		
		foreach( $matches[0] as $m )
		{
			if( !in_array( $m, $out ) )
			{
				$out[] = $m;
			}
		}
	}

	return array_reverse( $out );
	//return $out;
}

function parseText ( $str, $width = false, $height = false, $ajax = false )
{
	// Limit
	$titlim = 63;
	$deslim = 530;
	
	if( $ajax )
	{
		$str = getUrls( $str );
		
		$str = $str[0];
		
		// Youtube ------------------------------------------------------------------------------------------------------
		if( ( strstr( $str, 'youtube.com/watch?v=' ) || strstr( $str, 'youtu.be/' ) ) && $width > 0 && $height > 0 )
		{
			if( $ajax == 'video' )
			{
				$str = embedYoutube( $str, $width, $height );
				//$str = stripslashes ( $str );
				return $str;
			}
			else
			{
				$content = ContentByUrl( $str );
				$obj = ContentFilter( $content, $str );
				//$image = GetArrayByKeyAndParam( $obj->Link, 'itemprop', 'thumbnailUrl' );
				
				$obj->Type = 'video';
				$obj->Title = $obj->Meta['title'];
				$obj->Description = $obj->Meta['description'];
				//$obj->Images[0]['src'] = $image['href'];
				$obj->Images[0]['src'] = $obj->Meta['og:image'];
				$obj->Images[0]['width'] = $width;
				$obj->Images[0]['height'] = $height;
				
				if( $obj->Images[0]['src'] && $obj->Url && $obj->Title )
				{
					return $obj;
				}
				
				//$out = '';
				/*$image = makeImage( $image['href'], $width, $height, 'video', $folder );
				$out .= '<div class="ParseContent">';
				if( $image )
				{
					$out .= '<div class="image" link="' . $obj->Url . '" replace="">';
					$out .= $image;
					$out .= '<i></i></div>';
				}
				$out .= '<div class="text">';
				$out .= '<h3><a target="_blank" href="' . $obj->Url . '">' . dot_trim( $obj->Meta['title'], $titlim ) . '</a></h3>';
				$out .= '<p><a target="_blank" href="' . $obj->Url . '">' . dot_trim( $obj->Meta['description'], $deslim ) . '</a></p>';
				$out .= '<p class="url"><a target="_blank" href="' . $obj->Url . '">' . $obj->Domain . '</a></p>';
				$out .= '</div></div>';
				$out .= '<div class="Edit" onclick="removeParse()"><div></div></div>';
				$str = $out;
				$str = stripslashes ( $str );
				return $str;*/
			}
		}
		// Vimeo -------------------------------------------------------------------------------------------------------
		else if( strstr( $str, 'vimeo.com/' ) && $width > 0 && $height > 0 )
		{
			if( $ajax == 'video' )
			{
				$str = embedVimeo( $str, $width, $height );
				$str = stripslashes ( $str );
				return $str;
			}
			else
			{
				$content = ContentByUrl( $str );
				$obj = ContentFilter( $content, $str );
				
				$obj->Type = 'video';
				$obj->Title = $obj->Meta['og:title'];
				$obj->Description = $obj->Meta['og:description'];
				$obj->Images[0]['src'] = $obj->Meta['thumbnailUrl'];
				$obj->Images[0]['width'] = $width;
				$obj->Images[0]['height'] = $height;
				
				if( $obj->Images[0]['src'] && $obj->Url && $obj->Title )
				{
					return $obj;
				}
				
				//$out = '';
				/*$image = makeImage( $obj->Meta['thumbnailUrl'], $width, $height, 'video', $folder );
				$out .= '<div class="ParseContent">';
				if( $image )
				{
					$out .= '<div class="image" link="' . $obj->Url . '" replace="">';
					$out .= $image;
					$out .= '<i></i></div>';
				}
				$out .= '<div class="text">';
				$out .= '<h3><a target="_blank" href="' . $obj->Url . '">' . dot_trim( $obj->Meta['og:title'], $titlim ) . '</a></h3>';
				$out .= '<p><a target="_blank" href="' . $obj->Url . '">' . dot_trim( $obj->Meta['og:description'], $deslim ) . '</a></p>';
				$out .= '<p class="url"><a target="_blank" href="' . $obj->Url . '">' . $obj->Domain . '</a></p>';
				$out .= '</div></div>';
				$out .= '<div class="Edit" onclick="removeParse()"><div></div></div>';
				$str = $out;
				$str = stripslashes ( $str );
				return $str;*/
			}
		}
		// Livestream -------------------------------------------------------------------------------------------------
		else if( ( strstr( $str, 'livestream.com/' ) || strstr( $str, 'livestre.am/' ) ) && $width > 0 && $height > 0 )
		{
			if( $ajax == 'video' )
			{
				$str = embedLivestream( $str, $width, $height );
				//$str = stripslashes ( $str );
				return $str;
			}
			else
			{
				$content = ContentByUrl( $str );
				$obj = ContentFilter( $content, $str );
				
				$url = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', trim( $obj->Meta['og:url'] ) ) ) );
				
				$obj->Type = 'video';
				$obj->Url = $obj->Meta['og:url'];
				$obj->Title = $obj->Meta['og:title'];
				$obj->Description = $obj->Meta['og:description'];
				$obj->Domain = $url[0];
				$obj->Images[0]['src'] = $obj->Meta['og:image'];
				$obj->Images[0]['width'] = $width;
				$obj->Images[0]['height'] = $height;
				
				if( $obj->Images[0]['src'] && $obj->Url && $obj->Title )
				{
					return $obj;
				}
				
				//$out = '';
				/*//die( print_r( $obj,1 ) . ' .. ' );
				$image = makeImage( $obj->Meta['og:image'], $width, $height, 'video', $folder );
				$url = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', trim( $obj->Meta['og:url'] ) ) ) );
				$out .= '<div class="ParseContent">';
				if( $image )
				{
					$out .= '<div class="image" link="' . $obj->Meta['og:url'] . '" replace="">';
					$out .= $image;
					$out .= '<i></i></div>';
				}
				$out .= '<div class="text">';
				$out .= '<h3><a target="_blank" href="' . $obj->Meta['og:url'] . '">' . dot_trim( $obj->Meta['og:title'], $titlim ) . '</a></h3>';
				$out .= '<p><a target="_blank" href="' . $obj->Meta['og:url'] . '">' . dot_trim( $obj->Meta['og:description'], $deslim ) . '</a></p>';
				$out .= '<p class="url"><a target="_blank" href="' . $obj->Meta['og:url'] . '">' . $url[0] . '</a></p>';
				$out .= '</div></div>';
				$out .= '<div class="Edit" onclick="removeParse()"><div></div></div>';
				$str = $out;
				$str = stripslashes ( $str );
				return $str;*/
			}
		}
		// Remote image ------------------------------------------------------------------------------------------------
		else if( 
			strstr( strtolower( $str ), 'http' ) && 
			( 
				strstr( strtolower( $str ), '.jpg' ) || 
				strstr( strtolower( $str ), '.png' ) || 
				strstr( strtolower( $str ), '.gif' ) || 
				strstr( strtolower( $str ), '.jpeg' ) 
			) 
		)
		{
			$url = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', trim( $str ) ) ) );
			$imagesize = getimagesize( $str );
			
			$obj = new stdClass();
			$obj->Type = 'picture';
			$obj->Url = $str;
			$obj->Title = $str;
			$obj->Domain = $url[0];
			$obj->Images[0]['src'] = $str;
			$obj->Images[0]['width'] = $imagesize[0];
			$obj->Images[0]['height'] = $imagesize[1];
			
			if( $obj->Images[0]['src'] && $obj->Url && $obj->Title )
			{
				return $obj;
			}
			
			/*$class = 'image picture small';
			$imagesize = getimagesize( $str );
			if( $imagesize && $width && $width <= $imagesize[0] )
			{
				$class = 'image picture big';
			}
			$url = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', trim( $str ) ) ) );
			$out .= '';
			$out .= '<div class="ParseContent">';
			$out .= '<div class="' . $class . '"><a target="_blank" href="' . $str . '">';
			$out .= makeImage( $str, $imagesize[0], $imagesize[1], 'picture', $folder );
			$out .= '</a></div>';
			$out .= '<div class="text">';
			$out .= '<h3><a target="_blank" href="' . $str . '">' . $str . '</a></h3>';
			$out .= '<p class="url"><a target="_blank" href="' . $str . '">' . $url[0] . '</a></p>';
			$out .= '</div></div>';
			$out .= '<div class="Edit" onclick="removeParse()"><div></div></div>';
			$str = $out;
			$str = stripslashes ( $str );
			return $str;*/
		}
		// Remote site data --------------------------------------------------------------------------------------------------
		else if( strstr( strtolower( $str ), 'http' ) && $content = ContentByUrl( $str ) )
		{
			$obj = ContentFilter( $content, $str, 'site' );

			if( ( $desc = $obj->Meta['description'] ) || ( $desc = $obj->Meta['og:description'] ) )
			{
				$obj->Leadin = $desc;
			}
			$obj->Type = 'site';
			//die( print_r( $obj,1 ) . ' ..' );
			if( $obj->Title && $obj->Leadin )
			{
				return $obj;
			}
			
			//$out = '';
			//$class = 'image site small';
			/*if( $obj->Images[0]['width'] && $width && $width <= $obj->Images[0]['width'] )
			{
				$class = 'image site big';
			}
			//die( print_r( $obj,1 ) . ' ..' );
			$image = makeImage( $obj->Images[0]['src'], $obj->Images[0]['width'], $obj->Images[0]['height'], 'page', $folder );
			$out .= '<div class="ParseContent">';
			if( $image )
			{
				$out .= '<div class="' . $class . '"><a target="_blank" href="' . $obj->Url . '">';
				$out .= $image;
				$out .= '</a></div>';
			}
			$out .= '<div class="text">';
			$out .= '<h3><a target="_blank" href="' . $obj->Url . '">' . dot_trim( $obj->Title, $titlim ) . '</a></h3>';
			$out .= '<p><a target="_blank" href="' . $obj->Url . '">' . dot_trim( $obj->Leadin, $deslim ) . '</a></p>';
			$out .= '<p class="url"><a target="_blank" href="' . $obj->Url . '">' . $obj->Domain . '</a></p>';
			$out .= '</div></div>';
			$out .= '<div class="Edit" onclick="removeParse()"><div></div></div>';
			$str = $out;
			if( $image || ( $obj->Title && $obj->Leadin ) )
			{
				$str = stripslashes ( $str );
				return $str;
			}*/
		}
	}
	// Create links for urls --------------------------------------------------------------------------------------------------
    else
    {
      $str = makeLinks( $str );
		//$str = stripslashes ( $str );
		//$str = preg_replace ( '/\\\([\'|"])/i', '$1', $str );
		return $str;
    }
	//return $str;
	return false;
}
/*
function embedVimeo ( $str, $width, $height )
{
    $str = explode( '/', $str );
    $str = explode( '?', end( $str ) );
    $str = '<iframe src="//player.vimeo.com/video/' . $str[0] . '?title=0&amp;byline=0&amp;portrait=0&amp;color=ff3331" width="' . $width . '" height="' . $height . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    return trim( $str );
}

function embedLivestream ( $str, $width, $height )
{
	$height = ( $height + 200 );
    $str = explode( '/', $str );
    $clip = ( end( $str ) != $str[3] ? end( $str ) : '' );
    $str = '<iframe src="http://cdn.livestream.com/embed/' . $str[3] . '?layout=2' . ( $clip ? '&clip=' . $clip : '' ) . '&autoPlay=true&width=' . $width . '&height=' . $height . '" width="' . $width . '" height="' . $height . '" style="border:0;outline:0" frameborder=0 scrolling=no></iframe>';
	//die( $str . ' ..' );
	return trim( $str );
}*/
/*
function makeLinks( $str )
{
    $str = preg_replace( "/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2", $str );
    $str = preg_replace( "/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i", "<a target=\"_blank\" href=\"$1\">$1</a>", $str );
    $str = preg_replace( "/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i", "<a href=\"mailto:$1\">$1</a>", $str );
    return $str;
}
*/
function makeImage( $url, $width = false, $height = false, $type = false, $folder = false, $data = false )
{
	global $webuser;
	
	$style = '';
	if( $width && $height )
	{
		$style = 'max-width:' . $width . 'px;max-height:' . $height . 'px;';
	}
		
	if( $folder && $data )
	{
		$lib = new Library ();
		if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}
		$lib->ParentFolder = 'Album';
		$lib->FolderName = 'Other';
		$lib->SaveParsedData( $data );
		
		$img = new dbImage ();
		if( $lib->FileID > 0 && $img->Load( $lib->FileID ) )
		{
			$str = $img->getImageURL ( $width, $height, 'framed', false, 0xffffff );
		}
		
		if( $str ) $url = $str;
	}
	else if( $folder )
	{		
		$lib = new Library ();
		if( strtolower( $folder->MainName ) != 'profile' )
		{
			$lib->CategoryID = $folder->CategoryID;
		}
		$lib->ParentFolder = 'Album';
		$lib->FolderName = 'Other';
		$lib->UploadFileByUrl( $url );
		
		$img = new dbImage ();
		if( $lib->FileID > 0 && $img->Load( $lib->FileID ) )
		{
			$str = $img->getImageURL ( $width, $height, 'framed', false, 0xffffff );
		}
		
		if( $str ) $url = $str;
	}

    $url = preg_replace( "/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2", $url );
    $str = preg_replace( "/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i", "<img style=\"background-image:url($1);" . $style . "\"/>", $url );
    
	return $str;
}

function trimString( $str, $max, $find = false )
{
	//$str = trim( auto_decode( $str ) );
	if( $find ) 
	{
		$pos = strpos( strtolower( $str ), strtolower( $find ) );
	}
	if ( strlen ( $str ) > $max )
	{
		return substr ( $str, ( $find ? $pos : 0 ), $max ) . '...';
		/*' (' . $pos . ' - ' . strlen( $str ) . ' - ' . $max . ')';*/
	}
	return $str;
}

// Fix broken text characters brute force!
// TODO: Add a table of characters here, for broken øæå etc
function FixCharacters ( $string )
{
	$len = strlen ( $string );
	$out = '';
	for ( $a = 0; $a < $len; $a++ )
	{	
		switch ( $string{$a} )
		{
			default:
				$out .= $string{$a};
				break;
		}
	}
	return $out;
}
/*
function ContentByUrl ( $url )
{
	if( !$url ) return;
	
	// Get data
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$str = stripslashes ( curl_exec( $ch ) );
	
	// Detect encoding
	$encoding = mb_detect_encoding ( $str );
	if ( !$encoding ) $encoding = 'UTF-8';
		
	// Convert to unicode
	if ( strtoupper ( $encoding ) != 'UTF-8' )
		$str = iconv ( $encoding, 'UTF-8', $str );
	
	$str = correct_encoding( $str );
	
	// Return
	// TODO: add FixCharacters() here
	return $str;
}
*/

function ContentFilter ( $str, $url = false, $type = false )
{
	if( !$str ) return;
	
	// Parse the document -----------------------------------------------------------
	$doc = new DOMDocument();
	@$doc->loadHTML( $str );
	
	if( $doc )
	{
		$data = new stdClass();
		
		if( $url )
		{
			$data->Url = trim( $url );
			$url = explode( '/', str_replace( 'http://', '', str_replace( 'https://', '', $data->Url ) ) );
			$data->Domain = $url[0];
		}
		
		// Meta ----------------------------------------------------------------
		$content = ''; $key = ''; $data->Meta = array();
		foreach( $doc->getElementsByTagName( 'meta' ) as $meta )
		{
			foreach( array( 'name', 'property', 'itemprop' ) as $k )
			{
				$key = trim( $meta->getAttribute( $k ) );
				$key = mysql_real_escape_string( $key );
				$content = trim( $meta->getAttribute( 'content' ) );
				$content = mysql_real_escape_string( $content );
				if( $content != '' && $content != '0' && $key != '' )
				{
					$data->Meta[$key] = $content;
				}
			}
		}

		/*// Link ----------------------------------------------------------------
		$content = ''; $key = ''; $data->Link = array(); $i = 0;
		foreach( $doc->getElementsByTagName( 'link' ) as $link )
		{
			foreach( array( 'title', 'href', 'type', 'rel', 'type', 'sizes', 'media', 'id', 'class', 'data-loaded', 'itemprop' ) as $k )
			{
				$content = trim( $link->getAttribute( $k ) );
				$content = mysql_real_escape_string( $content );
				if( $content != '' && $content != '0' )
				{
					$data->Link[$i][$k] = $content;
				}
			}
			$i++;
		}*/
		
		// Title ---------------------------------------------------------------
		$value = '';
		foreach( $doc->getElementsByTagName( 'title' ) as $title )
		{
			$value = trim( $title->nodeValue );
			if( ( $tite = $data->Meta['title'] ) || ( $tite = $data->Meta['og:title'] ) )
			{
				$value = $tite;
			}
			//$value = correct_encoding( $value );
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
		
		// Style --------------------------------------------------------------
		foreach( $doc->getElementsByTagName( 'style' ) as $style )
		{
			$style->nodeValue = '';
		}
		
		// Images --------------------------------------------------------------
		$src = ''; $data->Images = array(); $i = 0;
		if( ( $im = $data->Meta['image'] ) || ( $im = $data->Meta['og:image'] ) )
		{
			$src = trim( $im );			
			if( $src != '' && !in_array( $src, $data->Images ) && $curl = CurlUrlExists( $src, 'content_type=>image' ) )
			{
				$imagesize = getimagesize( $src );
				if( $imagesize && $imagesize[0] >= '154' && $imagesize[1] >= '154' )
				{
					$data->Images[$i]['src'] = $src;
					$data->Images[$i]['width'] = $imagesize[0];
					$data->Images[$i]['height'] = $imagesize[1];
					$data->Images[$i]['type'] = $imagesize[2];
					$data->Images[$i]['attr'] = $imagesize[3];
					$data->Images[$i]['bits'] = $imagesize['bits'];
					$data->Images[$i]['mime'] = $imagesize['mime'];
					$i++;
				}
			}
		}
		foreach( $doc->getElementsByTagName( 'img' ) as $img )
		{
			if( $i >= 4 ) continue;
			$src = trim( $img->getAttribute( 'src' ) );			
			if( $src != '' && !in_array( $src, $data->Images ) && $curl = CurlUrlExists( $src, 'content_type=>image' ) )
			{
				$imagesize = getimagesize( $src );
				if( $imagesize && $imagesize[0] >= '154' && $imagesize[1] >= '154' )
				{
					$data->Images[$i]['src'] = $src;
					$data->Images[$i]['width'] = $imagesize[0];
					$data->Images[$i]['height'] = $imagesize[1];
					$data->Images[$i]['type'] = $imagesize[2];
					$data->Images[$i]['attr'] = $imagesize[3];
					$data->Images[$i]['bits'] = $imagesize['bits'];
					$data->Images[$i]['mime'] = $imagesize['mime'];
					$i++;
				}
			}
		}
		
		// Leadin -------------------------------------------------------------
		$content = ''; $cstr = ''; $data->Leadin = '';
		foreach( $doc->getElementsByTagName( 'p' ) as $body )
		{
			$content = trim( $body->nodeValue );
			$content = preg_replace( '/^\s+|\n|\r|\s+$/m', ' ', $content );
			$content = preg_replace( '/\s*$^\s*/m', ' ', $content );
			$content = preg_replace( '/[ \t]+/', ' ', $content );
			$content = strip_tags( $content );
			//$content = correct_encoding( $content );
			$content = mysql_real_escape_string( $content );
			if( strlen( $content ) > 100 && strlen( $cstr ) < 800 )
			{
				$cstr = ( $cstr ? ( $cstr . ' ' . $content ) : $content );
			}
		}
		if( $cstr )
		{
			$data->Leadin = $cstr;
			if( ( $desc = $data->Meta['description'] ) || ( $desc = $data->Meta['og:description'] ) )
			{
				$data->Leadin = $desc;
			}
		}
		
		//die( print_r( $data,1 ) . ' ..' );
		
		return $data;
	}
	
	return false;
}

function StrReplaceByAttribute ( $str, $attr, $replace )
{
	if( $str && $attr && $replace && strstr( $str, $attr ) )
	{
		return str_replace( $attr, $replace, $str );
	}
	return $str;
}

function GetArrayByKeyAndParam ( $array, $key, $param )
{
	if( !is_array( $array ) || !$key || !$param ) return;
	
	foreach( $array as $a )
	{
		if( trim( strtolower( $a[$key] ) ) == trim( strtolower( $param ) ) )
		{
			return $a;
		}
	}
	return false;
}

function UrlFromString ( $str )
{
    return str_replace( ' ', '_', strtolower( trim( $str ) ) );
}

function getFeedFolders( $userid, $type, $com = false )
{
    global $database;
    
    if( !$userid && !$type ) return false;
    
    if( $type = explode( ',', trim( $type ) ) )
    {
        foreach( $type as $k=>$t )
        {
            $type[$k] = strtolower( trim( $t ) );
        }
    }
    
    $query = array();
    
    if( in_array( 'contacts', $type ) )
    {        
        $query[] = $q = '
            SELECT 
                cr.*, ca.Name, ca.Type 
            FROM
                SBookContactRelation ct,
                SBookContact co,
                SBookCategoryRelation cr,
                SBookCategory ca,
                SBookCategory sc 
            WHERE 
                ct.ObjectID = \'' . $userid . '\' 
                AND ct.ObjectType = "Users" 
                AND ct.IsApproved = "1" 
                AND co.ID = ct.ContactID 
                AND cr.ObjectID = co.UserID
                AND cr.ObjectType = "Users"
                AND ca.ID = cr.CategoryID
                AND ca.Type = "SubGroup"
                AND ca.Name = "Wall"
                AND sc.ID = ca.CategoryID
                AND sc.Type = "Group"
                AND sc.Name = "Profile" 
            ORDER BY 
                cr.ID ASC 
        ';
        
        //die( $q . ' .. ' );
    }
    if( in_array( 'wall', $type ) )
    {
        $query[] = '
            SELECT 
                cr.*, ca.Name, ca.Type
            FROM 
                SBookCategory ca, 
                SBookCategoryRelation cr
            WHERE 
                cr.ObjectID = \'' . $userid . '\' 
                AND cr.ObjectType = "Users" 
                AND ca.ID = cr.CategoryID 
                AND ca.Type = "SubGroup" 
                AND ca.Name = "Wall" 
            ORDER BY 
                cr.ID ASC 
        ';
    }
    if( in_array( 'groups', $type ) )
    {
        $query[] = '
            SELECT 
                sc.*, cr.ID AS ID, cr.CategoryID as CategoryID 
            FROM 
                SBookCategoryRelation cr, 
                SBookCategory sc, 
                SBookCategory cs 
            WHERE 
                cr.ObjectType = "Users" 
                AND cr.ObjectID = \'' . $userid . '\' 
                AND sc.ID = cr.CategoryID 
                AND cs.ID = sc.CategoryID 
                AND cs.CategoryID = "0" 
                AND cs.Type = "Group" 
                AND cs.Name = "Groups" 
            ORDER BY 
                sc.ID ASC 
        ';
    }
    
    //die( print_r( $query,1 ) . ' .. ' . $userid . ' .. ' . print_r( $type,1 ) );
    
    $i = 0;
    $cats = array();
    $mcats = array();
    $object = array();
    
    if( $query )
    {	
        foreach( $query as $q )
        {
            if( $folders = $database->fetchObjectRows ( $q ) )
            {
                //if( $com == 'wall' ) die( print_r( $folders,1 ) . ' .. ' . print_r( $query,1 ) . ' .. ' . print_r( $type,1 ) . ' .. ' . $com );
                foreach( $folders as $folder )
                {
                    foreach( $folder as $k=>$f )
                    {
                        if( $k == 'ID' && !in_array( $f, $cats ) ) $cats[] = $f;
                        if( $k == 'CategoryID' && !in_array( $f, $mcats ) ) $mcats[] = $f;
                    }
                    $object[$i] = $folder;
                }
                $i++;
            }
        }
        
        if( $object )
        {
            foreach( $object as $obj )
            {
                foreach( $obj as $k=>$o )
                {
                    $obj->Categories = implode( ',', $cats );
                    $obj->MainCategories = implode( ',', $mcats );
                }
            }
            return $object;
        }
        return false;
    }
    return false;
}

function getEventsID ()
{
	global $database;
	
	if( $row = $database->fetchObjectRow ( '
		SELECT 
			c.* 
		FROM 
			SBookCategory c, 
			SBookCategory c2 
		WHERE 
				c.Type = "SubGroup" 
			AND c.Name = "Events" 
			AND c2.ID = c.CategoryID 
			AND c2.Type = "Group"
			AND c2.Name = "Profile" 
		ORDER BY 
			c.ID ASC 
	' ) )
	{
		return $row->ID;
	}
	
	return false;
}

function CheckWallUpdates ( $lastupdate )
{
	global $database;
	
	if( $lastupdate != '' ) $lastupdate = date( 'Y-m-d H:i:s', strtotime( $lastupdate ) );
	
	if( $row = $database->fetchObjectRow( 'SELECT MAX( DateModified ) AS LastUpdate FROM SBookMessage ORDER BY DateModified DESC' ) )
	{
		if( !$lastupdate )
		{
			return $row->LastUpdate;
		}
		else if( $row->LastUpdate != $lastupdate )
		{
			return $row->LastUpdate;
		}
	}
	
	// TODO: Add more plugins to the wall
	
	return false;
}



function WallMetaData ( $type, $source, $thumb, $filetype, $title, $description, $domain, $width, $height, $media = false )
{
	$str  = ''; $image = ''; $onclick = ''; $size = ''; $widthlimit = ''; $heightlimit = ''; $controls = '';
	
	// Add dependecy if it can't find it for no good reason ...
	if( file_exists( BASE_DIR . '/subether/components/library/include/functions.php' ) )
	{
		include_once ( BASE_DIR . '/subether/components/library/include/functions.php' );
	}
	
	switch( $type )
	{
		// Audio ----------------------------------------------------------------------------------
		case 'audio':
			$type = '';
			$filetype = ( $filetype ? $filetype : 'mp3' );
			$widthlimit = 743;
			$heightlimit = 420;
			$size = ' small';
			$onclick = 'embedAudio(this,\''.$widthlimit.'\',\''.$heightlimit.'\',\''.$media.'\')';
			$controls = true;
			break;
		// Video ----------------------------------------------------------------------------------
		case 'video':
			$type = '';
			$filetype = ( $filetype ? $filetype : 'mov' );
			$widthlimit = 743;
			$heightlimit = 420;
			$size = ' small';
			$onclick = 'embedVideo(this,\''.$widthlimit.'\',\''.$heightlimit.'\',\''.$media.'\')';
			$controls = true;
			break;
		// File -----------------------------------------------------------------------------------
		case 'file':
			$type = '';
			$filetype = ( $filetype ? $filetype : 'txt' );
			$widthlimit = 743;
			$heightlimit = 700;
			$size = ' small';
			$onclick = 'embedFile(this,\''.$widthlimit.'\',\''.$heightlimit.'\')';
			break;
		// Picture --------------------------------------------------------------------------------
		case 'picture':
			$filetype = ( $filetype ? $filetype : 'jpg' );
			$widthlimit = 743;
			$heightlimit = 420;
			$size = ( $widthlimit <= $width ? ' big' : ' small' );
			break;
		// Site -----------------------------------------------------------------------------------
		case 'site':
			$widthlimit = 743;
			$heightlimit = 420;
			$size = ( $widthlimit <= $width ? ' big' : ' small' );
			break;
	}
	
	//$mode = ( $widthlimit ? ( 'max-width:100%;' ) : '' ) . ( $heightlimit ? ( 'max-height:100%;' ) : '' ) . ( $width ? ( 'width:' . $width . 'px;' ) : '' ) . ( $height ? ( 'height:' . $height . 'px;' ) : '' ) . 'background-position:center center;background-repeat:no-repeat;background-size:cover;';
	$mode = 'max-width:100%;max-height:100%;width:100%;height:100%;background-position:center center;background-repeat:no-repeat;background-size:cover;';
	
	if( $source )
	{
		if( $thumb )
		{
			$url = explode( '/', $thumb );
			$filename = end( $url );
			$filepath = str_replace( $filename, '', $thumb );
			
			if( function_exists( 'libraryThumbs' ) && libraryThumbs( $thumb ) )
			{
				$image = true;
			}
			else
			{
				$thumb = ( function_exists( 'libraryIcons' ) && libraryIcons( $filetype, 128 ) ? ( 'subether/gfx/icons/' . libraryIcons( $filetype, 128 ) ) : '' );
				$width = 128;
				$height = 128;
			}
		}
		
		$str .= '<div class="filewrapper ' . $type . $size . ( $filetype ? ( ' ' . $filetype ) : '' ) . ( $image ? ( ' thumb' ) : '' ) . '">';
		
		if( $type == 'site' && $image || $type != 'site' )
		{
			$str .= '<div class="image ' . $type . $size . ( $filetype ? ( ' ' . $filetype ) : '' ) . ( $image ? ( ' thumb' ) : '' ) . '" ' . ( $thumb ? 'thumb="' . $thumb . '" ' : '' ) . ( $onclick ? ( 'link="' . $source . '" onclick="' . $onclick . '"' ) : '' ) . '>';
			if( $thumb )
			{
				$str .= '<div class="imagecontainer" style="background-image:url(\'' . $thumb . '\');' . $mode . '">';
				if( !$onclick )
				{
					$str .= '<a href="' . $source . '" target="_blank">';
					$str .= '<img style="visibility:hidden;max-width:100%;max-height:100%;height:auto;' . ( $width ? ( 'width:' . $width . 'px;' ) : '' ) . '" class="hidden" src="' . $thumb . '"/>';
					$str .= '</a>';
				}
				else
				{
					$str .= '<img style="visibility:hidden;max-width:100%;max-height:100%;height:auto;' . ( $width ? ( 'width:' . $width . 'px;' ) : '' ) . '" class="hidden" src="' . $thumb . '"/>';
				}
				$str .= '</div>';
			}
			if( $controls && $image )
			{
				$str .= '<em></em>';
			}
			$str .= '</div>';
		}
		
		if( $title || $description )
		{
			$str .= '<div class="text">';
			if( $title )
			{
				$str .= '<h3><a href="' . $source . '" target="_blank">' . $title . '</a></h3>';
			}
			if( $description )
			{
				$str .= '<p><a href="' . $source . '" target="_blank">' . $description . '</a></p>';
			}
			if( $domain && $description )
			{
				$str .= '<p class="url"><a href="http://' . str_replace( array( 'http://', 'https://' ), array( '', '' ), $domain ) . '" target="_blank">' . $domain . '</a></p>';
			}
			$str .= '</div>';
		}
		
		$str .= '</div>';
	}
	
	return $str;
}


function WallAlbum( $files, $pid, $images = false, $parent = false )
{
	$str = '';
	
	// Add dependecy if it can't find it for no good reason ...
	if( file_exists( BASE_DIR . '/subether/components/library/include/functions.php' ) )
	{
		include_once ( BASE_DIR . '/subether/components/library/include/functions.php' );
	}
	
	if( $files && is_array( $files ) )
	{
		$i = 1; $limit = 5;
		
		$pattern = '';
		
		foreach( $files as $f )
		{
			if( $i > $limit ) continue;
			
			$image = ''; $thumb = ''; $onclick = ''; $format = ''; $widthlimit = ''; $heightlimit = ''; $controls = ''; $bg = ''; $nail = '';
			
			$title = $f->FileName;
			$file = $f->FileID;
			$thumb = ( isset( $images[$f->FileID] ) ? $images[$f->FileID]->DiskPath : false/*$f->FilePath*/ );
			$source = ( $thumb ? $thumb : ( $f->DiskPath ? $f->DiskPath : $f->FilePath ) );
			$type = $f->MediaType;
			$media = $f->Media;
			$folder = $f->FileFolder;
			$width = $f->FileWidth;
			$height = $f->FileHeight;
			
			$mediaformat = ( strstr( $f->MediaFormat, '_' ) ? explode( '_', $f->MediaFormat ) : $f->MediaFormat );
			$mediapattern = ( is_array( $mediaformat ) ? $mediaformat[0] : $mediaformat );
			
			$filetype = strtolower( end( explode( '.', $f->FileName ) ) );
			
			// Overwrite album pattern based on first image
			$pattern = ( $pattern ? $pattern : $mediapattern );
			
			switch( $type )
			{
				// Audio ----------------------------------------------------------------------------------
				case 'audio':
					$type = 'other';
					$thumb = '';
					$filetype = ( $filetype ? $filetype : 'mp3' );
					$widthlimit = 743;
					$heightlimit = 420;
					//$format = ( ' ' . ( is_array( $mediaformat ) ? ( $mediaformat[0] . ' ' . implode( '_', $mediaformat ) ) : $mediaformat ) );
					$format = ( ' ' . $pattern );
					$onclick = 'embedAudio(this,\''.$widthlimit.'\',\''.$heightlimit.'\',\''.$media.'\')';
					$controls = true;
					break;
				// Video ----------------------------------------------------------------------------------
				case 'video':
					$type = 'other';
					$filetype = ( $filetype ? $filetype : 'mov' );
					$widthlimit = 743;
					$heightlimit = 420;
					//$format = ( ' ' . ( is_array( $mediaformat ) ? ( $mediaformat[0] . ' ' . implode( '_', $mediaformat ) ) : $mediaformat ) );
					$format = ( ' ' . $pattern );
					$onclick = 'embedVideo(this,\''.$widthlimit.'\',\''.$heightlimit.'\',\''.$media.'\')';
					$controls = true;
					break;
				// File -----------------------------------------------------------------------------------
				case 'file':
					$type = 'other';
					$thumb = '';
					$filetype = ( $filetype ? $filetype : 'txt' );
					$widthlimit = 743;
					$heightlimit = 700;
					//$format = ( ' ' . ( is_array( $mediaformat ) ? ( $mediaformat[0] . ' ' . implode( '_', $mediaformat ) ) : $mediaformat ) );
					$format = ( ' ' . $pattern );
					$onclick = 'embedFile(this,\''.$widthlimit.'\',\''.$heightlimit.'\')';
					break;
				// Picture --------------------------------------------------------------------------------
				case 'album':
				case 'image':
					$filetype = ( $filetype ? $filetype : 'jpg' );
					$widthlimit = 743;
					$heightlimit = 420;
					//$format = ( ' ' . ( is_array( $mediaformat ) ? ( $mediaformat[0] . ' ' . implode( '_', $mediaformat ) ) : $mediaformat ) );
					$format = ( ' ' . $pattern );
					$onclick = 'openFullscreen( \'Library\', \'' . $pid . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( \'' . (string)$file . '\', true ); }, \'wall\' )';
					break;
			}
			
			// Not use this contain method for now ...
			if( 1!=1 && $pattern != $mediapattern )
			{
				$bg = ' contain';
				$mode = 'max-width:100%;max-height:100%;width:100%;height:100%;background-position:left top;background-repeat:no-repeat;background-size:contain;';
				//$mode = ( $width ? ( 'width:' . $width . 'px;' ) : '' ) . ( $height ? ( 'height:' . $height . 'px;' ) : '' ) . 'background-position:left top;background-repeat:no-repeat;background-size:contain;';
			}
			else
			{
				$bg = ' cover';
				$mode = ( $widthlimit ? ( 'max-width:100%;' ) : '' ) . ( $heightlimit ? ( 'max-height:100%;' ) : '' ) . ( $width ? ( 'width:' . $width . 'px;' ) : '' ) . ( $height ? ( 'height:' . $height . 'px;' ) : '' ) . 'background-position:center center;background-repeat:no-repeat;background-size:cover;';
			}
			
			
			if( $source )
			{
				if( $thumb )
				{
					$url = explode( '/', $thumb );
					$filename = end( $url );
					$filepath = str_replace( $filename, '', $thumb );
					
					if( function_exists( 'libraryThumbs' ) && libraryThumbs( $thumb ) )
					{
						$image = true;
					}
					else
					{
						//$thumb = ( libraryIcons( $filetype, 128 ) ? ( 'subether/gfx/icons/' . libraryIcons( $filetype, 128 ) ) : '' );
						//$width = 128;
						//$height = 128;
						$thumb = false;
					}
				}
				
				$str .= '<div class="filewrapper ' . $type . $format . $bg . ( $filetype ? ( ' ' . $filetype ) : '' ) . ( $image ? ( ' thumb' ) : '' ) . ' nr' . $i . ' total' . ( count( $files ) > $limit ? $limit : count( $files ) ) . '">';
				
				$str .= '<div class="image ' . $type . $format . $bg . ( $filetype ? ( ' ' . $filetype ) : '' ) . ( $image ? ( ' thumb' ) : '' ) . ' nr' . $i . ' total' . ( count( $files ) > $limit ? $limit : count( $files ) ) . '" link="' . $source . '" ' . ( $onclick ? ( 'onclick="' . $onclick . '"' ) : '' ) . '>';
				
				if( $parent )
				{
					if( strstr( $parent->agent, 'mobile' ) )
					{
						$nail = '500x1000';
					}
					else
					{
						$nail = '1500x2000';
					}
					
					if( isset( $_REQUEST['christesting'] ) )
					{
						die( print_r( $parent->agent,1 ) . ' -- 500x500' );
					}
				}
				
				if( $thumb )
				{
					$str .= '<div class="imagecontainer" style="background-image:url(\'' . $thumb . $nail . '\');' . $mode . '">';
					$str .= '<img style="visibility:hidden;max-width:100%;max-height:100%;height:auto;' . ( $width ? ( 'width:' . $width . 'px;' ) : '' ) . '" class="hidden" src="' . $thumb . $nail . '"/>';
					if( count( $files ) > $limit && $i == $limit )
					{
						$str .= '<div class="morenum">+' . ( count( $files ) - $limit ) . '</div>';
					}
					$str .= '</div>';
				}
				if( $controls && $image )
				{
					$str .= '<em></em>';
				}
				
				$str .= '</div>';
				
				if( $title && !$image )
				{
					$str .= '<div class="text">';
					$str .= '<h3><a href="' . $source . '" target="_blank">' . $title . '</a></h3>';
					$str .= '</div>';
				}
				
				$str .= '</div>';
			}
			
			$i++;
		}
		
		//die( print_r( $files,1 ) . ' --' );
	}
	
	return $str;
}

function GetUserGroupOptions()
{
	global $database, $webuser;
	
	$group = $database->fetchObjectRow( '
		SELECT
			*
		FROM
			SBookCategory
		WHERE
				Type = "Group"
			AND Name = "Groups"
	' );
	
	if ( IsSystemAdmin() )
	{
		$q = '
			SELECT * FROM 
			( 
				( 
					SELECT 
						c.*,
						"" AS UserID,
						"" AS RelationID 
					FROM 
						SBookCategory c 
					WHERE 
							c.CategoryID = \'' . $group->ID . '\' 
						AND c.Type = "SubGroup" 
						AND c.IsSystem = "0"
						AND c.NodeID = "0"
						AND c.NodeMainID = "0" 
				) 
				UNION 
				( 
					SELECT 
						c.*, 
						r.ObjectID as UserID, 
						r.ID as RelationID 
					FROM 
						SBookCategory c, 
						SBookCategoryRelation r 
					WHERE 
							r.ObjectType = "Users" 
						AND r.ObjectID = \'' . $webuser->ID . '\' 
						AND c.CategoryID = \'' . $group->ID . '\' 
						AND c.Type = "SubGroup"
						AND c.IsSystem = "0" 
						AND c.ID = r.CategoryID  
				) 
			) z
			GROUP BY
				z.ID 
			ORDER BY 
				z.ID ASC 
		';
	}
	else
	{
		$q = '
			SELECT 
				c.*, 
				r.ObjectID as UserID, 
				r.ID as RelationID 
			FROM 
				SBookCategory c, 
				SBookCategoryRelation r 
			WHERE 
					r.ObjectType = "Users" 
				AND r.ObjectID = \'' . $webuser->ID . '\' 
				AND c.CategoryID = \'' . $group->ID . '\' 
				AND c.Type = "SubGroup"
				AND c.IsSystem = "0" 
				AND c.ID = r.CategoryID 
			ORDER BY 
				r.SortOrder ASC, 
				c.ID ASC 
		';
	}
	
	$ostr = '';
	
	$ostr .= '<option value="' . getWallID() . '">' . i18n( 'i18n_Post to your own profile' ) . '</option>';
	$ostr .= '<option value="' . getWallID() . '">- - -</option>';
	
	if ( $group->ID > 0 && ( $rows = $database->fetchObjectRows( $q ) ) )
	{
		//$cataccess = CategoryAccess( $webuser->ContactID, false, -1, 'IsAdmin' );
		
		if ( 1==1/*$cataccess*/ )
		{
			foreach ( $rows as $r )
			{
				//if ( !isset( $cataccess[$r->ID] ) || $r->ParentID > 0 ) continue;
				
				if( isset( $_SESSION['wall_default_categoryid'] ) && $_SESSION['wall_default_categoryid'] )
				{
					$s = ( $_SESSION['wall_default_categoryid'] == $r->ID ? ' selected="selected"' : '' );
				}
				else
				{
					$s = ( defined( 'WALL_DEFAULT_CATEGORYID' ) && WALL_DEFAULT_CATEGORYID == $r->ID ? ' selected="selected"' : '' );
				}
				
				$ostr .= '<option value="' . $r->ID . '"' . $s . '>' . i18n( 'i18n_Post to' ) . ' ' . $r->Name . '</option>';
			}
		}
	}
	
	return $ostr;
}

?>
