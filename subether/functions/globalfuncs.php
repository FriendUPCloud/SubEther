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

// Global Cache's
//$GLOBALS['CachedFileChecks'] = array ();
$_SESSION['CachedFileChecks'] = ( isset( $_SESSION['CachedFileChecks'] ) ? $_SESSION['CachedFileChecks'] : array () );

function setupNodeInfo()
{
	$info = ( file_exists( BASE_DIR . '/subether/info.txt' ) ? @file_get_contents( BASE_DIR . '/subether/info.txt' ) : false );
	
	if ( $info )
	{
		$info = explode( ',', $info );
		
		$name = $info[0];
		$indx = $info[1];
	}
	
	$version      = ( file_exists( BASE_DIR . '/subether/version.txt' )      ? @file_get_contents( BASE_DIR . '/subether/version.txt' )      : false );
	$verification = ( file_exists( BASE_DIR . '/subether/verification.txt' ) ? @file_get_contents( BASE_DIR . '/subether/verification.txt' ) : false );
	
	// Get name from subether/info.txt
	define ( 'NODE_INFO', isset( $name ) && $name ? trim( $name ) : 'Subether' );
	// Get index from subether/info.txt
	define ( 'NODE_INDEX', isset( $index ) && $index ? trim( $index ) : 'http://sub-ether.org/' );
	// Get version from subether/version.txt
	define ( 'NODE_VERSION', isset( $version ) && $version ? trim( $version ) : '1.0.00' );
	// Get version from subether/verification.txt
	define ( 'NODE_VERIFICATION', isset( $verification ) && $verification ? trim( $verification ) : '{DC65D301-0301-4CA7-B2E1-E6490BAB7F5E}' );
}

function setupMetaData( $title = false, $url = false, $image = false, $desc = false, $keys = false )
{
	global $document, $page;
	
	// TODO: Check language settings first.
	
	$document->sHeadData['charset'] 		= ( ( !isset( $document->sHeadData['charset'] ) ) 							? '		<meta charset="utf-8">' : $document->sHeadData['charset'] );
	
	$document->sHeadData['description'] 	= ( ( !isset( $document->sHeadData['description'] ) || $desc != false ) 	? '		<meta name="description" content="' . ( $desc != false ? $desc : 'The Decentralized Network' ) . '">' : $document->sHeadData['description'] );
	$document->sHeadData['keywords'] 	    = ( ( !isset( $document->sHeadData['keywords'] ) || $keys != false ) 	    ? '		<meta name="keywords" content="' . ( $keys != false ? $keys : 'Treeroot, Treeroot.org, SubEther, Sub-Ether.org, SubEther.org, Ether, Decentralized, Decentralized Network, Social Network, Social, Communications, Information, Knowledge, Trading, Services, Politics, Transport, Wall, Blogs, Encryption, Encrypted Chat, Chat, Nodes, Node Mesh, Node Network, Profiles, Groups, Messages, Calendar, Library, Cloud Storage, Freedom, Privacy, Open Source, {DC65D301-0301-4CA7-B2E1-E6490BAB7F5E}' ) . '">' : $document->sHeadData['keywords'] );
	$document->sHeadData['image']		    = ( ( !isset( $document->sHeadData['image'] ) || $image != false ) 		    ? '		<meta itemprop="image" content="' . ( $image != false ? $image : ( BASE_URL . 'upload/images-master/logo_black.png' ) ) . '">' : $document->sHeadData['image'] );
	
	$document->sHeadData['og:site_name'] 	= ( ( !isset( $document->sHeadData['og:site_name'] ) ) 						? '		<meta property="og:site_name" content="' . NODE_INFO . ' v' . NODE_VERSION . '">' : $document->sHeadData['og:site_name'] );
	$document->sHeadData['og:title'] 		= ( ( !isset( $document->sHeadData['og:title'] ) || $title != false )		? '		<meta property="og:title" content="' . ( $title != false ? $title : ( NODE_INFO . ' - ' . $page->Title ) ) . '">' : $document->sHeadData['og:title'] );
	$document->sHeadData['og:type'] 		= ( ( !isset( $document->sHeadData['og:type'] ) ) 							? '		<meta property="og:type" content="site">' : $document->sHeadData['og:type'] );
	$document->sHeadData['og:url'] 			= ( ( !isset( $document->sHeadData['og:url'] ) || $url != false ) 			? '		<meta property="og:url" content="' . ( $url != false ? $url : BASE_URL ) . '">' : $document->sHeadData['og:url'] );
	$document->sHeadData['og:image']		= ( ( !isset( $document->sHeadData['og:image'] ) || $image != false ) 		? '		<meta property="og:image" content="' . ( $image != false ? $image : ( BASE_URL . 'upload/images-master/logo_black.png' ) ) . '">' : $document->sHeadData['og:image'] );
	$document->sHeadData['og:locale'] 		= ( ( !isset( $document->sHeadData['og:locale'] ) ) 						? '		<meta property="og:locale" content="' . ( isset( $GLOBALS[ "Session" ]->LanguageCode ) ? ( $GLOBALS[ "Session" ]->LanguageCode . '_' . strtoupper( $GLOBALS[ "Session" ]->LanguageCode ) ) : 'en_us' ) . '">' : $document->sHeadData['og:locale'] );
	//$document->sHeadData['og:description'] 	= ( ( !isset( $document->sHeadData['og:description'] ) || $desc != false ) 	? '		<meta property="og:description" content="' . ( $desc != false ? $desc : 'The Decentralized Network' ) . '">' : $document->sHeadData['og:description'] );
}

if( !function_exists( 'json_obj_decode' ) )
{
	function json_obj_decode( $str, $type = false, $utf8 = false )
	{
		$obj = false;
		if ( $str != '' && is_string( $str ) && ( $obj = json_decode( $str ) ) )
		{
			if( $utf8 )
			{
				if( is_object( $obj ) )
				{
					foreach( $obj as $k=>$v )
					{
						if( is_string( $v ) )
						{
							$obj->{$k} = utf8_decode( $v );
							//$obj->{$k} = html_entity_decode( $v );
						}
					}
				}
				else if( is_array( $obj ) )
				{
					foreach( $obj as $k=>$v )
					{
						if( is_string( $v ) )
						{
							$obj[$k] = utf8_decode( $v );
							//$obj[$k] = html_entity_decode( $v );
							
						}
					}
				}
			}
		}
		if ( $type == 'array' && is_object( $obj ) )
		{
			$new = array();
			
			foreach( $obj as $k=>$v )
			{
				$new[$k] = $v;
			}
			
			$obj = $new;
		}
		if ( !$obj )
		{
			$obj = ( $type == 'array' ? array() : new stdClass() );
		}
		return $obj;
	}
}

