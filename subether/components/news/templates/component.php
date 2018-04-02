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
<div id="News">
	<div class="Box">
		<div id="NewsEditor"><?= $this->Editor ?>
			<!--<?
				/*$str = '';
				$img = new dbImage ();
				if( $this->n->MediaID > 0 && $img->Load( $this->n->MediaID ) )
				{
					$str .= '<div class="image">' . $img->getImageHTML ( 780, 300, 'framed', false, 0xffffff );
					$str .= '<div class="edit_icons">';
					$str .= '<span onclick="deleteImage(' . $this->n->MediaID . ')" title="Delete Image"><img class="Icon" src="admin/gfx/icons/page_delete.png"></span>';
					$str .= '</div>';
					$str .= '</div>';
				}
				return $str;*/
			?>
			<div class="header"><strong>Title</strong></div>
			<input type="text" id="Title" onclick="openNewsEditor()">
			<div class="header"><strong>Leadin</strong></div>
			<textarea id="Leadin"></textarea>
			<div class="header"><strong>Article</strong></div>
			<textarea id="Article"></textarea>
			<div class="publish">
				<button type="button" onclick="publishNews()">PUBLISH</button>
				<!--<select id="Type"><option value="Big">Big</option><option value="Medium">Medium</option><option value="Small">Small</option></select>-->
				<!--<select id="Status"><option value="IsPublished">Published</option><option value="NotPublished">UnPublished</option></select>
				<div class="upload_btn">
					<!--<div><span>Media</span></div>-->
					<!--<form method="post" target="fileIframe" name="NewsUpload" enctype="multipart/form-data" action="<?= $this->url . '?action=uploadfile' ?>">
						<input type="file" class="news_upload_btn" id="NewsUploadBtn" name="news" onchange="document.NewsUpload.submit();publishNews('Current')">
					</form>
					<script>setOpacity ( ge('NewsUploadBtn' ), 0 );</script>
				</div>
			</div>-->
		</div>
		<div id="NewsContent"><?= $this->News ?></div>
	</div>
</div>
