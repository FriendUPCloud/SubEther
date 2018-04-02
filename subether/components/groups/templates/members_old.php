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
<!--<div class="Box">
	<div class="members">
		<table class="panel">
			<tr>
				<td style="text-align:right;">
					<button onclick="openWindow( 'Groups', '<?= $this->parent->folder->CategoryID ?>', 'invite' )">+ Invite Members</button>
				</td>
			</tr>
		</table>-->
		<? 
			/*if( $members = getSBookGroupMembers( $this->parent->folder->CategoryID ) )
			{
				$td = 0;
				$str = '<table><tr>';
				foreach( $members as $m )
				{
					$td++;
					$u = new dbObject( 'SBookContact' );
					$u->UserID = $m->ObjectID;
					$u->load();
					
					$str .= '<td><table><tr>';
					$str .= '<td><div class="image"><a href="' . $this->parent->path . $u->Username . '">';
					$i = new dbImage ();
					if( $i->load( $u->ImageID ) )
					{
						$str .= $i->getImageHTML ( 50, 50, 'framed', false, 0xffffff );
					}
					$str .= '</a></div></td>';
					$str .= '<td><div><a href="' . $this->parent->path . $u->Username . '">' . $u->Username . '</a></div></td>';
					$str .= '</tr></table></td>';
					if( $td == 6 )
					{
						$td = 0;
						$str .= '</tr><tr>';
					}
				}
				$str .= '</tr></table>';
				return $str;
			}*/
		?>
	<!--</div>
</div>-->
	
