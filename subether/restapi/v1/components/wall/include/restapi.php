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

$xml = array(); $cid = array(); $fid = array();

// --- Wall Posts ------------------------------------------------------------------------------------

if ( $posts = $database->fetchObjectRows ( '
	SELECT
		m.*
	FROM
		SBookMessage m
	WHERE	
			m.NodeID = "0" 
		AND m.Access = "0"'
		. ( $_POST['CategoryID'] ? 'AND m.CategoryID = \'' . $_POST['CategoryID'] . '\'' : 'AND m.CategoryID > 0' ) 
		. ( $_POST['Type'] ? 'AND m.Type = \'' . $_POST['Type'] . '\'' : '' ) . '
	ORDER BY
		m.ID DESC
	LIMIT 15
' ) )
{
	$i = 0;
	foreach ( $posts as $row )
	{
		if ( $row->ParentID > 0 )
		{
			$msg = new dbObject( 'SBookMessage' );
			$msg->ID = $row->ParentID;
			$msg->Load();
		}
		
		$str  = '<Posts>';
		$str .= '<ID>' . $row->ID . '</ID>';
		$str .= '<SenderID>' . $row->SenderID . '</SenderID>';
		$str .= '<ReceiverID>' . $row->ReceiverID . '</ReceiverID>';
		$str .= '<CategoryID>' . $row->CategoryID . '</CategoryID>';
		$str .= '<Message><![CDATA[' . $row->Message . ']]></Message>';
		$str .= '<Data><![CDATA[' . $row->Data . ']]></Data>';
		$str .= '<Date>' . $row->Date . '</Date>';
		$str .= '<DateModified>' . $row->DateModified . '</DateModified>';
		$str .= '<ParentID>' . ( $row->ParentID > 0 && $msg->NodeMainID > 0 ? $msg->NodeMainID : $row->ParentID ) . '</ParentID>';
		$str .= '<SeenBy>' . $row->SeenBy . '</SeenBy>';
		$str .= '<Tags><![CDATA[' . $row->Tags . ']]></Tags>';
		$str .= '<Type>' . $row->Type . '</Type>';
		$str .= '<Rating>' . $row->Rating . '</Rating>';
		$str .= '</Posts>';
		
		$row->Data = json_decode( $row->Data );
		
		if ( $row->CategoryID > 0 && !in_array( $row->CategoryID, $cid ) )
		{
			$cid[] = $row->CategoryID;
		}
		
		if ( $row->Data )
		{
			$row->Data = isset( $row->Data->FileID ) ? array( $row->Data ) : $row->Data;
			
			foreach ( $row->Data as $data )
			{
				if ( $data->FileID > 0 && !in_array( $data->FileID, $fid ) )
				{
					$fid[] = $data->FileID;
				}
			}
		}
		
		$xml[] = $str;
		
		$i++;
	}
}

// --- Output ---------------------------------------------------------------------------

if ( count( $xml ) )
{
	$xml = array_reverse( $xml );
	
	$xml[] = '<Listed>' . count( $xml ) . '</Listed>';
	
	if ( count( $cid ) )
	{
		$xml[] = '<Categories>' . implode( ',', $cid ) . '</Categories>';
	}
	
	if ( count( $fid ) )
	{
		$xml[] = '<Images>' . implode( ',', $fid ) . '</Images>';
	}
	
	outputXML ( implode( $xml ) );
}

throwXmlError ( EMPTY_LIST );

?>
