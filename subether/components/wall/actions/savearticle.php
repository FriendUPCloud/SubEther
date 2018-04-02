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

if( $parent )
{
	$sm = new dbObject ( 'SBookMessage' );
	$sm->Date = date ( 'Y-m-d H:i:s' );
	$sm->DateModified = $sm->Date;
	$sm->Subject = strip_tags ( $_POST[ 'Heading' ] );
	$sm->Leadin = mysql_real_escape_string ( $_POST[ 'Leadin' ] );
	$sm->Message = mysql_real_escape_string ( $_POST[ 'Article' ] );
	$sm->Type = 'article';
	$sm->CategoryID = $parent->folder->CategoryID;
	$sm->SenderID = $parent->webuser->ContactID;
	if( strtolower( $parent->folder->MainName ) == 'profile' )
	{
		$sm->ReceiverID = $parent->cuser->ContactID;
	}
	$sm->Save();
	
	LogStats( 'wall', 'save', $sm->Type, $sm->SenderID, $sm->ReceiverID, $sm->CategoryID );
	
	/*if ( isset( $parent->folder->CategoryID ) || isset( $parent->folder->ID ) )
	{	
		$mr = new dbObject ( 'SBookRelation' );
		if( strtolower( $parent->folder->MainName ) == 'profile' )
		{
			$mr->ConnectedType = 'SBookCategoryRelation';
			$mr->ConnectedID = $parent->folder->ID;
		}
		else
		{
			$mr->ConnectedType = 'SBookCategory';
			$mr->ConnectedID = $parent->folder->CategoryID;
		}
		$mr->ObjectType = 'SBookMessage';
		$mr->ObjectID = $sm->ID;
		$mr->Type = 'WallArticle';
		$mr->Save();
	}*/
	die ( $sm->ID > 0 ? ( 'ok<!--separate-->' . $sm->ID ) : 'fail<!--separate-->0' );
}
die( 'fail' );

?>
