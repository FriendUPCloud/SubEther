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

if( $rows = $database->fetchObjectRows ( '
    SELECT 
        g2.* 
    FROM 
        SBookCategory g, SBookCategory g2 
    WHERE
        g.CategoryID = "0"
        AND g.Type = "Group"
        AND g.Name = "Groups"
        AND g2.CategoryID = g.ID
        AND g2.Type = "SubGroup"
        AND g2.Privacy != "SecretGroup" 
        ' . ( isset( $_REQUEST[ 'q' ] ) ? 'AND g2.Name LIKE "%' . str_replace( ' ', '+', $_REQUEST[ 'q' ] ) . '%"' : '' ) . '
    ORDER BY 
        g2.ID DESC 
' ) )
{
    $groups = ContactGroups( $parent->webuser->ContactID );
    
    $td = 0;
    $mstr .= '<div class="groups">';
    
	foreach( $rows as $r )
    {
        // --- Group button --- //
		
		$btn = '';
		
		if( isset( $groups[$parent->webuser->ContactID][$r->ID] ) )
		{
			//$btn .= '<button onclick="profileOptions()">';
			$btn .= '<button>';
            $btn .= '<span>' . i18n( 'Member' ) . '</span>';
			$btn .= '</button>';
		}
		else if( $webuser->ID > 0 && ( $r->Privacy != 'ClosedGroup' || isset( $parent->access->IsSystemAdmin ) ) )
		{
			$btn .= '<button onclick="joinGroup( \'' . $r->ID . '\' )">';
			$btn .= '<span>+ ' . i18n( 'Join Group' ) . '</span>';
			$btn .= '</button>';
		}
        
        // --- Group info --- //
        
        if( $r->NodeID == 0 || ( $r->NodeID > 0 && IsSystemAdmin() ) )
        {
            $td++;
            $mstr .= '<div class="group"><table><tr>';
			$mstr .= '<td><div class="image"><a href="groups/' . $r->ID . '/">';
			
            if( $folder = $database->fetchObjectRow ( '
                SELECT * FROM SBookMediaRelation
                WHERE CategoryID = \'' . $r->ID . '\' AND UserID = "0"
                AND Name = "Cover Photos" AND MediaType = "Folder"
            ' ) )
            {
				// TODO: Fix this ...
                $i = new dbImage ();
                $i->ImageFolder = $folder->MediaID;
                if( $i->load() && $i->ID > 0 )
                {
                    $mstr .= $i->getImageHTML ( 100, 100, 'framed', false, 0xffffff );
                }
            }
			else
			{
				$mstr .= '<div style="width:100%;height:100%;background-image:url(\'subether/gfx/img_placeholder.png\');background-position:center center;background-size:cover;background-repeat:no-repeat;"></div>';
			}
			
            $mstr .= '</a></div></td><td>';
            $mstr .= '<div><a title="' . htmlentities( $r->Name ) . '" href="groups/' . $r->ID . '/">' . dot_trim( htmlentities( $r->Name ), 25 ) . '</a></div>';
            $mstr .= '<div>' . i18n( $r->Privacy ) . '</div>';
            
			if( $m = getSBookGroupMembers( $r->ID ) )
            {
                $mstr .= '<div>' . count( $m ) . ' ' . i18n( 'Members' ) . '</div>';
            }
            
			$mstr .= '<div>' . dot_trim( strip_tags( $r->Description ), 40 ) . '</div>';
            
			if( $r->NodeID == 0 )
            {
                $mstr .= '<div>' . $btn . '</div>';
            }
            
			$mstr .= '</td></tr></table></div>';
        }
    }
	
	$mstr .= '<div class="clearboth" style="clear:both"></div>';
    $mstr .= '</div>';
}

?>
