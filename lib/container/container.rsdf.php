<?php

/* Containers sucks. Really! */

/*
	Created @ 22.10.2010 by TheFox@fox21.at
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

/*
	If you are looking for a standalone version of this
	module look here: http://github.com/TheFox/rsdf2txt
*/

if(!defined('ANTIHACK')) die('Hacking attempt.');


function containerExec($content){
	$retval = '';
	
	if($content == '')
		return '';
	
	$IV_HEX  = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF';
	$KEY_HEX = '8C35192D964DC3182C6F84F3252239EB4A320D2500000000';
	
	$iv = hex2str($IV_HEX);
	$key = hex2str($KEY_HEX);
	$iv2 = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $iv, MCRYPT_MODE_ECB, '0000000000000000');
	
	
	$content = hex2str($content);
	$links = explode("\r\n", $content);
	$out = '';
	
	foreach($links as $link){
		if(strlen($link) != 0){
			$b64 = base64_decode($link);
			$dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $b64, MCRYPT_MODE_CFB, $iv2);
			$dec = substr($dec, strpos($dec, '/files/')); # Ugly solution - lol ;P
			if($dec != '')
				$out .= "http://rapidshare.com$dec\n";
			
		}
	}
	
	return $retval;
}

?>