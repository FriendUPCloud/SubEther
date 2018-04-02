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

// TODO: Check why multiply render ...
// Prevent component of being rendered multiple times
if ( !isset ( $GLOBALS['renderedComponent'] ) )
{
	$GLOBALS['renderedComponent'] = array ();
}

// Render component
function RenderComponent ( $cname, $parent = false, $pname = false, $tabs = false, $type = false )
{
	$rootpath = 'subether/components';
	
	$rc =& $GLOBALS['renderedComponent'];
	
	$rckey = $cname.$parent->position;
	if ( !isset ( $rc[$rckey] ) )
	{
		if( !trim( $cname ) || !ComponentExists( $cname, $parent->module, $parent->position, ( $type && $type == 'SubComponent' ) ? $pname : '' ) )
		{
			return false;
		}
		
		$i = 0; $t = ''; $SubComponent = '';
		
		if( $parent && $tabs )
		{
			foreach( $tabs as $key=>$val )
			{
				if( !$parent->mode && $i == 0 )
				{
					$SubComponent = $parent->mode = $key;
					break;
				}
				else if( $parent->mode == $key )
				{
					$SubComponent = $key;
					break;
				}
				$i++;
			}
			
			
		}
		
		// Class based component from base folder ---------------------------------------------------------
		if ( file_exists ( ( $f = $rootpath . '/' . $cname ) . '/component.class.php' ) )
		{
			include_once ( $f . '/component.class.php' );
			$Component = new $cname();
		}
		// File based component from templates folder -----------------------------------------------------
		else if( $pname && file_exists ( ( $f = $rootpath . '/' . $pname ) . '/templates/' . $cname . '.php' ) )
		{
			$Component = new cPTemplate ( $f . '/templates/' . $cname . '.php' );
			$Component->parent = $parent;
			
			if( $SubComponent )
			{
				/* --- Assign sub component to this component --- */
				$Component->subpage = RenderComponent( $SubComponent, $parent, $pname, false, 'SubComponent' );
				$Component->tab = $SubComponent;
				$Component->count = ( $Component->count ? ( $Component->count + 1 ) : 1 );
			}
			
			$Component->count = ( $Component->count ? ( $Component->count + 1 ) : 1 );
			
			include ( $f . '/component.php' );
		}
		// File based component from template folder if mobile version -------------------------------------
		else if( ( UserAgent() != 'web' && UserAgent() != 'tablet' ) && file_exists ( ( $f = $rootpath . '/' . $cname ) . '/templates/mobile.php' ) )
		{
			$Component = new cPTemplate ( $f . '/templates/mobile.php' );
			$Component->parent = $parent;
			
			if( $SubComponent )
			{
				/* --- Assign sub component to this component --- */
				$Component->subpage = RenderComponent( $SubComponent, $parent, $pname, false, 'SubComponent' );
				$Component->tab = $SubComponent;
				$Component->count = ( $Component->count ? ( $Component->count + 1 ) : 1 );
			}
			
			$Component->count = ( $Component->count ? ( $Component->count + 1 ) : 1 );
			
			include ( $f . '/component.php' );
		}
		// File based component from base folder ----------------------------------------------------------
		else if( file_exists ( ( $f = $rootpath . '/' . $cname ) ) )
		{
			$Component = new cPTemplate ( $f . '/templates/component.php' );
			$Component->parent = $parent;
			
			if( $SubComponent )
			{
				/* --- Assign sub component to this component --- */
				$Component->subpage = RenderComponent( $SubComponent, $parent, $pname, false, 'SubComponent' );
				$Component->tab = $SubComponent;
				$Component->count = ( $Component->count ? ( $Component->count + 1 ) : 1 );
			}
			
			$Component->count = ( $Component->count ? ( $Component->count + 1 ) : 1 );
			
			//die( print_r( $Component,1 ) . ' --' );
			
			include ( $f . '/component.php' );
		}
		
		// Output -----------------------------------------------------------------------------------------
		if( !isset( $rc[$rckey] ) )
		{
			$rc[$rckey] = new stdClass();
		}
		if( $Component )
		{
			$rc[$rckey]->data = $Component->Render ();
			$rc[$rckey]->position = $parent->position;
		}
		else $rc[$rckey]->data = false;
	}
	// Return cached content
	return $rc[$rckey]->data;
}



