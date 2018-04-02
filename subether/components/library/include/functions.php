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

function createFolder( $root, $path = false, $name, $chmod = false )
{
    if( !$root || !$name ) return;
    
    $folderpath = ( $root . $path . $name );
    $chmod = ( $chmod ? $chmod : 0777 );
    
    if( !file_exists( $root ) )
    {
        die( 'path doesn\'t exist, can\'t create folder' );
    }
    if( !file_exists( $folderpath ) )
    {
        if( mkdir( $folderpath, $chmod, true ) )
        {
            return true;
        }
        return false;
    }
    return true;
}

function openFolder( $path )
{
    if( !$path ) return;
    
    if( file_exists ( $path ) && $dir = opendir ( $path ) )
    {
        $depth = 0;
        $out = array();
        while ( $file = readdir ( $dir ) )
        {
            if ( $file{0} == '.' ) continue;
            $filepath = ( $path . '/' . $file );
            $stats = stat( $filepath );
            $type = explode( '.', $file );
            $obj = new stdClass();
            $obj->path = $filepath;
            $obj->name = $file;
            $obj->type = end( $type );
            $obj->size = $stats['size'];
            $out[] = $obj;
            $depth++;
            if( $depth >= 1000 ) return false;
        }
        closedir ( $dir );
        return $out;
    }
    return false;
}

function saveFile( $path, $file )
{
    if( !$path || !$file && !is_array( $file ) ) return;
    
    $filefail = $file['error'];
    $filename = uniqueFile( $path, $file['name'] );
    $filetemp = $file['tmp_name'];
    $filetype = explode( '/', $file['type'] );
    $filesize = $file['size'];
    $filepath = $path . basename( $filename );
    
    if( !$filename )
    {
        die( 'something went wrong with the fileupload' );
    }
    if( !$filefail && move_uploaded_file( $filetemp, $filepath ) )
    {
        return true;
    }
    return false;
}

function openFile( $path, $file )
{
    if( $path && $file )
    {
        if( substr( $path, -1 ) != '/' )
        {
            $path = $path . '/';
        }
        
        $filepath = ( $path . $file );
        
        if( file_exists( $filepath ) )
        {
            if( $content = file_get_contents( $filepath, true ) )
            {
                return $content;
            }
        }
    }
    return false;
}

function deleteFile( $path, $file )
{
    if( !$path || !$file ) return;
    if( unlink( $path . $file ) )
    {
        return true;
    }
    return false;
}

function uniqueFile( $path, $filename )
{
    $ext = '';
    
    if( !$path || !$filename ) return false;
    
    $parts = explode ( '.', $filename );
    // Get the file extension, if any
    if( count( $parts ) > 1 )
    {
        $ext = array_pop ( $parts );
    }
    
    // Get the filename
    $file = implode( '.', $parts );
    
    if ( file_exists ( $path . $file . '.' . $ext ) )
    {
        $depth = 0;
        while ( file_exists ( $path . $file . '.' . $ext ) )
        {
            $file .= '_copy';
            $depth++;
            if( $depth >= 10 ) return false;
        }
    }
    return ( $file . '.' . $ext );
}

function embedMetaFileData( $url, $message, $title, $leadin, $filepath, $filewidth, $fileheight, $maxwidth, $maxheight )
{
    $str .= '';
    $str .= ( $message ? ( '<div class="content">' . $message . '</div>' ) : '' );
    $str .= '<div class="ParseContent">';
    if( $filewidth > 0 && $fileheight > 0 && file_exists( $filepath ) )
    {
        $str .= '<div class="image site' . ( $maxwidth <= $filewidth ? ' big' : ' small' ) . '">';
        $str .= '<a href="' . $filepath . '" target="_blank">';
        $str .= '<img style="background-image:url(' . $filepath . ');max-width:' . $filewidth . 'px;max-height:' . $fileheight . 'px;">';
        $str .= '</a></div>';
    }
    if( $title && $leadin )
    {
        $str .= '<div class="text">';
        $str .= '<h3><a href="' . $url . '" target="_blank">' . $title . '</a></h3>';
        $str .= '<p><a href="' . $url . '" target="_blank">' . $leadin . '</a></p>';
        $str .= '</div>';
    }
    $str .= '</div>';
    
    return $str;
}

function getMetaFileData( $path, $filename )
{
    if( !$path || !$filename ) return false;
    
    $ext = explode( '.', $filename );
	
    // If it's parse data
    if( file_exists( $path . ( $ext[0] . '.parse' ) ) )
    {
        $lib = new Library ();
        $obj = $lib->OpenFile( $path, ( $ext[0] . '.parse' ) );
        $obj = $obj ? json_decode( $obj ) : false;
        
        if( $obj )
        {
            $data = new stdClass();
            $data->Url = $obj->Url;
            $data->Message = $obj->Message;
            $data->Domain = $obj->Domain;
            $data->Title = $obj->Title;
            $data->Leadin = $obj->Leadin;
            $data->Type = $obj->Type;
            $data->Media = $obj->Media;
            $data->Limit = $obj->Limit;
            
            $obj = $data;
        }
        
        return $obj;
    }
    // If it's meta data
    else if( file_exists( $path . ( $ext[0] . '.meta' ) ) )
    {
        $lib = new Library ();
        $obj = $lib->OpenFile( $path, ( $ext[0] . '.meta' ) );
        $obj = $obj ? json_decode( $obj ) : false;
        
        if( $obj )
        {
            $data = new stdClass();
            $data->Url = $obj->Url;
            $data->Message = $obj->Message;
            $data->Domain = $obj->Domain;
            $data->Title = $obj->Title;
            $data->Leadin = $obj->Leadin;
            $data->Type = $obj->Type;
            $data->Media = $obj->Media;
            $data->Limit = $obj->Limit;
            
            $obj = $data;
        }
        
        return $obj;
    }
    
    return false;
}

