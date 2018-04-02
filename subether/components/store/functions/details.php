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

$str = ''; $edt = '';

if ( $_REQUEST['p'] && ( $row = $database->fetchObjectRow( $q = '
	SELECT 
		* 
	FROM 
		SBookProducts 
	WHERE 
			`ID` = \'' . $_REQUEST['p'] . '\' 
		AND `IsDeleted` = "0" 
	ORDER BY 
		ID DESC 
' ) ) )
{
	$img = array(); $ii = 1; $edit = false;
	
	$mimg = ''; $timg = '';
	
	if( $row->Images && strstr( $row->Images, ',' ) )
	{
		foreach( explode( ',', $row->Images ) as $im )
		{
			if( $im && !isset( $img[$im] ) )
			{
				$img[$im] = $im;
			}
		}
	}
	else if( $row->Images && !isset( $img[$row->Images] ) )
	{
		$img[$row->Images] = $row->Images;
	}
	
	if( $img && $im = $database->fetchObjectRows( $iq = '
		SELECT
			f.DiskPath, i.Filename, i.ID, i.Width, i.Height 
		FROM
			Folder f, Image i
		WHERE
			i.ID IN (' . implode( ',', $img ) . ') AND f.ID = i.ImageFolder
		ORDER BY
			ID ASC
	' ) )
	{
		$img = array();
		
		foreach( $im as $i )
		{
			$obj = new stdClass();
			$obj->ID = $i->ID;
			$obj->Width = $i->Width;
			$obj->Height = $i->Height;
			$obj->Filename = $i->Filename;
			$obj->DiskPath = ( $i->DiskPath != '' ? $i->DiskPath : ( BASE_URL . 'upload/images-master/' ) );
			$obj->ImgUrl = ( $obj->DiskPath && $obj->Filename ? ( $obj->DiskPath . $obj->Filename ) : false );
			
			$img[$i->ID] = $obj;
		}
	}
	
	$row->Images = ( strstr( $row->Images, ',' ) ? explode( ',', $row->Images ) : array( $row->Images ) );
	
	$str .= '<div class="product details">';
	
	$str .= '<div class="topbox">';
	//$str .= '<div class="heading"><h3>' . $row->Name . '</h3></div>';
	//$str .= '<div class="info">' . $row->Info . '</div>';
	$str .= '</div>';
	
	$str .= '<div class="media">';
	
	$str .= '<div id="ProductImages_' . $row->ID . '" class="image">';
	
	if( $row->Images && is_array( $row->Images ) )
	{
		foreach( $row->Images as $im )
		{
			if( isset( $img[$im]->ImgUrl ) )
			{				
				if( $ii == 1 )
				{
					$mimg .= '<div id="ProductMainImg_' . $img[$im]->ID . '" class="thumb">';
					$mimg .= '<div style="background-image:url(\'' . $img[$im]->ImgUrl . '\');">';
					
					if ( isset( $parent->access->IsAdmin ) )
					{
						$mimg .= '<div class="upload_btn thumbs" onclick="ge(\'FilesUploadBtn_' . $row->ID . $img[$im]->ID . '\').click();"><div>';
						$mimg .= '<form method="post" target="fileIframe" name="FilesUpload_' . $row->ID . $img[$im]->ID . '" enctype="multipart/form-data" action="' . $parent->route . '?component=library&action=uploadfile">';
						$mimg .= '<input type="file" class="file_upload_btn" id="FilesUploadBtn_' . $row->ID . $img[$im]->ID . '" name="store" onchange="fileselect( this, \'FilesUpload_' . $row->ID . $img[$im]->ID . '\' )"/>';
						$mimg .= '<input type="hidden" name="productid" value="' . $row->ID . '">';
						$mimg .= '<input type="hidden" name="fileid" value="' . $img[$im]->ID . '">';
						$mimg .= '</form>';
						$mimg .= '<script>setOpacity ( ge(\'FilesUploadBtn_' . $row->ID . $img[$im]->ID . '\' ), 0 );</script>';
						$mimg .= '</div></div>';
					}
					
					$mimg .= '<img style="width:' . $img[$im]->Width . 'px;" src="' . $img[$im]->ImgUrl . '">';
					
					$mimg .= '</div></div>';
				}
				
				$timg .= '<div id="ProductThumbImg_' . $img[$im]->ID . '" class="thumb">';
				$timg .= '<div style="background-image:url(\'' . $img[$im]->ImgUrl . '\');" onclick="SwitchProductImage(\'' . $img[$im]->ID . '\',\'' . $row->ID . '\')">';
				$timg .= '<img style="width:' . $img[$im]->Width . 'px;" src="' . $img[$im]->ImgUrl . '">';
				$timg .= '</div></div>';
				
				$ii++;
			}
		}
	}
	
	if ( isset( $parent->access->IsAdmin ) )
	{
		$edt .= '<div id="ProductThumbEdit_' . $row->ID . '" class="edit_btn thumb"><div onclick="ge(\'FilesUploadBtn_' . $row->ID . '\').click();">';
		$edt .= '<form method="post" target="fileIframe" name="FilesUpload_' . $row->ID . '" enctype="multipart/form-data" action="' . $parent->route . '?component=library&action=uploadfile">';
		$edt .= '<input type="file" class="file_upload_btn" id="FilesUploadBtn_' . $row->ID . '" name="store" onchange="fileselect( this, \'FilesUpload_' . $row->ID . '\' )"/>';
		$edt .= '<input type="hidden" name="productid" value="' . $row->ID . '">';
		$edt .= '</form>';
		$edt .= '<script>setOpacity ( ge(\'FilesUploadBtn_' . $row->ID . '\' ), 0 );</script>';
		$edt .= '</div></div>';
	}
	
	$str .= '<div class="mainimage"><div id="ProductMainImage_' . $row->ID . '">' . $mimg . '</div></div>';
	$str .= '<div class="thumbs"><div id="ProductThumbImage_' . $row->ID . '">';
	$str .= $timg . $edt;
	$str .= '</div></div>';
	
	$str .= '<div class="clearboth" style="clear:both"></div>';
	
	$str .= '</div>';
	
	$str .= '</div>';
	
	$str .= '<div id="ProductInfo_' . $row->ID . '" class="information">';
	
	$str .= '<div class="inputs">';
	$str .= '<div class="heading"><h3>' . $row->Name . '</h3></div>';
	
	$str .= '<div class="rating">';
	$str .= '<div class="ratebox"><div style="width: 0%;"></div></div>';
	$str .= '<span onmouseout="tooltips(this,\'close\')" onmouseover="tooltips(this,\'open\')" class="amount"></span>';
	$str .= '<div class="tooltips"><div class="inner"><ul>';
	
	$str .= '</ul></div></div></div>';
	$str .= '</div>';
	
	$str .= '<div class="artnr">Artnr: ' . $row->ID . '</div>';
	$str .= '<div class="instock">' . ( $row->InStock > 0 ? ( 'Instock: ' . $row->InStock ) : 'Out of stock' ) . '</div>';
	$str .= '<div class="info">' . $row->Info . '</div>';
	$str .= '<div class="price">' . $row->Price . '</div>';
	$str .= '<div class="button"><button onclick="AddToCart(' . $row->ID . ')">Add to Cart</button></div>';
	$str .= '</div>';
	
	$str .= '</div>';
	
	$str .= '<div class="clearboth" style="clear:both"></div>';
	
	$str .= '</div>';
	//$parent->cuser = false;
	//$parent->webuser = false;
	//die( print_r( $parent,1 ) . ' --' );
	// Testing ....
	//$str .= '<div id="Content">' . IncludeComponent( 'wall', $parent, true ) . '</div>';
}

?>
