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

$xml = array();

// Categories -----------------------------------------------------------------------------------

$cid = ( isset( $_POST['Categories'] ) ? explode( ',', $_POST['Categories'] ) : array() );

if( $cid )
{
	for ( $a = 0; $a < 10; $a++ )
	{
		if ( !count( $cid ) ) break;
	
		if ( $cid && $categories = $database->fetchObjectRows ( '
			SELECT
				c.*
			FROM
				SBookCategory c
			WHERE
					c.ID IN ( ' . implode( ',', $cid ) . ' ) 
				AND c.NodeID = "0"
			ORDER BY
				c.ID ASC
		' ) )
		{
			$cid = array();
		
			foreach ( $categories as $row )
			{
				$str  = '<Categories>';
				$str .= '<ID>' . $row->ID . '</ID>';
				$str .= '<CategoryID>' . $row->CategoryID . '</CategoryID>';
				$str .= '<Type>' . $row->Type . '</Type>';
				$str .= '<Name><![CDATA[' . $row->Name . ']]></Name>';
				$str .= '<Privacy>' . $row->Privacy . '</Privacy>';
				$str .= '<Settings><![CDATA[' . $row->Settings . ']]></Settings>';
				$str .= '<Description><![CDATA[' . $row->Description . ']]></Description>';
				$str .= '<IsSystem>' . $row->IsSystem . '</IsSystem>';
				$str .= '</Categories>';
			
				if( $row->CategoryID && !in_array( $row->CategoryID, $cid ) )
				{
					$cid[] = $row->CategoryID;
				}
			
				$xml[] = $str;
			}
		}
	}
}
else
{
	if ( $categories = $database->fetchObjectRows ( '
		SELECT
			c.*
		FROM
			SBookCategory c
		WHERE
			c.NodeID = "0"
		ORDER BY
			c.ID ASC
	' ) )
	{
		$cid = array();
		
		foreach ( $categories as $row )
		{
			$str  = '<Categories>';
			$str .= '<ID>' . $row->ID . '</ID>';
			$str .= '<CategoryID>' . $row->CategoryID . '</CategoryID>';
			$str .= '<Type>' . $row->Type . '</Type>';
			$str .= '<Name><![CDATA[' . $row->Name . ']]></Name>';
			$str .= '<Privacy>' . $row->Privacy . '</Privacy>';
			//$str .= '<Settings><![CDATA[' . $row->Settings . ']]></Settings>';
			$str .= '<Description><![CDATA[' . $row->Description . ']]></Description>';
			$str .= '<IsSystem>' . $row->IsSystem . '</IsSystem>';
			$str .= '</Categories>';
		
			if( $row->CategoryID && !in_array( $row->CategoryID, $cid ) )
			{
				$cid[] = $row->CategoryID;
			}
		
			$xml[] = $str;
		}
	}
}

// --- Output ---------------------------------------------------------------------------

if ( count( $xml ) )
{
	$xml = array_reverse( $xml );
	
	$xml[] = '<Listed>' . count( $xml ) . '</Listed>';
	
	outputXML ( implode( $xml ) );
}

throwXmlError ( EMPTY_LIST );

?>
