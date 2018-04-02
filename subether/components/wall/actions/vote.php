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

global $webuser;

if( isset( $_POST[ 'like' ] ) || isset( $_POST[ 'dislike' ] ) )
{
	$found = false;
	
	$m = new dbObject( 'SBookMessage' );
	if( $_POST[ 'like' ] > 0 && $m->Load( $_POST[ 'like' ] ) && SaveUserVote( $_POST[ 'like' ], 'wall' ) )
	{
		$rating = explode( '/', $m->Rating );
		$m->Rating = ( $rating[0] ? ( floor( $rating[0] + 1 ) . '/' . $rating[1] ) : '1/0' );
		
		// New method. TODO: Remove old. Also make support for Votes between nodes ...
		$m->RateUpBy = ( $m->RateUpBy != '' && is_string( $m->RateUpBy ) ? json_obj_decode( $m->RateUpBy, 'array' ) : array() );
		$m->RateDownBy = ( $m->RateDownBy != '' && is_string( $m->RateDownBy ) ? json_obj_decode( $m->RateDownBy, 'array' ) : array() );
		
		// Someone rating can change their mind
		if( is_array( $m->RateUpBy ) && is_array( $m->RateDownBy ) )
		{
			foreach( $m->RateUpBy as $ke=>$ru )
			{
				if( $ru == $webuser->ContactID )
				{
					$found = true;
					unset( $m->RateUpBy[$ke] );
				}
			}
			if( !$m->RateUpBy )
			{
				$m->RateUpBy = array();
			}
			foreach( $m->RateDownBy as $ke=>$rd )
			{
				if( $rd == $webuser->ContactID )
				{
					unset( $m->RateDownBy[$ke] );
				}
			}
			if( !$m->RateDownBy )
			{
				$m->RateDownBy = array();
			}
			
			if( !in_array( $webuser->ContactID, $m->RateUpBy ) && !in_array( $webuser->ContactID, $m->RateDownBy ) )
			{
				if ( !$found )
				{
					$m->RateUpBy[] = $webuser->ContactID;
				}
				$m->RateUpBy = json_obj_encode( $m->RateUpBy );
				$m->RateDownBy = json_obj_encode( $m->RateDownBy );
				$m->Save();
				
				if ( !$found )
				{
					LogStats( 'wall', 'like', $m->Type, $m->SenderID, $m->ReceiverID, $m->CategoryID );
				}
				
				die( 'ok<!--separate-->1' );
			}
		}
	}
	else if( $_POST[ 'dislike' ] > 0 && $m->Load( $_POST[ 'dislike' ] ) && SaveUserVote( $_POST[ 'dislike' ], 'wall' ) )
	{
		$rating = explode( '/', $m->Rating );
		$m->Rating = ( $rating[0] ? ( $rating[0] . '/' . floor( $rating[1] + 1 ) ) : '0/1' );
		
		// New method. TODO: Remove old. Also make support for Votes between nodes ...
		$m->RateUpBy = ( $m->RateUpBy != '' && is_string( $m->RateUpBy ) ? json_obj_decode( $m->RateUpBy, 'array' ) : array() );
		$m->RateDownBy = ( $m->RateDownBy != '' && is_string( $m->RateDownBy ) ? json_obj_decode( $m->RateDownBy, 'array' ) : array() );
		
		// Someone rating can change their mind
		if( is_array( $m->RateUpBy ) && is_array( $m->RateDownBy ) )
		{
			foreach( $m->RateUpBy as $ke=>$ru )
			{
				if( $ru == $webuser->ContactID )
				{
					unset( $m->RateUpBy[$ke] );
				}
			}
			if( !$m->RateUpBy )
			{
				$m->RateUpBy = array();
			}
			foreach( $m->RateDownBy as $ke=>$rd )
			{
				if( $rd == $webuser->ContactID )
				{
					$found = true;
					unset( $m->RateDownBy[$ke] );
				}
			}
			if( !$m->RateDownBy )
			{
				$m->RateDownBy = array();
			}
			
			if( !in_array( $webuser->ContactID, $m->RateUpBy ) && !in_array( $webuser->ContactID, $m->RateDownBy ) )
			{
				if ( !$found )
				{
					$m->RateDownBy[] = $webuser->ContactID;
				}
				$m->RateDownBy = json_obj_encode( $m->RateDownBy );
				$m->RateUpBy = json_obj_encode( $m->RateUpBy );
				$m->Save();
				
				if ( !$found )
				{
					LogStats( 'wall', 'dislike', $m->Type, $m->SenderID, $m->ReceiverID, $m->CategoryID );
				}
				
				die( 'ok<!--separate-->2' );
			}
		}
	}
	die( 'fail' );
}
// New method -----------------------------
else if( $_POST['vote'] && $_POST['mid'] )
{
	$found = false;
	
	$m = new dbObject( 'SBookMessage' );
	
	if( $m->Load( $_POST['mid'] ) )
	{
		$m->Rating = json_obj_decode( $m->Rating );
		
		if( !isset( $m->Rating->Votes ) )
		{
			$m->Rating->Votes = new stdClass();
		}
		
		foreach( $m->Rating->Votes as $check )
		{
			if( is_array( $check ) && in_array( $webuser->ContactID, $check ) )
			{
				/*foreach( $check as $key=>$val )
				{
					if( $val == $webuser->ContactID )
					{
						unset( $check[$key] );
					}
				}*/
				
				$found = true;
			}
		}
		
		if( !$found )
		{
			if( !isset( $m->Rating->Votes->{$_POST['vote']} ) )
			{
				$m->Rating->Votes->{$_POST['vote']} = array();
			}
			
			$m->Rating->Votes->{$_POST['vote']}[] = $webuser->ContactID;
			
			$m->Rating = json_obj_encode( $m->Rating );
			$m->DateModified = date ( 'Y-m-d H:i:s' );
			$m->Save();
			
			LogStats( 'wall', strtolower( $_POST['vote'] ), $m->Type, $m->SenderID, $m->ReceiverID, $m->CategoryID );
			
			die( 'ok<!--separate-->3' );
		}
	}
	
}
die( 'fail' );

?>
