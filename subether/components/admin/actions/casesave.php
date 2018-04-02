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

if( isset( $_POST[ 'content' ] ) && isset( $_POST[ 'cid' ] ) )
{
	$c = new dbObject( 'SBookCaseList' );
	if( $c->Load( $_POST[ 'cid' ] ) )
	{
		$c->Description = stripslashes( $_POST[ 'content' ] );
		$c->Save();
		
		die( 'ok<!--separate-->' . stripslashes( $_POST[ 'content' ] ) );
	}
}
else if( isset( $_POST[ 'type' ] ) && isset( $_POST[ 'value' ] ) && $parent->folder->CategoryID > 0 )
{
    $c = new dbObject( 'SBookCaseList' );
    $c->CategoryID = $parent->folder->CategoryID;
    $c->Type = ( $_POST[ 'type' ] == 'Case' ? 'Case' : 'Task' );
    if( $_POST[ 'param' ] == 'newtask' && $_POST[ 'cid' ] > 0 )
    {
	$c->CaseID = $_POST[ 'cid' ];
    }
    else if( $_POST[ 'param' ] == 'newcase' )
    {
	$c->CaseID = '0';
    }
    else if( !$_POST[ 'param' ] )
    {
	$c->Load( $_POST[ 'cid' ] );
    }
    $c->Name = $_POST[ 'value' ];
    $c->Save();
    
    CountCaseTasks( ( $c->CaseID > 0 ? $c->CaseID : $c->ID ) );
    
    die( 'ok<!--separate-->' );
}
die( 'fail' );

?>
