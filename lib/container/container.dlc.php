<?php

/* Containers sucks. Really! */

/*
	Created @ 23.10.2010 by TheFox@fox21.at
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


# You have to set up this vars!
$DLC_DESTTYPE = '';
$DLC_KEY1 = '';
$DLC_KEY2 = '';

function containerExec($content){
	
	global $DLC_KEY1, $DLC_KEY2;
	if($DLC_KEY1 == '' || $DLC_KEY2 == '')
		return 'ERROR: You have to set up the variable $DLC_KEY1 and $DLC_KEY2 in the dlc container module.';
	
	$retval = '';
	$TAILLEN = 88;
	$IV = hex2bin('00000000000000000000000000000000');
	
	$content = preg_replace('/[\r\n]+/s', '', $content);
	$tail = substr($content, strlen($content) - $TAILLEN);
	$content = substr($content, 0, strlen($content) - strlen($tail));
	$content = base64_decode($content);
	
	$response = dlcHttpPost($tail);
	
	$responseKey = '';
	if(preg_match('/<rc>(.*)<.rc>/', $response, $res)){
		$responseKey = $res[1];
	}
	if($responseKey == '' || $responseKey == '2YVhzRFdjR2dDQy9JL25aVXFjQ1RPZ')
		return '';
	
	$responseKeyDeb64 = base64_decode($responseKey);
	
	$responseKeyDeb64Decr = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $DLC_KEY1, $responseKeyDeb64, MCRYPT_MODE_ECB, $IV);
	mcrypt_ecb(MCRYPT_LOKI97, $key, $msg, MCRYPT_ENCRYPT); 
	$newkey = xorcrypt($responseKeyDeb64Decr, $DLC_KEY2);
	$newdlc = $newkey.$content;
	
	
	for($dlclen = strlen($content); $dlclen > 0; $dlclen = strlen($content)){
		$rest = $dlclen >= 16 ? 16 : $dlclen;
		$cutold = substr($content, 0, $rest);
		$cutnew = substr($newdlc, 0, $rest);
		$content = substr($content, $rest);
		$newdlc = substr($newdlc, $rest);
		$cutold = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $newkey, $cutold, MCRYPT_MODE_ECB, $IV);;
		$cutold = xorcrypt($cutold, $cutnew);
		$xml .= $cutold;
	}
	
	$xml = base64_decode($xml);
	
	if(preg_match_all('/<url>([^<]*)<.url>/', $xml, $res))
		foreach($res[1] as $id => $link){
			$link = base64_decode($link);
			if($link != 'http://jdownloader.org')
				$retval .= $link."\n";
		}
	
	
	return $retval;
}

function dlcHttpPost($data){
	global $DLC_DESTTYPE;
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, 'http://service.jdownloader.org/dlcrypt/service.php');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "destType=$DLC_DESTTYPE&srcType=dlc&data=$data");
	
	$response = curl_exec($ch);
	
	curl_close($ch);
	
	return $response;
}

function xorcrypt($data, $key){
	$encrypt = '';
	$kc = 0;
	$kl = strlen($key);
	$dl = strlen($data);
	for($i = 0; $i < $dl; $i++){
		$c = substr($data, $i, 1);
		if($kc > $kl - 1)
			$kc = 0;
		$k = substr($key, $kc, 1);
		$encrypt .= chr(ord($c) ^ ord($k));
		$kc++;
	}
	return $encrypt;
}

?>