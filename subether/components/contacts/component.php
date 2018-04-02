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

statistics( $parent->module, 'contacts' );

$root = 'subether/';
$cbase = 'subether/components/contacts';

i18nAddLocalePath ( $cbase . '/locale' );

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/contacts.css' );
$document->addResource ( 'javascript', $root . 'components/profile/javascript/profile.js' );

$cuser = $parent->cuser;

// Template: Module, Component, CategoryID, Status
SessionSet ( $parent->module, 'contacts', $parent->folder->CategoryID, 'online' );

include ( $cbase . '/functions/contacts.php' );

$Component->Content = $str;

statistics( $parent->module, 'contacts' );

?>
