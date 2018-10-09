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

//include_once ( 'subether/thirdparty/php/phpseclib/Crypt/RSA.php' );
//include_once ( 'subether/thirdparty/php/phpseclib/Crypt/AES.php' );
//include_once ( 'subether/thirdparty/php/phpseclib/Crypt/Rijndael.php' );
//include_once ( 'subether/thirdparty/php/phpseclib/Crypt/Random.php' );
//include_once ( 'subether/thirdparty/php/phpseclib/Math/BigInteger.php' );

//include_once ( 'subether/thirdparty/php/phpseclib/Net/SSH2.php' );

//die( '<pre>' . print_r( $_COOKIE,1 ) . '</pre>' );
$str = '';

// --- Testing ------------

//$pw1 = hex_sha256( 'chris@ideverket.no' );
//$pw2 = hex_sha256( 'ideverket' );

//$pw3 = ( $pw1 . ':' . $pw2 );

//$pwh = hex_sha256( $pw3 );

if( isset( $_REQUEST['crontest'] ) )
{
	/*$cron = new PHPCron();
	//$res = $cron->Activate();
	$res = $cron->Test();
	
	if( $res )
	{
		die( 'ok -- ' . $res );
	}
	else
	{	
		die( 'fail --' . $res );
	}*/
	
	/*if( RunVirtualCronJobs() )
	{
		die( 'ok jobs done' );
	}
	die( 'fail or no need for action' );*/
}

