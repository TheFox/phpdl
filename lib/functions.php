<?php

/*
	Created @ 04.04.2010 by TheFox@fox21.at
	Version: 1
	Copyright (c) 2010 TheFox
	Copy from FCMS/stdlib.php, 16.10.2010.
	
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

// Smarty >= 3.0b8
include_once('smarty/smarty.php');

if(file_exists('./config.php'))
	include_once('./config.php');

// Vars.
$YEAR = date('Y');

// Functions.
function ve($v){
	print '<pre>';
	var_export($v);
	print '</pre>';
}

function error($text, $fatal = false, $level = 0){
	
	$dbg = debug_backtrace();
	
	print "<b><pre>".($fatal ? 'FATAL ' : '')."ERROR in '".basename($dbg[$level]['file'])."' on line ".$dbg[$level]['line'].": '$text'</pre></b>";
	flush();
	if($fatal)
		exit(1);
}

function smartyNew(){
	global $CONFIG;
	
	$smarty = new Smarty();
	$smarty->debugging = $CONFIG['SMARTY_DEBUG'];
	$smarty->caching = $CONFIG['SMARTY_CACHING'];
	$smarty->cache_lifetime = $CONFIG['SMARTY_CACHE_LIFETIME'];
	$smarty->template_dir = $CONFIG['SMARTY_TEMPLATE_DIR'];
	$smarty->compile_dir = $CONFIG['SMARTY_COMPILE_DIR'];
	$smarty->cache_dir = $CONFIG['SMARTY_CACHE_DIR'];
	
	return $smarty;
}

function smartyAssignStd(&$smarty, $siteTitleSuffix = ''){
	global $CONFIG, $CONFIG_STYLE;
	
	$smarty->assign('siteHost', $CONFIG['SITE_HOST']);
	$smarty->assign('siteHostBasedir', $CONFIG['SITE_HOST_BASEDIR']);
	$smarty->assign('siteName', $CONFIG['SITE_NAME']);
	$smarty->assign('siteNameHtml', $CONFIG['SITE_NAME_HTML']);
	$smarty->assign('siteTitle', $CONFIG['SITE_TITLE'].($siteTitleSuffix == '' ? '' : ' - '.$siteTitleSuffix));
	$smarty->assign('siteAuthor', $CONFIG['SITE_AUTHOR']);
	$smarty->assign('siteCopyright', $CONFIG['SITE_COPYRIGHT']);
	$smarty->assign('siteContact', $CONFIG['SITE_CONTACT']);
	$smarty->assign('siteContactSite', $CONFIG['SITE_CONTACT_SITE']);
	#$smarty->assign('siteGenerator', $CONFIG['SITE_GENERATOR']);
	$smarty->assign('siteGenerator', $smarty->_version);
	$smarty->assign('siteDescription', $CONFIG['SITE_DESCRIPTION']);
	$smarty->assign('siteKeywords', $CONFIG['SITE_KEYWORDS']);
	$smarty->assign('siteRevisitafter', $CONFIG['SITE_REVISITAFTER']);
	$smarty->assign('siteOnlinesince', $CONFIG['SITE_ONLINESINCE']);
	$smarty->assign('siteStyle', $CONFIG['SITE_STYLE']);
	$smarty->assign('siteStyleDir', $CONFIG['SITE_STYLE_DIR'].'/'.$CONFIG['SITE_STYLE']);
	$smarty->assign('siteStyleTplDir', $CONFIG['SITE_STYLE_DIR'].'/'.$CONFIG['SITE_STYLE'].'/tpl');
	$smarty->assign('siteStyleTrA', $CONFIG_STYLE['SITE_STYLE_TABLE_TR_A']);
	$smarty->assign('siteStyleTrB', $CONFIG_STYLE['SITE_STYLE_TABLE_TR_B']);
	$smarty->assign('siteImgDir', $CONFIG['SITE_STYLE_DIR'].'/'.$CONFIG['SITE_STYLE'].'/img');
	$smarty->assign('siteFavicon', $CONFIG['SITE_FAVICON']);
	$smarty->assign('siteForward', $CONFIG['SITE_FORWARD']);
	
	$smarty->assign('smartyCache', $CONFIG['SMARTY_CACHING'] ? 'true' : 'false'); # as string!
	$smarty->assign('smartyCacheLifetime', (int)$smarty->cache_lifetime); # $CONFIG['SMARTY_CACHE_LIFETIME']
	
	$smarty->assign('date', date($CONFIG['DATE_FORMAT']));
	$smarty->assign('dateYear', date('Y'));
	$smarty->assign('dateLong', date($CONFIG['DATE_FORMAT_LONG']));
	$smarty->assign('dateNextupdate', date($CONFIG['DATE_FORMAT_LONG'], mktime() + $smarty->cache_lifetime)); # $CONFIG['SMARTY_CACHE_LIFETIME']
	$smarty->assign('dateRFC2822', date('r')); // RFC 2822: http://www.faqs.org/rfcs/rfc2822
	$smarty->assign('dateFormat', $CONFIG['DATE_FORMAT']);
	$smarty->assign('dateFormatLong', $CONFIG['DATE_FORMAT_LONG']);
	
	$smarty->assign('phpFullURI', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	
	$smarty->assign('htmlHeadExt', $CONFIG['HTML_HEAD_EXT']);
	
	$smarty->assign('httpReferer', $_SERVER['HTTP_REFERER']);
	$smarty->assign('phpdlVersion', $CONFIG['PHPDL_VERSION']);
	
}

function smartyAssignMenu(&$smarty, $user){
	global $CONFIG, $CONFIG_STYLE;
	if($user->get('superuser'))
		$smarty->assign('superuserMenu', '<a href="?a=superuser">Superuser</a><br />');
}

function dbConnect(){
	global $CONFIG;
	
	$dbh = @mysql_connect($CONFIG['DB_HOST'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
	if(!$dbh)
		error('no connection to database', true);
	
	$sel = @mysql_select_db($CONFIG['DB_NAME'], $dbh);
	if(!$sel)
		error('can\'t select database '.$CONFIG['DB_NAME'], true);
	
	return $dbh;
}

function dbClose($dbh){
	if($dbh)
		@mysql_close($dbh);
}

function getDbTable($dbh, $table, $where = ''){
	$rv = array();
	$sql = "select * from $table $where;";
	$res = mysql_query($sql, $dbh);
	if($res)
		while($row = mysql_fetch_assoc($res))
			$rv[$row['id']] = $row;
	else
		error("mysql_query error: '$sql'", true, 1);
	
	return $rv;
}

function getDateFromTs($ts = 0){
	global $CONFIG;
	
	$CONFIG['DATE_FORMAT'] = str_replace('"', '', $CONFIG['DATE_FORMAT']);
	$CONFIG['DATE_FORMAT'] = str_replace("'", '', $CONFIG['DATE_FORMAT']);
	
	return date($CONFIG['DATE_FORMAT'], $ts);
}

function getDateLongFromTs($ts = 0){
	global $CONFIG;
	
	$CONFIG['DATE_FORMAT_LONG'] = str_replace('"', '', $CONFIG['DATE_FORMAT_LONG']);
	$CONFIG['DATE_FORMAT_LONG'] = str_replace("'", '', $CONFIG['DATE_FORMAT_LONG']);
	
	return date($CONFIG['DATE_FORMAT_LONG'], $ts);
}

function checkInput($val, $pattern, $len = null){
	if($pattern !== null)
		$val = preg_replace('/[^'.$pattern.']*/', '', $val);
	if($len !== null)
		$val = substr($val, 0, $len);
	return $val;
}

