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
<div id="Panel" class="Panel">

	<div>
	<?
		$allowed = array( 'profile' );
		
		if( $cats = getCategories( 'Users', 'Group', false, $this->parent->cuser ) )
		{
			$str = '';
			$cng = array( 'Groups'=>'Create Group...', 'Pages'=>'Create a Page...' );
			foreach( $cats as $cs )
			{
				if( in_array( trim( strtolower( $cs->Name ) ), $allowed ) )
				{
					$estr = '';
					$str .= '<h4><span>' . $cs->Name . '</span></h4>';
					if( $cng[$cs->Name] )
					{
						$estr .= '<li>';
						$estr .= '<div>';
						$estr .= '<a href="javascript:void(0)" onclick="openWindow( ' . "'" . $cs->Name . "'" . ', ' . "'" . $cs->ID . "'" . ' )">';
						$estr .= '<span></span>';
						$estr .= '<span>' . $cng[$cs->Name] . '</span>';
						$estr .= '<span></span>';
						$estr .= '</a>';
						$estr .= '</div>';
						$estr .= '</li>';
					}
					if( $subcats = getCategories( 'Users', 'SubGroup', $cs->ID, $this->parent->cuser ) )
					{
						$str .= '<ul>';
						foreach( $subcats as $sc )
						{
							if( $sc->ID == $this->parent->folder->CategoryID )
							{
								$str .= '<li class="current">';
							}
							else
							{
								$str .= '<li>';
							}
							$str .= '<div>';
							$str .=	'<a href="en/home/profile/' . ( strtolower( $cs->Name ) != 'profile' ? strtolower( $cs->Name ) . '/' . $sc->ID . '/' : strtolower( str_replace( ' ', '_', $sc->Name ) ) . '/' ) . '">';
							$str .= '<span></span>';
							$str .=	'<span>' . $sc->Name . '</span>';
							$str .=	'<span></span>';
							$str .=	'</a>';
							$str .=	'</div>';
							$str .= '</li>';
						}
						$str .= $estr;
						$str .= '</ul>';
					}
					else
					{
						$str .= '<ul>' . $estr . '</ul>';
					}
				}
			}
			return $str;
		}
	?>
	</div>
	
</div>
