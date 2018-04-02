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

// TODO: Temporary put into a global function
$ext_large = array(
	'pdf'=>'pdf/pdf-128_32.png',
	'xls'=>'xls_win/xlsx_win-128_32.png',
	'doc'=>'docx_win/docx_win-128_32.png',
	'docx'=>'docx_win/docx_win-128_32.png',
	'jpg'=>'jpeg/jpeg-128_32.png', 
	'jpeg'=>'jpeg/jpeg-128_32.png',
	'png'=>'png/png-128_32.png',
	'gif'=>'gif/gif-128_32.png', 
	'mov'=>'mov/mov-128_32.png',
	'avi'=>'mov/mov-128_32.png',
	'ogv'=>'mov/mov-128_32.png',
	'mp4'=>'mov/mov-128_32.png',
	'swf'=>'mov/mov-128_32.png',
	'url'=>'url/url-128_32.png',
	'mp3'=>'mp3/mp3-128_32.png', 
	'txt'=>'text/text-128_32.png'
);

// The day we changed the message format :D
$oldMessageDate = strtotime( '2015-02-19 18:20:00' );

$maxWidth = 1280;
$maxHeight = 960;

$posterimage = ''; $replyimage = '';

if( !$p->NodeID && $imgs[$p->Image] )
{
	$posterimage = '<div style="width:40px;height:40px;background-image:url(\'' . $imgs[$p->Image]->DiskPath . '\');background-size:cover;background-repeat:no-repeat;"></div>';
	$replyimage = '<div style="width:30px;height:28px;background-image:url(\'' . $imgs[$p->Image]->DiskPath . '\');background-size:cover;background-repeat:no-repeat;"></div>';
}
else if( $p->NodeID > 0 && $nods[$p->Image] )
{
	$posterimage = '<div style="width:40px;height:40px;background-image:url(\'' . $nods[$p->Image]->DiskPath . '\');background-size:cover;background-repeat:no-repeat;"></div>';
	$replyimage = '<div style="width:30px;height:28px;background-image:url(\'' . $nods[$p->Image]->DiskPath . '\');background-size:cover;background-repeat:no-repeat;"></div>';
}

/*$i = new dbImage ();
if( $p->NodeID > 0 )
{
	$i->NodeID = $p->NodeID;
	$i->NodeMainID = $p->Image;
}
else
{
	$i->ID = $p->Image;
}
if( $i->Load() )
{
	$posterimage = $i->getImageHTML ( 40, 40, 'framed', false, 0xffffff );
	$replyimage = $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
}*/
$rating = explode( '/', $p->Rating );
$str .= '<div class="Comment' . ( $focusid > 0 && $focusid == $p->ID ? ' focus' : '' ) . '">';
$str .= '<div ' . ( isset( $_REQUEST['chris'] ) ? ( 'title="' . $p->ID . '"' ) : '' ) . ' class="Box" id="MessageID_' . $p->ID . '">';

$str .= '<div class="Edit">';
if( $p->SenderID == $user->ContactID || ( $module == 'profile' && $user->ContactID == $cuser->ContactID ) || IsSystemAdmin() )
{
	//$str .= '<div class="Edit" onclick="deleteWallContent(' . $p->ID . ')"><div></div></div>';
	//$str .= '<div onclick="embedWallEditor(' . $p->ID . ')">[Edit]</div>';
	$str .= '<div class="options" onclick="WallOptions( this, \'' . $p->ID . '\' )"></div>';
}
$str .= '<div class="bookmark' . ( isset( $bmarks ) && is_array( $bmarks ) && in_array( $p->ID, $bmarks ) ? ' marked' : '' ) . '" onclick="Bookmark(this,\'' . $p->ID . '\')"><div></div></div>';
$str .= '</div>';

