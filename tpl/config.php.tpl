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


include_once('stdconfig.php');


$EXTCONFIG = array(
	
	'PHPDL_INSTALLED' => {$PHPDL_INSTALLED},
	
	'DB_HOST' => '{$DB_HOST}',
	'DB_NAME' => '{$DB_NAME}',
	'DB_USER' => '{$DB_USER}',
	'DB_PASS' => '{$DB_PASS}',
	
	'USER_PASSWORD_SALT' => '{$USER_PASSWORD_SALT}',
	'USER_SESSION_TTL' => {$USER_SESSION_TTL},
	
	'DL_SLOTS' => 1,
	
	'SITE_STYLE' => 'style1',
	
	'DATE_FORMAT' => 'd.m.Y H:i',
	'DATE_FORMAT_LONG' => 'd.m.Y H:i:s T',
	
	'WGET' => '{$WGET}',
	'PS' => '{$PS}',
	
);

# merge $EXTCONFIG with $CONFIG
$CONFIG = array_merge($CONFIG, $EXTCONFIG);

# Extended config
$CONFIG['SITE_STYLE_INCLUDE'] = $CONFIG['SITE_STYLE_DIR'].'/'.$CONFIG['SITE_STYLE'].'/inc/include.php';

if(file_exists($CONFIG['SITE_STYLE_INCLUDE']))
	include_once($CONFIG['SITE_STYLE_INCLUDE']);
else
	die("FATAL ERROR in ".basename(__FILE__)." line ".__LINE__.": can't include ".$CONFIG['SITE_STYLE_INCLUDE']);

?>