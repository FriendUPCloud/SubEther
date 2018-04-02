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

$str = ''; $edt = ''; $editmode = ( isset( $_REQUEST['edit'] ) ? true : false );

$img = array(); $ii = 1; $edit = false; 

$row = new stdClass();

$mimg = ''; $timg = '';

$currency = array( 'NOK' => 'Kr', 'USD' => '$', 'EUR' => '€', 'BTC' => 'BTC' );

if ( $_REQUEST['p'] && ( $row = $database->fetchObjectRow( $q = '
	SELECT 
		* 
	FROM 
		SBookCrowdfunding 
	WHERE 
			`ID` = \'' . $_REQUEST['p'] . '\' 
		AND `IsDeleted` = "0" 
	ORDER BY 
		ID DESC 
' ) ) )
{
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
}



$str .= '<div class="product details">';

$str .= '<div class="topbox">';
$str .= '</div>';

$str .= '<div class="media">';

$str .= '<div id="ProductImages'.(isset($row->ID)?'_'.$row->ID:'').'" class="image">';

if( isset( $row->Images ) && $row->Images && is_array( $row->Images ) )
{
	foreach( $row->Images as $im )
	{
		if( isset( $img[$im]->ImgUrl ) )
		{				
			if( $ii == 1 )
			{
				$mimg .= '<div id="ProductMainImg_' . $img[$im]->ID . '" class="thumb">';
				$mimg .= '<div style="background-image:url(\'' . $img[$im]->ImgUrl . '\');">';
				
				if ( isset( $parent->access->IsAdmin ) && ( $row->UserID == $webuser->ContactID || !isset( $row->ID ) ) )
				{
					$mimg .= '<div class="upload_btn thumbs" onclick="ge(\'FilesUploadBtn_' . $row->ID . $img[$im]->ID . '\').click();"><div>';
					$mimg .= '<form method="post" target="fileIframe" name="FilesUpload_' . $row->ID . $img[$im]->ID . '" enctype="multipart/form-data" action="' . $parent->route . '?component=library&action=uploadfile">';
					$mimg .= '<input style="visibility:hidden;width:0;height:0;overflow:hidden;opacity:0;" type="file" class="file_upload_btn" id="FilesUploadBtn_' . $row->ID . $img[$im]->ID . '" name="crowdfunding" onchange="fileselect( this, \'FilesUpload_' . $row->ID . $img[$im]->ID . '\' )"/>';
					$mimg .= '<input type="hidden" name="fundraiserid" value="' . $row->ID . '">';
					$mimg .= '<input type="hidden" name="fileid" value="' . $img[$im]->ID . '">';
					$mimg .= '</form>';
					$mimg .= '<script>setOpacity ( ge(\'FilesUploadBtn_' . $row->ID . $img[$im]->ID . '\' ), 0 );</script>';
					$mimg .= '</div></div>';
				}
				
				$mimg .= '<img style="width:' . $img[$im]->Width . 'px;" src="' . $img[$im]->ImgUrl . '">';
				
				$mimg .= '</div></div>';
			}
			
			$timg .= '<div id="ProductThumbImg_' . $img[$im]->ID . '" class="thumb">';
			$timg .= '<div style="background-image:url(\'' . $img[$im]->ImgUrl . '\');" onclick="SwitchFundraiserImage(\'' . $img[$im]->ID . '\',\'' . $row->ID . '\')">';
			$timg .= '<img style="width:' . $img[$im]->Width . 'px;" src="' . $img[$im]->ImgUrl . '">';
			$timg .= '</div></div>';
			
			$ii++;
		}
	}
}

if( !$mimg )
{
	$mimg .= '<div id="ProductMainImg'.(isset($row->ID)?'_'.$row->ID:'').'" class="thumb">';
	$mimg .= '<div>';
	
	if ( isset( $parent->access->IsAdmin ) && ( $row->UserID == $webuser->ContactID || !isset( $row->ID ) ) )
	{
		$mimg .= '<div class="upload_btn thumbs" onclick="ge(\'FilesUploadBtn'.(isset($row->ID)?'_'.$row->ID:'').'\').click();"><div>';
		$mimg .= '<form method="post" target="fileIframe" name="FilesUpload'.(isset($row->ID)?'_'.$row->ID:'').'" enctype="multipart/form-data" action="' . $parent->route . '?component=library&action=uploadfile">';
		$mimg .= '<input style="visibility:hidden;width:0;height:0;overflow:hidden;opacity:0;" type="file" class="file_upload_btn" id="FilesUploadBtn'.(isset($row->ID)?'_'.$row->ID:'').'" name="crowdfunding" onchange="fileselect( this, \'FilesUpload'.(isset($row->ID)?'_'.$row->ID:'').'\' )"/>';
		
		if( isset( $row->ID ) )
		{
			$mimg .= '<input type="hidden" name="fundraiserid" value="' . $row->ID . '">';
		}
		
		$mimg .= '</form>';
		$mimg .= '<script>setOpacity ( ge(\'FilesUploadBtn'.(isset($row->ID)?'_'.$row->ID:'').'\' ), 0 );</script>';
		$mimg .= '</div></div>';
	}
	
	$mimg .= '<img>';
	
	$mimg .= '</div></div>';
} 
else if ( isset( $row->ID ) && isset( $parent->access->IsAdmin ) && ( $row->UserID == $webuser->ContactID || !isset( $row->ID ) ) )
{
	$edt .= '<div id="ProductThumbEdit_'.$row->ID.'" class="edit_btn thumb"><div onclick="ge(\'FilesUploadBtn_'.$row->ID.'\').click();">';
	$edt .= '<form method="post" target="fileIframe" name="FilesUpload_'.$row->ID.'" enctype="multipart/form-data" action="' . $parent->route . '?component=library&action=uploadfile">';
	$edt .= '<input style="visbility:hidden;width:0;height:0;overflow:hidden;opacity:0;" type="file" class="file_upload_btn" id="FilesUploadBtn_'.$row->ID.'" name="crowdfunding" onchange="fileselect( this, \'FilesUpload_'.$row->ID.'\' )"/>';
	$edt .= '<input type="hidden" name="fundraiserid" value="' . $row->ID . '">';
	$edt .= '</form>';
	$edt .= '<script>setOpacity ( ge(\'FilesUploadBtn_'.$row->ID.'\' ), 0 );</script>';
	$edt .= '</div></div>';
}

$str .= '<div class="mainimage"><div id="ProductMainImage'.(isset($row->ID)?'_'.$row->ID:'').'">' . $mimg . '</div></div>';
$str .= '<div class="thumbs"><div id="ProductThumbImage'.(isset($row->ID)?'_'.$row->ID:'').'">';
$str .= $timg . $edt;
$str .= '</div></div>';

$str .= '<div class="clearboth" style="clear:both"></div>';

$str .= '</div>';

$str .= '</div>';

$str .= '<div id="ProductInfo'.(isset($row->ID)?'_'.$row->ID:'').'" class="information">';

if( $editmode && isset( $parent->access->IsAdmin ) && ( $row->UserID == $webuser->ContactID || !isset( $row->ID ) ) )
{
	
	$str .= '<div class="inputs">';
	$str .= '<div class="name">';
	$str .= '<input id="FundraiserName'.(isset($row->ID)?'_'.$row->ID:'').'" type="text" placeholder="Name" value="'.(isset( $row->Name)?$row->Name:'').'"/>';
	$str .= '</div>';
	$str .= '<div class="info">';
	$str .= '<textarea id="FundraiserInfo'.(isset($row->ID)?'_'.$row->ID:'').'" type="text" placeholder="Info">'.(isset($row->Info)?$row->Info:'').'</textarea>';
	$str .= '</div>';
	$str .= '<div class="location">';
	$str .= '<input id="FundraiserLocation'.(isset($row->ID)?'_'.$row->ID:'').'" type="text" placeholder="Location" value="'.(isset($row->Location)?$row->Location:'').'"/>';
	$str .= '</div>';
	$str .= '<div class="tags">';
	$str .= '<input id="FundraiserTags'.(isset($row->ID)?'_'.$row->ID:'').'" type="text" placeholder="Tags" value="'.(isset($row->Tags)?$row->Tags:'').'"/>';
	$str .= '</div>';
	$str .= '<div class="goal">';
	$str .= '<input id="FundraiserGoal'.(isset($row->ID)?'_'.$row->ID:'').'" type="text" placeholder="Goal" value="'.(isset($row->Goal)?$row->Goal:'').'"/>';
	$str .= '</div>';
	$str .= '<div class="currency">';
	
	$str .= '<select id="FundraiserCurrency'.(isset($row->ID)?'_'.$row->ID:'').'">';
	
	foreach( array( 'NOK' => 'Norske Kroner', 'USD' => 'US Dollar', 'EUR' => 'Euro', 'BTC' => 'Bitcoin' ) as $k => $v )
	{
		$s = ( isset( $row->Currency ) && $row->Currency == $k ? ' selected="selected"' : '' );
		$str .= '<option value="' . $k . '"' . $s . '>' . $v . '</option>';
	}
	
	$str .= '</select>';
	
	$str .= '</div>';
	$str .= '<div class="end">';
	$str .= '<input id="FundraiserEnd'.(isset($row->ID)?'_'.$row->ID:'').'" type="text" placeholder="End" value="'.(isset($row->DateEnd)?$row->DateEnd:'').'"/>';
	$str .= '</div>';
	$str .= '<div class="status">';
	
	$str .= '<select id="FundraiserStatus'.(isset($row->ID)?'_'.$row->ID:'').'">';
	
	foreach( array( 0 => 'Draft', 1 => 'Active', 2 => 'Closed' ) as $k => $v )
	{
		$s = ( isset( $row->Status ) && $row->Status == $k ? ' selected="selected"' : '' );
		$str .= '<option value="' . $k . '"' . $s . '>' . $v . '</option>';
	}
	
	$str .= '</select>';
	
	$str .= '</div>';
	$str .= '</div>';
	
	
	
}
else
{
	
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
		
		if( $donated > 0 )
		{
			$funded = round( $donated / $row->Goal * 100 );
		}
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

	$str .= '<div class="heading"><h3>' . $row->Name . '</h3></div>';

	$str .= '<div class="rating">';
	$str .= '<div class="ratebox" title="' . $funded . '%"><div style="width: ' . ( $funded > 100 ? '100' : $funded ) . '%;"></div></div>';
	$str .= '</div>';
	


	$str .= '<div class="funding">';
	
	//$str .= '<div class="donors"><strong>€19,148</strong> <span>donated of €' . $row->Goal . ' goal</span></div>';
	$str .= '<div class="donors"><strong>'.(isset($currency[$row->Currency])?$currency[$row->Currency]:$row->Currency).' '. ($donated ? $donated : 0 ).'</strong>';
	$str .= ' <span>donated of '.(isset($currency[$row->Currency])?$currency[$row->Currency]:$row->Currency).' ' . $row->Goal . ' goal</span></div>';
	
	$str .= '<div class="supporters"><strong>' . ( $supporters ? $supporters : 0 ) . '</strong> <span>backers</span></div>';
	
	if( $row->DateEnd && $row->Status == 1 && ( date( 'd', strtotime( $row->DateEnd ) ) - date( 'd' ) ) > 0 )
	{
		$str .= '<div class="days"><strong>' . ( date( 'd', strtotime( $row->DateEnd ) ) - date( 'd' ) ) . '</strong> <span>days left</span></div>';
	}

	$str .= '</div>';

	$str .= '</div>';
	
	$str .= '<br>';
	
	$str .= '<div class="info">' . $row->Info . '</div>';
	
	$str .= '<br>';
	
	$str .= '<div class="donation button">';

	$str .= '<select id="DonationCurrency" name="currency">';

	foreach( $currency as $k => $v )
	{
		$s = ( isset( $row->Currency ) && $row->Currency == $k ? ' selected="selected"' : '' );
		if( $row->Currency != $k ) continue;
		$str .= '<option value="' . $k . '"' . $s . '>' . $v . '</option>';
	}

	$str .= '</select>';

	$str .= '<input id="DonationValue" name="donation" value="20"/>';
	
	$str .= '<button ' . ( $row->Status == 1 ? 'onclick="Donate(this,' . $row->ID . ')"' : 'disabled="disabled"' ) . '>Donate Now</button>';
	$str .= '</div>';
	
}

$str .= '</div>';


$str .= '</div>';

$str .= '<div class="clearboth" style="clear:both"></div>';

$str .= '<div id="ProductDetails'.(isset($row->ID)?'_'.$row->ID:'').'" class="detail">';

$str .= '<div class="maincontent">';

if( $editmode && isset( $parent->access->IsAdmin ) && ( $row->UserID == $webuser->ContactID || !isset( $row->ID ) ) )
{
	$str .= '<div class="editor">';
	
	//$str .= '<textarea id="FundraiserDetails'.(isset($row->ID)?'_'.$row->ID:'').'" class="textarea" style="width: 100%;min-height: 400px;">'.(isset($row->Details)?$row->Details:'').'</textarea>';
	
	$str .= '<div id="FundraiserDetails'.(isset($row->ID)?'_'.$row->ID:'').'" style="width: 100%;min-height: 400px;">'.(isset($row->Details)?$row->Details:'').'</div>';
	
	$str .= '<script> initCKEditor(\'FundraiserDetails'.(isset($row->ID)?'_'.$row->ID:'').'\'); </script>';
	$str .= '</div>';
}
else
{
	$str .= '<div class="details" style="width: 100%;min-height: 400px;">' . (isset($row->Details)?$row->Details:'') . '</div>';
}

$str .= '</div>';

$str .= '<div class="sidecontent">';

if ( isset( $row->ID ) && ( $donors = $database->fetchObjectRows( $q = '
	SELECT 
		d.*, c.Username 
	FROM 
		SBookDonations d 
			LEFT JOIN SBookContact c ON 
			(
					c.UserID > 0 
				AND d.UserID > 0 
				AND c.ID = d.UserID 
			)
	WHERE 
			d.Component = "crowdfunding" 
		AND d.ComponentID = \'' . $row->ID . '\' 
		AND d.IsDeleted = "0" 
	ORDER BY 
		d.ID DESC 
' ) ) )
{
	
	$str .= '<ul>';

	foreach( $donors as $k => $v )
	{
		$str .= '<li>';
		$str .= '<div>' . ( $v->Name && $v->Username ? $v->Username : 'Anonymous' );
		$str .= ' (' . (isset($currency[$v->Currency])?$currency[$v->Currency]:$v->Currency) . ' ' . $v->Donation . ')</div>';
		$str .= '<div>' . $v->DateCreated;
		
		if( $editmode && isset( $parent->access->IsAdmin ) && ( $row->UserID == $webuser->ContactID || !isset( $row->ID ) ) )
		{
		
			$str .= ' <span onclick="removeDonation(' . $v->ID . ')">[ x ]</span> ';
		
		}
		
		$str .= '</div>';
		$str .= '</li>';
	}

	$str .= '</ul>';
	
}

$str .= '</div>';

$str .= '</div>';

$str .= '</div>';

if( isset( $_REQUEST['function'] ) )
{
	die( 'ok<!--separate-->' . $str );
}

?>
