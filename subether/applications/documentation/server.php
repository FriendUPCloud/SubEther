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

function OpenFolder( $path )
{
	if( !$path ) return;
	
	$folderpath = $path;
	
	if( file_exists ( $folderpath ) && $dir = opendir ( $folderpath ) )
	{
		if( substr( $folderpath, -1 ) != '/' )
		{
			$folderpath = $folderpath . '/';
		}
		
		$depth = 0;
		$out = array();
		while ( $file = readdir ( $dir ) )
		{
			if ( $file{0} == '.' && strlen( $file ) <= 2 ) continue;
			$filepath = ( $folderpath . $file );
			$parts = explode( '.', $file );
			$type = end( $parts );
			$title = str_replace( ( $type ? ( '.'.$type ) : '' ), '', $file );
			$obj = new stdClass();
			$obj->dir = ( defined( 'BASE_DIR' ) ? ( BASE_DIR . '/' . str_replace( BASE_DIR, '', $folderpath ) ) : $folderpath ) . $file;
			$obj->path = ( defined( 'BASE_URL' ) ? BASE_URL : '' ) . ( defined( 'BASE_DIR' ) ? str_replace( ( BASE_DIR . '/' ), '', $filepath ) : $filepath );
			$obj->name = $file;
			$obj->title = $title;
			$obj->type = $type;
			$obj->isdir = ( is_dir( $filepath ) ? 1 : 0 );
			$obj->isfile = ( is_file( $filepath ) ? 1 : 0 );
			$obj->size = filesize( $filepath );
			$obj->modified = filemtime( $filepath );
			$out[] = $obj;
			$depth++;
			if( $depth >= 10000 ) return false;
		}
		closedir ( $dir );
		return $out;
	}
	return false;
}

$xml = [];

if( $folders = OpenFolder( 'subether/applications' ) )
{
	foreach( $folders as $fld )
	{
		if( $fld->name && file_exists( 'subether/applications/' . $fld->name . '/doc/server.php' ) )
		{
			$doc = '';
			
			include_once( 'subether/applications/' . $fld->name . '/doc/server.php' );
			
			if( $doc )
			{
				$xml[] = $doc;
			}
		}
	}
	
	//die( print_r( $folders,1 ) . ' -- ' . print_r( $xml,1 ) );
}

outputXML ( "\t\t".implode( "\n\t\t", $xml )."\n", false, 'documentation' );

?>
