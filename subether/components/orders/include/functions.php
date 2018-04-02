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

function renderHtmlFields( $ele, $type = false, $name = false, $value = false, $placeholder = false, $title = false, $data = false, $js = false, $mode = false, $attr = false )
{
	switch ( $ele )
	{
		
		// --- Input ------------------------------------------------------------------------------------------------------------------------------------
		
		case 'input':
			
			if ( $type == 'radio' )
			{
				if ( $data && ( is_object( $data ) || is_array( $data ) ) )
				{
					$str = '';
					
					foreach ( $data as $k=>$v )
					{
						$str .= '<input type="radio"' . ( $name != '' ? ( ' name="' . $name . '"' ) : '' ) . ' value="' . $k . '"' . ( $k == $value ? ' checked="1"' : '' ) . '/><span>' . i18n( $v ) . '</span>';
					}
					
					return $str;
				}
			}
			else if ( $type == 'checkbox' )
			{
				return '<input type="checkbox"' . ( $name != '' ? ( ' name="' . $name . '"' ) : '' ) . ( $value != '' ? ( ' value="' . $value . '"' ) : '' ) . ( $attr != '' ? ( ' ' . $attr . '' ) : '' ) . ( $js != '' ? ( ' ' . $js . '' ) : '' ) . ( $mode != '' ? ( ' ' . $mode . '' ) : '' ) . '/><span>' . $title . '</span>';
			}
			else
			{
				return '<input' . ( $type != '' ? ( ' type="' . $type . '"' ) : '' ) . ( $name != '' ? ( ' name="' . $name . '"' ) : '' ) . ( $value != '' ? ( ' value="' . $value . '"' ) : '' ) . ( $placeholder != '' ? ( ' placeholder="' . $placeholder . '"' ) : '' ) . ( $attr != '' ? ( ' ' . $attr . '' ) : '' ) . ( $js != '' ? ( ' ' . $js . '' ) : '' ) . ( $mode != '' ? ( ' ' . $mode . '' ) : '' ) . '/>';
			}
			
			break;
		
		// --- Textarea ----------------------------------------------------------------------------------------------------------------------------------
		
		case 'textarea':
			
			return '<textarea' . ( $name != '' ? ( ' name="' . $name . '"' ) : '' ) . ( $placeholder != '' ? ( ' placeholder="' . $placeholder . '"' ) : '' ) . ( $attr != '' ? ( ' ' . $attr . '' ) : '' ) . ( $js != '' ? ( ' ' . $js . '' ) : '' ) . ( $mode != '' ? ( ' ' . $mode . '' ) : '' ) . '>' . $value . '</textarea>';
			
			break;
		
		// --- Select ------------------------------------------------------------------------------------------------------------------------------------
		
		case 'select':
			
			if ( $data && ( is_object( $data ) || is_array( $data ) ) )
			{
				$str = '<select' . ( $name != '' ? ( ' name="' . $name . '"' ) : '' ) . ( $attr != '' ? ( ' ' . $attr . '' ) : '' ) . ( $js != '' ? ( ' ' . $js . '' ) : '' ) . ( $mode != '' ? ( ' ' . $mode . '' ) : '' ) . '>';
				
				foreach ( $data as $k=>$v )
				{
					$str .= '<option value="' . $k . '"' . ( $value && ( ( is_array( $value ) && in_array( $k, $value ) ) || ( !is_array( $value ) && $value == $k ) ) ? ' selected="selected"' : '' ) . '>' . i18n( $v ) . '</option>';
				}
				
				$str .= '</select>';
				
				return $str;
			}
			
			break;
		
		// --- Datefield ------------------------------------------------------------------------------------------------------------------------------------
		
		case 'datefield':
			
			return '<div class="' . i18n( 'i18n_day_' . date( 'd', strtotime( $value ? $value : date( 'Y-m-d' ) ) ) ) . ' datefield2 closed">' . 
				   '<div class="icon"' . ( $js ? ( ' ' . $js ) : '' ) . '></div>' . 
				   '<div class="calendar">' . 
				   '<div class="calendar_inner">' . renderOrderCalendar( ( $value ? date( 'Y-m-d', strtotime( $value ) ) : date( 'Y-m-d' ) ) ) . '</div>' .
				   '<div class="buttons"><button onclick="CloseOrderCalendar(this.parentNode.parentNode.parentNode)">' . i18n( 'i18n_Close' ) . '</button></div>' . 
				   '</div>' . 
				   '<input type="hidden" name="' . $name . '" value="' . ( $value ? strtotime( $value ) : strtotime( date( 'Y-m-d' ) ) ) . '"/>' . 
				   '<input type="text" ' . ( $js ? ( ' ' . $js ) : '' ) . ' value="' . ( $value ? i18n( date( 'D', strtotime( $value ) ) ) . date( ', d.m.Y', strtotime( $value ) ) : ( i18n( date( 'D' ) ) . date( ', d.m.Y' ) ) ) . '" readonly/>' . 
				   '</div>';
				   
			break;
		
		// --- Datetimefield ------------------------------------------------------------------------------------------------------------------------------------
		
		case 'datetimefield':
		
			$str  = '<div class="' . i18n( 'i18n_day_' . date( 'd', strtotime( $value ? $value : date( 'Y-m-d' ) ) ) ) . ' datetimefield closed">';
			$str .= '<div class="icon"' . ( $js ? ( ' ' . $js ) : '' ) . '></div>';
			$str .= '<div class="calendar">' . renderOrderCalendar( ( $value ? date( 'Y-m-d', strtotime( $value ) ) : date( 'Y-m-d' ) ) ) . '</div>';
			$str .= '<input type="hidden" id="' . $name[0] . '" name="' . $name[0] . '" value="' . ( $value ? strtotime( $value ) : strtotime( date( 'Y-m-d H:i:s' ) ) ) . '"/>';
			$str .= '<input class="dateinput" type="text" ' . ( $js ? ( ' ' . $js ) : '' ) . ' value="' . ( $value ? i18n( date( 'D', strtotime( $value ) ) ) . date( ', d.m.Y', strtotime( $value ) ) : ( i18n( date( 'D' ) ) . date( ', d.m.Y' ) ) ) . '" readonly/>';
			$str .= '<input class="timeinput" id="' . $name[1] . '" name="' . $name[1] . '" type="text" ' . ( $value ? ( ' value="' . date( 'H:i', strtotime( $value ) ) . '"' ) : '' ) . ( $placeholder != '' ? ( ' placeholder="' . $placeholder . '"' ) : '' ) . '/>';
			$str .= '</div>';
			
			return $str;
			
			break;
		
		// --- Hourfield ------------------------------------------------------------------------------------------------------------------------------------
		
		case 'hourfield':
			
			$str  = '<div class="hourfield">';
			
			if ( $data && ( is_array( $data ) || is_object( $data ) ) )
			{
				$str .= '<select' . ( isset( $name[1] ) ? ( ' name="' . $name[1] . '"' ) : '' ) . ( isset( $attr[1] ) ? ( ' ' . $attr[1] ) : '' ) . ( isset( $js[1] ) ? ( ' ' . $js[1] ) : '' ) . ( isset( $mode[1] ) ? ( ' ' . $mode[1] ) : '' ) . '>';
				
				foreach ( $data as $k=>$v )
				{
					$str .= '<option value="' . $k . '"' . ( isset( $value[1] ) && ( strstr( $value[1], (string)$k ) ) ? ' selected="selected"' : '' ) . '>' . i18n( $v ) . '</option>';
				}
				
				$str .= '</select>';
			}
			
			$str .= '<input type="number"' . ( isset( $name[0] ) ? ( ' name="' . $name[0] . '"' ) : '' ) . ( isset( $value[0] ) ? ( ' value="' . $value[0] . '"' ) : '' ) . ( $placeholder != '' ? ( ' placeholder="' . $placeholder . '"' ) : '' ) . ( isset( $attr[0] ) ? ( ' ' . $attr[0] ) : '' ) . ( isset( $js[0] ) ? ( ' ' . $js[0] ) : '' ) . ( isset( $mode[0] ) ? ( ' ' . $mode[0] ) : '' ) . '/>';
			$str .= '</div>';
			
			return $str;
			
			break;
		
		// --- Selectmanager -------------------------------------------------------------------------------------------------------------------------------
		
		case 'selectmanager':
			
			$opt1 = ''; $opt2 = '';
			
			$order = array();
			
			if ( $data && is_array( $data ) )
			{
				foreach( $data as $k=>$v )
				{
					if ( strstr( $value, (string)$k ) )
					{
						$opt1 .= '<option' . ( $js != '' && is_array( $js ) ? ( ' ' . $js['option1'] ) : '' ) . ' value="' . $k . '">' . $v . '</option>';
					}
					if ( !strstr( $value, (string)$k ) )
					{
						$opt2 .= '<option' . ( $js != '' && is_array( $js ) ? ( ' ' . $js['option2'] ) : '' ) . ' value="' . $k . '"' . /*( $value && is_string( $value ) && strstr( $value, (string)$k ) ? ' selected="selected"' : '' ) . */'>' . $v . '</option>';
					}
					
					$order[] = $k;
				}
			}
			
			$str  = '<table class="selectmanager" style="width:100%;"><tbody><tr>';
			$str .= '<td style="width:50%;"><select' . ( $name != '' ? ( ' name="' . $name . '"' ) : '' ) . ' class="selectmanager" style="width:100%;height:215px;" multiple' . ( $mode != '' ? ( ' ' . $mode . '' ) : '' ) . '>';
			$str .= $opt1;
			$str .= '</select></td>';
			$str .= '<td style="min-width:50px;vertical-align:middle;text-align:center;">';
			$str .= '<p><button style="background:#EEE;border:0;"' . ( $js != '' && is_array( $js ) ? ( ' ' . $js['button1'] ) : '' ) . '><img src="admin/gfx/icons/arrow_left.png"/></button></p>';
			$str .= '<p><button style="background:#EEE;border:0;"' . ( $js != '' && is_array( $js ) ? ( ' ' . $js['button2'] ) : '' ) . '><img src="admin/gfx/icons/arrow_right.png"/></button></p>';
			$str .= '</td><td style="width:50%;"><select class="selectmanager" style="width:100%;height:215px;" multiple' . ( $mode != '' ? ( ' ' . $mode . '' ) : '' ) . /*( $value && is_string( $value ) ? ' selected="' . $value . '"' : '' ) . */ ( $order ? ' sortorder="' . implode( ',', $order ) . '"' : '' ) . '>';
			$str .= $opt2;
			$str .= '</select></td>';
			$str .= '</tr></tbody></table>';
			
			return $str;
			
			break;
		
		// --- Structuremanager --------------------------------------------------------------------------------------------------------------------------------
		
		case 'structuremanager':
			
			if ( !function_exists( 'RenderStructureArray' ) )
			{
				function RenderStructureArray( $string, $js, $depth = 0, $data = false, $end = false, $part = false, $parent = false, $i = 1 )
				{
					$category = array(); $members = array();
					
					if ( $data->levels )
					{
						foreach( $data->levels as $k=>$d )
						{
							$category[$k] = $d;
						}
					}
					
					if ( $data->members )
					{
						foreach( $data->members as $k=>$d )
						{
							$members[$k] = $d;
						}
					}
					
					$cat = false;
					
					if ( $depth > 0 )
					{
						$depth = 0;
						$cat = true;
					}
					else
					{
						$depth++;
					}
					
					if ( $string && $js )
					{
						if ( !is_array( $string ) && !is_object( $string ) )
						{
							$str  = '';
							
							$str .= '<div>';
							
							if ( !$cat && $end )
							{
								$str .= '	<span class="spacer"></span>';
							}
							else
							{
								$str .= '	<span class="toggle" onclick="ultoggle(this,2)"></span>';
							}
							
							if ( $cat )
							{
								$str .= '<select class="h4" disabled>';
								
								foreach( $category as $k=>$v )
								{
									$sel = ( $k == $string ? ' selected="selected"' : '' );
									
									$str .= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>';
								}
								
								$str .= '</select>';
								
								//$str .= '<h4>' . $string . '</h4>';
							}
							else
							{
								$opt = '';
								
								foreach( $members as $k=>$v )
								{
									if ( !$part || $k == $string || ( $part && !strstr( $part, (string)$k ) ) )
									{
										if ( $k == $string )
										{
											$sel = ' selected="selected"';
											$selected = $k;
										}
										else
										{
											$sel = '';
										}
										
										$opt .= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>';
									}
								}
								
								$str .= '<select ' . $js . ( isset( $part ) && $part ? ( ' participants="' . $part . '"' ) : '' ) . ( isset( $selected ) && $selected ? ( ' selected="' . $selected . '"' ) : '' ) . ( isset( $parent ) && $parent ? ( ' parent="' . $parent . '"' ) : '' ) . '>';
								$str .= $opt;
								$str .= '</select>';
							}
							
							$str .= '</div>';
							
							return $str;
						}
						else if ( is_array( $string ) || is_object( $string ) )
						{
							$str = ''; $ii = 1;
							
							foreach( $string as $key=>$val )
							{
								$sort = ( ( $parent ? $parent : $key ) . '|' . $ii++ . '|' . $i++ );
								
								$str .= '<ul><li sort="' . $sort . '"' . ( isset( $data->open ) && strstr( $data->open, (string)$sort ) ? ' class="open"' : '' ) . '>';
								
								if ( !is_array( $key ) && !is_object( $key ) )
								{
									$str .= RenderStructureArray( $key, $js, $depth, $data, ( !$val ? true : false ), ( isset( $data->participants[$key] ) ? $data->participants[$key] : $part ), $parent, $i );
								}
								
								if ( $res = RenderStructureArray( $val, $js, $depth, $data, false, ( isset( $data->participants[$key] ) ? $data->participants[$key] : $part ), $key, $i ) )
								{
									$str .= $res;
								}
								
								$str .= '</li></ul>';
							}
							
							return $str;
						}
					}
					
					return false;
				}
			}
			
			$json = ''; $uid = '';
			
			if ( $data->hierarchy )
			{
				$array = $data->hierarchy;
			}
			
			if ( $data->participants )
			{
				$json = htmlentities( json_encode( $data->participants ) );
				
				foreach( $data->participants as $val )
				{
					if ( $val )
					{
						$uid = ( $uid ? ( $uid . ',' . $val ) : $val );
					}
				}
			}
			
			$str  = '<div class="structuremanager">' . RenderStructureArray( $array, $js, 0, $data ) . '</div>';
			
			$str .= '<input name="Participants" type="hidden" value="' . $uid . '"/>';
			$str .= '<input name="Data" type="hidden" value="' . $json . '"/>';
			
			return $str;
			
			break;
		
	}
	
}

