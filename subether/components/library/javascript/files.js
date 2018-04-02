
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

var checkContent;

var SubList = new Object();

function IsEditing ( fid )
{
	if( !fid || !ge( 'FileID_' + fid ) || strstr( ge( 'FileID_' + fid ).innerHTML, ' *' ) ) return;
	
	ge( 'FileID_' + fid ).innerHTML = ge( 'FileID_' + fid ).innerHTML.split(' *').join('') + ' *';
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=editing', 'post', true );
	j.addVar ( 'fid', fid );
	j.addVar ( 'type', ge( 'FileID_' + fid ).getAttribute( 'filetype' ) );
	j.addVar ( 'edit', '1' );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			ge( 'FileID_' + fid ).innerHTML = ge( 'FileID_' + fid ).innerHTML.split(' *').join('') + ' *';
		}
	}
	j.send ();
}

function NotEditing ( fid )
{
	if( !fid || !ge( 'FileID_' + fid ) || !strstr( ge( 'FileID_' + fid ).innerHTML, ' *' ) ) return;
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=editing', 'post', true );
	j.addVar ( 'fid', fid );
	j.addVar ( 'type', ge( 'FileID_' + fid ).getAttribute( 'filetype' ) );
	j.addVar ( 'edit', '0' );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			ge( 'FileID_' + fid ).innerHTML = ge( 'FileID_' + fid ).innerHTML.split(' *').join('');
		}
	}
	j.send ();
}

function cancelBubble ( e )
{
	// Cancel bubble
	var evt = e ? e:window.event;
	if (evt.stopPropagation)    evt.stopPropagation();
	if (evt.cancelBubble!=null) evt.cancelBubble = true;
}

