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

// First include module
$mpath = 'subether/modules/' . $conf->Type;
if( file_exists( $mpath ) && is_dir( $mpath ) && file_exists( $mpath . '/websnippet.php' ) )
{
	include( $mpath . '/websnippet.php' );

	$componentStr = '';

	// Render components in their own fields
	if( count( $components ) >= 1 && trim( $components[0] ) )
	{
		foreach ( $components as $componentFilename )
		{
			if( $conf->Type == 'main' )
			{
				$path = 'subether/components/' . $componentFilename;
				if( file_exists( $path ) && is_dir( $path ) && file_exists( $path .'/component.php' ) )
				{
					// Find ID of 
					$foundID = FindIdByRoute ( $mpath . $componentFilename . '/' );

					// Assign to template --------------------------------------
					$folder = getCategoryID( ( $foundID ? $foundID : $componentFilename ), $tmp->cuser->ID );
					$panel = FindComponentList( $module );
					//$tmp->tabs = renderTabs( $foundComponent, $tmp->folder, $module );
					$nav = $tmp->path . ( $folder ? $componentFilename . '/' . $folder->CategoryID . '/' : '' );

					// Assign to parent ----------------------------------------
					$parent->folder = $folder;
					$parent->panel = $panel;
					//$parent->tabs = $tmp->tabs;
					$parent->nav = $nav;
					
					if( file_exists( $path . '/templates/component.php' ) )
						$Component = new cPTemplate( $path . '/templates/component.php' );
					else $Component = new cPTemplate();
					include( $path .'/component.php' );
					$componentStr .= $Component->render ();
				}
			}
		}
	}

	// Render it
	$extension .= $componentStr;
	unset( $componentStr );
}

?>