function IncludeComponent ( $_com_, $parent, $_sub_ = false )
{
	$_rootpath_  = 'subether/components';
	$_rootpath2_ = 'subether/applications';
	
	// Check if component and parent object is defined or if the component exists and you have access
	if( ( !$_com_ && !$parent ) || ( !$_sub_ && !ComponentExists( $_com_, $parent->module, $parent->position ) ) || ( !$_sub_ && !ComponentAccess( $_com_, $parent->module, $parent->position ) ) )
	{
		return false;
	}
	
	if( !isset( $parent->rendered ) )
	{
		$parent->rendered = new stdClass();
		$parent->rendered->Components = array();
	}
	
	$parent->rendered->Components[] = $_com_;
	
	if( $_sub_ )
	{
		$parent->tabs = renderTabs( $_com_, $parent->folder, $parent->module, $parent->access );
	}
	
	$_i_ = 0;
	
	if( $parent->tabs )
	{
		foreach( $parent->tabs as $_key_=>$_val_ )
		{
			if( ( !$parent->mode || ( $parent->mode && !isset( $parent->tabs[$parent->mode] ) ) ) && $_i_ == 0 )
			{
				$parent->mode = $_key_;
				break;
			}
			$_i_++;
		}
	}
	
	// Get component based on template name
	if( $parent->module && file_exists ( ( $_temp_ = $_rootpath_ . '/' . $parent->module ) . '/templates/' . $_com_ . '.php' ) )
	{
		$Component = new cPTemplate ( $_temp_ . '/templates/' . $_com_ . '.php' );
		$Component->parent = $parent;
		
		include ( $_temp_ . '/component.php' );
		
		return $Component->Render ();
	}
	// Get component for mobile mode
	else if ( ( UserAgent() != 'web' && UserAgent() != 'tablet' ) && file_exists ( ( $_temp_ = $_rootpath_ . '/' . $_com_ ) . '/templates/mobile.php' ) )
	{
		$Component = new cPTemplate ( $_temp_ . '/templates/mobile.php' );
		$Component->parent = $parent;
		
		include ( $_temp_ . '/component.php' );
		
		return $Component->Render ();
	}
	// Get applications for desktop mode
	else if ( file_exists ( ( $_temp_ = $_rootpath2_ . '/' . $_com_ ) ) )
	{
		$Component = new cPTemplate ( $_temp_ . '/client.html' );
		//die( $Component->Render () );
		return $Component->Render ();
	}
	// Get component for desktop mode
	else if ( file_exists ( ( $_temp_ = $_rootpath_ . '/' . $_com_ ) ) )
	{
		$Component = new cPTemplate ( $_temp_ . '/templates/component.php' );
		$Component->parent = $parent;
		
		include ( $_temp_ . '/component.php' );
		
		return $Component->Render ();
	}
	
	return false;
}


function GetComponentData( $component )
{
	// TODO: whatever info needs to be listen on each component here
}


// Get component with ajax request
function GetComponentByRequest ( $request, $parent = false )
{
	$rootpath  = 'subether/components';
	$rootpath2 = 'subether/applications';
	
	if( isset( $request ) && file_exists ( $f = ( $rootpath2 . '/' . $request ) ) )
	{
		$Component = new cPTemplate ( $f . '/template/index.html' );
		return $Component->Render ();
	}
	else if( isset( $request ) && file_exists ( $f = ( $rootpath . '/' . $request ) ) )
	{
		$Component = new cPTemplate ( $f . '/templates/component.php' );
		$Component->parent = $parent;
		include ( $f . '/component.php' );
		return $Component->Render ();
	}
	return false;
}

function JaxQueueComponent ( $request, $parent = false )
{	
	$rootpath = 'subether/components'; $GLOBALS['jaxqueue'] = 1;
	
	if( isset( $request ) && file_exists ( $f = ( $rootpath . '/' . $request ) ) )
	{
		include ( $f . '/component.php' );
		return $GLOBALS['jaxqueue'];
	}
}

