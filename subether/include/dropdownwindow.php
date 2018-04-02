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

$root = 'subether';

if( isset( $_POST[ 'target' ] ) && isset( $_POST[ 'component' ] ) )
{
	include_once ( $root . '/components/wall/include/functions.php' );
	include_once ( $root . '/components/notification/include/functions.php' );
	
	switch( $_POST[ 'target' ] )
	{
		case 'contacts':
			if ( file_exists ( ( $f = 'subether/components/' . $_POST[ 'component' ] . '/functions/contacts.php' ) ) )
			{
				include ( $f );
			}
			break;
		case 'messages':
			if ( file_exists ( ( $f = 'subether/components/' . $_POST[ 'component' ] . '/functions/messages.php' ) ) )
			{
				include ( $f );
			}
			break;
		case 'notices':
			if ( file_exists ( ( $f = 'subether/components/' . $_POST[ 'component' ] . '/functions/notices.php' ) ) )
			{
				include ( $f );
			}
			break;
		case 'cart':
			if ( file_exists ( ( $f = 'subether/components/' . $_POST[ 'component' ] . '/functions/cart.php' ) ) )
			{
				include ( $f );
			}
			break;
		default:
			die( 'ok<!--separate-->test' );
			break;
	}
}
die( 'fail - dropdownwindow' );

?>
