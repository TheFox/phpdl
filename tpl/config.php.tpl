<?php

/*
	Created @ 04.04.2010 by TheFox@fox21.at
	Copyright (c) 2010 TheFox
	
	This file is part of PHPDL.
	
	PHPDL is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	PHPDL is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with PHPDL.  If not, see <http://www.gnu.org/licenses/>.
*/

if(!defined('ANTIHACK')) die('Hacking attempt.');


$CONFIG = array(
	
	'PHPDL_VERSION' => '0.2.0',
	'PHPDL_INSTALLED' => {$PHPDL_INSTALLED},
	
	'DB_HOST' => '{$DB_HOST}',
	'DB_NAME' => '{$DB_NAME}',
	'DB_USER' => '{$DB_USER}',
	'DB_PASS' => '{$DB_PASS}',
	
	'USER_PASSWORD_SALT' => '{$USER_PASSWORD_SALT}',
	'USER_SESSION_TTL' => {$USER_SESSION_TTL},
	
	'DL_SLOTS' => 1,
	
	
	'SITE_HOST' => '',
	'SITE_NAME' => 'PHP Downloader',
	'SITE_NAME_HTML' => '',
	'SITE_TITLE' => 'PHP Downloader',
	'SITE_AUTHOR' => 'TheFox',
	'SITE_COPYRIGHT' => 'Copyright (C) 2010 TheFox',
	'SITE_CONTACT' => 'phpdl (AT) fox21 at',
	'SITE_CONTACT_SITE' => 'http://fox21.at/contact',
	'SITE_DESCRIPTION' => 'PHP Downloader',
	'SITE_KEYWORDS' => 'PHP, Downloader',
	'SITE_REVISITAFTER' => '1 days',
	'SITE_ONLINESINCE' => '2010',
	'SITE_MENU' => array(),
	'SITE_STYLE_DIR' => 'styles',
	'SITE_STYLE' => 'style1',
	'SITE_FAVICON' => 'http://img.fox21.at/favicon.ico',
	'SITE_FORWARD' => '',
	
	'SMARTY_DEBUG' => false,
	'SMARTY_CACHING' => false,
	'SMARTY_CACHE_LIFETIME' => 900,
	'SMARTY_TEMPLATE_DIR' => 'tpl',
	'SMARTY_COMPILE_DIR' => 'cache/tpl_c',
	'SMARTY_CACHE_DIR' => 'cache/html',
	
	'DATE_FORMAT' => 'd.m.Y H:i',
	'DATE_FORMAT_LONG' => 'd.m.Y H:i:s T',
	
	'HTML_HEAD_EXT' => '',
	
	'WGET' => '{$WGET}',
	'PS' => '{$PS}',
	'BROWSER_USERAGENT' => 'Mozilla/5.0 (X11; U; Linux i686; de; rv:1.9.2.10) Gecko/20100914 Firefox/3.6.10',
	
	
	
);

# Extended config
$CONFIG['SITE_STYLE_INCLUDE'] = $CONFIG['SITE_STYLE_DIR'].'/'.$CONFIG['SITE_STYLE'].'/inc/include.php';



if(file_exists($CONFIG['SITE_STYLE_INCLUDE']))
	include_once($CONFIG['SITE_STYLE_INCLUDE']);
else
	die("FATAL ERROR in ".basename(__FILE__)." line ".__LINE__.": can't include ".$CONFIG['SITE_STYLE_INCLUDE']);

?>