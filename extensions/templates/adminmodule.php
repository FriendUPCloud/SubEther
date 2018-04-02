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

$conf = CreateObjectFromString ( $fieldObject->DataMixed );

$t = new cPTemplate ( 'extensions/templates/templates/adminmodule.php' );
$options = '';

if ( $dir = opendir ( 'upload/template/templates' ) )
{
	while ( $file = readdir ( $dir ) )
	{
		if ( $file{0} == '.' || substr ( $file, -4, 4 ) != 'html' )
			continue;
		$s = $conf->Template == $file ? ' selected="selected"' : '';
		$options .= '<option value="' . $file . '"' . $s . '>' . $file . '</option>';
	}
	closedir ( $dir );
}

$t->options = $options;
$t->field =& $fieldObject;

$extension .= $t->render ();

?>
