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

// Command: recover: --------------------------------------------------
if ( preg_match ( '/components\/register\/recover\//i', $_REQUEST[ 'route' ], $matches ) )
{
	require ( 'subether/restapi/v1/components/register/actions/recover.php' );
}

// Command: activate: --------------------------------------------------
if ( preg_match ( '/components\/register\/activate\//i', $_REQUEST[ 'route' ], $matches ) )
{
	require ( 'subether/restapi/v1/components/register/actions/activate.php' );
}

// Command: invite: --------------------------------------------------
//if ( preg_match ( '/components\/register\/invite\//i', $_REQUEST[ 'route' ], $matches ) )
//{
//	require ( 'subether/restapi/v1/components/register/actions/invite.php' );
//}

// Command: limited: --------------------------------------------------
//if ( preg_match ( '/components\/register\/limited\//i', $_REQUEST[ 'route' ], $matches ) )
//{
//	require ( 'subether/restapi/v1/components/register/actions/limited.php' );
//}

// Command: register: --------------------------------------------------
if ( preg_match ( '/components\/register\//i', $_REQUEST[ 'route' ], $matches ) )
{
	require ( 'subether/restapi/v1/components/register/actions/register.php' );
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
