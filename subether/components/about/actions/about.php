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

if( $parent->cuser->ID == $webuser->ID || IsSystemAdmin() )
{
	//die( print_r( $_POST,1 ) . ' ..' );
	$c = new dbObject( 'SBookContact' );
	$c->UserID = $parent->cuser->ID;
	if( $c->Load() && isset( $_POST ) )
	{
		$c->Data = json_obj_decode( $c->Data );
		
		if( !isset( $c->Data->Settings ) )
		{
			$c->Data->Settings = new stdClass();
		}
		if( !isset( $c->Data->Settings->Profile ) )
		{
			$c->Data->Settings->Profile = new stdClass();
		}
		
		$c->Custom = json_obj_decode( $c->Custom );
		
		if( !isset( $c->Custom->About ) )
		{
			$c->Custom->About = new stdClass();
		}
		
		foreach( $_POST as $k=>$v )
		{
			if( strstr( $k, '_checked' ) )
			{
				$key = str_replace( '_checked', '', $k );
				
				$c->Data->Settings->Profile->{$key} = $v;
				
				continue;
			}
			
			if( strstr( $k, 'Custom_' ) )
			{
				$key = explode( '_', $k );
				
				if( !isset( $c->Custom->About->{$key[1]} ) )
				{
					$c->Custom->About->{$key[1]} = new stdClass();
				}
				if( !isset( $c->Custom->About->{$key[1]}->{$key[2]} ) )
				{
					$c->Custom->About->{$key[1]}->{$key[2]} = new stdClass();
				}
				
				$c->Custom->About->{$key[1]}->{$key[2]}->{$key[3]} = $v;
				
				// Delete if label is empty
				if( $key[3] == 'Label' && !trim( $v ) && isset( $c->Custom->About->{$key[1]}->{$key[2]}->{$key[3]} ) )
				{
					unset( $c->Custom->About->{$key[1]}->{$key[2]} );
				}
				
				continue;
			}
			
			$c->{$k} = $v;
		}
		
		$c->Data = json_obj_encode( $c->Data );
		$c->Custom = json_obj_encode( $c->Custom );
		$c->DateModified = date( 'Y-m-d H:i:s' );
		$c->Save();
		
		UserActivity( 'contacts', 'contact', $c->ID, null, $c->ID, 'updated' );
		
		die( 'ok<!--separate-->' );
	}
	die( 'fail' );
}
die( 'fail' );

?>