function UpdateAccess ( mid, type, value )
{
	if( !mid || !type ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=access', 'post', true );
	j.addVar ( 'mid', mid );
	j.addVar ( 'value', value );
	j.addVar ( 'type', type );
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

function saveFilesContent()
{
	var content = ( ge( 'ContentEditor' ) ? ge( 'ContentEditor' ) : ge( 'TextAreaEditor' ) );
	var html = CKEDITOR && typeof CKEDITOR.instances['ContentEditorCK'] !== 'undefined' ? CKEDITOR.instances['ContentEditorCK'].getData() : ( content.value ? content.value : trim( content.innerHTML ) );
	
	var fid = content.getAttribute( 'fileid' );
	
	//alert( ge( 'Directory' ).getElementsByTagName( 'h4' ).length + ' .. ' + fid )
	if( !content || checkContent == html || ( ge( 'Directory' ).getElementsByTagName( 'h4' ).length > 0 && !fid ) ) return;
	
	// If Jax is stacking exclude this request until the previous one has loaded
	if( running( 'saveFilesContent', true ) )
	{
		return;
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=files&option=savefile', 'post', true );
	if( fid > 0 ) j.addVar ( 'fid', fid );
	j.addVar ( 'filetype', 'file' );
	j.addVar ( 'filecontent', html );
	j.onload = function ()
	{
		// So, tell we're completed
		running( 'saveFilesContent', false );
		
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			if( r[3] )
			{
				checkContent = r[3];
				
				if( !ge( 'Directory' ).getElementsByTagName( 'h4' ).length )
				{
					refreshFilesDirectory();
				}
				
				if( fid > 0 && ge( 'FileID_' + fid ) )
				{
					NotEditing( fid );
				}
			}
		}
	}
	j.send ();
}

// Saves the text content of a text file
function saveFileTextContent()
{
	var content = ( ge( 'ContentEditor' ) ? ge( 'ContentEditor' ) : ge( 'TextAreaEditor' ) );
	var html = CKEDITOR && typeof CKEDITOR.instances['ContentEditorCK'] !== 'undefined' ? CKEDITOR.instances['ContentEditorCK'].getData() : ( content.value ? content.value : trim( content.innerHTML ) );
	
	var fid = content.getAttribute( 'fileid' );
	var pid = content.getAttribute( 'folderid' );
	var fnm = content.getAttribute( 'filename' );
	
	console.log( 'saveFileTextContent() ... ' + fid + ' [] ' + pid + ' [] ' + fnm );
	
	if( !content || checkContent == html || ( ge( 'Directory' ).getElementsByTagName( 'h4' ).length > 0 && !fid && !pid ) ) return;
	
	// If Jax is stacking exclude this request until the previous one has loaded
	if( running( 'saveFileTextContent', true ) )
	{
		return;
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=files&option=' + ( fid ? 'savefiletextcontent' : 'createfile' ), 'post', true );
	if( fid > 0 ) j.addVar ( 'fid', fid );
	if( pid > 0 ) j.addVar ( 'pid', pid );
	if( fnm ) j.addVar ( 'filename', fnm );
	j.addVar ( 'filecontent', html );
	j.onload = function ()
	{
		// So, tell we're completed
		running( 'saveFileTextContent', false );
		
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			if( r[3] )
			{
				// Stores stored data to compare for later
				checkContent = r[3];
				
				if( !fid && r[2] )
				{
					content.setAttribute( 'fileid', r[2] );
				}
				
				if( !ge( 'Directory' ).getElementsByTagName( 'h4' ).length )
				{
					refreshFilesDirectory();
				}
				
				if( fid > 0 && ge( 'FileID_' + fid ) )
				{
					NotEditing( fid );
				}
			}
		}
	}
	j.send ();
}

function moveFile( fid, type, pid )
{
	if( !fid || !type || !pid ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=files&option=savefile', 'post', true );
	j.addVar ( 'fid', fid );
	j.addVar ( 'filetype', type );
	j.addVar ( 'pid', pid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			refreshFilesDirectory( ge( 'FolderID' ).value, false, ge( 'ThumbView' ).value );
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}

function saveFileName( fid, type, current, event )
{
	if( !fid && !ge( 'EditMode' ) && !ge( 'EditMode' ).value ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=files&option=savefile', 'post', true );
	j.addVar ( 'fid', fid );
	j.addVar ( 'filename', ge( 'EditMode' ).value );
	j.addVar ( 'filetype', type );
	if( current ) j.addVar ( 'current', current );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			refreshFilesDirectory( ( r[1] ? r[1] : false ), ( r[2] ? r[2] : false ), false, event, 'files' );
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}

function saveFolderName( fid, current, event )
{
	if( !fid && !ge( 'EditMode' ) && !ge( 'EditMode' ).value ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=files&option=savefolder', 'post', true );
	j.addVar ( 'fid', fid );
	j.addVar ( 'foldername', ge( 'EditMode' ).value );
	if( current ) j.addVar ( 'current', current );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			refreshFilesDirectory( r[1] ? r[1] : false, false, false, event );
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}

function refreshFilesDirectory( mid, fid, view, e, index, fpath )
{
	var directory = ge( 'Directory' );
	var folderid = ge( 'FolderID' );
	var inner = ge( 'ContentInner' );
	var tview = ge( 'ThumbView' );
	
	if( !view && tview )
	{
		view = tview.getAttribute( 'value' );
	}
	
	if( !inner && fid )
	{
		var obj = new Object();
		if( mid > 0 ) obj.mid = mid;
		if( fid > 0 ) obj.fid = fid;
		if( view ) obj.view = view;
		if( e ) obj.e = e;
		
		openFullscreen( 'Library', obj, 'content', function(){ InitCKEditor( 'ContentEditor' ) }, false, e, true );
		return;
	}
	else if( !inner && mid )
	{
		var toggle = ge( 'FolderID_' + mid ).parentNode.getElementsByTagName( 'span' )[0];
		
		if( toggle && ge( 'FolderID_' + mid ).parentNode )
		{
			openSubList( toggle, mid, e, ge( 'FolderID_' + mid ).parentNode );
		}
		return;
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&function=files' + ( index ? ( '&index=' + index ) : '' ), 'post', true );
	if( mid > 0 ) j.addVar ( 'mid', mid );
	if( fid > 0 ) j.addVar ( 'fid', fid );
	if( view ) j.addVar ( 'view', view );
	if( fpath ) j.addVar ( 'fpath', fpath );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			if( r[1] || r[2] || r[3] ) 
			{
				// If index is defined we are only updating index lists nothing more
				if( index && ge( 'FolderID_' + mid ) )
				{
					ge( 'FolderID_' + mid ).parentNode.parentNode.getElementsByTagName( 'ul' )[0].innerHTML = r[1];
				}
				else
				{
					// Commented out because we shouldn't update the whole directory list every time we ...
					//if( directory ) directory.innerHTML = r[1];
					if( folderid ) folderid.value = r[2];
					
					// Set current folder when doing folder switch ...
					if( ge( 'FolderID_' + mid ) && ge( 'Directory' ) )
					{
						var divs = ge( 'Directory' ).getElementsByTagName( 'div' );
						
						if( divs.length > 0 )
						{
							for( var a = 0; a < divs.length; a++ )
							{
								if( divs[a].className.indexOf( 'current' ) >= 0 )
								{
									divs[a].className = divs[a].className.split( 'current' ).join( '' );
								}
							}
						}
						
						ge( 'FolderID_' + mid ).parentNode.className = ge( 'FolderID_' + mid ).parentNode.className.split( 'current' ).join( '' ) + 'current';
					}
					
					if( tview )
					{
						tview.setAttribute( 'mid', r[2] );
						tview.setAttribute( 'value', ( view ? view : 0 ) );
						
						var li = tview.getElementsByTagName( 'li' );
						
						if( li.length > 0 )
						{
							var set = false;
							
							for( b = 0; b < li.length; b++ )
							{
								if( li[b].getAttribute( 'value' ) && view && li[b].getAttribute( 'value' ) == view )
								{
									li[b].className = 'selected';
									set = true;
								}
								else
								{
									li[b].className = '';
								}
							}
							
							if( !set )
							{
								li[0].className = 'selected';
							}
						}
					}
					if( inner ) inner.innerHTML = r[3];
					checkContent = r[4];
					
					/*if( !view )
					{
						// Reset select
						//tview.selectedIndex = 0;
					}*/
					
					if( SubList )
					{
						for( var k in SubList )
						{
							if( SubList[k] && ge( k ) )
							{
								if( ge( k ).parentNode && ge( k ).parentNode.getElementsByTagName( 'span' ) )
								{
									ge( k ).parentNode.getElementsByTagName( 'span' )[0].innerHTML = SubList[k];
								}
								if( ge( k ).parentNode.parentNode && ge( k ).parentNode.parentNode.getElementsByTagName( 'ul' )[0] )
								{
									ge( k ).parentNode.parentNode.getElementsByTagName( 'ul' )[0].className = 'open';
								}
							}
						}
					}
					
					if ( ge( 'ContentEditor' ) )
					{
						// Check if the file has scripts
						var scripts = '';
						var rt = r[3];
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
						
						InitCKEditor( 'ContentEditor' );
						
						/*if( ge( 'ContentEditor' ).tagName == 'TEXTAREA' )
						{
							// Set content editor and save
							var d = document.createElement( 'div' );
							d.id = 'ContentEditorCK';
							d.innerHTML = ge( 'ContentEditor' ).value;
							ge( 'ContentEditor' ).parentNode.appendChild( d );
							ge( 'ContentEditor' ).style.display = 'none';
							CKEDITOR.replace( 'ContentEditorCK' );
							CKEDITOR.instances['ContentEditorCK'].on( 'change', function()
							{
								IsEditing( ge( 'ContentEditor' ).getAttribute( 'fileid' ) );
							
								// Makes sure we delay saving always 500ms after typing
								if( window.contentEditorSaveTm )
								{
									clearInterval( window.contentEditorSaveTm );
									window.contentEditorSaveTm = false;
								}
								window.contentEditorSaveTm = setTimeout( saveFileTextContent, 500 );
							});
						}*/
					}
				}
			}
			else
			{
				if( directory ) directory.innerHTML = '';
				if( inner ) inner.innerHTML = '';
				checkContent = '';
			}
		}
	}
	j.send ();
	
	if( e )
	{
		// Cancel bubble
		var evt = e ? e:window.event;
		if (evt.stopPropagation)    evt.stopPropagation();
		if (evt.cancelBubble!=null) evt.cancelBubble = true;
	}
	return;
}

function InitCKEditor( id )
{
	if( id && ge( id ) && ge( id ).tagName == 'TEXTAREA' )
	{
		// Set content editor and save
		var d = document.createElement( 'div' );
		d.id = 'ContentEditorCK';
		d.innerHTML = ge( 'ContentEditor' ).value;
		ge( 'ContentEditor' ).parentNode.appendChild( d );
		ge( 'ContentEditor' ).style.display = 'none';
		CKEDITOR.replace( 'ContentEditorCK' );
		CKEDITOR.instances['ContentEditorCK'].on( 'change', function()
		{
			IsEditing( ge( id ).getAttribute( 'fileid' ) );
		
			// Makes sure we delay saving always 500ms after typing
			if( window.contentEditorSaveTm )
			{
				clearInterval( window.contentEditorSaveTm );
				window.contentEditorSaveTm = false;
			}
			window.contentEditorSaveTm = setTimeout( saveFileTextContent, 500 );
		});
	}
}

function createNewFile( pid, current )
{
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=files&option=createfile', 'post', true );
	j.addVar ( 'filename', 'File_1' );
	if( pid > 0 ) j.addVar ( 'pid', pid );
	if( current ) j.addVar ( 'current', current );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			refreshFilesDirectory( ( r[1] ? r[1] : false ), ( r[2] ? r[2] : false ) );
		}
		else alert( this.getResponseText() );
	}
	j.send ();
}

function createNewFolder( pid, current )
{
	if( !pid ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=files&option=createfolder', 'post', true );
	j.addVar ( 'directoryname', 'Folder_1' );
	j.addVar ( 'pid', pid );
	if( current ) j.addVar ( 'current', current );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			refreshFilesDirectory( r[1] ? r[1] : false );
		}
	}
	j.send ();
}

function deleteFile( fid, type, current )
{
	if( !fid ) return;
	var r = confirm( 'Are you sure?' );
	if( r == true )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=library&action=files&option=deletefile', 'post', true );
		j.addVar ( 'deletefile', fid );
		j.addVar ( 'filetype', type );
		if( current ) j.addVar ( 'current', current );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				refreshFilesDirectory( r[1] ? r[1] : false );
			}
		}
		j.send ();
	}
	return;
}

