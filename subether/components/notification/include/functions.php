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

function Notifications( $limit = 50 )
{
    global $webuser, $database;
    
	$guci = getUserContactsID( $webuser->ContactID );
	$gucids = is_string( $guci ) ? implode( ',', $guci ) : $webuser->ContactID;
	$uci = getUserGroupsID( $webuser->ID );
	$ucids = $uci ? implode( ',', $uci ) : false;
	
	$qn = '
		SELECT * FROM
		(
			(
				SELECT 
					sm.*,
					sm.ID AS PostID, 
					us.Username AS Name, 
					us.ImageID AS Image,
					u2.Username AS User_Name, 
					ca.Name AS SBC_Name, 
					ca.ID AS SBC_ID, 
					( sm.CategoryID != !GetWallID! ) AS `IsGroup` 
				FROM
					SBookContact us, 
					SBookMessage sm 
						LEFT JOIN SBookContact u2 ON
						(
								sm.ReceiverID = u2.ID
							AND sm.ReceiverID > 0
						) 
						LEFT JOIN SBookCategory ca ON
						(
								sm.CategoryID = ca.ID
							AND sm.CategoryID > 0
						) 
				WHERE 
						sm.ParentID = "0"
					AND sm.NodeID = "0" 
					AND sm.Type = "post" 
					AND sm.SenderID != !ContactID!
					AND sm.Date >= !UserDateCreated! 
					AND
					(
						' .  ( $ucids ? '
						(
								sm.CategoryID IN ( !UcIDS! ) 
							AND us.ID = sm.SenderID
						) 
						OR  ' : '' ) . '
						(
								sm.CategoryID = !GetWallID! 
							AND us.ID = sm.SenderID 
							AND sm.ReceiverID IN ( !GucIDS! )
						)
					) 
					AND
					(
						(
							sm.Access = "2" 
							AND sm.SenderID = !ContactID!
						) 
						OR
						(
								sm.Access = "1" 
							AND sm.SenderID IN ( !GucIDS! )
						) 
						OR
						(
							sm.Access = "0"
						)
					) 
			)
			UNION
			(
				SELECT 
					sc.*, 
					sm.ID AS PostID, 
					us.Username AS Name, 
					us.ImageID AS Image, 
					u2.Username AS User_Name, 
					ca.Name AS SBC_Name, 
					ca.ID AS SBC_ID, 
					( sm.CategoryID != !GetWallID! ) AS `IsGroup` 
				FROM
					SBookContact us, 
					SBookMessage sc, 
					SBookMessage sm 
						LEFT JOIN SBookContact u2 ON
						(
								sm.ReceiverID = u2.ID
							AND sm.ReceiverID > 0
						) 
						LEFT JOIN SBookCategory ca ON
						(
								sm.CategoryID = ca.ID
							AND sm.CategoryID > 0
						) 
				WHERE 
						sm.ParentID = "0"
					AND sm.NodeID = "0" 
					AND sm.SenderID = !ContactID!
					AND sm.Date >= !UserDateCreated! 
					AND sm.Type = "post" 
					AND sc.ThreadID = sm.ID 
					AND sc.Type = "comment" 
					AND sc.SenderID != !ContactID! 
					AND us.ID = sc.SenderID 
			)
		)
		z
		ORDER BY
			z.ID DESC 
		LIMIT ' . $limit . ' 
	';
	
	$qn = str_replace( '!GetWallID!', getWallID(), $qn );
	$qn = str_replace( '!ContactID!', $webuser->ContactID, $qn );
	$qn = str_replace( '!UserDateCreated!', ( '\'' . $webuser->DateCreated . '\'' ), $qn );
	$qn = str_replace( '!UcIDS!', $ucids, $qn );
	$qn = str_replace( '!GucIDS!', $gucids, $qn );
    
	$plugins = false;
	
	/*// Check plugins for wall sharedposts functionality
	if ( file_exists ( 'subether/plugins' ) )
	{
		if ( $dir = opendir ( 'subether/plugins' ) )
		{
			while ( $file = readdir ( $dir ) )
			{
				if ( $file{0} == '.' ) continue;
				if ( !file_exists ( 'subether/plugins/' . $file . '/notification' ) )
				{
					continue;
				}
				if ( !file_exists ( $f = 'subether/plugins/' . $file . '/notification/notices.php' ) )
				{
					continue;
				}
				include ( $f );
			}
			closedir ( $dir );
		}
	}*/
	
	$str = ''; $output = array();
	
	/*if( $plugins && is_array( $plugins ) )
	{
		foreach( $plugins as $plgs )
		{
			if( !is_array( $plgs ) ) continue;
			
			foreach( $plgs as $pl )
			{
				for( $a = 0; $a < 100; $a++ )
				{
					$sorting = $pl->Date;
					
					if( !isset( $output[strtotime($sorting).'_'.$a] ) )
					{
						$output[strtotime($sorting).'_'.$a] = $pl;
						break;
					}
				}
			}
		}
	}*/
	
	//die( ' .. ' . $qn );
	
	if( $posts = $database->fetchObjectRows( $qn, false, 'components/notification/include/functions.php' ) )
	{
		//die( print_r( $posts,1 ) . ' .. ' . $qn );
		
		foreach( $posts as $pos )
		{
			for( $a = 0; $a < 100; $a++ )
			{
				$sorting = $pos->Date;
				
				if( !isset( $output[strtotime($sorting).'_'.$a] ) )
				{
					$output[strtotime($sorting).'_'.$a] = $pos;
					break;
				}
			}
		}
	}
	
	krsort( $output );
	
    if( $output /*$sm = $database->fetchObjectRows( $q, false, 'components/notification/include/functions.php' )*/)
    {
        foreach( $output as $k=>$m )
        {
            $m->SeenBy = ( $m->SeenBy != '' && is_string( $m->SeenBy ) ? json_decode( $m->SeenBy ) : array() );
            if( in_array( $webuser->ContactID, $m->SeenBy ) )
            {
                unset( $sm[$k] );
            }
        }
        
        return $sm;
    }
    return false;
}


