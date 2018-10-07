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

$maxWidth = 1280;
$maxHeight = 960;

if( $p->Type && in_array( $p->Type, array( 'post', 'vote', 'event' ) ) && $p->ID > 0 && isset( $comments[$p->ID] ) )
{
	$posts = $comments[$p->ID];
	$posts[] = $p;
}
else
{
	$posts = array( $p );
}

if( $posts )
{
	$po = false; $co = array(); $images = array();
	
	// TODO: CLEAN MORE CODE !!!!!
	
	foreach( $posts as $pos )
	{
		if( $pos->ParentID > 0 && $pos->Type == 'comment' )
		{
			// Decode json
			$pos->SeenBy = is_string( $pos->SeenBy ) ? json_obj_decode( $pos->SeenBy, 'array' ) : false;
			$pos->RateDownBy = is_string( $pos->RateDownBy ) ? json_obj_decode( $pos->RateDownBy, 'array' ) : false;
			$pos->RateUpBy = is_string( $pos->RateUpBy ) ? json_obj_decode( $pos->RateUpBy, 'array' ) : false;
			$pos->Data = is_string( $pos->Data ) ? json_decode( $pos->Data ) : false;
		}
		
		if ( ( $_REQUEST['closed'] && strstr( $_REQUEST['closed'], $pos->ID ) ) || ( isset( $pos->GroupSettings->WallMode ) && $pos->GroupSettings->WallMode == 'forum' ) )
		{
			$pos->ClosedMode = 1;
			
			if ( !isset( $closedmode[$pos->ID] ) )
			{
				$closedmode[$pos->ID] = $pos->ID;
			}
		}
		
		/*if( $im[$pos->Image] )
		{
			$pos->PosterImage = '<div style="background-image:url(\'' . $im[$pos->Image]->DiskPath . '\');"></div>';
			$pos->ReplyImage = '<div style="background-image:url(\'' . $im[$pos->Image]->DiskPath . '\');"></div>';
		}*/
		
		if( $pos->Image > 0 && !$images[$pos->Image] )
		{
			$images[$pos->Image] = $pos->Image;
		}
		
		if( $pos->SenderID == $webuser->ContactID || ( $parent->module == 'profile' && $webuser->ContactID == $parent->cuser->ContactID ) || IsSystemAdmin() )
		{
			$pos->HasAccess = true;
		}
		
		if( $focusid > 0 && $focusid == $pos->ID )
		{
			$pos->IsFocus = true;
		}
		
		if( strtolower( $parent->folder->MainName ) == 'profile' && strtolower( $parent->module ) == 'main' && $pos->IsGroup )
		{
			$pos->Target = 'group';
		}
		else if( strtolower( $parent->folder->MainName ) == 'profile' && strtolower( $parent->module ) == 'main' && !$pos->IsGroup && $pos->Receiver && ( $pos->ReceiverID != $pos->SenderID ) )
		{
			$pos->Target = 'profile';
		}
		
		$pos->Sender = ( GetUserDisplayname( $pos->SenderID ) ? GetUserDisplayname( $pos->SenderID ) : $pos->Name );
		$pos->Receiver = ( GetUserDisplayname( $pos->ReceiverID ) ? GetUserDisplayname( $pos->ReceiverID ) : $pos->User_Name );
		
		if( is_array( $pos->RateDownBy ) && is_array( $pos->RateUpBy ) )
		{
			$sbstr = array(); $i = 0; $up = 0; $dn = 0;
			
			$rating = ( count( $pos->RateDownBy ) > count( $pos->RateUpBy ) ? $pos->RateDownBy : $pos->RateUpBy );
			
			foreach( $rating as $k=>$uid )
			{
				if( isset( $pos->RateDownBy[$k] ) )
				{
					$sbstr[] = '- 1 ' . GetUserDisplayname( $pos->RateDownBy[$k] );
					if( $pos->RateDownBy[$k] == $user->ContactID )
					{
						$pos->YourRate = 0;
					}
					$i++; $dn++;
				}
				if( isset( $pos->RateUpBy[$k] ) )
				{
					$sbstr[] = '+1 ' . GetUserDisplayname( $pos->RateUpBy[$k] );
					if( $pos->RateUpBy[$k] == $user->ContactID )
					{
						$pos->YourRate = 1;
					}
					$i++; $up++;
				}
			}
			
			$pos->RateList = $sbstr;
			$pos->RateAmount = $i;
			$pos->RatePercent = ( $i > 0 ? ( ( $up / $i * 100 ) ) : '0' );
		}
		
		if( $pos->SenderID == $webuser->ContactID && is_array( $pos->SeenBy ) )
		{
			$sbstr = array(); $i = 0;
			
			foreach( $pos->SeenBy as $uid )
			{
				$sbstr[] = GetUserDisplayname( $uid );
				$i++;
			}
			
			$pos->SeenList = $sbstr;
			$pos->SeenAmount = $i;
		}
		
		
		// TODO: MAKE DATA CONTENT EASIER !!! ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
		// Support old method
		if( is_array( $pos->Data ) )
		{
			$obj = new stdClass();
			$obj->LibraryFiles = $pos->Data;
			$obj->Media = 'album';
			$pos->Data = $obj;
		}
		
		$pos->ParsedData = '';
		
		if( $pos->Data && is_object( $pos->Data ) )
		{
			$type = ''; $cont = '';
			
			if( $pos->Data->LibraryFiles )
			{
				$pos->Data->Media = 'album';
			}
			
			switch( $pos->Data->Media )
			{
				// Audio ---------------------------------------------------------------------------------------------------------------------
				case 'audio':
					$type = 'Audio';
					$cont = WallMetaData (
						'audio',
						$pos->Data->Url,
						false,
						false,
						$pos->Data->Title,
						$pos->Data->Leadin,
						$pos->Data->Domain,
						false,
						false
					);
					break;
				// Video ---------------------------------------------------------------------------------------------------------------------
				case 'video':
					$type = 'Video';
					$cont = WallMetaData (
						'video',
						$pos->Data->Url,
						( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false/*( $pos->Data->Thumb ? $pos->Data->Thumb : $pos->Data->FilePath )*/ ), 
						false,
						$pos->Data->Title,
						$pos->Data->Leadin,
						$pos->Data->Domain,
						$pos->Data->FileWidth,
						$pos->Data->FileHeight
					);
					break;
				// Youtube -------------------------------------------------------------------------------------------------------------------
				case 'youtube':
					$type = 'Video';
					$cont = WallMetaData (
						'video',
						$pos->Data->Url,
						( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false/*( $pos->Data->Thumb ? $pos->Data->Thumb : $pos->Data->FilePath )*/ ), 
						false,
						$pos->Data->Title,
						$pos->Data->Leadin,
						$pos->Data->Domain,
						$pos->Data->FileWidth,
						$pos->Data->FileHeight,
						'youtube'
					);
					break;
				// Vimeo ---------------------------------------------------------------------------------------------------------------------
				case 'vimeo':
					$type = 'Video';
					$cont = WallMetaData (
						'video',
						$pos->Data->Url,
						( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false/*( $pos->Data->Thumb ? $pos->Data->Thumb : $pos->Data->FilePath )*/ ), 
						false,
						$pos->Data->Title,
						$pos->Data->Leadin,
						$pos->Data->Domain,
						$pos->Data->FileWidth,
						$pos->Data->FileHeight,
						'vimeo'
					);
					break;
				// Livestream ----------------------------------------------------------------------------------------------------------------
				case 'livestream':
					$type = 'Video';
					$cont = WallMetaData (
						'video',
						$pos->Data->Url,
						( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false/*( $pos->Data->Thumb ? $pos->Data->Thumb : $pos->Data->FilePath )*/ ), 
						false,
						$pos->Data->Title,
						$pos->Data->Leadin,
						$pos->Data->Domain,
						$pos->Data->FileWidth,
						$pos->Data->FileHeight,
						'livestream'
					);
					break;
				// Spotify ---------------------------------------------------------------------------------------------------------------------
				case 'spotify':
					$type = 'Audio';
					$cont = WallMetaData (
						'audio',
						$pos->Data->Url,
						( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false/*( $pos->Data->Thumb ? $pos->Data->Thumb : $pos->Data->FilePath )*/ ), 
						false,
						$pos->Data->Title,
						$pos->Data->Description,
						$pos->Data->Domain,
						$pos->Data->FileWidth,
						$pos->Data->FileHeight,
						'spotify'
					);
					break;
				// File ----------------------------------------------------------------------------------------------------------------------
				case 'file':
					$type = 'File';
					$cont = WallMetaData (
						'file',
						$pos->Data->Url,
						false,
						false,
						$pos->Data->Title,
						$pos->Data->Leadin,
						$pos->Data->Domain,
						false,
						false
					);
					break;
				// Image ----------------------------------------------------------------------------------------------------------------------
				case 'image':
					$type = 'Image';
					$cont = WallMetaData (
						'picture',
						$pos->Data->Url,
						( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false/*$pos->Data->FilePath*/ ), 
						false,
						$pos->Data->Title,
						$pos->Data->Leadin,
						$pos->Data->Domain,
						$pos->Data->FileWidth,
						$pos->Data->FileHeight
					);
					break;
				// Remote site/meta data ----------------------------------------------------------------------------------------------------------
				case 'site':
					$type = 'Site';
					$cont = WallMetaData (
						'site',
						$pos->Data->Url,
						( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false/*$pos->Data->FilePath*/ ), 
						false,
						$pos->Data->Title,
						$pos->Data->Leadin,
						$pos->Data->Domain,
						$pos->Data->FileWidth,
						$pos->Data->FileHeight
					);
					break;
				// Album ( File / Image ) --------------------------------------------------------------------------------------------------------
				case 'album':
					$type = 'Album';
					$cont = WallAlbum( $pos->Data->LibraryFiles, $pos->ID, $imgs, $parent );
					break;
				// Library ------------------------------------------------------------------------------------------------------------------------
				case 'library':
					$type = 'Library';
					$cont = embedLibrary( $pos->Data->Url, '100%', '640' );
					break;
			}
			
			if( $type && $cont )
			{
				$pos->ParsedData .= '<div class="html"><div class="ParseContent' . ( $type ? ( ' ' . $type ) : '' ) . '">';
				$pos->ParsedData .= $cont;
				$pos->ParsedData .= '<div class="clearboth" style="clear:both"></div>';
				$pos->ParsedData .= '</div></div>';
			}
			
		}
		
		// Data Content: End --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
		
		// Data Content: Start -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
		// TODO: Commented out because of new method that is being tested, when test is complete delete this.
		
		if( $pos->Data && !$pos->HTML && ( ( !is_array( $pos->Data ) && $pos->Data->Media != 'album' ) || ( is_array( $pos->Data ) && $pos->Data[0]->MediaType != 'album' ) ) )
		{
			//
		}
		else if( $pos->HTML )
		{
			// TODO: Check text encoding on $p->HTML - which sometimes goes MAD with weird characters
			$pos->ParsedData .= StrReplaceByAttribute( $pos->HTML, 'replace=""', 'onclick="embedVideo(this,\'743\',\'420\')"' );
		}
		
		// Data Content: End ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
		
		if( $pos->ParentID > 0 && $pos->Type == 'comment' )
		{
			$co[] = $pos;
		}
		else
		{
			$po = $pos;
		}
	}
	
	if( $co ) $po->Comments = $co;
	
	$p = $po;
}

