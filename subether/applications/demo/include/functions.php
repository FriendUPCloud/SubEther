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

// Put these function into the api standard output functions used in the api ...

function output ( $data )
{
	$out = new stdClass();
	$out->response = 'ok';
	$out->items = new stdClass();
	$out->items->data = $data;
	
	die( json_encode( $out ) );
}

function failed ( $msg )
{
	$out = new stdClass();
	$out->response = 'failed';
	$out->info = $msg;
	
	die( json_encode( $out ) );
}

function ok ( $msg )
{
	$out = new stdClass();
	$out->response = 'ok';
	$out->info = $msg;
	
	die( json_encode( $out ) );
}

// Present these as examples for the demo app but find out how to handle database stuff globally in the api ...

function load_json_db_data (  )
{
	//if( $fp = file_get_contents ( dirname(__FILE__) . '/database.json' ) )
	if( file_exists( BASE_DIR . '/subether/upload/database.json' ) && ( $fp = file_get_contents ( BASE_DIR . '/subether/upload/database.json' ) ) )
	{
		$json = json_decode( $fp );
		
		if( !$json )
		{
			$json = new stdClass();
		}
		
		if( isset( $json->{$_SERVER['REMOTE_ADDR']} ) )
		{
			return $json->{$_SERVER['REMOTE_ADDR']};
		}
	}
	
	return false;
}

function save_json_db_data ( $data )
{
	//if( $data && ( $fp = fopen ( dirname(__FILE__) . '/database.json', 'w' ) ) )
	if( $data && ( $fp = fopen ( BASE_DIR . '/subether/upload/database.json', 'w' ) ) )
	{
		//$res = file_get_contents ( dirname(__FILE__) . '/database.json' );
		$res = file_get_contents ( BASE_DIR . '/subether/upload/database.json' );
		
		if( !$json = json_decode( $res ) )
		{
			$json = new stdClass();
		}
		
		if( $json )
		{
			$json->{$_SERVER['REMOTE_ADDR']} = new stdClass();
			
			$json->{$_SERVER['REMOTE_ADDR']}->data = $data;
			
			fwrite ( $fp, json_encode( $json ) );
		}
		
		fclose ( $fp );
		
		return $json;
	}
	
	return false;
}

function delete_json_db_data (  )
{
	//if( $fp = fopen ( dirname(__FILE__) . '/database.json', 'w' ) )
	if( file_exists( BASE_DIR . '/subether/upload/database.json' ) && ( $fp = fopen ( BASE_DIR . '/subether/upload/database.json', 'w' ) ) )
	{
		//$res = file_get_contents ( dirname(__FILE__) . '/database.json' );
		$res = file_get_contents ( BASE_DIR . '/subether/upload/database.json' );
		
		if( !$json = json_decode( $res ) )
		{
			$json = new stdClass();
		}
		
		$json->{$_SERVER['REMOTE_ADDR']} = new stdClass();
		
		$json->{$_SERVER['REMOTE_ADDR']}->data = '';
		
		fwrite ( $fp, json_encode( $json ) );
		fclose ( $fp );
		
		return true;
	}
	
	return false;
}

?>
