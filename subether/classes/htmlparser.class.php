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

class SimpleHTML
{
	var $Document;
	
	public function __construct( $string )
	{
		//die( $string . ' --' );
		//$this->Document = $string;
		//if ( strstr( $string, '<video' ) ) die( $string . ' --' );
		// Remove scripts, styles and comments (not tag attributes) and whitespace
		$string = preg_replace( '/(\<script[^>]*?\>)([\w\W]*?)(\<\/script[^>]*?\>)/i', '$1$3',  $string );
		$string = preg_replace( '/(\<style[^>]*?\>)([\w\W]*?)(\<\/style[^>]*?\>)/i', '$1$3',  $string );
		$string = preg_replace( '/\<\!\-\-[\w\W]*?\-\-\>/i', '', $string );
		$string = preg_replace( '/\>[\ |\t|\r]+/i', '>', $string ); // After tags
		$string = preg_replace( '/[\ |\t|\r]*?\</i', '<', $string ); // Before tags
		$string = preg_replace( '/\>[\n\t]+/i', ">\n", $string ); // multiple \n After tags
		$string = preg_replace( '/[\n\t]*?\</i', "\n<", $string ); // multiple \n Before tags
		
		//die( $string . ' --' );
		
		$string = explode( '>', $string );
		//die( print_r( $string, 1 ) . ' --' );
		foreach( $string as $key=>$doc )
		{
			//if ( strstr( $doc, 'img class=' ) )
			//{
				//die( $doc . ' --' );
			//}
			$string[$key] = $doc . '>';
			if( !strstr( $doc, '</' ) )
			{
				if( strstr( $string[$key], '/>' ) )
				{
					$string[$key] = str_replace( '/>', ' nodeid="' . ( $key + 1 ) . '"/>', $string[$key] );
				}
				else
				{
					$string[$key] = str_replace( '>', ' nodeid="' . ( $key + 1 ) . '">', $string[$key] );
				}
				$string[$key] = preg_replace( '/\s+/', ' ', $string[$key] );
				if ( $string[$key][0] == ' ' )
				{
					$string[$key][0] = str_replace( ' ', "\n", $string[$key][0] );
				}
			}
		}
		//die( print_r( $string,1 ) . ' --' );
		$this->Document = implode( '', $string );
		//die( $this->Document . ' --' );
	}
	
	public function getElementById( $id )
	{
		/*preg_match_all( '/(<.*?id="' . $id . '"[^>]*?>)/i', $this->Document, $matches );*/
		//preg_match_all( '/\<?.* id="' . $id . '"[^>]*?\>(.*?)<\/' . $tag . '>/s', $this->Document, $matches );
		//return $matches;
		return $this->getElementsByTagName( '*', $id );
	}
	
	public function getElementsByTagName( $tag, $id = false )
	{
		$out = array();
		foreach( $this->tags( $tag ) as $t )
		{	
			preg_match_all( '/(<' . $t . ( $id ? '.*?id\=\"' . $id . '\"' : '' ) . '[^>]*?>)/i', $this->Document, $matches );
			//if ( $tag == 'video' ) die( print_r( $matches,1 ) . ' --' );
			//die( print_r( $matches,1 ) . ' ..' );
			//preg_match_all( '/\<' . $t . ( $id ? '.*?id\=\"' . $id . '\"' : '' ) . '[^>]*?\>(.*?)<\/' . $t . '>/s', $this->Document, $matches );
			//preg_match_all( '/\<' . $t . [^>]*?\>(.*?)<\/' . $t . '>/s', $this->Document, $matches );
			//preg_match_all( '/\<' . $t . '[^>]*?\>/i', $this->Document, $matches );
			//preg_match_all( '/<' . $t . '>(.*?)<\/' . $t . '>/s', $this->Document, $matches );
			//die( print_r( $matches,1 ) . ' -- ' . $this->Document );
			if( $matches[0] )
			{
				foreach( $matches[0] as $k=>$m )
				{
					$attr = array();
					$m = str_replace( ' = ', '=', $m );
					$tagdata = explode( '>', $m );
					$squote = explode( "'", $tagdata[0] );
					//die( print_r( $squote,1 ) . ' --' );
					$dquote = explode( '"', $tagdata[0] );
					if( isset( $squote[1] ) )
					{
						foreach( $squote as $sq )
						{
							$attr[] = $sq;
						}
					}
					if( isset( $dquote[1] ) )
					{
						foreach( $dquote as $dq )
						{
							$attr[] = $dq;
						}
					}
					//if( $k == 15 ) die( print_r( $attr,1 ) . ' -- ' . print_r( $matches[0],1 ) );
					$obj = new stdClass();
					$obj->tagName = $t;
					//$obj->tagData = $tagdata[0].'>';
					$obj->tagData = 'TagData String';
					if( trim($attr[1]) )
					{
						$obj->tagAttributes = array(); $i = false;
						foreach( $attr as $ky=>$at )
						{
							$attr[$ky] = trim( $at );
							$atr = explode( ' ', $attr[($i?$i:$ky)] );
							//die( print_r( $atr,1 ) . ' --' );
							$atr = ( $atr[1] ? trim( end( $atr ) ) : trim( $atr[0] ) );
							$chk = explode( '=', trim( $atr ) );
							//die( $atr . ' .. ' . print_r( $chk,1 ) . ' --' );
							
							if( strstr( $atr, '=' ) && !trim( $chk[1] ) && trim( $attr[($i?$i:$ky)+1] ) )
							{
								$obj->tagAttributes[str_replace( '=', '', $atr )] = trim($attr[($i?$i:$ky)+1]);
							}
							/*if( trim( $atr[1] ) && trim( $attr[($i?$i:$ky)+1] ) )
							{
								$obj->tagAttributes[trim($atr[1])] = trim($attr[($i?$i:$ky)+1]);
							}*/
							$i = ( ($i?$i:$ky) + 1 );
							
						}
						//die( print_r( $attr,1 ) . ' -- ' . print_r( $obj->tagAttributes,1 ) );
					}
					if( $obj->tagAttributes['nodeid'] )
					{
						$obj->nodeid = $obj->tagAttributes['nodeid'];
					}
					if( $obj->tagAttributes['id'] )
					{
						$obj->id = $obj->tagAttributes['id'];
					}
					if( $obj->tagAttributes['class'] )
					{
						$obj->className = $obj->tagAttributes['class'];
					}
					if( $obj->tagAttributes['name'] )
					{
						$obj->name = $obj->tagAttributes['name'];
					}
					if( $obj->tagAttributes['value'] )
					{
						$obj->value = $obj->tagAttributes['value'];
					}
					//$obj->innerHTML = $matches[1][$k];
					$obj->innerHTML = 'innerHTML String';
					//$obj->innerHTML = $this->innerHTML( $t, $obj->tagAttributes['nodeid'] );
					$out[] = $obj;
				}
			}
		}
		return $out;
	}
	
