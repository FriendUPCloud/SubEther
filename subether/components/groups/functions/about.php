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

$q = '
	SELECT 
		c.* 
	FROM 
		SBookCategory c 
	WHERE
		c.ID = \'' . $parent->folder->CategoryID . '\' 
';

$astr = ''; $pars = false; $accset = array();

if( $row = $database->fetchObjectRow( $q ) )
{
	//die( $row->Settings . ' --' );
	$row->Settings = json_obj_decode( $row->Settings );
	//die( print_r( $row->Settings,1 ) . ' --' );
	if( isset( $_POST[ 'edit' ] ) )
	{
		// Parent Settings
		if ( $row->ParentID > 0 && ( $pset = $database->fetchObjectRow( '
			SELECT 
				*
			FROM 
				SBookCategory
			WHERE
					ID = \'' . $row->ParentID . '\' 
				AND Type = "SubGroup" 
				AND IsSystem = "0" 
				AND ParentID = "0" 
		', false, 'components/groups/functions/about.php' ) ) )
		{
			$pars = true;
			
			$pset->Settings = json_obj_decode( $pset->Settings );
			
			if ( isset( $pset->Settings->AccessLevels ) )
			{
				$row->Settings->AccessLevels = $pset->Settings->AccessLevels;
			}
		}
		
		if ( $account = $database->fetchObjectRows( '
			SELECT 
				*
			FROM 
				SBookAccountingSettings
			WHERE
					' . ( $row->ParentID > 0 ? 'CategoryID = \'' . $row->ParentID . '\' ' : 'CategoryID = \'' . $parent->folder->CategoryID . '\' ' ) . '
				AND CategoryID > 0 
			ORDER BY
				ID ASC
		', false, 'components/groups/functions/about.php' ) )
		{
			//
			
			foreach ( $account as $k=>$v )
			{
				$accset[$v->ID] = $v->Name . ' (' . $v->VisualID . ')';
			}
		}
		
		//$astr .= '<table><tr><td>';
		
		if( isset( $_POST[ 'edit' ] ) )
		{
			$astr = '';
		}
		$astr .= '<div class="Box">';
		/*$astr .= '<div><h4>Plivo VoiceChat Settings</h4></div>';
		$astr .= '<table>';
		$astr .= '<tr><td>Username: </td><td><input name="Plivo_Username" value="' . $row->Settings->Plivo->Username . '"/></td></tr>';
		$astr .= '<tr><td>Password: </td><td><input name="Plivo_Password" value="' . $row->Settings->Plivo->Password . '"/></td></tr>';
		$astr .= '<tr><td>URI: </td><td><input name="Plivo_URI" value="' . $row->Settings->Plivo->URI . '"/></td></tr>';
		$astr .= '</table>';*/
		
		$astr .= '<div id="accesslevels"><h4>' . i18n( 'i18n_Access Levels' ) . '</h4>';
		//$astr .= '<table style="width:100%;" id="accesslevelslist">';
		$astr .= '<div id="accesslevelslist">';
		
		if ( is_object( $row->Settings ) && ( !isset( $row->Settings->AccessLevels[0] ) || !$row->Settings->AccessLevels[0]->ID && !$row->Settings->AccessLevels[0]->Name ) )
		{
			$row->Settings->AccessLevels = json_obj_decode( '[{"ID":"n","Name":"Owner","Display":"@","r":"1","w":"1","d":"1","a":"1","o":"1"},{"ID":"o","Name":"Admin","Display":"@","r":"1","w":"1","d":"1","a":"1","o":"0"},{"ID":"v","Name":"Moderator","Display":"+","r":"1","w":"1","d":"0","a":"1","o":"0"},{"ID":"i","Name":"Member","Display":"","r":"1","w":"1","d":"1","a":"0","o":"0"}]' );
		}
		
		$astr .= '<div><table style="width:100%;"><tbody>';
		$astr .= '<tr>';
		$astr .= '<td class="c0"><strong></strong></td>';
		$astr .= '<td class="c1"><strong>' . i18n( 'i18n_ID' ) . ':</strong></td>';
		$astr .= '<td class="c2"><strong>' . i18n( 'i18n_Name' ) . ':</strong></td>';
		$astr .= '<td class="c3"><strong>' . i18n( 'i18n_Display' ) . ':</strong></td>';
		
		$astr .= '<td class="c6"><strong>' . i18n( 'i18n_Read' ) . ':</strong></td>';
		$astr .= '<td class="c7"><strong>' . i18n( 'i18n_Write' ) . ':</strong></td>';
		$astr .= '<td class="c8"><strong>' . i18n( 'i18n_Delete' ) . ':</strong></td>';
		$astr .= '<td class="c9"><strong>' . i18n( 'i18n_Admin' ) . ':</strong></td>';
		$astr .= '<td class="c10"><strong>' . i18n( 'i18n_Owner' ) . ':</strong></td>';
		$astr .= '</tr>';
		$astr .= '</tbody></table></div>';
		
		$js = array(
			'button1'=>'onclick="OrderMoveSelectOption(this)"',
			'button2'=>'onclick="OrderMoveSelectOption(this,1)"'/*,
			'option1'=>'onclick="OrderMarkSelectOption(this)"',
			'option2'=>'onclick="OrderMarkSelectOption(this)"'*/
		);
		
		if ( isset( $row->Settings->AccessLevels ) )
		{
			foreach ( $row->Settings->AccessLevels as $acl )
			{
				if ( $acl->ID || $acl->Name || $acl->Display )
				{
					$astr .= '<div class="closed"><table style="width:100%;"><tbody>';
					$astr .= '<tr>';
					$astr .= '<td class="c0"><span class="toggle" onclick="toggleAccessOptions(this)"></span></td>';
					$astr .= '<td class="c1"><input type="text" value="' . $acl->ID . '" ' . ( !$pars ? 'name="ID" onclick="checkAccessInputs()"' : 'disabled' ) . '/></td>';
					$astr .= '<td class="c2"><input type="text" value="' . $acl->Name . '" ' . ( !$pars ? 'name="Name" onclick="checkAccessInputs()"' : 'disabled' ) . '/></td>';
					$astr .= '<td class="c3"><input type="text" value="' . $acl->Display . '" ' . ( !$pars ? 'name="Display" onclick="checkAccessInputs()"' : 'disabled' ) . '/></td>';
					
					$astr .= '<td class="c6"><input type="checkbox" value="1"' . ( $acl->r > 0 ? ' checked="checked"' : '' ) . ' ' . ( !$pars ? 'name="r" onclick="checkAccessInputs()"' : 'disabled' ) . '/></td>';
					$astr .= '<td class="c7"><input type="checkbox" value="1"' . ( $acl->w > 0 ? ' checked="checked"' : '' ) . ' ' . ( !$pars ? 'name="w" onclick="checkAccessInputs()"' : 'disabled' ) . '/></td>';
					$astr .= '<td class="c8"><input type="checkbox" value="1"' . ( $acl->d > 0 ? ' checked="checked"' : '' ) . ' ' . ( !$pars ? 'name="d" onclick="checkAccessInputs()"' : 'disabled' ) . '/></td>';
					$astr .= '<td class="c9"><input type="checkbox" value="1"' . ( $acl->a > 0 ? ' checked="checked"' : '' ) . ' ' . ( !$pars ? 'name="a" onclick="checkAccessInputs()"' : 'disabled' ) . '/></td>';
					$astr .= '<td class="c10"><input type="checkbox" value="1"' . ( $acl->o > 0 ? ' checked="checked"' : '' ) . ' ' . ( !$pars ? 'name="o" onclick="checkAccessInputs()"' : 'disabled' ) . '/></td>';
					$astr .= '</tr>';
					
					$astr .= '<tr>';
					$astr .= '<td class="c0"></td>';
					$astr .= '<td colspan="8">';
					
					$astr .= renderSelectManager( ( !$row->ParentID ? 'Accounting' : '' ), $acl->Accounting, $accset, $js, ( $row->ParentID ? 'disabled' : '' ) );
					
					/*$astr .= '<table style="width:100%;margin-top:12px;"><tbody>';
					$astr .= '<tr>';
					$astr .= '<td style="width:50%;"><select' . ( !$row->ParentID ? ' name="Accounting"' : ' disabled' ) . ' style="width:100%;height:215px;" multiple>';
					
					if ( $accset && isset( $acl->Accounting ) && $acl->Accounting )
					{
						foreach( $accset as $acc )
						{
							if ( strstr( $acl->Accounting, $acc->ID ) )
							{
								$astr .= '<option onclick="MarkSelectOption(this)" value="' . $acc->ID . '">' . $acc->Name . ' (' . $acc->VisualID . ')</option>';
							}
						}
					}
					
					$astr .= '</select></td>';
					$astr .= '<td style="min-width:50px;vertical-align:middle;text-align:center;">';
					$astr .= '<p><button style="background:#EEE;border:0;" onclick="MoveSelectOption(this)"><img src="admin/gfx/icons/arrow_left.png"/></button></p>';
					$astr .= '<p><button style="background:#EEE;border:0;" onclick="MoveSelectOption(this,1)"><img src="admin/gfx/icons/arrow_right.png"/></button></p>';
					$astr .= '</td>';
					$astr .= '<td style="width:50%;"><select' . ( !$row->ParentID ? '' : ' disabled' ) . ' style="width:100%;height:215px;" multiple' . ( isset( $acl->Accounting ) && $acl->Accounting ? ' selected="' . $acl->Accounting . '"' : '' ) . '>';
					
					if ( $accset )
					{
						foreach( $accset as $acc )
						{
							$s = '';
							if ( isset( $acl->Accounting ) && $acl->Accounting && strstr( $acl->Accounting, $acc->ID ) )
							{
								$s = ' selected="selected"';
							}
							$astr .= '<option onclick="MarkSelectOption(this)" value="' . $acc->ID . '"' . $s . '>' . $acc->Name . ' (' . $acc->VisualID . ')</option>';
						}
					}
					
					$astr .= '</select></td>';
					$astr .= '</tr>';
					$astr .= '</tbody></table>';*/
					
					$astr .= '</td>';
					$astr .= '</tr>';
					
					$astr .= '</tbody></table></div>';
				}
			}
		}
		
		$aedt = '';
		
		if ( !$pars )
		{
			$aedt .= '<table style="width:100%;"><tbody>';
			
			$aedt .= '<tr>';
			$aedt .= '<td class="c0"><span class="toggle" onclick="toggleAccessOptions(this)"></span></td>';
			$aedt .= '<td class="c1"><input type="text" name="ID" onclick="checkAccessInputs()"/></td>';
			$aedt .= '<td class="c2"><input type="text" name="Name" onclick="checkAccessInputs()"/></td>';
			$aedt .= '<td class="c3"><input type="text" name="Display" onclick="checkAccessInputs()"/></td>';
			
			// TODO: Add upload of icon for various accesslevels to include on profile picture and so on
			
			$aedt .= '<td class="c6"><input type="checkbox" name="r" value="1" onclick="checkAccessInputs()"/></td>';
			$aedt .= '<td class="c7"><input type="checkbox" name="w" value="1" onclick="checkAccessInputs()"/></td>';
			$aedt .= '<td class="c8"><input type="checkbox" name="d" value="1" onclick="checkAccessInputs()"/></td>';
			$aedt .= '<td class="c9"><input type="checkbox" name="a" value="1" onclick="checkAccessInputs()"/></td>';
			$aedt .= '<td class="c10"><input type="checkbox" name="o" value="1" onclick="checkAccessInputs()"/></td>';
			$aedt .= '</tr>';
			
			$aedt .= '<tr>';
			$aedt .= '<td class="c0"></td>';
			$aedt .= '<td colspan="8">';
			
			$aedt .= renderSelectManager( ( !$row->ParentID ? 'Accounting' : '' ), false, $accset, $js, ( $row->ParentID ? 'disabled' : '' ) );
			
			/*$aedt .= '<table style="width:100%;margin-top:12px;"><tbody>';
			$aedt .= '<tr>';
			$aedt .= '<td style="width:50%;"><select' . ( !$row->ParentID ? ' name="Accounting"' : ' disabled' ) . ' style="width:100%;height:215px;" multiple></select></td>';
			$aedt .= '<td style="min-width:50px;vertical-align:middle;text-align:center;">';
			$aedt .= '<p><button style="background:#EEE;border:0;" onclick="MoveSelectOption(this)"><img src="admin/gfx/icons/arrow_left.png"/></button></p>';
			$aedt .= '<p><button style="background:#EEE;border:0;" onclick="MoveSelectOption(this,1)"><img src="admin/gfx/icons/arrow_right.png"/></button></p>';
			$aedt .= '</td>';
			$aedt .= '<td style="width:50%;"><select' . ( !$row->ParentID ? '' : ' disabled' ) . ' style="width:100%;height:215px;" multiple>';
			
			if ( $accset )
			{
				foreach( $accset as $acc )
				{
					$aedt .= '<option onclick="MarkSelectOption(this)" value="' . $acc->ID . '">' . $acc->Name . ' (' . $acc->VisualID . ')</option>';
				}
			}
			
			$aedt .= '</select></td>';
			$aedt .= '</tr>';
			$aedt .= '</tbody></table>';*/
			
			$aedt .= '</td>';
			$aedt .= '</tr>';
			
			$aedt .= '</tbody></table>';
			
			$astr .= ( '<div' . ( !$accset ? ' class="closed"' : '' ) . '>' . $aedt . '</div>' );
		}
		
		$astr .= '</div>';
		$astr .= ( $aedt ? '<div id="accesslevelsedit" class="hidden' . ( !$accset ? ' closed' : '' ) . '">' . $aedt . '</div>' : '' );
		$astr .= '</div>';
		//$astr .= '</table></div>';
		
		//$astr .= '<div id="thirdparty"><h4>3rdparty iframe</h4>';
		//$astr .= '<table>';
		//$astr .= '<tr><td><span class="label">Source:</span></td><td><input type="text" name="3dparty_Source" value="' . ( isset( $row->Settings->{'3dparty'} ) ? $row->Settings->{'3dparty'}->Source : '' ) . '"/></td></tr>';
		//$astr .= '<tr><td><span class="label">Width:</span></td><td><input type="text" name="3dparty_Width" value="' . ( isset( $row->Settings->{'3dparty'} ) && $row->Settings->{'3dparty'}->Width ? $row->Settings->{'3dparty'}->Width : '100%' ) . '"/></td></tr>';
		//$astr .= '<tr><td><span class="label">Height:</span></td><td><input type="text" name="3dparty_Height" value="' . ( isset( $row->Settings->{'3dparty'} ) && $row->Settings->{'3dparty'}->Height ? $row->Settings->{'3dparty'}->Height : '420' ) . '"/></td></tr>';
		//$astr .= '<tr><td><span class="label">Scrollbar:</span></td><td><input type="checkbox" name="3dparty_Scrollbar" value="1" ' . ( isset( $row->Settings->{'3dparty'} ) && $row->Settings->{'3dparty'}->Scrollbar > 0 ? 'checked="checked"' : '' ) . '/></td></tr>';
		//$astr .= '</table></div>';
		
		$astr .= '<div id="maingroup"><h4>' . i18n( 'i18n_Main Group' ) . '</h4>';
		//$astr .= '<p>';
		$astr .= '<select name="ParentID">';
		
		$group = $database->fetchObjectRow( '
			SELECT
				*
			FROM
				SBookCategory
			WHERE
					Type = "Group"
				AND Name = "Groups"
		', false, 'components/groups/functions/about.php' );
		
		if( isset( $parent->access->IsSystemAdmin ) )
		{
			$q = '
				SELECT * FROM 
				( 
					( 
						SELECT 
							c.*,
							"" AS UserID,
							"" AS RelationID 
						FROM 
							SBookCategory c 
						WHERE 
								c.CategoryID = \'' . $group->ID . '\' 
							AND c.Type = "SubGroup" 
							AND c.IsSystem = "0"
							AND c.NodeID = "0"
							AND c.NodeMainID = "0" 
					) 
					UNION 
					( 
						SELECT 
							c.*, 
							r.ObjectID as UserID, 
							r.ID as RelationID 
						FROM 
							SBookCategory c, 
							SBookCategoryRelation r 
						WHERE 
								r.ObjectType = "Users" 
							AND r.ObjectID = \'' . $webuser->ID . '\' 
							AND c.CategoryID = \'' . $group->ID . '\' 
							AND c.Type = "SubGroup"
							AND c.IsSystem = "0" 
							AND c.ID = r.CategoryID  
					) 
				) z
				GROUP BY
					z.ID 
				ORDER BY 
					z.ID ASC 
			';
		}
		else
		{
			$q = '
				SELECT 
					c.*, 
					r.ObjectID as UserID, 
					r.ID as RelationID 
				FROM 
					SBookCategory c, 
					SBookCategoryRelation r 
				WHERE 
						r.ObjectType = "Users" 
					AND r.ObjectID = \'' . $webuser->ID . '\' 
					AND c.CategoryID = \'' . $group->ID . '\' 
					AND c.Type = "SubGroup"
					AND c.IsSystem = "0" 
					AND c.ID = r.CategoryID 
				ORDER BY 
					r.SortOrder ASC, 
					c.ID ASC 
			';
		}
		
		if ( $group->ID > 0 && ( $grps = $database->fetchObjectRows( $q, false, 'components/groups/functions/about.php' ) ) )
		{
			$cataccess = CategoryAccess( $webuser->ContactID, false, -1, 'IsAdmin' );
			
			$astr .= '<option value="0">' . i18n( 'i18n_No main group' ) . '</option>';
			$astr .= '<option value="0">- - -</option>';
			
			if( $cataccess )
			{
				foreach( $grps as $gr )
				{
					if( !isset( $cataccess[$gr->ID] ) || $gr->ParentID > 0 || $gr->ID == $row->ID ) continue;
					
					$s = ( $row->ParentID > 0 && $row->ParentID == $gr->ID ? ' selected="selected"' : '' );
					
					$astr .= '<option value="' . $gr->ID . '"' . $s . '>' . $gr->Name . '</option>';
				}
			}
		}
		
		$astr .= '</select>';
		//$astr .= '</p>';
		$astr .= '</div>';
		
		$astr .= '<div id="GroupName"><h4>' . i18n( 'i18n_Group Name' ) . '</h4>';
		$astr .= '<input style="width:100%;" type="text" name="Group" value="' . $row->Name . '"/></div>';
		
		$astr .= '<div id="GroupDescription"><h4>' . i18n( 'i18n_Description' ) . '</h4>';
		$astr .= '<div name="Description" class="textarea" contenteditable="true">' . $row->Description . '</div></div>';
		
		$astr .= '<div id="GroupPrivacySettings"><h4>' . i18n( 'i18n_Privacy setting' ) . '</h4>';
		$astr .= '<p>';
		foreach( array( 'OpenGroup'=>'Open', 'ClosedGroup'=>'Closed', 'SecretGroup'=>'Secret' ) as $type=>$v )
		{
			$sel = $type == $row->Privacy ? ' checked="checked"' : '';
			$astr .= '<input type="radio" name="Privacy"' . $sel . ' value="' . $type . '"/> ' . i18n( 'i18n_' . $v ) . ' ';
		}
		$astr .= '</p>';
		
		$ii = 0;
		$astr .= '<div id="GroupWallSettings"><h4>' . i18n( 'i18n_Wall mode' ) . '</h4>';
		$astr .= '<p>';
		foreach( array( 'wall'=>'Wall', 'forum'=>'Forum' ) as $type=>$v )
		{
			$sel = ( ( isset( $row->Settings->WallMode ) && $row->Settings->WallMode == $type ) || $ii == 0 ? ' checked="checked"' : '' );
			$astr .= '<input type="radio" name="WallMode"' . $sel . ' value="' . $type . '"/> ' . i18n( 'i18n_' . $v ) . ' ';
			$ii++;
		}
		$astr .= '</p>';
		
		$astr .= '<br>';
		
		$astr .= '<p><button onclick="saveGroupSettings()">' . i18n( 'i18n_Save' ) . '</button><button onclick="closeGroupSettings(1)">' . i18n( 'i18n_Close' ) . '</button></p>';
		
		$astr .= '</div></div><hr/>';
		if( isset( $_POST[ 'edit' ] ) )
		{
			die( 'ok<!--separate-->' . $astr );
		}
		
		//$astr .= '</td><td>';
		
		//$astr .= '</td></tr></table>';
	}
	else
	{
		//$astr .= '<table><tr><td>';
		
		if( isset( $_POST[ 'refresh' ] ) )
		{
			$astr = '';
		}
		//$astr .= '<div><h4>Description</h4>';
		//$astr .= '</div><table>';
		//$astr .= '<tr><td>';
		if( $row->Description )
		{
			$astr .= '<div class="Box description"><p>' . str_hide( stripslashes( $row->Description ), 250 ) . '</p></div>';
		}
		//$astr .= '</td></tr>';
		//$astr .= '</table>';
		if( isset( $_POST[ 'refresh' ] ) )
		{
			die( 'ok<!--separate-->' . $astr );
		}
		
		//$astr .= '</td><td>';
		
		//$astr .= '</td></tr></table>';
	}
}

?>
