
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

function IsEdit( editing, ele, mid )
{	
	if( !ge( 'ShareContent' ) ) return;
	
	if( ele && ele.innerHTML.length < 1 )
	{
		ge( 'ShareContent' ).className = '';
		return;
	}
	else if( !ge( 'ShareContent' ).className != 'editing' && editing )
	{
		ge( 'ShareContent' ).className = 'editing';
		if( mid > 0 && ge( 'MessageID_' + mid ) )
		{
			ge( 'MessageID_' + mid ).className = ge( 'MessageID_' + mid ).className.split(' focus').join('') + ' focus';
		}
		return;
	}
	else if( ge( 'ShareContent' ).className == 'editing' && !editing )
	{
		ge( 'ShareContent' ).className = '';
		if( mid > 0 && ge( 'MessageID_' + mid ) )
		{
			ge( 'MessageID_' + mid ).className = ge( 'MessageID_' + mid ).className.split(' focus').join('');
		}
		return;
	}
	return;
}

function IsFocus( mid )
{
	if( mid > 0 && ge( 'MessageID_' + mid ) )
	{
		ge( 'MessageID_' + mid ).className = ge( 'MessageID_' + mid ).className.split(' focus').join('') + ' focus';
	}
}

function SwitchWallMode( mid )
{
	if ( ge( 'WallModeClosed' ) )
	{
		var remove, current;
		
		var updated = new Array();
		
		var closed = ge( 'WallModeClosed' );
		
		if ( closed.value )
		{
			current = closed.value.split( ',' );
		}
		
		if ( mid > 0 && ge( 'MessageID_' + mid ) && ge( 'MessageID_' + mid ).className.indexOf( 'closed' ) >= 0 )
		{
			ge( 'MessageID_' + mid ).className = ge( 'MessageID_' + mid ).className.split(' closed').join('') + '';
			
			remove = mid;
		}
		else
		{
			ge( 'MessageID_' + mid ).className = ge( 'MessageID_' + mid ).className.split(' closed').join('') + ' closed';
			
			updated.push( mid );
		}
		
		if ( current && current.length > 0 )
		{
			for ( a = 0; a < current.length; a++ )
			{
				if ( remove == current[a] )
				{
					continue;
				}
				
				updated.push( current[a] );
			}
		}
		
		if ( closed && updated )
		{
			closed.value = updated.join( ',' );
		}
	}
}

function Bookmark( ele, pid )
{
	if( !ele || !pid ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=wall&action=bookmark', 'post', true );
	j.addVar ( 'pid', pid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			//loadShareMessages ( mid );
			loadShareMessages ( false, 1 );
		}
	}
	j.send ();
}

function InitWallEditor( ele )
{
	if( !ele ) return;
	CloseWallEditor();
	var parent = ele.parentNode.parentNode;
	
	//initCKEditor( 'ShareBox' );
	
	if( parent.className.indexOf( 'closed' ) >= 0 )
	{
		parent.className = parent.className.split( 'closed' ).join( 'open' );
		parent.id = 'WallEditor';
		ele.spellcheck = false;
		if( navigator.userAgent.toLowerCase().indexOf( 'webkit' ) >= 0 )
		{
			ele.onkeydown = function( e )
			{
				var wh = e.which ? e.which : e.keyCode;
				if( wh == 13 )
				{
					document.execCommand( 'insertHTML', false, '<br><br>' );
					if( e.stopPropagation )
						e.stopPropagation( e );
					if( e.preventDefault )
						e.preventDefault( e );
					e.cancelBubble = true;
					return;
				}
			}
		}
	}
}

function CloseWallEditor()
{
	if( !ge( 'WallEditor' ) ) return;
	
	if( ge( 'WallEditor' ).className.indexOf( 'open' ) >= 0 )
	{
		ge( 'WallEditor' ).className = ge( 'WallEditor' ).className.split( 'open' ).join( 'closed' );
		ge( 'WallEditor' ).id = '';
	}
}

function AddArticle()
{
	if( !ge( 'WallEditor' ) ) return;
	if ( ge( 'ArticleHeading' ).innerHTML.length > 0 )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=wall&action=savearticle', 'post', true );
		j.addVar ( 'Heading', ge( 'ArticleHeading' ).innerHTML );
		//j.addVar ( 'Leadin', ge( 'ArticleLeadin' ).innerHTML );
		//j.addVar ( 'Article', ge( 'ArticleContent' ).innerHTML );
		j.addVar ( 'Leadin', ge( 'ArticleLeadin' ).value );
		j.addVar ( 'Article', ge( 'ArticleContent' ).value );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				loadShareMessages ();
				ge( 'ArticleHeading' ).innerHTML = '';
				//ge( 'ArticleLeadin' ).innerHTML = '';
				//ge( 'ArticleContent' ).innerHTML = '';
				ge( 'ArticleLeadin' ).value = '';
				ge( 'ArticleContent' ).value = '';
				CloseWallEditor();
			}
			else if( this.getResponseText() != 'fail' )
			{
				alert ( this.getResponseText () );
			}
		}
		j.send ();
	}
}