$isedit = 'onmouseover="IsEdit(1,this,\'' . $p->ID . '\')" onmouseout="IsEdit(false,this,\'' . $p->ID . '\')"';

// --- Output ------------------------------------------------------------------------------------------------------------------------

if( $_POST['mid'] == $p->ID )
{
	$str = '';
}
else
{
	$str .= '<div class="messagebox' . ( $p->IsFocus ? ' focus' : '' ) . ( $p->Component ? ( ' ' . $p->Component ) : '' ) . ( $p->ClosedMode > 0 ? ' closed' : '' ) . '" id="MessageID_' . $p->ID . '" ' . $isedit . ' tabindex="0">';
}

// --- Wall posts --------------------------------------------------------------------------------------------------------------------
$str .= 	'<div class="post" id="MessagePost_' . $p->ID . '">';
// --- Posted ------------------------------------------------------------------------------------------------------------------------
$str .= 		'<div class="posted" onclick="SwitchWallMode(\'' . $p->ID . '\')">';
// --- Avatar ------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="avatar">';
$str .= 				'<a href="'/* . $path*/ . $p->Name . '">';
if( $imgs[$p->Image] )
{
	$str .= 				'<div style="background-image:url(\'' . $imgs[$p->Image]->DiskPath . '100x100\');"></div>';
}
else
{
	$str .= 				'<div style="background-image:url(\'admin/gfx/arenaicons/user_johndoe_128.png\');"></div>';
}
$str .= 				'</a>';
$str .= 			'</div>';
// --- Nickname ----------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="nickname">';
$str .= 				'<a class="sender" href="'/* . $path*/ . $p->Name . '">' . $p->Sender . '</a>';
switch( $p->Target )
{
	case 'group':
		$str .= 		'<span class="seperator"> &#10148; </span><a class="receiver" href="'/* . $path*/ . 'groups/' . $p->SBC_ID . '/wall/">' . $p->SBC_Name . '</a>';
		break;
	
	case 'profile':
		$str .= 		'<span class="seperator"> &#10148; </span><a class="receiver" href="'/* . $path*/ . $p->User_Name . '">' . $p->Receiver . '</a>';
		break;
}
$str .= 			'</div>';
// --- Edit --------------------------------------------------------------------------------------------------------------------------
if( $p->HasAccess )
{
	$str .= 		'<div class="edit">';
	//$str .= 			'<div class="options" onclick="WallOptions(this,\'' . $p->ID . '\');return cancelBubble(event)"></div>';
	$str .= 			'<div class="options" onclick="WallOptions(this);return cancelBubble(event)"></div>';
	
	$str .= '			<div class="walloptions" onclick="WallOptions(this);return cancelBubble(event)">';
	$str .= '				<div class="toparrow"></div>';
	$str .= '				<div class="inner">';
	
	$str .= '					<ul>';
	$str .= '						<li><div onclick="embedPostEditor(\'' . $p->ID . '\')"><span>' . i18n( 'i18n_Edit' ) . '</span></div></li>';
	$str .= '						<li><div onclick="deleteWallContent(' . $p->ID . ')"><span>' . i18n( 'i18n_Delete' ) . '</span></div></li>';
	$str .= '					</ul>';
	
	$str .= '				</div>';
	$str .= '			</div>';
	
	$str .= 		'</div>';
}
// --- Bookmark ----------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="bookmark' . ( isset( $bmarks ) && is_array( $bmarks ) && in_array( $p->ID, $bmarks ) ? ' marked' : '' ) . '" onclick="Bookmark(this,\'' . $p->ID . '\');return cancelBubble(event)">';
$str .= 				'<div></div>';
$str .= 			'</div>';
// --- Date --------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="date" datetime="' . $p->Date . '">' . TimeToHuman( $p->Date ) . '</div>';
// --- Access ------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="access">';
if( $p->HasAccess )
{
	$str .= 			'<select onclick="return cancelBubble(event)" onchange="UpdateAccess(\'' . $p->ID . '\',this.value)">';
	$str .= 				'<option title="' . ( $p->IsGroup ? i18n( 'i18n_Members' ) : i18n( 'i18n_Public' ) ) . '" value="0"' . ( $p->Access == 0 ? ' selected="selected"' : '' ) . '>' . ( !strstr( $parent->agent, 'mobile' ) ? ( $p->IsGroup ? i18n( 'i18n_Members' ) : i18n( 'i18n_Public' ) ) : '0' ) . '</option>';
	$str .= 				'<option title="' . i18n( 'i18n_Contacts' ) . '" value="1"' . ( $p->Access == 1 ? ' selected="selected"' : '' ) . '>' . ( !strstr( $parent->agent, 'mobile' ) ? i18n( 'i18n_Contacts' ) : '1' ) . '</option>';
	$str .= 				'<option title="' . i18n( 'i18n_Only Me' ) . '" value="2"' . ( $p->Access == 2 ? ' selected="selected"' : '' ) . '>' . ( !strstr( $parent->agent, 'mobile' ) ? i18n( 'i18n_Only Me' ) : '2' ) . '</option>';
	//$str .= 				'<option title="' . i18n( 'i18n_Custom' ) . '" value="3"' . ( $p->Access == 3 ? ' selected="selected"' : '' ) . '>' . ( !strstr( $parent->agent, 'mobile' ) ? i18n( 'i18n_Custom' ) : '3' ) . '</option>';
	if( isset( $parent->access->IsAdmin ) )
	{
		$str .= 				'<option title="' . i18n( 'i18n_Admin' ) . '" value="4"' . ( $p->Access == 4 ? ' selected="selected"' : '' ) . '>' . ( !strstr( $parent->agent, 'mobile' ) ? i18n( 'i18n_Admin' ) : '4' ) . '</option>';
	}
	$str .= 			'</select>';
	
	/*$arr = array();
	
	$opt = array();
	$opt['0'] = 'Public';
	$opt['1'] = 'Contacts';
	$opt['2'] = 'Only Me';
	//$opt['3'] = 'Custom';
	if( isset( $parent->access->IsAdmin ) )
	{
		$opt['4'] = 'Admin';
	}
	
	$s = 0;
	
	foreach( $opt as $k=>$v )
	{
		$obj = new stdClass();
		$obj->name = $v;
		$obj->value = $k;
		$obj->selected = ( $p->Access == $k || ( !$p->Access && $s == 0 ) ? true : false );
		
		$arr[] = $obj;
		
		$s++;
	}
	//die( print_r( $arr,1 ) );
	$str .= renderCustomSelect( $arr, false, false, 'onchange="UpdateAccess(\'' . $p->ID . '\',this.value)"' );*/
}
$str .= 			'</div>';
// --- Comments ----------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="comments">' . ( $p->Comments ? count( $p->Comments ) : '0' ) . '</div>';
// --- Clear -------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="clearboth"></div>';
// --- Posted end --------------------------------------------------------------------------------------------------------------------
$str .= 		'</div>';
// --- Content -----------------------------------------------------------------------------------------------------------------------
if( $p->Message )
{
	$str .= 	'<div class="content" id="MessageContent_' . $p->ID . '">' . convertToTags( $p->Message ) . '</div>';
}
// --- Editormode -------------------------------------------------------------------------------------------------------------
if( $p->HasAccess )
{
	$str .= 	'<div class="editor post hidden" id="EditMode_' . $p->ID . '">';
	$str .= 	'	<div class="text">';
	$str .= 	'		<div contenteditable="true" id="EditID_' . $p->ID . '" class="textarea post">' . convertToTags( $p->Message ) . '</div>';
	$str .= 	'	</div>';
	$str .= 	'	<div class="toolbar">';
	$str .= 	'		<div class="publish">';
	$str .= 	'			<button type="button" onclick="EditWallPost(ge(\'EditID_'.$p->ID.'\'),\''.$p->ID.'\',\'post\')" class="save_btn">SAVE</button>';
	$str .= 	'			<button type="button" onclick="CloseEditMode('.$p->ID.')" class="cancel_btn">CANCEL</button>';
	$str .= 	'		</div>';
	$str .= 	'	</div>';
	$str .= 	'</div>';
}
// --- ParsedData --------------------------------------------------------------------------------------------------------------------
if( $p->ParsedData )
{
	$str .= $p->ParsedData;
}
// --- Buttons -----------------------------------------------------------------------------------------------------------------------
$str .= 		'<div class="buttons">';
// --- Reply -------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="reply">';
if( $parent->webuser->ID > 0 )
{
	//$str .= 			'<button onclick="replyToPost(\'' . $p->ID . '\')">' . i18n( 'i18n_comment' ) . '</button>';
	$str .= 			'<button onclick="sharePost(\'' . $p->ID . '\')">' . i18n( 'i18n_share' ) . '</button>';
}
$str .= 			'</div>';
// --- Vote --------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="vote">';
if( $parent->webuser->ID > 0 )
{
	$str .= 			'<span onclick="voteComment(\'' . $p->ID . '\',\'like\',this)" class="voteimg up' . ( isset( $p->YourRate ) && $p->YourRate == 1 ? ' marked' : '' ) . '" title="Like"></span>';
	$str .= 			'<span onclick="voteComment(\'' . $p->ID . '\',\'dislike\',this)" class="voteimg down' . ( isset( $p->YourRate ) && $p->YourRate == 0 ? ' marked' : '' ) . '" title="Dislike"></span>';
}
$str .= 			'</div>';
// --- Rate --------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="rate">';
$str .= 				'<div class="ratebox">';
$str .= 					'<div style="width: ' . ( $p->RatePercent > 0 ? ( $p->RatePercent . '%' ) : '0%' ) . ';"></div>';
$str .= 				'</div>';
if( $p->RateAmount > 0 )
{
	$str .= 			'<span class="amount" onmouseover="tooltips(this,\'open\')" onmouseout="tooltips(this,\'close\')">(' . $p->RateAmount . ')</span>';
	$str .= 			'<div class="tooltips"><div class="bottomarrow"></div><div class="inner"><ul><li>' . implode( '</li><li>', $p->RateList ) . '</li></ul></div></div>';
}
$str .= 			'</div>';
// --- Seen --------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="seen">';
if( $p->SeenAmount > 0 )
{
	$str .= 			'<span class="amount" onmouseover="tooltips(this,\'open\')" onmouseout="tooltips(this,\'close\')">' . i18n( 'i18n_Seen by' ) . ' ' . $p->SeenAmount . '</span>';
	$str .= 			'<div class="tooltips"><div class="bottomarrow"></div><div class="inner"><ul><li>' . implode( '</li><li>', $p->SeenList ) . '</li></ul></div></div>';
}
$str .= 			'</div>';
// --- Clear -------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="clearboth"></div>';
// --- Buttons end -------------------------------------------------------------------------------------------------------------------
$str .= 		'</div>';
// --- Clear -------------------------------------------------------------------------------------------------------------------------
$str .= 		'<div class="clearboth"></div>';
// --- Post end ----------------------------------------------------------------------------------------------------------------------
$str .= 	'</div>';

