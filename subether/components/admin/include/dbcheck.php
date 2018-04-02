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

if ( !$_SESSION[ 'dbchecked_admin' ] )
{
	
	$_SESSION[ 'dbchecked_admin' ] = 1;
	
	/* --- SBookCaseList --------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookCaseList' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookCaseList`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `CaseID` bigint(20) NOT NULL DEFAULT \'0\',
			  `CategoryID` bigint(20) DEFAULT NULL,
			  `Type` varchar(255) DEFAULT NULL,
			  `Name` varchar(255) DEFAULT NULL,
			  `Description` text NOT NULL,
			  `Progress` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$userid = false;
		$files = false;
		$datecreated = false;
		$datemodified = false;
		$deadline = false;
		$products = false;
		$events = false;
		$status = false;
		$clientid = false;
		$comments = false;
		$projectid = false;
		$history = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'UserID' )
			{
				$userid = true;
			}
			if ( $name == 'Files' )
			{
				$files = true;
			}
			if ( $name == 'DateCreated' )
			{
				$datecreated = true;
			}
			if ( $name == 'DateModified' )
			{
				$datemodified = true;
			}
			if ( $name == 'Deadline' )
			{
				$deadline = true;
			}
			if ( $name == 'Products' )
			{
				$products = true;
			}
			if ( $name == 'Events' )
			{
				$events = true;
			}
			if ( $name == 'Status' )
			{
				$status = true;
			}
			if ( $name == 'ClientID' )
			{
				$clientid = true;
			}
			if ( $name == 'Comments' )
			{
				$comments = true;
			}
			if ( $name == 'ProjectID' )
			{
				$projectid = true;
			}
			if ( $name == 'History' )
			{
				$history = true;
			}
		}
		if ( !$userid )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `UserID` bigint(20) NOT NULL DEFAULT \'0\' 
				AFTER `CategoryID`
			' );
		}
		if ( !$files )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `Files` text NOT NULL 
				AFTER `Description`
			' );
		}
		if ( !$datecreated )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `DateCreated` datetime NOT NULL  
				AFTER `Progress`
			' );
		}
		if ( !$datemodified )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `DateModified` datetime NOT NULL  
				AFTER `Progress`
			' );
		}
		if ( !$deadline )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `Deadline` datetime NOT NULL  
				AFTER `Progress`
			' );
		}
		if ( !$products )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `Products` text NOT NULL 
				AFTER `Description` 
			' );
		}
		if ( !$events )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `Events` text NOT NULL 
				AFTER `Description` 
			' );
		}
		if ( !$status )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `Status` varchar(255) NOT NULL 
				AFTER `Files` 
			' );
		}
		if ( !$clientid )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `ClientID` bigint(20) NOT NULL DEFAULT \'0\' 
				AFTER `UserID` 
			' );
		}
		if ( !$comments )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `Comments` text NOT NULL 
				AFTER `Description` 
			' );
		}
		if ( !$projectid )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `ProjectID` bigint(20) NOT NULL DEFAULT \'0\' 
				AFTER `ClientID` 
			' );
		}
		if ( !$history )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `History` text NOT NULL 
				AFTER `Files` 
			' );
		}
	}
	
}

?>
