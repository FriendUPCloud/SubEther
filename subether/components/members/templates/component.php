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
<div id="Members">
	<div class="Box">
		<div class="members">
			<table class="panel">
				<tr>
					<td style="text-align:right;">
						<?if ( $this->parent->folder->ObjectID || isset( $this->parent->access->IsSystemAdmin ) ){ ?>
						<button onclick="openWindow( 'Members', '<?= $this->parent->folder->CategoryID ?>', 'invite' )">+ Invite Members</button>
						<?}?>
					</td>
				</tr>
			</table>
			<div id="MembersContent"><?= $this->Content ?></div>
		</div>
	</div>
</div>
