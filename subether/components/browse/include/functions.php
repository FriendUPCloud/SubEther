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

function YouTubeParser ( $url = false, $limit = false, $page = false, $counter = false )
{
	$tubeurl = ( $url ? 'https://www.youtube.com/results?search_query=' . $url : 'https://www.youtube.com' );
	
	$str = GetCurlContentByUrl( $tubeurl );
	
	// Parse the document -----------------------------------------------------------
	$doc = new DOMDocument();
	@$doc->loadHTML( $str );
	
	//$doc = new SimpleHTML( $str );
	
	//if( isset( $_REQUEST['chris'] ) ) die( /*$str . ' -- ' . $tubeurl . ' [] ' . */print_r( $doc,1 ) );
	
	if( $doc )
	{
		$data = array();
		
		//$out = array();
		
		/*if( $act = $doc->getElementByID( 'content' ) )
		{
			foreach( $act as $a )
			{
				$out[] = $doc->innerHTML( '*', $a->nodeid );
			}
		}*/
		
		// TODO: Fix this later .... youtube has changed stuff, this needs update ...
		
		//if( isset( $_REQUEST['chris'] ) ) die( print_r( $act,1 ) . ' -- ' . print_r( $out,1 ) );
		
		//if ( $act = $doc->getElementByID( 'gh-activityfeed' ) )
		if ( $act = $doc->getElementByID( 'content' ) )
		{
			// Count
			foreach( $act->getElementsByTagName( 'p' ) as $p )
			{
				if( $p->getAttribute( 'class' ) == 'num-results' )
				{
					foreach( $p->getElementsByTagName( 'strong' ) as $strong )
					{
						$data['count'] = trim( $strong->nodeValue );
					}
				}
			}
		
			// Pages
			foreach( $act->getElementsByTagName( 'div' ) as $div )
			{
				if( $div->getAttribute( 'role' ) == 'navigation' )
				{
					$data['pages'] = array();
				
					foreach( $div->getElementsByTagName( 'span' ) as $span )
					{
						if( $span->getAttribute( 'class' ) == 'yt-uix-button-content' )
						{
							$data['pages'][] = utf8_decode( trim( $span->nodeValue ) );
						}
					}
				}
			}
		
			foreach( $act->getElementsByTagName( 'li' ) as $li )
			{
				$obj = new stdClass();
			
				// Title
				foreach( $li->getElementsByTagName( 'h3' ) as $h3 )
				{
					$obj->title = utf8_decode( trim( $h3->nodeValue ) );
				}
			
				foreach( $li->getElementsByTagName( 'a' ) as $link )
				{
					// Link
					if( strstr( $link->getAttribute( 'href' ), 'watch?v=' ) )
					{
						$oid = ObjectFromString( $link->getAttribute( 'href' ) );
						if( $oid ) $obj->ID = $oid->v;
						$obj->href = $link->getAttribute( 'href' );
					}
				
					foreach( $link->getElementsByTagName( 'span' ) as $span )
					{
						// Video Time
						if( $span->getAttribute( 'class' ) == 'video-time' )
						{
							$obj->duration = trim( $span->nodeValue );
						}
					}
				}
			
				foreach( $li->getElementsByTagName( 'div' ) as $content )
				{
					if( $content->getAttribute( 'class' ) == 'yt-lockup-content' )
					{
						$i = 1;
						foreach( $content->getElementsByTagName( 'div' ) as $div )
						{
							if( $div->getAttribute( 'class' ) == 'yt-lockup-meta' )
							{
								$ii = 1;
								foreach( $div->getElementsByTagName( 'li' ) as $meta )
								{
									// Channel
									foreach( $meta->getElementsByTagName( 'a' ) as $channel )
									{
										$obj->channel = $channel->getAttribute( 'href' );
										$obj->by = utf8_decode( trim( $channel->nodeValue ) );
									}
								
									// Added
									if( $ii == 2 && $meta->nodeValue )
									{
										$obj->added = utf8_decode( trim( $meta->nodeValue ) );
									}
								
									// Views
									if( $ii == 3 && $meta->nodeValue )
									{
										$obj->views = utf8_decode( trim( $meta->nodeValue ) );
									}
									$ii++;
								}
							}
						
							// Description
							if( $i == 2 )
							{
								if( $div->nodeValue )
								{
									$obj->description = utf8_decode( trim( $div->nodeValue ) );
								}
							}
							$i++;
						}
					}
				}
			
				// Thumbnail
				foreach( $li->getElementsByTagName( 'img' ) as $img )
				{
					if( strstr( $img->getAttribute( 'src' ), '.jpg' ) )
					{
						$obj->thumb = $img->getAttribute( 'src' );
					}
				}
			
				if( $obj->href && $obj->title )
				{
					if( !$obj->thumb )
					{
						$obj->thumb = '//i1.ytimg.com/vi/' . $obj->ID . '/mqdefault.jpg';
					}
					$data[] = $obj;
				}
			}
		}
		
		//if( isset( $_REQUEST['chris'] ) ) die( print_r( $data,1 ) . ' ..' );
		
		return $data;
	}
	return false;
}

