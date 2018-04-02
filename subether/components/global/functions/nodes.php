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

$str = '<h2>Nodes</h2><br>';

$str .= '<div>';
$str .= '<input id="NodeVerify" type="hidden" value="' . ( defined( 'NODE_VERIFICATION' ) ? NODE_VERIFICATION : '"{DC65D301-0301-4CA7-B2E1-E6490BAB7F5E}.xml"' ) . '">';
$str .= '<input id="NodeFinder" style="width:calc(60% - 250px);margin-right:10px;" type="text"/> <button onclick="searchAfterNodes(ge(\'NodeFinder\').value)">Search The Ether</button>';
$str .= '</div>';

$str .= '<div id="SearchResults" style="padding-right:10px;padding-bottom:10px;overflow:auto;max-height:70px;margin-top:10px;"></div>';

$str .= '<div id="SearchCheck" style="padding-right:10px;height:30px;padding-bottom:5px;margin-top:10px;"></div>';

if( $nodes = $database->fetchObjectRows ( '
	SELECT * 
	FROM SNodes 
	ORDER BY ID ASC, SortOrder ASC 
' ) )
{
	if( !function_exists( 'NodePrivacy' ) )
	{
		function NodePrivacy( $open )
		{
			switch( $open )
			{
				case -1:
					return 'secret';
					break;
				
				case 1:
					return 'open';
					break;
				
				default:
					return 'closed';
					break;
				
			}
		}
	}
	
	$str .= '<table style="width:100%;margin-bottom:30px;margin-top:20px;"><tr>';
	$str .= '<td><strong>Node</strong></td>';
	$str .= '<td><strong>Product</strong></td>';
	$str .= '<td><strong>Version</strong></td>';
	$str .= '<td><strong>Privacy</strong></td>';
	$str .= '<td><strong>Users</strong></td>';
	$str .= '<td><strong>Location</strong></td>';
	$str .= '<td><strong>Owner</strong></td>';
	$str .= '<td><strong>Status</strong></td>';
	$str .= '<td><strong>LastLogin</strong></td>';
	$str .= '<td><strong>Key</strong></td>';
	$str .= '<td><strong>Main</strong></td>';
	$str .= '<td><strong>Index</strong></td>';
	$str .= '<td><strong>#</strong></td>';
	$str .= '</tr>';
	
	foreach( $nodes as $n )
	{
		$str .= '<tr>';
		$str .= '<td title="' . $n->UniqueID . '"><a href="' . $n->Url . '" target="_BLANK">' . $n->Url . '</a></td>';
		$str .= '<td>' . $n->Name . '</td>';
		$str .= '<td>' . $n->Version . '</td>';
		
		if( $n->IsMain )
		{
			$str .= '<td><select onchange="updatePrivacy(this.value)">';
			
			foreach( array( '1'=>'open', '0'=>'closed', '-1'=>'secret' ) as $k=>$v )
			{
				$str .= '<option value="' . $k . '"' . ( $n->Open == $k ? ' selected="selected"' : '' ) . '>' . $v . '</option>';
			}
			
			$str .= '</select></td>';
		}
		else
		{
			$str .= '<td>' . ( $n->DateCreated ? NodePrivacy( $n->Open ) : '' ) . '</td>';
		}
		
		$str .= '<td>' . $n->Users . '</td>';
		$str .= '<td>' . $n->Location . '</td>';
		$str .= '<td>' . ( $n->Email ? ( '<a href="mailto:' . $n->Email . '">' . $n->Owner . '</a>' ) : $n->Owner )  . '</td>';
		$str .= '<td>' . ( $n->IsPending ? 'pending' : '' ) . ( !$n->IsDenied && $n->IsConnected ? 'connected' : '' ) . ( $n->IsDenied ? 'denied' : '' ) . ( !$n->IsConnected && !$n->IsDenied && !$n->IsPending ? 'disconnected' : '' ) . '</td>';
		$str .= '<td>' . ( $n->DateLogin != '0000-00-00 00:00:00' ? date( 'd/m H:i', strtotime( $n->DateLogin ) ) : '' ) . '</td>';
		$str .= '<td title="' . $n->PublicKey . '">' . ( $n->PublicKey ? 1 : 0 ) . '</td>';
		$str .= '<td>' . ( $n->IsMain ? 1 : 0 ) . '</td>';
		$str .= '<td>' . ( $n->IsIndex ? 1 : 0 ) . '</td>';
		$str .= '<td>';
		
		if( !$n->IsMain )
		{
			if( !$n->IsConnected && !$n->IsDenied && !$n->IsPending )
			{
				$str .= '<button onclick="updateNode(\'' . $n->ID . '\',\'allow\')">Connect</button>';
			}
			else if( $n->IsDenied || ( !$n->IsConnected && !$n->IsDenied && !$n->IsPending ) )
			{
				$str .= '<button onclick="updateNode(\'' . $n->ID . '\',\'allow\')">Allow</button>';
			}
			else
			{
				$str .= '<button onclick="updateNode(\'' . $n->ID . '\',\'deny\')">Deny</button>';
			}
			
			$str .= '<button onclick="deleteNode(\'' . $n->ID . '\')">Delete</button>';
		}
		
		$str .= '</td>';
		$str .= '</tr>';
	}
	
	$str .= '</table>';
}
else
{
	$str .= '<div>No nodes ...</div>';
}

?>