if( !function_exists( 'json_obj_encode' ) )
{
	function json_obj_encode( $obj, $utf8 = false )
	{
		$str = false;
		if ( is_object( $obj ) )
		{
			if( $utf8 )
			{
				foreach( $obj as $k=>$v )
				{
					if( is_string( $v ) )
					{
						$obj->{$k} = utf8_encode( $v );
						//$obj->{$k} = htmlentities( $v );
					}
				}
			}
			if( defined( 'JSON_UNESCAPED_UNICODE' ) )
			{
				$str = json_encode( $obj, JSON_UNESCAPED_UNICODE );
			}
			else
			{
				$str = json_encode( $obj );
			}
		}
		else if ( is_array( $obj ) )
		{
			if( $utf8 )
			{
				foreach( $obj as $k=>$v )
				{
					if( is_string( $v ) )
					{
						$obj[$k] = utf8_encode( $v );
						//$obj[$k] = htmlentities( $v );
					}
				}
			}
			if( defined( 'JSON_UNESCAPED_UNICODE' ) )
			{
				$str = json_encode( $obj, JSON_UNESCAPED_UNICODE );
			}
			else
			{
				$str = json_encode( $obj );
			}
		}
		else if ( is_string( $obj ) )
		{
			$str = $obj;
		}
		return $str;
	}
}

if( !function_exists( 'json_last_error_msg' ) )
{
	function json_last_error_msg()
	{
		static $ERRORS = array(
			JSON_ERROR_NONE => 'No error',
			JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
			JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
			JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
			JSON_ERROR_SYNTAX => 'Syntax error',
			JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
		);
		
		$error = json_last_error();
		return isset($ERRORS[$error]) ? $ERRORS[$error] : 'Unknown error';
	}
}

if( !function_exists( 'hex_sha1' ) )
{
	function hex_sha1( $str )
	{
		return hash( 'sha1', $str );
	}
}

if( !function_exists( 'hex_sha256' ) )
{
	function hex_sha256( $str )
	{
		return hash( 'sha256', $str );
	}
}

if( !function_exists( 'hex_sha512' ) )
{
	function hex_sha512( $str )
	{
		return hash( 'sha512', $str );
	}
}

if( !function_exists( 'UniqueKey' ) )
{
	function UniqueKey( $option1 = false, $option2 = false, $option3 = false, $option4 = false )
	{
		$host = ( BASE_URL ? ( BASE_URL . '|' ) : '' );
		$option1 = ( $option1 ? ( $option1 . '|' ) : '' );
		$option2 = ( $option2 ? ( $option2 . '|' ) : '' );
		$option3 = ( $option3 ? ( $option3 . '|' ) : '' );
		$option4 = ( $option4 ? ( $option4 . '|' ) : '' );
		$current = ( time() . '|' );
		$random = str_replace( ' ', '', rand(0,999).rand(0,999).rand(0,999).microtime() );
		$hexkey = hex_sha256( $host.$option1.$option2.$option3.$option4.$current.$random );
		
		return $hexkey;
	}
}

if( !function_exists( 'UserActivity' ) )
{
	function UserActivity( $component, $type, $userid, $contactid = null, $typeid = null, $data = '' )
	{
		// 
	
		if( $component && $type && $userid )
		{
			$ua = new dbObject ( 'UserActivity' );
			$ua->Component  = $component;
			$ua->Type       = $type;
			$ua->UserID     = $userid;
			$ua->ContactID  = $contactid;
			$ua->Load();
			$ua->TypeID     = $typeid;
			$ua->Data       = $data;
			$ua->LastUpdate = time();
			$ua->Save();
			
			return true;
		}
	
		return false;
	}
}

class XMLSerializer
{
    // adopted from sean-barton.co.uk
    
	public static function generateValidXmlFromObj( $obj, $node_block = 'object', $node_name = 'array' )
	{
        $arr = get_object_vars( $obj );
        return self::generateValidXmlFromArray( $arr, $node_block, $node_name );
    }
	
    public static function generateValidXmlFromArray( $array, $node_block = 'object', $node_name = 'array' )
	{
        /*$xml = '<?xml version="1.0" encoding="UTF-8" ?>';*/
		
        $xml .= '<' . $node_block . '>';
        $xml .= self::generateXmlFromArray ( $array, $node_name );
        $xml .= '</' . $node_block . '>';
		
        return $xml;
    }
	
    private static function generateXmlFromArray( $array, $node_name )
	{
        $xml = '';
		
        if ( is_array( $array ) || is_object( $array ) )
		{
            foreach ( $array as $key=>$value )
			{
				$key = str_replace( '-' , '', $key );
				
                if ( is_numeric( $key ) )
				{
                    $key = $node_name;
                }
				
                $xml .= '<' . $key . '>' . self::generateXmlFromArray( $value, $node_name ) . '</' . $key . '>';
            }
        }
		else
		{
            $xml = htmlspecialchars( $array, ENT_QUOTES );
        }
		
        return $xml;
    }
}

