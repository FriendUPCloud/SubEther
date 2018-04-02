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

$status = array( 'Offer', 'Active', 'OnHold', 'Canceled', 'Finished', 'Archived' );

$str = '<div id="Case">';

$str .= '<div class="rows heading">';
$str .= '<span class="cols col1"><span>#</span></span>';
$str .= '<span class="cols col2"><span>Name</span></span>';
$str .= '<span class="cols col3"><span>Progress</span></span>';
$str .= '<div class="clearboth" style="clear:both;"></div>';
$str .= '</div>';

$str .= '<ul>';

if( $rows = $database->fetchObjectRows( '
	SELECT
		*
	FROM
		SBookCaseList
	WHERE
			CategoryID = \'' . $parent->folder->CategoryID . '\'
		AND Type IN ( "Case","Bug" )
		AND CaseID = "0" 
		AND UserID = "0" 
	ORDER BY
		Deadline ASC 
' ) )
{
	$i = 1;
	
	foreach( $rows as $r )
	{
		$str .= '<li class="sw' . $sw = ( $sw == 1 ? 2 : 1 ) . '">';
		$str .= '<div class="rows row1">';
		$str .= '<span class="cols col1"><span>[' . $i++ . ']</span></span>';
		$str .= '<span class="cols col2"><span>' . $r->Name . '</span></span>';
		$str .= '<span class="cols col3"><span>' . $r->Progress . '</span></span>';
		$str .= '<div class="clearboth" style="clear:both;"></div>';
		$str .= '</div>';
		$str .= '<div class="content row2 ' . ( $i == 2 ? '' : 'hidden' ) . '">';
		
		$str .= '<div class="info">';
		
		$str .= '<div class="leftbox">';
		$str .= '<div><span class="leftcol">Created:</span><span class="rightcol"><span><input type="text" name="DateCreated" value="' . $r->DateCreated . '"/></span></span><div class="clearboth" style="clear:both;"></div></div>';
		$str .= '<div><span class="leftcol">Deadline:</span><span class="rightcol"><span><input type="text" name="Deadline" value="' . $r->Deadline . '"/></span></span><div class="clearboth" style="clear:both;"></div></div>';
		$str .= '<div><span class="leftcol">OrderID:</span><span class="rightcol"><span><input type="text" value="' . $r->ID . '" readonly="true"/></span></span><div class="clearboth" style="clear:both;"></div></div>';
		$str .= '<div><span class="leftcol">Type:</span><span class="rightcol"><span><select name="Type"><option value="' . $r->Type . '">' . $r->Type . '</option></select></span></span><div class="clearboth" style="clear:both;"></div></div>';
		$str .= '<div><span class="leftcol">Assigned:</span><span class="rightcol"><span><select name="User"><option value="' . $r->UserID . '">' . ( $r->UserID == 0 ? 'available' : '' ) . '</option></select></span></span><div class="clearboth" style="clear:both;"></div></div>';
		$str .= '</div>';
		
		$str .= '<div class="rightbox">';
		$str .= '<div><span class="leftcol">Client:</span><span class="rightcol"><span><input type="text" name="ClientID" value="' . $r->ClientID . '"/></span></span><div class="clearboth" style="clear:both;"></div></div>';
		$str .= '<div><span class="leftcol">Project:</span><span class="rightcol"><span><input type="text" name="ProjectID" value="' . $r->ProjectID . '"/></span></span><div class="clearboth" style="clear:both;"></div></div>';
		$str .= '</div>';
		
		$str .= '<div class="bottombox">';
		$str .= '<div><span class="leftcol">Status:</span>';
		foreach( $status as $s )
		{
			$str .= '<span><input type="radio" name="Status" value="' . $s . '"/> ' . $s . '</span>';
		}
		$str .= '<div class="clearboth" style="clear:both;"></div>';
		$str .= '</div>';
		$str .= '</div>';
		$str .= '<div class="clearboth" style="clear:both;"></div>';
		
		$str .= '</div>';
		
		$str .= '<div class="textarea"><textarea name="Description">' . $r->Description . '</textarea></div>';
		
		$str .= '<div class="hours">';
		$str .= '<div class="head">';
		$str .= '<span class="subcols col1"><span>Date</span></span>';
		$str .= '<span class="subcols col2"><span>Start</span></span>';
		$str .= '<span class="subcols col3"><span>End</span></span>';
		$str .= '<span class="subcols col4"><span>Hours</span></span>';
		$str .= '<span class="subcols col5"><span>Description</span></span>';
		$str .= '<span class="subcols col6"><span>Entry</span></span>';
		$str .= '<span class="subcols col7"><span>Price</span></span>';
		$str .= '<div class="clearboth" style="clear:both;"></div>';
		$str .= '</div>';
		
		$str .= '<ul>';
		$str .= '<li>';
		$str .= '<div class="list">';
		$str .= '<span class="subcols col1"><span><input name="" type="text" value="' . date( 'Y-m-d' ) . '"/></span></span>';
		$str .= '<span class="subcols col2_1"><span><input type="checkbox"/></span></span>';
		$str .= '<span class="subcols col2_2"><span><input name="" type="text" value="' . date( 'H:i' ) . '"/></span></span>';
		$str .= '<span class="subcols col3_1"><span><input type="checkbox"/></span></span>';
		$str .= '<span class="subcols col3_2"><span><input name="" type="text" value="' . date( 'H:i' ) . '"/></span></span>';
		$str .= '<span class="subcols col4"><span><input name="" type="text" value="0.00"/></span></span>';
		$str .= '<span class="subcols col5"><span><input name="" type="text" value=""/></span></span>';
		$str .= '<span class="subcols col6"><span><input name="" type="text" value="S."/></span></span>';
		$str .= '<span class="subcols col7"><span><input name="" type="text" value=""/></span></span>';
		$str .= '<div class="clearboth" style="clear:both;"></div>';
		$str .= '</div>';
		$str .= '</li>';
		$str .= '</ul>';
		$str .= '</div>';
		
		$str .= '</div>';
		$str .= '</li>';
	}
}

$str .= '</ul>';

$str .= '</div>';

?>
