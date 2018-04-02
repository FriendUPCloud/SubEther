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

$str = ''; $astr = '';

$str .= '<div id="Tabs">';			
if ( $parent->tabs )
{
    include ( $cbase . '/functions/general.php' );
    $i = 0;
    $astr .= '<h2>' . i18n( 'i18n_General Account Settings' ) . '</h2>';
    $astr .= '<ul>';
    foreach( $parent->tabs as $key=>$val )
    {
        $edit = 'getAccount( \'account_' . $key . '\' )';
        $astr .= '<li>';
        $astr .= '<table>';
        $astr .= '<tr>';
        $astr .= '<td><div onclick="' . $edit . '">' . i18n( 'i18n_' . $val ) . '</div></td>';
        $astr .= '<td><div id="account_' . $key . '">' . $title[$key] . '</div></td>';
        $astr .= '<td><div onclick="' . $edit . '">' . i18n( 'i18n_Edit' ) . '</div></td>';
        $astr .= '</tr>';
        $astr .= '</table>';
        $astr .= '</li>';
        $i++;
    }
    $astr .= '</ul>';
    $str .= $astr;
}
$str .= '</div>';

if ( isset( $_REQUEST['function'] ) )
{
    die( 'ok<!--separate-->' . $astr );
}

?>
