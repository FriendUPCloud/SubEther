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

include_once ( ( $root ? ( $root.'/' ) : '' ) . 'subether/functions/globalfuncs.php' );

// TODO: Make password secure and unable to read in database, and make support for more auth methods ...

if( !$imap_running && function_exists( 'imap_open' ) && ( $accounts = $database->fetchObjectRows( '
	SELECT 
		* 
	FROM 
		SBookMailAccounts 
	WHERE 
			ErrorCode = "0" 
		AND IsDeleted = "0" 
	ORDER BY 
		ID ASC 
' ) ) )
{
	$imap_running = true;
	
	$limit = 1000; $i = 0;
	
	foreach( $accounts as $acc )
	{
		// If we have stored 1000headers take a break
		if( $i >= $limit ) break;
		
		$newfolder = false;
		
		$acc->Folders = json_obj_decode( $acc->Folders, 'array' );
		
		// --- Phase 1: Connect to imap server, if failure write a error code and continue ---
		
		$imbox = imap_open( '{' . $acc->Server . ':' . $acc->Port . ( $acc->SSL ? '/imap/ssl' : '' ) . '}', $acc->Username, $acc->Password );
		
		if( !$imbox )
		{
			$e = new dbObject( 'SBookMailAccounts' );
			$e->ID = $acc->ID;
			if( $e->Load() )
			{
				$e->ErrorCode = 404;
				$e->Save();
				
				continue;
			}
		}
		
		// --- Phase 2: List mailboxes and store new updates, after that close connection ---
		
		$folders = imap_listmailbox( $imbox, '{' . $acc->Server . ':' . $acc->Port . ( $acc->SSL ? '/imap/ssl' : '' ) . '}', '*' );
		
		if( $folders && is_array( $folders ) )
		{
			if( !strstr( $folders[0], 'INBOX' ) )
			{
				$folders = array_reverse( $folders, true );
			}
			
			foreach( $folders as $fold )
			{
				$fold = end( explode( '}', $fold ) );
				
				if( !in_array( $fold, $acc->Folders ) )
				{
					$acc->Folders[] = $fold;
					
					$newfolder = true;
				}
			}
			
			if( $newfolder )
			{
				$f = new dbObject( 'SBookMailAccounts' );
				$f->ID = $acc->ID;
				if( $f->Load() )
				{
					$f->Folders = json_obj_encode( $acc->Folders, 'array' );
					$f->Save();
				}
			}
		}
		
		imap_close( $imbox );
		
		// --- Phase 3: Loop through folders and open connection on each folder to check if there is new headers, if there is store them ---
		
		if( $acc->Folders && is_array( $acc->Folders ) )
		{
			foreach( $acc->Folders as $key=>$fld )
			{
				$mails = array();
				
				$imap = imap_open( '{' . $acc->Server . ':' . $acc->Port . ( $acc->SSL ? '/imap/ssl' : '' ) . '}' . $fld, $acc->Username, $acc->Password );
				
				$deletes = imap_search( $imap, 'DELETED' );
				
				if( $headers = imap_search( $imap, 'ALL' ) )
				{
					// Delete if found in database
					
					if( $deletes && ( $delete = $database->fetchObjectRows( $q = '
						SELECT 
							* 
						FROM 
							SBookMailHeaders 
						WHERE 
								UserID = \'' . $acc->UserID . '\' 
							AND AccountID = \'' . $acc->ID . '\' 
							AND Folder = \'' . $fld . '\' 
							AND MessageID IN ( ' . implode( ',', $deletes ) . ' ) 
						ORDER BY 
							ID ASC 
					' ) ) )
					{
						foreach( $delete as $del )
						{
							$d = new dbObject( 'SBookMailHeaders' );
							$d->ID = $del->ID;
							if( $d->Load() )
							{
								$d->IsDeleted = 1;
								$d->Save();
								//$d->Delete();
							}
						}
					}
					
					// Exclude mails allready stored
					
					if( $headers && ( $mail = $database->fetchObjectRows( $q = '
						SELECT 
							* 
						FROM 
							SBookMailHeaders 
						WHERE 
								UserID = \'' . $acc->UserID . '\' 
							AND AccountID = \'' . $acc->ID . '\' 
							AND Folder = \'' . $fld . '\' 
							AND MessageID IN ( ' . implode( ',', $headers ) . ' ) 
						ORDER BY 
							ID ASC 
					' ) ) )
					{
						foreach( $mail as $m )
						{
							$mails[] = $m->MessageID;
						}
					}
					
					// Get new ones first
					
					$headers = array_reverse( $headers, true );
					
					foreach( $headers as $k=>$hdr )
					{
						if( ( $deletes && in_array( $hdr, $deletes ) ) || ( $mails && in_array( $hdr, $mails ) ) )
						{
							unset( $headers[$k] );
							continue;
						}
						
						// TODO: Make support for moving emails into other folders from server
						// Store new mails
						
						$h = new dbObject( 'SBookMailHeaders' );
						$h->UserID = $acc->UserID;
						$h->AccountID = $acc->ID;
						$h->Folder = $fld;
						$h->MessageID = $hdr;
						if( !$h->Load() )
						{
							if( $info = imap_headerinfo( $imap, $hdr ) )
							{
								$h->Subject = $info->subject;
								$h->From = $info->fromaddress;
								$h->To = $info->toaddress;
								$h->ReplyTo = $info->reply_toaddress;
								$h->Date = date( 'Y-m-d H:i:s', strtotime( $info->date ) );
								$h->IsRead = trim( $info->Unseen ) ? 0 : 1;
								$h->Save();
							}
						}
						
						$i++;
					}
					
					imap_close( $imap );
				}
			}
		}
	}
	
	$imap_running = false;
}

?>
