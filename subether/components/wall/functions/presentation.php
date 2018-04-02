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

$maxWidth = 1280;
$maxHeight = 960;

$posts = array( $p );

if( $posts )
{
	$po = false; $co = array(); $images = array();
	
	// TODO: CLEAN MORE CODE !!!!!
	
	foreach( $posts as $pos )
	{
		if( $pos->ParentID > 0 && $pos->Type == 'comment' )
		{
			// Decode json
			$pos->SeenBy = is_string( $pos->SeenBy ) ? json_decode( $pos->SeenBy ) : false;
			$pos->RateDownBy = is_string( $pos->RateDownBy ) ? json_decode( $pos->RateDownBy ) : false;
			$pos->RateUpBy = is_string( $pos->RateUpBy ) ? json_decode( $pos->RateUpBy ) : false;
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
					//$cont = WallMetaData (
					//	'audio',
					//	$pos->Data->Url,
					//	false,
					//	false,
					//	$pos->Data->Title,
					//	$pos->Data->Leadin,
					//	$pos->Data->Domain,
					//	false,
					//	false
					//);
					$cont = embedAudio( $pos->Data->Url, $maxWidth, $maxHeight, false, false, false );
					break;
				// Video ---------------------------------------------------------------------------------------------------------------------
				case 'video':
					$type = 'Video';
					//$cont = WallMetaData (
					//	'video',
					//	$pos->Data->Url,
					//	( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false ), 
					//	false,
					//	$pos->Data->Title,
					//	$pos->Data->Leadin,
					//	$pos->Data->Domain,
					//	$pos->Data->FileWidth,
					//	$pos->Data->FileHeight
					//);
					$cont = embedVideo( $pos->Data->Url, $maxWidth, $maxHeight, false, false, false, false );
					break;
				// Youtube -------------------------------------------------------------------------------------------------------------------
				case 'youtube':
					$type = 'Video';
					//$cont = WallMetaData (
					//	'video',
					//	$pos->Data->Url,
					//	( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false ), 
					//	false,
					//	$pos->Data->Title,
					//	$pos->Data->Leadin,
					//	$pos->Data->Domain,
					//	$pos->Data->FileWidth,
					//	$pos->Data->FileHeight,
					//	'youtube'
					//);
					$cont = embedYoutube( $pos->Data->Url, $maxWidth, $maxHeight, false, false, false );
					break;
				// Vimeo ---------------------------------------------------------------------------------------------------------------------
				case 'vimeo':
					$type = 'Video';
					//$cont = WallMetaData (
					//	'video',
					//	$pos->Data->Url,
					//	( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false ), 
					//	false,
					//	$pos->Data->Title,
					//	$pos->Data->Leadin,
					//	$pos->Data->Domain,
					//	$pos->Data->FileWidth,
					//	$pos->Data->FileHeight,
					//	'vimeo'
					//);
					$cont = embedVimeo( $pos->Data->Url, $maxWidth, $maxHeight, false, false, false );
					break;
				// Livestream ----------------------------------------------------------------------------------------------------------------
				case 'livestream':
					$type = 'Video';
					//$cont = WallMetaData (
					//	'video',
					//	$pos->Data->Url,
					//	( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false ), 
					//	false,
					//	$pos->Data->Title,
					//	$pos->Data->Leadin,
					//	$pos->Data->Domain,
					//	$pos->Data->FileWidth,
					//	$pos->Data->FileHeight,
					//	'livestream'
					//);
					$cont = embedLivestream( $pos->Data->Url, $maxWidth, $maxHeight, false, false, false );
					break;
				// Spotify ---------------------------------------------------------------------------------------------------------------------
				case 'spotify':
					$type = 'Audio';
					//$cont = WallMetaData (
					//	'audio',
					//	$pos->Data->Url,
					//	( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false ), 
					//	false,
					//	$pos->Data->Title,
					//	$pos->Data->Description,
					//	$pos->Data->Domain,
					//	$pos->Data->FileWidth,
					//	$pos->Data->FileHeight,
					//	'spotify'
					//);
					$cont = embedSpotify( $pos->Data->Url, $maxWidth, $maxHeight, false, false, false, false );
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
						( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false ), 
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
						( isset( $imgs[$pos->Data->FileID] ) ? $imgs[$pos->Data->FileID]->DiskPath : false ), 
						false,
						$pos->Data->Title,
						$pos->Data->Leadin,
						$pos->Data->Domain,
						$pos->Data->FileWidth,
						$pos->Data->FileHeight
					);
					break;
				// Album ( File / Image ) ----------------------------------------------------------------------------------------------------
				case 'album':
					$type = 'Album';
					$cont = WallAlbum( $pos->Data->LibraryFiles, $pos->ID, $imgs );
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
		
		$po = $pos;
	}
	
	$p = $po;
}

