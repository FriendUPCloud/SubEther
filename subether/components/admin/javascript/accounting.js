
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

function deleteAccountingSettings( ele, cfm )
{
	if ( !ele || !ele.parentNode.parentNode ) return;
	
	var parent = ele.parentNode.parentNode;
	
	//if ( parent.id && confirm( 'are you sure?' ) )
	if ( parent.id && ( cfm || confirmCustom( 'are you sure?', 'deleteAccountingSettings('+ele+',true)' ) ) )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=admin&action=accountingdelete', 'post', true );
		j.addVar ( 'ID', parent.id.split( '#' )[1] );
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				parent.parentNode.removeChild( parent );
				
				console.log( 'deleted --- ' + parent.id );
			}
			else if( r[0] == 'failed' && r[1] )
			{
				alert( r[1] );
			}
		}
		j.send ();
	}
}

function accIsEdit( ele )
{
	if ( !ele || !ele.parentNode.parentNode ) return;
	
	var parent = ele.parentNode.parentNode;
	
	if ( parent.className.indexOf( 'editing' ) < 0 )
	{
		parent.className = parent.className.split( 'editing' ).join( '' ) + 'editing';
	}
}

function saveAccountingSettings( ele )
{
	if ( !ele || !ele.parentNode.parentNode ) return;
	
	var vars = new Object();
	
	var parent = ele.parentNode.parentNode;
	
	var inp = parent.getElementsByTagName( '*' );
	
	if ( inp.length > 0 )
	{
		for ( a = 0; a < inp.length; a++ )
		{
			if ( ( inp[a].tagName == 'INPUT' || inp[a].tagName == 'SELECT' ) && inp[a].type != 'checkbox' && inp[a].name )
			{
				vars[inp[a].name] = inp[a].value;
			}
		}
	}
	
	if ( vars )
	{
		var j = new bajax ();
		j.openUrl ( getPath() + '?component=admin&action=accountingsave', 'post', true );
		
		if ( parent.id )
		{
			j.addVar ( 'ID', parent.id.split( '#' )[1] );
		}
		
		for ( var key in vars )
		{
			j.addVar ( key, vars[key] );
		}
		
		j.onload = function ()
		{
			var r = this.getResponseText ().split ( '<!--separate-->' );
			if ( r[0] == 'ok' && r[1] )
			{
				if ( !parent.id )
				{
					parent.id = 'aid#' + r[1];
				}
				parent.className = parent.className.split( 'editing' ).join( '' );
				
				console.log( 'saved --- ' + 'aid#' + r[1] );
			}
			else if ( r[0] == 'fail' && r[1] )
			{
				alert( r[1] );
			}
		}
		j.send ();
	}
}

function checkSettingsInputs( ele )
{
	if ( ge( 'accountingsettings' ) )
	{
		var rows = ge( 'accountingsettings' ).getElementsByTagName( 'tr' );
		
		if ( rows.length > 0 )
		{
			var last = 0;
			
			var delt = new Object();
			var keep = new Object();
			
			for ( a = 0; a < rows.length; a++ )
			{
				if ( a == 0 ) continue;
				
				var found = false;
				
				var inp = rows[a].getElementsByTagName( 'input' );
				
				if ( inp.length > 0 )
				{
					for ( b = 0; b < inp.length; b++ )
					{
						if ( inp[b].type == 'text' && inp[b].value )
						{
							found = rows[a];
						}
						if ( inp[b].type == 'checkbox' && inp[b].checked )
						{
							found = rows[a];
						}
					}
				}
				
				if ( found )
				{
					last = a;
					
					keep[a] = rows[a];
				}
				else
				{
					delt[a] = rows[a];
				}
			}
			
			if ( delt )
			{
				for ( var k in delt )
				{
					if ( k > (last+1) )
					{
						ge( 'accountingsettings' ).removeChild( delt[k] );
					}
				}
			}
			
			if ( typeof delt[last+1] == 'undefined' )
			{
				var tr = document.createElement( 'tr' );
				tr.innerHTML = '<td class="c1"><input type="checkbox" name="Temp" value="1"/></td>' +
				'<td class="c2"><input type="text" name="VisualID" onclick="checkSettingsInputs()" onchange="saveAccountingSettings(this)" onkeydown="accIsEdit(this)"/></td>' + 
				'<td class="c3"><select name="Type" onchange="saveAccountingSettings(this)"><option>auto</option></select></td>' +
				'<td class="c4"><input type="text" name="Name" onclick="checkSettingsInputs()" onchange="saveAccountingSettings(this)" onkeydown="accIsEdit(this)"/></td>' + 
				'<td class="c5"><input type="text" name="Amount" onclick="checkSettingsInputs()" onchange="saveAccountingSettings(this)" onkeydown="accIsEdit(this)"/></td>' + 
				'<td class="c6"><span onclick="deleteAccountingSettings(this)"> [x] </span></td>';
				ge( 'accountingsettings' ).getElementsByTagName('tbody')[0].appendChild( tr );
			}
		}
	}
}
