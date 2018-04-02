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
	
	<div class="coverimage<?= !$this->coverimage ? ' display' : '' ?>">
		
		<div class="slideshow">
			<?
				global $database;
				
				if( $this->slideshow != '' )
				{
					return $this->slideshow;
				}
				else
				{
					$placeholder = 'subether/gfx/img_placeholder5.png';
					return '<div id="MainImage" style="background-image:url(\\'subether/gfx/img_placeholder4.png\\');background-position:center 50%;background-size:630px auto;background-color:#e5e5e5;background-repeat:no-repeat;"></div>';
				}
			?>	
		</div>
		
		<div class="imagebox<?= !$this->profileimage ? ' display' : '' ?>">
			<table>
				<tr>
					<td class="Col1">
						<?
							if( $this->profileimage != '' )
							{
								return $this->profileimage;
							}
							else
							{
								$imgurl = 'admin/gfx/arenaicons/user_johndoe_128.png';
								return '<div id="Avatar" class="image"><div style="background-image:url(\\'' . $imgurl . '\\');background-repeat:no-repeat;background-size:110% auto;background-position:center 25%;"></div></div>';
							}
						?>
						
						<!--<div id="Avatar" class="image" onclick="openWindow( 'Profile', '<?= $this->parent->cuser->ID ?>', 'avatar', function(){ Showroom.init( 'Avatar_Showroom' ); } )">
							<a href="javascript:void(0)">
								<?
									$i = new dbImage ();
									if( $i->load( $this->parent->cuser->Image ) )
									{
										return $i->getImageHTML ( 160, 160, 'framed', false, 0xffffff );
									}
								?>
							</a>
						</div>-->
						
						<?if ( ( $this->parent->webuser->ID == $this->parent->cuser->ID ) || IsSystemAdmin() ) { ?>
						<div class="edit_btn" onclick="ge('avatarUploadBtn').click();">
							<div><span><?= i18n( 'i18n_Edit Profile Picture' ) ?></span></div>
							<div class="uploadfile">
								<form method="post" target="fileIframe" name="avatarUpload" enctype="multipart/form-data" action="<?= $this->parent->route ?>?global=true&action=uploadfile">
									<input type="file" class="upload_btn" id="avatarUploadBtn" name="avatar" onchange="document.avatarUpload.submit();">
								</form>
								<script>//setOpacity ( ge('avatarUploadBtn' ), 0 );</script>
							</div>
						</div>
						<?}?>
						
					</td>
				</tr>
			</table> 
		</div>
		
		<div class="background_layer"></div>
		
		<div class="edit_wrapper">
			<?if ( ( $this->parent->webuser->ID == $this->parent->cuser->ID ) || IsSystemAdmin() ) { ?>
			<div class="edit_btn_cover" onclick="ge('coverUploadBtn').click();">
				<div><span><?= i18n( 'i18n_Change Cover' ) ?></span></div>
				<div class="uploadfile">
					<form method="post" target="fileIframe" name="coverUpload" enctype="multipart/form-data" action="<?= $this->parent->route ?>?global=true&action=uploadfile">
						<input type="file" class="upload_btn" id="coverUploadBtn" name="cover" onchange="document.coverUpload.submit();">
					</form>
					<script>//setOpacity ( ge('coverUploadBtn' ), 0 );</script>
				</div>
			</div>
			<?}?>
			
			<!--<?if ( $this->parent->cuser->Image ) { ?>
			<div class="view_btn_cover" onclick="openWindow( 'Profile', '<?= $this->parent->cuser->ID ?>', 'cover', function(){ Showroom.init( 'Cover_Showroom' ); } )">
				<div><span>View Album</span></div>
			</div>
			<?}?>-->
			
			<?
				if( $this->parent->cuser->ImageID )
				{
					return $this->coverimage;
				}
			?>
		</div>
	</div>
	
	<div class="name">
		<h2><a href="<?= $this->parent->cuser->Username ?>"><?= ( GetUserDisplayName( $this->parent->cuser->ContactID ) ? GetUserDisplayName( $this->parent->cuser->ContactID ) : $this->parent->cuser->Username ) . ( $this->parent->cuser->ShowAlternate > 0 && $this->parent->cuser->Alternate != '' ? '<span style="font-weight: normal;"> (' . $this->parent->cuser->Alternate . ')</span>' : '' ) ?></a></h2>
		<?if ( ( $this->parent->webuser->ID == $this->parent->cuser->ID ) || IsSystemAdmin() ) { ?>
		<div class="Edit profile"><div onclick="document.location='<?= $this->parent->cuser->Username ?>/about/?edit=true';" class="options"></div></div>
		<?}?>
	</div>
	
	<div class="infobox">
		<table>
			<tr class="Row1">
				<td class="Col1" colspan="10">
					<div>
						<?
							$video = false;
							
							$cdata = UserData( $this->parent->cuser->ID );
							$usdata = UserData( $this->parent->webuser->ID );
							$us = IsUserOnline( $this->parent->cuser->ID );
							
							$relations = ContactRelations();
							
							if( $us && ( stripos( $us->UserAgent, 'Chrome' ) !== false || $us->DataSource == 'node' ) && stripos( $_SERVER['HTTP_USER_AGENT'], 'Chrome' ) !== false )
							{
								$video = true;
							}
							
							if( ( date( 'YmdHi' ) - date( 'YmdHi', strtotime( $us->LastActivity ) ) ) > 4 )
							{
								$us = '';
							}
							
							$str = '';
							
							if( $this->parent->webuser->ID > 0 && isset( $this->parent->access->IsSystemAdmin ) )
							{
								$str .= '<button onclick="deleteUser()">';
								$str .= '<span>' . i18n( 'i18n_Delete User' ) . '</span>';
								$str .= '</button>';
							}
							
							if( $this->parent->webuser->ID > 0 && isset( $relations[$this->parent->cuser->ContactID] ) && $relations[$this->parent->cuser->ContactID]->Status == 'Contact' )
							{
								$str .= '<button onclick="profileOptions()">';
								$str .= '<span>' . i18n( 'i18n_Contact' ) . '</span>';
								$str .= '</button>';
							}
							else if( $this->parent->webuser->ID > 0 && isset( $relations[$this->parent->cuser->ContactID] ) && $relations[$this->parent->cuser->ContactID]->Status == 'Pending' )
							{
								$str .= '<button>';
								$str .= '<span>' . i18n( 'i18n_Pending' ) . '</span>';
								$str .= '</button>';
							}
							else if( $this->parent->webuser->ID > 0 && $this->parent->cuser->UserID != $this->parent->webuser->ID )
							{
								$str .= '<button onclick="addContact( \\'' . $this->parent->cuser->ContactID . '\\', this )">';
								$str .= '<span>' . i18n( 'i18n_Add Contact' ) . '</span>';
								$str .= '</button>';
							}
							
							//if( $usdata->Settings->Plivo->onLogin > 0 && $cdata->Settings->Plivo->onLogin > 0 )
							if( $video )
							{
								$voicechat = 'chatObject.callUser(\\'' . $this->parent->cuser->ContactID . '\\',\\'' . $this->parent->cuser->Username  . '\\',\\'false\\',event)';
								
								$str .= '<button onclick="' . $voicechat . '">';
								$str .= '<span>' . i18n( 'i18n_Call' ) . '</span>';
								$str .= '</button>';
							}
							
							$onclick = 'chatObject.addPrivateChat( \\'' . $this->parent->cuser->ContactID . '\\', \\'' . $this->parent->cuser->Username . '\\', \\'' . ( $us ? 1 : 0 ) . '\\', \\'default\\' )';
							if( $usdata && $usdata->Settings->Chat == '1' )
							{
								$onclick = 'openWindow( \\'Chat\\', \\'' . $this->parent->cuser->ID . '\\', \\'chatwindow\\', function(){ openPrivChat( \\'' . $this->parent->cuser->ContactID . '\\', \\'' . $this->parent->cuser->Username . '\\', \\'window\\' ); } );';
							}
							
							$str .= '<button onclick="' . $onclick . '">';
							$str .= '<span>' . i18n( 'i18n_Message' ) . '</span>';
							$str .= '</button>';
							
							$str .= '<div id="ProfileOptionsBox">';
							$str .= '<div class="toparrow"></div>';
							$str .= '<div class="inner"></div>';
							$str .= '</div>';
							
							//if( $this->parent->webuser->ID > 0 ) return $str;
							return $str;
						?>
					</div>
				</td>
			</tr>
			<tr class="Row2">
				<td class="Col1">
					<div>
						<div class="info<?= !$data->Settings->Profile ? ' display' : '' ?>">
							<?
								$data = json_decode( $this->parent->cuser->Data );
								
								/*$array = array(
									'Work'=>'no work',
									'HighSchool'=>'no highschool',
									'Philosophy'=>'no philosophy',
									'Political'=>'no political'
								);*/
								
								$i = 1; $str = '';
								//die( print_r( $data->Settings->Profile,1 ) . ' -- ' . print_r( $this->parent->cuser,1 ) );
								if( $this->parent->cuser->Data && $data->Settings->Profile )
								{
									foreach( $data->Settings->Profile as $k=>$v )
									{
										if( $v != '0' && $i <= 4 && $this->parent->cuser->$k )
										{
											$username = $this->parent->cuser->$k;
											$username = strip_tags( $username );
											$str .= '<div class="' . strtolower( $k ) . '"><span class="icon"></span><span class="name">' . $username . '</span></div>';
											$i++;
										}
									}
									if( $str )
									{
										return $str;
									}
								}
								
								if( $this->parent->cuser->Data && $data->Settings->Profile )
								{
									foreach( $data->Settings->Profile as $k=>$v )
									{
										if( $i <= 4 && $this->parent->cuser->$k )
										{
											$username = $this->parent->cuser->$k;
											$username = strip_tags( $username );
											$str .= '<div class="' . strtolower( $k ) . '"><span class="icon"></span><span class="name">' . $username . '</span></div>';
											$i++;
										}
									}
									if( $str )
									{
										return $str;
									}
								}
								
								return i18n( 'i18n_This user has not' ) . '<br>
								' . i18n( 'i18n_updated his or her' ) . '<br>
								' . i18n( 'i18n_profile yet' ) . '.';
							?>
							<?if ( ( $this->parent->webuser->ID == $this->parent->cuser->ID ) || IsSystemAdmin() ) { ?>
							<div class="Edit profile" title="Edit info"><div onclick="document.location='<?= $this->parent->cuser->Username ?>/about/?edit=true';" class="options"></div></div>
							<?}?>
						</div>
					</div>
				</td>
				<td class="Col2">
					<div>
						<?
							global $database, $webuser;
							
							$placeholder = 'background-image:url(\\'subether/gfx/img_placeholder4.png\\');background-position:center center;background-size:cover;background-color:#e5e5e5;background-repeat:no-repeat;';
							
							//if( $folder = getMediaFolders( 'Folder', 'Cover Photos', 1, false, $this->parent->cuser ) )
							$folder = new dbObject( 'SBookMediaRelation' );
							$folder->UserID = $this->parent->cuser->ID;
							$folder->CategoryID = '0';
							$folder->MediaType = 'Folder';
							$folder->Name = 'Cover Photos';
							$folder->addClause ( 'ORDER BY', 'ID ASC' );
							if( $folder->Load() )
							{
								$thumbWidth = 105;
								$thumbHeight = 84;
								$thumbLimit = 5;
								
								if( $thumb = $database->fetchObjectRows( '
									SELECT
										f.DiskPath, i.* 
									FROM
										Folder f, Image i
									WHERE
										i.ImageFolder = "' . $folder->MediaID . '" AND f.ID = i.ImageFolder
									ORDER BY
										i.SortOrder ASC, i.ID DESC
									LIMIT ' . $thumbLimit . '
								', false, 'components/profile/templates/component.php' ) )
								//$thumb = new dbImage ();
								//$thumb->ImageFolder = $folder->MediaID;
								//$thumb->addClause ( 'LIMIT', $thumbLimit );
								//$thumb->addClause ( 'ORDER BY', 'SortOrder ASC' );
								//if ( $thumb = $thumb->find () )
								{
									$arr = array();
									$ii = 0;
									foreach( $thumb as $t )
									{
										//$imgurl = str_replace( ' ', '%20', ( $t->DiskPath != '' ? $t->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $t->Filename );
										$imgurl = ( BASE_URL . 'secure-files/images/' . ( $t->UniqueID ? $t->UniqueID : $t->ID ) . '/' );
										
										if ( !FileExists( $imgurl ) )
										{
											$t->Filename = false;
										}
										
										if( $t->Filename )
										{
											$str  = '<div style="' . $placeholder . '">';
											//$str .= $t->getImageHTML ( $thumbWidth, $thumbHeight, 'framed', false, 0x000000 );
											$str .= '<div style="background-image:url(' . $imgurl . ');width:100%;height:100%;background-repeat:no-repeat;background-size:cover;background-position:center center;"></div>';
											$str .= '</div>';
											
											$arr[] = $str;
											
											$ii++;
										}
									}
									if( $ii < $thumbLimit )
									{
										$rest = ( $thumbLimit - $ii );
										for( $a = 0; $a < $rest; $a++ )
										{
											$arr[] = '<div style="' . $placeholder . '"></div>';
										}
									}
									
									$str = ( $arr && is_array( $arr ) ? implode( array_reverse( $arr ) ) : '' );
									
									return $str;
								}
							}
							else
							{
								return '<div style="' . $placeholder . '"></div style="' . $placeholder . '"><div style="' . $placeholder . '"></div><div style="' . $placeholder . '"></div><div style="' . $placeholder . '"></div><div style="' . $placeholder . '"></div>';
							}
						?>
					</div>
				</td>
			</tr>
		</table>
	</div>
	
</div>
