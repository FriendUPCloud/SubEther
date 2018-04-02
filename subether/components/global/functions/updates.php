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

include_once ( 'subether/classes/posthandler.class.php' );

$str = '';

$str .= '<h2>Updates</h2><br>';

if( $lu = $database->fetchObjectRow ( '
	SELECT * 
	FROM SBookCronJobs 
	WHERE Filename = "information_cron.php" 
	ORDER BY ID ASC 
' ) )
{
	$str .= '<div>';
	$str .= '<span>Last checked on ' . date( 'F j, Y', strtotime( $lu->LastExec ) ) . ' at ' . date( 'H:i', strtotime( $lu->LastExec ) ) . '. </span>';
	$str .= '<button onclick="alert(\'soon\')">Check Again</button>';
	$str .= '</div><br>';
}

if( $nd = $database->fetchObjectRows( 'SELECT * FROM SNodes WHERE IsMain = "0" AND IsIndex = "1" AND IsDenied = "0" ORDER BY ID ASC' ) )
{
	$upt = ''; $apt = '';
	
	foreach( $nd as $n )
	{
		$ph = new PostHandler ( $n->Url . 'information/' );
		$ph->AddVar ( 'UniqueID', $n->UniqueID );
		$ph->AddVar ( 'Url', $n->Url );
		$ph->AddVar ( 'Name', $n->Name );
		$ph->AddVar ( 'Version', $n->Version );
		$ph->AddVar ( 'Owner', $n->Owner );
		$ph->AddVar ( 'Email', $n->Email );
		$ph->AddVar ( 'Location', $n->Location );
		$ph->AddVar ( 'Users', $n->Users );
		$ph->AddVar ( 'Open', $n->Open );
		$ph->AddVar ( 'Created', $n->DateCreated );
		$res = $ph->Send();
		
		if( $res && substr( $res, 0, 5 ) == "<?xml" )
		{
			$xml = simplexml_load_string ( trim( $res ) );
			$dat = $xml->items[0]->Information;
			
			if( $xml->response == 'ok' && $dat )
			{
				if( isset( $dat->Releases->Release ) )
				{
					$releases = array();
					
					foreach( $dat->Releases->Release as $obj )
					{
						if ( $obj->FileTitle )
						{
							$title = explode( '_', $obj->FileTitle );
							$name = $title[0];
							
							$type = ( isset( $title[1] ) ? 'update' : 'full' );
							
							$version = str_replace( '.', '', $obj->Version );
							
							if( !isset( $releases[$name] ) )
							{
								$releases[$name] = array();
							}
							if( !isset( $releases[$name][$version] ) )
							{
								$releases[$name][$version] = array();
							}
							
							$releases[$name][$version][$type] = $obj;
						}
					}
					
					if ( isset( $releases[strtolower(NODE_INFO)] ) )
					{
						$current = end( $releases[strtolower(NODE_INFO)] );
						$update = $current['update'];
						$full = $current['full'];
						
						if( !file_exists( BASE_DIR . '/subether/upload/releases/' . ( isset( $update->FileName ) ? (string)$update->FileName : (string)$full->FileName ) ) )
						{
							$upt .= '<div>You can update to Treeroot ' . ( isset( $update->Version ) ? (string)$update->Version : (string)$full->Version ) . ' author: ' . $n->Owner . ' automatically or download the package and install it manually:</div><br>';
							$upt .= '<div>';
							$upt .= '<button onclick="updateCore(\'' . ( isset( $update->FilePath ) ? (string)$update->FilePath : (string)$full->FilePath ) . '\',this)">Update Now</button>';
							$upt .= '<button onclick="document.location=\'' . ( isset( $update->FilePath ) ? (string)$update->FilePath : (string)$full->FilePath ) . '\'">Download ' . ( isset( $update->Version ) ? (string)$update->Version : (string)$full->Version ) . '</button>';
							$upt .= '</div>';
							$upt .= '<br>';
						}
					}
					
					if ( isset( $releases['arena'] ) )
					{
						$current = end( $releases['arena'] );
						$update = $current['update'];
						$full = $current['full'];
						
						if( !file_exists( BASE_DIR . '/subether/upload/releases/' . ( isset( $update->FileName ) ? (string)$update->FileName : (string)$full->FileName ) ) )
						{
							$apt .= '<div>You can update to Arena ' . ( isset( $update->Version ) ? (string)$update->Version : (string)$full->Version ) . ' author: ' . $n->Owner . ' automatically or download the package and install it manually:</div><br>';
							$apt .= '<div>';
							$apt .= '<button onclick="updateCore(\'' . ( isset( $update->FilePath ) ? (string)$update->FilePath : (string)$full->FilePath ) . '\',this,\'arena\')">Update Now</button>';
							$apt .= '<button onclick="document.location=\'' . ( isset( $update->FilePath ) ? (string)$update->FilePath : (string)$full->FilePath ) . '\'">Download ' . ( isset( $update->Version ) ? (string)$update->Version : (string)$full->Version ) . '</button>';
							$apt .= '</div>';
							$apt .= '<br>';
						}
					}
				}
			}
		}
	}
	
	if( $upt )
	{
		$str .= '<h3>An updated version of Treeroot is available.</h3><br>';
		
		$str .= $upt;
		
		$str .= '<div>While your site is being updated, it will be in maintenance mode. As soon as your updates are complete, your site will return to normal.</div><br>';
	}
	else
	{
		$str .= '<h3>Core System</h3><br>';
		$str .= '<div>Treeroot is up to date.</div><br>';
	}
	
	// TODO: Make support for this also in the future, not just core updates
	/*$str .= '<h3>Modules</h3><br>';
	$str .= '<div>Your modules are all up to date.</div><br>';
	
	$str .= '<h3>Components</h3><br>';
	$str .= '<div>Your components are all up to date.</div><br>';
	
	$str .= '<h3>Plugins</h3><br>';
	$str .= '<div>Your plugins are all up to date.</div><br>';
	
	$str .= '<h3>Themes</h3><br>';
	//$str .= '<div>The following themes have new versions available. Check the ones you want to update and then click “Update Themes”.</div><br>';
	$str .= '<div>Your themes are all up to date.</div><br>';
	
	$str .= '<h3>Content Management System</h3><br>';
	$str .= '<div>Your CMS system is up to date.</div><br>';*/
	
	if( $apt )
	{
		$str .= '<h3>An updated version of Arena is available.</h3><br>';
		
		$str .= $apt;
		
		$str .= '<div>While your site is being updated, it will be in maintenance mode. As soon as your updates are complete, your site will return to normal.</div><br>';
	}
	else
	{
		$str .= '<h3>Content Management System</h3><br>';
		$str .= '<div>Your CMS system is up to date.</div><br>';
	}
	
	if ( $upt || $apt )
	{
		//$str .= '<div>While your site is being updated, it will be in maintenance mode. As soon as your updates are complete, your site will return to normal.</div><br>';
	}
}

?>
