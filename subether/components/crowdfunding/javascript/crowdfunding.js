
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

function EditFundraiser( ele, pid, e )
{
	if( !ele ) return;
	
	CloseFundraiser();
	
	ele.data = ele.innerHTML;
	ele.js = ele.parentNode.getAttribute( 'onclick' );
	ele.pid = ( 'ProductInfo' + ( pid ? ( '_' + pid ) : '' ) );
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=crowdfunding&function=details&edit=true', 'post', true );
	if( pid )
	{
		//j.addVar ( 'pid', pid );
		j.addVar ( 'p', pid );
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
			
			initCKEditor( 'FundraiserDetails'+(pid?'_'+pid:'') );
		}
	}
	j.send ();
	
	cancelBubble(e);
}

function SaveFundraiser()
{
	if( !ge( 'ProductEdit' ) ) return;
	
	var pro = ge( 'ProductEdit' );
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=crowdfunding&action=save', 'post', true );
	
	if( pro.getAttribute( 'pid' ) )
	{
		j.addVar ( 'pid', pro.getAttribute( 'pid' ) );
	}
	if( ge( 'FundraiserName' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ) )
	{
		j.addVar ( 'name', ge( 'FundraiserName' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ).value );
	}
	if( ge( 'FundraiserInfo' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ) )
	{
		j.addVar ( 'info', ge( 'FundraiserInfo' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ).value );
	}
	if( ge( 'FundraiserDetails' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ) )
	{
		if( CKEDITOR && typeof CKEDITOR.instances['ContentEditorCK'] !== 'undefined' )
		{
			j.addVar ( 'details', CKEDITOR.instances['ContentEditorCK'].getData() );
		}
		else
		{
			j.addVar ( 'details', ge( 'FundraiserDetails' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ).value );
		}
	}
	if( ge( 'FundraiserLocation' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ) )
	{
		j.addVar ( 'location', ge( 'FundraiserLocation' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ).value );
	}
	if( ge( 'FundraiserTags' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ) )
	{
		j.addVar ( 'tags', ge( 'FundraiserTags' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ).value );
	}
	if( ge( 'FundraiserGoal' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ) )
	{
		j.addVar ( 'goal', ge( 'FundraiserGoal' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ).value );
	}
	if( ge( 'FundraiserCurrency' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ) )
	{
		j.addVar ( 'currency', ge( 'FundraiserCurrency' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ).value );
	}
	if( ge( 'FundraiserEnd' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ) )
	{
		j.addVar ( 'end', ge( 'FundraiserEnd' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ).value );
	}
	if( ge( 'FundraiserStatus' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ) )
	{
		j.addVar ( 'status', ge( 'FundraiserStatus' + ( pro.getAttribute( 'pid' ) ? ( '_' + pro.getAttribute( 'pid' ) ) : '' ) ).value );
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
			RefreshFundraiser();
		}
	}
	j.send ();
}

function CloseFundraiser()
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

function DeleteFundraiser()
{
	if( ge( 'ProductEdit' ) && ge( 'ProductEdit' ).getAttribute( 'pid' ) && confirm( 'Are you sure?' ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=crowdfunding&action=delete', 'post', true );
		j.addVar ( 'pid', ge( 'ProductEdit' ).getAttribute( 'pid' ) );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );	
			if ( r[0] == 'ok' )
			{
				RefreshFundraiser();
			}
		}
		j.send ();
	}
}

function RefreshFundraiser( pid )
{
	if( !ge( 'FundingContent' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=crowdfunding&function=crowdfunding', 'post', true );
	if( pid )
	{
		j.addVar ( 'pid', pid );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' )
		{
			if( r[1] )
			{
				ge( 'FundingContent' ).innerHTML = r[1];
			}
		}
	}
	j.send ();
}

function IncludeFundraiserImage( fid, pid, cid )
{
	if( !fid ) return;
	if( ge( 'ProductImages' + ( pid ? ( '_' + pid ) : '' ) ) )
	{
		var pi = ge( 'ProductImages' + ( pid ? ( '_' + pid ) : '' ) );
		
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=crowdfunding&function=media', 'post', true );
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
						SwitchFundraiserImage( fid, pid );
					}
					else
					{
						SwitchFundraiserImage( fid );
					}
				}
				else if( pid > 0 && cid > 0 && ge( 'ProductThumbImg_' + cid ) )
				{
					var tmb = ge( 'ProductThumbImg_' + cid );
					
					tmb.innerHTML = r[1];
					tmb.id = 'ProductThumbImg_' + fid;
					
					SwitchFundraiserImage( fid, pid );
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

function SwitchFundraiserImage( fid, pid )
{
	if( !fid ) return;
	
	if( ge( 'ProductMainImage' + ( pid ? ( '_' + pid ) : '' ) ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=crowdfunding&function=media&getmain=1', 'post', true );
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

function Donate( ele, pid )
{
	if( !pid || !ge( 'DonationCurrency' ) || !ge( 'DonationValue' ) ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=crowdfunding&action=donate', 'post', true );
	j.addVar ( 'pid', pid );
	j.addVar ( 'currency', ge( 'DonationCurrency' ).value );
	j.addVar ( 'donation', ge( 'DonationValue' ).value );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			alert( 'donated ' + r[1] );
			location.reload();
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}

function removeDonation( did )
{
	if( !did ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=crowdfunding&action=remove', 'post', true );
	j.addVar ( 'did', did );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			location.reload();
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}

function initCKEditor( id )
{
	console.log( id + ' --' );
	if( id && ge( id )/* && ge( id ).tagName == 'TEXTAREA' && CKEDITOR && typeof CKEDITOR.instances['ContentEditorCK'] !== 'undefined'*/ )
	{
		// Set content editor and save
		var d = document.createElement( 'div' );
		d.id = 'ContentEditorCK';
		d.innerHTML = ( ge( id ).value ? ge( id ).value : ge( id ).innerHTML );
		ge( id ).parentNode.appendChild( d );
		ge( id ).style.display = 'none';
		CKEDITOR.replace( 'ContentEditorCK' );
		CKEDITOR.instances['ContentEditorCK'].on( 'change', function()
		{
			//IsEditing( ge( id ).getAttribute( 'fileid' ) );
		
			// Makes sure we delay saving always 500ms after typing
			//if( window.contentEditorSaveTm )
			//{
			//	clearInterval( window.contentEditorSaveTm );
			//	window.contentEditorSaveTm = false;
			//}
			//window.contentEditorSaveTm = setTimeout( saveFileTextContent, 500 );
		});
	}
}

/* --- Global Events -------------------------------------------------------- */

// Check Global Keys
function checkFundraiserKeys( e )
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
				SaveFundraiser();
			}
			break;
		// Esc key
		case 27:
			CloseFundraiser();
			break;
		// Enter key
		case 13:
			SaveFundraiser();
			break;
		// Delete key
		case 46:
			if( targ.tagName != 'INPUT' )
			{
				DeleteFundraiser();
			}
			break;
		default: break;
	}
}

// Check Global Cliks
function checkFundraiserClicks( e )
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
	window.addEventListener ( 'keydown', checkFundraiserKeys );
	window.addEventListener ( 'mousedown', checkFundraiserClicks );
}
else 
{
	window.attachEvent ( 'onkeydown', checkFundraiserKeys );
	window.attachEvent ( 'onmousedown', checkFundraiserClicks );
}