function deleteFolder( fid, current )
{
	if( !fid ) return;
	var r = confirm( 'Are you sure?' );
	if( r == true )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=library&action=files&option=deletefolder', 'post', true );
		j.addVar ( 'deletefolder', fid );
		if( current ) j.addVar ( 'current', current );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				refreshFilesDirectory( r[1] ? r[1] : false );
			}
		}
		j.send ();
	}
	return;
}

function editFile( fid, e )
{
	closeEditMode();
	if( !fid && !ge( 'FileID_' + fid ) ) return;
	
	var obj = new Object();
	obj.onclick = ge( 'FileID_' + fid ).parentNode.parentNode.getAttribute('onclick');
	obj.type = 'file';
	
	ge( 'FileID_' + fid ).parentNode.parentNode.setAttribute( 'onclick', 'return false' );
	ge( 'FileID_' + fid ).innerHTML = '<input onkeyup="if( event.keyCode == 13 ){ saveFileName( \'' + fid + '\', \'' + ge( 'FileID_' + fid ).getAttribute( 'filetype' ) + '\', false, event ); }" id="EditMode" class="' + ge( 'FileID_' + fid ).innerHTML + '" value="' + ge( 'FileID_' + fid ).innerHTML + '"/>';
	
	ge( 'EditMode' ).obj = obj;
	ge( 'EditMode' ).focus();
	
	// Cancel bubble
	var evt = e ? e:window.event;
	if (evt.stopPropagation)    evt.stopPropagation();
	if (evt.cancelBubble!=null) evt.cancelBubble = true;
	return;
}