function TorrentParser ( $url = false )
{
	$cats = array(
		/*'0'=>'all',*/ '100'=>'audio',
		'200'=>'video', '300'=>'applications',
		'400'=>'games'/*, '500'=>'porn'*/, '600'=>'other'
	);
	
	$out = array();
	foreach( $cats as $k=>$v )
	{
		$xmlurl = 'http://rss.thepiratebay.se/' . $k;
		
		$xml = GetCurlContentByUrl( $xmlurl );
		
		$obj = simplexml_load_string( $xml );
		
		$out[] = $obj;
	}
	
	return $out;
}

function TorrentFeed ( $query = false )
{
	if( $query )
	{
		$cats = array( 0=>'All' );
	}
	else
	{
		$cats = array(
			'movies'=>'Movies', 'tv'=>'TV', 'music'=>'Music',
			'games'=>'Games', 'applications'=>'Applications',
			'books'=>'Books', 'anime'=>'Anime', 'other'=>'Other',
			/*'xxx'=>'XXX'*/
		);
	}
	
	$out = array();
	foreach( $cats as $k=>$v )
	{
		if( $query )
		{
			$url = 'http://kickass.so/usearch/' . $query . '/?rss=1';
		}
		else
		{
			$url = 'http://kickass.so/' . $k . '/?rss=1';
		}
		
		$str = GetCurlContentByUrl( $url );
		
		$doc = new DOMDocument();
		@$doc->loadXML( $str );
		
		$rss = new stdClass();
		
		foreach( $doc->getElementsByTagName( 'channel' ) as $channel )
		{
			foreach( $channel->childNodes as $e )
			{
				if( $e->childNodes && $e->nodeName == 'item' )
				{
					$obj = new stdClass();
					foreach( $e->childNodes as $c )
					{
						if( trim( $c->nodeValue ) )
						{
							$tag = explode( ':', $c->nodeName );
							$obj->{( $tag[1] ? $tag[1] : $tag[0] )} = trim( $c->nodeValue );
						}
					}
					$tag = explode( ':', $e->nodeName );
					$rss->{( $tag[1] ? $tag[1] : $tag[0] )}[] = $obj;
				}
				else if( trim( $e->nodeValue ) )
				{
					$tag = explode( ':', $e->nodeName );
					$rss->{( $tag[1] ? $tag[1] : $tag[0] )} = trim( $e->nodeValue );
				}
			}
		}
		$out[] = $rss;
	}
	//die( print_r( $out,1 ) . ' ..' );
	return $out;
}

