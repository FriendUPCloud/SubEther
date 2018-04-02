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

if( $_POST[ 'type' ] == 'media' && ( isset( $_POST[ 'like' ] ) || isset( $_POST[ 'dislike' ] ) ) )
{
	$f = new dbObject( 'SBookFiles' );
	if( $_POST[ 'like' ] > 0 && $f->Load( $_POST[ 'like' ] ) )
	{
		$rating = explode( '/', $f->Rating );
		$f->Rating = ( $rating[0] ? ( floor( $rating[0] + 1 ) . '/' . $rating[1] ) : '1/0' );
		$f->Save();
	}
	else if( $_POST[ 'dislike' ] > 0 && $f->Load( $_POST[ 'dislike' ] ) )
	{
		$rating = explode( '/', $f->Rating );
		$f->Rating = ( $rating[0] ? ( $rating[0] . '/' . floor( $rating[1] + 1 ) ) : '0/1' );
		$f->Save();
	}
	die( 'ok<!--separate-->' );
}
else if( $_POST[ 'type' ] == 'comment' && ( isset( $_POST[ 'like' ] ) || isset( $_POST[ 'dislike' ] ) ) )
{
	$m = new dbObject( 'SBookMessage' );
	if( $_POST[ 'like' ] > 0 && $m->Load( $_POST[ 'like' ] ) )
	{
		$rating = explode( '/', $m->Rating );
		$m->Rating = ( $rating[0] ? ( floor( $rating[0] + 1 ) . '/' . $rating[1] ) : '1/0' );
		$m->Save();
	}
	else if( $_POST[ 'dislike' ] > 0 && $m->Load( $_POST[ 'dislike' ] ) )
	{
		$rating = explode( '/', $m->Rating );
		$m->Rating = ( $rating[0] ? ( $rating[0] . '/' . floor( $rating[1] + 1 ) ) : '0/1' );
		$m->Save();
	}
	die( 'ok<!--separate-->' );
}
die( 'fail' );

?>
