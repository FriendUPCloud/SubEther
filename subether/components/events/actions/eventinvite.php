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

global $database, $webuser;

$q = '
	SELECT 
		e.*,
		c.Email, 
		i.UniqueID AS ImageUniqueID, 
		i.Width, 
		i.Height 
	FROM
		SBookContact c, 
		SBookEvents e
			LEFT JOIN Image i ON
			(
					e.ImageID > 0
				AND e.ImageID = i.ID
			) 
	WHERE 
			e.ID = \'' . $_REQUEST['eid'] . '\'
		AND e.UserID > 0 
		AND c.ID = e.UserID 
	ORDER BY 
		e.ID DESC 
';

if ( $event = $database->fetchObjectRow( $q, false, 'components/events/actions/eventinvite.php' ) )
{
	$invited = false;
	
	$invite = new CalendarInvite();
	$invite->SetSummary( $event->Name );
	$invite->SetDescription( $event->Details );
	$invite->SetStart( $event->DateStart );
	$invite->SetEnd( $event->DateEnd );
	$invite->SetLocation( $event->Place );
	$invite->SetOrganizer( $event->Email, GetUserDisplayname( $event->UserID ) );
	
	if ( $hrs = $database->fetchObjectRows( '
		SELECT
			c.*
		FROM
			SBookHours h,
			SBookContact c 
		WHERE
				h.ProjectID = \'' . $event->ID . '\'
			AND h.Title = "events" 
			AND h.UserID > 0
			AND h.IsDeleted = "0" 
			AND c.ID = h.UserID 
		ORDER BY
			h.ID ASC 
	' ) )
	{
		foreach( $hrs as $hr )
		{
			$invited = true;
			
			$invite->AddAttendee( $hr->Email, GetUserDisplayname( $hr->ID ) );
		}
		
		//$invite->AddAttendee( 'cas@friendup.cloud', 'Chris Andre Strømland' );
		//$invite->AddAttendee( 'tw@friendup.cloud', 'Thomas Wollburg' );
		//$invite->AddAttendee( 'ht@friendup.cloud', 'Hogne Titlestad' );
	}
	
	if ( $invited )
	{
		if ( $data = $invite->Generate() )
		{
			$rawdata = false; $attachment = new stdClass();
			
			$attachment->Filename = $invite->filename;
			$attachment->Encoding = 'base64';
			$attachment->Type = 'text/calendar; charset=utf-8; method=REQUEST';
			$attachment->Data = $data;
			
			if ( $invite->attendees && is_array( $invite->attendees ) )
			{
				// Send invitation to emails
				
				$sendt = false;
				
				foreach( $invite->attendees as $email=>$name )
				{
					if ( $email )
					{
						// TODO: Add translation
						
						$cs  = 'Invitasjon: ' . $event->Name . ' @ ';
						$cs .= date( 'D. j. M Y H:i', strtotime( $event->DateStart ) );
						$cs .= date( ' - H:i (T)', strtotime( $event->DateEnd ) );
						$cs .= ' (' . $event->Email . ')';
						$cr  = $email;
						$cf  = $event->Email;
						$cm  = '&nbsp;';
						
						$ct  = 'html';
						
						$boundary = array(); $uniqueid = md5(uniqid(time()));
						
						//Create unique IDs and preset boundaries
						$boundary[1] = 'b1_' . $uniqueid;
						$boundary[2] = 'b2_' . $uniqueid;
						$boundary[3] = 'b3_' . $uniqueid;
						
						$rawdata = new stdClass();
						
						$rawdata->type = 'alt_attach';
						$rawdata->boundary = $boundary[1];
						
						$rawdata->body  = '--' . $boundary[1] . "\n";
						$rawdata->body .= 'Content-Type: multipart/alternative; boundary=' . $boundary[2] . "\n";
						$rawdata->body .= "\n";
						$rawdata->body .= '--' . $boundary[2] . "\n";
						$rawdata->body .= 'Content-Type: text/calendar; charset=UTF-8; method=REQUEST' . "\n";
						$rawdata->body .= 'Content-Transfer-Encoding: quoted-printable' . "\n";
						$rawdata->body .= "\n";
						$rawdata->body .= $data . "\n";
						$rawdata->body .= "\n";
						$rawdata->body .= '--' . $boundary[2] . '--' . "\n";
						$rawdata->body .= '--' . $boundary[1] . "\n";
						$rawdata->body .= 'Content-Type: application/ics; name="invite.ics"' . "\n";
						$rawdata->body .= 'Content-Disposition: attachment; filename="invite.ics"' . "\n";
						$rawdata->body .= 'Content-Transfer-Encoding: base64' . "\n";
						$rawdata->body .= "\n";
						$rawdata->body .= chunk_split( base64_encode( $data ) );
						$rawdata->body .= '--' . $boundary[1] . '--' . "\n";
						
						mailNow_ ( $cs, $cm, $cr, $ct, $cf, $attachment, 'plain', $rawdata );
						
						$sendt = true;
					}
				}
				
				die( 'ok<!--separate-->' . ( $sendt ? 'sendt' : 'something failed' ) );
			}
		}
	}
}

die( 'no data' );

?>