function KickAssTorrentsParser ( $query = false )
{
	$url = 'http://kickass.to/' . ( $query ? ( 'usearch/' . $query ) : 'new' ) . '/';
		
	$str = GetCurlContentByUrl( $url );
	die( $str . ' ..' );
	// Parse the document -----------------------------------------------------------
	$doc = new DOMDocument();
	@$doc->loadHTML( $str );
	
	if( $doc )
	{
		$data = array();
		
		$main = $doc->getElementByID( 'mainSearchTable' );
		
		foreach( $main->getElementsByTagName( 'table' ) as $table )
		{
			if( strstr( $table->getAttribute( 'class' ), 'data' ) )
			{
				$i = 0;
				foreach( $table->getElementsByTagName( 'tr' ) as $tr )
				{
					$obj = new stdClass();
					
					if( $i > 0 )
					{
						$ii = 0;
						foreach( $tr->getElementsByTagName( 'td' ) as $td )
						{
							// Torrent description
							if( $ii == 0 )
							{
								foreach( $td->getElementsByTagName( 'div' ) as $div )
								{
									// Icons
									if( strstr( $div->getAttribute( 'class' ), 'iaconbox' ) )
									{
										foreach( $div->getElementsByTagName( 'a' ) as $link )
										{
											// Comments
											if( strstr( $link->getAttribute( 'class' ), 'icomment' ) )
											{
												$obj->commentlink = $link->getAttribute( 'href' );
												$obj->comments = $link->nodeValue;
											}
											// Verified
											if( strstr( $link->getAttribute( 'class' ), 'iverify' ) )
											{
												$obj->verified = '1';
											}
											// Magnet
											if( strstr( $link->getAttribute( 'class' ), 'imagnet' ) )
											{
												$obj->magnet = $link->getAttribute( 'href' );
											}
											// Download
											if( strstr( $link->getAttribute( 'class' ), 'idownload' ) )
											{
												$obj->download = $link->getAttribute( 'href' );
											}
										}
									}
									// Torrent
									if( strstr( $div->getAttribute( 'class' ), 'torrentname' ) )
									{
										foreach( $div->getElementsByTagName( 'a' ) as $link )
										{
											// Title
											if( strstr( $link->getAttribute( 'class' ), 'cellMainLink' ) )
											{
												$obj->torrentlink = $link->getAttribute( 'href' );
												$obj->title = $link->nodeValue;
											}
											// User
											if( strstr( $link->getAttribute( 'href' ), '/user/' ) )
											{
												$obj->userlink = $link->getAttribute( 'href' );
												$obj->user = $link->nodeValue;
											}
										}
										// Category
										foreach( $div->getElementsByTagName( 'span' ) as $span )
										{
											if( $span->getAttribute( 'id' ) )
											{
												$obj->category = $span->nodeValue;
											}
										}
									}
								}
							}
							// Torrent data
							else
							{
								// Size
								if( $ii == 1 && $td->nodeValue )
								{
									$obj->size = $td->nodeValue;
								}
								// Files
								if( $ii == 2 && $td->nodeValue )
								{
									$obj->files = $td->nodeValue;
								}
								// Age
								if( $ii == 3 && $td->nodeValue )
								{
									$obj->age = $td->nodeValue;
								}
								// Seed
								if( $ii == 4 && $td->nodeValue )
								{
									$obj->seed = $td->nodeValue;
								}
								// Leech
								if( $ii == 5 && $td->nodeValue )
								{
									$obj->leech = $td->nodeValue;
								}
							}
							$ii++;
						}
					}
					$i++;
					
					if( $obj )
					{
						$data[] = $obj;
					}
				}
			}
		}
	}
	
	die( print_r( $data,1 ) . ' ..' );
}

