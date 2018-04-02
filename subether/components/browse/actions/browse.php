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

if( isset( $_POST[ 'comment' ] ) && isset( $_POST[ 'mid' ] ) )
{
	$r = new dbObject( 'SBookRelation' );
	$r->ConnectedID = $_POST[ 'mid' ];
	$r->ConnectedType = 'SBookFiles';
	$r->ObjectType = 'SBookMessage';
	$r->Type = 'MediaComment';
	$r->Load();
	
	$m = new dbObject( 'SBookMessage' );
	$m->SenderID = $webuser->ID;
	$m->Message = $_POST[ 'comment' ];
	$m->ParentID = $r->ObjectID > 0 ? $r->ObjectID : 0;
	$m->ThreadID = 0;
	$m->Type = 'text';
	$m->Date = date( 'Y-m-d H:i:s' );
	$m->DateModified = date( 'Y-m-d H:i:s' );
	$m->Save();
	
	$r->ObjectID = $m->ParentID > 0 ? $m->ParentID : $m->ID;
	$r->Save();
	
	die( 'ok<!--separate-->' );
}
die( 'fail' );

?>
