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

include_once ( 'subether/classes/htmlparser.class.php' );
include_once ( 'subether/classes/library.class.php' );
include_once ( 'subether/functions/globalfuncs.php' );
include_once ( 'subether/components/browse/include/functions.php' );

$required = array(
	/*'SessionID', */'Message' 
);

$options = array(
	'Type', 'Search', 'Json'
);

// TODO: remove when live or done debug testing
unset( $_REQUEST['route'] );

if ( isset( $_REQUEST ) )
{
	foreach( $_REQUEST as $k=>$p )
	{
		if( !in_array( $k, $required ) && !in_array( $k, $options ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	/*foreach( $required as $r )
	{
		if( !isset( $_REQUEST[$r] ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}*/
	
	// Get User data from sessionid
	/*$sess = new dbObject ( 'UserLogin' );
	$sess->Token = $_REQUEST['SessionID'];
	if ( $sess->Load () )
	{
		$u = new dbObject ( 'SBookContact' );
		$u->UserID = $sess->UserID;
		if( !$u->Load () )
		{
			throwXmlError ( AUTHENTICATION_ERROR );
		}
	}
	else
	{
		throwXmlError ( SESSION_MISSING );
	}*/
	
	if ( isset( $_REQUEST['Message'] ) )
	{
		$lib = new Library ();
		if ( $data = $lib->ParseUrl( $_REQUEST['Message'] ) )
		{
			$str  = '<Data>';
			$str .= '<Url><![CDATA[' . $data->Url . ']]></Url>';
			$str .= '<Domain><![CDATA[' . $data->Domain . ']]></Domain>';
			$str .= '<Title><![CDATA[' . $data->Title . ']]></Title>';
			
			if ( $data->Images )
			{
				$str .= '<Images>';
				
				foreach ( $data->Images as $img )
				{
					$str .= '<Image>';
					$str .= '<src><![CDATA[' . $img->src . ']]></src>';
					$str .= '<width>' . $img->width . '</width>';
					$str .= '<height>' . $img->height . '</height>';
					$str .= '<type><![CDATA[' . $img->type . ']]></type>';
					$str .= '<bits><![CDATA[' . $img->bits . ']]></bits>';
					$str .= '<mime><![CDATA[' . $img->mime . ']]></mime>';
					$str .= '</Image>';
				}
				
				$str .= '</Images>';
			}
			
			$str .= '<Leadin><![CDATA[' . $data->Leadin . ']]></Leadin>';
			$str .= '<Type><![CDATA[' . $data->Type . ']]></Type>';
			$str .= '<Media><![CDATA[' . $data->Media . ']]></Media>';
			$str .= '</Data>';
			
			$json = new stdClass();
			$json->Url = $data->Url;
			$json->Domain = $data->Domain;
			$json->Title = $data->Title;
			$json->Images = $data->Images;
			$json->Leadin = $data->Leadin;
			$json->Type = $data->Type;
			$json->Media = $data->Media;
			
			if ( isset( $_REQUEST['Json'] ) )
			{
				die( json_encode( $json ) );
			}
			else
			{
				outputXML ( $str );
			}
		}
	}
	else if ( isset( $_REQUEST['Type'] ) )
	{
		switch ( $_REQUEST['Type'] )
		{
			case 'youtube':
				
				if ( $data = YouTubeParser( str_replace( ' ', '+', $_REQUEST['Search'] ) ) )
				{
					$json = array(); $str = '';
					
					foreach( $data as $dat )
					{
						$str .= '<Data>';
						$str .= '<title><![CDATA[' . $dat->title . ']]></title>';
						$str .= '<ID><![CDATA[' . $dat->ID . ']]></ID>';
						$str .= '<href><![CDATA[' . $dat->href . ']]></href>';
						$str .= '<duration>' . $dat->duration . '</duration>';
						$str .= '<channel><![CDATA[' . $dat->channel . ']]></channel>';
						$str .= '<by><![CDATA[' . $dat->by . ']]></by>';
						$str .= '<added><![CDATA[' . $dat->added . ']]></added>';
						$str .= '<views><![CDATA[' . $dat->views . ']]></views>';
						$str .= '<description><![CDATA[' . $dat->description . ']]></description>';
						$str .= '<thumb><![CDATA[' . $dat->thumb . ']]></thumb>';
						$str .= '</Data>';
						
						$obj = new stdClass();
						$obj->title = $dat->title;
						$obj->ID = $dat->ID;
						$obj->href = $dat->href;
						$obj->duration = $dat->duration;
						$obj->channel = $dat->channel;
						$obj->by = $dat->by;
						$obj->added = $dat->added;
						$obj->views = $dat->views;
						$obj->description = $dat->description;
						$obj->thumb = $dat->thumb;
						
						$json[] = $obj;
					}
					
					if ( isset( $_REQUEST['Json'] ) )
					{
						die( json_encode( $json ) );
					}
					else
					{
						outputXML ( $str );
					}
				}
				
				break;
		}
	}
	
	throwXmlMsg ( EMPTY_LIST );
}

throwXmlError ( MISSING_PARAMETERS );

?>
