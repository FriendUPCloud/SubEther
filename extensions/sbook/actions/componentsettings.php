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

$f = new dbObject( 'ContentDataSmall' );
$f->load( $_REQUEST['fid'] );

$conf = CreateObjectFromString( $f->DataMixed );

if( isset( $_REQUEST['component'] ) && trim( $_REQUEST['component'] ) != '' )
{
	$cmp = $_REQUEST['component'];
	$cmp = str_replace( array( '..', '/' ), '', $cmp );

	$component_template = '';

	$activated = false;
	if( $components = explode( ',', $conf->Components ) )
	{
		foreach ( $components as $comp )
		{
			if( $comp == $_REQUEST['component'] )
				$activated = true;
		}
	}

	if( file_exists( 'subether/components/' . $cmp . '/component_setting.php' ) )
	{
		include( 'subether/components/' . $cmp . '/component_setting.php' );
	}
	else
	{
		$component_template = new cPTemplate( 'extensions/sbook/templates/component_setting.php' );
		$cp =& $component_template;
		
		$cp->activated = $activated;
		$cp->field = $f;
		$cp->component = ucfirst( $_REQUEST['component'] );
		
		// Get specific component settings
		$compConf = new stdClass();
		if( isset( $conf->{"Component_{$_REQUEST['component']}"} ) )
		{
			$compConf = $conf->{"Component_{$_REQUEST['component']}"};
			$compConf = CreateObjectFromString( 
				str_replace( array ( '<!--tab-->', '<!--nl-->' ), array( "\t", "\n" ), $compConf ) 
			);
		}
		
		// Get activated subcomponent list
		/*
		if( !isset( $conf->Components ) || !trim( $conf->Components ) || !( $components = explode( ',', $conf->Components ) ) )
		{
			$components = array();
		}
		else
		{
			$out = array();
			foreach ( $components as $comps )
			{
				$out[$comps] = true;
			}
			$components = $out;
			unset( $out );
		}

		$st = '<option value=""' . ( !count( $components ) ? ' selected="selected"' : '' ) . '>Default</option>';
		if( $dir = opendir( 'subether/components' ) )
		{
	
			while( $fl = readdir( $dir ) )
			{
				if( $fl{0} == '.' ) continue;
				if( is_dir( 'subether/components/' . $fl ) )
				{
					$set = isset( $components[$fl] ) ? ' active="active" style="background: #999999; color: white"' : '';
					$st .= '<option value="' . $fl . '"' . $set . '>' . ucfirst( $fl ) . '</option>';
				}
			}
			closedir( $dir );
		}
		die( 'ok<!--separate--><select id="components_' . $f->ID . '" size="10" style="min-width: 140px" multiple="multiple" onclick="loadComponentSettings' . $f->ID . '()">' . $st . '</select>' );
		*/
		
	}

	// Get the output
	// TODO: Check if object has render method
	die( 'ok<!--separate-->' . ( is_object( $component_template ) ? $component_template->render() : $component_template ) );
}
die( 'ok<!--separate-->Default has no settings...' );

?>
