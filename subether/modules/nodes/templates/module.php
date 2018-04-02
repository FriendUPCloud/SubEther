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
<table id="Table_Fields">
	<tbody>
		<tr>
			<?if ( $this->LeftCol != '' ) { ?><td class="Col1"><div id="Field_left"><?= $this->LeftCol ?></div></td><?}?>
			<?if ( $this->MiddleCol != '' ) { ?><td class="Col2"><div id="Field_middle"><?= $this->MiddleCol ?></div></td><?}?>
			<?if ( $this->RightCol != '' ) { ?><td class="Col3"><div id="Field_right"><?= $this->RightCol ?></div></td><?}?>
		</tr>
	</tbody>
</table>
