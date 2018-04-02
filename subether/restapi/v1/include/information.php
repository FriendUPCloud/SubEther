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

global $database;

allowAccess();

include_once ( ( $root ? ( $root.'/' ) : '' ) . 'subether/classes/library.class.php' );

$required = array(
	'UniqueID', 'PublicKey', 'Url', 'Name', 'Version', 'Owner', 'Email',
	'Location', 'Users', 'Open', 'Created'
);

// --- Information --- //

if ( isset( $_POST ) )
{
	$public = ( !$_POST ? true : false );
	
	if( !$public )
	{
		foreach( $_POST as $k=>$p )
		{
			if( !in_array( $k, $required ) )
			{
				throwXmlError ( MISSING_PARAMETERS, false, 'information' );
			}
		}
		foreach( $required as $r )
		{
			if( !isset( $_POST[$r] ) )
			{
				throwXmlError ( MISSING_PARAMETERS, false, 'information' );
			}
		}
		
		// --- Update received information from external node -------------------------------------------------------------
		
		$node = new dbObject( 'SNodes' );
		$node->UniqueID = $_POST['UniqueID'];
		$node->Load();
		$node->PublicKey = $_POST['PublicKey'];
		$node->Url = getNodeHost( $_POST['Url'] );
		$node->Name = $_POST['Name'];
		$node->Version = $_POST['Version'];
		$node->Owner = $_POST['Owner'];
		$node->Email = $_POST['Email'];
		$node->Location = $_POST['Location'];
		$node->Users = $_POST['Users'];
		$node->Open = $_POST['Open'];
		$node->DateCreated = $_POST['Created'];
		$node->DateModified = date( 'Y-m-d H:i:s' );
		if ( $node->IsAllowed || getNodeInfo( 'index' ) == $_POST['Url'] || ( !$node->IsDenied && getNodeInfo( 'index' ) == getNodeHost( BASE_URL ) ) )
		{
			$node->IsConnected = 1;
			$node->IsPending = 0;
			$node->IsAllowed = 1;
		}
		$node->Save();
	}
	
	// --- List main node info as xml ---------------------------------------------------------------------------------
	
	if( ( ( isset( $node ) && $node->IsAllowed ) || $public ) && ( $main = getNodeData( $root ) ) )
	{
		if( $public && $main->Open < 0 )
		{
			// Give access denied message
			throwXmlError ( ACCESS_DENIED, false, 'information' );
		}
		
		$inf = new stdClass();
		
		$xml  = "\t\t<Verification>" . $main->Verification . "</Verification>\n";
		$xml .= "\t\t<Information>\n";
		
		$xml .= "\t\t\t<Url>" . $main->Url . "</Url>\n";
		$xml .= "\t\t\t<Name>" . $main->Name . "</Name>\n";
		$xml .= "\t\t\t<Version>" . $main->Version . "</Version>\n";
		$xml .= "\t\t\t<UniqueID>" . $main->UniqueID . "</UniqueID>\n";
		$xml .= "\t\t\t<PublicKey>" . $main->PublicKey . "</PublicKey>\n";
		
		if( !$public )
		{
			$xml .= "\t\t\t<Owner>" . $main->Owner . "</Owner>\n";
			$xml .= "\t\t\t<Email>" . $main->Email . "</Email>\n";
		}
		
		$xml .= "\t\t\t<Location>" . $main->Location . "</Location>\n";
		$xml .= "\t\t\t<Users>" . $main->Users . "</Users>\n";
		
		$inf->Url       = $main->Url;
		$inf->Name      = $main->Name;
		$inf->Version   = $main->Version;
		$inf->UniqueID  = $main->UniqueID;
		$inf->PublicKey = $main->PublicKey;
		
		if( !$public )
		{
			$inf->Owner = $main->Owner;
			$inf->Email = $main->Email;
		}
		
		$inf->Location  = $main->Location;
		$inf->Users     = $main->Users;
		
		// --- Modules -----------------------------------------------------------------------------------------------
		
		if( !$public && isset( $main->Modules ) )
		{
			$inf->Modules = [];
			
			$xml .= "\t\t\t<Modules>\n";
			
			foreach( $main->Modules as $m )
			{
				$xml .= "\t\t\t\t<Module>" . $m->Name . "</Module>\n";
				
				$mod = new stdClass();
				$mod->Module = $m->Name;
				
				$inf->Modules[] = $mod;
			}
			
			$xml .= "\t\t\t</Modules>\n";
		}
		
		// --- Components --------------------------------------------------------------------------------------------
		
		if( !$public && isset( $main->Components ) )
		{
			$inf->Components = [];
			
			$xml .= "\t\t\t<Components>\n";
			
			foreach( $main->Components as $c )
			{
				$xml .= "\t\t\t\t<Component>" . $c->Name . "</Component>\n";
				
				$com = new stdClass();
				$com->Component = $c->Name;
				
				$inf->Components[] = $com;
			}
			
			$xml .= "\t\t\t</Components>\n";
		}
		
		// --- Plugins -----------------------------------------------------------------------------------------------
		
		if( !$public && isset( $main->Plugins ) )
		{
			$inf->Plugins = [];
			
			$xml .= "\t\t\t<Plugins>\n";
			
			foreach( $main->Plugins as $p )
			{
				$xml .= "\t\t\t\t<Plugin>" . $p->Name . "</Plugin>\n";
				
				$plg = new stdClass();
				$plg->Plugin = $p->Name;
				
				$inf->Plugins[] = $plg;
			}
			
			$xml .= "\t\t\t</Plugins>\n";
		}
		
		// --- Themes ------------------------------------------------------------------------------------------------
		
		if( !$public && isset( $main->Themes ) )
		{
			$inf->Themes = [];
			
			$xml .= "\t\t\t<Themes>\n";
			
			foreach( $main->Themes as $t )
			{
				$xml .= "\t\t\t\t<Theme>" . $t->Name . "</Theme>\n";
				
				$thm = new stdClass();
				$thm->Theme = $t->Name;
				
				$inf->Themes[] = $thm;
			}
			
			$xml .= "\t\t\t</Themes>\n";
		}
		
		// --- Nodes -------------------------------------------------------------------------------------------------
		
		if( isset( $main->Nodes ) )
		{
			$inf->Nodes = [];
			
			$xml .= "\t\t\t<Nodes>\n";
			
			foreach( $main->Nodes as $n )
			{
				if ( $n->IsConnected && $n->Open >= 0 )
				{
					$obj = new stdClass();
					
					$xml .= "\t\t\t\t<Node>\n";
					$xml .= "\t\t\t\t\t<UniqueID>" . $n->UniqueID . "</UniqueID>\n";
					$xml .= "\t\t\t\t\t<Url>" . $n->Url . "</Url>\n";
					$xml .= "\t\t\t\t</Node>\n";
					
					$obj->UniqueID = $n->UniqueID;
					$obj->Url = $n->Url;
					
					$nod = new stdClass();
					$nod->Node = $obj;
					
					$inf->Nodes[] = $nod;
				}
			}
			
			$xml .= "\t\t\t</Nodes>\n";
		}
		
		// --- Releases ----------------------------------------------------------------------------------------------
		
		if( isset( $main->Releases ) )
		{
			$inf->Releases = [];
			
			$xml .= "\t\t\t<Releases>\n";
			
			foreach( $main->Releases as $f )
			{
				$xml .= "\t\t\t\t<Release>\n";
				$xml .= "\t\t\t\t\t<FileName>" . $f->Name . "</FileName>\n";
				$xml .= "\t\t\t\t\t<FileTitle>" . $f->Title . "</FileTitle>\n";
				$xml .= "\t\t\t\t\t<FilePath>" . $f->Path . "</FilePath>\n";
				$xml .= "\t\t\t\t\t<FileType>" . $f->Type . "</FileType>\n";
				$xml .= "\t\t\t\t\t<FileSize>" . $f->Size . "</FileSize>\n";
				
				$obj = new stdClass();
				$obj->FileName  = $f->Name;
				$obj->FileTitle = $f->Title;
				$obj->FilePath  = $f->Path;
				$obj->FileType  = $f->Type;
				$obj->FileSize  = $f->Size;
				
				if( $f->Modified )
				{
					$xml .= "\t\t\t\t\t<Modified>" . $f->Modified . "</Modified>\n";
					
					$obj->Modified = $f->Modified;
				}
				
				if( $f->Version )
				{
					$xml .= "\t\t\t\t\t<Version>" . $f->Version . "</Version>\n";
					
					$obj->Version = $f->Version;
				}
				
				$xml .= "\t\t\t\t</Release>\n";
				
				$rel = new stdClass();
				$rel->Release = $obj;
				
				$inf->Releases[] = $rel;
			}
			
			$xml .= "\t\t\t</Releases>\n";
		}
		
		$xml .= "\t\t\t<Open>" . $main->Open . "</Open>\n";
		$xml .= "\t\t\t<Created>" . $main->DateCreated . "</Created>\n";
		$xml .= "\t\t</Information>\n";
		
		$inf->Open         = $main->Open;
		$inf->Created      = $main->DateCreated;
		
		$json = new stdClass();
		$json->Verification = $main->Verification;
		$json->Information = $inf;
		
		outputXML ( ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' ? $json : $xml ), false, 'information' );
	}
	else if( isset( $node ) && !$node->IsDenied )
	{
		// Give empty list message
		throwXmlMsg ( EMPTY_LIST, false, 'information' );
	}
	
	// Give access denied message
	throwXmlError ( ACCESS_DENIED, false, 'information' );
}

// Give default error
throwXmlError ( MISSING_PARAMETERS, false, 'information' );

?>
