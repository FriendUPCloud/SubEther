
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

/* --- Case List -------------------------------------------------------- */

function OpenTask( pid, el )
{
    var ele;
    if( !ge( 'TaskPID_' + pid ) || !el ) return;
    else ele = ge( 'TaskPID_' + pid );
    
    if( ge( 'CaseEditor' ) && ge( 'CaseEditor' ).name )
    {
        var name = ge( 'CaseEditor' ).name;
        ge( 'CaseEditor' ).removeAttribute( 'name' );
        ge( 'CaseEditor' ).id = name;
    }
    
    if( ele.className.indexOf( 'open' ) > 0 )
    {
        ele.className = ele.className.split( ' open' ).join( '' );
		el.className = '';
    }
    else
    {
        CloseAllTasks();
        ele.className = ele.className + ' open';
		el.className = 'open';
        
        if( ge( 'CaseContentID_' + pid ) )
        {
            ge( 'CaseContentID_' + pid ).name = ge( 'CaseContentID_' + pid ).id;
            ge( 'CaseContentID_' + pid ).id = 'CaseEditor';
        }
    }
}

function CloseAllTasks()
{
    if( !ge( 'AdminContent' ) ) return;
    
    var els = ge( 'AdminContent' ).getElementsByTagName( 'div' );
    if( els.length > 0 )
    {
        for( a = 0; a < els.length; a++ )
        {
            if( els[a].className.indexOf( 'open' ) > 0 )
            {
                els[a].className = els[a].className.split( ' open' ).join( '' );
            }
        }
    }
}

function InitEditMode ( id, type, ele, pid )
{
    if( !id || !type || !ele ) return;
    
    if( ge( 'EditModeActive' ) )
    {
        CloseAdminEditMode();
    }
    
    ele.parentNode.innerHTML = '<input type="text"' + ( pid ? ( ' pid="' + pid + '"' ) : '' ) + ' id="EditModeActive" class="' + type + '_' + id + '" fallback="' + ele.innerHTML + '" value="' + ele.innerHTML + '"/>';
}

function SaveCase ( id, type, ele, param, pid )
{
    if( !id && ge( 'EditModeActive' ) )
    {
        ele = ge( 'EditModeActive' );
        id = ele.className.split( '_' )[1];
        type = ele.className.split( '_' )[0];
		pid = ele.getAttribute( 'pid' );
    }
    else if( !ge( 'EditModeActive' ) && ( !type || !ele ) ) return;
    
    var j = new bajax ();
    j.openUrl ( getPath() + '?component=admin&action=casesave', 'post', true );
    j.addVar ( 'type', type );
    j.addVar ( 'value', ele.value );
    if( id ) j.addVar ( 'cid', id );
    if( param ) j.addVar ( 'param', param ); 
    j.onload = function ()
    {
        var r = this.getResponseText ().split ( '<!--separate-->' );
        if ( r[0] == 'ok' )
        {
            if( param )
            {
                ele.value = '';
                RefreshCaseList( false, pid );
            }
            else
            {
                RefreshCaseList( false, pid );
            }
        }
    }
    j.send ();
}

function DeleteCase ( id, pid )
{
    if( !id && ge( 'EditModeActive' ) )
    {
        id = ge( 'EditModeActive' ).className.split( '_' )[1];
		pid = ge( 'EditModeActive' ).getAttribute( 'pid' );
    }
    else if( !id ) return;
    
    if( confirm( 'Are you sure?' ) )
    {
        var j = new bajax ();
        j.openUrl ( getPath() + '?component=admin&action=casedelete', 'post', true );
        j.addVar ( 'cid', id );
        j.onload = function ()
        {
            var r = this.getResponseText ().split ( '<!--separate-->' );
            if ( r[0] == 'ok' )
            {
                RefreshCaseList( false, pid );
            }
        }
        j.send ();
    }
}

function CompleteTask ( id, ele, pid )
{
    if( !id || !ele ) return;
    
    var j = new bajax ();
    j.openUrl ( getPath() + '?component=admin&action=casecomplete', 'post', true );
    j.addVar ( 'cid', id );
    j.addVar ( 'checked', ( ele.checked ? '100%' : '0%' ) );
    j.onload = function ()
    {
        var r = this.getResponseText ().split ( '<!--separate-->' );
        if ( r[0] == 'ok' )
        {
            RefreshCaseList( false, pid );
        }
    }
    j.send ();
}

