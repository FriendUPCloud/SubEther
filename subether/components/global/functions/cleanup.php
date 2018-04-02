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

include_once ( 'subether/classes/library.class.php' );

$str = '<h2>Cleanup</h2><br>';

$clean = new Library();

$updated = $clean->OpenFile( BASE_DIR . '/current_core/', 'updated.txt' );
$updated = ( $updated ? explode( "\n", $updated ) : false );

if( $updated && is_array( $updated ) && ( $files = $clean->ListFilesAndFolders( BASE_DIR . '/current_core' ) ) )
{
	$i = 0;
	
	$str .= '<h3>These files didn\'t match last version update and are listed for termination.</h3><br>';
	
	$str .= '<ul>';
	
	rsort( $files );
	
	foreach( $files as $file )
	{
		if( !in_array( $file, $updated ) )
		{
			$str .= '<li>' . $file . '</li>';
		}
	}
	
	$str .= '</ul>';
}

?>
