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

$title_limit = array( 'Big'=>100, 'Medium'=>100, 'Small'=>35 );
$leadin_limit = array( 'Big'=>780, 'Medium'=>400, 'Small'=>190 );

if( $archive = $database->fetchObjectRows ( '
	SELECT 
		n.*, c.Username
	FROM 
		SBookNews n, 
		SBookContact c 
	WHERE 
			n.CategoryID = \'' . $folder->CategoryID . '\' 
		AND c.UserID = n.PostedID 
		AND n.Tags != "Current" 
	ORDER BY 
		n.SortOrder ASC, n.DateAdded DESC  
' ) )
{
	$arc = array();
	foreach( $archive as $a )
	{
		$astr = '';
		$img = new dbImage ();
		$astr .= '<div class="NewsBox Archive">';
		if( $a->MediaID > 0 && $img->Load( $a->MediaID ) )
		{
			$astr .= '<div class="image">' . $img->getImageHTML ( 780, $img->Height, 'framed', false, 0xffffff ) . '</div>';
		}
		else 
		{
			$astr .= '<div class="image"></div>';
		}
		$astr .= '<div><h3><a href="javascript:void(0)">' . dotTrim( $a->Title, $title_limit['Small'] ) . '</a></h3></div>';
		$astr .= '<div class="posted">';
		$astr .= '<span>' . date( 'Y-m-d', strtotime( $a->DateAdded ) ) . ' </span>';
		$astr .= '<span>' . $a->Username . ' </span>';
		$astr .= '</div>';
		$astr .= '<div><span>' . dotTrim( $a->Leadin, $leadin_limit['Small'] ) . '</span></div>';
		$astr .= '<div class="bottom">';
		$astr .= '<div class="rating">' . $a->Rating . '</div>';
		$astr .= '<div class="more"><a href="javascript:void(0)">Read in archive</a></div>';
		$astr .= '</div>';
		$astr .= '</div>';
		$arc[] = $astr;
	}
}

//$arc = '';

if( $arc ) $rules = array( 0=>'Big', 1=>'Medium', 3=>'Small' );
else $rules = array( 0=>'Big', 3=>'Small' );

// Fetch all news items by categoryid ------------------------------------------
if( $news = $database->fetchObjectRows ( '
	SELECT 
		n.*, c.Username
	FROM 
		SBookNews n, 
		SBookContact c 
	WHERE 
			n.CategoryID = \'' . $folder->CategoryID . '\' 
		AND c.UserID = n.PostedID 
		AND n.Tags != "Current" 
	ORDER BY 
		n.SortOrder ASC, n.DateAdded DESC 
' ) )
{
	$i = 0; $ii = 0; $nstr = ''; $check = array();
	foreach( $news as $n )
	{
		$img = new dbImage ();
		if( $rules[$ii] ) $rule = $rules[$ii]; else $rules[$ii] = $rule;
		
		if( isset( $_POST[ 'edit' ] ) && isset( $_REQUEST[ 'nid' ] ) && $_REQUEST[ 'nid' ] == $n->ID )
		{
			$rules[$ii] = 'Big';
			$nstr .= '<div id="NewsID_' . $n->ID . '" class="NewsBox Big Active">';
			$nstr .= '<div class="post">';
			if( $n->MediaID > 0 && $img->Load( $n->MediaID ) )
			{
				$nstr .= '<div class="image">' . $img->getImageHTML ( 780, $img->Height, 'framed', false, 0xffffff );
				$nstr .= '<div class="edit_icons">';
				$nstr .= '<span onclick="deleteImage(' . $n->MediaID . ')" title="Delete Image"><img class="Icon" src="admin/gfx/icons/page_delete.png"></span>';
				$nstr .= '</div>';
				$nstr .= '</div>';
			}
			$nstr .= '<div class="header"><strong>Title</strong></div>';
			$nstr .= '<input type="text" id="Title_' . $n->ID . '" value="' . $n->Title . '">';
			$nstr .= '<div class="header"><strong>Leadin</strong></div>';
			$nstr .= '<textarea id="Leadin_' . $n->ID . '">' . $n->Leadin . '</textarea>';
			$nstr .= '<div class="header"><strong>Article</strong></div>';
			$nstr .= '<textarea id="Article_' . $n->ID . '">' . $n->Article . '</textarea>';
			$nstr .= '<div class="publish">';
			$nstr .= '<div class="bottom">';
			$nstr .= '<button type="button" onclick="publishNews(' . $n->ID . ')">Save</button> ';
			$nstr .= '<button type="button" onclick="refreshNews()">Close</button> ';
			$nstr .= '</div>';
			
			// Generate select list
			$nstr .= '<select id="Status_' . $n->ID . '">';
			foreach( array( 'IsPublished'=>'Published', 'NotPublished'=>'UnPublished' ) as $k=>$v )
			{
				if( $n->IsPublished > 0 && $k == 'IsPublished' )
				{
					$s = 'selected="selected"';
				}
				else if( $n->IsPublished == 0 && $k == 'NotPublished' )
				{
					$s = 'selected="selected"';
				}
				else $s = '';
				$nstr .= '<option value="' . $k . '" ' . $s . '>' . $v . '</option>';
			}
			$nstr .= '</select>';
			
			$nstr .= '<div class="upload_btn">';
			$nstr .= '<form method="post" target="fileIframe" name="NewsUpload_' . $n->ID . '" enctype="multipart/form-data" action="' . $url . '?action=uploadfile&nid=' . $n->ID . '">';
			$nstr .= '<input type="file" class="news_upload_btn" id="NewsUploadBtn_' . $n->ID . '" name="news" onchange="document.NewsUpload_' . $n->ID . '.submit();">';
			$nstr .= '</form>';
			$nstr .= '<script> setOpacity ( ge(\'NewsUploadBtn_' . $n->ID . '\' ), 0 ); </script>';
			$nstr .= '</div>';
			$nstr .= '</div>';
			$nstr .= '</div>';
			$nstr .= '</div>';
		}
		else if( isset( $_REQUEST[ 'nid' ] ) && $_REQUEST[ 'nid' ] == $n->ID )
		{
			$rules[$ii] = 'Big';
			$nstr .= '<div id="NewsID_' . $n->ID . '" class="NewsBox Big Active' . ( $n->IsPublished == 0 ? ' NotPublished' : '' ) . '">';
			$nstr .= '<div class="edit_icons">';
			$nstr .= '<span onclick="refreshNews(\'' . $n->ID . '\', \'edit\')" title="Edit News"><img class="Icon" src="admin/gfx/icons/page_edit.png"></span>';
			$nstr .= '<span onclick="deleteNews(' . $n->ID . ')" title="Delete News"><img class="Icon" src="admin/gfx/icons/page_delete.png"></span>';
			$nstr .= '<span onclick="refreshNews()" title="Close"><img class="Icon" src="admin/gfx/icons/cancel.png"></span>';
			$nstr .= '</div>';
			if( $n->MediaID > 0 && $img->Load( $n->MediaID ) )
			{
				$nstr .= '<div class="image">' . $img->getImageHTML ( 780, $img->Height, 'framed', false, 0xffffff ) . '</div>';
			}
			$nstr .= '<div><h1><a href="' . $parent->route . '?nid=' . $n->ID . '#NewsID_' . $n->ID . '">' . $n->Title . '</a></h1></div>';
			$nstr .= '<div class="posted">';
			$nstr .= '<span>' . date( 'Y-m-d', strtotime( $n->DateAdded ) ) . ' </span>';
			$nstr .= '<span>' . $n->Username . ( $n->IsPublished == 0 ? ', NotPublished' : '' ) . ' </span>';
			$nstr .= '</div>';
			$nstr .= '<div><span>' . nl2br( $n->Leadin ) . '</span></div>';
			$nstr .= '<div><span>' . nl2br( $n->Article ) . '</span></div>';
			$nstr .= '<div class="bottom">';
			$nstr .= '<div class="rating">' . $n->Rating . '</div>';
			$nstr .= '</div>';
			$nstr .= '</div>';
			$nstr .= '<div class="commentbox">';
			$nstr .= '<input placeholder="write your comment ..." id="CommentPost">';
			$nstr .= '<button type="button" onclick="">Comment</button>';
			$nstr .= '<div id="NewsComments"></div>';
			$nstr .= '</div>';
		}
		else
		{
			$nstr .= '<div id="NewsID_' . $n->ID . '" class="NewsBox ' . $rules[$ii] . ( $n->IsPublished == 0 ? ' NotPublished' : '' ) . '">';
			$nstr .= '<div class="edit_icons">';
			$nstr .= '<span onclick="refreshNews(\'' . $n->ID . '\', \'edit\')" title="Edit News"><img class="Icon" src="admin/gfx/icons/page_edit.png"></span>';
			$nstr .= '<span onclick="deleteNews(' . $n->ID . ')" title="Delete News"><img class="Icon" src="admin/gfx/icons/page_delete.png"></span>';
			$nstr .= '</div>';
			if( $n->MediaID > 0 && $img->Load( $n->MediaID ) )
			{
				$nstr .= '<div class="image">' . $img->getImageHTML ( 780, $img->Height, 'framed', false, 0xffffff ) . '</div>';
			}
			else if( $rules[$ii] != 'Big' )
			{
				$nstr .= '<div class="image"></div>';
			}
			if( $rules[$ii] == 'Small' ) $nstr .= '<div><h3><a href="' . $parent->route . '?nid=' . $n->ID . '#NewsID_' . $n->ID . '">' . dotTrim( $n->Title, $title_limit[$rules[$ii]] ) . '</a></h3></div>';
			else if( $rules[$ii] == 'Medium' ) $nstr .= '<div><h2><a href="' . $parent->route . '?nid=' . $n->ID . '#NewsID_' . $n->ID . '">' . dotTrim( $n->Title, $title_limit[$rules[$ii]] ) . '</a></h2></div>';
			else $nstr .= '<div><h1><a href="' . $parent->route . '?nid=' . $n->ID . '#NewsID_' . $n->ID . '">' . dotTrim( $n->Title, $title_limit[$rules[$ii]] ) . '</a></h1></div>';
			$nstr .= '<div class="posted">';
			$nstr .= '<span>' . date( 'Y-m-d', strtotime( $n->DateAdded ) ) . ' </span>';
			$nstr .= '<span>' . $n->Username . ( $n->IsPublished == 0 ? ', NotPublished' : '' ) . ' </span>';
			$nstr .= '</div>';
			$nstr .= '<div><span>' . dotTrim( $n->Leadin, $leadin_limit[$rules[$ii]] ) . '</span></div>';
			$nstr .= '<div class="bottom">';
			$nstr .= '<div class="rating">' . $n->Rating . '</div>';
			$nstr .= '<div class="more"><a href="' . $parent->route . '?nid=' . $n->ID . '#NewsID_' . $n->ID . '">Read more</a></div>';
			$nstr .= '</div>';
			$nstr .= '</div>';
		}
		
		if( $arc && ( $rules[$ii] == 'Medium' || $rules[$ii-1] == 'Small' && $rules[$ii] == 'Small' ) )
		{
			$nstr .= $arc[$i];
			$rules[$ii] = 'Archive';
			$i++;
		}
		else if( $arc && $rules[$ii-1] == 'Archive' && $rules[$ii] == 'Small' && !$news[$ii+1] )
		{
			$nstr .= '<div class="EmptyBox Small"></div>';
			$nstr .= $arc[$i];
			$rules[$ii] = 'Archive';
			$i++;
		}
		$ii++;
	}
	$nstr .= '<div class="clearboth"></div>';
}

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
$estr .= '<div class="code_btn"></div>';
$estr .= '</div>';
$estr .= '</div>';

if( isset( $_POST[ 'nid' ] ) ) die( 'ok<!--separate-->' . $nstr . '<!--separate-->' . $estr );

?>
