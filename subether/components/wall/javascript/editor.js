
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

var savedRange;

function InsertCodeTag ()
{
	var pre = document.createElement( 'pre' );
	var code = document.createElement( 'code' );
	code.innerHTML = 'enter code here';
	pre.appendChild( code );
	insertElementAtCursor( pre );
	//code.focus();
	//code.select();
	//selectInnerText( code );
}

function InsertBold ()
{
	var bold = document.createElement( 'strong' );
	insertElementAtCursor( bold );
}

function InsertItalic ()
{
	var italic = document.createElement( 'i' );
	insertElementAtCursor( italic );
}

function SwitchViewMode ()
{
	var id = [ 'ArticleLeadin', 'ArticleContent' ];
	for( var a = 0; a < id.length; a++ )
	{
		var ele = ge( id[a] );
		if( ele.getAttribute( 'view' ) && ele.getAttribute( 'view' ) == 'code' )
		{
			ele.innerHTML = html_decode( ele.innerHTML );
			ele.removeAttribute( 'view' );
		}
		else
		{
			ele.innerHTML = html_encode( ele.innerHTML );
			ele.setAttribute( 'view', 'code' );
		}
	}
}

function html_decode ( str )
{
    var entities = [
        ['apos', '\''],
        ['amp', '&'],
        ['lt', '<'],
        ['gt', '>']
    ];

    for ( var i = 0, max = entities.length; i < max; ++i )
	{
        str = str.replace( new RegExp( '&' + entities[i][0] + ';', 'g' ), entities[i][1] );
	}

    return str;
}
 
function html_encode ( str )
{
	var buf = [];
	for ( var i = str.length -1; i >= 0; i-- )
	{
		buf.unshift( [ '&#', str[i].charCodeAt(), ';' ].join( '' ) );
	}
	return buf.join( '' );
}

/*
function html_decode ( str )
{
	return str.replace(/&#(\d+);/g, function( match, dec )
	{
		return String.fromCharCode( dec );
	} );
}


function html_encode ( str )
{
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function html_decode ( str )
{
    return String(str).replace('&amp;', '&').replace('&lt;', '<').replace('&gt;', '>').replace('&quot;', '"');
}*/

function highlightCode ( ele, e )
{
	return;
	var keycode = e.which ? e.which : e.keyCode;
	if( !ele || keycode == '32' ) return;
	var code = ele.getElementsByTagName( 'code' );
	if( code.length > 0 )
	{
		var last;
		for( a = 0; a < code.length; a++ )
		{
			if( strstr( code[a].innerHTML, 'var' ) )
			{
				code[a].innerHTML = code[a].innerHTML.split( '<span class="kwd">var</span>' ).join( 'var' );
				code[a].innerHTML = code[a].innerHTML.split( 'var' ).join( '<span class="kwd">var</span>' );
				last = code[a];
				//setCursorToEnd( code[a] );
			}
		}
		if( last )
		{
			var span = last.getElementsByTagName( 'span' );
			if( span.length > 0 )
			{
				for( a = 0; a < span.length; a++ )
				{
					last = span[a];
				}
			}
			//alert( last.innerHTML + ' --' );
			//var stored = last.innerHTML;
			//last.focus();
			//last.innerHTML = '';
			//last.innerHTML = stored;
			//alert( last.className + ' ..' );
			//last.focus();
			//last.textContent.focus();
			//setCursorToEnd( last );
			placeCaretAfterNode( last );
			//restoreSelection( savedRange );
		}
	}
}

function selectInnerText ( ele )
{
	if ( document.selection )
	{
		var range = document.body.createTextRange();
		range.moveToElementText( ele );
		range.select();
	}
	else if ( window.getSelection )
	{
		var range = document.createRange();
		range.selectNode( ele );
		window.getSelection().addRange( range );
	}
}

function placeCaretAfterNode ( node )
{
    if ( typeof window.getSelection != "undefined" )
	{
        var range = document.createRange();
        range.setStartAfter( node );
        range.collapse( true );
        var selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange( range );
    }
}

function setCursorToEnd ( ele )
{
	var range = document.createRange();
	var sel = window.getSelection();
	range.setStart( ele, 1 );
	range.collapse( true );
	sel.removeAllRanges();
	sel.addRange( range );
	ele.focus();
}

