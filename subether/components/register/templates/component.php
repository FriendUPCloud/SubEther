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
	<!--<table>
		<tr>
			<td class="LeftCol">
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
			<td class="RightCol">-->
				<div id="SignupBox">
					<form id="SignupForm" name="Signup" action="<?= $this->parent->mpath ?>?component=register&action=signup" method="post">
						<div class="heading">
							<h2>Sign Up</h2>
						</div>
						<table>
							<!--<tr>
								<td>Firstname: </td>
								<td><input type="text" name="Firstname" placeholder="Your Firstname"/></td>
							</tr>
							<tr>
								<td>Lastname: </td>
								<td><input type="text" name="Lastname" placeholder="Your Lastname"/></td>
							</tr>-->
							<tr class="Row1">
								<td class="Col1">Username: </td>
								<td class="Col2"><input type="text" name="Username" placeholder="Username"/></td>
							</tr>
							<tr class="Row2">
								<td class="Col1">Email: </td>
								<td class="Col2"><input type="email" name="Email" placeholder="Email"/></td>
							</tr>
							<tr class="ButtonRow">
								<!--<td class="Col1"></td>-->
								<td class="Col2 buttons" colspan="2" style="text-align:left;">
									<?if( IsSystemAdmin() ) { ?>
									<input type="checkbox" name="StoreKey" style="width:auto;margin-right:5px;position:relative;top:1px;margin-top:12px;"/><span style="margin-top:12px;">Store key in database</span>
									<? } ?>
									<button type="button" id="SignupButton" style="float:right;"><span>Join Network</span></button>
								</td>
							</tr>
						</table>
					</form>
				</div>
			<!--</td>
		</tr>
	</table>-->
</div>

<script> initSignUp(); </script>
