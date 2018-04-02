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
<div id="Browse">
	<div class="Box">
		
		<div class="tabs">
			<label for="show-browse-menu" class="show-browse-menu">Show Menu</label> <input type="checkbox" id="show-browse-menu" role="button">
			<ul id="BrowseMenu">
				<li class="network">
					<a <?= ( $_REQUEST[ 'r' ] == '' || $_REQUEST[ 'r' ] == 'network' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=network">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Network' ) ?></span>
					</a>
				</li>
				<li class="videos">
					<a <?= ( $_REQUEST[ 'r' ] == 'videos' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=videos">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Videos' ) ?></span>
					</a>
				</li>
				<!--<li class="streaming">
					<a <?= ( $_REQUEST[ 'r' ] == 'streaming' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=streaming">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Streaming' ) ?></span>
					</a>
				</li>-->
				<li class="files">
					<a <?= ( $_REQUEST[ 'r' ] == 'files' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=files">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Files' ) ?></span>
					</a>
				</li>
				<li class="images">
					<a <?= ( $_REQUEST[ 'r' ] == 'images' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=images">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Images' ) ?></span>
					</a>
				</li>
				<!--<li class="torrents">
					<a <?= ( $_REQUEST[ 'r' ] == 'torrents' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=torrents">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Torrents' ) ?></span>
					</a>
				</li>-->
				<li class="contacts">
					<a <?= ( $_REQUEST[ 'r' ] == 'contacts' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=contacts">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Contacts' ) ?></span>
					</a>
				</li>
				<li class="groups">
					<a <?= ( $_REQUEST[ 'r' ] == 'groups' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=groups">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Groups' ) ?></span>
					</a>
				</li>
				<!--<li class="articles">
					<a <?= ( $_REQUEST[ 'r' ] == 'articles' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=articles">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Articles' ) ?></span>
					</a>
				</li>-->
				<!--<li class="pages">
					<a <?= ( $_REQUEST[ 'r' ] == 'pages' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=pages">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Pages' ) ?></span>
					</a>
				</li>-->
				<!--<li class="events">
					<a <?= ( $_REQUEST[ 'r' ] == 'events' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=events">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Events' ) ?></span>
					</a>
				</li>-->
				<!--<li class="maps">
					<a <?= ( $_REQUEST[ 'r' ] == 'maps' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=maps">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Maps' ) ?></span>
					</a>
				</li>-->
				<!--<li class="servers">
					<a <?= ( $_REQUEST[ 'r' ] == 'servers' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=servers">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'Servers' ) ?></span>
					</a>
				</li>-->
				<!--<li class="faq">
					<a <?= ( $_REQUEST[ 'r' ] == 'faq' ? 'class="current"' : '' ) ?> href="<?= $this->parent->route ?>?q=<?= $_REQUEST[ 'q' ] ?>&r=faq">
						<span class="icon"></span>
						<span class="name"><?= i18n( 'FAQ' ) ?></span>
					</a>
				</li>-->
			</ul>
			<div style="clear:both" class="clearboth"></div>
		</div>
		
		<div id="Content"><?= $this->Content ?></div>
	</div>
</div>
