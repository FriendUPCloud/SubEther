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

// Command: save: --------------------------------------------------
if ( preg_match ( '/components\/events\/save\//i', $_REQUEST[ 'route' ], $matches ) )
{
	require ( 'subether/restapi/v1/components/events/actions/save.php' );
}

// Command: delete: --------------------------------------------------
if ( preg_match ( '/components\/events\/delete\//i', $_REQUEST[ 'route' ], $matches ) )
{
	require ( 'subether/restapi/v1/components/events/actions/delete.php' );
}

// Command: events: --------------------------------------------------
if ( preg_match ( '/components\/events\//i', $_REQUEST[ 'route' ], $matches ) )
{
	require ( 'subether/restapi/v1/components/events/functions/events.php' );
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
