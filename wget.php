<?php

/*
	Created @ 20.10.2010 by TheFox@fox21.at
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

define('ANTIHACK', 1);
if(isset($_SERVER['SERVER_ADDR'])) die('Hacking attempt.');

include_once('./lib/config.php');
include_once('./lib/functions.php');
include_once('./lib/class.dlfile.php');


if(count($argv) >= 2){
	
	print "\n";
	printd("start\n");
	
	$fileId = (int)$argv[1];
	$packetDownloadDir = '';
	
	if(!$fileId)
		exit();
	if(isset($argv[2]))
		$packetDownloadDir = $argv[2];
	
	if($packetDownloadDir == '')
		$packetDownloadDir = '.';
	
	printd("file id: $fileId\n");
	printd("download dir: '$packetDownloadDir'\n");
	
	$dbh = dbConnect();
	$file = new dlfile($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
	$hosters = getDbTable($dbh, 'hosters');
	dbClose($dbh);
	
	if($file->loadById($fileId)){
		#$file = $files[$fileId];
		printd("uri: '".$file->get('uri')."'\n");
		
		$thisHoster = null;
		foreach($hosters as $id => $thisHoster)
			if(preg_match('/'.$thisHoster['searchPattern'].'/i', $file->get('uri')))
				break;
		
		if($thisHoster){
			printd("hoster found\n");
			$libThisHosterPath = './lib/hoster/'.$thisHoster['phpPath'];
			if(file_exists($libThisHosterPath)){
				include_once($libThisHosterPath);
				printd("hoster plugin loaded: $libThisHosterPath\n");
				
				if(function_exists('hosterExec')){
					if(preg_match('/^http:/', $file->get('uri'))){
						printd("hoster plugin: hosterExec()\n");
						$filePath = hosterExec($file, $thisHoster, $packetDownloadDir);
						printd("hoster plugin: hosterExec() done: $filePath\n");
					}
				}
				else
					printd("ERROR: hoster plugin: no hosterExec() function\n");
			}
			else
				printd("ERROR: plugin not found: $libThisHosterPath\n");
		}
		else
			printd("ERROR: no hoster found\n");
	}
	else
		printd("ERROR: file not found\n");
	
	printd("exit\n");
}

?>