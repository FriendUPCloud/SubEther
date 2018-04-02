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
<div id="Contacts">

	<div>
	<?
		if( $contacts = getContacts( 'Users', $this->parent->cuser->ID ) )
		{
			$str = '';
			$str .= '<h4><span>Contacts</span></h4>';
			$str .= '<ul>';
			foreach( $contacts as $cs )
			{
				$str .= '<li>';
				$str .= '<div>';
				$str .= '<a href="en/home/' . $cs->Username . '"><span><div class="image">';
				$i = new dbImage ();
				if( $i->load( $cs->ImageID ) )
				{
					$str .= $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
				}
				$str .= '</div></span>';
				$str .= '<span>' . $cs->Username . '</span></a>';
				$str .=	'</div>';
				$str .= '</li>';
			}
			$str .= '</ul>';
			return $str;
		}
	?>
	</div>
	
</div>
