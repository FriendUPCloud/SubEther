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

// TODO: Make queue list in js

// TODO: Make this process better, the overall check for new mails are run by the cronscript, small request can be run here, else return okey if id's are not defined

$limit = 30;

$youshallpass = false;

if( ( $_POST['accid'] && $_POST['fld'] ) || $_POST['pass'] )
{
	$youshallpass = true;
}

if( !$_POST['accid'] && !$_POST['fld'] && $_POST['current'] && !isset( $_POST['moremail'] ) )
{
	$_POST['current'] = explode( '_', $_POST['current'] );
	
	$_POST['accid'] = $_POST['current'][0];
	$_POST['fld'] = $_POST['current'][1];
	
	$youshallpass = false;
}

// TODO: Open connection to mailboxes and store object in session until it's nessesary to close it, if not we have to run it in the cronscript

if( function_exists( 'imap_open' ) && $acc = $database->fetchObjectRows( '
	SELECT 
		* 
	FROM 
		SBookMailAccounts 
	WHERE 
		UserID = \'' . $webuser->ID . '\'
		AND ErrorCode = "0" 
		AND IsDeleted = "0" 
	ORDER BY 
		ID ASC 
' ) )
{
	$stored = array(); $closed = false; $i = 0;
	
	if( $youshallpass )
	{
		
		// List folders
		
		foreach( $acc as $m )
		{
			$m->Folders = json_obj_decode( $m->Folders, 'array' );
			
			$imbox = imap_open( '{' . $m->Server . ':' . $m->Port . ( $m->SSL ? '/imap/ssl' : '' ) . '}', $m->Username, $m->Password );
			
			// If we don't get a connection set error code and jump over
			if( !$imbox )
			{
				$e = new dbObject( 'SBookMailAccounts' );
				$e->ID = $m->ID;
				if( $e->Load() )
				{
					$m->ErrorCode = 404;
					
					$e->ErrorCode = 404;
					$e->Save();
					
					continue;
				}
			}
			// If we have a connection continue loading new mail
			if( $imbox )
			{
				$folders = imap_listmailbox( $imbox, '{' . $m->Server . ':' . $m->Port . ( $m->SSL ? '/imap/ssl' : '' ) . '}', '*' );
				
				if( $folders && is_array( $folders ) )
				{
					$new = false;
					
					if( !strstr( $folders[0], 'INBOX' ) )
					{
						$folders = array_reverse( $folders, true );
					}
					
					foreach( $folders as $fold )
					{
						$fold = end( explode( '}', $fold ) );
						
						if( !in_array( $fold, $m->Folders ) )
						{
							$m->Folders[] = $fold;
							
							$new = true;
						}
					}
					
					if( $new )
					{
						$f = new dbObject( 'SBookMailAccounts' );
						$f->ID = $m->ID;
						if( $f->Load() )
						{
							$f->Folders = json_obj_encode( $m->Folders, 'array' );
							$f->Save();
						}
					}
				}
				
				imap_close( $imbox );
			}
			
			$m->Folders = json_obj_encode( $m->Folders, 'array' );
		}
		
		// List headers
		
		foreach( $acc as $a )
		{
			if( $closed ) break;
			
			$a->Folders = json_obj_decode( $a->Folders, 'array' );
			
			if( $_POST['accid'] && $_POST['accid'] != $a->ID )
			{
				continue;
			}
			
			if( $a->ErrorCode > 0 )
			{
				continue;
			}
			
			if( $a->Folders && is_array( $a->Folders ) )
			{
				foreach( $a->Folders as $key=>$fld )
				{
					if( $closed ) break;
					
					if( $_POST['fld'] && $_POST['fld'] != $fld )
					{
						continue;
					}
					
					$imap = imap_open( '{' . $a->Server . ':' . $a->Port . ( $a->SSL ? '/imap/ssl' : '' ) . '}' . $fld, $a->Username, $a->Password );
					
					if( $imap )
					{
						if( $headers = imap_search( $imap, 'ALL' ) )
						{
							$mails = array();
							
							$deletes = imap_search( $imap, 'DELETED' );
							
							// TODO: Create support for moving emails to trash folder
							
							// Delete if found in database
							
							if( $deletes && ( $delete = $database->fetchObjectRows( $q = '
								SELECT 
									* 
								FROM 
									SBookMailHeaders 
								WHERE 
										UserID = \'' . $a->UserID . '\' 
									AND AccountID = \'' . $a->ID . '\' 
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
										$d->Delete();
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
										UserID = \'' . $a->UserID . '\' 
									AND AccountID = \'' . $a->ID . '\' 
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
							
							$headers = array_reverse( $headers, true );
							
							foreach( $headers as $k=>$hdr )
							{
								if( $closed ) break;
								
								if( $deletes && in_array( $hdr, $deletes ) )
								{
									unset( $headers[$k] );
									continue;
								}
								
								if( $mails && in_array( $hdr, $mails ) )
								{
									unset( $headers[$k] );
									continue;
								}
								
								// TODO: Make support for moving emails into other folders from server
								
								// Store new mails
								
								$h = new dbObject( 'SBookMailHeaders' );
								$h->UserID = $a->UserID;
								$h->AccountID = $a->ID;
								$h->Folder = $fld;
								$h->MessageID = $hdr;
								if( !$h->Load() )
								{
									if( $info = imap_headerinfo( $imap, $hdr ) )
									{
										//die( print_r( $info,1 ) . ' --' );
										$h->Subject = $info->subject;
										$h->From = $info->fromaddress;
										$h->To = $info->toaddress;
										$h->ReplyTo = $info->reply_toaddress;
										$h->Date = date( 'Y-m-d H:i:s', strtotime( $info->date ) );
										$h->IsRead = trim( $info->Unseen ) ? 0 : 1;
										$h->Save();
										
										if( $i >= $limit )
										{
											$closed = true;
											
											imap_close( $imap );
											
											output( 'ok<!--separate-->' . $h->ID );
											
											break;
										}
										
										$i++;
									}
								}
							}
						}
						
						if( !$closed ) imap_close( $imap );
					}
				}
			}
		}
		
	}
	
	output( 'ok<!--separate-->check' );
}

output( 'fail' . ( !function_exists( 'imap_open' ) ? ' function imap_open is missing, install dependency' : '' ) );

?>
