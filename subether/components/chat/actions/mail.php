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

$sumReplace = '<!-- REPLACE -->';

if( $acc = $database->fetchObjectRow( $q = '
	SELECT 
		a.*, 
		h.ID AS HeaderID,
		h.Folder, 
		h.FolderID, 
		h.MessageID, 
		h.Subject, 
		h.To, 
		h.From, 
		h.Date, 
		h.ReplyTo, 
		h.Message, 
		h.Files 
	FROM 
		SBookMailAccounts a, 
		SBookMailHeaders h 
	WHERE 
			a.UserID = \'' . $webuser->ID . '\' 
		AND a.ID = \'' . $_POST['accid'] . '\' 
		AND h.AccountID = a.ID 
		AND h.ID = \'' . $_POST['hid'] . '\' 
	ORDER BY 
		h.ID ASC 
' ) )
{
	$fld = ''; $fstr = ''; $mstr = ''; $files = false;
	
	$acc->Folders = json_obj_decode( $acc->Folders, 'array' );
	$acc->Files = json_obj_decode( $acc->Files, 'array' );
	
	if( $acc->Folders && is_array( $acc->Folders ) )
	{
		foreach( $acc->Folders as $key=>$folder )
		{
			if( $folder == $acc->Folder )
			{
				$fld = $folder;
			}
		}
	}
	
	if( $acc->Files && is_array( $acc->Files ) )
	{
		$files = $acc->Files;
	}
	
	// If we have a mail stored in the system choose that
	if( !$acc->MessageID && $acc->Message )
	{
		$output = new stdClass();
		$output->subject = $acc->Subject;
		$output->to = $acc->To;
		$output->date = $acc->Date;
		$output->from = $acc->From;
		$output->replyto = $acc->ReplyTo;
		$output->messageId = $acc->MessageID;
		$output->headerId = $acc->HeaderID;
		$output->account = $acc->Address;
		$output->body = $acc->Message;
	}
	// Else look for mail on mail server by messageid
	else if( $fld && ( $imap = imap_open( $s = ( '{' . $acc->Server . ':' . $acc->Port . ( $acc->SSL ? '/imap/ssl' : '' ) . '}' . $fld ), $acc->Username, $acc->Password ) ) )
	{
		$info = imap_headerinfo( $imap, $acc->MessageID );
		
		$output = new stdClass();
		$output->subject = $info->subject;
		$output->to = $info->toaddress;
		$output->date = $info->date;
		$output->from = $info->fromaddress;
		$output->replyto = $info->reply_toaddress;
		$output->messageId = $acc->MessageID;
		$output->headerId = $acc->HeaderID;
		$output->account = $acc->Address;
		$output->body = '';
		
		// Fetch email structure
		//$structure = imap_fetchstructure( $imap, $acc->MessageID );
		
		$body = imap_body( $imap, $acc->MessageID, FT_PEEK );
		
		//$header = imap_fetchbody( $imap, $acc->MessageID, 0 );
		//$mime = mail_mimesplit( $header, $body );
		$mime = mail_fetchparts( $imap, $acc->MessageID );
		
		//die( 'ok<!--separate-->' . print_r( $mime,1 ) );
		
		if( $mime && isset( $mime[1]->header ) && !isset( $mime[1]->header['filename'] ) && $mime[1]->body )
		{
			$body = $mime[1]->body;
		}
		
		if( $bod = explode( "\r\n", $body ) )
		{
			$out = $htmlout = $plain = '';
			$encoding = 'utf-8';
			
			// Check if we have html email
			$html = $begin = false;
			$limiter = false;
			$i = 0;
			
			foreach( $bod as $b )
			{
				// check limiter
				if( $i++ == 0 && !$limiter )
				{
					$limiter = trim( $b );
				}
				
				// Check if we've got HTML
				if( preg_match( '/content\-type\:[\s]+text\/htm/i', $b, $m ) )
				{
					$html = true;
					if( preg_match( '/charset\=(.*)/i', $b, $encoding ) )
					{
						$encoding = $encoding[1];	
					}
				}
				// Check if we're ready to begin
				if( $html == true && !$begin )
				{
					if( strlen( $b ) == 0 )
					{
						$begin = true;
					}
				}
				// Scoop up HTML!
				else if( $html == true && $begin )
				{
					if( $limiter && preg_match( '/' . $limiter . '/', $b ) )
					{
						break;
					}
					$b = str_replace( '=3D', $sumReplace, $b );
					if( substr( $b, -1, 1 ) == '=' )
					{
						$b = substr( $b, 0, strlen( $b ) - 1 );
					}
					$b = str_replace( $sumReplace, '=', $b );
					$htmlout .= $b;
				}
				// It's plain
				else
				{
					$plain .= $b . '<br>';
				}
			}
			
			//die( print_r( $bod,1 ) . ' -- ' . $htmlout . ' .. ' . $plain );
			
			// If html
			if( $htmlout )
			{
				$output->body = mb_convert_encoding( $htmlout, 'utf-8', $encoding );
			}
			// If plain
			else if( $plain )
			{
				$output->body = utf8_encode( $plain );
			}
		}
		
		// Remove some tags
		$output->body = preg_replace( '/\<[\/]{0,1}html[^>]*?\>/i', '', $output->body );
		$output->body = preg_replace( '/\<[\/]{0,1}body[^>]*?\>/i', '', $output->body );
		$output->body = preg_replace( '/\<[\/]{0,1}meta[^>]*?\>/i', '', $output->body );
		
		imap_close( $imap );
		
		// If no files save files if attached
		if( !$files && $mime && is_array( $mime ) )
		{
			$files = array();
			
			foreach( $mime as $m )
			{
				if( isset( $m->header['filename'] ) && isset( $m->body ) )
				{
					$base64 = false;
					
					if( isset( $m->header['Content-Transfer-Encoding'] ) && $m->header['Content-Transfer-Encoding'] == 'base64' )
					{
						//$m->body = $base64;
						$base64 = true;
					}
					
					$lib = new Library ();
					$lib->UserID = $parent->cuser->ID;
					//if( strtolower( $parent->folder->MainName ) != 'profile' )
					//{
					//	$lib->CategoryID = $parent->folder->CategoryID;
					//}
					$lib->ParentFolder = 'Library';
					$lib->FolderName = 'Emails';
					$lib->FolderAccess = 2;
					$lib->FileAccess = 2;
					$lib->FileEncoding = ( $base64 ? 'base64' : $m->header['Content-Transfer-Encoding'] );
					$lib->Filename = $m->header['filename'];
					$lib->FileContent = $m->body;
					$lib->SaveContentToFile();
					
					//die( print_r( $lib,1 ) . ' -- ' . print_r( $m,1 ) );
					
					if( $lib->FileID > 0 )
					{
						$files[] = $lib->FileID;
					}
				}
			}
			
			
			$h = new dbObject( 'SBookMailHeaders' );
			$h->ID = $acc->HeaderID;
			if( $h->Load() && $files && is_array( $files ) )
			{
				$h->Files = json_obj_encode( $files, 'array' );
				$h->Save();
			}
		}
	}
	
	
	
	// Get files by fileid
	if( $files && is_array( $files ) && ( $row = $database->fetchObjectRows( '
		SELECT
			*
		FROM
			File 
		WHERE
			ID IN ( ' . implode( ',', $files ) . ' )
		ORDER BY 
			ID ASC 
	' ) ) )
	{
		$fstr .= '<ul>';
		
		foreach( $row as $f )
		{
			$downloadLink = '?component=library&action=download&type=file&fid=' . $f->ID;
			
			$fstr .= '<li><span>' . $f->Title . ' ' . libraryFilesize( $f->Filesize ) . ' </span><span class="download"><a href="' . $downloadLink . '"><img src="admin/gfx/icons/disk.png"/></a></span></li>';
		}
		
		$fstr .= '</ul>';
	}
	
	$h = new dbObject( 'SBookMailHeaders' );
	$h->ID = $acc->HeaderID;
	$h->IsRead = '0';
	if( $h->Load() )
	{
		$h->IsRead = 1;
		$h->Save();
	}
	
	$mstr = '';
	
	$mstr .= '<div>';
	$mstr .= '<div class="headderbox">';
	$mstr .= '<table>';
	$mstr .= '<tr class="from"><td class="leftcol">From:</td><td class="rightcol"><span var="from">' . str_decode_replace( $output->from ) . '</span></td></tr>';
	$mstr .= '<tr class="date"><td class="leftcol">Date:</td><td class="rightcol"><span class="date">' . date( 'd. M Y H:i', strtotime( $output->date ) ) . '</span></td></tr>';
	$mstr .= '<tr class="to"><td class="leftcol">To:</td><td class="rightcol"><span var="to">' . str_decode_replace( $output->to ) . '</span></td></tr>';
	$mstr .= '<tr class="subject"><td class="leftcol">Subject:</td><td class="rightcol"><span var="subject">' . str_decode_replace( $output->subject ) . '</span></td></tr>';
	$mstr .= '</table>';
	$mstr .= '</div>';
	$mstr .= '<div class="headderfiles">';
	$mstr .= '<div id="Hfiles" class="hfiles_inner">';
	if( $fstr )
	{
		$mstr .= $fstr;
	}
	$mstr .= '</div>';
	$mstr .= '</div>';
	$mstr .= '<div class="clearboth" style="clear:both"></div>';
	$mstr .= '<div class="message" var="message">' . $output->body . '</div>';
	$mstr .= '<input type="hidden" name="address" value="' . $output->account . '"/>';
	$mstr .= '<input type="hidden" name="replyto" value="' . str_decode_replace( $output->replyto ) . '"/>';
	$mstr .= '<input type="hidden" name="messageid" value="' . $output->messageId . '"/>';
	$mstr .= '<input type="hidden" name="headerid" value="' . $output->headerId . '"/>';
	$mstr .= '<input type="hidden" name="date" value="' . $output->date . '"/>';
	$mstr .= '</div>';
	
	die( 'ok<!--separate-->' . $mstr );
}

die( 'fail' );

?>
