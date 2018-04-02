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

global $document, $database, $webuser;

$root = 'subether/';
$cbase = 'subether/components/bank';

// Setup resources -------------------------------------------------------------
$document->addResource ( 'stylesheet', $cbase . '/css/bank.css' );
$document->addResource ( 'javascript', $cbase . '/javascript/bank.js' );

// Check for user actions ------------------------------------------------------
if ( isset( $_REQUEST[ 'action' ] ) )
{
	if ( file_exists ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' ) )
    {
       include ( $cbase . '/actions/' . $_REQUEST[ 'action' ] . '.php' );
    }
	die( 'failed action request - bank' );
}

// Calculate Disposable -------------------------------------------------------

if ( $acc = $database->fetchObjectRows( '
	SELECT
		*
	FROM
		SBookAccounts
	WHERE
		UserID = \'' . $webuser->ID . '\' 
	ORDER BY
		ID ASC
' ) )
{
	foreach ( $acc as $ac )
	{
		if ( $negative = $database->fetchObjectRow( $q1 = '
			SELECT
				SUM(t.Amount) AS Amount 
			FROM
				SBookTransaction t
			WHERE
				t.From = \'' . $ac->Account . '\' 
			ORDER BY
				t.Amount ASC 
		' ) );
		
		if ( $positive = $database->fetchObjectRow( $q2 = '
			SELECT
				SUM(t.Amount) AS Amount 
			FROM
				SBookTransaction t
			WHERE
				t.To = \'' . $ac->Account . '\' 
			ORDER BY
				t.Amount ASC 
		' ) );
		
		$a = new dbObject( 'SBookAccounts' );
		if ( $a->Load( $ac->ID ) )
		{
			if ( $a->Disposable != ( $positive->Amount - $negative->Amount ) )
			{
				$a->Disposable = ( $positive->Amount - $negative->Amount );
				$a->Save();
			}
		}
	}
}

include ( $cbase . '/include/bank.php' );

$Component->Content = $str;

?>
