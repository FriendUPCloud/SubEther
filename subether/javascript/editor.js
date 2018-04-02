
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

function checkStr( ele, e )
{
    if ( !e ) e = window.event;
    
    if( !ele || !ele.innerHTML ) return;
    
    var str = ele.innerHTML;
    
    if( strstr( str, '*' ) )
    {
        //str = str.split( '*' ).join( '<ul class="checklist"><li id="InFocus" onkeyup="initChecks(this)"></li></ul>' );
        str = str.split( '*' ).join( '<ul class="checklist"><li></li></ul>' );
        
        ele.innerHTML = str;
        
        //ele.removeAttribute( 'contenteditable' );
        
        //initChecks( ge( 'InFocus' ) );
        //SetCursor( ge( 'InFocus' ) );
        
        //var input = ge( 'InFocus' ).getElementsByTagName( 'input' )[0];
        //input.setAttribute( 'type', 'checkbox' );
        //placeCaretAfterNode( input );
        //ge( 'InFocus' ).removeAttribute( 'id' );
        
        //ele.setAttribute( 'contenteditable', 'true' );
    }
    
    var keycode = e.which ? e.which : e.keyCode;
    
    if( keycode != 8 )
    {
        var ul = ele.getElementsByTagName( 'ul' );
        
        if( ul.length > 0 )
        {
            for( a = 0; a < ul.length; a++ )
            {
                var li = ul[a].getElementsByTagName( 'li' );
                
                if( li.length > 0 && ul[a].className == 'checklist' )
                {
                    for( b = 0; b < li.length; b++ )
                    {
                        initChecks( li[b], ele );
                    }
                }
            }
        }
    }
    
    return;
}

function IsEditing ( fid )
{
	if( !fid || !ge( fid ) || strstr( ge( fid ).innerHTML, '*' ) ) return;
	
	ge( fid ).innerHTML = ge( fid ).innerHTML + '*';
}

function CheckBox( editor, ele )
{
    alert( 'jau' );
    if( !ele || !editor || !ge( editor ) ) return;
    var input = ele.getElementsByTagName( 'input' )[0];
    //alert( input );
    ge( editor ).removeAttribute( 'contenteditable' );
    //input.checked = ( input.checked ? false : true );
    /*if( !input.checked )
    {
        input.checked = true;
    }
    else
    {
       input.checked = false; 
    }*/
    input.checked = true;
    ge( editor ).setAttribute( 'contenteditable', 'true' );
}

function editable( mode )
{
    if( !ge( 'CaseEditor' ) ) return;
    if( mode )
    {
        ge( 'CaseEditor' ).setAttribute( 'contenteditable', 'true' );
    }
    else
    {
        ge( 'CaseEditor' ).removeAttribute( 'contenteditable' );   
    }
}

function initChecks( ele, editor )
{
    var str = ele.innerHTML;
    
    if( !strstr( str, '<input' ) )
    {
        str = '<span onclick="editable(true)" contenteditable="false"><input type="checkbox" contenteditable="false" onclick="editable(false)"></span>' + str;
        
        ele.innerHTML = str;
        
        var input = ele.getElementsByTagName( 'input' )[0];
        input.setAttribute( 'type', 'checkbox' );
        var span = ele.getElementsByTagName( 'span' )[0];
        placeCaretAfterNode( span );
    }
}

function SetCursor( ele )
{
    if( !ele ) return;
    //var cursor = document.createElement( 'span' );
    //ele.appendChild( cursor ); 
    ele.setAttribute( 'contenteditable', 'true' );
    ele.focus();
    //ele.innerHTML = ele.innerHTML.split( '<span contenteditable="true">' ).join( '' );
    //ele.innerHTML = ele.innerHTML.split( '</span>' ).join( '' );
    ele.removeAttribute( 'contenteditable' );
}

function placeCaretAfterNode ( node )
{
    if ( typeof window.getSelection != 'undefined' )
	{
        var range = document.createRange();
        range.setStartAfter( node );
        range.collapse( true );
        var selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange( range );
    }
}
