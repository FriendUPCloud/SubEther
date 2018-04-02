
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

function EditProduct( ele, pid, e )
{
	if( !ele ) return;
	
	CloseProduct();
	
	ele.data = ele.innerHTML;
	ele.js = ele.parentNode.getAttribute( 'onclick' );
	ele.pid = ( 'ProductInfo' + ( pid ? ( '_' + pid ) : '' ) );
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=store&function=productedit', 'post', true );
	if( pid )
	{
		j.addVar ( 'pid', pid );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			if( pid )
			{
				ele.setAttribute( 'pid', pid );
			}
			ele.id = 'ProductEdit';
			ele.parentNode.removeAttribute( 'onclick' );
			ele.innerHTML = r[1];
			ele.parentNode.className = ele.parentNode.className.split( ' closed' ).join( '' ) + ' open';
			ele.parentNode.className = ele.parentNode.className.split( ' edit' ).join( '' ) + ' edit';
		}
	}
	j.send ();
	
	cancelBubble(e);
}

function SaveProduct()
{
	if( !ge( 'ProductEdit' ) ) return;
	
	var pro = ge( 'ProductEdit' );
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=store&action=productsave', 'post', true );
	
	if( pro.getAttribute( 'pid' ) )
	{
		j.addVar ( 'pid', pro.getAttribute( 'pid' ) );
	}
	if( ge( 'ProductName' ) )
	{
		j.addVar ( 'name', ge( 'ProductName' ).value );
	}
	if( ge( 'ProductInfo' ) )
	{
		j.addVar ( 'info', ge( 'ProductInfo' ).value );
	}
	if( ge( 'ProductPrice' ) )
	{
		j.addVar ( 'price', ge( 'ProductPrice' ).value );
	}
	if( ge( 'ProductImages' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ) )
	{
		var pi = ge( 'ProductImages' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) );
		
		if( pi.getAttribute( 'images' ) )
		{
			j.addVar ( 'images', pi.getAttribute( 'images' ) );
		}
	}
	
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			//alert( 'refresh this f5 :)' );
			//RefreshProduct( r[1] );
			RefreshProduct();
		}
	}
	j.send ();
}

function CloseProduct()
{
	if( ge( 'ProductEdit' ) )
	{
		ge( 'ProductEdit' ).innerHTML = ( ge( 'ProductEdit' ).data ? ge( 'ProductEdit' ).data : '' );
		ge( 'ProductEdit' ).parentNode.setAttribute( 'onclick', ge( 'ProductEdit' ).js );
		ge( 'ProductEdit' ).parentNode.className = ge( 'ProductEdit' ).parentNode.className.split( ' open' ).join( '' ) + ' closed';
		ge( 'ProductEdit' ).removeAttribute( 'pid' );
		ge( 'ProductEdit' ).id = ge( 'ProductEdit' ).pid;
	}
}

function DeleteProduct()
{
	if( ge( 'ProductEdit' ) && ge( 'ProductEdit' ).getAttribute( 'pid' ) && confirm( 'Are you sure?' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=store&action=productdelete', 'post', true );
		j.addVar ( 'pid', ge( 'ProductEdit' ).getAttribute( 'pid' ) );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );	
			if ( r[0] == 'ok' )
			{
				//RefreshProduct( r[1] );
				RefreshProduct();
			}
		}
		j.send ();
	}
}

