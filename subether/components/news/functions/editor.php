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

$cnews = $database->fetchObjectRow ( '
	SELECT 
		n.*, c.Username
	FROM 
		SBookNews n, 
		SBookContact c 
	WHERE 
			n.CategoryID = \'' . $folder->CategoryID . '\' 
		AND c.UserID = n.PostedID 
		AND n.Tags = "Current" 
	ORDER BY 
		n.ID DESC 
' );

$img = new dbImage ();
$estr = '<div class="post' . ( $cnews->MediaID > 0 ? ' Active' : '' ) . '">';
if( $cnews->MediaID > 0 && $img->Load( $cnews->MediaID ) )
{
	$estr .= '<div class="image">' . $img->getImageHTML ( 780, $img->Height, 'framed', false, 0xffffff );
	$estr .= '<div class="edit_icons">';
	$estr .= '<span onclick="deleteImage(' . $cnews->MediaID . ')" title="Delete Image"><img class="Icon" src="admin/gfx/icons/page_delete.png"></span>';
	$estr .= '</div>';
	$estr .= '</div>';
}
$estr .= '<div class="header"><strong>Title</strong></div>';
$estr .= '<input type="text" id="Title" value="' . $cnews->Title . '" onclick="openNewsEditor()">';
$estr .= '<div class="header"><strong>Leadin</strong></div>';
$estr .= '<textarea id="Leadin">' . $cnews->Leadin . '</textarea>';
$estr .= '<div class="header"><strong>Article</strong></div>';
$estr .= '<textarea id="Article">' . $cnews->Article . '</textarea>';
$estr .= '<div class="publish">';
$estr .= '<button type="button" onclick="publishNews(' . $cnews->ID . ')">PUBLISH</button> ';
$estr .= '<select id="Status">';
foreach( array( 'IsPublished'=>'Published', 'NotPublished'=>'UnPublished' ) as $k=>$v )
{
	if( $cnews->IsPublished > 0 && $k == 'IsPublished' )
	{
		$s = 'selected="selected"';
	}
	else if( $cnews->IsPublished == 0 && $k == 'NotPublished' )
	{
		$s = 'selected="selected"';
	}
	else $s = '';
	$estr .= '<option value="' . $k . '" ' . $s . '>' . $v . '</option>';
}
$estr .= '</select>';
$estr .= '<div class="upload_btn">';
$estr .= '<form method="post" target="fileIframe" name="NewsUpload" enctype="multipart/form-data" action="' . $url . '?action=uploadfile">';
$estr .= '<input type="file" class="news_upload_btn" id="NewsUploadBtn" name="news" onchange="document.NewsUpload.submit();publishNews(\'Current\')">';
$estr .= '</form>';
$estr .= '<script> setOpacity ( ge(\'NewsUploadBtn\' ), 0 ); </script>';
$estr .= '</div>';
$estr .= '</div>';
$estr .= '</div>';

if( isset( $_POST[ 'nid' ] ) ) die( 'ok<!--separate-->' . $nstr . '<!--separate-->' . $estr );

?>
