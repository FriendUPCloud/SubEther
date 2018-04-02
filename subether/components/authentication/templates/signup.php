<? /*******************************************************************************
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
*******************************************************************************/ ?>
<div class="signupbox">
	<table>
		<tr>
			<td class="Col1">
				<div id="InfoBox">
					<div>
						<h2>Decentralization</h2>
						<p>Instead of everyone’s data being contained on huge central servers owned by a large organization, local servers (“nodes”) can be set up anywhere in the world. You choose which node to register with - perhaps your local node - and seamlessly connect with the subether community worldwide.</p>
					</div>
					<div>
						<h2>Freedom</h2>
						<p>You can be whoever you want to be in subether. Unlike some networks, you don’t have to use your real identity. You can interact with whomever you choose in whatever way you want. The only limit is your imagination. subether is also Free Software, giving you liberty to use it as you wish.</p>
					</div>
					<div>
						<h2>Privacy</h2>
						<p>In subether you own your data. You do not sign over any rights to a corporation or other interest who could use it. With subether, your friends, your habits, and your content is your business ... not ours! In addition, you choose who sees what you share, using Permissions.</p>
					</div>
				</div>
			</td>
			<td class="Col2">
				<div id="SignupForm">
					<form name="Signup" action="?component=authentication&action=signup" method="post">
						<table>
							<tr>
								<td class="head" colspan="2"><h2>Sign Up</h2></td>
							</tr>
							<!--<tr>
								<td class="Col1"><input name="Firstname" placeholder="First Name"/></td>
								<td class="Col2"><input name="Lastname" placeholder="Last Name"/></td>
							</tr>-->
							<tr>
								<td>Username: </td>
								<td><input type="text" name="Username" class="obl" placeholder="Your Username"/></td>
							</tr>
							<tr>
								<td>Email: </td>
								<td><input type="email" name="Email" class="obl" placeholder="Your Email"/></td>
							</tr>
							<tr>
								<td>Confirm Email: </td>
								<td><input type="email" name="ConfirmEmail" class="obl" placeholder="Re-enter Email"/></td>
							</tr>
							<tr>
								<td>Password: </td>
								<td><input type="password" name="Password" class="obl" placeholder="Password"/></td>
							</tr>
							<tr class="disabled">
								<td>AuthKey: </td>
								<td><input id="AuthKey" type="text" name="AuthKey" class="disabled" placeholder="Your AuthKey"/></td>
							</tr>
							<tr>
								<td class="buttons" colspan="2">
									<button type="button" onclick="checkform( document.Signup, '?component=authentication&action=signup' )"><span>Sign Up</span></button>
								</td>
							</tr>
						</table>
					</form>
				</div>
			</td>
		</tr>
	</table>
</div>
