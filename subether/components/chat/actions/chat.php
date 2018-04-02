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

if( $_POST['u'] && ( $_POST['m'] || $_POST['data'] ) )
{
	if ( $nuser = CheckNodeUser( $_POST['u'] ) )
	{
		require( 'subether/restapi/components/chat/functions/restapi.php' );
		die( 'fail' );
	}
	
	
	
	$uniqueid = UniqueKey();
	
	// TODO: Remove all complexity and add keys to every message based on uniqueid. Maybe call it keyid to save some space.
	
	if( $_POST['encryption'] && $_POST['data'] )
	{
		//die( print_r( json_decode( $_POST['data'] ),1 ) . ' --' );
		
		$data = json_decode( $_POST['data'] );
		
		$encryptionid = UniqueKey();
		
		$test = array();
		
		if( $data->receivers && is_object( $data->receivers ) && $data->message )
		{
			//$newkeys = false;
			
			/*foreach( $data->receivers as $rec1=>$key1 )
			{
				if( $rec1 == 'sender' )
				{
					$contact = $webuser->ContactID;
					$reciever = $_POST['u'];
				}
				else
				{
					$contact = $rec1;
					$reciever = $webuser->ContactID;
				}
				
				if ( !$database->fetchObjectRow( $q = '
					SELECT 
						s.*, 
						u.PublicKey AS UserPublicKey 
					FROM 
						Users u, 
						SBookStorage s 
					WHERE 
							s.ContactID = \'' . $contact . '\' 
						AND s.IDs IN (' . $reciever . ') 
						AND s.Relation = "SBookContact" 
						AND s.IsDeleted = "0" 
						AND s.PublicKey = u.PublicKey 
						AND s.UserID = u.ID 
					ORDER BY s.ID DESC 
				', false, 'components/chat/action/chat.php' ) )
				{
					$newkeys = true;
				}
			}*/
			
			//if ( $newkeys || ( $data->newkey === true ) )
			
			/*// If there is no encryptionid it means we need to send out new keys
			if ( !$data->encryptionid )
			{*/
				foreach( $data->receivers as $rec2=>$key2 )
				{
					if( $rec2 == 'sender' )
					{
						$contact = $webuser->ContactID;
						$reciever = $_POST['u'];
					}
					else
					{
						$contact = $rec2;
						$reciever = $webuser->ContactID;
					}
					
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
						$m = new dbObject( 'SBookMail' );
						$m->UniqueID = $uniqueid;
						$m->ContactID = $webuser->ContactID;
						$m->SenderID = $reciever;
						$m->ReceiverID = $contact;
						$m->CategoryID = 0;
						$m->Type = 'cm';
						$m->Encryption = $_POST['encryption'];
						$m->UniqueKey = ( isset( $data->encryptionid ) ? $data->encryptionid : $encryptionid );
						$m->EncryptionKey = $key2;
						$m->PublicKey = $usr->PublicKey;
						$m->IsCrypto = 1;
						$m->Message = $data->message;
						$m->Date = date( 'Y-m-d H:i:s' );
						$m->Save();
						
						// Not using the old method anymore ...
						//$s = new dbObject( 'SBookStorage' );
						//$s->Relation = 'SBookContact';
						//$s->ContactID = $usr->ID;
						//$s->PublicKey = $usr->PublicKey;
						//$s->IDs = $reciever;
						//$s->EncryptionKey = $key2;
						//$s->UniqueID = $encryptionid = ( $encryptionid ? $encryptionid : UniqueKey() );
						//$s->UserID = $usr->UserID;
						//$s->DateCreated = date( 'Y-m-d H:i:s' );
						//$s->DateModified = date( 'Y-m-d H:i:s' );
						//$s->Save();
						
						$data->NewKeys = 1;
						
						// TODO: Add stats for saved chat messages for long-poll
						//LogStats( 'chat', 'save', $m->Type, $m->SenderID, $m->ReceiverID, $m->CategoryID, 'api' );
						
						UserActivity( 'messages', 'lastmessage', $m->SenderID, $m->ReceiverID, $m->ID, '' );
					}
				}
			/*}*/
		}
	}
	else
	{
		$m = new dbObject( 'SBookMail' );
		$m->UniqueID = $uniqueid;
		$m->SenderID = $webuser->ContactID;
		$m->ReceiverID = $_POST['u'];
		$m->CategoryID = 0;
		$m->Type = 'im';
		
		//$m->Message = htmlentities( $_POST['m'] );
		$m->Message = $_POST['m'];
		
		$m->Date = date( 'Y-m-d H:i:s' );
		$m->Save();
		
		UserActivity( 'messages', 'lastmessage', $m->SenderID, $m->ReceiverID, $m->ID, '' );
	}
	
	
	
	// Check if receiver is bot and send back an auto reply if command found
	if( $database->fetchObjectRow( '
		SELECT
			u.* 
		FROM
			SBookContact c, 
			Users u 
		WHERE 
				c.ID = \'' . $_POST['u'] . '\'
			AND u.ID = c.UserID
			AND u.IsDeleted = "0"
			AND u.IsDisabled = "0"
			AND u.UserType = "3" 
		ORDER BY
			u.ID DESC
	' ) )
	{
		include_once ( 'subether/restapi/functions.php' );
		
		if( $reply = AIbot( $_POST['m'], $webuser->Email ) )
		{
			$a = new dbObject( 'SBookMail' );
			$a->UniqueID = UniqueKey();
			$a->SenderID = $_POST['u'];
			$a->ReceiverID = $webuser->ContactID;
			$a->CategoryID = 0;
			$a->Type = 'im';
			$a->Message = $reply;
			$a->IsRead = 1;
			$a->IsNoticed = 1;
			$a->IsAlerted = 1;
			$a->Date = date( 'Y-m-d H:i:s' );
			$a->Save();
			
			UserActivity( 'messages', 'lastmessage', $a->SenderID, $a->ReceiverID, $a->ID, '' );
		}
	}
	
	if( $m->ID > 0 ) die( 'ok<!--separate-->' . ( isset( $data ) && $data ? print_r( $data,1 ) : '' ) );
	else die( 'fail' . print_r( $_POST,1 ) );
}
die( 'fail' . print_r( $_POST,1 ) );

?>
