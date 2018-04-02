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

$c = new dbObject( 'SBookCategory' );
if( $parent->folder->CategoryID > 0 && $c->Load( $parent->folder->CategoryID ) )
{
	if( !$c->Settings && $c->ID == 69 )
	{
		$p = new stdClass();
		$p->Username = 'acezerox1337140605204926';
		$p->Password = '123456789';
		$p->URI = 'acezerox1337140605204926@phone.plivo.com';
		
		$c->Settings->Plivo = new stdClass();
		$c->Settings->Plivo =& $p;
		$c->Settings = json_encode( $c->Settings );
		$c->Save();
	}
	/*else
	{
		$p = new stdClass();
		$p->Username = 'acezerox1337140605204926';
		$p->Password = '123456789';
		$p->URI = 'acezerox1337140605204926@phone.plivo.com';
		
		$c->Settings->Plivo = new stdClass();
		$c->Settings->Plivo =& $p;
		$c->Settings = json_encode( $c->Settings );
	}*/
	
	$c->Settings = json_decode( $c->Settings );
	
	if( isset( $_POST['login'] ) )
	{
		die( 'ok<!--separate-->' . $c->Settings->Plivo->Username . '<!--separate-->' . $c->Settings->Plivo->Password );
	}
	
	if( isset( $_POST['call'] ) )
	{
		die( 'ok<!--separate-->' . $c->Settings->Plivo->URI );
	}
}
die( 'fail' );

?>
