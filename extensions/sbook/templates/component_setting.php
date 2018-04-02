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

	<strong><?= isset( $this->component ) ? $this->component : i18n('component_default' ) ?></strong>
	
	<table class="Gui">
		<tr>
			<td>
				Activated:
			</td>
			<td>
				<input type="checkbox" onclick="toggleComponent<?= $this->field->ID ?>('<?= $this->component ?>', this.checked?'1':'0')" class="Activated"<?= $this->activated ? ' checked="checked"' : '' ?>/>
			</td>
		</tr>
	</table>
	
