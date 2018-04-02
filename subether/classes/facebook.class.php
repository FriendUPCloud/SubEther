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

include_once ( 'posthandler.class.php' );
//include_once ( 'subether/thirdparty/php-facebook-sdk/src/Facebook/HttpClients/FacebookCurlHttpClient.php' );
//include_once ( 'subether/thirdparty/php-facebook-sdk/src/Facebook/HttpClients/FacebookHttpable.php' );

class Facebook
{
	var $Url = 'https://graph.facebook.com';
	var $Version = '4.0.23';
	var $Method = 'GET';
	var $Redirect;
	var $Path;
	var $Request;
	var $Certificate = '/home/treeroot/treeroot.org/public/subether/thirdparty/php-facebook-sdk/certs/DigiCertHighAssuranceEVRootCA.pem';
	
	function __construct( $_redirect, $_path )
	{
		$this->Redirect = $_redirect;
		$this->Path = $_path;
	}
	
	public function GetLoginUrl( $_type = false, $_scope = false )
	{
		$this->state = $this->storeState( 16 );
		
		$params = array(
			'client_id' => FACEBOOK_CLIENT_ID,
			'redirect_uri' => $this->Redirect,
			'state' => $this->state,
			'sdk' => 'php-sdk-' . $this->Version,
			'scope' => implode( ',', $_scope )
		);
		
		if( in_array( $_type, array( true, 'reauthenticate', 'https' ), true ) )
		{
			$params['auth_type'] = $_type === true ? 'reauthenticate' : $_type;
		}
		
		return 'https://www.facebook.com/' . FACEBOOK_API_VERSION . '/dialog/oauth?' . http_build_query( $params, null, '&' );
	}
	
	public function GetSession()
	{
		if( $this->isValidRedirect() )
		{
			$params = array(
			  'client_id' => FACEBOOK_CLIENT_ID,
			  'redirect_uri' => $this->Redirect,
			  'client_secret' => FACEBOOK_CLIENT_SECRET,
			  'code' => $this->getCode()
			);
			
			$response = (new FacebookRequest(
			  FacebookSession::newAppSession($this->appId, $this->appSecret),
			  'GET',
			  '/oauth/access_token',
			  $params
			))->execute()->getResponse();
			
			// Graph v2.3 and greater return objects on the /oauth/access_token endpoint
			$accessToken = null;
			if( is_object( $response ) && isset( $response->access_token ) )
			{
				$accessToken = $response->access_token;
			}
			elseif( is_array( $response ) && isset( $response['access_token'] ) )
			{
				$accessToken = $response['access_token'];
			}
			
			if( isset( $accessToken ) )
			{
				return new FacebookSession( $accessToken );
			}
		}
		return null;
	}
	
	public function execute()
	{
		$url = $this->getRequestURL();
		$params = $this->getParameters();
		
		if( $this->method === 'GET' )
		{
			$url = self::appendParamsToUrl( $url, $params );
			$params = array();
		}
		
		$connection = self::getHttpClientHandler();
		$connection->addRequestHeader( 'User-Agent', 'fb-php-' . $this->Version );
		$connection->addRequestHeader( 'Accept-Encoding', '*' ); // Support all available encodings.
		
		$result = $connection->send( $url, $this->method, $params );
		
		
	}
	
	
	
	
	
	private function random( $bytes )
	{
		$buf = '';
		
		if( !ini_get( 'open_basedir' ) && is_readable( '/dev/urandom' ) )
		{
			$fp = fopen( '/dev/urandom', 'rb' );
			if( $fp !== FALSE )
			{
				$buf = fread( $fp, $bytes );
				fclose( $fp );
				if( $buf !== FALSE )
				{
					return bin2hex( $buf );
				}
			}
		}
		
		if( function_exists( 'mcrypt_create_iv' ) )
		{
			$buf = mcrypt_create_iv( $bytes, MCRYPT_DEV_URANDOM );
			if( $buf !== FALSE )
			{
				return bin2hex( $buf );
			}
		}
		
		while( strlen( $buf ) < $bytes )
		{
			$buf .= md5( uniqid( mt_rand(), true ), true ); 
			// We are appending raw binary
		}
		
		return bin2hex( substr( $buf, 0, $bytes ) );
	}
	
