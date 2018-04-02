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

global $webuser, $database;

$commands = explode ( "\n", file_get_contents ( 'subether/components/irc/data/help.txt' ) );

if( $commands )
{
	$help = '';
	foreach( $commands as $com )
	{
		$help .= '<div>' . htmlentities( $com ) . '</div>';
	}
}

if( $webuser && $webuser->ID && isset ( $_POST[ 'pid' ] ) && isset ( $_POST[ 'message' ] ) )
{
	if ( !trim ( $_POST['message'] ) )
		die ( 'fail' );
	
	$m = new dbObject( 'SBookChat' );
	$m->Type = 'im';
	$m->SenderID = $webuser->ID;
	$m->CategoryID = $_POST[ 'pid' ];
	$m->Message = $_POST[ 'message' ];
	if ( $m->Message{0} == '/' )
	{
		$m->Status = 'command';
		$m->Message = parseCommands( $m->Message, $m->SenderID, $m->CategoryID );
	}
	$m->Message = parseHighlights( $m->Message, $m->CategoryID );
	if( $m->Message != '' && $m->Message{0} != '/' )
	{
		$m->Date = date( 'Y-m-d H:i:s' );
		$m->Save();
	}
	
	if( $m->ID > 0 || $_POST[ 'message' ] == '/help' ) 
	{
		die( 'ok<!--separate-->' . ( $_POST[ 'message' ] == '/help' ? $help : '' ) );
	}
	else 
	{
		die( 'fail<!--separate-->' ); //. print_r( $_POST,1 ) );
	}
}

die ( 'fail<!--separate-->' ); //. print_r( $_POST,1 ) );

?>