function wget($url, $filePath = null, $speed = null, $httpUser = null, $httpPassword = null){
	$rv = null;
	$fh = null;
	
	if($filePath)
		$fh = fopen($filePath, 'w');
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i686; de; rv:1.9.2.10) Gecko/20100914 Firefox/3.6.10');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
	
	if($filePath){
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_FILE, $fh);
	}
	else{
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	}
	if($httpUser && $httpPassword)
		curl_setopt($ch, CURLOPT_USERPWD, "$httpUser:$httpPassword");
	
	$rv = curl_exec($ch);
	curl_close($ch);
	
	if($filePath)
		fclose($fh);
	
	return $rv;
}

function wgetHeaderSize($url, $httpUser = null, $httpPassword = null){
	$rv = null;
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i686; de; rv:1.9.2.10) Gecko/20100914 Firefox/3.6.10');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	
	if($httpUser && $httpPassword)
		curl_setopt($ch, CURLOPT_USERPWD, "$httpUser:$httpPassword");
	
	$header = curl_exec($ch);
	curl_close($ch);
	
	if(preg_match('/Content-Length: ?(\d+)/i', $header, $res))
		$rv = (int)$res[1];
	
	return $rv;
}

function hex2bin($hexstr) {
	$hexstr = str_replace(' ', '', $hexstr);
	$retstr = @pack('H*', $hexstr);
	return $retstr;
}

