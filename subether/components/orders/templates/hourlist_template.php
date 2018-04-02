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
		<style>
			
			body
			{
				font-size: 8pt;
			}
			
			table
			{
				border-collapse: collapse;
				width: 100%;
				border: none;
			}
			
			td
			{
				text-align: left;
			}
			
			.heading1 .col1
			{
				width: 68%;
			}
			.heading1 .col2
			{
				width: 32%;
			}
			
			.heading1 td
			{
				line-height: 10px;
			}
			
			.user .col1
			{
				width: 9%;
			}
			.user .col2
			{
				width: 15%;
			}
			.user .col3
			{
				width: 7%;
			}
			.user .col4
			{
				width: 18%;
			}
			.user .col5
			{
				width: 4%;
			}
			.user .col6
			{
				width: 13%;
			}
			
			.user td
			{
				line-height: 5px;
				border-bottom: 1px dotted black;
			}
			
			.info td
			{
				width: 100%;
				line-height: 10px;
				text-align: right;
			}
			
			.heading2 .col1
			{
				line-height: 10px;
				border-left: none;
				width: 9%;
				text-align: right;
			}
			.heading2 .col2
			{
				line-height: 10px;
				width: 25%;
			}
			.heading2 .col3
			{
				line-height: 10px;
				width: 5%;
				text-align: center;
			}
			.heading2 .col4
			{
				line-height: 6px;
				width: 13%;
				border-bottom: 1px solid black;
				text-align: center;
			}
			.heading2 .col5
			{
				line-height: 10px;
				width: 5%;
				text-align: center;
			}
			.heading2 .col6
			{
				line-height: 6px;
				width: 9%;
				border-bottom: 1px solid black;
				text-align: center;
			}
			.heading2 .col7
			{
				line-height: 10px;
				width: 13%;
			}
			.heading2 .col8
			{
				line-height: 6px;
				width: 13%;
				border-bottom: 1px solid black;
				text-align: center;
			}
			.heading2 .col9
			{
				line-height: 10px;
				width: 8%;
				text-align: center;
			}
			
			.heading2 td
			{
				font-size: 7pt;
				height: 10px;
				border-top: 2px solid black;
				border-left: 1px solid black;
				border-bottom: 2px solid black;
			}
			
			.heading3 .col1
			{
				width: 5%;
				text-align: center;
			}
			.heading3 .col2
			{
				width: 8%;
				text-align: center;
			}
			.heading3 .col3
			{
				width: 4%;
				text-align: center;
			}
			.heading3 .col4
			{
				width: 5%;
				text-align: center;
			}
			.heading3 .col5
			{
				width: 6%;
				text-align: center;
			}
			.heading3 .col6
			{
				width: 7%;
				text-align: center;
			}
			
			.heading3 td
			{
				font-size: 6pt;
				line-height: 5px;
				border-left: 1px solid black;
				border-bottom: 2px solid black;
			}
			
			.row td
			{
				line-height: 8px;
				border-top: 1px solid black;
				border-left: 1px solid black;
			}
			
			.row .col1
			{
				border-left: none;
				text-align: right;
			}
			
			.row .col3, .row .col4, .row .col5, .row .col6, .row .col7, .row .col8, .row .col10, .row .col11, .row .col12
			{
				text-align: center;
			}
			
			.sum td
			{
				line-height: 8px;
				border-top: 2px solid black;
				border-bottom: 2px solid black;
				border-left: 1px solid black;
				text-align: center;
			}
			
			.sum .col1
			{
				border-left: 1px solid white;
			}
			
			.sign
			{
				border-bottom: 1px solid black;
			}
			
			.footer1
			{
				line-height: 12px;
			}
			
			.footer2
			{
				line-height: 5px;
			}
			
			.footer3 td
			{
				font-size: 15pt;
				line-height: 10px;
				text-align: center;
			}
			
		</style>
	</head>
	<body>
		<table>
			
			<tr class="heading1">
				<td class="col1" colspan="10"><h1>TIMESEDDEL</h1></td>
				<td class="col2" rowspan="2" colspan="4"><span>ROGALAND ELEKTRO LOGO</span></td>
			</tr>
			
			<tr class="user">
				<td class="col1"><strong>Montør nr.:</strong></td>
				<td class="col2"><span><?= $this->d->UserID ?></span></td>
				<td class="col3"><strong>Uke nr.:</strong></td>
				<td class="col4" colspan="3"><span><?= $this->d->Date ? date( 'W', $this->d->Date ) : '' ?></span></td>
				<td class="col5"><strong>År:</strong></td>
				<td class="col6" colspan="3"><span><?= $this->d->Date ? date( 'Y', $this->d->Date ) : '20' ?></span></td>
			</tr>
			
			<tr class="info">
				<td colspan="14">
					<span>A: arbeidsleder</span>
					<span>B: bas</span>
					<span>F: formann</span>
					<span>AL: anleggsleder</span>
					<span>S: smusstillegg</span>
					<span>H: høydetillegg</span>
				</td>
			</tr>
			
			<tr class="heading2">
				<td class="col1" rowspan="2"><span>Ordrenr.</span></td>
				<td class="col2" rowspan="2" colspan="3"><span>Kunde</span></td>
				<td class="col3" rowspan="2"><span>Dato</span></td>
				
				<td class="col4" colspan="2"><span>Timer totalt</span></td>
				
				<td class="col5" rowspan="2"><span>Kode</span></td>
				
				<td class="col6" colspan="2"><span>Overtidstilleg</span></td>
				
				<td class="col7" rowspan="2"><span>Tilleggstekst</span></td>
				
				<td class="col8" colspan="2"><span>Diverse</span></td>
				
				<td class="col9" rowspan="2"><span>Km</span></td>
			</tr>
			
			<tr class="heading3">
				<td class="col1"><span>Dag tid</span></td>
				<td class="col2"><span>Avspasering</span></td>
				
				<td class="col3"><span>50%</span></td>
				<td class="col4"><span>100%</span></td>
				
				<td class="col5"><span>Bom</span></td>
				<td class="col6"><span>Parkering</span></td>
			</tr>
			
			<?php
				
				$str = '';
				
				for ( $a = 0; $a < 27; $a++ )
				{
					$data = ( isset( $this->d->Projects[$a] ) ? $this->d->Projects[$a] : false );
					
					$str .= '
						<tr class="row">
							<td class="col1"><span>' . ( isset( $data->ID ) ? $data->ID : '' ) . '</span></td>
							<td class="col2" colspan="3"><span>' . ( isset( $data->Name ) ? $data->Name : '' ) . '</span></td>
							<td class="col3"><span>' . ( isset( $data->Date ) ? date( 'd', $data->Date ) : '' ) . '</span></td>
							<td class="col4"><span>' . ( isset( $data->Hours ) ? $data->Hours : '' ) . '</span></td>
							<td class="col5"><span></span></td>
							<td class="col6"><span></span></td>
							<td class="col7"><span></span></td>
							<td class="col8"><span></span></td>
							<td class="col9"><span></span></td>
							<td class="col10"><span></span></td>
							<td class="col11"><span></span></td>
							<td class="col12"><span></span></td>
						</tr>
					';
				}
				
				return $str;
				
			?>
			
			<tr class="sum">
				<td class="col1" colspan="4"></td>
				<td><strong>SUM</strong></td>
				<td><span><?= isset( $this->d->Total ) ? $this->d->Total : '' ?></span></td>
				<td><span></span></td>
				<td><span></span></td>
				<td><span></span></td>
				<td><span></span></td>
				<td><span></span></td>
				<td><span></span></td>
				<td><span></span></td>
				<td><span></span></td>
			</tr>
			
			<tr class="footer1">
				<td colspan="3"><span>Montørens sign.</span></td>
				<td></td>
				<td colspan="5"><span>Formann/Bas sign.</span></td>
				<td></td>
				<td colspan="4"><span>Saksbehandler sign.</span></td>
			</tr>
			
			<tr class="footer2">
				<td class="sign" colspan="3"><span></span></td>
				<td></td>
				<td class="sign" colspan="5"><span></span></td>
				<td></td>
				<td class="sign" colspan="4"><span></span></td>
			</tr>
			
			<!--<tr class="footer3">
				<td colspan="14"><span>- Når du trenger elektriker</span></td>
			</tr>-->
			
		</table>
	</body>
</html>
