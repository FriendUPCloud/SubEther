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

if ( !$_SESSION[ 'dbchecked' ] || $_SESSION['dbchecked'] == 1 )
{
	/* --- SBookCaseList -------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookCaseList' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookCaseList`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `CaseID` bigint(20) NOT NULL DEFAULT \'0\',
			  `FileID` bigint(20) NOT NULL DEFAULT \'0\',
			  `CategoryID` bigint(20) DEFAULT NULL,
			  `UserID` bigint(20) NOT NULL DEFAULT \'0\',
			  `ClientID` bigint(20) NOT NULL DEFAULT \'0\',
			  `ProjectID` bigint(20) NOT NULL DEFAULT \'0\',
			  `Type` varchar(255) DEFAULT NULL,
			  `Name` varchar(255) DEFAULT NULL,
			  `Description` text,
			  `Comments` text NOT NULL,
			  `Events` text NOT NULL,
			  `Products` text NOT NULL,
			  `Files` text NOT NULL,
			  `History` text NOT NULL,
			  `Status` varchar(255) NOT NULL,
			  `IsFinished` bigint(20) NOT NULL DEFAULT \'0\',
			  `Progress` varchar(255) DEFAULT \'0%\',
			  `Deadline` datetime NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `DateCreated` datetime NOT NULL,
			PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$isfinished = false;
		$fileid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'IsFinished' )
			{
				$isfinished = true;
			}
			if ( $name == 'FileID' )
			{
				$fileid = true;
			}
		}
		if ( !$isfinished )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `IsFinished` bigint(20) NOT NULL default \'0\' 
				AFTER `Status`
			' );
		}
		if ( !$fileid )
		{
			$database->query ( '
				ALTER TABLE SBookCaseList
				ADD `FileID` bigint(20) NOT NULL default \'0\' 
				AFTER `CaseID`
			' );
		}
	}
	
	/* --- SBookCategory ------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookCategory' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookCategory`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `UniqueID` varchar(255) NOT NULL,
			  `CategoryID` bigint(20) NOT NULL,
			  `Type` varchar(255) DEFAULT NULL,
			  `Name` varchar(255) DEFAULT NULL,
			  `Privacy` varchar(255) DEFAULT NULL,
			  `Settings` text,
			  `Description` text,
			  `Owner` bigint(20) NOT NULL DEFAULT \'0\',
			  `ParentID` bigint(20) NOT NULL DEFAULT \'0\',
			  `IsSystem` tinyint(4) NOT NULL DEFAULT \'0\',
			  `SortOrder` bigint(20) NOT NULL DEFAULT \'0\',
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$owner = false;
		$parentid = false;
		$uniqueid = false;
		$sortorder = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'Owner' )
			{
				$owner = true;
			}
			if ( $name == 'ParentID' )
			{
				$parentid = true;
			}
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
			if ( $name == 'SortOrder' )
			{
				$sortorder = true;
			}
		}
		if ( !$owner )
		{
			$database->query ( '
				ALTER TABLE SBookCategory
				ADD `Owner` bigint(20) NOT NULL default \'0\' 
				AFTER `Description`
			' );
		}
		if ( !$parentid )
		{
			$database->query ( '
				ALTER TABLE SBookCategory
				ADD `ParentID` bigint(20) NOT NULL default \'0\' 
				AFTER `Owner`
			' );
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE SBookCategory
				ADD `UniqueID` varchar(255) NOT NULL 
				AFTER `ID`
			' );
		}
		if ( !$sortorder )
		{
			$database->query ( '
				ALTER TABLE SBookCategory
				ADD `SortOrder` bigint(20) NOT NULL default \'0\' 
				AFTER `IsSystem`
			' );
		}
	}
	
	/* --- SBookCategoryRelation ----------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookCategoryRelation' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookCategoryRelation`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `CategoryID` bigint(20) NOT NULL DEFAULT \'0\',
			  `ObjectType` varchar(255) NOT NULL DEFAULT \'\',
			  `ObjectID` bigint(20) NOT NULL DEFAULT \'0\',
			  `Permission` varchar(255) NOT NULL DEFAULT \'\',
			  `SortOrder` bigint(20) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookCategoryAccess ----------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookCategoryAccess' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookCategoryAccess`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `CategoryID` bigint(20) NOT NULL DEFAULT \'0\',
			  `UserID` bigint(20) NOT NULL DEFAULT \'0\',
			  `ContactID` bigint(20) NOT NULL,
			  `Access` varchar(255) NOT NULL,
			  `Read` tinyint(4) NOT NULL DEFAULT \'0\',
			  `Write` tinyint(4) NOT NULL DEFAULT \'0\',
			  `Delete` tinyint(4) NOT NULL DEFAULT \'0\',
			  `Admin` tinyint(4) NOT NULL DEFAULT \'0\',
			  `Owner` tinyint(4) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$access = false;
		$memberid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'Access' )
			{
				$access = true;
			}
			if ( $name == 'MemberID' )
			{
				$memberid = true;
			}
		}
		if ( !$access )
		{
			$database->query ( '
				ALTER TABLE SBookCategoryAccess
				ADD `Access` varchar(255) NOT NULL 
				AFTER `ContactID`
			' );
		}
		if ( !$memberid )
		{
			$database->query ( '
				ALTER TABLE SBookCategoryAccess
				ADD `MemberID` bigint(20) NOT NULL 
				AFTER `ContactID`
			' );
		}
	}
	
	/* --- SBookChat ---------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookChat' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookChat`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `SenderID` bigint(20) NOT NULL,
			  `Message` text NOT NULL,
			  `Date` datetime NOT NULL,
			  `CategoryID` bigint(20) NOT NULL DEFAULT \'0\',
			  `Type` varchar(255) NOT NULL,
			  `Status` varchar(255) NOT NULL,
			  PRIMARY KEY (`ID`),
			  UNIQUE KEY `ID` (`ID`,`SenderID`)
			)
		' );
	}
	
	/* --- SBookContact ------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookContact' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookContact`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `ImageID` bigint(20) NOT NULL DEFAULT \'0\',
			  `UserID` bigint(20) NOT NULL DEFAULT \'0\',
			  `SortOrder` int(11) NOT NULL DEFAULT \'0\',
			  `Username` varchar(255) DEFAULT NULL,
			  `AuthKey` varchar(255) DEFAULT NULL,
			  `Firstname` varchar(255) DEFAULT NULL,
			  `Middlename` varchar(255) DEFAULT NULL,
			  `Lastname` varchar(255) DEFAULT NULL,
			  `Gender` varchar(255) DEFAULT NULL,
			  `Languages` text,
			  `Alternate` varchar(255) DEFAULT NULL,
			  `ScreenName` varchar(255) DEFAULT NULL,
			  `Website` varchar(255) DEFAULT NULL,
			  `Address` varchar(255) DEFAULT NULL,
			  `Country` varchar(255) DEFAULT NULL,
			  `City` varchar(255) DEFAULT NULL,
			  `Postcode` varchar(255) DEFAULT NULL,
			  `Telephone` varchar(255) DEFAULT NULL,
			  `Mobile` varchar(255) DEFAULT NULL,
			  `Email` varchar(255) DEFAULT NULL,
			  `Work` text,
			  `College` text,
			  `HighSchool` text,
			  `Interests` text,
			  `Philosophy` varchar(255) DEFAULT NULL,
			  `Religion` varchar(255) DEFAULT NULL,
			  `Political` varchar(255) DEFAULT NULL,
			  `About` text,
			  `Quotations` text,
			  `Data` text,
			  `Custom` text,
			  `Image` bigint(20) DEFAULT NULL,
			  `Birthdate` datetime DEFAULT NULL,
			  `DateCreated` datetime DEFAULT NULL,
			  `DateModified` datetime DEFAULT NULL,
			  `Theme` bigint(20) NOT NULL DEFAULT \'0\',
			  `ShowAlternate` tinyint(4) DEFAULT \'0\',
			  `Display` tinyint(4) DEFAULT \'0\',
			  `IsMail` tinyint(4) DEFAULT \'0\',
			  `IsSMS` tinyint(4) DEFAULT \'0\',
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$theme = false;
		$custom = false;
		$birthdate = false;
		$uniqueid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'Theme' )
			{
				$theme = true;
			}
			if ( $name == 'Custom' )
			{
				$custom = true;
			}
			if ( $name == 'Birthdate' )
			{
				$birthdate = true;
			}
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
		}
		if ( !$theme )
		{
			$database->query ( '
				ALTER TABLE SBookContact
				ADD `Theme` bigint(20) NOT NULL default \'0\' 
				AFTER `DateModified`
			' );
		}
		if ( !$custom )
		{
			$database->query ( '
				ALTER TABLE SBookContact
				ADD `Custom` text 
				AFTER `Data`
			' );
		}
		if ( !$birthdate )
		{
			$database->query ( '
				ALTER TABLE SBookContact
				ADD `Birthdate` datetime DEFAULT NULL
				AFTER `Image`
			' );
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE SBookContact
				ADD `UniqueID` varchar(255) DEFAULT NULL
				AFTER `ID`
			' );
		}
	}
	
	/* --- SBookContactRelation ------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookContactRelation' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookContactRelation`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `ContactID` bigint(20) NOT NULL DEFAULT \'0\',
			  `ObjectType` varchar(255) NOT NULL DEFAULT \'\',
			  `ObjectID` bigint(20) NOT NULL DEFAULT \'0\',
			  `IsApproved` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsNoticed` tinyint(4) NOT NULL DEFAULT \'0\',
			  `SortOrder` bigint(20) NOT NULL DEFAULT \'0\',
			  `NodeID` bigint(20) NOT NULL, 
			  `NodeMainID` bigint(20) NOT NULL, 
			  `DateCreated` datetime DEFAULT NULL, 
			  `DateModified` datetime DEFAULT NULL, 
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$datecreated = false;
		$datemodified = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'DateCreated' )
			{
				$datecreated = true;
			}
			if ( $name == 'DateModified' )
			{
				$datemodified = true;
			}

		}
		if ( !$datemodified )
		{
			$database->query ( '
				ALTER TABLE SBookContactRelation
				ADD `DateModified` datetime DEFAULT NULL
				AFTER `NodeMainID`
			' );
		}
		if ( !$datecreated )
		{
			$database->query ( '
				ALTER TABLE SBookContactRelation
				ADD `DateCreated` datetime DEFAULT NULL
				AFTER `NodeMainID`
			' );
		}
	}
	
	/* --- SBookEvents --------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookEvents' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookEvents`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `UniqueID` varchar(255) NOT NULL,
			  `Component` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `Place` varchar(255) NOT NULL,
			  `Details` text NOT NULL,
			  `Price` varchar(255) NOT NULL,
			  `ExternalUrl` varchar(255) NOT NULL,
			  `DateStart` datetime NOT NULL,
			  `DateEnd` datetime NOT NULL,
			  `Slots` bigint(20) NOT NULL,
			  `Limit` bigint(20) NOT NULL,
			  `Hours` double NOT NULL,
			  `UserID` bigint(20) NOT NULL,
			  `ImageID` bigint(20) NOT NULL DEFAULT \'0\',
			  `CategoryID` bigint(20) NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `IsFinished` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\',
			  `Access` tinyint(4) NOT NULL DEFAULT \'0\',
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$imageid = false;
		$price = false;
		$slots = false;
		$limit = false;
		$uniqueid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'ImageID' )
			{
				$imageid = true;
			}
			if ( $name == 'Price' )
			{
				$price = true;
			}
			if ( $name == 'Slots' )
			{
				$slots = true;
			}
			if ( $name == 'Limit' )
			{
				$limit = true;
			}
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
		}
		if ( !$imageid )
		{
			$database->query ( '
				ALTER TABLE SBookEvents
				ADD `ImageID` bigint(20) NOT NULL default \'0\' 
				AFTER `UserID`
			' );
		}
		if ( !$price )
		{
			$database->query ( '
				ALTER TABLE SBookEvents
				ADD `Price` varchar(255) NOT NULL 
				AFTER `Details`
			' );
		}
		if ( !$slots )
		{
			$database->query ( '
				ALTER TABLE SBookEvents
				ADD `Slots` bigint(20) NOT NULL 
				AFTER `DateEnd`
			' );
		}
		if ( !$limit )
		{
			$database->query ( '
				ALTER TABLE SBookEvents
				ADD `Limit` bigint(20) NOT NULL 
				AFTER `Slots`
			' );
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE SBookEvents
				ADD `UniqueID` varchar(255) NOT NULL 
				AFTER `ID` 
			' );
		}
	}
	
	/* --- SBookHours --------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookHours' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookHours`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `UserID` bigint(20) NOT NULL,
			  `Title` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Role` varchar(255) NOT NULL,
			  `ProjectID` bigint(20) NOT NULL,
			  `DateStart` datetime NOT NULL,
			  `DateEnd` datetime NOT NULL,
			  `Hours` double NOT NULL,
			  `Hours50` double NOT NULL,
			  `Hours100` double NOT NULL,
			  `IsNight` tinyint(4) NOT NULL DEFAULT \'0\',
			  `Details` text NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `Extra` bigint(20) NOT NULL,
			  `Price` bigint(20) NOT NULL,
			  `Requests` text NOT NULL,
			  `IsReady` bigint(20) NOT NULL DEFAULT \'0\',
			  `IsAccepted` bigint(20) NOT NULL DEFAULT \'0\',
			  `IsFinished` bigint(20) NOT NULL DEFAULT \'0\',
			  `IsPaid` bigint(20) NOT NULL,
			  `IsPayable` bigint(20) NOT NULL,
			  `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\',
			  `Access` tinyint(4) NOT NULL DEFAULT \'0\',
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			  `F1` text NOT NULL,
			  `F2` text NOT NULL,
			  `F3` text NOT NULL,
			  `F4` text NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$isready = false;
		$isaccepted = false;
		$isfinished = false;
		$requests = false;
		$f1 = false;
		$f2 = false;
		$f3 = false;
		$f4 = false;
		$hours100 = false;
		$hours50 = false;
		$groupid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'IsReady' )
			{
				$isready = true;
			}
			if ( $name == 'IsAccepted' )
			{
				$isaccepted = true;
			}
			if ( $name == 'IsFinished' )
			{
				$isaccepted = true;
			}
			if ( $name == 'Requests' )
			{
				$requests = true;
			}
			if ( $name == 'F1' )
			{
				$f1 = true;
			}
			if ( $name == 'F2' )
			{
				$f2 = true;
			}
			if ( $name == 'F3' )
			{
				$f3 = true;
			}
			if ( $name == 'F4' )
			{
				$f4 = true;
			}
			if ( $name == 'Hours100' )
			{
				$hours100 = true;
			}
			if ( $name == 'Hours50' )
			{
				$hours50 = true;
			}
			if ( $name == 'GroupID' )
			{
				$groupid = true;
			}
		}
		if ( !$isaccepted )
		{
			$database->query ( '
				ALTER TABLE SBookHours
				ADD `IsAccepted` bigint(20) NOT NULL default \'0\' 
				AFTER `Price`
			' );
		}
		if ( !$isfinished )
		{
			$database->query ( '
				ALTER TABLE SBookHours
				ADD `IsFinished` bigint(20) NOT NULL default \'0\' 
				AFTER `IsAccepted`
			' );
		}
		if ( !$requests )
		{
			$database->query ( '
				ALTER TABLE SBookHours
				ADD `Requests` text NOT NULL 
				AFTER `Price`
			' );
		}
		if ( !$isready )
		{
			$database->query ( '
				ALTER TABLE SBookHours
				ADD `IsReady` bigint(20) NOT NULL default \'0\' 
				AFTER `Requests`
			' );
		}
		if ( !$f4 )
		{
			$database->query ( '
				ALTER TABLE SBookHours 
				ADD `F4` text NOT NULL 
				AFTER `NodeMainID` 
			' );
		}
		if ( !$f3 )
		{
			$database->query ( '
				ALTER TABLE SBookHours 
				ADD `F3` text NOT NULL 
				AFTER `NodeMainID` 
			' );
		}
		if ( !$f2 )
		{
			$database->query ( '
				ALTER TABLE SBookHours 
				ADD `F2` text NOT NULL 
				AFTER `NodeMainID` 
			' );
		}
		if ( !$f1 )
		{
			$database->query ( '
				ALTER TABLE SBookHours 
				ADD `F1` text NOT NULL 
				AFTER `NodeMainID` 
			' );
		}
		if ( !$hours100 )
		{
			$database->query ( '
				ALTER TABLE SBookHours 
				ADD `Hours100` double NOT NULL 
				AFTER `Hours` 
			' );
		}
		if ( !$hours50 )
		{
			$database->query ( '
				ALTER TABLE SBookHours 
				ADD `Hours50` double NOT NULL 
				AFTER `Hours` 
			' );
		}
		if ( !$groupid )
		{
			$database->query ( '
				ALTER TABLE SBookHours 
				ADD `GroupID` varchar(255) NOT NULL 
				AFTER `ProjectID` 
			' );
		}
	}
	
	/* --- SBookFiles ---------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookFiles' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookFiles`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `Parent` bigint(20) NOT NULL DEFAULT \'0\',
			  `Title` varchar(128) DEFAULT NULL,
			  `Filename` varchar(255) DEFAULT NULL,
			  `ContentData` text,
			  `Tags` text NOT NULL,
			  `FileFolder` int(11) DEFAULT \'0\',
			  `Filesize` int(11) DEFAULT NULL,
			  `Fileduration` varchar(255) NOT NULL,
			  `DateCreated` datetime DEFAULT NULL,
			  `DateModified` datetime DEFAULT NULL,
			  `SortOrder` bigint(20) DEFAULT \'0\',
			  `Filetype` varchar(16) DEFAULT NULL,
			  `MediaID` bigint(20) NOT NULL DEFAULT \'0\',
			  `MediaType` varchar(255) NOT NULL,
			  `Rating` varchar(255) NOT NULL DEFAULT \'0/0\',
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookMail ------------------------------------------------------------------------------ */
	
	$t = new cDatabaseTable ( 'SBookMail' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookMail`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `UniqueID` varchar(255) NOT NULL,
			  `UniqueKey` varchar(255) NOT NULL, 
			  `ContactID` bigint(20) NOT NULL,
			  `SenderID` bigint(20) NOT NULL,
			  `ReceiverID` bigint(20) NOT NULL,
			  `Message` text NOT NULL,
			  `CategoryID` bigint(20) NOT NULL DEFAULT \'0\',
			  `Type` varchar(255) NOT NULL,
			  `Encryption` varchar(255) NOT NULL,
			  `EncryptionKey` text NOT NULL,
			  `PublicKey` text NOT NULL,
			  `IsCrypto` tinyint(4) NOT NULL,
			  `IsTyping` tinyint(4) NOT NULL,
			  `IsRead` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsNoticed` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsAlerted` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsAccepted` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsConnected` tinyint(4) NOT NULL DEFAULT \'0\',
			  `Date` datetime NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `IsProcessed` bigint(20) NOT NULL,
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$istyping = false;
		$datemodified = false;
		$isalerted = false;
		$encryption = false;
		$iscrypto = false;
		$uniqueid = false;
		$isaccepted = false;
		$isconnected = false;
		$uniquekey = false;
		$encryptionkey = false;
		$publickey = false;
		$contactid = false;
		
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'IsTyping' )
			{
				$istyping = true;
			}
			if ( $name == 'DateModified' )
			{
				$datemodified = true;
			}
			if ( $name == 'IsAlerted' )
			{
				$isalerted = true;
			}
			if ( $name == 'Encryption' )
			{
				$encryption = true;
			}
			if ( $name == 'IsCrypto' )
			{
				$iscrypto = true;
			}
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
			if ( $name == 'IsAccepted' )
			{
				$isaccepted = true;
			}
			if ( $name == 'IsConnected' )
			{
				$isconnected = true;
			}
			if ( $name == 'UniqueKey' )
			{
				$uniquekey = true;
			}
			if ( $name == 'EncryptionKey' )
			{
				$encryptionkey = true;
			}
			if ( $name == 'PublicKey' )
			{
				$publickey = true;
			}
			if ( $name == 'ContactID' )
			{
				$contactid = true;
			}
		}
		if ( !$istyping )
		{
			$database->query ( '
				ALTER TABLE SBookMail
				ADD `IsTyping` tinyint(4) NOT NULL default \'0\' 
				AFTER `Type`
			' );
		}
		if ( !$datemodified )
		{
			$database->query ( '
				ALTER TABLE SBookMail
				ADD `DateModified` datetime NOT NULL 
				AFTER `Date`
			' );
		}
		if ( !$isalerted )
		{
			$database->query ( '
				ALTER TABLE SBookMail
				ADD `IsAlerted` tinyint(4) NOT NULL default \'0\' 
				AFTER `IsNoticed`
			' );
		}
		if ( !$encryption )
		{
			$database->query ( '
				ALTER TABLE SBookMail
				ADD `Encryption` varchar(255) NOT NULL 
				AFTER `Type`
			' );
		}
		if ( !$iscrypto )
		{
			$database->query ( '
				ALTER TABLE SBookMail
				ADD `IsCrypto` tinyint(4) NOT NULL 
				AFTER `Encryption`
			' );
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE SBookMail
				ADD `UniqueID` varchar(255) NOT NULL
				AFTER `ID`
			' );
		}
		if ( !$isaccepted )
		{
			$database->query ( '
				ALTER TABLE SBookMail
				ADD `IsAccepted` tinyint(4) NOT NULL 
				AFTER `IsAlerted`
			' );
		}
		if ( !$isconnected )
		{
			$database->query ( '
				ALTER TABLE SBookMail
				ADD `IsConnected` tinyint(4) NOT NULL 
				AFTER `IsAccepted`
			' );
		}
		if ( !$uniquekey )
		{
			$database->query ( '
				ALTER TABLE SBookMail
				ADD `UniqueKey` varchar(255) NOT NULL 
				AFTER `UniqueID`
			' );
		}
		if ( !$publickey )
		{
			$database->query ( '
				ALTER TABLE SBookMail
				ADD `PublicKey` text NOT NULL 
				AFTER `Encryption`
			' );
		}
		if ( !$encryptionkey )
		{
			$database->query ( '
				ALTER TABLE SBookMail
				ADD `EncryptionKey` text NOT NULL 
				AFTER `Encryption`
			' );
		}
		if ( !$contactid )
		{
			$database->query ( '
				ALTER TABLE SBookMail
				ADD `ContactID` bigint(20) NOT NULL 
				AFTER `UniqueKey`
			' );
		}
	}
	
	/* --- SBookMediaRelation -------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookMediaRelation' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookMediaRelation`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `MediaID` bigint(20) NOT NULL DEFAULT \'0\',
			  `MediaType` varchar(255) NOT NULL DEFAULT \'\',
			  `UserID` bigint(20) NOT NULL DEFAULT \'0\',
			  `CategoryID` bigint(20) NOT NULL DEFAULT \'0\',
			  `Title` varchar(255) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `Tags` varchar(255) NOT NULL DEFAULT \'\',
			  `SortOrder` bigint(20) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookMessage -------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookMessage' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookMessage`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `UniqueID` varchar(255) NOT NULL,
			  `Component` varchar(255) NOT NULL,
			  `SenderID` bigint(20) NOT NULL,
			  `ReceiverID` bigint(20) NOT NULL,
			  `CategoryID` bigint(20) NOT NULL,
			  `Subject` varchar(255) NOT NULL,
			  `Leadin` text NOT NULL,
			  `Message` text NOT NULL,
			  `Data` text NOT NULL,
			  `HTML` text NOT NULL,
			  `Options` text NOT NULL,
			  `Date` datetime NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `ThreadID` bigint(20) NOT NULL DEFAULT \'0\',
			  `ParentID` bigint(20) NOT NULL DEFAULT \'0\',
			  `IsNoticed` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsRead` tinyint(4) NOT NULL DEFAULT \'0\',
			  `SeenBy` text NOT NULL,
			  `ReadBy` text NOT NULL,
			  `Tags` text NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Status` varchar(255) NOT NULL,
			  `RateDownBy` text NOT NULL,
			  `RateUpBy` text NOT NULL,
			  `Rating` text NOT NULL,
			  `Access` tinyint(4) NOT NULL DEFAULT \'0\',
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`),
			  UNIQUE KEY `ID` (`ID`,`SenderID`)
			)
		' );
	}
	else
	{
		$access = false;
		$readby = false;
		$rateupby = false;
		$ratedownby = false;
		$options = false;
		$component = false;
		$uniqueid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'Access' )
			{
				$access = true;
			}
			if ( $name == 'ReadBy' )
			{
				$readby = true;
			}
			if ( $name == 'RateUpBy' )
			{
				$rateupby = true;
			}
			if ( $name == 'RateDownBy' )
			{
				$ratedownby = true;
			}
			if ( $name == 'Options' )
			{
				$options = true;
			}
			if ( $name == 'Component' )
			{
				$component = true;
			}
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
		}
		if ( !$access )
		{
			$database->query ( '
				ALTER TABLE SBookMessage
				ADD `Access` tinyint(4) NOT NULL default \'0\' 
				AFTER `Rating`
			' );
		}
		if ( !$readby )
		{
			$database->query ( '
				ALTER TABLE SBookMessage
				ADD `ReadBy` text NOT NULL 
				AFTER `SeenBy`
			' );
		}
		if ( !$rateupby )
		{
			$database->query ( '
				ALTER TABLE SBookMessage
				ADD `RateUpBy` text NOT NULL 
				AFTER `Status`
			' );
		}
		if ( !$ratedownby )
		{
			$database->query ( '
				ALTER TABLE SBookMessage
				ADD `RateDownBy` text NOT NULL 
				AFTER `Status`
			' );
		}
		if ( !$options )
		{
			$database->query ( '
				ALTER TABLE SBookMessage
				ADD `Options` text NOT NULL 
				AFTER `HTML`
			' );
		}
		if ( !$component )
		{
			$database->query ( '
				ALTER TABLE SBookMessage
				ADD `Component` varchar(255) NOT NULL 
				AFTER `ID`
			' );
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE SBookMessage
				ADD `UniqueID` varchar(255) NOT NULL 
				AFTER `ID`
			' );
		}
	}
	
	
	/* --- SBookStats -------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookStats' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookStats`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `SenderID` bigint(20) NOT NULL,
			  `ReceiverID` bigint(20) NOT NULL,
			  `CategoryID` bigint(20) NOT NULL,
			  `Component` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Action` varchar(255) NOT NULL,
			  `Counter` bigint(20) NOT NULL,
			  `DataSource` varchar(255) NOT NULL,
			  `Data` text NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookSettings -------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookSettings' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookSettings`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `Component` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `Counter` varchar(255) NOT NULL,
			  `ImageID` bigint(20) NOT NULL,
			  `Data` text NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  `DateModified` datetime NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookNews ---------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookNews' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookNews`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `Title` varchar(255) NOT NULL,
			  `Leadin` text NOT NULL,
			  `Article` text NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Tags` varchar(255) NOT NULL,
			  `CategoryID` bigint(20) NOT NULL,
			  `PostedID` bigint(20) NOT NULL,
			  `MediaType` varchar(255) NOT NULL,
			  `MediaID` bigint(20) NOT NULL,
			  `DateAdded` datetime NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `DateExpired` datetime NOT NULL,
			  `IsPublished` tinyint(4) NOT NULL,
			  `IsSticky` tinyint(4) NOT NULL,
			  `IsFocus` tinyint(4) NOT NULL,
			  `SortOrder` bigint(20) NOT NULL,
			  `Rating` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookNotification --------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookNotification' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookNotification`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `ObjectID` bigint(20) NOT NULL DEFAULT \'0\',
			  `Type` varchar(255) NOT NULL DEFAULT \'\',
			  `Command` varchar(255) NOT NULL,
			  `SenderID` bigint(20) NOT NULL DEFAULT \'0\',
			  `ReceiverID` varchar(1000) NOT NULL DEFAULT \'0\',
			  `IsRead` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsNoticed` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsOnline` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsAccepted` bigint(20) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$isaccepted = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'IsAccepted' )
			{
				$isaccepted = true;
			}
		}
		if ( !$isaccepted )
		{
			$database->query ( '
				ALTER TABLE SBookNotification
				ADD `IsAccepted` bigint(20) NOT NULL default \'0\' 
				AFTER `IsOnline`
			' );
		}
	}
	
	/* --- SBookRelation --------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookRelation' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookRelation`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `Type` varchar(255) NOT NULL,
			  `ObjectID` bigint(20) NOT NULL,
			  `ObjectType` varchar(255) NOT NULL,
			  `ConnectedID` bigint(20) NOT NULL,
			  `ConnectedType` varchar(255) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookStatus -------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookStatus' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookStatus`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `UserID` bigint(20) DEFAULT NULL,
			  `Status` varchar(255) DEFAULT NULL,
			  `Component` varchar(255) DEFAULT NULL,
			  `Module` varchar(255) DEFAULT NULL,
			  `CategoryID` bigint(20) DEFAULT NULL,
			  `UserAgent` text,
			  `LastActivity` datetime DEFAULT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$useragent = false;
		$datasource = false;
		$token = false;
		$datexpires = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'UserAgent' )
			{
				$useragent = true;
			}
			if ( $name == 'DataSource' )
			{
				$datasource = true;
			}
			if ( $name == 'Token' )
			{
				$token = true;
			}
			if ( $name == 'DateExpired' )
			{
				$datexpires = true;
			}
		}
		if ( !$useragent )
		{
			$database->query ( '
				ALTER TABLE SBookStatus 
				ADD `UserAgent` text 
				AFTER `CategoryID` 
			' );
		}
		if ( !$datasource )
		{
			$database->query ( '
				ALTER TABLE SBookStatus 
				ADD `DataSource` text 
				AFTER `CategoryID` 
			' );
		}
		if ( !$token )
		{
			$database->query ( '
				ALTER TABLE SBookStatus 
				ADD `Token` varchar(255) DEFAULT NULL 
				AFTER `ID` 
			' );
		}
		if ( !$datexpires )
		{
			$database->query ( '
				ALTER TABLE SBookStatus 
				ADD `DateExpired` datetime DEFAULT NULL 
				AFTER `LastActivity` 
			' );
		}
	}
	
	/* --- SBookVotes ----------------------------------------------------------------------------- */

	$t = new cDatabaseTable ( 'SBookVotes' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookVotes`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `ObjectID` bigint(20) NOT NULL DEFAULT \'0\',
			  `Type` varchar(255) NOT NULL DEFAULT \'\',
			  `VoteID` text,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SEngineContent ------------------------------------------------------------------------ */

	$t = new cDatabaseTable ( 'SEngineContent' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SEngineContent`
			(
			  `ID` bigint(255) NOT NULL AUTO_INCREMENT,
			  `Link` varchar(255) NOT NULL,
			  `Content` text NOT NULL,
			  `DateModified` datetime DEFAULT NULL,
			  `DateCreated` datetime DEFAULT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SEngineLinks -------------------------------------------------------------------------- */

	$t = new cDatabaseTable ( 'SEngineLinks' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SEngineLinks`
			(
			  `ID` bigint(255) NOT NULL AUTO_INCREMENT,
			  `Link` varchar(255) DEFAULT NULL,
			  `Links` text,
			  `KeyWords` text,
			  `Status` varchar(255) DEFAULT NULL,
			  `ContentID` bigint(255) DEFAULT \'0\',
			  `IsParsed` tinyint(4) DEFAULT \'0\',
			  `IsBroken` tinyint(4) DEFAULT \'0\',
			  `IsStored` tinyint(4) DEFAULT \'0\',
			  `DateModified` datetime DEFAULT NULL,
			  `DateCreated` datetime DEFAULT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SEngineSearch -------------------------------------------------------------------------- */

	$t = new cDatabaseTable ( 'SEngineSearch' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SEngineSearch`
			(
			  `ID` bigint(255) NOT NULL AUTO_INCREMENT,
			  `Title` varchar(255) NOT NULL,
			  `Link` varchar(255) NOT NULL,
			  `Description` text NOT NULL,
			  `Leadin` text NOT NULL,
			  `Links` text,
			  `KeyWords` text,
			  `ContentID` bigint(255) NOT NULL DEFAULT \'0\',
			  `Hits` bigint(255) NOT NULL DEFAULT \'0\',
			  `SortOrder` bigint(255) NOT NULL DEFAULT \'0\',
			  `DateModified` datetime DEFAULT NULL,
			  `DateCreated` datetime DEFAULT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SComponents --------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SComponents' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SComponents`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `Name` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Module` varchar(255) NOT NULL,
			  `Position` varchar(255) NOT NULL,
			  `UserLevels` varchar(255) NOT NULL,
			  `Categories` varchar(255) NOT NULL,
			  `SortOrder` bigint(20) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SModules ------------------------------------------------------------------------------ */
	
	$t = new cDatabaseTable ( 'SModules' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SModules`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `Name` varchar(255) NOT NULL,
			  `DisplayName` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Position` varchar(255) NOT NULL,
			  `UserLevels` varchar(255) NOT NULL,
			  `Visible` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsMain` tinyint(4) NOT NULL DEFAULT \'0\',
			  `SortOrder` bigint(20) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- STabs --------------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'STabs' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `STabs`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `Tab` varchar(255) NOT NULL,
			  `DisplayName` varchar(255) NOT NULL,
			  `Component` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Module` varchar(255) NOT NULL,
			  `Position` varchar(255) NOT NULL,
			  `Permission` varchar(255) NOT NULL,
			  `SortOrder` bigint(20) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SNodes ---------------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SNodes' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SNodes`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `IsMain` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsIndex` tinyint(4) NOT NULL DEFAULT \'0\',
			  `UniqueID` varchar(255) NOT NULL,
			  `PublicKey` text NOT NULL,
			  `UserID` bigint(20) NOT NULL,
			  `Url` varchar(255) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `Version` varchar(255) NOT NULL,
			  `Owner` varchar(255) NOT NULL,
			  `Email` varchar(255) NOT NULL,
			  `Uptime` varchar(255) NOT NULL,
			  `Location` varchar(255) NOT NULL,
			  `Users` varchar(255) NOT NULL,
			  `Modules` text NOT NULL,
			  `Components` text NOT NULL,
			  `Plugins` text NOT NULL,
			  `Themes` text NOT NULL,
			  `Release` text NOT NULL,
			  `AuthKey` varchar(255) NOT NULL,
			  `SessionID` varchar(255) NOT NULL,
			  `Rating` varchar(255) NOT NULL,
			  `Open` tinyint(4) NOT NULL DEFAULT \'0\',
			  `SortOrder` bigint(20) NOT NULL DEFAULT \'0\',
			  `IsPending` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsConnected` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsAllowed` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsDenied` tinyint(4) NOT NULL DEFAULT \'0\',
			  `DateModified` datetime DEFAULT NULL,
			  `DateLogin` datetime NOT NULL,
			  `DateCreated` datetime DEFAULT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$release = false;
		$ismain = false;
		$uniqueid = false;
		$publickey = false;
		$isindex = false;
		$datelogin = false;
		$modules = false;
		$nname = false;
		$plugins = false;
		$themes = false;
		$ispending = false;
		$isallowed = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'Release' )
			{
				$release = true;
			}
			if ( $name == 'IsMain' )
			{
				$ismain = true;
			}
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
			if ( $name == 'PublicKey' )
			{
				$publickey = true;
			}
			if ( $name == 'IsIndex' )
			{
				$isindex = true;
			}
			if ( $name == 'DateLogin' )
			{
				$datelogin = true;
			}
			if ( $name == 'Modules' )
			{
				$modules = true;
			}
			if ( $name == 'Name' )
			{
				$nname = true;
			}
			if ( $name == 'Plugins' )
			{
				$plugins = true;
			}
			if ( $name == 'Themes' )
			{
				$themes = true;
			}
			if ( $name == 'IsPending' )
			{
				$ispending = true;
			}
			if ( $name == 'IsAllowed' )
			{
				$isallowed = true;
			}
		}
		if ( !$release )
		{
			$database->query ( '
				ALTER TABLE SNodes 
				ADD `Release` text NOT NULL 
				AFTER `Components` 
			' );
		}
		if ( !$ismain )
		{
			$database->query ( '
				ALTER TABLE SNodes 
				ADD `IsMain` tinyint(4) NOT NULL DEFAULT \'0\'
				AFTER `ID` 
			' );
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE SNodes 
				ADD `UniqueID` varchar(255) NOT NULL 
				AFTER `IsMain` 
			' );
		}
		if ( !$publickey )
		{
			$database->query ( '
				ALTER TABLE SNodes 
				ADD `PublicKey` text NOT NULL  
				AFTER `UniqueID` 
			' );
		}
		if ( !$isindex )
		{
			$database->query ( '
				ALTER TABLE SNodes 
				ADD `IsIndex` tinyint(4) NOT NULL DEFAULT \'0\' 
				AFTER `IsMain` 
			' );
		}
		if ( !$datelogin )
		{
			$database->query ( '
				ALTER TABLE SNodes 
				ADD `DateLogin` datetime NOT NULL 
				AFTER `DateModified` 
			' );
		}
		if ( !$modules )
		{
			$database->query ( '
				ALTER TABLE SNodes 
				ADD `Modules` text NOT NULL 
				AFTER `Users` 
			' );
		}
		if ( !$nname )
		{
			$database->query ( '
				ALTER TABLE SNodes 
				ADD `Name` varchar(255) NOT NULL 
				AFTER `Url` 
			' );
		}
		if ( !$plugins )
		{
			$database->query ( '
				ALTER TABLE SNodes 
				ADD `Plugins` text NOT NULL 
				AFTER `Components` 
			' );
		}
		if ( !$themes )
		{
			$database->query ( '
				ALTER TABLE SNodes 
				ADD `Themes` text NOT NULL 
				AFTER `Plugins` 
			' );
		}
		if ( !$ispending )
		{
			$database->query ( '
				ALTER TABLE SNodes 
				ADD `IsPending` tinyint(4) NOT NULL DEFAULT \'0\' 
				AFTER `SortOrder` 
			' );
		}
		if ( !$isallowed )
		{
			$database->query ( '
				ALTER TABLE SNodes 
				ADD `IsAllowed` tinyint(4) NOT NULL DEFAULT \'0\' 
				AFTER `IsConnected` 
			' );
		}
	}
	
	/* --- SNodesRelation -------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SNodesRelation' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SNodesRelation`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `Field` varchar(255) NOT NULL,
			  `NodeID` bigint(20) NOT NULL,
			  `NodeType` varchar(255) NOT NULL,
			  `NodeValue` varchar(255) NOT NULL,
			  `ConnectedID` bigint(20) NOT NULL,
			  `ConnectedType` varchar(255) NOT NULL,
			  `ConnectedValue` varchar(255) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookBookmarks -------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookBookmarks' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookBookmarks`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `UserID` bigint(20) NOT NULL,
			  `Component` varchar(255) NOT NULL,
			  `Bookmarks` text NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookAccounts --------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookAccounts' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookAccounts`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `UniqueID` varchar(255) NOT NULL,
			  `UserID` bigint(20) NOT NULL,
			  `Security` bigint(20) NOT NULL,
			  `Account` bigint(20) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Balance` varchar(255) NOT NULL DEFAULT \'0\',
			  `Disposable` varchar(255) NOT NULL DEFAULT \'0\',
			  `Verified` tinyint(4) NOT NULL,
			  `IsFrozen` tinyint(4) NOT NULL,
			  `DateFrozen` datetime NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$uniqueid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE SBookAccounts 
				ADD `UniqueID` varchar(255) NOT NULL 
				AFTER `ID` 
			' );
		}
	}
	
	/* --- SBookTransaction --------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookTransaction' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookTransaction`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `UniqueID` varchar(255) NOT NULL,
			  `CID` bigint(20) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `From` varchar(255) NOT NULL,
			  `To` varchar(255) NOT NULL,
			  `Details` text NOT NULL,
			  `Message` text NOT NULL,
			  `Amount` bigint(20) NOT NULL,
			  `Verified` tinyint(4) NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  `ProcessCreated` datetime NOT NULL,
			  `IsProcessed` bigint(20) NOT NULL,
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$uniqueid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE SBookTransaction 
				ADD `UniqueID` varchar(255) NOT NULL 
				AFTER `ID` 
			' );
		}
	}
	
	/* --- SBookApiAccounts --------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookApiAccounts' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookApiAccounts`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `UniqueID` varchar(255) NOT NULL,
			  `UserID` bigint(20) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `App` varchar(255) NOT NULL,
			  `Url` varchar(255) NOT NULL,
			  `SessionID` varchar(255) NOT NULL,
			  `Username` varchar(255) NOT NULL,
			  `Password` varchar(255) NOT NULL,
			  `IsGlobal` tinyint(4) NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookAccountingSettings -------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookAccountingSettings' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookAccountingSettings`
			(
			  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
			  `VisualID` bigint(20) NOT NULL,
			  `CategoryID` bigint(20) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Amount` bigint(20) NOT NULL DEFAULT \'0\',
			  `DateModified` datetime NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookProducts --------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookProducts' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookProducts`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `UniqueID` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `Info` text NOT NULL,
			  `Details` text NOT NULL,
			  `Images` text NOT NULL,
			  `Price` text NOT NULL,
			  `UserID` bigint(20) NOT NULL,
			  `CategoryID` bigint(20) NOT NULL,
			  `InStock` bigint(20) NOT NULL DEFAULT \'0\',
			  `DateCreated` datetime NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\',
			  `Access` tinyint(4) NOT NULL DEFAULT \'0\',
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$instock = false;
		$uniqueid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'InStock' )
			{
				$instock = true;
			}
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
		}
		if ( !$instock )
		{
			$database->query ( '
				ALTER TABLE SBookProducts 
				ADD `InStock` bigint(20) NOT NULL default \'0\' 
				AFTER `CategoryID` 
			' );
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE SBookProducts 
				ADD `UniqueID` varchar(255) NOT NULL 
				AFTER `ID` 
			' );
		}
	}
	
	/* --- SBookCrowdfunding --------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookCrowdfunding' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookCrowdfunding`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `UniqueID` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `Location` text NOT NULL,
			  `Info` text NOT NULL,
			  `Details` text NOT NULL,
			  `Images` text NOT NULL,
			  `Goal` text NOT NULL,
			  `Donated` text NOT NULL,
			  `Backers` text NOT NULL,
			  `Currency` varchar(255) NOT NULL,
			  `DateEnd` timestamp NOT NULL,
			  `UserID` bigint(20) NOT NULL,
			  `CategoryID` bigint(20) NOT NULL,
			  `Tags` text NOT NULL,
			  `Status` bigint(20) NOT NULL DEFAULT \'0\',
			  `DateCreated` datetime NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\',
			  `Access` tinyint(4) NOT NULL DEFAULT \'0\',
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$status = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'Status' )
			{
				$status = true;
			}
		}
		if ( !$status )
		{
			$database->query ( '

				ALTER TABLE SBookCrowdfunding 
				ADD `Status` bigint(20) NOT NULL default \'0\' 
				AFTER `Tags` 
			' );
		}
	}
	
	/* --- SBookDonations --------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookDonations' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookDonations`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `UniqueID` varchar(255) NOT NULL,
			  `Component` varchar(255) NOT NULL,
			  `ComponentID` varchar(255) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `UserID` bigint(20) NOT NULL,
			  `CategoryID` bigint(20) NOT NULL,
			  `PaymentType` varchar(255) NOT NULL,
			  `PaymentID` varchar(255) NOT NULL,
			  `Donation` varchar(255) NOT NULL,
			  `Currency` varchar(255) NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  `SortOrder` bigint(20) NOT NULL,
			  `IsPaid` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\',
			  `NodeID` bigint(20) NOT NULL,
			  `NodeMainID` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookStorage --------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookStorage' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookStorage` 
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `UniqueID` varchar(255) NOT NULL,
			  `UnlockID` bigint(20) NOT NULL,
			  `UserID` bigint(20) NOT NULL,
			  `ContactID` bigint(20) NOT NULL,
			  `CategoryID` bigint(20) NOT NULL,
			  `Relation` varchar(255) NOT NULL,
			  `IDs` text NOT NULL,
			  `EncryptionKey` text NOT NULL,
			  `PublicKey` text NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`ID`)
			)
		' );	
	}
	else
	{
		$unlockid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'UnlockID' )
			{
				$unlockid = true;
			}
		}
		if ( !$unlockid )
		{
			$database->query ( '
				ALTER TABLE SBookStorage 
				ADD `UnlockID` bigint(20) NOT NULL 
				AFTER `UniqueID` 
			' );
		}
	}
	
	/* --- SBookTemplates -------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookTemplates' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookTemplates`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `UserID` bigint(20) NOT NULL,
			  `CategoryID` bigint(20) NOT NULL,
			  `Relation` varchar(255) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookTemplateFields --------------------------------------------------------------------- */
	
	/*$t = new cDatabaseTable ( 'SBookTemplateFields' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookTemplateFields`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `TemplateID` bigint(20) NOT NULL,
			  `Relation` varchar(255) NOT NULL,
			  `Function` varchar(255) NOT NULL,
			  `Target` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `Field` varchar(255) NOT NULL,
			  `Column` varchar(255) NOT NULL,
			  `Name` varchar(255) NOT NULL,
			  `Data` text NOT NULL,
			  `Placeholder` varchar(255) NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  `Access` tinyint(4) NOT NULL DEFAULT \'0\', 
			  `SortOrder` bigint(20) NOT NULL,
			  `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$access = false;
		$placeholder = false;
		$value = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'Access' )
			{
				$access = true;
			}
			if ( $name == 'Placeholder' )
			{
				$access = true;
			}
			if ( $name == 'Value' )
			{
				$value = true;
			}
		}
		if ( !$access )
		{
			$database->query ( '
				ALTER TABLE SBookTemplateFields 
				ADD `Access` tinyint(4) NOT NULL DEFAULT \'0\' 
				AFTER `DateCreated` 
			' );
		}
		if ( !$placeholder )
		{
			$database->query ( '
				ALTER TABLE SBookTemplateFields 
				ADD `Placeholder` varchar(255) NOT NULL 
				AFTER `Data` 
			' );
		}
		if ( !$value )
		{
			$database->query ( '
				ALTER TABLE SBookTemplateFields 
				ADD `Value` varchar(255) NOT NULL 
				AFTER `Data` 
			' );
		}
	}*/
	
	$t = new cDatabaseTable ( 'SBookTemplateFields' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookTemplateFields`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `SortOrder` bigint(20) NOT NULL,
			  `TemplateID` bigint(20) NOT NULL,
			  `Resource` varchar(255) NOT NULL,
			  `Target` varchar(255) NOT NULL,
			  `Function` varchar(255) NOT NULL,
			  `Heading` varchar(255) NOT NULL,
			  `Element` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `rName` varchar(255) NOT NULL,
			  `tName` varchar(255) NOT NULL,
			  `Value` varchar(255) NOT NULL,
			  `Title` varchar(255) NOT NULL,
			  `Placeholder` varchar(255) NOT NULL,
			  `Data` text NOT NULL,
			  `JS` varchar(255) NOT NULL,
			  `Mode` varchar(255) NOT NULL,
			  `Access` tinyint(4) NOT NULL DEFAULT \'0\', 
			  `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\',
			  `DateModified` datetime NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookOrders ----------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookOrders' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookOrders`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `TemplateID` bigint(20) NOT NULL,
			  `OrderID` varchar(255) NOT NULL,
			  `JobID` varchar(255) NOT NULL,
			  `CustomerID` varchar(255) NOT NULL,
			  `UserID` bigint(20) NOT NULL,
			  `CategoryID` bigint(20) NOT NULL,
			  `Participants` text NOT NULL,
			  `Status` varchar(255) NOT NULL,
			  `Progress` varchar(255) NOT NULL,
			  `Price` varchar(255) NOT NULL,
			  `Deadline` datetime NOT NULL,
			  `DateModified` datetime NOT NULL,
			  `DateCreated` datetime NOT NULL,
			  `SortOrder` bigint(20) NOT NULL,
			  `IsFinished` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsRead` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsArchived` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsControlled` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\',
			  `F1` text NOT NULL,
			  `F2` text NOT NULL,
			  `F3` text NOT NULL,
			  `F4` text NOT NULL,
			  `F5` text NOT NULL,
			  `F6` text NOT NULL,
			  `F7` text NOT NULL,
			  `F8` text NOT NULL,
			  `F9` text NOT NULL,
			  `F10` text NOT NULL,
			  `F11` text NOT NULL,
			  `F12` text NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$participants = false;
		$isfinished = false;
		$isread = false;
		$isarchived = false;
		$iscontrolled = false;
		$orderid = false;
		$jobid = false;
		$data = false;
		$parentid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'Participants' )
			{
				$participants = true;
			}
			if ( $name == 'IsFinished' )
			{
				$isfinished = true;
			}
			if ( $name == 'IsRead' )
			{
				$isread = true;
			}
			if ( $name == 'IsArchived' )
			{
				$isarchived = true;
			}
			if ( $name == 'IsControlled' )
			{
				$iscontrolled = true;
			}
			if ( $name == 'OrderID' )
			{
				$orderid = true;
			}
			if ( $name == 'JobID' )
			{
				$jobid = true;
			}
			if ( $name == 'Data' )
			{
				$data = true;
			}
			if ( $name == 'ParentID' )
			{
				$parentid = true;
			}
		}
		if ( !$participants )
		{
			$database->query ( '
				ALTER TABLE SBookOrders 
				ADD `Participants` text NOT NULL 
				AFTER `CategoryID` 
			' );
		}
		if ( !$isfinished )
		{
			$database->query ( '
				ALTER TABLE SBookOrders 
				ADD `IsFinished` tinyint(4) NOT NULL DEFAULT \'0\' 
				AFTER `SortOrder` 
			' );
		}
		if ( !$isread )
		{
			$database->query ( '
				ALTER TABLE SBookOrders 
				ADD `IsRead` tinyint(4) NOT NULL DEFAULT \'0\' 
				AFTER `IsFinished` 
			' );
		}
		if ( !$isarchived )
		{
			$database->query ( '
				ALTER TABLE SBookOrders 
				ADD `IsArchived` tinyint(4) NOT NULL DEFAULT \'0\' 
				AFTER `IsRead` 
			' );
		}
		if ( !$iscontrolled )
		{
			$database->query ( '
				ALTER TABLE SBookOrders 
				ADD `IsControlled` tinyint(4) NOT NULL DEFAULT \'0\' 
				AFTER `IsArchived` 
			' );
		}
		if ( !$orderid )
		{
			$database->query ( '
				ALTER TABLE SBookOrders 
				ADD `OrderID` varchar(255) NOT NULL 
				AFTER `TemplateID` 
			' );
		}
		if ( !$jobid )
		{
			$database->query ( '
				ALTER TABLE SBookOrders 
				ADD `JobID` varchar(255) NOT NULL 
				AFTER `OrderID` 
			' );
		}
		if ( !$data )
		{
			$database->query ( '
				ALTER TABLE SBookOrders 
				ADD `Data` text NOT NULL 
				AFTER `Participants` 
			' );
		}
		if ( !$parentid )
		{
			$database->query ( '
				ALTER TABLE SBookOrders 
				ADD `ParentID` varchar(255) NOT NULL 
				AFTER `OrderID` 
			' );
		}
	}
	
	/* --- SBookTemplateValues --------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookOrderItems' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookOrderItems`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `OrderID` bigint(20) NOT NULL,
			  `SortOrder` bigint(20) NOT NULL,
			  `IsDeleted` tinyint(4) NOT NULL DEFAULT \'0\',
			  `F1` text NOT NULL,
			  `F2` text NOT NULL,
			  `F3` text NOT NULL,
			  `F4` text NOT NULL,
			  `F5` text NOT NULL,
			  `F6` text NOT NULL,
			  `F7` text NOT NULL,
			  `F8` text NOT NULL,
			  `F9` text NOT NULL,
			  `F10` text NOT NULL,
			  `F11` text NOT NULL,
			  `F12` text NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	
	/* --- SBookCronJobs --------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'SBookCronJobs' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `SBookCronJobs`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `MinDelay` bigint(20) NOT NULL,
			  `Filename` varchar(255) NOT NULL,
			  `LastExec` datetime NOT NULL,
			  `Error` text NOT NULL,
			  `Type` varchar(255) NOT NULL,
			  `IsRunning` tinyint(4) NOT NULL DEFAULT \'0\',
			  `IsActive` tinyint(4) NOT NULL DEFAULT \'1\',
			  `IsMaintenance` bigint(20) NOT NULL DEFAULT \'0\',
			  `SortOrder` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$ismaintenance = false;
		$type = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'IsMaintenance' )
			{
				$ismaintenance = true;
			}
			if ( $name == 'Type' )
			{
				$type = true;
			}
		}
		if ( !$ismaintenance )
		{
			$database->query ( '
				ALTER TABLE SBookCronJobs 
				ADD `IsMaintenance` bigint(20) NOT NULL default \'0\' 
				AFTER `IsActive` 
			' );
		}
		if ( !$type )
		{
			$database->query ( '
				ALTER TABLE SBookCronJobs 
				ADD `Type` varchar(255) NOT NULL 
				AFTER `Error` 
			' );
		}
	}
	
	/* --- Log ------------------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'Log' );
	if ( $t->load () )
	{
		$userid = false;
		$connectedid = false;
		$connectedtype = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'UserID' )
			{
				$userid = true;
			}
			if ( $name == 'ConnectedID' )
			{
				$connectedid = true;
			}
			if ( $name == 'ConnectedType' )
			{
				$connectedtype = true;
			}
		}
		if ( !$userid )
		{
			$database->query ( '
				ALTER TABLE Log 
				ADD `UserID` bigint(20) NOT NULL default \'0\' 
				AFTER `ID` 
			' );
		}
		if ( !$connectedid )
		{
			$database->query ( '
				ALTER TABLE Log 
				ADD `ConnectedID` bigint(20) NOT NULL default \'0\' 
				AFTER `ObjectID` 
			' );
		}
		if ( !$connectedtype )
		{
			$database->query ( '
				ALTER TABLE Log 
				ADD `ConnectedType` varchar(255) NOT NULL 
				AFTER `ObjectID` 
			' );
		}
	}
	
	/* --- UserActivity ----------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'UserActivity' );
	if ( !$t->load () )
	{
		$database->query ( '
			CREATE TABLE IF NOT EXISTS `UserActivity`
			(
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `Component` varchar(255) NOT NULL,
			  `Type` varchar(255) NOT NULL, 
			  `TypeID` bigint(20) NULL,
			  `UserID` bigint(20) NULL,
			  `ContactID` bigint(20) NULL,
			  `Data` text NOT NULL,
			  `LastUpdate` bigint(20) NULL,
			  PRIMARY KEY (`ID`)
			)
		' );
	}
	else
	{
		$typeid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'TypeID' )
			{
				$typeid = true;
			}
		}
		if ( !$typeid )
		{
			$database->query ( '
				ALTER TABLE UserActivity 
				ADD `TypeID` bigint(20) DEFAULT NULL 
				AFTER `Type` 
			' );
		}
	}
	
	/* --- UserLogin ------------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'UserLogin' );
	if ( $t->load () )
	{
		$nodeid = false;
		$ip = false;
		$data = false;
		$lastheartbeat = false;
		$datecreated = false;
		$dateexpired = false;
		$useragent = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'NodeID' )
			{
				$nodeid = true;
			}
			if ( $name == 'IP' )
			{
				$ip = true;
			}
			if ( $name == 'Data' )
			{
				$data = true;
			}
			if ( $name == 'LastHeartbeat' )
			{
				$lastheartbeat = true;
			}
			if ( $name == 'DateCreated' )
			{
				$datecreated = true;
			}
			if ( $name == 'DateExpired' )
			{
				$dateexpired = true;
			}
			if ( $name == 'UserAgent' )
			{
				$useragent = true;
			}
		}
		if ( !$nodeid )
		{
			$database->query ( '
				ALTER TABLE UserLogin 
				ADD `NodeID` tinyint(4) DEFAULT NULL 
				AFTER `UserID` 
			' );
		}
		if ( !$ip )
		{
			$database->query ( '
				ALTER TABLE UserLogin 
				ADD `IP` varchar(255) DEFAULT NULL 
				AFTER `ID` 
			' );
		}
		if ( !$data )
		{
			$database->query ( '
				ALTER TABLE UserLogin 
				ADD `Data` text 
				AFTER `DataSource` 
			' );
		}
		if ( !$lastheartbeat )
		{
			$database->query ( '
				ALTER TABLE UserLogin 
				ADD `LastHeartbeat` datetime NOT NULL 
				AFTER `DataSource` 
			' );
		}
		if ( !$datecreated )
		{
			$database->query ( '
				ALTER TABLE UserLogin 
				ADD `DateCreated` datetime NOT NULL 
				AFTER `DataSource` 
			' );
		}
		if ( !$dateexpired )
		{
			$database->query ( '
				ALTER TABLE UserLogin 
				ADD `DateExpired` datetime NOT NULL 
				AFTER `DataSource` 
			' );
		}
		if ( !$useragent )
		{
			$database->query ( '
				ALTER TABLE UserLogin 
				ADD `UserAgent` text 
				AFTER `Data` 
			' );
		}
	}
	
	
	
	/* --- Folder ----------------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'Folder' );
	if ( $t->load () )
	{
		$userid = false;
		$categoryid = false;
		$nodeid = false;
		$nodemainid = false;
		$access = false;
		$uniqueid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'UserID' )
			{
				$userid = true;
			}
			if ( $name == 'CategoryID' )
			{
				$categoryid = true;
			}
			if ( $name == 'NodeID' )
			{
				$nodeid = true;
			}
			if ( $name == 'NodeMainID' )
			{
				$nodemainid = true;
			}
			if ( $name == 'Access' )
			{
				$access = true;
			}
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
		}
		if ( !$userid )
		{
			$database->query ( '
				ALTER TABLE Folder
				ADD `UserID` bigint(20) NOT NULL default \'0\' 
				AFTER `DiskPath`
			' );
		}
		if ( !$categoryid )
		{
			$database->query ( '
				ALTER TABLE Folder
				ADD `CategoryID` bigint(20) NOT NULL default \'0\' 
				AFTER `DiskPath`
			' );
		}
		if ( !$nodeid )
		{
			$database->query ( '
				ALTER TABLE Folder
				ADD `NodeID` bigint(20) NOT NULL default \'0\' 
				AFTER `DiskPath`
			' );
		}
		if ( !$nodemainid )
		{
			$database->query ( '
				ALTER TABLE Folder
				ADD `NodeMainID` bigint(20) NOT NULL default \'0\' 
				AFTER `DiskPath`
			' );
		}
		if ( !$access )
		{
			$database->query ( '
				ALTER TABLE Folder
				ADD `Access` tinyint(4) NOT NULL default \'0\' 
				AFTER `DiskPath`
			' );
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE Folder
				ADD `UniqueID` varchar(255) NOT NULL 
				AFTER `ID`
			' );
		}
	}
	
	/* --- Image ----------------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'Image' );
	if ( $t->load () )
	{
		$nodeid = false;
		$nodemainid = false;
		$userid = false;
		$categoryid = false;
		$access = false;
		$verified = false;
		$modid = false;
		$isedit = false;
		$uniqueid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'NodeID' )
			{
				$nodeid = true;
			}
			if ( $name == 'NodeMainID' )
			{
				$nodemainid = true;
			}
			if ( $name == 'UserID' )
			{
				$userid = true;
			}
			if ( $name == 'CategoryID' )
			{
				$categoryid = true;
			}
			if ( $name == 'Access' )
			{
				$access = true;
			}
			if ( $name == 'Verified' )
			{
				$verified = true;
			}
			if ( $name == 'ModID' )
			{
				$modid = true;
			}
			if ( $name == 'IsEdit' )
			{
				$isedit = true;
			}
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
		}
		if ( !$nodeid )
		{
			$database->query ( '
				ALTER TABLE Image
				ADD `NodeID` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$nodemainid )
		{
			$database->query ( '
				ALTER TABLE Image
				ADD `NodeMainID` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$userid )
		{
			$database->query ( '
				ALTER TABLE Image
				ADD `UserID` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$categoryid )
		{
			$database->query ( '
				ALTER TABLE Image
				ADD `CategoryID` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$access )
		{
			$database->query ( '
				ALTER TABLE Image
				ADD `Access` tinyint(4) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$verified )
		{
			$database->query ( '
				ALTER TABLE Image
				ADD `Verified` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$modid )
		{
			$database->query ( '
				ALTER TABLE Image
				ADD `ModID` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$isedit )
		{
			$database->query ( '
				ALTER TABLE Image
				ADD `IsEdit` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE Image
				ADD `UniqueID` varchar(255) NOT NULL 
				AFTER `ID`
			' );
		}
	}
	
	/* --- File ----------------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'File' );
	if ( $t->load () )
	{
		$nodeid = false;
		$nodemainid = false;
		$userid = false;
		$categoryid = false;
		$access = false;
		$verified = false;
		$modid = false;
		$isedit = false;
		$uniqueid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'NodeID' )
			{
				$nodeid = true;
			}
			if ( $name == 'NodeMainID' )
			{
				$nodemainid = true;
			}
			if ( $name == 'UserID' )
			{
				$userid = true;
			}
			if ( $name == 'CategoryID' )
			{
				$categoryid = true;
			}
			if ( $name == 'Access' )
			{
				$access = true;
			}
			if ( $name == 'Verified' )
			{
				$verified = true;
			}
			if ( $name == 'ModID' )
			{
				$modid = true;
			}
			if ( $name == 'IsEdit' )
			{
				$isedit = true;
			}
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
		}
		if ( !$nodeid )
		{
			$database->query ( '
				ALTER TABLE File
				ADD `NodeID` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$nodemainid )
		{
			$database->query ( '
				ALTER TABLE File
				ADD `NodeMainID` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$userid )
		{
			$database->query ( '
				ALTER TABLE File
				ADD `UserID` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$categoryid )
		{
			$database->query ( '
				ALTER TABLE File
				ADD `CategoryID` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$access )
		{
			$database->query ( '
				ALTER TABLE File
				ADD `Access` tinyint(4) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$verified )
		{
			$database->query ( '
				ALTER TABLE File
				ADD `Verified` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$modid )
		{
			$database->query ( '
				ALTER TABLE File
				ADD `ModID` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$isedit )
		{
			$database->query ( '
				ALTER TABLE File
				ADD `IsEdit` bigint(20) NOT NULL default \'0\' 
				AFTER `FilenameOriginal`
			' );
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE File
				ADD `UniqueID` varchar(255) NOT NULL 
				AFTER `ID`
			' );
		}
	}
	
	/* --- Users ----------------------------------------------------------------------------------- */
	
	$t = new cDatabaseTable ( 'Users' );
	if ( $t->load () )
	{
		$authkey = false;
		$expires = false;
		$inactive = false;
		$isdeleted = false;
		$islimited = false;
		$usertype = false;
		$publickey = false;
		$uniqueid = false;
		$storekey = false;
		$nodeid = false;
		$nodeuserid = false;
		foreach ( $t->getFieldNames () as $name )
		{
			if ( $name == 'AuthKey' )
			{
				$authkey = true;
			}
			if ( $name == 'Expires' )
			{
				$expires = true;
			}
			if ( $name == 'InActive' )
			{
				$inactive = true;
			}
			if ( $name == 'IsDeleted' )
			{
				$isdeleted = true;
			}
			if ( $name == 'IsLimited' )
			{
				$islimited = true;
			}
			if ( $name == 'UserType' )
			{
				$usertype = true;
			}
			if ( $name == 'PublicKey' )
			{
				$publickey = true;
			}
			if ( $name == 'UniqueID' )
			{
				$uniqueid = true;
			}
			if ( $name == 'StoreKey' )
			{
				$storekey = true;
			}
			if ( $name == 'NodeID' )
			{
				$nodeid = true;
			}
			if ( $name == 'NodeUserID' )
			{
				$nodeuserid = true;
			}
		}
		if ( !$authkey )
		{
			$database->query ( '
				ALTER TABLE Users
				ADD `AuthKey` varchar(255) NOT NULL 
				AFTER `Password`
			' );
		}
		if ( !$expires )
		{
			$database->query ( '
				ALTER TABLE Users
				ADD `Expires` datetime NOT NULL 
				AFTER `DateModified`
			' );
		}
		if ( !$inactive )
		{
			$database->query ( '
				ALTER TABLE Users
				ADD `InActive` bigint(20) NOT NULL default \'0\' 
				AFTER `IsDisabled`
			' );
		}
		if ( !$isdeleted )
		{
			$database->query ( '
				ALTER TABLE Users
				ADD `IsDeleted` bigint(20) NOT NULL default \'0\' 
				AFTER `IsDisabled`
			' );
		}
		if ( !$islimited )
		{
			$database->query ( '
				ALTER TABLE Users
				ADD `IsLimited` bigint(20) NOT NULL default \'0\' 
				AFTER `IsDisabled`
			' );
		}
		if ( !$usertype )
		{
			$database->query ( '
				ALTER TABLE Users
				ADD `UserType` bigint(20) NOT NULL default \'0\' 
				AFTER `IsDisabled`
			' );
		}
		if ( !$publickey )
		{
			$database->query ( '
				ALTER TABLE Users
				ADD `PublicKey` text NOT NULL 
				AFTER `Password`
			' );
		}
		if ( !$uniqueid )
		{
			$database->query ( '
				ALTER TABLE Users 
				ADD `UniqueID` varchar(255) NOT NULL 
				AFTER `ID` 
			' );
		}
		if ( !$storekey )
		{
			$database->query ( '
				ALTER TABLE Users 
				ADD `StoreKey` bigint(20) NOT NULL default \'0\' 
				AFTER `UserType` 
			' );
		}
		if ( !$nodeuserid )
		{
			$database->query ( '
				ALTER TABLE Users 
				ADD `NodeUserID` bigint(20) NOT NULL default \'0\' 
				AFTER `IsTemplate` 
			' );
		}
		if ( !$nodeid )
		{
			$database->query ( '
				ALTER TABLE Users 
				ADD `NodeID` bigint(20) NOT NULL default \'0\' 
				AFTER `IsTemplate` 
			' );
		}
	}
	
	/* --- Import default fields if empty db ----------------------------------------------------- */
	
	if ( $database && !$database->fetchObjectRows( 'SELECT ID FROM SComponents ORDER BY ID ASC' ) )
	{
		if ( file_exists( BASE_DIR . '/subether/defaultdb.sql' ) )
		{
			// Import structure
			$sql = file_get_contents ( BASE_DIR . '/subether/defaultdb.sql' );
			$sql = explode ( ');', $sql );
			foreach ( $sql as $s )
			{
				if ( $s{0} == '-' ) continue;
				if ( !trim ( $s ) ) continue;
				$database->query ( trim ( $s.')' ) );
			}
			
			// Create user
			if ( $usr = $database->fetchObjectRow( 'SELECT * FROM Users ORDER BY ID ASC' ) )
			{				
				$user = new dbObject ( 'SBookContact' );
				$user->UserID = $usr->ID;
				if( !$user->Load() )
				{
					$user->Username = $usr->Name;
					$user->Email = $usr->Email;
					$user->DateCreated = date( 'Y-m-d H:i:s' );
					$user->DateModified = date( 'Y-m-d H:i:s' );
					$user->Save();
				}
				
			}
		}
	}
	
	$_SESSION['dbchecked'] = ( $_SESSION['dbchecked'] ? 2 : 1 );
}

?>
