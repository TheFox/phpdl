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

if(!defined('ANTIHACK')) die('Hacking attempt.');


function hosterExec($thisHoster, $packet, $packetDownloadDir, $file){
	global $CONFIG;
	$retval = '';
	$rapidpro = $thisHoster['user'] != '' && $thisHoster['password'] != '';
	
	printd("hoster.rapidshare-com.php hosterExec\n");
	if(preg_match('/files\/([^\/]*)\/(.*)/i', $file->get('uri'), $res)){
		#var_export($res);
		$fileid = $res[1];
		$filename = $res[2];
		
		$protocol = 'http'.($thisHoster['ssl'] && $rapidpro ? 's' : '');
		$path = "cgi-bin/rsapi.cgi?sub=download_v1&fileid=$fileid&filename=$filename&withmd5hex=1";
		if($rapidpro)
			$path .= "&login=".$thisHoster['user']."&password=".$thisHoster['password'];
		$url = "$protocol://api.rapidshare.com/$path";
		
		
		$tmp = './tmp/'.$filename.'.tmp';
		
		printd("wget '$url'\n");
		wget($url, $tmp, null, null, null, $CONFIG['DL_IP_OUT']);
		
		if(preg_match('/DL:([^,]*),([^,]*),([^,]*),(.*)/', file_get_contents($tmp), $res)){
			$hostname = $res[1];
			$dlauth = $res[2];
			$countdown = (int)$res[3] + 5;
			$md5 = strtolower($res[4]);
			
			printd("host: '$hostname'\n");
			printd("auth: '$dlauth'\n");
			printd("ct: '$countdown'\n");
			printd("md5: '$md5'\n");
			
			$url = "$protocol://".$hostname."/$path";
			if(!$rapidpro){
				$url .= "&dlauth=".$dlauth;
				
				# lol
				# RS docu:
				#	Downloading as a free user: It IS allowed to create tools, 
				#	which download ONE file as a free user from RapidShare.
				#	It is NOT allowed to implement queuing mechanisms.
				
				printd("wait $countdown\n");
				sleep($countdown);
			}
			
			
			
			$file->set('size', wgetHeaderSize($url, null, null, $CONFIG['DL_IP_OUT']));
			$file->set('md5', $md5);
			$file->save();
			
			$tmpfile = $packetDownloadDir.'/.'.$filename;
			
			printd("wget '$url'\n");
			wget($url, $tmpfile, null, null, null, $CONFIG['DL_IP_OUT']);
			
			$error = 0;
			if(file_exists($tmpfile)){
				
				$size = filesize($tmpfile);
				
				if($size <= 10000)
					if(preg_match('/All free download slots are full/s', @file_get_contents($tmpfile)))
						$error = $DLFILE_ERROR['ERROR_NO_FREE_SLUTS'];
				
				if($md5 == strtolower(md5_file($tmpfile)))
					$file->save('md5Verified', 1);
				
				$newfilePath = $packetDownloadDir.'/'.$filename;
				rename($tmpfile, $newfilePath);
				
				if(file_exists($newfilePath))
					$retval = $newfilePath;
			}
			
			
			if($error)
				$retval = (int)$error;
			
		}
		unlink($tmp);
	}
	
	return $retval;
}

?>