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

$required = array(
	'SessionID' 
);

$options = array(
	'LastPost', 'PostID', 'CategoryID', 'Limit' 
);

// TODO: remove when live or done debug testing
unset( $_REQUEST['route'] );

if ( isset( $_REQUEST ) )
{
	foreach( $_REQUEST as $k=>$p )
	{
		if( !in_array( $k, $required ) && !in_array( $k, $options ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	foreach( $required as $r )
	{
		if( !isset( $_REQUEST[$r] ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	
	// Get User data from sessionid
	$sess = new dbObject ( 'UserLogin' );
	$sess->Token = $_REQUEST['SessionID'];
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
	
	$guci = getUserContactsID( $u->ID );
	$gucids = ( $guci ? implode( ',', $guci ) : $u->ID );
	$uci = getUserGroupsID( $u->UserID );
	$ucids = ( $uci ? implode( ',', $uci ) : false );
	
	$qp = '
		SELECT 
			sm.ID,
			sm.UniqueID,
			sm.SeenBy,
			sm.Date,
			sm.DateModified,
			sm.SenderID,
			sm.ReceiverID,
			sm.CategoryID,
			sm.Subject,
			sm.Leadin,
			sm.Message,
			sm.Data,
			sm.Options,
			sm.ThreadID,
			sm.ParentID,
			sm.ReadBy,
			sm.Tags,
			sm.Type,
			sm.RateDownBy,
			sm.RateUpBy,
			sm.Access,
			sm.NodeID,
			sm.NodeMainID,
			sm.ID AS PostID, 
			us.Username AS Name, 
			us.ImageID AS Image,
			u2.Username AS User_Name,
			u2.ImageID AS User_Image, 
			ca.Name AS SBC_Name, 
			ca.ID AS SBC_ID,
			( sm.CategoryID != !GetWallID! ) AS `IsGroup` 
		FROM
			SBookContact us, 
			SBookMessage sm 
				LEFT JOIN SBookContact u2 ON
				(
						sm.ReceiverID = u2.ID
					AND sm.ReceiverID > 0
				) 
				LEFT JOIN SBookCategory ca ON
				(
						sm.CategoryID = ca.ID
					AND sm.CategoryID > 0
				) 
		WHERE 
				sm.Type IN ( "post", "article", "vote" ) 
			AND sm.ParentID = "0"
			AND
			(
				   sm.ThreadID = sm.ID
				OR sm.ThreadID = "0"
			) 
			AND sm.NodeID = "0" 
			AND sm.SenderID != !ContactID!
			AND
			( ' .  ( $ucids ? '
				(
						sm.CategoryID IN ( !UcIDS! ) 
					AND us.ID = sm.SenderID
				) 
				OR  ' : '' ) . '
				(
						sm.CategoryID = !GetWallID! 
					AND us.ID = sm.SenderID 
					AND sm.ReceiverID IN ( !GucIDS! )
				)
			) 
			AND
			(
				(
						sm.Access = "4" 
					AND sm.SenderID = !ContactID!
				) 
				OR
				(
						sm.Access = "2" 
					AND sm.SenderID = !ContactID!
				) 
				OR
				(
						sm.Access = "1" 
					AND sm.SenderID IN ( !GucIDS! )
				) 
				OR
				(
					sm.Access = "0"
				)
			)
			' . ( $_REQUEST['CategoryID'] > 0 ? '
			AND sm.CategoryID = \'' . $_REQUEST['CategoryID'] . '\' ' : '' ) . '
			' . ( $_REQUEST['PostID'] > 0 ? '
			AND sm.ID = \'' . $_REQUEST['PostID'] . '\' ' : '' ) . '
			' . ( $_REQUEST['LastPost'] > 0 ? '
			AND sm.ID > \'' . $_REQUEST['LastPost'] . '\' ' : '' ) . '
		ORDER BY
			sm.ID DESC 
		LIMIT ' . ( $_REQUEST['Limit'] ? $_REQUEST['Limit'] : 50 ) . ' 
	';
	
	$cq = '
		SELECT 
			m.ID,
			m.UniqueID,
			m.SeenBy,
			m.Date,
			m.DateModified,
			m.SenderID,
			m.ReceiverID,
			m.CategoryID,
			m.Subject,
			m.Leadin,
			m.Message,
			m.Data,
			m.Options,
			m.ThreadID,
			m.ParentID,
			m.ReadBy,
			m.Tags,
			m.Type,
			m.RateDownBy,
			m.RateUpBy,
			m.Access,
			m.NodeID,
			m.NodeMainID,
			m.ID AS PostID, 
			u.Username AS Name, 
			u.ImageID AS Image 
		FROM 
			SBookMessage m, 
			SBookContact u 
		WHERE
				m.ParentID IN ( !ComIDS! ) 
			AND m.NodeID = "0"
			AND u.ID = m.SenderID 
		ORDER BY  
			m.ID ASC
	';
	
	$qp = str_replace( '!GetWallID!', getWallID(), $qp );
	$qp = str_replace( '!ContactID!', $u->ID, $qp );
	$qp = str_replace( '!UcIDS!', $ucids, $qp );
	$qp = str_replace( '!GucIDS!', $gucids, $qp );
	
	$xml = array(); $ids = array(); $cms = array(); $cid = array(); $fid = array(); $filter = array(); $comments = array(); $posts = array();
	
	// --- Wall Posts ------------------------------------------------------------------------------------
	
	if ( $walldb = $database->fetchObjectRows( $qp ) )
	{
		foreach ( $walldb as $db )
		{
			if ( $db->ID > 0 )
			{
				$cms[$db->ID] = $db->ID;
			}
			
			$filter[] = $db;
		}
		
		// --- Post Comments ----------------------------------------------------------------------------
		
		$cq = str_replace( '!ComIDS!', ( is_array( $cms ) ?  implode( ',', $cms ) : 'FALSE' ), $cq );
		
		if ( is_array( $cms ) && ( $comdb = $database->fetchObjectRows( $cq ) ) )
		{
			foreach ( $comdb as $com )
			{
				$filter[] = $com;
			}
		}
		
		// --- Post Output ------------------------------------------------------------------------------
		
		foreach ( $filter as $out )
		{
			if ( $out->SenderID > 0 )
			{
				$ids[$out->SenderID] = $out->SenderID;
			}
			
			if ( $out->ReceiverID > 0 )
			{
				$ids[$out->ReceiverID] = $out->ReceiverID;
			}
			
			if ( $out->CategoryID > 0 )
			{
				$cid[$out->CategoryID] = $out->CategoryID;
			}
			
			if ( $out->Image > 0 )
			{
				$fid[$out->Image] = $out->Image;
			}
			
			if ( $out->User_Image > 0 )
			{
				$fid[$out->User_Image] = $out->User_Image;
			}
			
			if ( $out->Data && is_string( $out->Data ) )
			{
				$data = json_decode( $out->Data );
				
				if( isset( $data->FileID ) || isset( $data->LibraryFiles ) )
				{
					if( isset( $data->LibraryFiles ) && is_array( $data->LibraryFiles ) )
					{
						foreach( $data->LibraryFiles as $fi )
						{
							switch( $fi->MediaType )
							{
								case 'image':
								case 'album':
									$fid[$fi->FileID] = $fi->FileID;
									break;
							}
						}
					}
					else
					{
						switch( $data->MediaType )
						{
							case 'image':
								$fid[$data->FileID] = $data->FileID;
								break;
						}
					}
				}
			}
			
			if ( $out->RateDownBy || $out->RateUpBy )
			{
				$rdn = ( $out->RateDownBy && is_string( $out->RateDownBy ) ? json_decode( $out->RateDownBy ) : array() );
				$rup = ( $out->RateUpBy && is_string( $out->RateUpBy ) ? json_decode( $out->RateUpBy ) : array() );
				
				$sbstr = array(); $i = 0; $up = 0; $dn = 0;
				
				$rating = ( count( $rdn ) > count( $rup ) ? $rdn : $rup );
				
				foreach ( $rating as $k=>$uid )
				{
					if ( isset( $rdn[$k] ) )
					{
						$sbstr[] = '- 1 ' . GetUserDisplayname( $rdn[$k] );
						if ( $rdn[$k] == $u->ID )
						{
							$out->YouVotedDown = 0;
						}
						$i++; $dn++;
					}
					if ( isset( $rup[$k] ) )
					{
						$sbstr[] = '+1 ' . GetUserDisplayname( $rup[$k] );
						if ( $rup[$k] == $u->ID )
						{
							$out->YouVotedUp = 1;
						}
						$i++; $up++;
					}
				}
				
				$out->VoteList = json_encode( $sbstr );
				$out->VoteAmount = $i;
				$out->VotePercent = ( $i > 0 ? ( ( $up / $i * 100 ) ) : '0' );
			}
			
			if ( $out->SenderID == $u->ID && is_string( $out->SeenBy ) )
			{
				$sbn = json_decode( $out->SeenBy );
				
				$sbstr = array(); $i = 0;
				
				if ( $sbn )
				{
					foreach ( $sbn as $uid )
					{
						$sbstr[] = GetUserDisplayname( $uid );
						$i++;
					}
					
					$out->SeenList = json_encode( $sbstr );
					$out->SeenAmount = $i;
				}
			}
			
			if ( $out->ParentID > 0 && $out->Type == 'comment' )
			{
				if ( !isset( $comments[$out->ParentID] ) )
				{
					$comments[$out->ParentID] = array();
				}
				
				$comments[$out->ParentID][] = $out;
			}
			else
			{
				$posts[] = $out;
			}
		}
		
		// --- Xml output ------------------------------------------------------------------------------------
		
		$dnam = GetUserDisplayname( $ids );
		
		foreach ( $posts as $row )
		{
			$str  = '<Post>';
			$str .= '<PostID>' . $row->ID . '</PostID>';
			$str .= '<UniqueID>' . $row->UniqueID . '</UniqueID>';
			$str .= '<SenderID>' . $row->SenderID . '</SenderID>';
			$str .= '<SenderImage>' . $row->Image . '</SenderImage>';
			$str .= '<SenderUsername><![CDATA[' . $row->Name . ']]></SenderUsername>';
			$str .= '<SenderName><![CDATA[' . ( isset( $dnam[$row->SenderID] ) ? $dnam[$row->SenderID] : $row->Name ) . ']]></SenderName>';
			$str .= '<ReceiverID>' . $row->ReceiverID . '</ReceiverID>';
			
			if ( $row->ReceiverID > 0 && isset( $dnam[$row->ReceiverID] ) )
			{
				$str .= '<ReceiverImage>' . $row->User_Image . '</ReceiverImage>';
				$str .= '<ReceiverUserName><![CDATA[' . $row->User_Name . ']]></ReceiverUserName>';
				$str .= '<ReceiverName><![CDATA[' . ( isset( $dnam[$row->ReceiverID] ) ? $dnam[$row->ReceiverID] : $row->User_Name ) . ']]></ReceiverName>';
			}
			
			$str .= '<CategoryID>' . $row->CategoryID . '</CategoryID>';
			
			if ( $row->IsGroup )
			{
				$str .= '<CategoryName><![CDATA[' . $row->SBC_Name . ']]></CategoryName>';
			}
			
			$str .= '<Subject><![CDATA[' . $row->Subject . ']]></Subject>';
			$str .= '<Leadin><![CDATA[' . $row->Leadin . ']]></Leadin>';
			$str .= '<Message><![CDATA[' . $row->Message . ']]></Message>';
			$str .= '<Data><![CDATA[' . $row->Data . ']]></Data>';
			$str .= '<Options><![CDATA[' . $row->Options . ']]></Options>';
			$str .= '<Date>' . $row->Date . '</Date>';
			$str .= '<DateModified>' . $row->DateModified . '</DateModified>';
			$str .= '<ThreadID>' . $row->ThreadID . '</ThreadID>';
			$str .= '<ParentID>' . $row->ParentID . '</ParentID>';
			
			if ( $row->SenderID == $u->ID )
			{
				$str .= '<ReadBy>' . $row->ReadBy . '</ReadBy>';
				$str .= '<SeenBy>' . $row->SeenBy . '</SeenBy>';
			}
			if ( $out->SeenList )
			{
				$str .= '<SeenList><![CDATA[' . $out->SeenList . ']]></SeenList>';
				$str .= '<SeenAmount>' . $out->SeenList . '</SeenAmount>';
			}
			
			$str .= '<Tags><![CDATA[' . $row->Tags . ']]></Tags>';
			$str .= '<Type>' . $row->Type . '</Type>';
			$str .= '<VoteDownBy>' . $row->RateDownBy . '</VoteDownBy>';
			$str .= '<VoteUpBy>' . $row->RateUpBy . '</VoteUpBy>';
			
			if ( $row->VoteList )
			{
				$str .= '<VoteList><![CDATA[' . $row->VoteList . ']]></VoteList>';
				$str .= '<VoteAmount>' . $row->VoteAmount . '</VoteAmount>';
				$str .= '<VotePercent>' . $row->VotePercent . '</VotePercent>';
			}
			if ( $row->YouVotedDown )
			{
				$str .= '<YouVotedDown>' . $row->YouVotedDown . '</YouVotedDown>';
			}
			if ( $row->YouVotedUp )
			{
				$str .= '<YouVotedUp>' . $row->YouVotedUp . '</YouVotedUp>';
			}
			
			$str .= '<Access>' . $row->Access . '</Access>';
			$str .= '<IsGroup>' . $row->IsGroup . '</IsGroup>';
			
			if ( isset( $comments[$row->ID] ) )
			{
				$str .= '<Comments>';
				
				foreach( $comments[$row->ID] as $com )
				{
					$str .= '<Comment>';
					$str .= '<CommentID>' . $com->ID . '</CommentID>';
					$str .= '<UniqueID>' . $com->UniqueID . '</UniqueID>';
					$str .= '<SenderImage>' . $com->Image . '</SenderImage>';
					$str .= '<SenderUsername><![CDATA[' . $com->Name . ']]></SenderUsername>';
					$str .= '<SenderName><![CDATA[' . ( isset( $dnam[$com->SenderID] ) ? $dnam[$com->SenderID] : $com->Name ) . ']]></SenderName>';
					$str .= '<SenderID>' . $com->SenderID . '</SenderID>';
					$str .= '<ReceiverID>' . $com->ReceiverID . '</ReceiverID>';
					$str .= '<Message><![CDATA[' . $com->Message . ']]></Message>';
					$str .= '<Data><![CDATA[' . $com->Data . ']]></Data>';
					$str .= '<Date>' . $com->Date . '</Date>';
					$str .= '<DateModified>' . $com->DateModified . '</DateModified>';
					$str .= '<ThreadID>' . $com->ThreadID . '</ThreadID>';
					$str .= '<ParentID>' . $com->ParentID . '</ParentID>';
					
					if ( $com->SenderID == $u->ID )
					{
						$str .= '<SeenBy>' . $com->SeenBy . '</SeenBy>';
						$str .= '<ReadBy>' . $com->ReadBy . '</ReadBy>';
					}
					
					$str .= '<Tags><![CDATA[' . $com->Tags . ']]></Tags>';
					$str .= '<Type>' . $com->Type . '</Type>';
					$str .= '<VoteDownBy>' . $com->RateDownBy . '</VoteDownBy>';
					$str .= '<VoteUpBy>' . $com->RateUpBy . '</VoteUpBy>';
					
					if ( $com->VoteList )
					{
						$str .= '<VoteList><![CDATA[' . $com->VoteList . ']]></VoteList>';
						$str .= '<VoteAmount>' . $com->VoteAmount . '</VoteAmount>';
						$str .= '<VotePercent>' . $com->VoteAmount . '</VotePercent>';
					}
					if ( $com->YouVotedDown )
					{
						$str .= '<YouVotedDown>' . $com->YouVotedDown . '</YouVotedDown>';
					}
					if ( $com->YouVotedUp )
					{
						$str .= '<YouVotedUp>' . $com->YouVotedUp . '</YouVotedUp>';
					}
					
					$str .= '<Access>' . $com->Access . '</Access>';
					$str .= '</Comment>';
				}
				
				$str .= '<Listed>' . count( $comments[$row->ID] ) . '</Listed>';
				$str .= '</Comments>';
			}
			
			$str .= '</Post>';
			
			$xml[] = $str;
		}
	}
	
	// --- Output ---------------------------------------------------------------------------
	
	if ( count( $xml ) )
	{
		//$xml = array_reverse( $xml );
		
		$xml[] = '<Listed>' . count( $xml ) . '</Listed>';
		
		if ( count( $cid ) )
		{
			$xml[] = '<Categories>' . implode( ',', $cid ) . '</Categories>';
		}
		
		if ( count( $fid ) )
		{
			$xml[] = '<Images>' . implode( ',', $fid ) . '</Images>';
		}
		
		outputXML ( '<Posts>' . implode( $xml ) . '</Posts>' );
	}
	
	throwXmlMsg ( EMPTY_LIST );
}

throwXmlError ( MISSING_PARAMETERS );

?>
