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
<table>
	<!--<tr>
		<td><label><?= i18n( 'i18n_Current' ) ?>:</label></td>
		<td><input type="password" id="Validation"/></td>
	</tr>-->
	<tr>
		<td><label><?= i18n( 'i18n_Recovery' ) ?>:</label>
		<td><input type="text" id="Recovery" readonly="true"/></td>
	</tr>
	<tr>
		<td><label><?= i18n( 'i18n_New' ) ?>:</label></td>
		<td><input type="password" id="Password"/></td>
	</tr>
	<tr>
		<td><label><?= i18n( 'i18n_Re-type New' ) ?>:</label></td>
		<td><input type="password" id="Confirmed"/></td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="hidden" id="Username" value="<?= $GLOBALS['webuser']->Email ?>"/>
			<input type="hidden" id="UniqueID" value="<?= UniqueID() ?>"/>
			<hr>
		</td>
	</tr>
	<tr>
		<!--<td colspan="2"><button id="Submit" class="disabled" name="account_password">Save Changes</button><button onclick="closeAccount()">Cancel</button></td>-->
		<td colspan="2">
			<button class="save_btn" id="Submit" onclick="changePassword()" name="account_password"><?= i18n( 'i18n_Save' ) ?></button>
			<button class="cancel_btn" onclick="closeAccount()"><?= i18n( 'i18n_Cancel' ) ?></button>
		</td>
	</tr>
</table>
<script> ge( 'Recovery' ).value = getBrowserStorage( 'recoverykey' ); </script>
