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

global $webuser;

if( isset( $_POST['mid'] ) && $_POST['mid'] > 0 )
{
	// TODO: Check on if the user has access to this change
	$m = new dbObject( 'SBookMessage' );
	if( $m->Load( $_POST['mid'] ) )
	{
		$imgs = []; $fils = [];
		
		$data = $m->Data;
		
		if( is_string( $data ) )
		{
			$data = json_decode( $data );
		}
		
		if( isset( $data->FileID ) || isset( $data->LibraryFiles ) || is_array( $data ) )
		{
			// TODO: Support files also, video format, other ???
			
			if( isset( $data->LibraryFiles ) && is_array( $data->LibraryFiles ) )
			{
				foreach( $data->LibraryFiles as $fi )
				{
					switch( $fi->MediaType )
					{
						case 'image':
						case 'album':
							$imgs[$fi->FileID] = $fi->FileID;
							break;
						default:
							$fils[$fi->FileID] = $fi->FileID;
							break;
					}
				}
			}
			else if( isset( $data ) && is_array( $data ) )
			{
				foreach( $data as $fi )
				{
					switch( $fi->MediaType )
					{
						case 'image':
						case 'album':
							$imgs[$fi->FileID] = $fi->FileID;
							break;
						default:
							$fils[$fi->FileID] = $fi->FileID;
							break;
					}
				}
			}
			else
			{
				switch( $data->MediaType )
				{
					case 'image':
						$imgs[$data->FileID] = $data->FileID;
						break;
					default:
						$fils[$data->FileID] = $data->FileID;
						break;
				}
			}
		}
		
		$m->Access = $_POST['value'];
		//$m->DateModified = date ( 'Y-m-d H:i:s' );
		$m->Save();
		
		if( $imgs )
		{
			foreach( $imgs as $img )
			{
				$i = new dbObject( 'Image' );
				if( $i->Load( $img ) )
				{
					// Temporary if UserID and CategoryID is null update
					if( $parent && !$i->UserID )
					{
						$i->UserID = $parent->cuser->UserID;
					}
					if( $parent && strtolower( $parent->folder->MainName ) != 'profile' && !$i->CategoryID )
					{
						$i->CategoryID = $parent->folder->CategoryID;
					}
					
					$i->Access = $_POST['value'];
					$i->DateModified = date ( 'Y-m-d H:i:s' );
					$i->Save();
				}
			}
		}
		
		if( $fils )
		{
			foreach( $fils as $fil )
			{
				$f = new dbObject( 'File' );
				if( $f->Load( $fil ) )
				{
					// Temporary if UserID and CategoryID is null update
					if( $parent && !$f->UserID )
					{
						$f->UserID = $parent->cuser->UserID;
					}
					if( $parent && strtolower( $parent->folder->MainName ) != 'profile' && !$f->CategoryID )
					{
						$f->CategoryID = $parent->folder->CategoryID;
					}
					
					$f->Access = $_POST['value'];
					$f->DateModified = date ( 'Y-m-d H:i:s' );
					$f->Save();
				}
			}
		}
		
		die( 'ok<!--separate-->' );
	}
}
die( 'fail' );

?>
