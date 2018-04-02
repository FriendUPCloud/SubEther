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
<div id="profilecover">
	<div class="top">
		<div class="buttons">
			<?if ( $this->Mode == 'fullscreen' ){ ?>
			<div class="close" title="Exit Fullscreen" onclick="openWindow( 'Profile', '<?= $this->parent->cuser->ID ?>', 'cover', function(){ Showroom.init( 'Cover_Showroom' ); } )"><span>X</span></div>
			<?}?>
			<?if ( $this->Mode == 'default' ){ ?>
			<div class="fullscreen" title="Fullscreen" onclick="openFullscreen( 'Profile', '<?= $this->ParentID ?>', 'cover', function(){ Showroom.init( 'Cover_Showroom' ); } )"><span>[ ]</span></div>
			<div class="close" title="Close" onclick="closeWindow()"><span>X</span></div>
			<?}?>
		</div>
	</div>
	<div class="content">
		<?= $this->Content ?>
	</div>
	<div class="bottom">
	<div id="OptionsBox">
		<div class="inner"></div>
		<div class="bottomarrow"></div>
	</div>
		<div class="status">
			<div class="name"><strong><?= $this->obj->Name ?></strong></div>
		</div>
		<?if ( $this->parent->webuser->ID > 0 && $this->parent->cuser->ID == $this->parent->webuser->ID ){ ?>
		<div class="buttons">
			<div onclick="moreOptions( this, 'Profile', 'options' )"><span>Options</span></div>
		</div>
		<?}?>
	</div>
</div>
