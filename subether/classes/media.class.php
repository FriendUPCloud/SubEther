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

class MediaHandler
{
	var $Error = 0;
	
	function convert_media( $filename, $rootpath, $inputpath, $outputpath, $width, $height, $bitrate, $samplingrate )
	{
		$ffmpeg = ( file_exists( BASE_DIR . '/subether/thirdparty/ffmpeg/ffmpeg' ) ? BASE_DIR . '/subether/thirdparty/ffmpeg/' : '' );
		
		$rPath = $rootpath . '/ffmpeg';
		$format = explode( '.', $filename );
		$size = $width . 'x' . $height;
		$outfile = str_replace( '.' . end( $format ), '', $filename ) . '.mp4';
		$outfile = $this->UniqueFile( $outfile, $outputpath );
		//$outfile2 = str_replace( '.' . end( $format ), '', $filename ) . '_converted.swf';
		//$outfile3 = str_replace( '.' . end( $format ), '', $filename ) . '_converted.ogv';
		$ffmpegcmd1 = $ffmpeg . 'ffmpeg -i ' . $inputpath . $filename . ' -ar 22050 ' . $outputpath . $outfile . ' 2>&1';
		//$ffmpegcmd2 = 'ffmpeg -i ' . $inputpath . '/' . $filename . ' -ar 22050 ' . $outputpath . '/' . $outfile2;
		//$ffmpegcmd3 = 'ffmpeg -i ' . $inputpath . '/' . $filename . ' -ar 22050 ' . $outputpath . '/' . $outfile3;
		//$ffmpegcmd1 = 'ffmpeg -i ' . $inputpath . '\\' . $filename . ' -acodec mp3 -ar ' . $samplingrate . ' -ab ' . $bitrate . '-f flv -s ' . $size . ' ' . $outputpath . '\\' . outfile;
		//die( $ffmpegcmd1 . ' --' );
		$ret = shell_exec( $ffmpegcmd1 );
		//die( $ffmpegcmd1 . ' - ' . $ret );
		if( file_exists( $outputpath . $outfile ) )
		{
			//shell_exec( $ffmpegcmd2 );
			//shell_exec( $ffmpegcmd3 );
			unlink( $inputpath . $filename );
			return $outfile;
		}
		$this->Error = $ret;
		return false;
	}
	
	function set_buffering( $filename, $rootpath, $path )
	{
		$ffmpeg = ( file_exists( BASE_DIR . '/subether/thirdparty/ffmpeg/ffmpeg' ) ? BASE_DIR . '/subether/thirdparty/ffmpeg/' : '' );
		
		$path = $rootpath;
		$ffmpegcmd1 = $ffmpeg . 'flvtool2 -U ' . $path . $filename;
		if( $ret = shell_exec( $ffmpegcmd1 ) )
		{
			return $ret;
		}
		$this->Error = $ret;
		return false;
	}
	
	function grab_image( $filename, $rootpath, $inputpath, $outputpath, $no_of_thumbs, $frame_number, $image_format, $width, $height )
	{
		$ffmpeg = ( file_exists( BASE_DIR . '/subether/thirdparty/ffmpeg/ffmpeg' ) ? BASE_DIR . '/subether/thirdparty/ffmpeg/' : '' );
		
		$_rootpath = $rootpath . '/ffmpeg';
		$format = explode( '.', $filename );
		$size = $width . 'x' . $height;
		$outfile = str_replace( '.' . end( $format ), '', $filename ) . '.png';
		$ffmpegcmd1 = $ffmpeg . 'ffmpeg -i ' . $inputpath .$filename . ' -vframes ' . $no_of_thumbs . ' -ss 00:00:03 -an -vcodec ' . $image_format . ' -f rawvideo -s ' . $size . ' ' . $outputpath . $outfile;
		$ret = shell_exec( $ffmpegcmd1 );
		
		return $outfile;
	}
	
	function get_duration( $filename, $rootpath )
	{
		$ffmpeg = ( file_exists( BASE_DIR . '/subether/thirdparty/ffmpeg/ffmpeg' ) ? BASE_DIR . '/subether/thirdparty/ffmpeg/' : '' );
		
		if( $ret = shell_exec( $ffmpeg . 'ffmpeg -i ' . $rootpath . $filename . ' 2>&1' ) )
		{
			if( preg_match( '/.*Duration: ([0-9:]+).*/', $ret, $matches ) ) 
			{
				return $matches[1];
			}
		}
		$this->Error = $ret;
		return false;
	}
	
	// --- Helper functions -----------------------------------------------------------------------------------------------------------
	
	function UniqueFile( $filename, $path )
	{
		$ext = '';
		
		if( !$filename || !$path ) return false;
		
		if( substr( $path, -1 ) != '/' )
		{
			$path = $path . '/';
		}
		
		$parts = explode ( '.', $filename );
		// Get the file extension, if any
		if( count( $parts ) > 1 )
		{
			$ext = array_pop ( $parts );
		}
		
		// Get the filename
		$file = implode( '.', $parts );
		
		if ( file_exists ( $path . $file . '.' . $ext ) )
		{	
			$num = end( explode( '_', $file ) );
			$file = str_replace( ( '_' . $num ), '', $file );
			
			$depth = 0;
			while ( file_exists ( $path . $file . '.' . $ext ) )
			{
				$file .= ( $num && is_numeric( $num ) ? ( $num + 1 ) : '_1' );
				$depth++;
				if( $depth >= 1000 ) return false;
			}
		}
		return ( $file . '.' . $ext );
	}
}

?>