if( isset( $_POST[ 'mid' ] ) && $_POST[ 'mid' ] == $p->ID )
{
	$str = '';
}
$str .= '<div class="messagebox">';
$str .= '<div class="posted"><table><tr><td><div class="image"><a href="' . $path . $p->Name . '">' . $posterimage . '</a></div></td><td><div>';
$str .= '<p class="name"><a href="' . $path . $p->Name . '">' . ( GetUserDisplayname( $p->SenderID ) ? GetUserDisplayname( $p->SenderID ) : $p->Name ) . '</a>';
if( strtolower( $folder->MainName ) == 'profile' && strtolower( $module ) == 'main' && $p->IsGroup )
{
	$str .= '<span> &#10148; </span><a href="' . $path . 'groups/' . $p->SBC_ID . '/wall/">' . $p->SBC_Name . '</a>';
}
else if( 
	strtolower( $folder->MainName ) == 'profile' && 
	strtolower( $module ) == 'main' && 
	!$p->IsGroup && 
	$p->Receiver && 
	( $p->ReceiverID != $p->SenderID ) 
)
{
	$str .= '<span> &#10148; </span><a href="' . $path . $p->User_Name . '">' . ( GetUserDisplayname( $p->ReceiverID ) ? GetUserDisplayname( $p->ReceiverID ) : $p->User_Name ) . '</a>';
}

$str .= '</p><p class="date">' . TimeToHuman( $p->Date );
// If this post is my post show access controls
if( $p->SenderID == $user->ContactID )
{
	$str .= ' <select onchange="UpdateAccess(\'' . $p->ID . '\',this.value)">';
	$str .= '<option value="0"' . ( $p->Access == 0 ? ' selected="selected"' : '' ) . '>Public</option>';
	$str .= '<option value="1"' . ( $p->Access == 1 ? ' selected="selected"' : '' ) . '>Contacts</option>';
	$str .= '<option value="2"' . ( $p->Access == 2 ? ' selected="selected"' : '' ) . '>Only Me</option>';
	//$str .= '<option value="3"' . ( $p->Access == 3 ? ' selected="selected"' : '' ) . '>Custom</option>';
	if( isset( $parent->access->IsAdmin ) )
	{
		$str .= 				'<option value="4"' . ( $p->Access == 4 ? ' selected="selected"' : '' ) . '>Admin</option>';
	}
	$str .= '</select>';
}
$str .= '</p>';
$str .= '</div></td></tr></table></div>';
//$str .= '<div id="MessageContent_' . $p->ID . '" class="content">' . parseText ( formatText ( $p->Message ) ) . '</div>';
if( strtotime( $p->Date ) < $oldMessageDate )
	$str .= '<div id="MessageContent_' . $p->ID . '" class="content">' . formatText ( $p->Message ) . '</div>';
else $str .= '<div id="MessageContent_' . $p->ID . '" class="content">' . convertToTags( $p->Message ) . '</div>';

// Data Content: Start -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