function stripTags( ele )
{
	if( ge( 'ParseContent' ) || !ele || !ele.innerHTML ) return;
	var str = ele.textContent || ele.innerText || "";
	ele.innerHTML = str;
}
/*
function parseText( ele )
{
	if( !ge( 'WallEditor' ) || ge( 'ParseContent' ) || !ele || !ele.innerHTML ) return;
	if( ge( 'WallEditor' ).className.indexOf( 'open' ) >= 0 )
	{
		if( ge( 'AjaxLoader' ) )
		{
			ge( 'AjaxLoader' ).className = 'loading';
		}
		
		//stripTags( ele );
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=wall&function=parse', 'post', true );
		j.addVar ( 'Message', ele.innerHTML.trim() );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				var div = document.createElement( 'div' );
				div.id = 'ParseContent';
				div.innerHTML = r[1].split( '<!--replaceimage-->' ).join( '<div id="ParseImageBox"></div>' );
				
				ge( 'WallEditor' ).getElementsByTagName( 'div' )[0].appendChild( div );
				
				// if we have json object do the new thing :)
				if( r[2] && ge( 'ParseImageBox' ) )
				{
					var pb = ge( 'ParseImageBox' );
					pb.obj = JSON.parse( r[2] );
					
					// If type is video ------------------------------------------------------
					if( pb.obj.Type == 'video' && pb.obj.Images )
					{
						pb.className = 'image';
						pb.setAttribute( 'link', pb.obj.Url );
						pb.setAttribute( 'replace', '' );
						// Image
						pb.img = document.createElement( 'img' );
						pb.img.style.backgroundImage = 'url(' + pb.obj.Images[0]['src'] + ')';
						pb.img.style.maxWidth = pb.obj.Limit['width'] + 'px';
						pb.img.style.maxHeight = pb.obj.Limit['Height'] + 'px';
						pb.img.setAttribute( 'ImageID', '0' );
						// Play
						pb.ply = document.createElement( 'i' );
						// Assign Image to ParseImageBox
						pb.appendChild( pb.img );
						// Assign Play icon to ParseImageBox
						pb.appendChild( pb.ply );
					}
					// If type is site -------------------------------------------------------
					else if( pb.obj.Images )
					{
						var cls = ' small';
						if( pb.obj.Limit['width'] <= pb.obj.Images[0]['width'] )
						{
							cls = ' big';
						}
						
						// Set className for ParseImageBox
						pb.className = 'image ' + pb.obj.Type + cls;
						// RemoveImageParse
						pb.div = document.createElement( 'div' );
						pb.div.id = 'RemoveImageParse';
						pb.div.className = 'Edit';
						pb.div.setAttribute( 'onclick', 'removeImageParse()' );
						pb.div.innerHTML = '<div></div>';
						// Image
						pb.img = document.createElement( 'img' );
						pb.img.style.backgroundImage = 'url(' + pb.obj.Images[0]['src'] + ')';
						pb.img.style.maxWidth = pb.obj.Limit['width'] + 'px';
						pb.img.style.maxHeight = pb.obj.Limit['Height'] + 'px';
						pb.img.setAttribute( 'ImageID', '0' );
						// ParseImages
						pb.pis = document.createElement( 'div' );
						pb.pis.id = 'ParseArrows';
						pb.pis.className = 'arrows';
						pb.pis.innerHTML =  '<button class="PrevArrow" onclick="SwitchParseImage(0)"> << </button>' +
											'<button class="NextArrow" onclick="SwitchParseImage(2)"> >> </button>';
						// Assign Image to ParseImageBox
						pb.appendChild( pb.img );
						// Assign RemoveImageParse to ParseImageBox
						pb.appendChild( pb.div );
						// Assign ParseImages to ParseImageBox
						pb.appendChild( pb.pis );
					}
				}
			}
			else if( r[0] != '' ) alert( this.getResponseText() );
			
			if( ge( 'AjaxLoader' ) )
			{
				ge( 'AjaxLoader' ).className = false;
			}
		}
		j.send ();
	}
}

function SwitchParseImage( num )
{
	var pb = ge( 'ParseImageBox' );
	
	if( !pb || !num || num == 0 || !pb.obj || num > pb.obj.Images.length )
	{
		return;
	}
	
	var cls = ' small';
	if( pb.obj.Limit['width'] <= pb.obj.Images[(num-1)]['width'] )
	{
		cls = ' big';
	}

	// Set className for ParseImageBox
	pb.className = 'image ' + pb.obj.Type + cls;
	// Image
	pb.img.style.backgroundImage = 'url(' + pb.obj.Images[(num-1)]['src'] + ')';
	pb.img.setAttribute( 'ImageID', (num-1) );
	pb.obj.ImageID = (num-1);
	// ParseImages
	pb.pis.innerHTML =  '<button class="PrevArrow" onclick="SwitchParseImage(' + ( num - 1 ) + ')"> << </button>' +
						'<button class="NextArrow" onclick="SwitchParseImage(' + ( num + 1 ) + ')"> >> </button>';
}*/