function WatchSeriesParser ( $query = false, $link = false )
{
	$url = 'http://watchseries.lt' . ( $query ? ( ( !$link ? '/search/' : '' ) . $query ) : '' );
		
	$str = GetCurlContentByUrl( $url );

	// Parse the document -----------------------------------------------------------
	$doc = new DOMDocument();
	@$doc->loadHTML( $str );
	
	if( $doc )
	{
		//die( $str . ' .. ' . $url );
		
		$data = array();
		
		if( $link && strstr( $query, 'http' ) )
		{
			// Include movies from Zmovie.tw
			return ZmovieParser( $query, 1 );
		}
		else if( $link && strstr( $query, '/movies/view/' ) )
		{
			// Include movies from Zmovie.tw
			$data[] = ZmovieParser( $query, 1 );
		}
		else if( $link && strstr( $query, '/open/cale/' ) )
		{
			foreach( $doc->getElementsByTagName( 'a' ) as $link )
			{
				if( $link->getAttribute( 'class' ) == 'myButton' && strstr( $link->getAttribute( 'href' ), 'http' ) )
				{
					$href = $link->getAttribute( 'href' );
				}
			}
			
			if( $href )
			{
				$str = GetCurlContentByUrl( $href );
				
				$vid = new DOMDocument();
				@$vid->loadHTML( $str );
				
				foreach( $vid->getElementsByTagName( 'input' ) as $input )
				{
					if( strstr( strtolower( $input->getAttribute( 'value' ) ), 'iframe' ) &&
					    strstr( strtolower( $input->getAttribute( 'value' ) ), 'src=' ) &&
					    strstr( strtolower( $input->getAttribute( 'value' ) ), 'http' ) &&
					    strstr( strtolower( $input->getAttribute( 'value' ) ), 'embed' ) )
					{
						$embed = $input->getAttribute( 'value' );
					}
				}
				foreach( $vid->getElementsByTagName( 'textarea' ) as $textarea )
				{
					if( strstr( strtolower( $textarea->nodeValue ), 'iframe' ) &&
					    strstr( strtolower( $textarea->nodeValue ), 'src=' ) &&
					    strstr( strtolower( $textarea->nodeValue ), 'http' ) &&
					    strstr( strtolower( $textarea->nodeValue ), 'embed' ) )
					{
						$embed = $textarea->nodeValue;
					}
				}
				
				if( !$embed )
				{
					return header( 'Location: ' . $href );
				}
			}
			
			//die( print_r( $embed,1 ) . ' .. ' );
			
			return $embed;
			
		}
		else if( $link && strstr( $query, '/episode/' ) )
		{
			// Render episode list
			foreach( $doc->getElementsByTagName( 'div' ) as $div )
			{
				if( strstr( $div->getAttribute( 'id' ), 'lang_' ) )
				{
					$obj = new stdClass();
					
					// Render language list
					foreach( $div->getElementsByTagName( 'h2' ) as $h2 )
					{
						if( $h2->getAttribute( 'class' ) == 'channel-title' )
						{
							$title = explode( ' -', $h2->nodeValue );
							// title
							$obj->title = $title[0];
						}
					}
					
					// Url
					$obj->url = 'http://watchseries.lt';
					
					// Assign items
					$obj->item = array();
					foreach( $div->getElementsByTagName( 'a' ) as $link )
					{
						$links = new stdClass();
						
						if( $link->getAttribute( 'class' ) == 'buttonlink' && $link->getAttribute( 'title' ) && strstr( $link->getAttribute( 'href' ), '/open/cale/' ) )
						{
							// title
							$links->name = $link->getAttribute( 'title' );
							// title
							$links->title = $link->getAttribute( 'title' );
							// href
							$links->href = $link->getAttribute( 'href' );
							
							$obj->item[] = $links;
						}
					}
					
					// Assign object to output array
					$data[] = $obj;
				}
			}
			
			//die( print_r( $data,1 ) . ' ..' );
		}
		else if( $link && strstr( $query, '/serie/' ) )
		{
			$i = 0;
			
			// Render season list
			foreach( $doc->getElementsByTagName( 'h2' ) as $h2 )
			{
				if( $h2->getAttribute( 'class' ) == 'lists' && trim( $h2->nodeValue ) != '' )
				{
					$obj = new stdClass();
					// title
					$obj->title = $h2->nodeValue;
					// Url
					$obj->url = 'http://watchseries.lt';
					// Assign object to output array
					$data[$i] = $obj;
					
					$i++;
				}
			}
			
			$ii = 0;
			
			// Render episode list
			foreach( $doc->getElementsByTagName( 'ul' ) as $ul )
			{
				if( strstr( $ul->getAttribute( 'class' ), 'episodeListings' ) )
				{
					$obj = new stdClass();
					
					// Assign items
					$data[$ii]->item = array();
					foreach( $ul->getElementsByTagName( 'a' ) as $link )
					{
						if( $link->getAttribute( 'title' ) && $link->getAttribute( 'href' ) && trim( $link->nodeValue ) != '' )
						{
							$links = new stdClass();
							
							foreach( $link->getElementsByTagName( 'span' ) as $span )
							{
								if( $span->getAttribute( 'class' ) == 'epnum' )
								{
									// release
									$links->release = $span->nodeValue;
								}
								else
								{
									// name
									$links->name = $span->nodeValue;
								}
							}
							
							// title
							$links->title = $link->getAttribute( 'title' );
							// href
							$links->href = $link->getAttribute( 'href' );
							// items
							$data[$ii]->item[] = $links;
						}
					}
					
					$ii++;
				}
				
			}
			
			//die( print_r( $data,1 ) . ' ..' );
		}
		else if( $query && !$link )
		{
			// Render search list
			foreach( $doc->getElementsByTagName( 'div' ) as $div )
			{
				if( $div->getAttribute( 'class' ) == 'fullwrap' )
				{
					$obj = new stdClass();
					
					// Assign title
					foreach( $div->getElementsByTagName( 'h1' ) as $title )
					{
						if( $title->getAttribute( 'class' ) == 'channel-title' )
						{
							$obj->title = trim( $title->nodeValue );
						}
					}
					
					// Url
					$obj->url = 'http://watchseries.lt';
					
					// Assign items
					$obj->item = array();
					foreach( $div->getElementsByTagName( 'a' ) as $link )
					{
						if( $link->getAttribute( 'title' ) && $link->getAttribute( 'href' ) && trim( $link->nodeValue ) != '' )
						{
							$links = new stdClass();
							
							$links->name = $link->nodeValue;
							$links->title = $link->getAttribute( 'title' );
							$links->href = $link->getAttribute( 'href' );
							
							$obj->item[] = $links;
						}
					}
				}
			}
			
			// Include movies from Zmovie.tw
			if( $item = ZmovieParser( $query )->item )
			{
				foreach( $item as $itm )
				{
					$obj->item[] = $itm;
				}
			}
			
			// Assign object to output array
			$data[] = $obj;
		}
		else
		{
			// Render main list
			foreach( $doc->getElementsByTagName( 'div' ) as $div )
			{
				if( $div->getAttribute( 'class' ) == 'div-home' || $div->getAttribute( 'class' ) == 'div-home-2' )
				{
					$obj = new stdClass();
					
					// Assign title
					foreach( $div->getElementsByTagName( 'div' ) as $title )
					{
						if( $title->getAttribute( 'class' ) == 'div-home-title' || $title->getAttribute( 'class' ) == 'div-home-title-2' )
						{
							$obj->title = trim( $title->nodeValue );
						}
					}
					
					// Url
					$obj->url = 'http://watchseries.lt';
					
					// Assign items
					$obj->item = array();
					foreach( $div->getElementsByTagName( 'a' ) as $link )
					{
						if( $link->getAttribute( 'title' ) )
						{
							$links = new stdClass();
							
							$links->name = $link->nodeValue;
							$links->title = $link->getAttribute( 'title' );
							$links->href = $link->getAttribute( 'href' );
							
							$obj->item[] = $links;
						}
					}
					
					// Assign views
					$i = 0;
					foreach( $div->getElementsByTagName( 'p' ) as $view )
					{
						if( $view->getAttribute( 'class' ) == 'view-blue' || $view->getAttribute( 'class' ) == 'view-white' )
						{
							$obj->item[$i]->views = $view->nodeValue;
						}
						
						$i++;
					}
					
					// Assign object to output array
					$data[] = $obj;
				}
			}
			
			// Include movies from Zmovie.tw
			$data[] = ZmovieParser( $query );
		}
		
		//die( print_r( $data,1 ) . ' ..' );
		
		return $data;
	}
	
	return false;
}

