
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

function OrderMemberExport( key, type, ajax, update )
{
	if( key && type && ajax )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=admin&action=memberexport&type=' + type + '&groupid=' + key + '&save=true', 'post', true );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				if ( update )
				{
					location.reload();
				}
			}
			else console.log( this.getResponseText() );
		}
		j.send ();
	}
	else if( key && type )
	{
		window.open( getPath() + '?component=admin&action=memberexport&type=' + type + '&groupid=' + key, '_blank' );
	}
}

function DeleteOrder( oid, cfm )
{
	if( !oid ) return;
	
	//if( confirm( 'er du sikker?' ) )
	if( cfm || confirmCustom( 'er du sikker?', 'DeleteOrder('+oid+',true)' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=orders&action=orderdelete', 'post', true );
		j.addVar ( 'oid', oid );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				//alert( 'order deleted' );
				// TODO: refresh orders
				EditOrder( oid, true );
				
				//alert( 'slettet' );
				
				alertWindow( 'Slettet', false, 2000 );
				
				console.log( 'Slettet' );
			}
			//else alert( this.getResponseText() );
			else console.log( this.getResponseText() );
		}
		j.send ();
	}
}

function EditOrder( oid, update, newsub )
{
	if ( !oid ) return;
	
	var ele = ge( 'OrderDetails_' + oid + ( newsub ? '_0' : '' ) );
	
	if ( ele )
	{
		if ( !update && ele.className.indexOf( 'open' ) >= 0 )
		{
			ele.className = ele.className.split( ' open' ).join( '' );
			//ele.innerHTML = '';
			return;
		}
		else if ( !update && ele.innerHTML != '' )
		{
			ele.className = ele.className.split( ' open' ).join( '' ) + ' open';
			return;
		}
		
		if ( ge( 'oid_' + oid ) && ge( 'oid_' + oid ).className.indexOf( 'loading' ) < 0 )
		{
			ge( 'oid_' + oid ).className = ge( 'oid_' + oid ).className.split( ' loading' ).join( '' ) + ' loading';
		}
		
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=orders&function=orderdetails' + ( getPathVar( 's' ) ? '&s=' + getPathVar( 's' ) : '&s=1' ) + ( getPathVar( 'h' ) ? '&h=' + getPathVar( 'h' ) : '' ), 'post', true );
		j.addVar ( 'oid', oid );
		if ( newsub )
		{
			j.addVar ( 'newsub', true );
			j.addVar ( 'ID', '' );
			j.addVar ( 'ParentID', oid );
			j.addVar ( 'JobID', '' );
			j.addVar ( 'F2', '' );
		}
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				if ( update && ( oid == '0' || newsub ) )
				{
					location.reload();
				}
				else
				{
					ele.className = ele.className.split( ' open' ).join( '' ) + ' open';
					ele.innerHTML = r[1];
				}
			}
			else
			{
				if ( update )
				{
					location.reload();
				}
				
				//ele.className = ele.className.split( ' open' ).join( '' );
				//ele.innerHTML = '';
				
				console.log( this.getResponseText() );
			}
			
			if ( ge( 'oid_' + oid ) )
			{
				ge( 'oid_' + oid ).className = ge( 'oid_' + oid ).className.split( ' loading' ).join( '' );
			}
		}
		j.send ();
	}
}

