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
<div id="Profile">
	
	<div class="coverimage">
		
		<div class="slideshow">
			<?
				if( $folder = getMediaFolders( 'Folder', 'Cover Photos', 1, false, $this->parent->cuser ) )
				{
					$imageWidth = 1000;
					$imageHeight = 315;
					
					$i = new dbImage ();
					/*$i->Tags = 'main';*/
					$i->ImageFolder = $folder->ID;
					if( $i->load () && $i->ID > 0 )
					{
						$str = '';	
						$str .= '<div id="MainImage" onclick="nextCoverImage( event )">';
						$str .= $i->getImageHTML ( $imageWidth, $imageHeight, 'framed', false, 0x000000 );
						$str .= '</div>';
						$str .= '<div class="Navigation">';
						$str .= '<div class="ArrowPrev" onclick="coverStopSlideshow(); prevCoverImage()"><span>«</span></div>';
						$str .= '<div id="CoverPages">';
						
						$pgs = new dbImage ();
						$pgs->ImageFolder = $folder->ID;
						$pgs->addClause ( 'ORDER BY', 'SortOrder ASC' );
						if ( $pgs = $pgs->find () )
						{
							$ii = 0;
							$pg = '';
							foreach( $pgs as $p )
							{
								$imgurl = '';
								if ( $ii == 0 ) $c = ' Current'; else $c = '';
								++$ii;
								$imgurl = $p->getImageUrl ( $imageWidth, $imageHeight, 'framed', false, 0x000000 );
								$pg .= '<div class="Page Nr' . $ii . $c . '" onclick="setCoverImage(' . "'" . $imgurl . "'" . ', ' . "'" . $ii . "'" . ')"><span>' . $ii . '</span></div>';
							}
							if( $ii > 1 )
							{
								$str .= $pg;
							}
						}
						
						$str .= '</div>';
						$str .= '<div class="ArrowNext" onclick="coverStopSlideshow(); nextCoverImage()"><span>»</span></div>';
						$str .= '</div>';
						
						return $str;
					}
				}
			?>	
		</div>
		
		<div class="imagebox">
			<table>
				<tr>
					<td class="Col1">
					
						<div id="Avatar" class="image" onclick="openWindow( 'Profile', '<?= $this->parent->cuser->ID ?>', 'avatar', function(){ Showroom.init( 'Avatar_Showroom' ); } )">
							<a href="javascript:void(0)">
								<?
									$i = new dbImage ();
									if( $i->load( $this->parent->cuser->Image ) )
									{
										return $i->getImageHTML ( 160, 160, 'framed', false, 0xffffff );
									}
								?>
							</a>
						</div>
						
						<?if ( $this->parent->webuser->ID == $this->parent->cuser->ID ) { ?>
						<div class="edit_btn">
							<div><span>Edit Profile Picture</span></div>
							<form method="post" target="fileIframe" name="avatarUpload" enctype="multipart/form-data" action="?action=uploadfile">
								<input type="file" class="upload_btn" id="avatarUploadBtn" name="avatar" onchange="document.avatarUpload.submit();">
							</form>
							<script>setOpacity ( ge('avatarUploadBtn' ), 0 );</script>
						</div>
						<?}?>
						
					</td>
					<td class="Col2">
						<div>
							<h2><a href="en/home/<?= $this->parent->cuser->Username ?>"><?= $this->parent->cuser->Username ?></a></h2>
						</div>
					</td>
				</tr>
			</table> 
		</div>
		
		<?if ( $this->parent->webuser->ID == $this->parent->cuser->ID ) { ?>
		<div class="edit_btn_cover">
			<div><span>Change Cover</span></div>
			<form method="post" target="fileIframe" name="coverUpload" enctype="multipart/form-data" action="?action=uploadfile">
				<input type="file" class="upload_btn" id="coverUploadBtn" name="cover" onchange="document.coverUpload.submit();">
			</form>
			<script>setOpacity ( ge('coverUploadBtn' ), 0 );</script>
		</div>
		<?}?>
		
		<div class="view_btn_cover" onclick="openWindow( 'Profile', '<?= $this->parent->cuser->ID ?>', 'cover', function(){ Showroom.init( 'Cover_Showroom' ); } )">
			<div><span>View Album</span></div>
		</div>
		
	</div>
	
	<div class="infobox">
		<table>
			<tr class="Row1">
				<td class="Col1" colspan="10">
					<div>
						<?
							$str = '';
							if( $this->webuser->parent->ID > 0 && $this->parent->cuser->ID == $this->parent->webuser->ID )
							{
								$str .= '<button>';
								$str .= '<span>Update</span>';
								$str .= '</button>';
							}
							else if( $this->parent->webuser->ID > 0 && $this->parent->cuser->Contacts && in_object( $this->parent->webuser->ID, $this->parent->cuser->Contacts ) )
							{
								$str .= '<button>';
								$str .= '<span>Contact</span>';
								$str .= '</button>';
							}
							else if( $this->parent->webuser->ID > 0 )
							{
								$str .= '<button onclick="addContact( \\'' . $this->patent->cuser->ID . '\\', this )">';
								$str .= '<span>+ Add Contact</span>';
								$str .= '</button>';
							}
							return $str;
						?>
					</div>
				</td>
			</tr>
			<tr class="Row2">
				<td class="Col1">
					<div>
						<div>
							<div><span></span><span>Taskmaster at Sub-Ether</span></div>
							<div><span></span><span>Hard reality of life</span></div>
							<div><span></span><span>Norwegian, English and German</span></div>
							<div><span></span><span>"live free and die hard"</span></div>
						</div>
					</div>
				</td>
				<td class="Col2">
					<div>
						<? 
							if( $folder = getMediaFolders( 'Folder', 'Cover Photos', 1, false, $this->parent->cuser ) )
							{
								$thumbWidth = 105;
								$thumbHeight = 84;
								$thumbLimit = 5;
								
								$thumb = new dbImage ();
								$thumb->ImageFolder = $folder->ID;
								$thumb->addClause ( 'LIMIT', $thumbLimit );
								$thumb->addClause ( 'ORDER BY', 'SortOrder ASC' );
								if ( $thumb = $thumb->find () )
								{
									$str = '';
									$ii = 0;
									foreach( $thumb as $t )
									{
										$ii++;
										$str .= '<div>';
										$str .= $t->getImageHTML ( $thumbWidth, $thumbHeight, 'framed', false, 0x000000 );
										$str .= '</div>';
									}
									if( $ii < $thumbLimit )
									{
										$rest = ( $thumbLimit - $ii );
										for( $a = 0; $a < $rest; $a++ )
										{
											$str .= '<div></div>';
										}
									}
									return $str;
								}
							}
							else
							{
								return '<div></div><div></div><div></div><div></div><div></div>';
							}
						?>
					</div>
				</td>
			</tr>
		</table>
	</div>
	
</div>
<iframe style="position: absolute; left: -20000px" name="fileIframe"></iframe>

