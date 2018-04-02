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

global $webuser;

if( isset( $_POST['type'] ) && isset( $_POST['fid'] ) && isset( $_POST['edit'] ) )
{
	$f = new dbObject( $_POST['type'] == 'image' ? 'Image' : 'File' );
	if( $f->Load( $_POST['fid'] ) )
	{
		if( $f->IsEdit == 0 || $f->IsEdit == $webuser->ID )
		{
			$f->IsEdit = ( $_POST['edit'] > 0 ? $webuser->ID : 0 );
			$f->Save();
			
			die( 'ok<!--separate-->' );
		}
	}
}
die( 'fail' );

?>