function parseText( ele )
{
	InitWallEditor( ele );
	
	if( !ge( 'WallEditor' ) || ge( 'ParseContent' ) || !ele ) return;
	
	var walle = ge( 'WallEditor' );
	
	var value = ( ele.value ? ele.value : ele.innerHTML.trim() );
	
	if( ge( 'WallEditor' ).className.indexOf( 'open' ) >= 0 && value )
	{
		if( ge( 'AjaxLoader' ) )
		{
			ge( 'AjaxLoader' ).className = 'loading';
		}
		
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=wall&function=parse', 'post', true );
		j.addVar ( 'Message', value );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] && walle )
			{
				var div = document.createElement( 'div' );
				div.id = 'ParseContent';
				
				if ( ele.parentNode.className.indexOf( 'text' ) >= 0 )
				{
					ele.parentNode.appendChild( div );
				}
				else
				{
					walle.getElementsByTagName( 'div' )[0].appendChild( div );
				}
				
				var pb;
				var pc = ge( 'ParseContent' );
				pc.obj = JSON.parse( r[1] );
				
				// If type is video ------------------------------------------------------
				if( pc.obj.Type == 'video' && pc.obj.Images && pc.obj.Images[0] )
				{
					pb = '<div id="ParseImageBox" class="image small" link="' + pc.obj.Url + '">' +
					'<img ImageID="0" style="background-image:url(\'' + pc.obj.Images[0]['src'] + '\');width:' +
					pc.obj.Images[0]['width'] + 'px;height:' + pc.obj.Images[0]['height'] + 'px;max-width:100%;max-height:100%;"/>' + 
					'<i></i>' + 
					'</div>';
				}
				// If type is video ------------------------------------------------------
				else if( pc.obj.Type == 'video' && !pc.obj.Images )
				{
					pb = '<div id="ParseImageBox" class="image small" link="' + pc.obj.Url + '">' +
					'<img ImageID="0" style="background-image:url(\'admin/gfx/icons/page_white.png\');max-width:100%;max-height:100%;">' +
					'<i></i>' + 
					'</div>';
				}
				// If type is audio ------------------------------------------------------
				else if( pc.obj.Type == 'audio' && pc.obj.Images && pc.obj.Images[0] )
				{
					pb = '<div id="ParseImageBox" class="image small" link="' + pc.obj.Url + '">' +
					'<img ImageID="0" style="background-image:url(\'' + pc.obj.Images[0]['src'] + '\');width:' +
					pc.obj.Images[0]['width'] + 'px;height:' + pc.obj.Images[0]['height'] + 'px;max-width:100%;max-height:100%;"/>' + 
					'<i></i>' + 
					'</div>';
				}
				// If type is audio ------------------------------------------------------
				else if( pc.obj.Type == 'audio' && !pc.obj.Images )
				{
					pb = '<div id="ParseImageBox" class="image small" link="' + pc.obj.Url + '">' +
					'<img ImageID="0" style="background-image:url(\'admin/gfx/icons/page_white.png\');max-width:100%;max-height:100%;">' +
					'<i></i>' + 
					'</div>';
				}
				// If type is file ------------------------------------------------------
				else if( pc.obj.Type == 'file' )
				{
					pb = '<div id="ParseImageBox" class="image small" link="' + pc.obj.Url + '">' +
					'<img ImageID="0" style="background-image:url(\'admin/gfx/icons/page_white.png\');max-width:100%;max-height:100%;">' +
					'</div>';
				}
				// If type is site -------------------------------------------------------
				else if( pc.obj.Images && pc.obj.Images[0] )
				{
					var cls = ' small';
					if( pc.obj.Limit['width'] <= pc.obj.Images[0]['width'] )
					{
						cls = ' big';
					}
					
					pb = '<div id="ParseImageBox" class="image ' + pc.obj.Type + ' ' + cls + '">' +
					'<img ImageID="0" style="background-image:url(\'' + pc.obj.Images[0]['src'] + '\');width:' +
					pc.obj.Images[0]['width'] + 'px;height:' + pc.obj.Images[0]['height'] + 'px;max-width:100%;max-height:100%;"/>' +
					'<div class="Edit" onclick="removeImageParse()"><div></div></div>' + 
					'<div id="ParseArrows" class="arrows">' + 
					'<button class="PrevArrow" onclick="SwitchParseImage(0)"> << </button>' +
					'<button class="NextArrow" onclick="SwitchParseImage(2)"> >> </button>' + 
					'</div></div>';
				}
				
				if( pc.obj.Type == 'library' && pc.obj.Url )
				{
					pc.innerHTML = '<div class="ParseContent">' + embedLibrary( pc.obj.Url, '100%', '640' ) +
					'<div class="Edit" onclick="removeParse()"><div></div></div></div>' +
					'<div class="clearboth" style="clear:both"></div></div>';
				}
				else
				{
					// Output ----------------------------------------------------------------
					pc.innerHTML = '<div class="ParseContent">' + ( pb ? pb : '' ) + '<div class="text">' + 
					'<h3><a target="_blank" href="' + pc.obj.Url + '">' + ( pc.obj.Title ? pc.obj.Title : '' ) + '</a></h3>' + 
					'<p><a target="_blank" href="' + pc.obj.Url + '">' + ( pc.obj.Leadin ? pc.obj.Leadin : ( pc.obj.Description ? pc.obj.Description : '' ) ) + '</a></p>' + 
					'<p class="url"><a target="_blank" href="' + pc.obj.Url + '">' + pc.obj.Domain + '</a></p>' + 
					'<div class="Edit" onclick="removeParse()"><div></div></div></div>' +
					'<div class="clearboth" style="clear:both"></div></div>';
				}
			}
			else if( r[0] != '' ) 
			{
				if( r[0] != 'fail' )
				{
					alert( this.getResponseText() );
				}
			}
			
			if( ge( 'AjaxLoader' ) )
			{
				ge( 'AjaxLoader' ).className = false;
			}
		}
		j.send ();
	}
}

function SwitchParseImage( num )
{
	var pc = ge( 'ParseContent' );
	var pb = ge( 'ParseImageBox' );
	
	if( !pc || !pb || !num || num == 0 || !pc.obj || num > pc.obj.Images.length )
	{
		return;
	}
	
	var cls = ' small';
	if( pc.obj.Limit['width'] <= pc.obj.Images[(num-1)]['width'] )
	{
		cls = ' big';
	}
	
	pc.obj.ImageID = (num-1);
	
	pb.className = 'image ' + pc.obj.Type + cls;
	pb.innerHTML = '<img ImageID="' + (num-1) + '" style="background-image:url(\'' + pc.obj.Images[(num-1)]['src'] + '\');width:' +
	pc.obj.Images[(num-1)]['width'] + 'px;height:' + pc.obj.Images[(num-1)]['height'] + 'px;max-width:100%;max-height:100%;"/>' +
	'<div class="Edit" onclick="removeImageParse()"><div></div></div>' + 
	'<div id="ParseArrows" class="arrows">' +
	'<button class="PrevArrow" onclick="SwitchParseImage(' + ( num - 1 ) + ')"> << </button>' +
	'<button class="NextArrow" onclick="SwitchParseImage(' + ( num + 1 ) + ')"> >> </button>' + 
	'</div>';
}

function embedLibrary( url, width, height )
{
	return '<iframe src="' + url + '?rendermodule=main,profile&rendercomponent=groups,library&displaymode=1&save=0" width="' + width + '" height="' + height + '" frameborder="0"></iframe>';
}

