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
<div>
	<?
		$str = '';
		
		if( $themes = ThemeData() )
		{
			if( is_array( $themes ) )
			{
				foreach( $themes as $tms )
				{
					if( $tms->Name )
					{
						$str .= '<div class="floatleft" style="float:left;text-align:center;">';
						if( isset( $tms->Thumb ) && file_exists( $tms->Thumb ) )
						{
							$str .= '<div class="image floatleft" style="width:90px;height:60px;margin:5px;padding:5px;text-align:left;background:url(\\'' . $tms->Thumb . '\\');background-position:center center;background-size:cover;background-repeat:no-repeat;">';
							$str .= '<input type="radio" name="Theme" value="' . $tms->ID . '" ' . ( $this->u->Theme == $tms->ID ? 'checked="checked"' : '' ) . '/>';
							$str .= '</div>';
						}
						else
						{
							$str .= '<div class="image floatleft" style="width:90px;height:60px;margin:5px;padding:5px;text-align:left;background:url(\\'subether/gfx/img_placeholder.png\\');background-position:center center;background-size:cover;background-repeat:no-repeat;">';
							$str .= '<input type="radio" name="Theme" value="' . $tms->ID . '" ' . ( $this->u->Theme == $tms->ID ? 'checked="checked"' : '' ) . '/>';
							$str .= '</div>';
						}
						$str .= '<span> ' . $tms->Name . ' </span>';
						$str .= '</div>';
					}
				}
			}
		}
		
		return $str;
	?>
	<div class="clearboth" style="clear:both;"></div>
	<button class="save_btn" id="Submit" onclick="saveAccount( 'account_themes' )" name="account_themes"><?= i18n( 'i18n_Save' ) ?></button>
	<button class="cancel_btn" onclick="closeAccount()"><?= i18n( 'i18n_Cancel' ) ?></button>
</div>
