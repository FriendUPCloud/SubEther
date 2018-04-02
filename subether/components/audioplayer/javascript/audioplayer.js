
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

function testonMouseDown( e, obj )
{
	e = e || window.event; //window.event for IE

	//alert("Keycode of key pressed: " + (e.keyCode || e.which));
	//alert("Offset-X = " + obj.offsetLeft);
	//alert("Offset-Y = " + obj.offsetTop);
	document.title = "Offset-X = " + obj.offsetLeft + " | Offset-Y = " + obj.offsetTop;
}

function hideElements( _this, id )
{
	if( !_this || !id || !ge( id ) ) return;
	
	var ele = ge( id ).getElementsByTagName( '*' );
	
	if( ele.length > 0 )
	{
		for( a = 0; a < ele.length; a++ )
		{
			if( ele[a].className.indexOf( 'hidden' ) < 0 && ele[a] == _this )
			{
				ele[a].className = ele[a].className + ' hidden';
			}
			else if( ele[a].className.indexOf( 'hidden' ) >= 0 )
			{
				ele[a].className = ele[a].className.split( ' hidden' ).join( '' );
			}
		}
	}
}

function selectElement( _this, id )
{
	if( !_this || !id || !ge( id ) ) return;
	
	var ele = ge( id ).getElementsByTagName( '*' );
	
	if( ele.length > 0 )
	{
		for( a = 0; a < ele.length; a++ )
		{
			if( ele[a].className.indexOf( 'selected' ) < 0 && ele[a] == _this )
			{
				ele[a].className = ele[a].className + 'selected';
			}
			else if( ele[a].className.indexOf( 'selected' ) >= 0 )
			{
				ele[a].className = ele[a].className.split( 'selected' ).join( '' );
			}
		}
	}
}

function gc( cls, id )
{
	if( !cls ) return false;
	
	if( id )
	{
		var ele = ge( id ).getElementsByTagName( '*' );
	}
	else
	{
		var ele = document.getElementsByTagName( '*' );
	}
	
	if( ele.length > 0 )
	{
		for( a = 0; a < ele.length; a++ )
		{
			if( ele[a].className == cls )
			{
				return ele[a];
			}
		}
	}
	
	return false;
}

function audioSelect( _this, ele )
{
	if( !_this || !ele ) return false;
	
	selectElement( _this, ele );
	
	var pas = gc( 'pause', 'AudioPlayer' );
	
	if( pas )
	{
		audioPause( pas, ge('AudioElement') );
	}
	
	if( _this.getAttribute( 'audiourl' ) )
	{
		audioSwitch( _this, ge('AudioElement'), _this.getAttribute( 'audiourl' ) );
	}
	
	var art = gc( 'artist', 'AudioPlayer' );
	
	if( art && _this.getAttribute( 'artist' ) )
	{
		art.innerHTML = _this.getAttribute( 'artist' );
	}
	
	var ply = gc( 'play', 'AudioPlayer' );
	
	if( ply )
	{
		ply.click();
	}
	
	return true;
}

function audioPlay( _this, ele )
{
	if( !_this || !ele ) return false;
	
	ele.play();
	
	hideElements( _this, 'AudioPlayer' );
	
	return true;
}

function audioPause( _this, ele )
{
	if( !_this || !ele ) return false;
	
	ele.pause();
	
	hideElements( _this, 'AudioPlayer' );
	
	return true;
}

function audioVolumeIncrease( _this, ele, volume )
{
	if( !_this || !ele || !volume ) return false;
	
	ele.volume+=volume;
	
	return true;
}

function audioVolumeDecrease( _this, ele, volume )
{
	if( !_this || !ele || !volume ) return false;
	
	ele.volume-=volume;
	
	return true;
}

function audioSeekableStart( _this, ele )
{
	if( !_this || !ele ) return false;
	
	return ele.seekable.start();
}

function audioSeekableEnd( _this, ele )
{
	if( !_this || !ele ) return false;
	
	return ele.seekable.end();
}

function audioSeek( _this, ele, seconds )
{
	if( !_this || !ele || !seconds ) return false;
	
	ele.currentTime = seconds;
	
	return true;
}

function audioPlayer( _this, ele )
{
	if( !_this || !ele ) return false;
	
	return ele.played.end();
}

function audioSwitch( _this, ele, source )
{
	if( !_this || !ele || !source ) return false;
	
	ele.src = source;
	
	return true;
}

function audioAutoPlay( _this, ele )
{
	if( !_this || !ele ) return false;
	
	if( ele.getAttribute( 'autoplay' ) )
	{
		audioRemoveAutoPlay( _this, ele );
	}
	else
	{
		audioSetAutoPlay( _this, ele );
	}
	
	return true;
}

function audioSetAutoPlay( _this, ele )
{
	if( !_this || !ele ) return false;
	
	ele.setAttribute( 'autoplay', '1' );
	
	return true;
}

function audioRemoveAutoPlay( _this, ele )
{
	if( !_this || !ele ) return false;
	
	ele.removeAttribute( 'autoplay' );
	
	return true;
}

function audioLoop( _this, ele )
{
	if( !_this || !ele ) return false;
	
	if( ele.getAttribute( 'loop' ) )
	{
		audioRemoveLoop( _this, ele );
	}
	else
	{
		audioSetLoop( _this, ele );
	}
	
	return true;
}

function audioSetLoop( _this, ele )
{
	if( !_this || !ele ) return false;
	
	ele.setAttribute( 'loop', '1' );
	
	return true;
}

function audioRemoveLoop( _this, ele )
{
	if( !_this || !ele ) return false;
	
	ele.removeAttribute( 'loop' );
	
	return true;
}
