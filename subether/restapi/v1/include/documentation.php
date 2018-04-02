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

$xml = [];

// TODO: Add node connect handling with connect/block somehow simulair to contact and group connects

//<header>Authorization: Encrypted [Token]</header>

$xml[] = '<information>
			<load>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/information/</xml>
					<json>/api-json/v1/information/</json>
				</http>
				<data>
					<required>
						<UniqueID>SHA256 string from domain/ip + time() + rand() used as nodeid</UniqueID>
						<Url>String (Ex. "https://nodedomain.com/")</Url>
						<Name>String (Ex. "SubEther")</Name>
						<Version>String (Ex. "1.0.00")</Version>
						<Owner>String (Ex. "NodeOwner")</Owner>
						<Email>String (Ex. "owner@nodedomain.com")</Email>
						<Location>String (Ex. "NO")</Location>
						<Users>String (Ex. "5")</Users>
						<Open>Opt. ["-1":"Secret","0":"Closed","1":"Open"]</Open>
						<Created>String (Ex. "2014-10-06 16:00:00")</Created>
					</required>
				</data>
			</load>
		</information>';

$xml[] = '<register>
			<save>
				<register>
					<method>POST</method>
					<http>
						<xml>/api-xml/v1/components/register/</xml>
						<json>/api-json/v1/components/register/</json>
					</http>
					<data>
						<required>
							<Email>String (Ex. "your@email.com")</Email>
						</required>
						<optional>
							<Username>String (Ex. "YourNickName")</Username>
							<Firstname>String (Ex. "YourFirstName")</Firstname>
							<Middlename>String (Ex. "YourMiddleName")</Middlename>
							<Lastname>String (Ex. "YourLastName")</Lastname>
							<Gender>String (Ex. "Male")</Gender>
							<Mobile>String (Ex. "12345678")</Mobile>
							<Image>Binaryblob</Image>
						</optional>
					</data>
					<result>
						<AuthKey>MD5 string to use for account activation</AuthKey>
					</result>
				</register>
				<activate>
					<method>POST</method>
					<http>
						<xml>/api-xml/v1/components/register/activate/</xml>
						<json>/api-json/v1/components/register/activate/</json>
					</http>
					<data>
						<required>
							<AuthKey>MD5 string from account register request</AuthKey>
						</required>
						<optional>
							<Email>String (Ex. "your@email.com")</Email>
							<Username>String (Ex. "YourNickName")</Username>
							<UniqueID>SHA256 string from user account</UniqueID>
							<UserType>Opt. ["0":"SocialNetwork","1":"ApiUsers","2":"NodeNetwork"]</UserType>
							<Source>String (Ex. "YourSystemName")</Source>
							<Firstname>String (Ex. "YourFirstName")</Firstname>
							<Middlename>String (Ex. "YourMiddleName")</Middlename>
							<Lastname>String (Ex. "YourLastName")</Lastname>
							<Gender>String (Ex. "Male")</Gender>
							<Mobile>String (Ex. "12345678")</Mobile>
							<Image>Binaryblob</Image>
						</optional>
					</data>
				</activate>
				<recover>
					<method>POST</method>
					<http>
						<xml>/api-xml/v1/components/register/recover/</xml>
						<json>/api-json/v1/components/register/recover/</json>
					</http>
					<data>
						<optional>
							<UniqueID>SHA256 string from user account</UniqueID>
							<Email>String (Ex. "your@email.com")</Email>
							<Username>String (Ex. "YourNickName")</Username>
						</optional>
					</data>
				</recover>
			</save>
		</register>';

// TODO: Create optional login using only publickey for those that have keys stored in client and return only encrypted token.

// TODO: Only send publickey base64 encoded for authentication with source or other things to find the correct session, make privatekey based on username@email.com:MD5(password), store a secondary privatekey in the database encrypted with the master key for opening everything else, then it's easier when changing username / password for user.

// TODO: When username / password is changed it only affects new data, previous data will be able to unlock with the old data stored.

$xml[] = '<authenticate>
			<load>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/authenticate/</xml>
					<json>/api-json/v1/authenticate/</json>
				</http>
				<data>
					<required>
						<Email>Email used to log into the system</Email>
						<Source>String (Ex. "YourSystemName")</Source>
					</required>
				</data>
				<result>
					<UniqueID>SHA256 string from user account</UniqueID>
					<Token>RSA1024 string encrypted with token using users publickey</Token>
				</result>
			</load>
		</authenticate>';

// TODO: Create verifiable requests that can only be sent once and accepted once, if there is a new request it has to be re-verified, because requests even when encrypted can be sniffed and resent to server by others

$xml[] = '<secure-files>
			<load>
				<images>
					<method>GET</method>
					<http>/api/v1/secure-files/images/[UniqueID]/[Token]/</http>
				</images>
				<files>
					<method>GET</method>
					<http>/api/v1/secure-files/files/[UniqueID]/[Token]/</http>
				</files>
			</load>
		</secure-files>';

