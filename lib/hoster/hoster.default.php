<?php

/*
	Created @ 06.12.2010
	Copyright (C) 2010 Christian Mayer <http://fox21.at>
	
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
	
	$url = $file->get('uri');
	$filename = basename($url);
	#printd("hoster.default.php hosterExec '$url'\n");
	
	$file->save('size', wgetHeaderSize($url, $packet->get('httpUser'), $packet->get('httpPassword')));
			
	$tmpfile = $packetDownloadDir.'/.'.$filename;
	wget($url, $tmpfile, null, $packet->get('httpUser'), $packet->get('httpPassword'));
	$error = 0;
	
	if(file_exists($tmpfile)){
		$size = filesize($tmpfile);
		$newfilePath = $packetDownloadDir.'/'.$filename;
		rename($tmpfile, $newfilePath);
		if(file_exists($newfilePath))
			$retval = $newfilePath;
		if($error)
			$retval = (int)$error;
	}
	
	return $retval;
}
