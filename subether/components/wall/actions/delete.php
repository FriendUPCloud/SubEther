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

if( isset( $_POST[ 'delete' ] ) && $_POST[ 'delete' ] > 0 )
{
    $m = new dbObject( 'SBookMessage' );
    if( $m->Load( $_POST[ 'delete' ] ) )
    {
        if( $m->ParentID == '0' )
        {
            $cc = new dbObject( 'SBookMessage' );
            $cc->ParentID = $m->ID;
            if( $cc = $cc->Find() )
            {
                foreach( $cc as $c )
                {
                    $sm = new dbObject( 'SBookMessage' );
                    if( $sm->Load( $c->ID ) )
                    {
                        // Delete comment
                        $sm->Delete();
                    }
                    
                }
            }
        }
        
        $mr = new dbObject( 'SBookRelation' );
        $mr->ObjectType = 'SBookMessage';
        $mr->ObjectID = $m->ID;
        if( $mr->Load() )
        {
            // Delete relation
            $mr->Delete();
        }
        
        $nn = new dbObject( 'SBookNotification' );
        $nn->Type = 'wall';
        $nn->ObjectID = $m->ID;
        if( $nn = $nn->Find() )
        {
            foreach( $nn as $n )
            {
                $sn = new dbObject( 'SBookNotification' );
                if( $sn->Load( $n->ID ) )
                {
                    // Delete notify
                    $sn->Delete();
                }
            }
        }
        
        LogStats( 'wall', 'delete', $m->Type, $m->SenderID, $m->ReceiverID, $m->CategoryID );
        
        // Delete post or comment
        $m->Delete();
        
        die( 'ok<!--separate-->' );
    }
    die( 'fail' );
}
die( 'fail' );

?>