function SaveOrder( oid, newsub )
{
	if( !oid ) return;
	
	var OrderData = ge( 'OrderDetails_' + oid + ( newsub ? '_0' : '' ) );
	
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
								else if ( items[b].type == 'checkbox' )
								{
									obj.Fields[items[b].name] = ( items[b].checked ? items[b].value : '' );
								}
								else if ( items[b].type == 'select-multiple' )
								{
									var sld = new Array();
									
									var sel = items[b].getElementsByTagName( 'option' );
									
									if ( sel.length > 0 )
									{
										for ( var c = 0; c < sel.length; c++ )
										{
											if ( sel[c].selected || items[b].className == 'selectmanager' )
											{
												sld.push( sel[c].value );
											}
										}
									}
									
									obj.Fields[items[b].name] = ( sld ? sld.join(',') : '' );
								}
								else if ( !items[b].type || items[b].type == 'textarea' || items[b].tagName == 'SELECT' || items[b].type == 'text' || items[b].type == 'hidden' )
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
		//console.log( data );
		if ( data )
		{
			var j = new bajax ();
			j.openUrl ( getPath() + '?component=orders&action=ordersave', 'post', true );
			j.addVar ( 'data', JSON.stringify( data ) );
			if ( newsub )
			{
				j.addVar ( 'newsub', true );
			}
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );
				if ( r[0] == 'ok' && r[1] )
				{
					if ( newsub )
					{
						// TODO: Update whole list and add new suborder in list
						EditOrder( oid, true, true );
					}
					else
					{
						EditOrder( oid, true );
					}
					//EditOrder( oid );
					
					//alert( 'lagret' );
					
					alertWindow( 'Lagret', false, 2000 );
					
					console.log( 'Lagret' );
				}
				//else alert( this.getResponseText() );
				else console.log( this.getResponseText() );
			}
			j.send ();
		}
	}
}

var saveMessages = {};

function SaveHour( ele, oid, hid, uid, fromdate, todate, cfm )
{
	if ( !oid ) return;
	
	var data = new Array();
	
	if ( ele )
	{
		var parent = ele.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
	}
	else
	{
		var parent = ge( 'HourID_' + oid + '_' + ( hid ? hid : '0' ) );
	}
	
	if ( parent )
	{		
		var inp = parent.getElementsByTagName( '*' );
		
		if ( inp.length > 0 )
		{
			var obj = new Object();
			obj.Table = 'SBookHours';
			obj.Primary = 'ID';
			obj.Fields = new Object();
			
			for ( var a = 0; a < inp.length; a++ )
			{
				if ( inp[a].name && ( inp[a].tagName == 'INPUT' || inp[a].tagName == 'SELECT' || inp[a].tagName == 'TEXTAREA' ) )
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
	
	//if ( data && ( obj.Fields.Hours <= 7.5 || ( obj.Fields.Hours > 7.5 && confirm( 'Du har skrevet mer enn 7.5timer er du sikker?' ) ) ) )
	if ( data && ( obj.Fields.Hours <= 7.5 || ( obj.Fields.Hours > 7.5 && ( cfm || confirmCustom( 'Du har skrevet mer enn 7.5timer er du sikker?', 'SaveHour('+ele+','+oid+','+hid+','+uid+','+fromdate+','+todate+',true)' ) ) ) ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=orders&action=ordersavehour', 'post', true );
		j.addVar ( 'data', JSON.stringify( data ) );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				if ( uid > 0 && ge( 'MemberDetails_' + uid ) )
				{
					EditMemberHours( uid, fromdate, todate, true );
				}
				else if ( hid > 0 && ge( 'HourID_' + hid ) )
				{
					// TODO: Update only hour box get data from server, or update just with js
					//EditHours( hid, true );
					openHourEditor( false, hid );
				}
				else if ( oid > 0 && ge( 'OrderDetails_' + oid ) )
				{
					EditOrder( oid, true );
					//EditOrder( oid );
				}
				
				alertWindow( 'Lagret', false, 2000 );
				
				console.log( 'Lagret' );
				
				//alert( 'lagret' );
				
				/*var orderdetails = document.getElementById('OrderDetails_' + oid);
				var saved = documen.createElement('div')
				saved.innerHTML = 'Lagret...';
				saved.className = 'saved';
				
				orderdetails.appendChild( saved );
				
				if( !saveMessages['hideSavedOrderDetails' + oid ] )
				{
					saveMessages['hideSavedOrderDetails' + oid ] = function() {
						
					}
				}
				
				setTimeOut(function() {
					
				},3000)
				*/
				
			}
			//else alert( this.getResponseText() );
			else console.log( this.getResponseText() );
		}
		j.send ();
	}
}

function EditHours( hid, update )
{
	if( !hid ) return;
	
	if( !update && ge( 'HourID_' + hid ).className.indexOf( 'open' ) >= 0 )
	{
		ge( 'HourID_' + hid ).className = ge( 'HourID_' + hid ).className.split( ' open' ).join( '' );
		return;
	}
	else if( !update && ge( 'HourID_' + hid ).innerHTML != '' )
	{
		ge( 'HourID_' + hid ).className = ge( 'HourID_' + hid ).className.split( ' open' ).join( '' ) + ' open';
		return;
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=orders&function=orderdetails' + ( getPathVar( 's' ) ? '&s=' + getPathVar( 's' ) : '&s=1' ) + ( getPathVar( 'h' ) ? '&h=' + getPathVar( 'h' ) : '' ), 'post', true );
	j.addVar ( 'hid', hid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'HourID_' + hid ).className = ge( 'HourID_' + hid ).className.split( ' open' ).join( '' ) + ' open';
			ge( 'HourID_' + hid ).innerHTML = r[1];
		}
		else
		{
			//ge( 'HourID_' + hid ).className = ge( 'HourID_' + hid ).className.split( ' open' ).join( '' );
			//ge( 'HourID_' + hid ).innerHTML = '';
			console.log( this.getResponseText() );
		}
	}
	j.send ();
}

function DeleteHour( hid, oid, uid, fromdate, todate, cfm )
{
	//if ( hid && oid && confirm( 'er du sikker?' ) )
	if ( hid && oid && ( cfm || confirmCustom( 'er du sikker?', 'DeleteHour('+hid+','+oid+','+uid+','+fromdate+','+todate+',true)' ) ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=orders&action=orderdeletehour', 'post', true );
		j.addVar ( 'hid', hid );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				if ( uid > 0 && ge( 'MemberDetails_' + uid ) )
				{
					//EditMemberHours( uid, fromdate, todate, true );
					EditHoursMember( uid, false, fromdate, todate, true );
				}
				else if ( oid > 0 && ge( 'OrderDetails_' + oid ) )
				{
					EditOrder( oid, true );
				}
				
				alertWindow( 'Slettet', false, 2000 );
				
				console.log( 'Slettet' );
			}
			else console.log( this.getResponseText() );
		}
		j.send ();
	}
}


