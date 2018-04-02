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
<div id="createaccount">
	<div class="head">
		<h1>Create New Account</h1>
	</div>
	<div class="content">
		<table>
			<tr>
				<td><strong>Account Name:</strong></td>
				<td>
					<div class="inputfield">
						<input type="text" name="name"/>
					</div>
				</td>
			</tr>
			<tr>
				<td><strong>Social Security Number:</strong></td>
				<td>
					<div class="inputfield">
						<input type="text" name="security"/>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="bottom">
		<div class="buttons">
			<button class="submit" onclick="createAccount( <?= $this->ParentID ?> )"><span>Create</span></button>
			<button class="cancel" onclick="closeWindow()"><span>Cancel</span></button>
		</div>
	</div>
</div>
