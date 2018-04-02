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

$astr = '';

$mstr .= '<div class="video">';

// Videoes from the network -------------------------------------------------------------

$fq = '
	SELECT 
		i.*, f.DiskPath 
	FROM 
		Folder f, 
		File i 
			LEFT JOIN `SBookCategory` c ON
			(
				i.CategoryID = c.ID
			)
	WHERE
			i.NodeID = "0" 
		AND i.Filetype IN ( "webm", "ogg", "ogv", "mp4", "swf" ) 
		AND f.ID = i.FileFolder 
		' . ( isset( $_POST[ 'v' ] ) ? 'AND i.ID = \'' . $_POST[ 'v' ] . '\' ' : '' ) . '
		AND 
		(
			
			/* --- Public / Members Access --- */
			
			(
				(
						i.Access = "0"
					AND f.Access <= i.Access 
				)
				AND
				(
					
					/* --- Profile / Other Access --- */
					
					(
						i.CategoryID = "0"
					)
					OR
					(
							i.CategoryID > 0
						AND c.IsSystem = "1" 
					)
					
					/* --- Group Access --- */
					
					OR 
					(
							i.CategoryID > 0
						AND c.Privacy = "OpenGroup" 
					)
					OR 
					(
							i.CategoryID > 0
						AND c.Privacy = "ClosedGroup" 
						AND f.Name = "Cover Photos" 
					)
					OR
					(
							i.CategoryID > 0
						AND i.CategoryID IN ( !UserCats! ) 
					)
				)
			)
			
			/* --- Contact Access --- */
			
			OR
			(
					i.Access = "1"
				AND i.UserID > 0 
				AND i.UserID IN ( !UserIDS! )
				AND f.Access <= i.Access 
			)
			
			/* --- File Owner Access --- */
			
			OR
			(
					i.UserID = !UserID!
				AND i.UserID > 0 
			)
			
			/* --- Admin Access --- */
			
			OR
			(
					i.Access != "2"
				AND i.CategoryID > 0 
				AND i.CategoryID IN ( !AdminCats! ) 
			)
			
			/* --- No Owner / All Access --- */
			
			OR
			(
					i.UserID = "0" 
				AND i.CategoryID = "0" 
			)
			
		) 
	ORDER BY 
		f.ID DESC 
';


$usrs = ( $webuser && isset( $webuser->ContactID ) ? getUserContactsID( $webuser->ContactID, true ) : false );
$usrs = ( $usrs && is_array( $usrs ) ? implode( ',', $usrs ) : ( $webuser ? $webuser->ID : false ) );

$acat = ( $webuser && isset( $webuser->ContactID ) ? CategoryAccess( $webuser->ContactID, false, -1, 'IsAdmin' ) : false );
$acat = ( $acat && isset( $acat['CategoryID'] ) ? $acat['CategoryID'] : false );

$ucat = ( $webuser && isset( $webuser->ContactID ) ? CategoryAccess( $webuser->ContactID, false, -1 ) : false );
$ucat = ( $ucat && isset( $ucat['CategoryID'] ) ? $ucat['CategoryID'] : false );

// TODO: Add support for admins of groups and members of groups and super admin of the system

$fq = str_replace( '!UserID!', ( isset( $webuser ) && $webuser ? ( '\'' . $webuser->ID . '\'' ) : 'NULL' ), $fq );
$fq = str_replace( '!UserIDS!', ( isset( $usrs ) && $usrs ? $usrs : 'NULL' ), $fq );
$fq = str_replace( '!AdminCats!', ( isset( $acat ) && $acat ? $acat : 'NULL' ), $fq );
$fq = str_replace( '!UserCats!', ( isset( $ucat ) && $ucat ? $ucat : 'NULL' ), $fq );


