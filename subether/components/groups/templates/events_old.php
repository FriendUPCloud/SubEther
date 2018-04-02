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
<div class="Box">
	<div class="tabs">
		<table>
			<tr>
				<td>
					<a <?= ( $_REQUEST[ 'r' ] == '' || $_REQUEST[ 'r' ] == 'month' ? 'class="current"' : '' ) ?> href="<?= $this->parent->nav ?>events/?r=month">
						<span></span>
						<span>Month</span>
					</a>
				</td>
				<td>
					<a <?= ( $_REQUEST[ 'r' ] == 'week' ? 'class="current"' : '' ) ?> href="<?= $this->parent->nav ?>events/?r=week">
						<span></span>
						<span>Week</span>
					</a>
				</td>
				<td>
					<a <?= ( $_REQUEST[ 'r' ] == 'day' ? 'class="current"' : '' ) ?> href="<?= $this->parent->nav ?>events/?r=day">
						<span></span>
						<span>Day</span>
					</a>
				</td>
			</tr>
		</table>
	</div>
</div>
<div id="CalendarContent"><?= $this->parent->switch ?></div>
	
