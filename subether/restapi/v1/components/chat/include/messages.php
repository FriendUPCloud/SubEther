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

include_once ( 'subether/functions/userfuncs.php' );

//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//ini_set('display_errors', 1);

// Set header
setXmlHeader();

// Set limit
$limit = 300;

// Loop limit (10min)
$loop = 600;

$required = array(
	'SessionID', 'ContactID' 
);

$options = array(
	'MessageID', 'LastMessage', 'LastActivity', 'Limit', 'Loop', 'Encoding', 'Kill', 'Test' 
);

if ( isset( $_REQUEST ) || isset( $_POST ) )
{
	// Temporary to view data i browser for development
	if( !$_POST )
	{
		if( isset( $_REQUEST['route'] ) )
		{
			unset( $_REQUEST['route'] );
		}
		
		$_POST = $_REQUEST;
	}
	
	//die( print_r( $_POST,1 ) . ' --' );
	
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
	else
	{
		throwXmlError ( SESSION_MISSING, false, 'messages' );
	}
	
	// TODO: remove complexity later atm we created new type for encrypted messages since we have more then one message row
	
	$storage = $database->fetchObjectRow( '
		SELECT 
			s.*, 
			u.PublicKey AS UserPublicKey 
		FROM 
			Users u, 
			SBookStorage s 
		WHERE 
				s.ContactID = \'' . $u->ID . '\' 
			AND s.IDs IN (' . $_POST['ContactID'] . ') 
			AND s.Relation = "SBookContact" 
			AND s.IsDeleted = "0" 
			AND s.PublicKey = u.PublicKey 
			AND s.UserID = u.ID 
		ORDER BY 
			s.ID DESC 
		LIMIT 1 
	' );
	
	
	$i = 0; $str = 'pong ...';
	
	//set php runtime to unlimited
	set_time_limit(0);
	
	//ob_start();
	
	if( isset( $_POST['Loop'] ) )
	{
		$loop = $_POST['Loop'];
	}
	
	if( isset( $_POST['LastActivity'] ) && !$_POST['LastActivity'] )
	{
		$_POST['LastActivity'] = strtotime( date( 'Y-m-d H:i:s' ) );
	}
	else if( isset( $_POST['LastActivity'] ) && strstr( $_POST['LastActivity'], '-' ) )
	{
		$_POST['LastActivity'] = strtotime( $_POST['LastActivity'] );
	}
	
	
	
	// main loop
	while ( !connection_aborted() )
	{
		if( isset( $_REQUEST['Kill'] ) )
		{
			die( 'ws: has been killed' );
		}
		
		// PHP caches file data, like requesting the size of a file, by default. clearstatcache() clears that cache
		clearstatcache();
		
		if( isset( $_POST['Test'] ) && isset( $_POST['LastActivity'] ) )
		{
			$str = ( $str == 'ping ... ' ? 'pong ... ' : 'ping ... ' );
			
			echo /*$str*/" ";
			
        	ob_flush();
        	flush();
			
			if( $i >= $loop )
			{
				ob_end_clean();
				
				throwXmlMsg ( NO_NEW_UPDATES, false, 'messages' );
				
				break;
			}
		}
		else if( $i >= 1 )
		{
			break;
		}
		
		$i++;
		
		if( $messages = fetchObjectRows( '
			SELECT 
				m.*,
				m.EncryptionKey AS CryptoKey, 
				c.ID AS PosterID,
				c.ImageID, 
				c.Firstname, 
				c.Middlename, 
				c.Lastname, 
				c.Display, 
				c.Username 
			FROM 
				SBookMail m, 
				SBookContact c 
			WHERE 
				(
					(
							m.SenderID = \'' . $_POST['ContactID'] . '\' 
						AND m.ReceiverID = \'' . $u->ID . '\' 
						AND m.Type IN ( "im", "vm" ) 
						AND c.ID = m.SenderID
					) 
					OR 
					(		m.SenderID = \'' . $u->ID . '\' 
						AND m.ReceiverID = \'' . $_POST['ContactID'] . '\' 
						AND m.Type IN ( "im", "vm" ) 
						AND c.ID = m.SenderID
					)
					OR
					(
							m.SenderID = \'' . $_POST['ContactID'] . '\' 
						AND m.ReceiverID = \'' . $u->ID . '\' 
						AND m.Type IN ( "cm" ) 
						AND c.ID = m.ContactID 
					)
				) 
				AND m.Message != "" 
				' . ( isset( $_POST['LastMessage'] ) ? '
				AND m.ID' . ( $_POST['LastMessage'] == 0 ? ' = "0" ' : ' >= \'' . $_POST['LastMessage'] . '\' ' ) . ' 
				' : '' ) . '
				' . ( isset( $_POST['MessageID'] ) ? '
				AND m.ID = \'' . $_POST['MessageID'] . '\' 
				' : '' ) . '
				' . ( isset( $_POST['LastActivity'] ) && $_POST['LastActivity'] > 0 ? '
				AND m.Date > \'' . date( 'Y-m-d H:i:s', $_POST['LastActivity'] ) . '\' 
				' : '' ) . '
			GROUP BY 
				m.UniqueID
			ORDER BY 
				m.ID DESC
			LIMIT ' . ( isset( $_POST['Limit'] ) && $_POST['Limit'] ? $_POST['Limit'] : $limit ) . '
		' ) )
		{
			$xml = ''; $json = new stdClass(); $cryptoid = false; $cryptokey = false; $publickey = false; $ids = array(); $uids = array();
			
			$lastactivity = 0; $lastmessage = 0;
			
			$json->Messages = [];
			
			foreach ( $messages as $row )
			{
				switch ( $row->Display )
				{
					case 1:
						$row->Poster = trim( $row->Firstname . ' ' . $row->Middlename . ' ' . $row->Lastname );
						break;
					case 2:
						$row->Poster = trim( $row->Firstname . ' ' . $row->Lastname );
						break;
					case 3:
						$row->Poster = trim( $row->Lastname . ' ' . $row->Firstname );
						break;
					default:
						$row->Poster = $row->Username;
						break;
				}
				
				$xml .= '<Messages>';
				$xml .= '<ID>' . $row->ID . '</ID>';
				$xml .= '<UniqueID>' . $row->UniqueID . '</UniqueID>';
				$xml .= '<ImageID>' . $row->ImageID . '</ImageID>';
				$xml .= '<PosterID>' . $row->PosterID . '</PosterID>';
				$xml .= '<Poster><![CDATA[' . $row->Poster . ']]></Poster>';
				$xml .= '<Message><![CDATA[' . $row->Message . ']]></Message>';
				$xml .= '<CategoryID>' . $row->CategoryID . '</CategoryID>';
				$xml .= '<Type>' . $row->Type . '</Type>';
				$xml .= '<Encryption>' . $row->Encryption . '</Encryption>';
				$xml .= '<CryptoID>' . $row->UniqueKey . '</CryptoID>';
				
				$obj = new stdClass();
				$obj->ID         = $row->ID;
				$obj->UniqueID   = $row->UniqueID;
				$obj->ImageID    = $row->ImageID;
				$obj->PosterID   = $row->PosterID;
				$obj->Poster     = $row->Poster;
				$obj->Message    = $row->Message;
				$obj->CategoryID = $row->CategoryID;
				$obj->Type       = $row->Type;
				$obj->Encryption = $row->Encryption;
				$obj->CryptoID   = $row->UniqueKey;
				
				// New method for encrypted messages
			
				if( $row->Type == 'cm' )
				{
					if( !$cryptokey )
					{
						$cryptoid = $row->UniqueKey;
						$cryptokey = $row->CryptoKey;
						$publickey = $row->PublicKey;
					}
				
					$xml .= '<CryptoKey><![CDATA[' . $row->CryptoKey . ']]></CryptoKey>';
					$xml .= '<PublicKey><![CDATA[' . $row->PublicKey . ']]></PublicKey>';
					
					$obj->CryptoKey = $row->CryptoKey;
					$obj->PublicKey = $row->PublicKey;
				}
				//else if( $storage )
				//{
				//	$xml .= '<CryptoKey><![CDATA[' . $storage->EncryptionKey . ']]></CryptoKey>';
				//	
				//	$obj->CryptoKey = $storage->EncryptionKey;
				//}
				
				$xml .= '<IsCrypto>' . $row->IsCrypto . '</IsCrypto>';
				$xml .= '<IsTyping>' . $row->IsTyping . '</IsTyping>';
				$xml .= '<IsRead>' . $row->IsRead . '</IsRead>';
				$xml .= '<IsNoticed>' . $row->IsNoticed . '</IsNoticed>';
				$xml .= '<IsAlerted>' . $row->IsAlerted . '</IsAlerted>';
				$xml .= '<IsAccepted>' . $row->IsAccepted . '</IsAccepted>';
				$xml .= '<IsConnected>' . $row->IsConnected . '</IsConnected>';
				$xml .= '<Date>' . $row->Date . '</Date>';
				$xml .= '<DateModified>' . $row->DateModified . '</DateModified>';
				$xml .= '<TimeCreated>' . strtotime( $row->Date ) . '</TimeCreated>';
				$xml .= '<TimeModified>' . strtotime( $row->DateModified ) . '</TimeModified>';
				$xml .= '</Messages>';
				
				$obj->IsCrypto     = $row->IsCrypto;
				$obj->IsTyping     = $row->IsTyping;
				$obj->IsRead       = $row->IsRead;
				$obj->IsNoticed    = $row->IsNoticed;
				$obj->IsAlerted    = $row->IsAlerted;
				$obj->IsAccepted   = $row->IsAccepted;
				$obj->IsConnected  = $row->IsConnected;
				$obj->Date         = $row->Date;
				$obj->DateModified = $row->DateModified;
				$obj->TimeCreated  = strtotime( $row->Date );
				$obj->TimeModified = strtotime( $row->DateModified );
				
				$lastmessage  = ( $row->ID && $lastmessage < $row->ID ? $row->ID : $lastmessage );
				$lastactivity = ( $row->Date && $lastactivity < strtotime( $row->Date ) ? strtotime( $row->Date ) : $lastactivity );
					
				if( $row->IsNoticed == '0' && $row->PosterID != $u->ID )
				{
					$ids[$row->ID] = $row->ID;
					$uids[] = $row->UniqueID;
				}
				
				$json->Messages[] = $obj;
			}
			
			/*if ( $ids )
			{
				foreach ( $ids as $v )
				{
					$m = new dbObject( 'SBookMail' );
					if ( $m->Load( $v ) )
					{
						//$m->IsRead = 1;
						$m->IsNoticed = 1;
						$m->DateModified = date( 'Y-m-d H:i:s' );
						$m->Save();
					}
				}
			}*/
			/*if ( $uids )
			{
				// TODO: Check also encrypted messages connected on the same uniqueid for new method
			
				foreach ( $uids as $v )
				{*/
					if( $msg = fetchObjectRows( /*'
						SELECT 
							m.ID 
						FROM 
							SBookMail m
						WHERE 
								m.UniqueID = \'' . $v . '\'
							AND m.IsNoticed = "0" 
						ORDER BY 
							m.ID DESC 
					'*/'
						SELECT 
							m.ID 
						FROM 
							SBookMail m 
						WHERE 
								m.SenderID = ' . $_POST['ContactID'] . ' 
							AND m.ReceiverID = ' . $u->ID . ' 
							AND m.Message != "" 
							AND m.IsNoticed = "0" 
						ORDER BY 
							m.ID DESC 
						LIMIT 100 
					' ) )
					{
						foreach( $msg as $mg )
						{
							$m = new dbObject( 'SBookMail' );
							if ( $m->Load( $mg->ID ) )
							{
								//$m->IsRead = 1;
								$m->IsNoticed = 1;
								$m->DateModified = date( 'Y-m-d H:i:s' );
								$m->Save();
							}
						}
					}
				/*}
			}*/
			
			// Get encrytion key from storage to open encrypted messages
		
			if ( $cryptokey )
			{
				$xml .= '<CryptoType>1</CryptoType>';
				$xml .= '<CryptoID>' . $cryptoid . '</CryptoID>';
				$xml .= '<CryptoKey><![CDATA[' . $cryptokey . ']]></CryptoKey>';
				$xml .= '<PublicKey><![CDATA[' . $publickey . ']]></PublicKey>';
				
				$json->CryptoType = '1';
				$json->CryptoID   = $cryptoid;
				$json->CryptoKey  = $cryptokey;
				$json->PublicKey  = $publickey;
			}
			else if( $storage = fetchObjectRow( '
				SELECT 
					m.* 
				FROM 
					SBookMail m, 
					Users u, 
					SBookContact c 
				WHERE
						m.SenderID = \'' . $_POST['ContactID'] . '\' 
					AND m.ReceiverID = \'' . $u->ID . '\' 
					AND m.Type IN ( "cm" ) 
					AND c.ID = m.ReceiverID 
					AND u.ID = c.UserID 
					AND u.PublicKey = m.PublicKey 
				ORDER BY 
					m.ID DESC 
				LIMIT 1 
			' ) )
			{
				$xml .= '<CryptoType>2</CryptoType>';
				$xml .= '<CryptoID>' . $storage->UniqueKey . '</CryptoID>';
				$xml .= '<CryptoKey><![CDATA[' . $storage->EncryptionKey . ']]></CryptoKey>';
				$xml .= '<PublicKey><![CDATA[' . $storage->PublicKey . ']]></PublicKey>';
				
				$json->CryptoType = '2';
				$json->CryptoID   = $storage->UniqueKey;
				$json->CryptoKey  = $storage->EncryptionKey;
				$json->PublicKey  = $storage->PublicKey;
			}
			else if( $storage )
			{
				$xml .= '<CryptoType>3</CryptoType>';
				$xml .= '<CryptoID>' . $storage->UniqueID . '</CryptoID>';
				$xml .= '<CryptoKey><![CDATA[' . $storage->EncryptionKey . ']]></CryptoKey>';
				
				$json->CryptoType = '3';
				$json->CryptoID   = $storage->UniqueID;
				$json->CryptoKey  = $storage->EncryptionKey;
			}
			
			$xml .= '<LastMessage>' . ( $lastmessage ? $lastmessage : 0 ) . '</LastMessage>';
			$xml .= '<LastActivity>' . ( $lastactivity ? $lastactivity : 0 ) . '</LastActivity>';
			$xml .= '<ID>' . $u->ID . '</ID>';
			
			$json->LastMessage = ( $lastmessage ? $lastmessage : 0 );
			$json->LastActivity = ( $lastactivity ? $lastactivity : 0 );
			$json->ID = $u->ID;
			
			ob_end_clean();
			
			outputXML ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' ? $json : $xml, false, 'messages' );
			
			break;
		}
		
		if( isset( $_POST['Test'] ) && isset( $_POST['LastActivity'] ) )
		{
			// 1 sec loop delay ...
			sleep( 1 );
		}
		
	}
	
	ob_end_clean();
	
	throwXmlMsg ( EMPTY_LIST, false, 'messages' );
}

ob_end_clean();

throwXmlError ( MISSING_PARAMETERS, false, 'messages' );

?>