function embedVideo( ele, width, height, media )
{
	if( !ele || !ele.getAttribute( 'link' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=wall&function=parse', 'post', true );
	j.addVar ( 'Video', ele.getAttribute( 'Link' ) );
	if( media ) j.addVar ( 'Media', media );
	if( width ) j.addVar ( 'Width', width );
	if( height ) j.addVar ( 'Height', height );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ele.removeAttribute( 'onclick' );
			ele.className = ele.className + ' open';
			ele.parentNode.className = ele.parentNode.className + ' open';
			ele.innerHTML = r[1];
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}

function embedAudio( ele, width, height, media )
{
	if( !ele || !ele.getAttribute( 'link' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=wall&function=parse', 'post', true );
	j.addVar ( 'Audio', ele.getAttribute( 'Link' ) );
	if( media ) j.addVar ( 'Media', media );
	if( width ) j.addVar ( 'Width', width );
	if( height ) j.addVar ( 'Height', height );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ele.removeAttribute( 'onclick' );
			ele.className = ele.className + ' open';
			ele.parentNode.className = ele.parentNode.className + ' open';
			ele.innerHTML = r[1];
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}

function embedFile( ele, width, height, media )
{
	if( !ele || !ele.getAttribute( 'link' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=wall&function=parse', 'post', true );
	j.addVar ( 'File', ele.getAttribute( 'Link' ) );
	if( media ) j.addVar ( 'Media', media );
	if( width ) j.addVar ( 'Width', width );
	if( height ) j.addVar ( 'Height', height );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ele.removeAttribute( 'onclick' );
			ele.className = ele.className + ' open';
			ele.parentNode.className = ele.parentNode.className + ' open';
			ele.innerHTML = r[1];
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}

function removeParse()
{
	if( ge( 'ParseContent' ) )
	{
		ge( 'ParseContent' ).parentNode.removeChild( ge( 'ParseContent' ) );
	}
}

function removeImageParse()
{
	if( ge( 'ParseImageBox' ) )
	{
		ge( 'ParseImageBox' ).parentNode.removeChild( ge( 'ParseImageBox' ) );
	}
}

function postShare()
{
	var pc;
	if( !ge( 'share' ) || !ge( 'PopupPost' ) ) return;
	var div = ge( 'share' ).getElementsByTagName( 'div' );
	if( div.length )
	{
		for( a = 0; a < div.length; a++ )
		{
			if( div[a].className == 'html' )
			{
				pc = div[a].innerHTML;
			}
		}
	}
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=wall&action=savesharemessage', 'post', true );
	j.addVar ( 'Message', ( ge( 'PopupPost' ).value ? ge( 'PopupPost' ).value : ge( 'PopupPost' ).innerHTML ) );
	if( pc ) j.addVar ( 'ParseContent', pc );
	j.addVar ( 'Type', 'Users' );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			loadShareMessages ( false, 1 );
			closeWindow();
		}
		else if( r[0] != 'fail' )
		{
			alert ( this.getResponseText () );
		}
	}
	j.send ();
}

function addShareContent ( o, e, type, trid )
{
	if( !e ) e = window.event;
	var tr = e.target ? e.target : e.srcElement;
	var sid;
	var pc;
	var pb;
	var tg;
	if( strstr( o.id, 'ShareBox_' ) )
	{
		sid = str_replace( 'ShareBox_', '', o.id );
	}
	if( ge( 'ParseContent' ) )
	{
		pc = ge( 'ParseContent' ).getElementsByTagName( 'div' )[0];
		pb = ge( 'ParseContent' );
	}
	if( ge( 'PostTarget' ) && ge( 'PostTarget' ).value )
	{
		tg = ge( 'PostTarget' ).value;
	}
	
	var value = ( o.value ? o.value : o.innerHTML );
	
	if ( ge( 'PendingPosts' ) )
	{
		// TODO: make this somewhat better ...
		
		var stored = ge( 'PendingPosts' ).innerHTML;
		var storing = '<div class="messagebox"><div class="post"><div class="posted"></div><div class="content">' + value + '</div><div class="buttons"></div></div></div>';
		
		ge( 'PendingPosts' ).innerHTML = storing + stored;
	}
	
	var j = new bajax ();
	//j.openUrl ( getPath() + '?component=wall&action=savesharemessage', 'post', true );
	j.openUrl( getPath() + '?component=wall&action=savepost' + ( getPathVar( 'categoryid' ) ? ( '&categoryid=' + getPathVar( 'categoryid' ) ) : '' ), 'post', true );
	j.addVar ( 'Type', ( type ? type : 'post' ) );
	j.addVar ( 'Message', value );
	j.addVar ( 'Access', ge( 'PostAccess' ).value );
	if( tg )
	{
		j.addVar ( 'CategoryID', tg );
	}
	if( trid )
	{
		j.addVar ( 'ThreadID', trid );
	}
	if( pb && pb.obj )
	{
		j.addVar ( 'Data', JSON.stringify( pb.obj ) );
	}
	if( pc )
	{
		if( ge( 'ParseContent' ).images )
		{
			var images = ge( 'ParseContent' ).images;
			
			j.addVar ( 'Data', JSON.stringify( images ) );
		}
	}
	if( sid )
	{
		j.addVar ( 'sid', sid );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			loadShareMessages ( false, 1 );
			CloseWallEditor();
			CloseEditMode();
			if( o.value )
			{
				o.value = '';
			}
			else
			{
				o.innerHTML = '';
			}
			if( ge( 'ParseContent' ) )
			{
				ge( 'ParseContent' ).parentNode.removeChild( ge( 'ParseContent' ) );
			}
		}
		else if( r[0] != 'fail' )
		{
			alert ( this.getResponseText () );
		}
		tr.removeAttribute( 'disabled' );
	}
	j.send ();
	if( tr && tr.nodeName == 'BUTTON' )
	{
		tr.setAttribute( 'disabled', 'disabled' );
	}
}

function sharePost ( mid )
{
	if ( mid )
	{
		openWindow( 'Wall', mid, 'share' );
		alert( 'soon :)' );
	}
}

function replyToPost ( mid )
{
	if( !mid ) return;
	
	if( ge( 'ReplyContent_' + mid ) )
	{
		ge( 'ReplyContent_' + mid ).parentNode.parentNode.className = 'replybox';
		ge( 'ReplyContent_' + mid ).focus();
		return;
	}
}

function replyToMessage ( mid, img )
{
	if( ge( 'ReplyContent_' + mid ) )
	{
		ge( 'ReplyContent_' + mid ).focus();
		return;
	}
	ge ( 'mBox_' + mid ).className = 'ReplyBox';
	ge ( 'mBox_' + mid ).innerHTML = '' +
	'<table><tr><td style="width:37px"><div class="image">' + ( img ? '<img src="' + img + '"/>' : '' ) + '</div></td><td><div class="reply">' +
	//'<textarea id="ReplyContent_' + mid + '" onkeyup="IsEdit(1,this)" class="textarea post" placeholder="Write a comment..."></textarea>' +
	'<div id="ReplyContent_' + mid + '" onkeyup="IsEdit(1,this)" class="textarea post" contenteditable="true" placeholder="Write a comment..."></div>' +
	//'<input id="ReplyContent_' + mid + '" placeholder="Write a comment...">' +
	'<button type="button" onclick="sendReply(\'' + mid + '\', ge( \'ReplyContent_' + mid + '\' ), false, event )">REPLY</button>' +
	'</div></td></tr></table>';
}

// TODO: Add support for refreshing only one post right now MessageID doesn't work
function loadShareMessages ( mid, refresh )
{
	var sc;
	
	// Temporary hack to reload site for bookmarks
	if ( ge( 'Bookmarks' ) )
	{
		location.reload();
		return;
	}
	
	var func = ( ge( 'Bookmarks' ) ? 'bookmarks&bypass=true' : 'sharedposts' );
	
	var closed = ge( 'WallModeClosed' );
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=wall&function=' + func + ( getPathVar( 'categoryid' ) ? ( '&categoryid=' + getPathVar( 'categoryid' ) ) : '' ) + ( getPathVar( 'event' ) ? ( '&event=' + getPathVar( 'event' ) ) : '' ), 'post', true );
	
	if( mid && ge( 'MessageID_' + mid ) && ge( 'MessageID_' + mid ).className.indexOf( 'messagebox' ) >= 0 )
	{
		sc = ge( 'MessageID_' + mid );
		j.addVar ( 'mid', mid );
	}
	else if( mid && ge( 'MessagePost_' + mid ) )
	{
		sc = ge( 'MessagePost_' + mid );
		j.addVar ( 'mid', mid );
	}
	else
	{
		sc = ge( 'ShareContent' );
	}
	
	if ( closed && closed.value )
	{
		j.addVar ( 'closed', closed.value );
	}
	
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		
		if ( ge( 'ShareContent' ) && r[0] == 'ok' && r[1] )
		{
			if( !refresh )
			{
				var iframe = ge( 'ShareContent' ).getElementsByTagName( 'iframe' );
				
				if ( iframe.length > 0  )
				{
					for ( a = 0; a < iframe.length; a++ )
					{
						if ( iframe[a].className.indexOf( 'active' ) >= 0 )
						{
							return;
						}
					}
				}
				
				if( ge( 'ShareContent' ).className == 'editing' ) return;
			}
			
			sc.innerHTML = r[1];
			
			
			
			// Check if the file has scripts
			var scripts = '';
			var rt = r[1];
			var wholescript = [];
			while( scripts = rt.match( /\<script[^>]*?\>([\w\W]*?)\<\/script[^>]*?\>/i ) )
			{
				wholescript.push( scripts[1] );
				rt = rt.split( scripts[0] ).join ( '' );
			}
			// Run script
			if( wholescript.length )
			{
				eval( wholescript.join ( '' ) );
			}
		}
	}
	j.send ();
}

// Update a message
function updateMessage( sid, o, threadid, type )
{
	if( !sid || !o ) return;
	
	var value = ( o.value ? o.value : o.innerHTML );
	
	if( value )
	{
		var j = new bajax();
		//j.openUrl( getPath() + '?component=wall&action=savesharemessage', 'post', true );
		j.openUrl( getPath() + '?component=wall&action=savepost', 'post', true );
		j.addVar( 'Type', ( type ? type : 'post' ) );
		j.addVar( 'Message', value );
		j.addVar( 'ThreadID', threadid );
		j.addVar( 'sid', sid );
		j.onload = function()
		{
			var r = this.getResponseText().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				loadShareMessages ( false, 1 );
				value = '';
			}
		}
		j.send();
	}
}

// Send a reply
function sendReply( mid, o, threadid, e )
{
	if( !e ) e = window.event;
	var tr = e.srcElement ? e.srcElement : e.target;
	if( tr.nodeName != 'BUTTON' )
	{
		tr = false;
	}
	if ( !threadid ) threadid = mid;
	//if ( o.value.length > 0 )
	
	var value = ( o.value ? o.value : o.innerHTML );
	
	if ( value )
	{
		// Add the reply to the document before it has been confirmed by server
		
		if ( ge( 'EditorBox_' + mid ) && ge( 'EditorReply_' + mid ) )
		{
			var i = 1;
			
			var replybox = ge( 'EditorBox_' + mid ).innerHTML;
			var pendingbox = ge( 'EditorReply_' + mid );
			
			if ( replybox && pendingbox )
			{
				var pending = pendingbox.getElementsByTagName( 'div' );
				
				if ( pending.length > 0 )
				{
					for ( var a = 0; a < pending.length; a++ )
					{
						if ( pending.className.indexOf( 'comment' ) >= 0 )
						{
							i++;
						}
					}
				}
				
				var div = document.createElement( 'div' );
				div.id = 'EditorReply_' + mid + '_' + i;
				div.className = 'comment';
				div.innerHTML = replybox;
				
				pendingbox.appendChild( div );
				
				if ( ge( div.id ) )
				{
					var elem = ge( div.id ).getElementsByTagName( 'div' );
					
					if ( elem.length > 0 )
					{
						for ( var b = 0; b < elem.length; b++ )
						{
							if ( elem[b].className.indexOf( 'content' ) >= 0 )
							{
								elem[b].innerHTML = value;
							}
						}
					}
				}
			}
		}
		
		var j = new bajax ();
		//j.openUrl ( getPath() + '?component=wall&action=savesharemessage', 'post', true );
		j.openUrl( getPath() + '?component=wall&action=savepost', 'post', true );
		//j.addVar ( 'Message', o.value );
		j.addVar ( 'Type', 'comment' );
		j.addVar ( 'Message', value );
		j.addVar ( 'ParentID', mid );
		j.addVar ( 'ThreadID', threadid );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				IsEdit();
				loadShareMessages ( false, 1 );
				//o.value = '';
				value = '';
			}
			else alert ( this.getResponseText () );
			//if( tr ) tr.removeAttribute( 'disabled' );
		}
		j.send ();
	}
	if( tr ) tr.setAttribute( 'disabled', 'disabled' );
}

