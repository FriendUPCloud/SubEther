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

function SendFromOutServer ( $from = MAIL_REPLYTO, $to, $subject, $message, $attachments = false, $type = 'html', $server = MAIL_SMTP_HOST, $port = 25, $username = MAIL_USERNAME, $password = MAIL_PASSWORD )
{
	//die( ' Server: ' . $server . ' -- Username: ' . $username . ' .. Password: ' . $password . ' .. Subject: ' . ( strstr( $subject, '=?UTF-8?B?' ) ? $subject : ( '=?UTF-8?B?' . base64_encode ( $subject ) . '?=' ) ) . ' .. Port: ' . ( defined( 'MAIL_SMTP_PORT' ) && $port != 25 ? MAIL_SMTP_PORT : $port ) . ' .. From: ' . $from . ' .. To: ' . $to . ' .. Header: ' . "text/" . $type . "; charset=iso-8859-1" . ' .. Message: ' . utf8_decode ( $message ) );
	
	$email = new eMail ();
	$email->setHostInfo ( $server, $username, $password );
	$email->setSubject ( ( strstr( $subject, '=?UTF-8?B?' ) ? $subject : ( '=?UTF-8?B?' . base64_encode ( $subject ) . '?=' ) ) );
	$email->setPort ( defined( 'MAIL_SMTP_PORT' ) && $port != 25 ? MAIL_SMTP_PORT : $port );
	$email->setFrom ( $from );
	$email->_recipients = array ( $to );
	$email->addHeader ( "Content-type", "text/" . $type . "; charset=iso-8859-1" );
	
	$Article = utf8_decode ( $message );
	
	if ( $attachments )
	{
		foreach ( $attachments as $att )
		{
			$email->addAttachment ( $att );
		}
	}
	
	// Extract all images and add to mail data
	$embedImages = array ();
	$cid = 1;
	preg_match_all ( '/\<img[^>]*?\>/i', $Article, $matches );
	foreach ( $matches[0] as $match )
	{
		preg_match ( '/src\=\"([^"]*?)\"/i', $match, $src );
		preg_match ( '/style\=\"([^"]*?)\"/i', $match, $style );
		preg_match ( '/border\=\"([^"]*?)\"/i', $match, $border );
		if ( $style ) $style = ' style="' . $style[1] . '"'; else $style = '';
		if ( $border ) $border = ' border="' . $border[1] . '"'; else $border = '';
		$embedImages[] = array ( $match, '<img' . $style . $border . ' src="cid:image_' . 
		$mail->ID . '_' . $cid . '"/>', $src[1], 'image_' . $mail->ID . '_' . $cid );
		$cid++;
	}
	if ( count ( $embedImages ) && is_array ( $embedImages ) )
	{
		foreach ( $embedImages as $row )
		{
			list ( $original, $replace, $file, $tempName ) = $row;
			$Article = str_replace ( $original, $replace, $Article );
			$email->embedImage ( $file, false, false, $tempName );
		}
	}
	
	$email->setMessage ( $Article );
	
	$res = $email->send ();
	
	return $res;
}



if( $_POST )
{
	if( $acc = $database->fetchObjectRow( '
		SELECT 
			* 
		FROM 
			SBookMailAccounts 
		WHERE 
				UserID = \'' . $webuser->ID . '\' 
			AND Address = \'' . trim( $_POST['from'] ) . '\' 
		ORDER BY 
			ID ASC 
	' ) )
	{
		$attachments = false;
		
		if( $_POST['files'] && ( $files = $database->fetchObjectRows( '
			SELECT 
				fi.*, 
				fi.Access AS FileAccess, 
				fl.DiskPath AS FolderPath, 
				fl.Access AS FolderAccess 
			FROM 
				File fi, 
				Folder fl 
			WHERE 
					fi.FileFolder = fl.ID 
				AND fi.ID IN ( ' . $_POST['files'] . ' ) 
			ORDER BY 
				fi.ID ASC 
		' ) ) )
		{
			$attachments = array();
			
			foreach( $files as $file )
			{
				$attachments[] = BASE_DIR . '/' . $file->FolderPath . $file->Filename;
			}
		}
		
		//die( print_r( $attachments,1 ) . ' --' );
		
		$res = SendFromOutServer( $_POST['from'], $_POST['to'], $_POST['subject'], $_POST['message'], $attachments, 'html', $acc->OutServer, $acc->OutPort, $acc->OutUser, $acc->OutPass );
		
		// If the main send method doesn't work run backup
		
		if( !$res || $res != 'All done!' )
		{
			$res = SendFromOutServer( $_POST['from'], $_POST['to'], $_POST['subject'], $_POST['message'], $attachments );
		}
		
		if( $res == 'All done!' )
		{
			$folder = false;
			
			$acc->Folders = json_obj_decode( $acc->Folders, 'array' );
			
			if( $acc->Folders && is_array( $acc->Folders ) )
			{
				foreach( $acc->Folders as $key=>$fld )
				{
					if( strtolower( $fld ) == 'sent' )
					{
						$folder = $fld;
					}
					else if( !$folder && strtolower( end( explode( '.', $fld ) ) ) == 'sent' )
					{
						$folder = $fld;
					}
				}
			}
			
			if( $folder )
			{
				$_POST['files'] = ( $_POST['files'] ? explode( ',', $_POST['files'] ) : false );
				
				$h = new dbObject( 'SBookMailHeaders' );
				$h->UserID = $acc->UserID;
				$h->AccountID = $acc->ID;
				$h->Folder = $folder;
				$h->Subject = $_POST['subject'];
				$h->From = $_POST['from'];
				$h->To = $_POST['to'];
				$h->ReplyTo = $_POST['from'];
				$h->Message = $_POST['message'];
				$h->Files = ( $_POST['files'] && is_array( $_POST['files'] ) ? json_obj_encode( $_POST['files'], 'array' ) : '' );
				$h->Date = date( 'Y-m-d H:i:s' );
				$h->IsRead = 1;
				$h->Save();
			}
			
			die( 'ok<!--separate-->sendt: ' . $res );
		}
		
		die( 'fail<!--separate-->' . $res );
	}
}

die( 'fail' );

?>
