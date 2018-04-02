
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

function updateCore( src, btn, type )
{
	if( !src || !btn ) return;
	
	var domain = location.protocol + '//' + location.hostname;
	
	var j = new bajax ();
	j.openUrl ( baseUrl() + 'subether/include/updatecore.php', 'post', true );
	j.addVar ( 'src', src );
	j.addVar ( 'type', type );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			initMaintenance( type );
		}
		else if( this.getResponseText().trim() != '' )
		{
			alert( this.getResponseText() );
		}
		
		if ( this.getResponseText().trim() == '' )
		{
			initMaintenance( type );
		}
	}
	j.send ();
	
	//alert( 'Update started' );
	btn.disabled = true;
}

function initMaintenance( type )
{
	//var domain = location.protocol + '//' + location.hostname;
	
	var j = new bajax ();
	//j.openUrl ( domain + '/subether/include/maintenance.php', 'post', true );
	j.openUrl ( baseUrl() + '?global=true&function=maintenance', 'post', true );
	j.addVar ( 'init', true );
	if( type )
	{
		j.addVar ( 'type', type );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			if( MaintenanceStarting )
			{
				alert( r[1] );
				MaintenanceStarting = false;
			}
		}
	}
	j.send ();
}
