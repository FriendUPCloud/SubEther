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
		<ul class="EventDateTable">
			<li <?= ( $_REQUEST[ 'r' ] == 'year' ? 'class="current"' : '' ) ?>>
				<a href="<?= $this->parent->route ?>?r=year">
						<span>Year</span>
				</a>
			</li>
			<li <?= ( $_REQUEST[ 'r' ] == '' || $_REQUEST[ 'r' ] == 'month' ? 'class="current"' : '' ) ?>>
				<a href="<?= $this->parent->route ?>?r=month">
					<span>Month</span>
				</a>
			</li>
			<li <?= ( $_REQUEST[ 'r' ] == 'week' ? 'class="current"' : '' ) ?>>
				<a href="<?= $this->parent->route ?>?r=week">
					<span>Week</span>
				</a>
			</li>
			<li <?= ( $_REQUEST[ 'r' ] == 'day' ? 'class="current"' : '' ) ?>>
				<a href="<?= $this->parent->route ?>?r=day">
					<span>Day</span>
				</a>
			</li>
			<li id="EventDate" class="datenavigate">
				<?= $this->EventDate ?>
			</li>
		</ul>
	</div>
</div>
<div><?= $this->Content ?></div>