function RefreshCaseList ( id, pid )
{
	var plg = false;
    var ele;
    if( !id && ge( 'AdminContent' ) ) ele = ge( 'AdminContent' );
	else if ( !id && ge( 'AdminPlugin' ) )
	{
		plg = true;
		ele = ge( 'AdminPlugin' );
	}
    else ele = ge( 'CaseID_' + id );
    
    var j = new bajax ();
    j.openUrl ( getPath() + '?component=admin&function=case', 'post', true );
    if( id ) j.addVar ( 'cid', id );
	if( pid ) j.addVar ( 'pid', pid );
	if( plg ) j.addVar ( 'plugin', true );
    j.onload = function ()
    {
        var r = this.getResponseText ().split ( '<!--separate-->' );
        if ( r[0] == 'ok' && r[1] )
        {
            ele.innerHTML = r[1];
        }
    }
    j.send ();
}

function createNewTask ( pid )
{
    var ele;
    if( !ge( 'NewTaskPID_' + pid ) ) return;
    else ele = ge( 'NewTaskPID_' + pid );
    
    if( ge( 'TaskPID_' + pid ).className.indexOf( 'open' ) <= 0 )
    {
        OpenTask( pid );
    }
    if( ele.className.indexOf( 'open' ) <= 0 )
    {
        ele.className = ele.className + ' open';
    }
}

function createNewCase ()
{
    var ele;
    if( !ge( 'NewCaseID' ) ) return;
    else ele = ge( 'NewCaseID' );
    
    if( ele.className.indexOf( 'open' ) <= 0 )
    {
        ele.className = ele.className + ' open';
    }
}

function CloseAdminEditMode ()
{
    if( !ge( 'EditModeActive' ) ) return;
    
    var em = ge( 'EditModeActive' );
    
    em.parentNode.innerHTML = '<div onclick="InitEditMode( \'' + em.className.split( '_' )[1] + '\', \'' + em.className.split( '_' )[0] + '\', this )">' + em.getAttribute( 'fallback' ) + '</div>';
}

var checkContent;

function SaveCaseContent()
{
    var content = ge( 'CaseEditor' );
	var html = trim( content.innerHTML );
    var cid = content.name.split( 'CaseContentID_' ).join( '' );
    if( !content || !cid || checkContent == html ) return;
    
    // If Jax is stacking exclude this request until the previous one has loaded
	if( running( 'SaveCaseContent', true ) )
	{
		return;
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=admin&action=casesave', 'post', true );
	j.addVar ( 'cid', cid );
	j.addVar ( 'content', html );
	j.onload = function ()
	{
		// So, tell we're completed
		running( 'SaveCaseContent', false );
		
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
            checkContent = stripSlashes( r[1] );
            
            if( ge( 'CaseNameID_' + cid ) )
            {
                ge( 'CaseNameID_' + cid ).innerHTML = ge( 'CaseNameID_' + cid ).innerHTML.split('*').join('');
            }
            
            //alert( 'Saved' );
		}
	}
	j.send ();
}

function checkStr( ele, e, d )
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
    
    if( keycode != 8 && !d )
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

/* --- Global Events -------------------------------------------------------- */

// Check Global Keys
function checkKeys( e )
{
    if ( !e ) e = window.event;
    var targ = e.srcElement ? e.srcElement : e.target;
    var keycode = e.which ? e.which : e.keyCode;
    switch ( keycode )
    {
        // Esc Key
        case 27:
            CloseAdminEditMode();
            break;
        // Enter key
        case 13:
            SaveCase();
            break;
        // Delete key
        case 46:
            DeleteCase();
            break;
        default: break;
    }
}

// Check Global Cliks
function checkClicks( e )
{
    if ( !e ) e = window.event;
    var targ = e.srcElement ? e.srcElement : e.target;
    if( ge( 'CaseEditor' ) && targ.id != 'CaseEditor' )
	{
        SaveCaseContent();
	}
    if( ge( 'EditModeActive' ) && targ.tagName != 'INPUT' )
    {
        CloseAdminEditMode();
    }
    else if( !targ.parentNode.id ) return;
}

// Global Events
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
