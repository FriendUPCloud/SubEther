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

global $database;

if( $_POST['mid'] > 0 )
{
	$str = '';
	
	if( $post = $database->fetchObjectRow( '
			SELECT 
				m.* 
			FROM 
				SBookMessage m 
			WHERE 
				m.ID = \'' . $_POST['mid'] . '\' 
			ORDER BY 
				m.ID DESC 
		' ) )
	{
		$str .= '<div id="EditMode" class="editor post">'; 
		$str .= 	'<div class="text">'; 
		$str .= 		'<div contenteditable="true" class="textarea post" id="EditID_' . $post->ID . '">' . $post->Message . '</div>'; 
		$str .= 	'</div>';
		$str .= 	'<div class="toolbar">'; 
		$str .= 		'<div class="publish">';
		$str .= 			'<button class="save_btn" onclick="EditWallPost(ge(\'EditID_' . $post->ID . '\'),\'' . $post->ID . '\',\'' . $post->Type . '\')" type="button">SAVE</button>';
		$str .= 			'<button class="cancel_btn" onclick="CloseEditMode()" type="button">CANCEL</button>';
		$str .= 		'</div>';
		$str .= 	'</div>';
		$str .= '</div>';
		
		die( 'ok<!--separate-->' . $str );
	}
}

die( 'fail' );

?>
