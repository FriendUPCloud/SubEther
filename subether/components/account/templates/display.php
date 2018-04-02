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
<table>
	<tr>
		<td><label><?= i18n( 'i18n_Choose mode' ) ?>:</label></td>
		<td>
			<select id="DisplayCode">
				<?
					$modes = array( 0=>'Default', 1=>'Mobile', 2=>'Presentation', 3=>'Tablet' );
					
					$str = ''; $data = false;
					
					if ( isset( $this->parent->webuser->Data ) )
					{
						if ( is_string( $this->parent->webuser->Data ) )
						{
							$data = json_obj_decode( $this->parent->webuser->Data );
						}
						else
						{
							$data = $this->parent->webuser->Data;
						}
					}
					
					foreach ( $modes as $key=>$val )
					{
						$s = ( isset( $data->Display ) && $data->Display == $key ? ' selected="selected"' : '' );
						$str .= '<option value="' . $key . '"' . $s . '>' . i18n( 'i18n_' . $val ) . '</option>';
					}
					
					return $str;
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	<tr>
		<td colspan="2">
			<button class="save_btn" id="Submit" onclick="changeDisplayMode(event)"><?= i18n( 'i18n_Save' ) ?></button>
			<button class="cancel_btn" onclick="closeAccount()"><?= i18n( 'i18n_Cancel' ) ?></button>
		</td>
	</tr>
</table>