if( $p->Comments )
{
	foreach( $p->Comments as $c )
	{
		// --- Comment posts ---------------------------------------------------------------------------------------------------------
		$str .= 	'<div class="comment" id="MessagePost_' . $c->ID . '">';
		// --- Edit ------------------------------------------------------------------------------------------------------------------
		if( $c->HasAccess )
		{
			$str .= 	'<div class="edit">';
			//$str .= 		'<div class="options" onclick="WallOptions(this,\'' . $c->ID . '\')"></div>';
			$str .= 			'<div class="options" onclick="WallOptions(this);return cancelBubble(event)"></div>';
			
			$str .= '			<div class="walloptions" onclick="WallOptions(this);return cancelBubble(event)">';
			$str .= '				<div class="toparrow"></div>';
			$str .= '				<div class="inner">';
			
			$str .= '					<ul>';
			$str .= '						<li><div onclick="embedPostEditor(\'' . $c->ID . '\')"><span>' . i18n( 'i18n_Edit' ) . '</span></div></li>';
			$str .= '						<li><div onclick="deleteWallContent(' . $c->ID . ')"><span>' . i18n( 'i18n_Delete' ) . '</span></div></li>';
			$str .= '					</ul>';
			
			$str .= '				</div>';
			$str .= '			</div>';
			
			$str .= 		'</div>';
		}
		// --- Posted -----------------------------------------------------------------------------------------------------------------
		$str .= 		'<div class="posted">';
		// --- Avatar -----------------------------------------------------------------------------------------------------------------
		$str .= 			'<div class="avatar">';
		$str .= 				'<a href="'/* . $path*/ . $c->Name . '">';
		if( $imgs[$c->Image] )
		{
			$str .= 				'<div style="background-image:url(\'' . $imgs[$c->Image]->DiskPath . '100x100\');"></div>';
		}
		else
		{
			$str .= 				'<div style="background-image:url(\'admin/gfx/arenaicons/user_johndoe_128.png\');"></div>';
		}
		$str .= 				'</a>';
		$str .= 			'</div>';
		// --- Nickname ---------------------------------------------------------------------------------------------------------------
		$str .= 			'<div class="nickname">';
		$str .= 				'<a class="sender" href="'/* . $path*/ . $c->Name . '">' . $c->Sender . '</a>';
		$str .= 			'</div>';
		// --- Rate -------------------------------------------------------------------------------------------------------------------
		$str .= 			'<div class="rate">';
		$str .= 				'<div class="ratebox">';
		$str .= 					'<div style="width: ' . ( $c->RatePercent > 0 ? ( $c->RatePercent . '%' ) : '0%' ) . ';"></div>';
		$str .= 				'</div>';
		if( $c->RateAmount > 0 )
		{
			$str .= 			'<span class="amount" onmouseover="tooltips(this,\'open\')" onmouseout="tooltips(this,\'close\')">(' . $c->RateAmount . ')</span>';
			$str .= 			'<div class="tooltips"><div class="bottomarrow"></div><div class="inner"><ul><li>' . implode( '</li><li>', $c->RateList ) . '</li></ul></div></div>';
		}
		$str .= 			'</div>';
		// --- Date -------------------------------------------------------------------------------------------------------------------
		$str .= 			'<div class="date" datetime="' . $c->Date . '">' . TimeToHuman( $c->Date ) . '</div>';
		// --- Vote -------------------------------------------------------------------------------------------------------------------
		$str .= 			'<div class="vote">';
		if( $parent->webuser->ID > 0 )
		{
			$str .= 			'<span onclick="voteComment(\'' . $c->ID . '\',\'like\',this,\'' . $p->ID . '\')" class="voteimg up' . ( isset( $c->YourRate ) && $c->YourRate == 1 ? ' marked' : '' ) . '" title="Like"></span>';
			$str .= 			'<span onclick="voteComment(\'' . $c->ID . '\',\'dislike\',this,\'' . $p->ID . '\')" class="voteimg down' . ( isset( $c->YourRate ) && $c->YourRate == 0 ? ' marked' : '' ) . '" title="Dislike"></span>';
		}
		$str .= 			'</div>';
		// --- Clear ------------------------------------------------------------------------------------------------------------------
		$str .= 			'<div class="clearboth"></div>';
		// --- Posted end -------------------------------------------------------------------------------------------------------------
		$str .= 		'</div>';
		// --- Content ----------------------------------------------------------------------------------------------------------------
		if( $c->Message )
		{
			$str .= 	'<div class="content" id="MessageContent_' . $c->ID . '">' . convertToTags( $c->Message ) . '</div>';
		}
		// --- Editormode -------------------------------------------------------------------------------------------------------------
		if( $c->HasAccess )
		{
			$str .= '<div class="editor post hidden" id="EditMode_' . $c->ID . '">';
			$str .= '	<div class="text">';
			$str .= '		<div contenteditable="true" id="EditID_' . $c->ID . '" class="textarea post">' . convertToTags( $c->Message ) . '</div>';
			$str .= '	</div>';
			$str .= '	<div class="toolbar">';
			$str .= '		<div class="publish">';
			$str .= '			<button type="button" onclick="EditWallPost(ge(\'EditID_'.$c->ID.'\'),\''.$c->ID.'\',\'comment\')" class="save_btn">SAVE</button>';
			$str .= '			<button type="button" onclick="CloseEditMode('.$c->ID.')" class="cancel_btn">CANCEL</button>';
			$str .= '		</div>';
			$str .= '	</div>';
			$str .= '</div>';
		}
		// --- ParsedData -------------------------------------------------------------------------------------------------------------
		if( $c->ParsedData )
		{
			$str .= $c->ParsedData;
		}
		// --- Buttons ----------------------------------------------------------------------------------------------------------------
		$str .= 		'<div class="buttons">';
		// --- Clear -------------------------------------------------------------------------------------------------------------------
		$str .= 			'<div class="clearboth"></div>';
		// --- Buttons end -------------------------------------------------------------------------------------------------------------
		$str .= 		'</div>';
		// --- Clear -------------------------------------------------------------------------------------------------------------------
		$str .= 		'<div class="clearboth"></div>';
		// --- Comment end -------------------------------------------------------------------------------------------------------------
		$str .= 	'</div>';
		
		// Set status IsRead on user notify
		if( $p->IsFocus )
		{
			IsRead( $c->ID );
		}
		
	}
}