function EditHoursMember( uid, oid, fromdate, todate, update )
{
	if( !uid ) return;
	
	if( !update && ge( 'MemberDetails_' + uid ).className.indexOf( 'open' ) >= 0 )
	{
		ge( 'MemberDetails_' + uid ).className = ge( 'MemberDetails_' + uid ).className.split( ' open' ).join( '' );
		return;
	}
	else if( !update && ge( 'MemberDetails_' + uid ).innerHTML != '' )
	{
		ge( 'MemberDetails_' + uid ).className = ge( 'MemberDetails_' + uid ).className.split( ' open' ).join( '' ) + ' open';
		return;
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=orders&function=orderdetails&mode=members' + ( getPathVar( 's' ) ? '&s=' + getPathVar( 's' ) : '&s=1' ) + ( getPathVar( 'h' ) ? '&h=' + getPathVar( 'h' ) : '' ), 'post', true );
	if ( oid )
	{
		j.addVar ( 'oid', oid );
	}
	j.addVar ( 'uid', uid );
	if ( fromdate ) j.addVar ( 'fromdate', fromdate );
	if ( todate ) j.addVar ( 'todate', todate );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'MemberDetails_' + uid ).className = ge( 'MemberDetails_' + uid ).className.split( ' open' ).join( '' ) + ' open';
			ge( 'MemberDetails_' + uid ).innerHTML = r[1];
		}
		else
		{
			ge( 'MemberDetails_' + uid ).className = ge( 'MemberDetails_' + uid ).className.split( ' open' ).join( '' );
			ge( 'MemberDetails_' + uid ).innerHTML = '';
			console.log( this.getResponseText() );
		}
	}
	j.send ();
}


function CheckThis( ele, e )
{
	ele.checked = ( ele.checked ? true : false );
	
	return cancelBubble(e);
}