function editFolder( fid, e )
{
	closeEditMode();
	if( !fid && !ge( 'FolderID_' + fid ) ) return;
	
	var obj = new Object();
	obj.onclick = ge( 'FolderID_' + fid ).parentNode.getAttribute('onclick');
	obj.type = 'folder';
	
	ge( 'FolderID_' + fid ).parentNode.setAttribute( 'onclick', 'return false' );
	ge( 'FolderID_' + fid ).innerHTML = '<input onclick="cancelBubble(event)" onkeyup="if( event.keyCode == 13 ){ saveFolderName( ' + fid + ', false, event ); }" id="EditMode" class="' + ge( 'FolderID_' + fid ).innerHTML + '" value="' + ge( 'FolderID_' + fid ).innerHTML + '"/>';
	
	ge( 'EditMode' ).obj = obj;
	ge( 'EditMode' ).focus();
	
	// Cancel bubble
	var evt = e ? e:window.event;
	if (evt.stopPropagation)    evt.stopPropagation();
	if (evt.cancelBubble!=null) evt.cancelBubble = true;
	return;
}

function closeEditMode( e )
{
	if( !ge( 'EditMode' ) ) return;
	
	if( ge( 'EditMode' ).obj.type == 'file' )
	{
		ge( 'EditMode' ).parentNode.parentNode.parentNode.setAttribute( 'onclick', ge( 'EditMode' ).obj.onclick );
		ge( 'EditMode' ).parentNode.innerHTML = ge( 'EditMode' ).className;
	}
	else if( ge( 'EditMode' ).obj.type == 'folder' )
	{
		ge( 'EditMode' ).parentNode.parentNode.setAttribute( 'onclick', ge( 'EditMode' ).obj.onclick );
		ge( 'EditMode' ).parentNode.innerHTML = ge( 'EditMode' ).className;
	}
	//ge( 'EditMode' ).parentNode.setAttribute( 'onClick', 'refreshFilesDirectory( ' + ge( 'EditMode' ).parentNode.id.split( '_' )[1] + ' )' );
	//ge( 'EditMode' ).parentNode.innerHTML = ge( 'EditMode' ).className;
}

