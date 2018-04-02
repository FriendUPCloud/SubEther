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
<div id="AudioPlayer">
	<div class="player">
		<div class="artist">
			01.mp3
		</div>
		<div>
			<div class="played">1:17</div>
			<div class="tracker_wrapper">
				<div class="tracker_bar" style="width:30%;">
					<div class="tracker_control" onmousedown="testonMouseDown(event,this)"></div>
				</div>
			</div>
			<div class="length">4:45</div>
			<div class="clearboth" style="clear:both"></div>
		</div>
		<div>
			<div class="shuffle" onclick="audioLoop(this,ge('AudioElement'))"></div>
			<div class="rewind"></div>
			<div class="play" onclick="audioPlay(this,ge('AudioElement'))"></div>
			<div class="pause hidden" onclick="audioPause(this,ge('AudioElement'))"></div>
			<div class="forward"></div>
			<div class="volume"></div>
			<div class="clearboth" style="clear:both"></div>
		</div>
		
		<audio id="AudioElement" src="http://treeroot.org/subether/upload/profile/113/library/audio/ahcrap.mp3"></audio>
		
		<!--<div class="pl"></div>
		<div class="title"></div>
		<div class="artist"></div>
		<div class="cover"></div>
		<div class="controls">
			<div class="play"></div>
			<div class="pause"></div>
			<div class="rew"></div>
			<div class="fwd"></div>
		</div>
		<div class="volume"></div>
		<div class="tracker"></div>-->
	</div>
	<ul class="playlist">
		<li audiourl="http://treeroot.org/subether/upload/profile/113/library/audio/ahcrap.mp3" cover="cover1.jpg" artist="ahcrap" class="selected" onclick="audioSelect(this,'AudioPlayer')">ahcrap.mp3</li>
		<li audiourl="http://treeroot.org/subether/upload/profile/113/library/audio/AllRight.mp3" cover="cover2.jpg" artist="AllRight" onclick="audioSelect(this,'AudioPlayer')">AllRight.mp3</li>
	</ul>
</div>
