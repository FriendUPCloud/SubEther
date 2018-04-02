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

class PHPEncrypt
{
	var $Default = true;
	var $KeySize = 1024; // Size of Key.
	var $KeyType = '01001'; //65537 default openssl public exponent for rsa key type
	var $PrivKey = null;
	var $PubKey = null;
	
	function __construct ( $KeySize = false )
	{
		$this->KeySize = ( $KeySize ? (int)$KeySize : $this->KeySize );
	}
	
	// TODO: Create support for a defined privkey based on password
	function SetHashPassword ( $Hash )
	{
		$length = strlen( $Hash );
		
		if( $length )
		{
			$key = '';
			for( $a = 0; $a < ($this->KeySize/$length); $a++ )
			{
				$key = ( $key ? ( $key . $Hash ) : $Hash );
			}
			
			$length2 = strlen( $key );
		}
		
		$this->Hash = $key;
	}
	
	function GetKey (  )
	{
		$res = openssl_pkey_new (
			array(
				'private_key_bits' => ( $this->KeySize ? $this->KeySize : 1024 ),
				'private_key_type' => ( $this->KeyType ? $this->KeyType : OPENSSL_KEYTYPE_RSA ),
			)
		);
		
		openssl_pkey_export( $res, $this->PrivKey );
		$this->PubKey = openssl_pkey_get_details( $res );
		$this->PrivKey = trim( $this->PrivKey );
		$this->PubKey = trim( $this->PubKey['key'] );
		
		openssl_free_key( $res );
	}
	
	function GetPrivateKey (  )
	{
		return $this->PrivKey;
	}
	
	function GetPublicKey (  )
	{
		return $this->PubKey;
	}
	
	function SetPrivateKey ( $PrivKey )
	{
		$this->PrivKey = trim( $PrivKey );
	}
	
	function SetPublicKey ( $PubKey )
	{
		$this->PubKey = trim( $PubKey );
	}
	
	function Encrypt ( $Str )
	{
		if( !$Str || !$this->PubKey ) return false;
		
		$output = '';
		
		if( $this->Default )
		{
			openssl_public_encrypt( $Str, $output, $this->PubKey );
			$output = base64_encode( $output );
		}
		else
		{
			// Compress the data to be sent
			$data = gzcompress( $Str );
			
			$a_key = openssl_pkey_get_details( $this->PubKey );
			
			// Encrypt the data in small chunks and then combine and send it.
			$chunkSize = ceil( $a_key['bits'] / 8 ) - 11;
			
			while ( $data )
			{
				$chunk = substr( $data, 0, $chunkSize );
				$data = substr( $data, $chunkSize );
				$encrypted = '';
				if ( !openssl_public_encrypt( $chunk, $encrypted, $this->PubKey ) )
				{
					return false;
				}
				$output .= $encrypted;
			}
			
			openssl_free_key( $this->PubKey );
		}
		
		if( $output )
		{
			return $output;
		}
		
		return false;
	}
	
	function Decrypt ( $Str )
	{
		if( !$Str || !$this->PrivKey ) return false;
		
		$output = '';
		
		if( $this->Default )
		{
			$Str = base64_decode( $Str );
			openssl_private_decrypt( $Str, $output, $this->PrivKey );
		}
		else
		{
			$a_key = openssl_pkey_get_details( $this->PrivKey );
			
			// Decrypt the data in the small chunks
			$chunkSize = ceil( $a_key['bits'] / 8 );
			
			while ( $Str )
			{
				$chunk = substr( $Str, 0, $chunkSize );
				$Str = substr( $Str, $chunkSize );
				$decrypted = '';
				if ( !openssl_private_decrypt( $chunk, $decrypted, $this->PrivKey ) )
				{
					return false;
				}
				$output .= $decrypted;
			}
			
			openssl_free_key( $this->PrivKey );
			
			// Uncompress the unencrypted data.
			$output = gzuncompress( $output );
		}
		
		if( $output )
		{
			return $output;
		}
		
		return false;
	}
}

?>