function strstr( haystack, needle, bool )
{
    var pos = 0;
    haystack += "";
    pos = haystack.indexOf( needle );
	if ( pos == -1 )
	{
        return false;
    }
	else
	{
        if ( bool )
		{
            return haystack.substr( 0, pos );
        }
		else
		{
            return haystack.slice( pos );
        }
    }
}

function insertElementAtCursor( ele )
{
    var sel, range, html;
    if ( window.getSelection )
	{
        sel = window.getSelection();
        if ( sel.getRangeAt && sel.rangeCount )
		{
			if( savedRange )
			{
				range = savedRange;
			}
			else
			{
				return;
				range = sel.getRangeAt(0);
			}
            range.deleteContents();
			range.insertNode( ele );
        }
    }
	else if ( document.selection && document.selection.createRange )
	{
        document.selection.createRange().text = text;
    }
}

function saveSelection()
{
    if ( window.getSelection )
	{
        sel = window.getSelection();
        if ( sel.getRangeAt && sel.rangeCount )
		{
			savedRange = sel.getRangeAt(0);
            return sel.getRangeAt(0);
        }
    }
	else if ( document.selection && document.selection.createRange )
	{
        return document.selection.createRange();
    }
    return null;
}

function restoreSelection( range )
{
    if ( range )
	{
        if ( window.getSelection )
		{
            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange( range );
        }
		else if ( document.selection && range.select )
		{
            range.select();
        }
    }
}

/*
var savedRange,isInFocus;

function InsertCodeTag ()
{
	insertAtCursor( 'testing' );
	//alert( document.selection.createRange().text + ' ..' );
	//alert( savedRange + ' .. ' + isInFocus + ' .. ' + document.selection );
}

function insertAtCursor( text )
{
    //var field = document.frmMain.Expression;
	var field = ge( 'ArticleLeadin' );

    if( document.selection )
	{
        var range = document.selection.createRange();
		
        if ( !range || range.parentElement() != field )
		{
            field.focus();
            range = field.createTextRange();
            range.collapse( false );
        }
        range.text = text;
        range.collapse( false );
        range.select();
    }
}

function saveSelection ()
{
    if( window.getSelection ) // non IE Browsers
    {
        savedRange = window.getSelection().getRangeAt(0);
    }
    else if( document.selection ) // IE
    { 
        savedRange = document.selection.createRange();  
    } 
}

function restoreSelection ( id )
{
	if( !id ) return;
    isInFocus = true;
    document.getElementById( id ).focus();
	
    if( savedRange != null )
	{
        if( window.getSelection ) // non IE and there is already a selection
        {
            var s = window.getSelection();
            if( s.rangeCount > 0 )
			{
                s.removeAllRanges();
			}
            s.addRange( savedRange );
        }
        else if( document.createRange ) // non IE and no selection
        {
            window.getSelection().addRange( savedRange );
        }
        else if( document.selection ) // IE
        {
            savedRange.select();
        }
    }
}

// this part onwards is only needed if you want to restore selection onclick
var isInFocus = false;

function onDivBlur ()
{
    isInFocus = false;
}

function cancelEvent ( e )
{
	return;
    if( isInFocus == false && savedRange != null )
	{
        if( e && e.preventDefault )
		{
            e.stopPropagation (); // DOM style ( return false doesn't always work in FF )
            e.preventDefault();
        }
        else
		{
            window.event.cancelBubble = true; // IE stopPropagation
        }
        restoreSelection ();
        return false; // false = IE style
    }
}*/

function replaceSelectionWithHtml(html) {
    var range, html;
    if (window.getSelection && window.getSelection().getRangeAt) {
        range = window.getSelection().getRangeAt(0);
        range.deleteContents();
        var div = document.createElement("div");
        div.innerHTML = html;
        var frag = document.createDocumentFragment(), child;
        while ( (child = div.firstChild) ) {
            frag.appendChild(child);
        }
        range.insertNode(frag);
    } else if (document.selection && document.selection.createRange) {
        range = document.selection.createRange();
        html = (node.nodeType == 3) ? node.data : node.outerHTML;
        range.pasteHTML(html);
    }
}

function initCKEditor( id )
{
	if( id && ge( id ) && ge( id ).tagName == 'TEXTAREA'/* && CKEDITOR && typeof CKEDITOR.instances['ContentEditorCK'] !== 'undefined'*/ )
	{
		// Set content editor and save
		var d = document.createElement( 'div' );
		d.id = 'ContentEditorCK';
		d.innerHTML = ge( id ).value;
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

