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

	if( isset( $_REQUEST[ 'p' ] ) && $_REQUEST[ 'p' ] == $p->ID )
	{
		$str = '';
	}

	$posterimage = ''; $replyimage = '';
	$i = new dbImage ();
	if( $i->load( $p->Image ) )
	{
		$posterimage = $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
		$replyimage = $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
	}
	$rating = explode( '/', $p->Rating );
	$str .= '<div class="Comment Article" onmouseover="IsEditing(1)" onmouseout="IsEditing()">';
	$str .= '<div class="Box" id="MessageID_' . $p->ID . '">';
	if( $p->SenderID == $user->ID || ( $module == 'profile' && $user->ID == $cuser->ID ) )
	{
		$str .= '<div class="Edit" onclick="deleteWallContent(' . $p->ID . ')"><div></div></div>';
	}
	if( isset( $_POST[ 'mid' ] ) && $_POST[ 'mid' ] == $p->ID )
	{
		$str = '';
	}
	$str .= '<div class="messagebox">';
	$str .= '<h2><a href="' . $path . 'wall/' . UrlFromString( $p->Subject ) . '/?p=' . $p->ID . '">' . $p->Subject . '</a></h2>';
	$str .= '<div class="posted"><table><tr><td><div class="image"><a href="' . $path . $p->Name . '">' . $posterimage . '</a></div></td><td><div><p><a href="' . $path . $p->Name . '">' . ( $p->DisplayName ? $p->DisplayName : $p->Name ) . '</a></p><p>' . TimeToHuman( $p->Date ) . '</p></div></td></tr></table></div>';
	$str .= '<div>' . $p->Leadin . '</div>';
	if( isset( $_REQUEST[ 'p' ] ) && $_REQUEST[ 'p' ] == $p->ID )
	{
		$str .= '<p>' . parseText ( $p->Message, '760', '515' ) . '</p>';
	}
	$str .= '<div class="Buttons">';
	if( $parent->folder->ObjectID )
	{
		$str .= '<a href="javascript:void(0)" onclick="replyToMessage( \'' . $p->ID . '\', \'' . $userimg . '\' )">' . i18n ( 'i18n_comment' ) . '</a>&nbsp;';
	}
	$str .= '<a href="javascript:void(0)" onclick="openWindow( \'Wall\', \'' . $p->ID . '\', \'share\' )">' . i18n ( 'i18n_share' ) . '</a>&nbsp;';
	$str .= '<span> ' . ( ( $rating[0] - $rating[1] ) == 0 ? '' : ( $rating[0] - $rating[1] ) ) . ' </span>';
	if( $parent->folder->ObjectID )
	{
		$str .= '<a href="javascript:void(0)" onclick="voteComment( \'' . $p->ID . '\', \'like\' )"><img class="voteimg" title="Like" src="subether/gfx/thumb_up_grey_icon.png"></a>';
		$str .= '<a href="javascript:void(0)" onclick="voteComment( \'' . $p->ID . '\', \'dislike\' )"><img class="voteimg" title="Dislike" src="subether/gfx/thumb_down_grey_icon.png"></a>';
	}
	$str .= '</div>';
	$str .= '</div>';
	if( isset( $_POST[ 'mid' ] ) && $_POST[ 'mid' ] == $p->ID )
	{
		die( 'ok<!--separate-->' . $str );
	}
	$str .= '</div>';
	$str .= '<div id="mBox_' . $p->ID . '">';
	
	if ( $p->NodeMainID > 0 )
	{
		$qc = '
			SELECT 
				m.*, u.Display, u.Firstname, u.Middlename, u.Lastname, u.Username AS Name, u.ImageID AS Image 
			FROM 
				SBookMessage m, 
				SBookContact u 
			WHERE
				m.NodeMainID > 0 
				AND m.ParentID = \'' . $p->NodeMainID . '\' 
				AND u.UserID = m.SenderID
				AND u.NodeID = m.NodeID 
			ORDER BY  
				m.ID ASC 
		';
	}
	else
	{
		$qc = '
			SELECT 
				m.*, u.Display, u.Firstname, u.Middlename, u.Lastname, u.Username AS Name, u.ImageID AS Image 
			FROM 
				SBookMessage m, 
				SBookContact u 
			WHERE
				m.ParentID = \'' . $p->ID . '\' 
				AND u.UserID = m.SenderID
				AND u.NodeID = m.NodeID 
			ORDER BY  
				m.ID ASC 
		';	
	}
	
	if( $comments = $database->fetchObjectRows( $qc ) )
	{
		foreach( $comments as $c )
		{
			if( $c->ParentID == 0 ) continue;
			
			if( $c->Firstname ) $first = $c->Firstname . ' '; else $first = '';
			if( $c->Middlename ) $middle = $c->Middlename . ' '; else $middle = '';
			if( $c->Lastname ) $last = $c->Lastname . ' '; else $last = '';
			
			if( $c->Display == 1 )
			{
				$c->DisplayName = trim( $first . $middle . $last );
			}
			else if( $c->Display == 2 )
			{
				$c->DisplayName = trim( $first . $last );
			}
			else if( $c->Display == 3 )
			{
				$c->DisplayName = trim( $last . $first );
			}
			else $c->DisplayName = $c->Name;
			
			$posterimage = ''; $replyimage = '';
			$i = new dbImage ();
			if( $i->load( $c->Image ) )
			{
				$posterimage = $i->getImageHTML ( 40, 40, 'framed', false, 0xffffff );
				$replyimage = $i->getImageHTML ( 30, 28, 'framed', false, 0xffffff );
			}
			$crating = explode( '/', $c->Rating );
			$str .= '<div class="Comment">';
			$str .= '<div class="Box" id="MessageID_' . $c->ID . '">';
			if( $c->SenderID == $user->ID || ( $module == 'profile' && $user->ID == $cuser->ID ) )
			{
				$str .= '<div class="Edit" onclick="deleteWallContent(' . $c->ID . ')"><div></div></div>';
			}
			if( isset( $_POST[ 'mid' ] ) && $_POST[ 'mid' ] == $c->ID )
			{
				$str = '';
			}
			$str .= '<div class="commentbox">';
			$str .= '<table><tr>';
			$str .= '<td style="width:37px"><div class="image"><a href="' . $path . $c->Name . '">' . $replyimage . '</a></div></td>';
			$str .= '<td><div><p><a href="' . $path . $c->Name . '"><u>' . ( $c->DisplayName ? $c->DisplayName : $c->Name ) . '</u></a> <span>' . parseText ( $c->Message ) . '</span></p>';
			$str .= StrReplaceByAttribute( $c->HTML, 'replace=""', 'onclick="embedVideo(this,\'710\',\'380\')"' );
			$str .= '<p><span>' . TimeToHuman( $c->Date ) . ' </span>';
			$str .= '<span> ' . ( ( $crating[0] - $crating[1] ) == 0 ? '' : ( $crating[0] - $crating[1] ) ) . ' </span>';
			if( $parent->folder->ObjectID )
			{
				$str .= '<a href="javascript:void(0)" onclick="voteComment( \'' . $c->ID . '\', \'like\' )"><img class="voteimg" title="Like" src="subether/gfx/thumb_up_grey_icon.png"></a>';
				$str .= '<a href="javascript:void(0)" onclick="voteComment( \'' . $c->ID . '\', \'dislike\' )"><img class="voteimg" title="Dislike" src="subether/gfx/thumb_down_grey_icon.png"></a>';
			}
			$str .= '</p></div></td>';
			$str .= '</tr></table>';
			$str .= '</div>';
			if( isset( $_POST[ 'mid' ] ) && $_POST[ 'mid' ] == $c->ID )
			{
				die( 'ok<!--separate-->' . $str );
			}
			$str .= '</div>';
			$str .= '</div>';
		}
		
		$str .= '<div class="ReplyBox"><div class="Box"><table><tr><td style="width:37px">';
		$str .= '<div class="image">';
		if( $userimage )
		{
			$str .= $userimage;
		}
		$str .= '</div></td><td>';
		$str .= '<div class="reply"><input id="ReplyContent_' . $p->ID . '" placeholder="Write a comment...">';
		$str .= '<button type="button" onclick="sendReply(\'' . $p->ID . '\', ge( \'ReplyContent_' . $p->ID . '\' ) )">REPLY</button></div>';
		$str .= '</td></tr></table></div></div>';
	}
	
	$str .= '</div>';
	$str .= '</div>';
	
	// Set status IsRead on user notify
	IsRead( $p->ID );
	
	if( isset( $_REQUEST[ 'p' ] ) && $_REQUEST[ 'p' ] == $p->ID )
	{
		$astr = $str; 
	}

?>
