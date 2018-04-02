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

if( isset( $_REQUEST[ 'command' ] ) && $folder->CategoryID > 0 && $webuser->ID > 0 )
{
	$n = new dbObject( 'SBookNotification' );
	$n->Type = 'irc';
	$n->IsNoticed = '1';
	$n->ReceiverID = $webuser->ID;
	$n->Command = trim( $_REQUEST[ 'command' ] );
	$n->ObjectID = $folder->CategoryID;
	if( $nf = $n->Find() )
	{
		foreach( $nf as $na )
		{
			$a = new dbObject( 'SBookNotification' );
			if( $a->Load( $na->ID ) )
			{
				$a->Delete();
			}
		}
		die( 'ok' );
	}
	$n->ReceiverID = '0';
	if( $nf = $n->Find() )
	{
		foreach( $nf as $na )
		{
			$a = new dbObject( 'SBookNotification' );
			if( $a->Load( $na->ID ) )
			{
				$a->Delete();
			}
		}
		die( 'ok' );
	}
	die( 'fail' );
}
die( 'fail' );

?>
