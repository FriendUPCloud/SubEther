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

global $database;

$required = array(
	/*'SessionID', */'ContactID', 'Message'
);

$options = array(
	/*'UserType', */'SessionID', 'UniqueID', 'Date', 'Encryption', 'IsCrypto', 'CryptoID', 'CryptoKeys'
);

// Temporary to view data in browser for development
if( !$_POST && $_REQUEST )
{
	$_POST = $_REQUEST;
}
unset( $_POST['route'] );

// Node Connect
if ( isset( $_POST ) )
{
	foreach( $_POST as $k=>$p )
	{
		if( !in_array( $k, $required ) && !in_array( $k, $options ) )
		{
			throwXmlError ( MISSING_PARAMETERS, false, 'messages' );
		}
	}
	foreach( $required as $r )
	{
		if( !isset( $_POST[$r] ) )
		{
			throwXmlError ( MISSING_PARAMETERS, false, 'messages' );
		}
	}
	
	// TODO: Adding support for getting basic info abiut contacts with an anonymous account.
	
	$u = new stdClass();
	
	if( isset( $_POST['SessionID'] ) && $_POST['SessionID'] )
	{
		// Get User data from sessionid
		$sess = new dbObject ( 'UserLogin' );
		$sess->Token = $_POST['SessionID'];
		if ( $sess->Load () )
		{
			$u = new dbObject ( 'SBookContact' );
			$u->UserID = $sess->UserID;
			if( !$u->Load () )
			{
				throwXmlError ( AUTHENTICATION_ERROR, false, 'messages' );
			}
		}
	}
	
	// If there is no encryptionid it means we need to send out new keys
	if ( isset( $_POST['CryptoKeys'] ) && $_POST['CryptoKeys'] )
	{
		$data = json_decode( $_POST['CryptoKeys'] );
		
		$uniqueid = UniqueKey();
		
		if ( $data && $data->receivers && is_object( $data->receivers ) )
		{
			foreach( $data->receivers as $rec=>$key )
			{
				if ( $rec == 'sender' )
				{
					$contact = $u->ID;
					$reciever = $_POST['ContactID'];
				}
				else
				{
					$contact = $_POST['ContactID'];
					$reciever = $u->ID;
				}
				
				if( $contact > 0 )
				{
					// Create new keys for all
					if ( $usr = $database->fetchObjectRow ( '
						SELECT 
							c.*, u.PublicKey 
						FROM 
							SBookContact c, 
							Users u 
						WHERE 
								c.ID = \'' . $contact . '\' 
							AND u.ID = c.UserID 
					', false, 'components/chat/action/chat.php' ) )
					{
						if( isset( $_POST['UniqueID'] ) )
						{
							// Edit Message ...
							
							$m = new dbObject( 'SBookMail' );
							$m->UniqueID = $_POST['UniqueID'];
							$m->SenderID = $reciever;
							$m->ReceiverID = $contact;
							if( $m->Load() )
							{
								$m->Type = 'cm';
								$m->Encryption = ( isset( $_POST['Encryption'] ) ? $_POST['Encryption'] : '' );
								$m->UniqueKey = ( isset( $_POST['CryptoID'] ) ? $_POST['CryptoID'] : '' );
								$m->EncryptionKey = $key;
								$m->PublicKey = $usr->PublicKey;
								$m->IsCrypto = 1;
								$m->Message = str_replace ( array ( '<', '>' ), array ( '&lt;', '&gt;' ), stripslashes ( $_POST['Message'] ) );
								$m->DateModified = date( 'Y-m-d H:i:s' );
								$m->Save();
							}
						}
						else
						{
							// New Message ...
							
							$m = new dbObject( 'SBookMail' );
							$m->UniqueID = $uniqueid;
							$m->ContactID = ( $u->ID > 0 ? $u->ID : 0 );
							$m->SenderID = $reciever;
							$m->ReceiverID = $contact;
							$m->CategoryID = 0;
							$m->Type = 'cm';
							$m->Encryption = ( isset( $_POST['Encryption'] ) ? $_POST['Encryption'] : '' );
							$m->UniqueKey = ( isset( $_POST['CryptoID'] ) ? $_POST['CryptoID'] : '' );
							$m->EncryptionKey = $key;
							$m->PublicKey = $usr->PublicKey;
							$m->IsCrypto = 1;
							$m->Message = str_replace ( array ( '<', '>' ), array ( '&lt;', '&gt;' ), stripslashes ( $_POST['Message'] ) );
							$m->Date = date( 'Y-m-d H:i:s' );
							$m->DateModified = date( 'Y-m-d H:i:s' );
							$m->Save();
						}
						
						UserActivity( 'messages', 'lastmessage', $m->SenderID, $m->ReceiverID, $m->ID, '' );
					}
				}
			}
		}
	}
	else
	{
		if( isset( $_POST['UniqueID'] ) )
		{
			// Edit Message ...
			
			$m = new dbObject( 'SBookMail' );
			$m->UniqueID = $_POST['UniqueID'];
			$m->SenderID = ( $u->ID > 0 ? $u->ID : 0 );
			$m->ReceiverID = $_POST['ContactID'];
			if( $m->Load() )
			{
				$m->Type = 'im';
				$m->Encryption = ( isset( $_POST['Encryption'] ) ? $_POST['Encryption'] : '' );
				$m->UniqueKey = ( isset( $_POST['CryptoID'] ) ? $_POST['CryptoID'] : '' );
				$m->IsCrypto = ( isset( $_POST['IsCrypto'] ) ? $_POST['IsCrypto'] : 0 );
				$m->Message = str_replace ( array ( '<', '>' ), array ( '&lt;', '&gt;' ), stripslashes ( $_POST['Message'] ) );
				$m->DateModified = date( 'Y-m-d H:i:s' );
				$m->Save();
			}
		}
		else
		{
			// New Message ...
			
			$m = new dbObject( 'SBookMail' );
			$m->UniqueID = UniqueKey();
			$m->ContactID = ( $u->ID > 0 ? $u->ID : 0 );
			$m->SenderID = ( $u->ID > 0 ? $u->ID : 0 );
			$m->ReceiverID = $_POST['ContactID'];
			$m->CategoryID = 0;
			$m->Type = 'im';
			$m->Encryption = ( isset( $_POST['Encryption'] ) ? $_POST['Encryption'] : '' );
			$m->UniqueKey = ( isset( $_POST['CryptoID'] ) ? $_POST['CryptoID'] : '' );
			$m->IsCrypto = ( isset( $_POST['IsCrypto'] ) ? $_POST['IsCrypto'] : 0 );
			$m->Message = str_replace ( array ( '<', '>' ), array ( '&lt;', '&gt;' ), stripslashes ( $_POST['Message'] ) );
			$m->DateModified = date( 'Y-m-d H:i:s' );
			$m->Save();
		}
		
		UserActivity( 'messages', 'lastmessage', $m->SenderID, $m->ReceiverID, $m->ID, '' );
	}
	
	
	
	// If your communicating with a bot use this to reply || This is not supported with encryption yet, only plaintext
	if( $database->fetchObjectRow( '
		SELECT
			u.* 
		FROM
			SBookContact c, 
			Users u 
		WHERE 
				c.ID = \'' . $_POST['ContactID'] . '\'
			AND u.ID = c.UserID
			AND u.IsDeleted = "0"
			AND u.IsDisabled = "0"
			AND u.UserType = "3" 
		ORDER BY
			u.ID DESC
	' ) )
	{
		if( $u->ID > 0 )
		{
			if( $reply = AIbot( $_POST['Message'], $u->Email ) )
			{
				$a = new dbObject( 'SBookMail' );
				$a->SenderID = $_POST['ContactID'];
				$a->ReceiverID = $u->ID;
				$a->CategoryID = 0;
				$a->Type = 'im';
				$a->Message = $reply;
				$a->IsRead = 1;
				$a->IsNoticed = 1;
				$a->IsAlerted = 1;
				$a->Date = date( 'Y-m-d H:i:s' );
				$a->DateModified = date( 'Y-m-d H:i:s' );
				$a->Save();
			
				UserActivity( 'messages', 'lastmessage', $a->SenderID, $a->ReceiverID, $a->ID, '' );
			}
		}
	}
	
	if( $m->ID > 0 )
	{
		showXmlData ( $m->ID, false, 'messages' );
	}
}

// Give default error
throwXmlError ( MISSING_PARAMETERS, false, 'messages' );

?>
