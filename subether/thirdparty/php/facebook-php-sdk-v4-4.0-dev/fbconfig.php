<?php
session_start();
// added in v4.0.0
require_once 'autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;
// init app with app id and secret
FacebookSession::setDefaultApplication( FACEBOOK_CLIENT_ID, FACEBOOK_CLIENT_SECRET );
// login helper with redirect_uri
    $helper = new FacebookRedirectLoginHelper( 'http://treeroot.org/en/home/messages/' );
try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  // When Facebook returns an error
} catch( Exception $ex ) {
  // When validation fails or other local issues
}
// see if we have a session
if ( isset( $session ) ) {
  // graph api request for user data
  $request = new FacebookRequest( $session, 'GET', '/me' );
  $response = $request->execute();
  // get response
  $graphObject = $response->getGraphObject();
     	$fbid = $graphObject->getProperty('id');              // To Get Facebook ID
 	    $fbfullname = $graphObject->getProperty('name'); // To Get Facebook full name
	    $femail = $graphObject->getProperty('email');    // To Get Facebook email ID
	/* ---- Session Variables -----*/
	    $_SESSION['FBID'] = $fbid;           
        $_SESSION['FULLNAME'] = $fbfullname;
	    $_SESSION['EMAIL'] =  $femail;
    /* ---- header location after session ----*/
	die( 'fbid: ' . $fbid . ' fbfullname: ' . $fbfullname . ' femail: ' . $femail . ' okey what now???' );
  header("Location: index.php");
} else {
  $loginUrl = $helper->getLoginUrl();
  
  $form = '<form action="' . $loginUrl . '&email=' . FACEBOOK_DEV_USERNAME . '&pass=' . FACEBOOK_DEV_PASSWORD . '&login=Login" method="get">';
				$loginUrl = explode( '?', $loginUrl );
				$loginUrl = explode( '&', $loginUrl[1] );
				foreach( $loginUrl as $key=>$pos )
				{
					$loginUrl[$key] = explode( '=', $pos );
					if( $loginUrl[$key][0] == 'redirect_uri' ) continue;
					$form .= '<input type="hidden" name="' . $loginUrl[$key][0]. '" value="' . $loginUrl[$key][1] . '">';
				}
  $form .=     '<input type="hidden" name="redirect_uri" value="http://treeroot.org/en/home/messages/">
				<input type="hidden" name="email" value="' . FACEBOOK_DEV_USERNAME . '">
				<input type="hidden" name="pass" value="' . FACEBOOK_DEV_PASSWORD . '">
				<input type="hidden" name="login" value="Login">
				<button type="submit">Login</button>
			  </form>';
  
  die( $form . ' -- ' );
 header("Location: ".$loginUrl);
}
?>