if( isset( $_REQUEST['php'] ) )
{
	
	$fcrypt = new fcrypto();
	$keys = $fcrypt->generateKeys(); 
	
	$privateKey = $fcrypt->getPrivateKey();
	$publicKey  = $fcrypt->getPublicKey();
	
	/*$privateKey = '-----BEGIN RSA PRIVATE KEY-----
MIICWQIBAAKBgHzglB9n44EkgSfqEsiitDWs9xbzKIVdFZ9rbpz5RUILRs7h6A0E
au64pjnkh2sRIZGXru9zhYvPwYinqIMP7Gw4PrJJrnkl50oXL10lshYiNqQer9RF
45XuBWeLRPtQZXhq/j/dFZrXABReYzFDwXkUN+0KbNzXaUzeDKFi01r3AgEDAoGA
U0Biv5qXq22rb/Fh2xciznNPZKIbA5Njv5z0aKYuLAeEievwCK2cnyXEJphaR2DB
C7p0n6JZB9/WWxpwV1/y8dsVRI+P8N4cD5LJn57CCsV56vhkayC8eeBYXFUuLbVC
BOTaxyX0vTZ/tnMFXtTREL+6/sNU34IYk6munlhSLAsCQQDicYAxfRlo/dTBOEku
6m+0gBaw5kpN8iIUXhMegndyaKPECArupYeHYn5rhyd2aV+iZpB7PN0/DRdVd278
Leo5AkEAjS1LQNl2b79d+cikiKSWRX+s+TLpRta9CSLJ7P0/ThnNT64KNYD3fd4E
RlP7jh6AUjjeajCwVTdYCg9E4iourwJBAJb2VXZTZkX+jdYlhh9G9SMADyCZht6h
bA2UDL8BpPbwbS1asfRuWlpBqZ0ExPmblRbvCvzTPioIujj6Sf1z8XsCQF4eMis7
pEp/k/vbGFsYZC5VHft3Ri8501tshp3+KjQRM4p0Bs5V+lPpWC7ip7QUVYwl6Zwg
dY4k5VwKLewcHx8CQHhV6u+PtLy4wX+5q1gK84QlNMIHISSaq+dhQ+hOzeTlB82X
kqSNAIslzSauBs3+v9qbIJWAVES8jRi0frS4NnA=
-----END RSA PRIVATE KEY-----';
	$publicKey	= '-----BEGIN PUBLIC KEY-----
MIGcMA0GCSqGSIb3DQEBAQUAA4GKADCBhgKBgHzglB9n44EkgSfqEsiitDWs9xbz
KIVdFZ9rbpz5RUILRs7h6A0Eau64pjnkh2sRIZGXru9zhYvPwYinqIMP7Gw4PrJJ
rnkl50oXL10lshYiNqQer9RF45XuBWeLRPtQZXhq/j/dFZrXABReYzFDwXkUN+0K
bNzXaUzeDKFi01r3AgED
-----END PUBLIC KEY-----';*/
	
	$encrypted  = $fcrypt->encryptString( 'This is a test!', $publicKey );
	$ciphertext = $encrypted->cipher;
	
	$decrypted = $fcrypt->decryptString( $ciphertext, $privateKey );
	$plaintext = $decrypted->plaintext;
	
	//die( $ciphertext . ' -- ' . $plaintext );
	
	$signed      = $fcrypt->signCertificate( 'This is a test!', $publicKey, $privateKey );
	$certificate = $signed->certificate;
	$signid   	 = $signed->signature;
	
	$valid = $fcrypt->verifyCertificate( $certificate, $privateKey, $signid );
	
	//die( print_r( $signed,1 ) . ' -- ' . ( $valid ? 'Valid' : 'Not Valid' ) );
	
	$array = array(
		array(
			'privateKey' => '-----BEGIN RSA PRIVATE KEY-----MIICWgIBAAKBgQCGNPuIj2IemSCnxYPgMEQJF7xh/lpstnc2RgMuKkA5oY4Djnr1gmUSJv6mNGlXaA/E/QIuJJm4d5n/HeJ7UZl9IlIviamHqBcMksDuK+bQ/9aXJiy5UJmvs3tvCYFEFqI8MsU50S3pnbxPoMtUrN+GOZw4ubn2Jb/Uj81ZllLjTwIBAwKBgFl4p7Bflr8QwG/ZApV1grC6fZapkZ3O+iQurMlxgCZrtAJe/KOsQ2FvVG7Nm4+atS3+AXQYZnr6ZqoT7FI2ZlJ0sIduF14AbUy5fXSVz60U0FSCxJ92oYcPx1anD6RoZVkXjZf7GyMyxbDxLyANbRIRF5J6uvCSQWquDPqHcOj7AkEAw/YyhI38JbzFZAp6xZRyQyrseCf47O3DFNYOyp9eToYgMcAKe6bWXQZ//NA+u7b3x9h5gbmPTqtp6nEH8rn0DQJBAK9TMd/4foG2VBh6RIWa2x1zK+nd0TG5ogb6XkRKbyuEFl2wy1zeXnSNRjo0Zd0E81ggY3/n/fuySqBI2dhvkcsCQQCCpCGts/1ufdjtXFHZDaGCHJ2lb/tInoIN5Ancaj7fBBV2gAb9GeQ+BFVTNX8nz0/akFEBJl+Jx5vxoK/3JqKzAkB04iE/+v8BJDgQUYMDvJIToh1Gk+DL0RavUZQthvTHrWQ+ddzolD74Xi7ReEPorfeQFZeqmqlSdtxq2zvln7aHAkA8yVfKC6jP20mPQtjWwHd95DhNwEXhYAw+dY6gDV+/9M/PS/WMCn/n6pn+CxWZVA5+qOAcwZPr6QoE0PdSveer-----END RSA PRIVATE KEY-----',
			'publicKey' => '-----BEGIN PUBLIC KEY-----MIGdMA0GCSqGSIb3DQEBAQUAA4GLADCBhwKBgQCGNPuIj2IemSCnxYPgMEQJF7xh/lpstnc2RgMuKkA5oY4Djnr1gmUSJv6mNGlXaA/E/QIuJJm4d5n/HeJ7UZl9IlIviamHqBcMksDuK+bQ/9aXJiy5UJmvs3tvCYFEFqI8MsU50S3pnbxPoMtUrN+GOZw4ubn2Jb/Uj81ZllLjTwIBAw==-----END PUBLIC KEY-----',
			'password' => '3c30c5aa42e1511e87975f3584a3d7da8ba4abfd54ea7e76c5d47185d3a0f18d',
			'signature' => 'mcbFWm27d/YwmnenCdyvc5Nh4wINJFNUphjQ5Al5YULCEHr4RPoazUHAjhXHjivy+hryQC/Ub1fFzBeWOSyjfW8+rqeG1bICsf0HRVlpHZLys7b6Fw8wlx9A4ggoKkoyk2VKwbT8NsT967g5JZKmXkQC+i2D+X/0VBkK/D6Ghw==',
			'result' => false
		),
		array(
			'privateKey' => '-----BEGIN RSA PRIVATE KEY-----MIICWgIBAAKBgQCGNPuIj2IemSCnxYPgMEQJF7xh/lpstnc2RgMuKkA5oY4Djnr1gmUSJv6mNGlXaA/E/QIuJJm4d5n/HeJ7UZl9IlIviamHqBcMksDuK+bQ/9aXJiy5UJmvs3tvCYFEFqI8MsU50S3pnbxPoMtUrN+GOZw4ubn2Jb/Uj81ZllLjTwIBAwKBgFl4p7Bflr8QwG/ZApV1grC6fZapkZ3O+iQurMlxgCZrtAJe/KOsQ2FvVG7Nm4+atS3+AXQYZnr6ZqoT7FI2ZlJ0sIduF14AbUy5fXSVz60U0FSCxJ92oYcPx1anD6RoZVkXjZf7GyMyxbDxLyANbRIRF5J6uvCSQWquDPqHcOj7AkEAw/YyhI38JbzFZAp6xZRyQyrseCf47O3DFNYOyp9eToYgMcAKe6bWXQZ//NA+u7b3x9h5gbmPTqtp6nEH8rn0DQJBAK9TMd/4foG2VBh6RIWa2x1zK+nd0TG5ogb6XkRKbyuEFl2wy1zeXnSNRjo0Zd0E81ggY3/n/fuySqBI2dhvkcsCQQCCpCGts/1ufdjtXFHZDaGCHJ2lb/tInoIN5Ancaj7fBBV2gAb9GeQ+BFVTNX8nz0/akFEBJl+Jx5vxoK/3JqKzAkB04iE/+v8BJDgQUYMDvJIToh1Gk+DL0RavUZQthvTHrWQ+ddzolD74Xi7ReEPorfeQFZeqmqlSdtxq2zvln7aHAkA8yVfKC6jP20mPQtjWwHd95DhNwEXhYAw+dY6gDV+/9M/PS/WMCn/n6pn+CxWZVA5+qOAcwZPr6QoE0PdSveer-----END RSA PRIVATE KEY-----',
			'publicKey' => '-----BEGIN PUBLIC KEY-----MIGdMA0GCSqGSIb3DQEBAQUAA4GLADCBhwKBgQCGNPuIj2IemSCnxYPgMEQJF7xh/lpstnc2RgMuKkA5oY4Djnr1gmUSJv6mNGlXaA/E/QIuJJm4d5n/HeJ7UZl9IlIviamHqBcMksDuK+bQ/9aXJiy5UJmvs3tvCYFEFqI8MsU50S3pnbxPoMtUrN+GOZw4ubn2Jb/Uj81ZllLjTwIBAw==-----END PUBLIC KEY-----',
			'password' => 'bfb9efb416b148a846e5f5e8b22bc2bf765b53359a4e492ded8d347bcda3fdf1',
			'signature' => 'LITWqlYk+vUdegHk4h3ACjc7TQD4Sjtm5ASwLd1v/HRVIt4wp1AcrFVFV4wWFpNXKRoGg/3xoFoktuR+Q59+FFn22S+t0xn/NHAVUaz/ROX43elAtuBJi3V3hB8aF59SFBrXPqqN0bUqKL9E8tD8R1vHStBKWsdCgwcZUshh2pc=',
			'result' => false
		),
		array(
			'privateKey' => '-----BEGIN RSA PRIVATE KEY-----MIICWgIBAAKBgQCGNPuIj2IemSCnxYPgMEQJF7xh/lpstnc2RgMuKkA5oY4Djnr1gmUSJv6mNGlXaA/E/QIuJJm4d5n/HeJ7UZl9IlIviamHqBcMksDuK+bQ/9aXJiy5UJmvs3tvCYFEFqI8MsU50S3pnbxPoMtUrN+GOZw4ubn2Jb/Uj81ZllLjTwIBAwKBgFl4p7Bflr8QwG/ZApV1grC6fZapkZ3O+iQurMlxgCZrtAJe/KOsQ2FvVG7Nm4+atS3+AXQYZnr6ZqoT7FI2ZlJ0sIduF14AbUy5fXSVz60U0FSCxJ92oYcPx1anD6RoZVkXjZf7GyMyxbDxLyANbRIRF5J6uvCSQWquDPqHcOj7AkEAw/YyhI38JbzFZAp6xZRyQyrseCf47O3DFNYOyp9eToYgMcAKe6bWXQZ//NA+u7b3x9h5gbmPTqtp6nEH8rn0DQJBAK9TMd/4foG2VBh6RIWa2x1zK+nd0TG5ogb6XkRKbyuEFl2wy1zeXnSNRjo0Zd0E81ggY3/n/fuySqBI2dhvkcsCQQCCpCGts/1ufdjtXFHZDaGCHJ2lb/tInoIN5Ancaj7fBBV2gAb9GeQ+BFVTNX8nz0/akFEBJl+Jx5vxoK/3JqKzAkB04iE/+v8BJDgQUYMDvJIToh1Gk+DL0RavUZQthvTHrWQ+ddzolD74Xi7ReEPorfeQFZeqmqlSdtxq2zvln7aHAkA8yVfKC6jP20mPQtjWwHd95DhNwEXhYAw+dY6gDV+/9M/PS/WMCn/n6pn+CxWZVA5+qOAcwZPr6QoE0PdSveer-----END RSA PRIVATE KEY-----',
			'publicKey' => '-----BEGIN PUBLIC KEY-----MIGdMA0GCSqGSIb3DQEBAQUAA4GLADCBhwKBgQCGNPuIj2IemSCnxYPgMEQJF7xh/lpstnc2RgMuKkA5oY4Djnr1gmUSJv6mNGlXaA/E/QIuJJm4d5n/HeJ7UZl9IlIviamHqBcMksDuK+bQ/9aXJiy5UJmvs3tvCYFEFqI8MsU50S3pnbxPoMtUrN+GOZw4ubn2Jb/Uj81ZllLjTwIBAw==-----END PUBLIC KEY-----',
			'password' => 'bfb9efb416b148a846e5f5e8b22bc2bf765b53359a4e492ded8d347bcda3fdf1',
			'signature' => 'TN/yTJOnwN8g6jzHs6HueXykAA2E1s3T8LPwj8Wyx0/MttEM4DL8pUi0RgrYZ88eZTR+5Dx5duHCWqQYd+MzOkI8jos+yAF7Q8yXsR1vZgVLDxBZf4LqJNHBW4hNcJyC6AvLMApNXChgE08Z4A98h8RbuY518C41/EBYBguZE1Q=',
			'result' => false
		)
	);
	
	foreach( $array as $k=>$v )
	{
	
		/*$privateKey = '-----BEGIN RSA PRIVATE KEY-----MIICWgIBAAKBgQCGNPuIj2IemSCnxYPgMEQJF7xh/lpstnc2RgMuKkA5oY4Djnr1gmUSJv6mNGlXaA/E/QIuJJm4d5n/HeJ7UZl9IlIviamHqBcMksDuK+bQ/9aXJiy5UJmvs3tvCYFEFqI8MsU50S3pnbxPoMtUrN+GOZw4ubn2Jb/Uj81ZllLjTwIBAwKBgFl4p7Bflr8QwG/ZApV1grC6fZapkZ3O+iQurMlxgCZrtAJe/KOsQ2FvVG7Nm4+atS3+AXQYZnr6ZqoT7FI2ZlJ0sIduF14AbUy5fXSVz60U0FSCxJ92oYcPx1anD6RoZVkXjZf7GyMyxbDxLyANbRIRF5J6uvCSQWquDPqHcOj7AkEAw/YyhI38JbzFZAp6xZRyQyrseCf47O3DFNYOyp9eToYgMcAKe6bWXQZ//NA+u7b3x9h5gbmPTqtp6nEH8rn0DQJBAK9TMd/4foG2VBh6RIWa2x1zK+nd0TG5ogb6XkRKbyuEFl2wy1zeXnSNRjo0Zd0E81ggY3/n/fuySqBI2dhvkcsCQQCCpCGts/1ufdjtXFHZDaGCHJ2lb/tInoIN5Ancaj7fBBV2gAb9GeQ+BFVTNX8nz0/akFEBJl+Jx5vxoK/3JqKzAkB04iE/+v8BJDgQUYMDvJIToh1Gk+DL0RavUZQthvTHrWQ+ddzolD74Xi7ReEPorfeQFZeqmqlSdtxq2zvln7aHAkA8yVfKC6jP20mPQtjWwHd95DhNwEXhYAw+dY6gDV+/9M/PS/WMCn/n6pn+CxWZVA5+qOAcwZPr6QoE0PdSveer-----END RSA PRIVATE KEY-----';*/
		
		$signature = $fcrypt->signString( $v['password'], $v['privateKey'] );
	
		//$sign1 = $signature;
	
		//$signature = 'mcbFWm27d/YwmnenCdyvc5Nh4wINJFNUphjQ5Al5YULCEHr4RPoazUHAjhXHjivy+hryQC/Ub1fFzBeWOSyjfW8+rqeG1bICsf0HRVlpHZLys7b6Fw8wlx9A4ggoKkoyk2VKwbT8NsT967g5JZKmXkQC+i2D+X/0VBkK/D6Ghw==';
	
		/*$publicKey = '-----BEGIN PUBLIC KEY-----MIGdMA0GCSqGSIb3DQEBAQUAA4GLADCBhwKBgQCGNPuIj2IemSCnxYPgMEQJF7xh/lpstnc2RgMuKkA5oY4Djnr1gmUSJv6mNGlXaA/E/QIuJJm4d5n/HeJ7UZl9IlIviamHqBcMksDuK+bQ/9aXJiy5UJmvs3tvCYFEFqI8MsU50S3pnbxPoMtUrN+GOZw4ubn2Jb/Uj81ZllLjTwIBAw==-----END PUBLIC KEY-----';*/
	
		$valid = $fcrypt->verifyString( $v['password'], $v['signature'], $v['publicKey'] );
		
		//die( $signature . ' -- ' . ( $valid ? 'Valid' : 'Not Valid' ) . ' -- ' . '3c30c5aa42e1511e87975f3584a3d7da8ba4abfd54ea7e76c5d47185d3a0f18d' . ' [||] ' . $publicKey . "\r\n\r\n" . $sign1 . ' [||] ' . $privateKey );
		
		$array[$k]['result'] = ( $signature . ' -- ' . ( $valid ? 'Valid' : 'Not Valid' ) );
	}
	
	die( print_r( $array, 1 ) );
	
	$key = $fcrypt->generateKey( 'ideverket' );
	
	die( print_r( $keys,1 ) . ' -- ' . $ciphertext . ' -- ' . $plaintext . ' -- ' . $certificate . ' -- ' . $signid . ' -- ' . $signature . ' -- ' . ( $valid ? 'Valid' : 'Not Valid' ) . ' -- ' . print_r( $key,1 ) );
	
	
	
	/*$encrypted = base64_decode("IkmR6MwNFxEGCpouQuAGhl3joEuB86+vYowBnLrgATs="); // data_base64 from JS
	$iv        = base64_decode("EBESExQVFhcYGRobHB0eHw==");   // iv_base64 from JS
	$key       = base64_decode("q/qPgx1bFlIgZe145jvEZwt71FMVbKKus5QeqWjB3NU=");  // key_base64 from JS

	$plaintext = mcrypt_decrypt( MCRYPT_RIJNDAEL_128, $key, $encrypted, MCRYPT_MODE_CBC, $iv );
	$l = strlen( $plaintext ) - 1;
	$m = 0;
	for( $a = $l; $a > 0; $a-- )
	{
		if( $m == 0 )
		{
			$ch = ord( $plaintext{$a} );
			if( $ch == 0 || $ch == 11 ) continue;
			else $m = 1;
		}
		if( $m == 1 ) break;
	}
	$plaintext = substr( $plaintext, 0, $a );
	
	
	die( $plaintext );
	
	$aeskey = '95048106920180afbb0aa70f6460050a0e563bd10ddde721b820530cab565f3f';
	$string = 'This is a test!';
	
	$cipher = MCRYPT_RIJNDAEL_128;
	//$key = $aeskey;
	$data = $string;
	$mode = MCRYPT_MODE_CBC;
	$iv_size = mcrypt_get_iv_size( $cipher, $mode );
	$iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
	$encrypted = mcrypt_encrypt( $cipher, $key, $data, $mode, $iv );
	
	$data_base64 = base64_encode( $encrypted );
	$iv_base64 = base64_encode( $iv );
	$key_base64 = base64_encode( $key );
	
	die( $plaintext . ' -- :D:D:D:D:D | Data(b64): ' . $data_base64 . ' iv(b64): ' . $iv_base64 . ' key(b64): ' . $key_base64 );*/
	
	
	function GenerateRSAKeys()
	{
		$rsa = new Crypt_RSA();
		//$rsa->setPrivateKeyFormat(CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
		//$rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_PKCS1);
		
		//define('CRYPT_RSA_EXPONENT', 65537);
		//define('CRYPT_RSA_EXPONENT', '01001');
		//define('CRYPT_RSA_SMALLEST_PRIME', 64); // makes it so multi-prime RSA is used
		$keys = $rsa->createKey(1024); // == $rsa->createKey(1024) where 1024 is the key size
		
		$string = 'This is a test!';
		
		//$aeskey = 'abcdefghijklmnopqrstuvwxyz123456';
		
		//die( decryptAESCBC( encryptAESCBC( $string, $aeskey ), $aeskey ) . ' --' );
		
		//$encrypted = EncryptString( $string, $keys['publickey'] );
		
		$encrypted = EncryptStringAES( $string, $keys['publickey'] );
		
		//$decrypted = DecryptString( $encrypted, $keys['privatekey'] );
		$decrypted = DecryptStringAES( $encrypted->cipher, $keys['privatekey'] );
		
		die( ' -- ' . print_r( $keys,1 ) . ' .. encrypted: ' . $encrypted->cipher . ' -- decrypted: ' . $decrypted->plaintext );
		
		$signed = SignMessageRSA( $string, $keys['privatekey'] );
		
		$verified = VerifyMessageRSA( $string, $signed, $keys['publickey'] );
		
		die( print_r( $keys,1 ) . "\r\n\r\n Encrypted: " . $encrypted . "\r\n\r\n Decrypted: " . $decrypted . "\r\n\r\n Signed: " . $signed . "\r\n\r\n Verification: " . $verified );
	}
	
	function EncryptStringAES( $str, $pubkey )
	{		
		//$output = new stdClass();
		
        //$aeskey = generateAESKey();
		
		//$cipherblock = '';
		
		/*print( 'String: ' . $str . "\n\n" );
		print( 'Aeskey: ' . $aeskey . "\n\n" );
		
		print( 'Cipher0: ' . encryptRSA( $aeskey, $pubkey ) . "\n\n" );
		print( 'AESCBC: ' . encryptAESCBC( $str, $aeskey ) . "\n\n" );
		
		print( "Result: \n" );*/
		
		//$cipherblock .= base64_encode( encryptRSA( $aeskey, $pubkey ) ) . "?";
        //$cipherblock .= base64_encode( encryptAESCBC( $str, $aeskey ) );
		
		$aesencrypted = encryptAESCBC( 'This is a test!', 'abcdefghijklmnopqrstuvwxyz123456' );
		$encryptedb64 = base64_encode( $aesencrypted );
		
		$encryptedbin = base64_decode( $encryptedb64 );
		$aesdecrypted = decryptAESCBC( $encryptedbin, 'abcdefghijklmnopqrstuvwxyz123456' );
		
		die(
			'AES Encrypted: ' . $aesencrypted . "\n\n" .
			'Base64 Encoded: ' . $encryptedb64 . "\n\n" .
			'PlainText: ' . 'This is a test!' . "\n\n" .
			'AESKey: ' . 'abcdefghijklmnopqrstuvwxyz123456' . "\n\n" . 
			'Base64 Decoded: ' . $encryptedbin . "\n\n" . 
			'Decrypted: ' . $aesdecrypted . "\n\n"
		);
		
		//$output->status = 'success';
		//$output->cipher = chunk_split( $cipherblock, 64 );
		//$output->cipher = $cipherblock;
		//die( print_r( string2bytes( $aeskey ),1 ) . ' -- ' . $aeskey . ' -- ' . base64_encode( $aeskey ) . ' -- ' . print_r( $keys,1 ) . ' -- ' . chunk_split( $cipherblock, 64 ) . ' -- ' . $str . ' -- '/* . decryptAESCBC( base64_decode( $cipherblock ), $aeskey )*/ );
		//return $output;
	}
	
	function encryptAESCBC( $str, $aeskey )
	{
		/*$cipher = new Crypt_AES(); // could use CRYPT_AES_MODE_CBC
		// keys are null-padded to the closest valid size
		// longer than the longest key and it's truncated
		$cipher->setKeyLength(256);
		$cipher->setKey( $aeskey );
		//$cipher->setIV('...'); // defaults to all-NULLs if not explicitely defined
		
		return $cipher->encrypt( $str );*/
		
		$cipher = new Crypt_Rijndael( CRYPT_RIJNDAEL_MODE_CBC ); // could use CRYPT_RIJNDAEL_MODE_CBC
		$cipher->setBlockLength(256);
		//$cipher->setBlockLength(128);
		// keys are null-padded to the closest valid size
		// longer than the longest key and it's truncated
		$cipher->setKeyLength(256);
		$cipher->setKey( $aeskey );
		//$cipher->setIV('...'); // defaults to all-NULLs if not explicitely defined
		//$cipher->disablePadding();
		
		return $cipher->encrypt( $str );
	}
	
	function decryptAESCBC( $str, $aeskey )
	{
		/*$cipher = new Crypt_AES(); // could use CRYPT_AES_MODE_CBC
		// keys are null-padded to the closest valid size
		// longer than the longest key and it's truncated
		$cipher->setKeyLength(256);
		$cipher->setKey( $aeskey );
		//$cipher->setIV('...'); // defaults to all-NULLs if not explicitely defined
		
		return $cipher->decrypt( $str );*/
		
		$cipher = new Crypt_Rijndael( CRYPT_RIJNDAEL_MODE_CBC ); // could use CRYPT_RIJNDAEL_MODE_CBC
		$cipher->setBlockLength(256);
		//$cipher->setBlockLength(128);
		// keys are null-padded to the closest valid size
		// longer than the longest key and it's truncated
		$cipher->setKeyLength(256);
		$cipher->setKey( $aeskey );
		//$cipher->setIV('...'); // defaults to all-NULLs if not explicitely defined
		//$cipher->disablePadding();
		
		return $cipher->decrypt( $str );
	}
	
	function DecryptStringAES( $str, $privkey )
	{
		$output = new stdClass();
		
        $cipherblock = explode( "?", $str );
        $aeskey = decryptRSA( base64_decode( $cipherblock[0] ), $privkey );
		
        if( !$aeskey )
        {
			$output->status = 'failure';
            return $output;
        }
		
		$plaintext = decryptAESCBC( base64_decode( $cipherblock[1] ), base64_decode( $aeskey ) );
		
		$output->status = 'success';
		$output->plaintext = $plaintext;
		$output->signature = 'unsigned';
		
		return $output;
	}
	
	function EncryptString( $str, $pubkey )
	{
		$rsa = new Crypt_RSA();
		
		$rsa->loadKey( $pubkey );
		$rsa->setEncryptionMode( CRYPT_RSA_ENCRYPTION_PKCS1 );
		$encrypted = $rsa->encrypt( $str );
		
		$base64 = base64_encode( $encrypted );
		
		return chunk_split( $base64, 64 );
	}
	
	function DecryptString( $str, $privkey )
	{
		$rsa = new Crypt_RSA();
		
		$rsa->loadKey( $privkey );
		$rsa->setEncryptionMode( CRYPT_RSA_ENCRYPTION_PKCS1 );
		
		$str = base64_decode( $str );
		
		return $rsa->decrypt( $str );
	}
	
	function SignMessageRSA( $msg, $privkey )
	{
		$rsa = new Crypt_RSA();
		//$rsa->setPassword('password');
		$rsa->loadKey( $privkey ); // private key
		
		$rsa->setSignatureMode( CRYPT_RSA_SIGNATURE_PKCS1 );
		$signature = $rsa->sign( $msg );
		
		$base64 = base64_encode( $signature );
		
		return chunk_split( $base64, 64 );
	}
	
	function VerifyMessageRSA( $msg, $str, $pubkey )
	{
		$rsa = new Crypt_RSA();
		//$rsa->setPassword('password');
		$rsa->loadKey( $pubkey ); // public key
		
		$rsa->setSignatureMode( CRYPT_RSA_SIGNATURE_PKCS1 );
		
		$str = base64_decode( $str );
		
		return ( $rsa->verify( $msg, $str ) ? 'verified' : 'unverified' );
	}
	
	function SignCertificateRSA( $data, $privkey, $pubkey )
	{
		
	}
	
	function VerifyCertificateRSA( $cert, $privkey )
	{
		
	}
	
	// Converts a string to a byte array.
    function string2bytes( $string )
    {
        $bytes = array();
        for ( $i = 0; $i < strlen( $string ); $i++ ) 
        {
            $bytes[] = ord( $string{$i} );
        }
        return $bytes;
    }
	
	// Converts a byte array to a string.
    function bytes2string( $bytes )
    {
        $string = "";
        for ( $i = 0; $i < count( $bytes ); $i++ )
        {
            $string .= chr( $bytes[$i] );
        }   
        return $string;
    }
	
	function generateAESKey()
	{
		return crypt_random_string(32);
	}
	
	function encryptRSA( $str, $pubkey )
	{
		$rsa = new Crypt_RSA();
		
		$rsa->loadKey( $pubkey );
		$rsa->setEncryptionMode( CRYPT_RSA_ENCRYPTION_PKCS1 );
		
		return $rsa->encrypt( $str );
	}
	
	function decryptRSA( $cipher, $privkey )
	{
		$rsa = new Crypt_RSA();
		
		$rsa->loadKey( $privkey );
		$rsa->setEncryptionMode( CRYPT_RSA_ENCRYPTION_PKCS1 );
		
		return $rsa->decrypt( $cipher );
	}
	
	function signCert( $data, $pubkey, $signkey )
	{
		/*//publickeystring = my.stripHeader(publickeystring);
        var cipherblock = "";
        var aeskey = my.generateAESKey();
        try
        {
            var publickey = my.publicKeyFromString(publickeystring);
            cipherblock += my.b16to64(publickey.encrypt(my.bytes2string(aeskey))) + "?";
        }
        catch(err)
        {
            return {status: "Invalid public key"};
        }
        if(signingkey)
        {
            signString = cryptico.b16to64(signingkey.signString(plaintext, "sha256"));
            plaintext += "::52cee64bb3a38f6403386519a39ac91c::";
            plaintext += cryptico.publicKeyString(signingkey);
            plaintext += "::52cee64bb3a38f6403386519a39ac91c::";
            plaintext += signString;
        }
        cipherblock += my.encryptAESCBC(plaintext, aeskey);
        cipherblock = linebrk( cipherblock, 64 );
        return {status: "success", cipher: cipherblock};*/
	}
	
	function verifyCert()
	{
		
	}
	
	/*function GenerateKeys()
	{
		$crypt = new PHPEncrypt( 1024 );
		//$crypt->SetHashPassword(  );
		$crypt->GetKey();
		$privkey = $crypt->GetPrivateKey();
		$pubkey = $crypt->GetPublicKey();
		
		$string = 'This is a PHP test!';
		
		$encrypted = EncryptString( $string, $pubkey );
		
		die( print_r( $crypt,1 ) . "<br><br>\r\n String: " . $string . "\r\n<br><br>\r\n Encoded: " . $encrypted . "\r\n<br><br>\r\n Decoded: " );
	}
	
	function EncryptString( $str, $pubkey )
	{
		// Create the encryption object.
		$crypt = new PHPEncrypt();
		
		// Set the public.
		$crypt->SetPublicKey( $pubkey );
		
		//return $crypt->Encrypt( $str );
		return $crypt->EncryptAES( $str );
	}
	
	function DecryptString( $str, $privkey )
	{
		// Create the encryption object.
		$crypt = new PHPEncrypt();
		
		// Set the private.
		$crypt->SetPrivateKey( $privkey );
		
		$decrypted = $crypt->Decrypt( $str );
		
		if( !$decrypted )
		{
			return false;
		}
		return $decrypted;
	}
	
	//GenerateKeys();*/
	//GenerateRSAKeys();
}

