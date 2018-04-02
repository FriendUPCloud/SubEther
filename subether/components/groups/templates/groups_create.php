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
<div id="creategroup">
	<div class="head">
		<h1><?= i18n( 'i18n_Create New Group' ) ?></h1>
	</div>
	<div class="content">
		<table>
			<tr>
				<td><strong><?= i18n( 'i18n_Group Name' ) ?>:</strong></td>
				<td>
					<div class="inputfield">
						<input type="text" name="name"/>
					</div>
				</td>
			</tr>
			<tr>
				<td><strong><?= i18n( 'i18n_Members' ) ?>:</strong></td>
				<td>
					<div class="inputfield">
						<span id="FindMembers"></span>
						<input id="MemberSearch" type="text" name="search" placeholder="<?= i18n( 'i18n_Who do you want to add to the group' ) ?>?" onkeyup="findMembers( false, this )"/>
					</div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div id="ListFoundMembers"></div>
				</td>
			</tr>
			<?
				global $database, $webuser;
				
				$group = $database->fetchObjectRow( '
					SELECT
						*
					FROM
						SBookCategory
					WHERE
							Type = "Group"
						AND Name = "Groups"
				' );
				
				if ( IsSystemAdmin() )
				{
					$q = '
						SELECT * FROM 
						( 
							( 
								SELECT 
									c.*,
									"" AS UserID,
									"" AS RelationID 
								FROM 
									SBookCategory c 
								WHERE 
										c.CategoryID = \\'' . $group->ID . '\\' 
									AND c.Type = "SubGroup" 
									AND c.IsSystem = "0"
									AND c.NodeID = "0"
									AND c.NodeMainID = "0" 
							) 
							UNION 
							( 
								SELECT 
									c.*, 
									r.ObjectID as UserID, 
									r.ID as RelationID 
								FROM 
									SBookCategory c, 
									SBookCategoryRelation r 
								WHERE 
										r.ObjectType = "Users" 
									AND r.ObjectID = \\'' . $webuser->ID . '\\' 
									AND c.CategoryID = \\'' . $group->ID . '\\' 
									AND c.Type = "SubGroup"
									AND c.IsSystem = "0" 
									AND c.ID = r.CategoryID  
							) 
						) z
						GROUP BY
							z.ID 
						ORDER BY 
							z.ID ASC 
					';
				}
				else
				{
					$q = '
						SELECT 
							c.*, 
							r.ObjectID as UserID, 
							r.ID as RelationID 
						FROM 
							SBookCategory c, 
							SBookCategoryRelation r 
						WHERE 
								r.ObjectType = "Users" 
							AND r.ObjectID = \\'' . $webuser->ID . '\\' 
							AND c.CategoryID = \\'' . $group->ID . '\\' 
							AND c.Type = "SubGroup"
							AND c.IsSystem = "0" 
							AND c.ID = r.CategoryID 
						ORDER BY 
							r.SortOrder ASC, 
							c.ID ASC 
					';
				}
				
				if ( $group->ID > 0 && ( $rows = $database->fetchObjectRows( $q ) ) )
				{
					$cataccess = CategoryAccess( $webuser->ContactID, false, -1, 'IsAdmin' );
					
					$ostr  = '<option value="0">' . i18n( 'i18n_No main group' ) . '</option>';
					$ostr .= '<option value="0">- - -</option>';
					
					if ( $cataccess )
					{
						foreach ( $rows as $r )
						{
							if ( !isset( $cataccess[$r->ID] ) || $r->ParentID > 0 ) continue;
							
							$s = ( $this->ParentID > 0 && $this->ParentID == $r->ID ? ' selected="selected"' : '' );
							
							$ostr .= '<option value="' . $r->ID . '"' . $s . '>' . $r->Name . '</option>';
						}
					}
					
					$str  = '<tr>';
					$str .= '<td><strong>' . i18n( 'i18n_Main Group' ) . ':</strong></td>';
					$str .= '<td><div class="selectfield"><select name="parent">' . $ostr . '</select></div></td>';
					$str .= '</tr>';
					
					return $str;
				}
				
				return false;
			?>
			<tr class="middle">
				<td><strong><?= i18n( 'i18n_Privacy' ) ?>:</strong></td>
				<td>
					<ul>
						<li>
							<input type="radio" name="privacy" value="OpenGroup"/>
							<strong><?= i18n( 'i18n_Open' ) ?></strong>
						</li>
						<li>
							<input type="radio" name="privacy" value="ClosedGroup" checked="checked"/>
							<strong><?= i18n( 'i18n_Closed' ) ?></strong>
						</li>
						<li>
							<input type="radio" name="privacy" value="SecretGroup"/>
							<strong><?= i18n( 'i18n_Secret' ) ?></strong>
						</li>
					</ul>
				</td>
			</tr>
		</table>
	</div>
	<div class="bottom">
		<div class="buttons">
			<button class="submit" onclick="createGroup( <?= $this->ParentID ?> )"><span><?= i18n( 'i18n_Create' ) ?></span></button>
			<button class="cancel" onclick="closeWindow()"><span><?= i18n( 'i18n_Cancel' ) ?></span></button>
		</div>
	</div>
</div>
