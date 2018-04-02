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

include_once ( 'subether/classes/fcrypto.class.php' );

$fcrypt = new fcrypto();

if ( !$webuser->ID && !$_POST['UniqueID'] )
{
	output( 'fail<!--separate-->' . BASE_URL );
}
else if ( !$webuser->ID && $_POST['PublicKey'] && $_POST['UniqueID'] && $usr = $database->fetchObjectRow( '
	SELECT
		*
	FROM
		`Users`
	WHERE
			InActive = "0"
		AND UserType = "0" 
		AND IsDisabled = "0" 
		AND IsDeleted = "0" 
		AND UniqueID = \'' . trim( $_POST['UniqueID'] ) . '\' 
	ORDER BY
		ID DESC
' ) )
{
	$pkey = $fcrypt->stripHeader( $_POST['PublicKey'] );
	$dkey = $fcrypt->stripHeader( $usr->PublicKey );
	
	if ( $pkey == $dkey && $usr->Password )
	{
		$ciphertext = $fcrypt->encryptRSA( $usr->Password, $usr->PublicKey );
		
		if ( $ciphertext )
		{
			output( 'ok<!--separate-->' . trim( $ciphertext ) );
		}
		
		output( 'fail' );
	}
	
	output( 'fail<!--separate-->' . BASE_URL );
}
else if ( $webuser->ID > 0 )
{
	output( 'authenticated<!--separate-->allready logged in<!--separate-->' . $fcrypt->encryptRSA( $webuser->GetToken(), $webuser->PublicKey ) );
}

output( 'fail' );

?>
