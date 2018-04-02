
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

function ExportMemberHours( type, cfm )
{
	var data = false;
	var checks = false;
	var hids = false;
	
	//console.log( type + ' --' );
	
	// TODO: Get data from document if there is any unarchived hours to give a message about
	
	if ( ge( 'AdminDate' ) )
	{
		var nav = ge( 'AdminDate' ).getElementsByTagName( 'input' );
		
		if ( nav.length > 0 )
		{
			for ( var a = 0; a < nav.length; a++ )
			{
				if ( nav[a].name && nav[a].value )
				{	
					if ( !data )
					{
						data = new Array();
					}
					
					data.push( nav[a].name + '=' + nav[a].value );
				}
			}
		}
	}
	
	if ( ge( 'AdminContent' ) )
	{
		var check = ge( 'AdminContent' ).getElementsByTagName( 'input' );
		
		if ( check.length > 0 )
		{
			for ( var b = 0; b < check.length; b++ )
			{
				if ( check[b].name && check[b].name == 'uid' && check[b].value && check[b].checked )
				{
					if ( !checks )
					{
						checks = new Array();
					}
					
					checks.push( check[b].value );
					
					if ( ge( 'MemberDetails_' + check[b].value ) )
					{
						var inp = ge( 'MemberDetails_' + check[b].value ).getElementsByTagName( 'input' );
						
						if ( inp.length > 0 )
						{
							for ( var c = 0; c < inp.length; c++ )
							{
								if ( inp[c].type == 'checkbox' && inp[c].parentNode.className.indexOf( 'checked' ) >= 0 && inp[c].checked && !inp[c].disabled )
								{
									if ( !hids )
									{
										hids = new Array();
									}
									
									if ( inp[c].value.split('_')[1] )
									{
										hids.push( inp[c].value.split('_')[1] );
									}
								}
							}
						}
					}
				}
			}
		}
	}
	
	// TODO: Don't open window if there is no data ...
	
	//if ( hids && confirm( 'Vil du arkivere disse timene?' ) )
	if ( hids )
	{
		if ( cfm || confirmCustom( 'Vil du arkivere disse timene?', function(){ ExportMemberHours( type, true ); } ) )
		{
			window.open( getPath() + '?component=admin&action=memberexport&type=' + type + ( data ? ( '&' + data.join( '&', data ) ) : '' ) + ( checks ? ( '&mid=' + checks.join( ',', checks ) ) : '' ) + '&hid=' + hids.join( ',', hids ) + '&finished=true', '_blank' );
			
			document.location = document.location.href.split( '?' )[0] + '?r=members' + ( data ? ( '&' + data.join( '&', data ) ) : '' );
		}
	}
	else
	{
		window.open( getPath() + '?component=admin&action=memberexport&type=' + type + ( data ? ( '&' + data.join( '&', data ) ) : '' ) + ( checks ? ( '&mid=' + checks.join( ',', checks ) ) : '' ), '_blank' );
		
		document.location = document.location.href.split( '?' )[0] + '?r=members' + ( data ? ( '&' + data.join( '&', data ) ) : '' );
	}
	
	//document.location = ( getPath() + '?component=admin&action=memberexport' );
	//window.open( getPath() + '?component=admin&action=memberexport&type=' + type + ( data ? ( '&' + data.join( '&', data ) ) : '' ) + ( checks ? ( '&mid=' + checks.join( ',', checks ) ) : '' ) + ( confirm( 'Do you want to archive these hours?' ) ? '&finished=true' : '' ), '_blank' );
	
	// TODO: Document reload with current data after submit of this request to se updated data
	
	//location.reload();
	
	/*var j = new bajax ();
	j.openUrl ( getPath() + '?component=admin&action=memberexport', 'post', true );
	//j.addVar ( 'data', JSON.stringify( data ) );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			//
		}
		else alert( this.getResponseText() );
	}
	j.send ();*/
}

function FilterMemberHours()
{
	if ( !ge( 'AdminDate' ) || !ge( 'AdminContent' ) ) return;
	
	var inp = ge( 'AdminDate' ).getElementsByTagName( 'input' );
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=admin&function=members', 'post', true );
	if ( inp.length > 0 )
	{
		for ( a = 0; a < inp.length; a++ )
		{
			if ( inp[a].name )
			{
				j.addVar ( inp[a].name, inp[a].value );
			}
		}
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );	
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'AdminContent' ).innerHTML = r[1];
		}
	}
	j.send ();
}

function EditMemberHours( uid, fromdate, todate, update )
{
	if( !uid ) return;
	
	if( !update && ge( 'MemberDetails_' + uid ).className.indexOf( 'open' ) >= 0 )
	{
		ge( 'MemberDetails_' + uid ).className = ge( 'MemberDetails_' + uid ).className.split( ' open' ).join( '' );
		return;
	}
	else if( !update && ge( 'MemberDetails_' + uid ).innerHTML != '' )
	{
		ge( 'MemberDetails_' + uid ).className = ge( 'MemberDetails_' + uid ).className.split( ' open' ).join( '' ) + ' open';
		return;
	}
	
	if ( ge( 'uid_' + uid ) && ge( 'uid_' + uid ).className.indexOf( 'loading' ) < 0 )
	{
		ge( 'uid_' + uid ).className = ge( 'uid_' + uid ).className.split( ' loading' ).join( '' ) + ' loading';
	}
	
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=orders&function=orderdetails&mode=members' + ( getPathVar( 's' ) ? '&s=' + getPathVar( 's' ) : '&s=1' ), 'post', true );
	j.addVar ( 'uid', uid );
	if ( fromdate ) j.addVar ( 'fromdate', fromdate );
	if ( todate ) j.addVar ( 'todate', todate );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'MemberDetails_' + uid ).className = ge( 'MemberDetails_' + uid ).className.split( ' open' ).join( '' ) + ' open';
			ge( 'MemberDetails_' + uid ).innerHTML = r[1];
		}
		else
		{
			ge( 'MemberDetails_' + uid ).className = ge( 'MemberDetails_' + uid ).className.split( ' open' ).join( '' );
			ge( 'MemberDetails_' + uid ).innerHTML = '';
			console.log( this.getResponseText() );
		}
		
		if ( ge( 'uid_' + uid ) )
		{
			ge( 'uid_' + uid ).className = ge( 'uid_' + uid ).className.split( ' loading' ).join( '' );
		}
	}
	j.send ();
}

function memIsEdit( ele )
{
	if ( !ele || !ele.parentNode.parentNode ) return;
	
	var parent = ele.parentNode.parentNode;
	
	if ( parent.className.indexOf( 'editing' ) < 0 )
	{
		parent.className = parent.className.split( 'editing' ).join( '' ) + 'editing';
	}
}

function saveMemberData( aid, ele )
{
	if ( !aid || !ele || !ele.parentNode.parentNode ) return;
	
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
		j.openUrl ( getPath() + '?component=admin&action=membersave', 'post', true );
		
		if ( aid )
		{
			j.addVar ( 'ID', aid );
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
				parent.className = parent.className.split( 'editing' ).join( '' );
				
				console.log( 'saved --- ' + r[1] );
			}
			else if ( r[0] == 'fail' && r[1] )
			{
				alert( r[1] );
			}
		}
		j.send ();
	}
}
