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

// Make sure we only refresh the contact list every 15 secs
$time = time ();
if( !isset( $_SESSION[ 'contactlist_lasttime' ] ) )
{
	$_SESSION[ 'contactlist_lasttime' ] = $time;
}

if( ( $time - $_SESSION[ 'contactlist_lasttime' ] ) > 15 || !isset( $_SESSION[ 'contactlist' ] ) || !isset( $_SESSION['contactlist_presense'] ) )
{
	// TODO: Add support for images connected to user
	if( $contacts = ContactRelations( false, 'Contact' ) )
	{
		$_SESSION[ 'contactlist' ] = $contacts;
	}
	$_SESSION['contactlist_lasttime'] = time();
	
	// TODO: Create support for multiple userdata
	//$cdata = UserData( $cs->UserID );
	
	// Get all the online presenses
	$uids = array(); $usrs = array();
	foreach( $contacts as $cs )
	{
		$uids[$cs->UserID] = $cs->UserID;
		$uids[$cs->ID] = $cs->ID;
	}
	
	// TODO: Add this also to ContactRelations
	$unam = GetUserDisplayname( $usrs );
	
	// TODO: Add this into the UserOnline info
	$title = ( $us->CategoryID > 0 ? getCategoryByID( $us->CategoryID )->Name : '' );
	
	// TODO: Maybe make this more advanced with time away and so on, connected to last activity, and how many online
	$_SESSION['contactlist_presense'] = IsUserOnline( $uids );
}

$cstr = '';

// Refresh contacts from session	
if( $contacts = $_SESSION[ 'contactlist' ] )
{
	$array = array();
	
	$ii = 0; $k = 10; $mode = 'default'; $uid = array();
	
	$cstr .= '<div class="inner"><ul>';
	
	foreach( $contacts as $cs )
	{
		$us = ''; $con = ''; $vchat = '';
		
		$cs->Img = ( $cs->Img ? $cs->Img : 'admin/gfx/arenaicons/user_johndoe_32.png' );
		
		$cs->Status = 'admin/gfx/icons/bullet_white.png';
		
		$us = isset( $_SESSION['contactlist_presense'][$cs->UserID] ) ? $_SESSION['contactlist_presense'][$cs->UserID] : false;
		
		if( $us )
		{
			$cs->LastActivity = $us->LastActivity;
			if( ( date( 'YmdHi' ) - date( 'YmdHi', strtotime( $us->LastActivity ) ) ) > 10 )
			{
				$us = '';
			}
			else
			{
				$cs->LastActivity = '';
				$cs->Status = 'admin/gfx/icons/bullet_green.png';
				$uid[] = $cs->UserID;
				$ii++;
			}
		}
		
		if( !$cs->LastActivity && $usdata->Settings->Plivo->onLogin > 0 && $cdata->Settings->Plivo->onLogin > 0 )
		{
			$vchat = '<img class="voicechat" src="subether/gfx/conf_icon.png" onclick="callUser(\'' . $cs->UserID . '\', event);">';
		}
		
		// Onclick
		$onclick = 'chatObject.addPrivateChat(\'' . $cs->ID . '\',\'' . GetUserDisplayname( $cs->ID ) . '\',\'' . $cs->Img . '\');';
		
		// If mobile version
		if( ( $parent && $parent->agent && $parent->agent != 'web' && $parent->agent != 'browser' ) || UserAgent() != 'web' )
		{
			$onclick = 'chatObject.openContactMessage(\'' . $cs->ID . '\');';
		}
		else if( $usdata && $usdata->Settings->Chat == '1' )
		{
			$onclick = 'openWindow( \'Chat\', \'' . $cs->UserID . '\', \'chatwindow\', function(){ openPrivChat( \'' . $cs->ID . '\', \'' . GetUserDisplayname( $cs->ID ) . '\', \'window\' ); } );openChat();';
		}
		
		$con .= $cs->ID . '<!--var--><div class="contact" ' . ( $cs->Title ? ( 'title="idling at ' . $cs->Title . '"' ) : '' ) . '>';
		$con .= '<div id="c' . $cs->ID . '" class="anchor" style="visibility:hidden;"></div>';
		$con .= '<a href="javascript:void(0)" onclick="' . $onclick . 'return false;">';
		$con .= '<span><div class="image">';
		$con .= '<img src="' . $cs->Img . '" style="background-image:url(\'' . $cs->Img . '\')"/>';
		$con .= '</div></span>';
		$con .= '<span>' . GetUserDisplayname( $cs->ID ) . '</span><span>';
		$con .= '<div class="status"><span class="' . ( $vchat ? 'voicechat' : 'time' ) . '" time="' . $cs->LastActivity . '">' . ( $cs->LastActivity ? date( 'H:i', strtotime( $cs->LastActivity ) ) : $vchat ) . '</span> ';
		$con .= '<img src="' . $status . '"></div></span>';
		//$con .= '<span><div class="noti"></div></span>';
		$con .= '</a></div>';
		
		// If we have contactid from url get messages for this contact
		if( $cid > 0 && $cid == $cs->ID )
		{
			// Don't need this atm
			//$con .= $mstr;
		}
		
		$key = ( $us ? ( '1' . $k++ ) : ( $cs->LastActivity ? ( '2' . $k++ ) : ( '3' . $k++ ) ) );
		$array[$key] = $con;
	}
	
	ksort( $array );
	
	$uid = ( $uid ? implode( ',', $uid ) : 0 );
	
	if( isset( $_REQUEST[ 'component' ] ) && $_REQUEST[ 'component' ] == 'chat' )
	{
		// Output splittable "array" with index list :
		output( 'ok<!--separate-->' .   // 0 
			( $array && is_array( $array ) ? implode( '<!--contacts-->', $array ) : '' ) . '<!--separate-->' .  // 1 
			$uid . '<!--separate-->' .  // 2 online contacts
			$voicechat					// 3 user has voicechat feature
		);
	}
	
	foreach( $array as $key=>$arr )
	{
		if( strstr( $arr, '<!--var-->' ) )
		{
			$var = explode( '<!--var-->', $arr );
			
			$array[$key] = '<li id="ChatContact_' . $var[0] . '"' /*. ( $cid > 0 && $cid == $var[0] ? 'class="active "' : '' )*/ . '>' . $var[1] . '</li>';
		}
	}
	
	$cstr .= implode( $array );
	$cstr .= '</ul></div>';
}

if( isset( $_REQUEST[ 'component' ] ) && $_REQUEST[ 'component' ] == 'chat' )
{
	output( 'fail' );
}

?>
