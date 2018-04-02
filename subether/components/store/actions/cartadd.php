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

if ( $_POST['pid'] > 0 && ( $prod = $database->fetchObjectRow( '
	SELECT 
		* 
	FROM 
		SBookProducts 
	WHERE 
			`ID` = \'' . $_POST['pid'] . '\'
		AND `CategoryID` = \'' . $parent->folder->CategoryID . '\' 
		AND `IsDeleted` = "0" 
	ORDER BY 
		ID DESC 
' ) ) )
{
	$o = new dbObject( 'SBookOrders' );
	$o->TemplateID = 1;
	$o->CustomerID = $webuser->ContactID;
	$o->CategoryID = $parent->folder->CategoryID;
	if( !$o->Load() )
	{
		$o->DateCreated = date( 'Y-m-d H:i:s' );
	}
	$o->DateModified = date( 'Y-m-d H:i:s' );
	$o->Save();
	
	if( $tmp1 = $database->fetchObjectRows( '
		SELECT 
			* 
		FROM 
			SBookTemplateFields 
		WHERE 
				`TemplateID` = \'' . $o->TemplateID . '\' 
			AND `Relation` = "SBookOrderItems" 
			AND `Function` = "display" 
			AND `Target` = "SBookProducts" 
			AND `IsDeleted` = "0" 
		ORDER BY 
			ID ASC 
	' ) )
	{
		$i = new dbObject( 'SBookOrderItems' );
		$i->OrderID = $o->ID;
		
		foreach( $tmp1 as $f )
		{
			$i->{$f->Field} = $prod->{$f->Column};
		}
		
		$i->Save();
	}
	
	/*if( $tmp2 = $database->fetchObjectRows( '
		SELECT 
			* 
		FROM 
			SBookTemplateFields 
		WHERE 
				`TemplateID` = \'' . $o->TemplateID . '\' 
			AND `Relation` = "SBookOrders" 
			AND `Function` = "sum" 
			AND `Target` = "SBookOrderItems" 
			AND `IsDeleted` = "0" 
		ORDER BY 
			ID ASC 
	' ) )
	{
		$s = new dbObject( 'SBookOrders' );
		$s->ID = $o->ID;
		if( $s->Load() )
		{
			foreach( $tmp2 as $f )
			{
				if( $sum = $database->fetchObjectRow( '
					SELECT 
						SUM(' . $f->Column . ') AS Total
					FROM 
						SBookOrderItems 
					WHERE 
						`OrderID` = \'' . $s->ID . '\' 
					ORDER BY 
						ID ASC 
				' ) )
				{
					$s->{$f->Field} = $sum->Total;
				}
			}
			
			$s->Save();
		}
	}*/
	
	die( 'ok<!--separate-->' );
}

die( 'fail' );

?>
