
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

var _progress = document.getElementById( '_progress' );
var _file_container = document.getElementById( '_file_container' );

var videoFormats = [
	'webm', 'mkv', 'flv', 'ogv', 'ogg', 'drc', 'mng', 'avi',
	'mov', 'qt', 'wmv', 'rm', 'rmvb', 'asf', 'mp4', 'm4p',
	'm4v', 'mpg', 'mp2', 'mpeg', 'mpg', 'mpe', 'mpv', 'mpeg',
	'm2v', 'm4v', 'svi', '3gp', '3g2', 'mxf', 'roq', 'nsv'
];

Number.prototype.formatBytes = function()
{
    var units = ['b', 'kb', 'mb', 'gb', 'tb'], bytes = this, i;
 
    for ( i = 0; bytes >= 1024 && i < 4; i++ )
	{
        bytes /= 1024;
    }
 
    return bytes.toFixed(2) + units[i];
}

function desktopselect( e, name, form )
{
	e.stopPropagation();
	e.preventDefault();
	
	var _file = e.dataTransfer;
	_file.name = ( name ? name : 'library' );
	
	if ( _file )
	{
		fileselect( _file, ( form ? form : 'FilesUpload' ) );
	}
}

function fileselect( _file, form )
{
	if( _file.files.length > 0 )
	{
		var str;
		
		for ( var a = 0; a < _file.files.length; a++ )
		{
			str = ( str ? str : '' ) + 
			'<div id="_file_' + a + '"><div class="progress_outer">' + 
			'<div class="progress" id="_progress_' + a + '"></div>' +
			'<div class="progress_info">' +
			'<span class="progress_name" id="_name_' + a + '"> ' + _file.files[a].name + ' ( ' + _file.files[a].size.formatBytes() + ' ) </span>' +
			'<span class="progress_abort" id="_abort_' + a + '"> [x] </span>' + 
			'<span class="progress_number" id="_number_' + a + '"></span>' + 
			'</div></div></div>';
		}
		
		_file_container.innerHTML = str;
		
		uploadfiles( _file, form );
	}
}

function uploadfiles( _file, form )
{
	if( _file.files.length === 0 )
	{
        return;
    }
	
	var _inpt = document[form].getElementsByTagName( 'input' );
	
	for( a = 0; a < _file.files.length; a++ )
	{
		upload = function( data, file, rand )
		{
			//upload the file
			var xhr = new Array();

			xhr[rand] = new XMLHttpRequest();
			xhr[rand].open( 'POST', getPath() + '?component=library&action=uploadfile&jax=true' );
			
			xhr[rand].upload.addEventListener( 'progress', function ( event )
			{
				//console.log( event );
				if ( event.lengthComputable )
				{
					/*if ( inArray( file.name.split('.')[1], videoFormats ) )
					{
						document.getElementById( '_progress_' + rand ).style.width = ( ( event.loaded / ( event.total * 2 ) ) * 100 + '%' );
						document.getElementById( '_number_' + rand ).innerHTML = ( ( ( event.loaded / ( event.total * 2 ) ) * 100 ).toFixed(2) + '%' );
					}
					else
					{*/
						document.getElementById( '_progress_' + rand ).style.width = ( ( event.loaded / event.total ) * 100 + '%' );
						document.getElementById( '_number_' + rand ).innerHTML = ( ( ( event.loaded / event.total ) * 100 ).toFixed(2) + '%' );
					/*}*/
				}
				else
				{
					alert( 'Failed to compute file upload length' );
				}
			}, false );
			
			xhr[rand].addEventListener( 'error', function ( event ){ console.log( 'There was an error attempting to upload the file.' ); }, false );      
            xhr[rand].addEventListener( 'abort', function ( event ){ console.log( 'The upload has been canceled by the user or the browser dropped the connection.' ); }, false );
			
			xhr[rand].onreadystatechange = function ( oEvent )
			{ 
				if ( xhr[rand].readyState === 4 )
				{ 
					if ( xhr[rand].status === 200 )
					{
						document.getElementById( '_progress_' + rand ).style.width = '100%';
						document.getElementById( '_number_' + rand ).innerHTML = '100%';
						
						document.getElementById( '_file_' + rand ).parentNode.removeChild( document.getElementById( '_file_' + rand ) );
						
						// Check if the file has scripts
						var scripts = '';
						var rt = xhr[rand].responseText;
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
					else
					{ 
						//alert( 'Error : ' + xhr[rand].statusText );
						console.log( 'Error : ' + xhr[rand].statusText );
					} 
				} 
			}; 
			
			// Set headers
			//xhr[rand].setRequestHeader( 'Content-Type', 'multipart/form-data' );
			//xhr[rand].setRequestHeader( 'X-File-Name', data.fileName );
			//xhr[rand].setRequestHeader( 'X-File-Size', data.fileSize );
			//xhr[rand].setRequestHeader( 'X-File-Type', data.type );
			
			// Send the file
			xhr[rand].send( data );
			
			document.getElementById( '_abort_' + rand ).onclick = function(){ xhr[rand].abort() };
		}
		
		var data = new FormData();
		data.append( _file.name, _file.files[a] );
		
		if( _inpt.length > 0 )
		{
			for( i = 0; i < _inpt.length; i++ )
			{
				if( _inpt[i].type != 'file' && _inpt[i].name )
				{
					data.append( _inpt[i].name, _inpt[i].value );
				}
			}
		}
		
		upload( data, _file.files[a], a );
	}
	
	_file.value = '';
}

function handleDragOver( e )
{
	e.stopPropagation();
	e.preventDefault();
	e.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
}

function handleDragLeave( e )
{
	e.stopPropagation();
	e.preventDefault();
}

function handleDrop( fid, type, did, name, form, e )
{
	if ( !e ) return;
	
	delete window.dragId;
	delete window.dragType;
	
	e.stopPropagation();
	e.preventDefault();
	
	if ( !e.dataTransfer.files.length && fid && type && did )
	{
		moveFile( fid, type, did );
	}
	else
	{
		desktopselect( e, name, form );
	}
}

function handleDragStart( _file, fid, type, e )
{
	if ( !_file || !fid || !type || !e ) return;
	
	window.dragId = fid;
	window.dragType = type;
	
	var details = false;
	
	// Some forward thinking, utilise the custom data attribute to extend attributes available.
	if ( typeof _file.dataset === 'undefined' )
	{
		if ( _file.getAttribute( 'data-downloadurl' ) )
		{
			// Grab it the old way
			details = _file.getAttribute( 'data-downloadurl' );
			//console.log( details + ' -- ' );
		}
	}
	else
	{
		details = _file.dataset.downloadurl;
	}
	
	// e.dataTransfer in Firefox uses the DataTransfer constructor
	// instead of Clipboard
	// make sure it's Chrome and not Safari (both webkit-based).
	// setData on DownloadURL returns true on Chrome, and false on Safari
	//console.log( 'mja -- ' + details );
	/*if ( e.dataTransfer && e.dataTransfer.setData( 'DownloadURL', 'http://dev.treeroot.org' ) )
	{*/
		//var url = ( _file.dataset && _file.dataset.downloadurl ) || _file.getAttribute( "data-downloadurl" );
		//e.dataTransfer.setData( "DownloadURL", url );
		//console.log( url + ' -- ' );
	/*}*/
	
	if ( details )
	{
		e.dataTransfer.setData( 'DownloadURL', details );
	}
}
