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

global $webuser;

// Set limit
$limit = $_POST[ 'limit' ] ? $_POST[ 'limit' ] : 1000;

$mstr = ''; $msgs = array(); $lastmessage = ''; $lastdatetime = '';

if( $acc = $database->fetchObjectRow( '
	SELECT
		*
	FROM
		SBookMailAccounts
	WHERE
		UserID = \'' . $webuser->ID . '\' 
		AND IsDeleted = "0" 
	ORDER BY
		ID ASC
' ) )
{
	if( $acc->Folders )
	{
		$acc->Folders = json_obj_decode( $acc->Folders, 'array' );
	}
	
	if( $headers = $database->fetchObjectRows( $q = '
		SELECT
			*
		FROM
			SBookMailHeaders
		WHERE
				UserID = \'' . $webuser->ID . '\' 
			AND AccountID = \'' . ( $_POST['accid'] ? $_POST['accid'] : $acc->ID ) . '\' 
			' . ( $_POST[ 'lastmessage' ] > 0 ? 'AND ID > \'' . $_POST[ 'lastmessage' ] . '\' ' : '' ) . '
			AND IsDeleted = "0"
			AND ( ( Folder = \'' . ( $_POST['fld'] ? $_POST['fld'] : 'INBOX' ) . '\' AND MovedTo = "" ) 
			OR MovedTo = \'' . ( $_POST['fld'] ? $_POST['fld'] : 'INBOX' ) . '\' ) 
		ORDER BY 
			Date DESC
		' . ( $limit ? ( 'LIMIT ' . $limit ) : '' ) . '
	' ) )
	{
		$ii = 0; 
		
		foreach( $headers as $m )
		{
			$lastmessage = ( $lastmessage && $lastmessage > $m->ID ? $lastmessage : $m->ID );
		}
		
		foreach( $headers as $m )
		{
			// Set lastmessage and notify
			if( $ii == 0 )
			{
				$lastdatetime = $m->Date;
			}
			
			// If folder is sent change to recipient	
			if( $m->Folder && strtolower( $m->Folder ) == 'sent' )
			{
				$m->From = $m->To;
			}
			
			// If mail has moved folder choose moved folder
			if( $m->MovedTo )
			{
				$m->Folder = $m->MovedTo;
			}
			
			$msg  = '<div rowid="' . $m->ID . '" class="Message' . ( $m->IsRead == 0 ? ' NotRead' : '' ) . '" accountid="' . $m->AccountID . '" folder="' . $m->Folder . '" messageid="' . $m->MessageID . '" onclick="openMail(this)">';
			$msg .= '<div class="Mark"><input type="checkbox"/></div>';
			$msg .= '<div class="From">' . str_decode_replace( strip_tags( $m->From ) ) . '</div>';
			$msg .= '<div class="Subject">' . str_decode_replace( $m->Subject ) . '</div>';
			$msg .= '<div class="Date">' . date( 'd. M Y H:i', strtotime( $m->Date ) ) . '</div>';
			$msg .= '<div class="clearboth" style="clear:both"></div>';
			$msg .= '</div>';
			
			$msgs[] = $msg;
			
			$ii++;
		}
	}
	
	if( $limit && $msgs )
	{
		$mstr .= '<ul>';
		
		//for( $l = $limit; $l >= 0; $l-- )
		for( $l = 0; $l <= $limit; $l++ )
		{
			if( $msgs[$l] )
			{
				$mstr .= '<li class="line_' . $l . '">' . $msgs[$l] . '</li>';
			}
		}
		
		$mstr .= '</ul>';
	}
	
	if( isset( $_REQUEST['bajaxrand'] ) )
	{
		die( $msgs ? ( 'ok<!--separate-->' . ( $msgs ? implode( '<!--message-->', $msgs ) : '' ) . '<!--separate-->' . $lastmessage . '<!--separate-->' . $lastdatetime ) : 'fail<!--separate-->' );
	}
	
	//die( print_r( $msgs,1 ) . ' -- ' . $mstr . ' -- ' . $q );
}

?>
