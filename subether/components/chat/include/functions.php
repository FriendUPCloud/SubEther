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

function setAudioNotices ( $command, $uid, $cid, $type )
{
	global $webuser;
	
	if( !$command || !$cid || !$type ) return false;

	$n = new dbObject( 'SBookNotification' );
	$n->Type = $type;
	$n->Command = trim( $command );
	$n->ObjectID = $cid;
	$n->SenderID = $webuser->ID;
	$n->ReceiverID = ( $uid ? $uid : '0' );
	$n->Load();
	$n->IsNoticed = '1';
	$n->Save();
	
	return true;
}

function checkAudioNotices ( $uid, $cid, $type )
{
	if( !$cid || !$type ) return false;
	
	$n = new dbObject( 'SBookNotification' );
	$n->Type = $type;
	$n->ObjectID = $cid;
	$n->ReceiverID = ( $uid ? $uid : '0' );
	$n->IsNoticed = '1';
	if( $n->Load() && $n->Command != '' )
	{
		if( deleteAudioNotices ( $n->Command, ( $uid ? $uid : '0' ), $cid, $type ) )
		{
			return $n->Command;
		}
	}
	return false;
}

function deleteAudioNotices ( $command, $uid, $cid, $type )
{
	if( !$command || !$cid || !$type ) return false;
	
	$n = new dbObject( 'SBookNotification' );
	$n->Type = $type;
	$n->IsNoticed = '1';
	$n->ReceiverID = ( $uid ? $uid : '0' );
	$n->Command = trim( $command );
	$n->ObjectID = $cid;
	if( $nf = $n->Find() )
	{
		foreach( $nf as $na )
		{
			$a = new dbObject( 'SBookNotification' );
			if( $a->Load( $na->ID ) )
			{
				$a->Delete();
			}
		}
		return true;
	}
	return false;
}

function str_decode_replace ( $str )
{
	$str = trim( htmlEntities( $str ) );
	//$str = trim( strip_tags( $str ) );
	$str = utf8_encode( iconv_mime_decode( $str, ICONV_MIME_DECODE_CONTINUE_ON_ERROR ) );
	//$str = utf8_encode( $str );
	return $str;
}

// get the body of a part of a message according to the
// string in $part
function mail_fetchpart( $mbox, $msgNo, $part )
{
	$parts = mail_fetchparts( $mbox, $msgNo );
	
	$partNos = explode( ".", $part );
	
	$currentPart = $parts;
	
	while( list( $key, $val ) = each( $partNos ) )
	{
		$currentPart = $currentPart[$val];
	}
	
	if ( $currentPart != "" ) return $currentPart;
	else return false;
}

// splits a message given in the body if it is
// a mulitpart mime message and returns the parts,
// if no parts are found, returns false
function mail_mimesplit( $header, $body )
{
	$parts = array();
	
	$PN_EREG_BOUNDARY = "Content-Type:(.*)boundary=\"([^\"]+)\"";
	
	if ( eregi( $PN_EREG_BOUNDARY, $header, $regs ) )
	{
		$boundary = $regs[2];
		
		$delimiterReg = "([^\r\n]*)$boundary([^\r\n]*)";
		
		if ( eregi( $delimiterReg, $body, $results ) )
		{
			$delimiter = $results[0];
			$parts = explode( $delimiter, $body );
			$parts = array_slice( $parts, 1, -1 );
		}
		
		return $parts;
	}
	else
	{
		return false;
	}
}

// returns an array with all parts that are
// subparts of the given part
// if no subparts are found, return the body of
// the current part
function mail_mimesub( $part )
{
	$i = 1;
	$out = new stdClass();
	$headDelimiter = "\r\n\r\n";
	$delLength = strlen( $headDelimiter );

	// get head & body of the current part
	$endOfHead = strpos( $part, $headDelimiter );
	$head = substr( $part, 0, $endOfHead );
	$body = substr( $part, $endOfHead + $delLength, strlen( $part ) );
	
	// Edit made to attach header info on each part
	if( $head )
	{
		$hparts = explode( "\r\n", $head );
		
		if( $hparts && is_array( $hparts ) )
		{
			$out->header = array();
			
			foreach( $hparts as $hp )
			{
				if( trim( $hp ) && strstr( trim( $hp ), ';' ) && ( $hp2 = explode( ';', trim( $hp ) ) ) )
				{
					foreach( $hp2 as $p2 )
					{
						if( !strstr( $p2, '=' ) && strstr( $p2, ':' ) && ( $c = explode( ':', $p2 ) ) )
						{
							$out->header[trim($c[0])] = str_replace( array( '"', "'" ), array( '', '' ), trim($c[1]) );
						}
						if( strstr( $p2, '=' ) && !strstr( $p2, ':' ) && ( $l = explode( '=', $p2 ) ) )
						{
							$out->header[trim($l[0])] = str_replace( array( '"', "'" ), array( '', '' ), trim($l[1]) );
						}
					}
				}
				else if( !strstr( $hp, '=' ) && strstr( $hp, ':' ) && ( $p = explode( ':', $hp ) ) )
				{
					$out->header[trim($p[0])] = str_replace( array( '"', "'" ), array( '', '' ), trim($p[1]) );
				}
				else if( strstr( $hp, '=' ) && !strstr( $hp, ':' ) && ( $p = explode( '=', $hp ) ) )
				{
					$out->header[trim($p[0])] = str_replace( array( '"', "'" ), array( '', '' ), trim($p[1]) );
				}
			}
		}
	}
	
	$out->body = $body;
	
	// check whether it is a message according to rfc822
	if ( stristr( $head, "Content-Type: message/rfc822" ) )
	{
		$part = substr( $part, $endOfHead + $delLength, strlen( $part ) );
		$returnParts[1] = mail_mimesub( $part );
		return $returnParts;
	}
	// if no message, get subparts and call function recursively
	elseif ( $subParts = mail_mimesplit( $head, $body ) )
	{
		// got more subparts
		while( list( $key, $val ) = each( $subParts ) )
		{
			$returnParts[$i] = mail_mimesub( $val );
			$i++;
		}           
		return $returnParts;
	}
	else
	{
		return $out;
	}
}

// get an array with the bodies all parts of an email
// the structure of the array corresponds to the
// structure that is available with imap_fetchstructure
function mail_fetchparts( $mbox, $msgNo )
{
	$parts = array();
	$header = imap_fetchheader( $mbox, $msgNo );
	$body = imap_body( $mbox, $msgNo, FT_INTERNAL );

	$i = 1;

	if ( $newParts = mail_mimesplit( $header, $body ) )
	{
		while( list( $key, $val ) = each( $newParts ) )
		{
			$parts[$i] = mail_mimesub( $val );
			$i++;               
		}
	}
	else
	{
		$parts[$i] = $body;
	}
	return $parts;
}

?>
