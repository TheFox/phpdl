<?php

/* Created @ 04.04.2010 by TheFox */

if(!defined('ANTIHACK')) die('Hacking attempt.');


$CONFIG = array(
	
	'DB_HOST' => 'localhost',
	'DB_NAME' => 'phpdl',
	'DB_USER' => 'phpdl',
	'DB_PASS' => '7NuW5RCtuGDEazHz',
	
	'SITE_HOST' => '',
	'SITE_NAME' => 'PHP Downloader',
	'SITE_NAME_HTML' => '',
	'SITE_TITLE' => 'PHP Downloader',
	'SITE_AUTHOR' => 'TheFox',
	'SITE_COPYRIGHT' => 'Copyright (C) 2010 FOX21.at',
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
	
	'WGET' => '/usr/bin/wget',
	
);

# Extended config
$CONFIG['SITE_STYLE_INCLUDE'] = $CONFIG['SITE_STYLE_DIR'].'/'.$CONFIG['SITE_STYLE'].'/inc/include.php';



if(file_exists($CONFIG['SITE_STYLE_INCLUDE']))
	include_once($CONFIG['SITE_STYLE_INCLUDE']);
else
	die("FATAL ERROR in ".basename(__FILE__)." line ".__LINE__.": can't include ".$CONFIG['SITE_STYLE_INCLUDE']);

?>