$xml[] = '<parse>
			<load>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/parse/</xml>
					<json>/api-json/v1/parse/</json>
				</http>
				<data>
					<required>
						<Query>An external url or a search query.</Query>
					</required>
					<optional>
						<Type>String (Ex. "youtube")</Type>
					</optional>
				</data>
			</load>
		</parse>';

// TODO: Find out if we are going to use [ContactID] / [UserID] or [UniqueID] to find user through API ???

$xml[] = '<contacts>
			<load>
				<contacts>
					<method>POST</method>
					<http>
						<xml>/api-xml/v1/components/contacts/</xml>
						<json>/api-json/v1/components/contacts/</json>
					</http>
					<data>
						<required>
							<SessionID>[Token] MD5 hash received on authentication</SessionID>
						</required>
						<optional>
							<ContactID>String (Ex. "9")</ContactID>
							<Limit>String (Ex. "50")</Limit>
						</optional>
					</data>
				</contacts>
				<relations>
					<method>POST</method>
					<http>
						<xml>/api-xml/v1/components/contacts/relations/</xml>
						<json>/api-json/v1/components/contacts/relations/</json>
					</http>
					<data>
						<required>
							<SessionID>[Token] MD5 hash received on authentication</SessionID>
						</required>
						<optional>
							<LastActivity>Use this to initiate long-polling by leaving it blank or by setting a timestamp.</LastActivity>
							<Loop>Max timeout in seconds (Ex. "600" default 10min)</Loop>
							<Limit>String (Ex. "300")</Limit>
						</optional>
					</data>
				</relations>
				<requests>
					<method>POST</method>
					<http>
						<xml>/api-xml/v1/components/contacts/requests/</xml>
						<json>/api-json/v1/components/contacts/requests/</json>
					</http>
					<data>
						<required>
							<SessionID>[Token] MD5 hash received on authentication</SessionID>
						</required>
						<optional>
							<ContactID>String (Ex. "9")</ContactID>
							<Limit>String (Ex. "50")</Limit>
						</optional>
					</data>
				</requests>
			</load>
			<save>
				<requests>
					<new>
						<method>POST</method>
						<http>
							<xml>/api-xml/v1/components/contacts/requests/</xml>
							<json>/api-json/v1/components/contacts/requests/</json>
						</http>
						<data>
							<required>
								<SessionID>[Token] MD5 hash received on authentication</SessionID>
							</required>
							<optional>
								<ContactID>String (Ex. "9")</ContactID>
								<ContactEmail>String (Ex. "contact@email.com")</ContactEmail>
								<ContactNumber>String (Ex. "12345678")</ContactNumber>
								<ContactUsername>String (Ex. "Username")</ContactUsername>
							</optional>
						</data>
						<result>
							<ContactID>Contacts ID number.</ContactID>
						</result>
					</new>
					<allow>
						<method>POST</method>
						<http>
							<xml>/api-xml/v1/components/contacts/requests/</xml>
							<json>/api-json/v1/components/contacts/requests/</json>
						</http>
						<data>
							<required>
								<SessionID>[Token] MD5 hash received on authentication</SessionID>
								<AllowID>String (Ex. "9" ContactID)</AllowID>
							</required>
						</data>
						<result>
							<RequestID>ID of the contact request.</RequestID>
						</result>
					</allow>
					<deny>
						<method>POST</method>
						<http>
							<xml>/api-xml/v1/components/contacts/requests/</xml>
							<json>/api-json/v1/components/contacts/requests/</json>
						</http>
						<data>
							<required>
								<SessionID>[Token] MD5 hash received on authentication</SessionID>
								<DenyID>String (Ex. "9" ContactID)</DenyID>
							</required>
						</data>
						<result>
							<RequestID>ID of the contact request.</RequestID>
						</result>
					</deny>
				</requests>
			</save>
			<delete>
				<requests>
					<method>POST</method>
					<http>
						<xml>/api-xml/v1/components/contacts/requests/</xml>
						<json>/api-json/v1/components/contacts/requests/</json>
					</http>
					<data>
						<required>
							<SessionID>[Token] MD5 hash received on authentication</SessionID>
							<CancelID>String (Ex. "9" ContactID)</CancelID>
						</required>
					</data>
					<result>
						<RequestID>ID of the contact request.</RequestID>
					</result>
				</requests>
			</delete>
		</contacts>';

