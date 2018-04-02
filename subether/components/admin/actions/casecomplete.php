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

if( isset( $_POST[ 'cid' ] ) && isset( $_POST[ 'checked' ] ) )
{
    $tsk = new dbObject( 'SBookCaseList' );
    if( $tsk->Load( $_POST[ 'cid' ] ) )
    {
    	$tsk->Progress = $_POST[ 'checked' ];
        $tsk->Save();
    	
    	// Count the progress of tasks in the parent case
        /*$ts = new dbObject( 'SBookCaseList' );
        $ts->CaseID = $tsk->CaseID;
        if( $ts = $ts->Find() )
        {
            $count = 0;
            foreach( $ts as $t )
            {
            	if( $t->Progress == '100%' )
            	{
            	    $count++;
            	}
            }
	    //die( ( $count / count( $ts ) * 100 ) . ' .. ' . $count );
	    
            $c = new dbObject( 'SBookCaseList' );
            if( $c->Load( $tsk->CaseID ) )
            {
            	$c->Progress = count( $ts ) > 0 ? ( ( $count / count( $ts ) * 100 ) . '%' ) : '0%';
            	$c->Save();
            }
        }*/
	CountCaseTasks( $tsk->CaseID );
        die( 'ok<!--separate-->' );
    }
    die( 'fail' );
}
die( 'fail' );

?>
