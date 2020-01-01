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

/*------------------------------------------------------------------------------

© 2011 Idéverket AS
@version 0.95

Usage with username and password (plain):

	$email = new eMail ();
	$email->setHostInfo ( "smtp.hostname.com", "user@name.com", "password" );
	$email->setSubject ( "Email subject" );
	$email->setFrom ( "frommail@yourhost.com" );
	$email->addRecipient ( "recipient@ahost.com" );
	$email->addHeader ( "Content-type", "text/html; charset=utf8" );
	$email->setMessage ( $yourHtmlMail );
	$email->send ();

The mail class will extract all images in the message and attach them to the 
e-mail.



------------------------------------------------------------------------------*/

class eMail 
{
	var $_recipients;
	var $_from;
	var $_headers;
	var $_subject;
	var $_smtpHelo;
	var $_smtpHostname;
	var $_smtpUsername;
	var $_smtpPassword;
	var $_smtpPort;
	var $_attachments;
	var $_rawfile;
	var $_embeddedimages;
	var $_message;
	var $_encoding;
	var $_contentType;
	var $_error_report;
	var $_error_reponse;
	
	function __construct ( $to = false, $subject = false, $from = false, $header = false )
	{
		$this->_encoding = 'iso-8859-1';
		$this->_contentType = 'multipart/related';
		
		// Init variables
		$this->purge ();
		
		// Set vars if possible
		if ( $to )
			$this->addRecipient ( $to );
		if ( $subject )
			$this->setSubject ( $subject );
		if ( $from )
			$this->setFrom ( $from );
		if ( $header )
			$this->addHeader ( $header );
		$this->setPort ( 25 );
	}
	
	function addRecipient ( $recp )
	{
		$this->_recipients[] = trim ( $recp );
	}
	
	function setPort ( $port )
	{
		$this->_smtpPort = $port;	
	}
	
	function setSubject ( $sub )
	{
		$this->_subject = trim ( $sub );
	}
	
	function setMessage ( $mes )
	{
		$this->_message = $mes;
	}
	
	function setFrom ( $from )
	{
		$this->_from = trim ( $from );
	}
	
	function addHeader ( $header, $value )
	{
		$this->_headers[strtolower(trim($header))] = trim ( $value );
		if ( strtolower(trim($header)) == 'content-type' )
		{
			if ( $value = explode ( ';', $value ) )
			{
				$en = explode ( 'charset=', $value[1] );
				if ( trim ( $en[1] ) )
				{
					$this->_encoding = trim ( $en[1] );
				}
			}
		}
	}
	
	function embedImage ( $imgurl, $width = false, $height = false, $tempName = false )
	{
		$o = new stdClass ();
		$o->Url = $imgurl;
		$o->Width = $width;
		$o->Height = $height;
		$o->TempName = $tempName;
		$this->_embeddedimages[] = $o;
	}
	
	function addAttachment ( $file )
	{
		$o = new stdClass ();
		$o->Url = $file;
		$this->_attachments[] = $o;
	}
	
	function addRawFile ( $data, $filename, $type = 'application/octet-stream', $encoding = 'base64' )
	{
		if ( $data && $filename )
		{
			$o = new stdClass ();
			$o->Data = $data;
			$o->Filename = $filename;
			$o->Encoding = $encoding;
			$o->Type = $type;
			$this->_rawfile[] = $o;
		}
	}
	
	function setHostInfo ( $host, $username, $password, $helo = false )
	{
		$this->_smtpHostname = $host;
		$this->_smtpUsername = $username;
		$this->_smtpPassword = $password;
		if ( $helo )
			$this->_smtpHelo = $helo;
		else $this->_smtpHelo = $username;
	}
	
