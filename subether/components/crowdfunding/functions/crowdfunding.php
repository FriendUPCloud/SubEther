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

$str = ''; $edt = ''; $edit = false;

$currency = array( 'NOK' => 'Kr', 'USD' => '$', 'EUR' => '€', 'BTC' => 'BTC' );

if ( isset( $parent->access->IsAdmin ) )
{
	$edt .= '<div class="product admin closed" onclick="EditFundraiser(ge(\'ProductInfo\'),false,event)">';
	
	/*$edt .= '<div id="ProductImages" class="image"><div id="ProductMainImage">';
	
	$edt .= '<div class="upload_btn" onclick="ge(\'FilesUploadBtn\').click()"><div>';
	$edt .= '<form method="post" target="fileIframe" name="FilesUpload" enctype="multipart/form-data" action="' . $parent->route . '?component=library&action=uploadfile">';
	$edt .= '<input type="file" class="file_upload_btn" id="FilesUploadBtn" name="crowdfunding" onchange="fileselect( this, \'FilesUpload\' )"/>';
	$edt .= '</form>';
	$edt .= '<script>setOpacity ( ge(\'FilesUploadBtn\' ), 0 );</script>';
	$edt .= '</div></div>';
	
	$edt .= '</div></div>';*/
	
	$edt .= '<div id="ProductInfo" class="information"></div>';
	$edt .= '</div>';
}

if( !$edit )
{
	$str .= $edt;
}

if ( $rows = $database->fetchObjectRows( $q = '
	SELECT 
		* 
	FROM 
		SBookCrowdfunding 
	WHERE 
		' . ( isset( $parent->folder->CategoryID ) ? ( '`CategoryID` = \'' . $parent->folder->CategoryID . '\' AND ' ) : '' ) . '
		`IsDeleted` = "0" 
	ORDER BY 
		ID DESC 
