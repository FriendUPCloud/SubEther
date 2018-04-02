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

$str  = '<div id="Accounting">';

$str .= '<br>';

$str .= '<table style="width:100%;" id="accountingsettings">';

$str .= '<tr>';
$str .= '<td class="c1"><strong>#</strong></td>';
$str .= '<td class="c2"><strong>' . i18n( 'i18n_ID' ) . ':</strong></td>';
$str .= '<td class="c3"><strong>' . i18n( 'i18n_Type' ) . ':</strong></td>';
$str .= '<td class="c4"><strong>' . i18n( 'i18n_Name' ) . ':</strong></td>';
$str .= '<td class="c5"><strong>' . i18n( 'i18n_Amount' ) . ':</strong></td>';
$str .= '<td class="c6"></td>';
$str .= '</tr>';

$pars = false;

// Default Settings
$rows = $database->fetchObjectRows( '
	SELECT * FROM SBookAccountingSettings
	WHERE CategoryID = \'' . $parent->folder->CategoryID . '\' 
	ORDER BY ID ASC
' );

// Parent Settings
if ( $pset = $database->fetchObjectRows( '
	SELECT 
		a.* 
	FROM 
		SBookCategory c, 
		SBookCategory p,
		SBookAccountingSettings a 
	WHERE
			c.ID = \'' . $parent->folder->CategoryID . '\'
		AND c.Type = "SubGroup"
		AND c.IsSystem = "0"
		AND c.ParentID > 0 
		AND p.ID = c.ParentID 
		AND p.Type = "SubGroup" 
		AND p.IsSystem = "0" 
		AND p.ParentID = "0"
		AND a.CategoryID = p.ID
	ORDER BY
		a.ID ASC 
' ) )
{
	$pars = true;
	
	$rows = $pset;
}

/*$options = array(
	'Auto'=>'Auto',
	'1-T-Hours'=>'TIM',
	'1-T-Hours-50'=>'TIM-50%',
	'1-T-Hours-100'=>'TIM-100%',
	'1-T-Hours-Addition'=>'TIL',
	'2-V-Product'=>'VAR'
);*/

$options = array(
	'Auto'=>i18n('Auto'),
	'Hours'=>i18n('Hours'),
	'Hours-50'=>i18n('Hours (50%)'),
	'Hours-100'=>i18n('Hours (100%)'),
	'Hours-Addition'=>i18n('Addition'),
	'Product-KM'=>i18n('KM'),
	'Product-Toll'=>i18n('Toll'),
	'Product-Parking'=>i18n('Parking')
);

if ( $rows )
{
	foreach( $rows as $row )
	{
		$str .= '<tr id="aid#' . $row->ID . '">';
		$str .= '<td class="c1"><input type="checkbox" value="1" ' . ( $pars ? 'disabled' : 'name="Temp"' ) . '/></td>';
		$str .= '<td class="c2"><input type="text" value="' . $row->VisualID . '" ' . ( $pars ? 'disabled' : 'name="VisualID" onclick="checkSettingsInputs()" onchange="saveAccountingSettings(this)" onkeydown="accIsEdit(this)"' ) . '/></td>';
		$str .= '<td class="c3"><select ' . ( $pars ? 'disabled' : 'name="Type" onchange="saveAccountingSettings(this)"' ) . '>';
		
		foreach( $options as $k=>$v )
		{
			$str .= '<option value="' . $k . '"' . ( $row->Type == $k ? ' selected="selected"' : '' ) . '>' . $v . '</option>';
		}
		
		$str .= '</select></td>';
		$str .= '<td class="c4"><input type="text" value="' . $row->Name . '" ' . ( $pars ? 'disabled' : 'name="Name" onclick="checkSettingsInputs()" onchange="saveAccountingSettings(this)" onkeydown="accIsEdit(this)"' ) . '/></td>';
		$str .= '<td class="c5"><input type="text" value="' . ( $row->Amount ? $row->Amount : 0 ) . '" ' . ( $pars ? 'disabled' : 'name="Amount" onclick="checkSettingsInputs()" onchange="saveAccountingSettings(this)" onkeydown="accIsEdit(this)"' ) . '/></td>';
		$str .= '<td class="c6">' . ( $pars ? '' : '<span onclick="deleteAccountingSettings(this)"> [x] </span>' ) . '</td>';
		$str .= '</tr>';
	}
}

if ( !$pars )
{
	$str .= '<tr>';
	$str .= '<td class="c1"><input type="checkbox" name="Temp" value="1"/></td>';
	$str .= '<td class="c2"><input type="text" name="VisualID" onclick="checkSettingsInputs()" onchange="saveAccountingSettings(this)"/ onkeydown="accIsEdit(this)"></td>';
	$str .= '<td class="c3"><select name="Type" onchange="saveAccountingSettings(this)">';
	
	foreach( $options as $k=>$v )
	{
		$str .= '<option value="' . $k . '">' . $v . '</option>';
	}
	
	$str .= '</select></td>';
	$str .= '<td class="c4"><input type="text" name="Name" onclick="checkSettingsInputs()" onchange="saveAccountingSettings(this)" onkeydown="accIsEdit(this)"/></td>';
	$str .= '<td class="c5"><input type="text" name="Amount" onclick="checkSettingsInputs()" onchange="saveAccountingSettings(this)" onkeydown="accIsEdit(this)"/></td>';
	$str .= '<td class="c6"><span onclick="deleteAccountingSettings(this)"> [x] </span></td>';
	$str .= '</tr>';
}

$str .= '</table>';

$str .= '</div>';

?>
