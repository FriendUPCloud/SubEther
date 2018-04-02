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
<div id="Files" class="mobileview">
	<div class="Box">
		<div id="Main">
			<table>
				<tbody>
					<?if ( $this->Buttons ) { ?>
					<tr>
						<td colspan="2" class="topRow">
							<!--<div id="Controls">-->
								<?= $this->Buttons ?>
							<!--</div>-->
						</td>
					</tr>
					<?}?>
					<tr>
						<td class="leftCol">
							<!--<div id="Directory">-->
								<?= $this->Directory ?>
							<!--</div>-->
							<?if ( ( $this->parent->webuser->ID == $this->parent->cuser->ID ) || IsSystemAdmin() ) { ?>
							<div class="upload_btn">
								<!--<div><span>Upload Files</span></div>-->
								<form method="post" target="fileIframe" name="FilesUpload" enctype="multipart/form-data" action="<?= $this->parent->route . '?component=library&action=uploadfile' ?>">
								<!--<form method="post" name="FilesUpload" enctype="multipart/form-data" action="<?= $this->parent->route . '?component=library&action=uploadfile' ?>">-->
									<!--<input type="file" class="file_upload_btn" id="FilesUploadBtn" name="library" onchange="document.FilesUpload.submit();">-->
									<label class="file_upload_label">
									<span><input type="file" class="file_upload_btn" id="FilesUploadBtn" name="library" onchange="fileselect( this, 'FilesUpload' )" multiple/></span>
									</label>
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