function GetNotifications( $limit = 50, $notseen = false )
{
	global $database, $webuser;
	
	$guci = getUserContactsID( $webuser->ContactID );
	$gucids = ( $guci ? implode( ',', $guci ) : $webuser->ContactID );
	$uci = getUserGroupsID( $webuser->ID );
	$ucids = ( $uci ? implode( ',', $uci ) : false );
	
	$qp = '
		SELECT * FROM
		(
			(
				SELECT 
					sm.ID,
					sm.SeenBy,
					sm.Date,
					sm.DateModified,
					' . ( !$notseen ? '
					sm.SenderID,
					sm.ReceiverID,
					sm.CategoryID,
					sm.Subject,
					sm.Leadin,
					' . /*'sm.Message,
					sm.Data,' .*/ '
					sm.Options,
					sm.ThreadID,
					sm.ParentID,
					sm.ReadBy,
					sm.Tags,
					sm.Type,
					sm.RateDownBy,
					sm.RateUpBy,
					sm.Access,
					sm.NodeID,
					sm.NodeMainID,
					sm.ID AS PostID, 
					us.Username AS Name, 
					us.ImageID AS Image,
					u2.Username AS User_Name, 
					ca.Name AS SBC_Name, 
					ca.ID AS SBC_ID,
					' : '' ) . '
					( sm.CategoryID != !GetWallID! ) AS `IsGroup`,
					"post" AS Mode 
				FROM
					SBookContact us, 
					SBookMessage sm 
						LEFT JOIN SBookContact u2 ON
						(
								sm.ReceiverID = u2.ID
							AND sm.ReceiverID > 0
						) 
						LEFT JOIN SBookCategory ca ON
						(
								sm.CategoryID = ca.ID
							AND sm.CategoryID > 0
						) 
				WHERE 
						sm.Type IN ( "post", "vote" ) 
					AND sm.ParentID = "0"
					AND
					(
						   sm.ThreadID = sm.ID
						OR sm.ThreadID = "0"
					) 
					AND sm.NodeID = "0" 
					AND sm.SenderID != !ContactID!
					AND sm.Date >= !UserDateCreated! 
					AND
					( ' .  ( $ucids ? '
						(
								sm.CategoryID IN ( !UcIDS! ) 
							AND us.ID = sm.SenderID
						) 
						OR  ' : '' ) . '
						(
								sm.CategoryID = !GetWallID! 
							AND us.ID = sm.SenderID 
							AND sm.ReceiverID IN ( !GucIDS! )
						)
					) 
					AND
					(
						(
								sm.Access = "2" 
							AND sm.SenderID = !ContactID!
						) 
						OR
						(
								sm.Access = "1" 
							AND sm.SenderID IN ( !GucIDS! )
						) 
						OR
						(
							sm.Access = "0"
						)
					) 
			)
			UNION
			(
				SELECT 
					sc.ID,
					sc.SeenBy,
					sc.Date,
					sc.DateModified,
					' . ( !$notseen ? '
					sc.SenderID,
					sc.ReceiverID,
					sc.CategoryID,
					sc.Subject,
					sc.Leadin,
					' . /*'sc.Message,
					sc.Data,' .*/ '
					sc.Options,
					sc.ThreadID,
					sc.ParentID,
					sc.ReadBy,
					sc.Tags,
					sc.Type,
					sc.RateDownBy,
					sc.RateUpBy,
					sc.Access,
					sc.NodeID,
					sc.NodeMainID,
					sm.ID AS PostID, 
					us.Username AS Name, 
					us.ImageID AS Image, 
					u2.Username AS User_Name, 
					ca.Name AS SBC_Name, 
					ca.ID AS SBC_ID,
					' : '' ) . '
					( sm.CategoryID != !GetWallID! ) AS `IsGroup`,
					"comment" AS Mode 
				FROM
					SBookContact us, 
					SBookMessage sc, 
					SBookMessage sm 
						LEFT JOIN SBookContact u2 ON
						(
								sm.ReceiverID = u2.ID
							AND sm.ReceiverID > 0
						) 
						LEFT JOIN SBookCategory ca ON
						(
								sm.CategoryID = ca.ID
							AND sm.CategoryID > 0
						) 
				WHERE 
						sm.Type IN ( "post", "vote" ) 
					AND sm.ParentID = "0"
					AND
					(
						   sm.ThreadID = sm.ID
						OR sm.ThreadID = "0"
					) 
					AND sm.NodeID = "0" 
					AND sm.SenderID = !ContactID!
					AND sm.Date >= !UserDateCreated! 
					AND sm.Type = "post" 
					AND sc.ParentID = sm.ID 
					AND sc.Type = "comment" 
					AND sc.SenderID != !ContactID! 
					AND us.ID = sc.SenderID 
			)
		) z 
		ORDER BY 
			z.ID DESC 
		LIMIT ' . $limit . ' 
	';
	
	$qp = str_replace( '!GetWallID!', getWallID(), $qp );
	$qp = str_replace( '!ContactID!', $webuser->ContactID, $qp );
	$qp = str_replace( '!UserDateCreated!', ( '\'' . $webuser->DateCreated . '\'' ), $qp );
	$qp = str_replace( '!UcIDS!', $ucids, $qp );
	$qp = str_replace( '!GucIDS!', $gucids, $qp );
	
	$plugins = false;
	
	// Check plugins for wall sharedposts functionality
	if ( file_exists ( 'subether/plugins' ) )
	{
		if ( $dir = opendir ( 'subether/plugins' ) )
		{
			while ( $file = readdir ( $dir ) )
			{
				if ( $file{0} == '.' ) continue;
				if ( !file_exists ( 'subether/plugins/' . $file . '/notification' ) )
				{
					continue;
				}
				if ( !file_exists ( $f = 'subether/plugins/' . $file . '/notification/notices.php' ) )
				{
					continue;
				}
				include ( $f );
			}
			closedir ( $dir );
		}
	}
	
	$output = array();
	
	if( $plugins && is_array( $plugins ) )
	{
		foreach( $plugins as $plgs )
		{
			if( !is_array( $plgs ) ) continue;
			
			foreach( $plgs as $pl )
			{
				for( $a = 0; $a < 100; $a++ )
				{
					$sorting = $pl->Date;
					
					if( !isset( $output[strtotime($sorting).'_'.$a] ) )
					{
						$output[strtotime($sorting).'_'.$a] = $pl;
						break;
					}
				}
			}
		}
	}
	
	if( $posts = $database->fetchObjectRows( $qp ) )
	{
		foreach( $posts as $pos )
		{
			for( $a = 0; $a < 100; $a++ )
			{
				$sorting = $pos->Date;
				
				if( !isset( $output[strtotime($sorting).'_'.$a] ) )
				{
					$output[strtotime($sorting).'_'.$a] = $pos;
					break;
				}
			}
		}
	}
	
	krsort( $output );
	
	if( $output )
	{
		if( $notseen )
		{
			foreach( $output as $k=>$m )
			{
				if( !isset( $m->SeenBy ) && $m->IsNoticed == 0 )
				{
					continue;
				}
				else if( !isset( $m->SeenBy ) )
				{
					unset( $output[$k] );
				}
				else
				{
					$m->SeenBy = json_obj_decode( $m->SeenBy, 'array' );
					
					if( is_array( $m->SeenBy ) && in_array( $webuser->ContactID, $m->SeenBy ) )
					{
						unset( $output[$k] );
					}
				}
			}
		}
		
		return $output;
	}
	
	return false;
}

