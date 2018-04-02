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

global $Session, $webuser;

if ( is_numeric ( $_POST['Language'] ) )
{
	$c = new dbObject( 'SBookContact' );
	$c->UserID = $webuser->ID;
	if ( $c->Load() && isset( $_POST ) )
	{
		// TODO: Make sure we find a way to return data if json fails and also when converted so we don't loose any other saved in data
		
		$c->Data = json_obj_decode( $c->Data );
		
		$lang = new dbObject ( 'Languages' );
		if ( $lang->load ( $_POST['Language'] ) )
		{
			$Session->Set( 'CurrentLanguage', $lang->ID );
			$Session->Set( 'LanguageCode', $lang->Name );
			
			$_SESSION['UserLanguage'] = $lang->Name;
			
			$c->Data->CurrentLanguage = $lang->ID;
			$c->Data->LanguageCode = $lang->Name;
			
			$c->Data = json_obj_encode( $c->Data );
			
			$c->Save();
			
			die( 'ok<!--separate-->' );
		}
	}
}

die( 'fail' );

?>
