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

global $page, $document, $webuser;

$page->loadExtraFields ();

$document->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );

// Look for sass with an all.css index file
if ( ( file_exists ( 'upload/template/css/all.css' ) && filesize ( 'upload/template/css/all.css' ) <= 0 ) || defined ( 'TEMPLATES_SASS_COMPILE_ALWAYS' ) )
{
	if ( file_exists ( 'upload/template/css/scss' ) && is_dir ( $d = 'upload/template/css/scss' ) )
	{
		if ( $dir = opendir ( $d ) )
		{
			require_once ( 'extensions/templates/include/thirdparty/scss.inc.php' );
			$sass = new scssc ();
			$sass->addImportPath ( 'upload/template/css/scss' );
			$css = $sass->compile ( file_get_contents ( $d . '/all.scss' ) );
			if ( $fr = fopen ( 'upload/template/css/all.css', 'w+' ) )
			{
				fwrite ( $fr, $css );
				fclose ( $fr );
			}
		}
	}
}

function checkUserAgent ( $type = false )
{
	// overrule from request or from session
	if( $_REQUEST['displaymode'] == '0' )
	{
		if( !isset( $_REQUEST['save'] ) || $_REQUEST['save'] )
		{
			$_SESSION['UserAgent'] = 'browser';
		}
		return 'browser';
	}
	if( $_REQUEST['displaymode'] == '1' || $_SESSION['UserAgent'] == 'mobile' )
	{
		if( !isset( $_REQUEST['save'] ) || $_REQUEST['save'] )
		{
			$_SESSION['UserAgent'] = 'mobile';
		}
		return 'mobile';
	}
	if( $_REQUEST['displaymode'] == '2' || $_SESSION['UserAgent'] == 'presentation' )
	{
		if( !isset( $_REQUEST['save'] ) || $_REQUEST['save'] )
		{
			$_SESSION['UserAgent'] = 'presentation';
		}
		return 'mobile presentation';
	}
	if ( $_REQUEST['displaymode'] == '3' || $_SESSION['UserAgent'] == 'tablet' )
	{
		if( !isset( $_REQUEST['save'] ) || $_REQUEST['save'] )
		{
			$_SESSION['UserAgent'] = 'tablet';
		}
		return 'tablet';
	}
	// find user agent viewing
	$user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
	// matches popular bots
	if ( $type == 'bot' ) 
	{
		// watchmouse|pingdom\.com are "uptime services"
		if ( preg_match ( '/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/i', $user_agent ) ) 
		{
			return 'bot';
		}
	} 
	// matches core browser types
	else if ( $type == 'browser' ) 
	{
		if ( preg_match ( "/mozilla\/|opera\//", $user_agent ) )
		{
			return 'browser';
		}
	}
	else if ( $type == 'mobile' ) 
	{
		// matches popular mobile devices that have small screens and/or touch inputs
		// mobile devices have regional trends; some of these will have varying popularity in Europe, Asia, and America
		// detailed demographics are unknown, and South America, the Pacific Islands, and Africa trends might not be represented, here
		// these are the most common
		if ( preg_match ( '/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/i', $user_agent ) ) 
		{
			// Don't include tablets!
			if ( preg_match ( '/ipad/i', $user_agent ) )
			{
				return false;
			}
			return 'mobile';
		} 
		// these are less common, and might not be worth checking
		else if ( preg_match ( '/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /i', $user_agent ) )
		{
			// Don't include tablets!
			if ( preg_match ( '/ipad/i', $user_agent ) )
			{
				return false;
			}
			return 'mobile';
		}
	}
	else if ( $type == 'tablet' )
	{
		if ( preg_match ( '/ipad|android/i', $user_agent ) )
		{
			return 'tablet';
		}
	}
	return false;
}

