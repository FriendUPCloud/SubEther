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

if( isset( $_POST ) && ( in_array( $parent->folder->Permission, array( 'admin', 'owner' ) ) || isset( $parent->access->IsAdmin ) ) )
{
	if ( $parent->folder->CategoryID > 0 )
	{
		$c = new dbObject( 'SBookCategory' );
		$c->ID = $parent->folder->CategoryID;
		if ( $c->Load() )
		{
			//die( print_r( $_POST,1 ) . ' --' );
			// Get Settings
			$c->Settings = is_string( $c->Settings ) ? json_obj_decode( $c->Settings ) : new stdClass();
			// Save Access Levels
			if ( $_POST['accesslevelslist'] )
			{
				$c->Settings->AccessLevels = json_obj_decode( $_POST['accesslevelslist'] );
			}
			if ( $_POST['WallMode'] )
			{
				$c->Settings->WallMode = $_POST['WallMode'];
			}
			// Save 3dparty Description
			//$c->Settings->{'3dparty'} = new stdClass();
			//$c->Settings->{'3dparty'}->Source = $_POST['3dparty_Source'];
			//$c->Settings->{'3dparty'}->Width = $_POST['3dparty_Width'];
			//$c->Settings->{'3dparty'}->Height = $_POST['3dparty_Height'];
			//$c->Settings->{'3dparty'}->Scrollbar = $_POST['3dparty_Scrollbar'] ? 1 : 0;
			$c->Settings = json_obj_encode( $c->Settings );
			
			// Save Group Name
			$c->Name = $_POST['Group'];
			// Save Group Description
			$c->Description = sanitizeText( $_POST['Description'] );
			// Save Group Privacy
			$c->Privacy = $_POST['Privacy'];
			// Save Group Parent
			$c->ParentID = $_POST['ParentID'];
			
			$c->Save();
			
			die( 'ok<!--separate-->' );
		}
		
		die( 'fail<!--separate-->no permission' );
	}
}

die( 'fail<!--separate-->' . $parent->folder->Permission );

?>
