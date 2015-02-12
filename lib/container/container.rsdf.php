<?php

/* Containers sucks. Really! */

/*
	Created @ 22.10.2010
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
	
	$key = hex2bin($KEY_HEX);
	$iv2 = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, hex2bin($IV_HEX), MCRYPT_MODE_ECB, '0000000000000000');
	
	
	$content = hex2bin($content);
	$links = explode("\r\n", $content);
	
	foreach($links as $link){
		if(strlen($link) != 0){
			$b64 = base64_decode($link);
			$dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $b64, MCRYPT_MODE_CFB, $iv2);
			$dec = substr($dec, strpos($dec, '/files/')); # Ugly solution - lol ;P
			if($dec != '')
				$retval .= "http://rapidshare.com$dec\n";
			
		}
	}
	
	return $retval;
}
