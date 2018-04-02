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

include_once ( 'subether/classes/library.class.php' );

// TODO: Create support for big backups, nginx limits the time used and it fails on about 700mb of data ...

$str = '<h2>Backups</h2><br>';

$backup = new Library();

if( $files = $backup->OpenFolder( BASE_DIR . '/', 'backup/' ) )
{
	$str .= '<h3>Core backups:</h3>';
	
	$str .= '<ul>';
	
	rsort( $files );
	
	foreach( $files as $file )
	{
		if( isset( $file->name ) && is_file( $file->dir . $file->name ) && strstr( $file->name, '.' ) )
		{
			$str .= '<li><a href="' . $file->path . '"><span>' . $file->name . '</span></a><span> ' . $file->size . ' </span>' . ( $file->modified ? ( '<span> ( ' . date( 'F j, Y', $file->modified ) . ' at ' . date( 'H:i', $file->modified ) . ' )</span>' ) : '' ) . '</li>';
		}
	}
	
	$str .= '</ul>';
	
}

/*$str .= '<h3>Data backups:</h3>';

if( $files = $backup->OpenFolder( BASE_DIR . '/backup/', 'subether_backup/' ) )
{
	$str .= '<ul>';
	
	rsort( $files );
	
	foreach( $files as $file )
	{
		if( isset( $file->name ) && is_file( $file->dir . $file->name ) && strstr( $file->name, '.' ) )
		{
			$str .= '<li><a href="' . $file->path . '"><span>' . $file->name . '</span></a><span> ' . $file->size . ' </span>' . ( $file->modified ? ( '<span> ( ' . date( 'F j, Y', $file->modified ) . ' at ' . date( 'H:i', $file->modified ) . ' )</span>' ) : '' ) . '</li>';
		}
	}
	
	$str .= '</ul>';
}

$str .= '<button onclick="backupData(this)">Start Backup</button>';*/

$str .= '<h3>Database backups:</h3>';

if( $files = $backup->OpenFolder( BASE_DIR . '/backup/', 'database_backup/' ) )
{
	$str .= '<ul>';
	
	rsort( $files );
	
	foreach( $files as $file )
	{
		if( isset( $file->name ) && is_file( $file->dir . $file->name ) && strstr( $file->name, '.' ) )
		{
			$str .= '<li><a href="' . $file->path . '"><span>' . $file->name . '</span></a><span> ' . $file->size . ' </span>' . ( $file->modified ? ( '<span> ( ' . date( 'F j, Y', $file->modified ) . ' at ' . date( 'H:i', $file->modified ) . ' )</span>' ) : '' ) . '</li>';
		}
	}
	
	$str .= '</ul>';
}

$str .= '<button onclick="backupDatabase(this)">Start Backup</button>';

?>
