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

global $database, $webuser;

$contacts = array(); $chatlist = false;

if( $data = getContacts( 'Users', $webuser->ID, 'IsApproved' ) )
{
	foreach( $data as $d )
	{
		$contacts[] = $d;
	}
}

if( $data = ContactRelations( $webuser->ContactID, 'Pending', 'ReceiverID' ) )
{
	foreach( $data as $d )
	{
		$contacts[] = $d;
	}
}

$connects = $database->fetchObjectRows ( 'SELECT * FROM SNodes WHERE IsConnected = "0" AND IsPending = "0" AND IsDenied = "0" AND IsAllowed = "0" ORDER BY ID ASC' );

$str = '<div class="inner"><ul>';

if( $contacts )
{
	foreach( $contacts as $cs )
	{
		$str .= '<li><div><a href="javascript:void(0)">';
		$str .= '<span><div class="image">';
		$i = new dbImage ();
		if( $i->load( $cs->ImageID ) )
		{
			$str .= $i->getImageHTML ( 50, 50, 'framed', false, 0xffffff );
		}
		$str .= '</div></span>';
		$str .= '<span><div class="name">' . GetUserDisplayname( $cs->ID ) . '</div></span>';
		$str .= '<span><div class="buttons">';
		$str .= '<button onclick="allowContact( ' . $cs->ID . ' )">' . i18n( 'i18n_Allow' ) . '</button>';
		$str .= '<button onclick="denyContact( ' . $cs->ID . ' )">' . i18n( 'i18n_Deny' ) . '</button>';
		$str .= '</div></span>';
		$str .= '</a></div></li>';
		
		$usr ? $usr = $usr . ',' . $cs->ID : $usr = $cs->ID;
	}
}

if( IsSystemAdmin() && $connects )
{
	foreach( $connects as $cs )
	{
		$str .= '<li><div><a href="javascript:void(0)">';
		$str .= '<span><div class="image" style="background: #dcdddd url(\'' . $cs->Url . 'favicon.ico\') center center no-repeat;">';
		$str .= '</div></span>';
		$str .= '<span><div class="name">' . $cs->Url . '</div></span>';
		$str .= '<span><div class="buttons">';
		$str .= '<button onclick="allowConnect( ' . $cs->ID . ' )">' . i18n( 'i18n_Allow' ) . '</button>';
		$str .= '<button onclick="denyConnect( ' . $cs->ID . ' )">' . i18n( 'i18n_Deny' ) . '</button>';
		$str .= '</div></span>';
		$str .= '</a></div></li>';
	}
}

$str .= '</ul></div>';

// Add support for chatlist in mobile version
if( 1!=1 && UserAgent() != 'web' )
{
	$chatlist = true;
	
	//$str .= '<div class="chatlist"><ul>';
	//$str .= '<li> --- add support for chatlist --- </li>';
	//$str .= '</ul></div>';
	
	$ostr = $str;
	
	include ( 'subether/components/chat/functions/chat.php' );
	
	$str = $ostr . $str;
}
else if ( !$contacts && !$connects )
{
	$chatlist = true;
	
	$str  = '<div class="inner"><ul>';
	$str .= '<li class="empty">' . i18n( 'i18n_No new contact requests' ) . '</li>';
	$str .= '</ul></div>';
}


if( $contacts || $connects || $chatlist )
{
	die( 'ok<!--separate-->' . $str . '<!--separate-->' . $usr );
}

die( 'fail - notification - contacts' );

?>
