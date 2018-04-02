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

class PostHandler
{
	var $vars;
	var $getvars;
	var $server;
	var $rawpost;
	var $headers;
	var $follow;
	
	function __construct ( $server, $follow = false )
	{
		$this->server = trim ( $server );
		$this->vars = array ();
		$this->rawpost = false;
		$this->headers = array ();
		$this->follow = $follow;
	}
	function SetHeader ( $header, $value )
	{
		$this->_headers[] = array ( $header, $value );
		$this->_options[] = $header;
	}
	function AddVar ( $var, $value, $type = 'post' )
	{
		$type = strtolower ( $type );
		if ( $type == 'post' )
		{
			if ( !trim ( $var ) )
			{
				$this->rawpost = $value;
			}
			else $this->vars[$var] = $value;
		}
		else $this->getvars[$var] = $value;
	}
	function Send ()
	{
		$data = '';
		$url = $this->server;
		if ( count ( $this->vars ) )
		{
			$i = 0;
			foreach ( $this->vars as $key=>$var )
			{
				if ( $i++ > 0 )
					$data .= '&';
				$data .= $key . '=' . urlencode ( utf8_decode ( trim ( $var ) ) );
			}
		}
		if ( count ( $this->getvars ) )
		{
			$i = 0;
			foreach ( $this->getvars as $key=>$var )
			{
				if ( $i == 0 && substr ( $url, -1, 1 ) != '&' )
				{
					if ( strstr ( $url, '?' ) )
						$url .= '&';
					else $url .= '?';
				}
				else if ( $i > 0 )
					$url .= '&';
				$url .= $key . '=' . urlencode ( utf8_decode ( trim ( $var ) ) );
				$i++;
			}
		}
		$this->_lastQuery = $url . "\n" . ( $this->rawpost ? ( is_array ( $this->rawpost ) ? print_r ( $this->rawpost, 1 ) : $this->rawpost ) : $data );
		$cu = curl_init ( $url );
		if( !isset( $this->_options ) || !in_array( CURLOPT_POST, $this->_options ) )
		{
			curl_setopt ( $cu, CURLOPT_POST, 1 );
		}
		if ( is_array ( $this->rawpost ) && ( !isset( $this->_options ) || !in_array( CURLOPT_POSTFIELDS, $this->_options ) ) )
		{
			curl_setopt ( $cu, CURLOPT_POSTFIELDS, $this->rawpost );
		}
		else if ( trim ( $this->rawpost ) && ( !isset( $this->_options ) || !in_array( CURLOPT_POSTFIELDS, $this->_options ) ) )
		{
			curl_setopt ( $cu, CURLOPT_POSTFIELDS, trim ( $this->rawpost ) );
		}
		else if ( isset ( $data ) && ( !isset( $this->_options ) || !in_array( CURLOPT_POSTFIELDS, $this->_options ) ) )
		{
			curl_setopt ( $cu, CURLOPT_POSTFIELDS, $data );
		}
		if ( isset ( $this->_headers ) && count ( $this->_headers ) )
		{
			foreach ( $this->_headers as $head )
			{
				curl_setopt ( $cu, $head[0], $head[1] );
			}
		}
		if( !isset( $this->_options ) || !in_array( CURLOPT_HEADER, $this->_options ) )
		{
			curl_setopt ( $cu, CURLOPT_HEADER, 0 );
		}
		//curl_setopt ( $cu, CURLOPT_FOLLOWLOCATION, 1 );
		if( !isset( $this->_options ) || !in_array( CURLOPT_FAILONERROR, $this->_options ) )
		{
			curl_setopt ( $cu, CURLOPT_FAILONERROR, 1 );
		}
		if( !isset( $this->_options ) || !in_array( CURLOPT_RETURNTRANSFER, $this->_options ) )
		{
			curl_setopt ( $cu, CURLOPT_RETURNTRANSFER, 1 );
		}
		
		if ( $this->follow )
		{
			return $this->curl_exec_follow( $cu );
		}
		else
		{
			return curl_exec ( $cu );
		}
	}
	
	function curl_exec_follow( $ch, &$maxredirect = null )
	{
		$mr = $maxredirect === null ? 5 : intval( $maxredirect );
		if ( ini_get( 'open_basedir' ) == '' && ini_get( 'safe_mode' == 'Off' ) )
		{
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, $mr > 0 );
			curl_setopt( $ch, CURLOPT_MAXREDIRS, $mr );
		}
		else
		{
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
			if ( $mr > 0 )
			{
				$newurl = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );
				$rch = curl_copy_handle( $ch );
				curl_setopt( $rch, CURLOPT_HEADER, true );
				curl_setopt( $rch, CURLOPT_NOBODY, true );
				curl_setopt( $rch, CURLOPT_FORBID_REUSE, false );
				curl_setopt( $rch, CURLOPT_RETURNTRANSFER, true );
				do
				{
					curl_setopt( $rch, CURLOPT_URL, $newurl );
					$header = curl_exec( $rch );
					if ( curl_errno( $rch ) )
					{
						$code = 0;
					}
					else
					{
						$code = curl_getinfo( $rch, CURLINFO_HTTP_CODE );
						if ( $code == 301 || $code == 302 )
						{
							preg_match( '/Location:(.*?)\n/', $header, $matches );
							$oldurl = $newurl;
							$newurl = trim( array_pop( $matches ) );
							
							if( $newurl && !strstr( $newurl, 'http://' ) && !strstr( $newurl, 'https://' ) )
							{
								if( strstr( $oldurl, 'https://' ) )
								{
									$parts = explode( '/', str_replace( 'https://', '', $oldurl ) );
									$newurl = ( 'https://' . reset( $parts ) . ( $newurl{0} != '/' ? '/' : '' ) . $newurl );
								}
								if( strstr( $oldurl, 'http://' ) )
								{
									$parts = explode( '/', str_replace( 'http://', '', $oldurl ) );
									$newurl = ( 'http://' . reset( $parts ) . ( $newurl{0} != '/' ? '/' : '' ) . $newurl );
								}
								
							}
						}
						else
						{
							$code = 0;
						}
					}
				}
				while ( $code && --$mr );
				curl_close( $rch );
				if ( !$mr )
				{
					if ( $maxredirect === null )
					{
						trigger_error( 'Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING );
					}
					else
					{
						$maxredirect = 0;
					}
					return false;
				}
				curl_setopt( $ch, CURLOPT_URL, $newurl );
			}
		}
		
		$ch = curl_exec( $ch );
		
		if( $ch )
		{
			return $ch;
		}
		
		return false;
	}
}

?>