function CheckHour( ele, e, update )
{
	if ( ele && ele.value )
	{
		var oid = ele.value.split('_')[0];
		var hid = ele.value.split('_')[1];
		
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=orders&action=ordercheckhour' + ( getPathVar( 'h' ) ? '&h=' + getPathVar( 'h' ) : '' ), 'post', true );
		j.addVar ( 'hid', hid );
		j.addVar ( 'checked', ele.checked ? 1 : 0 );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				if ( update && ge( 'HourID_' + ele.value ) && oid && ge( 'OrderDetails_' + oid ) )
				{
					EditOrder( oid, true );
				}
				else
				{
					ge( 'HourID_' + ele.value ).className = ge( 'HourID_' + ele.value ).className.split( ' allowed' ).join( '' );
					ge( 'HourID_' + ele.value ).className = ge( 'HourID_' + ele.value ).className.split( ' pending' ).join( '' );
					ge( 'HourID_' + ele.value ).className = ge( 'HourID_' + ele.value ).className + ( ele.checked ? ' allowed' : ' pending' );
				}
			}
			else console.log( this.getResponseText() );
		}
		j.send ();
	}
	
	return cancelBubble(e);
}

function CheckAllHours( alert, save, cfm )
{
	if ( ge( 'Orders' ) && alert )
	{
		var checks = '';
		
		var inps = ge( 'Orders' ).getElementsByTagName( 'input' );
		
		if ( inps.length > 0 )
		{
			for( a = 0; a < inps.length; a++ )
			{
				if ( inps[a].type == 'checkbox' && inps[a].parentNode.className == 'checked' && inps[a].value && inps[a].checked )
				{
					checks = ( checks ? ( checks + ',' + inps[a].value ) : inps[a].value );
				}
			}
		}
		
		//if ( confirm( alert ) )
		if ( cfm || confirmCustom( alert, 'CheckAllHours(alert,'+save+',true)' ) )
		{
			if ( checks )
			{
				//alert( 'checked: ' + checks );
				
				var j = new bajax ();
				j.openUrl ( getPath() + '?component=orders&action=ordercheckhour&group=true' + ( getPathVar( 'h' ) ? '&h=' + getPathVar( 'h' ) : '' ), 'post', true );
				j.addVar ( 'hid', checks );
				j.addVar ( 'checked', 1 );
				j.onload = function ()
				{
					var r = this.getResponseText ().split ( '<!--separate-->' );
					if ( r[0] == 'ok' )
					{
						if ( save && r[1] )
						{
							OrderMemberExport( r[1], 'pdf', true, true );
						}
						else
						{
							location.reload();
						}
					}
					else console.log( this.getResponseText() );
				}
				j.send ();
			}
			else
			{
				alert( 'ingen timer valgt' );
			}
		}
	}
}

function MarkAllHours( ele, e )
{
	if ( ele && ele.value )
	{
		var elem = ( ge( 'OrderDetails_' + ele.value ) ? ge( 'OrderDetails_' + ele.value ) : ge( 'MemberDetails_' + ele.value ) );
		
		if ( elem )
		{
			var checked = ( ele.checked ? true : false );
			
			var checks = elem.getElementsByTagName( 'input' );
			
			if ( checks.length > 0 )
			{
				for( a = 0; a < checks.length; a++ )
				{
					if ( checks[a].type == 'checkbox' )
					{
						checks[a].checked = ( checked ? true : false );
					}
				}
			}
		}
	}
	
	return cancelBubble(e);
}

function OpenHour( hid, oid, uid, fromdate, todate, cfm )
{
	//if ( hid && oid && confirm( 'er du sikker?' ) )
	if ( hid && oid && ( cfm || confirmCustom( 'er du sikker?', 'OpenHour('+hid+','+oid+','+uid+','+fromdate+','+todate+',true)' ) ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=orders&action=orderopenhour', 'post', true );
		j.addVar ( 'hid', hid );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				if ( uid > 0 && ge( 'MemberDetails_' + uid ) )
				{
					EditMemberHours( uid, fromdate, todate, true );
				}
				else if ( oid > 0 && ge( 'OrderDetails_' + oid ) )
				{
					// TODO: Refresh Hour not the whole order
					EditOrder( oid, true );
				}
			}
			else console.log( this.getResponseText() );
		}
		j.send ();
	}
}

