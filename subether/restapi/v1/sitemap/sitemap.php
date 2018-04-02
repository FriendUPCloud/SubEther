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

header ( 'Content-type: text/xml; charset=utf-8' );

include_once ( 'subether/functions/componentfuncs.php' );
include_once ( 'subether/functions/userfuncs.php' );

// TODO: Add Open: [ Pages, Profiles, Groups, Wall?, Images?, Videos? ]

// 1. Profiles (Contacts)
// 2. Profile Sublinks (Wall, About, Library)
// 3. Wall & Library sublinks?
// 4. Groups
// 5. Group Sublinks (Wall, Members, SubGroups, Library, Calendar )
// 6. Wall & Library & Calendar sublinks?
// 7. Images
// 8. Files
// 9. Videos

// TODO: Add meta data to these links if possible

// TODO: Add robot.txt to repo with link to this file when adding api

$out = []; $xml = []; $urls = 0; $ulimit = 45000;

$out[] = '<?xml version="1.0" encoding="utf-8"?>';
$out[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';

// --- Example ----------------------------------------------------------------------------------------------------------------------------------

//$url   = [];
//$url[] = '	<url>'; 
//$url[] = '		<loc>http://www.example.com/foo.html</loc>';
//$url[] = '		<lastmod>2017-03-01</lastmod>';
//$url[] = '		<changefreq>daily</changefreq>';
//$url[] = '		<priority>1.0</priority>';
//$url[] = '		<image:image>';
//$url[] = '			<image:loc>http://example.com/image.jpg</image:loc>';
//$url[] = '			<image:caption>Dogs playing poker</image:caption>';
//$url[] = '		</image:image>';
//$url[] = '		<video:video>';
//$url[] = '			<video:content_loc>http://www.example.com/video123.flv</video:content_loc>';
//$url[] = '			<video:player_loc allow_embed="yes" autoplay="ap=1">http://www.example.com/videoplayer.swf?video=123</video:player_loc>';
//$url[] = '			<video:thumbnail_loc>http://www.example.com/thumbs/123.jpg</video:thumbnail_loc>';
//$url[] = '			<video:title>Grilling steaks for summer</video:title>';  
//$url[] = '			<video:description>Cook the perfect steak every time.</video:description>';
//$url[] = '		</video:video>';
//$url[] = '	</url>';
//$xml[] = implode( "\n", $url );

// --- Open Pages --------------------------------------------------------------------------------------------------------------------------------

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'information/' . ( getNodeVerification() ? getNodeVerification() . '.xml' : '' ) . '</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

if( ComponentAccess( 'browse', 'main' ) )
{
	$url   = [];
	$url[] = '	<url>'; 
	$url[] = '		<loc>' . BASE_URL . 'browse/</loc>';
	$url[] = '	</url>';
	$xml[] = implode( "\n", $url );
	
	//$url   = [];
	//$url[] = '	<url>'; 
	//$url[] = '		<loc>' . BASE_URL . 'browse/?r=network</loc>';
	//$url[] = '	</url>';
	//$xml[] = implode( "\n", $url );
	
	//$url   = [];
	//$url[] = '	<url>'; 
	//$url[] = '		<loc>' . BASE_URL . 'browse/?r=videos</loc>';
	//$url[] = '	</url>';
	//$xml[] = implode( "\n", $url );
	
	//$url   = [];
	//$url[] = '	<url>'; 
	//$url[] = '		<loc>' . BASE_URL . 'browse/?r=files</loc>';
	//$url[] = '	</url>';
	//$xml[] = implode( "\n", $url );
	
	//$url   = [];
	//$url[] = '	<url>'; 
	//$url[] = '		<loc>' . BASE_URL . 'browse/?r=images</loc>';
	//$url[] = '	</url>';
	//$xml[] = implode( "\n", $url );
	
	//$url   = [];
	//$url[] = '	<url>'; 
	//$url[] = '		<loc>' . BASE_URL . 'browse/?r=contacts</loc>';
	//$url[] = '	</url>';
	//$xml[] = implode( "\n", $url );
	
	//$url   = [];
	//$url[] = '	<url>'; 
	//$url[] = '		<loc>' . BASE_URL . 'browse/?r=groups</loc>';
	//$url[] = '	</url>';
	//$xml[] = implode( "\n", $url );
}

if( ComponentAccess( 'register', 'register' ) && $database->fetchObjectRow( 'SELECT * FROM SNodes WHERE IsMain = "1" AND Open = "1"' ) )
{
	$url   = [];
	$url[] = '	<url>'; 
	$url[] = '		<loc>' . BASE_URL . 'register/</loc>';
	$url[] = '	</url>';
	$xml[] = implode( "\n", $url );
}

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'ecovillage/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'university/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'services/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'travel/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

if( ComponentAccess( 'store', 'trading' ) )
{
	$url   = [];
	$url[] = '	<url>'; 
	$url[] = '		<loc>' . BASE_URL . 'trading/</loc>';
	$url[] = '	</url>';
	$xml[] = implode( "\n", $url );
}

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'nodes/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'about/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'terms/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'copyright/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'advertising/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'privacy/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'policy_feedback/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'creators_partners/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

$url   = [];
$url[] = '	<url>'; 
$url[] = '		<loc>' . BASE_URL . 'developers/</loc>';
$url[] = '	</url>';
$xml[] = implode( "\n", $url );

$urls = ( $urls + 23 );

// --- Contacts ----------------------------------------------------------------------------------------------------------------------------------

if( ComponentAccess( 'profile', 'profile' ) )
{
	if( $contacts = $database->fetchObjectRows ( '
		SELECT 
			c.*,
			i.UniqueID, 
			i.Filename 
		FROM 
			Users u, 
			SBookContact c
				LEFT JOIN Image i ON
				(
					c.ImageID = i.ID
				)
		WHERE 
				u.ID = c.UserID 
			AND u.IsDeleted = "0" 
			AND c.Username != "" 
			AND c.NodeID = "0" 
		ORDER BY 
			c.DateModified DESC, 
			c.ID DESC, 
			c.Firstname ASC, 
			c.Username ASC 
	' ) )
	{
		$slinks = [];
		
		if( $subs = $database->fetchObjectRows( '
			SELECT 
				c2.* 
			FROM 
				SBookCategory c1, 
				SBookCategory c2 
			WHERE 
					c1.CategoryID = "0" 
				AND c1.Type = "Group" 
				AND c1.Name = "Profile" 
				AND c1.IsSystem = "1" 
				AND c1.NodeID = "0" 
				AND c2.CategoryID = c1.ID 
				AND c2.Type = "SubGroup" 
				AND c2.IsSystem = "1" 
				AND c2.NodeID = "0" 
			ORDER BY 
				c2.ID ASC 
		' ) )
		{
			foreach( $subs as $sub )
			{
				if( ComponentAccess( strtolower( $sub->Name ), 'profile' ) )
				{
					$slinks[] = strtolower( $sub->Name );
				}
			}
			
			unset( $subs );
		}
		
		foreach( $contacts as $con )
		{
			$first  = ( $con->Firstname  ? $con->Firstname  . ' ' : '' );
			$middle = ( $con->Middlename ? $con->Middlename . ' ' : '' );
			$last   = ( $con->Lastname   ? $con->Lastname   . ' ' : '' );
			
			$con->DisplayName = '';
			
			$con->DisplayName = ( $con->Display == 1 ? trim( $first . $middle . $last ) : $con->DisplayName );
			$con->DisplayName = ( $con->Display == 2 ? trim( $first . $last           ) : $con->DisplayName );
			$con->DisplayName = ( $con->Display == 3 ? trim( $last  . $first          ) : $con->DisplayName );
			
			$con->DisplayName = ( $con->DisplayName ? $con->DisplayName : $con->Username );
			
			$url   = [];
			$url[] = '	<url>'; 
			$url[] = '		<loc>' . BASE_URL . urlencode( $con->Username ) . '</loc>';
			
			if( $con->ImageID > 0 )
			{
				$url[] = '		<image:image>';
				$url[] = '			<image:loc>' . BASE_URL . 'secure-files/images/' . $con->ImageID . '/</image:loc>';
				$url[] = '			<image:caption>' . urlencode( $con->DisplayName ) . '</image:caption>';
				$url[] = '		</image:image>';
				
				$urls++; if( $urls >= $ulimit ) break;
			}
			
			$url[] = '	</url>';
			$xml[] = implode( "\n", $url );
			
			if( $slinks )
			{
				foreach( $slinks as $sli )
				{
					$url   = [];
					$url[] = '	<url>'; 
					$url[] = '		<loc>' . BASE_URL . urlencode( $con->Username ) . '/' . $sli . '/</loc>';
					$url[] = '	</url>';
					$xml[] = implode( "\n", $url );
					
					$urls++; if( $urls >= $ulimit ) break;
				}
			}
			
			$urls++; if( $urls >= $ulimit ) break;
		}
		
		unset( $first ); unset( $middle ); unset( $last ); unset( $slinks ); unset( $contacts );
	}
}

// --- Groups ------------------------------------------------------------------------------------------------------------------------------------

if( ComponentAccess( 'groups', 'main' ) )
{
	if( $groups = $database->fetchObjectRows ( '
		SELECT 
			g2.*, 
			mr.MediaID 
		FROM 
			SBookCategory g1, 
			SBookCategory g2 
				LEFT JOIN SBookMediaRelation mr ON 
				(
						mr.CategoryID = g2.ID 
					AND mr.UserID = "0" 
					AND mr.Name = "Cover Photos" 
					AND mr.MediaType = "Folder" 
				)
		WHERE 
				g1.CategoryID = "0" 
			AND g1.Type = "Group" 
			AND g1.Name = "Groups" 
			AND g2.CategoryID = g1.ID 
			AND g2.Type = "SubGroup" 
			AND g2.Privacy != "SecretGroup" 
		ORDER BY 
			g2.ID DESC,
			g2.Name ASC 
	' ) )
	{
		$meids = []; $imids = []; $slinks = [];
		
		if( $tabs = $database->fetchObjectRows( '
			SELECT
				* 
			FROM
				STabs 
			WHERE
					Type = "global"
				AND Component = "groups"
				AND Module = "main"
				AND Permission = "" 
			ORDER BY
				SortOrder ASC,
				Tab ASC 
		' ) )
		{
			foreach( $tabs as $tab )
			{
				$slinks[] = $tab->Tab;
			}
			
			unset( $tabs );
		}
		
		foreach( $groups as $grp )
		{
			if( $grp->MediaID )
			{
				$meids[$grp->MediaID] = $grp->MediaID;
			}
		}
		
		if( $meids && ( $images = $database->fetchObjectRows( '
			SELECT 
				i.* 
			FROM 
				Image i 
			WHERE 
				i.ImageFolder IN (' . implode( ',', $meids ) . ') 
			ORDER BY 
				i.SortOrder ASC, 
				i.ID DESC 
		' ) ) )
		{
			foreach( $images as $img )
			{
				if( isset( $imids[$img->ImageFolder] ) ) continue;
				
				$imids[$img->ImageFolder] = $img;
			}
			
			unset( $images );
		}
		
		foreach( $groups as $grp )
		{
			$url   = [];
			$url[] = '	<url>'; 
			$url[] = '		<loc>' . BASE_URL . 'groups/' . $grp->ID . '/</loc>';
			
			if( isset( $imids[$grp->MediaID] ) )
			{
				$url[] = '		<image:image>';
				$url[] = '			<image:loc>' . BASE_URL . 'secure-files/images/' . $imids[$grp->MediaID]->ID . '/</image:loc>';
				$url[] = '			<image:caption>' . urlencode( $grp->Name ) . '</image:caption>';
				$url[] = '		</image:image>';
				
				$urls++; if( $urls >= $ulimit ) break;
			}
			
			$url[] = '	</url>';
			$xml[] = implode( "\n", $url );
			
			if( $slinks )
			{
				foreach( $slinks as $sli )
				{
					$url   = [];
					$url[] = '	<url>'; 
					$url[] = '		<loc>' . BASE_URL . 'groups/' . $grp->ID . '/' . $sli . '/</loc>';
					$url[] = '	</url>';
					$xml[] = implode( "\n", $url );
					
					$urls++; if( $urls >= $ulimit ) break;
				}
			}
			
			$urls++; if( $urls >= $ulimit ) break;
		}
		
		unset( $meids ); unset( $imids ); unset( $slinks ); unset( $groups );
	}
}

// --- Nodes ------------------------------------------------------------------------------------------------------------------------------

if( $nodes = $database->fetchObjectRows( '
	SELECT 
		Url, 
		Version, 
		Uptime, 
		Open, 
		Users, 
		Rating, 
		Location 
	FROM 
		SNodes 
	WHERE 
			IsConnected = "1" 
		AND IsPending = "0" 
		AND IsDenied = "0" 
		AND Open >= "0" 
	ORDER BY 
		ID ASC 
' ) )
{
	foreach( $nodes as $nod )
	{
		$url   = [];
		$url[] = '	<url>'; 
		$url[] = '		<loc>' . $nod->Url . '</loc>';
		$url[] = '	</url>';
		$xml[] = implode( "\n", $url );
		
		$urls++; if( $urls >= $ulimit ) break;
	}
}

// TODO: Sort urls based on datemodified to get the next stuff on top dynamically

$out[] = implode( "\n", $xml );

$out[] = '</urlset>';

//die( 'urls: ' . $urls );

die( implode( "\n", $out ) );

?>