' ) )
{
	$img = array(); $ii = 1;
	
	foreach( $rows as $p )
	{
		if( $p->Images && strstr( $p->Images, ',' ) )
		{
			foreach( explode( ',', $p->Images ) as $im )
			{
				if( $im && !isset( $img[$im] ) )
				{
					$img[$im] = $im;
				}
			}
		}
		else if( $p->Images && !isset( $img[$p->Images] ) )
		{
			$img[$p->Images] = $p->Images;
		}
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
	
	$ii = 3;
	
	foreach( $rows as $row )
	{
		if( !$row->Status && ( !isset( $parent->access->IsAdmin ) || $row->UserID != $webuser->ContactID ) ) continue;
		
		$onclick = ( isset( $parent->access->IsAdmin ) && $row->UserID == $webuser->ContactID ? 'EditFundraiser(ge(\'ProductInfo_' . $row->ID . '\'),\'' . $row->ID . '\',event)' : '' );

		$row->Images = ( strstr( $row->Images, ',' ) ? explode( ',', $row->Images ) : array( $row->Images ) );

		$str .= '<div class="product' . ( ' nr' . $ii++ ) . '" onclick="document.location=\'' . $parent->route . '?p=' . $row->ID . '\';return false;">';

		if( $onclick )
		{
			$str .= '<div class="edit_btn_product" onclick="' . $onclick . ';return false;"></div>';
		}

		$str .= '<div id="ProductImages_' . $row->ID . '" class="image">';

		if( isset( $row->Images[0] ) && isset( $img[$row->Images[0]]->ImgUrl ) )
		{
			$str .= '<div id="ProductMainImage_' . $row->ID . '">';
	
			$str .= '<div id="ProductMainImg_' . $img[$row->Images[0]]->ID . '" class="thumb">';
	
			$str .= '<div style="background-image:url(\'' . $img[$row->Images[0]]->ImgUrl . '\');">';
	
			if ( ( isset( $parent->access->IsAdmin ) && $row->UserID == $webuser->ContactID ) || 1==1 )
			{
				$str .= '<div class="upload_btn thumbs" onclick="ge(\'FilesUploadBtn_' . $row->ID . $img[$row->Images[0]]->ID . '\').click();"><div>';
				$str .= '<form method="post" target="fileIframe" name="FilesUpload_' . $row->ID . $img[$row->Images[0]]->ID . '" enctype="multipart/form-data" action="' . $parent->route . '?component=library&action=uploadfile">';
				$str .= '<input type="file" class="file_upload_btn" id="FilesUploadBtn_' . $row->ID . $img[$row->Images[0]]->ID . '" name="crowdfunding" onchange="fileselect( this, \'FilesUpload_' . $row->ID . $img[$row->Images[0]]->ID . '\' )"/>';
				$str .= '<input type="hidden" name="fundraiserid" value="' . $row->ID . '">';
				$str .= '<input type="hidden" name="fileid" value="' . $img[$row->Images[0]]->ID . '">';
				$str .= '</form>';
				$str .= '<script>setOpacity ( ge(\'FilesUploadBtn_' . $row->ID . $img[$row->Images[0]]->ID . '\' ), 0 );</script>';
				$str .= '</div></div>';
			}
	
			$str .= '<img style="width:' . $img[$row->Images[0]]->Width . 'px;" src="' . $img[$row->Images[0]]->ImgUrl . '">';
	
			$str .= '</div>';
	
			$str .= '</div>';
	
			$str .= '</div>';
		}

		$str .= '</div>';
		
		$str .= '<div id="ProductInfo_' . $row->ID . '" class="information">';
		
		$funded = 0; $donated = 0; $supporters = 0;
		
		if ( $sum = $database->fetchObjectRow( '
			SELECT 
				SUM( d.Donation ) AS Donated 
			FROM 
				SBookDonations d 
			WHERE 
					d.Component = "crowdfunding" 
				AND d.ComponentID = \'' . $row->ID . '\' 
				AND d.Currency = \'' . $row->Currency . '\' 
				AND d.IsDeleted = "0" 
			ORDER BY 
				d.ID DESC 
		' ) )
		{
			$donated = $sum->Donated;
			
			$funded = round( $donated / $row->Goal * 100 );
		}
		
		if ( $count = $database->fetchObjectRow( '
			SELECT 
				COUNT( d.ID ) AS Supporters 
			FROM 
				SBookDonations d 
			WHERE 
					d.Component = "crowdfunding" 
				AND d.ComponentID = \'' . $row->ID . '\' 
				AND d.IsDeleted = "0" 
			ORDER BY 
				d.ID DESC 
		' ) )
		{
			$supporters = $count->Supporters;
		}
		
		$str .= '<div class="inputs">';
		$str .= '<div class="heading"><strong>' . $row->Name . '</strong></div>';
		$str .= '<div class="info">' . $row->Info . '</div>';

		$str .= '<div class="rating">';
		$str .= '<div class="ratebox" title="' . $funded . '%"><div style="width: ' . ( $funded > 100 ? '100' : $funded ) . '%;"></div></div>';
		$str .= '</div>';

		$str .= '<div class="funding">';
		
		if( $donated )
		{
			//$str .= '<div class="donors"><strong>€19,148</strong> <span>donated</span></div>';
			$str .= '<div class="donors"><strong>'.(isset($currency[$row->Currency])?$currency[$row->Currency]:$row->Currency).' '.$donated.'</strong> <span>donated</span></div>';
		}
		if( $supporters )
		{
			$str .= '<div class="supporters"><strong>' . $supporters . '</strong> <span>backers</span></div>';
		}
		if( $row->DateEnd && $row->Status == 1 && ( date( 'd', strtotime( $row->DateEnd ) ) - date( 'd' ) ) > 0 )
		{
			$str .= '<div class="days"><strong>' . ( date( 'd', strtotime( $row->DateEnd ) ) - date( 'd' ) ) . '</strong> <span>days left</span></div>';
		}
	
		$str .= '</div>';
		$str .= '</div>';

		$str .= '</div>';

		$str .= '</div>';
	}
}



$str .= '<div class="clearboth" style="clear:both"></div>';

if( isset( $_REQUEST['function'] ) )
{
	die( 'ok<!--separate-->' . $str );
}

?>
