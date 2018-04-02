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
<form name="LoginForm">
	<table>
		<tr>
			<td colspan="2">
				<input name="Username" placeholder="Email"/>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input name="Password" placeholder="Password"/>
			</td>
		</tr>
		<tr>
			<td class="Col1">
				<div><input type="checkbox" name="Remember"/> <span>Keep me logged in</span></div>
				<div><a href="#">Forgot your password?</a></div>		
			</td>
			<td class="Col2">
				<button type="button" onclick="checkLoginForm()">Login</button>
			</td>
		</tr>
	</table>
</form>
<script>
	checkLoginForm()
	{
		var f = form.LoginForm;
		
		if( !f ) return;
		
		
	}
</script>
