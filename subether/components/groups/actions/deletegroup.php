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

if( !$folder && $parent ) $folder = $parent->folder;

if( $webuser && $folder && ( $folder->Permission == 'owner' || isset( $parent->access->IsOwner ) || isset( $parent->access->IsSystemAdmin ) ) )
{
	$q = '
		SELECT 
			r.* 
		FROM 
			SBookCategory c,
			SBookCategoryRelation r 
		WHERE
			c.ID = \'' . $folder->CategoryID . '\'
			AND r.CategoryID = c.ID
			AND r.ObjectType = "Users" 
		ORDER BY  
			r.ID DESC 
	';
	
	if( $rows = $database->fetchObjectRows( $q ) )
	{
		foreach( $rows as $row )
		{
			// Old method - TODO: Remove when every trace of this is deleted in the system
			$r = new dbObject( 'SBookCategoryRelation' );
			if( $r->Load( $row->ID ) )
			{
				$r->IsDeleted = 1;
				$r->Save();
				//$r->Delete();
			}
		}
		
		$g = new dbObject( 'SBookCategory' );
		if( $g->Load( $folder->CategoryID ) )
		{
			// New method
			$sca = new dbObject( 'SBookCategoryAccess' );
			$sca->CategoryID = $g->ID;
			if( $sca = $sca->Find() )
			{
				foreach( $sca as $ca )
				{
					$a = new dbObject( 'SBookCategoryAccess' );
					$a->ID = $ca->ID;
					if( $a->Load() )
					{
						$a->IsDeleted = 1;
						$a->Save();
						//$a->Delete();
					}
				}
			}
			
			$g->IsDeleted = 1;
			$g->Save();
			//$g->Delete();
			die( 'ok' );
		}
	}
}
die( 'fail' );

?>
