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

// Set limit
$limit = 50;

$required = array(
	'SessionID' 
);

$options = array(
	'ContactID', 'Type', 'IsCrypto', 'IsTyping', 
	'IsRead', 'IsNoticed', 'IsAlerted', 'IsAccepted', 
	'IsConnected', 'Count', 'Limit' 
);

if ( isset( $_REQUEST ) || isset( $_POST ) )
{
	/*foreach( $_POST as $k=>$p )
	{
		if( !in_array( $k, $required ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	foreach( $required as $r )
	{
		if( !isset( $_POST[$r] ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}*/
	
	// Temporary to view data i browser for development
	if( !$_POST )
	{
		$_POST = $_REQUEST;
	}
	
	// Get User data from sessionid
	$sess = new dbObject ( 'UserLogin' );
	$sess->Token = $_POST[ 'SessionID' ];
	if ( $sess->Load () )
	{
		$u = new dbObject ( 'SBookContact' );
		$u->UserID = $sess->UserID;
		if( !$u->Load () )
		{
			throwXmlError ( AUTHENTICATION_ERROR );
		}
	}
	else
	{
		throwXmlError ( SESSION_MISSING );
	}
	
	if( isset( $_POST['Count'] ) && $_POST['Count'] )
	{
		if( $count = $database->fetchObjectRow ( '
			SELECT 
				count(*) as Total 
			FROM 
				SBookMail 
			WHERE 
				(
					(
							ReceiverID = \'' . $u->ID . '\' 
						' . ( isset( $_POST['ContactID'] ) ? '
						AND SenderID IN (' . $_POST['ContactID'] . ') 
						' : '' ) . ' 
						AND Type IN ( "im", "vm" ) 
					) 
					OR 
					(		SenderID = \'' . $u->ID . '\' 
						' . ( isset( $_POST['ContactID'] ) ? ' 
						AND ReceiverID IN (' . $_POST['ContactID'] . ') 
						' : '' ) . ' 
						AND Type IN ( "im", "vm" ) 
					)
					OR
					(	
							ReceiverID = \'' . $u->ID . '\' 
						' . ( isset( $_POST['ContactID'] ) ? ' 
						AND SenderID IN (' . $_POST['ContactID'] . ') 
						' : '' ) . ' 
						AND Type IN ( "cm" ) 
					)
				)
				AND Message != ""
				' . ( isset( $_POST['Type'] ) ? '
				AND Type = \'' . $_POST['Type'] . '\' 
				' : '' ) . '
				' . ( isset( $_POST['IsCrypto'] ) ? '
				AND IsCrypto = \'' . $_POST['IsCrypto'] . '\' 
				' : '' ) . '
				' . ( isset( $_POST['IsTyping'] ) ? '
				AND IsTyping = \'' . $_POST['IsTyping'] . '\' 
				' : '' ) . '
				' . ( isset( $_POST['IsAlerted'] ) ? '
				AND IsAlerted = \'' . $_POST['IsAlerted'] . '\' 
				' : '' ) . '
				' . ( isset( $_POST['IsAccepted'] ) ? '
				AND IsAccepted = \'' . $_POST['IsAccepted'] . '\' 
				' : '' ) . '
				' . ( isset( $_POST['IsConnected'] ) ? '
				AND IsConnected = \'' . $_POST['IsConnected'] . '\' 
				' : '' ) . '
				' . ( isset( $_POST['IsRead'] ) ? '
				AND IsRead = \'' . $_POST['IsRead'] . '\' 
				' : '' ) . '
				' . ( isset( $_POST['IsNoticed'] ) ? '
				AND IsNoticed = \'' . $_POST['IsNoticed'] . '\' 
				' : '
				AND IsNoticed = "0" 
				' ) . '
			ORDER BY 
				ID DESC 
		' ) )
		{
			$xml  = '<Total>' . $count->Total . '</Total>';
			$xml .= '<ID>' . $u->ID . '</ID>';
			
			$json = new stdClass();
			$json->Total = $count->Total;
			$json->ID    = $u->ID;
			
			outputXML ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' ? $json : $xml );
		}
	}
	else if( $messages = $database->fetchObjectRows ( '
		SELECT 
			m.*, 
			m.EncryptionKey AS CryptoKey, 
			c.ID AS PosterID, 
			c.ImageID, 
			c.Username 
		FROM 
			SBookMail m, 
			SBookContact c 
		WHERE 
			(
				(
						m.ReceiverID = \'' . $u->ID . '\' 
					' . ( isset( $_POST['ContactID'] ) ? '
					AND m.SenderID IN (' . $_POST['ContactID'] . ') 
					' : '' ) . ' 
					AND m.Type IN ( "im", "vm" ) 
					AND c.ID = m.SenderID
				) 
				OR 
				(		m.SenderID = \'' . $u->ID . '\' 
					' . ( isset( $_POST['ContactID'] ) ? '
					AND m.ReceiverID IN (' . $_POST['ContactID'] . ') 
					' : '' ) . ' 
					AND m.Type IN ( "im", "vm" ) 
					AND c.ID = m.SenderID
				)
				OR
				(	
						m.ReceiverID = \'' . $u->ID . '\' 
					' . ( isset( $_POST['ContactID'] ) ? '
					AND m.SenderID IN (' . $_POST['ContactID'] . ') 
					' : '' ) . ' 
					AND m.Type IN ( "cm" ) 
					AND c.ID = m.ContactID 
				)
			)
			AND m.Message != ""
			' . ( isset( $_POST['Type'] ) ? '
			AND m.Type = \'' . $_POST['Type'] . '\' 
			' : '' ) . '
			' . ( isset( $_POST['IsCrypto'] ) ? '
			AND m.IsCrypto = \'' . $_POST['IsCrypto'] . '\' 
			' : '' ) . '
			' . ( isset( $_POST['IsTyping'] ) ? '
			AND m.IsTyping = \'' . $_POST['IsTyping'] . '\' 
			' : '' ) . '
			' . ( isset( $_POST['IsAlerted'] ) ? '
			AND m.IsAlerted = \'' . $_POST['IsAlerted'] . '\' 
			' : '' ) . '
			' . ( isset( $_POST['IsAccepted'] ) ? '
			AND m.IsAccepted = \'' . $_POST['IsAccepted'] . '\' 
			' : '' ) . '
			' . ( isset( $_POST['IsConnected'] ) ? '
			AND m.IsConnected = \'' . $_POST['IsConnected'] . '\' 
			' : '' ) . '
			' . ( isset( $_POST['IsRead'] ) ? '
			AND m.IsRead = \'' . $_POST['IsRead'] . '\' 
			' : '' ) . '
			' . ( isset( $_POST['IsNoticed'] ) ? '
			AND m.IsNoticed = \'' . $_POST['IsNoticed'] . '\' 
			' : '
			AND m.IsNoticed = "0" 
			' ) . '
		ORDER BY 
			m.ID DESC
		LIMIT ' . ( $_POST['Limit'] ? $_POST['Limit'] : $limit ) . '
	' ) )
	{
		$xml = ''; $cids = []; $msg = [];
		
		$IsCrypto = []; $IsTyping = []; $IsRead = []; $IsNoticed = []; $IsAlerted = []; $IsAccepted = []; $IsConnected = [];
		
		foreach ( $messages as $row )
		{
			$cids[$row->PosterID] = $row->PosterID;
		}
		
		$cids = GetUserDisplayname( $cids );
		
		foreach ( $messages as $row )
		{
			//$row->Poster = ( GetUserDisplayname( $row->PosterID ) ? GetUserDisplayname( $row->PosterID ) : $row->Username );
			$row->Poster = ( isset( $cids[$row->PosterID] ) ? $cids[$row->PosterID] : $row->Username );
			
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
			
			// New method for encrypted messages
			
			if( $row->Type == 'cm' )
			{
				$obj->CryptoKey = $row->CryptoKey;
				$obj->PublicKey = $row->PublicKey;
				
				$xml .= '<CryptoKey><![CDATA[' . $row->CryptoKey . ']]></CryptoKey>';
				$xml .= '<PublicKey><![CDATA[' . $row->PublicKey . ']]></PublicKey>';
			}
			
			$xml .= '<IsCrypto>' . $row->IsCrypto . '</IsCrypto>';
			$xml .= '<IsTyping>' . $row->IsTyping . '</IsTyping>';
			$xml .= '<IsRead>' . $row->IsRead . '</IsRead>';
			$xml .= '<IsNoticed>' . $row->IsNoticed . '</IsNoticed>';
			$xml .= '<IsAlerted>' . $row->IsAlerted . '</IsAlerted>';
			$xml .= '<IsAccepted>' . $row->IsAccepted . '</IsAccepted>';
			$xml .= '<IsConnected>' . $row->IsConnected . '</IsConnected>';
			$xml .= '<Date>' . $row->Date . '</Date>';
			$xml .= '<DateModified>' . $row->DateModified . '</DateModified>';
			$xml .= '</Messages>';
			
			if( $row->IsCrypto > 0    ) $IsCrypto[$row->ID]    = $row->ID;
			if( $row->IsTyping > 0    ) $IsTyping[$row->ID]    = $row->ID;
			if( $row->IsRead > 0      ) $IsRead[$row->ID]      = $row->ID;
			if( $row->IsNoticed > 0   ) $IsNoticed[$row->ID]   = $row->ID;
			if( $row->IsAlerted > 0   ) $IsAlerted[$row->ID]   = $row->ID;
			if( $row->IsAccepted > 0  ) $IsAccepted[$row->ID]  = $row->ID;
			if( $row->IsConnected > 0 ) $IsConnected[$row->ID] = $row->ID;
			
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
			
			if( $row->Type == 'cm' )
			{
				$obj->CryptoKey = $row->CryptoKey;
				$obj->PublicKey = $row->PublicKey;
			}
			
			$obj->IsCrypto     = $row->IsCrypto;
			$obj->IsTyping     = $row->IsTyping;
			$obj->IsRead       = $row->IsRead;
			$obj->IsNoticed    = $row->IsNoticed;
			$obj->IsAlerted    = $row->IsAlerted;
			$obj->IsAccepted   = $row->IsAccepted;
			$obj->IsConnected  = $row->IsConnected;
			$obj->Date         = $row->Date;
			$obj->DateModified = $row->DateModified;
			
			$msg[] = $obj;
		}
		
		$xml .= '<IsCrypto>'    . ( $IsCrypto    ? implode( ',', $IsCrypto )    : 0 ) . '</IsCrypto>';
		$xml .= '<IsTyping>'    . ( $IsTyping    ? implode( ',', $IsTyping )    : 0 ) . '</IsTyping>';
		$xml .= '<IsRead>'      . ( $IsRead      ? implode( ',', $IsRead )      : 0 ) . '</IsRead>';
		$xml .= '<IsNoticed>'   . ( $IsNoticed   ? implode( ',', $IsNoticed )   : 0 ) . '</IsNoticed>';
		$xml .= '<IsAlerted>'   . ( $IsAlerted   ? implode( ',', $IsAlerted )   : 0 ) . '</IsAlerted>';
		$xml .= '<IsAccepted>'  . ( $IsAccepted  ? implode( ',', $IsAccepted )  : 0 ) . '</IsAccepted>';
		$xml .= '<IsConnected>' . ( $IsConnected ? implode( ',', $IsConnected ) : 0 ) . '</IsConnected>';
		
		$xml .= '<ID>' . $u->ID . '</ID>';
		
		$json = new stdClass();
		$json->Messages    = $msg;
		$json->IsCrypto    = ( $IsCrypto    ? implode( ',', $IsCrypto )    : 0 );
		$json->IsTyping    = ( $IsTyping    ? implode( ',', $IsTyping )    : 0 );
		$json->IsRead      = ( $IsRead      ? implode( ',', $IsRead )      : 0 );
		$json->IsNoticed   = ( $IsNoticed   ? implode( ',', $IsNoticed )   : 0 );
		$json->IsAlerted   = ( $IsAlerted   ? implode( ',', $IsAlerted )   : 0 );
		$json->IsAccepted  = ( $IsAccepted  ? implode( ',', $IsAccepted )  : 0 );
		$json->IsConnected = ( $IsConnected ? implode( ',', $IsConnected ) : 0 );
		
		$json->ID = $u->ID;
		
		outputXML ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' ? $json : $xml );
	}
	
	throwXmlMsg ( EMPTY_LIST );
}

throwXmlError ( MISSING_PARAMETERS );

?>
