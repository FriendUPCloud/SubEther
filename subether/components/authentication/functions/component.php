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

include_once ( 'subether/include/functions.php' );
include_once ( 'subether/components/authentication/include/functions.php' );

$str = '';

if( $webuser->ID > 0 )
{
	$str .= '<div class="menu">';
	//$str .= 	'<table>';
	//$str .= 		'<tr>';
	//$str .= 			'<td>';
	$str .= 		'<ul>';
	$str .= 			'<li>';
	$str .= 				'<div id="DropDownMenu">';
	$str .= 					'<ul>';
	$str .= 						'<li><a href="javascript:void(0)" class="cpanel" onclick="openDropDownWindow( \'MenuBox\' )"><span>&nbsp;</span></a></li>';
	$str .= 					'</ul>';
	$str .= 				'</div>';
	$str .= 			'</li>';
	//$str .= 			'</td>';
	//$str .= 		'</tr>';
	//$str .= 		'<tr>';
	//$str .= 			'<td>';
	$str .= 			'<li>';
	$str .= 				'<div id="MenuBox">';
	$str .= 					'<div class="toparrow"></div>';
	$str .= 					'<div id="UserMenu">';
	$str .= 						'<ul>';
	//$str .= 							'<li><a href="en/home/account/"><span>Account Settings</span></a></li>';
	//$str .= 							'<li><a href="en/home/global/"><span>Global Settings</span></a></li>';
	//die( print_r( $parent,1 ) . ' --' );
	if( $menu = renderDropDownList( $parent->position ) )
	{
		if( isset( $_REQUEST['chris'] ) ) die( print_r( $menu,1 ) . ' --' );
		foreach( $menu as $m )
		{
			$m->DisplayName = $m->Name == 'profile' ? $parent->cuser->Username : $m->DisplayName;
			
			$str .= '<li class="maincategory ' . $m->Name . '"><a class="maincategory ' . $m->Name . '" href="' . ( $m->Name != 'main' ? ( ( $m->Name == 'profile' ? $parent->cuser->Username : $m->Name ) . '/' ) : 'home/' ) . '"><span>' . i18n( 'i18n_' . ( $m->DisplayName ? $m->DisplayName : $m->Name ) ) . '</span></a>';
			
			if( $m->Components && is_array( $m->Components ) )
			{
				$str .= '<ul class="categorylist ' . $m->Name . '">';
				
				foreach( $m->Components as $c )
				{
					if ( ComponentExists( $c->Name, $c->Module ) && ComponentAccess( $c->Name, $c->Module ) && file_exists( 'subether/components/' . $c->Name . '/include/panel.php' ) )
					{
						$str .= '<li class="subcategory ' . $c->Name . '">';
						$module = $c->Module;
						include_once ( 'subether/components/' . $c->Name . '/include/panel.php' );
						$str .= '</li>';
					}
				}
				
				$str .= '</ul>';
			}
			
			$str .= '</li>';
		}
	}
	
	$str .= 							'<li class="maincategory report"><a href="javascript:void(0)" onclick="openWindow( \'Admin\', \'Component\', \'report\' )"><span>' . i18n( 'i18n_Report a Problem' ) . '</span></a></li>';
	
	if ( isset( $_SESSION['UserAgent'] ) && $_SESSION['UserAgent'] == 'presentation' )
	{
		$str .= 							'<li class="maincategory fullscreenmode"><a href="javascript:void(0)" onclick="toggleFullScreen()"><span>' . i18n( 'i18n_Fullscreen view' ) . '</span></a></li>';
		$str .= 							'<li class="maincategory defaultmode"><a href="javascript:void(0)" onclick="toggleFullScreen()"><span>' . i18n( 'i18n_Default view' ) . '</span></a></li>';
	}
	
	$str .= 							'<li class="maincategory logout"><a href="javascript:void(0)" onclick="logout()"><span>' . i18n( 'i18n_Log Out' ) . '</span></a></li>';
	$str .= 						'</ul>';
	$str .= 					'</div>';
	$str .= 				'</div>';
	$str .= 			'</li>';
	$str .= 		'</ul>';
	//$str .= 			'</td>';
	//$str .= 		'</tr>';
	//$str .= 	'</table>';
	$str .= '</div>';
}
else
{
	$str  = '<div class="Authentication">';
	//$str .= 	'<table>';
	//$str .= 		'<tr>';
	//$str .= 			'<td>';
	$str .= 		'<ul>';
	$str .= 			'<li>';
	$str .= 				'<div class="buttons">';
	$str .= 					'<button class="loginbtn" onclick="openLoginBox()">Login</button>';
	/*$str .= 					'<button onclick="openSignupBox()">Sign up</button>';*/
	$str .= 					'<button class="signupbtn" onclick="document.location=\'en/register/\'">Sign up</button>';
	$str .= 				'</div>';
	$str .= 			'</li>';
	//$str .= 			'</td>';
	//$str .= 		'</tr>';
	//$str .= 		'<tr>';
	//$str .= 			'<td>';
	$str .= 			'<li>';
	$str .= 				'<div id="LoginBox">';
	$str .= 					'<div class="toparrow"></div>';
	$str .= 					'<div id="LoginForm">';
	$str .= 						'<form name="Authentication">';
	$str .= 							'<table>';
	$str .= 								'<tr class="Row1">';
	$str .= 									'<td class="Col1 Span2" colspan="2">';
	$str .= 										'<input type="text" id="webuser" onkeyup="if(event.keyCode==13){ge(\'LoginButton\').onclick()}" name="Username" placeholder="Email"/>';
	$str .= 									'</td>';
	$str .= 								'</tr>';
	$str .= 								'<tr class="Row2">';
	$str .= 									'<td class="Col1 Span2" colspan="2">';
	$str .= 										'<input id="inputpw" type="password" onpaste="setTimeout( function() { md5cryptate(this,document.Authentication) }, 0 )" onkeyup="md5cryptate(this,document.Authentication);if(event.keyCode==13){ge(\'LoginButton\').onclick()}" placeholder="Password"/>';
	$str .= 										'<input type="hidden" name="Password"/>';
	$str .= 									'</td>';
	$str .= 								'</tr>';
	$str .= 								'<tr class="Row3">';
	$str .= 									'<td class="Col1">';
	$str .= 										'<div><input type="checkbox" name="Remember"/> <span>Keep me logged in</span></div>';
	$str .= 									'</td>';
	$str .= 									'<td class="Col2">';
	$str .= 										'<button id="LoginButton" type="button" onclick="login( document.Authentication )"><span>Login</span></button>';
	$str .= 									'</td>';
	$str .= 								'</tr>';
	$str .= 								'<tr class="Row4">';
	$str .= 									'<td class="Col1 Span2" colspan="2">';
	$str .= 										'<div><a href="javascript:void(0)" onclick="openWindow( \'Authentication\', false, \'recover\' )">Forgot your password?</a></div>';
	$str .= 									'</td>';
	$str .= 								'</tr>';
	$str .= 							'</table>';
	$str .= 						'</form>';
	$str .= 					'</div>';
	$str .= 				'</div>';
	$str .= 			'</li>';
	$str .= 		'</ul>';
	//$str .= 			'</td>';
	//$str .= 		'</tr>';
	//$str .= 	'</table>';
	$str .= '</div>';
}

if( isset( $_REQUEST['function'] ) && $_REQUEST['function'] == 'component' ) die( 'ok<!--separate2-->' . $str );

?>
