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

/*
if( isset( $_POST[ 'search' ] ) )
{
	$fm = findMembers ( $_POST[ 'search' ] );
	
	if( $fm )
	{
		$mg = new dbObject( 'SBookCategory' );
		$mg->ID = $_POST[ 'groupid' ];
		if( $mg->load() )
		{
			$gcheck = $mg->Type;
		}

		$str = '<div><table>';
		foreach( $fm as $m )
		{
			$cr = new dbObject( 'SBookCategoryRelation' );
			$cr->ObjectType = 'Users';
			$cr->ObjectID = $m->UserID;
			$cr->CategoryID = $_POST[ 'groupid' ];
			if( $cr->load() && $gcheck && $gcheck != 'Group' )
			{
				$str .= '<tr>';
				$str .= '<td style="width:35px;"><div class="image">';
				$i = new dbImage ();
				if( $i->load( $m->ImageID ) )
				{
					$str .= $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
				}
				$str .= '</div></td>';
				$str .= '<td><div>' . $m->Username . '<br/><span>already a member</span></div></td>';
				$str .= '</tr>';
			}
			else
			{
				$str .= '<tr onclick="selectMember( \'' . $m->UserID . '\', \'' . $m->Username . '\' )">';
				$str .= '<td style="width:35px;"><div class="image">';
				$i = new dbImage ();
				if( $i->load( $m->ImageID ) )
				{
					$str .= $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
				}
				$str .= '</div></td>';
				$str .= '<td><div>' . $m->Username . '</div></td>';
				$str .= '</tr>';
			}
		}
		$str .= '</table></div>';
	}
	
	if( isset( $_REQUEST[ 'bajaxrand' ] ) )
	{
		if( $fm ) die ( 'ok<!--separate-->' . $str ); else die ( 'fail' );
	}
}*/

?>