// --- Output ------------------------------------------------------------------------------------------------------------------------

//die( print_r( $p,1 ) . ' --' );

$IsMedia = false;

if( $p->ParsedData &&
( in_array( $p->Data->Media, array( 'audio', 'video', 'youtube', 'vimeo', 'livestream' , 'spotify', 'image' ) ) ||
( $p->Data->Media == 'site' && $p->Data->FileWidth >= 743 ) ||
( $p->Data->Media == 'album' && isset( $p->Data->LibraryFiles[0]->MediaType ) && in_array( $p->Data->LibraryFiles[0]->MediaType, array( 'audio', 'video' ) ) ) ||
( $p->Data->Media == 'album' && isset( $p->Data->LibraryFiles[0]->MediaType ) && in_array( $p->Data->LibraryFiles[0]->MediaType, array( 'album', 'image' ) ) && $p->Data->LibraryFiles[0]->FileWidth >= 743 ) ) )
{
	$IsMedia = true;
}

$sstr = '';

if( $_POST['mid'] == $p->ID )
{
	$sstr = '';
}
else
{
	$sstr .= '<div class="messagebox' . ( ( $IsMedia && !isset( $curnt['media'] ) ) || ( !$IsMedia && !isset( $curnt['scroll'] ) ) ? ' current' : '' ) . '" id="MessageID_' . $p->ID . '">';
}

// --- Wall posts --------------------------------------------------------------------------------------------------------------------
$sstr .= 	'<div class="post" id="MessagePost_' . $p->ID . '">';
// --- Content -----------------------------------------------------------------------------------------------------------------------
if( $p->Message )
{
	$sstr .= 	'<div class="content" id="MessageContent_' . $p->ID . '">' . convertToTags( $p->Message ) . '</div>';
}
// --- ParsedData --------------------------------------------------------------------------------------------------------------------
if( $p->ParsedData )
{
	$sstr .= $p->ParsedData;
}
// --- Clear -------------------------------------------------------------------------------------------------------------------------
$sstr .= 		'<div class="clearboth"></div>';
// --- Post end ----------------------------------------------------------------------------------------------------------------------
$sstr .= 	'</div>';

// --- Clear ----------------------------------------------------------------------------------------------------------------------------
$sstr .= '<div class="clearboth"></div>';

// --- Output end -----------------------------------------------------------------------------------------------------------------------

if( $_POST['mid'] != $p->ID )
{
	$sstr .= '</div>';
}

// Set status IsRead on user notify
if( $p->IsFocus )
{
	IsRead( $p->ID );
}

if( $IsMedia )
{
	if( !isset( $curnt['media'] ) )
	{
		$curnt['media'] = $p->ID;
	}
	
	$mstr[] = $sstr;
}
else if( $p->Data->Media != 'album' && ( $p->Message || $p->ParsedData ) )
{
	if( !isset( $curnt['scroll'] ) )
	{
		$curnt['scroll'] = $p->ID;
	}
	
	$pstr[] = $sstr;
}

?>
