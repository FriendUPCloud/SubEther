
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

//Settings
var _imgseed = 1;
coverInterval_ = 0;

function setCoverImage ( ml, n )
{	
	if( !ml ) var ml = false;
	if( !n ) var n = false;

	var mi = document.getElementById( 'MainImage' );

	var currImage = mi.getElementsByTagName ( 'img' )[0];
	if ( !currImage ) return;
	var pn = mi;
	
	// Clear current fade
	if ( window.isTweening )
	{
		var two = window.isTweening;
		clearTimeout ( two.tmo )
		currImage.src = two.src;
		//currImage.style.backgroundImage = two.backgroundImage;
		if ( currImage.parentNode.prevImage == two )
		{
			currImage.parentNode.prevImage = false;
		}
		two.parentNode.removeChild ( two );
		window.isTweening = false;
	}
	else
	{
		// Remove prev image if it still exists
		if ( pn.prevImage )	pn.removeChild ( pn.prevImage );
	}
	
	currImage.style.zIndex = '1';
	
	// Load next image . . . . . . 
	var nextImage = document.createElement ( 'img' );
	nextImage.src = ml;
	//nextImage.style.backgroundImage = 'url(\'' + ml + '\')';
	setOpacity ( nextImage, 0 );
	nextImage.style.width = getElementWidth ( currImage ) + 'px';
	nextImage.style.height = getElementHeight ( currImage ) + 'px';
	nextImage.style.top = currImage.offsetTop + 'px';
	nextImage.style.width = currImage.offsetWidth + 'px';
	//nextImage.style.width = '100%';
	//nextImage.style.height = '100%';
	//nextImage.style.top = '0px';
	pn.appendChild ( nextImage );
	pn.prevImage = nextImage;
	nextImage.style.zIndex = '2';
	nextImage.curr = currImage;
	nextImage.n = n;
	nextImage.tween = function ()
	{
		var ph = ( new Date () ).getTime ();
		var t = ( ph - this.tm ) / 1000;
		if ( t > 1 ) t = 1;
		if ( t < 1 )
		{
			setOpacity ( this, Math.pow ( Math.sin ( t * 0.5 * Math.PI ), 3 ) );
			this.tmo = setTimeout ( 'if ( ge(\''+ this.id +'\') ) ge(\'' + this.id + '\').tween();', 16 );
			return;
		}
		clearTimeout ( this.tmo );
		this.curr.src = this.src;
		//this.curr.style.backgroundImage = this.backgroundImage;
		this.parentNode.prevImage = false;
		this.parentNode.removeChild ( this );
		window.isTweening = false;
	}
	nextImage.onload = function ()
	{
		// Highlight pages
		var pages = document.getElementById( 'CoverPages' );
		if ( !pages ) return;
		var page = pages.getElementsByTagName ( 'div' );
		for ( var a = 0; a < page.length; a++ )
		{			
			if ( a+1 == this.n )
			{
				page[a].className = page[a].className.split ( ' Current' ).join ( '' ) + ' Current';
			}
			else 
			{
				page[a].className = page[a].className.split ( ' Current' ).join ( '' );
			}
		}
		if ( !this.id )
		{
			this.id = 'image' + _imgseed++;
		}
		if ( !this.opa )
		{
			window.isTweening = this;
			this.opa = 0;
			this.tm = ( new Date () ).getTime ();
			return setTimeout ( 'if ( ge ( \'' + this.id + '\' ) ) ge(\'' + this.id + '\').tween()', 16 );
		}
	}
	if ( nextImage.width > 0 && nextImage.height > 0 ) nextImage.onload ();
}

function nextCoverImage ()
{
	if ( !window.isTweening )
	{
		var pages = document.getElementById( 'CoverPages' );
		if( !pages ) return;
		var imgs = pages.getElementsByTagName ( 'div' );
		for ( var a = 0; a < imgs.length; a++ )
		{
			if ( imgs[a].className.indexOf ( 'Current' ) >= 0 )
			{
				var b = a + 1;
				if ( b >= imgs.length ) b = 0;
				imgs[b].onclick ();
				return;
			}
		}
	}
}

function prevCoverImage ()
{
	if ( !window.isTweening )
	{
		var pages = document.getElementById( 'CoverPages' );
		if( !pages ) return;
		var imgs = pages.getElementsByTagName ( 'div' );
		for ( var a = 0; a < imgs.length; a++ )
		{
			if ( imgs[a].className.indexOf ( 'Current' ) >= 0 )
			{
				var b = a - 1;
				if ( b < 0 ) b = imgs.length - 1;
				imgs[b].onclick ();
				return;
			}
		}
	}
}

// autoplay
function coverSlideshowPlay ( evt )
{
	nextCoverImage ();
}

function coverStopSlideshow ()
{
	clearInterval ( coverInterval_ );
	coverInterval_ = 0;
}

//var coverInterval_ = setInterval ( 'coverSlideshowPlay()', 5000 );
