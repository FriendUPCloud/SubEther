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

if( ( isset( $_POST[ 'allow' ] ) || isset( $_POST[ 'deny' ] ) ) && IsSystemAdmin() )
{
	if( $_POST[ 'allow' ] > 0 )
	{
		$node = new dbObject( 'SNodes' );
		if( $node->Load( $_POST[ 'allow' ] ) )
		{
			//require ( 'subether/restapi/actions/connect.php' );
			
			$node->IsPending = 1;
			$node->IsDenied = 0;
			$node->IsAllowed = 1;
			$node->DateModified = date( 'Y-m-d H:i:s' );
			$node->Save();
			
			die( 'ok<!--separate-->' . ( $node->IsAllowed == 1 ? 'allowed: ' : 'denied: ' ) . $node->Url );
		}
	}
	else if( $_POST[ 'deny' ] > 0 )
	{
		$node = new dbObject( 'SNodes' );
		if( $node->Load( $_POST[ 'deny' ] ) )
		{
			$node->IsPending = 0;
			$node->IsDenied = 1;
			$node->IsAllowed = 0;
			$node->DateModified = date( 'Y-m-d H:i:s' );
			$node->Save();
			
			die( 'ok<!--separate-->' . ( $node->IsDenied == 1 ? 'denied: ' : 'allowed: ' ) . $node->Url );
		}
	}
}

die( 'fail' );

?>
