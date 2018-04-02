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

if( $_POST['oid'] > 0 && ( $order = $database->fetchObjectRow( '
	SELECT 
		o.* 
	FROM 
		SBookOrders o 
	WHERE 
			o.ID = \'' . $_POST['oid'] . '\' 
	ORDER BY 
		o.ID ASC 
' ) ) )
{
	$o = new dbObject( 'SBookOrders' );
	$o->ID = $order->ID;
	if( $o->Load() )
	{
		if( $items = $database->fetchObjectRows( '
			SELECT 
				i.* 
			FROM 
				SBookOrderItems i 
			WHERE 
					i.OrderID = \'' . $o->ID . '\' 
			ORDER BY 
				i.ID ASC 
		' ) )
		{
			foreach( $items as $item )
			{
				$i = new dbObject( 'SBookOrderItems' );
				$i->ID = $item->ID;
				if( $i->Load() )
				{
					$dl = $i->ID;
					$us = $i->UserID;
					
					//$i->IsDeleted = 1;
					//$i->Save();
					$i->Delete();
					
					LogUserActivity( $us, 'delete hour', false, 'delete', 'SBookHours', $dl );
				}
			}
		}
		
		$del = $o->ID;
		$usr = $o->UserID;
		
		//$o->IsDeleted = 1;
		//$o->Save();
		$o->Delete();
		
		LogUserActivity( $usr, 'delete order', false, 'delete', 'SBookOrders', $del );
	}
	
	die( 'ok<!--separate-->' . $del );
}

die( 'fail' );

?>
