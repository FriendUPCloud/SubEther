
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

function OrderMoveSelectOption( ele, reverse )
{
	if ( !ele || !ele.parentNode.parentNode.parentNode ) return;
	
	var parent = ele.parentNode.parentNode.parentNode;
	
	var sel1 = parent.getElementsByTagName( 'select' )[0];
	var sel2 = parent.getElementsByTagName( 'select' )[1];
	
	var opt = new Array();
	var cur = new Object();
	var rep = new Array();
	
	if ( sel1 && sel2 )
	{
		if ( reverse )
		{
			var opt1 = sel1.getElementsByTagName( 'option' );
			
			if ( opt1.length > 0 )
			{
				for ( var a = 0; a < opt1.length; a++ )
				{
					if ( opt1[a].selected )
					{
						opt1[a].selected = false;
						
						var tag = document.createElement( 'option' );
						tag.value = opt1[a].value;
						tag.innerHTML = opt1[a].innerHTML;
						
						opt1[a].obj = tag;
						
						opt.push( opt1[a] );
					}
				}
			}
			
			if ( opt.length > 0 )
			{
				for ( var b = 0; b < opt.length; b++ )
				{
					sel2.appendChild( opt[b].obj );
					sel1.removeChild( opt[b] );
				}
				
				var sort = sel2.getAttribute( 'sortorder' ).split( ',' );
				var opt2 = sel2.getElementsByTagName( 'option' );
				
				if ( sort.length > 0 && opt2.length > 0 )
				{
					for ( var c = 0; c < opt2.length; c++ )
					{
						var tag = document.createElement( 'option' );
						tag.value = opt2[c].value;
						tag.innerHTML = opt2[c].innerHTML;
						
						opt2[c].obj = tag;
						
						cur[opt2[c].value] = opt2[c];
					}
					
					sel2.innerHTML = '';
					
					for ( var d = 0; d < sort.length; d++ )
					{
						if ( cur[sort[d]] )
						{
							sel2.appendChild( cur[sort[d]].obj );
						}
					}
				}
			}
			
			sel1.removeAttribute( 'selected' );
		}
		else
		{
			var opt2 = sel2.getElementsByTagName( 'option' );
			
			if ( opt2.length > 0 )
			{
				for ( a = 0; a < opt2.length; a++ )
				{
					if ( opt2[a].selected )
					{
						opt2[a].selected = false;
						
						var tag = document.createElement( 'option' );
						tag.value = opt2[a].value;
						tag.innerHTML = opt2[a].innerHTML;
						
						opt2[a].obj = tag;
						
						opt.push( opt2[a] );
					}
				}
			}
			
			if ( opt.length > 0 )
			{
				for ( b = 0; b < opt.length; b++ )
				{
					sel1.appendChild( opt[b].obj );
					sel2.removeChild( opt[b] );
				}
				
				var sort = sel2.getAttribute( 'sortorder' ).split( ',' );
				var opt1 = sel1.getElementsByTagName( 'option' );
				
				if ( sort.length > 0 && opt1.length > 0 )
				{
					for ( var c = 0; c < opt1.length; c++ )
					{
						var tag = document.createElement( 'option' );
						tag.value = opt1[c].value;
						tag.innerHTML = opt1[c].innerHTML;
						
						opt1[c].obj = tag;
						
						cur[opt1[c].value] = opt1[c];
					}
					
					sel1.innerHTML = '';
					
					for ( var d = 0; d < sort.length; d++ )
					{
						if ( cur[sort[d]] )
						{
							sel1.appendChild( cur[sort[d]].obj );
						}
					}
				}
			}
			
			sel2.removeAttribute( 'selected' );
		}
	}
}

function OrderMarkSelectOption( ele )
{
	if ( !ele || !ele.parentNode ) return;
	
	var sel = ele.parentNode;
	
	var cur = ( sel.getAttribute( 'selected' ) ? sel.getAttribute( 'selected' ).split( ',' ) : false );
	var rep = new Array();
	
	var opt = sel.parentNode.getElementsByTagName( 'option' );
	
	if ( opt && opt.length > 0 )
	{
		for ( a = 0; a < opt.length; a++ )
		{
			if ( ele == opt[a] && cur && cur.indexOf( opt[a].value ) >= 0 )
			{
				opt[a].selected = false;
			}
			else if ( opt[a].value && cur && cur.indexOf( opt[a].value ) >= 0 )
			{
				opt[a].selected = true;
				rep.push( opt[a].value );
			}
			else if ( opt[a].value && opt[a].selected )
			{
				opt[a].selected = true;
				rep.push( opt[a].value );
			}
		}
	}
	
	if ( rep.length > 0 )
	{
		sel.setAttribute( 'selected', rep.join( ',' ) );
	}
	else
	{
		sel.removeAttribute( 'selected' );
	}
}
