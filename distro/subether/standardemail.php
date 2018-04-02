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
		<title>
			<?= $this->subject ?>
		</title>
	</head>
	<body>
		<style>
			a { color: #000000; font-weight: normal; text-decoration: underline; }
		</style>
		<br/><br/>
		<div style="width: 720px; margin: auto;">
			<div style="color: black; font-family: verdana, sans serif; font-size: 15px; border-radius: 6px; -moz-border-radius: 6px; border: 1px solid #C0C0C0;">
				<div style="background: #FFFFFF; padding: 15px; border-radius: 6px 6px 0 0; -moz-border-radius: 6px 6px 0 0;">
					<h4 style="font-size: 18px; color: #000000; margin: 0 0 15px 0"><?= $this->subject ?></h4>
					<?= $this->body ?>
				</div>
				<div style="background: #DCDDDD; padding: 15px; color: black; font-family: verdana, sans serif; font-size: 11px; border-radius: 0 0 6px 6px; -moz-border-radius: 0 0 6px 6px;">
					<div style="float: right; margin: 0 0 0 5px; position: relative; top: 15px;"><img style="height: 45px;" src="<?= 'upload/images-master/logo_symbol_black.png' ?>"></div><br/>
					<a href="<?= BASE_URL ?>about/">About</a> | <a href="<?= BASE_URL ?>terms/">Terms</a> | <a href="<?= BASE_URL ?>copyright/">Copyright</a> | <a href="<?= BASE_URL ?>advertising/">Advertising</a> | <a href="<?= BASE_URL ?>privacy/">Privacy</a> | <a href="<?= BASE_URL ?>policy_feedback/">Policy &amp; Feedback</a> | <a href="<?= BASE_URL ?>creators_partners/">Creators &amp; Partners</a> | <a href="<?= BASE_URL ?>developers/">Developers</a><br/>
					<br/>
					<?= ( '<a href="' . BASE_URL . '">SubEther</a> v' . ( defined( 'NODE_VERSION' ) ? NODE_VERSION : '1.0.0' ) . ' © 2018' ) ?> <?=  ?><br/>
					<br/>
				</div>
			</div>
		</div>
		<br/>
		<br/>
	</body>
</html>
