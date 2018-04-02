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

require_once( 'smodules.php' );

// 1. Check if we're not installed
$version = GetSetting( 'Treeroot', 'Version' );
if( !$version )
{
	// 2. Clear all SModules rows
	foreach( $modules as $mmode => $module )
	{
		foreach( $module as $kmode => $mode )
		{
			foreach( $mode as $moduleObject )
			{
				// 3. Insert module object into SModules
				$o = new dbObject( 'SModules' );
				foreach( $moduleObject as $k => $v )
					$o->$k = $v;
				$o->Type = $kmode;
				$o->Name = $mmode;
				$o->Save();
				$moduleObjId = $o->ID;
				// 4. Insert all SComponents rows
				foreach( $scomponents as $cmode => $component )
				{
					// 5. Underneath all SComponents, insert all STabs rows
					foreach( $stabs as $smode => $tab )
					{
						
					}
				}
			}
		}
	}

	// 6. Set setting that we've installed version X.
	SetSettingValue( 'Treeroot', 'Version', '0.9' );
}
// 7. Do upgrades based on current version
else
{
	// TODO: This is in the future
}

?>
