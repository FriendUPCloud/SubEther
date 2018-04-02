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

global $document, $webuser;

statistics( $parent->module, 'panel' );

$root = 'subether/';
$cbase = 'subether/components/panel';

include_once ( $root . '/components/notification/include/functions.php' );

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/panel.css' );
$document->addResource ( 'javascript', $root . '/components/groups/javascript/groups.js' );
$document->addResource ( 'javascript', $root . '/components/members/javascript/component.js' );

$folder = $parent->folder;
$panel = $parent->panel;
$module = $parent->module;
$url = $parent->url;
$user = $parent->webuser;
$path = $parent->path;
$mpath = $parent->mpath;

include ( $cbase . '/functions/panel.php' );

$Component->ProfileLink = $pstr;
$Component->ListPanel = $str;

statistics( $parent->module, 'panel' );

?>
