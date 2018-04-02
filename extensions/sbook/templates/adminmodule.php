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

	<div class="Container" style="box-sizing: border-box; display: block; width: 100%; overflow-x: auto">
		<table width="250px" class="Gui">
			<tr>
				<td>
					<strong>Module:</strong>
				</td>
				<td>
					<strong>Component:</strong>
				</td>
				<td style="min-width: 200px">
					<strong>Settings:</strong>
				</td>
			</tr>
			<tr>
				<td>
					<select size="10" id="mode<?= $this->field->ID ?>" onchange="loadComponents<?= $this->field->ID?>()">
						<?= $this->options ?>
					</select>	
				</td>
				<td id="component<?= $this->field->ID ?>">
				</td>
				<td id="componentsettings<?= $this->field->ID ?>" style="vertical-align: top">
				</td>
			</tr>
			<!--<tr>
				<td width="90px">
					Site:
				</td>
				<td>
					<input type="checkbox" id="site<?= $this->field->ID ?>" <?= ( $this->site > 0 ? 'checked="checked"' : '' ) ?> style="width:auto;"/>	
				</td>
			</tr>-->
		</table>
	</div>
	<script type="text/javascript">
		// On save
		AddSaveFunction( function ()
		{
			var j = new bajax();
			j.openUrl( 'admin.php?module=extensions&extension=sbook&action=savefield', 'post', true );
			j.addVar( 'type', ge ( 'mode<?= $this->field->ID ?>' ).value );
			j.addVar( 'fid', '<?= $this->field->ID ?>' );
			j.addVar( 'components', getActivatedComponents<?= $this->field->ID?>().join( ',' ) );
			j.send();
		} );
		// Get an array of activated components
		function getActivatedComponents<?= $this->field->ID?>()
		{
			var out = [];
			if( ge( 'components_<?= $this->field->ID?>' ) )
			{
				var o = ge( 'components_<?= $this->field->ID?>' ).getElementsByTagName( 'option' );
				for( var a = 0; a < o.length; a++ )
				{
					if( o[a].getAttribute( 'active' ) )
					{
						out.push( o[a].value );
					}
				}
			}
			return out;
		}
		// Load available components
		function loadComponents<?= $this->field->ID?>()
		{
			var j = new bajax();
			j.openUrl( 'admin.php?module=extensions&extension=sbook&action=getcomponents', 'post', true );
			j.addVar( 'mode', ge( 'mode<?= $this->field->ID ?>' ).value );
			j.addVar( 'fid', '<?= $this->field->ID ?>' );
			j.onload = function()
			{
				var r = this.getResponseText().split( '<!--separate-->' );
				if( r[0] == 'ok' )
				{
					ge( 'component<?= $this->field->ID ?>' ).innerHTML = r[1];
				}
				loadComponentSettings<?= $this->field->ID?>();
			}
			j.send();
		}
		// Load per component settings
		function loadComponentSettings<?= $this->field->ID?>()
		{
			var j = new bajax();
			j.openUrl( 'admin.php?module=extensions&extension=sbook&action=componentsettings', 'post', true );
			j.addVar( 'mode', ge( 'mode<?= $this->field->ID ?>' ).value );
			j.addVar( 'component', ge( 'components_<?= $this->field->ID ?>' ).value );
			j.addVar( 'fid', '<?= $this->field->ID ?>' );
			j.onload = function()
			{
				var r = this.getResponseText().split( '<!--separate-->' );
				if( r[0] == 'ok' )
				{
					ge( 'componentsettings<?= $this->field->ID ?>' ).innerHTML = r[1];
				}
			}
			j.send();
		}
		function toggleComponent<?= $this->field->ID?>( comp, status )
		{
			var j = new bajax();
			j.openUrl( 'admin.php?module=extensions&extension=sbook&action=togglecomponent', 'post', true );
			j.addVar( 'component', comp.toLowerCase() );
			j.addVar( 'status', status );
			j.addVar( 'fid', '<?= $this->field->ID ?>' );
			j.onload = function()
			{
				loadComponentSettings<?= $this->field->ID ?>();
				loadComponents<?= $this->field->ID?>();
			}
			j.send();
		}
		loadComponents<?= $this->field->ID?>();
	</script>
	
