<? /*******************************************************************************
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
*******************************************************************************/ ?>
<div id="Testing">
	<div class="Box">
		<div id="TestingContent" class="content"><?= $this->Content ?></div>
	</div>
</div>

<script type="text/javascript">
	
	console.log( localStorage );
	
	//var RSAObject = false;
	
	/*function encryptString( str )
	{
		// Create the encryption object.
		var crypt = new JSEncrypt();
		
		// Set the public.
		crypt.setPublicKey( ge( 'pubkey' ).value );
		
		ge( 'crypted' ).value = crypt.encrypt( str );
		ge( 'input' ).value = '';
	}
	
	function decryptString( str )
	{
		// Create the encryption object.
		var crypt = new JSEncrypt();
		
		// Set the private.
		crypt.setPrivateKey( ge( 'privkey' ).value );
		
		var decrypted = crypt.decrypt( str );
		if( !decrypted )
		{
			decrypted = 'failed';
			return false;
		}
		ge( 'input' ).value = decrypted;
		ge( 'crypted' ).value = '';
		
		return true;
	}
	
	function signMessage( msg )
	{
		var rsa = new RSAKey();
		rsa.readPrivateKeyFromPEMString( ge( 'privkey' ).value );
		var hashAlg = 'SHA256';
		var hSig = rsa.signString( msg, hashAlg );
		ge( 'hexmsg' ).value = linebrk( hSig, 64 );
	}
	
	function verifyMessage( msg, str )
	{
		var x509 = new X509();
		x509.readCertPEM( ge( 'pubkey' ).value );
		var isValid = x509.subjectPublicKeyRSA.verifyString( msg, str );
		
		if( isValid )
		{
			return true;
		}
		else
		{
			return false;
		}
	}*/
	
	// Execute when they click the button.
	ge( 'authenticate' ).onclick = function ()
	{
		// Get the input and crypted values.
		var msg = ge( 'msg' ).value;
		var signature = ge( 'md5key' ).value;
		
		// Alternate the values.
		if( !signature )
		{
			signCredentials( msg );
		}
		else if( signature )
		{
			if( verifyCredentials( msg, signature ) )
			{
				alert( 'AUTHENTICATED' );
			}
			else
			{
				alert( 'NOT AUTHENTICATED' );
			}
		}
	};
	
	// Execute when they click the button.
	ge( 'verify' ).onclick = function ()
	{
		// Get the input and crypted values.
		var msg = ge( 'msg' ).value;
		var hash = ge( 'hexmsg' ).value;
		
		// Alternate the values.
		if( !hash )
		{
			signCertificate( msg );
		}
		else if( hash )
		{
			if( verifyCertificate( hash ) )
			{
				alert( 'VALID' );
			}
			else
			{
				alert( 'NOT VALID' );
			}
		}
	};
	
	// Execute when they click the button.
	ge( 'execute' ).onclick = function ()
	{
		// Get the input and crypted values.
		var input = ge( 'input' ).value;
		var crypted = ge( 'crypted' ).value;
		
		// Alternate the values.
		if( input )
		{
			encryptStringAES( input, ge( 'input' ), ge( 'crypted' ) );
			//encryptStringRSA( input, ge( 'input' ), ge( 'crypted' ) );
		}
		else if( crypted )
		{
			decryptStringAES( crypted, ge( 'input' ), ge( 'crypted' ) );
			//decryptStringRSA( crypted, ge( 'input' ), ge( 'crypted' ) );
		}
	};
	
	ge( 'encryptfile' ).onclick = function ()
	{
		// Get the input and crypted values.
		var input = ge( 'plainfile' ).value;
		var crypted = ge( 'cryptedfile' ).value;
		
		// Alternate the values.
		if( input )
		{
			encryptFileAES( input, ge( 'plainfile' ), ge( 'cryptedfile' ) );
			//encryptStringRSA( input, ge( 'input' ), ge( 'crypted' ) );
		}
		else if( crypted )
		{
			decryptFileAES( crypted, ge( 'plainfile' ), ge( 'cryptedfile' ) );
			//decryptStringRSA( crypted, ge( 'input' ), ge( 'crypted' ) );
		}
	};
	
	/*function generateKeys()
	{
		console.log( 'generating keys ...' );
		var sKeySize = ge( 'key-size' ).value;
		var keySize = parseInt( sKeySize );
		var crypt = new JSEncrypt( keySize );
		if( ge( 'hexpass' ) )
		{
			crypt.setHashPassword( ge( 'hexpass' ).value );
		}
		var async;;
		var dt = new Date();
		var time = -( dt.getTime() );
		if( async )
		{
			ge( 'time-report' ).innerHTML = '.';
			var load = setInterval( function ()
			{
				var text = ge( 'time-report' ).innerHTML;
				ge( 'time-report' ).innerHTML = text + '.';
			}, 500 );
			crypt.getKey( function ()
			{
				clearInterval( load );
				dt = new Date();
				time += ( dt.getTime() );
				ge( 'time-report' ).innerHTML = 'Generated in ' + time + ' ms';
				ge( 'privkey').value = crypt.getPrivateKey();
				ge( 'pubkey' ).value = crypt.getPublicKey();
			} );
		}
		else
		{
			crypt.getKey();
			dt = new Date();
			time += ( dt.getTime() );
			ge( 'time-report' ).innerHTML = 'Generated in ' + time + ' ms';
			ge( 'privkey' ).value = crypt.getPrivateKey();
			ge( 'pubkey').value = crypt.getPublicKey();
		}
	}*/
	
	function hashcryptate( first, second, ele )
	{
		if( !first || !ge( first ) || !ele || !ge( ele ) ) return false;
		
		if( first && second && ge( second ) )
		{
			var pw1 = ( ge( first ).value ? ge( first ).value.trim() : '' );
			var pw2 = ( ge( second ).value ? md5( ge( second ).value.trim() ) : '' );
			
			ge( ele ).value = ( ( pw1 || pw2 ) ? ( ( pw1 && pw2 ) ? pw1+':'+pw2 : ( pw1 + pw2 ) ) : '' );
		}
		else
		{
			ge( ele ).value = ( ge( first ).value ? ge( first ).value : '' );
		}
		
		return true;
	}
	/*
    // If they wish to generate new keys.
    //generateKeys();
	
	function dotheAESthing()
	{
		//usedKey="World678World678";
		//myStr="Osama Oransa2012Osama Oransa2011RashaOsama Oransa2012Osama Oransa2011RashaOsama Oransa2012Osama Oransa2011RashaOsama Oransa2012Osama Oransa2011Rasha";
		usedKey = ge( 'crypted' ).value;
		myStr = ge( 'privkey' ).value;
		console.log(myStr);
		console.log(usedKey);
		var key=init(usedKey);
		console.log(key);
		encrypted=encryptLongString(myStr,key);
		console.log('after encrypt='+encrypted);
		decrypted=decryptLongString(encrypted,key);
		console.log('after decrypt='+decrypted);
		finish();
		
		ge( 'AES_TEST' ).innerHTML = '<br><br>Str: ' + myStr + '<br><br>usedkey: ' + usedKey + '<br><br>key: ' + key + '<br><br>after encrypt= ' + encrypted + '<br><br>after decrypt= ' + decrypted;
	}*/
	
	function generateRSAKeys()
	{
		var KeySize = parseInt( ge( 'key-size' ).value );
		var PassPhrase = ge( 'hexpass' ).value;
		
		var dt = new Date();
		var time = -( dt.getTime() );
		
		ge( 'time-report' ).innerHTML = '.';
		var load = setInterval( function ()
		{
			var text = ge( 'time-report' ).innerHTML;
			ge( 'time-report' ).innerHTML = text + '.';
		}, 500 );
		
		var RSAKey = fcrypt.generateKeys( PassPhrase, KeySize );
		
		RSAObject = RSAKey;
		
		ge( 'privkey' ).value = fcrypt.getPrivateKey();
		ge( 'pubkey' ).value = fcrypt.getPublicKey();
		
		clearInterval( load );
		dt = new Date();
		time += ( dt.getTime() );
		ge( 'time-report' ).innerHTML = 'Generated in ' + time + ' ms';
	}
	
	function encryptStringRSA( str, input, crypted )
	{
		if( !str || !input || !crypted ) return false;
		
		var PublicKey = ge( 'pubkey' ).value;
		var Encrypted = fcrypt.encryptRSA( str, PublicKey );
		crypted.value = Encrypted;
		input.value = '';
		
		return true;
	}
	
	function decryptStringRSA( str, input, crypted )
	{
		if( !str || !input || !crypted ) return false;
		
		var PrivateKey = ge( 'privkey' ).value;
		var Decrypted = fcrypt.decryptRSA( str, PrivateKey );
		Decrypted = Decrypted;
		
		if( !Decrypted )
		{
			input.value = 'failed';
			return false;
		}
		input.value = Decrypted;
		crypted.value = '';
		
		return true;
	}
	
	function encryptStringAES( str, input, crypted )
	{
		if( !str || !input || !crypted ) return false;
		
		var PrivateKey = ge( 'privkey' ).value;
		var PublicKey = ge( 'pubkey' ).value;
		
		if( !PublicKey && PrivateKey )
		{
			var privKeyObject = fcrypt.setPrivateKeyRSA( PrivateKey );
			
			var PublicKey = fcrypt.getPublicKey( privKeyObject );
			
			if ( PublicKey )
			{
				ge( 'pubkey' ).value = PublicKey;
			}
		}
		
		var Encrypted = fcrypt.encryptString( str, PublicKey );
		crypted.value = Encrypted.cipher;
		input.value = '';
		
		return true;
	}
	
	function decryptStringAES( str, input, crypted )
	{
		if( !str || !input || !crypted ) return false;
		
		var PrivateKey = ge( 'privkey' ).value;
		var Decrypted = fcrypt.decryptString( str, PrivateKey );
		Decrypted = Decrypted.plaintext;
		
		if( !Decrypted )
		{
			input.value = 'failed';
			return false;
		}
		input.value = Decrypted;
		crypted.value = '';
		
		return true;
	}
	
	function encryptFileAES( blob, input, crypted )
	{
		if( !blob || !input || !crypted ) return false;
		
		var PublicKey = ge( 'pubkey' ).value;
		var Encrypted = fcrypt.encryptString( blob, PublicKey );
		crypted.value = Encrypted.cipher;
		input.value = '';
		
		var a = ge( 'FileEncryption' );
		
		//var base64 = blob.toString(CryptoJS.enc.Base64);
		
		a.innerHTML = a.getAttribute( 'download' );
		
		a.setAttribute( 'href', 'data:application/octet-stream,' + fcrypt.stripHeader( Encrypted.cipher ) );
		a.setAttribute( 'download', a.getAttribute( 'download' ) + '.encrypted' );
		
		/*var file = ge( 'FileInput' ).files[0];
		
		var reader = new FileReader();
		
		// Closure to capture the file information.
		reader.onload = ( function( ele, key )
		{
			return function(e)
			{
				var encrypted = fcrypt.encryptString( ele.getAttribute( 'href' ), key );
				
				ele.innerHTML = ele.getAttribute( 'download' );
				
				ele.setAttribute( 'href', 'data:application/octet-stream,' + fcrypt.stripHeader( encrypted.cipher ) );
				ele.setAttribute( 'download', ele.getAttribute( 'download' ) + '.encrypted' );
			};
		})(a,PublicKey);
		
		// Read in the image file as a data URL.
		reader.readAsDataURL(file);*/
		
		/*var reader = new window.FileReader();
		reader.readAsDataURL( blob );
		reader.onloadend = function( input, crypted )
		{
			var PublicKey = ge( 'pubkey' ).value;
			var Encrypted = fcrypt.encryptString( reader.result, PublicKey );
			crypted.value = Encrypted.cipher;
			input.value = '';
			
			var a = ge( 'FileEncryption' );
			
			a.innerHTML = ele.getAttribute( 'download' );
			
			a.setAttribute( 'href', 'data:application/octet-stream,' + fcrypt.stripHeader( Encrypted.cipher ) );
			a.setAttribute( 'download', ele.getAttribute( 'download' ) + '.encrypted' );
		}*/
		
		return true;
	}
	
	function decryptFileAES( str, input, crypted )
	{
		if( !str || !input || !crypted ) return false;
		
		var PrivateKey = ge( 'privkey' ).value;
		var Decrypted = fcrypt.decryptString( str, PrivateKey );
		Decrypted = Decrypted.plaintext;
		
		if( !Decrypted )
		{
			input.value = 'failed';
			return false;
		}
		input.value = Decrypted;
		crypted.value = '';
		
		//var base64 = Decrypted.toString(CryptoJS.enc.Base64);
		//var base64 = fcrypt.b16to64( Decrypted );
		//var base64 = btoa( Decrypted );
		//var base64 = CryptoJS.enc.Base64.stringify( Decrypted );
		
		//console.log( base64 + ' --' );
		
		//var data = 'data:image/jpeg;base64,' + base64;
		//var name = a.innerHTML;
		
		/*// Only process image files.
		if ( data && data.indexOf( 'data:image' ) >= 0 )
		{*/
			// Render thumbnail.
			//a.innerHTML = ['<img style="max-width:50%;" class="thumb" src="', data, '" title="', name, '"/>'].join('');
		/*}
		else
		{
			a.innerHTML = name;
		}*/
		
		//a.setAttribute( 'href', data );
		//a.setAttribute( 'download', name );
		
		//var bytes = new Uint8Array(Decrypted.length);
		//for ( var i = 0; i < Decrypted.length; i++ )
		//{
		//	bytes[i] = Decrypted.charCodeAt(i);
		//}
		
		//var arrayBufferView = new Uint8Array( Decrypted );
		//var file = new Blob( [ bytes ], { type: "image/jpeg" } );
		
		//var file = new Blob( Decrypted, {type : 'image/jpeg'}); // the blob
		
		//var file = ge( 'FileInput' ).files[0];
		
		/*var reader = new FileReader();
		
		// Closure to capture the file information.
		reader.onload = ( function( ele, key )
		{
			return function(e)
			{
				var decrypted = fcrypt.decryptString( ele.getAttribute( 'href' ).split(',')[1], key );
				
				var data = decrypted.plaintext;
				var name = ele.innerHTML;
				
				// Only process image files.
				if ( data && data.indexOf( 'data:image' ) >= 0 )
				{
					// Render thumbnail.
					ele.innerHTML = ['<img style="max-width:50%;" class="thumb" src="', data, '" title="', name, '"/>'].join('');
				}
				else
				{
					ele.innerHTML = name;
				}
				
				ele.setAttribute( 'href', data );
				ele.setAttribute( 'download', name );
			};
		})(a,PrivateKey);
		
		reader.readAsText(file);*/
		
		/*var reader = new window.FileReader();
		reader.readAsDataURL( file );
		reader.onloadend = function()
		{
			var a = ge( 'FileEncryption' );
			
			var data = reader.result;
			var name = a.innerHTML;
			
			// Only process image files.
			if ( data && data.indexOf( 'data:image' ) >= 0 )
			{
				// Render thumbnail.
				a.innerHTML = ['<img style="max-width:50%;" class="thumb" src="', data, '" title="', name, '"/>'].join('');
			}
			else
			{
				a.innerHTML = name;
			}
			
			a.setAttribute( 'href', data );
			a.setAttribute( 'download', name );
			
			//console.log( reader.result + ' --' );
		}*/
		
		var a = ge( 'FileEncryption' );
		
		var data = Decrypted;
		var name = a.innerHTML;
		
		// Only process image files.
		if ( data && data.indexOf( 'data:image' ) >= 0 )
		{
			// Render thumbnail.
			a.innerHTML = ['<img style="max-width:50%;" class="thumb" src="', data, '" title="', name, '"/>'].join('');
		}
		else
		{
			a.innerHTML = name;
		}
		
		a.setAttribute( 'href', data );
		a.setAttribute( 'download', name );
		
		return true;
	}
	
	function signCredentials( msg )
	{
		if( !msg ) return false;
		
		var PrivateKey = ge( 'privkey' ).value;
		
		var Sign = fcrypt.signString( msg, PrivateKey );
		
		if( !Sign )
		{
			return false;
		}
		
		//ge( 'hexmsg' ).value = Sign.certificate;
		ge( 'md5key' ).value = Sign;
		
		return true;
	}
	
	function verifyCredentials( msg, str )
	{
		if( !msg || !str ) return false;
		
		var PublicKey = ge( 'pubkey' ).value;
		//var Signature = ge( 'md5key' ).value;
		
		var isValid = fcrypt.verifyString( msg, str, PublicKey );
		
		if( isValid )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function signCertificate( data )
	{
		if( !data ) return false;
		
		var PrivateKey = ge( 'privkey' ).value;
		var PublicKey = ge( 'pubkey' ).value;
		
		var Sign = fcrypt.signCertificate( data, PublicKey, PrivateKey );
		
		if( !Sign || !Sign.certificate )
		{
			return false;
		}
		
		ge( 'hexmsg' ).value = Sign.certificate;
		ge( 'md5key' ).value = Sign.signature;
		
		return true;
	}
	
	function verifyCertificate( str )
	{
		if( !str ) return false;
		
		var PrivateKey = ge( 'privkey' ).value;
		var Signature = ge( 'md5key' ).value;
		
		var isValid = fcrypt.verifyCertificate( str, PrivateKey, Signature );
		
		if( isValid )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	generateRSAKeys();
	//generateKeys();
	
function handleFileSelect( _file )
{
	var files = _file.files; // FileList object
	
	// Loop through the FileList and render image files as thumbnails.
	for ( var i = 0, f; f = files[i]; i++ )
	{
		
		var reader = new FileReader();
		
		// Closure to capture the file information.
		reader.onload = ( function( theFile, ele )
		{
			return function(e)
			{
				var a = ele.parentNode.getElementsByTagName( 'a' )[0];
				
				// Only process image files.
				if ( theFile.type.match( 'image.*' ) )
				{
					// Render thumbnail.
					a.innerHTML = ['<img style="max-width:50%;" class="thumb" src="', e.target.result, '" title="', theFile.name, '"/>'].join('');
				}
				else
				{
					a.innerHTML = escape(theFile.name);
				}
				
				a.setAttribute( 'href', e.target.result );
				a.setAttribute( 'download', theFile.name );
				
				ge( 'plainfile' ).value = e.target.result;
			};
		})(f,_file);
		
		// Read in the image file as a data URL.
		reader.readAsDataURL(f);
		
		/*var binary = new FileReader();
		
		binary.onload = ( function( theFile )
		{
			return function(e)
			{
				ge( 'plainfile' ).value = e.target.result;
			};
		})(f);
		
		binary.readAsBinaryString(f);*/
	}
}

/*window.addEventListener( 'paste', function( evt )
{
	var pastedItems = (evt.clipboardData || evt.originalEvent.clipboardData).items;
	
	for( var i in pastedItems ) 
	{
		var item = pastedItems[i];
		
		console.log( 'item ', item );
	}
} );*/

function paste( _this, e )
{
	//alert( 'testing ...' );
	
	var evt = ( window.event || e );
	
	if( evt )
	{
		var pastedItems = (evt.clipboardData || evt.originalEvent.clipboardData).items;
		
		if( pastedItems )
		{
			for( i in pastedItems )
			{
				var item = pastedItems[i];
				
				if( item.kind === 'file' ) 
				{
					var blob = item.getAsFile();
					filetype = ( blob.type == '' ? 'application/octet-stream' : blob.type );
					
					var reader = new FileReader();
					
					// Closure to capture the file information.
					reader.onload = ( function( theFile, ele )
					{
						return function(e)
						{
							var a = ele.parentNode.getElementsByTagName( 'a' )[0];
				
							// Only process image files.
							if ( theFile.type.match( 'image.*' ) )
							{
								// Render thumbnail.
								a.innerHTML = ['<img style="max-width:50%;" class="thumb" src="', e.target.result, '" title="', theFile.name, '"/>'].join('');
							}
							else
							{
								a.innerHTML = escape(theFile.name);
							}
				
							a.setAttribute( 'href', e.target.result );
							a.setAttribute( 'download', theFile.name );
				
							ge( 'plainfile' ).value = e.target.result;
						};
					})( blob, _this );
					
					// Read in the image file as a data URL.
					reader.readAsDataURL( blob );
					
					console.log( 'item ', { filetype : filetype, blob : blob, item : item } );
					
					//evt.preventDefault();
					//evt.stopPropagation();
					
					break;
				}
			}
		}
		else
		{
			console.log( 'nothing to paste ...' );
		}
	}
	else
	{
		console.log( 'no events ... ', evt );
	}
}
	
</script>