function getMessageNotices( $cid = false )
{
	global $database, $webuser;
	
	// TODO: Make support for message notices pr user or default ur user
}

function NotRead( $component = false, $cid = false, $type = false )
{
    global $webuser, $database;
    
    if( $component == 'wall' && $cid > 0 && $type )
    {
        $q = '
            SELECT n.*
            FROM SBookNotification n, SBookRelation r 
            WHERE n.Type = "wall" AND n.IsRead = "1"
            AND r.ObjectID = n.ObjectID 
            AND r.ConnectedID = \'' . $cid . '\' 
            AND r.ConnectedType = \'' . $type . '\' 
            ORDER BY n.ID DESC 
        ';
    }
    else if( $component == 'messages' )
    {
        $q = '
            SELECT m.* 
            FROM SBookMail m 
            WHERE m.ReceiverID = \'' . $webuser->ContactID . '\' 
            AND m.IsRead = "0"
            GROUP BY m.SenderID 
            ORDER BY m.ID DESC 
        ';
    }
    else
    {
        $q = '
            SELECT n.* FROM SBookNotification n
            WHERE n.Type = "wall" AND n.IsRead = "1" 
            ORDER BY n.ID DESC 
        ';
    }
    
    if( $notify = $database->fetchObjectRows ( $q, false, 'components/notification/include/functions.php' ) )
    {
        $out = array();
        foreach( $notify as $n )
        {
            if( !strstr( $n->ReceiverID, $webuser->ContactID ) ) continue;
            $out[] = $n;
        }
        return $out;
    }
    return false;
}

