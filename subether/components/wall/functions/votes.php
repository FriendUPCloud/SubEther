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

// TODO: Mode all of this to main, only php and html rendering mode is supposed to be here

$found = false;

$p->Rating = json_obj_decode( $p->Rating );
$p->Options = json_obj_decode( $p->Options );
//die( print_r( $p->Options,1 ) . ' --' );
if( $p->Rating->Votes )
{
	$sbstr = array(); $i = 0;
	
	foreach( $p->Rating->Votes as $key => $check )
	{
		if( is_array( $check ) && in_array( $webuser->ContactID, $check ) )
		{
			$found = true;
		}
		
		if( is_array( $check ) )
		{
			$i = ( $i ? ( $i + count( $check ) ) : count( $check ) );
			
			foreach( $check as $uid )
			{
				$sbstr[] = $key . ' | ' . GetUserDisplayname( $uid );
			}
		}
	}
	
	if( $p->SenderID == $user->ContactID )
	{
		$p->Rating->RateList = $sbstr;
	}
	
	$p->Rating->RateAmount = $i;
}

if( $p->SenderID == $user->ContactID || ( $module == 'profile' && $user->ContactID == $cuser->ContactID ) || IsSystemAdmin() )
{
	$p->HasAccess = true;
}

if( $focusid > 0 && $focusid == $p->ID )
{
	$p->IsFocus = true;
}

if( strtolower( $folder->MainName ) == 'profile' && strtolower( $module ) == 'main' && $p->IsGroup )
{
	$p->Target = 'group';
}
else if( strtolower( $folder->MainName ) == 'profile' && strtolower( $module ) == 'main' && !$p->IsGroup && $p->Receiver && ( $p->ReceiverID != $p->SenderID ) )
{
	$p->Target = 'profile';
}

$p->Sender = ( GetUserDisplayname( $p->SenderID ) ? GetUserDisplayname( $p->SenderID ) : $p->Name );
$p->Receiver = ( GetUserDisplayname( $p->ReceiverID ) ? GetUserDisplayname( $p->ReceiverID ) : $p->User_Name );

if( $p->SenderID == $user->ContactID && is_array( $p->SeenBy ) )
{
	$sbstr = array(); $i = 0;
	
	foreach( $p->SeenBy as $uid )
	{
		$sbstr[] = GetUserDisplayname( $uid );
		$i++;
	}
	
	$p->SeenList = $sbstr;
	$p->SeenAmount = $i;
}




// --- Output ------------------------------------------------------------------------------------------------------------------------

if( $_POST['mid'] != $p->ID )
{
	$str .= '<div class="messagebox' . ( $p->IsFocus ? ' focus' : '' ) . '" id="MessageID_' . $p->ID . '" tabindex="0">';
}

