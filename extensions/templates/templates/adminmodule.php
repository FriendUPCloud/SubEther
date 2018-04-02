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
	<table class="Gui">
		<tr>
			<td><strong>Velg malfil:</strong></td>
			<td>
				<select id="select<?= $this->field->ID ?>"><?= $this->options ?></select>
			</td>
		</tr>
	</table>
	<script>
		AddSaveFunction ( function ()
		{
			var j = new bajax ();
			j.openUrl ( 'admin.php?module=extensions&extension=templates&action=savefield', 'post', true );
			j.addVar ( 'field', '<?= $this->field->ID ?>' );
			j.addVar ( 'template', ge('select<?= $this->field->ID ?>').value );
			j.send ();
		}
		);
	</script>