if( $p->Data && !$p->HTML && ( ( !is_array( $p->Data ) && $p->Data->Media != 'album' ) || ( is_array( $p->Data ) && $p->Data[0]->MediaType != 'album' ) ) )
{
	$media = '';
	
	if( is_array( $p->Data ) )
	{
		$p->Data = $p->Data[0];
	}
	
	if( isset( $_REQUEST['dbugg'] ) ) die( print_r( $p,1 ) . ' --' );
	
	// TODO: collect this dbImage & dbFile & dbFolder into on string at top
	if( $p->NodeID > 0 && $p->Data->MediaType != 'file' )
	{
		$media = new dbImage ();
		$media->NodeID = $p->NodeID;
		$media->NodeMainID = $p->Data->FileID;
		$media->Load();
	}
	else if( $p->Data->MediaType != 'file' )
	{
		$media = new dbImage ();
		$media->ID = $p->Data->FileID;
		$media->Load();
	}
	/*else if( $p->NodeID > 0 && $p->Data->MediaType == 'file' )
	{
		$media = new dbFile ();
		$media->NodeID = $p->NodeID;
		$media->NodeMainID = $p->Data->FileID;
	}
	else if( $p->Data->MediaType == 'file' )
	{
		$media = new dbFile ();
		$media->ID = $p->Data->FileID;
	}*/
	
	//if( isset( $_REQUEST['dbug'] ) && $p->ID == 1988 ) die( print_r( $p,1 ) . ' ..' );
	
	if( $media->ID > 0 )
	{
		//$folder = new dbFolder();
		//$folder->Load( $media->FileFolder > 0 ? $media->FileFolder : $media->ImageFolder );
		
		//$p->Data->Url = ( $folder->DiskPath && $p->Data->FileName ? ( $folder->DiskPath . $p->Data->FileName ) : $p->Data->Url );
	}
	
	switch( $p->Data->Type )
	{
		// Video ----------------------------------------------------------------------------------------------------
		case 'video':
			$str .= '<div class="html"><div class="ParseContent Video">';
			if( $p->Data->Thumb )
			{
				$str .= '<div class="image" link="' . $p->Data->Url . '" onclick="embedVideo(this,\'743\',\'420\',\''.$p->Data->Media.'\')">';
				$str .= '<img style="background-image:url(\'' . $p->Data->Thumb . '\');">';
				$str .= '<em></em>';
				$str .= '</div>';
			}
			else if( $media->ID > 0 )
			{
				$str .= '<div class="image" link="' . $p->Data->Url . '" onclick="embedVideo(this,\'743\',\'420\',\''.$p->Data->Media.'\')">';
				$str .= '<img style="background-image:url(' . $media->getImageURL ( $p->Data->Limit->width, $p->Data->Limit->height, 'framed', false, 0xffffff ) . ');max-width:' . $p->Data->FileWidth . 'px;max-height:' . $p->Data->FileHeight . 'px;">';
				$str .= '<em></em>';
				$str .= '</div>';
			}
			else
			{
				$str .= '<div class="image" link="' . $p->Data->Url . '" onclick="embedVideo(this,\'743\',\'420\',\''.$p->Data->Media.'\')">';
				$str .= '<img style="background-image:url(\'admin/gfx/icons/page_white.png\');">';
				$str .= '<em></em>';
				$str .= '</div>';
			}
			$str .= '<div class="text">';
			$str .= '<h3><a href="' . $p->Data->Url . '" target="_blank">' . dot_trim( $p->Data->Title, $p->Data->Limit->title ) . '</a></h3>';
			$str .= '<p><a href="' . $p->Data->Url . '" target="_blank">' . dot_trim( html_entity_decode( $p->Data->Leadin ), $p->Data->Limit->description ) . '</a></p>';
			$str .= '<p class="url"><a href="http://' . $p->Data->Domain . '" target="_blank">' . $p->Data->Domain . '</a></p>';
			$str .= '</div></div></div>';
			break;
		// Audio ----------------------------------------------------------------------------------------------------
		case 'audio':
			$str .= '<div class="html"><div class="ParseContent Audio">';
			$str .= '<div class="image" link="' . $p->Data->Url . '" onclick="embedAudio(this,\'743\',\'420\',\''.$p->Data->Media.'\')">';
			$str .= '<img style="background-image:url(\'admin/gfx/icons/page_white.png\');">';
			$str .= '<em></em>';
			$str .= '</div>';
			$str .= '<div class="text">';
			$str .= '<h3><a href="' . $p->Data->Url . '" target="_blank">' . dot_trim( $p->Data->Title, $p->Data->Limit->title ) . '</a></h3>';
			$str .= '<p><a href="' . $p->Data->Url . '" target="_blank">' . dot_trim( html_entity_decode( $p->Data->Leadin ), $p->Data->Limit->description ) . '</a></p>';
			$str .= '<p class="url"><a href="http://' . $p->Data->Domain . '" target="_blank">' . $p->Data->Domain . '</a></p>';
			$str .= '</div></div></div>';
			break;
		// File ----------------------------------------------------------------------------------------------------
		case 'file':
			$str .= '<div class="html"><div class="ParseContent File">';
			$str .= '<div class="image" link="' . $p->Data->Url . '" onclick="embedFile(this,\'743\',\'700\',\''.$p->Data->Media.'\')">';
			//$str .= '<img style="background-image:url(\'admin/gfx/icons/page_white.png\');">';
			$str .= '<img src="subether/gfx/icons/' . ( $ext_large[$p->Data->FileType] ? $ext_large[$p->Data->FileType] : $ext_large['txt'] ) . '">';
			$str .= '</div>';
			$str .= '<div class="text">';
			$str .= '<h3><a href="' . $p->Data->Url . '" target="_blank">' . dot_trim( $p->Data->Title, $p->Data->Limit->title ) . '</a></h3>';
			$str .= '<p><a href="' . $p->Data->Url . '" target="_blank">' . dot_trim( html_entity_decode( $p->Data->Leadin ), $p->Data->Limit->description ) . '</a></p>';
			$str .= '<p class="url"><a href="http://' . $p->Data->Domain . '" target="_blank">' . $p->Data->Domain . '</a></p>';
			$str .= '</div></div></div>';
			break;
		// Remote site data -------------------------------------------------------------------------------------------
		default:
			$str .= '<div class="html"><div class="ParseContent Site">';
			if( $media->ID > 0 )
			{
				$iurl = $media->getImageURL ( $p->Data->Limit->width, $p->Data->Limit->height, 'framed', false, 0xffffff );
				$str .= '<div class="image ' . $p->Data->Type . ( $p->Data->Limit->width <= $p->Data->FileWidth ? ' big' : ' small' ) . '">';
				$str .= '<div class="imagecontainer">';
				$str .= '<a href="' . $p->Data->FilePath . '" target="_blank" style="background-image: url(' . $iurl . ')">';
				$str .= '<img src="' . $iurl . '" style="background-image:url(' . $iurl . ');max-width:' . $p->Data->FileWidth . 'px;max-height:' . $p->Data->FileHeight . 'px;">';
				$str .= '</a></div>';
				$str .= '</div>';
			}
			$str .= '<div class="text">';
			$str .= '<h3><a href="' . $p->Data->Url . '" target="_blank">' . dot_trim( $p->Data->Title, $p->Data->Limit->title ) . '</a></h3>';
			$str .= '<p><a href="' . $p->Data->Url . '" target="_blank">' . dot_trim( html_entity_decode( $p->Data->Leadin ), 
				$p->Data->Limit->description ) . '</a></p>';
			$str .= '<p class="url"><a href="http://' . $p->Data->Domain . '" target="_blank">' . $p->Data->Domain . '</a></p>';
			$str .= '</div></div></div>';
			break;
	}
	
	if( isset( $_REQUEST['test'] ) ) die( print_r( $p,1 ) . ' --' );
}
else if( $p->Data && $p->Data[0]->MediaType == 'album' )
{
	$pri = priorityList( 'media' );
	$data = sortByPriority( $p->Data, 'MediaFormat', $pri );
	$i = 1; $limit = 4;
	$str .= '<div class="html"><div class="ParseContent Album">';
	$dcount = count ( $data );
	foreach( $data as $obj )
	{
		$albumdata = renderWallAlbum( $maxWidth, $maxHeight, 4, $obj->FileWidth, $obj->FileHeight, $obj->MediaFormat, $i );
		
		if( $i > $limit ) continue;
		
		$im = new dbImage ();
		if( $p->NodeID > 0 )
		{
			$im->NodeID = $p->NodeID;
			$im->NodeMainID = $obj->FileID;
		}
		else
		{
			$im->ID = $obj->FileID;
		}
		if( $im->Load() )
		{
			$cn = 0;
			if ( $fldimgs = $database->fetchRows ( 'SELECT * FROM Image WHERE ImageFolder=\'' . $im->ImageFolder . '\' ORDER BY ID ASC' ) )
			{
				foreach ( $fldimgs as $fim )
				{
					if ( $fim['ID'] == $im->ID ) break;
					$cn++;
				}
			}
			
			// Don't allow too high images
			$showHeight = $albumdata->Width / $obj->FileWidth * $obj->FileHeight;
			$scale = true;
			
			if ( $obj->FileWidth < $albumdata->Width || $obj->FileHeight < $albumdata->Height )
			{
				$finalWidth = $obj->FileWidth;
				$finalHeight = $obj->FileHeight;
				$scale = false;
			}
			else
			{
				$finalWidth = $albumdata->Width;
				$finalHeight = $showHeight;
			}
			
			$mode = '';
			if ( $obj->FileHeight >= $obj->FileWidth * 2 )
			{
				$mode = '; background-position: left top; background-repeat: no-repeat; background-size: contain';
				$finalHeight = $obj->FileHeight > $albumdata->Height ? $albumdata->Height : $obj->FileHeight;
			}
			if ( $scale == false )
				$mode = '; background-position: left top; background-repeat: no-repeat; background-size: auto'; 
			
			// Get image
			$image = $im->getImageUrl ( $finalWidth, $finalHeight, 'proximity', false, 0xffffff );
			
			$str .= '<div class="image ' . $obj->MediaType . ' ' . $obj->MediaFormat . ' nr' . $i++ . ' total' . ( $dcount > $limit ? $limit : $dcount ) . '">';
			$str .= '<div class="imagecontainer">';
			//$str .= '<a href="' . $obj->FilePath . '" target="_blank">';
			$str .= '<a href="javascript:void(0)" onclick="openFullscreen( \'Library\', \'' . $obj->FileFolder . '\', \'album\', function(){ Showroom.init( \'Album_Showroom\' ); Showroom.off (); Showroom.changePage ( ' . (string)$cn . ' ); } )">';
			$str .= '<img src="' . $image . '" style="background-image:url(\'' . $image . '\'); width:' . $finalWidth . 'px; height: ' . floor ( $finalHeight ) . 'px' . $mode . '">';
			$str .= '</a></div>';
			$str .= '</div>';
		}
	}
	$str .= '<div class="clearboth"></div>';
	$str .= '</div></div>';
}
else if( $p->HTML )
{
	// TODO: Check text encoding on $p->HTML - which sometimes goes MAD with weird characters
	$str .= StrReplaceByAttribute( $p->HTML, 'replace=""', 'onclick="embedVideo(this,\'743\',\'420\')"' );
}

