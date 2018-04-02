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
<div id="Files">
	<div class="Box">
		<div id="Main">
			<table>
				<tbody>
					<tr>
						<td colspan="2" class="topRow">
							<!--<div id="Controls">-->
								<?= $this->Buttons ?>
							<!--</div>-->
						</td>
					</tr>
					<tr>
						<td class="leftCol">
							<!--<div id="Directory">-->
								<?= $this->Directory ?>
							<!--</div>-->
						</td>
						<td class="rightCol">
							<!--<div id="Content" onclick="focusEditor()" ondragover="this.style.outline = '1px solid black'; handleDragOver(event); return false" ondragleave="this.style.outline = ''; handleDragLeave(event); return false" ondrop="handleDrop(window.dragId,window.dragType,ge('FolderID').value,false,false,event); this.style.outline = ''; return false;">
								<div id="ContentInner">-->
									<?= $this->ContentInner ?>
								<!--</div>
							</div>-->
							<?if ( ( $this->parent->webuser->ID == $this->parent->cuser->ID ) || isset( $this->parent->access->IsAdmin ) ) { ?>
							<div class="upload_btn">
								<div><span>Upload Files</span></div>
								<form method="post" target="fileIframe" name="FilesUpload" enctype="multipart/form-data" action="<?= $this->parent->route . '?component=library&action=uploadfile' ?>">
								<!--<form method="post" name="FilesUpload" enctype="multipart/form-data" action="<?= $this->parent->route . '?component=library&action=uploadfile' ?>">-->
									<!--<input type="file" class="file_upload_btn" id="FilesUploadBtn" name="library" onchange="document.FilesUpload.submit();">-->
									
									<label class="mobileuploadfile"> <span> <input type="file" multiple="" onchange="fileselect( this, 'FilesUpload' )" name="library" id="FilesUploadBtn" class="file_upload_btn" style="opacity: 0;"> </span> </label>
									
									<!--<input type="file" class="file_upload_btn" id="FilesUploadBtn" name="library" onchange="fileselect( this, 'FilesUpload' )" multiple/>-->
									<input type="hidden" id="FolderID" name="folderid" value="<?= $this->FolderID ?>">
								</form>
								<script>setOpacity ( ge('FilesUploadBtn' ), 0 );</script>
							</div>
							<?}?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