var LastWallUpdate;

function checkWallUpdates ( mid )
{
	var q = new jaxqueue ( 'checkWallUpdates' );
	q.addUrl ( '?component=wall&function=wallupdates', ( mid ? true : false ) );
	if( LastWallUpdate )
	{
		q.addVar ( 'LastWallUpdate', LastWallUpdate );
	}
	q.onload ( function ( data )
	{
		var r = data.split ( '<!--separate-->' );
		
		if ( r[0] == 'ok' && r[1] )
		{
			if( ge( 'ShareContent' ) )
			{	
				var iframe = ge( 'ShareContent' ).getElementsByTagName( 'iframe' );
				
				if ( iframe.length > 0  )
				{
					for ( a = 0; a < iframe.length; a++ )
					{
						if ( iframe[a].className.indexOf( 'active' ) >= 0 )
						{
							return;
						}
					}
				}
				
				if( ge( 'ShareContent' ).className == 'editing' ) return;
			}
			
			//if( LastWallUpdate ) loadShareMessages( mid );
			if( LastWallUpdate ) loadShareMessages();
			LastWallUpdate = r[1];
		}
	} );
	q.save();
}

checkWallUpdates();

function voteComment ( mid, vote, ele, pid )
{
	if( !mid || !vote ) return;
	
	if( ele )
	{
		var mark = ele.parentNode.getElementsByTagName( '*' );
		
		if( mark.length > 0 )
		{
			for( a = 0; a < mark.length; a++ )
			{
				if( mark[a].className.indexOf( 'marked' ) >= 0 )
				{
					mark[a].className = mark[a].className.split( ' marked' ).join( '' );
				}
				else if( mark[a] == ele )
				{
					mark[a].className = mark[a].className.split( ' marked' ).join( '' ) + ' marked';
				}
			}
		}
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=wall&action=vote', 'post', true );
	j.addVar ( vote, mid );
	j.addVar ( 'mid', mid );
	j.addVar ( 'vote', vote );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			loadShareMessages ( pid ? pid : mid );
			//loadShareMessages ();
		}
	}
	j.send ();
}

