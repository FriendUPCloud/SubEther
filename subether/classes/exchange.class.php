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

class exchange
{
	
	function __construct ( $server = null, $username = null, $password = null )
	{
		//
		
		// If possible do auth on client to protect username / password or secret in order to get token ...
		
	}
	
	
	
	private function query( $command, $args = false, $method = 'POST', $headers = false )
	{		
		//
		
		if( substr( $command, 0, 4 ) == 'http' )
		{
			$url = $command;
		}
		else
		{
			$url = trim( $this->Server ) . '/' . $this->api_path . '/' . $this->api_type . '/' . $this->api_version . $command;
		}
		
		$base64 = base64_encode( trim( $this->Username ) . ':' . trim( $this->Password ) );
		
		$headers = ( $headers ? $headers : array( 'Content-Type: application/json' ) );
		
		$headers[] = ( 'Authorization: Basic ' . $base64 );
		
		
		$curl = curl_init();
		
		curl_setopt( $curl, CURLOPT_URL, $url );
		
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		
		if( $method != 'POST' )
		{
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $method );
		}
		
		if( $args )
		{
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, ( is_object( $args ) || is_array( $args ) ? json_encode( $args ) : $args ) );
		}
		
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		
		$output = curl_exec( $curl );
		
		$httpCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		
		curl_close( $curl );
		
		if( $output )
		{
			return $output;
		}
		
		return '';
	}
	
}

?>
