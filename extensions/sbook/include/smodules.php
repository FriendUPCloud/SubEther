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

// Define all database structures
$modules = array(
	// Account settings module
	'account' => array(
		'global' => array( 
			(object) array(
				'DisplayName' => 'Account Settings',
				'Position'    => 'LeftCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Account Settings',
				'Position'    => 'Middlecol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '1',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Account Settings',
				'Position'    => 'RightCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Account Settings',
				'Position'    => 'Chat',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Account Settings',
				'Position'    => 'Top',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
		),
		'mobile' => array(
			(object) array(
				'DisplayName' => 'Account Settings',
				'Position'    => 'LeftCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Account Settings',
				'Position'    => 'Middlecol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '1',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Account Settings',
				'Position'    => 'RightCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Account Settings',
				'Position'    => 'Chat',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Account Settings',
				'Position'    => 'Top',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
		)
	),
	// Login and authentication module
	'authentication' => array(
		'global' => array(
			(object) array(
				'DisplayName' => 'Authentication',
				'Position'    => 'Top',
				'UserLevels'  => ',99,1,',
				'Visible'     => '1', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			)
		),
		'mobile' => array(
			(object) array(
				'DisplayName' => 'Authentication',
				'Position'    => 'Top',
				'UserLevels'  => ',99,1,',
				'Visible'     => '1', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			)
		),
	),
	// Search engine module
	'engine' => array(
		'global' => array(
			(object) array(
				'DisplayName' => 'Search Engine',
				'Position'    => 'GlobalSearch',
				'UserLevels'  => ',0,',
				'Visible'     => '1', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Search Engine',
				'Position'    => 'SearchEngine',
				'UserLevels'  => ',0,',
				'Visible'     => '1', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			)
		),
		'mobile' => array(
			(object) array(
				'DisplayName' => 'Search Engine',
				'Position'    => 'GlobalSearch',
				'UserLevels'  => ',0,',
				'Visible'     => '1', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Search Engine',
				'Position'    => 'SearchEngine',
				'UserLevels'  => ',0,',
				'Visible'     => '1', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			)
		),
	),
	// Super admin settings
	'global' => array(
		'global' => array(
			(object) array(
				'DisplayName' => 'Global Settings',
				'Position'    => 'LeftCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Global Settings',
				'Position'    => 'MiddleCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '1',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Global Settings',
				'Position'    => 'RightCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Global Settings',
				'Position'    => 'Chat',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Global Settings',
				'Position'    => 'Top',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			)
		),
		'mobile' => array(
			(object) array(
				'DisplayName' => 'Global Settings',
				'Position'    => 'LeftCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Global Settings',
				'Position'    => 'MiddleCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '1',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Global Settings',
				'Position'    => 'RightCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Global Settings',
				'Position'    => 'Chat',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Global Settings',
				'Position'    => 'Top',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			)
		)
	),
	// Main module
	'main' => array(
		'global' => array(
			(object) array(
				'DisplayName' => 'Home',
				'Position'    => 'LeftCol',
				'UserLevels'  => ',0,',
				'Visible'     => '2', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Home',
				'Position'    => 'MiddleCol',
				'UserLevels'  => ',0,',
				'Visible'     => '2', 
				'IsMain'      => '1',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Home',
				'Position'    => 'RightCol',
				'UserLevels'  => ',0,',
				'Visible'     => '2', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Home',
				'Position'    => 'Chat',
				'UserLevels'  => ',0,',
				'Visible'     => '2', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Home',
				'Position'    => 'Top',
				'UserLevels'  => ',0,',
				'Visible'     => '2', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			)
		),
		'mobile' => array(
			(object) array(
				'DisplayName' => 'Home',
				'Position'    => 'LeftCol',
				'UserLevels'  => ',0,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Home',
				'Position'    => 'MiddleCol',
				'UserLevels'  => ',0,',
				'Visible'     => '3', 
				'IsMain'      => '1',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Home',
				'Position'    => 'RightCol',
				'UserLevels'  => ',0,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Home',
				'Position'    => 'Chat',
				'UserLevels'  => ',0,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Home',
				'Position'    => 'Top',
				'UserLevels'  => ',0,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			)
		)
	),
	'nodes' => array(
		'global' => array(
			(object) array(
				'DisplayName' => 'Nodes',
				'Position'    => '',
				'UserLevels'  => ',99,',
				'Visible'     => '1', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			)
		),
		'mobile' => array(
			(object) array(
				'DisplayName' => 'Nodes',
				'Position'    => '',
				'UserLevels'  => ',99,',
				'Visible'     => '1', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			)
		)
	),
	// Registered users home profile
	'profile' => array(
		'global' => array(
			(object) array(
				'DisplayName' => 'Profile',
				'Position'    => 'Scene',
				'UserLevels'  => ',99,1,',
				'Visible'     => '2', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Profile',
				'Position'    => 'LeftCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '2', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Profile',
				'Position'    => 'MiddleCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '2', 
				'IsMain'      => '1',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Profile',
				'Position'    => 'RightCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '2', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Profile',
				'Position'    => 'Chat',
				'UserLevels'  => ',99,1,',
				'Visible'     => '2', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Profile',
				'Position'    => 'Top',
				'UserLevels'  => ',99,1,',
				'Visible'     => '2', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			)
		),
		'mobile' => array(
			(object) array(
				'DisplayName' => 'Profile',
				'Position'    => 'Scene',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Profile',
				'Position'    => 'LeftCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Profile',
				'Position'    => 'MiddleCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '1',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Profile',
				'Position'    => 'RightCol',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Profile',
				'Position'    => 'Chat',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			),
			(object) array(
				'DisplayName' => 'Profile',
				'Position'    => 'Top',
				'UserLevels'  => ',99,1,',
				'Visible'     => '3', 
				'IsMain'      => '0',
				'SortOrder'   => '0'
			)
		)
	),
	// Register new user module
	'register' => array(
		'global' => array(
			(object) array(
				'DisplayName' => 'Register',
				'Position'    => 'MiddleCol',
				'UserLevels'  => ',0,',
				'Visible'     => '1', 
				'IsMain'      => '1',
				'SortOrder'   => '0'
			)
		),
		'mobile' => array(
			(object) array(
				'DisplayName' => 'Register',
				'Position'    => 'MiddleCol',
				'UserLevels'  => ',0,',
				'Visible'     => '1', 
				'IsMain'      => '1',
				'SortOrder'   => '0'
			)
		)
	)
);


?>
