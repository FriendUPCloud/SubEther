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

// Control buttons

//$bstr  = '<div class="topcontrols">';
//$bstr .= '<select id="ThumbView" mid="' . $_POST['mid'] . '" onchange="refreshFilesDirectory(this.getAttribute(\'mid\'),false,this.value)">';
//$bstr .= '<option value="0" ' . ( $view == 0 ? 'selected="selected"' : '' ) . '>Icon View</option>';
//$bstr .= '<option value="1" ' . ( $view == 1 ? 'selected="selected"' : '' ) . '>List View</option></select>';
//$bstr .= '</div>';

$bstr  = '<div class="topcontrols">';
$bstr .= '<ul id="ThumbView" mid="' . $_POST['mid'] . '" ' . ( $view == 1 ? 'value="1"' : 'value="0"' ) . '>';
$bstr .= '<li value="0" ' . ( $view == 0 ? 'class="selected"' : '' ) . ' onclick="refreshFilesDirectory(this.parentNode.getAttribute(\'mid\'),false,\'0\')"><span>' . i18n( 'i18n_Icon View' ) . '</span></li>';
$bstr .= '<li value="1" ' . ( $view == 1 ? 'class="selected"' : '' ) . ' onclick="refreshFilesDirectory(this.parentNode.getAttribute(\'mid\'),false,\'1\')"><span>' . i18n( 'i18n_List View' ) . '</span></li>';
$bstr .= '</ul>';
$bstr .= '<div class="clearboth" style="clear:both"></div>';
$bstr .= '</div>';

?>
