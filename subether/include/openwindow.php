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

global $database, $webuser;

if( isset( $_POST[ 'query' ] ) )
{
	$ParentID = $_POST[ 'pid' ];
	$Mode = isset( $_REQUEST[ 'fullscreen' ] ) ? 'fullscreen' : 'default';
	$Resolution = isset( $_REQUEST[ 'res' ] ) ? $_REQUEST[ 'res' ] : '';
	$Act = $_POST[ 'act' ];
	
	switch ( $_POST[ 'query' ] )
	{
		case 'Groups':
			if( file_exists ( 'subether/components/groups/templates/groups_' . $_POST[ 'act' ] . '.php' ) )
			{
				include_once ( 'subether/components/groups/functions/windowgallery.php' );
				$t = new cPTemplate ( 'subether/components/groups/templates/groups_' . $_POST[ 'act' ] . '.php' );
				$t->Content = $str;
				$t->obj = $obj;
			}
			break;
		case 'Library':
			if( file_exists ( 'subether/components/library/templates/library_' . $_POST[ 'act' ] . '.php' ) )
			{
				if( $_POST['act'] == 'content' )
				{
					if( $vars = json_decode( $_POST['pid'] ) )
					{
						if ( $vars->fid )
						{
							$_POST['fid'] = $vars->fid;
						}
						if ( $vars->mid )
						{
							$_POST['mid'] = $vars->mid;
						}
					}
					else
					{
						$_POST['fid'] = $_POST[ 'pid' ];
					}
					
					include_once ( 'subether/components/library/component.php' );
				}
				else if( $_POST['act'] == 'share' )
				{
					$_POST['mid'] = $_POST[ 'pid' ];
					
					include_once ( 'subether/components/library/functions/share.php' );
				}
				else
				{
					include_once ( 'subether/components/library/functions/windowgallery.php' );
				}
				$t = new cPTemplate ( 'subether/components/library/templates/library_' . $_POST[ 'act' ] . '.php' );
				$t->Content = $str;
				$t->obj = $obj;
			}
			break;
		case 'Members':
			if( file_exists ( 'subether/components/members/templates/' . $_POST[ 'act' ] . '.php' ) )
			{
				$t = new cPTemplate ( 'subether/components/members/templates/' . $_POST[ 'act' ] . '.php' );
			}
			break;
		case 'Profile':
			if( file_exists ( 'subether/components/profile/templates/profile_' . $_POST[ 'act' ] . '.php' ) )
			{
				include_once ( 'subether/components/profile/functions/windowgallery.php' );
				$t = new cPTemplate ( 'subether/components/profile/templates/profile_' . $_POST[ 'act' ] . '.php' );
				$t->Content = $str;
				$t->obj = $obj;
			}
			break;
		case 'Authentication':
			if( file_exists ( 'subether/components/authentication/templates/' . $_POST[ 'act' ] . '.php' ) )
			{
				$t = new cPTemplate ( 'subether/components/authentication/templates/' . $_POST[ 'act' ] . '.php' );
			}
			break;
		case 'Wall':
			if( file_exists ( 'subether/components/wall/templates/' . $_POST[ 'act' ] . '.php' ) )
			{
				include_once ( 'subether/components/wall/functions/share.php' );
				$t = new cPTemplate ( 'subether/components/wall/templates/' . $_POST[ 'act' ] . '.php' );
				$t->obj = $obj;
			}
			break;
		case 'Chat':
			if( file_exists ( 'subether/components/chat/templates/' . $_POST[ 'act' ] . '.php' ) )
			{
				$_POST[ 'u' ] = $ParentID;
				include_once ( 'subether/components/chat/functions/chat.php' );
				$t = new cPTemplate ( 'subether/components/chat/templates/' . $_POST[ 'act' ] . '.php' );
				$t->obj->u = $u;
				$t->obj->Content = $str;
			}
			break;
		case 'Admin':
			if( file_exists ( 'subether/components/admin/templates/' . $_POST[ 'act' ] . '.php' ) )
			{
				$t = new cPTemplate ( 'subether/components/admin/templates/' . $_POST[ 'act' ] . '.php' );
			}
			break;
		case 'Bank':
			if( $_POST[ 'act' ] == 'payment' )
			{
				include_once ( 'subether/components/bank/include/payment.php' );
				$t = new cPTemplate ( 'subether/components/bank/templates/' . $_POST[ 'act' ] . '.php' );
				$t->Content = $str;
			}
			else if( $_POST[ 'act' ] == 'transfer' )
			{
				include_once ( 'subether/components/bank/include/transfer.php' );
				$t = new cPTemplate ( 'subether/components/bank/templates/' . $_POST[ 'act' ] . '.php' );
				$t->Content = $str;
			}
			else if( file_exists ( 'subether/components/bank/templates/' . $_POST[ 'act' ] . '.php' ) )
			{
				$t = new cPTemplate ( 'subether/components/bank/templates/' . $_POST[ 'act' ] . '.php' );
			}
			break;
		case 'Events':
			if( file_exists ( 'subether/components/events/templates/' . $_POST[ 'act' ] . '.php' ) )
			{
				$_POST['date'] = date( 'Y-m-d' );
				$_POST['mode'] = 'extended2';
				include_once ( 'subether/components/events/functions/eventedit.php' );
				$t = new cPTemplate ( 'subether/components/events/templates/' . $_POST[ 'act' ] . '.php' );
				$t->Content = $str;
			}
			break;
		case 'Page':
			include_once ( 'subether/components/pages/component.php' );
			//include_once ( 'admin/system.php' );
			$t = new cPTemplate ( 'subether/components/pages/templates/' . $_POST[ 'act' ] . '.php' );
			//$t->Content = $str;
			break;
		case 'Messages':
			$t = new cPTemplate ( 'subether/components/chat/templates/' . $_POST[ 'act' ] . '.php' );
			$t->obj = $database->fetchObjectRow( 'SELECT * FROM SBookMailAccounts WHERE UserID = \'' . $webuser->ID . '\' AND ID = \'' . $ParentID . '\' AND IsDeleted = "0"' );
			break;
	}
	
	$t->ParentID = $ParentID;
	$t->Mode = $Mode;
	$t->Resolution = $Resolution;
	$t->parent = $parent;
	
	if( isset( $_REQUEST[ 'bajaxrand' ] ) )
	{
		if( $t ) die ( 'ok<!--separate-->' . $t->render() ); else die ( 'fail' );
	}
}

?>
