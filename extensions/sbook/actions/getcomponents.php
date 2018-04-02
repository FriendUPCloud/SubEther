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

// Get activated component list
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

?>
