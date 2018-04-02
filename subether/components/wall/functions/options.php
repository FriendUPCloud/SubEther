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

$str  = '<ul>';
if( NODE_DEV_MODE )
{
	$str .= '<li><div onclick="embedPostEditor(\'' . $_POST[ 'mid' ] . '\')"><span>' . i18n( 'i18n_Edit' ) . '</span></div></li>';
}
else
{
	$str .= '<li><div onclick="embedWallEditor(\'' . $_POST[ 'mid' ] . '\', \'' . $_POST[ 'cid' ] . '\')"><span>' . i18n( 'i18n_Edit' ) . '</span></div></li>';
}
$str .= '<li><div onclick="deleteWallContent(' . ( $_POST[ 'cid' ] ? $_POST[ 'cid' ] : $_POST[ 'mid' ] ) . ')"><span>' . i18n( 'i18n_Delete' ) . '</span></div></li>';
$str .= '</ul>';

die( 'ok<!--separate-->' . $str );

?>