$str .= '<div>';
$str .= '<h1>Keypair RSA Key Generator</h1>';
$str .= '<select id="key-size">';
$str .= '<option value="512">512 bit</option>';
$str .= '<option value="1024" selected="selected">1024 bit</option>';
$str .= '<option value="2048">2048 bit</option>';
$str .= '<option value="4096">4096 bit</option>';
$str .= '</select>';
$str .= '<button id="generate" onclick="generateRSAKeys();">Generate New Keys</button>';
$str .= '<div><small id="time-report"></small></div>';
//$str .= '<label for="async-ck"><input id="async-ck" type="checkbox" style="display:inline !important;"/> Async</label><br>';

$str .= '<label for="user">Username</label><br/>';
$str .= '<textarea id="user" style="width:100%" onkeyup="hashcryptate(\'user\',\'pass\',\'hexpass\');" onchange="hashcryptate(\'user\',\'pass\',\'hexpass\');" onpaste="setTimeout( function() { hashcryptate(\'user\',\'pass\',\'hexpass\'); }, 0 )"></textarea>';
$str .= '<label for="pass">Password</label><br/>';
$str .= '<textarea id="pass" style="width:100%" onkeyup="hashcryptate(\'user\',\'pass\',\'hexpass\');" onchange="hashcryptate(\'user\',\'pass\',\'hexpass\');" onpaste="setTimeout( function() { hashcryptate(\'user\',\'pass\',\'hexpass\'); }, 0 )"></textarea>';
//$str .= '<label for="hexpass">SHA256 Hash</label><br/>';
$str .= '<input type="hidden" id="hexpass" style="width:100%" readonly="readonly"/>';

