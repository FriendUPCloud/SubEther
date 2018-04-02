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

if ( isset( $_POST['cname'] ) && isset( $_POST['mname'] ) && isset( $_POST['position'] ) && isset( $_POST['checked'] ) )
{
	$m = new dbObject( 'SModules' );
	$m->Type = ( $_POST['mtype'] ? $_POST['mtype'] : 'global' );
	$m->Name = $_POST['mname'];
	$m->Position = $_POST['position'];
	if ( !$m->Load() )
	{
		$m->UserLevels = ( $m->UserLevels ? $m->UserLevels : ',0,' );
		$m->Save();
	}
	
	$c = new dbObject( 'SComponents' );
	$c->Type = ( $_POST['mtype'] ? $_POST['mtype'] : 'global' );
	$c->Name = $_POST['cname'];
	$c->Module = $_POST['mname'];
	$c->Position = $_POST['position'];
	$c->Load();
	if ( $_POST['checked'] > 0 )
	{
		$c->UserLevels = ( $_POST['access'] ? $_POST['access'] : ',99,' );
		$c->Save();
	}
	else
	{
		$c->Delete();
	}
	
	die( 'ok<!--separate-->' );
}

die( 'fail' );

?>