$componentCache = array();
$componentCacheSet = false;
function ComponentPrefetcher()
{
	global $componentCache, $componentCacheSet, $database;
	
	if( $componentCacheSet )
	{
		return;
	}
	
	$type = ( UserAgent() == 'web' ? 'global' : 'mobile' );
	
	if( $rows = $database->fetchObjectRows( $q = '
		SELECT
			c.*,
			c.UserLevels AS ComponentAccess,
			m.UserLevels AS ModuleAccess, 
			m.IsMain,
			m.Visible,
			m.Name AS ModuleName
		FROM
			SModules m,
			SComponents c 
		WHERE
			    m.Type = "' . $type . '"
			AND c.Type = m.Type
			AND m.Visible != "0" 
			AND c.Position = m.Position
			AND c.Module = m.Name
		ORDER BY
			c.SortOrder ASC,
			c.Position ASC
	', false, 'functions/componentfuncs.php' ) )
	{
		//die( print_r( $rows,1 ) . ' -- ' . $q );
		foreach( $rows as $row )
		{
			if( !isset( $componentCache[$row->ModuleName] ) )
			{
				$componentCache[$row->ModuleName] = array();
			}
			if( !isset( $componentCache[$row->ModuleName]['_all_'] ) )
			{
				$componentCache[$row->ModuleName]['_all_'] = array();
			}
			if( trim( $row->Name ) && !isset( $componentCache[$row->ModuleName][$row->Name] ) )
			{
				$componentCache[$row->ModuleName][$row->Name] = array();
			}
			
			$componentCache[$row->ModuleName]['_all_'][] = $row;
			
			if( trim( $row->Name ) )
			{
				$componentCache[$row->ModuleName][$row->Name][] = $row;
			}
		}
		
		if( $tabs = $database->fetchObjectRows( '
			SELECT * 
			FROM STabs 
			WHERE Type = "' . $type . '" 
			ORDER BY SortOrder ASC, Tab ASC 
		', false, 'functions/componentfuncs.php' ) )
		{
			foreach( $tabs as $tab )
			{
				if( isset( $componentCache[$tab->Module][$tab->Component] ) )
				{
					if( !isset( $componentCache[$tab->Module][$tab->Component]['_tabs_'] ) )
					{
						$componentCache[$tab->Module][$tab->Component]['_tabs_'] = array();
					}
					if( !isset( $componentCache[$tab->Module][$tab->Component]['_tabs_']['_all_'] ) )
					{
						$componentCache[$tab->Module][$tab->Component]['_tabs_']['_all_'] = array();
					}
					if( trim( $tab->Tab ) && !isset( $componentCache[$tab->Module][$tab->Component]['_tabs_'][$tab->Tab] ) )
					{
						$componentCache[$tab->Module][$tab->Component]['_tabs_'][$tab->Tab] = array();
					}
					
					$componentCache[$tab->Module][$tab->Component]['_tabs_']['_all_'][] = $tab;
					
					if( trim( $tab->Tab ) )
					{
						$componentCache[$tab->Module][$tab->Component]['_tabs_'][$tab->Tab][] = $tab;
					}
				}
			}
		}
	}
	
	$componentCacheSet = true;
}

function ListComponentPosition ( $module, $component = false )
{
	global $database, $webuser, $componentCache;
	
	if( !$module ) return false;
	
	ComponentPrefetcher();
	
	$access = array( ',0,' );
	
	if( $webuser->ID > 0 )
	{
		$access[] = ',99,1,';
		
		if( IsSystemAdmin() )
		{
			$access[] = ',99,';
		}
	}
	
	/*$type = ( UserAgent() == 'web' ? 'global' : 'mobile' );
	
	$q = '
		SELECT
			c.*,
			c.UserLevels AS ComponentAccess,
			m.UserLevels AS ModuleAccess, 
			m.IsMain 
		FROM
			SModules m,
			SComponents c 
		WHERE
			m.Name = \'' . $module . '\'
			AND m.Type = "' . $type . '"
			AND m.Visible != "0" 
			AND c.Position = m.Position
			AND c.Module = m.Name
			AND c.Type = "' . $type . '" 
			' . ( $component ? 'AND ( m.IsMain = "0" OR ( m.IsMain = "1"
			AND c.Name = \'' . $component . '\' ) ) ' : '' ) . '
		ORDER BY
			c.SortOrder ASC,
			c.Position ASC 
	';*/
	
	// Try cache
	$rows = false;
	if( $component && isset( $componentCache[$module][$component] ) )
		$rows = $componentCache[$module][$component];
	else if( !$component && isset( $componentCache[$module]['_all_'] ) )
		$rows = $componentCache[$module]['_all_'];
	
	// Fetch implicit components
	if( $component && isset( $componentCache[$module]['_all_'] ) )
	{
		foreach( $componentCache[$module]['_all_'] as $comp )
		{
			if( $comp->IsMain == '0' )
				$rows[] = $comp;
		}
	}
	
	if( $rows ) //= $database->fetchObjectRows( $q, false, 'functions/componentfuncs.php' ) )
	{
		$ids = array(); $out = array(); $i = 0;
		
		foreach( $rows as $k=>$row )
		{
			if( in_array( $row->ID, $ids ) || !in_array( $row->ModuleAccess, $access ) || !in_array( $row->ComponentAccess, $access ) )
			{
				unset( $rows[$k] );
				continue;
			}
			
			$s = ( ( $row->SortOrder > 0 ? $row->SortOrder : '' ) . $i++ );
			$out[$s] = $row;
			
			$ids[$row->ID] = $row->ID;
		}
		
		ksort( $out );
		
		return $out ? $out : false;
	}
	return false;
}

function ComponentAccess( $component, $module = false, $position = false )
{
	global $database, $webuser, $componentCache;
	
	if( !$component ) return false;
	
	ComponentPrefetcher();
	
	$access = array( ',0,' );
	
	if( $webuser->ID > 0 )
	{
		$access[] = ',99,1,';
		
		if( IsSystemAdmin() )
		{
			$access[] = ',99,';
		}
	}
	else if( !in_array( $component, array( 'authentication', 'register' ) ) && $database->fetchObjectRow( 'SELECT * FROM SNodes WHERE IsMain = "1" AND Open = "-1"' ) )
	{
		return false;
	}
	
	/*$type = ( UserAgent() == 'web' ? 'global' : 'mobile' );
	
	$q = '
		SELECT 
			c.*, 
			c.UserLevels AS ComponentAccess, 
			m.UserLevels AS ModuleAccess, 
			m.IsMain 
		FROM 
			SModules m, 
			SComponents c 
		WHERE 
			c.Name = \'' . $component . '\' 
			AND c.Type = "' . $type . '" 
			AND m.Position = c.Position 
			AND m.Type = "' . $type . '" 
			AND m.Visible != "0" 
			' . ( $module ? 'AND c.Module = \'' . $module . '\' AND m.Name = \'' . $module . '\' ' : '' ) . '
		ORDER BY 
			c.SortOrder ASC, 
			c.Position ASC 
	';*/
	
	// Try cache
	$rows = false;
	if( $component && isset( $componentCache[$module][$component] ) )
		$rows = $componentCache[$module][$component];
	else if( !$component && isset( $componentCache[$module]['_all_'] ) )
		$rows = $componentCache[$module]['_all_'];
	
	if( $rows ) //= $database->fetchObjectRows( $q, false, 'functions/componentfuncs.php' ) )
	{
		foreach( $rows as $row )
		{
			if( in_array( $row->ModuleAccess, $access ) && in_array( $row->ComponentAccess, $access ) )
			{
				if ( !$position || ( $row->Position == $position ) )
				{
					return $row;
				}
			}
		}
	}
	return false;
}

function FindComponentPosition ( $pos, $component, $module )
{
	if( !$pos || !$component || !$module ) return false;

	$pos = strtolower( trim( $pos ) );

	switch( $module )
	{
		case 'main':
			switch( $component )
			{
				case 'wall':
					$a = array(
						'left'=>array( 'panel' ),
						'middle'=>array( 'wall' )
					);
					return $a[$pos];
				case 'messages':
					$a = array(
						'left'=>array( 'panel' ),
						'middle'=>array( 'messages' )
					);
					return $a[$pos];
				case 'events':
					$a = array(
						'left'=>array( 'panel' ),
						'middle'=>array( 'events' )
					);
					return $a[$pos];
				case 'groups':
					$a = array(
						'left'=>array( 'panel' ),
						'middle'=>array( 'groups' )
					);
					return $a[$pos];
				default:
					$a = array(
						'left'=>array( 'panel' ),
						'middle'=>array( $component )
					);
					return $a[$pos];
			}
			break;
		case 'profile':
			switch( $component )
			{
				case 'profile':
					$a = array(
						'left'=>array( 'panel', 'contacts' ),
						'middle'=>array( 'wall' ),
						'right'=>array( 'events' )
					);
					return $a[$pos];
				case 'library':
					$a = array(
						'left'=>array( 'panel', 'contacts' ),
						'middle'=>array( 'library' )
					);
					return $a[$pos];
				default:
					$a = array(
						'left'=>array( 'panel', 'contacts' ),
						'middle'=>array( $component )
					);
					return $a[$pos];
			}
			break;
		case 'account':
			switch( $component )
			{
				case 'account':
					$a = array(
						'left'=>array( 'panel' ),
						'middle'=>array( 'account' )
					);
					return $a[$pos];
				default:
					$a = array(
						'left'=>array( 'panel' ),
						'middle'=>array( $component )
					);
					return $a[$pos];
			}
			break;
		case 'global':
			switch( $component )
			{
				case 'global':
					$a = array(
						'left'=>array( 'panel' ),
						'middle'=>array( 'global' )
					);
					return $a[$pos];
				default:
					$a = array(
						'left'=>array( 'panel' ),
						'middle'=>array( $component )
					);
					return $a[$pos];
			}
			break;
	}

	return false;
}

function FindComponentList ( $module )
{
	if( !$module ) return false;

	$module = strtolower( trim( $module ) );

	switch( $module )
	{
		case 'main':
			return array( 'favorites', 'groups', 'contacts', 'pages', 'bank' );
			break;
		case 'profile':
			return array( 'profile' );
			break;
	}
}

// Finds a component by url route
function FindComponentByRoute ( $module = false, $reverse = false )
{
	//global $Session;
	//$str = str_replace( $path, '', $_REQUEST['route'] );
	//$str = $_REQUEST['route'];
	//if ( !preg_match ( '/([a-z]*?)\//i', $str, $m ) )
	//{
	//	return false;
	//}
	//$component = $m[1];
	//die( $_REQUEST['route'] . ' -- ' . $path . ' -- ' . $m[1] . ' .. ' . $str );
	
	$url = explode( '/', $_REQUEST['route'] );
	
	//die( print_r( $url,1 ) . ' --' );
	
	if ( $url && is_array( $url ) )
	{
		if ( $reverse )
		{
			$url = array_reverse ( $url );
		}
		
		if ( file_exists( 'subether/components' ) && ( $dir = opendir ( 'subether/components' ) ) )
		{
			$files = array();
			
			while ( $file = readdir ( $dir ) )
			{
				if ( $file{0} == '.' ) continue;
				
				$files[] = strtolower( trim( $file ) );
			}
			
			closedir ( $dir );
			
			if ( $files )
			{
				foreach ( $url as $u )
				{
					if ( $reverse && trim( $u ) != '' && is_numeric( $u ) )
					{
						return false;
					}
					
					if ( trim( $u ) != '' && !is_numeric( $u ) )
					{
						$vars = explode( '?', $u );
						
						if ( $vars[0] && !strstr( $vars[0], '?' ) )
						{
							if ( in_array( strtolower( trim( $vars[0] ) ), $files ) )
							{
								return strtolower( trim( $vars[0] ) );
							}
						}
					}
				}
			}
		}
		
		if ( file_exists( 'subether/applications' ) && ( $dir = opendir ( 'subether/applications' ) ) )
		{
			$files = array();
			
			while ( $file = readdir ( $dir ) )
			{
				if ( $file{0} == '.' ) continue;
				
				$files[] = strtolower( trim( $file ) );
			}
			
			closedir ( $dir );
			
			if ( $files )
			{
				foreach ( $url as $u )
				{
					if ( $reverse && trim( $u ) != '' && is_numeric( $u ) )
					{
						return false;
					}
					
					if ( trim( $u ) != '' && !is_numeric( $u ) )
					{
						$vars = explode( '?', $u );
						
						if ( $vars[0] && !strstr( $vars[0], '?' ) )
						{
							if ( in_array( strtolower( trim( $vars[0] ) ), $files ) )
							{
								return strtolower( trim( $vars[0] ) );
							}
						}
					}
				}
			}
		}
		
	}
	return false;
}

function ComponentExists ( $component, $module = false, $position = false, $parent = false )
{
	global $webuser, $database, $componentCache;
	
	ComponentPrefetcher();
	
	$rootpath  = 'subether/components';
	$rootpath2 = 'subether/applications';
	
	if( !$component ) return false;
	
	$component = trim( strtolower( $component ) );
	if( $module ) $module = trim( strtolower( $module ) );
	if( $parent ) $parent = trim( strtolower( $parent ) );
	
	if( $webuser && IsSystemAdmin() )
	{
		return true;
	}
	
	// If we are looking for tabs and it can't be found return false
	if( $parent && !isset( $componentCache[$module][$parent]['_tabs_'][$component] ) )
	{
		return false;
	}
	// Else if we are looking for a component and position is defined and we can't find it return false
	else if( !$parent && isset( $componentCache[$module][$component] ) )
	{
		$rows = $componentCache[$module][$component];
		
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
	// Else if we can't find the module and component at all return false
	else if( !$parent && !isset( $componentCache[$module][$component] ) )
	{
		return false;
	}
	
	/*$type = ( UserAgent() == 'web' ? 'global' : 'mobile' );
	
	if( $parent && !$database->fetchObjectRows( '
		SELECT * 
		FROM STabs 
		WHERE Tab = \'' . $component . '\' AND Component = \'' . $parent . '\' AND Type = "global" 
		' . ( $module ? 'AND Module = \'' . $module . '\' ' : '' ) . ' 
		ORDER BY SortOrder ASC, Tab ASC 
	', false, 'functions/componentfuncs.php' ) )
	{
		return false;
	}
	else if( !$parent && !$database->fetchObjectRows( '
		SELECT * 
		FROM SComponents 
		WHERE Name = \'' . $component . '\' AND Type = "' . $type . '" 
		' . ( $module ? 'AND Module = \'' . $module . '\' ' : '' ) . ' 
		' . ( $position ? 'AND Position = \'' . $position . '\' ' : '' ) . ' 
		ORDER BY SortOrder ASC, Name ASC 
	', false, 'functions/componentfuncs.php' ) )
	{
		return false;
	}*/
	
	if( !file_exists( $rootpath . '/' . $component ) && !file_exists( $rootpath2 . '/' . $component ) )
	{
		return false;	
	}
	
	return true;
}

function ListComponentTabs ( $component, $module, $position = false, $obj = false, $access = false )
{
	global $database, $componentCache;
	
	if( !$component || !$module ) return false;
	
	ComponentPrefetcher();
	
	$rows = false;
	
	if( isset( $componentCache[$module][$component]['_tabs_']['_all_'] ) )
	{
		$rows = $componentCache[$module][$component]['_tabs_']['_all_'];
		
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
	
	/*$q = '
		SELECT * 
		FROM STabs 
		WHERE Component = \'' . $component . '\' AND Type = "global" 
		AND Module = \'' . $module . '\' 
		' . ( $position ? 'AND Position = \'' . $position . '\' ' : '' ) . ' 
		ORDER BY SortOrder ASC, Tab ASC 
	';*/
	
	if( $rows ) //= $database->fetchObjectRows( $q, false, 'functions/componentfuncs.php' ) )
	{
		$out = array(); $i = 0;
		
		foreach( $rows as $r )
		{
			$permission = explode( ',', $r->Permission );
			
			if( $obj && $permission[0] && !in_array( $obj->Permission, $permission ) && !isset( $access->IsAdmin ) )
			{
				continue;
			}
			
			if( $r->Tab == 'groups' && !isset( $obj->SubGroups ) )
			{
				continue;
			}
			
			if( $i == 0 )
			{
				$r->DisplayName = '<strong>' . ( $obj->Name != '' ? $obj->Name : $r->DisplayName ) . '</strong>';
			}
			
			$out[$r->Tab] = ( $r->DisplayName ? i18n( 'i18n_' . $r->DisplayName ) : i18n( 'i18n_' . $r->Tab ) );
			
			if( $r->Tab == 'members' && $obj->CategoryID > 0 )
			{
				if( $m = getSBookGroupMembers( $obj->CategoryID ) )
				{
					$out[$r->Tab] = i18n( 'i18n_' . $out[$r->Tab] ) . ' (' . count( $m ) . ')';
				}
			}
			$i++;
		}
		
		return $out;
	}
	return false;
}

?>
