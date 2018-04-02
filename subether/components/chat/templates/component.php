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
<div class="chatwrapper clearfix">
	<div id="ChatTabs"></div>
	<div id="ChatBox">
		<div id="Chat">
			<div id="Chat_inner">
				<div id="Chat_head" class="head" onclick="openChat()">Chat (<?= ( $this->Online ? $this->Online : '0' ) ?>)</div>
				<div id="Chat_list"></div>
			</div>
			<div class="search">
				<div class="buttons">
					<div id="ChatSettings">
						<div class="inner"></div>
						<div class="bottomarrow"></div>
					</div>
					<i class="edit" onclick="chatSettings( this, 'Chat', 'settings' )"></i>
				</div>
				<input placeholder="Search" onkeyup="filterContactList(this.value)">
			</div>
		</div>
	</div>
</div>
