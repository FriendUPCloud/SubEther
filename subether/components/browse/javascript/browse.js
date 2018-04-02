
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

function refreshMedia ( mid )
{
	if( !ge( 'Content' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=browse&function=browse', 'post', true );
	j.addVar ( 'mid', mid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'Content' ).innerHTML = r[1];
		}
	}
	j.send ();
}

function refreshMediaRating ( mid )
{
	if( !mid || !ge( 'MediaRating' ) ) return false;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=browse&function=browse', 'post', true );
	j.addVar ( 'refresh', 'mediarating' );
	j.addVar ( 'mid', mid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'MediaRating' ).innerHTML = r[1];
		}
	}
	j.send ();
}

function saveComment ( mid, ele )
{
	if( !mid || !ele.value ) return false;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=browse&action=browse', 'post', true );
	j.addVar ( 'comment', ele.value );
	j.addVar ( 'mid', mid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			ele.value = '';
			refreshComments( mid );
		}
	}
	j.send ();
}

function refreshComments ( mid )
{
	if( !ge( 'MediaComments' ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=browse&function=browse', 'post', true );
	j.addVar ( 'refresh', 'comments' );
	j.addVar ( 'mid', mid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'MediaComments' ).innerHTML = r[1];
		}
	}
	j.send ();
}

function refreshComment ( mid, cid )
{
	if( !mid || !ge( 'CommentID_' + cid ) ) return;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=browse&function=browse', 'post', true );
	j.addVar ( 'mid', mid );
	j.addVar ( 'cid', cid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' && r[1] )
		{
			ge( 'CommentID_' + cid ).innerHTML = r[1];
		}
	}
	j.send ();
}

function voteMedia ( mid, vote )
{
	if( !mid || !vote ) return false;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=browse&action=vote', 'post', true );
	j.addVar ( 'type', 'media' );
	j.addVar ( vote, mid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			refreshMediaRating( mid );
		}
	}
	j.send ();
}

function voteComment ( cid, mid, vote )
{
	if( !cid || !mid || !vote ) return false;
	var j = new bajax ();
	j.openUrl ( getPath() + '?component=browse&action=vote', 'post', true );
	j.addVar ( 'type', 'comment' );
	j.addVar ( vote, cid );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			refreshComment( mid, cid );
		}
	}
	j.send ();
}
