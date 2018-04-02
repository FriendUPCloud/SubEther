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

// Include basic api functions for v2 ... to be a library for all included applications ...

if ( preg_match ( '/v2\//i', $_REQUEST['route'], $matches ) )
{
	include_once ( 'subether/restapi/functions.php' );
	
	// Create support for PUT, DELETE methods, put this into the globals function for the api ...
	
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && ( $_SERVER['REQUEST_METHOD'] == "PUT" || $_SERVER['REQUEST_METHOD'] == "DELETE" ) ) 
	{
		$post_vars = false;
		
		$phpdata = @file_get_contents( 'php://input' );
		
		if( isset( $_SERVER['CONTENT_TYPE'] ) && strstr( $_SERVER['CONTENT_TYPE'], '/json' ) )
		{
			if( $phpdata && !is_array( $phpdata ) )
			{
				$post_vars = array();
				
				if( $json = json_decode( $phpdata ) )
				{
					foreach( $json as $k=>$v )
					{
						$post_vars[$k] = urldecode( $v );
					}
					
					unset( $json );
				}
			}
		}
		else
		{
			@parse_str( $phpdata, $post_vars );
		}
		
		$GLOBALS["_".$_SERVER['REQUEST_METHOD'].""] = ( $post_vars ? $post_vars : array() );
		
		if( $post_vars )
		{
			// Add these request vars into _REQUEST, mimicing default behavior, PUT/DELETE will override existing COOKIE/GET vars
			$_REQUEST = $_REQUEST + $post_vars;
			unset( $post_vars );
		}
		
		if( $phpdata )
		{
			unset( $phpdata );
		}
	}
	
	if ( preg_match ( '/v2\/([a-z0-9]*?)\/.*/i', $_SERVER['REQUEST_URI'], $app ) )
	{
		if( isset( $app[1] ) && $app[1] && file_exists( 'subether/applications/' . strtolower( $app[1] ) . '/server.php' ) )
		{
			require ( 'subether/applications/' . strtolower( $app[1] ) . '/server.php' );
		}
	}
	
}

?>