if( $parent->webuser->ID > 0 )
{
	// --- Editorbox -------------------------------------------------------------------------------------------------------------
	$str .= 	'<div class="editorbox hidden" id="EditorBox_' . $p->ID . '">';
	// --- Edit ------------------------------------------------------------------------------------------------------------------
	//$str .= 	'<div class="edit">';
	//$str .= 		'<div class="options" onclick="WallOptions(this,\'' . $c->ID . '\')"></div>';
	//$str .= 	'</div>';
	// --- Posted -----------------------------------------------------------------------------------------------------------------
	$str .= 		'<div class="posted">';
	// --- Avatar -----------------------------------------------------------------------------------------------------------------
	$str .= 			'<div class="avatar">';
	$str .= 				'<a href="' . $username . '">' . $userimage . '</a>';
	$str .= 			'</div>';
	// --- Nickname ---------------------------------------------------------------------------------------------------------------
	$str .= 			'<div class="nickname">';
	$str .= 				'<a class="sender" href="' . $username . '">' . $userdisplay . '</a>';
	$str .= 			'</div>';
	// --- Rate -------------------------------------------------------------------------------------------------------------------
	$str .= 			'<div class="rate">';
	$str .= 				'<div class="ratebox">';
	$str .= 					'<div style="width: 0%;"></div>';
	$str .= 				'</div>';
	$str .= 			'</div>';
	// --- Date -------------------------------------------------------------------------------------------------------------------
	$str .= 			'<div class="date" datetime="' . date( 'Y-m-d H:i:s' ) . '">' . TimeToHuman( date( 'Y-m-d H:i:s' ) ) . '</div>';
	// --- Vote -------------------------------------------------------------------------------------------------------------------
	$str .= 			'<div class="vote">';
	$str .= 			'<span class="voteimg up" title="Like"></span>';
	$str .= 			'<span class="voteimg down" title="Dislike"></span>';
	$str .= 			'</div>';
	// --- Clear ------------------------------------------------------------------------------------------------------------------
	$str .= 			'<div class="clearboth"></div>';
	// --- Posted end -------------------------------------------------------------------------------------------------------------
	$str .= 		'</div>';
	// --- Content ----------------------------------------------------------------------------------------------------------------
	$str .= 	'<div class="content"></div>';
	// --- Buttons ----------------------------------------------------------------------------------------------------------------
	//$str .= 		'<div class="buttons">';
	// --- Clear -------------------------------------------------------------------------------------------------------------------
	//$str .= 			'<div class="clearboth"></div>';
	// --- Buttons end -------------------------------------------------------------------------------------------------------------
	//$str .= 		'</div>';
	// --- Clear -------------------------------------------------------------------------------------------------------------------
	$str .= 		'<div class="clearboth"></div>';
	// --- Editorbox end -----------------------------------------------------------------------------------------------------------
	$str .= 	'</div>';
	// --- Pendingreply ------------------------------------------------------------------------------------------------------------
	$str .= 	'<div class="pendingreply" id="EditorReply_' . $p->ID . '"></div>';
}



