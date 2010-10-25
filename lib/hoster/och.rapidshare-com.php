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

if(!defined('ANTIHACK')) die('Hacking attempt.');


function ochExec($uri, $thisOch){
	global $CONFIG;
	$retval = false;
	
	print "och.rapidshare-com.php ochExec '$uri'\n";
	if(preg_match('/files\/([^\/]*)\/(.*)/i', $uri, $res)){
		#var_export($res);
		$fileid = $res[1];
		$filename = $res[2];
		
		$protocol = 'http'.($thisOch['ssl'] ? 's' : '');
		$path = "cgi-bin/rsapi.cgi?sub=download_v1&fileid=$fileid&filename=$filename&login=".$thisOch['user']."&password=".$thisOch['password'];
		$url = "$protocol://api.rapidshare.com/$path";
		
		print "link '$url'\n";
		$tmp = './tmp/'.$filename.'.tmp';
		wget($CONFIG['WGET'], $url, $tmp);
		if(preg_match('/DL:([^,]*),/', file_get_contents($tmp), $res)){
			$url = "$protocol://".$res[1]."/$path";
			print "link '$url'\n";
			wget($CONFIG['WGET'], $url, './downloads/.'.$filename);
			if(file_exists('./downloads/.'.$filename))
				rename('./downloads/.'.$filename, './downloads/'.$filename);
		}
		unlink($tmp);
	}
	
	return $retval;
}

?>