function toggleLibrary( ele )
{
	var parent = ele.parentNode;
	
	var elem = parent.getElementsByTagName( 'div' )[1];
	
	if( elem )
	{
		if( elem.className.indexOf( 'closed' ) >= 0 )
		{
			elem.className = 'open ' + elem.className.split( 'closed ' ).join( '' );
		}
		else
		{
			elem.className =  'closed ' + elem.className.split( 'open ' ).join( '' );
		}
	}
}

function openSubList( ele, fid, e, curr )
{
	if( !ele  ) return;
	
	var fld = false;
	
	if( curr && ge( 'Directory' ) )
	{
		var cufl = ge( 'Directory' ).getElementsByTagName( 'div' );
		
		if ( cufl.length > 0 )
		{
			for ( a = 0; a < cufl.length; a++ )
			{
				if ( cufl[a].className == 'current' )
				{
					fld = cufl[a];
				}
			}
		}
	}
	
	if( !ele.parentNode.parentNode.getElementsByTagName( 'ul' )[0] )
	{
		if( fid )
		{
			var j = new bajax ();
			j.openUrl ( getPath() + '?component=library&function=files&index=files', 'post', true );
			j.addVar ( 'mid', fid );
			j.onload = function ()
			{
				var r = this.getResponseText ().split ( '<!--separate-->' );
				if ( r[0] == 'ok' && r[1] )
				{
					if ( curr && fld )
					{
						fld.className = '';
						curr.className = 'current';
						
					}
					
					var ul = document.createElement( 'ul' );
					ul.className = 'open'
					ul.innerHTML = r[1];         
					ele.parentNode.parentNode.appendChild( ul );
					
					ele.className = ele.className.split( ' open' ).join( '' ) + ' open';
					ele.innerHTML = '<img class="Icon" src="lib/icons/bullet_toggle_minus.png">';
					SubList[ 'FolderID_' + fid ] = '<img class="Icon" src="lib/icons/bullet_toggle_minus.png">';
				}
			}
			j.send ();
		}
	}
	else
	{
		if( ele.parentNode.parentNode.getElementsByTagName( 'ul' )[0].className == 'open' )
		{
			ele.className = ele.className.split( ' open' ).join( '' );
			ele.innerHTML = '<img class="Icon" src="lib/icons/bullet_toggle_plus.png">';
			ele.parentNode.parentNode.getElementsByTagName( 'ul' )[0].className = '';
			SubList[ 'FolderID_' + fid ] = false;
		}
		else
		{
			if ( curr && fld )
			{
				fld.className = '';
				curr.className = 'current';
				
			}
			
			ele.className = ele.className.split( ' open' ).join( '' ) + ' open';
			ele.innerHTML = '<img class="Icon" src="lib/icons/bullet_toggle_minus.png">';
			ele.parentNode.parentNode.getElementsByTagName( 'ul' )[0].className = 'open';
			SubList[ 'FolderID_' + fid ] = '<img class="Icon" src="lib/icons/bullet_toggle_minus.png">';
		}
	}
	
	// Cancel bubble
	var evt = e ? e:window.event;
	if (evt.stopPropagation)    evt.stopPropagation();
	if (evt.cancelBubble!=null) evt.cancelBubble = true;
	return;
}

