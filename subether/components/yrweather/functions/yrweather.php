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

$ystr = '';

// TODO: Add this cache type based on data from sql so all members don't refresh this every time they see it.

if ( isset( $_SESSION['yrweather'][1] ) && ( date( 'YmdH' ) - date( 'YmdH', $_SESSION['yrweather'][1] ) ) <= 3 )
{
	$ystr = $_SESSION['yrweather'][0];
}
else
{

	// TODO: Add settings control
	
	//$dest = 'Norway/Rogaland/Stavanger/Auglendsmyrå/';
	$dest = 'Norway/Rogaland/Stavanger/Stavanger/';
	
	$img = 'http://www.yr.no/grafikk/sym/b38';
	
	$ph = new PostHandler ( 'http://www.yr.no/place/' . $dest . 'forecast.xml' );
	$res = $ph->send();
	
	if( $res && substr( $res, 0, 5 ) == "<?xml" )
	{
		class simple_xml_extended extends SimpleXMLElement
		{
			public function Attribute( $name )
			{
				foreach( $this->Attributes() as $key=>$val )
				{
					if( $key == $name )
					{
						return (string)$val;
					}
				}
			}
		}
		
		$xml = simplexml_load_string( trim( $res ), 'simple_xml_extended' );
		
		if( $xml )
		{
			$i = 1; $l = 3; $day = false;
			
			foreach( $xml->forecast->tabular->time as $time )
			{
				if ( $i > $l ) break;
				
				if( ( date( 'YmdHis' ) <= date( 'YmdHis', strtotime( $time->Attribute('from') ) ) || date( 'YmdHis' ) <= date( 'YmdHis', strtotime( $time->Attribute('to') ) ) ) && $day != date( 'd/m', strtotime( $time->Attribute('from') ) ) )
				{
					$ystr .= '<div class="YrDay yrday_' . date( 'D', strtotime( $time->Attribute('from') ) ) . '" timestamp="' . $time->Attribute('from') . '">';
					$ystr .= '<div class="Sym"><img src="' . $img . '/' . $time->symbol->Attribute('var') . '.png"></div>';
					
					$ystr .= '<p class="Header"><strong>' . i18n ( 'Weather in' ) . ' ' . $xml->forecast->text->location->Attribute('name') . '</strong></p>';
					$ystr .= '<p class="DayName">';
					
					foreach( $xml->forecast->text->location->time as $text )
					{
						if( $text->Attribute('from') == substr( $time->Attribute('from'), 0, 10 ) )
						{
							
							$ystr .= '<span class="DayName">' . i18n ( 'yrday_' . $text->title ) . '</span> ';
						}
					}
					
					$ystr .= '<span class="Date">' . ( $day = ( date( 'd/m', strtotime( $time->Attribute('from') ) ) ) ) . '</span></p>';
					
					$ystr .= '<table class="Weather">';
					$ystr .= '<tbody>';
					$ystr .= '<tr class="Time">';
					$ystr .= '<th>' . i18n ( 'Time' ) . ':</th>';
					$ystr .= '<td>' . date( 'H:i', strtotime( $time->Attribute('from') ) ) . '-' . date( 'H:i', strtotime( $time->Attribute('to') ) ) . '</td>';
					$ystr .= '</tr>';
					$ystr .= '<tr class="Temp">';
					$ystr .= '<th>' . i18n ( 'Temp' ) . ':</th>';
					$ystr .= '<td>' . $time->temperature->Attribute('value') . '°</td>';
					$ystr .= '</tr>';
					$ystr .= '<tr class="Rain">';
					$ystr .= '<th>' . i18n ( 'Rain' ) . ':</th>';
					$ystr .= '<td>' . $time->precipitation->Attribute('value') . ' mm</td>';
					$ystr .= '</tr>';
					$ystr .= '<tr class="Wind">';
					$ystr .= '<th>' . i18n ( 'Wind' ) . ':</th>';
					$ystr .= '<td>' . $time->windSpeed->Attribute('mps') . ' m/s</td>';
					$ystr .= '</tr>';
					$ystr .= '<tr class="Direction">';
					$ystr .= '<th>' . i18n ( 'Direction' ) . ':</th>';
					$ystr .= '<td>' . i18n ( 'from' ) . ' ' . i18n ( $time->windDirection->Attribute('name') ) . '</td>';
					$ystr .= '</tr>';
					$ystr .= '</tbody>';
					$ystr .= '</table>';
					$ystr .= '</div>';
					
					$i++;
				}
			}
			
			$ystr .= '<div class="More">';
			$ystr .= '<a onclick="window.open(\'' . $xml->credit->link->Attribute('url') . '\')" href="javascript:void(0)">';
			$ystr .= i18n ( 'More information' );
			$ystr .= '</a>';
			$ystr .= '</div>';
			$ystr .= '<div class="Credit">';
			$ystr .= '<a onclick="window.open(\'' . $xml->credit->link->Attribute('url') . '\')" href="javascript:void(0)">';
			$ystr .= i18n ( $xml->credit->link->Attribute('text') );
			$ystr .= '</a>';
			$ystr .= '</div>';
			
			$_SESSION['yrweather'] = array( 0=>$ystr, 1=>time() );
		}
	}
}

?>