$xml[] = '<messages>
			<load>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/components/chat/messages/</xml>
					<json>/api-json/v1/components/chat/messages/</json>
				</http>
				<data>
					<required>
						<SessionID>[Token] MD5 hash received on authentication</SessionID>
						<ContactID>String (Ex. "9")</ContactID>
					</required>
					<optional>
						<MessageID>String (Ex. "4989")</MessageID>
						<LastMessage>String (Ex. "4989")</LastMessage>
						<LastActivity>Use this to initiate long-polling by leaving it blank or by setting a timestamp.</LastActivity>
						<Loop>Max timeout in seconds (Ex. "600" default 10min)</Loop>
						<Limit>String (Ex. "300")</Limit>
					</optional>
				</data>
			</load>
			<save>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/components/chat/post/</xml>
					<json>/api-json/v1/components/chat/post/</json>
				</http>
				<data>
					<required>
						<SessionID>[Token] MD5 hash received on authentication</SessionID>
						<ContactID>String (Ex. "9")</ContactID>
						<Message>String (Ex. "Hello Contact")</Message>
					</required>
					<optional>
						<Encryption>String (Ex. "1024 bit RSA")</Encryption>
						<IsCrypto>Opt. ["0","1"]</IsCrypto>
						<CryptoID>Uniqueid that can be used for verifying encryption key used.</CryptoID>
						<CryptoKeys>JSON string (Ex. "{"receivers":{"sender":"[CryptoKey]","9":"[CryptoKey]"}}")</CryptoKeys>
					</optional>
				</data>
			</save>
		</messages>';

$xml[] = '<library>
			<load>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/components/library/</xml>
					<json>/api-json/v1/components/library/</json>
				</http>
				<data>
					<required>
						<SessionID>[Token] MD5 hash received on authentication</SessionID>
					</required>
					<optional>
						<CategoryID>String (Ex. "22")</CategoryID>
						<Folders>String (Ex. "31,32,33")</Folders>
						<Images>String (Ex. "21,22,23")</Images>
						<Files>String (Ex. "11,12,13")</Files>
						<Limit>String (Ex. "300")</Limit>
					</optional>
				</data>
			</load>
		</library>';

$xml[] = '<notification>
			<load>
				<messages>
					<method>POST</method>
					<http>
						<xml>/api-xml/v1/components/notification/messages/</xml>
						<json>/api-json/v1/components/notification/messages/</json>
					</http>
					<data>
						<required>
							<SessionID>[Token] MD5 hash received on authentication</SessionID>
						</required>
						<optional>
							<ContactID>String (Ex. "9")</ContactID>
							<Type>Opt. ["im","vm","cm"]</Type>
							<IsCrypto>Opt. ["0","1"]</IsCrypto>
							<IsTyping>Opt. ["0","1"]</IsTyping>
							<IsRead>Opt. ["0","1"]</IsRead>
							<IsNoticed>Opt. ["0","1"]</IsNoticed>
							<IsAlerted>Opt. ["0","1"]</IsAlerted>
							<IsAccepted>Opt. ["0","1"]</IsAccepted>
							<IsConnected>Opt. ["0","1"]</IsConnected>
							<Count>Opt. ["0","1"]</Count>
							<Limit>String (Ex. "300")</Limit>
						</optional>
					</data>
				</messages>
			</load>
		</notification>';

$xml[] = '<category>
			<load>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/components/category/</xml>
					<json>/api-json/v1/components/category/</json>
				</http>
				<data>
					<required>
						<SessionID>[Token] MD5 hash received on authentication</SessionID>
					</required>
					<optional>
						<Categories>String (Ex. "21,22,23")</Categories>
					</optional>
				</data>
			</load>
		</category>';

$xml[] = '<groups>
			<load>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/components/groups/</xml>
					<json>/api-json/v1/components/groups/</json>
				</http>
				<data>
					<required>
						<SessionID>[Token] MD5 hash received on authentication</SessionID>
					</required>
				</data>
			</load>
		</groups>';

$xml[] = '<wall>
			<load>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/components/wall/posts/</xml>
					<json>/api-json/v1/components/wall/posts/</json>
				</http>
				<data>
					<required>
						<SessionID>[Token] MD5 hash received on authentication</SessionID>
					</required>
					<optional>
						<LastPost>String (Ex. "8800")</LastPost>
						<PostID>String (Ex. "8800")</PostID>
						<CategoryID>String (Ex. "21")</CategoryID>
						<Limit>String (Ex. "50")</Limit>
					</optional>
				</data>
			</load>
			<save>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/components/wall/post/</xml>
					<json>/api-json/v1/components/wall/post/</json>
				</http>
				<data>
					<required>
						<SessionID>[Token] MD5 hash received on authentication</SessionID>
						<Type>Opt. ["post","comment"]</Type>
						<Message>String (Ex. "Hello World")</Message>
					</required>
					<optional>
						<PostID>String (Ex. "8800")</PostID>
						<ParentID>String (Ex. "8800")</ParentID>
						<CommentID>String (Ex. "8801")</CommentID>
						<ReceiverID>String (Ex. "9")</ReceiverID>
						<CategoryID>String (Ex. "21")</CategoryID>
						<Open>Opt. ["0":"Public","1":"Contacts","2":"Only Me","3":"Custom","4":"Admin"]</Open>
						<Data>JSON string with parsed data from the /parse/ api</Data>
					</optional>
				</data>
			</save>
		</wall>';