function UpdateAccess ( mid, value )
{
	if( !mid ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=wall&action=access', 'post', true );
	j.addVar ( 'mid', mid );
	j.addVar ( 'value', value );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			//
		}
	}
	j.send ();
}

function deleteWallContent( id )
{
	if( !id ) return;
	
	if( confirm( 'Are you sure?' ) )
	{
		if ( ge( 'MessageID_' + id ) )
		{
			ge( 'MessageID_' + id ).parentNode.removeChild( ge( 'MessageID_' + id ) );
		}
		else if ( ge( 'MessagePost_' + id ) )
		{
			ge( 'MessagePost_' + id ).parentNode.removeChild( ge( 'MessagePost_' + id ) );
		}
		
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=wall&action=delete', 'post', true );
		j.addVar ( 'delete', id );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				//loadShareMessages();
				loadShareMessages ( false, 1 );
			}
		}
		j.send ();
	}
}

function InitWallTabs( id )
{
	if( !id || !ge( id ) ) return;
	var ele = ge( id ).getElementsByTagName( '*' );
	if( ele.length > 0 )
	{
		for( a = 0; a < ele.length; a++ )
		{
			if( ele[ a ].className == 'tabs' ) continue;
			if( ele[ a ].className.indexOf( 'tab' ) == 0 )
			{

				ele[a].onclick = function ( )
				{
					ActivateWallTab( id, this );
				}
			}
		}
	}
}

function ActivateWallTab( id, tab )
{
	if( !id || !ge( id ) || !tab ) return;
	var ele = ge( id ).getElementsByTagName( '*' );
	if( ele.length > 0 )
	{
		var c;
		var p = 1;
		var t = 1;
		for( a = 0; a < ele.length; a++ )
		{
			if( ele[ a ].className == 'tabs' ) continue;
			if( ele[ a ].className.indexOf( 'tab' ) == 0 )
			{
				if( ele[ a ] == tab )
				{
					ele[ a ].className = 'tab current';
					c = t;
				}
				else ele[ a ].className = 'tab';
				t++;
			}
			if( ele[ a ].className.indexOf( 'page' ) == 0 )
			{
				if( c && c == p )
				{
					ele[ a ].className = 'page active';
				}
				else ele[ a ].className = 'page';
				p++;
			}
		}
	}
}