function RefreshProduct( pid )
{
	if( !ge( 'StoreContent' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=store&function=store', 'post', true );
	if( pid )
	{
		j.addVar ( 'pid', pid );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			/*// Refresh only by id
			if( eid && ge( 'BookingID_' + eid ) && r[1] )
			{
				ge( 'BookingID_' + eid ).innerHTML = r[1];
				ge( 'BookingID_' + eid ).className = ge( 'BookingID_' + eid ).className.split( ' open' ).join( '' ) + ' closed';
				if( img )
				{
					ge( 'BookingID_' + eid ).className = ge( 'BookingID_' + eid ).className.split( ' edit' ).join( '' ) + '';
				}
			}
			// Remove by id
			else if( eid && ge( 'BookingID_' + eid ) && !r[1] )
			{
				ge( 'BookingID_' + eid ).parentNode.removeChild( ge( 'BookingID_' + eid ) );
			}
			// Refresh all
			else
			{
				ge( 'BookingContent' ).innerHTML = r[1];
			}*/
			
			if( r[1] )
			{
				ge( 'StoreContent' ).innerHTML = r[1];
			}
		}
	}
	j.send ();
}

function IncludeProductImage( fid, pid, cid )
{
	if( !fid ) return;
	if( ge( 'ProductImages' + ( pid ? ( '_' + pid ) : '' ) ) )
	{
		var pi = ge( 'ProductImages' + ( pid ? ( '_' + pid ) : '' ) );
		
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=store&function=media', 'post', true );
		j.addVar ( 'fid', fid );
		if( pid )
		{
			j.addVar ( 'pid', pid );
		}
		if( cid )
		{
			j.addVar ( 'cid', cid );
		}
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				if( pid > 0 )
				{
					pi.setAttribute( 'images', ( pi.getAttribute( 'images' ) ? ( pi.getAttribute( 'images' ) + ',' + fid ) : fid ) );
				}
				else
				{
					pi.setAttribute( 'images', fid );
				}
				
				if( fid > 0 && !ge( 'ProductThumbImage' + ( pid ? ( '_' + pid ) : '' ) ) )
				{
					if( pid > 0 )
					{
						SwitchProductImage( fid, pid );
					}
					else
					{
						SwitchProductImage( fid );
					}
				}
				else if( pid > 0 && cid > 0 && ge( 'ProductThumbImg_' + cid ) )
				{
					var tmb = ge( 'ProductThumbImg_' + cid );
					
					tmb.innerHTML = r[1];
					tmb.id = 'ProductThumbImg_' + fid;
					
					SwitchProductImage( fid, pid );
				}
				else if( pid > 0 && ge( 'ProductThumbImage_' + pid ) )
				{
					var tmb = ge( 'ProductThumbImage_' + pid );
					var edt = ge( 'ProductThumbEdit_' + pid );
					
					if( edt )
					{
						var div = document.createElement( 'div' );
						div.id = edt.id;
						div.className = edt.className;
						div.innerHTML = edt.innerHTML;
						
						edt.parentNode.removeChild( edt );
						
						tmb.innerHTML = tmb.innerHTML + r[1];
						tmb.appendChild( div );
					}
				}
			}
			else alert( this.getResponseText() );
		}
		j.send ();
	}
}

function SwitchProductImage( fid, pid )
{
	if( !fid ) return;
	
	if( ge( 'ProductMainImage' + ( pid ? ( '_' + pid ) : '' ) ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=store&function=media&getmain=1', 'post', true );
		j.addVar ( 'fid', fid );
		if( pid )
		{
			j.addVar ( 'pid', pid );
		}
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				ge( 'ProductMainImage' + ( pid ? ( '_' + pid ) : '' ) ).innerHTML = r[1];
			}
			else alert( this.getResponseText() );
		}
		j.send ();
	}
}

function AddToCart( pid )
{
	if( !pid ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=store&action=cartadd', 'post', true );
	j.addVar ( 'pid', pid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			alert( 'product added to your cart' );
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}

/* --- Global Events -------------------------------------------------------- */

// Check Global Keys
function checkStoreKeys( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var keycode = e.which ? e.which : e.keyCode;
	switch ( keycode )
	{
		// Tab key
		case 9:
			if( targ.id == 'ProductAccess' )
			{
				SaveProduct();
			}
			break;
		// Esc key
		case 27:
			CloseProduct();
			break;
		// Enter key
		case 13:
			SaveProduct();
			break;
		// Delete key
		case 46:
			if( targ.tagName != 'INPUT' )
			{
				DeleteProduct();
			}
			break;
		default: break;
	}
}

// Check Global Cliks
function checkStoreClicks( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	if( ge( 'ProductEdit' ) && targ.tagName != 'SELECT' && targ.tagName != 'INPUT' && targ.tagName != 'OPTION' )
	{
		//CloseProduct();
	}
	else if( !targ.parentNode.id ) return;
}

// Global Events
if ( window.addEventListener )
{
	window.addEventListener ( 'keydown', checkStoreKeys );
	window.addEventListener ( 'mousedown', checkStoreClicks );
}
else 
{
	window.attachEvent ( 'onkeydown', checkStoreKeys );
	window.attachEvent ( 'onmousedown', checkStoreClicks );
}
