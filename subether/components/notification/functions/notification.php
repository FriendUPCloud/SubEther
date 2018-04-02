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

global $webuser, $database;

$mode = ''; $str = '';

/* Commented out because this is not used.

// Chat Settings
$wu = new dbObject( 'SBookContact' );
$wu->UserID = $webuser->ID;
if( $wu->Load() )
{
	$wu->Data = json_decode( $wu->Data );
	
	if( $wu->Data && $wu->Data->Settings->Chat == '1' )
	{
		$mode = 'window';
	}
	else
	{
		$mode = '';
	}
}*/
/*
if( isset( $_POST[ 'voicechat' ] ) )
{
	// Update Notify to IsNoticed
	$n = new dbObject( 'SBookNotification' );
	$n->ObjectID = $_POST[ 'voicechat' ];
	$n->Type = 'voicechat';
	$n->IsNoticed = '1';
	if( $n->Load() && strstr( $n->ReceiverID, $webuser->ID ) )
	{
		$n->ReceiverID = str_replace( ( $webuser->ID . ',' ), '', $n->ReceiverID );
		$n->Save();
	}
}
*/

// --- Messages is noticed ----------------------------------------------------------
if( isset( $_POST[ 'messages' ] ) )
{
	/*foreach( explode( ',', $_POST[ 'messages' ] ) as $usr )
	{
		// Update Notify to IsRead
		$nms = new dbObject( 'SBookMail' );
		$nms->SenderID = $usr;
		$nms->ReceiverID = $webuser->ContactID;
		if( $nms = $nms->Find() )
		{
			foreach( $nms as $nm )
			{
				$m = new dbObject( 'SBookMail' );
				if( $m->Load( $nm->ID ) )
				{
					//$m->IsRead = 1;
					$m->IsNoticed = 1;
					$m->DateModified = date( 'Y-m-d H:i:s' );
					$m->Save();
				}
				
				// Delete notification
				$n = new dbObject( 'SBookNotification' );
				$n->ObjectID = $nm->ID;
				$n->Type = 'chat';
				$n->SenderID = $usr;
				$n->ReceiverID = $webuser->ID;
				if( $n->Load() )
				{
					$n->Delete();
				}
			}
		}
	}*/	
}

// --- Contacts is noticed ---------------------------------------------------------
if( isset( $_POST[ 'contacts' ] ) )
{
	foreach( explode( ',', $_POST[ 'contacts' ] ) as $usr )
	{
		$ct = new dbObject( 'SBookContact' );
		$ct->ID = $usr;
		if( $ct->Load() )
		{
			// Update Notify to IsNoticed
			$r = new dbObject( 'SBookContactRelation' );
			$r->ContactID = $ct->ID;
			$r->ObjectType = 'SBookContact';
			$r->ObjectID = $webuser->ContactID;
			if( $r->Load() )
			{
				$r->IsNoticed = 1;
				$r->Save();
			}
			
			/*// TODO remove old way
			// Update Notify to IsNoticed
			$scr = new dbObject( 'SBookContactRelation' );
			$scr->ObjectID = $webuser->ID;
			$scr->ObjectType = 'Users';
			$scr->ContactID = $ct->ID;
			if( $scr = $scr->Find() )
			{
				foreach( $scr as $sc )
				{
					$c = new dbObject( 'SBookContactRelation' );
					if( $c->Load( $sc->ID ) )
					{
						$c->IsNoticed = 1;
						$c->Save();
					}
				}
			}*/
		}
	}	
}

// --- Notices is noticed ----------------------------------------------------------
if( isset( $_POST[ 'notices' ] ) )
{
	/*foreach( explode( ',', $_POST[ 'notices' ] ) as $ids )
	{*/
		/*// Update Notify to IsNoticed
		$sn = new dbObject( 'SBookNotification' );
		//$n->ObjectID = $ids;
		$sn->Type = 'wall';
		$sn->IsNoticed = '1';
		if( $sn = $sn->Find() )
		{
			foreach( $sn as $ns )
			{
				if( !strstr( $ns->ReceiverID, $webuser->ID ) ) continue;
				
				$n = new dbObject( 'SBookNotification' );
				if( $n->Load( $ns->ID ) )
				{
					$n->ReceiverID = str_replace( ( $webuser->ID . ',' ), '', $n->ReceiverID );
					$n->Save();
					
					if( $n->ReceiverID == '' || $n->ReceiverID == ',' || $n->ReceiverID == '0' )
					{
						$n->Delete();
					}
				}
			}
		}*/
	/*}*/
	//die( $_POST[ 'notices' ] . ' .. ' . $n->ReceiverID . ' .. ' . $webuser->ID );
}