// Edit a message (mid = parent/thread id, cid = comment id)
function embedWallEditor( mid, cid )
{
	if( !mid ) return;
	
	var mcbox = cid ? ge( 'ReplyContent_' + mid + '_' + cid ) : ge( 'MessageContent_' + mid );
	
	if( !mcbox ) return;
	
	CloseEditMode();
	
	mcbox.cont = ( mcbox.value ? mcbox.value : mcbox.innerHTML );
	
	if( cid )
	{
		var cont;
		var span = mcbox.getElementsByTagName( 'span' );
		if( span.length > 0 )
		{
			for( a = 0; a < span.length; a++ )
			{
				if( span[a].className == 'replycontent' )
				{
					cont = span[a].innerHTML;
				}
			}
		}
		
		cont = cont.split( /[\n|\r]/i ).join ( '' );
		
		mcbox.innerHTML = '' + 
		'<div id="EditMode" class="editor post">' + 
		'<div class="text">' + 
		'<div contenteditable="true" class="textarea post" id="CommentID_' + cid + '">' +
		cont + 
		'</div></div>' + 
		'<div class="toolbar">' + 
		'<div class="publish">' + 
		'<button onclick="updateMessage(\'' + cid + '\', ge(\'CommentID_' + cid + '\'), \'' + mid + '\' )" type="button">SAVE</button>' +
		'<button onclick="CloseEditMode()" type="button">CANCEL</button>' + 
		'</div></div></div>';
	}
	else
	{
		mcbox.innerHTML = '' + 
		'<div id="EditMode" class="editor post">' + 
		'<div class="text">' + 
		'<div contenteditable="true" class="textarea post" id="ShareBox_' + mid + '">' +
		mcbox.cont + 
		'</div></div>' + 
		'<div class="toolbar">' + 
		'<div class="publish">' + 
		'<button onclick="addShareContent(ge(\'ShareBox_' + mid + '\'))" type="button">SAVE</button>' +
		'<button onclick="CloseEditMode()" type="button">CANCEL</button>' + 
		'</div></div></div>';
	}
}

function CloseEditMode( mid )
{
	if ( mid && ge( 'MessageContent_' + mid ) && ge( 'EditMode_' + mid ) )
	{
		ge( 'MessageContent_' + mid ).className = ge( 'MessageContent_' + mid ).className.split( ' hidden' ).join( '' );
		ge( 'EditMode_' + mid ).className = ge( 'EditMode_' + mid ).className.split( ' hidden' ).join( '' ) + ' hidden';
	}
	else if ( ge( 'EditMode' ) )
	{
		ge( 'EditMode' ).parentNode.innerHTML = ge( 'EditMode' ).parentNode.cont;
	}
}

function embedPostEditor( mid )
{
	if ( mid && ge( 'MessageContent_' + mid ) && ge( 'EditMode_' + mid ) )
	{
		ge( 'MessageContent_' + mid ).className = ge( 'MessageContent_' + mid ).className.split( ' hidden' ).join( '' ) + ' hidden';
		ge( 'EditMode_' + mid ).className = ge( 'EditMode_' + mid ).className.split( ' hidden' ).join( '' );
	}
}

/*function embedPostEditor( mid )
{
	if( !ge( 'MessageContent_' + mid ) ) return;
	
	CloseEditMode();
	
	ge( 'MessageContent_' + mid ).cont = ge( 'MessageContent_' + mid ).innerHTML;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=wall&function=walleditor', 'post', true );
	j.addVar ( 'mid', mid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'MessageContent_' + mid ).innerHTML = r[1];
		}
	}
	j.send ();
}*/

// Update a message
function EditWallPost( ele, sid, type )
{
	if( !ele || !sid ) return;
	
	var value = ( ele.value ? ele.value : ele.innerHTML );
	
	if( value )
	{
		if( ge( 'MessageContent_' + sid ) )
		{
			CloseEditMode( sid );
			ge( 'MessageContent_' + sid ).innerHTML = value;
		}
		
		var j = new bajax();
		//j.openUrl( getPath() + '?component=wall&action=savesharemessage', 'post', true );
		j.openUrl( getPath() + '?component=wall&action=savepost', 'post', true );
		j.addVar( 'Type', ( type ? type : 'post' ) );
		j.addVar( 'Message', value );
		j.addVar( 'sid', sid );
		j.onload = function()
		{
			var r = this.getResponseText().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				//loadShareMessages();
				loadShareMessages ( false, 1 );
			}
		}
		j.send();
	}
}

function WallOptions( ele )
{
	if ( !ele ) return;
	
	if ( ele.parentNode.className.indexOf( 'open' ) >= 0 )
	{
		ele.parentNode.className = ele.parentNode.className.split( ' open' ).join( '' );
	}
	else
	{
		ele.parentNode.className = ele.parentNode.className.split( ' open' ).join( '' ) + ' open';
	}
}

/*function WallOptions( el, mid, cid )
{
	if( !el || !mid ) return;
	else
	{
		el = el.parentNode;
	}
	
	if( ge( 'WallOptions' ) )
	{
		while( ge( 'WallOptions' ) )
		{
			if( ge( 'WallOptions' ).parentNode )
			{
				ge( 'WallOptions' ).parentNode.removeChild( ge( 'WallOptions' ) );
			}
		}
	}
	else
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=wall&function=options', 'post', true );
		j.addVar ( 'mid', mid );
		if( cid ) j.addVar ( 'cid', cid );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				var wo = document.createElement( 'div' );
				wo.id = 'WallOptions';
				wo.className = 'open';
				var ta = document.createElement( 'div' );
				ta.className = 'toparrow';
				var wi = document.createElement( 'div' );
				wi.className = 'inner';
				wi.innerHTML = r[1];
				wo.appendChild( ta );
				wo.appendChild( wi );
				el.appendChild( wo );
				
				// Remove on select
				if( ge( 'WallOptions' ) )
				{
					ge( 'WallOptions' ).onclick = function()
					{
						if( ge( 'WallOptions' ).parentNode )
						{
							ge( 'WallOptions' ).parentNode.removeChild( ge( 'WallOptions' ) );
						}
					}
				}
			}
		}
		j.send ();
	}
}*/

