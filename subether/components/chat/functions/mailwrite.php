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

$wstr = ''; $fwre = '';

if( isset( $_REQUEST['reply'] ) )
{
	$fwre  = '<br><br>';
	$fwre .= 'From: ' . $_POST['from'] . '<br>';
	$fwre .= 'Sent: ' . $_POST['date'] . '<br>';
	$fwre .= 'To: ' . $_POST['to'] . '<br>';
	$fwre .= 'Subject: ' . $_POST['subject'] . '<br>';
	$fwre .= '<br>';
}

if( isset( $_REQUEST['forward'] ) )
{
	$fwre  = '<br><br>';
	$fwre .= '-------- Original Message --------<br>';
	$fwre .= 'From: ' . $_POST['from'] . '<br>';
	$fwre .= 'Sent: ' . $_POST['date'] . '<br>';
	$fwre .= 'To: ' . $_POST['to'] . '<br>';
	$fwre .= 'Subject: ' . $_POST['subject'] . '<br>';
	$fwre .= '<br>';
}

if( $_POST['from'] )
{
	$_POST['from'] = html_entity_decode( $_POST['from'] );
	
	if( strstr( $_POST['from'], '<' ) && strstr( $_POST['from'], '>' ) )
	{
		$_POST['from'] = end( explode( '<', $_POST['from'] ) );
		$_POST['from'] = trim( reset( explode( '>', $_POST['from'] ) ) );
	}
}

if( $_POST['replyto'] )
{
	$_POST['replyto'] = html_entity_decode( $_POST['replyto'] );
	
	if( strstr( $_POST['replyto'], '<' ) && strstr( $_POST['replyto'], '>' ) )
	{
		$_POST['replyto'] = end( explode( '<', $_POST['replyto'] ) );
		$_POST['replyto'] = trim( reset( explode( '>', $_POST['replyto'] ) ) );
	}
}

if( $acc = $database->fetchObjectRow( '
	SELECT
		*
	FROM
		SBookMailAccounts
	WHERE
			UserID = \'' . $webuser->ID . '\'
		AND ID =\'' . $_POST['accid'] . '\'
		OR 	Address = \'' . $_POST['address'] . '\' 
	ORDER BY 
		ID ASC 
' ) )
{
	$fstr = '';
	
	if( $header = $database->fetchObjectRow( $q = '
		SELECT 
			* 
		FROM 
			SBookMailHeaders 
		WHERE 
				UserID = \'' . $webuser->ID . '\' 
			AND ID = \'' . $_POST['headerid'] . '\' 
	' ) )
	{
		$files = false;
		
		$header->Files = json_obj_decode( $header->Files, 'array' );
		
		if( $header->Files && is_array( $header->Files ) )
		{
			$files = $header->Files;
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
			
			$fstr .= '<input type="hidden" name="files" value="' . implode( ',', $files ) . '"/>';
		}
	}
	
	$wstr .= '<div>';
	$wstr .= '<div class="headderbox">';
	$wstr .= '<table>';
	//$wstr .= '<tr class="from"><td class="leftcol">From:</td><td class="rightcol"><input type="text" name="from" value="' . $acc->Address . '"/></td></tr>';
	$wstr .= '<tr class="from"><td class="leftcol">From:</td><td class="rightcol"><select name="from"><option value="' . $acc->Address . '">' . $acc->Address . '</option></select></td></tr>';
	$wstr .= '<tr class="date"><td class="leftcol">Date:</td><td class="rightcol"><input type="text" name="date" value="' . date( 'd. M Y H:i' ) . '"/></td></tr>';
	$wstr .= '<tr class="to"><td class="leftcol">To:</td><td class="rightcol"><input type="text" name="to" value="' . ( !isset( $_REQUEST['forward'] ) ? $_POST['replyto'] : '' ) . '"/></td></tr>';
	$wstr .= '<tr class="subject"><td class="leftcol">Subject:</td><td class="rightcol"><input type="text" name="subject" value="' . ( isset( $_REQUEST['reply'] ) ? 'Re: ' : '' ) . ( isset( $_REQUEST['forward'] ) ? 'Fwd: ' : '' ) . $_POST['subject'] . '"/></td></tr>';
	$wstr .= '</table>';
	$wstr .= '</div>';
	$wstr .= '<div class="headderfiles">';
	$wstr .= '<div class="upload_btn">';
	$wstr .= '<ul><li onclick="ge(\'FilesUploadBtn\').click()">[+] Add Files</li></ul>';
	$wstr .= '</div>';
	$wstr .= '<div id="Hfiles" class="hfiles_inner">';
	if( isset( $_REQUEST['forward'] ) && $fstr )
	{
		$wstr .= $fstr;
	}
	$wstr .= '</div>';
	$wstr .= '</div>';
	$wstr .= '<div class="clearboth" style="clear:both"></div>';
	$wstr .= '<div class="message"><div class="textarea" contenteditable="true" name="message" var="message">' . ( $acc->Signature ? ( '<br>' . nl2br( $acc->Signature ) ) : '' ) . $fwre . $_POST['message'] . '</div></div>';
	$wstr .= '</div>';
	
	$wstr .= '<form method="post" target="fileIframe" name="FilesUpload" enctype="multipart/form-data" action="' . $parent->route . '?component=library&action=uploadfile" style="overflow:hidden;height:0;height:0;">';
	$wstr .= '<label class="mobileuploadfile"> <span> <input type="file" multiple="" onchange="fileselect( this, \'FilesUpload\' )" name="mail" id="FilesUploadBtn" class="file_upload_btn" style="opacity: 0;"> </span> </label>';
	//$wstr .= '<input type="hidden" id="MessageID" name="messageid" value="' . $_POST['mid'] . '">';
	$wstr .= '</form>';
	
	die( 'ok<!--separate-->' . $wstr );
}

die( 'fail' );

?>
