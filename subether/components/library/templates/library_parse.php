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
<div id="libraryparse">
    <div class="head">
        <h1><?= i18n( 'i18n_Parse' ) ?></h1>
    </div>
	<div class="post">
			<!--
			<img id="AjaxLoader" width="16" height="11" src="subether/gfx/loader.gif">
			-->
			<!-- added by pampers -->
			<div id="AjaxLoader">
			  <div class="bounce1"></div>
			  <div class="bounce2"></div>
			  <div class="bounce3"></div>
			</div>
			<!-- end of new loader -->
		<div contenteditable="true" class="textarea" id="PopupPost" onpaste="setTimeout( parseUrl(this.innerHTML), 50 )" onkeyup="if( event.keyCode == 13 ){ parseUrl(this.innerHTML); }"></div>
	</div>
    <div class="content" id="ParseContent"></div>
    <div class="bottom">
        <div class="buttons">
            <button class="submit" onclick="saveParseData( '<?= $this->ParentID ?>' )"><span><?= i18n( 'i18n_Save' ) ?></span></button>
			<button class="cancel" onclick="closeWindow()"><span><?= i18n( 'i18n_Cancel' ) ?></span></button>
        </div>
    </div>
</div>