function tplLoadMenu ( $maxdepth, $skiproot, $parent = '0', $depth = 0 )
{
	global $database, $page, $Session;
	$str = '';
	$nd = $depth + 1;
	$lang = $Session->CurrentLanguage;
	if ( $nd > $maxdepth ) return;
	if ( $rows = $database->fetchObjectRows ( '
		SELECT * FROM ContentElement
		WHERE
			    Parent=\'' . $parent . '\'
			AND IsPublished
			AND !IsDeleted
			AND !IsSystem
			AND MainID = ID
			AND Language=\'' .  $lang . '\'
		ORDER BY
			SortOrder ASC
	' ) )
	{
		$str .= '<ul' . ( $depth == 0 ? " class=\"menuroot\"" : '' ) . '>';
		foreach ( $rows as $row )
		{
			$class = 'li_' . texttourl ( $row->MenuTitle );
			if ( $row->MainID == $page->MainID )
				$class .= ' current';
			$str .= '<li class="' . $class . '">';
			if ( $depth > 0 || !$skiproot )
			{
				$p = new dbContent ( $row->ID );
				if ( $row->Link )
				{
					$data = CreateObjectFromString ( $row->LinkData );
					$lnk = $row->Link;
					$trg = ' target="' . $data->LinkTarget . '"';
				}
				else 
				{
					$trg = '';
					$lnk = $p->getUrl ();
				}
				$str .= '<a href="' . $lnk . '"' . $trg . '>' . $row->MenuTitle . '</a>';
			}
			$str .= tplLoadMenu ( $maxdepth, $skiproot, $row->MainID, $nd );
			$str .= '</li>';
		}
		$str .= '</ul>';
	}
	return $str;
}

if ( !isset( $_REQUEST['save'] ) || $_REQUEST['save'] )
{
	$_SESSION['rendermodule'] = ( isset( $_REQUEST['rendermodule'] ) ? $_REQUEST['rendermodule'] : $_SESSION['rendermodule'] );
}


$templateFile = ( ( $_SESSION['rendermodule'] || $_REQUEST['rendermodule'] ) ? 'custom.html' : 'index.html' );

foreach ( $page as $k=>$v )
{
	if ( substr ( $k, 0, 7 ) == '_field_' )
	{
		if ( $v->Type == 'extension' && $v->DataString == 'templates' )
		{
			$conf = CreateObjectFromString ( $v->DataMixed );
			if ( isset ( $conf->Template ) )
			{
				$templateFile = $conf->Template;
			}
		}
	}
}

if ( file_exists ( 'upload/template/templates/' . $templateFile ) )
{
	$parsedFields = array ();
	
	// Get template
	$m = file_get_contents ( 'upload/template/templates/' . $templateFile );
	
	// Check if browser refers to another template
	if ( preg_match ( '/\ mobileversion\=\"([^"]*?)\"/i', $m, $matches ) )
	{
		if ( checkUserAgent ( 'mobile' ) )
		{
			$m = file_get_contents ( 'upload/template/templates/' . $matches[1] );
		}
		$m = str_replace ( $matches[0], '', $m );
	}

	// Do replacements .......................
	
	// Html
	if ( preg_match ( '/\<html[^>]*?\>/i', $m, $h ) )
	{
		// TODO: Find Languagecode ... and maybe more stuff we need ...
		//$GLOBALS['Session']['LanguageCode']
		$m = str_replace ( $h[0], '<html lang="' . ( isset( $GLOBALS[ "Session" ]->LanguageCode ) ? $GLOBALS[ "Session" ]->LanguageCode : 'en' ) . '">', $m );
	}
	
	// Body
	if ( preg_match ( '/\<body[^>]*?\>/i', $m, $b ) )
	{
		$bodyClass = texttourl ( $page->MenuTitle );
		if ( isset ( $document->BodyClass ) )
		{
			$bodyClass .= ' ' . $document->BodyClass;
		}
		
		// Bot ---
		if( $t = checkUserAgent( 'bot' ) )
		{
			$bodyClass .= ' ' . $t;
		}
		// Mobile ---
		else if( $t = checkUserAgent( 'mobile' ) )
		{
			$bodyClass .= ' ' . $t;
		}
		// Tablet ---
		else if( $t = checkUserAgent( 'tablet' ) )
		{
			$bodyClass .= ' ' . $t;
		}
		// Presentation ---
		else if( $t = checkUserAgent( 'presentation' ) )
		{
			$bodyClass .= ' ' . $t;
		}
		// Browser ---
		else if( $t = checkUserAgent( 'browser' ) )
		{
			$bodyClass .= ' ' . $t;
		}
		//die( $bodyClass );
		/*if( ( $t = checkUserAgent( 'bot' ) ) || ( $t = checkUserAgent( 'browser' ) ) || ( $t = checkUserAgent( 'mobile' ) ) || ( $t = checkUserAgent( 'tablet' ) ) )
		{
			$bodyClass .= ' ' . $t;
		}*/
		if( $_SESSION['rendermodule'] || $_REQUEST['rendermodule'] )
		{
			$bodyClass .= ' custom';
		}
		$m = str_replace ( $b[0], '<body class="' . $bodyClass . '">', $m );
	}
	// Groups
	if ( preg_match_all ( '/\<group[^>]*?\>/i', $m, $matches ) )
	{
		//die( print_r( $matches[0],1 ) . ' -- ' . print_r( $page,1 ) );	
		foreach ( $matches[0] as $n )
		{
			if ( preg_match ( '/name\=\"([^"]*?)\"/i', $n, $nm ) )
			{
				$s = '';
				foreach ( $page as $k=>$v )
				{
					if ( substr ( $k, 0, 7 ) == '_extra_' )
					{
						$k2 = str_replace ( '_extra_', '_field_', $k );
						$pl = str_replace ( '_extra_', '', $k );
						$fl = $page->$k2;
						//die( $fl . ' -- ' . trim($nm[1]) );
						if ( trim($fl->ContentGroup) == trim($nm[1]) )
						{
							// Replaced fields
							if ( isset ( $page->{"_replacement_{$pl}"} ) )
							{
								$s .= $page->{"_replacement_{$pl}"};
							}
							// Overwritten fields
							else if ( isset ( $page->{"_protected_{$pl}"} ) )
							{
								$s .= $page->{"_protected_{$pl}"};
							}
							// Already rendered?
							else if ( $page->$pl )
							{
								$s .= $page->$pl;
							}
							// Re-Render
							else 
							{
								$s .= $page->renderExtraField ( $fl );
							}
							$parsedFields[$pl] = true;
						}
					}
				}
				$m = str_replace ( $n, $s, $m );
			}
		}
	}
	// ex. <title/>
	if ( preg_match_all ( '/\<title[^>]*?\>/i', $m, $matches ) )
	{
		$title = isset ( $document->sTitle ) ? $document->sTitle : ( defined ( 'SITE_TITLE' ) ? SITE_TITLE : SITE_ID );
		foreach ( $matches[0] as $n )
		{
			$m = str_replace ( $n, '<title>' . $title . ( trim ( $page->MenuTitle ) ? ( ' - ' . $page->MenuTitle ) : '' ) . '</title>', $m );
		}
	}
	// ex. <basehref/>
	if ( preg_match_all ( '/\<basehref[^>]*?\>/i', $m, $matches ) )
	{
		foreach ( $matches[0] as $n )
		{
			$m = str_replace ( $n, '<base href="' . BASE_URL . '"/>', $m );
		}
	}
	// TopMenu 
	// ex. <topmenu levels="3"/>
	if ( preg_match_all ( '/\<topmenu[^>]*?\>/i', $m, $matches ) )
	{
		foreach ( $matches[0] as $n )
		{
			$levels = $skiproot = '';
			if ( preg_match ( '/levels\=\"([^"]*?)\"/i', $n, $nm ) )
			{
				$levels = $nm[1];
			}
			if ( preg_match ( '/skiproot\=\"([^"]*?)\"/i', $n, $nm ) )
			{
				$skiproot = $nm[1];
			}
			
			$m = str_replace ( $n, tplLoadMenu ( $levels, $skiproot ), $m );
		}
	}
	// Fix placeholders (oneliners)
	// <field name="MyField"/>
	if ( preg_match_all ( '/\<field[^>]*?\/\>/i', $m, $matches ) )
	{
		foreach ( $matches[0] as $n )
		{
			$group = $type = $value = $name = $replace = '';
			if ( preg_match ( '/name\=\"([^"]*?)\"/i', $n, $nm ) )
			{
				$name = $nm[1];
			}
			if ( preg_match ( '/replace\=\"([^"]*?)\"/i', $n, $nm ) )
			{
				$replace = $nm;
			}
			if ( !isset ( $parsedFields[$name] ) && isset ( $page->$name ) )
			{
				$p = isset ( $page->{"_replacement_{$name}"} ) ? "_replacement_{$name}" : $name;
				$block = $page->$p;
				if ( isset ( $replace ) && strlen ( $replace[1] ) )
				{
					if ( $replacements = explode ( ';', $replace[1] ) )
					{
						foreach ( $replacements as $repl )
						{
							if ( $repl = explode ( '=', $repl ) )
							{
								$block = str_replace ( trim ( $repl[0] ), trim ( $repl[1] ), $block );
							}
						}
					}
					$block = str_replace ( $nm[0], '', $block );
				}
				$m = str_replace ( $n, $block, $m );
				continue;
			}
			if ( preg_match ( '/group\=\"([^"]*?)\"/i', $n, $nm ) )
			{
				$group = $nm[1];
			}
			if ( preg_match ( '/type\=\"([^"]*?)\"/i', $n, $nm ) )
			{
				$type = $nm[1];
			}
			if ( preg_match ( '/value\=\"([^"]*?)\"/i', $n, $nm ) )
			{
				$value = $nm[1];
			}
			if ( !isset ( $parsedFields[$name] ) && file_exists ( $value ) && $f = getimagesize ( $value ) )
			{
				if ( in_array ( $f[2], array ( 1, 2, 3, 4 ) ) )
				{
					$m = str_replace ( $n, '<div id="' . $name . '"><img src="' . $value . '" width="' . $f[0] . '" height="' . $f[1] . '" alt="image"/></div>', $m );
				}
			}
			else if ( isset ( $parsedFields[$name] ) )
			{
				$m = str_replace ( $n, '', $m );
			}
			$m = str_replace ( $n, '<div id="' . $name . '"></div>', $m );
		}
	}
	
	// Fix placeholders (multiline)
	if ( preg_match_all ( '/\<field[^>]*?\>[\w\W]*?\<\/field\>/i', $m, $matches ) )
	{
		foreach ( $matches[0] as $n )
		{
			$group = $type = $value = $name = '';
			if ( preg_match ( '/\<field[^>]*?\>/i', $n, $f ) )
			{
				if ( preg_match ( '/name\=\"([^"]*?)\"/i', $f[0], $nm ) )
				{
					$name = $nm[1];
				}
				if ( preg_match ( '/group\=\"([^"]*?)\"/i', $f[0], $nm ) )
				{
					$group = $nm[1];
				}
				if ( preg_match ( '/type\=\"([^"]*?)\"/i', $f[0], $nm ) )
				{
					$type = $nm[1];
				}
				if ( preg_match ( '/\<field[^>]*?\>([\w\W]*?)\<\/field\>/i', $n, $nm ) )
				{
					$value = $nm[1];
				}
				if ( !isset ( $parsedFields[$name] ) )
				{
					$m = str_replace ( $n, '<div id="' . $name . '">' . $value . '</div>', $m );
				}
				else
				{
					$m = str_replace ( $n, '', $m );
				}
			}
			$m = str_replace ( $n, '<div id="' . $name . '"></div>', $m );
		}
	}
	
	// Add resources
	if ( count ( $document->sHeadData ) )
	{
		$s = '';
		$i = 0;
		
		$combinedCSS = array();
		$combinedJS  = array();
		
		foreach ( $document->sHeadData as $sd )
		{
			if( defined( 'COMBINE_RESOURCES' ) )
			{
				if( preg_match( '/\<link rel=\"stylesheet\".*?href\=\"([^"]*?)\"/i', $sd, $matches ) )
					$combinedCSS[] = $matches[1];
				if( preg_match( '/\<script.*?src\=\"([^"]*?)\"/i', $sd, $matches ) )
					$combinedJS[]  = $matches[1];
			}
			else 
			{
				if ( $i++ > 0 )
					$s .= "\n\t";
				$s .= "\t" . trim ( $sd );
			}
		}
		
		// Combine the CSS
		if( count( $combinedCSS ) )
		{
			$s .= "\t" . '<link rel="stylesheet" href="lib/resources.php?files=';
			$s .= implode( ',', $combinedCSS );
			$s .= '"/>' . "\n";
		}
		
		// Combine the javascript
		if( count( $combinedJS ) )
		{
			$s .= "\t" . '<script src="lib/resources.php?files=';
			$s .= implode( ',', $combinedJS );
			$s .= '"></script>' . "\n";
		}
		
		$m = str_replace ( '</head>', $s . "\n\t" . '</head>', $m );
	}
	// Meta / titles etc
	$m = str_replace ( '<title></title>', '<title>' . SITE_ID . ' - ' . $page->Title . '</title>' . "\n" . "\t\t<base href=\"" . BASE_URL . "\"/>", $m );
	
	// Create "wrapper" template that inherits from document
	$t = new cPTemplate (); 
	$t->_bodyClasses = $document->_bodyClasses;
	$t->_template = $m;
	
	// Strip all css and apply new engine
	$html = $t->render();
	if( !isset( $_REQUEST['debug'] ) )
	{
		$theme = ( defined( 'NODE_THEME' ) && !isset( $_SESSION['theme'] ) ? NODE_THEME : $_SESSION['theme'] );
		
		$override = ( isset( $_SESSION['theme_path_override'] ) ? $_SESSION['theme_path_override'] : false );
		
		// Apply new engine
		if( $theme && file_exists( 'subether/themes/' . $theme ) )
		{
			$html = preg_replace( 
				array(
					'/\<link rel=\"stylesheet\"[^>]*?\>[\s]+/',
					'/\ style\=\"[^"]\"/i', 
					'/\<style[^>]*?\>[\w\W]*?\<\/style\>[\s]+/i'
				),
				'', 
				$html 
			);
			
			$link = "\t".'<link rel="stylesheet" href="'.BASE_URL.'template-css/subether/themes/'.$theme.'/theme.css'.( $override ? ';'.$override.'theme.css' : '' ).'"/>';
			$html = str_replace( '</head>', $link . "\n\t</head>", $html );
		}
	}
	
	// Output
	// Augment HTML body
	if( isset( $document->_bodyClasses ) )
	{
		$var = ' ' . $document->_bodyClasses;
		$html = preg_replace( '/\<body[\s]+class\=\"([^"]*?)\"/i', '<body class="$1' . $var . '"', $html );
	}
	
	if( defined( 'IMAGE_HOSTS' ) )
	{
		if( $hosts = explode( '|', IMAGE_HOSTS ) )
		{
			// Catch
			$html = str_replace( BASE_URL . 'subether/upload/', '!!zubether/', $html );
			$html = str_replace( BASE_URL . 'upload/',          '!!zpload/', $html );
			$html = str_replace( 'subether/upload/',            '!!zubether/', $html );
			$html = str_replace( 'upload/',                     '!!zpload/', $html );
			
			// Finally
			$html = str_replace( '!!zpload/', $hosts[0] . 'upload/', $html );
			$html = str_replace( '!!zubether/', $hosts[0] . 'subether/upload/', $html );
		}
	}
	//die( print_r( $t,1 ) . ' [] ' );
	die ( $html );
}
else
{
	ArenaDie ( 'Error with module! Please check your index.html template!' );
}

?>
