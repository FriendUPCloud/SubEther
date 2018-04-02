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

$language = false; $display = false;

$u = new dbObject( 'SBookContact' );
$u->UserID = $webuser->ID;
if( $u->Load() )
{
    if ( isset( $u->Data ) )
    {
        if ( is_string( $u->Data ) )
        {
            $u->Data = json_obj_decode( $u->Data );
        }
    }
    
    $first = $u->Firstname ? ( $u->Firstname . ' ' ) : '';
    $middle = $u->Middlename ? ( $u->Middlename . ' ' ) : '';
    $last = $u->Lastname ? ( $u->Lastname . ' ' ) : '';
    
    // TODO: Fix this scheme in a clobal function (trim strtolower preg_replace combo)
    $u->Username = trim ( strtolower ( preg_replace ( '/[\s]+/', '.', $u->Username ) ) );
    
    if ( $lang = $database->fetchObjectRows( 'SELECT * FROM Languages ORDER BY ID ASC' ) )
    {
       
        foreach ( $lang as $obj )
        {
            //if ( isset( $GLOBALS[ "Session" ]->LanguageCode ) && $GLOBALS[ "Session" ]->LanguageCode == $obj->Name )
            if ( isset( $u->Data->LanguageCode ) && $u->Data->LanguageCode == $obj->Name )
            {
                $language = ( $obj->NativeName . ' (' . strtoupper( $obj->Name ) . ')' );
            }
        }
    }
    
    foreach ( array( 0=>'Default', 1=>'Mobile', 2=>'Presentation', 3=>'Tablet' ) as $key=>$val )
    {
        if ( isset( $u->Data->Display ) && $u->Data->Display == $key )
        {
            $display = i18n( 'i18n_' . $val );
        }
    }
    
    $title = array(
        'name'=>'<div onclick="getAccount( \'account_name\' )"><strong>' . trim( $first . $middle . $last ) . '</strong></div>',
        'username'=>'<div onclick="getAccount( \'account_username\' )">' . BASE_URL . '<strong>' . $u->Username . '</strong></div>',
        'email'=>'<div onclick="getAccount( \'account_email\' )">' . i18n( 'i18n_Primary' ) . ': <strong>' . $u->Email . '</strong></div>',
        'password'=>'<div onclick="getAccount( \'account_password\' )">' . i18n( 'i18n_Password last changed' ) . ' ' . TimeToHuman( $webuser->DateModified ) . '.</div>',
        'networks'=>'<div onclick="getAccount( \'account_networks\' )">' . i18n( 'i18n_No networks' ) . '.</div>',
        'language'=>'<div onclick="getAccount( \'account_language\' )"><strong>' . ( $language ? $language : 'English (EN)' ) . '</strong></div>',
        'themes'=>'<div onclick="getAccount( \'account_themes\' )"><strong>' . ( isset( ThemeData( $u->Theme )->Name ) ? ThemeData( $u->Theme )->Name : i18n( 'i18n_Default' ) ) . '</strong></div>',
        'display'=>'<div onclick="getAccount( \'account_display\' )"><strong>' . ( $display ? $display : i18n( 'i18n_Default' ) ) . '</strong></div>'
    );
}

?>
