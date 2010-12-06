<?php

/*
	Created @ 06.12.2010 by TheFox@fox21.at
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


function hosterExec($file, $thisHoster, $loadingDir, $speed = 0){
	global $CONFIG;
	$retval = '';
	
	$url = $file->get('uri');
	$filename = basename($url);
	print "hoster.default.php hosterExec '$url'\n";
			
	$tmpfile = $loadingDir.'/.'.$filename;
	wget($CONFIG['WGET'], $url, $tmpfile, $speed);
	$error = 0;
	
	if(file_exists($tmpfile)){
		$size = filesize($tmpfile);
		$newfilePath = $loadingDir.'/'.$filename;
		rename($tmpfile, $newfilePath);
		if(file_exists($newfilePath))
			$retval = $newfilePath;
		if($error)
			$retval = (int)$error;
	}
	
	return $retval;
}

?>