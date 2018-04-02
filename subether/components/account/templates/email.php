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
		<td><span><?= i18n( 'i18n_Primary email' ) ?>:</span></td>
		<td>
			<ul>
				<?
					if( $this->u->Email && $this->d->Emails )
					{
						$str = '';
						foreach( $this->d->Emails as $key=>$email )
						{
							$str .= '<li><input name="PrimaryEmail" type="radio" value="' . $email . '" ' . ( $key == '0' ? 'checked="checked"' : '' ) . '/> <span>' . $email . '</span> ' . ( $key != '0' ? '<a href="javascript:void(0);" onclick="removeEmail(\\'' . $email . '\\')">' . i18n( 'i18n_Remove' ) . '</a>' : '' ) . '</li>';
						}
						return $str;
					}
					
				?>
				<li><span><?= strtolower( $this->u->Username ? $this->u->Username : 'username' ) . '@' . str_replace( array( 'http://', 'www.', '/' ), '', BASE_URL ) ?></span></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	<tr>
		<td><label><?= i18n( 'i18n_New Email' ) ?>:</label></td>
		<td><input type="text" name="NewEmail" placeholder="<?= i18n( 'i18n_Optional' ) ?>"/></td>
	</tr>
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	<tr>
		<td><label>System <?= i18n( 'i18n_email' ) ?>:</label></td>
		<td><span><?= strtolower( $this->u->Username ? $this->u->Username : i18n( 'i18n_username' ) ) . '@' . str_replace( array( 'http://', 'www.', '/' ), '', BASE_URL ) ?></span></td>
	</tr>
	<!--<tr>
		<td colspan="2"><span>Your Sub-Ether email is based on your public username. Email sent to this address goes to Sub-Ether Messages.</span></td>
	</tr>-->
	<!--<tr>
		<td colspan="2"><hr></td>
	</tr>
	<tr>
		<td colspan="2"><input type="checkbox"/> <span>Allow contacts to include my email address in <a href="#">Download Your Information</a></span></td>
	</tr>-->
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
			<button class="save_btn" id="Submit" onclick="changeEmail()" name="account_email"><?= i18n( 'i18n_Save' ) ?></button>
			<button class="cancel_btn" onclick="closeAccount()"><?= i18n( 'i18n_Cancel' ) ?></button>
		</td>
		<!--<td colspan="2"><button id="Submit" class="disabled" name="account_email">Save Changes</button><button onclick="closeAccount()">Cancel</button></td>-->
	</tr>
</table>
