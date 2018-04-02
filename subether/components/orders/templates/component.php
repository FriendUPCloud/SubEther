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
<div id="Orders">
	<div class="Box">
		<!--<div class="tabs">-->
			<!--<label for="show-browse-menu" class="show-browse-menu">Show Menu</label> <input type="checkbox" id="show-browse-menu" role="button">-->
			<!--<ul id="OrdersMenu">
				<li class="pending">
					<a <?= ( $_REQUEST[ 's' ] == '0' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?s=0">
						<span class="icon"></span>
						<span class="name">Pending</span>
					</a>
				</li>
				<li class="active">
					<a <?= ( $_REQUEST[ 's' ] == '' || $_REQUEST[ 's' ] == '1' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?s=1">
						<span class="icon"></span>
						<span class="name">Active</span>
					</a>
				</li>
				<li class="onhold">
					<a <?= ( $_REQUEST[ 's' ] == '2' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?s=2">
						<span class="icon"></span>
						<span class="name">OnHold</span>
					</a>
				</li>
				<li class="canceled">
					<a <?= ( $_REQUEST[ 's' ] == '3' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?s=3">
						<span class="icon"></span>
						<span class="name">Canceled</span>
					</a>
				</li>
				<li class="finished">
					<a <?= ( $_REQUEST[ 's' ] == '4' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?s=4">
						<span class="icon"></span>
						<span class="name">Finished</span>
					</a>
				</li>
				<li class="archived">
					<a <?= ( $_REQUEST[ 's' ] == '5' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?s=5">
						<span class="icon"></span>
						<span class="name">Archived</span>
					</a>
				</li>
			</ul>
			<div style="clear:both" class="clearboth"></div>
		</div>-->
		<div id="OrderDate" class="datenavigate"><?= $this->Navigation ?></div>
		<div id="OrderContent"><?= $this->Content ?></div>
	</div>
</div>