function openHourList( ele )
{
	var parent = ele.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
	
	if ( !ele || !parent ) return;
	
	if ( parent.className.indexOf( 'open' ) >= 0 )
	{
		parent.className = parent.className.split( ' open' ).join( '' ) + ' closed';
		ele.className = ele.className.split( ' open' ).join( '' );
		ele.innerHTML = '<span>[+]</span>';
	}
	else
	{
		parent.className = parent.className.split( ' closed' ).join( '' ) + ' open';
		ele.className = ele.className.split( ' open' ).join( '' ) + ' open';
		ele.innerHTML = '<span>[-]</span>';
		
		var inps = parent.getElementsByTagName( 'input' );
		
		if ( inps.length > 0 )
		{
			for ( a = 0; a < inps.length; a++ )
			{
				if ( inps[a].type == 'text' )
				{
					inps[a].focus();
					break;
				}
			}
		}
	}
}

function openHourEditor( ele, hid )
{
	if ( ele )
	{
		var parent = ele.parentNode;
	}
	else
	{
		var parent = ge( 'HourID_' + ( hid ? hid : '0' ) );
	}
	
	if ( parent.className.indexOf( 'open' ) >= 0 )
	{
		parent.className = parent.className.split( ' open' ).join( '' ) + ' closed';
		
		if ( parent.id )
		{
			window.location.hash = '';
		}
	}
	else
	{
		parent.className = parent.className.split( ' closed' ).join( '' ) + ' open';
		
		if ( parent.id )
		{
			window.location.hash = parent.id;
		}
		
		var inps = parent.getElementsByTagName( 'input' );
		
		if ( inps.length > 0 )
		{
			for ( a = 0; a < inps.length; a++ )
			{
				if ( ( inps[a].type == 'text' || inps[a].type == 'number' ) && !inps[a].readOnly && !inps[a].disabled )
				{
					inps[a].focus();
					break;
				}
			}
		}
	}
}

function openHourGroup( ele )
{
	if ( ele && ele.parentNode )
	{
		var elem = ele.parentNode;
		
		if ( elem.className.indexOf( 'open' ) >= 0 )
		{
			elem.className = elem.className.split( ' open' ).join( '' ) + ' closed';
		}
		else
		{
			elem.className = elem.className.split( ' closed' ).join( '' ) + ' open';
		}
	}
}

function OrderHistory( oid, e )
{
	if ( oid > 0 )
	{
		DialogWindow( e, 'orders', 'history', { 'oid' : oid } );
	}
}

function HourHistory( hid, oid, e )
{
	if ( hid > 0 && oid > 0 )
	{
		DialogWindow( e, 'orders', 'history', { 'hid' : hid, 'oid' : oid } );
	}
}

function UpdateStructureManager( oid, ele )
{
	if ( oid && ele && ge( 'structuremanager_' + oid ) )
	{
		//var participants = ele.getAttribute( 'participants' );
		var current = ele.getAttribute( 'selected' );
		var parent = ele.getAttribute( 'parent' );
		var value = ele.value;
		
		//alert( 'soon: ' + current + ' [] ' + value + ' [] ' + parent );
		
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=orders&action=orderupdateaccess', 'post', true );
		
		j.addVar ( 'oid', oid );
		
		if ( parent )
		{
			j.addVar ( 'parent', parent );
		}
		if ( current )
		{
			j.addVar ( 'current', current );
		}
		if ( value )
		{
			j.addVar ( 'value', value );
		}
		
		var inp = ge( 'structuremanager_' + oid ).getElementsByTagName( 'input' );
		
		if ( inp.length > 0 )
		{
			for ( a = 0; a < inp.length; a++ )
			{
				if ( inp[a].name )
				{
					j.addVar ( inp[a].name, inp[a].value );
				}
			}
		}
		
		var li = ge( 'structuremanager_' + oid ).getElementsByTagName( 'li' );
		
		if ( li.length > 0 )
		{
			var open;
			
			for ( b = 0; b < li.length; b++ )
			{
				if ( li[b].className.indexOf( 'open' ) >= 0 && li[b].getAttribute( 'sort' ) )
				{
					open = ( open ? ( open + ',' + li[b].getAttribute( 'sort' ) ) : li[b].getAttribute( 'sort' ) );
				}
			}
			
			if ( open )
			{
				j.addVar ( 'open', open );
			}
		}
		
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				ge( 'structuremanager_' + oid ).innerHTML = r[1];
			}
			else console.log( this.getResponseText() );
		}
		j.send ();
	}
}
