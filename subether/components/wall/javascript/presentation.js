
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

// TODO: Support stop slideshow when showing movie or some other action and then start it with changepage function again. Or when movie stops.

var SlidePresent = new Object();

SlidePresent = {
	
	current: false,
	
	// Initialize
	init: function( ele )
	{
		this.current = ele;
		
		SlidePresent[ele] = document.getElementById ( ele );
		var _this = SlidePresent[ele];
		_this.ele = ele;
		
		if( !_this ) return;
		
		_this.parent = this;
		_this.index = 0;
		var elems = _this.getElementsByTagName ( 'div' );
		
		_this.PageContainer;
		_this.ArrowContainer;
		_this.ImageContainer;
		
		var i = 0;
		
		// Create pages for each element
		for ( var a = 0; a < elems.length; a++ )
		{
			if ( !_this.PageContainer && elems[a].className.indexOf( 'Pages' ) >= 0 )
			{
				_this.PageContainer = elems[a];
			}
			
			if ( !_this.ArrowContainer && elems[a].className.indexOf( 'Arrows' ) >= 0 )
			{
				_this.ArrowContainer = elems[a];
			}
			
			if ( elems[a].className.indexOf( 'messagebox' ) >= 0 )
			{
				if ( i == 0 )
				{
					elems[a].className.split( ' current' ).join( '' ) + ' current';
				}
				
				if ( !_this.ImageContainer )
				{
					_this.ImageContainer = elems[a].parentNode;
				}
				
				i++;
			}
		}
		
		if ( i <= 1 )
		{
			if ( _this.interval )
			{
				clearTimeout( _this.interval );
			}
			
			return;
		}
		
		// Arrow navigation
		
		if ( _this.ArrowContainer )
		{
			var an = document.createElement ( 'div' ); 
			an.className = 'ArrowNext';
			an.innerHTML = '<span>»</span>';
			an.object = _this;
			
			// Click arrownext
			
			an.onclick = function () 
			{
				var iframe = this.object.ImageContainer.getElementsByTagName( 'iframe' );
				
				if ( iframe.length > 0  )
				{
					for ( a = 0; a < iframe.length; a++ )
					{
						if ( iframe[a].className.indexOf( 'active' ) >= 0 )
						{
							iframe[a].className = iframe[a].className.split( 'active' ).join( '' );
						}
					}
				}
				
				this.object.parent.changePage ( this.object.index+1, false, this.object.ele );
			}
			
			var ap = document.createElement ( 'div' ); 
			ap.className = 'ArrowPrev';
			ap.innerHTML = '<span>«</span>';
			ap.object = _this;
			
			// Click arrowprev
			
			ap.onclick = function () 
			{
				var iframe = this.object.ImageContainer.getElementsByTagName( 'iframe' );
				
				if ( iframe.length > 0  )
				{
					for ( a = 0; a < iframe.length; a++ )
					{
						if ( iframe[a].className.indexOf( 'active' ) >= 0 )
						{
							iframe[a].className = iframe[a].className.split( 'active' ).join( '' );
						}
					}
				}
				
				this.object.parent.changePage ( this.object.index-1, false, this.object.ele );
			}
			
			_this.Arrows = [ ap, an ];
			
			_this.ArrowContainer.innerHTML = '';
			
			_this.ArrowContainer.appendChild ( ap ); 
			_this.ArrowContainer.appendChild ( an );
		}
		
		_this.parent.update();
		
		if ( document.getElementById(_this.id) && document.getElementById(_this.id).parent )
		{
			if ( _this.interval )
			{
				clearTimeout( _this.interval );
			}
			
			_this.interval = setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.changePage(\'next\');', 10000 );
		}
	},
	
	update: function ( ele )
	{
		if ( ele )
		{
			this.current = ele;
		}
		
		var _this = SlidePresent[this.current];
		
		
		// Update list of images
		
		var i = 0;
		
		if ( _this.ImageContainer )
		{
			_this.Images = new Array ();
			_this.Pages = new Array ();
			
			var elems = _this.ImageContainer.getElementsByTagName ( 'div' );
			
			for ( var a = 0; a < elems.length; a++ )
			{
				if ( elems[a].className.indexOf( 'messagebox' ) >= 0 )
				{
					_this.Images.push ( elems[a] );
					
					var Page = document.createElement ( 'span' );
					Page.innerHTML = (i+1);
					Page.object = _this;
					Page.index = i;
					Page.className = ( elems[a].className.indexOf( 'current' ) >= 0 ? 'PageActive' : 'Page' );
					
					Page.onclick = function ()
					{
						var iframe = this.object.ImageContainer.getElementsByTagName( 'iframe' );
						
						if ( iframe.length > 0  )
						{
							for ( a = 0; a < iframe.length; a++ )
							{
								if ( iframe[a].className.indexOf( 'active' ) >= 0 )
								{
									iframe[a].className = iframe[a].className.split( 'active' ).join( '' );
								}
							}
						}
						
						this.object.parent.changePage( this.object.index, false, this.object.ele );
					}
					
					_this.Pages.push ( Page );
					
					i++;
				}
			}
			
			// Update liste og pages
			
			if ( _this.PageContainer && _this.Pages.length )
			{
				_this.PageContainer.innerHTML = '';
				
				for ( var b = 0; b < _this.Pages.length; b++ )
				{
					_this.PageContainer.appendChild( _this.Pages[b] );
				}
				
				var Total = document.createElement ( 'span' );
				Total.innerHTML = ' of ' + _this.Pages.length;
				Total.className = 'PageTotal';
				
				_this.PageContainer.appendChild( Total );
			}
		}
		
	},
	
	changePage: function ( ind, useid, ele, del )
	{
		if ( ele )
		{
			this.current = ele;
		}
		
		var _this = SlidePresent[this.current];
		
		if ( _this.timeout )
		{
			clearTimeout( _this.timeout );
		}
		
		_this.timeout = false;
		
		if ( ind == 'next' ) 
		{
			ind = _this.index + 1;
		}
		
		if ( _this.start > 0 ) 
		{
			return;
		}
		
		ind = parseInt( ind );
		
		_this.pindex = _this.index;
		
		var iframe = _this.ImageContainer.getElementsByTagName( 'iframe' );
		
		if ( iframe.length > 0  )
		{
			for ( f = 0; f < iframe.length; f++ )
			{
				if ( iframe[f].className.indexOf( 'active' ) >= 0 )
				{
					return;
				}
			}
		}
		
		if ( ind >= _this.Images.length ) 
		{
			ind = 0;
		}
		else if ( ind < 0 ) 
		{
			ind = _this.Images.length - 1;
		}
		if ( ind == _this.index ) 
		{
			_this.parent.tweener ( false, ele );
			return;
		}
		
		_this.index = ind;
		_this.parent.tweener ( false, ele );
		
		if ( document.getElementById(_this.id) && document.getElementById(_this.id).parent )
		{
			if ( _this.interval )
			{
				clearTimeout( _this.interval );
			}
			
			//_this.interval = setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.update();document.getElementById(\''+_this.id+'\').parent.changePage(\'next\');', 10000 );
			_this.interval = setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.changePage(\'next\');', 10000 );
		}
	},
	
	tweener: function ( running, ele )
	{
		if ( ele )
		{
			this.current = ele;
		}
		
		var _this = SlidePresent[this.current];
		
		if ( !running )
		{
			_this.start = ( new Date () ).getTime ();
			setOpacity ( _this.Images[_this.index], 0 );
			_this.Images[_this.index].style.right = '0%';
			
			for ( var i = 0; i < _this.Images.length; i++ )
			{
				if ( _this.Images[i] && i != _this.index && i != _this.pindex )
				{
					setOpacity ( _this.Images[i], 0 );
					
					_this.Images[i].style.visibility = 'hidden';
				}
			}
			
			return setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.tweener(1)', 5 );
		}
		else
		{
			var p1 = ( ( new Date () ).getTime () - _this.start ) / 1000;
			
			var p2 = ( 1 - p1 );
			
			if ( p1 >= 1 ) 
			{
				p1 = 1;
			}
			
			_this.Images[_this.pindex].style.visibility = 'visible';
			
			if ( p2 <= 0 ) 
			{
				p2 = 0;
				
				_this.Images[_this.pindex].style.visibility = 'hidden';
			}
			
			_this.Images[_this.index].style.visibility = 'visible';
			
			var pp1 = Math.pow ( Math.sin ( p1 * 0.5 * Math.PI ), 3 );
			setOpacity ( _this.Images[_this.index], pp1 );
			
			var pp2 = Math.pow ( Math.sin ( p2 * 0.5 * Math.PI ), 3 );
			setOpacity ( _this.Images[_this.pindex], pp2 );
			
			if ( p1 < 1 )
			{
				_this.tm = setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.tweener(1)', 5 );
			}
			else 
			{
				_this.Images[_this.pindex].style.right = '100%';
				_this.start = false;
				clearTimeout ( _this.tm ); 
				_this.tm = 0;
				
				for ( var i = 0; i < _this.Pages.length; i++ )
				{
					if ( _this.Images[i] && i == _this.index )
					{
						_this.Pages[i].className = 'PageActive';
						_this.Images[i].className = _this.Images[i].className.split( ' current' ).join( '' ) + ' current';
						
					}
					else if ( _this.Images[i] ) 
					{
						_this.Pages[i].className = 'Page';
						_this.Images[i].className = _this.Images[i].className.split( ' current' ).join( '' );
					}
					
				}
			}
			
			return true;
		}
	}
	
}

