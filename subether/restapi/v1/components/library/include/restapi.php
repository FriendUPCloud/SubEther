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

global $database;

include_once ( 'subether/functions/globalfuncs.php' );
include_once ( 'subether/functions/componentfuncs.php' );
include_once ( 'subether/functions/userfuncs.php' );

// Set limit
$limit = 300;

$required = array(
	'SessionID' 
);

$options = array(
	'CategoryID', 'Images', 'Files', 'Folders', 'Limit', 'Encoding' 
);

// Temporary to view data in browser for development
if( !$_POST && $_REQUEST )
{
	$_POST = $_REQUEST;
}

unset( $_POST['route'] );

// --- Temporary until security option is verified for user --- // 


$listall = ( !isset( $_POST['Images'] ) && !isset( $_POST['Files'] ) && !isset( $_POST['Folders'] ) ? true : false );



$xml = array(); $json = new stdClass(); $fld = array();



// Get User data from sessionid
$sess = new dbObject ( 'UserLogin' );
$sess->Token = $_POST['SessionID'];
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
}



// --- Images -----------------------------------------------------------------------------------

$img = ( isset( $_POST['Images'] ) ? explode( ',', $_POST['Images'] ) : false );

if ( ( $img || $listall ) && $imgs = $database->fetchObjectRows ( '
	SELECT
		i.*, f.DiskPath 
	FROM
		Image i,
		Folder f 
	WHERE 
			i.NodeID = "0"
		AND f.ID = i.ImageFolder 
		' . ( $img ? '
		AND i.ID IN ( ' . implode( ',', $img ) . ' ) 
		' : '
		AND i.UserID = \'' . $u->UserID . '\' ' ) . ' 
	ORDER BY
		i.ID ASC 
	LIMIT ' . ( isset( $_POST['Limit'] ) && $_POST['Limit'] ? $_POST['Limit'] : $limit ) . '
' ) )
{
	$json->Images = [];
	
	foreach ( $imgs as $row )
	{
		$str  = '<Images>';
		$str .= '<ID>' . $row->ID . '</ID>';
		$str .= '<Title><![CDATA[' . $row->Title . ']]></Title>';
		$str .= '<Filename>' . $row->Filename . '</Filename>';
		$str .= '<Description><![CDATA[' . $row->Description . ']]></Description>';
		$str .= '<DiskPath>' . $row->DiskPath . '</DiskPath>';
		$str .= '<Tags>' . $row->Tags . '</Tags>';
		$str .= '<ColorSpace>' . $row->ColorSpace . '</ColorSpace>';
		$str .= '<ImageFolder>' . $row->ImageFolder . '</ImageFolder>';
		$str .= '<Filesize>' . $row->Filesize . '</Filesize>';
		$str .= '<Width>' . $row->Width . '</Width>';
		$str .= '<Height>' . $row->Height . '</Height>';
		$str .= '<Filetype>' . $row->Filetype . '</Filetype>';
		$str .= '</Images>';
		
		$obj = new stdClass();
		$obj->ID          = $row->ID;
		$obj->Title       = $row->Title;
		$obj->Filename    = $row->Filename;
		$obj->Description = $row->Description;
		$obj->DiskPath    = $row->DiskPath;
		$obj->Tags        = $row->Tags;
		$obj->ColorSpace  = $row->ColorSpace;
		$obj->ImageFolder = $row->ImageFolder;
		$obj->Filesize    = $row->Filesize;
		$obj->Width       = $row->Width;
		$obj->Height      = $row->Height;
		$obj->Filetype    = $row->Filetype;
		
		$json->Images[] = $obj;
		
		if( $row->ImageFolder && !in_array( $row->ImageFolder, $fld ) )
		{
			$fld[] = $row->ImageFolder;
		}
		
		$xml[] = $str;
	}
}

// --- Files -----------------------------------------------------------------------------------
	
$fid = ( isset( $_POST['Files'] ) ? explode( ',', $_POST['Files'] ) : false );
	
if ( ( $fid || $listall ) && $fils = $database->fetchObjectRows ( '
	SELECT
		i.*, f.DiskPath 
	FROM
		File i,
		Folder f 
	WHERE
			i.NodeID = "0"
		AND f.ID = i.FileFolder 
		' . ( $fid ? '
		AND i.ID IN ( ' . implode( ',', $fid ) . ' ) 
		' : '
		AND i.UserID = \'' . $u->UserID . '\' ' ) . ' 
	ORDER BY
		i.ID ASC 
	LIMIT ' . ( isset( $_POST['Limit'] ) && $_POST['Limit'] ? $_POST['Limit'] : $limit ) . '
' ) )
{
	$json->Files = [];	
	
	foreach ( $fils as $row )
	{
		$str  = '<Files>';
		$str .= '<ID>' . $row->ID . '</ID>';
		$str .= '<Title><![CDATA[' . $row->Title . ']]></Title>';
		$str .= '<Filename>' . $row->Filename . '</Filename>';
		$str .= '<Description><![CDATA[' . $row->Description . ']]></Description>';
		$str .= '<DiskPath>' . $row->DiskPath . '</DiskPath>';
		$str .= '<Tags>' . $row->Tags . '</Tags>';
		$str .= '<ColorSpace>' . $row->ColorSpace . '</ColorSpace>';
		$str .= '<FileFolder>' . $row->FileFolder . '</FileFolder>';
		$str .= '<Filesize>' . $row->Filesize . '</Filesize>';
		$str .= '<Width>' . $row->Width . '</Width>';
		$str .= '<Height>' . $row->Height . '</Height>';
		$str .= '<Filetype>' . $row->Filetype . '</Filetype>';
		$str .= '</Files>';
		
		$obj = new stdClass();
		$obj->ID          = $row->ID;
		$obj->Title       = $row->Title;
		$obj->Filename    = $row->Filename;
		$obj->Description = $row->Description;
		$obj->DiskPath    = $row->DiskPath;
		$obj->Tags        = $row->Tags;
		$obj->ColorSpace  = $row->ColorSpace;
		$obj->FileFolder  = $row->FileFolder;
		$obj->Filesize    = $row->Filesize;
		$obj->Width       = $row->Width;
		$obj->Height      = $row->Height;
		$obj->Filetype    = $row->Filetype;
		
		$json->Files[] = $obj;
		
		if( $row->FileFolder && !in_array( $row->FileFolder, $fld ) )
		{
			$fld[] = $row->FileFolder;
		}
		
		$xml[] = $str;
	}
}

// Folders -----------------------------------------------------------------------------------

$fld = ( isset( $_POST['Folders'] ) ? explode( ',', $_POST['Folders'] ) : $fld );

$json->Folders = [];

if( $fld )
{
	for ( $a = 0; $a < 10; $a++ )
	{
		if ( !count( $fld ) ) break;
	
		if ( $fld && $folders = $database->fetchObjectRows ( '
			SELECT
				f.*
			FROM
				Folder f
			WHERE
					f.ID IN ( ' . implode( ',', $fld ) . ' ) 
				AND f.NodeID = "0" 
			ORDER BY
				f.ID ASC 
			LIMIT ' . ( isset( $_POST['Limit'] ) && $_POST['Limit'] ? $_POST['Limit'] : $limit ) . '
		' ) )
		{
			$fld = array(); 
		
			foreach ( $folders as $row )
			{
				$str  = '<Folders>';
				$str .= '<ID>' . $row->ID . '</ID>';
				$str .= '<Name><![CDATA[' . $row->Name . ']]></Name>';
				$str .= '<Parent>' . $row->Parent . '</Parent>';
				$str .= '<Description><![CDATA[' . $row->Description . ']]></Description>';
				$str .= '<DiskPath>' . $row->DiskPath . '</DiskPath>';
				$str .= '<Notes><![CDATA[' . $row->Notes . ']]></Notes>';
				$str .= '<UserID>' . $row->UserID . '</UserID>';
				$str .= '<CategoryID>' . $row->CategoryID . '</CategoryID>';
				$str .= '</Folders>';
			
				$obj = new stdClass();
				$obj->ID          = $row->ID;
				$obj->Name        = $row->Name;
				$obj->Parent      = $row->Parent;
				$obj->Description = $row->Description;
				$obj->DiskPath    = $row->DiskPath;
				$obj->Notes       = $row->Notes;
				$obj->UserID      = $row->UserID;
				$obj->CategoryID  = $row->CategoryID;
			
				$json->Folders[] = $obj;
			
				if( $row->Parent && !in_array( $row->Parent, $fld ) )
				{
					$fld[] = $row->Parent;
				}
			
				$xml[] = $str;
			}
		}
	}
}
else if( $listall )
{
	if ( $folders = $database->fetchObjectRows ( '
		SELECT
			f.*
		FROM
			Folder f
		WHERE
				f.NodeID = "0" 
			' . ( $fld ? '
			AND f.ID IN ( ' . implode( ',', $fld ) . ' ) 
			' : '
			AND f.UserID = \'' . $u->UserID . '\' ' ) . ' 
		ORDER BY
			f.ID ASC 
		LIMIT ' . ( isset( $_POST['Limit'] ) && $_POST['Limit'] ? $_POST['Limit'] : $limit ) . '
	' ) )
	{
		$fld = array(); 
	
		foreach ( $folders as $row )
		{
			$str  = '<Folders>';
			$str .= '<ID>' . $row->ID . '</ID>';
			$str .= '<Name><![CDATA[' . $row->Name . ']]></Name>';
			$str .= '<Parent>' . $row->Parent . '</Parent>';
			$str .= '<Description><![CDATA[' . $row->Description . ']]></Description>';
			$str .= '<DiskPath>' . $row->DiskPath . '</DiskPath>';
			$str .= '<Notes><![CDATA[' . $row->Notes . ']]></Notes>';
			$str .= '<UserID>' . $row->UserID . '</UserID>';
			$str .= '<CategoryID>' . $row->CategoryID . '</CategoryID>';
			$str .= '</Folders>';
		
			$obj = new stdClass();
			$obj->ID          = $row->ID;
			$obj->Name        = $row->Name;
			$obj->Parent      = $row->Parent;
			$obj->Description = $row->Description;
			$obj->DiskPath    = $row->DiskPath;
			$obj->Notes       = $row->Notes;
			$obj->UserID      = $row->UserID;
			$obj->CategoryID  = $row->CategoryID;
		
			$json->Folders[] = $obj;
		
			if( $row->Parent && !in_array( $row->Parent, $fld ) )
			{
				$fld[] = $row->Parent;
			}
		
			$xml[] = $str;
		}
	}
}

// --- Output ---------------------------------------------------------------------------

if ( count( $xml ) )
{
	$xml = array_reverse( $xml );
	
	$xml[] = '<Listed>' . count( $xml ) . '</Listed>';
	
	$json->Listed = count( $xml );
	
	outputXML ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' ? $json : implode( $xml ) );
}

throwXmlError ( EMPTY_LIST );





// --- Bypassed because it's not working yet .. --- //

if ( isset( $_POST ) )
{
	foreach( $_POST as $k=>$p )
	{
		if( !in_array( $k, $required ) && !in_array( $k, $options ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	foreach( $required as $r )
	{
		if( !isset( $_POST[$r] ) )
		{
			throwXmlError ( MISSING_PARAMETERS );
		}
	}
	
	// Get User data from sessionid
	$sess = new dbObject ( 'UserLogin' );
	$sess->Token = $_POST['SessionID'];
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
	}
	
	
	$xml = array(); $json = new stdClass(); $fld = array();
	
	
	
	$usrs = ( $u && isset( $u->ID ) ? getUserContactsID( $u->ID, true ) : false );
	$usrs = ( $usrs && is_array( $usrs ) ? implode( ',', $usrs ) : ( $u ? $u->UserID : false ) );
	
	$acat = ( $u && isset( $u->ID ) ? CategoryAccess( $u->ID, false, -1, 'IsAdmin' ) : false );
	$acat = ( $acat && isset( $acat['CategoryID'] ) ? $acat['CategoryID'] : false );
	
	$ucat = ( $u && isset( $u->ID ) ? CategoryAccess( $u->ID, false, -1 ) : false );
	$ucat = ( $ucat && isset( $ucat['CategoryID'] ) ? $ucat['CategoryID'] : false );
	
	// --- Home -------------------------------------------------------------------------------------
	
	// Set root folder
	$root = new dbFolder();
	$root = $root->getRootFolder();
	
	// Fetch home folder
	
	if( $hflds = $database->fetchObjectRows ( '
		SELECT 
			mf.ID, 
			mf.Name, 
			mf.Parent, 
			mf.DiskPath, 
			mf.SortOrder, 
			mf.Access,
			mf.DateCreated, 
			mf.DateModified, 
			mr.Title AS Name, 
			mr.SortOrder 
		FROM 
			Folder rf, 
			Folder hf, 
			Folder mf, 
			SBookMediaRelation mr 
		WHERE 
				rf.Parent = \'' . $root->ID . '\' 
			AND hf.Parent = rf.ID 
			AND mf.Parent = hf.ID 
			AND mr.MediaID = mf.ID 
			AND mr.MediaType = "Folder" 
			' . ( isset( $_POST['CategoryID'] ) ? '
			AND mr.CategoryID = \'' . $_POST['CategoryID'] . '\'
			AND mr.UserID = "0"
			' : '
			AND mr.UserID = \'' . $u->UserID . '\' 
			AND mr.CategoryID = "0"
			' ) . ' 
		ORDER BY 
			mr.SortOrder ASC, 
			mf.ID ASC 
	' ) )
	{
		$mfids = array();
		
		foreach( $hflds as $mf )
		{
			$mfids[] = $mf->ID;
			
			$fld[] = $mf->ID;
		}
		
		// Fetch sub folders
		$sq = '
			SELECT
				f.ID,
				f.UniqueID,
				f.Name, 
				f.Parent, 
				f.DiskPath, 
				f.SortOrder,
				f.UserID,
				f.CategoryID,
				f.Access, 
				f.DateCreated, 
				f.DateModified, 
				r.Title AS Name,
				r.SortOrder,
				c.Name AS CategoryName,
				c.Privacy AS CategoryPrivacy,
				c.IsSystem AS CategorySystem 
			FROM 
				SBookMediaRelation r, 
				Folder f 
					LEFT JOIN SBookCategory c ON 
					(
						f.CategoryID = c.ID 
					) 
			WHERE 
					f.Parent IN ( !SubFolderIDS! ) 
				AND r.MediaID = f.ID
				AND 
				(
					
					/* --- Public / Members Access --- */
					
					(
						(
								f.Access = "0"
							AND f.UserID > 0 
							AND f.CategoryID > 0 
						)
						AND
						(
							
							/* --- Profile / Other Access --- */
							
							(
								c.IsSystem = "1" 
							)
							
							/* --- Group Access --- */
							
							OR 
							(
								c.Privacy = "OpenGroup" 
							)
							OR 
							(
									c.Privacy = "ClosedGroup" 
								AND f.Name = "Cover Photos" 
							)
							OR
							(
								f.CategoryID IN ( !UserCats! ) 
							)
						)
					)
					
					/* --- Contact Access --- */
					
					OR
					(
							f.Access = "1"
						AND f.UserID > 0 
						AND f.CategoryID > 0 
						AND f.UserID IN ( !UserIDS! ) 
					)
					
					/* --- File Owner Access --- */
					
					OR
					(
							f.UserID = !UserID!
						AND f.UserID > 0 
						AND f.CategoryID > 0 
					)
					
					/* --- Admin Access --- */
					
					OR
					(
							f.Access != "2"
						AND f.UserID > 0 
						AND f.CategoryID > 0 
						AND f.CategoryID IN ( !AdminCats! ) 
					)
					
					/* --- No Owner / All Access --- */
					
					OR
					(
						(
							f.UserID = "0" 
						)
						OR
						(
							f.CategoryID = "0" 
						)
					)
					
				) 
			ORDER BY 
				r.SortOrder ASC,
				f.ID ASC 
		';
		
		// TODO: Add support for admins of groups and members of groups and super admin of the system
		
		$sq = str_replace( '!UserID!', ( isset( $u ) && $u->UserID ? ( '\'' . $u->UserID . '\'' ) : 'NULL' ), $sq );
		$sq = str_replace( '!UserIDS!', ( isset( $usrs ) && $usrs ? $usrs : 'NULL' ), $sq );
		$sq = str_replace( '!AdminCats!', ( isset( $acat ) && $acat ? $acat : 'NULL' ), $sq );
		$sq = str_replace( '!UserCats!', ( isset( $ucat ) && $ucat ? $ucat : 'NULL' ), $sq );
		
		if( $mfids && $flds = $database->fetchObjectRows ( str_replace( '!SubFolderIDS!', implode( ',', $mfids ), $sq ) ) )
		{
			foreach( $flds as $sf )
			{
				$fld[] = $sf->ID;
			}
		}
	}
	
	$fld = ( isset( $_POST['Folders'] ) && $_POST['Folders'] != '*' ? explode( ',', $_POST['Folders'] ) : $fld );
	
	// --- Images -----------------------------------------------------------------------------------
	
	if( ( !isset( $_POST['Files'] ) && !isset( $_POST['Folders'] ) ) || isset( $_POST['Images'] ) )
	{
		$img = ( isset( $_POST['Images'] ) && $_POST['Images'] != '*' ? explode( ',', $_POST['Images'] ) : false );
		
		$iq = '
			SELECT * FROM
			( 
				(
					SELECT 
						i.ID,
						i.UniqueID,
						i.Title,
						i.Filename,
						i.Description,
						i.Tags, 
						i.DateCreated,
						i.DateModified,
						i.SortOrder,
						i.Filetype, 
						i.UserID,
						i.CategoryID,
						i.Filesize,
						i.Width,
						i.Height,
						i.ImageFolder AS FolderID,
						i.Access AS FileAccess,
						i.ModID,
						i.IsEdit,
						i.Verified,
						f.Name AS FolderName,
						f.DiskPath AS FolderPath,
						f.Access AS FolderAccess,
						f.UserID AS FolderUserID,
						f.CategoryID AS FolderCategoryID,
						c.Name AS CategoryName,
						c.Privacy AS CategoryPrivacy,
						c.IsSystem AS CategorySystem, 
						"image" AS MediaType 
					FROM 
						`Folder` f, 
						`Image` i 
							LEFT JOIN `SBookCategory` c ON
							(
								i.CategoryID = c.ID
							)
					WHERE 
							i.NodeID = "0" 
						AND i.ImageFolder = f.ID
						AND f.ID IN ( !FolderIDS! )
						' . ( $img ? '
						AND ( i.ID IN (' . implode( ',', $img ) . ') OR i.UniqueID IN (' . implode( ',', $img ) . ') ) 
						' : '' ) . '
				)
			)
			z
			WHERE 
				(
					
					/* --- Public / Members Access --- */
					
					(
						(
								z.FileAccess = "0"
							AND z.UserID > 0 
							AND z.CategoryID > 0 
							AND z.FolderAccess <= z.FileAccess 
						)
						AND
						(
							
							/* --- Profile / Other Access --- */
							
							(
								z.CategorySystem = "1" 
							)
							
							/* --- Group Access --- */
							
							OR 
							(
								z.CategoryPrivacy = "OpenGroup" 
							)
							OR 
							(
									z.CategoryPrivacy = "ClosedGroup" 
								AND z.FolderName = "Cover Photos" 
							)
							OR
							(
								z.CategoryID IN ( !UserCats! ) 
							)
						)
					)
					
					/* --- Contact Access --- */
					
					OR
					(
							z.FileAccess = "1"
						AND z.UserID > 0 
						AND z.CategoryID > 0 
						AND z.UserID IN ( !UserIDS! )
						AND z.FolderAccess <= z.FileAccess 
					)
					
					/* --- File Owner Access --- */
					
					OR
					(
							z.UserID = !UserID!
						AND z.UserID > 0 
						AND z.CategoryID > 0 
					)
					
					/* --- Admin Access --- */
					
					OR
					(
							z.FileAccess != "2"
						AND z.UserID > 0 
						AND z.CategoryID > 0 
						AND z.CategoryID IN ( !AdminCats! ) 
					)
					
					/* --- No Owner / All Access --- */
					
					OR
					(
						(
							z.UserID = "0" 
						)
						OR
						(
							z.CategoryID = "0" 
						)
					)
					
				)
			ORDER BY
				z.SortOrder ASC,
				z.DateCreated ASC
		';
		
		$iq = str_replace( '!UserID!', ( isset( $u ) && $u ? ( '\'' . $u->UserID . '\'' ) : 'NULL' ), $iq );
		$iq = str_replace( '!UserIDS!', ( isset( $usrs ) && $usrs ? $usrs : 'NULL' ), $iq );
		$iq = str_replace( '!AdminCats!', ( isset( $acat ) && $acat ? $acat : 'NULL' ), $iq );
		$iq = str_replace( '!UserCats!', ( isset( $ucat ) && $ucat ? $ucat : 'NULL' ), $iq );
		
		if ( $imgs = $database->fetchObjectRows ( str_replace( '!FolderIDS!', implode( ',', $fld ), $iq ) ) )
		{
			$json->Images = [];
			
			foreach ( $imgs as $row )
			{
				$str  = '<Images>';
				$str .= '<ID>' . $row->ID . '</ID>';
				$str .= '<UniqueID>' . $row->UniqueID . '</UniqueID>';
				$str .= '<Title><![CDATA[' . $row->Title . ']]></Title>';
				$str .= '<Filename>' . $row->Filename . '</Filename>';
				$str .= '<Description><![CDATA[' . $row->Description . ']]></Description>';
				$str .= '<DiskPath>' . ( ( defined( 'BASE_URL' ) ? BASE_URL : '' ) . 'secure-files/images/' . ( $row->UniqueID ? $row->UniqueID : $row->ID ) . ( isset( $_REQUEST['SessionID'] ) ? ( '/' . $_REQUEST['SessionID'] ) : '' ) . '/' ) . '</DiskPath>';
				$str .= '<Tags>' . $row->Tags . '</Tags>';
				$str .= '<FolderID>' . $row->FolderID . '</FolderID>';
				$str .= '<Filesize>' . $row->Filesize . '</Filesize>';
				$str .= '<Width>' . $row->Width . '</Width>';
				$str .= '<Height>' . $row->Height . '</Height>';
				$str .= '<Filetype>' . $row->Filetype . '</Filetype>';
				$str .= '<FileAccess>' . $row->FileAccess . '</FileAccess>';
				$str .= '</Images>';
				
				$obj = new stdClass();
				$obj->ID = $row->ID;
				$obj->UniqueID = $row->UniqueID;
				$obj->Title = $row->Title;
				$obj->Filename = $row->Filename;
				$obj->Description = $row->Description;
				$obj->DiskPath = ( ( defined( 'BASE_URL' ) ? BASE_URL : '' ) . 'secure-files/images/' . ( $row->UniqueID ? $row->UniqueID : $row->ID ) . ( isset( $_REQUEST['SessionID'] ) ? ( '/' . $_REQUEST['SessionID'] ) : '' ) . '/' );
				$obj->Tags = $row->Tags;
				$obj->FolderID = $row->FolderID;
				$obj->Filesize = $row->Filesize;
				$obj->Width = $row->Width;
				$obj->Height = $row->Height;
				$obj->Filetype = $row->Filetype;
				$obj->FileAccess = $row->FileAccess;
				
				$json->Images[] = $obj;
				
				if( $row->ImageFolder && !in_array( $row->ImageFolder, $fld ) )
				{
					$fld[] = $row->ImageFolder;
				}
				
				$xml[] = $str;
			}
		}
	}
	
	// --- Files -----------------------------------------------------------------------------------
	
	if( ( !isset( $_POST['Images'] ) && !isset( $_POST['Folders'] ) ) || isset( $_POST['Files'] ) )
	{
		$fid = ( isset( $_POST['Files'] ) && $_POST['Files'] != '*' ? explode( ',', $_POST['Files'] ) : false );
		
		$fq = '
			SELECT * FROM
			(
				(
					SELECT 
						i.ID,
						i.UniqueID,
						i.Title,
						i.Filename,
						i.Description,
						i.Tags, 
						i.DateCreated,
						i.DateModified,
						i.SortOrder,
						i.Filetype, 
						i.UserID,
						i.CategoryID,
						i.Filesize,
						0 AS Width,
						0 AS Height,
						i.FileFolder AS FolderID,
						i.Access AS FileAccess,
						i.ModID,
						i.IsEdit,
						i.Verified,
						f.Name AS FolderName,
						f.DiskPath AS FolderPath,
						f.Access AS FolderAccess,
						f.UserID AS FolderUserID,
						f.CategoryID AS FolderCategoryID,
						c.Name AS CategoryName,
						c.Privacy AS CategoryPrivacy,
						c.IsSystem AS CategorySystem, 
						"file" AS MediaType 
					FROM 
						`Folder` f, 
						`File` i 
							LEFT JOIN `SBookCategory` c ON
							(
								i.CategoryID = c.ID
							)
					WHERE 
							i.NodeID = "0" 
						AND i.FileFolder = f.ID 
						AND f.ID IN ( !FolderIDS! ) 
						' . ( $fid ? '
						AND ( i.ID IN (' . implode( ',', $fid ) . ') OR i.UniqueID IN (' . implode( ',', $fid ) . ') ) 
						' : '' ) . '
				) 
			)
			z
			WHERE 
				(
					
					/* --- Public / Members Access --- */
					
					(
						(
								z.FileAccess = "0"
							AND z.UserID > 0 
							AND z.CategoryID > 0 
							AND z.FolderAccess <= z.FileAccess 
						)
						AND
						(
							
							/* --- Profile / Other Access --- */
							
							(
								z.CategorySystem = "1" 
							)
							
							/* --- Group Access --- */
							
							OR 
							(
								z.CategoryPrivacy = "OpenGroup" 
							)
							OR 
							(
									z.CategoryPrivacy = "ClosedGroup" 
								AND z.FolderName = "Cover Photos" 
							)
							OR
							(
								z.CategoryID IN ( !UserCats! ) 
							)
						)
					)
					
					/* --- Contact Access --- */
					
					OR
					(
							z.FileAccess = "1"
						AND z.UserID > 0 
						AND z.CategoryID > 0 
						AND z.UserID IN ( !UserIDS! )
						AND z.FolderAccess <= z.FileAccess 
					)
					
					/* --- File Owner Access --- */
					
					OR
					(
							z.UserID = !UserID!
						AND z.UserID > 0 
						AND z.CategoryID > 0 
					)
					
					/* --- Admin Access --- */
					
					OR
					(
							z.FileAccess != "2"
						AND z.UserID > 0 
						AND z.CategoryID > 0 
						AND z.CategoryID IN ( !AdminCats! ) 
					)
					
					/* --- No Owner / All Access --- */
					
					OR
					(
						(
							z.UserID = "0" 
						)
						OR
						(
							z.CategoryID = "0" 
						)
					)
					
				)
			ORDER BY
				z.SortOrder ASC,
				z.DateCreated ASC
		';
		
		$fq = str_replace( '!UserID!', ( isset( $u ) && $u ? ( '\'' . $u->UserID . '\'' ) : 'NULL' ), $fq );
		$fq = str_replace( '!UserIDS!', ( isset( $usrs ) && $usrs ? $usrs : 'NULL' ), $fq );
		$fq = str_replace( '!AdminCats!', ( isset( $acat ) && $acat ? $acat : 'NULL' ), $fq );
		$fq = str_replace( '!UserCats!', ( isset( $ucat ) && $ucat ? $ucat : 'NULL' ), $fq );
		
		if ( $fils = $database->fetchObjectRows ( str_replace( '!FolderIDS!', implode( ',', $fld ), $fq ) ) )
		{
			$json->Files = [];
			
			foreach ( $fils as $row )
			{
				$str  = '<Files>';
				$str .= '<ID>' . $row->ID . '</ID>';
				$str .= '<UniqueID>' . $row->UniqueID . '</UniqueID>';
				$str .= '<Title><![CDATA[' . $row->Title . ']]></Title>';
				$str .= '<Filename>' . $row->Filename . '</Filename>';
				$str .= '<Description><![CDATA[' . $row->Description . ']]></Description>';
				$str .= '<DiskPath>' . ( ( defined( 'BASE_URL' ) ? BASE_URL : '' ) . 'secure-files/files/' . ( $row->UniqueID ? $row->UniqueID : $row->ID ) . ( isset( $_REQUEST['SessionID'] ) ? ( '/' . $_REQUEST['SessionID'] ) : '' ) . '/' ) . '</DiskPath>';
				$str .= '<Tags>' . $row->Tags . '</Tags>';
				$str .= '<FolderID>' . $row->FolderID . '</FolderID>';
				$str .= '<Filesize>' . $row->Filesize . '</Filesize>';
				$str .= '<Width>' . $row->Width . '</Width>';
				$str .= '<Height>' . $row->Height . '</Height>';
				$str .= '<Filetype>' . $row->Filetype . '</Filetype>';
				$str .= '<FileAccess>' . $row->FileAccess . '</FileAccess>';
				$str .= '</Files>';
				
				$obj = new stdClass();
				$obj->ID = $row->ID;
				$obj->UniqueID = $row->UniqueID;
				$obj->Title = $row->Title;
				$obj->Filename = $row->Filename;
				$obj->Description = $row->Description;
				$obj->DiskPath = ( ( defined( 'BASE_URL' ) ? BASE_URL : '' ) . 'secure-files/files/' . ( $row->UniqueID ? $row->UniqueID : $row->ID ) . ( isset( $_REQUEST['SessionID'] ) ? ( '/' . $_REQUEST['SessionID'] ) : '' ) . '/' );
				$obj->Tags = $row->Tags;
				$obj->FolderID = $row->FolderID;
				$obj->Filesize = $row->Filesize;
				$obj->Width = $row->Width;
				$obj->Height = $row->Height;
				$obj->Filetype = $row->Filetype;
				$obj->FileAccess = $row->FileAccess;
				
				$json->Files[] = $obj;
				
				if( $row->FileFolder && !in_array( $row->FileFolder, $fld ) )
				{
					$fld[] = $row->FileFolder;
				}
				
				$xml[] = $str;
			}
		}
	}
	
	// Folders -----------------------------------------------------------------------------------
	
	if( ( !isset( $_POST['Images'] ) && !isset( $_POST['Files'] ) ) || isset( $_POST['Folders'] ) )
	{
		$fld = ( isset( $_POST['Folders'] ) && $_POST['Folders'] != '*' ? explode( ',', $_POST['Folders'] ) : $fld );
		
		// TODO: Check for folder access
		
		//for ( $a = 0; $a < 10; $a++ )
		//{
			//if ( !count( $fld ) ) break;
			
			if ( $fld && $folders = $database->fetchObjectRows ( '
				SELECT
					f.*
				FROM
					Folder f
				WHERE
						( f.ID IN ( ' . implode( ',', $fld ) . ' ) OR f.UniqueID IN ( ' . implode( ',', $fld ) . ' ) ) 
					AND f.NodeID = "0" 
				ORDER BY
					f.ID ASC
			' ) )
			{
				//$fld = array();
				
				$mainfld = []; $subfld = [];
				
				$json->Folders = [];
				
				foreach ( $folders as $row )
				{
					if( $row->Parent > 0 && !in_array( $row->ID, $mfids ) )
					{
						if( !isset( $subfld[$row->Parent] ) )
						{
							$subfld[$row->Parent] = [];
						}
						
						$subfld[$row->Parent][] = $row;
					}
					else
					{
						$mainfld[$row->ID] = $row;
					}
				}
				
				if( $mainfld )
				{
					foreach ( $mainfld as $row )
					{
						$str  = '<Folders>';
						$str .= '<ID>' . $row->ID . '</ID>';
						$str .= '<UniqueID>' . $row->UniqueID . '</UniqueID>';
						$str .= '<Name><![CDATA[' . $row->Name . ']]></Name>';
						$str .= '<Parent>' . $row->Parent . '</Parent>';
						$str .= '<Description><![CDATA[' . $row->Description . ']]></Description>';
						//$str .= '<DiskPath>' . $row->DiskPath . '</DiskPath>';
						$str .= '<Notes><![CDATA[' . $row->Notes . ']]></Notes>';
						$str .= '<UserID>' . $row->UserID . '</UserID>';
						$str .= '<CategoryID>' . $row->CategoryID . '</CategoryID>';
						$str .= '<Access>' . $row->Access . '</Access>';
						
						if( isset( $subfld[$row->ID] ) )
						{
							foreach( $subfld[$row->ID] as $sub )
							{
								$str .= '<Folders>';
								$str .= '<ID>' . $sub->ID . '</ID>';
								$str .= '<UniqueID>' . $sub->UniqueID . '</UniqueID>';
								$str .= '<Name><![CDATA[' . $sub->Name . ']]></Name>';
								$str .= '<Parent>' . $sub->Parent . '</Parent>';
								$str .= '<Description><![CDATA[' . $sub->Description . ']]></Description>';
								//$str .= '<DiskPath>' . $sub->DiskPath . '</DiskPath>';
								$str .= '<Notes><![CDATA[' . $sub->Notes . ']]></Notes>';
								$str .= '<UserID>' . $sub->UserID . '</UserID>';
								$str .= '<CategoryID>' . $sub->CategoryID . '</CategoryID>';
								$str .= '<Access>' . $sub->Access . '</Access>';
								$str .= '</Folders>';
							}
						}
						
						$str .= '</Folders>';
						
						$obj = new stdClass();
						$obj->ID = $row->ID;
						$obj->UniqueID = $row->UniqueID;
						$obj->Name = $row->Name;
						$obj->Parent = $row->Parent;
						$obj->Description = $row->Description;
						$obj->Notes = $row->Notes;
						$obj->UserID = $row->UserID;
						$obj->CategoryID = $row->CategoryID;
						$obj->Access = $row->Access;
						
						if( isset( $subfld[$row->ID] ) )
						{
							$obj->Folders = $subfld[$row->ID];
						}
						
						$json->Folders[] = $obj;
						
						//if( $row->Parent && !in_array( $row->Parent, $fld ) )
						//{
						//	$fld[] = $row->Parent;
						//}
						
						$xml[] = $str;
					}
				}
			}
		//}
	}
	
	// --- Output ---------------------------------------------------------------------------
	
	if ( count( $xml ) )
	{
		$xml = array_reverse( $xml );
		
		$xml[] = '<Listed>' . count( $xml ) . '</Listed>';
		
		$json->Listed = count( $xml );
		
		$out = ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' ? $json : implode( $xml ) );
		
		outputXML ( $out );
	}
	
	throwXmlError ( EMPTY_LIST );

}

throwXmlError ( MISSING_PARAMETERS );

?>
