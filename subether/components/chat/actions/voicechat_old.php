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

$udata = UserData( $webuser->ID );

if( isset( $_POST[ 'login' ] ) )
{	
	// Chat Plivo Settings
	$c = new dbObject( 'SBookContact' );
	$c->UserID = $webuser->ID;
	if( $c->Load() )
	{
		if( !is_object( $c->Data ) ) $c->Data = json_decode( $c->Data );
		
		if( !$c->Data->Settings )
		{
			$c->Data->Settings = new stdClass();
		}
		if( !$c->Data->Settings->Plivo )
		{
			$c->Data->Settings->Plivo = new stdClass();
		}
		$c->Data->Settings->Plivo->onLogin = '1';
		$c->Data = json_encode( $c->Data );
		$c->Save();
		
		die( 'ok' );
	}
}
if( isset( $_POST[ 'call' ] ) && $udata && $udata->Settings->Plivo->URI )
{
	if( $_POST['call'] > 1 )
	{
		$u = new dbObject( 'Users' );
		$u->Load( $_POST['call'] );
	}
	
	// Notify Reciever
	$n = new dbObject( 'SBookNotification' );
	$n->ObjectID = $_POST[ 'call' ];
	$n->Type = 'voicechat';
	$n->SenderID = $webuser->ID;
	$n->ReceiverID = $_POST[ 'call' ];
	$n->Load();
	//$n->Command = $udata->Settings->Plivo->URI;
	$n->IsNoticed = '1';
	$n->Save();
	
	die( 'ok<!--separate-->' . $u->Name );
}
if( isset( $_POST[ 'accept' ] ) )
{	
	// Update status to IsRead
	$n = new dbObject( 'SBookNotification' );
	$n->Type = 'voicechat';
	$n->ReceiverID = $webuser->ID;
	if( $n->Load() )
	{
		$n->IsRead = '1';
		$n->Save();
		
		die( 'ok' );
	}
}
if( isset( $_POST[ 'decline' ] ) )
{	
	// Update status to IsRead
	$n = new dbObject( 'SBookNotification' );
	$n->Type = 'voicechat';
	$n->ReceiverID = $webuser->ID;
	if( $n->Load() )
	{
		$n->Delete();
		
		die( 'ok' );
	}
}
die( 'fail' );

?>
