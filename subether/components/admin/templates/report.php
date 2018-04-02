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
<div id="report">
	<div class="head">
		<h1><?= i18n( 'i18n_Report a Problem' ) ?></h1>
	</div>
	<div class="content" id="ReportContent">
		<!--<form enctype="multipart/form-data" name="ReportForm" method="post">-->
			<input type="hidden" name="CategoryID" value="<?= $this->parent->folder->CategoryID ?>"/>
			<table>
				<tr>
					<td><strong><?= i18n( 'i18n_Type' ) ?>:</strong></td>
					<td>
						<select name="Type">
							<option value="Bug"><?= i18n( 'i18n_Bug' ) ?></option>
							<option value="Feature"><?= i18n( 'i18n_Feature' ) ?></option>
							<option value="Other"><?= i18n( 'i18n_Other' ) ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td><strong><?= i18n( 'i18n_Name' ) ?>:</strong></td>
					<td>
						<div class="inputfield">
							<input name="Name" type="text" value="<?= i18n( 'i18n_' . $this->parent->folder->Name ) ?>">
						</div>
					</td>
				</tr>
				<tr>
					<td><strong><?= i18n( 'i18n_Screenshot' ) ?>:</strong></td>
					<td>
						<div class="inputfield">
							<input name="Screenshot" type="file">
						</div>
					</td>
				</tr>
				<tr class="middle">
					<td><strong><?= i18n( 'i18n_Description' ) ?>:</strong></td>
					<td>
						<!--<div name="Description" contenteditable="true" class="textarea" id="CaseEditor" onkeyup="checkStr(this,event)"></div>-->
						<!--<div name="Description" contenteditable="true" class="textarea" id="CaseEditor"></div>-->
						<textarea name="Description" class="textarea" id="CaseEditor"></textarea>
					</td>
				</tr>
			</table>
		<!--</form>-->
	</div>
	<div class="bottom">
		<div class="buttons">
			<button class="submit" onclick="saveReport('ReportContent')"><span><?= i18n( 'i18n_Report' ) ?></span></button>
			<button class="cancel" onclick="closeWindow()"><span><?= i18n( 'i18n_Cancel' ) ?></span></button>
		</div>
	</div>
</div>
