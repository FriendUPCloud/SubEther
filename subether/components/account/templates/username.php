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
	<tr>
		<td colspan="2">
			<span><?= i18n( 'i18n_Your public username is the same as your address for' ) ?>:</span>
			<ul>
				<li><?= i18n( 'i18n_Profile' ) ?>: <?= str_replace( array( 'http://', 'www.' ), '', BASE_URL ) . '<strong>' . trim ( strtolower ( preg_replace ( '/[\\s]+/', '.', ( $this->u->Username ? $this->u->Username : i18n( 'i18n_username' ) ) ) ) ). '</strong>' ?></li>
				<li><?= i18n( 'i18n_Email' ) ?>: <?= '<strong>' . trim ( strtolower ( preg_replace ( '/[\\s]+/', '.', ( $this->u->Username ? $this->u->Username : i18n( 'i18n_username' ) ) ) ) ) . '</strong>@' . str_replace( array( 'http://', 'www.', '/' ), '', BASE_URL ) ?></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td><label><?= i18n( 'i18n_Username' ) ?>:</label></td>
		<td><input type="text" name="Username" value="<?= $this->u->Username ?>"/></td>
	</tr>
	<tr>
		<!--<td colspan="2">
			<span>Note:</span>
			<ul>
				<li>Your username can only be changed once.</li>
				<li>Contacts will be able to see your new email address on your profile.</li>
			</ul>
		</td>-->
	</tr>
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	<!--<tr>
		<td colspan="2"><span>To save these settings, please enter your password.</span></td>
	</tr>
	<tr>
		<td><label>Password:</label></td>
		<td><input type="password" id="Validation" onkeyup="validateAccount(this)"/></td>
	</tr>-->
	<tr>
		<td colspan="2">
			<button class="save_btn" id="Submit" onclick="saveAccount( 'account_username' )" name="account_username"><?= i18n( 'i18n_Save' ) ?></button>
			<button class="cancel_btn" onclick="closeAccount()"><?= i18n( 'i18n_Cancel' ) ?></button>
		</td>
		<!--<td colspan="2"><button id="Submit" class="disabled" name="account_username">Save Changes</button><button onclick="closeAccount()">Cancel</button></td>-->
	</tr>
</table>
