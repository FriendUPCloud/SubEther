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

$archive = array(
	1=>'Record1',
	2=>'Record2',
	3=>'Record3',
	4=>'Record4',
	5=>'Record5'
);


$str = '';

$str .= '<table id="EventWrapper" EventID="' . $_REQUEST['e'] . '"><tr><td class="leftCol">';

$str .= '<div class="broadcast">';
if ( $_REQUEST['e'] && ( $row = $database->fetchObjectRow( '
	SELECT
		*
	FROM
		SBookEvents
	WHERE
			Component = "broadcast"
		AND CategoryID = \'' . $parent->folder->CategoryID . '\'
		AND ID = \'' . $_REQUEST['e'] . '\' 
	ORDER BY
		ID DESC 
' ) ) )
{
	$str .= '<div class="video">';
	if ( $row->ExternalUrl )
	{
		$str .= '<iframe src="' . $row->ExternalUrl . 'player?layout=4&color=0xe7e7e7&autoPlay=true&mute=false&iconColorOver=0x888888&iconColor=0x777777&allowchat=true&height=300" style="border:0;outline:0" frameborder="0" scrolling="no"></iframe>';
	}
	$str .= '</div>';
	if ( $parent->folder->Permission == 'admin' || $parent->folder->Permission == 'owner' )
	{
		$str .= '<div class="buttons">';
		$str .= '<span onclick="if(ge(\'InfoPanel\').className==\'closed\'){ge(\'InfoPanel\').className=\'\'}else{ge(\'InfoPanel\').className=\'closed\'}"> ' . htmlentities( '</>' ) . ' </span>';
		$str .= '</div>';
		
		$str .= '<div id="InfoPanel" class="closed">';
		$str .= '<input type="text" id="EventUrl" name="url" value="' . $row->ExternalUrl . '" onclick="this.select();" />';
		$str .= '</div>';
	}
}
$str .= '<div class="chat">';
$str .= '<table><tr><td class="leftCol">';
$str .= '<div class="dialog">';
$str .= '<h4>Chat</h4>';
$str .= '<div class="inner"></div>';
$str .= '</div>';
$str .= '</td><td class="rightCol">';
$str .= '<div class="participants">';
$str .= '<h4>Participants</h4>';
$str .= '<div class="inner"></div>';
$str .= '</div>';
$str .= '</td></tr></table>';
$str .= '<div class="publish"></div>';
$str .= '</div>';
$str .= '</div>';

$str .= '</td><td class="rightCol">';

$str .= '<div class="archive">';
$str .= '<h4>Archive</h4>';
$str .= '<div class="inner">';
foreach( $archive as $k=>$arc )
{
	$str .= '<div class="recording">';
	$str .= '<div class="thumb">';
	$str .= '<div class="duration">03:42</div>';
	$str .= '</div>';
	$str .= '<div class="heading"><a href="' . $parent->route . '?e=1&a=' . $k . '">' . $arc . '</a></div>';
	$str .= '</div>';
}
$str .= '</div></div>';

$str .= '</td></tr></table>';

?>
