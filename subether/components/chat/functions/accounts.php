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

$fstr = '';

if( $acc = $database->fetchObjectRows( '
	SELECT
		*
	FROM
		SBookMailAccounts
	WHERE
		UserID = \'' . $webuser->ID . '\'
		' . /*( $_POST['accid'] ? 'AND ID =\'' . $_POST['accid'] . '\' ' : '' ) .*/ '
		AND IsDeleted = "0" 
	ORDER BY
		SortOrder ASC, DateCreated ASC, ID ASC 
' ) )
{
	$i = 0;
	
	foreach( $acc as $a )
	{
		$a->Folders = json_obj_decode( $a->Folders, 'array' );
		
		if( $a->Address )
		{
			$fstr .= '<h4 onclick="getHeaders(\'' . $a->ID . '\',\'INBOX\')" accountid="' . $a->ID . '">';
			//$fstr .= '<h4>';
			if( $a->ErrorCode > 0 )
			{
				$fstr .= '<span class="ealert" title="' . $a->ErrorCode . '"><img src="admin/gfx/icons/error.png"/> </span>';
			}
			$fstr .= '<span>' . $a->Address . '</span>';
			$fstr .= '</h4>';
			
			if( $a->Folders && is_array( $a->Folders ) )
			{
				$ii = 0; $current = '';
				
				$fstr .= '<ul>';
				
				foreach( $a->Folders as $k=>$f )
				{
					$mail = $database->fetchObjectRow( '
						SELECT 
							COUNT(ID) AS Count
						FROM 
							SBookMailHeaders
						WHERE 
								UserID = \'' . $webuser->ID . '\' 
							AND AccountID = \'' . $a->ID . '\'
							AND Folder = \'' . $f . '\'
							AND IsRead = "0" 
						ORDER BY 
							ID ASC 
					' );
					
					if( $current == '' && !$_POST['fld'] && $_POST['current'] )
					{
						if( is_string( $_POST['current'] ) && strstr( $_POST['current'], '_' ) )
						{
							$_POST['current'] = explode( '_', $_POST['current'] );
						}
						
						if( $a->ID == $_POST['current'][0] && $f == $_POST['current'][1] )
						{
							$current = ' class="current first"';
						}
					}
					else if( $current == '' && $_POST['accid'] && $_POST['fld'] )
					{
						if( $_POST['accid'] == $a->ID && $_POST['fld'] == $f )
						{
							$current = ' class="current"';
						}
					}
					else if( $current == '' && $i == 0 && $ii == 0 )
					{
						$current = ' class="current"';
					}
					else
					{
						$current = '';
					}
					
					$fstr .= '<li' . $current . ' accountid="' . $a->ID . '" folder="' . $f . '" onclick="openFolder(this)">';
					$fstr .= '<div>' . str_replace( 'INBOX.', '', $f ) . ( $mail && $mail->Count > 0 ? ( ' (' . $mail->Count . ')' ) : '' ) . '</div>';
					$fstr .= '</li>';
					
					$ii++;
				}
				
				$fstr .= '</ul>';
			}
		}
		
		$i++;
	}
}

if( isset( $_REQUEST['bajaxrand'] ) )
{
	die( $fstr ? ( 'ok<!--separate-->' . $fstr ) : 'fail' );
}

?>