$xml[] = '<events>
			<load>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/components/events/</xml>
					<json>/api-json/v1/components/events/</json>
				</http>
				<data>
					<required>
						<SessionID>[Token] MD5 hash received on authentication</SessionID>
					</required>
					<optional>
						<Mode>Opt. ["month","day","week","year"]</Mode>
						<Type>Opt. ["calendar","events"]</Type>
						<Date>String (Ex. "2014-10-06 16:00:00")</Date>
						<CategoryID>String (Ex. "21")</CategoryID>
					</optional>
				</data>
			</load>
			<save>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/components/events/save/</xml>
					<json>/api-json/v1/components/events/save/</json>
				</http>
				<data>
					<required>
						<SessionID>[Token] MD5 hash received on authentication</SessionID>
						<EventName>String (Ex. "New event")</EventName>
						<DateStart>String (Ex. "2014-10-06 16:00:00")</DateStart>
						<DateEnd>String (Ex. "2014-10-06 18:00:00")</DateEnd>
					</required>
					<optional>
						<EventID>String (Ex. "22")</EventID>
						<HourID>String (Ex. "212")</HourID>
						<CategoryID>String (Ex. "21")</CategoryID>
						<ContactID>String (Ex. "9")</ContactID>
						<EventPlace>String (Ex. "Someplace")</EventPlace>
						<EventDetails>String (Ex. "Some details")</EventDetails>
						<HourRole>String (Ex. "Attendee")</HourRole>
						<HourSlots>String (Ex. "5")</HourSlots>
					</optional>
				</data>
			</save>
			<delete>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/components/events/delete/</xml>
					<json>/api-json/v1/components/events/delete/</json>
				</http>
				<data>
					<required>
						<SessionID>[Token] MD5 hash received on authentication</SessionID>
					</required>
					<optional>
						<EventID>String (Ex. "22")</EventID>
						<HourID>String (Ex. "212")</HourID>
					</optional>
				</data>
			</delete>
		</events>';

$xml[] = '<statistics>
			<save>
				<method>POST</method>
				<http>
					<xml>/api-xml/v1/statistics/log/</xml>
					<json>/api-json/v1/statistics/log/</json>
				</http>
				<data>
					<required>
						<SessionID>[Token] MD5 hash received on authentication</SessionID>
						<Component>String (Ex. "wall")</Component>
						<Type>String (Ex. "post")</Type>
						<Action>String (Ex. "save")</Action>
					</required>
					<optional>
						<CategoryID>String (Ex. "21")</CategoryID>
						<ContactID>String (Ex. "9")</ContactID>
						<Source>String (Ex. "YourSystemName")</Source>
						<Source>JSON string with useful data</Source>
					</optional>
				</data>
			</save>
		</statistics>';

/*$xml[] = '<browse>
			<method>POST</method>
			<http>
				<xml>/api-xml/v1/browse/</xml>
				<json>/api-json/v1/browse/</json>
			</http>
			<header>Authorization: Encrypted [Token]</header>
			<data>
				<optional>
					<Query>A search query.</Query>
					<Mode>Opt. ["web","network","videos","files","images","contacts","groups"]</Mode>
					<Page></Page>
					<Limit>String (Ex. "50")</Limit>
				</optional>
			</data>
			<result></result>
		</browse>';*/

/*$xml[] = '<account>
			<method>POST</method>
			<http>
				<xml>/api-xml/v1/account/</xml>
				<json>/api-json/v1/account/</json>
			</http>
			<header>Authorization: Encrypted [Token]</header>
			<data></data>
			<result></result>
		</account>';*/

/*$xml[] = '<about>
			<method>POST</method>
			<http>
				<xml>/api-xml/v1/about/</xml>
				<json>/api-json/v1/about/</json>
			</http>
			<header>Authorization: Encrypted [Token]</header>
			<data></data>
			<result></result>
		</about>';*/

// TODO: Add PUT, GET/POST and DELETE
// TODO: GET and POST for receiving data and PUT for storing data

// TODO: Find out if we are to put result for different requests or just response = ok / response = fail

// TODO: Should there be added example and more description for every part

// TODO: Find out where to put reporting of buggs etc for node admin.

outputXML ( ( isset( $_REQUEST['Encoding'] ) && $_REQUEST['Encoding'] == 'json' ? $json : ( "\t\t".implode( "\n\t\t", $xml )."\n" ) ), false, 'documentation' );

?>
