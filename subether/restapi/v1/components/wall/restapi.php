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
if ( preg_match ( '/components\/wall\/post\//i', $_REQUEST[ 'route' ], $matches ) )
{
	require ( 'subether/restapi/v1/components/wall/actions/post.php' );
}

// Command: parse: --------------------------------------------------
if ( preg_match ( '/components\/wall\/parse\//i', $_REQUEST[ 'route' ], $matches ) )
{
	require ( 'subether/restapi/v1/components/wall/include/parse.php' );
}

// Command: posts: --------------------------------------------------
if ( preg_match ( '/components\/wall\/posts\//i', $_REQUEST[ 'route' ], $matches ) )
{
	require ( 'subether/restapi/v1/components/wall/include/posts.php' );
}

// Give default error
throwXmlError ( MISSING_PARAMETERS );

?>