function sortUp( fid, num, type, current )
{
	if( !fid || num == 0 || !type ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=files&sortup=true&option=' + ( type == 'folder' ? 'savefolder' : 'savefile' ), 'post', true );
	j.addVar ( 'fid', fid );
	j.addVar ( 'sortorder', num );
	j.addVar ( 'filetype', type );
	//if( current ) j.addVar ( 'current', current );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			//refreshFilesDirectory();
			refreshFilesDirectory( ge( 'FolderID' ).value );
		}
	}
	j.send ();
}

function sortDown( fid, num, type, current )
{
	if( !fid || num < 0 || !type ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&action=files&sortdown=true&option=' + ( type == 'folder' ? 'savefolder' : 'savefile' ), 'post', true );
	j.addVar ( 'fid', fid );
	j.addVar ( 'sortorder', num );
	j.addVar ( 'filetype', type );
	//if( current ) j.addVar ( 'current', current );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			//refreshFilesDirectory();
			refreshFilesDirectory( ge( 'FolderID' ).value );
		}
	}
	j.send ();
}

function focusEditor()
{
	if( ge( 'ContentEditor' ) )
	{
		ge( 'ContentEditor' ).focus();
	}
}

function parseUrl( url )
{
	if( !url ) return;
	
	if( ge( 'AjaxLoader' ) ) ge( 'AjaxLoader' ).className = 'loading';
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=library&function=parse', 'post', true );
	j.addVar ( 'url', url );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			var pb;
			var pc = ge( 'ParseContent' );
			pc.obj = JSON.parse( r[1] );
			
			// If type is video ------------------------------------------------------
			if( pc.obj.Type == 'video' && pc.obj.Images )
			{
				pb = '<div id="ParseImageBox" class="image" link="' + pc.obj.Url + '">' +
				'<img ImageID="0" style="background-image:url(' + pc.obj.Images[0]['src'] + ');max-width:' +
				pc.obj.Images[0]['width'] + 'px;max-height:' + pc.obj.Images[0]['height'] + 'px;"/>' + 
				'<i></i>' + 
				'</div>';
			}
			// If type is audio ------------------------------------------------------
			else if( pc.obj.Type == 'audio' )
			{
				pb = '<div id="ParseImageBox" class="image" link="' + pc.obj.Url + '">' +
				'<img ImageID="0" style="background-image:url(\'admin/gfx/icons/page_white.png\');">' +
				'<i></i>' + 
				'</div>';
			}
			// If type is site -------------------------------------------------------
			else if( pc.obj.Images )
			{
				var cls = ' small';
				if( pc.obj.Limit['width'] <= pc.obj.Images[0]['width'] )
				{
					cls = ' big';
				}
				
				pb = '<div id="ParseImageBox" class="image ' + pc.obj.Type + ' ' + cls + '">' +
				'<img ImageID="0" style="background-image:url(' + pc.obj.Images[0]['src'] + ');max-width:' +
				pc.obj.Images[0]['width'] + 'px;max-height:' + pc.obj.Images[0]['height'] + 'px;"/>' + 
				'<div id="ParseArrows" class="arrows">' +
				'<button class="PrevArrow" onclick="switchParsedUrlImage(0)"> << </button>' +
				'<button class="NextArrow" onclick="switchParsedUrlImage(2)"> >> </button>' + 
				'</div></div>';
			}
			
			// Output ----------------------------------------------------------------
			pc.innerHTML = '<div class="ParseContent">' + pb + '<div class="text">' + 
			'<h3><a target="_blank" href="' + pc.obj.Url + '">' + ( pc.obj.Title ? pc.obj.Title : '' ) + '</a></h3>' + 
			'<p><a target="_blank" href="' + pc.obj.Url + '">' + ( pc.obj.Leadin ? pc.obj.Leadin : '' ) + '</a></p>' + 
			'<p class="url"><a target="_blank" href="' + pc.obj.Url + '">' + pc.obj.Domain + '</a></p>' + 
			'</div></div>';
		}
		else if( !r[0] ) alert( this.getResponseText() );
		
		if( ge( 'AjaxLoader' ) ) ge( 'AjaxLoader' ).className = false;
	}
	j.send ();
}

