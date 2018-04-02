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

if( isset( $_POST[ 'mname' ] ) )
{
	// If it's a component run update for access
	if( $_POST['cname'] && $_POST['position'] )
	{
		$c = new dbObject( 'SComponents' );
		$c->Type = ( $_POST[ 'type' ] ? $_POST[ 'type' ] : 'global' );
		$c->Name = $_POST[ 'cname' ];
		$c->Module = $_POST[ 'mname' ];
		$c->Position = $_POST[ 'position' ];
		if( $c->Load() )
		{
			$c->UserLevels = ( $_POST[ 'access' ] ? $_POST[ 'access' ] : $c->UserLevels );
			$c->Save();
			
			die( 'ok<!--separate-->' );
		}
		die( 'hæ ' . $_POST[ 'type' ] . ' - ' . $_POST[ 'mname' ] );
	}
	else
	{
		// Else run module update for access
		$mod = new dbObject( 'SModules' );
		$mod->Type = ( $_POST[ 'type' ] ? $_POST[ 'type' ] : 'global' );
		$mod->Name = $_POST[ 'mname' ];
		if( $mod = $mod->Find() )
		{
			foreach( $mod as $mo )
			{
				$m = new dbObject( 'SModules' );
				if( $m->Load( $mo->ID ) )
				{
					$m->UserLevels = ( $_POST[ 'access' ] ? $_POST[ 'access' ] : $m->UserLevels );
					$m->Save();
				}
			}
			
			die( 'ok<!--separate-->' );
		}
		die( 'mja ' . $_POST[ 'type' ] . ' - ' . $_POST[ 'mname' ] );
	}
}

die( 'fail' );

?>
