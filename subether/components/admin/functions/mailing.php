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

global $database;

$str  = '<div class="mailing"><div id="MainBox"><table><tr>';
$str .= '<td class="leftCol">';

$str .= '<div id="MailList"><ul>';
if( $members = getSBookGroupMembers( $parent->folder->CategoryID ) )
{
	foreach( $members as $m )
	{
		$str .= '<li><div><table><tr><td class="first">';
		$str .= '<div class="image">';
		$i = new dbImage ();
		if( $i->load( $m->ImageID ) )
		{
			$str .= $i->getImageHTML ( 35, 35, 'framed', false, 0xffffff );
		}
		$str .= '</div>';
		$str .= '</td><td class="second">';
		$str .= '<div>' . $m->Username . '</div>';
		$str .= '<div>' . $m->Email . '</div>';
		$str .= '</td><td class="last">';
		$str .= '<div><input type="checkbox" name="Check" value="' . $m->UserID . '"></div>';
		$str .= '</td></tr></table></div></li>';
	}
}
$str .= '<li><div><table><tr><td class="first">';
$str .= '<div class="image"></div>';
$str .= '</td><td class="second">';
$str .= '<div>Custom Email</div>';
$str .= '<div><input onchange="ge(\'CustomMail\').value=\'CustomMail:\'+this.value" placeholder="custom@email.com"/></div>';
$str .= '</td><td class="last">';
$str .= '<div><input id="CustomMail" type="checkbox" name="CustomMail"></div>';
$str .= '</td></tr></table></div></li>';
$str .= '</ul></div>';

$str .= '</td><td class="rightCol">';

$str .= '<div id="MessageBox"><div contenteditable="true" id="MessageContent"></div></div>';
$str .= '<div id="PostBox">';
$str .= '<div class="publish"><button type="button" onclick="sendAdminMail()">POST</button></div>';
$str .= '<div class="check">';
$str .= '<input type="checkbox" name="Message"> Send Internal ';
$str .= '<input type="checkbox" name="Email"> Send Email ';
$str .= '</div>';
$str .= '</div>';

$str .= '</td></tr></table></div></div>';

?>
