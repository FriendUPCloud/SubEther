<?php

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

global $database, $webuser;

if( $webuser->ID > 0 )
{
	if( $user = $database->fetchObjectRow( '
		SELECT 
			u.*, c.ID AS ContactID 
		FROM 
			Users u, SBookContact c 
		WHERE 
				u.ID = \'' . $webuser->ID . '\' 
			AND c.UserID = u.ID 
		ORDER BY 
			u.ID DESC 
	' ) )
	{
		// Probably no use for setting alot of stuff isdeleted except the user tables
		
		// TODO: Make support for deleting files, images, folders and other media formats. Also set data to IsDeleted instead of delete when going live
		
		// SBookAccounts --------------------
		
		/*if( $acc = $database->fetchObjectRows( '
			SELECT * 
			FROM SBookAccounts 
			WHERE UserID = \'' . $user->ID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $acc as $ac )
			{
				$n = new dbObject( 'SBookAccounts' );
				$n->ID = $ac->ID;
				if( $n->Load() )
				{
					$n->IsDeleted = 1;
					$n->Save();
					//$n->Delete();
				}
			}
		}
		
		// SBookBookmarks -------------------
		
		if( $smk = $database->fetchObjectRows( '
			SELECT * 
			FROM SBookBookmarks 
			WHERE UserID = \'' . $user->ContactID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $smk as $sk )
			{
				$k = new dbObject( 'SBookBookmarks' );
				$k->ID = $sk->ID;
				if( $k->Load() )
				{
					$k->IsDeleted = 1;
					$k->Save();
					//$k->Delete();
				}
			}
		}
		
		// SBookCategoryAccess -------------------
		
		if( $sca = $database->fetchObjectRows( '
			SELECT * 
			FROM SBookCategoryAccess 
			WHERE UserID = \'' . $user->ContactID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $sca as $ca )
			{
				$a = new dbObject( 'SBookCategoryAccess' );
				$a->ID = $ca->ID;
				if( $a->Load() )
				{
					$a->IsDeleted = 1;
					$a->Save();
					//$a->Delete();
				}
			}
		}
		
		// SBookCategoryRelation ------------------
		
		if( $sgr = $database->fetchObjectRows( '
			SELECT * 
			FROM SBookCategoryRelation 
			WHERE ObjectID = \'' . $user->ContactID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $sgr as $gr )
			{
				$g = new dbObject( 'SBookCategoryRelation' );
				$g->ID = $gr->ID;
				if( $g->Load() )
				{
					$g->IsDeleted = 1;
					$g->Save();
					//$g->Delete();
				}
			}
		}
		
		// SBookContactRelation ------------------
		
		if( $scr = $database->fetchObjectRows( '
			SELECT * 
			FROM SBookContactRelation 
			WHERE ContactID = \'' . $user->ContactID . '\'
			OR ObjectID = \'' . $user->ContactID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $scr as $cr )
			{
				$r = new dbObject( 'SBookContactRelation' );
				$r->ID = $cr->ID;
				if( $r->Load() )
				{
					$r->IsDeleted = 1;
					$r->Save();
					//$r->Delete();
				}
			}
		}*/
		
		/*// SBookHours ----------------------------
		
		if( $sbh = $database->fetchObjectRows( '
			SELECT * 
			FROM SBookHours 
			WHERE UserID = \'' . $user->ContactID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $sbh as $hs )
			{
				$o = new dbObject( 'SBookHours' );
				$o->ID = $hs->ID;
				if( $o->Load() )
				{
					$o->Delete();
				}
			}
		}*/
		
		/*// SBookMail ----------------------------
		
		if( $sbm = $database->fetchObjectRows( '
			SELECT * 
			FROM SBookMail 
			WHERE SenderID = \'' . $user->ContactID . '\'
			OR ReceiverID = \'' . $user->ContactID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $sbm as $sb )
			{
				$b = new dbObject( 'SBookMailAccounts' );
				$b->ID = $sb->ID;
				if( $b->Load() )
				{
					$b->Delete();
				}
			}
		}*/
		
		// SBookMailAccounts ---------------------
		
		/*if( $sma = $database->fetchObjectRows( '
			SELECT * 
			FROM SBookMailAccounts 
			WHERE UserID = \'' . $user->ID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $sma as $sa )
			{
				$a = new dbObject( 'SBookMailAccounts' );
				$a->ID = $sa->ID;
				if( $a->Load() )
				{
					$a->IsDeleted = 1;
					$a->Save();
					//$a->Delete();
				}
			}
		}
		
		// SBookMailHeaders ---------------------
		
		if( $smh = $database->fetchObjectRows( '
			SELECT * 
			FROM SBookMailHeaders 
			WHERE UserID = \'' . $user->ID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $smh as $sh )
			{
				$h = new dbObject( 'SBookMailHeaders' );
				$h->ID = $sh->ID;
				if( $h->Load() )
				{
					$h->IsDeleted = 1;
					$h->Save();
					//$h->Delete();
				}
			}
		}*/
		
		/*// SBookMediaRelation ---------------------
		
		if( $smr = $database->fetchObjectRows( '
			SELECT * 
			FROM SBookMediaRelation 
			WHERE UserID = \'' . $user->ID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $smr as $sm )
			{
				$m = new dbObject( 'SBookMediaRelation' );
				$m->ID = $sm->ID;
				if( $m->Load() )
				{
					$m->Delete();
				}
			}
		}*/
		
		// SBookStatus ----------------------------
		
		/*if( $sbt = $database->fetchObjectRows( '
			SELECT * 
			FROM SBookStatus 
			WHERE UserID = \'' . $user->ID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $sbt as $st )
			{
				$t = new dbObject( 'SBookStatus' );
				$t->ID = $st->ID;
				if( $t->Load() )
				{
					$t->IsDeleted = 1;
					$t->Save();
					//$t->Delete();
				}
			}
		}
		
		// SBookStorage ----------------------------
		
		if( $sbs = $database->fetchObjectRows( '
			SELECT * 
			FROM SBookStorage 
			WHERE UserID = \'' . $user->ID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $sbs as $ss )
			{
				$s = new dbObject( 'SBookStorage' );
				$s->ID = $ss->ID;
				if( $s->Load() )
				{
					$s->IsDeleted = 1;
					$s->Save();
					//$s->Delete();
				}
			}
		}
		
		// UserLogin -------------------------------
		
		if( $usl = $database->fetchObjectRows( '
			SELECT * 
			FROM UserLogin 
			WHERE UserID = \'' . $user->ID . '\' 
			ORDER BY ID DESC 
		' ) )
		{
			foreach( $usl as $ul )
			{
				$l = new dbObject( 'UserLogin' );
				$l->ID = $ul->ID;
				if( $l->Load() )
				{
					$l->IsDeleted = 1;
					$l->Save();
					//$l->Delete();
				}
			}
		}
		
		// UsersGroups ----------------------------
		
		if( $ugs = $database->fetchObjectRows( '
			SELECT * 
			FROM UsersGroups 
			WHERE UserID = \'' . $user->ID . '\' 
			ORDER BY UserID DESC 
		' ) )
		{
			foreach( $ugs as $gu )
			{
				$g = new dbObject( 'SBookContact' );
				$g->ID = $ug->ID;
				if( $g->Load() )
				{
					$g->IsDeleted = 1;
					$g->Save();
					//$g->Delete();
				}
			}
		}
		
		// SBookContact ---------------------------
		
		$c = new dbObject( 'SBookContact' );
		$c->ID = $user->ContactID;
		if( $c->Load() )
		{
			$c->IsDeleted = 1;
			$c->Save();
			//$c->Delete();
		}*/
		
		// Users ----------------------------------
		
		$u = new dbObject( 'Users' );
		$u->ID = $user->ID;
		if( $u->Load() )
		{
			$u->IsDeleted = 1;
			$u->Save();
			//$u->Delete();
		}
		
		UserActivity( 'contacts', 'contact', $user->ContactID, null, $user->ContactID, 'removed' );
	}
	
	die( 'ok<!--separate-->' );
}

die( 'fail' );

?>
