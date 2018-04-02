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

function FindModuleByRoute ()
{
	if ( $dir = opendir ( 'subether/modules' ) )
	{
		while ( $file = readdir ( $dir ) )
		{
			if ( $file{0} == '.' ) continue;
			if ( preg_match ( '/.*?\/(' . $file . ')/', $_REQUEST['route'], $matches ) )
			{
				closedir ( $dir );
				return $file;
			}
		}
		closedir ( $dir );
	}
	return false;
}

function GetModuleParentObject ( $module )
{
	global $document, $webuser, $page;
	
	if ( $module == 'main' )
	{
		// Parent object
		$url = explode( '/', strtolower( $_REQUEST['route'] ) );
		$parent = new stdClass();
		$parent->module = 'main';
		$parent->mbase = 'subether/modules/main';
		$parent->mode = $url[4];
		$parent->switch = switchCalendarMode ( false, $_REQUEST['r'] );
		$parent->folder = getCategoryID( 'Wall' );
		$parent->path = 'en/home/';
		$parent->mpath = 'en/home/';
		$parent->route = $_REQUEST['route'];
		$parent->url = $url;
		$parent->panel = array ( 'favorites', 'groups', 'contacts', 'pages' );
		$parent->curl = getUrl();

		// If we have userinfo that's valid, render the module -----------------
		$cuser = setupUserData ( $webuser );
		$user = setupUserData ();
		$parent->cuser = $cuser;
		$parent->webuser = $user;
		
		// Done
		return $parent;
	}
	return false;
}

function FindModuleByName ( $nam )
{
	if ( $dir = opendir ( 'subether/modules' ) )
	{
		while ( $file = readdir ( $dir ) )
		{
			if ( $file{0} == '.' ) continue;
			if ( $file == $nam )
			{
				closedir ( $dir );
				return $file;
			}
		}
		closedir ( $dir );
	}
	return false;
}

// Render component
function RenderModuleTemplate ( $fname, &$parent = false )
{
	if( !$fname ) return false;
	else if( is_array( $fname ) )
	{
		foreach( $fname as $k=>$v )
		{
			$fname = $v;
			if( $k ) $tmp = $k;
		}
	}
	
	if ( file_exists ( ( $f = 'subether/module/' . $fname ) ) )
	{
		// File based module
		$Module = new cPTemplate ( $f . '/templates/' . ( $tmp ? $tmp . '.php' : 'module.php' ) );
		$Module->parent =& $parent;
		// Assign subpage to module
		if( $parent->tabs )
		{
			$i = 0;
			foreach( $parent->tabs as $key=>$val )
			{
				if( $i == 0 && !$parent->mode && file_exists ( $f . '/templates/' . $key . '.php' ) )
				{
					$parent->mode = $key;
					$Subpage = new cPTemplate ( $f . '/templates/' . $key . '.php' );
				}
				else if ( $key == $parent->mode && file_exists ( $f . '/templates/' . $key . '.php'  ) )
				{
					$Subpage = new cPTemplate ( $f . '/templates/' . $key . '.php' );
				}
				$i++;
			}
			if( $Subpage ) 
			{
				$Subpage->parent =& $parent;
				$Module->subpage = $Subpage->render();
			}
		}
		return $Module->Render ();
	}
	return false;
}

$moduleCache = array();
$moduleCacheSet = false;
function ModulePrefetcher()
{
	global $moduleCache, $moduleCacheSet, $database;
	
	if( $moduleCacheSet )
	{
		return;
	}
	
	$type = ( UserAgent() == 'web' ? 'global' : 'mobile' );
	
	if( $rows = $database->fetchObjectRows( '
		SELECT * 
		FROM SModules 
		WHERE Type = "' . $type . '" AND Visible != "0" 
		ORDER BY SortOrder ASC, Name ASC 
	', false, 'functions/modulefuncs.php' ) )
	{
		foreach( $rows as $row )
		{
			if( !isset( $moduleCache['_all_'] ) )
			{
				$moduleCache['_all_'] = array();
			}
			if( !isset( $moduleCache[$row->Name] ) )
			{
				$moduleCache[$row->Name] = array();
			}
			
			$moduleCache['_all_'][] = $row;
			
			if( trim( $row->Name ) )
			{
				$moduleCache[$row->Name][] = $row;
			}
		}
	}
	
	$moduleCacheSet = true;
}

function ModuleExists ( $module, $position = false )
{
	global $webuser, $database, $moduleCache;
	
	ModulePrefetcher();
	
	$rootpath = 'subether/modules';
	
	if( !$module ) return false;
	$module = trim( strtolower( $module ) );
	
	if( $webuser && IsSystemAdmin() )
	{
		return true;
	}
	
	if( isset( $moduleCache[$module] ) )
	{
		$rows = $moduleCache[$module];
		
		if( $position )
		{
			$found = false;
			
			foreach( $rows as $row )
			{
				if( $row->Position == $position )
				{
					$found = true;
				}
			}
			
			if( !$found )
			{
				return false;
			}
		}
	}
	
	/*if( !$database->fetchObjectRows( '
		SELECT * 
		FROM SModules 
		WHERE Name = \'' . $module . '\' AND Type = "global" AND Visible != "0" 
		' . ( $position ? 'AND Position = \'' . $position . '\' ' : '' ) . ' 
		ORDER BY SortOrder ASC, Name ASC 
	', false, 'functions/modulefuncs.php' ) )
	{
		return false;
	}*/
	
	if( !file_exists( $rootpath . '/' . $module ) )
	{
		return false;	
	}
	
	return true;
}

?>
