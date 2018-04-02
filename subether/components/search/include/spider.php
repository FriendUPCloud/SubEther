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

global $database;

$time_start = microtime_float();

if( isset( $_REQUEST[ 'die' ] ) || isset( $_REQUEST[ 'q' ] ) && isset( $_REQUEST[ 'a' ] ) && $_REQUEST[ 'a' ] == 'submit' )
{
	
	function updateSearchEntry ( $r )
	{
		if( !$r ) return false;
		
		$doc = new DOMDocument();
		@$doc->loadHTML( $r );
		
		//$r = preg_replace( '/<script[^>]*>.*?< *script[^>]*>/i', '', $r );
		//$r = preg_replace( '/< *script[^>]*>(.*?)<\/script *>/i', ' ', $r );
		//$r = preg_replace( '/^\s+|\n|\r|\s+$/m', '', $r );
		
		//$r = preg_replace( '/[ \t]+/', ' ', preg_replace( '/\s*$^\s*/m', "\n", $r ) );
		//$r = preg_replace( '/^\s+|\n|\r|\s+$/m', ' ', $r );
		
		$d = new stdClass();
		
		/*ob_clean ();*/
		if ( preg_match ( '/\<title\>([\w\W]*?)\<\/title\>/i', $r, $m ) )
		{
			$d->Title = trim ( strip_tags ( $m[1] ) );
		}
		if ( preg_match ( '/\<meta name\=\"description\" content\=\"([^"]*?)\"/i', $r, $desc ) )
		{
			$d->Description = trim ( strip_tags ( $desc[1] ) );
		}
		if ( preg_match ( '/\<h1[^>]*?\>[\w\W]*?\<\/h1\>([\w\W]*)/i', $r, $h ) )
		{
			$h = trim ( preg_replace ( '/\<li[^>]*?\>[\w\W]*?\<\/li\>/i', '', $h[1] ) );
			$h = strip_html_tags( $h );
			$h = preg_replace( '/[ \t]+/', ' ', preg_replace( '/\s*$^\s*/m', ' ', $h ) );
			$d->Leadin = trim( $h );
			//$d->Leadin = trim ( $h );
			/*if ( trim ( strip_tags ( $h ) ) )
			{
				$d->Leadin = trim ( strip_tags ( $h ) );
			}*/
		}
		else if ( preg_match ( '/\<h2[^>]*?\>[\w\W]*?\<\/h2\>([\w\W]*)/i', $r, $h ) )
		{
			$h = trim ( preg_replace ( '/\<li[^>]*?\>[\w\W]*?\<\/li\>/i', '', $h[1] ) );
			$h = strip_html_tags( $h );
			$h = preg_replace( '/[ \t]+/', ' ', preg_replace( '/\s*$^\s*/m', ' ', $h ) );
			$d->Leadin = trim( $h );
			//$d->Leadin = trim ( $h );
			/*if ( trim ( strip_tags ( $h ) ) )
			{
				$d->Leadin = trim ( strip_tags ( $h ) );
			}*/
		}
		else if ( preg_match ( '/\<h3[^>]*?\>[\w\W]*?\<\/h3\>([\w\W]*)/i', $r, $h ) )
		{
			$h = trim ( preg_replace ( '/\<li[^>]*?\>[\w\W]*?\<\/li\>/i', '', $h[1] ) );
			$h = strip_html_tags( $h );
			$h = preg_replace( '/[ \t]+/', ' ', preg_replace( '/\s*$^\s*/m', ' ', $h ) );
			$d->Leadin = trim( $h );
			//$d->Leadin = trim ( $h );
			/*if ( trim ( strip_tags ( $h ) ) )
			{
				$d->Leadin = trim ( strip_tags ( $h ) );
			}*/
		}
		else if ( preg_match ( '/\<strong[^>]*?\>[\w\W]*?\<\/strong\>([\w\W]*)/i', $r, $h ) )
		{
			$h = trim ( preg_replace ( '/\<li[^>]*?\>[\w\W]*?\<\/li\>/i', '', $h[1] ) );
			$h = strip_html_tags( $h );
			$h = preg_replace( '/[ \t]+/', ' ', preg_replace( '/\s*$^\s*/m', ' ', $h ) );
			$d->Leadin = trim( $h );
			//$d->Leadin = trim ( $h );
			/*if ( trim ( strip_tags ( $h ) ) )
			{
				$d->Leadin = trim ( strip_tags ( $h ) );
			}*/
		}
		
		//$r = trim( strip_tags( $r, '<a>' ) );
		
		/*if( preg_match_all ( "/a[\s]+[^>]*?href[\s]?=[\s\"\']+".
					"(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/a>/", 
					$r, &$a ) )
		{
			$d->Links = $a[1];
			$d->KeyWords = $a[2];
		}*/
		
		if( $doc )
		{
			$links = array();
			$keywords = array();
	 		
	 		//die( print_r( $doc->getElementsByTagName( 'a' )[0]->getAttribute( 'href' ) ) . ' ..' );
	 		
	 		foreach( $doc->getElementsByTagName( 'h1' ) as $keyword )
			{
				if( !in_array( trim( $keyword->nodeValue ), $keywords ) )
				{
					$keywords[] = trim( $keyword->nodeValue );
				}
			}
			foreach( $doc->getElementsByTagName( 'h2' ) as $keyword )
			{
				if( !in_array( trim( $keyword->nodeValue ), $keywords ) )
				{
					$keywords[] = trim( $keyword->nodeValue );
				}
			}
			foreach( $doc->getElementsByTagName( 'h3' ) as $keyword )
			{
				if( !in_array( trim( $keyword->nodeValue ), $keywords ) )
				{
					$keywords[] = trim( $keyword->nodeValue );
				}
			}
	 		
			foreach( $doc->getElementsByTagName( 'a' ) as $link )
			{
				if( !in_array( trim( $link->getAttribute( 'href' ) ), $links ) 
				&& strstr( trim( $link->getAttribute( 'href' ) ), 'http' )
				&& trim( $link->getAttribute( 'href' ) ) )
				{
					$links[] = trim( $link->getAttribute( 'href' ) );
					
					if( !in_array( trim( $link->nodeValue ), $keywords ) 
					&& trim( $link->nodeValue ) 
					&& $link->nodeValue != '0' )
					{
						$keywords[] = trim( $link->nodeValue );
					}
				}
				//$links[] = array( 'url' => $link->getAttribute( 'href' ), 'text' => $link->nodeValue );
			}
			
			/*$d->Links = $links;
			$d->KeyWords = $keywords;*/
			
			$d->Links = implode( ', ', $links );
			$d->KeyWords = implode( ', ', $keywords );
		
		}
		
		/*ob_clean ();*/
		
		return $d;
	}
	
	/*$q = LinkFilter( $_REQUEST[ 'q' ] );*/
	$q = $_REQUEST[ 'q' ];
	
	if( !$_REQUEST[ 'die' ] && !$database->fetchRow( 'SELECT * FROM SEngineSearch WHERE Link = \'' . $q . '\' ORDER BY ID DESC' ) )
	{		
		$r = getContentFast( $q );
		
		$d = StringFilter( $r );
		
		// Store this link and engine data
		if( $r && $d )
		{
			if( !$database->fetchRow( 'SELECT * FROM SEngineLinks WHERE Link = \'' . $d->Link . '\' ORDER BY ID DESC' ) )
			{
				// Store engine data
				$database->query( '
					INSERT INTO SEngineSearch ( Title, Link, Description, Leadin, Links, KeyWords, DateModified, DateCreated ) 
					VALUES ( \'' . $d->Title . '\', \'' . $d->Link . '\', \'' . $d->Description . '\', 
					\'' . $d->Leadin . '\', \'' . $d->Links . '\', \'' . $d->KeyWords . '\', 
					\'' . date( 'Y-m-d H:i:s' ) . '\', \'' . date( 'Y-m-d H:i:s' ) . '\' ) 
				' );
				// Store link
				$database->query( '
					INSERT INTO SEngineLinks ( Link, Links, KeyWords, IsParsed, DateModified, DateCreated ) 
					VALUES ( \'' . $d->Link . '\', \'' . $d->Links . '\', \'' . $d->KeyWords . '\', "1", 
					\'' . date( 'Y-m-d H:i:s' ) . '\', \'' . date( 'Y-m-d H:i:s' ) . '\' ) 
				' );
			}
		}
	}
	
	if( isset( $_REQUEST[ 'die' ] ) )
	{
		die( print_r( BingParser( $q, '5', '3' ),1 ) . ' .. ---' );
		//die( print_r( $d,1 ) . ' -- ' .  $q );
	}
	
}

?>
