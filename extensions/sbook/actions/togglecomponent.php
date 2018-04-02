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

$f = new dbObject( 'ContentDataSmall' );
if( $f->Load( $_REQUEST['fid'] ) )
{
	$c = CreateObjectFromString( $f->DataMixed );
	if( isset( $c->Components ) )
	{
		if( $components = explode( ',', $c->components ) )
		{
			$out = array();
			foreach ( $components as $comp )
			{
				if( $comp == $_REQUEST['component'] )
					continue;
				$out[] = trim( $comp );
			}
			if( $_REQUEST['status'] == '1' )
			{
				$out[] = trim( $_REQUEST['component'] );
			}
			$c->Components = implode( ',', $out );
		}
	}
	else
	{
		$c->Components = $_REQUEST['status'] == '1' ? $_REQUEST['component'] : '';
	}
	$f->DataMixed = CreateStringFromObject( $c );
	$f->Save();
	die( 'ok' );
}
die( 'fail' );

?>
