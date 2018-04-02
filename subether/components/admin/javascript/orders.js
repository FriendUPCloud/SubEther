
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

function DeleteOrder( oid )
{
	if( !oid ) return;
	
	if( confirm( 'are you sure?' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=admin&action=orderdelete', 'post', true );
		j.addVar ( 'oid', oid );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				alert( 'order deleted' );
			}
			else alert( this.getResponseText() );
		}
		j.send ();
	}
}

function EditOrder( oid )
{
	if( !oid ) return;
	
	if( ge( 'OrderDetails_' + oid ).className.indexOf( 'open' ) >= 0 )
	{
		ge( 'OrderDetails_' + oid ).className = ge( 'OrderDetails_' + oid ).className.split( ' open' ).join( '' );
		ge( 'OrderDetails_' + oid ).innerHTML = '';
		return;
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=admin&function=orderdetails', 'post', true );
	j.addVar ( 'oid', oid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'OrderDetails_' + oid ).className = ge( 'OrderDetails_' + oid ).className.split( ' open' ).join( '' ) + ' open';
			ge( 'OrderDetails_' + oid ).innerHTML = r[1];
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}

function SaveOrder( oid )
{
	if( !oid ) return;
	
	var OrderData = ge( 'OrderDetails_' + oid );
	
	if ( OrderData && OrderData.className.indexOf( 'open' ) >= 0 )
	{
		var data = new Array();
		
		var tables = OrderData.getElementsByTagName( 'div' );
		
		if ( tables.length > 0 )
		{
			for ( var a = 0; a < tables.length; a++ )
			{
				if ( tables[a].getAttribute( 'table' ) )
				{
					var items = tables[a].getElementsByTagName( '*' );
					
					if ( items.length > 0 )
					{
						var obj = new Object();
						obj.Table = tables[a].getAttribute( 'table' );
						obj.Primary = 'ID';
						obj.Fields = new Object();
						
						for ( var b = 0; b < items.length; b++ )
						{
							if ( items[b].name && ( items[b].tagName == 'INPUT' || items[b].tagName == 'SELECT' || items[b].tagName == 'TEXTAREA' ) )
							{
								if ( items[b].type == 'radio' && items[b].checked )
								{
									obj.Fields[items[b].name] = items[b].value;
								}
								if ( items[b].type == 'checkbox' )
								{
									obj.Fields[items[b].name] = ( items[b].checked ? items[b].value : '' );
								}
								if ( items[b].type == 'select-multiple' )
								{
									var sld = new Array();
									
									var sel = items[b].getElementsByTagName( 'option' );
									
									if ( sel.length > 0 )
									{
										for ( var c = 0; c < sel.length; c++ )
										{
											if ( sel[c].selected )
											{
												sld.push( sel[c].value );
											}
										}
									}
									
									obj.Fields[items[b].name] = ( sld ? sld.join(',') : '' );
								}
								if ( !items[b].type || items[b].type == 'textarea' || items[b].type == 'select' || items[b].type == 'text' )
								{
									obj.Fields[items[b].name] = items[b].value;
								}
								//console.log( items[b].tagName + ' -- ' + items[b].type + ' -- ' + items[b].value );
							}
						}
						
						if ( obj.Fields )
						{
							data.push( obj );
						}
					}
				}
			}
		}
		
		if ( data )
		{
			var j = new bajax ();
			j.openUrl ( getPath() + '?component=admin&action=ordersave', 'post', true );
			j.addVar ( 'data', JSON.stringify( data ) );
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );
				if ( r[0] == 'ok' && r[1] )
				{
					alert( 'Saved' );
				}
				else alert( this.getResponseText() );
			}
			j.send ();
		}
	}
}

function SaveHour( ele, oid )
{
	if( !ele || !oid ) return;
	
	var data = new Array();
	
	var parent = ele.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
	
	if ( parent )
	{		
		var inp = parent.getElementsByTagName( 'input' );
		
		if ( inp.length > 0 )
		{
			var obj = new Object();
			obj.Table = 'SBookHours';
			obj.Primary = 'ID';
			obj.Fields = new Object();
			
			for ( var a = 0; a < inp.length; a++ )
			{
				if ( inp[a].name )
				{
					if ( inp[a].type == 'checkbox' )
					{
						obj.Fields[inp[a].name] = ( inp[a].checked ? inp[a].value : 0 );
					}
					else
					{
						obj.Fields[inp[a].name] = inp[a].value;
					}
				}
			}
			
			if ( obj.Fields )
			{
				data.push( obj );
			}
		}
	}
	
	if ( data )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=orders&action=ordersavehour', 'post', true );
		j.addVar ( 'data', JSON.stringify( data ) );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				EditOrder( oid, true );
			}
			else alert( this.getResponseText() );
		}
		j.send ();
	}
}

function openHourList( ele )
{
	var parent = ele.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
	
	if ( !ele || !parent ) return;
	
	if ( parent.className == 'open' )
	{
		parent.className = '';
		ele.className = ele.className.split( ' open' ).join( '' );
		ele.innerHTML = '<span>[+]</span>';
	}
	else
	{
		parent.className = 'open';
		ele.className = ele.className.split( ' open' ).join( '' ) + ' open';
		ele.innerHTML = '<span>[-]</span>';
	}
}
