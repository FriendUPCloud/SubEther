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

$_POST[ 'jaxqueue' ] = jsondecode( $_POST[ 'jaxqueue' ] );

if( $_POST[ 'jaxqueue' ] )
{
	$queue = $_POST[ 'jaxqueue' ]; $output = array();
	
	foreach( $queue as $obj )
	{
		$act = false; $str = '';
		
		if( $obj->Url )
		{
			// Set GET vars from url
			$_REQUEST = ArrayFromString( $obj->Url );
			
			// Set POST vars from object
			$_POST = array ();
			
			if( $obj->postVars )
			{
				foreach( $obj->postVars as $k=>$var )
				{
					if ( !trim ( $k ) ) continue;
					$_POST[$k] = $var;
				}
			}
			
			// Check for global ajax function requests -----------------------------------
			if( isset( $_REQUEST[ 'global' ] ) && ( ( $act = $_REQUEST[ 'function' ] ) || ( $act = $_REQUEST[ 'action' ] ) ) )
			{
				$GLOBALS['jaxqueue'] = 1;
				
				if( file_exists ( $root . '/include/' . $act . '.php' ) )
				{
					include ( $root . '/include/' . $act . '.php' );
					$output[] = $obj->Name . '<!--jsfunction-->' . $GLOBALS['jaxqueue'];
				}
			}
			
			// Check for component ajax function requests -------------------------------
			if( isset( $_REQUEST[ 'component' ] ) && ( $_REQUEST[ 'function' ] || $_REQUEST[ 'action' ] ) )
			{
				$output[] = $obj->Name . '<!--jsfunction-->' . JaxQueueComponent( $_REQUEST[ 'component' ], $parent );
			}
		}
	}
}

die( ( is_array( $output ) ? implode( '<!--jaxqueue-->', $output ) : 'fail' ) . '<!--jaxqueue-->' /*. print_r( $queue,1 )*/ );

?>