// Data Content: End ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

$str .= '<div class="Buttons">';
$str .= '<div class="floatleft">';
if( $parent->webuser->ID > 0 )
{
	$str .= '<a href="javascript:void(0)" onclick="replyToMessage( \'' . $p->ID . '\', \'' . $userimg . '\' )">' . i18n ( 'i18n_comment' ) . '</a>&nbsp;';
}
//$str .= '<a href="javascript:void(0)" onclick="openWindow( \'Wall\', \'' . $p->ID . '\', \'share\' )">' . i18n ( 'i18n_share' ) . '</a>&nbsp;';
//$str .= '<span> ' . ( ( $rating[0] - $rating[1] ) == 0 ? '' : ( $rating[0] - $rating[1] ) ) . ' </span>';
if( $parent->webuser->ID > 0 )
{
	$str .= '<a href="javascript:void(0)" onclick="voteComment( \'' . $p->ID . '\', \'like\' )"><span class="voteimg up" title="Like" style="background-image:url(\'subether/gfx/thumb_up_grey_icon.png\');background-repeat:no-repeat;width:14px;height:18px;display:block;float:left;border:0;"></span></a>';
	$str .= '<a href="javascript:void(0)" onclick="voteComment( \'' . $p->ID . '\', \'dislike\' )"><span class="voteimg down" title="Dislike" style="background-image:url(\'subether/gfx/thumb_down_grey_icon.png\');background-repeat:no-repeat;width:14px;height:18px;display:block;float:left;border:0;"></span></a>';
}
$str .= '<div class="clearboth"></div>';
$str .= '</div>';
// --- Rating ----------------------------------------------------------------------------
if( 1==1 )
{
	$sbstr = ''; $i = 0; $up = 0; $dn = 0;
	
	if( is_array( $p->RateDownBy ) && is_array( $p->RateUpBy ) )
	{	
		$rating = ( count( $p->RateDownBy ) > count( $p->RateUpBy ) ? $p->RateDownBy : $p->RateUpBy );
		
		foreach( $rating as $k=>$uid )
		{
			if( isset( $p->RateDownBy[$k] ) )
			{
				$sbstr .= '<li>- 1 ' . GetUserDisplayname( $p->RateDownBy[$k] ) . '</li>';
				$i++; $dn++;
			}
			if( isset( $p->RateUpBy[$k] ) )
			{
				$sbstr .= '<li>+1 ' . GetUserDisplayname( $p->RateUpBy[$k] ) . '</li>';
				$i++; $up++;
			}
		}
	}
	
	$str .= '<div class="floatright">';
	if( $i > 0 )
	{
		$str .= '<a href="javascript:void(0)" onmouseover="tooltips(this,\'open\')" onmouseout="tooltips(this,\'close\')">(' . $i . ')</a>';
		$str .= '<div class="tooltips"><div class="bottomarrow"></div><div class="inner"><ul>';
		$str .= $sbstr;
		$str .= '</ul></div></div>';
	}
	$str .= '<div class="clearboth"></div>';
	$str .= '</div>';
	$str .= '<div class="floatright">';
	$str .= '<div class="ratebox"><div class="ratebar2"><div style="width: ' . ( $i > 0 ? ( ( $up / $i * 100 ) . '%' ) : '0%' ) . ';"></div></div></div>';
	$str .= '<div class="clearboth"></div>';
	$str .= '</div>';
}
// --- Seen -------------------------------------------------------------------------------
if( $p->SenderID == $user->ContactID && is_array( $p->SeenBy ) )
{
	$sbstr = ''; $i = 0;
	foreach( $p->SeenBy as $uid )
	{
		$sbstr .= '<li>' . GetUserDisplayname( $uid ) . '</li>';
		$i++;
	}
	$str .= '<div class="floatright"><a href="javascript:void(0)" onmouseover="tooltips(this,\'open\')" onmouseout="tooltips(this,\'close\')">Seen by ' . $i . '</a>&nbsp;';
	$str .= '<div class="tooltips"><div class="bottomarrow"></div><div class="inner"><ul>';
	$str .= $sbstr;
	$str .= '</ul></div></div>';
	$str .= '<div class="clearboth"></div>';
	$str .= '</div>';
}
$str .= '<div class="clearboth"></div>';
$str .= '</div>';
$str .= '</div>';
if( isset( $_POST[ 'mid' ] ) && $_POST[ 'mid' ] == $p->ID )
{
	die( 'ok<!--separate-->' . $str );
}
$str .= '</div>';
$str .= '<div id="mBox_' . $p->ID . '">';

