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

//die( print_r( $_POST,1 ) . ' ..' );

if( $_REQUEST[ 'function' ] == 'account' && isset( $_POST[ 'tmp' ] ) )
{
	$template = str_replace( 'account_' , '', $_POST[ 'tmp' ] );
	
	// --- Template --- //
	if( file_exists ( 'subether/components/account/templates/' . $template . '.php' ) )
	{
		$tmp = new cPTemplate ( 'subether/components/account/templates/' . $template . '.php' );
		
		$tmp->parent = $parent;
		
		$u = new dbObject( 'SBookContact' );
		$u->UserID = $webuser->ID;
		if( $u->Load() )
		{
			$tmp->u =& $u;
			
			$usr = new dbObject( 'SBookContact' );
			$usr->UserID = $webuser->ID;
			if( $usr->Load() )
			{
				$obj = StringToObject( $usr->Data );
				if( !$obj )
				{
					$obj = new stdClass();
					$obj->Emails = array( $usr->Email );
					$usr->Data = ObjectToString( $obj );
				}
				if( !$obj->Emails )
				{
					$obj->Emails = array( $usr->Email );
					$usr->Data = ObjectToString( $obj );
				}
				$tmp->d =& $obj;
			}
		}
	}
	
	// --- Output --- //
	if( $tmp ) die( 'ok<!--separate-->' . $tmp->render() );
	else die( 'fail' );
}
die( 'fail' );

?>
