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
		<div id="MainIM">
			<table>
				<tbody>
					<tr>
						<td class="leftCol">
							<div id="ListIM">
								<div id="ListIM_inner"><?= $this->ListIM ?></div>
							</div>
						</td>
						<td class="rightCol">
							<div id="RightIM" onmouseup="denyScroll(this)">
								<div id="RightIM_inner"><?= $this->RightIM ?></div>
							</div>
							<div class="post" id="Message_Post">
								<input rows="4" cols="30" placeholder="Write a reply" onkeyup="if( event.keyCode == 13 ) { saveMessage( this ) }">
								<!--<button type="button"><?= i18n ( 'i18n_sharebox_share' ) ?></button>-->
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script> refreshMessageList( <?= ( ( $this->parent->url[3] && is_numeric( $this->parent->url[3] ) ) ? $this->parent->url[3] : '' ) ?> ); </script>
