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
<div id="Wall">	
	
	<input type="hidden" id="uid" value="<?= $this->parent->webuser->ID ?>"/>
	<?if ( $this->parent->webuser->ID > 0 ) { ?>
	<div class="Box">
		<div class="tabs">
			<table>
				<tr>
					<td>
						<a class="current" href="#">
							<span></span>
							<span>Update Status</span>
						</a>
					</td>
					<td>
						<a href="#">
							<span></span>
							<span>Add Photos/Video</span>
						</a>
					</td>
				</tr>
			</table>
		</div>
		<div class="post">
			<input rows="4" cols="30" placeholder="Share your inspiration" id="ShareBox" onkeyup="checkShareContent(this)"></textarea>
			<button type="button" onclick="addShareContent(ge('ShareBox'))">
				<?= i18n ( 'i18n_sharebox_share' ) ?>
			</button>
		</div>
	</div>
	<?}?>
	<div id="ShareContent"></div>
	
</div>
	<script type="text/javascript">
		/*function checkShareContent ( o )
		{
		}
		function addShareContent ( o )
		{
			if ( o.value.length > 0 )
			{
				var j = new bajax ();
				j.openUrl ( '<?= $this->parent->page->getUrl () ?>?function=savesharemessage', 'post', true );
				j.addVar ( 'Message', o.value );
				j.addVar ( 'CategoryID', <?= $this->parent->folder->ID ?> );
				j.addVar ( 'Type', 'Users' );
				j.onload = function ()
				{
					var r = this.getResponseText ().split ( '<!--separate-->' );
					if ( r[0] == 'ok' )
					{
						loadShareMessages ();
						o.value = '';
					}
					else alert ( this.getResponseText () );
				}
				j.send ();
				return;
			}
			alert ( '<?= i18n ( 'i18n_no_share_content_message' ) ?>' );
		}
		function replyToMessage ( mid )
		{
			ge ( 'mBox_' + mid ).className = 'ReplyBox';
			ge ( 'mBox_' + mid ).innerHTML = '' +
			'<table><tr><td style="width:37px"><div class="image"></div></td><td><div class="reply"><input placeholder="Write a comment..."><button type="button" onclick="sendReply(\'' + mid + '\', this.parentNode.parentNode.getElementsByTagName ( \'input\' )[0] )"><?= i18n ( 'i18n_store_reply' ) ?></button></div></td></tr></table>';
		}
		function loadShareMessages (  )
		{
			var sc = ge( 'ShareContent' );
			var j = new bajax ();
			j.openUrl ( '<?= $this->parent->page->getUrl () ?>?function=sharedposts', 'post', true );
			j.addVar ( 'CategoryID', <?= $this->parent->folder->ID ?> )
			j.addVar ( 'UserID', <?= $this->parent->cuser->ID ?> )
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );
				if ( r[0] == 'ok' )
				{
					if( r[1] ) sc.innerHTML = r[1]; 
				}
			}
			j.send ();
		}
		function sendReply ( mid, o, threadid )
		{
			if ( !threadid ) threadid = mid;
			if ( o.value.length > 0 )
			{
				var j = new bajax ();
				j.openUrl ( '<?= $this->parent->page->getUrl () ?>?function=savesharemessage', 'post', true );
				j.addVar ( 'Message', o.value );
				j.addVar ( 'ParentID', mid );
				j.addVar ( 'ThreadID', threadid );
				j.onload = function ()
				{
					var r = this.getResponseText ().split ( '<!--separate-->' );
					if ( r[0] == 'ok' )
					{
						loadShareMessages ();
						o.value = '';
					}
					else alert ( this.getResponseText () );
				}
				j.send ();
				return;
			}
			alert ( '<?= i18n ( 'i18n_no_share_content_message' ) ?>' );
		}
		function likeMessage ( mid )
		{
			alert ( 'Soon my man!' );
		}
		loadShareMessages ();*/
	</script>