function output ( $string )
{
	if( $GLOBALS['jaxqueue'] == 1 )
	{
		$GLOBALS['jaxqueue'] = $string;
	}
	else if( !$GLOBALS['jaxqueue'] )
	{
		die ( $string );
	}
	return;
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function statistics ( $module, $component = false )
{
	if( !defined( 'DEBUG_RENDER_DELAY' ) || !DEBUG_RENDER_DELAY )
	{
		return;
	}
	
	if( !$_SESSION['statistics'] )
	{
		$_SESSION['statistics'] = array();
	}
	
	if( $component && $_SESSION['statistics'][$module] )
	{
		if( !$_SESSION['statistics'][$module]->Components )
		{
			$_SESSION['statistics'][$module]->Components = array();
		}
		
		$obj = new stdClass();
		if( $_SESSION['statistics'][$module]->Components[$component]->Start && !$_SESSION['statistics'][$module]->Components[$component]->End )
		{
			$end = microtime_float();
			$delay = ( $end - $_SESSION['statistics'][$module]->Components[$component]->Start );
		}
		else
		{
			$start = microtime_float();
			$end = false;
			$delay = false;
		}
		
		if( is_object( $_SESSION['statistics'][$module]->Components[$component] ) )
		{
			if( $end )
			{
				$_SESSION['statistics'][$module]->Components[$component]->End = $end;
				$_SESSION['statistics'][$module]->Components[$component]->Delay = $delay;
			}
			else
			{
				$_SESSION['statistics'][$module]->Components[$component]->Start = $start;
			}
		}
		else
		{
			$obj = new stdClass();
			$obj->Start = $start;
			$obj->End = $end;
			$obj->Delay = $delay;
			$_SESSION['statistics'][$module]->Components[$component] = $obj;
		}
	}
	else
	{
		if( $_SESSION['statistics'][$module]->Start && !$_SESSION['statistics'][$module]->End )
		{
			$end = microtime_float();
			$delay = ( $end - $_SESSION['statistics'][$module]->Start );
		}
		else
		{
			$start = microtime_float();
			$end = false;
			$delay = false;
		}
		
		if( is_object( $_SESSION['statistics'][$module] ) )
		{
			if( $end )
			{
				$_SESSION['statistics'][$module]->End = $end;
				$_SESSION['statistics'][$module]->Delay = $delay;
			}
			else
			{
				$_SESSION['statistics'][$module]->Start = $start;
			}
		}
		else
		{
			$obj = new stdClass();
			$obj->Start = $start;
			$obj->End = $end;
			$obj->Delay = $delay;
			$_SESSION['statistics'][$module] = $obj;
		}
	}
}

function getUrl ()
{
	$http = explode( '/', strtolower( $_SERVER['SERVER_PROTOCOL'] ) );
	$host = $_SERVER['HTTP_HOST'];
	$uri = $_SERVER['REQUEST_URI'];
	$fname = 'index.html';
	return $http[0] . '://' . $host . $uri . $fname;
}

function correct_encoding( $str, $param = false )
{
	/*if( !$str ) return;
	$str = mb_convert_encoding( $str, "UTF-8", "auto" );
	if( $param )
	{
		$str = utf8_decode( $str );
	}*/
	
	$found = false;
	
	$utf8 = array ( 'Ãž', 'Ã¥', 'ÃŠ', 'â' );
	
	foreach( $utf8 as $match )
	{
		if( strstr( $str, $match ) )
		{
			$found = true;
		}
	}
	
	if( $found )
	{
		$enc = mb_detect_encoding( $str, 'auto' );
		if( $enc == 'UTF-8' )
		{
			$str = utf8_decode( $str );
		}
		/*else if( $enc = iconv_get_encoding ( $str ) )
		{
			$str = iconv ( $enc, 'UTF-8', $str );
		}*/
	}
	//die( $enc . ' -- ' . ' .. ' . $found );
	return $str;
}

function utf8_force ( $str )
{
	return $str;
	// TODO: Remove when not in use anywhere thuis crashed apache, and no need for it any longer.
	if ( preg_match('%^(?:
	[\x09\x0A\x0D\x20-\x7E]            # ASCII
	| [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	| \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
	| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	| \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
	| \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
	| [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	| \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
	)*$%xs', $str ) )
	{
		return $str;
	}
	else
	{
		return iconv( 'CP1252', 'UTF-8', $str );
	}
}

function urlStr ( $str )
{
	if( !$str ) return;
	return strtolower( str_replace( ' ', '_', $str ) );
}

function diebug ( $str )
{
	if( isset( $_REQUEST[ 'diebug' ] ) ) return die( print_r( $str,1 ) ); 
	else return false;
}

function dot_trim( $str, $max, $find = false, $dotted = true )
{
	//$str = trim( auto_decode( $str ) );
	if( $find ) 
	{
		$pos = strpos( strtolower( $str ), strtolower( $find ) );
	}
	if ( $max && strlen ( $str ) > $max )
	{
		return substr ( $str, ( $find ? $pos : 0 ), $max ) . ( $dotted ? '...' : '' );
	}
	return $str;
}

function str_hide( $str, $max )
{
	if( $str && $max && ( strlen( $str ) > $max ) )
	{
		$hstr  = substr( $str, 0, $max ) . '<span class="str show">...</span>';
		$hstr .= '<span class="str hide">' . substr( $str, $max, strlen( $str ) ) . '</span> ';
		$hstr .= '<span class="str link"><a href="javascript:void(0)" onclick="strShow(this)">See More</a></span> ';
		$str = $hstr;
	}
	return $str;
}

function implodeFromObject ( $seperator, $object, $field )
{
	if( !$seperator || !$object || !$field || !is_array( $object ) ) return;
	
	$out = array();
	foreach( $object as $obj )
	{
		foreach( $obj as $k=>$o )
		{
			if( trim( strtolower( $field ) ) != trim( strtolower( $k ) ) ) continue;
			$out[] = $o;
		}
	}
	return implode( $seperator, $out );
}

function getNodeRelation ( $value, $field, $node, $table, $reverse = false, $found = false )
{
	global $database;
	
	if ( $value && $field && $node && $table && $row = $database->fetchObjectRow ( '
		SELECT * 
		FROM SNodesRelation 
		WHERE Field = \'' . $field . '\' 
		AND NodeID = \'' . $node . '\' 
		AND NodeType = "SNodes" 
		' . ( $reverse ? 'AND ConnectedValue = \'' . $value . '\'' : 'AND NodeValue = \'' . $value . '\'' ) . ' 
		AND ConnectedType = \'' . $table . '\' 
		ORDER BY ID DESC 
	', false, 'functions/globalfuncs.php' ) )
	{
		if ( $reverse )
		{
			return $row->NodeValue;
		}
		else
		{
			return $row->ConnectedValue;
		}
	}
	
	if( $found )
	{
		return false;
	}
	return $value;
}

function getCategoryByID ( $id )
{
	global $database;
	
	if( !$id ) return false;
	
	if( $row = $database->fetchObjectRow( '
		SELECT
			*
		FROM
			SBookCategory
		WHERE
			ID = \'' . $id . '\'
		ORDER BY
			ID DESC
	', false, 'functions/globalfuncs.php' ) )
	{
		return $row;
	}
	return false;
}

$groupMembersCache = array();
$groupMembersCacheSet = false;
function GroupMembersPrefetcher()
{
	global $groupMembersCache, $groupMembersCacheSet, $database;
	
	if( $groupMembersCacheSet )
	{
		return;
	}
	
	// TODO: Fix this i implemented limit temporary but it doesn't fix the problem find out why it's needed ...
	
	// TODO: only list users groups if possible
	if( $rows = $database->fetchObjectRows( '
		SELECT 
			r.*, c.ID AS GroupID, u.ID AS ContactID, u.UserID, u.Username, u.Email, u.ImageID 
		FROM 
			SBookCategory c, 
			SBookCategoryRelation r,
			SBookContact u,
			Users u2 
		WHERE 
				r.CategoryID = c.ID 
			AND r.ObjectType = "Users"
			AND u.UserID = r.ObjectID
			AND u.UserID != "0"
			AND u.NodeID = "0"
			AND u2.ID = u.UserID 
			AND u2.IsDeleted = "0" 
		ORDER BY  
			r.SortOrder ASC, 
			r.ID ASC 
		LIMIT 500 
	', false, 'functions/globalfuncs.php' ) )
	{
		foreach( $rows as $row )
		{
			/*if( !isset( $groupMembersCache['_all_'] ) )
			{
				$groupMembersCache['_all_'] = array();
			}*/
			if( !isset( $groupMembersCache[$row->CategoryID] ) )
			{
				$groupMembersCache[$row->CategoryID] = array();
			}
			
			//$groupMembersCache['_all_'][] = $row;
			
			if( $row->ID > 0 )
			{
				$groupMembersCache[$row->CategoryID][] = $row;
			}
		}
	}
	
	$groupMembersCacheSet = true;
}

// Check all group members and return SBookCategoryRelation for each user
function getSBookGroupMembers ( $groupid )
{
	global $database, $groupMembersCache;
	
	if( !$groupid ) return false;
	
	$rows = false;
	
	// TODO: Fix this crap code ......
	
	if( is_array( $groupid ) )
	{
		$rows = $database->fetchObjectRows( '
			SELECT 
				r.*, u.ID AS ContactID, u.UserID, u.Username, u.Email, u.ImageID 
			FROM 
				SBookCategory c, 
				SBookCategoryRelation r,
				SBookContact u,
				Users u2 
			WHERE
					c.ID IN ( ' . implode( ',', $groupid ) . ' ) 
				AND r.CategoryID = c.ID 
				AND r.ObjectType = "Users"
				AND u.UserID = r.ObjectID
				AND u.UserID != "0"
				AND u.NodeID = "0"
				AND u2.ID = u.UserID 
				AND u2.IsDeleted = "0" 
			ORDER BY  
				r.SortOrder ASC, 
				r.ID ASC 
			LIMIT 500 
		', false, 'functions/globalfuncs.php' );
	}
	else
	{
		// TODO: Commented out because of shitcode ....
		
		//GroupMembersPrefetcher();
		//die( print_r( $groupMembersCache,1 ) . ' --' );
		//$rows = $groupMembersCache[$groupid];
		
		$rows = $database->fetchObjectRows( '
			SELECT 
				r.*, u.ID AS ContactID, u.UserID, u.Username, u.Email, u.ImageID 
			FROM 
				SBookCategory c, 
				SBookCategoryRelation r,
				SBookContact u,
				Users u2 
			WHERE
					c.ID = \'' . $groupid . '\' 
				AND r.CategoryID = c.ID 
				AND r.ObjectType = "Users"
				AND u.UserID = r.ObjectID
				AND u.UserID != "0"
				AND u.NodeID = "0"
				AND u2.ID = u.UserID 
				AND u2.IsDeleted = "0" 
			ORDER BY 
				r.SortOrder ASC, 
				r.ID ASC 
			LIMIT 500 
		', false, 'functions/globalfuncs.php' );
	}
	
	/*$q = '
		SELECT 
			r.*, u.ID AS ContactID, u.UserID, u.Username, u.Email, u.ImageID 
		FROM 
			SBookCategory c, 
			SBookCategoryRelation r,
			SBookContact u 
		WHERE
				c.ID = \'' . $groupid . '\' 
			AND r.CategoryID = c.ID 
			AND r.ObjectType = "Users"
			AND u.UserID = r.ObjectID
			AND u.UserID != "0"
			AND u.NodeID = "0"
		ORDER BY  
			r.SortOrder ASC, 
			r.ID ASC 
	';*/
	
	$out = array();
	
	if( $rows ) //= $database->fetchObjectRows( $q, false, 'functions/globalfuncs.php' ) )
	{
		foreach( $rows as $row )
		{
			if( is_array( $groupid ) )
			{
				if( !isset( $out[$row->ID] ) )
				{
					$out[$row->ID] = array();
				}
				
				$out[$row->ID][$row->ContactID] = $row;
			}
			else
			{
				$out[$row->ContactID] = $row;
			}
		}
		return $out;
	}
	return false;
}

function ObjectFromString ( $str )
{
    $out = new stdClass();
    $str = explode( '?', $str );
	if( $str[0] )
	{
		$out->url = $str[0];
	}
    $str = explode( '&', $str[1] );
    foreach( $str as $val )
    {
        $s = explode( '=', $val );
        if ( !trim ( $s[0] ) ) continue;
        $out->{$s[0]} = $s[1];
    }
    return $out;
}

function ArrayFromString ( $str )
{
    $out = array();
    $str = explode( '?', $str );
	if( $str[0] )
	{
		$out['url'] = $str[0];
	}
    $str = explode( '&', $str[1] );
    foreach( $str as $val )
    {
        $s = explode( '=', $val );
        if ( !trim ( $s[0] ) ) continue;
        $out[$s[0]] = $s[1];
    }
    return $out;
}

function FileExists ( $filepath )
{
	global $webuser;
	
	return true;
	
	// TODO: Add support for multiple requests with curl
	
	// TODO: Find a better way then session to store cache for all virtual file path checks on the site
	
	// TODO: Find it by one sql query perhaps or a gathered one somehow
	
	if ( $filepath )
	{
		if ( $webuser->ID > 0 )
		{
			$filepath = ( !strstr( $filepath, $webuser->GetToken() ) ? ( $filepath . $webuser->GetToken() . '/' ) : $filepath );
		}
		
		if ( isset( $_SESSION['CachedFileChecks'][$filepath] ) )
		{
			if ( $_SESSION['CachedFileChecks'][$filepath] )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		$headers = @get_headers( $filepath );
		//die( print_r( $headers,1 ) . ' -- ' . $filepath );
		if ( $headers && !strstr( $headers[0], '404 Not Found' ) )
		{
			$_SESSION['CachedFileChecks'][$filepath] = true;
			
			return true;
		}
		else
		{
			$_SESSION['CachedFileChecks'][$filepath] = false;
			
			return false;
		}
	}
	
	return false;
}

function CurlUrlExists ( $url, $param = false )
{
	$ok = false;
	if( !$url ) return false;
	$c = curl_init();
	curl_setopt( $c, CURLOPT_URL, $url );
	curl_setopt( $c, CURLOPT_NOBODY, 1 );
	curl_setopt( $c, CURLOPT_FAILONERROR, 1 );
    curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );
	$r = curl_exec( $c );
	$k = curl_getinfo( $c, CURLINFO_HTTP_CODE );
	if( $r !== false && ( $k == 200 || $k == 301 ) )
	{
		$ok = curl_getinfo( $c );
		// Check if parameters match
		if( $param && strstr( $param, '=>' ) )
		{
			$param = explode( '=>', trim( $param ) );
			if( !strstr( $ok[ trim( $param[0] ) ], trim( $param[1] ) ) )
			{
				$ok = false;
			}
		}
	}
	curl_close( $c );
	return $ok;
}

function GetCurlContentByUrl ( $url )
{
	if( !$url ) return;
	
	if( function_exists( 'curl_init' ) )
	{
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		//curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_ENCODING, ' ');
		return curl_exec( $ch );
	}
	
	return false;
}

function ContentByUrl ( $url )
{
	if( !$url ) return;
	
	// Get data
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	//curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$str = stripslashes ( curl_exec( $ch ) );
	
	// Detect encoding
	$encoding = mb_detect_encoding ( $str );
	if ( !$encoding ) $encoding = 'UTF-8';
		
	// Convert to unicode
	if ( strtoupper ( $encoding ) != 'UTF-8' )
		$str = iconv ( $encoding, 'UTF-8', $str );
	
	$str = correct_encoding( $str );
	
	// Return
	// TODO: add FixCharacters() here
	return $str;
}

function grab_image( $url, $path, $file )
{
	if( !$url || !$path || !$file ) return false;
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_BINARYTRANSFER,1 );
    $raw = curl_exec( $ch );
    curl_close( $ch );
    if( file_exists( $path . $file ) )
	{
        $ext = explode( '.', $file );
		$file = str_replace( ( '.' . end( $ext ) ), ( '_copy.' . end( $ext ) ), $file );
    }
    $fp = fopen( $path . $file, 'x' );
    if( fwrite( $fp, $raw ) )
	{
		fclose( $fp );
		return $file;
	}
	return false;
}

function json_encode_unescaped_unicode( $struct )
{
	return preg_replace( "/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode( $struct ) );
}

// Better JSON decoder!
function jsondecode( $str )
{
	for( $i = 0; $i <= 31; ++$i )
		$str = str_replace( chr( $i ), '', $str );
	$str = str_replace( chr(127), '', $str );
	if( 0 === strpos( bin2hex( $str ), 'efbbbf' ) )
		$str = substr( $str, 3 );
	return json_decode( stripslashes( $str ) );
}

function makeLinks( $str )
{
	// Convert x:// a href linkes into just x:// text
	$str = preg_replace( "/\<a[^>]*?\>[a-zA-Z]+\:\/\/(.*?)\<\/a\>/i", "$1", $str );
	// Convert www. text into http:// text
    $str = preg_replace( "/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2", $str );
	// Convert some x:// text that doesn't start with >http:// (ex) into <a href> links
	$str = preg_replace( "/[^>=\"a-z]{0,1}([a-zA-Z]*\:\/\/[\w-?&;\!\æ\ø\å\:\%\+#~=\.\/\@\(\)]+[\w-?&;\!\æ\ø\å\:\%\+#~=\.\/\@\(\)])/i", " <a " . ( strstr( $str, '/library/' ) ? '' : 'target="_blank"' ) . " href=\"$1\">$1</a>", $str );
	// Convert emails into links
    $str = preg_replace( "/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i", " <a href=\"mailto:$1\">$1</a>", $str );
    return $str;
}

function account_format( $str )
{
	return substr( $str, 0, 4 ) . '.' . substr( $str, 4, -5 ) . '.' . substr( $str, -5 );
}

function embedYoutube ( $url, $width, $height, $auto = true, $control = true, $active = true )
{
    $obj = ObjectFromString( $url );
    $url = explode( '/', $url );
	$url = end( $url );
	
	if( strstr( $url, '?v=' ) )
	{
		$url = explode( '?v=', $url );
		$url = end( $url );
	}
	
    $str = '<iframe ' . ( $active ? 'class="active" ' : 'onmouseover="this.className=\'active\'" onclick="this.className=\'active\'" ' ) . 'width="' . $width . '" height="' . $height . '" src="//www.youtube.com/embed/' . ( $obj->v ? $obj->v : $url ) . ( $auto ? '?autoplay=1' : '?autoplay=0' ) . ( $control ? '&controls=1' : '&controls=0' ) . '&showinfo=0" frameborder="0" allowfullscreen></iframe>';
    return trim( $str );
}

function embedVimeo ( $str, $width, $height, $auto = true, $control = true, $active = true )
{
    $str = explode( '/', $str );
    $str = explode( '?', end( $str ) );
    $str = '<iframe ' . ( $active ? 'class="active" ' : 'onmouseover="this.className=\'active\'" onclick="this.className=\'active\'" ' ) . 'src="//player.vimeo.com/video/' . $str[0] . '?title=0&amp;byline=0&amp;portrait=0&amp;color=ff3331" width="' . $width . '" height="' . $height . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    return trim( $str );
}

function embedLivestream ( $str, $width, $height, $auto = true, $control = true, $active = true )
{
	$height = ( $height + 200 );
    $str = explode( '/', $str );
    $clip = ( end( $str ) != $str[3] ? end( $str ) : '' );
    
    if( strstr( $clip, '?' ) )
    {
    	$clip = explode( '?', $clip );
    	$clip = $clip[0];
    }
    //$str = '<iframe ' . ( $active ? 'class="active" ' : 'onmouseover="this.className=\'active\'" onclick="this.className=\'active\'" ' ) . 'src="http://cdn.livestream.com/embed/' . $str[3] . '?layout=2' . ( $clip ? '&clip=' . $clip : '' ) . ( $auto ? '&autoPlay=true' : '' ) . ( $control ? '&controls=1' : '' ) . '&width=' . $width . '&height=' . $height . '" width="' . $width . '" height="' . $height . '" style="border:0;outline:0" frameborder=0 scrolling=no></iframe>';
    $str = '<iframe ' . ( $active ? 'class="active" ' : 'onmouseover="this.className=\'active\'" onclick="this.className=\'active\'" ' ) . 'src="https://livestream.com/' . $str[3] . ( $clip ? '/events/' . $clip : '' ) . '/player?width=' . $width . '&height=' . $height . ( $auto ? '&autoPlay=true' : '' ) . ( $control ? '&controls=1' : '' ) . '" width="' . $width . '" height="' . $height . '" style="border:0;outline:0" frameborder=0 scrolling=no></iframe>';
	return trim( $str );
}

function embedVideo ( $url, $width, $height, $tag = false, $auto = true, $control = true, $active = true )
{
	if( $tag == 'video' )
	{
		// mp4, ogg, webm supported
		//$str  = '<video width="' . $width . '" height="' . $height . '" controls>';
		//$str .= '<source src="' . $url . '" type="video/' . strtolower( end( explode( '.', $url ) ) ) . '">';
		//$str .= 'Your browser does not support the video tag.';
		//$str .= '</video>';
		$str = '<video width="' . $width . '" height="' . $height . '" preload="metadata" ' . ( $control ? 'controls="" ' : '' ) . ( $auto ? 'autoplay="" ' : '' ) . 'src="' . $url . '">Your browser does not support the video tag.</video>';
	}
	else
	{
		$str = '<iframe ' . ( $active ? 'class="active" ' : 'onmouseover="this.className=\'active\'" onclick="this.className=\'active\'" ' ) . 'width="' . $width . '" height="' . $height . '" src="' . trim( $url ) . '" frameborder="0" allowfullscreen></iframe>';
	}
	return trim( $str );
}

function embedSpotify ( $url, $width, $height, $tag = false, $auto = true, $control = true, $active = true )
{
	if( $tag == 'audio' )
	{
		// mp3, ogg, wav supported
		$str = '<audio width="' . $width . '" height="' . $height . '" preload="metadata" ' . ( $control ? 'controls="" ' : '' ) . ( $auto ? 'autoplay="" ' : '' ) . 'src="' . $url . '">Your browser does not support the audio tag.</audio>';
	}
	else
	{
		$str = '<iframe ' . ( $active ? 'class="active" ' : 'onmouseover="this.className=\'active\'" onclick="this.className=\'active\'" ' ) . 'width="' . $width . '" height="' . $height . '" src="' . trim( $url ) . '" frameborder="0"></iframe>';
	}
	return trim( $str );
}

function embedAudio ( $url, $width, $height, $auto = true, $control = true, $active = true )
{
    //$str = '<iframe width="' . $width . '" height="' . $height . '" src="' . trim( $url ) . '" frameborder="0"></iframe>';
    $str = '<iframe ' . ( $active ? 'class="active" ' : 'onmouseover="this.className=\'active\'" onclick="this.className=\'active\'" ' ) . 'width="' . $width . '" src="' . trim( $url ) . '" frameborder="0"></iframe>';
	return trim( $str );
}

function embedPDF ( $url, $width, $height, $active = true )
{
    $str = '<iframe ' . ( $active ? 'class="active" ' : 'onmouseover="this.className=\'active\'" onclick="this.className=\'active\'" ' ) . 'width="' . $width . '" height="' . $height . '" src="' . trim( $url ) . '" frameborder="0"></iframe>';
    return trim( $str );
}

function embedMSOffice ( $url, $width, $height, $active = true )
{
    $str = '<iframe ' . ( $active ? 'class="active" ' : 'onmouseover="this.className=\'active\'" onclick="this.className=\'active\'" ' ) . 'width="' . $width . '" height="' . $height . '" src="https://view.officeapps.live.com/op/embed.aspx?src=' . trim( $url ) . '" frameborder="0"></iframe>';
    return trim( $str );
}

function embedLibrary( $url, $width, $height )
{
	$str = '<iframe src="' . trim( $url ) . '?rendermodule=main,profile&rendercomponent=groups,library&displaymode=1&save=0" width="' . $width . '" height="' . $height . '" frameborder="0"></iframe>';
    return trim( $str );
}

function defineMediaFormat ( $width, $height )
{
	if( !$width || !$height ) return false;
	
	$imgclass = ''; $diff = '';
	
	if( $width > $height )
	{
		$imgclass = 'wide';
		$diff = ( $width - $height );
	}
	else if( $width < $height )
	{
		$imgclass = 'long';
		$diff = ( $height - $width );
	}
	else if( $width == $height )
	{
		$imgclass = 'match';
	}
	
	if( $diff > 1000 ) return ( $imgclass . '_1000' );
	if( $diff > 900  ) return ( $imgclass . '_900'  );
	if( $diff > 800  ) return ( $imgclass . '_800'  );
	if( $diff > 700  ) return ( $imgclass . '_700'  );
	if( $diff > 600  ) return ( $imgclass . '_600'  );
	if( $diff > 500  ) return ( $imgclass . '_500'  );
	if( $diff > 400  ) return ( $imgclass . '_400'  );
	if( $diff > 300  ) return ( $imgclass . '_300'  );
	if( $diff > 200  ) return ( $imgclass . '_200'  );
	if( $diff > 100  ) return ( $imgclass . '_100'  );
	
	return ( $imgclass );
}

function priorityList( $type )
{
	$pri = array(
		'long_1000', 'long_900', 'long_800', 'long_700', 'long_600',
		'long_500', 'long_400', 'long_300', 'long_200', 'long_100',
		'wide_1000', 'wide_900', 'wide_800', 'wide_700', 'wide_600',
		'wide_500', 'wide_400', 'wide_300', 'wide_200', 'wide_100',
		'wide', 'long', 'match'
	);
	
	return $pri;
}

// arr = array to sort, fld = field in object to sort by, pri = array of priority list
function sortByPriority( $arr, $fld, $pri )
{
	$out = array();
	
	if( is_array( $pri ) )
	{
		// Assign the matched objects to the prio array
		foreach( $pri as $k=>$p )
		{
			$val = $p;
			$pri[$k] = array();
			
			if( is_array( $arr ) )
			{
				foreach( $arr as $a )
				{
					if( $a->$fld == $val )
					{
						$pri[$k][] = $a;
					}
				}
			}
		}
		// Loop through the prio array and put everyting into an output array
		foreach( $pri as $p )
		{
			if( $p )
			{
				foreach( $p as $a )
				{
					if( $a )
					{
						$out[] = $a;
					}
				}
			}
		}
		// Output the result
		return $out;
	}
	
	return false;
}

function renderWallAlbum( $width, $height, $margin, $maxwidth, $maxheight, $format, $number )
{
	$out = new stdClass();
	
	$format = explode( '_', $format );
	
	/*if( $number > 2 )
	{
		$width = ( ( $width - $margin ) / 3 );
		$height = ( ( $height - $margin ) / 3 );
	}*/
	
	$margin = ( $margin + 4 );
	
	if( $number > 1 )
	{
		$width = ( ( $width - $margin ) / 3 );
		$height = ( ( ( $height - $margin ) / 3 ) - 50 );
	}
	
	switch( $format[0] )
	{
		case 'long':
			if( !$format[1] )
			{
				$out->Width = $width;
				$out->Height = $height;
				return $out;
			}
			else if( $format[1] > 100 )
			{
				$out->Width = $width;
				$out->Height = $height;
				return $out;
			}
			else
			{
				$out->Width = $width;
				$out->Height = $height;
				return $out;
			}
			break;
			
		case 'wide':
			if( !$format[1] )
			{
				$out->Width = $width;
				$out->Height = $height;
				return $out;
			}
			else if( $format[1] > 200 )
			{
				$out->Width = $width;
				$out->Height = ( $out->Width / 2 );
				return $out;
			}
			else
			{
				$out->Width = $width;
				$out->Height = $height;
				return $out;
			}
			break;
		
		case 'match':
			$out->Width = $width;
			$out->Height = $height;
			return $out;
			break;
	}
	
	$out->Width = $width;
	$out->Height = $height;
	return $out;
}

function renderSmileys( $str )
{
	$smileys = array(
		":)"=>"smile", ":("=>"frown", ":p"=>"tongue", ":D"=>"grin", ":o"=>"gasp", ";)"=>"wink",
		":v"=>"pacman", ">:("=>"grumpy", ":/"=>"unsure", ":'("=>"cry", "^_^"=>"kiki", "8-)"=>"glasses",
		"B|"=>"sunglasses", "<3"=>"heart", "3:)"=>"devil", "O:)"=>"angel", "-_-"=>"squint",
		"o.O"=>"confused", ">:o"=>"upset", ":3"=>"colonthree", "(y)"=>"like"
	);
	
	// Fix http
	if( strstr( $str, 'https://' ) )
	{
		$str = str_replace ( 'https://', '<!--replacehttps-->', $str );
	}
	else
	{
		$str = str_replace ( 'http://', '<!--replacehttp-->', $str );
	}
	
	// Add support for smilies with a nose
	$WithLines = array ();
	$key = false;
	foreach ( $smileys as $k=>$smilWL )
	{
		if ( !strstr ( $k, '-' ) && strlen ( $k ) == 2 && $k{0} == ':' )
		{
			$key = $k{0} . '-' . $k{1};
			$WithLines[$key] = $smilWL;
		}
	}
	if ( $key ) $smileys = array_merge ( $smileys, $WithLines );
	
	// Make smilies
	foreach( $smileys as $k=>$v )
	{
		if( strlen( $str ) < 4 )
		{
			$str = str_replace( $k, ( '<i class="emoticon emoticon_' . $v . '"></i>' ), $str );
		}
		else
		{
			$str = str_replace( ( ' ' . $k ), ( ' <i class="emoticon emoticon_' . $v . '"></i>' ), $str );
		}
	}
	
	// Fix back http
	if( strstr( $str, '<!--replacehttps-->' ) )
	{
		$str = str_replace ( '<!--replacehttps-->', 'https://', $str );
	}
	else
	{
		$str = str_replace ( '<!--replacehttp-->', 'http://', $str );
	}
	
	return $str;
}

function renderCustomSelect( $arr, $name = false, $id = false, $attr = false )
{
	$str = ''; $opt = ''; $c = ''; $i = 0;
	
	foreach( $arr as $obj )
	{
		if( $obj->selected )
		{
			$s = ' selected';
			$c = (string)$obj->value;
		}
		else
		{
			$s = '';
		}
		
		$opt .= '<li class="option' . $s . strtolower( ' ' . str_replace( ' ', '_', $obj->name ) ) . '" value="' . $obj->value . '" onclick="setOptionCustomSelect(this)">';
		$opt .= '<em></em>';
		$opt .= '<span>' . $obj->name . '</span>';
		$opt .= '<div class="clearboth" style="clear:both"></div>';
		$opt .= '</li>';
		
		$i++;
	}
	
	$str .= '<div ' . ( $id ? ( 'id="' . $id . '" ' ) : '' ) . ( $name ? ( 'name="' . $name . '" ' ) : '' ) . ( $c != '' ? ( 'value="' . $c . '" ' ) : '' ) . 'class="custom select" onclick="toggleCustomSelect(this,event)"' . ( $attr ? ( ' ' . $attr ) : '' ) . '>';
	$str .= '<input type="hidden" ' . ( $name ? ( 'name="' . $name . '" ' ) : '' ) . ( $c != '' ? ( 'value="' . $c . '"' ) : '' ) . ( $attr ? ( ' ' . $attr ) : '' ) . '/>';
	$str .= '<div class="button arrow"></div>';
	$str .= '<ul>';
	$str .= $opt;
	$str .= '</ul>';
	$str .= '</div>';
	
	return $str;
}

function renderSelectManager( $name = false, $value = false, $data = false, $js = false, $mode = false )
{
	$opt1 = ''; $opt2 = '';
	
	$order = array();
	
	if ( $data && is_array( $data ) )
	{
		foreach( $data as $k=>$v )
		{
			if ( strstr( $value, (string)$k ) )
			{
				$opt1 .= '<option' . ( $js != '' && is_array( $js ) ? ( ' ' . $js['option1'] ) : '' ) . ' value="' . $k . '">' . $v . '</option>';
			}
			if ( !strstr( $value, (string)$k ) )
			{
				$opt2 .= '<option' . ( $js != '' && is_array( $js ) ? ( ' ' . $js['option2'] ) : '' ) . ' value="' . $k . '"' . /*( $value && is_string( $value ) && strstr( $value, (string)$k ) ? ' selected="selected"' : '' ) . */'>' . $v . '</option>';
			}
			
			$order[] = $k;
		}
	}
	
	$str  = '<table class="selectmanager" style="width:100%;"><tbody><tr>';
	$str .= '<td style="width:50%;"><select' . ( $name != '' ? ( ' name="' . $name . '"' ) : '' ) . ' class="selectmanager" style="width:100%;height:215px;" multiple' . ( $mode != '' ? ( ' ' . $mode . '' ) : '' ) . '>';
	$str .= $opt1;
	$str .= '</select></td>';
	$str .= '<td style="min-width:50px;vertical-align:middle;text-align:center;">';
	$str .= '<p><button style="background:#EEE;border:0;"' . ( $js != '' && is_array( $js ) ? ( ' ' . $js['button1'] ) : '' ) . '><img src="admin/gfx/icons/arrow_left.png"/></button></p>';
	$str .= '<p><button style="background:#EEE;border:0;"' . ( $js != '' && is_array( $js ) ? ( ' ' . $js['button2'] ) : '' ) . '><img src="admin/gfx/icons/arrow_right.png"/></button></p>';
	$str .= '</td><td style="width:50%;"><select class="selectmanager" style="width:100%;height:215px;" multiple' . ( $mode != '' ? ( ' ' . $mode . '' ) : '' ) . /*( $value && is_string( $value ) ? ' selected="' . $value . '"' : '' ) . */ ( $order ? ' sortorder="' . implode( ',', $order ) . '"' : '' ) . '>';
	$str .= $opt2;
	$str .= '</select></td>';
	$str .= '</tr></tbody></table>';
	
	return $str;
}

function renderDatefield( $name = false, $value = false, $js = false )
{
	return  '<div class="' . i18n( 'i18n_day_' . date( 'd', strtotime( $value ? $value : date( 'Y-m-d' ) ) ) ) . ' datefield2 closed">' . 
			'<div class="icon"' . ( $js ? ( ' ' . $js ) : '' ) . '></div>' . 
			'<div class="calendar">' . renderAdminCalendar( ( $value ? date( 'Y-m-d', strtotime( $value ) ) : date( 'Y-m-d' ) ) ) . '</div>' . 
			'<input type="hidden" name="' . $name . '" value="' . ( $value ? strtotime( $value ) : strtotime( date( 'Y-m-d' ) ) ) . '"/>' . 
			'<input type="text" ' . ( $js ? ( ' ' . $js ) : '' ) . ' value="' . ( $value ? i18n( date( 'D', strtotime( $value ) ) ) . date( ', d.m.Y', strtotime( $value ) ) : ( i18n( date( 'D' ) ) . date( ', d.m.Y' ) ) ) . '" readonly/>' . 
			'</div>';
}



if( !function_exists( 'mailNow_' ) )
{
	function mailNow_ ( $subject, $message, $receiver, $type = 'html', $from = MAIL_REPLYTO, $attachments = false, $template = false, $rawdata = false )
	{
		include_once ( 'subether/classes/mail.class.php' );
		
		$email = new eMail ();
		$email->setHostInfo ( MAIL_SMTP_HOST, MAIL_USERNAME, MAIL_PASSWORD );
		$email->setSubject ( ( strstr( $subject, '=?UTF-8?B?' ) ? $subject : ( '=?UTF-8?B?' . base64_encode ( $subject ) . '?=' ) ) );
		$email->setPort ( defined ( 'MAIL_SMTP_PORT' ) ? MAIL_SMTP_PORT : '25' );
		$email->setFrom ( $from );
		$email->_error_report = true;
		$email->_recipients = array ( $receiver );
		$email->addHeader ( "Content-type", "text/" . $type . "; charset=iso-8859-1" );
		
		$Article = utf8_decode ( $message );
		
		if ( $attachments )
		{
			if ( is_object( $attachments ) && isset( $attachments->Data ) && isset( $attachments->Filename ) )
			{
				$email->addRawFile ( $attachments->Data, $attachments->Filename, $attachments->Type, $attachments->Encoding );
			}
			else if ( is_string( $attachments ) )
			{
				foreach ( $attachments as $att )
				{
					$email->addAttachment ( $att );
				}
			}
			else if ( is_array( $attachments ) )
			{
				foreach ( $attachments as $att )
				{
					if ( is_object( $att ) )
					{
						if ( isset( $att->Data ) && isset( $att->Filename ) )
						{
							$email->addRawFile ( $att->Data, $att->Filename, $att->Type, $att->Encoding );
						}
					}
					else
					{
						$email->addAttachment ( $att );
					}
				}
			}
		}
		
		// Use template
		if ( !$template && file_exists ( 'subether/templates/standardemail.php' ) )
		{
			$a = new cPTemplate ( 'subether/templates/standardemail.php' );
			$a->subject = $subject;
			$a->body = $Article;
			$Article = $a->render ();
		}
		
		// Extract all images and add to mail data
		$embedImages = array ();
		$cid = 1;
		preg_match_all ( '/\<img[^>]*?\>/i', $Article, $matches );
		foreach ( $matches[0] as $match )
		{
			preg_match ( '/src\=\"([^"]*?)\"/i', $match, $src );
			preg_match ( '/style\=\"([^"]*?)\"/i', $match, $style );
			preg_match ( '/border\=\"([^"]*?)\"/i', $match, $border );
			if ( $style ) $style = ' style="' . $style[1] . '"'; else $style = '';
			if ( $border ) $border = ' border="' . $border[1] . '"'; else $border = '';
			$embedImages[] = array ( $match, '<img' . $style . $border . ' src="cid:image_' . 
			$mail->ID . '_' . $cid . '"/>', $src[1], 'image_' . $mail->ID . '_' . $cid );
			$cid++;
		}
		if ( count ( $embedImages ) && is_array ( $embedImages ) )
		{
			foreach ( $embedImages as $row )
			{
				list ( $original, $replace, $file, $tempName ) = $row;
				$Article = str_replace ( $original, $replace, $Article );
				$email->embedImage ( $file, false, false, $tempName );
			}
		}
		
		$email->setMessage ( $Article );
		
		if( $email->send ( $rawdata ) )
		{
			return array( 'ok' => true, 'error' => '' );
		}
		else
		{
			return array( 'ok' => false, 'error' => $email->_error_reponse );
		}
	}
}

?>
