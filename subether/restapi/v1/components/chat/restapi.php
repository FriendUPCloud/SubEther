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

// Command: post: --------------------------------------------------
if ( preg_match ( '/components\/chat\/post\//i', $_REQUEST[ 'route' ], $matches ) )
{
	require ( 'subether/restapi/v1/components/chat/actions/messages.php' );
}

// Command: messages: --------------------------------------------------
if ( preg_match ( '/components\/chat\/messages\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
{
	require ( 'subether/restapi/v1/components/chat/include/messages.php' );
}

// Command: chat: --------------------------------------------------
//if ( preg_match ( '/components\/chat\//i', $_REQUEST[ 'route' ], $matches ) )
//{
//	require ( 'subether/restapi/v1/components/chat/include/restapi.php' );
//}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