	function send ( $rawdata = false )
	{
		if( defined( 'MAIL_TRANSPORT' ) && strtolower( MAIL_TRANSPORT ) == 'phpmailer' )
		{
			if( file_exists( 'lib/3rdparty/phpmailer/class.phpmailer.php' ) )
			{
				if( file_exists( 'lib/3rdparty/phpmailer/class.smtp.php' ) )
					require_once( 'lib/3rdparty/phpmailer/class.smtp.php' );
				require_once( 'lib/3rdparty/phpmailer/class.phpmailer.php' );
				
				$html = false;
				$mail = new PHPMailer();
				
				if( isset( $this->_headers ) && $headerdata = $this->_headers )
				{
					foreach( $headerdata as $k=>$d )
					{
						switch( strtolower( trim( $k ) ) )
						{
							case 'content-type':
								if( strstr( strtolower ( $d ), 'html' ) )
									$html = true;
								else $html = false;
								if( strstr( strtolower ( $d ), 'utf-8' ) )
									$mail->CharSet = 'UTF-8';
								break;
							default:
								break;
						}
					}
				}
				
				if( $html )
					$mail->isHTML( true );
				else $mail->isHTML( false );
				
				$mail->IsSMTP(); // telling the class to use SMTP
				
				$mail->Host = $this->_smtpHostname;
				
				$mail->helo = 'helo';
				//$mail->SMTPDebug = 2;  // debugging: 1 = errors and messages, 2 = messages only
				$mail->SMTPAuth = TRUE; // turn on SMTP authentication
				$mail->SMTPSecure = ( defined( 'MAIL_SECURITY' ) ? strtolower( MAIL_SECURITY ) : 'tls' ); // secure transfer enabled REQUIRED for GMail
				//$mail->SMTPAutoTLS = false;
				$mail->Port = $this->_smtpPort;
				$mail->Username = $this->_smtpUsername;
				$mail->Password = $this->_smtpPassword;
				$mail->From = $this->_from;

				$mail->FromName = MAIL_FROMNAME;
				$mail->AddReplyTo ( MAIL_REPLYTO, MAIL_FROMNAME );
				
				if ( isset( $this->_recipients ) && is_array ( $this->_recipients ) )
				{
					foreach( $this->_recipients as $r )
					{
						$mail->AddAddress( $r );
					}
				}
				else 
				{
					if( $this->_error_report )
					{
						$this->_error_reponse = 'No recipients.';
						return false;
					}
					return ArenaDie( 'No recipients.' );
				}
				
				// Add embeddedimages
				if( count( $this->_embeddedimages ) )
				{
					$i = 1;
					$cid = 1;
					foreach( $this->_embeddedimages as $o )
					{
						$mail->AddEmbeddedImage( $o->Url, $o->TempName );
					}
				}
				
				// Add embeddedimages
				if( count( $this->_attachments ) )
				{
					foreach( $this->_attachments as $o )
					{
						$fn = end( explode( '/', $o->Url ) );
						$mail->AddAttachment( $o->Url, $fn, 'base64', 'application/octet-stream' );
					}
				}
				
				// Add rawstringdata
				if( count( $this->_rawfile ) )
				{
					foreach( $this->_rawfile as $o )
					{
						$mail->AddStringAttachment( $o->Data, $o->Filename, ( $o->Encoding ? $o->Encoding : 'base64' ), ( $o->Type ? $o->Type : 'application/octet-stream' ) );;
					}
				}
				
				/*// Add attachments
				$extra = '....';
				if( count ( $this->_attachments ) )
				{
					$i = 1;
					$extra .= '<hr/><h2>Vedlegg:</h2>';
					for( $a = 0; $a < count( $this->_attachments ); $a++ )
					{
						$o = $this->_attachments[$a];
						
						$fn = explode ( '/', $o->Url ); $fn = $fn[count($fn)-1];
												
						$tmp = 'Embedded' . $i++;
						$mail->AddEmbeddedImage( $o->Url, $tmp );
						$extra .= '<p><a href="cid:' . $tmp . '">Last ned ' . $fn . '</a></p>';
						if( $f = fopen( '/tmp/logger.txt', 'a+' ) )
						{
							fwrite( $f, "\n" . $o->Url . " is embedded - (" . $fn . ")\n" );
							fwrite( $f, $extra . "\n" );
							fclose( $f );
						}
					}
				}*/
				
				// TODO: Make sure the hack edit doesn't dissapear in phpmailer
				
				$mail->Subject = $this->_subject;
				$mail->Body = $this->_message . $extra;
				if ( $html && ( !defined( 'MAIL_PROHIBIT_ATTACHMENTS' ) || ( defined( 'MAIL_PROHIBIT_ATTACHMENTS' ) && !MAIL_PROHIBIT_ATTACHMENTS ) ) )
				{
					//$mail->AltBody = strip_tags( $this->_message );
					$mail->AltBody = $this->_subject;
					
					if ( $rawdata )
					{
						//die( print_r( $rawdata, 1 ) . ' --' );
						$mail->_rawbody = $rawdata;
					}
				}
				$mail->WordWrap = 50;
				
				if( !$mail->Send() )
				{
					if ( isset( $this->_recipients ) && is_array ( $this->_recipients ) )
					{
						$complete = false;
						foreach( $this->_recipients as $r )
						{
							// To send HTML mail, the Content-type header must be set
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=' . $mail->CharSet . "\r\n";
							
							// Additional headers
							$headers .= 'From: ' . ( MAIL_FROMNAME ? MAIL_FROMNAME : 'SubEther' ) . ' <' . ( MAIL_USERNAME ? MAIL_USERNAME : 'noreply@sub-ether.org' ) . '>' . "\r\n";
							$headers .= 'Reply-To: ' . ( MAIL_REPLYTO ? MAIL_REPLYTO : 'noreply@sub-ether.org' ) . "\r\n";
							$headers .= 'X-Mailer: PHP/' . phpversion();
							
							// Mail it
							if( mail( $r, $this->_subject, ( $this->_message . $extra ), $headers ) )
							{
								$complete = true;
							}
						}
						if( $complete )
						{
							return 'All done!';
						}
					}
					
					if( $this->_error_report )
					{
						$this->_error_reponse = 'Sending mail failed. The SMTP server is not set up correctly. ' . $mail->ErrorInfo;
						return false;
					}
					return ArenaDie( 'Sending mail failed. The SMTP server is not set up correctly.' );
				}
				return 'All done!';
			}
		}
		
		// Vars
		$contentType = isset ( $this->_headers[ 'content-type' ] ) ? 
			$this->_headers[ 'content-type' ] : 'text/plain; charset=' . $this->_encoding;
		$boundry = 'ideverk-boundry!!';
		
		// If attachments are prohibited deny attachments and embedded images
		if( defined( 'MAIL_PROHIBIT_ATTACHMENTS' ) && MAIL_PROHIBIT_ATTACHMENTS )
		{
			$this->_attachments = false;
			$this->_embeddedimages = false;
		}
		
		// If no attachment is included change content type to header setup
		if( !$this->_attachments && !$this->_embeddedimages )
		{
			$this->_contentType = explode( ';', $contentType );
			$this->_contentType = $this->_contentType[0];
		}
		
		// Connect to server
		$errno = 0;
		$errstr = '';
		$timeout = 10; // ten secs
		if ( @( $socket = fsockopen ( $this->_smtpHostname, $this->_smtpPort, $errno, $errstr, $timeout ) ) )
		{
			$str = fread ( $socket, 128 );
			// check if we're in!
			if ( substr ( $str, 0, 3 ) == '220' )
			{
				// Say hello to server with username and password
				if ( $this->_smtpUsername && $this->_smtpPassword )
				{
					// First try ehlo
					$ehlo = 0;
					fwrite ( $socket, 'ehlo ' . $this->_smtpUsername . "\n" );
					if ( substr ( $str = fread ( $socket, 1024 ), 0, 3 ) != '250' )
					{ $errstr = $str; }
					else $ehlo = 1;
					
					// Then try helo if it doesn't work
					if ( !$ehlo && $errstr )
					{
						fwrite ( $socket, 'helo ' . $this->_smtpUsername . "\n" );
						if ( substr ( $str = fread ( $socket, 128 ), 0, 3 ) != '250' )
						{ $errstr = $str; }
						else $errstr = '';
					}
					
					// Send login info
					$usePassword = true;
					if ( !$ehlo )
					{
						if ( !$errstr )
						{
							fwrite ( $socket, 'auth login' . "\n" );
							if ( substr ( $str = fread ( $socket, 128 ), 0, 3 ) != '334' )
							{ 
								// If not "authentication not enabled"
								if ( substr ( $str, 0, 3 ) == '503' )
								{
									$errstr = '';
									$usePassword = false;
								}
								else $errstr = $str; 
							}
						}
						if ( !$errstr && $usePassword )
						{
							fwrite ( $socket, base64_encode ( $this->_smtpUsername ) . "\n" . base64_encode ( $this->_smtpPassword ) . "\n" );
							if ( substr ( $str = fread ( $socket, 128 ), 0, 3 ) != '334' )
							{ 
								$errstr = $str; 
							}
						}
					}
					// Try ehlo method
					else
					{
						// Login
						$errstr = '';
						fwrite ( $socket, 'auth login ' . base64_encode ( $this->_smtpUsername ) . "\n" );
						if ( substr ( $str = fread ( $socket, 128 ), 0, 3 ) != '334' )
						{ 
							// If not "authentication not enabled"
							if ( substr ( $str, 0, 3 ) == '503' )
							{
								$errstr = '';
								$usePassword = false;
							}
							else $errstr = $str; 
						}
						// Password
						else
						{
							fwrite ( $socket, base64_encode ( $this->_smtpPassword ) . "\n" );
							if ( substr ( $str = fread ( $socket, 128 ), 0, 3 ) != '235' )
							{ $errstr = $str; }
						}
					}
				}
				// Try without username and password
				else
				{
					fwrite ( $socket, 'helo ' . $this->_smtpHelo . "\n" );
					if ( substr ( $str = fread ( $socket, 128 ), 0, 3 ) != '250' )
					{ $errstr = $str; }
				}
				
				// Mail from
				if ( !$errstr )
				{
					fwrite ( $socket, 'mail from: ' . $this->_from . "\n" );
					if ( substr ( $str = fread ( $socket, 128 ), 0, 3 ) != '250' )
					{ $errstr = $str; }
				}
				
				// All recipients
				if ( !$errstr )
				{
					foreach ( $this->_recipients as $re )
					{
						$re = explode ( ' ', $re );
						fwrite ( $socket, 'rcpt to: ' . $re[0] . "\n" );
					}
					if ( substr ( $str = fread ( $socket, 128 ), 0, 3 ) != '250' )
					{ $errstr = $str; }
				}
				
				// Start data
				if ( !$errstr )
				{
					fwrite ( $socket, "data\n" );
					if ( substr ( $str = fread ( $socket, 128 ), 0, 3 ) != '354' )
					{ $errstr = $str; }
				}
				
				// Build message
				// 1. Set common header information
				$headers = "To: " . implode ( ',', $this->_recipients ) . "\r\n" . 
						   "Subject: =?UTF-8?B?" . base64_encode( $this->_subject ) . "?=" . "\r\n" . 
						   "From: =?UTF-8?B?" . base64_encode( $this->_from ) . "?= <" . $this->_from . ">" . "\r\n" . 
						   "MIME-Version: 1.0" . "\r\n";
				
				// 2. Set other headers
				$headers .= "Content-type: " . $this->_contentType . "; charset=" . $this->_encoding . "; boundary=" . $boundry . "\r\n";
				
				// 3. Set date
				$headers .= "Date: " . date ( 'D, d M Y H:i:s O (T)' ) . "\r\n";
				
				// 4. Add message
				if ( $this->_contentType == 'multipart/related' )
				{
					$headers .= '--' . $boundry . "\r\n";
					$headers .= 'Content-Transfer-Encoding: 8Bit' . "\r\n";
					$headers .= 'Content-Type: ' . $contentType . "\r\n\n";
					$headers .= $this->_message . "\r\n";
					$headers .= '--' . $boundry . "\r\n";
				}
				else
				{
					$headers .= $this->_message . "\r\n";
				}
				
				// 5. Add attachments
				if ( !$errstr && count ( $this->_attachments ) && $this->_contentType == 'multipart/related' )
				{
					$i = 1;
					foreach ( $this->_attachments as $o )
					{
						$data = file_get_contents ( $o->Url );
						$fn = explode ( '/', $o->Url ); $fn = $fn[count($fn)-1];
						
						$headers .= 'Content-Transfer-Encoding: base64' . "\r\n";
						$headers .= 'Content-Disposition: attachment; filename="' . $fn . '"' . "\r\n";
						$headers .= 'Content-Type: application/octet-stream' . "\r\n";
						$headers .= "\r\n";
						$headers .= chunk_split ( base64_encode ( $data ) ) . "\r\n";
						$headers .= '--' . $boundry . "\r\n";
					}
				}
				
				// 6. Embed images
				if ( !$errstr && count ( $this->_embeddedimages ) && $this->_contentType == 'multipart/related' )
				{
					$i = 1;
					foreach ( $this->_embeddedimages as $o )
					{
						$ext = explode ( '.', $o->Url );
						$fn = explode ( '/', $o->Url ); $fn = $fn[count($fn)-1];
						$ext = $ext[count($ext)-1];
						$data = file_get_contents ( $o->Url );
						$tempName = $o->TempName ? $o->TempName : ( 'image_' . $i++ );
						
						$headers .= 'Content-Transfer-Encoding: base64' . "\r\n";
						$headers .= 'Content-Disposition: inline; filename="' . $fn . '"' . "\r\n";
						$headers .= 'Content-Type: image/' . $ext . '; x-unix-mode=0644; name="' . $fn . '"' . "\r\n";
						$headers .= 'Content-ID: <' . $tempName . ">\r\n";
						$headers .= "\r\n";
						$headers .= chunk_split ( base64_encode ( $data ) ) . "\r\n";
						$headers .= '--' . $boundry . "\n";
					}
				}
				
				// 7. Add rawdata
				if ( !$errstr && $rawdata )
				{
					$i = 1;
					
					$raw = ( is_array( $rawdata ) ? $rawdata : array( $rawdata ) );
					
					foreach ( $raw as $dat )
					{
						$headers .= $dat . "\n";
						$headers .= '--' . $boundry . "\n";
					}
				}
				
				// 8. Finish message
				if ( $this->_contentType == 'multipart/related' )
				{
					$headers .= "--" . $boundry;
				}
				$headers .= "\r\n.\n";
				
				// Send email					
				if ( !$errstr )
				{
					fwrite ( $socket, $headers );
					if ( substr ( $str = fread ( $socket, 128 ), 0, 3 ) != '250' )
					{ $errstr = $str; }
				}
				
				// Hang up
				if ( !$errstr )
				{
					fwrite ( $socket, "QUIT\n" );
					if ( substr ( $str = fread ( $socket, 128 ), 0, 3 ) != '221' )
					{ $errstr = $str; }
					$errstr = 'All done!';
				}
			}
			
			// Disconnect
			fclose ( $socket );
			return $errstr;
		}
		else
		{
			$errorMessage = '';
			
			$to      = implode ( ',', $this->_recipients );
			$subject = $this->_subject;
			$message = $this->_message;
			$headers = 'From: ' . $this->_from . "\r\n" . 'Reply-To: ' . $this->_from . "\r\n" . 'X-Mailer: PHP/' . phpversion();
			
			$success = mail( $to, $subject, $message, $headers );
			
			if( !$success ) 
			{
				$errorMessage = error_get_last()['message'];
			}
			else
			{
				return 'All done!';
			}
			
			$f = 'Failed to connecto to host ' . $this->_smtpHostname . ' on port ' . $this->_smtpPort . '. ( ' . $errorMessage . ' ) ';
			
			if( $this->_error_report )
			{
				$this->_error_reponse = $f;
				return false;
			}
			if ( function_exists ( 'ArenaDie' ) )
			{
				ArenaDie ( $f );
			}
			else die ( $f );
		}
	}
	
	// Reset all values
	function purge ()
	{
		$this->_recipients = array ();
		$this->_from = '';
		$this->_headers = array ();
		$this->_subject = '';
		$this->_smtpUsername = '';
		$this->_smtpPassword = '';
		$this->_smtpHostname = '';
		$this->_smtpHelo = '';
		$this->_smtpPort = 25;
		$this->_attachments = array ();
		$this->_embeddedimages = array ();
		$this->_message = '';
	}
}

?>

