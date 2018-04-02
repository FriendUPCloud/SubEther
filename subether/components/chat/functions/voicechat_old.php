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

if( $_POST['call'] > 1 )
{
	$u = new dbObject( 'Users' );
	$u->Load( $_POST['call'] );
}

// Chat Settings
$c = new dbObject( 'SBookContact' );
$c->UserID = $_POST['call'] > 1 ? $u->ID : $webuser->ID;
if( $c->Load() )
{
	if( !is_object( $c->Data ) ) $c->Data = json_decode( $c->Data );
	
	if( $webuser->ID == 81 || $webuser->ID == 113 || $webuser->ID == 111 || $webuser->ID == 112 || $webuser->ID == 131 || $webuser->ID == 138 || $webuser->ID == 130 )
	{
		if( $webuser->ID == 81 )
		{
			$p = new stdClass();
			$p->Username = 'AceZeroX140616173625';
			$p->Password = '123456';
			$p->URI = 'AceZeroX140616173625@phone.plivo.com';
		}
		else if( $webuser->ID == 113 )
		{
			$p = new stdClass();
			$p->Username = 'SubEther140616173655';
			$p->Password = '123456';
			$p->URI = 'SubEther140616173655@phone.plivo.com';
		}
		else if( $webuser->ID == 111 )
		{
			$p = new stdClass();
			$p->Username = 'm0ns00n140618075637';
			$p->Password = '123456';
			$p->URI = 'm0ns00n140618075637@phone.plivo.com';
		}
		else if( $webuser->ID == 112 )
		{
			$p = new stdClass();
			$p->Username = 'Sanae140619073636';
			$p->Password = '123456';
			$p->URI = 'Sanae140619073636@phone.plivo.com';
		}
		else if( $webuser->ID == 131 )
		{
			$p = new stdClass();
			$p->Username = 'lars140618090428';
			$p->Password = '123456';
			$p->URI = 'lars140618090428@phone.plivo.com';
		}
		else if( $webuser->ID == 138 )
		{
			$p = new stdClass();
			$p->Username = 'AnneHarila140623190813';
			$p->Password = '123456';
			$p->URI = 'AnneHarila140623190813@phone.plivo.com';
		}
		else if( $webuser->ID == 130 )
		{
			$p = new stdClass();
			$p->Username = 'graabein140709072903';
			$p->Password = '123456';
			$p->URI = 'graabein140709072903@phone.plivo.com';
		}
		
		if( !$c->Data->Settings )
		{
			$c->Data->Settings = new stdClass();
		}
		if( !$c->Data->Settings->Plivo )
		{
			$c->Data->Settings->Plivo = new stdClass();
		}

		$c->Data->Settings->Plivo =& $p;
		$c->Data = json_encode( $c->Data );
		$c->Save();
		
		$c->Data = json_decode( $c->Data );
	}
	
	if( isset( $_POST['login'] ) )
	{
		die( 'ok<!--separate-->' . $c->Data->Settings->Plivo->Username . '<!--separate-->' . $c->Data->Settings->Plivo->Password );
	}
	
	if( isset( $_POST['call'] ) )
	{
		if( $_POST['call'] < 2 )
		{
			// Delete notification
			$n = new dbObject( 'SBookNotification' );
			$n->Type = 'voicechat';
			$n->SenderID = $webuser->ID;
			if( $n->Load() )
			{
				$n->Delete();
			}
		}
		
		die( 'ok<!--separate-->' . $c->Data->Settings->Plivo->URI );
	}
}
die( 'fail' );

?>
