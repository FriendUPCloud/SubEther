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

$str = ''; $imgs = 0;

if( !isset( $_REQUEST['rendercomponent'] ) )
{
	$str .= '<div class="coverimage"><div class="slideshow">';
	//if( $folder = getMediaFolders( 'Folder', 'Cover Photos', 1, $parent->folder->CategoryID ) )
	if( $folder = $database->fetchObjectRow ( 'SELECT * FROM SBookMediaRelation WHERE UserID = "0" AND CategoryID = \'' . $parent->folder->CategoryID . '\' AND MediaType = "Folder" AND Name = "Cover Photos" ORDER BY ID ASC' ) )
	{
		$imageWidth = 815;
		$imageHeight = 338;
		
		$obj = new stdClass();
		
		if( $img = $database->fetchObjectRow( '
			SELECT
				f.DiskPath, i.* 
			FROM
				Folder f, Image i
			WHERE
				i.ImageFolder = "' . $folder->MediaID . '" AND f.ID = i.ImageFolder
			ORDER BY
				i.ID DESC
			LIMIT 1 
		', false, 'components/groups/functions/groups.php' ) )
		{
			$obj->ID = $img->ID;
			$obj->Filename = $img->Filename;
			$obj->FileFolder = $img->ImageFolder;
			$obj->Filesize = $img->Filesize;
			$obj->FileWidth = $img->Width;
			$obj->FileHeight = $img->Height;
			$obj->DiskPath = str_replace( ' ', '%20', ( $img->DiskPath != '' ? $img->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $img->Filename );
			if ( $img->Filename )
			{
				$obj->DiskPath = ( BASE_URL . 'secure-files/images/' . ( $img->UniqueID ? $img->UniqueID : $img->ID ) . '/' );
			}
			
			if ( !FileExists( $obj->DiskPath ) )
			{
				$obj = false;
			}
		}
		
		//$i = new dbImage ();
		//$i->ImageFolder = $folder->MediaID;
		if( /*$i->load() && $i->ID > 0*/ $obj && $obj->ID > 0 )
		{
			// TODO: Change this to the profile slideshow, this one is old and outdated
			
			$str .= '<div id="MainImage" onclick="nextCoverImage( event )">';
			//$str .= $i->getImageHTML( $imageWidth, $imageHeight, 'framed', false, 0x000000 );
			//$str .= '<img style="background-image:url(\'' . $obj->DiskPath . '\');display:block;width:100%;height:100%;max-width:100%;max-height:100%;background-repeat:no-repeat;background-size:cover;background-position:center center;" src="' . ( $groupimage = $obj->DiskPath ) . '"/>';
			$str .= '<img style="background-image:url(\'' . ( $groupimage = $obj->DiskPath ) . '\');display:block;width:100% !important;height:100% !important;max-width:100%;max-height:100%;background-repeat:no-repeat;background-size:cover;background-position:center center;" src="' . ( $groupimage = $obj->DiskPath ) . '"/>';
			$str .= '</div>';
			$str .= '<div class="Navigation">';
			$str .= '<div class="ArrowPrev" onclick="coverStopSlideshow(); prevCoverImage()"><span>«</span></div>';
			$str .= '<div id="CoverPages">';
			
			if( $pgs = $database->fetchObjectRows( '
				SELECT
					f.DiskPath, i.* 
				FROM
					Folder f, Image i
				WHERE
					i.ImageFolder = "' . $folder->MediaID . '" AND f.ID = i.ImageFolder
				ORDER BY
					i.SortOrder ASC, i.ID DESC
			', false, 'components/groups/functions/groups.php' ) )
			//$pgs = new dbImage ();
			//$pgs->ImageFolder = $folder->MediaID;
			//$pgs->addClause( 'ORDER BY', 'SortOrder ASC' );
			//if ( $pgs = $pgs->find() )
			{
				$ii = 0;
				$pg = '';
				foreach( $pgs as $p )
				{
					$imgurl = '';
					if ( $ii == 0 ) $c = ' Current'; else $c = '';
					++$ii;
					//$imgurl = $p->getImageUrl( $imageWidth, $imageHeight, 'framed', false, 0x000000 );
					//$imgurl = str_replace( ' ', '%20', ( $p->DiskPath != '' ? $p->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $p->Filename );
					$imgurl = ( BASE_URL . 'secure-files/images/' . ( $p->UniqueID ? $p->UniqueID : $p->ID ) . '/' );
					
					if ( !FileExists( $imgurl ) )
					{
						$imgurl = false;
					}
					
					if ( $imgurl )
					{
						$pg .= '<div class="Page Nr' . $ii . $c . '" onclick="setCoverImage(' . "'" . $imgurl . "'" . ', ' . "'" . $ii . "'" . ')"><span>' . $ii . '</span></div>';
						$imgs++;
					}
				}
				if( $ii > 1 )
				{
					$str .= $pg;
				}
			}
			
			$str .= '</div>';
			$str .= '<div class="ArrowNext" onclick="coverStopSlideshow(); nextCoverImage()"><span>»</span></div>';
			$str .= '</div>';
		}
	}
	// Fallback
	else
	{
		$str .= '<div id="MainImage" style="background-image:url(\'subether/gfx/img_placeholder4.png\');background-position:center 50%;background-size:630px auto;background-color:#e5e5e5;background-repeat:no-repeat;"></div>';
	}
	$str .= '</div>';
	
	$str .= '<div class="edit_btn">';
	
	if( $parent && ( $parent->folder->Permission == 'admin' || $parent->folder->Permission == 'owner' || isset( $parent->access->IsAdmin ) ) )
	{
		$str .= '<div class="edit_btn_cover" onclick="ge(\'coverUploadBtn\').click();">';
		$str .= '<div><span>' . i18n( 'i18n_Change Cover' ) . '</span></div>';
		$str .= '<div class="uploadfile">';
		$str .= '<form method="post" target="fileIframe" name="coverUpload" enctype="multipart/form-data" action="' . $parent->route . '?global=true&action=uploadfile">';
		$str .= '<input type="file" class="upload_btn" id="coverUploadBtn" name="cover" onchange="document.coverUpload.submit();">';
		$str .= '</form>';
		//$str .= '<script>setOpacity ( ge(\'coverUploadBtn\' ), 0 );</script>';
		$str .= '</div>';
		$str .= '</div>';
	}
	
	if( $imgs > 0 && ( $folder = $database->fetchObjectRow ( 'SELECT * FROM SBookMediaRelation WHERE CategoryID = \'' . $parent->folder->CategoryID . '\' AND UserID = "0" AND MediaType = "Folder" AND Name = "Cover Photos" ORDER BY ID ASC' ) ) )
	{
		$cn = 0;
		if ( $fldimgs = $database->fetchRows ( 'SELECT * FROM Image WHERE ImageFolder=\'' . $folder->MediaID . '\' ORDER BY ID ASC' ) )
		{
			foreach ( $fldimgs as $fim )
			{
				if ( $fim['ID'] == $im->ID ) break;
				$cn++;
			}
		}
		
		$str .= '<div class="view_btn_cover" onclick="openFullscreen( \'Library\', \'' . $folder->MediaID . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( ' . (string)$cn . ' ); } )">';
		$str .= '<div><span>' . i18n( 'i18n_View Album' ) . '</span></div>';
		$str .= '</div>';
	}
	
	$str .= '</div>';
	
	$str .= '</div>';
	
	$str .= '<div id="Tabs">';
	
	//if( $parent->MainTabs )
	if( $parent->tabs )
	{
		//if( $webuser->ID == 81 ) die( print_r( $parent->tabs,1 ) . ' --' );
		$i = 0; $tab = '';
		//$str .= '<label for="show-menu" class="show-menu">Show Menu</label> <input type="checkbox" id="show-menu" role="button">';
		$str .= '<ul id="GroupMenu">';
		//foreach( $parent->MainTabs as $key=>$val )
		foreach( $parent->tabs as $key=>$val )
		{
			if( $val == '0' || !ComponentExists( $key, $parent->module, $parent->position, 'groups' ) ) continue;
			$str .= '<li ' . ( $parent->mode == $key ? 'class="current"' : '' ) . '><a href="' . $parent->nav . ( $i == 0 ? '' : $key . '/' ) . '"><span>' . i18n( 'i18n_' . $val ) . '</span></a></li>';
			//if( ( $parent->MainMode ? $parent->MainMode : $parent->mode ) == $key )
			if( $parent->mode == $key )
			{
				$tab = $i;
			}
			$i++;
		}
		$str .= '</ul>';
		$str .= '<div class="clearboth" style="clear:both"></div>';
	}
	
	$str .= '<div class="buttons">';
	
	if( $parent->folder->ObjectID )
	{
		$str .= '<button onclick="groupOptions()">';
		$str .= '<span>' . i18n( 'i18n_Edit' ) . '</span>';
		$str .= '</button>';
	}
	else if( $webuser->ID > 0 && ( $parent->folder->Privacy != 'ClosedGroup' || isset( $parent->access->IsSystemAdmin ) ) )
	{
		$str .= '<button onclick="joinGroup( \'' . $parent->folder->CategoryID . '\' )"><span>+ ' . i18n( 'Join Group' ) . '</span></button>';
	}
	
	if( !$parent->folder->ObjectID && isset( $parent->access->IsSystemAdmin ) )
	{
		$str .= '<button onclick="groupOptions()">';
		$str .= '<span>' . i18n( 'i18n_Edit' ) . '</span>';
		$str .= '</button>';
	}
	
	$str .= '<div id="GroupOptionsBox">';
	$str .= '<div class="toparrow"></div>';
	$str .= '<div class="inner"></div>';
	$str .= '</div>';
	$str .= '</div>';
	
	$str .= '</div>';
	
	$str .= '<div id="About">';
	if( $tab == 0 && $astr )
	{
		$str .= $astr;
		//$str .= ( $parent->folder->Description ? ( '<div class="Box description">' . nl2br( $parent->folder->Description ) . '</div>' ) : '' );
	}
	$str .= '</div>';
	//$parent->cuser = false;
	//$parent->webuser = false;
	//die( print_r( $parent,1 ) . ' --' );
	//$str .= '<div id="Content">' . $Component->subpage . '</div>';

}

if( $parent->folder->Privacy == 'ClosedGroup' && $webuser->ID <= 0 && !in_array( $parent->mode, array( 'members' ) ) )
{
	// No content to show	
}
else
{
	$str .= '<div id="Content">' . ( ( $parent->mode && !$Component->subpage ) ? IncludeComponent( $parent->mode, $parent, true ) : $Component->subpage ) . '</div>';
}

?>