function switchParsedUrlImage( num )
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
	pb.innerHTML = '<img ImageID="' + (num-1) + '" style="background-image:url(' + pc.obj.Images[(num-1)]['src'] + ');max-width:' +
	pc.obj.Images[(num-1)]['width'] + 'px;max-height:' + pc.obj.Images[(num-1)]['height'] + 'px;"/>' + 
	'<div id="ParseArrows" class="arrows">' +
	'<button class="PrevArrow" onclick="switchParsedUrlImage(' + ( num - 1 ) + ')"> << </button>' +
	'<button class="NextArrow" onclick="switchParsedUrlImage(' + ( num + 1 ) + ')"> >> </button>' + 
	'</div>';
}

function saveParseData( pid )
{
	if( pid ) pid = pid.split( '_' );
	
	if( ge( 'ParseContent' ) && ge( 'ParseContent' ).obj )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=library&action=parse', 'post', true );
		j.addVar ( 'data', JSON.stringify( ge( 'ParseContent' ).obj ) );
		j.addVar ( 'pid', pid[1] );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' )
			{
				refreshFilesDirectory( r[1] ? r[1] : false );
				closeWindow();
			}
			else alert( this.getResponseText() );
		}
		j.send ();
	}
}

/* --- Global Files Event -------------------------------------------------------- */

// Check Global Keys
function checkKeys( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	var keycode = e.which ? e.which : e.keyCode;
	switch ( keycode )
	{
		case 27:
			closeEditMode();
			break;
		default: break;
	}
}

// Check Global Cliks
function checkClicks( e )
{
	if ( !e ) e = window.event;
	var targ = e.srcElement ? e.srcElement : e.target;
	if( !ge( 'EditMode' ) && ( ge( 'ContentEditor' ) || ge( 'TextAreaEditor' ) ) )
	{
		//if( targ.id != 'ContentEditor' && targ.tagName != 'HTML' && targ.tagName != 'BODY' )
		if( targ.id != 'ContentEditor' && targ.id != 'TextAreaEditor' )
		{
			//saveFilesContent();
			saveFileTextContent();
		}
	}
	if( ge( 'EditMode' ) )
	{
		if( targ.id != 'EditMode' && targ.tagName != 'HTML' && targ.tagName != 'BODY' )
		{
			closeEditMode();
		}
	}
}

// Assign Global Listeners
if ( window.addEventListener )
{
	window.addEventListener ( 'keydown', checkKeys );
	window.addEventListener ( 'mousedown', checkClicks );
}
else 
{
	window.attachEvent ( 'onkeydown', checkKeys );
	window.attachEvent ( 'onmousedown', checkClicks );
}
