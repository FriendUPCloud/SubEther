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

global $database;

if ( $usr = $database->fetchObjectRow( '
	SELECT
		*
	FROM
		`Users`
	WHERE
			IsDeleted = "0" 
		AND Username = \'' . trim( $_POST['Username'] ) . '\' 
		AND NodeID = "0" 
	ORDER BY
		ID DESC
' ) )
{
	// If user doesn't have a uniqueid make one
	if( !$usr->UniqueID )
	{
		$usu = new dbObject( 'Users' );
		$usu->ID = $usr->ID;
		if( $usu->Load() )
		{
			$usu->UniqueID = UniqueKey( $usr->Username );
			$usu->Save();
		}
	}
	
	$keys = [];
	
	// Handle request for keystorage if user has StoreKey = 1
	if( $usr->StoreKey > 0 )
	{
		if ( !$database->fetchObjectRow( '
			SELECT 
				* 
			FROM 
				SBookStorage 
			WHERE 
					UserID = \'' . $usr->ID . '\' 
				AND Relation = "Users" 
				AND IsDeleted = "0" 
			ORDER BY 
				ID ASC 
		' ) )
		{
			include_once ( 'subether/classes/fcrypto.class.php' );
			
			// Get list of SystemAdmins and check if keys from this user is missing
			if ( $adm = $database->fetchObjectRows( $q = '
				SELECT 
					co.*, us.PublicKey 
				FROM 
					Groups gr, 
					UsersGroups ug,
					Users us,
					SBookContact co 
				WHERE 
						gr.SuperAdmin = "1" 
					AND ug.GroupID = gr.ID 
					AND ug.UserID > 0 
					AND us.ID = ug.UserID 
					AND us.IsDeleted = "0" 
					AND us.IsAdmin = "1" 
					AND co.UserID = us.ID 
				ORDER BY 
					us.ID ASC 
			' ) )
			{
				foreach( $adm as $a )
				{
					$first  = ( $a->Firstname  ? $a->Firstname  . ' ' : '' );
					$middle = ( $a->Middlename ? $a->Middlename . ' ' : '' );
					$last   = ( $a->Lastname   ? $a->Lastname   . ' ' : '' );
					
					$a->DisplayName = '';
					
					$a->DisplayName = ( $a->Display == 1 ? trim( $first . $middle . $last ) : $a->DisplayName );
					$a->DisplayName = ( $a->Display == 2 ? trim( $first . $last           ) : $a->DisplayName );
					$a->DisplayName = ( $a->Display == 3 ? trim( $last  . $first          ) : $a->DisplayName );
					
					$a->DisplayName = ( $a->DisplayName ? $a->DisplayName : $a->Username );
					
					$obj = new stdClass();
					$obj->UserID    = $a->UserID;
					$obj->Name      = $a->DisplayName;
					$obj->Email     = $a->Email;
					$obj->PublicKey = $a->PublicKey;
					
					$keys[] = $obj;
				}
			}
		}
	}
	
	die( 'ok<!--separate-->' . ( $usr->UniqueID ? $usr->UniqueID : $usu->UniqueID ) . ( $keys ? ( '<!--separate-->' . json_encode( $keys ) ) : '' ) );
}

die( 'fail' );

?>
