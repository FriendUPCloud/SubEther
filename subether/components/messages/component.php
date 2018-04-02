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

global $document, $webuser;

if( isset( $_REQUEST['christester'] ) || isset( $_GET['code'] ) )
{
	$test = '<script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : \'' . FACEBOOK_CLIENT_ID . '\',
          xfbml      : true,
          version    : \'v2.3\'
        });
		FB.getLoginStatus(function(response) {
		if (response.status === \'connected\') {
		  console.log(\'Logged in.\');
		}
		else {
		  FB.login(function(response) {
			// Original FB.login code
		  }, { auth_type: \'reauthenticate\' })
		}
	  });
      };

      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
		 js.src = "//connect.facebook.net/en_US/sdk/debug.js";
         '/*js.src = "//connect.facebook.net/en_US/sdk.js";*/.'
         fjs.parentNode.insertBefore(js, fjs);
       }(document, \'script\', \'facebook-jssdk\'));
    </script>';
	
	//die( $test . ' --' );
	
	include ( 'subether/thirdparty/php/facebook-php-sdk-v4-4.0-dev/fbconfig.php' );
	
	/*include ( 'subether/classes/facebook.class.php' );
	
	$form = '<form action="https://graph.facebook.com/oauth/authorize?client_id=' . urlencode ( FACEBOOK_CLIENT_ID ) . '&redirect_uri=' . urlencode ( 'http://treeroot.org/' ) . '" method="post">
				<input type="hidden" name="email" value="' . FACEBOOK_DEV_USERNAME . '">
				<input type="hidden" name="pass" value="' . FACEBOOK_DEV_PASSWORD . '">
				<input type="hidden" name="login" value="Login">
				<button type="submit">Login</button>
			  </form>';
	
	//die( $form );
	
	$fb = new Facebook( 'http://treeroot.org/en/home/messages/' );
	$loginUrl = $fb->GetLoginUrl();
	$response = $fb->execute();
	
	die( 'hm???' );*/
}

// Redirect to chat component until it's moved completely to the chat component
include( 'subether/components/chat/component.php' );

/*
statistics( $parent->module, 'messages' );

$root = 'subether/';
$cbase = 'subether/components/messages';

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/messages.css' );
$document->addResource ( 'javascript', $cbase . '/javascript/messages.js' );

// Template: Module, Component, CategoryID, Status
SessionSet ( $parent->module, 'messages', $parent->folder->CategoryID, 'online' );

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	die( 'failed action request - messages' );
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
	die( 'failed function request - messages' );
}

include ( $cbase . '/functions/getmessages.php' );
$Component->ListIM = $str;
include ( $cbase . '/functions/getmessages.php' );
$Component->RightIM = $mstr;

statistics( $parent->module, 'messages' );*/

?>
