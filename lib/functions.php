<?php

/*
	Created @ 04.04.2010 by TheFox@fox21.at
	Version: 1
	Copyright (c) 2010 TheFox
	Copy from FCMS/stdlib.php, 16.10.2010.
	
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

// Smarty >= 3.0b8
include_once('smarty/Smarty-3.0rc4/Smarty.class.php');

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
	
	$smarty = new Smarty;
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
	
}

function smartyAssignMenu(&$smarty, $current){
	global $CONFIG, $CONFIG_STYLE;
	if(isset($CONFIG_STYLE['SITE_STYLE_CURRENTMENUITEM']))
		foreach($CONFIG['SITE_MENU'] as $item){
			if($item == $current)
				$smarty->assign('menueCurrentItemClass'.$item, $CONFIG_STYLE['SITE_STYLE_CURRENTMENUITEM']);
			else
				$smarty->assign('menueCurrentItemClass'.$item, '');
		}
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

function dbClose(){
	mysql_close();
}

function getDbTable($dbh, $table, $where = ''){
	$rv = array();
	$sql = "select * from $table $where;";
	$res = mysql_query($sql, $dbh);
	if($res){
		while($row = mysql_fetch_assoc($res))
			$rv[$row['id']] = $row;
	}
	else
		error("mysqli_query error: '$sql'", true, 1);
	
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

function checkInput($val, $pattern, $len = -1){
	$val = preg_replace('/[^'.$pattern.']*/', '', $val);
	if($len != -1)
		$val = substr($val, 0, $len);
	return $val;
}

?>