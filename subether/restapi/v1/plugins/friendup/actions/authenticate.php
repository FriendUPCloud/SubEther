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

include_once ( BASE_DIR . '/subether/classes/posthandler.class.php' );

$plugins = array();

if ( $accounts = $database->fetchObjectRows ( '
	SELECT * 
	FROM SBookApiAccounts
	WHERE Name = "friendup"
	AND Url != "" 
	AND Username != "" 
	AND Password != ""
	AND IsGlobal = "1" 
	ORDER BY ID ASC 
' ) )
{
	$i = 0;
	
	foreach( $accounts as $acc )
	{
		$ph = new PostHandler ( $acc->Url . 'system.library/login' );
		$ph->AddVar ( 'username', $acc->Username );
		$ph->AddVar ( 'password', 'HASHED' . $acc->Password );
		$res = $ph->send();
		//die( $res . ' .. ' );
		if ( $res && ( $json = json_decode( trim( $res ) ) ) )
		{
			if ( $json->ErrorMessage == 0 )
			{
				$a = new dbObject( 'SBookApiAccounts' );
				$a->ID = $acc->ID;
				if ( $a->Load() )
				{
					$a->SessionID = $acc->SessionID = $json->sessionid;
					$a->DateModified = date( 'Y-m-d H:i:s' );
					$a->Save();
				}
				
				if ( !isset( $plugins[$acc->Name] ) )
				{
					$plugins[$acc->Name] = array();
				}
				
				$plugins[$acc->Name][($acc->App?$acc->App:$i)] = $acc;
				
				$i++;
			}
		}
	}
}

?>
