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

global $database, $webuser;

$bstr = '';

if( $bms = $database->fetchObjectRows( $q = '
	SELECT
		*
	FROM
		SBookBookmarks
	WHERE
		UserID = \'' . $webuser->ContactID . '\'
	ORDER BY
		DateModified DESC
' ) )
{
	foreach( $bms as $bm )
	{
		$bookmarks = json_obj_decode( $bm->Bookmarks, 'array' );
		
		// Temporary hack
		if( $bookmarks )
		{
			$array = array();
			
			foreach( $bookmarks as $v )
			{
				if( !strstr( $v,'#' ) )
				{
					$array[] = $v;
				}
			}
			
			if( $array )
			{
				$bookmarks = $array;
			}
			
			rsort( $bookmarks );
		}
		
		//die( print_r( $bookmarks,1 ) . ' --' );
		
		// TODO: Sort after bookmark added ID's
		
		if( $bookmarks && $module != 'profile' && ComponentExists( $bm->Component, $parent->module ) && ComponentAccess( $bm->Component, $parent->module ) )
		{
			$str = '';
			
			include_once ( 'subether/components/' . $bm->Component . '/component.php' );
			
			$bstr .= '<div id="Wall" class="posts bookmarks">';
			$bstr .= '<div id="ShareContent" class="editing">' . $str . '</div>';
			$bstr .= '</div>';
		}
	}
}

if( isset( $_REQUEST[ 'function' ] ) ) die( 'ok<!--separate-->' . $bstr );

// TODO: Fix this bookmark mess

?>