	private function storeState( $bytes )
	{
		if( $_SESSION && $bytes )
		{
			return $_SESSION['FBRLH_state'] = $this->random( $bytes );
		}
	}
	
	private function loadState()
	{
		if( isset( $_SESSION['FBRLH_state'] ) )
		{
			return $this->state = $_SESSION['FBRLH_state'];
		}
		return null;
	}
		
	private function isValidRedirect()
	{
		$savedState = $this->loadState();
		
		if( !$this->getCode() || !isset( $_GET['state'] ) )
		{
			return false;
		}
		
		$givenState = $_GET['state'];
		$savedLen = mb_strlen($savedState);
		$givenLen = mb_strlen($givenState);
		
		if( $savedLen !== $givenLen )
		{
			return false;
		}
		
		$result = 0;
		
		for( $i = 0; $i < $savedLen; $i++ )
		{
			$result |= ord( $savedState[$i] ) ^ ord( $givenState[$i] );
		}
		return $result === 0;
	}
	
	private function getCode()
	{
		return isset( $_GET['code'] ) ? $_GET['code'] : null;
	}
	
	
	
	
	
	
	
	
	
	
	
	private function CustomAuth()
	{
		$url = '';
		$client_id = FACEBOOK_CLIENT_ID;
		$redirect_uri = 'http://treeroot.org/';
		$fb_user = FACEBOOK_DEV_USERNAME;
		$fb_pass = FACEBOOK_DEV_PASSWORD;
		
		
		
	}
	
