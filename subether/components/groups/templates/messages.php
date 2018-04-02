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
<div id="Messages">
	<div class="Box">
		<div class="toolbar">
			<a href="javascript:void(0)" onclick="PopoutChat(<?= $this->parent->folder->CategoryID ?>)">
				<span>Pop out chat</span> 
				<img src="admin/gfx/icons/application_side_boxes.png" alt="Popout"/>
			</a>		
		</div>
		<div id="MainIM">
			<table>
				<tbody>
					<tr>
						<td class="leftCol">
							<div><div id="ListIM"></div></div>
						</td>
						<td class="rightCol">
							<div><div id="RightIM"></div></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="post">
			<input rows="4" cols="30" id="InstantMessage" placeholder="Write a reply" onkeyup="if( event.keyCode == 13 ) { chatBuffer.saveIM( '<?= $this->parent->folder->CategoryID ?>' ) }"></textarea>
			<!--<button type="button" onclick="saveIM( <?= $this->parent->folder->CategoryID ?> )"><?= i18n ( 'i18n_sharebox_share' ) ?></button>-->
		</div>
	</div>
</div>
<script> chatBuffer.refreshIM( '<?= $this->parent->folder->CategoryID ?>' ); </script>
	
