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

if( $_POST['fid'] > 0 && ( $image = $database->fetchObjectRow( '
	SELECT 
		im.*, 
		im.Access AS FileAccess, 
		fl.DiskPath AS FolderPath, 
		fl.Access AS FolderAccess 
	FROM 
		Image im, 
		Folder fl 
	WHERE 
			im.ImageFolder = fl.ID 
		AND im.ID = \'' . $_POST['fid'] . '\' 
', false, 'components/crowdfunding/functions/media.php' ) ) )
{
	$str = ''; $style = '';
	
	$fullPath = BASE_URL . $image->FolderPath . $image->Filename;
	
	if( $image->Width && $image->Height )
	{
		$style = 'width:' . $image->Width . 'px;';
	}
	
	if( $image->Filename )
	{
		$onclick = false; $id = 'ProductThumbImg_' . $image->ID;
		
		if( isset( $_REQUEST['getmain'] ) )
		{
			$id = 'ProductMainImg_' . $image->ID;
		}
		else if( $_POST['pid'] )
		{
			$onclick = 'SwitchFundraiserImage(\'' . $_POST['fid'] . '\',\'' . $_POST['pid'] . '\')';
		}
		
		if( !isset( $_POST['cid'] ) )
		{
			$str .= '<div' . ( $id ? ( ' id="' . $id . '"' ) : '' ) . ' class="thumb">';
		}
		
		$str .= '<div style="background-image:url(\'' . $fullPath . '\');"' . ( $onclick ? ( ' onclick="' . $onclick . '"' ) : '' ) . '>';
		
		if ( $id && isset( $parent->access->IsAdmin ) )
		{
			$str .= '<div class="upload_btn thumbs" onclick="ge(\'FilesUploadBtn_' . $_POST['pid'] . $image->ID . '\').click();"><div>';
			$str .= '<form method="post" target="fileIframe" name="FilesUpload_' . $_POST['pid'] . $image->ID . '" enctype="multipart/form-data" action="' . $parent->route . '?component=library&action=uploadfile">';
			$str .= '<input type="file" class="file_upload_btn" id="FilesUploadBtn_' . $_POST['pid'] . $image->ID . '" name="crowdfunding" onchange="fileselect( this, \'FilesUpload_' . $_POST['pid'] . $image->ID . '\' )"/>';
			
			if( $_POST['pid'] )
			{
				$str .= '<input type="hidden" name="fundraiserid" value="' . $_POST['pid'] . '">';
			}
			
			$str .= '<input type="hidden" name="fileid" value="' . $image->ID . '">';
			$str .= '</form>';
			$str .= '<script>setOpacity ( ge(\'FilesUploadBtn_' . $_POST['pid'] . $image->ID . '\' ), 0 );</script>';
			$str .= '</div></div>';
		}
		
		$str .= '<img src="' . $fullPath . '" style="' . $style . '"/>';
		$str .= '</div>';
		
		if( !isset( $_POST['cid'] ) )
		{
			$str .= '</div>';
		}
		
		die( 'ok<!--separate-->' . $str );
	}
}

die( 'fail' );

?>
