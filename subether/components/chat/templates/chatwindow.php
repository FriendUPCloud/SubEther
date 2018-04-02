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
<div id="chatwindow" <?= ( $this->Mode == 'fullscreen' ? 'class="fullscreen"' : 'class="default"' ) ?>>
    <div class="head">
		<div class="buttons">
			<?if ( $this->Mode == 'fullscreen' ){ ?>
			<div class="close" title="Exit Fullscreen" onclick="openWindow( 'Chat', '<?= $this->obj->u->ID ?>', 'chatwindow', function(){ openPrivChat( '<?= $this->obj->u->ID ?>', '<?= $this->obj->u->Name ?>', 'window' ); } );"><span>X</span></div>
			<?}?>
			<?if ( $this->Mode == 'default' ){ ?>
			<span class="minimize" onclick="closePrivChat( '<?= $this->obj->u->ID ?>' );addPrivChat( '<?= $this->obj->u->ID ?>', '<?= $this->obj->u->Name ?>', 1, 'window' );closeWindow();">_</span>
			<span class="fullsize" onclick="openFullscreen( 'Chat', '<?= $this->obj->u->ID ?>', 'chatwindow', function(){ openPrivChat( '<?= $this->obj->u->ID ?>', '<?= $this->obj->u->Name ?>', 'window' ); } );">[ ]</span>
			<span class="closewindow" onclick="closeWindow();hangup();">x</span>
			<?}?>
		</div>
        <span class="name"><?= $this->obj->u->Name ?></span>
    </div>
    <div id="ChatContent" class="content">
		<div id="<?= ( 'Chat_' . $this->obj->u->ID ) ?>" class="chattab">
			<div class="chatpriv">
				<div id="<?= ( 'Chat_inner_' . $this->obj->u->ID ) ?>" class="messages" onmouseup="checkScroll(this,'<?= $this->obj->u->ID ?>')">
					<?= $this->obj->Content ?>
				</div>
			</div>
		</div>
    </div>
    <div class="editor post">
		<div class="text">
			<!--<div class="textarea post" id="<?= ( 'ChatWindow_' . $this->obj->u->ID ) ?>" contenteditable="true" placeholder="Send a message" onkeyup="if( event.keyCode == 13 ){ savePrivChat( '<?= $this->obj->u->ID ?>', '<?= $this->obj->u->Name ?>', this, 'window' ); }"></div>-->
			<div class="textarea post" id="<?= ( 'ChatWindow_' . $this->obj->u->ID ) ?>" contenteditable="true" placeholder="Send a message"></div>
		</div>
		<div class="toolbar">
			<div class="publish">
				<button onclick="savePrivChat( '<?= $this->obj->u->ID ?>', '<?= $this->obj->u->Name ?>', ge( '<?= ( 'ChatWindow_' . $this->obj->u->ID ) ?>' ), 'window' )" type="button">SEND</button>
			</div>
			<div class="postmedia"></div>
		</div>
	</div>
</div>
