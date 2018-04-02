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
<div id="createmailaccount">
	<div class="head">
		<h1><?= $this->ParentID != 'false' ? 'Edit Mail Account' : 'Create New Mail Account' ?></h1>
	</div>
	<div class="content">
		<table>
			<tr>
				<td><strong>Account Name:</strong></td>
				<td>
					<div class="inputfield">
						<input type="text" name="Address" value="<?= $this->obj->Address ?>" <?= $this->obj->Address ? 'readonly="1"' : '' ?>/>
					</div>
				</td>
			</tr>
			<tr>
				<td><strong>(IMAP) Server:</strong></td>
				<td>
					<div class="inputfield">
						<input type="text" name="Server" value="<?= $this->obj->Server ?>"/>
					</div>
				</td>
			</tr>
			<tr>
				<td><strong>Port:</strong></td>
				<td>
					<div class="inputfield">
						<input type="text" name="Port" value="<?= $this->obj->Port ?>"/>
					</div>
				</td>
			</tr>
			<tr>
				<td><strong>Username:</strong></td>
				<td>
					<div class="inputfield">
						<input type="text" name="Username" value="<?= $this->obj->Username ?>"/>
					</div>
				</td>
			</tr>
			<tr>
				<td><strong>Password:</strong></td>
				<td>
					<div class="inputfield">
						<input type="text" name="Password" value="<?= $this->obj->Password ?>"/>
					</div>
				</td>
			</tr>
			<tr>
				<td><strong>(Security) SSL:</strong></td>
				<td>
					<div>
						<input style="width:15px;" type="checkbox" name="SSL" <?= $this->obj->SSL ? 'checked="checked"' : '' ?>/>
					</div>
				</td>
			</tr>
			<tr>
				<td><strong>(SMTP) OutServer:</strong></td>
				<td>
					<div class="inputfield">
						<input type="text" name="OutServer" value="<?= $this->obj->OutServer ?>"/>
					</div>
				</td>
			</tr>
			<tr>
				<td><strong>OutPort:</strong></td>
				<td>
					<div class="inputfield">
						<input type="text" name="OutPort" value="<?= $this->obj->OutPort ?>"/>
					</div>
				</td>
			</tr>
			<tr>
				<td><strong>OutUser:</strong></td>
				<td>
					<div class="inputfield">
						<input type="text" name="OutUser" value="<?= $this->obj->OutUser ?>"/>
					</div>
				</td>
			</tr>
			<tr>
				<td><strong>OutPass:</strong></td>
				<td>
					<div class="inputfield">
						<input type="text" name="OutPass" value="<?= $this->obj->OutPass ?>"/>
					</div>
				</td>
			</tr>
			<tr>
				<td><strong>Signature:</strong></td>
				<td>
					<div>
						<textarea style="width:97%;" class="textarea" name="Signature"><?= $this->obj->Signature ?></textarea>
					</div>
				</td>
			</tr>
		</table>
		<input type="hidden" name="accid" value="<?= $this->ParentID != 'false' ? $this->ParentID : '' ?>"/>
	</div>
	<div class="bottom">
		<div class="buttons">
			<button class="submit" onclick="createMailAccount()"><span><?= $this->ParentID != 'false' ? 'Save' : 'Create' ?></span></button>
			<button class="cancel" onclick="closeWindow()"><span>Cancel</span></button>
		</div>
	</div>
</div>
