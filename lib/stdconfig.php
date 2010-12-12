<?php

/*
	Created @ 07.11.2010 by TheFox@fox21.at
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
	
	'PHPDL_VERSION' => '0.7.0-dev',
	'PHPDL_RELEASEID' => 0,
	'PHPDL_STACK_PIDFILE' => 'tmp/.stack.php.pid',
	'PHPDL_STACK_SDFILE' => 'tmp/.stack.php.sd',
	
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
	'SITE_FAVICON' => 'http://img.fox21.at/favicon.ico',
	
	'SMARTY_DEBUG' => false,
	'SMARTY_CACHING' => false,
	'SMARTY_CACHE_LIFETIME' => 900,
	'SMARTY_TEMPLATE_DIR' => 'tpl',
	'SMARTY_COMPILE_DIR' => 'cache/tpl_c',
	'SMARTY_CACHE_DIR' => 'cache/html',
	
	'HTML_HEAD_EXT' => '',
	
	
);

?>