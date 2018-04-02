
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

var Showrooms = new Object();

Showroom = {
	
	current: false,
	
	// Initialize
	init: function( ele, w, h )
	{
		this.current = ele;
		
		Showrooms[ele] = document.getElementById ( ele );
		var _this = Showrooms[ele];
		_this.ele = ele;
		//_this.container = document.getElementById ( ele );
		if( !_this ) return;
		
		//_this.object = _this;
		_this.parent = this;
		_this.w = w; 
		_this.h = h;
		_this.index = 0;
		var imgs = _this.getElementsByTagName ( 'span' );
		var imgA = new Array ();
		_this.pages = new Array ();
		var p = document.createElement ( 'div' );
		p.className = 'Pages';
		_this.direction = false;
		_this.start = false;
		var l = imgs.length;
		
		// Create pages for each element
		for ( var a = 0; a < l; a++ )
		{
			var d = document.createElement ( 'div' );
			d.innerHTML = '<span>' + (a+1) + '</span>';
			d.object = _this;
			d.parent = _this.parent;
			d.index = a;
			
			d.onclick = function () 
			{ 
				clearInterval ( this.object.interval ); 
				this.parent.changePage( this.object.index, false, this.object.ele ); 
			}
			
			d.className = 'Page';
			p.appendChild ( d );
			_this.pages.push ( d );
			imgA.push ( imgs[a] );
			
			if ( a == 0 ) 
			{
				d.className = 'PageActive';
			}
		}
		
		// Create info about how many pages we have
		var d = document.createElement ( 'div' );
		d.innerHTML = '<span>of ' + l + '</span>';
		d.className = 'PageTotal';
		p.appendChild ( d );
		
		// Arrow navigation
		var ar = document.createElement ( 'div' );
		ar.className = 'Arrows';
		var an = document.createElement ( 'div' ); 
		an.className = 'ArrowNext';
		var ap = document.createElement ( 'div' ); 
		ap.className = 'ArrowPrev';
		an.object = _this;
		an.parent = _this.parent;
		ap.object = _this;
		ap.parent = _this.parent;
		
		// Click arrownext
		an.onclick = function () 
		{ 
			clearInterval ( this.object.interval ); 
			this.parent.changePage ( this.object.index+1, false, this.object.ele ); 
		}
		
		// Click arrowprev
		ap.onclick = function () 
		{ 
			clearInterval ( this.object.interval ); 
			this.parent.changePage ( this.object.index-1, false, this.object.ele ); 
		}
		
		// Show arrows gfx and add stuff to dom
		an.innerHTML = '<span>»</span>';
		ap.innerHTML = '<span>«</span>';
		ar.appendChild ( ap ); 
		ar.appendChild ( an );
		_this.appendChild ( p );
		_this.appendChild ( ar );
		_this.arrows = [ ap, an ];
		
		// Create image container and description
		var i = document.createElement ( 'div' );
		i.className = 'ImageContainer';
		var k = document.createElement ( 'div' );
		k.className = 'ImageDescriptions';
		_this.images = new Array ();
		_this.descriptions = new Array ();
		
		// Create images
		for ( var a = 0; a < l; a++ )
		{
			if( !w ) w = imgs[a].getAttribute ( 'width' );
			if( !h ) h = imgs[a].getAttribute ( 'height' );
			
			var d = document.createElement ( 'div' );
			
			if ( a == 0 ) 
			{
				d.style.right = '0%';
			}
			if ( a == 0 )
			{
				d.className = 'ImageCurrent'; 
			}
			else 
			{
				d.className = 'Image';
			}
			
			var ds = document.createElement ( 'div' );
			ds.className = a == 0 ? 'DescriptionCurrent' : 'Description';
			_this.ds = ds;
			
			// Create image placeholder
			var o = new Object ();
			o.src = imgs[a].getAttribute ( 'title' );
			o.fid = imgs[a].getAttribute ( 'fid' );
			o.unique = imgs[a].getAttribute ( 'unique' );
			o.description = imgs[a].getAttribute ( 'description' );
			d.fid = o.fid;
			d.unieue = o.unique;
			d.notLoaded = o;
			d.isLoaded = false;
			
			if ( imgs[a].getAttribute ( 'extended' ).length )
			{
				ds.innerHTML += imgs[a].getAttribute ( 'extended' );
			}
			
			i.appendChild ( d );
			d.object = _this;
			d.parent = this;
			
			d.onclick = function ()
			{
				clearInterval ( this.object.interval );
				
				if ( this.object.direction == 'next' )
				{
					this.parent.changePage ( this.object.index - 1, false, this.object.ele );
				}
				else if ( this.object.direction == 'prev' )
				{
					this.parent.changePage ( this.object.index + 1, false, this.object.ele );
				}
			}
			
			_this.images.push ( d );
			
			if ( _this.images.length > 1 )
			{
				setOpacity ( d, 0 );
			}
			
			_this.descriptions.push ( ds );
			k.appendChild ( ds );
		}
		
		for ( var a in imgA )
		{
			if ( imgA[a].parentNode == _this )
			{
				_this.removeChild ( imgA[a] );
			}			
		}
		
		_this.appendChild ( i );
		_this.appendChild ( k );
		//_this.style.width = w + 'px';
		//_this.style.height = h + 'px';
		_this.className = 'Showroom';
		
		_this.timeout = false;
		
		_this.onmouseout = function ()
		{
			var pr = this.arrows[0];
			var nr = this.arrows[1];
			pr.className = 'ArrowPrev';
			nr.className = 'ArrowNext';
			this.direction = false;
		}
		
		// Load first image
		this.ShowroomLoad( 0, _this.id );
	},
	
	changePage: function ( ind, useid, ele, del )
	{
		if ( ele )
		{
			this.current = ele;
		}
		
		var _this = Showrooms[this.current];
		
		if ( _this.timeout )
		{
			clearTimeout( _this.timeout );
		}
		_this.timeout = false;
		
		if( useid && _this.images.length > 0 )
		{
			var pos = 0;
			
			for( a = 0; a < _this.images.length; a++ )
			{
				if( _this.images[a].fid && ind == _this.images[a].fid )
				{
					pos = a;
				}
			}
			
			ind = pos;
		}
		
		
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
		
		if ( ind >= _this.images.length ) 
		{
			ind = 0;
		}
		else if ( ind < 0 ) 
		{
			ind = _this.images.length - 1;
		}
		if ( ind == _this.index ) 
		{
			_this.parent.tweener ( false, ele );
			return;
		}
		
		// Only change page if image is loaded, else delay operation
		if ( _this.images && _this.images[ind] && _this.images[ind].firstChild && _this.images[ind].isLoaded )
		{
			_this.index = ind;
			_this.parent.tweener ( false, ele );
		}
		// Load
		else if ( _this.images && _this.images[ind].notLoaded )
		{
			_this.parent.ShowroomLoad( ind, _this.id );
			_this.timeout = setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.changePage(\''+ind+'\')', 10 );
		}
		// Just reload
		else
		{
			_this.timeout = setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.changePage(\''+ind+'\')', 10 );
		}
		
		if ( del && _this.index )
		{
			console.log( _this.index );
			
			// TODO: Fix this so it's possible to delete image without the tweener getting stuck
			
			//_this.parent.deleteImage( _this.index, _this.id );
			
			console.log( ind );
		}
	},
	
	deleteImage: function ( imgnr, ele )
	{
		if ( ele )
		{
			this.current = ele;
		}
		
		var _this = Showrooms[this.current];
		
		var imgs = new Array();
		
		if ( _this.images && _this.images.length > 0 )
		{
			for ( var c = 0; c < _this.images.length; c++ )
			{
				if ( _this.images[c] && _this.images[imgnr] && imgnr != c )
				{
					imgs.push( _this.images[c] );
				}
			}
			
			_this.images = imgs;
			
			console.log( _this.images );
		}
	},
	
	off: function ( ele )
	{
		if ( ele )
		{
			this.current = ele;
		}
		
		var _this = Showrooms[this.current];
		
		for ( var img in _this.images )
		{
			var i = _this.images[img];
			if ( i && i.firstChild )
			{
				setOpacity ( i, 0 );
			}
		}
	},
	
	tweener: function ( running, ele )
	{
		if ( ele )
		{
			this.current = ele;
		}
		
		var _this = Showrooms[this.current];
		
		if ( !running )
		{
			_this.start = ( new Date () ).getTime ();
			setOpacity ( _this.images[_this.index], 0 );
			_this.images[_this.index].style.right = '0%';
			
			for ( var i = 0; i < _this.images.length; i++ )
			{
				if ( _this.images[i] && i != _this.index && i != _this.pindex )
				{
					_this.images[i].style.zIndex = '10';
				}
			}
			_this.images[_this.index].style.zIndex = '12';
			_this.images[_this.pindex].style.zIndex = '11';
			return setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.tweener(1)', 5 );
		}
		else
		{
			var p = ( ( new Date () ).getTime () - _this.start ) / 1000;
			
			if ( p >= 1 ) 
			{
				p = 1;
			}
			
			var pp = Math.pow ( Math.sin ( p * 0.5 * Math.PI ), 3 );
			setOpacity ( _this.images[_this.index], pp );
			
			if ( p < 1 )
			{
				_this.tm = setTimeout ( 'document.getElementById(\''+_this.id+'\').parent.tweener(1)', 5 );
			}
			else 
			{
				_this.images[_this.pindex].style.right = '100%';
				_this.start = false;
				clearTimeout ( _this.tm ); 
				_this.tm = 0;
				
				for ( var i = 0; i < _this.pages.length; i++ )
				{
					if ( _this.images[i] && i == _this.index )
					{
						_this.pages[i].className = 'PageActive';
						_this.images[i].className = 'ImageCurrent';
						_this.descriptions[i].className = 'DescriptionCurrent';
					}
					else if ( _this.images[i] ) 
					{
						_this.pages[i].className = 'Page';
						_this.images[i].className = 'Image';
						_this.descriptions[i].className = 'Description';
					}
					
				}
			}
		}
	},
	
	ShowroomLoad: function ( imgnr, ele )
	{
		if ( ele )
		{
			this.current = ele;
		}
		
		var _this = Showrooms[this.current];
		
		if ( _this.images[imgnr].notLoaded )
		{
			var img = _this.images[imgnr].notLoaded;
			_this.images[imgnr].notLoaded = false;
			
			var im = document.createElement ( 'div' );
			im.style.backgroundPosition = 'center center';
			im.style.backgroundRepeat = 'no-repeat';
			im.style.backgroundImage = 'url(' + img.src + ')';
			im.style.backgroundSize = 'contain';
			im.style.width = '100%';
			im.style.height = '100%';
			im.style.top = '0';
			im.style.left = '0';
			
			im.setAttribute( 'fid', img.fid );
			im.setAttribute( 'unique', img.unique );
			im.setAttribute( 'description', img.description );
			
			// Loading progress
			im.loader = document.createElement( 'img' );
			im.loader.src = img.src;
			im.loader.fid = img.fid;
			im.loader.unique = img.unique;
			im.loader.onload = function ()
			{
				this.style.display = 'none';
				im.style.backgroundImage = 'url(' + this.src + ')';
				_this.images[imgnr].fid = this.fid;
				_this.images[imgnr].unique = this.unique;
				_this.images[imgnr].isLoaded = true;
			}
			im.appendChild( im.loader );
			if( im.loader.width > 0 || im.loader.height > 0 )
			{
				im.loader.onload();
			}
			
			_this.images[imgnr].fid = img.fid;
			_this.images[imgnr].unique = img.unique;
			_this.images[imgnr].appendChild ( im );
			
			_this.ds.innerHTML = '<h2>' + im.description + '</h2>';
			_this.currentImage = im;
		}
	}
}

