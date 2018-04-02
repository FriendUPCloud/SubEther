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

if( $_REQUEST[ 'action' ] == 'report' && isset( $_POST ) )
{
	$fileid = false;
	
	if( $_FILES )
	{
		if ( $group = $database->fetchObjectRow( '
			SELECT
				*
			FROM
				SBookCategory
			WHERE
					Type = "Group"
				AND Name = "Groups"
		' ) )
		{
			// Assign report to System group
			$g = new dbObject( 'SBookCategory' );
			$g->CategoryID = $group->ID;
			$g->Type = 'SubGroup';
			$g->Name = 'System';
			$g->Privacy = 'SecretGroup';
			$g->IsSystem = 1;
			$g->Owner = 0;
			if( !$g->Load() )
			{
				$g->Save();
			}
			
			if( $g->ID > 0 )
			{
				$lib = new Library ();
				$lib->CategoryID = $g->ID;
				$lib->ParentFolder = 'Library';
				$lib->FolderName = 'Reports';
				$lib->UploadFile( $_FILES['Screenshot'] );
				
				$fileid = $lib->FileID;
			}
		}
	}
	
	$c = new dbObject( 'SBookCaseList' );
	foreach( $_POST as $k=>$v )
	{
		$c->$k = stripslashes( trim( $v ) );
	}
	$c->UserID = $webuser->ID;
	if( $fileid )
	{
		$c->FileID = $fileid;
	}
	$c->DateCreated = date( 'Y-m-d H:i:s' );
	$c->Save();
	
	die( 'ok<!--separate-->' . $c->ID );
}
die( 'fail' );

?>
