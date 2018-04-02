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

if ( !$_SESSION[ 'dbchecked_chat' ] )
{
	
	$_SESSION[ 'dbchecked_chat' ] = 1;
	
	/* --- SBookMailAccounts --------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookMailAccounts' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookMailAccounts`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `UserID` bigint(20) NOT NULL DEFAULT \'0\',
			  `CategoryID` bigint(20) DEFAULT NULL,
			  `Address` varchar(255) DEFAULT NULL,
			  `Server` varchar(255) DEFAULT NULL,
			  `Port` bigint(20) DEFAULT NULL,
			  `Username` varchar(255) DEFAULT NULL,
			  `Password` varchar(255) DEFAULT NULL,
			  `OutServer` varchar(255) DEFAULT NULL,
			  `OutPort` bigint(20) DEFAULT NULL,
			  `OutUser` varchar(255) DEFAULT NULL,
			  `OutPass` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$folders = false;
		$ssl = false;
		$isdeleted = false;
		$sortorder = false;
		$datecreated = false;
		$signature = false;
		$errorcode = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'Folders' )
			{
				$folders = true;
			}
			if ( $name == 'SSL' )
			{
				$ssl = true;
			}
			if ( $name == 'IsDeleted' )
			{
				$isdeleted = true;
			}
			if ( $name == 'SortOrder' )
			{
				$sortorder = true;
			}
			if ( $name == 'DateCreated' )
			{
				$datecreated = true;
			}
			if ( $name == 'Signature' )
			{
				$signature = true;
			}
			if ( $name == 'ErrorCode' )
			{
				$errorcode = true;
			}
		}
		if ( !$folders )
		{
			$database->query ( '
				ALTER TABLE SBookMailAccounts
				ADD `Folders` text NOT NULL
				AFTER `OutPass`
			' );
		}
		if ( !$ssl )
		{
			$database->query ( '
				ALTER TABLE SBookMailAccounts
				ADD `SSL` tinyint(4) NOT NULL DEFAULT \'0\' 
				AFTER `Password`
			' );
		}
		if ( !$isdeleted )
		{
			$database->query ( '
				ALTER TABLE SBookMailAccounts
				ADD `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\' 
				AFTER `OutPass`
			' );
		}
		if ( !$sortorder )
		{
			$database->query ( '
				ALTER TABLE SBookMailAccounts
				ADD `SortOrder` bigint(20) NOT NULL DEFAULT \'0\' 
				AFTER `OutPass`
			' );
		}
		if ( !$datecreated )
		{
			$database->query ( '
				ALTER TABLE SBookMailAccounts
				ADD `DateCreated` datetime NOT NULL  
				AFTER `OutPass`
			' );
		}
		if ( !$signature )
		{
			$database->query ( '
				ALTER TABLE SBookMailAccounts
				ADD `Signature` text NOT NULL 
				AFTER `OutPass`
			' );
		}
		if ( !$errorcode )
		{
			$database->query ( '
				ALTER TABLE SBookMailAccounts
				ADD `ErrorCode` bigint(20) NOT NULL DEFAULT \'0\' 
				AFTER `SortOrder`
			' );
		}
	}
	
	/* --- SBookMailHeaders -------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookMailHeaders' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookMailHeaders`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `UserID` bigint(20) NOT NULL DEFAULT \'0\',
			  `CategoryID` bigint(20) DEFAULT NULL,
			  `AccountID` bigint(20) DEFAULT NULL,
			  `FolderID` bigint(20) DEFAULT NULL,
			  `Subject` varchar(255) DEFAULT NULL,
			  `From` varchar(255) DEFAULT NULL,
			  `To` varchar(255) DEFAULT NULL,
			  `ReplyTo` varchar(255) DEFAULT NULL,
			  `MessageID` bigint(20) DEFAULT NULL,
			  `Date` datetime NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$isread = false;
		$files = false;
		$isdeleted = false;
		$message = false;
		$folder = false;
		$movedto = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'IsRead' )
			{
				$isread = true;
			}
			if ( $name == 'Files' )
			{
				$files = true;
			}
			if ( $name == 'IsDeleted' )
			{
				$isdeleted = true;
			}
			if ( $name == 'Message' )
			{
				$message = true;
			}
			if ( $name == 'Folder' )
			{
				$folder = true;
			}
			if ( $name == 'MovedTo' )
			{
				$movedto = true;
			}
		}
		if ( !$isread )
		{
			$database->query ( '
				ALTER TABLE SBookMailHeaders
				ADD `IsRead` tinyint(4) NOT NULL DEFAULT \'0\' 
				AFTER `Date`
			' );
		}
		if ( !$files )
		{
			$database->query ( '
				ALTER TABLE SBookMailHeaders
				ADD `Files` text NOT NULL 
				AFTER `MessageID`
			' );
		}
		if ( !$isdeleted )
		{
			$database->query ( '
				ALTER TABLE SBookMailHeaders
				ADD `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\' 
				AFTER `IsRead`
			' );
		}
		if ( !$message )
		{
			$database->query ( '
				ALTER TABLE SBookMailHeaders
				ADD `Message` text NOT NULL 
				AFTER `ReplyTo`
			' );
		}
		if ( !$folder )
		{
			$database->query ( '
				ALTER TABLE SBookMailHeaders
				ADD `Folder` varchar(255) DEFAULT NULL 
				AFTER `FolderID`
			' );
		}
		if ( !$movedto )
		{
			$database->query ( '
				ALTER TABLE SBookMailHeaders
				ADD `MovedTo` varchar(255) NOT NULL 
				AFTER `Folder`
			' );
		}
	}
	
}

?>
