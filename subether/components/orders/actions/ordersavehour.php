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

if ( $_POST['data'] )
{
	$hours = json_decode( $_POST['data'] );
	
	if ( $hours )
	{
		foreach ( $hours as $hr )
		{
			if ( $hr->Table && $hr->Fields )
			{
				$db = new dbObject( $hr->Table );
				
				if ( $hr->Primary )
				{
					$db->{$hr->Primary} = $hr->Fields->{$hr->Primary};
					$db->Load();
				}
				
				foreach ( $hr->Fields as $k=>$v )
				{
					if ( $k == $hr->Primary ) continue;
					if ( is_datetime( $v ) )
					{
						$db->{$k} = date( 'Y-m-d H:i:s', $v );
					}
					else
					{
						$db->{$k} = $v;
					}
				}
				
				$db->Save();
				
				LogUserActivity( $db->UserID, ( $hr->Primary && $hr->Fields->{$hr->Primary} ? 'update hour' : 'save hour' ), false, ( $hr->Primary && $hr->Fields->{$hr->Primary} ? 'update' : 'save' ), $hr->Table, ( $hr->Primary && $db->{$hr->Primary} ? $db->{$hr->Primary} : false ) );
			}
		}
		
		//die( print_r( json_decode( $_POST['data'] ),1 ) );
		die( 'ok<!--separate-->' . print_r( json_decode( $_POST['data'] ),1 ) );
	}
}

die( 'fail' );

?>
