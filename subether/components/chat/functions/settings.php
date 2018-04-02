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

// Chat Settings
$c = new dbObject( 'SBookContact' );
$c->UserID = $webuser->ID;
if( $c->Load() )
{
	$c->Data = json_decode( $c->Data );
}

$str  = '<ul>';
$str .= '<li><input name="sound" type="checkbox" value="1" onclick="chatObject.saveChatSettings(this)" ' . ( $c->Data && ( !isset( $c->Data->Settings->Sound ) || $c->Data->Settings->Sound ) == '1' ? 'checked="checked"' : '' ) . '/><span> sound</span></li>';
$str .= '<li><input name="crypto" type="checkbox" value="1" onclick="chatObject.saveChatSettings(this)" ' . ( $c->Data && $c->Data->Settings->Crypto == '1' ? 'checked="checked"' : '' ) . '/><span> crypto</span></li>';
$str .= '<li><input name="mode" type="radio" value="0" onclick="chatObject.saveChatSettings(this)" ' . ( $c->Data && $c->Data->Settings->Chat == '1' ? '' : 'checked="checked"' ) . '/><span> default</span></li>';
//$str .= '<li><input name="mode" type="radio" value="1" onclick="chatObject.saveChatSettings(this)" ' . ( $c->Data && $c->Data->Settings->Chat == '1' ? 'checked="checked"' : '' ) . '/><span> window</span></li>';
$str .= '</ul>';

die( 'ok<!--separate-->' . $str );

?>