// TODO: onmouseover stop scroll and onmouseout continue

var ScrollPresent = new Object();

ScrollPresent = {
	
	current: false,
	
	// Initialize
	init: function( ele )
	{
		this.current = ele;
		
		ScrollPresent[ele] = document.getElementById ( ele );
		var _this = ScrollPresent[ele];
		_this.ele = ele;
		
		if( !_this ) return;
		
		_this.parent = this;
		_this.index = 0;
		var elems = _this.getElementsByTagName ( 'div' );
		
		_this.TextContainer;
		
		for ( var a = 0; a < elems.length; a++ )
		{
			if ( !_this.TextContainer && elems[a].className.indexOf( 'TextContainer' ) >= 0 )
			{
				_this.TextContainer = elems[a];
			}
		}
		
		if ( _this.TextContainer && document.getElementById(_this.id).offsetWidth > _this.TextContainer.offsetWidth )
		{
			if ( _this.interval )
			{
				clearTimeout( _this.interval );
			}
			
			return;
		}
		
		if ( document.getElementById(_this.id) && document.getElementById(_this.id).parent )
		{
			if ( _this.interval )
			{
				clearTimeout( _this.interval );
			}
			
			_this.interval = setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.tweener();', 10000 );
		}
	},
	
	tweener: function ( running, ele )
	{
		if ( ele )
		{
			this.current = ele;
		}
		
		var _this = ScrollPresent[this.current];
		
		if ( !running )
		{
			_this.start = ( new Date () ).getTime ();
			
			_this.interval = setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.tweener(1)', 5 );
		}
		else
		{
			var mode;
			
			//var p1 = ( ( new Date () ).getTime () - _this.start ) / 10;
			var p1 = ( ( new Date () ).getTime () - _this.start ) / 30;
			
			var p2 = ( document.getElementById(_this.id).offsetWidth - p1 );
			
			if ( _this.TextContainer.style.left && _this.TextContainer.style.left.split( 'px' ).join( '' ) > 0 ) 
			{
				mode = 1;
				
				_this.TextContainer.style.left = p2 + 'px';
			}
			else
			{
				mode = 2;
				
				_this.TextContainer.style.left = '-' + p1 + 'px';
			}
			
			if ( mode == 1 && p2 && p2 <= 0.5 )
			{
				_this.TextContainer.style.left = '0px';
				_this.interval = setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.tweener()', 5 );
			}
			else if ( mode == 2 && p1 >= _this.TextContainer.offsetWidth )
			{
				_this.TextContainer.style.left = document.getElementById(_this.id).offsetWidth + 'px';
				_this.interval = setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.tweener()', 5 );
			}
			else
			{
				_this.interval = setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.tweener(1)', 5 );
			}
		}
	}
}