if( $rows = $database->fetchObjectRows ( $fq ) )
{
	foreach( $rows as $r )
	{
		//die( print_r( $rows,1 ) . ' ..' );
		$poster = ''; $posterimg = '';
		$u = new dbObject( 'SBookContact' );
		$u->UserID = $r->UserID;
		$img = new dbImage ();
		if( $r->UserID > 0 && $u->Load() ) 
		{
			$poster = $u->Username;
			if( $img->Load( $u->ImageID ) )
			{
				$posterimg = $img->getImageHTML ( 40, 40, 'framed', false, 0xffffff );
			}
		}
		$c = new dbObject( 'SBookCategory' );
		if( $r->CategoryID > 0 && $c->Load( $r->CategoryID ) ) 
		{
			$poster = $c->Name;
		}
		
		$rating = explode( '/', $r->Rating );
		$total = floor( $rating[0] + $rating[1] );
		
		if( isset( $_POST[ 'mid' ] ) && $_POST[ 'mid' ] == $r->ID || isset( $_REQUEST[ 'v' ] ) && $_REQUEST[ 'v' ] == $r->ID )
		{
			$astr .= '<div id="Media_' . $r->ID . '" class="videobox Active">';
			$astr .= '<div class="media">';
			
			$astr .= '<video width="400" height="300" controls="controls" preload="auto" poster="' . $r->DiskPath . str_replace( array( ".webm", ".ogg", ".ogv", ".mp4", ".swf" ), array( "", "", "", "", "" ), $r->Filename ) . '.png">';
			$astr .= '<source src="' . $r->DiskPath . str_replace( array( ".webm", ".ogg", ".ogv", ".mp4", ".swf" ), array( "", "", "", "", "" ), $r->Filename ) . '.mp4" type="video/mp4" />';
			
			/*$astr .= '<source src="upload/' . $r->Title . '_converted.webm" type="video/webm" />';*/
			$astr .= '<source src="' . $r->DiskPath . str_replace( array( ".webm", ".ogg", ".ogv", ".mp4", ".swf" ), array( "", "", "", "", "" ), $r->Filename ) . '.ogv" type="video/ogg" />';
			
			$astr .= '<object width="400" height="300">';
			$astr .= '<param name="movie" value="http://fpdownload.adobe.com/strobe/FlashMediaPlayback.swf"></param>';
			$astr .= '<param name="flashvars" value="' . $r->DiskPath . str_replace( array( ".webm", ".ogg", ".ogv", ".mp4", ".swf" ), array( "", "", "", "", "" ), $r->Filename ) . '.png"></param>';
			$astr .= '<param name="allowFullScreen" value="true"></param>';
			$astr .= '<param name="allowscriptaccess" value="always"></param>';
			$astr .= '<embed src="http://fpdownload.adobe.com/strobe/FlashMediaPlayback.swf" type="application/x-shockwave-flash" allowscriptaccess="always" ';
			$astr .= 'allowfullscreen="true" width="400" height="300" flashvars="src=' . $r->DiskPath . str_replace( array( ".webm", ".ogg", ".ogv", ".mp4", ".swf" ), array( "", "", "", "", "" ), $r->Filename ) . '.swf&poster=' . $r->DiskPath . str_replace( array( ".webm", ".ogg", ".ogv", ".mp4", ".swf" ), array( "", "", "", "", "" ), $r->Filename ) . '.png"></embed>';
			$astr .= '</object>';
			
			$astr .= '</video>';
			
			/*$astr .= '<object data="upload/' . $r->Title . '_converted.mp4" wmode="transparent" ';
			$astr .= 'type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true">';
			$astr .= '<param name="width" value="350">';
			$astr .=	'<param name="height" value="250">';
			$astr .= '<param name="wmode" value="transparent">';
			$astr .= '<param name="movie" value="upload/' . $r->Title . '_converted.mp4">';
			$astr .= '<param name="allowfullscreen" value="true">';
			$astr .= '<param name="allowscriptaccess" value="always">';
			$astr .= '</object>';*/
			
			$astr .= '</div>';
			
			$astr .= '<div class="content">';
			$astr .= '<h3><span>' . $r->Title . '</span></h3>';
			$astr .= '<div class="posted">';
			$astr .= '<table><tr><td style="width:40px">';
			$astr .= '<div class="image">';
			$astr .= $posterimg;
			$astr .= '</div>';
			$astr .= '</td><td>';
			$astr .= '<div class="info">';
			$astr .= '<p><a href="' . $poster . '">' . $poster . '</a></p>';
			$astr .= '<p><span>' . date( 'F j', strtotime ( $r->DateCreated ) ) . ' at ' . date( 'g:ia', strtotime ( $r->DateCreated ) ) . ' </span></p>';
			$astr .= '</div>';
			$astr .= '</td><td style="width:120px;text-align:right">';
			$astr .= '<div id="MediaRating" class="rating">';
			if( isset( $_POST[ 'refresh' ] ) && $_POST[ 'refresh' ] == 'mediarating' )
			{
				$astr = '';
			}
			$astr .= '<p>' . ( ( $rating[0] - $rating[1] ) == 0 ? '' : ( $rating[0] - $rating[1] ) ) . '</p>';
			$astr .= '<div class="ratebar">';
			$astr .= '<div style="width: ' . ( $rating[0] > 0 ? ( $rating[0] / $total * 100 ) : 0 ) . '%"></div>';
			$astr .= '</div>';
			$astr .= '<p><a href="javascript:void(0)" onclick="voteMedia( \'' . $r->ID . '\', \'like\' )"><img src="subether/gfx/thumb_up_grey_icon.png"></a>';
			$astr .= '<span> ' . ( $rating[0] > 0 ? $rating[0] : '' ) . ' </span>';
			$astr .= '<a href="javascript:void(0)" onclick="voteMedia( \'' . $r->ID . '\', \'dislike\' )"><img src="subether/gfx/thumb_down_grey_icon.png"></a>';
			$astr .= '<span> ' . ( $rating[1] > 0 ? $rating[1] : '' ) . ' </span></p>';
			if( isset( $_POST[ 'refresh' ] ) && $_POST[ 'refresh' ] == 'mediarating' )
			{
				die( 'ok<!--separate-->' . $astr );
			}
			$astr .= '</div>';
			$astr .= '</td></tr></table>';
			$astr .= '</div>';
			$astr .= '<div class="description">' . $r->ContentData . '</div>';
			$astr .= '</div>';
			
			$astr .= '<div class="comments">';
			$astr .= '<div class="postbox">';
			$astr .= '<input placeholder="write your comment ..." id="CommentPost">';
			$astr .= '<button type="button" onclick="saveComment( \'' . $r->ID . '\', ge( \'CommentPost\' ) )">Comment</button>';
			$astr .= '</div>';
			$astr .= '<div id="MediaComments">';
			
			if( $comments = $database->fetchObjectRows ( '
				SELECT 
					m.*, c.Username, c.ImageID 
				FROM  
					SBookRelation r, 
					SBookMessage m, 
					SBookContact c 
				WHERE 
						r.ConnectedID = \'' . $r->ID . '\' 
					AND r.ConnectedType = "SBookFiles" 
					AND r.ObjectType = "SBookMessage" 
					AND r.Type = "MediaComment" 
					AND ( m.ID = r.ObjectID OR m.ParentID = r.ObjectID ) 
					AND c.UserID = m.SenderID 
				ORDER BY 
					m.ID DESC 
			' ) )
			{
				$cstr = ''; $crating = '';
				foreach( $comments as $c )
				{
					$crating = explode( '/', $c->Rating );
					
					$cstr .= '<div id="CommentID_' . $c->ID . '" class="commentbox">';
					if( isset( $_POST[ 'cid' ] ) && $_POST[ 'cid' ] == $c->ID )
					{
						$cstr = '';
					}
					$cstr .= '<table><tr><td style="width:37px">';
					$cstr .= '<div class="image">';
					$ci = new dbImage ();
					if( $c->ImageID > 0 && $ci->Load( $c->ImageID ) )
					{
						$cstr .= $ci->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
					}
					$cstr .= '</div>';
					$cstr .= '</td><td>';
					$cstr .= '<div class="comment">';
					$cstr .= '<p><a href="' . $c->Username . '">' . $c->Username . '</a>';
					$cstr .= '<span> ' . $c->Message . '</span></p>';
					$cstr .= '<p><span>' . date ( 'F j', strtotime ( $c->Date ) ) . ' at ' . date ( 'g:ia', strtotime ( $c->Date ) ) . ' </span>';
					$cstr .= '<span> · </span>';
					$cstr .= '<span> ' . ( ( $crating[0] - $crating[1] ) == 0 ? '' : ( $crating[0] - $crating[1] ) ) . ' </span>';
					$cstr .= '<a href="javascript:void(0)" onclick="voteComment( \'' . $c->ID . '\', \'' . $r->ID . '\', \'like\' )"><img src="subether/gfx/thumb_up_grey_icon.png"></a>';
					/*$cstr .= '<span> ' . ( $crating[0] > 0 ? $crating[0] : '' ) . ' </span>';*/
					$cstr .= '<a href="javascript:void(0)" onclick="voteComment( \'' . $c->ID . '\', \'' . $r->ID . '\', \'dislike\' )"><img src="subether/gfx/thumb_down_grey_icon.png"></a>';
					/*$cstr .= '<span> ' . ( $crating[1] > 0 ? $crating[1] : '' ) . ' </span></p>';*/
					$cstr .= '</div>';
					$cstr .= '</td></tr></table>';
					if( isset( $_POST[ 'cid' ] ) && $_POST[ 'cid' ] == $c->ID )
					{
						die( 'ok<!--separate-->' . $cstr );
					}
					$cstr .= '</div>';
				}
				$astr .= $cstr;
			}
			
			$astr .= '</div>';
			$astr .= '</div>';
			
			$astr .= '</div>';
		}
		else
		{
			$mstr .= '<div id="Media_' . $r->ID . '" class="videobox">';
			$mstr .= '<div class="thumb">' . '<img src="' . $r->DiskPath . str_replace( array( ".webm", ".ogg", ".ogv", ".mp4", ".swf" ), array( "", "", "", "", "" ), $r->Filename ) . '.png"/>';
			if( $r->Fileduration != '' ) 
			{
				$mstr .= '<div class="duration">' . $r->Fileduration . '</div>';
			}
			$mstr .= '</div>';
			$mstr .= '<div class="content">';
			//$mstr .= '<h3><a href="javascript:void(0)" onclick="refreshMedia( ' . $r->ID . ' )"><span>' . $r->Title . '</span></a></h3>';
			$mstr .= '<h3><a href="browse/?v=' . $r->ID . ( $_REQUEST[ 'q' ] ? ( '&q=' . $_REQUEST[ 'q' ] ) : '' ) . '&r=videos"><span>' . $r->Title . '</span></a></h3>';
			$mstr .= '<div>';
			$mstr .= '<span>' . $poster . '</span> ';
			$mstr .= '<span>' . date( 'Y-m-d', strtotime( $r->DateCreated ) ) . '</span> ';
			$mstr .= '</div>';
			$mstr .= '</div>';
			$mstr .= '</div>';
		}
	}
	//$mstr .= '<div class="clearboth"></div>';
}

// --- Youtube videos -------------------------------------------------------------------

if ( !defined( 'HIDE_EXTERNAL_VIDEOS' ) || !HIDE_EXTERNAL_VIDEOS )
{
	foreach( array( YouTubeParser( str_replace( ' ', '+', $_REQUEST[ 'q' ] ) ) ) as $rows )
	{
		if( $rows )
		{
			//die( print_r( $rows,1 ) . ' ..' );
			$check = array();
			foreach( $rows as $r )
			{
				if( !$r->ID || in_array( $r->ID, $check ) ) continue;
				
				if( isset( $_REQUEST[ 'v' ] ) && $_REQUEST[ 'v' ] == $r->ID )
				{
					$astr .= '<div id="YouTube_' . $r->ID . '" class="videobox Active"><div class="media">';
					$astr .= embedYoutube ( $r->href, '400', '300' );
					$astr .= '</div></div>';
					//die( $astr . ' ..' );
				}
				else
				{
					$mstr .= '<div id="YouTube_' . $r->ID . '" class="videobox">';
					$mstr .= '<div class="thumb"><a href="browse/?v=' . $r->ID . ( $_REQUEST[ 'q' ] ? ( '&q=' . $_REQUEST[ 'q' ] ) : '' ) . '&r=videos"><img style="background-image:url(' . $r->thumb . ');"/></a>';
					$mstr .= '<div class="source"></div>';
					if( $r->duration != '' ) 
					{
						$mstr .= '<div class="duration">' . $r->duration . '</div>';
					}
					$mstr .= '</div>';
					$mstr .= '<div class="content">';
					$mstr .= '<h3><a href="en/home/browse/?v=' . $r->ID . ( $_REQUEST[ 'q' ] ? ( '&q=' . $_REQUEST[ 'q' ] ) : '' ) . '&r=videos"><span>' . $r->title . '</span></a></h3>';
					$mstr .= '<div>';
					$mstr .= '<span>' . $r->by . '</span> ';
					$mstr .= '<span>' . $r->added . '</span> ';
					$mstr .= '</div>';
					$mstr .= '</div>';
					$mstr .= '</div>';
				}
				
				$check[] = $r->ID;
			}
		}
	}
}

$mstr .= '<div class="clearboth"></div>';
$mstr .= '</div>';

if( isset( $_POST[ 'refresh' ] ) && $_POST[ 'refresh' ] == 'comments' )
{
	$mstr = $cstr;
}
else if( isset( $_POST[ 'mid' ] ) && $_POST[ 'mid' ] > 0 || isset( $_REQUEST[ 'v' ] ) && $_REQUEST[ 'v' ] != '' )
{
	$mstr = '<table><tr><td class="LeftCol">' . $astr . '</td><td class="RightCol">' . $mstr . '</td></tr></table>';
}

if( isset( $_POST[ 'mid' ] ) ) die( 'ok<!--separate-->' . $mstr );

?>
