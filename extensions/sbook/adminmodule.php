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

// Get config file
$conf = CreateObjectFromString ( $fieldObject->DataMixed );

// Available modules
$options = array (
	'default'=>'Default',
	'main'=>'Main',
	'authentication'=>'Authentication',
	'account'=>'Account',
	'register'=>'Register',
	'profile'=>'Profile',
	'engine'=>'Engine',
	'nodes'=>'Nodes',
	'ecovillage'=>'Ecovillage',
	'university'=>'University',
	'services'=>'Services',
	'travel'=>'Travel',
	'trading'=>'Trading',
	'global'=>'Global',
	'testing'=>'Testing',
	'layer'=>'Layer'
);

// Adminmodule template
$t = new cPTemplate ( 'extensions/sbook/templates/adminmodule.php' );
$t->conf = $conf;
$t->fid = $fieldObject->ID;

// Show module options
$str = '';
foreach ( $options as $k=>$option )
{
	$s = ( $k == $conf->Type ) ? ' selected="selected"' : '';
	$str .= '<option value="' . $k . '"' . $s . '>' . $option . '</option>';
}
$t->options = $str;
$t->site = $conf->Site;
$t->field =& $fieldObject;

// Render ir
$extension .= $t->render ();

// Clean up
unset( $t, $conf, $options, $str, $option );

?>