function ZmovieParser ( $query = false, $link = false )
{
	$url = 'http://www2.zmovie.tw' . ( !$link ? ( $query ? ( '/search/title/' . $query ) : '/movies/top' ) : $query );
		
	$str = GetCurlContentByUrl( !strstr( $query, 'http' ) ? $url : $query );

	// Parse the document -----------------------------------------------------------
	$doc = new DOMDocument();
	@$doc->loadHTML( $str );
	
	if( $doc )
	{
		//die( $str . ' .. ' . $url );
		
		$obj = new stdClass();
		
		if( $link && strstr( $query, 'http' ) )
		{
			foreach( $doc->getElementsByTagName( 'input' ) as $input )
			{
				if( strstr( strtolower( $input->getAttribute( 'value' ) ), 'iframe' ) &&
					strstr( strtolower( $input->getAttribute( 'value' ) ), 'src=' ) &&
					strstr( strtolower( $input->getAttribute( 'value' ) ), 'http' ) &&
					strstr( strtolower( $input->getAttribute( 'value' ) ), 'embed' ) )
				{
					$embed = $input->getAttribute( 'value' );
				}
			}
			foreach( $doc->getElementsByTagName( 'textarea' ) as $textarea )
			{
				if( strstr( strtolower( $textarea->nodeValue ), 'iframe' ) &&
					strstr( strtolower( $textarea->nodeValue ), 'src=' ) &&
					strstr( strtolower( $textarea->nodeValue ), 'http' ) &&
					strstr( strtolower( $textarea->nodeValue ), 'embed' ) )
				{
					$embed = $textarea->nodeValue;
				}
			}
			
			if( !$embed )
			{
				return header( 'Location: ' . $query );
			}
			
			return $embed;
		}
		else if( $link && strstr( $query, '/movies/view/' ) )
		{
			$obj->url = 'http://www2.zmovie.tw';
			$obj->item = array();
			
			foreach( $doc->getElementsByTagName( 'a' ) as $link )
			{
				$links = new stdClass();
				
				if( $link->getAttribute( 'class' ) == 'atest' && strstr( $link->getAttribute( 'href' ), 'http' ) )
				{
					$name = explode( '.', str_replace( array( 'http://', 'https://', 'www.' ), '', $link->getAttribute( 'href' ) ) );
					// link
					$links->href = $link->getAttribute( 'href' );
					// Name
					$links->name = $name[0];
					
					$obj->item[] = $links;
				}
			}
		}
		else
		{
			$obj->title = 'Most Popular Movies';
			$obj->url = 'http://www2.zmovie.tw';
			$obj->item = array();
			
			foreach( $doc->getElementsByTagName( 'a' ) as $link )
			{
				$links = new stdClass();
				
				// Image & View
				if( strstr( $link->getAttribute( 'href' ), '/view/' ) && $link->getAttribute( 'title' ) )
				{
					foreach( $link->getElementsByTagName( 'img' ) as $img )
					{
						if( $img->getAttribute( 'alt' ) )
						{
							// Image
							$links->image = $img->getAttribute( 'src' );
							// View
							$links->href = str_replace( 'http://www2.zmovie.tw', '', $link->getAttribute( 'href' ) );
							// Name
							$links->name = $link->getAttribute( 'title' );
						}
					}
					if( strstr( $link->getAttribute( 'title' ), 'Watch ' ) && strstr( $link->getAttribute( 'title' ), ' online' ) )
					{
						continue;
					}
				}
				/*// Genre
				if( strstr( $link->getAttribute( 'href' ), '/genre/' ) )
				{
					$obj->genre = $link->getAttribute( 'href' );
				}
				// Date
				if( strstr( $link->getAttribute( 'href' ), '/date/' ) )
				{
					$obj->date = $link->getAttribute( 'href' );
				}*/
				
				if( $links->image || $links->href || $links->name )
				{
					$obj->item[] = $links;
				}
			}
		}
		
		return $obj;
	}
	return false;
}

?>