// --- Wall posts --------------------------------------------------------------------------------------------------------------------
$str .= 	'<div class="post" id="MessagePost_' . $p->ID . '">';
// --- Posted ------------------------------------------------------------------------------------------------------------------------
$str .= 		'<div class="posted">';
// --- Avatar ------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="avatar">';
$str .= 				'<a href="' . $path . $p->Name . '">';
if( $imgs[$p->Image] )
{
	$str .= 				'<div style="background-image:url(\'' . $imgs[$p->Image]->DiskPath . '\');"></div>';
}
$str .= 				'</a>';
$str .= 			'</div>';
// --- Nickname ----------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="nickname">';
$str .= 				'<a class="sender" href="' . $path . $p->Name . '">' . $p->Sender . '</a>';
switch( $p->Target )
{
	case 'group':
		$str .= 		'<span class="seperator"> &#10148; </span><a class="receiver" href="' . $path . 'groups/' . $p->SBC_ID . '/wall/">' . $p->SBC_Name . '</a>';
		break;
	
	case 'profile':
		$str .= 		'<span class="seperator"> &#10148; </span><a class="receiver" href="' . $path . $p->User_Name . '">' . $p->Receiver . '</a>';
		break;
}
$str .= 			'</div>';
/*// --- Edit --------------------------------------------------------------------------------------------------------------------------
if( $p->HasAccess )
{
	$str .= 		'<div class="edit">';
	$str .= 			'<div class="options" onclick="WallOptions(this,\'' . $p->ID . '\')"></div>';
	$str .= 		'</div>';
}*/
// --- Edit --------------------------------------------------------------------------------------------------------------------------
if( $p->HasAccess )
{
	$str .= 		'<div class="edit">';
	$str .= 			'<div class="options" onclick="WallOptions(this);return cancelBubble(event)"></div>';
	
	$str .= '			<div class="walloptions" onclick="WallOptions(this);return cancelBubble(event)">';
	$str .= '				<div class="toparrow"></div>';
	$str .= '				<div class="inner">';
	
	$str .= '					<ul>';
	//$str .= '						<li><div onclick="embedPostEditor(\'' . $p->ID . '\')"><span>' . i18n( 'i18n_Edit' ) . '</span></div></li>';
	$str .= '						<li><div onclick="deleteWallContent(' . $p->ID . ')"><span>' . i18n( 'i18n_Delete' ) . '</span></div></li>';
	$str .= '					</ul>';
	
	$str .= '				</div>';
	$str .= '			</div>';
	
	$str .= 		'</div>';
}
// --- Date --------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="date">' . TimeToHuman( $p->Date ) . '</div>';
// --- Access ------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="access">';
if( $p->HasAccess )
{
	$str .= 			'<select onchange="UpdateAccess(\'' . $p->ID . '\',this.value)">';
	$str .= 				'<option value="0"' . ( $p->Access == 0 ? ' selected="selected"' : '' ) . '>Public</option>';
	$str .= 				'<option value="1"' . ( $p->Access == 1 ? ' selected="selected"' : '' ) . '>Contacts</option>';
	$str .= 				'<option value="2"' . ( $p->Access == 2 ? ' selected="selected"' : '' ) . '>Only Me</option>';
	//$str .= 				'<option value="3"' . ( $p->Access == 3 ? ' selected="selected"' : '' ) . '>Custom</option>';
	if( isset( $parent->access->IsAdmin ) )
	{
		$str .= 				'<option value="4"' . ( $p->Access == 4 ? ' selected="selected"' : '' ) . '>Admin</option>';
	}
	$str .= 			'</select>';
}
$str .= 			'</div>';
// --- Clear -------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="clearboth"></div>';
// --- Posted end --------------------------------------------------------------------------------------------------------------------
$str .= 		'</div>';
// --- Content -----------------------------------------------------------------------------------------------------------------------
$str .= 		'<div class="content" id="MessageContent_' . $p->ID . '"><h3>' . convertToTags( $p->Message ) . '</h3></div>';
// --- Options -----------------------------------------------------------------------------------------------------------------------
$str .=     	'<div class="voteoptions' . ( $found ? ' showresult' : '' ) . '" id="VoteOptions_' . $p->ID . '">';
// --- Vote --------------------------------------------------------------------------------------------------------------------------
foreach( ( $p->Options && is_array( $p->Options ) ? $p->Options : array( 0=>'No', 1=>'Yes' ) ) as $k=>$opt )
{
	$key = ( $k+1 );
	
	$str .= 	'<div class="option"><div class="result">';
	$str .= 	'<div class="rate" style="width:' . ( $p->Rating->RateAmount > 0 ? ( ( count( $p->Rating->Votes->{$key} ) / $p->Rating->RateAmount * 100 ) ) : '0' ) . '%"></div>';
	$str .= 	'<div class="info" title="' . count( $p->Rating->Votes->{$key} ) . ' / ' . $p->Rating->RateAmount . '"><span class="num" style="float:right;">' . round( $p->Rating->RateAmount > 0 ? ( ( count( $p->Rating->Votes->{$key} ) / $p->Rating->RateAmount * 100 ) ) : '0' ) . '%</span>';
	$str .= 	'<span class="name"> ' . $key . ' | ' . $opt . ' </span></div></div>';
	$str .= 	'<div class="input" onclick="voteComment(\'' . $p->ID . '\',\'' . $key . '\');this.getElementsByTagName(\'input\')[0].checked = true;"><input type="radio"/><span> ' . $opt . ' </span></div>';
	$str .= 	'</div>';
}
// --- Vote end ----------------------------------------------------------------------------------------------------------------------
$str .= 		'</div>';
// --- Buttons -----------------------------------------------------------------------------------------------------------------------
$str .= 		'<div class="buttons">';
// --- See result --------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="result seen">';

if( isset( $p->Rating->RateAmount ) )
{
	$str .= 			'<span class="amount" onmouseover="tooltips(this,\'open\')" onmouseout="tooltips(this,\'close\')">' . $p->Rating->RateAmount . '&nbsp;Votes</span>';
	$str .= 			( $p->Rating->RateList ? '<div class="tooltips"><div class="bottomarrow"></div><div class="inner"><ul><li>' . implode( '</li><li>', $p->Rating->RateList ) . '</li></ul></div></div>' : '' );
	$str .= 			( !$found ? '<span class="result" onclick="SeeResult(\'VoteOptions_' . $p->ID . '\',this)">&nbsp;|&nbsp;See Result&nbsp;</span>' : '' );
}

// --- Se result end -----------------------------------------------------------------------------------------------------------------
$str .= 			'</div>';
// --- Seen --------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="seen">';
if( $p->SeenAmount > 0 )
{
	$str .= 			'<span class="amount" onmouseover="tooltips(this,\'open\')" onmouseout="tooltips(this,\'close\')">Seen by ' . $p->SeenAmount . '&nbsp;|&nbsp;</span>';
	$str .= 			'<div class="tooltips"><div class="bottomarrow"></div><div class="inner"><ul><li>' . implode( '</li><li>', $p->SeenList ) . '</li></ul></div></div>';
}
$str .= 			'</div>';
// --- Clear -------------------------------------------------------------------------------------------------------------------------
$str .= 			'<div class="clearboth"></div>';
// --- Buttons end -------------------------------------------------------------------------------------------------------------------
$str .= 		'</div>';
// --- Clear -------------------------------------------------------------------------------------------------------------------------
$str .= 		'<div class="clearboth"></div>';
// --- Post end ----------------------------------------------------------------------------------------------------------------------
$str .= 	'</div>';

// --- Clear ----------------------------------------------------------------------------------------------------------------------------
$str .= '<div class="clearboth"></div>';

// --- Output end -----------------------------------------------------------------------------------------------------------------------

if( $_POST['mid'] != $p->ID )
{
	$str .= '</div>';
}

// Set status IsRead on user notify
if( $p->IsFocus )
{
	IsRead( $p->ID );
}


?>
