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

if( isset( $_POST[ 'mname' ] ) && isset( $_POST[ 'value' ] ) )
{
	$mod = new dbObject( 'SModules' );
	$mod->Type = ( $_POST[ 'mtype' ] ? $_POST[ 'mtype' ] : 'global' );
	$mod->Name = $_POST[ 'mname' ];
	if( $mod = $mod->Find() )
	{
		foreach( $mod as $mo )
		{
			$m = new dbObject( 'SModules' );
			if( $m->Load( $mo->ID ) )
			{
				$m->UserLevels = ( $_POST[ 'access' ] ? $_POST[ 'access' ] : $m->UserLevels );
				$m->Visible = $_POST[ 'value' ];
				$m->Save();
			}
		}
		die( 'ok<!--separate-->' );	
	}
	else
	{
		$arr = array();
		
		if( $root && file_exists( $f = ( $root . 'modules/' . $_POST[ 'mname' ] . '/info' ) ) )
		{
			$cnt = file_get_contents( $f, true );
			
			if( $cnt && ( $pos = explode( ',', $cnt ) ) )
			{
				foreach( $pos as $p )
				{
					$obj = new stdClass();
					$obj->Name = $module;
					$obj->Type = $type;
					$obj->Position = $p;
					
					$arr[] = $obj;
				}
			}
		}
		else
		{
			$arr[] = true;
		}
		
		foreach( $arr as $a )
		{
			$mod = new dbObject( 'SModules' );
			$mod->Type = ( $_POST[ 'mtype' ] ? $_POST[ 'mtype' ] : 'global' );
			if( isset( $a->Position ) )
			{
				$mod->Position = $a->Position;
			}
			$mod->Name = $_POST[ 'mname' ];
			$mod->UserLevels = ( $_POST[ 'access' ] ? $_POST[ 'access' ] : ',99,' );
			$mod->Visible = $_POST[ 'value' ];
			$mod->Save();
		}
		die( 'ok<!--separate-->' );	
	}
}
die( 'fail' );

?>