function renderOrderFields( $tmp, $res = false, $tar = false, $data = false, $js = false, $oid = false, $cid = false, $tid = false, $nav = false )
{
	global $database, $webuser;
	
	if ( $data && is_string( $data ) )
	{
		if ( $json = json_decode( $data ) )
		{
			$data = $json;
		}
	}
	
	$value = $resource = false;
	
	$tmp->rName = ( strstr( $tmp->rName, ',' ) ? explode( ',', $tmp->rName ) : $tmp->rName );
	$tmp->tName = ( strstr( $tmp->tName, ',' ) ? explode( ',', $tmp->tName ) : $tmp->tName );
	
	if ( ( isset( $tar->{$tmp->tName} ) && $tar->{$tmp->tName} ) || ( isset( $tar->{$tmp->tName[0]} ) && $tar->{$tmp->tName[0]} ) )
	{
		if ( is_array( $tmp->tName ) )
		{
			$value = array();
			
			foreach( $tmp->tName as $tn )
			{
				$value[] = ( isset( $tar->{$tn} ) ? $tar->{$tn} : '' );
			}
		}
		else
		{
			$value = $tar->{$tmp->tName};
		}
	}
	else if ( $tmp->Resource != $tmp->Target && ( ( isset( $res->{$tmp->rName} ) && $res->{$tmp->rName} ) || ( isset( $res->{$tmp->rName[0]} ) && $res->{$tmp->rName[0]} ) ) )
	{
		if ( is_array( $tmp->rName ) )
		{
			$resource = array();
			
			foreach( $tmp->rName as $rn )
			{
				$resource[] = ( isset( $res->{$rn} ) ? $res->{$rn} : '' );
			}
		}
		else
		{
			$resource = $res->{$tmp->rName};
		}
	}
	else
	{
		$allowed = array( 'date' );
		
		if ( $tmp->Value && strstr( $tmp->Value, 'function:' ) )
		{
			$func = explode( 'function:', $tmp->Value );
			
			if ( isset( $func[1] ) )
			{
				$vars = false;
				
				if ( strstr( $func[1], '(' ) && strstr( $func[1], ')' ) )
				{
					$bracks = explode( '(', $func[1] );
					$vars = explode( ')', $bracks[1] );
					
					//$vars = explode( ',', $vars[0] );
					$vars = $vars[0];
					
					if ( $vars && ( $vars[0] == '"' || $vars[0] == "'" ) )
					{
						$vars = substr( $vars, 1, -1 );
					}
				}
				
				foreach ( $allowed as $k=>$v )
				{
					if ( substr( $func[1], 0, strlen( $v ) ) == $v )
					{
						$value = call_user_func( ( isset( $bracks[0] ) ? $bracks[0] : $func[1] ), ( $vars ? $vars : '' ) );
					}
				}
			}
		}
		else
		{
			if ( is_array( $tmp->tName ) )
			{
				$value = explode( ',', $tmp->Value );
			}
			else
			{
				$value = $tmp->Value;
			}
		}
	}
	
	// --- Render function participants -------------------------------------------------------------------------------------------------------------
	
	if ( strstr( $tmp->Function, 'participants' ) )
	{
		$js = array(
			'button1'=>'onclick="OrderMoveSelectOption(this)"',
			'button2'=>'onclick="OrderMoveSelectOption(this,1)"'/*,
			'option1'=>'onclick="OrderMarkSelectOption(this)"',
			'option2'=>'onclick="OrderMarkSelectOption(this)"'*/
		);
		
		if ( $parts = $database->fetchObjectRows( $q = '
			SELECT 
				r.*, c.ID AS ContactID 
			FROM 
				' . $tmp->Resource . ' r, 
				SBookContact c 
			WHERE 
					r.' . $tmp->rName . ' = \'' . $cid . '\' 
				AND r.ObjectType = "Users" 
				AND r.ObjectID > 0 
				AND c.UserID = r.ObjectID 
			ORDER BY 
				r.ID ASC 
		' ) )
		{
			$data = array(); $usrs = array();
			
			foreach ( $parts as $k=>$v )
			{
				$usrs[$v->ContactID] = $v->ContactID;
				//$data[$v->ContactID] = GetUserDisplayname( $v->ContactID );
			}
			
			$data = GetUserDisplayname( $usrs );
			
			//$value = ( $value ? explode( ',', $value ) : false );
			
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $resource ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
		else
		{
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $resource ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
	}
	
	// --- Render function structuremanager -------------------------------------------------------------------------------------------------------------
	
	if ( strstr( $tmp->Function, 'structuremanager' ) )
	{
		if ( $group = $database->fetchObjectRow( '
			SELECT 
				c.* 
			FROM 
				SBookCategory c 
			WHERE
				c.ID = \'' . $cid . '\' 
		' ) )
		{
			$group->Settings = json_obj_decode( $group->Settings );
			
			// Parent Settings
			if ( $group->ParentID > 0 && ( $pset = $database->fetchObjectRow( '
				SELECT 
					*
				FROM 
					SBookCategory
				WHERE
						ID = \'' . $group->ParentID . '\' 
					AND Type = "SubGroup" 
					AND IsSystem = "0" 
					AND ParentID = "0" 
			' ) ) )
			{
				$pset->Settings = json_obj_decode( $pset->Settings );
				
				if ( isset( $pset->Settings->AccessLevels ) )
				{
					$group->Settings->AccessLevels = $pset->Settings->AccessLevels;
				}
			}
			
			if ( is_object( $group->Settings ) && ( !isset( $group->Settings->AccessLevels[0] ) || !$group->Settings->AccessLevels[0]->ID && !$group->Settings->AccessLevels[0]->Name ) )
			{
				$group->Settings->AccessLevels = json_obj_decode( '[{"ID":"n","Name":"Owner","Display":"@","r":"1","w":"1","d":"1","a":"1","o":"1"},{"ID":"o","Name":"Admin","Display":"@","r":"1","w":"1","d":"1","a":"1","o":"0"},{"ID":"v","Name":"Moderator","Display":"+","r":"1","w":"1","d":"0","a":"1","o":"0"},{"ID":"i","Name":"Member","Display":"","r":"1","w":"1","d":"1","a":"0","o":"0"}]' );
			}
		}
		
		if ( $parts = $database->fetchObjectRows( $q = '
			SELECT 
				r.*, 
				c.ID AS ContactID,
				a.MemberID,
				a.Access,
				a.Read,
				a.Write,
				a.Delete,
				a.Admin,
				a.Owner
			FROM 
				' . $tmp->Resource . ' r,  
				SBookContact c,
				SBookCategoryAccess a 
			WHERE 
					r.' . $tmp->rName . ' = \'' . $cid . '\' 
				AND r.ObjectType = "Users" 
				AND r.ObjectID > 0
				AND c.UserID = r.ObjectID
				AND a.CategoryID = r.CategoryID
				AND a.ContactID = c.ID 
			ORDER BY 
				r.ID ASC 
		' ) )
		{
			$data = new stdClass(); $usrs = array(); $levels = array(); $members = array( '-'=>'---' );
			
			if ( $group->Settings->AccessLevels )
			{
				foreach( $group->Settings->AccessLevels as $lvl )
				{
					if ( $lvl->ID && $lvl->Name )
					{
						$levels[$lvl->ID] = $lvl->Name;
					}
				}
			}
			
			foreach ( $parts as $k=>$v )
			{
				//$members[$v->ContactID] = GetUserDisplayname( $v->ContactID );
				$usrs[$v->ContactID] = $v->ContactID;
			}
			
			$usrs = GetUserDisplayname( $usrs );
			
			$members = array_replace( $members, $usrs );
			
			if ( $levels )
			{
				$data->levels = $levels;
			}
			
			if ( $members )
			{
				$data->members = $members;
			}
			
			$arr = array(); $cat = array();
			
			$levels = array_reverse( $levels );
			
			$ii = 0; $uid = array();
			
			// TODO: Only list those added to the project hierarchy
			
			// TODO: Get saved data from data col in order
			
			$parts = false;
			
			if ( isset( $tar->Data ) && $tar->Data )
			{
				$array = json_decode( $tar->Data );
				
				if ( $array )
				{
					foreach ( $array as $key=>$val )
					{
						$obj = array();
						
						$parts = explode( ',', $val );
						
						if ( $parts )
						{
							foreach ( $parts as $v )
							{	
								$obj[$v] = $arr;
								
								$uid[$key] = ( $uid[$key] ? ( $uid[$key] . ',' . $v ) : $v );
							}
						}
						
						if ( !$obj )
						{
							$uid[$key] = '';
						}
						
						if ( $obj || !$parts )
						{
							$obj['-'] = array();
							
							$arr = array( $key=>$obj );
						}
						else
						{
							$obj['-'] = array();
						}
						
						$cat[$key] = $obj;
					}
				}
			}
			else
			{
				foreach ( $levels as $key=>$lvl )
				{
					$obj = array();
					
					if ( $parts )
					{
						foreach ( $parts as $v )
						{
							if ( $v->Access == $key )
							{
								$obj[$v->ContactID] = $arr;
								
								$uid[$key] = ( $uid[$key] ? ( $uid[$key] . ',' . $v->ContactID ) : $v->ContactID );
							}
							else if ( $ii == 0 && !$v->Access )
							{
								$obj[$v->ContactID] = $arr;
								
								$uid[$key] = ( $uid[$key] ? ( $uid[$key] . ',' . $v->ContactID ) : $v->ContactID );
							}
						}
					}
					
					if ( !$obj )
					{
						$uid[$key] = '';
					}
					
					if ( $obj || !$parts )
					{
						$obj['-'] = array();
						
						$arr = array( $key=>$obj );
					}
					else
					{
						$obj['-'] = array();
					}
					
					$cat[$key] = $obj;
					
					$ii++;
				}
			}
			
			// TODO: Send the data as a json string without the editor option and get latest from order data col and loop through it adding editor for output.
			
			//die( print_r( $res,1 ) . ' -- ' );
			
			$cat = array_reverse( $cat );
			
			if ( $arr )
			{
				$data->hierarchy = $cat;
			}
			
			if ( $uid )
			{
				$data->participants = $uid;
			}
			
			return '<div id="structuremanager_' . ( $tar->ID ? $tar->ID : '0' ) . '">' . renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $resource ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, 'onchange="UpdateStructureManager(\''.( $tar->ID ? $tar->ID : '0' ).'\',this)"', $tmp->Mode ) . '</div>';
		}
		else
		{
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $resource ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
	}
	
	// --- Render function hours -------------------------------------------------------------------------------------------------------------
	
	if ( strstr( $tmp->Function, 'hours' ) )
	{
		$js = array( $js );
		$mode = explode( ',', $tmp->Mode );
		
		return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $resource ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, false, $mode );
	}
	
	// --- Render function count -------------------------------------------------------------------------------------------------------------------
	
	else if ( strstr( $tmp->Function, 'count' ) )
	{
		if ( $count = $database->fetchObjectRow( '
			SELECT 
				COUNT(' . $tmp->rName . ') AS Amount
			FROM 
				' . $tmp->Resource . ' 
			WHERE 
					`OrderID` = \'' . $tar->ID . '\' 
				AND `IsDeleted` = "0" 
			ORDER BY 
				ID ASC 
		' ) )
		{
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, $count->Amount, i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
		else
		{
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $resource ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
	}
	
	// --- Render function sum -------------------------------------------------------------------------------------------------------------------
	
	else if ( strstr( $tmp->Function, 'sum' ) )
	{
		if ( $sum = $database->fetchObjectRow( '
			SELECT 
				SUM(' . $tmp->rName . ') AS Total 
			FROM 
				' . $tmp->Resource . ' 
			WHERE 
					`OrderID` = \'' . $tar->ID . '\' 
				AND `IsDeleted` = "0" 
			ORDER BY 
				ID ASC 
		' ) )
		{
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, $sum->Total, i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
		else
		{
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $resource ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
	}
	
	// --- Render function sumtime ------------------------------------------------------------------------------------------------------------------
	
	else if ( strstr( $tmp->Function, 'sumtime' ) )
	{
		if ( strstr( $tmp->rName, ',' ) && ( $diff = $database->fetchObjectRow( $q = '
			SELECT
				TIMESTAMPDIFF(HOUR, ' . $tmp->rName . ') AS Hours 
			FROM 
				' . $tmp->Resource . ' 
			WHERE 
					`ID` = \'' . $tar->ID . '\' 
				AND `IsDeleted` = "0" 
			ORDER BY 
				ID ASC 
		' ) ) )
		{
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $diff->Hours * $tar->{$tmp->tName} ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
		else
		{
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $resource ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
	}
	
	// --- Render function time ---------------------------------------------------------------------------------------------------------------------
	
	else if ( strstr( $tmp->Function, 'time' ) )
	{
		if ( strstr( $tmp->rName, ',' ) && ( $diff = $database->fetchObjectRow( $q = '
			SELECT
				TIMESTAMPDIFF(HOUR, ' . $tmp->rName . ') AS Hours 
			FROM 
				' . $tmp->Resource . ' 
			WHERE 
					`ID` = \'' . $tar->ID . '\' 
				AND `IsDeleted` = "0" 
			ORDER BY 
				ID ASC 
		' ) ) )
		{
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $diff->Hours . '.00' ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
		else
		{
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $resource ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
	}
	
	// --- Render function calendar ----------------------------------------------------------------------------------------------------------------
	
	else if ( strstr( $tmp->Function, 'calendar' ) )
	{
		return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $resource ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, 'onclick="OrderCalendar(this)"', $tmp->Mode );
	}
	
	// --- Render function template -----------------------------------------------------------------------------------------------------------------
	
	else if ( strstr( $tmp->Function, 'template' ) )
	{
		return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $tid ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
	}
	
	// --- Render function category -----------------------------------------------------------------------------------------------------------------
	
	else if ( strstr( $tmp->Function, 'category' ) )
	{
		return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $cid ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
	}
	
	// --- Render function status -------------------------------------------------------------------------------------------------------------------
	
	else if ( strstr( $tmp->Function, 'status' ) )
	{
		return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $nav ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
	}
	
	// --- Render function webuser -------------------------------------------------------------------------------------------------------------------
	
	else if ( strstr( $tmp->Function, 'webuser' ) )
	{
		if ( $parts = $database->fetchObjectRows( $q = '
			SELECT 
				r.*, c.ID AS ContactID 
			FROM 
				' . $tmp->Resource . ' r, 
				SBookContact c 
			WHERE 
					r.' . $tmp->rName . ' = \'' . $cid . '\' 
				AND r.ObjectType = "Users" 
				AND r.ObjectID > 0 
				AND c.UserID = r.ObjectID 
			ORDER BY 
				r.ID ASC 
		' ) )
		{
			$data = array(); $usrs = array();
			
			foreach ( $parts as $k=>$v )
			{
				//$data[$v->ContactID] = GetUserDisplayname( $v->ContactID );
				$usrs[$v->ContactID] = $v->ContactID;
			}
			
			$data = GetUserDisplayname( $usrs );
			
			$value = ( $value ? explode( ',', $value ) : false );
			
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $webuser->ContactID ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
		else
		{
			return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $webuser->ContactID ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
		}
	}
	
	// --- Else default ------------------------------------------------------------------------------------------------------------------------------
	
	else
	{
		return renderHtmlFields( $tmp->Element, $tmp->Type, $tmp->tName, ( $value ? $value : $resource ), i18n( $tmp->Placeholder ), i18n( $tmp->Title ), $data, $js, $tmp->Mode );
	}
	
	return false;
}

function is_datetime( $x, $f = false )
{
    if ( !strstr( $x, '-' ) && !strstr( $x, ':' ) && strlen( $x ) == 10 && strtotime( date( ( $f ? $f : 'Y-m-d H:i:s' ), $x ) ) == $x )
	{
		return true;
	}
	
	return false;
}

function checkOrderAccess( $cid, $data )
{
	global $webuser;
	
	if ( IsSystemAdmin() )
	{
		return true;
	}
	
	if ( $cid && $data && $webuser->ContactID )
	{
		$Contact = false; $User = false;
		
		if ( is_string( $data ) )
		{
			$data = json_obj_decode( $data );
		}
		
		$i = 1;
		
		if ( $data )
		{
			foreach ( $data as $k=>$v )
			{
				if ( $v && strstr( ( ','.(string)$v.',' ), ( ','.(string)$webuser->ContactID.',' ) ) )
				{
					$User = $i;
				}
				
				if ( $v && strstr( ( ','.(string)$v.',' ), ( ','.(string)$cid.',' ) ) )
				{
					$Contact = $i;
				}
				
				$i++;
			}
			
			if ( $User && $Contact && ( $webuser->ContactID == $cid && $User >= $Contact || $User > $Contact ) )
			{
				return true;
			}
		}
	}
	
	return false;
}

function renderOrderCalendar( $date )
{
	return renderOrderWebCalendar( $date );
	
	switch( UserAgent() )
	{
		case 'web':
			return renderOrderWebCalendar( $date );
			break;
		
		default:
			return renderOrderMobileCalendar( $date );
			break;
	}
}

function renderOrderWebCalendar( $date, $events = false )
{
	$year = date( 'Y', strtotime( $date ) );
	$month = date( 'm', strtotime( $date ) );
	$day = date( 'd', strtotime( $date ) );
	$week = date( 'W', strtotime( $date ) );
	
	$nstr = '<tr class="nv"><th colspan="8">';
	$nstr .= '<div class="nav">';
	$nstr .= '<span class="Left"><a href="javascript:void(0)" class="prevYear" title="' . i18n ( 'i18n_Previous_Year' ) . '" onclick="RefreshOrderCalendar(\'' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) ), date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) )-1 ) . '\',this)"> << </a>';
	$nstr .= '<a href="javascript:void(0)" class="prevMonth" title="' . i18n ( 'i18n_Previous_Month' ) . '" onclick="RefreshOrderCalendar(\'' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) )-1, date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) ) ) . '\',this)"> < </a></span>';
	$nstr .= '<span class="Center"><a href="javascript:void(0)" title="' . i18n ( 'i18n_Todays_Date' ) . '" onclick="RefreshOrderCalendar(\'' . mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) ) . '\',this)">' . i18n ( 'i18n_' . date ( 'F', strtotime ( $year . '-' . $month ) ) ) . ', ' . $year . '</a></span>';
	$nstr .= '<span class="Right"><a href="javascript:void(0)" class="nextMonth" title="' . i18n ( 'i18n_Next_Month' ) . '" onclick="RefreshOrderCalendar(\'' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) )+1, date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) ) ) . '\',this)"> > </a>';
	$nstr .= '<a href="javascript:void(0)" class="nextYear" title="' . i18n ( 'i18n_Next_Year' ) . '" onclick="RefreshOrderCalendar(\'' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) ), date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) )+1 ) . '\',this)"> >> </a> </span>';
	$nstr .= '</div>';
	$nstr .= '</th></tr>';
	
	$o = new CalendarMonth( $date = ( $year . '-' . $month . '-' . $day ) );
	
	if ( count ( $events ) && is_array ( $events ) )
	{
		$o->ImportEvents ( $events, 'DateStart', 'DateEnd' );
	}
	
	$i = 0; $sw = 2; $s = 0; $y = 0; $evt = '';
	$str = '<table class="CalendarMonth"><tbody>' . $nstr;
	foreach( $o->weeks as $key=>$w )
	{	
		$x = 0;
		$ch = 1 + $s;
		$sw = ( $sw == '1' ? '2' : '1' );
		$hstr = '<tr class="hd">';
		$dstr = '<tr class="sw' . $sw . '">';
		foreach( $w->days as $k=>$d )
		{
			$ch = ( $ch + 1 ) % 2; $evnt = '';
			
			$ct = strtotime( $d->date );
			
			$cw = new DateTime();
			$cw->setISODate( date( 'Y', $ct ), date( 'W', $ct ), 1 );
			
			if( $x == 0 )
			{	
				$hstr .= '<th><div class="Week"><div class="heading">' . i18n ( 'i18n_s_Week' ) . '</div></div></th>';
				$dstr .= '<td><div class="Week"><div class="number">' . $d->week . '</div></div></td>';
			}
			
			if( $i == 0 )
			{
				$hstr .= '<th><div class="' . $d->name . '"><div class="number">' . i18n ( 'i18n_s_' . $d->name ) . '</div></div></th>';
			}
			
			if( date( 'Y-m-d', strtotime( $d->date ) ) == $date ) $sel = ' Selected'; else $sel = '';
			
			$slot = ( $d->events && $d->events[0]->Reserved ? ' Reserved' : false );
			
			$dstr .= ( $d->month == $o->month ? '<td class="ch' . ( $ch + 1 ) . '"><div class="' . $d->name . ( $slot ? $slot : ' Available' ) . $sel . '" ' . ( !$slot ? 'onclick="SetOrderDate(\'' . $ct . '\',\'' . i18n('i18n_day_'.date('d',$ct)) . '\',\'' . ( i18n( 'i18n_s_' . date( 'D', $ct ) ) . date( ', d.m.Y', $ct ) ) . '\',this)"' : '' ) . '><div class="events">' . $evnt . '</div><div class="number">' . $d->day . '</div></div></td>' : '<td class="ch' . ( $ch + 1 ) . '"><div class="Space" ' . ( !$slot ? 'onclick="SetOrderDate(\'' . $ct . '\',\'' . i18n('i18n_day_'.date('d',$ct)) . '\',\'' . ( i18n( 'i18n_s_' . date( 'D', $ct ) ) . date( ', d.m.Y', $ct ) ) . '\',this)"' : '' ) . '><div class="number">' . $d->day . '</div></div></td>' );
			
			$x++;
		}
		if( $i == 0 && $hstr ) $str .= $hstr . '</tr>';
		if( $dstr ) $str .= $dstr . '</tr>';
		$s = ( $s + 1 ) % 2;
		$i++; $y++;
	}
	$str .= '</tbody></table>';
	return $str;
}

function renderOrderMobileCalendar( $date, $events = false )
{
	$year = date( 'Y', strtotime( $date ) );
	$month = date( 'm', strtotime( $date ) );
	$day = date( 'd', strtotime( $date ) );
	$week = date( 'W', strtotime( $date ) );
	
	$nstr .= '<div class="nav"><div><div><div>';
	$nstr .= '<span class="Left"><a href="javascript:void(0)" class="prevYear" title="' . i18n ( 'i18n_Previous_Year' ) . '" onclick="RefreshOrderCalendar(\'' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) ), date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) )-1 ) . '\',this)"> << </a>';
	$nstr .= '<a href="javascript:void(0)" class="prevMonth" title="' . i18n ( 'i18n_Previous_Month' ) . '" onclick="RefreshOrderCalendar(\'' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) )-1, date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) ) ) . '\',this)"> < </a></span>';
	$nstr .= '<span class="Center"><a href="javascript:void(0)" title="' . i18n ( 'i18n_Todays_Date' ) . '" onclick="RefreshOrderCalendar(\'' . mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) ) . '\',this)">' . i18n ( 'i18n_' . date ( 'F', strtotime ( $year . '-' . $month ) ) ) . ', ' . $year . '</a></span>';
	$nstr .= '<span class="Right"><a href="javascript:void(0)" class="nextMonth" title="' . i18n ( 'i18n_Next_Month' ) . '" onclick="RefreshOrderCalendar(\'' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) )+1, date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) ) ) . '\',this)"> > </a>';
	$nstr .= '<a href="javascript:void(0)" class="nextYear" title="' . i18n ( 'i18n_Next_Year' ) . '" onclick="RefreshOrderCalendar(\'' . mktime( 0, 0, 0, date( 'm', strtotime( $date ) ), date( 'd', strtotime( $date ) ), date( 'Y', strtotime( $date ) )+1 ) . '\',this)"> >> </a> </span>';
	$nstr .= '</div></div></div></div>';
	
	$o = new CalendarMonth( $date = ( $year . '-' . $month . '-' . $day ) );
	
	if ( count ( $events ) && is_array ( $events ) )
	{
		$o->ImportEvents ( $events, 'DateStart', 'DateEnd' );
	}
	
	$i = 0; $sw = 2; $s = 0; $y = 0; $evt = '';
	$str = '<div class="CalendarMonth mobile">' . $nstr . '<div class="datelist"><div><div>';
	foreach( $o->weeks as $key=>$w )
	{	
		$x = 0;
		$ch = 1 + $s;
		$sw = ( $sw == 1 ? 2 : 1 );
		
		foreach( $w->days as $k=>$d )
		{
			$ch = ( $ch + 1 ) % 2; $evnt = '';
			
			$ct = strtotime( $d->date );
			
			$cw = new DateTime();
			$cw->setISODate( date( 'Y', $ct ), date( 'W', $ct ), 1 );
			
			if( date( 'Y-m-d', strtotime( $d->date ) ) == $date ) $sel = ' Selected'; else $sel = '';
			
			$slot = ( $d->events && $d->events[0]->Reserved ? ' Reserved' : false );
			
			$dstr .= ( $d->month == $o->month ? '<div class="' . $d->name . ( $slot ? $slot : ' Available' ) . $sel . '" ' . ( !$slot ? 'onclick="SetOrderDate(\'' . $ct . '\',\'' . i18n('i18n_day_'.date('d',$ct)) . '\',\'' . ( i18n( 'i18n_s_' . date( 'D', $ct ) ) . date( ', d.m.Y', $ct ) ) . '\',this)"' : '' ) . '><div class="number">' . date( 'D, d.m.Y', $ct ) . '</div></div>' : '<div class="Space" ' . ( !$slot ? 'onclick="SetOrderDate(\'' . $ct . '\',\'' . i18n('i18n_day_'.date('d',$ct)) . '\',\'' . ( i18n( 'i18n_s_' . date( 'D', $ct ) ) . date( ', d.m.Y', $ct ) ) . '\',this)"' : '' ) . '><div class="number">' . ( i18n( 'i18n_s_' . date( 'D', $ct ) ) . date( ', d.m.Y', $ct ) ) . '</div></div>' );
			
			$x++;
		}
		$s = ( $s + 1 ) % 2;
		$i++; $y++;
	}
	if( $dstr ) $str .= $dstr;
	$str .= '</div></div></div></div>';
	return $str;
}

?>
