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
		<td><label><?= i18n( 'i18n_Choose primary' ) ?>:</label></td>
		<td>
			<select id="LanguageCode">
				<?
					global $database, $webuser;
					
					$str = '';
					
					$u = new dbObject( 'SBookContact' );
					$u->UserID = $webuser->ID;
					if( $u->Load() )
					{
						if ( isset( $u->Data ) )
						{
							if ( is_string( $u->Data ) )
							{
								$u->Data = json_obj_decode( $u->Data );
							}
						}
					}
					
					if ( $lang = $database->fetchObjectRows( 'SELECT * FROM Languages ORDER BY ID ASC' ) )
					{
						foreach ( $lang as $obj )
						{
							$str .= '<option value="' . $obj->ID . '"' . ( /*isset( $GLOBALS[ "Session" ]->LanguageCode ) && $GLOBALS[ "Session" ]->LanguageCode == $obj->Name*/isset( $u->Data->LanguageCode ) && $u->Data->LanguageCode == $obj->Name ? ' selected="selected"' : '' ) . '>' . $obj->NativeName . ' (' . strtoupper( $obj->Name ) . ')' . '</option>';
						}
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
			<button class="save_btn" id="Submit" onclick="changeLanguage()"><?= i18n( 'i18n_Save' ) ?></button>
			<button class="cancel_btn" onclick="closeAccount()"><?= i18n( 'i18n_Cancel' ) ?></button>
		</td>
	</tr>
</table>
