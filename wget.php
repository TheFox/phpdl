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
	if(!$fileId)
		exit();
	
	$packetDownloadDir = '';
	if(isset($argv[2]))
		$packetDownloadDir = $argv[2];
	if($packetDownloadDir == '')
		$packetDownloadDir = '.';
	
	$speed = 0;
	if(isset($argv[3]))
		$speed = (int)$argv[3];
	
	printd("file id: $fileId\n");
	printd("download dir: '$packetDownloadDir'\n");
	printd("speed: $speed\n");
	
	$dbh = dbConnect();
	$file = new dlfile($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
	$hosters = getDbTable($dbh, 'hosters');
	dbClose($dbh);
	
	if($file->loadById($fileId)){
		printd("uri: '".$file->get('uri')."'\n");
		
		$thisHoster = null;
		foreach($hosters as $id => $hostersThisHoster)
			if(preg_match('/'.$hostersThisHoster['searchPattern'].'/i', $file->get('uri'))){
				$thisHoster = $hostersThisHoster;
				break;
			}
		
		
		if(!$thisHoster){
			printd("default hoster matched\n");
			$thisHoster = array(
				'id' => 0,
				'name' => 'Default',
				'phpPath' => 'hoster.default.php',
				'searchPattern' => '.*',
				'ssl' => 0,
				'user' => '',
				'password' => '',
				'ctime' => 1291639243,
			);
		}
		
		$libThisHosterPath = './lib/hoster/'.$thisHoster['phpPath'];
		if(file_exists($libThisHosterPath)){
			include_once($libThisHosterPath);
			printd("hoster plugin loaded: $libThisHosterPath\n");
			
			if(function_exists('hosterExec')){
				if(preg_match('/^http:/', $file->get('uri'))){
					
					$file->set('error', $DLFILE_ERROR['ERROR_NO_ERROR']);
					$file->set('stime', mktime());
					$file->set('ftime', 0);
					$file->save();
					
					printd("hoster plugin: hosterExec()\n");
					$filePath = hosterExec($file, $thisHoster, $packetDownloadDir, $speed);
					printd("hoster plugin: hosterExec() done: '$filePath'\n");
					
					$error = $DLFILE_ERROR['ERROR_NO_ERROR'];
					$size = 0;
					
					if(is_numeric($filePath))
						$error = $filePath;
					elseif($filePath == '')
						$error = $DLFILE_ERROR['ERROR_DOWNLOAD_FAILED'];
					elseif(!($size = filesize($filePath)))
						$error = $DLFILE_ERROR['ERROR_FILE_SIZE_IS_NULL'];
					
					if($size)
						trafficUpdate($file->getDbh(), date('Y-m-d'), $size);
					
					if($error){
						if(file_exists($filePath) && $filePath != '')
							unlink($filePath);
						printd("file failed: ".getDlFileErrorMsg($error)."\n");
					}
					else
						printd("file ok: $size byte\n");
					
					
					$file->set('error', $error);
					$file->set('size', $size);
					
				}
			}
			else
				printd("ERROR: hoster plugin: no hosterExec() function\n");
		}
		else
			printd("ERROR: plugin not found: $libThisHosterPath\n");
		
		
		if(!$file->get('stime'))
			$file->set('stime', mktime());
		$file->set('ftime', mktime());
		$file->save();
	}
	else
		printd("ERROR: file not found\n");
	
	printd("exit\n");
}

?>