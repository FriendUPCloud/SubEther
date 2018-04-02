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
		<td><label><?= i18n( 'i18n_First' ) ?>:</label></td>
		<td><input type="text" id="Firstname" name="Firstname" value="<?= $this->u->Firstname ?>"/></td>
	</tr>
	<tr>
		<td><label><?= i18n( 'i18n_Middle' ) ?>:</label></td>
		<td><input type="text" id="Middlename" name="Middlename" value="<?= $this->u->Middlename ?>"/></td>
	</tr>
	<tr>
		<td><label><?= i18n( 'i18n_Last' ) ?>:</label></td>
		<td><input type="text" id="Lastname" name="Lastname" value="<?= $this->u->Lastname ?>"/></td>
	</tr>
	<tr>
		<td><label><?= i18n( 'i18n_Display as' ) ?>:</label></td>
		<td><select id="Display" name="Display">
			<?
				$str = '';
				$first = $this->u->Firstname ? ( $this->u->Firstname . ' ' ) : '';
				$middle = $this->u->Middlename ? ( $this->u->Middlename . ' ' ) : '';
				$last = $this->u->Lastname ? ( $this->u->Lastname . ' ' ) : '';
				$str .= '<option value="0" ' . ( $this->u->Display == '0' ? 'selected="selected"' : '' ) . '>' . $this->u->Username . '</option>';
				$str .= '<option value="1" ' . ( $this->u->Display == '1' ? 'selected="selected"' : '' ) . '>' . trim( $first . $middle . $last ) . '</option>';
				$str .= '<option value="2" ' . ( $this->u->Display == '2' ? 'selected="selected"' : '' ) . '>' . trim( $first . $last ) . '</option>';
				$str .= '<option value="3" ' . ( $this->u->Display == '3' ? 'selected="selected"' : '' ) . '>' . trim( $last . $first ) . '</option>';
				return $str;
			?>
		</select></td>
	</tr>
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	<tr>
		<td><label><?= i18n( 'i18n_Alternate name' ) ?>:</label></td>
		<td><input type="text" name="Alternate" value="<?= $this->u->Alternate ?>"/></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="checkbox" name="ShowAlternate" <?= $this->u->ShowAlternate ? 'Checked="1"' : '' ?>/> <span><?= i18n( 'i18n_Include this on my profile' ) ?></span></td>
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
			<button class="save_btn" id="Submit" onclick="saveAccount( 'account_name' )" name="account_name"><?= i18n( 'i18n_Save' ) ?></button>
			<button class="cancel_btn" onclick="closeAccount()"><?= i18n( 'i18n_Cancel' ) ?></button>
		</td>
		<!--<td colspan="2"><button id="Submit" class="disabled" name="account_name">Save Changes</button><button onclick="closeAccount()">Cancel</button></td>-->
	</tr>
</table>
