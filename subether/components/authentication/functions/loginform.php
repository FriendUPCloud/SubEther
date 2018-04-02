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

$str  = '
<div class="Authentication">
 	<div id="LoginFront">
 		<div id="LoginForm">
 			<form name="Authentication">
 				<table>
 					<tr class="Row1">
 						<td class="Col1 Span2" colspan="2">
 							<input type="email" id="webuser" 
 								onkeyup="if(event.keyCode==13){ge(\'LoginButton\').onclick()}" name="Username" placeholder="' . i18n( 'i18n_Email' ) . '"/>
 						</td>
 					</tr>
 					<tr class="Row2">
 						<td class="Col1 Span2" colspan="2">
 							<input id="inputpw" type="password" 
 								onpaste="setTimeout( function() { md5cryptate(this,document.Authentication) }, 0 )" 
 								onkeydown="md5cryptate(this,document.Authentication);if(event.keyCode==13){ge(\'LoginButton\').onclick()}" 
 								placeholder="' . i18n( 'i18n_Password' ) . '"/>
 							<input type="hidden" name="Password"/>
 						</td>
 					</tr>
 					<tr class="Row3">
 						<td class="Col1">
 							<div><input type="checkbox" name="Remember"/> <span>' . i18n( 'i18n_Keep me logged in' ) . '</span></div>
 						</td>
 						<td class="Col2">
 							<button id="LoginButton" type="button" onclick="login( document.Authentication )"><span>' . i18n( 'i18n_Login' ) . '</span></button>
 						</td>
 					</tr>
 					<tr class="Row4">
 						<td class="Col1 Span2" colspan="2">
 							<div>
 								<a href="javascript:void(0)" 
 									onclick="openWindow( \'Authentication\', false, \'recover\' )">
 										' . i18n( 'i18n_Forgot your password' ) . '?</a></div>
 						</td>
 					</tr>
 				</table>
 			</form>
 		</div>
 	</div>
 </div>
';

?>
