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

$gstr = ''; $mstr = '';

if( $modulefolder = openFolder( $root . 'modules' ) )
{
	// --- Global view -------------------------------------------------------------------
	
	$gstr = '<ul>';
	
	foreach( $modulefolder as $mf )
	{
		$positions = ModulePosition( $mf->name );
		
		$gstr .= '<li class="closed">';
		$gstr .= '<table><tr><td style="width: 300px;">';
		$gstr .= '<span onclick="openRenderList( this )">[+]</span>';
		$gstr .= '<span> Module: <strong>' . $mf->name . '</strong> </span>';
		$gstr .= '</td><td align="right">';
		$gstr .= '<input style="width:20px;" type="text" module="' . $mf->name . '" value="' . ( $positions[0]->SortOrder ? $positions[0]->SortOrder : 0 ) . '" onchange="updateGlobalSortOrder( this )"/>';
		$gstr .= '<select module="' . $mf->name . '" onchange="updateGlobalAccess( this )">';
		$gstr .= '<option title="Admin" value=",99," ' . ( $positions[0]->UserLevels == ',99,' ? 'selected="selected"' : '' ) . '>2</option>';
		$gstr .= '<option title="Users" value=",99,1," ' . ( $positions[0]->UserLevels == ',99,1,' ? 'selected="selected"' : '' ) . '>1</option>';
		$gstr .= '<option title="Public" value=",0," ' . ( $positions[0]->UserLevels == ',0,' ? 'selected="selected"' : '' ) . '>0</option>';
		$gstr .= '</select> access ';
		$gstr .= '<input name="' . $mf->name . '_global" value="0" type="radio" ' . ( $positions[0]->Visible == '0' ? 'checked="checked"' : '' ) . ' onclick="updateGlobalVisibility( this )"> hidden ';
		$gstr .= '<input name="' . $mf->name . '_global" value="1" type="radio" ' . ( $positions[0]->Visible == '1' ? 'checked="checked"' : '' ) . ' onclick="updateGlobalVisibility( this )"> visible ';
		$gstr .= '<input name="' . $mf->name . '_global" value="2" type="radio" ' . ( $positions[0]->Visible == '2' ? 'checked="checked"' : '' ) . ' onclick="updateGlobalVisibility( this )"> menu ';
		$gstr .= '<input name="' . $mf->name . '_global" value="3" type="radio" ' . ( $positions[0]->Visible == '3' ? 'checked="checked"' : '' ) . ' onclick="updateGlobalVisibility( this )"> dropdown ';
		$gstr .= '</td></tr></table>';
		
		if( $positions )
		{
			$head = ''; $row = '';
			$gstr .= '<table>';
			foreach( $positions as $po )
			{
				if( $po->Position == '' ) continue;
				
				$cobj = ComponentPosition( $mf->name, $po->Position, 'Name' );
				
				$head .= '<th>';
				$head .= $po->Position;
				$head .= '<input module="' . $mf->name . '" position="' . $po->Position . '" type="checkbox" ' . ( $po->IsMain > 0 ? 'checked="checked"' : '' ) . ' onclick="updateGlobalRoute( this )">';
				$head .= '</th>';
				
				$row .= '<td><ul>';
				
				if( $componentfolder = openFolder( $root . 'components' ) )
				{
					foreach( $componentfolder as $cf )
					{
						$row .= '<li>';
						$row .= '<span onclick="openTabList( this )">[+]</span>';
						$row .= '<input module="' . $mf->name . '" position="' . $po->Position . '" component="' . $cf->name . '" type="checkbox" ' . ( $cobj[$cf->name] ? 'checked="checked"' : '' ) . ' onclick="updateGlobalSettings( this )">';
						$row .= '<input style="width:20px;" type="text" module="' . $mf->name . '" position="' . $po->Position . '" component="' . $cf->name . '" value="' . ( $cobj[$cf->name]->SortOrder ? $cobj[$cf->name]->SortOrder : 0 ) . '" onchange="updateGlobalSortOrder( this )"/>';
						$row .= '<select module="' . $mf->name . '" position="' . $po->Position . '" component="' . $cf->name . '" onchange="updateGlobalAccess( this )">';
						$row .= '<option title="Admin" value=",99," ' . ( isset( $cobj[$cf->name] ) && $cobj[$cf->name]->UserLevels == ',99,' ? 'selected="selected"' : '' ) . '>2</option>';
						$row .= '<option title="Users" value=",99,1," ' . ( isset( $cobj[$cf->name] ) && $cobj[$cf->name]->UserLevels == ',99,1,' ? 'selected="selected"' : '' ) . '>1</option>';
						$row .= '<option title="Public" value=",0," ' . ( isset( $cobj[$cf->name] ) && $cobj[$cf->name]->UserLevels == ',0,' ? 'selected="selected"' : '' ) . '>0</option>';
						$row .= '</select>';
						$row .= '<span> ' . $cf->name . '</span>';
						$row .= '</li>';
					}
				}
				
				if( $appfolder = openFolder( $root . 'applications' ) )
				{
					foreach( $appfolder as $cf )
					{
						$row .= '<li>';
						$row .= '<span onclick="openTabList( this )">[+]</span>';
						$row .= '<input module="' . $mf->name . '" position="' . $po->Position . '" component="' . $cf->name . '" type="checkbox" ' . ( $cobj[$cf->name] ? 'checked="checked"' : '' ) . ' onclick="updateGlobalSettings( this )">';
						$row .= '<input style="width:20px;" type="text" module="' . $mf->name . '" position="' . $po->Position . '" component="' . $cf->name . '" value="' . ( $cobj[$cf->name]->SortOrder ? $cobj[$cf->name]->SortOrder : 0 ) . '" onchange="updateGlobalSortOrder( this )"/>';
						$row .= '<select module="' . $mf->name . '" position="' . $po->Position . '" component="' . $cf->name . '" onchange="updateGlobalAccess( this )">';
						$row .= '<option title="Admin" value=",99," ' . ( isset( $cobj[$cf->name] ) && $cobj[$cf->name]->UserLevels == ',99,' ? 'selected="selected"' : '' ) . '>2</option>';
						$row .= '<option title="Users" value=",99,1," ' . ( isset( $cobj[$cf->name] ) && $cobj[$cf->name]->UserLevels == ',99,1,' ? 'selected="selected"' : '' ) . '>1</option>';
						$row .= '<option title="Public" value=",0," ' . ( isset( $cobj[$cf->name] ) && $cobj[$cf->name]->UserLevels == ',0,' ? 'selected="selected"' : '' ) . '>0</option>';
						$row .= '</select>';
						$row .= '<span> ' . $cf->name . '</span>';
						$row .= '</li>';
					}
					
				}
				
				$row .= '</td></ul>';
			}
			$gstr .= '<tr>' . $head . '</tr>';
			$gstr .= '<tr>' . $row . '</tr>';
			$gstr .= '</table>';
		}
		
		$gstr .= '</li>';
	}
	
	$gstr .= '</ul>';
	
	// --- Mobile view ----------------------------------------------------------------
	
	$mstr = '<ul>';
	
	foreach( $modulefolder as $mf )
	{
		$positions = ModulePosition( $mf->name, 'mobile', $root );
		
		$mstr .= '<li class="closed">';
		$mstr .= '<table><tr><td style="width: 300px;">';
		$mstr .= '<span onclick="openRenderList( this )">[+]</span>';
		$mstr .= '<span> Module: <strong>' . $mf->name . '</strong> </span>';
		$mstr .= '</td><td align="right">';
		$mstr .= '<input style="width:20px;" type="text" view="mobile" module="' . $mf->name . '" value="' . ( $positions[0]->SortOrder ? $positions[0]->SortOrder : 0 ) . '" onchange="updateGlobalSortOrder( this )"/>';
		$mstr .= '<select view="mobile" module="' . $mf->name . '" onchange="updateGlobalAccess( this )">';
		$mstr .= '<option title="Admin" value=",99," ' . ( $positions[0]->UserLevels == ',99,' ? 'selected="selected"' : '' ) . '>2</option>';
		$mstr .= '<option title="Users" value=",99,1," ' . ( $positions[0]->UserLevels == ',99,1,' ? 'selected="selected"' : '' ) . '>1</option>';
		$mstr .= '<option title="Public" value=",0," ' . ( $positions[0]->UserLevels == ',0,' ? 'selected="selected"' : '' ) . '>0</option>';
		$mstr .= '</select> access ';
		$mstr .= '<input view="mobile" name="' . $mf->name . '_mobile" value="0" type="radio" ' . ( $positions[0]->Visible == '0' ? 'checked="checked"' : '' ) . ' onclick="updateGlobalVisibility( this )"> hidden ';
		$mstr .= '<input view="mobile" name="' . $mf->name . '_mobile" value="1" type="radio" ' . ( $positions[0]->Visible == '1' ? 'checked="checked"' : '' ) . ' onclick="updateGlobalVisibility( this )"> visible ';
		$mstr .= '<input view="mobile" name="' . $mf->name . '_mobile" value="2" type="radio" ' . ( $positions[0]->Visible == '2' ? 'checked="checked"' : '' ) . ' onclick="updateGlobalVisibility( this )"> menu ';
		$mstr .= '<input view="mobile" name="' . $mf->name . '_mobile" value="3" type="radio" ' . ( $positions[0]->Visible == '3' ? 'checked="checked"' : '' ) . ' onclick="updateGlobalVisibility( this )"> dropdown ';
		$mstr .= '</td></tr></table>';
		
		if( $positions )
		{
			$head = ''; $row = '';
			$mstr .= '<table>';
			foreach( $positions as $po )
			{
				if( $po->Position == '' ) continue;
				
				$cobj = ComponentPosition( $mf->name, $po->Position, 'Name', false, 'mobile' );
				
				$head .= '<th>';
				$head .= $po->Position;
				$head .= '<input view="' . $po->Type . '" module="' . $mf->name . '" position="' . $po->Position . '" type="checkbox" ' . ( $po->IsMain > 0 ? 'checked="checked"' : '' ) . ' onclick="updateGlobalRoute( this )">';
				$head .= '</th>';
				if( $componentfolder = openFolder( $root . 'components' ) )
				{
					$row .= '<td><ul>';
					foreach( $componentfolder as $cf )
					{
						$row .= '<li>';
						$row .= '<span onclick="openTabList( this )">[+]</span>';
						$row .= '<input view="' . $po->Type . '" module="' . $mf->name . '" position="' . $po->Position . '" component="' . $cf->name . '" type="checkbox" ' . ( $cobj[$cf->name] ? 'checked="checked"' : '' ) . ' onclick="updateGlobalSettings( this )">';
						$row .= '<input style="width:20px;" type="text" view="' . $po->Type . '" module="' . $mf->name . '" position="' . $po->Position . '" component="' . $cf->name . '" value="' . ( $cobj[$cf->name]->SortOrder ? $cobj[$cf->name]->SortOrder : 0 ) . '" onchange="updateGlobalSortOrder( this )"/>';
						$row .= '<select view="' . $po->Type . '" module="' . $mf->name . '" position="' . $po->Position . '" component="' . $cf->name . '" onchange="updateGlobalAccess( this )">';
						$row .= '<option title="Admin" value=",99," ' . ( isset( $cobj[$cf->name] ) && $cobj[$cf->name]->UserLevels == ',99,' ? 'selected="selected"' : '' ) . '>2</option>';
						$row .= '<option title="Users" value=",99,1," ' . ( isset( $cobj[$cf->name] ) && $cobj[$cf->name]->UserLevels == ',99,1,' ? 'selected="selected"' : '' ) . '>1</option>';
						$row .= '<option title="Public" value=",0," ' . ( isset( $cobj[$cf->name] ) && $cobj[$cf->name]->UserLevels == ',0,' ? 'selected="selected"' : '' ) . '>0</option>';
						$row .= '</select>';
						$row .= '<span> ' . $cf->name . '</span>';
						$row .= '</li>';
					}
					$row .= '</td></ul>';
				}
			}
			$mstr .= '<tr>' . $head . '</tr>';
			$mstr .= '<tr>' . $row . '</tr>';
			$mstr .= '</table>';
		}
		
		$mstr .= '</li>';
	}
	
	$mstr .= '</ul>';
}

?>
