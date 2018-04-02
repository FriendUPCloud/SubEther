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

function Validation ( $str )
{
    global $webuser;
    if( !$str || !$webuser ) return false;
    if( $webuser->Password == md5( trim( $str ) ) )
    {
        return true;
    }
    return false;
}

function FindUserName ( $name )
{
    if( !$name ) return false;
    $u = new dbObject( 'SBookContact' );
    $u->Username = trim( $name );
    if( $u->Load() )
    {
        return true;
    }
    return false;
}

function ObjectToString ( $obj )
{
    if( !$obj ) return;
    
    return json_encode( $obj );
    
    /*$str = ' Object (';
    foreach( $obj as $field=>$o )
    {
        $str .= ' [' . $field . '] => ';
        if( is_array( $o ) )
        {
            $str .= 'Array (';
            foreach( $o as $key=>$a )
            {
                $str .= ' [' . $key . '] => ';
                $str .= $a;
            }
            $str .= ' )';
        }
        else
        {
            $str .= $o;
        }
    }
    $str .= ' )';
    
    return $str;*/
}

function StringToObject ( $str )
{
    if( !$str ) return;
    
    return json_decode( $str );
    
    /*$string = preg_match_all( '/\(([^)]+)\)/', $str, $m );
    //$string = str_replace( array( '(', ')' ), '|', $str );
    //$string = explode( '|', $string );
    die( $str . ' ..' . print_r( $m,1 ) );
    $obj = new stdClass();
    if( substr( $str, 0, 10 ) == ' Object ( ' )
    {
        $test = explode( '] => ', $str );
        die( $str . ' ..' . print_r( $test,1 ) );
        die( print_r( explode( ' => ', $test[1] ),1 ) . ' ..' );
        $str = str_replace( ' Object ( ', '', $str );
        $fields = explode( ' => ', $str );
        foreach( $fields as $k=>$field )
        {
            //die( $field . ' --' );
            if( substr( $field, 0, 1 ) != '[' ) continue;
            $fld = str_replace( ']', '', str_replace( '[', '', $field ) );
            $value = $fields[$k+1];
            if( substr( $value, 0, 8 ) == 'Array ( ' )
            {
                $value = array();
                $str = str_replace( ( $field . ' => Array (' ), '', $str );
                //die( ' -- ' . $str );
                //$arrays = explode( ' => ', $str );
                $arrays = explode( ' [', $str );
                die( print_r( $arrays,1 ) . ' ..' );
                foreach( $arrays as $key=>$array )
                {
                    if( substr( $array, 0, 1 ) != '[' ) continue;
                    $arr = str_replace( ']', '', str_replace( '[', '', $array ) );
                    $value[$arr] = $arrays[$key+1];
                }
            }
            $obj->$fld = $value;
        }
        die( print_r( $fields,1 ) . ' -- ' . print_r( $obj,1 ) . ' .. ' . $str );
    }
    die( 'fail' );*/
}

?>
