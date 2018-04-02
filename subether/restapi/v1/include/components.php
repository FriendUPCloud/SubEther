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

allowAccess();

// Command: wall: ------------------------------------------------------
if ( preg_match ( '/components\/wall\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
{
	require ( 'subether/restapi/v1/components/wall/restapi.php' );
}

// Command: category: ---------------------------------------------------
if ( preg_match ( '/components\/category\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
{
	require ( 'subether/restapi/v1/components/category/restapi.php' );
}

// Command: library: ---------------------------------------------------
if ( preg_match ( '/components\/library\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
{
	require ( 'subether/restapi/v1/components/library/restapi.php' );
}

// Command: contacts: --------------------------------------------------
if ( preg_match ( '/components\/contacts\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
{
	require ( 'subether/restapi/v1/components/contacts/restapi.php' );
}

// Command: groups: ----------------------------------------------------
if ( preg_match ( '/components\/groups\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
{
	require ( 'subether/restapi/v1/components/groups/restapi.php' );
}

// Command: chat: ----------------------------------------------------
if ( preg_match ( '/components\/chat\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
{
	require ( 'subether/restapi/v1/components/chat/restapi.php' );
}

// Command: messages: ----------------------------------------------------
//if ( preg_match ( '/components\/messages\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
//{
//	require ( 'subether/restapi/v1/components/messages/restapi.php' );
//}

// Command: irc: ----------------------------------------------------
//if ( preg_match ( '/components\/irc\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
//{
//	require ( 'subether/restapi/v1/components/irc/restapi.php' );
//}

// Command: events: ------------------------------------------------
if ( preg_match ( '/components\/events\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
{
	require ( 'subether/restapi/v1/components/events/restapi.php' );
}

// Command: register: ------------------------------------------------
if ( preg_match ( '/components\/register\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
{
	require ( 'subether/restapi/v1/components/register/restapi.php' );
}

// Command: statistics: ------------------------------------------------
if ( preg_match ( '/components\/statistics\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
{
	require ( 'subether/restapi/v1/components/statistics/restapi.php' );
}

// Command: notification: ------------------------------------------------
if ( preg_match ( '/components\/notification\//i', $_REQUEST[ 'route' ], $matches ) && verifySessionId() )
{
	require ( 'subether/restapi/v1/components/notification/restapi.php' );
}

// Give default error
throwXmlError ( MISSING_PROTOCOL_IDENTIFIER );

?>