	public function innerHTML( $tag, $nodeid )
	{
		if( !$tag || !$nodeid ) return false;
		
		preg_match_all( '/\<' . $tag . '.*?nodeid\=\"' . $nodeid . '\"[^>]*?\>[\w\W]+/i', $this->Document, $matches );
		
		//die( print_r( $matches,1 ) . ' .. ' . $nodeid );	
		// $html is <div ...>.... (so including match)
		$html = $matches[0][0];
		$mode = 0;
		$out = '';
		$tagstart = '<'.$tag;
		$tagstartl = strlen ( $tagstart );
		$tagend = '</'.$tag;
		$tagendl = strlen ( $tagend );
		$htmll = strlen ( $html );
		
		// Loop through part to find innerhtml
		for ( $a = 0; $a < $htmll; $a++ )
		{
			if ( $mode == 0 && substr ( $html, $a, $tagstartl ) == $tagstart )
			{
				while ( $html{$a++} != '>' )
					continue;
				$a--;
				$start = $mode++;
				continue;
			}
			else if ( $mode > 0 )
			{
				// Another nested tag of same type
				if ( substr ( $html, $a, $tagstartl ) == $tagstart )
				{
					$start = $mode++;
				}
				// An end of the type of tag
				else if ( substr ( $html, $a, $tagendl ) == $tagend )
				{
					$end = $mode--;
					// End of input
					if ( $mode == 0 )
						break;
				}
				$out .= $html{$a};
			}
		}
		
		return $out;
	}
	
	private function getAttribute( $att, $tag )
	{
		$re = '/' . preg_quote( $att ) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
		if( preg_match( $re, $tag, $match ) )
		{
			return urldecode( $match[2] );
		}
		return false;
	}
	
	private function tags( $tag )
	{
		$tags = array(
			'a', 'abbr', 'acronym', 'address', 'applet', 'area', 'article', 'aside', 'audio', 'b', 'base',
			'basefont', 'bdi', 'bdo', 'big', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'center',
			'cite', 'code', 'col', 'colgroup', 'datalist', 'dd', 'del', 'details', 'dfn', 'dialog', 'dir', 'div',
			'dl', 'dt', 'em', 'embed', 'fieldset', 'figcaption', 'figure', 'font', 'footer', 'form', 'frame', 'frameset',
			'head', 'header', 'hgroup', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'html', 'i', 'iframe', 'img', 'input',
			'ins', 'kbd', 'keygen', 'label', 'legend', 'li', 'link', 'main', 'map', 'mark', 'menu', 'menuitem', 'meta',
			'meter', 'nav', 'noframes', 'noscript', 'object', 'ol', 'optgroup', 'option', 'output', 'p', 'param', 'pre',
			'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp', 'script', 'section', 'select', 'small', 'source', 'span',
			'strike', 'strong', 'style', 'sub', 'summary', 'sub', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead',
			'time', 'title', 'tr', 'track', 'tt', 'u', 'ul', 'var', 'video', 'wbr'
		);
		return ( $tag != '*' ? array( $tag ) : $tags );
	}
}

?>
