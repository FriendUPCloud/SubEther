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

global $webuser, $database;

if( isset( $_POST['search'] ) )
{
	$query = array();
	
	$keywords = explode( ' ', $_POST['search'] );
	
	foreach( $keywords as $key )
	{
		$query[] = 'c.Firstname LIKE "' . $key . '%"';
	}
	
	if ( IsSystemAdmin() )
	{
		$q = '
			SELECT 
				c.*,
				i.UniqueID AS ImageUniqueID,
				i.Filename,
				f.DiskPath  
			FROM 
				SBookContact c
					LEFT JOIN Image i ON
					(
						c.ImageID = i.ID
					)
					LEFT JOIN Folder f ON
					(
						i.ImageFolder = f.ID
					) 
			WHERE 
				(
						c.Display = "0"
					AND c.Username LIKE "' . $keywords[0] . '%" 
				) 
				OR
				(
						c.Display > 0
					AND ( ' . implode( ' OR ', $query ) . ' ) 
				) 
			ORDER BY 
				c.Firstname ASC,
				c.Username ASC 
		';
	}
	else
	{
		$q = '
			SELECT 
				c.*,
				i.UniqueID AS ImageUniqueID,
				i.Filename,
				f.DiskPath  
			FROM 
				SBookContact c
					LEFT JOIN Image i ON
					(
						c.ImageID = i.ID
					)
					LEFT JOIN Folder f ON
					(
						i.ImageFolder = f.ID
					)
					LEFT JOIN SBookContactRelation r ON
					(
						(
								r.ObjectID = \'' . $webuser->ID . '\'
							AND r.ObjectType = "Users"
							AND c.ID = r.ContactID
						) 
						OR
						(
								r.ObjectID = \'' . $webuser->ContactID . '\'
							AND r.ObjectType = "SBookContact"
							AND c.ID = r.ContactID
						) 
						OR
						(
								r.ContactID = \'' . $webuser->ContactID . '\'
							AND r.ObjectType = "SBookContact"
							AND c.ID = r.ObjectID
						)
					)
			WHERE 
				(
					(
							c.ID > 0 
						AND r.ID > 0 
					)
					OR
					(
							c.ID = \'' . $webuser->ContactID . '\' 
						AND r.ID IS NULL 
					)
				) 
				AND
				(
					(
							c.Display = "0"
						AND c.Username LIKE "' . $keywords[0] . '%"
					) 
					OR
					(
							c.Display > 0
						AND ( ' . implode( ' OR ', $query ) . ' ) 
					)
				)
			ORDER BY  
				c.Firstname ASC,
				c.Username ASC 
		';
	}
	//die( $q . ' --' );
	if( $fm = $database->fetchObjectRows( $q ) )
	{
		$mg = new dbObject( 'SBookCategory' );
		$mg->ID = $_POST['groupid'];
		if( $_POST['groupid'] > 0 && $mg->Load() )
		{
			$gcheck = $mg->Type;
		}
		
		$str = '<div><table>';
		foreach( $fm as $m )
		{
			$m->Username = ( GetUserDisplayname( $m->ID ) ? GetUserDisplayname( $m->ID ) : $m->Username );
			
			$cr = new dbObject( 'SBookCategoryRelation' );
			$cr->ObjectType = 'Users';
			$cr->ObjectID = $m->UserID;
			$cr->CategoryID = $_POST['groupid'];
			if ( $_POST['groupid'] > 0 && $cr->Load() && $gcheck && $gcheck != 'Group' )
			{
				$str .= '<tr>';
				/*$str .= '<td style="width:35px;"><div class="image">';
				$i = new dbImage ();
				if( $i->load( $m->ImageID ) )
				{
					$str .= $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
				}*/
				
				$img = '';
				
				if ( $m->ImageID > 0 && $m->Filename )
				{
					$img .= ' style="width:30px;height:28px;background-size:cover;background-repeat:no-repeat;background-position:center center;background-image: url(\'' . ( BASE_URL . 'secure-files/images/' . ( $m->ImageUniqueID ? $m->ImageUniqueID : $m->ImageID ) . '/' ) . '\')"';
				}
				else
				{
					$img .= ' style="width:30px;height:28px;background-size:cover;background-repeat:no-repeat;background-position:center center;background-image: url(\'admin/gfx/arenaicons/user_johndoe_128.png\')"';
				}			
				
				$str .= '<td style="width:35px;"><div class="image"' . $img . '>';
				
				$str .= '</div></td>';
				$str .= '<td><div>' . $m->Username . '<br/><span>' . i18n( 'already a member' ) . '</span></div></td>';
				//$str .= '<td><div>' . $m->Username . '<br/><span>already a member</span></div></td>';
				$str .= '</tr>';
			}
			else
			{
				$str .= '<tr onclick="selectMember( \'' . $m->UserID . '\', \'' . $m->Username . '\' )">';
				/*$str .= '<td style="width:35px;"><div class="image">';
				$i = new dbImage ();
				if( $i->load( $m->ImageID ) )
				{
					$str .= $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
				}*/
				
				$img = '';
				
				if ( $m->ImageID > 0 && $m->Filename )
				{
					$img .= ' style="width:30px;height:28px;background-size:cover;background-repeat:no-repeat;background-position:center center;background-image: url(\'' . ( BASE_URL . 'secure-files/images/' . ( $m->ImageUniqueID ? $m->ImageUniqueID : $m->ImageID ) . '/' ) . '\')"';
				}
				else
				{
					$img .= ' style="width:30px;height:28px;background-size:cover;background-repeat:no-repeat;background-position:center center;background-image: url(\'admin/gfx/arenaicons/user_johndoe_128.png\')"';
				}			
				
				$str .= '<td style="width:35px;"><div class="image"' . $img . '>';
				
				$str .= '</div></td>';
				$str .= '<td><div>' . $m->Username . '</div></td>';
				//$str .= '<td><div>' . $m->Username . '</div></td>';
				$str .= '</tr>';
			}
		}
		$str .= '</table></div>';
	}
	
	if( isset( $_REQUEST[ 'bajaxrand' ] ) )
	{
		if( $fm ) die ( 'ok<!--separate-->' . $str ); else die ( 'fail' );
	}
}

?>