function findContact( ele )
{
	if( !ele ) return;
	
	if( ge( 'ListFoundContacts' ) )
	{
		ge( 'ListFoundContacts' ).parentNode.removeChild( ge( 'ListFoundContacts' ) );
	}
	if( !ge( 'ListFoundContacts' ) )
	{
		var div = document.createElement( 'div' );
		div.id = 'ListFoundContacts';
		ele.parentNode.appendChild( div );
	}
	var lfc = ge( 'ListFoundContacts' );
	lfc.innerHTML = '';
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=wall&function=findcontacts', 'post', true );
	j.addVar ( 'search', ele.innerHTML );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			lfc.innerHTML = r[1];
		}
	}
	j.send ();
}

function selectContact ( cid, name )
{
	alert( 'Not yet, soon.' );
	ge( 'ListFoundContacts' ).parentNode.removeChild( ge( 'ListFoundContacts' ) );
	return;
	if( !cid || !name ) return;
	var s = document.createElement ( 'span' );
	s.className = 'contact';
	s.innerHTML = name + '<input type="hidden" id="cid_' + cid + '" value="' + cid + '"/><a href="javascript:void(0);" onclick="removeContact( ' + cid + ' )">x</a>';
	//insertTextAtCursorTest( 'testings...' );
	var span = '<span class="contact">' + name + '<input type="hidden" id="cid_' + cid + '" value="' + cid + '"/><a href="javascript:void(0);" onclick="removeContact( ' + cid + ' )">x</a></span>';
	//pasteHtmlAtCaretTest( span );
	//insertElementAtCursor( s );
	//placeCaretAfterNode( s );
	var i = document.createElement ( 'input' );
	i.id = 'cid_' + cid;
	i.className = 'contact';
	i.value = name;
	ge( 'ShareBox' ).appendChild( s );
}

function AddQuestion( trid )
{
	if( !ge( 'VoteContent' ) ) return;
	
	var value = ( ge( 'VoteContent' ).value ? ge( 'VoteContent' ).value : ge( 'VoteContent' ).innerHTML );
	
	if ( value )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=wall&action=savevote', 'post', true );
		j.addVar ( 'Content', value );
		j.addVar ( 'Access', ge( 'VoteAccess' ).value );
		
		if( trid )
		{
			j.addVar ( 'ThreadID', trid );
		}
		
		if( ge( 'VoteOptions' ) )
		{
			var inps = new Array();
			var inp = ge( 'VoteOptions' ).getElementsByTagName( 'input' );
			
			if( inp.length > 0 )
			{
				for( a = 0; a < inp.length; a++ )
				{
					if( inp[a].value )
					{
						inps.push( inp[a].value );
					}
				}
			}
			
			if( inps )
			{
				j.addVar ( 'Options', JSON.stringify( inps ) );
			}
		}
		
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				value = '';
				ge( 'VoteOptions' ).innerHTML = '';
				//loadShareMessages ();
				loadShareMessages ( false, 1 );
			}
		}
		j.send ();
	}
}

function AddPollOptions()
{
	if( ge( 'VoteOptions' ) && !ge( 'VoteOptions' ).innerHTML )
	{
		var str = '' + 
		'<div class="options"><input type="text" placeholder="Add an option..."/></div>' +
		'<div class="options"><input type="text" placeholder="Add an option..."/></div>' +
		'<div class="options"><input type="text" placeholder="Add an option..." onkeydown="if(event.keyCode==9){MorePollOptions();}"/></div>';
		
		ge( 'VoteOptions' ).innerHTML = str;
	}
}

function MorePollOptions()
{
	if( ge( 'VoteOptions' ) && ge( 'VoteOptions' ).innerHTML )
	{
		var opt = document.createElement( 'div' );
		opt.className = 'options';
		opt.innerHTML = '<input type="text" placeholder="Add an option..." onkeydown="if(event.keyCode==9){MorePollOptions();}"/>';
		
		//var inp = document.createElement( 'input' );
		
		//opt.appendChild( inp );
		
		ge( 'VoteOptions' ).appendChild( opt );
		
		//inp.focus();
		
		if( opt.getElementsByTagName( 'input' )[0] )
		{
			opt.getElementsByTagName( 'input' )[0].focus();
		}
	}
}

function SeeResult( id, ele )
{
	if( !id || !ge( id ) || !ele ) return;
	
	if( ge( id ).className.indexOf( 'showresult' ) >= 0 )
	{
		ge( id ).className = ge( id ).className.split( ' showresult' ).join( '' );
		ele.innerHTML = '&nbsp;|&nbsp;See Result&nbsp;';
	}
	else
	{
		ge( id ).className = ge( id ).className.split( ' showresult' ).join() + ' showresult';
		ele.innerHTML = '&nbsp;|&nbsp;Vote Now&nbsp;';
	}
}

/* --- Global Wall Event -------------------------------------------------------- */

// Check Global Keys
function wallCheckKeys( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var keycode = e.which ? e.which : e.keyCode;
	
	switch ( keycode )
	{
		case 27:
			if( ge( 'WallEditor' ) && ge( 'WallEditor' ).className.indexOf( 'open' ) >= 0 )
			{
				CloseWallEditor();
			}
			if( ge( 'EditMode' ) )
			{
				CloseEditMode();
			}
			break;
		default: break;
	}
}

// Check Global Cliks
function wallCheckClicks( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var button = e.which ? e.which : e.button;
	if( ge( 'WallEditor' ) && ge( 'WallEditor' ).className.indexOf( 'open' ) >= 0 )
	{
		var tags = ge( 'WallEditor' ).getElementsByTagName( '*' );
		if( tags.length > 0 )
		{
			for( a = 0; a < tags.length; a++ )
			{
				if( targ.id == 'WallEditor' || targ == tags[a] || targ.tagName == 'HTML' )
				{
					return;
				}
			}
			CloseWallEditor();
		}
	}
}

// Assign Global Listeners
if ( window.addEventListener )
{
	window.addEventListener ( 'keydown', wallCheckKeys );
	window.addEventListener ( 'mousedown', wallCheckClicks );
}
else 
{
	window.attachEvent ( 'onkeydown', wallCheckKeys );
	window.attachEvent ( 'onmousedown', wallCheckClicks );
}