function libraryAccess( $mod, $acc, $wid, $cid )
{
    if( !$mod || !$wid || !$cid ) return false;
    
    // Check access
    if( ( $mod == 'Profile' && $wid == $cid ) || ( $acc == 'admin' || $acc == 'owner' ) || IsSystemAdmin() )
    {
        return true;
    }
    
    return false;
}

function libraryFileAccess( $fid, $type, $uniqueid = false )
{
    global $database, $webuser;
    
    $id = ( $uniqueid ? 'UniqueID' : 'ID' );
    
    if( $fid && $type && ( $file = $database->fetchObjectRow ( 'SELECT * FROM ' . $type . ' WHERE ' . $id . ' = ' . $fid ) ) )
    {
        // Owner of file in user library
        if( $webuser->ID == $file->UserID )
        {
            return true;
        }
        // Admin of group library
        if( $file->CategoryID > 0 && ( $acc = CategoryAccess( $webuser->ContactID, $file->CategoryID ) ) )
        {
            return true;
        }
        // IsSystemAdmin
        if( IsSystemAdmin() )
        {
            return true;
        }
    }
    
    return false;
}

function libraryIcons( $name, $size )
{
    if( !$name || !$size ) return false;
    
    switch( $size )
    {
        case 16:
            $icons = array(
                'pdf'=>'pdf/pdf-16_32.png',
                'xls'=>'xls_win/xlsx_win-16_32.png',
                'doc'=>'docx_win/docx_win-16_32.png',
                'docx'=>'docx_win/docx_win-16_32.png',
                'jpg'=>'jpeg/jpeg-16_32.png', 
                'jpeg'=>'jpeg/jpeg-16_32.png',
                'png'=>'png/png-16_32.png',
                'gif'=>'gif/gif-16_32.png', 
                'mov'=>'mov/mov-16_32.png',
                'avi'=>'mov/mov-16_32.png',
                'ogv'=>'mov/mov-16_32.png',
                'mp4'=>'mov/mov-16_32.png',
                'swf'=>'mov/mov-16_32.png',
                'url'=>'url/url-16_32.png',
                'mp3'=>'mp3/mp3-16_32.png', 
                'txt'=>'text/text-16_32.png'
            );
            break;
            
        case 128:
            $icons = array(
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
            break;
            
        default:
            $icons = array();
            break;
    }
    
    return ( isset( $icons[strtolower($name)] ) ? $icons[strtolower($name)] : false );
}


function libraryMedia( $type )
{
    if( !$type ) return false;
    
    $media = array(
        'txt'=>'content',
        'plain'=>'content',
        'css'=>'content',
        'pdf'=>'pdf',
        'webm'=>'video', 
        'ogv'=>'video',
        'mp4'=>'video', 
        'swf'=>'video',
        'mp3'=>'audio',
        'ogg'=>'audio',
        'wav'=>'audio'
    );
    
    return ( isset( $media[strtolower($type)] ) ? $media[strtolower($type)] : false );
}

function libraryThumbs( $path, $filename = false, $type = false, $ext = false )
{
    global $webuser;
    
    if( !$path ) return false;
    
    $fullpath = false;
    
    if( !$filename )
    {  
        $fn = explode( '/', $path );
        $fn = end( $fn );
        
        if ( strstr( $fn, '.' ) )
        {
            $fullpath = true;
            $filename = $fn;
            $path = str_replace( $filename, '', $path );
        }
        
        if ( !$filename )
        {
            return $path;
        }
    }
    
    if( $type == 'video' )
    {
        $filename = ( ( $ext && libraryMedia( $ext ) == 'video' ) ? ( str_replace( end( explode( '.', $filename ) ), '', $filename ) . 'png' ) : $filename );
    }
    
    switch( $type )
    {
        case 'video':
            $filepath = ( $path . $filename );
            break;
        
        default:
            $filepath = ( $path . $filename );
            break;
    }
    
    if ( $fullpath )
    {
        //die( ( $path . $webuser->GetToken() . '/' . $filename ) . ' --' );
    }
    
    if ( $filepath && $fullpath && @exif_imagetype( $path . ( $webuser->ID > 0 && $webuser->GetToken() ? $webuser->GetToken() . '/' : '' ) ) )
    {
        return $filepath;
    }
    else if( file_exists( $filepath ) && @exif_imagetype( $filepath ) )
    {
        return $filepath;
    }
    
    return false;
}

function libraryFilesize( $size )
{
    if( !$size ) return '0b';
    
    // --- gigabytes ----------------------
    
    if( $size >= 1000000000 )
    {
        return ( round( $size / 1000000000, 1 ) . 'gb' );
    }
    
    // --- megabytes ----------------------
    
    if( $size >= 1000000 )
    {
        return ( round( $size / 1000000, 1 ) . 'mb' );
    }
    
    // --- kilobytes ----------------------
    
    if( $size >= 1000 )
    {
        return ( round( $size / 1000, 1 ) . 'kb' );
    }
    
    return ( $size . 'b' );
}

function max_file_upload_in_bytes()
{
    //select maximum upload size
    $max_upload = return_bytes( ini_get( 'upload_max_filesize' ) );
    //select post limit
    $max_post = return_bytes( ini_get( 'post_max_size' ) );
    //select memory limit
    $memory_limit = return_bytes( ini_get( 'memory_limit' ) );
    // return the smallest of them, this defines the real limit
    return min( $max_upload, $max_post, $memory_limit );
}

?>
