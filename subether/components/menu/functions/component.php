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

include_once ( 'subether/components/menu/include/functions.php' );

$str = '';

if( $webuser->ID > 0 )
{
	$str .= '<div class="menu">';
	//$str .= 	'<table>';
	//$str .= 		'<tr>';
	//$str .= 			'<td>';
	$str .= 				'<div id="GlobalMenu">';
	$str .= 					'<ul>';
	//$str .= 						'<li><a href="javascript:void(0)" class="cpanel" onclick="openDropDownWindow( \'MenuBox\' )"><span>&nbsp;</span></a></li>';
	//$str .= 						'<li><a href="en/trading/"><span>Trading</span></a></li>';
	//$str .= 						'<li><a href="en/travel/"><span>Travel</span></a></li>';
	//$str .= 						'<li><a href="en/services/"><span>Services</span></a></li>';
	//$str .= 						'<li><a href="en/university/"><span>University</span></a></li>';
	//$str .= 						'<li><a href="en/free_energy/"><span>Free-Energy</span></a></li>';
	//$str .= 						'<li><a href="en/ecovillage/"><span>EcoVillage</span></a></li>';
	//$str .= 						'<li><a href="en/home/profile/"><span>Profile</span></a></li>';
	//$str .= 						'<li><a href="en/home/"><span>Home</span></a></li>';
	
	/*$str .=							'<li>';
	$modules = array ( 
		'main'=>'Main page', 
		'trading'=>'Trading',
		'services'=>'Services',
		'university'=>'University',
		'free_energy'=>'Free energy',
		'ecovillage'=>'Eco Village'
	);
	$str .= '<select class="Modules">';
	foreach ( $modules as $k=>$mod )
	{
		if ( !$GLOBALS['module'] )
			$GLOBALS[ 'module' ] = 'main';
		$s = $k == $GLOBALS['module'] ? ' selected="selected"' : '';
		$str .= '<option value="' . $k . '"'. $s . '>' . $mod . '</option>';
	}
	$str .= '</select>';
	$str .= 						'</li>';*/
	
	if( $menu = renderMenuList() )
	{
		//die( print_r( $parent,1 ) . ' -- ' . print_r( $menu,1 ) );
		
		foreach( $menu as $m )
		{
			if( $m->Name == 'profile' )
			{
				$str .= '<li class="profile link"><a href="profile/" ' . ( $m->Name == strtolower( $parent->module ) ? 'class="current"' : '' ) . '><span>' . i18n( 'i18n_' . $m->DisplayName ) . '</span></a></li>';
				
				$user = CacheUser( $webuser->ContactID );
				$user = $user->ContactInfo;
				
				$imgurl = 'admin/gfx/arenaicons/user_johndoe_128.png';
				$str .= '<li class="profile thumb">';
				$str .= '<a href="' . $user->Username . '" ' . ( $m->Name == strtolower( $parent->module ) ? 'class="current"' : '' ) . '>';
				$i = new dbImage ();
				if( $i->Load( $user->ImageID ) )
				{
					$imgurl1 = ( BASE_URL . 'secure-files/images/' . ( $i->UniqueID ? $i->UniqueID : $i->ID ) . '/' );
					//$imgurl = str_replace( ' ', '%20', $i->getImageURL() );
					
					if ( !FileExists( $imgurl1 ) )
					{
						//
					}
					else
					{
						$imgurl = $imgurl1;
					}
				}
				$name = explode( ' ', GetUserDisplayname( $webuser->ContactID ) );
				//$str .= '<img class="image" src="' . $imgurl . '" />';
				$str .= '<div class="image" style="background-image:url(\'' . $imgurl . '\');background-repeat:no-repeat;background-size:cover;background-position:center center;"></div>';
				$str .= '<span class="name">' . reset( $name ) . '</span>';
				$str .= '<div class="clearboth" style="clear:both"></div>';
				$str .= '</a></li>';
			}
			else
			{
				$str .= '<li class="' . $m->Name . ' link"><a href="' . ( $m->Name != 'main' ? ( $m->Name . '/' ) : 'home/' ) . '" ' . ( $m->Name == strtolower( $parent->module ) ? 'class="current"' : '' ) . '><span>' . i18n( 'i18n_' . $m->DisplayName ) . '</span></a></li>';
			}
		}
	}
	
	$str .= 					'</ul>';
	$str .= 				'</div>';
	//$str .= 			'</td>';
	//$str .= 		'</tr>';
	//$str .= 		'<tr>';
	//$str .= 			'<td>';
	//$str .= 				'<div id="MenuBox">';
	//$str .= 					'<div class="toparrow"></div>';
	//$str .= 					'<div id="UserMenu">';
	//$str .= 						'<ul>';
	//$str .= 							'<li><a href="en/home/account/"><span>Account Settings</span></a></li>';
	//$str .= 							'<li><a href="en/home/global/"><span>Global Settings</span></a></li>';
	//$str .= 							'<li><a href="javascript:void(0)" onclick="logout()"><span>Log Out</span></a></li>';
	//$str .= 						'</ul>';
	//$str .= 					'</div>';
	//$str .= 				'</div>';
	//$str .= 			'</td>';
	//$str .= 		'</tr>';
	//$str .= 	'</table>';
	$str .= '</div>';
}

if( isset( $_REQUEST['function'] ) && $_REQUEST['function'] == 'component' ) die( 'ok<!--separate2-->' . $str );

?>
