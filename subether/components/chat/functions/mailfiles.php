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

include_once( 'subether/components/library/include/functions.php' );

if( $_POST['files'] && ( $files = $database->fetchObjectRows( '
	SELECT
		*
	FROM
		File 
	WHERE
		ID IN ( ' . $_POST['files'] . ' )
	ORDER BY 
		ID ASC 
' ) ) )
{
	$ostr = '<ul>';
	
	foreach( $files as $f )
	{
		$ostr .= '<li>' . $f->Title . ' ' . libraryFilesize( $f->Filesize ) . ' (x)</li>';
	}
	
	$ostr .= '</ul>';
	
	$ostr .= '<input type="hidden" name="files" value="' . $_POST['files'] . '"/>';
	
	die( 'ok<!--separate-->' . $ostr );
}

die( 'fail' );

?>
