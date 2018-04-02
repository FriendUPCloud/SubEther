<?php


/*******************************************************************************
The contents of this file are subject to the Mozilla Public License
Version 1.1 (the "License"); you may not use this file except in
compliance with the License. You may obtain a copy of the License at
http://www.mozilla.org/MPL/

Software distributed under the License is distributed on an "AS IS"
basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
License for the specific language governing rights and limitations
under the License.

The Original Code is (C) 2004-2010 Blest AS.

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
Rune Nilssen
*******************************************************************************/



class cSMS
{
	var $_protocol;
	var $_message;
	var $_username;
	var $_password;
	var $_host;
	var $_receivers = array ( );
	
	function __construct ( $protocol )
	{
		$this->setProtocol ( $protocol );
	}
	function setProtocol ( $protocol )
	{
		switch ( strtolower ( $protocol ) )
		{
			case 'limb':
				$this->_protocol = strtolower ( $protocol );
				return true;
			case 'clickatell':
				$this->_protocol = strtolower ( $protocol );
				return true;
		}
		return false;
	}
	function addReceiver ( $recv )
	{
		if ( strpos ( $recv, ',' ) >= 0 )
		{
			$recv = explode ( ',', $recv );
			foreach ( $recv as $r )
			{
				$this->_receivers[] = trim ( $r );
			}
		}
		else if ( trim ( $recv ) )
			$this->_receivers[] = trim ( $recv );
	}
	function setMessage ( $msg )
	{
		if ( !trim ( $msg ) ) return false;
		$this->_message = $msg;
	}
	function setHostname ( $hst )
	{
		if ( !trim ( $hst ) ) return false;
		$this->_host = $hst;
	}
	function setUsername ( $user )
	{
		if ( !trim ( $user ) ) return false;
		$this->_username = $user;
	}
	function setPassword ( $pass )
	{
		if ( !trim ( $pass ) ) return false;
		$this->_password = $pass;
	}
	function setApiID ( $apiid )
	{
		if ( !trim ( $apiid ) ) return false;
		$this->_api_id = $apiid;
	}
	function setFromName ( $fn )
	{
		if ( !trim ( $fn ) ) return false;
		$this->_fromName = SMS_FROMNAME;
	}
	function send ( )
	{
		if ( trim ( $this->_message ) )
		{
			return $this->{'send_' . $this->_protocol} ( );
		}
		return false;
	}
	
	/** Protocol specific stuff *******************************************/
	
	// Sending with clickatell
	function send_clickatell ( )
	{
		if ( !count ( $this->_receivers ) )
			return false;
			
		$user = $this->_username;
		$pass = $this->_password;
		$api_id = $this->_api_id;
		
		$xml = '';
		foreach ( $this->_receivers as $recv )
		{
			// Add norway
			if ( strlen ( $recv ) == 8 )
				$recv = "47$recv";
			$xml .= "<sendMsg><api_id>$api_id</api_id><user>$user</user><password>$pass</password><to>$recv</to><text>" . $this->_message . "</text><unicode>0</unicode><from>" . $this->_fromName . "</from></sendMsg>";
		}
		
		// Make a new curl resource
		$curl = curl_init ();

		// Set target script
		curl_setopt ( $curl, CURLOPT_URL, $this->_host );

		// We are using POST
		curl_setopt ( $curl, CURLOPT_POST, true );

		// We don't want output to the browser
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );

		// Assign and urlencode the data to be posted
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, "data=" . urlencode ( "<clickAPI>$xml</clickAPI>" ) );

		// Do the salsa
		$output = curl_exec ( $curl ); // can be used for logging...

		// Close the curl resource
		curl_close ( $curl );
		
		// Try to log
		if ( $f = fopen ( 'upload/sms_clickatell.log', 'a+' ) )
		{
			fwrite ( $f, "
SMS Output log: " . date ( 'Y-m-d H:i:s' ) . "

{$output}
-------------------------------------------------------------------
Sent XML: 
{$xml}
*******************************************************************
" );
			fclose ( $f );
		}
	}
	
	// Sending with the limb protocol
	function send_limb ( )
	{
		if ( !count ( $this->_receivers ) )
			return false;
			
		$user = $this->_username;
		$pass = $this->_password;
			
		// Generate receiver string
		$r_str = '';
		foreach ( $this->_receivers as $recv )
		{
			$r_str .= '<receiver>' . $recv . '</receiver>';
		}
		
		// Some used variables
		$dateBase = strtotime ( date ( 'Y-m-d H:i:s' ) );
		$dateBase += 120;
		$date = explode( '-', date( 'Y-n-j', $dateBase ) );
		$clock = explode( ':', date( 'H:i:s', $dateBase ) );
		
		// Generate xml
		$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>
<kqrepsms>
	<method>send</method>
	<params>
		<access>
			<user>$user</user>
			<password>$pass</password>
		</access>
		<profiles>
			<profile>
				<id>1</id>
				<delete>false</delete>
				<messages>
					<message>
						<unixweek>false</unixweek>
						<text>" . utf8_decode ( $this->_message ) . "</text>
						<years>".( $date[ 0 ]?$date[ 0 ]:( '' ) )."</years>
						<months>".( $date[ 1 ]?$date[ 1 ]:( '' ) )."</months>
						<days>".( $date[ 2 ]?$date[ 2 ]:( '' ) )."</days>
						<hours>".( $clock[ 0 ]?$clock[ 0 ]:( '' ) )."</hours>
						<mins>".( $clock[ 1 ]?$clock[ 1 ]:( '' ) )."</mins>
						<weekday></weekday>
						<extraid>2</extraid>
						<receivers>
							$r_str
						</receivers>
					</message>
				</messages>
			</profile>
		</profiles>
	</params>
</kqrepsms>";
		
		
		// Make a new curl resource
		$curl = curl_init ();

		// Set target script
		curl_setopt ( $curl, CURLOPT_URL, $this->_host );

		// We are using POST
		curl_setopt ( $curl, CURLOPT_POST, true );

		// We don't want output to the browser
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );

		// Assign and urlencode the data to be posted
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, "XML=" . urlencode ( $xml ) );

		// Do the salsa
		$output = curl_exec ( $curl ); // can be used for logging...

		// Close the curl resource
		curl_close ( $curl );
	}
}
?>
