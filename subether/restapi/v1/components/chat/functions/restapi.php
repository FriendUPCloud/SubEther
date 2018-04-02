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

include_once ( 'subether/classes/posthandler.class.php' );

$node = new dbObject( 'SNodes' );
if ( $node->Load( $nuser->NodeID ) )
{
	$m = new dbObject( 'SBookMail' );
	$m->SenderID = $webuser->ContactID;
	$m->ReceiverID = $nuser->ID;
	$m->CategoryID = 0;
	$m->Type = 'im';
	//$m->Message = str_replace ( array ( '<', '>' ), array ( '&lt;', '&gt;' ), stripslashes ( $_POST[ 'm' ] ) );
	$m->Message = mysql_real_escape_string( htmlentities( $_POST[ 'm' ] ) );
	$m->Date = date( 'Y-m-d H:i:s' );
	$m->Save();
	
	if ( $m->ID > 0 )
	{
		$ph = new PostHandler ( $node->Url . 'components/chat/' );
		$ph->AddVar ( 'Url', NODE_URL );
		$ph->AddVar ( 'SessionID', $node->SessionID );
		$ph->AddVar ( 'ID', $m->ID );
		$ph->AddVar ( 'SenderID', $m->SenderID );
		$ph->AddVar ( 'ReceiverID', $nuser->NodeMainID );
		$ph->AddVar ( 'Message', utf8_encode( $m->Message ) );
		$ph->AddVar ( 'Date', $m->Date );	
		$res = $ph->send();
		
		$u = new dbObject( 'SBookMail' );
		$u->Load( $m->ID );
		
		if ( $res && substr( $res, 0, 5 ) == "<?xml" )
		{
			$xml = simplexml_load_string ( trim( $res ) );
			
			if ( $xml->response == 'ok' && $xml->data )
			{
				$u->IsProcessed = 4;
			}
			else
			{
				$u->IsProcessed = -35;
			}
		}
		else
		{
			$u->IsProcessed = -35;
		}
		
		$u->Save();
		
		die( 'ok<!--separate-->' );
	}
}

?>
