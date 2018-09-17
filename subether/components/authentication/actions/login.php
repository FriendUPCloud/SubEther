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

include_once ( 'subether/classes/fcrypto.class.php' );
include_once ( 'subether/components/register/include/functions.php' );

if ( $_REQUEST['action'] == 'login' )
{
	// If we have uniqueid use this method
	if ( $_REQUEST['PublicKey'] && $_REQUEST['UniqueID'] )
	{
		if ( $usr = $database->fetchObjectRow( '
			SELECT
				*
			FROM 
				`Users` 
			WHERE
					UserType = "0" 
				AND UniqueID = \'' . trim( $_REQUEST['UniqueID'] ) . '\' 
				AND NodeID = "0" 
			ORDER BY
				ID DESC 
		' ) )
		{
			if ( /*$usr->InActive == 0 && $usr->IsDisabled == 0 && */$usr->IsDeleted == 0 )
			{
				$fcrypt = new fcrypto();
				
				// If we have publickey and it doesn't exist update the database
				if( $_REQUEST['PublicKey'] && !$usr->PublicKey )
				{
					$usp = new dbObject( 'Users' );
					$usp->ID = $usr->ID;
					if( $usp->Load() )
					{
						$usp->PublicKey = $_REQUEST['PublicKey'];
						$usp->Save();
					}
				}
				
				$u = new dbObject( 'Users' );
				$u->ID = $usr->ID;
				if ( $u->Load() && $u->PublicKey && $fcrypt->stripHeader( $u->PublicKey ) == $fcrypt->stripHeader( $_REQUEST['PublicKey'] ) )
				{
					// --- Phase1: Create random key based on sha256 and send it to be signed -------------------------------
					if ( !$_REQUEST['Signature'] && !$_REQUEST['SessionID'] )
					{
						//$u->Password = UniqueKey();
						$u->Password = md5(rand(0,9999).rand(0,9999).rand(0,9999).microtime());
						$u->InActive = 0;
						$u->IsDisabled = 0;
						$u->AuthKey = '';
						$u->Save();
						
						$encrypted = $fcrypt->encryptRSA( $u->Password, $u->PublicKey );
						$ciphertext = $encrypted;
						
						if ( $u->ID > 0 && $ciphertext )
						{
							die( 'authenticate<!--separate-->' . trim( $ciphertext ) );
						}
					}
					// --- Phase2: Verify signature for authentication -------------------------------------------------------
					else if ( $_REQUEST['Signature'] && $fcrypt->verifyString( $u->Password, $_REQUEST['Signature'], $u->PublicKey ) )
					{
						if( isset( $_POST['Key'] ) )
						{
							if( $_POST['Key'] == '-1' )
							{
								$u->StoreKey = 0;
								$u->Save();
							}
							else if( $key = json_decode( $_POST['Key'] ) )
							{
								$uniqueid = UniqueKey();
								
								foreach( $key as $k )
								{
									if( $k->UserID && $k->EncryptionKey && $k->PublicKey )
									{
										$ss = new dbObject( 'SBookStorage' );
										$ss->UnlockID = $k->UserID;
										$ss->UserID = $u->ID;
										$ss->Relation = 'Users';
										$ss->IsDeleted = '0';
										if( !$ss->Load() )
										{
											$ss->UniqueID = $uniqueid;
											$ss->DateCreated = date( 'Y-m-d H:i:s' );
										}
										$ss->EncryptionKey = $k->EncryptionKey;
										$ss->PublicKey = $k->PublicKey;
										$ss->DateModified = date( 'Y-m-d H:i:s' );
										$ss->Save();
									}
								}
							}
						}
						
						// Check if this user is new and are missing dependency in database
						$co = new dbObject( 'SBookContact' );
						$co->UserID = $u->ID;
						if( !$co->Load() )
						{
							$co->Username = $u->Name;
							$co->Email = $u->Email;
							$co->DateCreated = date( 'Y-m-d H:i:s' );
							$co->DateModified = date( 'Y-m-d H:i:s' );
							$co->Save();
							
							$gr = new dbObject( 'Groups' );
							$gr->Name = 'SocialNetwork';
							if( !$gr->Load() )
							{
								$gr->Save();
							}
							
							$ug = new dbObject( 'UsersGroups' );
							$ug->GroupID = $gr->ID;
							$ug->UserID = $us->ID;
							if( !$ug->Load() )
							{
								$ug->Save();
							}
							
							// Assign to SubEther user/group
							if( function_exists( 'assignToNewMembers' ) )
							{
								assignToNewMembers( $co->ID );
							}
						}
						
						// Login User
						$webuser = new dbUser();
						$webuser->setEncryptionMethod( 'plain' );
						$webuser->reauthenticate( $u->Username, $u->Password );
						if ( $webuser->is_authenticated )
						{
							// If this user is superadmin give access to arena admin
							if ( $webuser->isSuperUser() )
							{
								// TODO: Get access to the admin session somehow, right now we are only using cookie to get arena admin access
								//$Session = new Session ( SITE_ID . 'admin' );
								//$GLOBALS[ 'Session' ] = $Session;
								
								//$_SESSION['arena_Username'] = $u->Username;
								//$_SESSION['arena_Password'] = $u->Password;
								
								setcookie( 'arena_UserToken', $webuser->GetToken(), time() + 2592000, '/' );
							}
							
							if ( isset( $_REQUEST['bajaxrand'] ) )
							{
								//die( $webuser->ID . ' -- ' . $webuser->GetToken() . ' [] ' . print_r( $_SESSION,1 ) );
								
								die( 'ok<!--separate-->' . BASE_URL . 'home/<!--separate-->' . print_r( $_SESSION,1 ) );
								
								//die( 'ok<!--separate-->' . BASE_URL . 'home/' );
							}
							else
							{
								if ( isset( $_REQUEST['refresh'] ) )
								{
									header( 'Location: index.php' );
								}
								else
								{
									// Redirect to main view when logged in
									header( 'Location: ' . BASE_URL . ( isset( $_REQUEST['redirect'] ) ? $_REQUEST['redirect'] : 'home/' ) );
								}
								
								die( 'ok' );
							}
						}
					}
					// --- Secondary option: Using SessionID to verify authentication --------------------------------------
					else if ( $_REQUEST['SessionID'] )
					{
						$sess = new dbObject ( 'UserLogin' );
						$sess->Token = $_REQUEST['SessionID'];
						if ( $sess->Load () )
						{
							// Check if this user is new and are missing dependency in database
							$co = new dbObject( 'SBookContact' );
							$co->UserID = $u->ID;
							if( !$co->Load() )
							{
								$co->Username = $u->Name;
								$co->Email = $u->Email;
								$co->DateCreated = date( 'Y-m-d H:i:s' );
								$co->DateModified = date( 'Y-m-d H:i:s' );
								$co->Save();
								
								$gr = new dbObject( 'Groups' );
								$gr->Name = 'SocialNetwork';
								if( !$gr->Load() )
								{
									$gr->Save();
								}
								
								$ug = new dbObject( 'UsersGroups' );
								$ug->GroupID = $gr->ID;
								$ug->UserID = $us->ID;
								if( !$ug->Load() )
								{
									$ug->Save();
								}	
								
								// Assign to SubEther user/group
								if( function_exists( 'assignToNewMembers' ) )
								{
									assignToNewMembers( $co->ID );
								}
							}
							
							// Login User
							$webuser = new dbUser();
							$webuser->setEncryptionMethod( 'plain' );
							$webuser->reauthenticate( $u->Username, $u->Password );
							if ( $webuser->is_authenticated )
							{
								// If this user is superadmin give access to arena admin
								if ( $webuser->isSuperUser() )
								{
									// TODO: Get access to the admin session somehow, right now we are only using cookie to get arena admin access
									//$Session = new Session ( SITE_ID . 'admin' );
									//$GLOBALS[ 'Session' ] = $Session;
									
									//$_SESSION['arena_Username'] = $u->Username;
									//$_SESSION['arena_Password'] = $u->Password;
									
									setcookie( 'arena_UserToken', $webuser->GetToken(), time() + 2592000, '/' );
								}
								
								if ( isset( $_REQUEST['bajaxrand'] ) )
								{
									die( 'ok<!--separate-->' . BASE_URL . 'home/' );
								}
								else
								{
									if ( isset( $_REQUEST['refresh'] ) )
									{
										header( 'Location: index.php' );
									}
									else
									{
										// Redirect to main view when logged in
										header( 'Location: ' . BASE_URL . ( isset( $_REQUEST['redirect'] ) ? $_REQUEST['redirect'] : 'home/' ) );
									}
									
									die( 'ok' );
								}
							}
						}
						else
						{
							die( 'fail<!--separate-->Session expired or wasn\'t found' );
						}
					}
				}
			}
			/*else if ( $usr->InActive == 1 )
			{
				die( 'locked<!--separate-->Your account isn\'t activated, activate it first' . ( $usr->AuthKey ? ( '<!--separate-->' . BASE_URL . 'register/?activate=' . $usr->UniqueID . '&email=' . $usr->Username ) : '' ) );
			}*/
			else if ( /*$usr->IsDisabled == 1 || */$usr->IsDeleted == 1 )
			{
				die( 'locked<!--separate-->Your account is disabled, recover it first' . ( $usr->AuthKey ? ( '<!--separate-->' . BASE_URL . 'register/?recover=' . $usr->UniqueID . '&user=' . $usr->Username ) : '' ) );
			}
		}
		
		die( 'fail<!--separate-->Username and Password didnt match ( user@email.com / yourpassword )' );
	}
}

die( 'fail<!--separate-->Something whent wrong contact support ' . print_r( $_REQUEST,1 ) );

?>
