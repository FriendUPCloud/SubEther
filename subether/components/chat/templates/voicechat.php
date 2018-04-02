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
<div id="voicechat_incoming">
	<div class="head">
		<h1><?= i18n( 'i18n_Incoming call' ) ?></h1>
	</div>
	<div class="content">
		<?= $this->Content ?>
	</div>
	<div class="bottom">
		<div class="buttons">
			<a class="Button Accept" href="<?= $this->Data->url ?>" target="_blank" onclick="chatObject.acceptCall('<?= $this->Data->cid ?>','<?= ( $this->Data->url ? true : false ) ?>','<?= $this->Data->user ?>','<?= $this->Data->img ?>')"><span><?= i18n( 'i18n_Answer' ) ?></span></a>
			<button class="cancel" onclick="chatObject.declineCall('<?= $this->Data->cid ?>','<?= ( $this->Data->url ? true : false ) ?>','<?= $this->Data->user ?>','<?= $this->Data->img ?>')"><span><?= i18n( 'i18n_Decline' ) ?></span></button>
		</div>
	</div>
</div>
