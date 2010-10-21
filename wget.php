<?php

/*
	Created @ 20.10.2010 by TheFox@fox21.at
	Copyright (c) 2010 TheFox
	
	This file is part of PHPDL.
	
	PHPDL is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	PHPDL is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with PHPDL.  If not, see <http://www.gnu.org/licenses/>.
*/

define('ANTIHACK', 1);
if(isset($_SERVER['SERVER_ADDR'])) die('Hacking attempt.');

include_once('./lib/config.php');
include_once('./lib/functions.php');


if(count($argv) >= 2){
	
	$fileId = $argv[1];
	print "file id: $fileId\n";
	
	$dbh = dbConnect();
	$files = getDbTable($dbh, 'files', "where id = '$fileId' limit 1");
	$och = getDbTable($dbh, 'och');
	dbClose($dbh);
	
	if(count($files)){
		$file = $files[$fileId];
		print "uri: ".$file['uri']."\n";
		
		$thisOch = null;
		foreach($och as $id => $thisOch)
			if(preg_match('/'.$thisOch['searchPattern'].'/i', $file['uri']))
				break;
		
		if($thisOch){
			include_once('./lib/och/'.$thisOch['phpPath']);
			
			if(function_exists('ochExec'))
				@ochExec($file['uri'], $thisOch);
			
		}
		else
			print "no och found\n";
		
	}
	else
		print "file not found\n";
	
	
}


?>