$str .= '<label for="privkey">Private Key</label><br/>';
$str .= '<textarea id="privkey" rows="15" style="width:100%"></textarea>';

$str .= '<label for="pubkey">Public Key</label><br/>';
$str .= '<textarea id="pubkey" rows="8" style="width:100%"></textarea>';

$str .= '<h1>RSA Text Encryption Test</h1>';
$str .= '<label for="input">Text to encrypt:</label><br/>';
$str .= '<textarea id="input" name="input" rows="17" style="width: 100%">This is a test!</textarea>';
$str .= '<button id="execute" class="btn btn-primary">Encrypt / Decrypt</button><br>';
$str .= '<label for="crypted">Encrypted:</label><br/>';
$str .= '<textarea id="crypted" name="crypted" rows="17" style="width: 100%"></textarea>';

$str .= '<h1>RSA File Encryption Test</h1>';

$str .= '<label for="plainfile">File to encrypt:</label><br/>';
$str .= '<div>';
$str .= '<input id="FileInput" type="file" onchange="handleFileSelect(this)">';
$str .= '<br><a id="FileEncryption" href="" download=""></a>';
$str .= '<textarea id="plainfile" name="input" rows="17" style="width: 100%" onpaste="paste(this,event)"></textarea>';
$str .= '</div>';

$str .= '<button id="encryptfile">Encrypt / Decrypt</button><br>';

$str .= '<label for="cryptedfile">Encrypted file:</label><br/>';
$str .= '<textarea id="cryptedfile" name="cryptedfile" rows="17" style="width: 100%"></textarea>';

$str .= '<h1>RSA Signing/Authentication</h1>';

$str .= '<label for="msg">Message/Author</label><br/>';
$str .= '<textarea id="msg" style="width:100%">This is a test!</textarea>';

$str .= '<label for="hexmsg">Certificate</label><br/>';
$str .= '<textarea id="hexmsg" rows="17" style="width:100%"></textarea>';

$str .= '<label for="md5key">Signature</label><br/>';
$str .= '<textarea id="md5key" style="width:100%"></textarea>';

$str .= '<button id="verify" class="btn btn-primary">Sign / Verify Certificate</button>';
$str .= '<button id="authenticate" class="btn btn-primary">Login / Authenticate</button><br>';

//$str .= '<button onclick="dotheAESthing()">AES</button>';
//$str .= '<div id="AES_TEST"></div>';

//$str .= '<button onclick="SocketTest()">Test</button>';

$str .= '</div>';

?>
