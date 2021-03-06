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
<div id="payment">
	<div class="head">
		<h1>Payment</h1>
	</div>
	<div class="content">
		<?= $this->Content ?>
	</div>
	<div class="bottom">
		<div class="buttons">
			<button class="submit" onclick="payment( <?= $this->ParentID ?> )"><span>Save</span></button>
			<button class="cancel" onclick="closeWindow()"><span>Cancel</span></button>
		</div>
	</div>
</div>
