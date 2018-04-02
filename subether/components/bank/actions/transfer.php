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

if ( $_POST )
{
	$required = array( 'From', 'To', 'Amount', 'ProcessDate' );
	
	foreach ( $required as $r )
	{
		if ( !isset( $_POST[$r] ) || trim( $_POST[$r] ) == '' )
		{
			die( 'This field is required : ' . $_POST[$r] );
		}
	}
}

if ( !$verify = $database->fetchObjectRow( 'SELECT * FROM SBookAccounts WHERE Account = \'' . str_replace( array( '.', ' ' ), array( '', '' ), trim( $_POST['To'] ) ) . '\' ORDER BY ID ASC' ) )
{

	die( 'Account doesnt exist!' );
}

if ( str_replace( array( '.', ' ' ), array( '', '' ), trim( $_POST['From'] ) ) == str_replace( array( '.', ' ' ), array( '', '' ), trim( $_POST['To'] ) ) )
{
	die( 'Cant make a transfer to the same account' );
}

if ( $_REQUEST['accid'] > 0 && $_POST['From'] > 0 && ( $acc = $database->fetchObjectRow( '
	SELECT
		*
	FROM
		SBookAccounts
	WHERE
		UserID = \'' . $webuser->ID . '\' AND Account = \'' . str_replace( array( '.', ' ' ), array( '', '' ), trim( $_POST['From'] ) ) . '\' 
	ORDER BY
		ID ASC
' ) ) )
{
	$t = new dbObject( 'SBookTransaction' );
	$t->UniqueID = UniqueKey();
	$t->CID = $_POST['CID'];
	$t->Name = $_POST['Message'];
	$t->From = $acc->Account;
	$t->To = str_replace( array( '.', ' ' ), array( '', '' ), trim( $_POST['To'] ) );
	$t->Details = $_POST['Message'];
	$t->Message = $_POST['Message'];
	$t->Amount = $_POST['Amount'];
	$t->Verified = 0;
	$t->DateCreated = date( 'Y-m-d H:i:s' );
	$t->ProcessCreated = date( 'Y-m-d H:i:s', strtotime( $_POST['ProcessDate'] ) );
	$t->Save();
	
	if ( $t->ID > 0 )
	{
		if ( $negative = $database->fetchObjectRow( '
			SELECT
				SUM(t.Amount) AS Amount 
			FROM
				SBookTransaction t
			WHERE
				t.From = \'' . $acc->Account . '\' 
			ORDER BY
				t.Amount ASC 
		' ) );
		
		if ( $positive = $database->fetchObjectRow( '
			SELECT
				SUM(t.Amount) AS Amount 
			FROM
				SBookTransaction t
			WHERE
				t.To = \'' . $acc->Account . '\' 
			ORDER BY
				t.Amount ASC 
		' ) );
		
		$a = new dbObject( 'SBookAccounts' );
		if ( $a->Load( $acc->ID ) )
		{
			if ( $a->Disposable != ( $positive->Amount - $negative->Amount ) )
			{
				$a->UniqueID = ( $a->UniqueID ? $a->UniqueID : UniqueKey() );
				$a->Disposable = ( $positive->Amount - $negative->Amount );
				$a->Save();
			}
		}
		
		include ( 'subether/components/bank/include/bank.php' );
		
		die( 'ok<!--separate-->' . $str );
	}
}

die( 'fail' );

?>
