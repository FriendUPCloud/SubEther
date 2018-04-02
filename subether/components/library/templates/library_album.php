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
<div id="libraryalbum" class="<?= $this->Mode ?>">
	<div class="top">
		<div class="buttons">
			<?if ( $this->Mode == 'fullscreen' ){ ?>
			<!--<div class="close" title="Exit Fullscreen" onclick="openWindow( 'Library', '<?= $this->ParentID ?>', 'album', function(){ Showroom.init( 'Album_Showroom' ); } )"><span>X</span></div>-->
			<div class="close" title="Close" onclick="closeWindow()"><span>X</span></div>
			<?}?>
			<?if ( $this->Mode == 'default' ){ ?>
			<div class="fullscreen" title="Fullscreen" onclick="openFullscreen( 'Library', '<?= $this->ParentID ?>', 'album', function(){ Showroom.init( 'Album_Showroom' ); } )"><span>[ ]</span></div>
			<div class="close" title="Close" onclick="closeWindow()"><span>X</span></div>
			<?}?>
		</div>
	</div>
	<div class="content <?= $this->obj->Images ? ( ' imgs' . $this->obj->Images ) : '' ?>">
		<?= $this->Content ?>
	</div>
	<div class="bottom">
	<div id="OptionsBox">
		<div class="inner"></div>
		<div class="bottomarrow"></div>
	</div>
		<div class="status">
			<div class="name"><strong><?= i18n( 'i18n_' . $this->obj->Name ) ?></strong></div>
		</div>
		<div class="buttons">
			<div onclick="moreOptions( this, 'library', 'options', getCurrentImage(), getCurrentImage( 'uniqueid' ) )"><span><?= i18n( 'i18n_Options' ) ?></span></div>
		</div>
	</div>
</div>
