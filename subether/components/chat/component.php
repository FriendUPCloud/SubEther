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

global $document;

statistics( $parent->module, 'chat' );

$root = 'subether/';
$cbase = 'subether/components/chat';

include_once ( $cbase . '/include/dbcheck.php' );
include_once ( $cbase . '/include/functions.php' );

include_once ( 'subether/classes/fcrypto.class.php' );

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', 'subether/css/emoticons.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/messages.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/chat.css' );
$document->addResource ( 'stylesheet', $cbase . '/css/chatwindow.css' );
$document->addResource ( 'javascript', $cbase . '/javascript/audio.js' );
$document->addResource ( 'javascript', $root . '/javascript/messagehandler.js' );
$document->addResource ( 'javascript', $root . 'restapi/v1/api.js' );

if( $_REQUEST['view'] == 'mail' )
{
	$document->addResource ( 'stylesheet', $cbase . '/css/mail.css' );
	$document->addResource ( 'javascript', $cbase . '/javascript/mail.js' );
}

// Init chat system
if( isset( $Component ) )
{
	// TODO: Clean up the old code, new code is working and old is obsolete ...
	//if( !defined( 'CHAT_VERSION' ) )
	//	$document->addResource ( 'javascript', $cbase . '/javascript/chat.js' );
	//else 
	//{
		$document->addResource( 'javascript', $cbase . '/javascript/chatObject.js' );
		$document->addResource( 'javascript', $cbase . '/javascript/voicechat.js' );
		$Component->Load( $cbase . '/templates/component_v1.php' );
	//}
}

if( !strstr( $document->_bodyClasses, 'chat' ) )
{
	$document->_bodyClasses = ( $document->_bodyClasses ? $document->_bodyClasses . ' chat' : 'chat' );
}

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	output( 'failed action request - chat' );
}
// Check for user functions ----------------------------------------------------
else if ( isset( $_REQUEST[ 'function' ] ) )
{
	if ( file_exists ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' ) )
    {
       include ( $cbase . '/functions/' . $_REQUEST[ 'function' ] . '.php' );
    }
	else if ( file_exists ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' ) )
	{
		include ( $root . '/include/' . $_REQUEST[ 'function' ] . '.php' );
	}
	output( 'failed function request - chat' );
}
else
{
	
	if( $parent->position == 'MiddleCol' )
	{
		$Component->Load( $cbase . '/templates/messages.php' );
		//die( $parent->agent );
		// Mail
		if( $_REQUEST['view'] == 'mail' )
		{
			include ( $cbase . '/functions/accounts.php' );
			include ( $cbase . '/functions/mail.php' );
			
			$tstr  = '<div class="buttonslist">';
			$tstr .= '<span class="button account delete" onclick="deleteAccount()">Delete Account</span>';
			$tstr .= '<span class="seperator account"> | </span>';
			$tstr .= '<span class="button account edit" onclick="editAccount()">Edit Account</span>';
			$tstr .= '<span class="seperator account"> | </span>';
			$tstr .= '<span class="button account create" onclick="openWindow( \'Messages\', false, \'mail\' )">Create Account</span>';
			//$tstr .= '<span> | </span>';
			//$tstr .= '<span class="button" onclick="getHeaders()">Get Mail</span>';
			//$tstr .= '<span> | </span>';
			$tstr .= '<span class="button write" onclick="writeMail()">Write</span>';
			$tstr .= '</div>';
			
			$tstr .= '<div class="buttonsmail">';
			$tstr .= '<span class="button" onclick="sendMail()">Send</span>';
			//$tstr .= '<span> | </span>';
			//$tstr .= '<span class="button">Insert</span>';
			//$tstr .= '<span> | </span>';
			//$tstr .= '<span class="button">Save</span>';
			$tstr .= '</div>';
			
			$tstr .= '<div class="buttonsread">';
			$tstr .= '<span class="button" onclick="writeReply()">Reply</span>';
			//$tstr .= '<span> | </span>';
			//$tstr .= '<span class="button" onclick="writeReplyAll()">ReplyAll</span>';
			$tstr .= '<span class="seperator account"> | </span>';
			$tstr .= '<span class="button" onclick="writeForward()">Forward</span>';
			//$tstr .= '<span> | </span>';
			//$tstr .= '<span class="button">Archive</span>';
			//$tstr .= '<span> | </span>';
			//$tstr .= '<span class="button">Junk</span>';
			$tstr .= '<span class="seperator account"> | </span>';
			//$tstr .= '<span class="button" onclick="deleteMail()">Delete</span>';
			$tstr .= '<span class="button" onclick="moveMail(\'Trash\')">Delete</span>';
			$tstr .= '</div>';
			
			$Component->Folders = '<div id="Accounts_inner">' . $fstr . '</div>';
			$Component->Messages  = '<div class="MailBox"><div id="Mail_top">' . $tstr . '</div>';
			$Component->Messages .= '<div id="Mail_inner">' . $mstr . '</div></div>';
		}
		// Mobile mode ---
		else if( $parent && $parent->agent && $parent->agent != 'web' && $parent->agent != 'browser' && !strstr( $parent->agent, 'tablet' ) )
		{
			$Component->Load( $cbase . '/templates/mobile.php' );
			
			include ( $cbase . '/functions/messages.php' );
			//include ( $cbase . '/functions/contacts.php' );
			include ( $cbase . '/functions/chat.php' );
			
			$Component->Contacts = '<div id="Chat"><div id="Chat_list">' . $str . '</div></div>';
		}
		// Desktop mode ---
		else
		{
			//include ( $cbase . '/functions/contacts.php' );
			include ( $cbase . '/functions/folders.php' );
			include ( $cbase . '/functions/messages.php' );
			
			//$Component->Contacts = $cstr;
			$Component->Folders = '<div id="ListIM_inner">' . $fstr . '</div>';
			$Component->Messages = '<div id="RightIM_inner">' . $mstr . '</div>';
		}
		
		$Component->cid = $cid;
	}
	// Chat window mode ---
	else
	{
		include ( $cbase . '/functions/chat.php' );
	}
	
	$Component->Online = $ii;
}

statistics( $parent->module, 'chat' );

?>
