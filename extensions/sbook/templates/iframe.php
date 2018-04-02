<? /*******************************************************************************
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
*******************************************************************************/ ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?= $this->title ?></title>
		<base href="<?= BASE_URL ?>"/>
		<link rel="stylesheet" href="/extensions/sbook/css/layer.css"/>
		<script src="/extensions/sbook/javascript/layer.js"></script>
		<script src="/extensions/sbook/javascript/managedwindow.js"></script>
	</head>
	<body class="Layer">
		<div id="WindowOverlay"></div>
		<div id="Windows"></div>
		<div id="Resources"></div>
		<iframe src="no/home" onload="cloneResources(this)"><iframe>
	</body>
</html>