	private function FirstAuth( $_path )
	{
		//$this->Url = 'https://www.facebook.com';
		$this->Path = $_path;
		$this->Request = $url = ( $this->Url . $_path );
		
		$this->Params = $params = array(
			'client_id' => FACEBOOK_CLIENT_ID,
			'redirect_uri' => $this->Redirect/*,
			'response_type' => 'code' */
		);
		
		if( $this->Method === 'GET' )
		{
			$url = $this->appendParamsToUrl( $url, $params );
		}
		
		$this->Header = $header = array(
			'User-Agent' => 'fb-php-' . $this->Version, 
			'Accept-Encoding' => '*' 
		);
		
		$this->Options = $options = array(
			CURLOPT_URL            => $url,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_RETURNTRANSFER => true, // Follow 301 redirects
			CURLOPT_HEADER         => true, // Enable header processing
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_CAINFO         => $this->Certificate,
			CURLOPT_HTTPHEADER	   => $this->compileRequestHeaders( $header ),
			CURLOPT_IPRESOLVE	   => CURL_IPRESOLVE_V4,
			CURLOPT_FAILONERROR	   => true
		);
		
		//$url = 'https://graph.facebook.com/oauth/authorize?client_id=' . urlencode ( FACEBOOK_CLIENT_ID ) . '&redirect_uri=' . urlencode ( 'http://treeroot.org/' );
		$url = 'https://www.facebook.com/dialog/oauth?client_id=' . urlencode ( FACEBOOK_CLIENT_ID ) . '&redirect_uri=' . urlencode ( 'http://treeroot.org/' ) . '&response_type=code';
		
		//die( print_r( $_SERVER,1 ) . ' --' );
		$ph = new PostHandler ( $url );
		$ph->SetHeader ( CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
		$ph->SetHeader ( CURLOPT_POSTFIELDS, 'email=' . urlencode( FACEBOOK_DEV_USERNAME ) . '&pass=' . urlencode( FACEBOOK_DEV_PASSWORD ) . '&login=Login' );
		//$ph->SetHeader ( CURLOPT_POSTFIELDS, 'email=' . urlencode( FACEBOOK_DEV_USERNAME ) . '&password=' . urlencode( FACEBOOK_DEV_PASSWORD ) . '&action=Login' );
		//$ph->SetHeader ( CURLOPT_CONNECTTIMEOUT, 30 );
		//$ph->SetHeader ( CURLOPT_COOKIEJAR, '/home/treeroot/treeroot.org/public/subether/thirdparty/php-facebook-sdk/facebook_cookies.txt' );
		//$ph->SetHeader ( CURLOPT_COOKIEFILE, '/home/treeroot/treeroot.org/public/subether/thirdparty/php-facebook-sdk/facebook_cookies.txt' );
		//$ph->SetHeader ( CURLOPT_COOKIEJAR, 'facebook_cookies.txt' );
		//$ph->SetHeader ( CURLOPT_COOKIEFILE, 'facebook_cookies.txt' );
		//$ph->SetHeader ( CURLOPT_TIMEOUT, 60 );
		//$ph->SetHeader ( CURLOPT_RETURNTRANSFER, true );
		//$ph->SetHeader ( CURLOPT_HEADER, true );
		//$ph->SetHeader ( CURLOPT_SSL_VERIFYHOST, 2 );
		//$ph->SetHeader ( CURLOPT_SSL_VERIFYPEER, false );
		//$ph->SetHeader ( CURLOPT_CAINFO, $this->Certificate );
		//$ph->SetHeader ( CURLOPT_HTTPHEADER, $this->compileRequestHeaders( $header ) );
		
		//$ph->AddVar ( 'email', FACEBOOK_DEV_USERNAME );
		//$ph->AddVar ( 'pass', FACEBOOK_DEV_PASSWORD );
		//$ph->AddVar ( 'login', 'Login' );
		
		$res = $ph->send();
		
		die( $res . ' .. ' . print_r( $_GET,1 ) . ' FirstAuth()' );
		
		/*$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	 
		$result = curl_exec($ch);
		curl_close($ch);
		
		die( $result . ' ..' );*/
		
		$ph = curl_init();
		curl_setopt_array( $ph, $options );
		$res = curl_exec( $ph );
		
		die( $res . ' .. ' . print_r( $this,1 ) );
	}
	
	private function Auth( $_path )
	{
		$this->Path = $_path;
		$this->Request = $url = ( $this->Url . '/' . FACEBOOK_API_VERSION . $_path );
		
		$this->Params = $params = array(
			'client_id' => FACEBOOK_CLIENT_ID,
			'redirect_uri' => $this->Redirect,
			'client_secret' => FACEBOOK_CLIENT_SECRET,
			'code' => $this->Code
		);
		
		if( $this->Method === 'GET' )
		{
			$url = $this->appendParamsToUrl( $url, $params );
		}
		
		$this->Header = $header = array(
			'User-Agent' => 'fb-php-' . $this->Version, 
			'Accept-Encoding' => '*' 
		);
		
		$this->Options = $options = array(
			CURLOPT_URL            => $url,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_RETURNTRANSFER => true, // Follow 301 redirects
			CURLOPT_HEADER         => true, // Enable header processing
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_CAINFO         => $this->Certificate,
			CURLOPT_HTTPHEADER	   => $this->compileRequestHeaders( $header ),
			CURLOPT_IPRESOLVE	   => CURL_IPRESOLVE_V4
		);
		
		$ph = curl_init();
		curl_setopt_array( $ph, $options );
		//$res = curl_exec( $ph );
		$res = curl_exec_follow( $ph );
		
		//$url = 'https://www.facebook.com/' . FACEBOOK_API_VERSION . '/dialog/oauth?' . http_build_query( $params, null, '&' );
		
		//die( $url . ' .. ' );
		
		//$ph = new PostHandler ( $url );
		//$ph->SetHeader ( 'User-Agent', 'fb-php-' . $this->Version );
		//$ph->SetHeader ( 'Accept-Encoding', '*' );
		//$ph->SetHeader ( 'CURLOPT_USERAGENT', 'fb-php-' . $this->Version );
		//$ph->SetHeader ( 'CURLOPT_ENCODING', '*' );
		
		/*$ph->SetHeader ( CURLOPT_CONNECTTIMEOUT, 10 );
		$ph->SetHeader ( CURLOPT_TIMEOUT, 60 );
		$ph->SetHeader ( CURLOPT_RETURNTRANSFER, true ); // Follow 301 redirects
		$ph->SetHeader ( CURLOPT_HEADER, true ); // Enable header processing
		$ph->SetHeader ( CURLOPT_SSL_VERIFYHOST, 2 );
		$ph->SetHeader ( CURLOPT_SSL_VERIFYPEER, true );
		$ph->SetHeader ( CURLOPT_CAINFO, $this->Certificate = ( '/home/treeroot/treeroot.org/public/subether/thirdparty/php-facebook-sdk/certs/DigiCertHighAssuranceEVRootCA.pem' ) );
		$ph->SetHeader ( CURLOPT_HTTPHEADER, $options );*/
		/*$ph->AddVar ( 'client_id', FACEBOOK_CLIENT_ID, $this->Method );
		$ph->AddVar ( 'redirect_uri', $this->Redirect, $this->Method );
		$ph->AddVar ( 'client_secret', FACEBOOK_CLIENT_SECRET, $this->Method );
		$ph->AddVar ( 'code', $this->Code, $this->Method );*/
		//$res = $ph->send();
		
		/*$url = $this->Request;
		
		$params = array(
			'client_id' => FACEBOOK_CLIENT_ID,
			'redirect_uri' => $this->Redirect,
			'client_secret' => FACEBOOK_CLIENT_SECRET,
			'code' => $this->Code
		);
		
		if( $this->method === 'GET' )
		{
			$url = $this->appendParamsToUrl( $url, $params );
			$params = array();
		}
		
		$curl = new FacebookCurlHttpClient();
		$curl->addRequestHeader( 'User-Agent', 'fb-php-' . $this->Version );
		$curl->addRequestHeader( 'Accept-Encoding', '*' ); // Support all available encodings.
		
		$res = $curl->send( $url, $this->method, $params );*/
		
		die( $res . ' -- ' . print_r( $this,1 ) . ' .. ' . $url );
	}
	
	
	// Get auth code from Bullhorn
	function getOAuthCode()
	{
		$url = 'https://graph.facebook.com/oauth/authorize?client_id=' . urlencode ( FACEBOOK_CLIENT_ID ) . '&redirect_uri=' . urlencode ( 'http://treeroot.org/' ) . '&response_type=code';
		
		$options = array( 
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $data,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => true,    
			/*CURLOPT_FOLLOWLOCATION => true,*/
			CURLOPT_AUTOREFERER    => true,    
			CURLOPT_CONNECTTIMEOUT => 120,
			CURLOPT_TIMEOUT        => 120,
			CURLOPT_USERAGENT      => "Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A543a Safari/419.3",
			CURLOPT_SSL_VERIFYPEER => false
		); 
	
		$ch  = curl_init ( $url );
		curl_setopt_array ( $ch, $options );
		//$content = curl_exec ( $ch );
		$content = $this->curl_exec_follow( $ch );
	
		curl_close ( $ch );
		
		$authcode = '';
		if ( preg_match ( '#Location: (.*)#', $content, $r ) )
		{
			$l = trim( $r[1] );
			$temp = preg_split ( "/code=/", $l );
			$authcode = $temp[1];
		}
		
		die( $content . ' -- ' . print_r( $_GET,1 ) . ' getOAuthCode() ' . $authcode );
		
		// 10. jan. 2013
		// Hogne was here: måtte se hvorfor denne feilet, men det viste seg
		// at brukerkontoen var utestengt..
		//else die ( 'Authcode was: ' . $authcode . "($content)\n" );
		//die( "\n".$url."\n".$data."\n".$authcode."\n" );
		return $authcode;
	}
	
	
	
	private function appendParamsToUrl( $url, $params = array() )
	{
		if( !$params )
		{
			return $url;
		}
		
		if( strpos( $url, '?' ) === false )
		{
			return $url . '?' . http_build_query( $params, null, '&' );
		}
		
		list( $path, $query_string ) = explode( '?', $url, 2 );
		parse_str( $query_string, $query_array );
		
		// Favor params from the original URL over $params
		$params = array_merge( $params, $query_array );
		
		return $path . '?' . http_build_query( $params, null, '&' );
	}
	
	public function compileRequestHeaders( $options )
	{
		$return = array();
		
		foreach ( $options as $key => $value )
		{
			$return[] = $key . ': ' . $value;
		}
		
		return $return;
	}
	
	
	
	private function sendCurl( $url, $method = 'GET', $parameters = array() )
	{
		$this->openCurlConnection( $url, $method, $parameters );
		$this->tryToSendRequest();
		
		// Separate the raw headers from the raw body
		list( $rawHeaders, $rawBody ) = $this->extractResponseHeadersAndBody();
		
		$this->responseHeaders = self::headersToArray( $rawHeaders );
		
		$this->closeConnection();
		
		return $rawBody;
	}
	
	public function openCurlConnection( $url, $method = 'GET', $parameters = array() )
	{
		$options = array(
			CURLOPT_URL            => $url,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_RETURNTRANSFER => true, // Follow 301 redirects
			CURLOPT_HEADER         => true, // Enable header processing
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_CAINFO         => $this->Certificate,
		);
		
		if( $method !== "GET" )
		{
			$options[CURLOPT_POSTFIELDS] = $parameters;
		}
		if( $method === 'DELETE' || $method === 'PUT' )
		{
			$options[CURLOPT_CUSTOMREQUEST] = $method;
		}
		
		if( !empty( $this->requestHeaders ) )
		{
			$options[CURLOPT_HTTPHEADER] = $this->compileRequestHeaders();
		}
		
		if( self::$disableIPv6 )
		{
			$options[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
		}
		
		self::$facebookCurl->init();
		self::$facebookCurl->setopt_array($options);
	}
	
	function curl_exec_follow( $ch, &$maxredirect = null )
	{
		$mr = $maxredirect === null ? 5 : intval( $maxredirect );
		if ( ini_get( 'open_basedir' ) == '' && ini_get( 'safe_mode' == 'Off' ) )
		{
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, $mr > 0 );
			curl_setopt( $ch, CURLOPT_MAXREDIRS, $mr );
		}
		else
		{
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
			if ( $mr > 0 )
			{
				$newurl = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );
				
				$rch = curl_copy_handle( $ch );
				curl_setopt( $rch, CURLOPT_HEADER, true );
				curl_setopt( $rch, CURLOPT_NOBODY, true );
				curl_setopt( $rch, CURLOPT_FORBID_REUSE, false );
				curl_setopt( $rch, CURLOPT_RETURNTRANSFER, true );
				do
				{
					curl_setopt( $rch, CURLOPT_URL, $newurl );
					$header = curl_exec( $rch );
					if ( curl_errno( $rch ) )
					{
						$code = 0;
					}
					else
					{
						$code = curl_getinfo( $rch, CURLINFO_HTTP_CODE );
						if ( $code == 301 || $code == 302 )
						{
							preg_match( '/Location:(.*?)\n/', $header, $matches );
							$newurl = trim( array_pop( $matches ) );
						}
						else
						{
							$code = 0;
						}
					}
				}
				while ( $code && --$mr );
				curl_close( $rch );
				if ( !$mr )
				{
					if ( $maxredirect === null )
					{
						trigger_error( 'Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING );
					}
					else
					{
						$maxredirect = 0;
					}
					return false;
				}
				curl_setopt( $ch, CURLOPT_URL, $newurl );
			}
		}
		return curl_exec( $ch );
	}
}

?>