if( $parent->webuser->ID > 0 )
{
	// --- Replybox --------------------------------------------------------------------------------------------------------------------
	$str .= 	'<div class="replybox' . ( $p->Comments ? '' : ' closed' ) . '">';
	// --- Avatar ----------------------------------------------------------------------------------------------------------------------
	$str .= 		'<div class="avatar">' . $userimage . '</div>';
	// --- Reply -----------------------------------------------------------------------------------------------------------------------
	$str .= 		'<div class="reply">';
	// --- Textarea --------------------------------------------------------------------------------------------------------------------
	$str .= 			'<div id="ReplyContent_' . $p->ID . '" class="textarea post" onkeyup="IsEdit(1,this,\'' . $p->ID . '\')" onclick="IsFocus(\'' . $p->ID . '\')" contenteditable="true" placeholder="' . i18n( 'i18n_Write a comment' ) . '..."></div>';
	// --- Button ----------------------------------------------------------------------------------------------------------------------
	$str .= 			'<button type="button" onclick="sendReply(\'' . $p->ID . '\',ge(\'ReplyContent_' . $p->ID . '\'),false,event)">' . i18n( 'i18n_Reply' ) . '</button>';
	// --- Reply end --------------------------------------------------------------------------------------------------------------------
	$str .= 		'</div>';
	// --- Clear ------------------------------------------------------------------------------------------------------------------------
	$str .= 		'<div class="clearboth"></div>';
	// --- Replybox end -----------------------------------------------------------------------------------------------------------------
	$str .= 	'</div>';
}

// --- Clear ----------------------------------------------------------------------------------------------------------------------------
$str .= '<div class="clearboth"></div>';

// --- Output end -----------------------------------------------------------------------------------------------------------------------

if( $_POST['mid'] != $p->ID )
{
	$str .= '</div>';
}

// Set status IsRead on user notify
if( $p->IsFocus )
{
	IsRead( $p->ID );
}

?>
