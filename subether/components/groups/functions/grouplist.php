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

$mgroup = getCategories( 'Users', 'Group', false, false, 'Groups' );
$grouplist = getCategories( 'Users', 'SubGroup', $mgroup->ID );

/*
$str = '<div class="Box">';

$str .= '<h2>Groups you are a member of</h2><hr/>';

if( $grouplist )
{
    foreach( $grouplist as $group )
    {
        $str .= '<h3><a href="en/home/groups/' . $group->ID . '/">' . $group->Name . '</a></h3>';
        $str .= '<div class="Description">' . $group->Description . '</div>';
    }
}

$str .= '</div>';*/

//die( print_r( $GLOBALS['renderedComponent'],1 ) . ' --' );

$str = '';

if( strtolower( $parent->mode ) == 'groups' )
{
	$grouplist = false;
	
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
			AND g2.ParentID = \'' . $parent->folder->CategoryID . '\' 
		ORDER BY 
			g2.ID DESC 
	' ) )
	{
		$grouplist = $rows;
	}
}

if( $grouplist )
{
    $groups = ContactGroups( $parent->cuser->ContactID );
    
    $td = 0;
    $str .= '<div class="Box">';
	
    foreach( $grouplist as $r )
    {
        // --- Group button --- //
		
		$btn = '';
		
		if( isset( $groups[$parent->cuser->ContactID][$r->ID] ) )
		{
			//$btn .= '<button onclick="profileOptions()">';
			$btn .= '<button>';
            $btn .= '<span>' . i18n( 'Member' ) . '</span>';
			$btn .= '</button>';
		}
		else if( $webuser->ID > 0 && ( $r->Privacy != 'ClosedGroup' || isset( $parent->access->IsSystemAdmin ) ) )
		{
            if( strtolower( $parent->mode ) != 'groups' ) continue;
            
			$btn .= '<button onclick="joinGroup( \'' . $r->ID . '\' )">';
			$btn .= '<span>+ ' . i18n( 'Join Group' ) . '</span>';
			$btn .= '</button>';
		}
        
        // --- Group info --- //
        
        $td++;
        $str .= '<div class="group"><table class="group"><tr>';
		$str .= '<td><div class="image"><a href="groups/' . $r->ID . '/">';
		
        if( $folder = $database->fetchObjectRow ( '
            SELECT * FROM SBookMediaRelation
            WHERE CategoryID = \'' . $r->ID . '\' AND UserID = "0"
            AND Name = "Cover Photos" AND MediaType = "Folder"
        ' ) )
        {
            $i = new dbImage ();
            $i->ImageFolder = $folder->MediaID;
            if( $i->load() && $i->ID > 0 )
            {
                $str .= $i->getImageHTML ( 100, 100, 'framed', false, 0xffffff );
            }
        }
		else
		{
			$str .= '<img style="width:100px;height:100px;background-position:center center;background-size:cover;background-repeat:no-repeat;" src="subether/gfx/img_placeholder.png"/>';
		}
		
        $str .= '</a></div></td><td>';
        $str .= '<div><a title="' . htmlentities( $r->Name ) . '" href="groups/' . $r->ID . '/">' . dot_trim( htmlentities( $r->Name ), 25 ) . '</a></div>';
        $str .= '<div>' . i18n( $r->Privacy ) . '</div>';
        
		if( $m = getSBookGroupMembers( $r->ID ) )
        {
            $str .= '<div>' . count( $m ) . ' ' . i18n( 'Members' ) . '</div>';
        }
		
        $str .= '<div>' . dot_trim( strip_tags( $r->Description ), 40 ) . '</div>';
        $str .= '<div>' . $btn . '</div>';
        $str .= '</td></tr></table></div>';
		
    }
	
	$str .= '<div class="clearboth" style="clear:both"></div>';
    $str .= '</div>';
}

?>
