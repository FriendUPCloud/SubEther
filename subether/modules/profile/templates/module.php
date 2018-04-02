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
<?if ( $this->Scene != '' ) { ?><div id="Field_top"><?= $this->Scene ?></div><?}?>
<table id="Table_Fields">
	<tbody>
		<tr>
			<?if ( $this->LeftCol != '' ) { ?><td class="Col1"><div id="Field_left"><?= $this->LeftCol ?></div></td><?}?>
			<?if ( $this->MiddleCol != '' ) { ?><td class="Col2"><div id="Field_middle"><?= $this->MiddleCol ?></div></td><?}?>
			<?if ( $this->RightCol != '' ) { ?><td class="Col3"><div id="Field_right"><?= $this->RightCol ?></div></td><?}?>
		</tr>
	</tbody>
</table>
<div id="File_Uploader">
	<style>
		#File_Uploader
		{
			/*display: none;*/
			bottom: 0;
			direction: ltr;
			position: fixed;
			left: 0;
			z-index: 300;
			padding: 5px;
			margin: 5px;
			/*border: 1px solid black;
			background-color: white;*/
		}
		#File_Uploader > div > div
		{
			height: 25px;
		}
		.container
		{
			/*width: 600px;*/
			margin: 0 auto;
		}
		.progress_outer
		{
			position: relative;
			border: 1px solid #000;
			background-color: white;
			/*float: right;*/
			width: 350px;
		}
		.progress_info
		{
			position: absolute;
			left: 5px;
			top: 0;
			width: 97%;
		}
		.progress_number
		{
			float: right;
		}
		.progress
		{
			width: 0%;
			background: #DEDEDE;
			height: 20px;  
		}
    </style>
	<div id="_file_container" class='container'></div>
	<script src='subether/components/library/javascript/jsupload.js'></script>
</div>
<iframe style="position:absolute;left:-20000px;height:0px;width:0px;visibility:hidden;" name="fileIframe"></iframe>
<?if ( $this->Chat != '' ) { ?><div id="Field_bottom"><?= $this->Chat ?></div><?}?>
