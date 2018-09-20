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
<div class="recoverbox">
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
					<form id="RecoverForm">
						<div class="heading">
							<h2><?= i18n( 'i18n_Recover Account' ) ?></h2>
						</div>
						<table>
							<tr class="Row1">
								<td class="Col1"><?= i18n( 'i18n_RecoveryKey' ) ?>: </td>
								<td class="Col2">
									<input type="hidden" id="UniqueID" value="<?= $_REQUEST['recover'] ?>"/>
									<input type="text" id="Key" placeholder="<?= i18n( 'i18n_RecoveryKey here' ) ?>" <?= $_REQUEST['key'] ? ( 'value="' . $_REQUEST['key'] . '"' ) : '' ?>/>
								</td>
							</tr>
							<tr class="Row2">
								<td class="Col1"><?= i18n( 'i18n_Username' ) ?>: </td>
								<td class="Col2">
									<input type="text" id="Username" placeholder="<?= i18n( 'i18n_Username here' ) ?>" <?= $_REQUEST['user'] ? ( 'value="' . $_REQUEST['user'] . '"' ) : '' ?>/>
								</td>
							</tr>
							<tr class="Row3">
								<td class="Col1"><?= i18n( 'i18n_Password' ) ?>: </td>
								<td class="Col2">
									<input type="password" id="Password" placeholder="<?= i18n( 'i18n_New Password here' ) ?>" <?= $_REQUEST['key'] ? ( 'value="' . $_REQUEST['key'] . '"' ) : '' ?>/>
								</td>
							</tr>
							<tr class="Row4">
								<td class="Col1"><?= i18n( 'i18n_Re-type' ) ?>: </td>
								<td class="Col2">
									<input type="password" id="Confirmed" placeholder="<?= i18n( 'i18n_Re-type Password here' ) ?>" <?= $_REQUEST['key'] ? ( 'value="' . $_REQUEST['key'] . '"' ) : '' ?>/>
								</td>
							</tr>
							<tr class="ButtonRow">
								<td class="buttons" colspan="2">
									<button type="button"><span><?= i18n( 'i18n_Recover' ) ?></span></button>
								</td>
							</tr>
						</table>
					</form>
				</div>
			<!--</td>
		</tr>
	</table>-->
</div>

<script> initRecover('<?= $_REQUEST['auto'] ?>'); </script>