/*//var Showroom = new Object();

// Initialize showroom
Showroom.init = function ( ele, w, h )
{
	var _this = ( Showrooms[ele] ? Showrooms[ele] : this );
	_this.ele = ele;
	
	_this.container = document.getElementById ( ele );
	if( !_this.container ) return;
	
	_this.container.object = _this;
	_this.w = w; 
	_this.h = h;
	_this.index = 0;
	var imgs = _this.container.getElementsByTagName ( 'span' );
	var imgA = new Array ();
	_this.pages = new Array ();
	var p = document.createElement ( 'div' );
	p.className = 'Pages';
	_this.direction = false;
	_this.start = false;
	var l = imgs.length;
	
	// Create pages for each element
	for ( var a = 0; a < l; a++ )
	{
		var d = document.createElement ( 'div' );
		d.innerHTML = '<span>' + (a+1) + '</span>';
		d.object = _this;
		d.index = a;
		
		d.onclick = function () 
		{ 
			clearInterval ( this.object.interval ); 
			this.object.changePage(this.index); 
		}
		
		d.className = 'Page';
		p.appendChild ( d );
		_this.pages.push ( d );
		imgA.push ( imgs[a] );
		
		if ( a == 0 ) 
		{
			d.className = 'PageActive';
		}
	}
	
	// Create info about how many pages we have
	var d = document.createElement ( 'div' );
	d.innerHTML = '<span>of ' + l + '</span>';
	d.className = 'PageTotal';
	p.appendChild ( d );
	
	// Arrow navigation
	var ar = document.createElement ( 'div' );
	ar.className = 'Arrows';
	var an = document.createElement ( 'div' ); 
	an.className = 'ArrowNext';
	var ap = document.createElement ( 'div' ); 
	ap.className = 'ArrowPrev';
	an.object = _this;
	ap.object = _this;
	
	// Click arrownext
	an.onclick = function () 
	{ 
		clearInterval ( this.object.interval ); 
		this.object.changePage ( this.object.index+1 ); 
	}
	
	// Click arrowprev
	ap.onclick = function () 
	{ 
		clearInterval ( this.object.interval ); 
		this.object.changePage ( this.object.index-1 ); 
	}
	
	// Show arrows gfx and add stuff to dom
	an.innerHTML = '<span>»</span>';
	ap.innerHTML = '<span>«</span>';
	ar.appendChild ( ap ); 
	ar.appendChild ( an );
	_this.container.appendChild ( p );
	_this.container.appendChild ( ar );
	_this.arrows = [ ap, an ];
	
	// Create image container and description
	var i = document.createElement ( 'div' );
	i.className = 'ImageContainer';
	var k = document.createElement ( 'div' );
	k.className = 'ImageDescriptions';
	_this.images = new Array ();
	_this.descriptions = new Array ();
	
	// Create images
	for ( var a = 0; a < l; a++ )
	{
		if( !w ) w = imgs[a].getAttribute ( 'width' );
		if( !h ) h = imgs[a].getAttribute ( 'height' );
		
		var d = document.createElement ( 'div' );
		
		if ( a == 0 ) 
		{
			d.style.right = '0%';
		}
		if ( a == 0 )
		{
			d.className = 'ImageCurrent'; 
		}
		else 
		{
			d.className = 'Image';
		}
		
		var ds = document.createElement ( 'div' );
		ds.className = a == 0 ? 'DescriptionCurrent' : 'Description';
		_this.ds = ds;
		
		// Create image placeholder
		var o = new Object ();
		o.src = imgs[a].getAttribute ( 'title' );
		o.fid = imgs[a].getAttribute ( 'fid' );
		o.description = imgs[a].getAttribute ( 'description' );
		d.fid = o.fid;
		d.notLoaded = o;
		d.isLoaded = false;
		
		if ( imgs[a].getAttribute ( 'extended' ).length )
		{
			ds.innerHTML += imgs[a].getAttribute ( 'extended' );
		}
		
		i.appendChild ( d );
		d.object = _this;
		
		d.onclick = function ()
		{
			clearInterval ( this.object.interval );
			
			if ( this.object.direction == 'next' )
			{
				this.object.changePage ( this.object.index - 1 );
			}
			else if ( this.object.direction == 'prev' )
			{
				this.object.changePage ( this.object.index + 1 );
			}
		}
		
		_this.images.push ( d );
		
		if ( _this.images.length > 1 )
		{
			setOpacity ( d, 0 );
		}
		
		_this.descriptions.push ( ds );
		k.appendChild ( ds );
	}
	
	for ( var a in imgA )
	{
		if ( imgA[a].parentNode == _this.container )
		{
			_this.container.removeChild ( imgA[a] );
		}			
	}
	
	_this.container.appendChild ( i );
	_this.container.appendChild ( k );
	//_this.container.style.width = w + 'px';
	//_this.container.style.height = h + 'px';
	_this.container.className = 'Showroom';
	
	_this.timeout = false;
	_this.changePage = function ( ind, useid )
	{
		if ( this.timeout )
			clearTimeout( this.timeout );
		this.timeout = false;
		
		if( useid && this.images.length > 0 )
		{
			var pos = 0;
			
			for( a = 0; a < this.images.length; a++ )
			{
				//console.log( this.images[a].fid + ' -- ' + ind );
				
				if( this.images[a].fid && ind == this.images[a].fid )
				{
					pos = a;
				}
			}
			
			ind = pos;
		}
		
		ind = parseInt( ind );
		
		if ( ind == 'next' ) 
		{
			ind = this.index + 1;
		}
		if ( this.start > 0 ) 
		{
			return;
		}
		
		this.pindex = this.index;
		
		if ( ind >= this.images.length ) 
		{
			ind = 0;
		}
		else if ( ind < 0 ) 
		{
			ind = this.images.length - 1;
		}
		if ( ind == this.index ) 
		{
			this.tweener ();
			return;
		}
		
		// Only change page if image is loaded, else delay operation
		if ( this.images[ind] && this.images[ind].firstChild && this.images[ind].isLoaded )
		{
			this.index = ind;
			this.tweener ();
		}
		// Load
		else if ( this.images[ind].notLoaded )
		{
			ShowroomLoad( ind, this.container.id );
			this.timeout = setTimeout ( 'document.getElementById(\''+this.container.id+'\').object.changePage(\''+ind+'\')', 10 );
		}
		// Just reload
		else
		{
			this.timeout = setTimeout ( 'document.getElementById(\''+this.container.id+'\').object.changePage(\''+ind+'\')', 10 );
		}
	}
	
	_this.off = function ()
	{
		for ( var a in this.images )
		{
			var i = this.images[a];
			if ( i && i.firstChild )
			{
				setOpacity ( i, 0 );
			}
		}
	}
	
	_this.tweener = function ( running )
	{
		if ( !running )
		{
			this.start = ( new Date () ).getTime ();
			setOpacity ( this.images[this.index], 0 );
			this.images[this.index].style.right = '0%';
			
			for ( var a = 0; a < this.images.length; a++ )
			{
				if ( a != this.index && a != this.pindex )
				{
					this.images[a].style.zIndex = '10';
				}
			}
			this.images[this.index].style.zIndex = '12';
			this.images[this.pindex].style.zIndex = '11';
			return setTimeout ( 'document.getElementById(\''+this.container.id+'\').object.tweener(1)', 5 );
		}
		else
		{
			var p = ( ( new Date () ).getTime () - this.start ) / 1000;
			
			if ( p >= 1 ) 
			{
				p = 1;
			}
			
			var pp = Math.pow ( Math.sin ( p * 0.5 * Math.PI ), 3 );
			setOpacity ( this.images[this.index], pp );
			
			if ( p < 1 )
			{
				this.tm = setTimeout ( 'document.getElementById(\''+this.container.id+'\').object.tweener(1)', 5 );
			}
			else 
			{
				this.images[this.pindex].style.right = '100%';
				this.start = false;
				clearTimeout ( this.tm ); 
				this.tm = 0;
				
				for ( var a = 0; a < this.pages.length; a++ )
				{
					if ( a == this.index )
					{
						this.pages[a].className = 'PageActive';
						this.images[a].className = 'ImageCurrent';
						this.descriptions[a].className = 'DescriptionCurrent';
					}
					else 
					{
						this.pages[a].className = 'Page';
						this.images[a].className = 'Image';
						this.descriptions[a].className = 'Description';
					}
					
				}
			}
		}
	}
	
	_this.container.onmouseout = function ()
	{
		var pr = this.object.arrows[0];
		var nr = this.object.arrows[1];
		pr.className = 'ArrowPrev';
		nr.className = 'ArrowNext';
		this.object.direction = false;
	}
	
	Showrooms[ele] = _this;
	
	// Load first image
	ShowroomLoad( 0, _this.container.id );
}

// Test and load one image
function ShowroomLoad( imgnr, ele )
{
	var _this = Showrooms[ele];
	
	if ( _this.images[imgnr].notLoaded )
	{
		var img = _this.images[imgnr].notLoaded;
		_this.images[imgnr].notLoaded = false;
		
		var im = document.createElement ( 'div' );
		im.style.backgroundPosition = 'center center';
		im.style.backgroundRepeat = 'no-repeat';
		im.style.backgroundImage = 'url(' + img.src + ')';
		im.style.backgroundSize = 'contain';
		im.style.width = '100%';
		im.style.height = '100%';
		im.style.top = '0';
		im.style.left = '0';
		
		im.setAttribute( 'fid', img.fid );
		im.setAttribute( 'description', img.description );
		
		// Loading progress
		im.loader = document.createElement( 'img' );
		im.loader.src = img.src;
		im.loader.fid = img.fid;
		im.loader.onload = function ()
		{
			this.style.display = 'none';
			im.style.backgroundImage = 'url(' + this.src + ')';
			_this.images[imgnr].fid = this.fid;
			_this.images[imgnr].isLoaded = true;
		}
		im.appendChild( im.loader );
		if( im.loader.width > 0 || im.loader.height > 0 )
		{
			im.loader.onload();
		}
		
		_this.images[imgnr].fid = img.fid;
		_this.images[imgnr].appendChild ( im );
		
		_this.ds.innerHTML = '<h2>' + im.description + '</h2>';
		_this.currentImage = im;
	}
}*/
