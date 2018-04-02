
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

function IncludeMedia( fid, fld, mid, type, path )
{
	//alert( fid + ' .. ' + fld + ' alert ja: mediaID: ' + mid + ' type: ' + type + ' path: ' + path );
	
	if( !ge( 'WallEditor' ) || !fid || !fld || !mid || !type ) return;
	if( ge( 'WallEditor' ).className.indexOf( 'open' ) >= 0 )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=wall&function=media', 'post', true );
		j.addVar ( 'fid', fid );
		j.addVar ( 'fld', fld );
		j.addVar ( 'mid', mid );
		j.addVar ( 'type', type );
		j.addVar ( 'path', path );
		if( ge( 'WallEditor' ) && ge( 'ParseContent' ) && ge( 'ParseContent' ).getElementsByTagName( 'div' ).length )
		{
			j.addVar ( 'multiple', true );
		}
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				// If we are gonna add more pictures to the post
				if( ge( 'WallEditor' ) && ge( 'ParseContent' ) && ge( 'ParseContent' ).getElementsByTagName( 'div' ).length )
				{
					var pc = ge( 'ParseContent' ).getElementsByTagName( 'div' )[0];
					
					pc.innerHTML = pc.innerHTML + r[1];
					
					if( ge( 'ParseClear' ) ) ge( 'ParseClear' ).parentNode.removeChild( ge( 'ParseClear' ) );
					var clear = document.createElement( 'div' );
					clear.id = 'ParseClear';
					clear.className = 'clearboth';
					ge( 'ParseContent' ).getElementsByTagName( 'div' )[0].appendChild( clear );
				}
				// If its the first media to add
				else
				{
					// Clean up
					if( ge( 'ParseContent' ) )
					{
						ge( 'ParseContent' ).parentNode.removeChild( ge( 'ParseContent' ) );
					}
					
					// Create new
					var div = document.createElement( 'div' );
					div.id = 'ParseContent';
					div.images = new Array();
					div.innerHTML = r[1];
					
					if ( ge( 'ShareBox' ) && ge( 'ShareBox' ).parentNode.className.indexOf( 'text' ) >= 0 )
					{
						ge( 'ShareBox' ).parentNode.appendChild( div );
					}
					else
					{
						ge( 'WallEditor' ).getElementsByTagName( 'div' )[0].appendChild( div );
					}
				}
				// If we have json object assign this to the ParseImg node
				if( r[2] && ge( 'ParseContent' ).images )
				{
					ge( 'ParseContent' ).images.push( JSON.parse( r[2] ) );
				}
			}
			else alert( this.getResponseText() );
		}
		j.send ();
	}
}

function removeImage( fid )
{
	if( !fid ) return;

	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=files&option=deletefile', 'post', true );
	j.addVar ( 'deletefile', fid );
	j.addVar ( 'filetype', 'image' );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			if( ge( 'ParseImg_' + fid ) )
			{
				// Remove image object from images array
				if( ge( 'ParseContent' ).images.length > 0 )
				{
					var images = ge( 'ParseContent' ).images;
					
					for( a = 0; a < images.length; a++ )
					{
						if( images[a].FileID == fid )
						{
							images.splice(a,1);
						}
					}
				}
				// Remove image html
				ge( 'ParseImg_' + fid ).parentNode.removeChild( ge( 'ParseImg_' + fid ) );
			}
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}

function priorityList( type )
{
	var pri = [
		'wide1000', 'wide900', 'wide800', 'wide700', 'wide600', 'wide500',
		'wide400', 'wide300', 'wide200', 'wide100', 'wide',
		'long1000', 'long900', 'long800', 'long700', 'long600', 'long500',
		'long400', 'long300', 'long200', 'long100', 'long',
		'match'
	];
	
	return pri;
}