$qc = '
	SELECT 
		m.*, u.Display, u.Firstname, u.Middlename, u.Lastname, u.Username AS Name, u.ImageID AS Image 
	FROM 
		SBookMessage m, 
		SBookContact u 
	WHERE
		( ( m.ParentID = \'' . $p->ID . '\' 
		AND m.NodeID = "0" ) 
		OR ( m.NodeID > 0
		AND m.NodeID = \'' . $p->NodeID . '\'
		AND m.ParentID = \'' . $p->NodeMainID . '\' ) )
		AND u.ID = m.SenderID 
	ORDER BY  
		m.ID ASC 
';	

if( $p->Type == 'post' && $comments = $database->fetchObjectRows( $qc ) )
{
	foreach( $comments as $c )
	{
		if( $c->ParentID == 0 ) continue;
		
		// Decode json
		$c->SeenBy = is_string( $c->SeenBy ) ? json_decode( $c->SeenBy ) : false;
		$c->RateDownBy = is_string( $c->RateDownBy ) ? json_decode( $c->RateDownBy ) : false;
		$c->RateUpBy = is_string( $c->RateUpBy ) ? json_decode( $c->RateUpBy ) : false;
		$c->Data = is_string( $c->Data ) ? json_decode( $c->Data ) : false;
		
		$posterimage = ''; $replyimage = '';
		$i = new dbImage ();
		if( $c->NodeID > 0 )
		{
			$i->NodeID = $c->NodeID;
			$i->NodeMainID = $c->Image;
		}
		else
		{
			$i->ID = $c->Image;
		}
		if( $i->Load() )
		{
			$posterimage = $i->getImageHTML ( 40, 40, 'framed', false, 0xffffff );
			$replyimage = $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
		}
		$crating = explode( '/', $c->Rating );
		$str .= '<div class="Comment">';
		$str .= '<div class="Box" ' . ( isset( $_REQUEST['chris'] ) ? ( 'title="' . $c->ID . '"' ) : '' ) . ' id="MessageID_' . $c->ID . '">';
		if( $c->SenderID == $user->ContactID || $p->SenderID == $user->ContactID || ( $module == 'profile' && $user->ContactID == $cuser->ContactID ) || IsSystemAdmin() )
		{
			//$str .= '<div class="Edit" onclick="deleteWallContent(' . $c->ID . ')"><div></div></div>';
			//$str .= '<div onclick="embedWallEditor(\'' . $p->ID . '\', \'' . $c->ID . '\')">[Edit]</div>';
			$str .= '<div class="Edit comment"><div class="options" onclick="WallOptions( this, \'' . $p->ID . '\', \'' . $c->ID . '\' )"></div></div>';
		}
		if( isset( $_POST[ 'mid' ] ) && $_POST[ 'mid' ] == $c->ID )
		{
			$str = '';
		}
		$str .= '<div class="commentbox">';
		$str .= '<table><tr>';
		$str .= '<td style="width:37px"><div class="image"><a href="' . $path . $c->Name . '">' . $replyimage . '</a></div></td>';
		$str .= '<td><div id="ReplyContent_' . $p->ID . '_' . $c->ID . '"><p class="name"><a href="' . $path . $c->Name . '"><u>' . ( GetUserDisplayname( $c->SenderID ) ? GetUserDisplayname( $c->SenderID ) : $c->Name ) . '</u></a> <span class="replycontent">' . formatText ( $c->Message ) . '</span></p>';
		
		// Data Content: Start -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
		if( $c->Data && !$c->HTML && ( ( !is_array( $c->Data ) && $c->Data->Media != 'album' ) || ( is_array( $c->Data ) && $c->Data[0]->MediaType != 'album' ) ) )
		{
			$cmedia = new dbImage ();
			if( $c->Data->MediaType != 'file' )
			{
				$cmedia->Load( $c->Data->FileID );
			}
			
			switch( $c->Data->Type )
			{
				// Video ----------------------------------------------------------------------------------------------------
				case 'video':
					$str .= '<div class="html"><div class="ParseContent Video">';
					if( $cmedia->ID > 0 )
					{
						$cimage = $cmedia->getImageURL ( $c->Data->Limit->width, $c->Data->Limit->height, 'framed', false, 0xffffff );
						$str .= '<div class="image" link="' . $c->Data->Url . '" onclick="embedVideo(this,\'710\',\'380\',\''.$c->Data->Media.'\')">';
						$str .= '<img style="background-image:url(' . $cimage . ');max-width:' . $c->Data->FileWidth . 'px;max-height:' . $c->Data->FileHeight . 'px;">';
						$str .= '<em></em>';
						$str .= '</div>';
					}
					$str .= '<div class="text">';
					$str .= '<h3><a href="' . $c->Data->Url . '" target="_blank">' . dot_trim( stripslashes( $c->Data->Title ), $c->Data->Limit->title ) . '</a></h3>';
					$str .= '<p><a href="' . $c->Data->Url . '" target="_blank">' . dot_trim( html_entity_decode( $c->Data->Leadin ), $c->Data->Limit->description ) . '</a></p>';
					$str .= '<p class="url"><a href="http://' . $c->Data->Domain . '" target="_blank">' . $c->Data->Domain . '</a></p>';
					$str .= '</div></div></div>';
					break;
				// Remote site data -------------------------------------------------------------------------------------------
				default:
					$str .= '<div class="html"><div class="ParseContent Site">';
					if( $cmedia->ID > 0 )
					{
						$cimage = $cmedia->getImageURL ( $c->Data->Limit->width, $c->Data->Limit->height, 'framed', false, 0xffffff );
						$str .= '<div class="image ' . $c->Data->Type . ' small">';
						$str .= '<a href="' . $c->Data->FilePath . '" target="_blank" style="background-image:url(' . $cimage . ')">';
						$str .= '<img style="background-image:url(' . $cimage . ');max-width:' . $c->Data->FileWidth . 'px;max-height:' . $c->Data->FileHeight . 'px;">';
						$str .= '</a></div>';
					}
					$str .= '<div class="text">';
					$str .= '<h3><a href="' . $c->Data->Url . '" target="_blank">' . dot_trim( stripslashes( $c->Data->Title ), $c->Data->Limit->title ) . '</a></h3>';
					$str .= '<p><a href="' . $c->Data->Url . '" target="_blank">' . dot_trim( html_entity_decode( $c->Data->Leadin ), $c->Data->Limit->description ) . '</a></p>';
					$str .= '<p class="url"><a href="http://' . $c->Data->Domain . '" target="_blank">' . $c->Data->Domain . '</a></p>';
					$str .= '</div></div></div>';
					break;
			}
		}
		else
		{
			$str .= StrReplaceByAttribute( $c->HTML, 'replace=""', 'onclick="embedVideo(this,\'710\',\'380\')"' );
		}
		
		// Data Content: End ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
		$str .= '<p>';
		
		// --- Rating ----------------------------------------------------------------------------
		if( is_array( $c->RateDownBy ) && is_array( $c->RateUpBy ) )
		{
			$sbstr = ''; $i = 0; $up = 0; $dn = 0;
			
			$rating = ( count( $c->RateDownBy ) > count( $c->RateUpBy ) ? $c->RateDownBy : $c->RateUpBy );
			
			foreach( $rating as $k=>$uid )
			{
				if( isset( $c->RateDownBy[$k] ) )
				{
					$sbstr .= '<li>- 1 ' . GetUserDisplayname( $c->RateDownBy[$k] ) . '</li>';
					$i++; $dn++;
				}
				if( isset( $c->RateUpBy[$k] ) )
				{
					$sbstr .= '<li>+1 ' . GetUserDisplayname( $c->RateUpBy[$k] ) . '</li>';
					$i++; $up++;
				}
			}
			
			$str .= '<div class="floatright">';
			$str .= '<a href="javascript:void(0)" onmouseover="tooltips(this,\'open\')" onmouseout="tooltips(this,\'close\')">(' . $i . ')</a>';
			$str .= '<div class="tooltips"><div class="bottomarrow"></div><div class="inner"><ul>';
			$str .= $sbstr;
			$str .= '</ul></div></div>';
			$str .= '<div class="clearboth"></div>';
			$str .= '</div>';
			$str .= '<div class="floatright">';
			$str .= '<div class="ratebox"><div class="ratebar2"><div style="width: ' . ( $i > 0 ? ( ( $up / $i * 100 ) . '%' ) : '0%' ) . ';"></div></div></div>';
			$str .= '<div class="clearboth"></div>';
			$str .= '</div>';
		}
		
		$str .= '<div class="floatleft">';
		$str .= '<span class="date">' . TimeToHuman( $c->Date ) . ' </span>';
		//$str .= '<span> ' . ( ( $crating[0] - $crating[1] ) == 0 ? '' : ( $crating[0] - $crating[1] ) ) . ' </span>';
		if( $parent->webuser->ID > 0 )
		{
			$str .= '<a href="javascript:void(0)" onclick="voteComment( \'' . $c->ID . '\', \'like\' )"><span class="voteimg up" title="Like" style="background-image:url(\'subether/gfx/thumb_up_grey_icon.png\');background-repeat:no-repeat;width:14px;height:18px;display:block;float:left;border:0;"></span></a>';
			$str .= '<a href="javascript:void(0)" onclick="voteComment( \'' . $c->ID . '\', \'dislike\' )"><span class="voteimg down" title="Dislike" style="background-image:url(\'subether/gfx/thumb_down_grey_icon.png\');background-repeat:no-repeat;width:14px;height:18px;display:block;float:left;border:0;"></span></a>';
		}
		$str .= '<div class="clearboth"></div>';
		$str .= '</div>';
		
		$str .= '</p>';
		$str .= '<div class="clearboth"></div>';
		$str .= '</div>';
		$str .= '</td>';
		$str .= '</tr></table>';
		$str .= '</div>';
		if( isset( $_POST[ 'mid' ] ) && $_POST[ 'mid' ] == $c->ID )
		{
			die( 'ok<!--separate-->' . $str );
		}
		$str .= '</div>';
		$str .= '</div>';
		
		// Set status IsRead on user notify
		if( $focusid > 0 && $focusid == $p->ID )
		{
			IsRead( $c->ID );
		}
	}
	
	if( $webuser->ID > 0 )
	{
		$str .= '<div class="ReplyBox"><div class="Box"><table><tr><td style="width:37px">';
		$str .= '<div class="image">';
		if( $userimage )
		{
			$str .= $userimage;
		}
		$str .= '</div></td><td>';
		$str .= '<div class="reply">';
		//$str .= '<input id="ReplyContent_' . $p->ID . '" placeholder="Write a comment...">';
		$str .= '<div id="ReplyContent_' . $p->ID . '" class="textarea post" onkeyup="IsEdit(1,this)" contenteditable="true" placeholder="Write a comment..."></div>';
		$str .= '<button type="button" onclick="sendReply(\'' . $p->ID . '\', ge( \'ReplyContent_' . $p->ID . '\' ), false, event )">REPLY</button></div>';
		$str .= '</td></tr></table></div></div>';
	}
}

$str .= '</div>';
$str .= '</div>';

// Set status IsRead on user notify
if( $focusid > 0 && $focusid == $p->ID )
{
	IsRead( $p->ID );
}

?>
