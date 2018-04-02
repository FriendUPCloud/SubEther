
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

var jaxInterval = [];
var jaxIntervalInstance = [];

// Ex. var x = new jaxqueue ( 'refreshPrivChat( u, n, m )', 3000, u ); TODO: create support for vars inside (  ) on funcName when running the function, and also have to make a clearinterval function in this class to remove from queue
var jaxqueue = function ( funcName, funcIntval, funcNum )
{
	var Name;
	var Intval;
	var Num;
	var Url;
	var Mode;
	var funVars;
	var postVars;
	
	this.Intval = 10000;
	
	if( funcName )
	{
		this.Name = funcName;
	}
	if( funcIntval )
	{
		this.Intval = funcIntval;
	}
	if( funcNum )
	{
		this.Num = funcNum;
		this.Name = this.Name + '_' + funcNum;
	}
}
jaxqueue.prototype.addVar = function ( varName, varValue )
{
	if( !this.postVars )
	{
		this.postVars = new Object ();
	}
	
	// Set post vars that is to be sendt with the bajax to server
	this.postVars[ varName ] = varValue;
}
jaxqueue.prototype.fncVar = function ( varName, varValue )
{
	if( !this.funcVars )
	{
		this.funcVars = new Object ();
	}
	
	// Set post vars that is to be sendt with the bajax to server
	this.funcVars[ varName ] = varValue;
}
jaxqueue.prototype.addUrl = function ( url_, mode_ )
{
	this.Url = url_;
	
	// If mode is set, run a bajax request seperate from the jaxqueue for faster response
	if( mode_ )
	{
		this.Mode = 'refresh';
	}
}
jaxqueue.prototype.onload = function ( data_ )
{
	// If we have data from php return it to function
	if( data_ )
	{
		this.onLoaded = data_;
	}
}
jaxqueue.prototype.save = function ()
{
	var queueName = 'jaxqueue' + this.Intval;
	
	// If we have no queue create the first queue and set interval on it
	if( !jaxInterval[this.Intval] )
	{
		jaxInterval[this.Intval] = new Object ();
		jaxInterval[this.Intval].list = new Array ();
		jaxInterval[this.Intval].intval = this.Intval;
		jaxInterval[this.Intval].num = this.Num;
		// Set up the queue function as default or defined by a seperate bajax request for faster response via funcName
		jaxInterval[this.Intval].checkQueue = function ( funcName )
		{
			// If special request is not defined and jaxqueue is running exclude this request until the previous one has loaded
			if( !funcName && getRunning( queueName ) )
			{
				console.log( 'We dropped this one..' + queueName );
				return;
			}
			// Set jaxqueue as running
			setRunning( queueName, true );
			
			// If we have a queue list allready set up
			if( this.list.length > 0 )
			{
				for ( var a = 0; a < this.list.length; a++ )
				{
					if( funcName && this.list[a].Name == funcName )
					{
						// Make a new list based on function name
						funcName = new Array ();
						funcName.push( this.list[a] );
					}
					else
					{
						// If num is defined remove it from name to run the right function
						var func = ( this.num ? this.list[a].Name.split('_')[0] : this.list[a].Name );
						
						if( this.funcVars )
						{
							// Refresh the functions in the list
							window[func]( this.funcVars.join(',') );
						}
						else
						{
							// Refresh the functions in the list
							window[func]();
						}
					}
				}
			}
			
			// Send the data to the server and wait for response
			var j = new bajax ();
			// If we have a seperate bajax request mark it
			j.openUrl ( getPath() + '?function=checkqueue&global=true' + ( funcName ? ( '&refresh=' + funcName[0].Name ) : '&int=' + this.intval ), 'post', true );
			// If we have a seperate bajax request only send the defined data not the whole list
			j.addVar ( 'jaxqueue', JSON.stringify ( funcName ? funcName : this.list ) );
			j.list = this.list;
			j.onload = function ()
			{
				var res = this.getResponseText ().split ( '<!--jaxqueue-->' );
				for ( var b = 0; b < res.length; b++ )
				{
					// get type from result list
					var rnam = res[b].split ( '<!--jsfunction-->' )[0];
					var rdat = res[b].split ( '<!--jsfunction-->' )[1];
					
					for ( var a = 0; a < this.list.length; a++ )
					{
						// If function name is found in return data send data back to the function in the jaxqueue list
						if ( rnam == this.list[a].Name )
						{
							this.list[a].onLoaded ( rdat );
						}
					}
				}
				
				// Set to completed
				setRunning( queueName, false );
			}
			j.send ();
		}
		
		// Set interval for how often this bajax will run
		// Clear previous if running
		if( typeof( jaxIntervalInstance[this.Intval] ) != 'undefined' && jaxIntervalInstance[this.Intval] )
		{
			clearInterval( jaxIntervalInstance[this.Intval] );
			jaxIntervalInstance[this.Intval] = false;
		}
		jaxIntervalInstance[this.Intval] = setInterval ( 'jaxInterval['+this.Intval+'].checkQueue()', this.Intval );
	}
	
	// If function name is defined create or update jaxqueue
	if( this.Name )
	{
		var found = false;
		
		if( jaxInterval[this.Intval].list.length > 0 )
		{
			for ( var a = 0; a < jaxInterval[this.Intval].list.length; a++ )
			{
				// Update the jaxqueue with new data from function if its found in the list
				if ( this.Name == jaxInterval[this.Intval].list[a].Name )
				{
					jaxInterval[this.Intval].list[a] = this; found = true;
				}
			}
		}
		
		// If we didn't find the function in the jaxqueue list add it to the list
		if( !found ) jaxInterval[this.Intval].list.push( this );
		
		// If mode is set for refresh run a seperate bajax request only for this function for faster response
		if( this.Mode )
		{
			jaxInterval[this.Intval].checkQueue( this.Name );
		}
	}
}
