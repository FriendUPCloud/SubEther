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

if( !$folder && $parent ) $folder = $parent->folder;

if( isset( $_REQUEST[ 'Message' ] ) || isset( $_POST[ 'Video' ] ) || isset( $_POST[ 'Audio' ] ) || isset( $_POST[ 'File' ] ) )
{
	$titlim = 63;
	$deslim = 530;
	
	if( $_POST[ 'Width' ] && $_POST[ 'Height' ] )
	{
		$width = $_POST[ 'Width' ];
		$height = $_POST[ 'Height' ];
	}
	else if( !$width && !$height )
	{
		$width = '743';
		$height = '420';
	}
	
	/*if( $_POST[ 'Message' ] != '' )
	{
		$str = $out = $data = '';
		
		$obj = parseText( $_POST[ 'Message' ], $width, $height, 'parse' );

		if( $obj )
		{
			$obj->Limit['width'] = $width;
			$obj->Limit['height'] = $height;
			$obj->Limit['title'] = $titlim;
			$obj->Limit['description'] = $deslim;

			$out .= '<div class="ParseContent">';
			$out .= '<div><!--replaceimage--></div>';
			$out .= '<div class="text">';
			$out .= '<h3><a target="_blank" href="' . $obj->Url . '">' . dot_trim( $obj->Title, $obj->Limit['title'] ) . '</a></h3>';
			$out .= '<p><a target="_blank" href="' . $obj->Url . '">' . dot_trim( $obj->Leadin, $obj->Limit['description'] ) . '</a></p>';
			$out .= '<p class="url"><a target="_blank" href="' . $obj->Url . '">' . $obj->Domain . '</a></p>';
			$out .= '</div></div>';
			$out .= '<div class="Edit" onclick="removeParse()"><div></div></div>';
			$str = $out;
			$str = stripslashes( $str );
			
			$data = new stdClass();
			$data->Type = $obj->Type;
			$data->Url = $obj->Url;
			$data->ImageID = '0';
			if( $obj->Images )
			{
				$data->Images = $obj->Images;
			}
			$data->Limit = $obj->Limit;

		}
	}
	else if( $_POST[ 'Video' ] != '' )
	{
		$str = parseText( $_POST[ 'Video' ], $width, $height, 'video' );
		$str = stripslashes( $str );
	}
	if( $_REQUEST[ 'function' ] == 'parse' && $str ) die( 'ok<!--separate-->' . $str . '<!--separate-->' . ( $data ? json_encode( $data ) : '' ) );*/
	
	if( isset( $_POST[ 'Video' ] ) && !$_POST[ 'Media' ] )
	{
		// Youtube ------------------------------------------------------------------------------------------------------
		if( ( strstr( $_POST[ 'Video' ], 'youtube.com/watch?v=' ) || strstr( $_POST[ 'Video' ], 'youtu.be/' ) ) && $width > 0 && $height > 0 )
		{
			$_POST[ 'Media' ] = 'youtube';
		}
		// Vimeo -------------------------------------------------------------------------------------------------------
		else if( strstr( $_POST[ 'Video' ], 'vimeo.com/' ) && $width > 0 && $height > 0 )
		{
			$_POST[ 'Media' ] = 'vimeo';
		}
		// Livestream -------------------------------------------------------------------------------------------------
		else if( ( strstr( $_POST[ 'Video' ], 'livestream.com/' ) || strstr( $_POST[ 'Video' ], 'livestre.am/' ) ) && $width > 0 && $height > 0 )
		{
			$_POST[ 'Media' ] = 'livestream';
		}
		// Default Video ---------------------------------------------------------------------------------------------
		else
		{
			$_POST[ 'Media' ] = 'video';
		}
	}
	
	if( isset( $_POST[ 'Audio' ] ) && !$_POST[ 'Media' ] )
	{
		// Spotify -------------------------------------------------------------------------------------------------------
		if( strstr( $_POST[ 'Audio' ], 'p.scdn.co/mp3-preview/' ) && $width > 0 && $height > 0 )
		{
			$_POST[ 'Media' ] = 'spotify';
		}
		// Default Video ---------------------------------------------------------------------------------------------
		else
		{
			$_POST[ 'Media' ] = 'audio';
		}
	}
	
	//die( 'ur here .. ' . print_r( $_POST,1 ) . ' || ' . print_r( $_REQUEST,1 ) );
	if( isset( $_POST[ 'Video' ] ) && isset( $_POST[ 'Media' ] ) )
	{
		$str = '';
		
		switch( $_POST[ 'Media' ] )
		{
			// Youtube ----------------------------------------------------------------------------------------------------
			case 'youtube':
				$str = embedYoutube( $_POST[ 'Video' ], $width, $height );
				break;
			// Vimeo ------------------------------------------------------------------------------------------------------
			case 'vimeo':
				$str = embedVimeo( $_POST[ 'Video' ], $width, $height );
				break;
			// Livestream -------------------------------------------------------------------------------------------------
			case 'livestream':
				$str = embedLivestream( $_POST[ 'Video' ], $width, $height );
				break;
			// Video ------------------------------------------------------------------------------------------------------
			default:
				$str = embedVideo( $_POST[ 'Video' ], $width, $height, 'video' );
				break;
		}
		
		die( 'ok<!--separate-->' . $str );
	}
	else if( isset( $_POST[ 'Audio' ] ) && isset( $_POST[ 'Media' ] ) )
	{
		$str = '';
		
		switch( $_POST[ 'Media' ] )
		{
			// Spotify ---------------------------------------------------------------------------------------------------
			case 'spotify':
				$str = embedSpotify( $_POST[ 'Audio' ], $width, $height );
				break;
			// Audio ------------------------------------------------------------------------------------------------------
			default:
				$str = embedAudio( $_POST[ 'Audio' ], $width, $height );
				break;
		}
		
		die( 'ok<!--separate-->' . $str );
	}
	else if( isset( $_POST[ 'File' ] ) )
	{	
		$str = embedPDF( $_POST[ 'File' ], $width, $height );
		
		die( 'ok<!--separate-->' . $str );
	}
	else
	{
		$lib = new Library ();
		if( $data = $lib->ParseUrl( $_REQUEST[ 'Message' ] ) )
		{
			$data->ImageID = '0';
			if( !isset( $data->Limit ) )
				$data->Limit = new stdclass();
			$data->Limit->width = $width;
			$data->Limit->height = $height;
			$data->Limit->title = $titlim;
			$data->Limit->description = $deslim;
		}
		//die( ' .. ' . print_r( $data ) );
		if( $_REQUEST[ 'function' ] == 'parse' && $data ) die( 'ok<!--separate-->' . json_encode( $data ) );
	}
}
if( $_REQUEST[ 'function' ] == 'parse' ) die();

?>
