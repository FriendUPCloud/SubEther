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

if ( $account = $database->fetchObjectRow ( '
	SELECT * 
	FROM SBookApiAccounts
	WHERE Name = "friendup"
	AND Url != "" 
	AND Username != "" 
	AND Password != ""
	AND SessionID != "" 
	AND IsGlobal = "1" 
	ORDER BY ID ASC
	LIMIT 1 
' ) )
{
	//$urlstr = ( $account->Url . 'webclient/app.html?app=FriendChat&sessionid=' . $account->SessionID );
	//$urlstr = $account->Url . 'webclient/app.html?app=FriendChat&sessionid=' . $account->SessionID . '&data=' . urlencode( '{"type":"live","data":{"roomId":"room-a7c6d246-d92c-4c4b-8674-0cdbf30c4a6e","type":"invite","token":"private-e0d0db2a-a928-4c4e-8713-3e29e5ba638e","host":"wss://bomb.openfriendup.net/hello/live/","version":"1.0.0"}}' );
	//$urlstr = $account->Url . 'webclient/app.html?app=FriendChat&sessionid=' . $account->SessionID . '&data=' . urlencode( '{"type":"live-host"}' );
	//$urlstr = $account->Url . 'webclient/app.html?app=FriendChat&theme=amigadark&data=' . urlencode( '{"type":"live-host"}' );
	//$urlstr = $account->Url . 'webclient/app.html?app=FriendChat&theme=borderless&data=' . urlencode( '{"type":"live-host"}' );
	$urlstr = $account->Url . 'webclient/app.html?app=FriendChat&data=' . urlencode( '{"type":"live-host"}' );
	
	die( 'ok<!--separate-->' . $urlstr );
}

die( 'fail' );

?>
