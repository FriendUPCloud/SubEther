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

// Server based stuff here ...

include_once( 'include/functions.php' );

// Catch all incoming requests ...

if( isset( $_SERVER['REQUEST_METHOD'] ) )
{
	switch( strtoupper( $_SERVER['REQUEST_METHOD'] ) )
	{
		// POST requests for creating go here ...
		// PUT requests for updates go here ...
		
		case 'POST': 
		case 'PUT': 
			include_once( 'include/save/put.php' );
			break;
		
		// DELETE requests go here ...
		
		case 'DELETE': 
			include_once( 'include/delete/delete.php' );
			break;
		
		// GET requests go here ...
		
		case 'GET': 
		default: 
			include_once( 'include/load/get.php' );
			break;
		
	}
}

failed( 'end of the line ... ' . json_encode( $_POST ) . json_encode( $_PUT ) . json_encode( $_DELETE ) . json_encode( $_GET ) );

?>