// --- Messages notifications -----------------------------------------------------
if( $sm = $database->fetchObjectRows ( '
	SELECT 
		m.* 
	FROM 
		SBookMail m 
	WHERE 
			m.ReceiverID = \'' . $webuser->ContactID . '\'
		AND m.SenderID > 0 
		AND m.IsNoticed = "0"
		AND m.Type IN ( "im", "vm", "cm" ) 
	ORDER BY 
		m.Date DESC 
', false, 'components/notification/functions/notification.php' ) )
{
	$ustr = ''; $nstr = ''; $umod = ''; $imgr = ''; $alert = ''; $msg = ''; $i = 0; $uids = array(); $imgags = array(); $checked = array();
	
	$im = 'admin/gfx/arenaicons/user_johndoe_32.png';
	
	foreach( $sm as $m )
	{
		if( $m->SenderID > 0 )
		{
			$uids[$m->SenderID] = $m->SenderID;
		}
	}
	
	if( $uids && is_array( $uids ) && ( $img = $database->fetchObjectRows( $q = '
		SELECT
			c.ID AS ContactID, f.DiskPath, i.* 
		FROM
			SBookContact c, Folder f, Image i
		WHERE
			c.ID IN (' . implode( ',', $uids ) . ') AND i.ID = c.ImageID AND f.ID = i.ImageFolder
		ORDER BY
			i.ID ASC 
	', false, 'components/notification/functions/notification.php' ) ) )
	{
		$imgags = array();
		
		foreach( $img as $iii )
		{
			$obj = new stdClass();
			$obj->ID = $iii->ID;
			$obj->Filename = $iii->Filename;
			$obj->FileFolder = $iii->ImageFolder;
			$obj->Filesize = $iii->Filesize;
			$obj->FileWidth = $iii->Width;
			$obj->FileHeight = $iii->Height;
			$obj->DiskPath = str_replace( ' ', '%20', ( $iii->DiskPath != '' ? $iii->DiskPath : ( BASE_URL . 'upload/images-master/' ) ) . $iii->Filename );
			
			if ( $iii->Filename )
			{
				$obj->DiskPath = ( BASE_URL . 'secure-files/images/' . ( $iii->UniqueID ? $iii->UniqueID : $iii->ID ) . '/' );
			}
			
			$imgags[$iii->ContactID] = $obj;
		}
	}
	//die( print_r( $uids,1 ) . ' --' );
	$uids = GetUserDisplayname( $uids );
	
	$liveurl = '';
	
	foreach( $sm as $m )
	{
		// If it has gone 5min since the message was created and has not been alerted, ship alerting old messages
		if( $m->IsAlerted == 0 && ( date( 'YmdHi' ) - date( 'YmdHi', strtotime( $m->Date ) ) ) >= 5 )
		{
			$m->IsAlerted = 1;
		}
		
		$ustr = ( $ustr ? ( $ustr . ',' . $m->SenderID ) : $m->SenderID );
		$umod = ( $umod ? ( $umod . ',' . $mode ) : $mode );
		$nstr = ( $nstr ? ( $nstr . ( isset( $uids[$m->SenderID] ) ? ( ',' . $uids[$m->SenderID] ) : '' ) ) : ( isset( $uids[$m->SenderID] ) ? $uids[$m->SenderID] : '' ) );
		$imgr = ( $imgr ? ( $imgr . ',' . ( isset( $imgags[$m->SenderID] ) ? $imgags[$m->SenderID]->DiskPath : $im ) ) : ( isset( $imgags[$m->SenderID] ) ? $imgags[$m->SenderID]->DiskPath : $im ) );
		$msg = ( $msg ? ( $msg . ',' . $m->Message ) : $m->Message );
		
		$alert = ( $alert ? ( $alert . ',' . $m->IsAlerted ) : $m->IsAlerted );
		
		// If we have voicechat notification
		if ( ( $m->Type == 'vm' || strstr( $m->Message, '&zwnj;&zwnj;&zwnj;' ) ) && $m->IsAlerted == '0' && $m->ReceiverID == $webuser->ContactID )
		{
			if ( strstr( $m->Message, '&zwnj;&zwnj;&zwnj;' ) && ( preg_match( '/&zwnj;&zwnj;&zwnj;.*(http[s]?:\/\/[^\\s]+)/i', $m->Message, $matches ) ) )
			{
				$liveurl = $matches[1];
			}
			else
			{
				$liveurl = $m->Message;
			}
		}
		
		
		
		// If we have voicechat notification
		//if ( $m->Type == 'vm' && $m->IsAlerted == '0' && $m->ReceiverID == $webuser->ContactID )
		//{
		//	$liveurl = $m->Message;
		//}
		
		if( !in_array( $m->SenderID, $checked ) ) $i++;
		$checked[$m->SenderID] = $m->SenderID;
	}
	
	$messages = 'messages<!--separate-->' . $ustr . '<!--separate-->' . $nstr . '<!--separate-->' . ( $i > 0 ? $i : '' ) . '<!--separate-->' . $umod . '<!--separate-->' . $imgr . '<!--separate-->' . $alert . '<!--separate-->' . $msg . '<!--separate-->' . $liveurl;
}

// --- Contacts notifications ---------------------------------------------------
$i = 0;
$contacts = array();
/*// TODO remove old way
if( $data = getContacts( 'Users', $webuser->ID, 'IsNoticed' ) )
{
	foreach( $data as $d )
	{
		$contacts[] = $d;
	}
}*/
if( $data = ContactRelations( $webuser->ContactID, 'Pending', 'ReceiverID' ) )
{
	foreach( $data as $d )
	{
		$contacts[] = $d;
	}
}
if( $contacts )
{
	$i = count( $contacts );
	
	/*foreach( $contacts as $cs )
	{
		$i++;
	}*/
}
$contacts = ( $i > 0 ? ( 'contacts<!--separate-->' . $i ) : '' );

// --- Notice notifications ----------------------------------------------------
$i = 0; $limit = 50;
//if( $notices = Notifications( $limit ) )
if( $notices = GetNotifications( $limit, true ) )
{
	$i = count( $notices );
	
	/*foreach( $notices as $ns )
	{
		$i++;
	}*/
}
$notices = ( $i > 0 ? ( 'notices<!--separate-->' . $i ) : '' );

// --- Connects notifications --------------------------------------------------
$i = 0;
if( IsSystemAdmin() && $connects = $database->fetchObjectRows ( '
	SELECT
		ID
	FROM
		SNodes
	WHERE
			IsConnected = "0"
		AND IsPending = "0" 
		AND IsDenied = "0"
		AND IsAllowed = "0"
	ORDER BY
		ID ASC
', false, 'components/notification/functions/notification.php' ) )
{
	$i = count( $connects );
	
	/*foreach( $connects as $co )
	{
		$i++;
	}*/
}
$connects = ( $i > 0 ? ( 'connects<!--separate-->' . $i ) : '' );

// --- Cart content ------------------------------------------------------------
$i = 0;
if( $cart = $database->fetchObjectRows ( '
	SELECT
		i.ID
	FROM
		SBookOrders o,
		SBookOrderItems i
	WHERE
			o.CustomerID = \'' . $webuser->ContactID . '\' 
		AND o.IsDeleted = "0" 
		AND !o.Status 
		AND i.OrderID = o.ID 
		AND i.IsDeleted = "0" 
	ORDER BY
		ID ASC
', false, 'components/notification/functions/notification.php' ) )
{
	$i = count( $cart );
}
$cart = ( $i > 0 ? ( 'cart<!--separate-->' . $i ) : '' );

// --- Output ------------------------------------------------------------------
if( isset( $_REQUEST[ 'function' ] ) ) output( ( $messages ? $messages . '<!--split-->' : 'none<!--split-->' ) . ( $contacts ? $contacts . '<!--split-->' : 'none<!--split-->' ) . ( $notices ? $notices . '<!--split-->' : 'none<!--split-->' ) . ( $connects ? $connects . '<!--split-->' : 'none<!--split-->' ) . ( $cart ? $cart . '<!--split-->' : 'none<!--split-->' ) );

// TODO: Counting correctly??
if ( is_string ( $messages ) ) $messages = count ( explode ( ',', $messages ) );

?>
