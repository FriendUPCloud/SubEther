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
<div id="Wall" class="posts">
	
	
	
	<!--<script src="subether/thirdparty/javascript/ckeditor/ckeditor.js"></script>-->
	
	<div id="WallTabs" class="Box">
		<?if ( $this->parent->webuser->ID > 0 ){ ?>
		<div class="tabs">
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
			<table>
				<tr>
					<td>
						<a class="tab current" href="javascript:void(0)">
							<span></span>
							<span><?= i18n( 'i18n_Write Post' ) ?></span>
						</a>
					</td>
					<!--<td>
						<a class="tab" href="javascript:void(0)">
							<span></span>
							<span>Write Article</span>
						</a>
					</td>-->
					<!--<td>
						<a class="tab" href="javascript:void(0)">
							<span></span>
							<span>Add Photos/Files</span>
						</a>
					</td>-->
					<td>
						<a class="tab" href="javascript:void(0)">
							<span></span>
							<span><?= i18n( 'i18n_Create Poll' ) ?></span>
						</a>
					</td>
				</tr>
			</table>
		</div>
		<div class="page active">
			<div class="editor post closed">
				<div class="topbar">
					<div class="targetbox">
						
						<?
							$str = '';
							
							$isgroup = ( ( strtolower( $this->parent->folder->MainName ) == 'groups' || strtolower( $this->parent->module ) == 'profile' || strtolower( $this->parent->folder->Name ) == 'events' ) ? true : false );
							
							if ( !$isgroup && ( $opts = GetUserGroupOptions() ) )
							{
								$str = '<select id="PostTarget">';
								
								$str .= $opts;
								
								$str .= '</select>';
							}
							
							//$opt = array();
							//$opt['0'] = 'Post to your own profile';
							//$opt['1'] = 'Post to a contacts profile';
							//$opt['2'] = 'Post to a group';
							//$opt['3'] = 'Post to an event';
							//$opt['4'] = 'Post to a private message';
							
							//foreach( $opt as $k=>$v )
							//{
							//	$str .= '<option value="' . $k . '"' . ( $k == $select ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_' . $v ) . '</option>';
							//}
							
							return $str;
							//return renderCustomSelect( $arr, false, 'PostAccess' );
							
						?>
						
					</div>
				</div>
				<div class="text" style="position:relative;">
				
					<div id="ShareBox" class="textarea post" onclick="InitWallEditor(this)" onpaste="handlePaste('postMediaUpload','wall',event);setTimeout( function() { parseText( ge( 'ShareBox' ) ); }, 50 );" onkeyup="if( event.keyCode == 13 ){ parseText( ge( 'ShareBox' ) ); }" contenteditable="true" placeholder="<?= i18n( 'i18n_Write post here' ) ?>"></div>
				
				</div>
				<div class="toolbar">
					<div class="publish">
						
						<?
							$str = '<select id="PostAccess">';
							
							$isgroup = ( strtolower( $this->parent->folder->MainName ) == 'groups' ? true : false );
							$select = ( !$isgroup ? ( defined( 'WALL_DEFAULT_ACCESS' ) && WALL_DEFAULT_ACCESS ? WALL_DEFAULT_ACCESS : 0 ) : 0 );
							
							$opt = array();
							$opt['0'] = ( $isgroup ? 'Members' : 'Public' );
							$opt['1'] = 'Contacts';
							$opt['2'] = 'Only Me';
							//$opt['3'] = 'Custom';
							if( isset( $this->parent->access->IsAdmin ) )
							{
								$opt['4'] = 'Admin';
							}
							
							foreach( $opt as $k=>$v )
							{
								$str .= '<option value="' . $k . '"' . ( $k == $select ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_' . $v ) . '</option>';
							}
							
							$str .= '</select>';
							
							return $str;
							//return renderCustomSelect( $arr, false, 'PostAccess' );
							
						?>
						
						<button type="button" onclick="addShareContent(ge('ShareBox'),event,'<?= ( $_REQUEST['event'] > 0 ? 'event' : 'post' ) ?>','<?= ( $_REQUEST['event'] > 0 ? $_REQUEST['event'] : '' ) ?>')">
							<?= i18n ( 'i18n_Post' ) ?>
						</button>
					</div>
					<div class="postmedia">
						<div class="edit_btn">
							<div><span><?= i18n( 'i18n_Upload Photo/Video' ) ?></span></div>
							<form method="post" target="fileIframe" name="postMediaUpload" enctype="multipart/form-data" action="<?= $this->parent->route ?>?global=true&action=uploadfile">
								<input type="file" class="upload_btn" id="postMediaUploadBtn" name="wall" onchange="fileselect( this, 'postMediaUpload' ); document.postMediaUpload.reset()" multiple/>
							</form>
							<script>setOpacity ( ge('postMediaUploadBtn' ), 0 );</script>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--<div class="page">
			<div class="editor article closed">
				<div class="topbar">
					<div class="targetbox">
						
						<?
							$str = '';
							
							$isgroup = ( ( strtolower( $this->parent->folder->MainName ) == 'groups' || strtolower( $this->parent->module ) == 'profile' ) ? true : false );
							
							if ( !$isgroup && ( $opts = GetUserGroupOptions() ) )
							{
								$str = '<select id="ArticleTarget">';
								
								$str .= $opts;
								
								$str .= '</select>';
							}
							
							//$opt = array();
							//$opt['0'] = 'Post to your own profile';
							//$opt['1'] = 'Post to a contacts profile';
							//$opt['2'] = 'Post to a group';
							//$opt['3'] = 'Post to an event';
							//$opt['4'] = 'Post to a private message';
							
							//foreach( $opt as $k=>$v )
							//{
							//	$str .= '<option value="' . $k . '"' . ( $k == $select ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_' . $v ) . '</option>';
							//}
							
							return $str;
							//return renderCustomSelect( $arr, false, 'PostAccess' );
							
						?>
						
					</div>
				</div>
				<div class="text">
					<div id="ArticleHeading" class="input heading" onclick="InitWallEditor(this)" contenteditable="true"></div>-->
					<!--<div id="ArticleLeadin" class="textarea leadin extrafield" onblur="onDivBlur()" onmousedown="return cancelEvent(event);" onclick="return cancelEvent(event);" onmouseup="saveSelection()" onkeyup="saveSelection()" onfocus="restoreSelection( 'ArticleLeadin' )" contentEditable="true"></div>
					<div id="ArticleContent" class="textarea article extrafield" onblur="onDivBlur()" onmousedown="return cancelEvent(event);" onclick="return cancelEvent(event);" onmouseup="saveSelection()" onkeyup="saveSelection()" onfocus="restoreSelection( 'ArticleContent' )" contentEditable="true"></div>-->
					<!--<div id="ArticleLeadin" class="textarea leadin extrafield" contentEditable="true" onkeyup="saveSelection();highlightCode(this,event);" onmouseup="saveSelection()"></div>
					<div id="ArticleContent" class="textarea article extrafield" contentEditable="true" onkeyup="saveSelection();highlightCode(this,event);" onmouseup="saveSelection()"></div>-->
					<!--<textarea id="ArticleLeadin" class="mceSelector textarea leadin extrafield"></textarea>
					<textarea id="ArticleContent" class="mceSelector textarea article extrafield"></textarea>
					<script>
						/*function InitWallEditors ()
						{
							if ( typeof ( texteditor ) == 'undefined' )
								return setTimeout ( 'InitWallEditors()', 10 );
							texteditor.mode = 'admin';
							texteditor.init ( {classNames : "mceSelector"} );
						}
						InitWallEditors ();*/
					</script>
				</div>
				<div class="toolbar">
					<div class="publish">
						<select name="Access">
							<option value="0">Public</option>
							<option value="1">Contacts</option>
							<option value="2">Only Me</option>-->
							<!--<option value="3">Custom</option>-->
						<!--</select>
						<button type="button" onclick="AddArticle()">
							<?= i18n ( 'i18n_sharebox_share' ) ?>
						</button>
					</div>-->
					<!--<div class="icons">
						<span onclick="InsertCodeTag()">{ }</span>
					</div>
					<div class="icons">
						<span onclick="SwitchViewMode()"><?= htmlentities( '</>' ) ?></span>
					</div>
					<div class="icons">
						<span onclick="InsertBold()"><strong>B</strong></span>
					</div>
					<div class="icons">
						<span onclick="InsertItalic()"><i>i</i></span>
					</div>-->
				<!--</div>
			</div>
		</div>-->
		<!--<div class="page">
			<div class="fileupload">
				<div class="edit_btn">
					<div><span>Upload Photo/Video</span></div>
					<form method="post" target="fileIframe" name="mediaUpload" enctype="multipart/form-data" action="<?= $this->path ?>?global=true&action=uploadfile">
						<input type="file" class="upload_btn" id="mediaUploadBtn" name="wall" onchange="document.mediaUpload.submit();">
					</form>
					<script>setOpacity ( ge('mediaUploadBtn' ), 0 );</script>
				</div>
			</div>
		</div>-->
		<div class="page">
			<div class="editor vote closed">
				<div class="topbar">
					<div class="targetbox">
						
						<?
							$str = '';
							
							$isgroup = ( ( strtolower( $this->parent->folder->MainName ) == 'groups' || strtolower( $this->parent->module ) == 'profile' ) ? true : false );
							
							if ( !$isgroup && ( $opts = GetUserGroupOptions() ) )
							{
								$str = '<select id="VoteTarget">';
								
								$str .= $opts;
								
								$str .= '</select>';
							}
							
							//$opt = array();
							//$opt['0'] = 'Post to your own profile';
							//$opt['1'] = 'Post to a contacts profile';
							//$opt['2'] = 'Post to a group';
							//$opt['3'] = 'Post to an event';
							//$opt['4'] = 'Post to a private message';
							
							//foreach( $opt as $k=>$v )
							//{
							//	$str .= '<option value="' . $k . '"' . ( $k == $select ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_' . $v ) . '</option>';
							//}
							
							return $str;
							//return renderCustomSelect( $arr, false, 'PostAccess' );
							
						?>
						
					</div>
				</div>
				<div class="text" style="position:relative;">
					<div id="VoteContent" class="textarea post" onclick="InitWallEditor(this)" contenteditable="true" placeholder="<?= i18n( 'i18n_Write question here' ) ?>"></div>
				</div>
				<div class="poll">
					<div id="VoteOptions" class="voteoptions"></div>
				</div>
				<div class="toolbar">
					<div class="publish">
						
						<!--<select id="VoteAccess">
							<option value="0"><?= i18n( 'i18n_Public' ) ?></option>
							<option value="1" selected="selected"><?= i18n( 'i18n_Contacts' ) ?></option>
							<option value="2"><?= i18n( 'i18n_Only Me' ) ?></option>-->
							<!--<option value="3"><?= i18n( 'i18n_Custom' ) ?></option>-->
							<!--<?= ( isset( $this->parent->access->IsAdmin ) ? '<option value="4">' . i18n( 'i18n_Admin' ) . '</option>' : '' ) ?>
						</select>-->
						
						<?
							$str = '<select id="VoteAccess">';
							
							$isgroup = ( strtolower( $this->parent->folder->MainName ) == 'groups' ? true : false );
							$select = ( !$isgroup ? 1 : 0 );
							
							$opt = array();
							$opt['0'] = ( $isgroup ? 'Members' : 'Public' );
							$opt['1'] = 'Contacts';
							$opt['2'] = 'Only Me';
							//$opt['3'] = 'Custom';
							if( isset( $this->parent->access->IsAdmin ) )
							{
								$opt['4'] = 'Admin';
							}
							
							foreach( $opt as $k=>$v )
							{
								$str .= '<option value="' . $k . '"' . ( $k == $select ? ' selected="selected"' : '' ) . '>' . i18n( 'i18n_' . $v ) . '</option>';
							}
							
							$str .= '</select>';
							
							return $str;
							//return renderCustomSelect( $arr, false, 'PostAccess' );
							
						?>
						
						<button type="button" onclick="AddQuestion(<?= ( $_REQUEST['event'] > 0 ? $_REQUEST['event'] : '' ) ?>)">
							<?= i18n ( 'i18n_Post' ) ?>
						</button>
					</div>					
					<div class="postmedia">
						<div class="addoptions" onclick="AddPollOptions()">
							<a href="javascript:void(0);"><?= i18n( 'i18n_Add Poll Options' ) ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?}?>
	</div>
	<script> InitWallTabs( 'WallTabs' ); </script>
	<div id="ShareContent"><?= $this->Content ?></div>
	
</div>
