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

global $database;

if( isset( $_POST['plugin'] ) )
{
	$plugin = true;
}

$str = '<div class="case"><table>';

$str .= '<tr>';
$str .= '<th class="col1"><span>ID</span></th>';
$str .= '<th class="col2"><span>Name</span></th>';
$str .= '<th class="col3"><span>Progress</span></th>';
$str .= '</tr>';

if( $content = $database->fetchObjectRows( 'SELECT * FROM SBookCaseList WHERE CategoryID = \'' . $parent->folder->CategoryID . '\' ORDER BY ID ASC' ) )
{
	foreach( $content as $cnt )
	{
		// --- Case ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		if( $cnt->Type != 'Case' ) continue;
		$cnt->Progress = floor ( (float) $cnt->Progress ) . '%';
		$str .= '<tr id="CaseID_' . $cnt->ID . '" class="sw' . $sw = ( $sw == 2 ? 1 : 2 ) . '">';
		$str .= '<td class="col1" onclick="OpenTask(\'' . $cnt->ID . '\',this)"><span class="image"><img src="subether/components/admin/gfx/arrow_closed.png"/></span><span class="num"> #' . $cnt->ID . '</span></td>';
		$str .= '<td class="col2"><div class="edit">';
		if( !isset( $plugin ) )
		{
			//$str .= '<img title="Create New Task" onclick="createNewTask( ' . $cnt->ID . ' )" class="Icon" src="admin/gfx/icons/page_add.png">';
			$str .= '<span class="EditCase" title="Edit Case" onclick="SaveCase( ' . $cnt->ID . ' )"><img title="Edit Case" class="Icon" onclick="SaveCase( ' . $cnt->ID . ' )" src="admin/gfx/icons/folder_edit.png"></span>';
			$str .= '<span class="NewCase" title="Create New Case" onclick="createNewCase()"><img title="Create New Case" class="Icon" onclick="createNewCase()" src="admin/gfx/icons/folder_add.png"></span>';
			$str .= '<span class="DeleteCase" title="Delete Case" onclick="DeleteCase( ' . $cnt->ID . ' )"><img title="Delete Case" onclick="DeleteCase( ' . $cnt->ID . ' )" class="Icon" src="admin/gfx/icons/folder_delete.png"></span>';
		}
		$str .= '</div><div id="CaseNameID_' . $cnt->ID . '" onclick="InitEditMode( \'' . $cnt->ID . '\', \'case\', this )">' . $cnt->Name . '</div></td>';
		$str .= '<td class="col3"><span class="ProgressNum">' . $cnt->Progress . '</span><div class="ratebar"><div style="width: ' . ( $cnt->Progress ? $cnt->Progress : '0%' ) . ';"></div></div></td>';
		$str .= '</tr>';
	
		$str .= '<tr class="tasks"><td colspan="3"><div id="TaskPID_' . $cnt->ID . '" class="task' . ( $_POST['pid'] == $cnt->ID ? ' open' : '' ) . '">';
		//$str .= '<div id="CaseContentID_' . $cnt->ID . '" class="textarea" contenteditable="true" onkeyup="checkStr(this,event);IsEditing(\'CaseNameID_' . $cnt->ID . '\')">' . $cnt->Description . '</div>';
		$str .= '<table>';
		
		foreach( $content as $tsk )
		{
			// --- Task --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
			if( $tsk->Type != 'Task' ) continue;
			else if( $tsk->CaseID == $cnt->ID )
			{
				$str .= '<tr id="CaseID_' . $tsk->ID . '">';
				$str .= '<td class="col1"><span>#' . $tsk->ID . '</span></td>';
				$str .= '<td class="col2"><div class="edit">';
				$str .= '<span class="EditTask" title="Edit Task" onclick="SaveCase( \'' . $tsk->ID . '\', \'' . $cnt->ID . '\' )"><img class="EditTask" title="Edit Task" onclick="SaveCase( \'' . $tsk->ID . '\', \'' . $cnt->ID . '\' )" class="Icon" src="admin/gfx/icons/page_edit.png"></span>';
				if( !isset( $plugin ) )
				{
					//$str .= '<img title="Create New Task" onclick="createNewTask( ' . $cnt->ID . ' )" class="Icon" src="admin/gfx/icons/page_add.png">';
					$str .= '<span class="DeleteTask" title="Delete Task" onclick="DeleteCase( \'' . $tsk->ID . '\', \'' . $cnt->ID . '\' )"><img class="Icon" title="Delete Task" onclick="DeleteCase( \'' . $tsk->ID . '\', \'' . $cnt->ID . '\' )" src="admin/gfx/icons/page_delete.png">';
				}
				$str .= '</div><div onclick="InitEditMode( \'' . $tsk->ID . '\', \'task\', this, \'' . $cnt->ID . '\' )">' . $tsk->Name . '</div></td>';
				$str .= '<td class="col3"><input type="checkbox" ' . ( $tsk->Progress == '100%' ? 'checked="checked"' : '' ) . ' onclick="CompleteTask( \'' . $tsk->ID . '\', this, \'' . $cnt->ID . '\' )"/></td>';
				$str .= '</tr>';
			}
		}
		$str .= '<tr class="inputs"><td colspan="3"><div id="NewTaskPID_' . $cnt->ID . '" class="newtask"><table>';
		$str .= '<tr>';
		$str .= '<td class="col1"></td>';
		$str .= '<td class="col2"><input id="NewTask" type="text" placeholder="New Task..." onkeyup="if( event.keyCode == 13 ){ SaveCase( \'' . $cnt->ID . '\', \'Task\', this, \'newtask\', \'' . $cnt->ID . '\' ); }"/></td>';
		$str .= '<td class="col3"></td>';
		$str .= '</tr>';
		$str .= '</table></div></td></tr>';
		$str .= '</table></div></td></tr>';
	}
}
else
{
    $newcase = ' open';
}
$str .= '<tr class="inputs"><td colspan="3"><div id="NewCaseID" class="newcase' . $newcase . '"><table>';
$str .= '<tr>';
$str .= '<td class="col1"></td>';
if( !isset( $plugin ) )
{
	$str .= '<td class="col2"><input id="NewCase" type="text" placeholder="New Case..." onkeyup="if( event.keyCode == 13 ){ SaveCase( false, \'Case\', this, \'newcase\' ); }"/></td>';
}
$str .= '<td class="col3"></td>';
$str .= '</tr>';
$str .= '</table></div></td></tr>';
$str .= '</table></div>';

if( isset( $plugin ) && !$content )
{
	$str = '';
}

if( $_REQUEST[ 'function' ] == 'case' ) die( 'ok<!--separate-->' . $str );

?>
