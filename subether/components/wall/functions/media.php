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

if( isset( $_POST[ 'fid' ] ) && isset( $_POST[ 'fld' ] ) && isset( $_POST[ 'mid' ] ) && isset( $_POST[ 'type' ] ) && isset( $_POST[ 'path' ] ) )
{
	// --- Images ------------------------------------------------------------------------------------------------------------------------------
	if( $_POST[ 'fid' ] > 0 && $_POST[ 'fld' ] > 0 && $_POST[ 'mid' ] > 0 && $_POST[ 'type' ] == 'image' )
	{
		$root = ( $_POST[ 'path' ] != '' ? $_POST[ 'path' ] : 'upload/images-master/' ); $str = ''; $istr = '';
		
		$str .= '<div class="ParseContent">';
		
		$str .= '<div class="fileupload" onclick="ge(\'postMediaUploadBtn\').click()"><div></div></div>';
		
		$i = new dbImage ();
		if( $i->load( $_POST[ 'fid' ] ) )
		{
			if( $i->Width && $i->Height )
			{
				$style = 'max-width:' . $i->Width . 'px;max-height:' . $i->Height . 'px;';
			}
			else $style = '';
			
			if( $i->Filename )
			{
				$data = new stdClass();
				$data->MediaFormat = defineMediaFormat( $i->Width, $i->Height );
				$data->MediaID = $_POST[ 'mid' ];
				$data->MediaType = 'album';
				$data->FileID = $_POST[ 'fid' ];
				$data->FileFolder = $_POST[ 'fld' ];
				$data->FileName = $i->Filename;
				$data->FileWidth = $i->Width;
				$data->FileHeight = $i->Height;
				$data->FilePath = $root . $i->Filename;
				
				$istr .= '<div id="ParseImg_' . $data->FileID . '" class="image album ' . $data->MediaFormat . '"><a target="_blank" href="' . $data->FilePath . '">';
				$istr .= '<img style="background-image:url(\'' . $data->FilePath . '\');' . $style . '"/>';
				$istr .= '</a><div class="Edit" onclick="removeImage(' . $data->FileID . ')"><div></div></div></div>';
			}
		}
		
		$str .= $istr;
		
		$str .= '</div>';
		//$str .= '<div class="Edit" onclick="removeParse()"><div></div>';
		$str .= '</div>';
		
		die( 'ok<!--separate-->' . ( isset( $_POST[ 'multiple' ] ) ? $istr : $str ) . '<!--separate-->' . ( $data ? json_encode( $data ) : '' ) );
	}
	// --- Videos ----------------------------------------------------------------------------------------------------------------------------
	else if( $_POST[ 'fid' ] > 0 && $_POST[ 'fld' ] > 0 && $_POST[ 'mid' ] > 0 && $_POST[ 'type' ] == 'video' )
	{
		$root = ( $_POST[ 'path' ] != '' ? $_POST[ 'path' ] : 'upload/images-master/' ); $str = ''; $istr = '';
		
		$str .= '<div class="ParseContent">';
		
		$str .= '<div class="fileupload" onclick="ge(\'postMediaUploadBtn\').click()"><div></div></div>';
		
		$i = new dbFile ();
		if( $i->load( $_POST[ 'fid' ] ) )
		{
			if( $i->Filename )
			{
				$data = new stdClass();
				$data->MediaID = $_POST[ 'mid' ];
				$data->MediaType = 'video';
				$data->FileID = $_POST[ 'fid' ];
				$data->FileFolder = $_POST[ 'fld' ];
				$data->FileType = $i->Filetype;
				$data->FileName = $i->Filename;
				$data->FilePath = $root . $i->Filename;
				
				$data->Title = $i->Filename;
				$data->Type = 'video';
				$data->Url = $root . $i->Filename;
				$data->Thumb = $root . str_replace( end( explode( '.', $i->Filename ) ), '', $i->Filename ) . 'png';
				$data->Media = 'video';
				
				$istr .= '<div id="ParseImg_' . $data->FileID . '" class="image album"><a target="_blank" href="' . $data->FilePath . '">';
				if( file_exists( $data->Thumb ) )
				{
					$istr .= '<img style="background-image:url(\'' . $data->Thumb . '\');"/>';
				}
				else
				{
					$istr .= '<img src="subether/gfx/icons/' . ( $ext_large[$data->FileType] ? $ext_large[$data->FileType] : $ext_large['mov'] ) . '"/>';
				}
				$istr .= '</a><div class="Edit" onclick="removeImage(' . $data->FileID . ')"><div></div></div></div>';
			}
		}
		
		$str .= $istr;
		
		$str .= '</div>';
		//$str .= '<div class="Edit" onclick="removeParse()"><div></div>';
		$str .= '</div>';
		
		die( 'ok<!--separate-->' . ( isset( $_POST[ 'multiple' ] ) ? $istr : $str ) . '<!--separate-->' . ( $data ? json_encode( $data ) : '' ) );
		//die( 'Not supported yet. Soon!' );
	}
	// --- Audio -----------------------------------------------------------------------------------------------------------------------------
	else if( $_POST[ 'fid' ] > 0 && $_POST[ 'fld' ] > 0 && $_POST[ 'mid' ] > 0 && $_POST[ 'type' ] == 'audio' )
	{
		die( 'Not supported yet. Soon!' );
	}
	// --- Files -----------------------------------------------------------------------------------------------------------------------------
	else if( $_POST[ 'fid' ] > 0 && $_POST[ 'fld' ] > 0 && $_POST[ 'mid' ] > 0 && $_POST[ 'type' ] == 'file' )
	{
		$root = ( $_POST[ 'path' ] != '' ? $_POST[ 'path' ] : 'upload/' ); $str = ''; $istr = '';
		
		$str .= '<div class="ParseContent">';
		
		$str .= '<div class="fileupload" onclick="ge(\'postMediaUploadBtn\').click()"><div></div></div>';
		
		$i = new dbFile ();
		if( $i->load( $_POST[ 'fid' ] ) )
		{
			if( $i->Filename )
			{
				$data = new stdClass();
				$data->MediaID = $_POST[ 'mid' ];
				$data->MediaType = 'file';
				$data->FileID = $_POST[ 'fid' ];
				$data->FileFolder = $_POST[ 'fld' ];
				$data->FileType = $i->Filetype;
				$data->FileName = $i->Filename;
				$data->FilePath = $root . $i->Filename;
				
				$data->Title = $i->Filename;
				$data->Type = 'file';
				$data->Url = $root . $i->Filename;
				$data->Media = 'file';
				
				$istr .= '<div id="ParseImg_' . $data->FileID . '" class="image album"><a target="_blank" href="' . $data->FilePath . '">';
				$istr .= '<img src="subether/gfx/icons/' . ( $ext_large[$data->FileType] ? $ext_large[$data->FileType] : $ext_large['txt'] ) . '"/>';
				$istr .= '</a><div class="Edit" onclick="removeImage(' . $data->FileID . ')"><div></div></div></div>';
			}
		}
		
		$str .= $istr;
		
		$str .= '</div>';
		//$str .= '<div class="Edit" onclick="removeParse()"><div></div>';
		$str .= '</div>';
		
		die( 'ok<!--separate-->' . ( isset( $_POST[ 'multiple' ] ) ? $istr : $str ) . '<!--separate-->' . ( $data ? json_encode( $data ) : '' ) );
	}
}
die( 'This filetype is not supported.' . "\n" . 'Please try with a different filetype.' );

?>
