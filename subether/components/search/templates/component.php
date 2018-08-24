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
<div id="Search">
	<table>
		<tr>
			<td>
				<div id="SearchIcon" onclick="initSearchField()"></div>
				<div id="SearchWrapper">
					<select id="SearchFilter">
						<?
							if( $this->parent->webuser->ID > 0 )
							{
								//$options = array(
								//	'0'=>'All', 'network'=>'Network',/* 'images'=>'Images',*/
								//	'videos'=>'Videos', 'streaming'=>'Streaming', 'torrents'=>'Torrents',
								//	'contacts'=>'Contacts', 'groups'=>'Groups'
								//);
							}
							else
							{
								//$options = array(
								//	'0'=>'All'
								//);
							}
							
							$options = array(
								'0'=>'Web', 'network'=>'Network', 'images'=>'Images', 'files'=>'Files', 
								'videos'=>'Videos', /*'streaming'=>'Streaming', 'torrents'=>'Torrents',*/
								'contacts'=>'Contacts', 'groups'=>'Groups', 'wall'=>'Wall'
							);
							
							$str = '';
							foreach( $options as $k=>$value )
							{
								if( !in_array( 'home', $this->parent->url ) && $k == 'wall' && $value != 'Web' )
								{
									continue;
								}
								else if( in_array( 'home', $this->parent->url ) && !$_REQUEST[ 'r' ] && $k == 'network' )
								{
									$s = 'selected="selected"';
								}
								else if ( $_REQUEST[ 'r' ] == $k )
								{
									$s = 'selected="selected"';
								}
								else $s = '';
								$str .= '<option value="' . $k . '" ' . $s . '>' . i18n( 'i18n_' . $value ) . '</option>';
							}
							return $str;
						?>
					</select>
					<input id="SearchContent" type="text" onkeyup="if( event.keyCode == 13 ){ search( this.value, false, 'submit', this.parentNode.getElementsByTagName('select')[0].value ); }" value="<?= $_REQUEST[ 'q' ] ?>"/>
					<button onclick="search( this.parentNode.getElementsByTagName('input')[0].value, false, 'submit', this.parentNode.getElementsByTagName('select')[0].value );">
						<span>Search The Ether</span>
					</button>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="SearchOptions"></div>
			</td>
		</tr>
	</table>
</div>

