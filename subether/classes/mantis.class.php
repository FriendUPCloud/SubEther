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

class Mantis
{
	var $Project = 'General';
	var $Category = 'General';
	var $Database;
	var $ProjectID;
	var $CategoryID;
	var $UserID;
	var $BugID;
	var $Summery;
	var $Description;
	
	function __construct ( $id = false, $username = false, $email = false )
	{
		$this->SetDatabase();
		
		if( $user = $this->Database->fetchObjectRow ( '
			SELECT 
				id 
			FROM 
				mantis_user_table 
			WHERE 
				id = \'' . $id . '\' OR username = \'' . $username . '\' OR email = \'' . $email . '\' 
			ORDER BY 
				id DESC 
		' ) )
		{
			$this->UserID = $user->id;
		}
		
		$pro = new dbObject( 'mantis_project_table', $this->Database );
		if( $this->ProjectID > 0 )
		{
			$pro->id = $this->ProjectID;
		}
		else
		{
			$pro->name = $this->Project;
		}
		$pro->Load();
		$pro->Save();
		
		$cat = new dbObject( 'mantis_category_table', $this->Database );
		if( $this->CategoryID > 0 )
		{
			$cat->id = $this->CategoryID;
		}
		else
		{
			$cat->category_id = $pro->id;
			$cat->name = $this->Category;
		}
		$cat->Load();
		$cat->Save();
		
		if( $pro->id > 0 && $cat->id > 0 )
		{
			$this->ProjectID = $pro->id;
			$this->CategoryID = $cat->id;
		}
	}
	
	function SetDatabase()
	{
		$mdb = new cDatabase ();
		$mdb->SetUsername ( CRON_DB_USERNAME );
		$mdb->SetPassword ( CRON_DB_PASSWORD );
		$mdb->SetHostname ( CRON_DB_HOSTNAME );
		$mdb->SetDb ( 'mantis' );
		
		$mdb->open () or die ( 'Failed to connect' );
		
		$this->Database = $mdb;
	}
	
	//function List ()
	//{
	//	
	//}
	
	//function Delete ()
	//{
	//	
	//}
	
	function Save ()
	{
		if( $this->ProjectID > 0 && $this->CategoryID && $this->Summery )
		{
			$bug = new dbObject( 'mantis_bug_table', $this->Database );
			
			if( $this->BugID > 0 )
			{
				$bug->id = $this->BugID;
				$bug->Load();
			}
			else
			{
				$bug->date_submitted = strtotime( date( 'Y-m-d H:i:s' ) );
			}
			
			$text = new dbObject( 'mantis_bug_text_table', $this->Database );
			if( $bug->bug_text_id > 0 )
			{
				$text->id = $bug->bug_text_id;
				$text->Load();
			}
			$text->description = $this->Description;
			$text->Save();
			
			$bug->bug_text_id = $text->id;
			$bug->project_id = $this->ProjectID;
			$bug->reporter_id = $this->UserID;
			$bug->category_id = $this->CategoryID;
			$bug->summary = $this->Summery;
			$bug->last_updated = strtotime( date( 'Y-m-d H:i:s' ) );
			$bug->Save();
			
			return $bug->id;
		}
		return false;
	}
}

?>
