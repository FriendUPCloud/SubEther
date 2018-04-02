<?php

/*******************************************************************************
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
*******************************************************************************/

global $database, $webuser;

// TODO remove old way
//$contacts = getContacts( 'Users', $cuser->ID );
$contacts = ContactRelations( $parent->cuser->ContactID, 'Contact' );

if( $contacts && $cuser )
{
	$imgs = array(); $idns = array();
	
	foreach( $contacts as $val )
	{
		if( !$val->NodeID && $val->ImageID > 0 && !$imgs[$val->ImageID] )
		{
			$imgs[$val->ImageID] = $val->ImageID;
		}
		
		if( $val->ID > 0 )
		{
			$idns[$val->ID] = $val->ID;
		}
	}
	
	if( $imgs && $img = $database->fetchObjectRows( '
		SELECT
			f.DiskPath, i.* 
		FROM
			Folder f, Image i
		WHERE
			i.ID IN (' . implode( ',', $imgs ) . ') AND f.ID = i.ImageFolder
		ORDER BY
			ID ASC
	', false, 'components/wall/functions/sharedposts.php' ) )
	{
		$imgs = array();
		
		foreach( $img as $i )
		{
			$obj = new stdClass();
			$obj->ID = $i->ID;
			$obj->Filename = $i->Filename;
			$obj->FileFolder = $i->ImageFolder;
			$obj->Filesize = $i->Filesize;
			$obj->FileWidth = $i->Width;
			$obj->FileHeight = $i->Height;
			$obj->DiskPath = str_replace( ' ', '%20', ( $i->DiskPath != '' ? $i->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $i->Filename );
			if ( $i->Filename )
			{
				$obj->DiskPath = ( BASE_URL . 'secure-files/images/' . ( $i->UniqueID ? $i->UniqueID : $i->ID ) . '/' );
			}
			
			$imgs[$i->ID] = $obj;
			
			if ( !FileExists( $obj->DiskPath ) )
			{
				unset( $imgs[$i->ID] );
			}
		}
	}
	
	$str = '';
	
	if( $parent->position == 'MiddleCol' )
	{
		$udn = GetUserDisplayname( $idns );
		
		$relations = ContactRelations();
		
		$td = 0;
		$str .= '<div class="Box">';
		//$str .= '<table><tr>';
		foreach( $contacts as $cs )
		{
			//if( !ContactRelation( $cs->UserID, $cuser->ID, true ) ) continue;
			
			$btn = '';
			
			if( isset( $relations[$cs->ID] ) && $relations[$cs->ID]->Status == 'Contact' )
			{
				$btn .= '<button onclick="profileOptions()">';
				$btn .= '<span>' . i18n( 'Contact' ) . '</span>';
				$btn .= '</button>';
			}
			else if( isset( $relations[$cs->ID] ) && $relations[$cs->ID]->Status == 'Pending' )
			{
				$btn .= '<button>';
				$btn .= '<span>' . i18n( 'Pending' ) . '</span>';
				$btn .= '</button>';
			}
			else if( $cs->UserID != $parent->webuser->ID )
			{
				$btn .= '<button onclick="addContact( \'' . $cs->ID . '\', this )">';
				$btn .= '<span>+ ' . i18n( 'Add Contact' ) . '</span>';
				$btn .= '</button>';
			}
			
			$td++;
			//$str .= '<td><div class="contact"><table><tr>';
			$str .= '<div class="contact"><table><tr>';
			$str .= '<td>';
			/*$i = new dbImage ();
			if( $cs->NodeID > 0 )
			{
				$i->NodeID = $cs->NodeID;
				$i->NodeMainID = $cs->ImageID;
			}
			else
			{
				$i->ID = $cs->ImageID;
			}
			if( $i->Load() )*/
			if( isset( $imgs[$cs->ImageID]->DiskPath ) )
			{
				//$str = $i->getImageHTML ( 100, 100, 'framed', false, 0xffffff );
				//$str .= '<div class="image" style="background-image:url(\'' . $i->getImageURL ( 100, 100, 'framed', false, 0xffffff ) . '\');background-position:center center;background-size:cover;background-repeat:no-repeat;"><a href="' . $parent->path . $cs->Username . '"></a></div>';
				$str .= '<div class="image" style="background-image:url(\'' . $imgs[$cs->ImageID]->DiskPath . '\');background-position:center center;background-size:cover;background-repeat:no-repeat;"><a href="' . $parent->path . $cs->Username . '"></a></div>';
			}
			else
			{
				$str .= '<div class="image" style="background-image:url(\'admin/gfx/arenaicons/user_johndoe_128.png\');background-position:center center;background-size:cover;background-repeat:no-repeat;"><a href="' . $parent->path . $cs->Username . '"></a></div>';
			}
			$str .= '</td><td>';
			//$str .= '<div><a href="' . $parent->path . $cs->Username . '"><strong>' . GetUserDisplayname( $cs->ID ) . '</strong></a></div>';
			$str .= '<div><a href="'/* . $parent->path*/ . $cs->Username . '"><strong>' . ( isset( $udn[$cs->ID] ) ? $udn[$cs->ID] : $cs->Username ) . '</strong></a></div>';
			$str .= '<p>' . $cs->About . '</p>';
			$str .= '<div>' . $btn . '</div>';
			$str .= '</td></tr></table></div>';
			/*$str .= '</td></tr></table></div></td>';
			if( $td == 4 )
			{
				$td = 0;
				$str .= '</tr><tr>';
			}*/
		}
		//$str .= '</tr></table>';
		$str .= '<div class="clearboth" style="clear:both"></div>';
		$str .= '</div>';
	}
	else
	{
		$cname = GetUserDisplayname( $idns );
		
		$str .= '<h4><span>' . i18n( 'i18n_Contacts' ) . '</span></h4>';
		$str .= '<ul>';
		foreach( $contacts as $cs )
		{
			//if( !ContactRelation( $cs->UserID, $cuser->ID, true ) ) continue;
			
			$str .= '<li>';
			$str .= '<div onmouseover="tooltips(this,\'open\')" onmouseout="tooltips(this,\'close\')">';
			$str .= '<a href="' . $cs->Username . '"><span><div class="image" style="background-image:url(\'' . ( isset( $imgs[$cs->ImageID]->DiskPath ) ? $imgs[$cs->ImageID]->DiskPath : 'admin/gfx/arenaicons/user_johndoe_32.png' ) . '\');background-position:center center;background-size:cover;background-repeat:no-repeat;">';
			/*$i = new dbImage ();
			if( $cs->NodeID > 0 )
			{
				$i->NodeID = $cs->NodeID;
				$i->NodeMainID = $cs->ImageID;
			}
			else
			{
				$i->ID = $cs->ImageID;
			}
			if( $i->Load() )
			{
				$str .= $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
			}*/
			$str .= '</div></span>';
			$str .= '<span>' . ( isset( $cname[$cs->ID] ) ? $cname[$cs->ID] : $cs->Username ) . '</span></a>';
			//$str .= '<span>' . ( isset( $cname[$cs->ID] ) ? $cname[$cs->ID] : $cs->Username ) . '</span></a>';
			
			// --- Tooltips ---
			
			$str .= '<div class="tooltips">';
			$str .= '<div class="inner">';
			$str .= '<div class="image" style="background-image:url(\'' . ( isset( $imgs[$cs->ImageID]->DiskPath ) ? $imgs[$cs->ImageID]->DiskPath : 'admin/gfx/arenaicons/user_johndoe_32.png' ) . '\');background-repeat:no-repeat;background-position:center center;background-size:cover;"></div>';
			$str .= '<div class="text">';
			$str .= '<h3>' . ( isset( $cname[$cs->ID] ) ? $cname[$cs->ID] : $cs->Username ) . '</h3>';
			$str .= '</div>';
			$str .= '<div class="clearboth" style="clear:both"></div>';
			$str .= '</div>';
			$str .= '</div>';
			
			// ---
			
			$str .=	'</div>';
			$str .= '</li>';
		}
		$str .= '</ul>';
		
		//die( print_r( $cname,1 ) . ' -- ' . print_r( $ids,1 ) );
	}
}

?>