function IsNoticed( $id, $type = false )
{
    global $webuser, $database;
    
	if( !$id ) return false;
	
	switch( $type )
	{
		case 'events':
			if( $not = $database->fetchObjectRows ( $q = '
				SELECT
					ID
				FROM
					SBookNotification
				WHERE
					' . ( is_array( $id ) ? 'ID IN ( ' . implode( ',', $id ) . ' ) ' : 'ID = \'' . $id . '\' ' ) . '
				ORDER BY
					ID DESC
			', false, 'components/notification/include/functions.php' ) )
			{
				foreach( $not as $n )
				{
					$m = new dbObject( 'SBookNotification' );
					if( $m->Load( $n->ID ) )
					{
						$m->IsNoticed = 1;
						$m->Save();
					}
				}
			}
			break;
		
		case 'contacts':
			if( $not = $database->fetchObjectRows ( $q = '
				SELECT
					ID
				FROM
					SBookContactRelation
				WHERE
					' . ( is_array( $id ) ? 'ID IN ( ' . implode( ',', $id ) . ' ) ' : 'ID = \'' . $id . '\' ' ) . '
				ORDER BY
					ID DESC
			', false, 'components/notification/include/functions.php' ) )
			{
				foreach( $not as $n )
				{
					$m = new dbObject( 'SBookContactRelation' );
					if( $m->Load( $n->ID ) )
					{
						$m->IsNoticed = 1;
						$m->Save();
					}
				}
			}
			break;
		
		case 'messages':
			if( $not = $database->fetchObjectRows ( $q = '
				SELECT
					ID
				FROM
					SBookMail
				WHERE
					' . ( is_array( $id ) ? 'ID IN ( ' . implode( ',', $id ) . ' ) ' : 'ID = \'' . $id . '\' ' ) . '
					AND IsNoticed = "0" 
				ORDER BY
					ID DESC
			', false, 'components/notification/include/functions.php' ) )
			{
				$uids = array();
				
				foreach( $not as $n )
				{
					$m = new dbObject( 'SBookMail' );
					if( $m->Load( $n->ID ) )
					{
						if( $m->Type == 'cm' )
						{
							$uids[] = $m->UniqueID;
						}
						
						$m->IsNoticed = 1;
						$m->DateModified = date( 'Y-m-d H:i:s' );
						$m->Save();
					}
				}
				
				// TODO: Check this first
				/*if( $uids )
				{
					// TODO: Check also encrypted messages connected on the same uniqueid for new method
					
					foreach ( $uids as $v )
					{
						if( $msg = $database->fetchObjectRows ( '
							SELECT 
								m.ID 
							FROM 
								SBookMail m
							WHERE 
									m.UniqueID = \'' . $v . '\'
								AND m.IsNoticed = "0" 
							ORDER BY 
								m.ID DESC 
						' ) )
						{
							foreach( $msg as $mg )
							{
								$m = new dbObject( 'SBookMail' );
								if ( $m->Load( $mg->ID ) )
								{
									$m->IsNoticed = 1;
									$m->DateModified = date( 'Y-m-d H:i:s' );
									$m->Save();
								}
							}
						}
					}
				}*/
			}
			break;
		
		default:
			if( $not = $database->fetchObjectRows ( $q = '
				SELECT
					ID
				FROM
					SBookMessage
				WHERE
					' . ( is_array( $id ) ? 'ID IN ( ' . implode( ',', $id ) . ' ) ' : 'ID = \'' . $id . '\' ' ) . ' 
				ORDER BY
					ID DESC
			', false, 'components/notification/include/functions.php' ) )
			{
				foreach( $not as $n )
				{
					$m = new dbObject( 'SBookMessage' );
					if( $m->Load( $n->ID ) )
					{
						$m->SeenBy = json_obj_decode( $m->SeenBy, 'array' );
						
						if( is_array( $m->SeenBy ) && !in_array( $webuser->ContactID, $m->SeenBy ) && $m->SenderID != $webuser->ContactID )
						{
							$m->SeenBy[] = $webuser->ContactID;
							$m->SeenBy = json_obj_encode( $m->SeenBy, 'array' );
							$m->Save();
						}
					}
				}
			}
		break;
	}
}

function IsRead( $id, $type = false )
{
    global $webuser, $database;
    
    if( !$id ) return;
	
    switch( $type )
	{
		case 'messages':
			if( $red = $database->fetchObjectRows ( '
				SELECT
					ID
				FROM
					SBookMail
				WHERE
					' . ( is_array( $id ) ? 'ID IN ( ' . implode( ',', $id ) . ' ) ' : 'ID = \'' . $id . '\' ' ) . '
					AND IsRead = "0" 
				ORDER BY
					ID DESC
			', false, 'components/notification/include/functions.php' ) )
			{
				$uids = array();
				
				foreach( $red as $r )
				{
					$m = new dbObject( 'SBookMail' );
					if( $m->Load( $r->ID ) )
					{
						if( $m->Type == 'cm' )
						{
							$uids[] = $m->UniqueID;
						}
						
						$m->IsRead = 1;
						$m->DateModified = date( 'Y-m-d H:i:s' );
						$m->Save();
					}
				}
				
				// TODO: Check this first
				/*if( $uids )
				{
					// TODO: Check also encrypted messages connected on the same uniqueid for new method
					
					foreach ( $uids as $v )
					{
						if( $msg = $database->fetchObjectRows ( '
							SELECT 
								m.ID 
							FROM 
								SBookMail m
							WHERE 
									m.UniqueID = \'' . $v . '\'
								AND m.IsRead = "0" 
							ORDER BY 
								m.ID DESC 
						' ) )
						{
							foreach( $msg as $mg )
							{
								$m = new dbObject( 'SBookMail' );
								if ( $m->Load( $mg->ID ) )
								{
									$m->IsRead = 1;
									$m->DateModified = date( 'Y-m-d H:i:s' );
									$m->Save();
								}
							}
						}
					}
				}*/
			}
			break;
		
		default:
			if( $red = $database->fetchObjectRows ( '
				SELECT
					ID
				FROM
					SBookMessage
				WHERE
					' . ( is_array( $id ) ? 'ID IN ( ' . implode( ',', $id ) . ' ) ' : 'ID = \'' . $id . '\' ' ) . '
				ORDER BY
					ID DESC
			', false, 'components/notification/include/functions.php' ) )
			{
				foreach( $red as $r )
				{
					$m = new dbObject( 'SBookMessage' );
					if( $m->Load( $r->ID ) )
					{
						$m->ReadBy = json_obj_decode( $m->ReadBy, 'array' );
						
						if( is_array( $m->ReadBy ) && !in_array( $webuser->ContactID, $m->ReadBy ) && $m->SenderID != $webuser->ContactID )
						{
							$m->ReadBy[] = $webuser->ContactID;
							$m->ReadBy = json_obj_encode( $m->ReadBy, 'array' );
							$m->Save();
						}
					}
				}
			}
		break;
	}
}

?>