function mkpasswd($salt, $plainpw){
	return md5('TheFox'.$salt.$plainpw);
}

function scheduler($dbh){
	
	/*
		Return values:
			>  0	Matched a scheduler entry. Download active.
			<  0	Matched a scheduler entry. Download inactive.
			== 0	Machted no scheduler entry. Download inactive.
	*/
	
	$res = mysql_query("select * from scheduler where active = '1' order by sortnr;", $dbh);
	while($sched = mysql_fetch_assoc($res)){
		$active = false;
		
		$activeDayTimeBegin = mktime(0, 0, 0, date('n'), date('j'), date('Y')) + $sched['activeDayTimeBegin'];
		$activeDayTimeEnd = mktime(0, 0, 0, date('n'), date('j'), date('Y')) + $sched['activeDayTimeEnd'];
		
		if(mktime() >= $activeDayTimeBegin && mktime() <= $activeDayTimeEnd)
			$active = true;
		
		$active = $sched['activeDayTimeInvert'] ? !$active : $active;
		
		if($active)
			return($sched['download'] ? $sched['id'] : -$sched['id']);
	}
	
	return 0;
}

function filesDownloading($dbh){
	$retval = 0;
	
	$res = mysql_query("select id from packets where archive = '0';", $dbh);
	while($row = mysql_fetch_assoc($res)){
		$res2 = mysql_fetch_assoc(mysql_query("select count(id) c from files where _packet = '".$row['id']."' and stime != '0' and ftime = '0';", $dbh));
		$retval += $res2['c'];
	}
	
	return $retval;
}

function fileWrite($path, $content){
	if($fh = fopen($path, 'w')){
		fwrite($fh, $content);
		fclose($fh);
		
		return filesize($path);
	}
	return 0;
}

function printd($text = ''){
	list($usec, $sec) = explode(' ', microtime());
	printf("%s.%03d %5d %s", date('Y/m/d H:i:s'), (int)($usec * 1000), posix_getpid(), $text);
}

function rmdirr($dir){
	if(is_dir($dir) && $dir != '.'){
		$objects = scandir($dir);
		foreach($objects as $object)
			if($object != '.' && $object != '..'){
				if(filetype($dir.'/'.$object) == 'dir')
					rmdirr($dir.'/'.$object);
				else
					unlink($dir.'/'.$object);
			}
		reset($objects);
		rmdir($dir);
	}
}

function getPacketFilename($id, $name){
	$path = $id.'.'.$name;
	$path = str_replace(' ', '.', $path);
	$path = strtolower($path);
	return $path;
}

function getIecBinPrefix($byte, $maxlevel = null){
	$prefixes = array('Byte', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
	$prefixesc = count($prefixes);
	if($maxlevel === null || $maxlevel >= $prefixesc)
		$maxlevel = $prefixesc - 1;
	
	for($level = 0; $byte >= 1024 && $level < $maxlevel; $byte /= 1024)
		$level++;
	
	$format = '%.2f %s';
	$rv = sprintf($format, $byte, $prefixes[$level]);
	
	return $rv;
}

function trafficUpdate($dbh, $tday, $byte){
	$res = mysql_fetch_assoc(mysql_query("select id from traffic where tday = '$tday' limit 1;"));
	if(isset($res['id']))
		mysql_query("update traffic set traffic = traffic + ".(int)$byte." where id = ".$res['id']." limit 1;");
	else
		mysql_query("insert into traffic(tday, traffic, ctime) values ('$tday', '".(int)$byte."', '".mktime()."');");
}

?>