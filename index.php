<?php

/*
	Created @ 15.10.2010 by TheFox@fox21.at
	Version: 0.1.0
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

define('ANTIHACK', 1);

include_once('./lib/config.php');
include_once('./lib/functions.php');
include_once('./lib/class.user.php');
include_once('./lib/class.dlfile.php');


$a = $_GET['a'];
$id = (int)$_GET['id'];
$smarty = smartyNew();

$user = new user($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
$user->loadBySessionId($_COOKIE['userSessionId']);


if($user->isGuest){
	
	// Only for non-loggedin users.
	switch($a){
		
		default:
		case 'login':
			
			$tpl = 'default-guest.tpl';
			$cacheId = 'default-guest';
			if(!$smarty->isCached($tpl, $cacheId)){
				smartyAssignStd($smarty);
				
				
			}
			$smarty->display($tpl, $cacheId);
			
		break;
		
		case 'loginExec':
			/*$dbh = dbConnect();
			dbClose($dbh);*/
			
			$login = checkInput($_POST['login'], 'a-z0-9_', 32);
			$password = md5($CONFIG['USER_PASSWORD_SALT'].$_POST['password']);
			
			
			$loginuser = new user($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
			if($loginuser->loadByLoginAndPassword($login, $password))
				setcookie('userSessionId', $loginuser->get('sessionId'), mktime() + $CONFIG['USER_SESSION_TTL']);
			else
				setcookie('userSessionId', 'x', mktime());
			
			#print "$login|$password|".$loginuser->get('sessionId')."|".(mktime() + $CONFIG['USER_SESSION_TTL']);
			
			header('Location: ?');
			
		break;
		
	}
}
else{
	
	
	switch($a){
		
		default:
			
			$tpl = 'default.tpl';
			$cacheId = 'default';
			if(!$smarty->isCached($tpl, $cacheId)){
				smartyAssignStd($smarty);
				
				
				$dbh = dbConnect();
				$users = getDbTable($dbh, 'users');
				#$packets = getDbTable($dbh, 'packets', "where _user = '".$user->get('id')."'");
				$packets = getDbTable($dbh, 'packets', "where archive = '0'");
				
				
				$stack = '';
				foreach($packets as $packetId => $packet){
					
					$res = mysql_fetch_assoc(mysql_query("select count(id) c from `files` where _packet = ".$packet['id']." and error != '".$DLFILE_ERROR_NO_ERROR."'", $dbh));
					$errors = $res['c'];
					
					$class = '';
					if($errors)
						$class = 'packetHasError';
					elseif($packet['stime'] && !$packet['ftime'])
						$class = 'packetIsDownloading';
					
					$stack .= '
						<tr>
							<td class="'.$class.'">'.$packet['id'].'</td>
							<td class="'.$class.'">'.$users[$packet['_user']]['login'].'</td>
							<td class="'.$class.'">'.$packet['name'].'</td>
							<td class="'.$class.'">'.date($CONFIG['DATE_FORMAT'], $packet['ctime']).'</td>
							<td class="'.$class.'">'.($packet['stime'] ? date($CONFIG['DATE_FORMAT'], $packet['stime']) : 'waiting').'</td>
							<td class="'.$class.'">'.($packet['ftime'] ? date($CONFIG['DATE_FORMAT'], $packet['ftime']) : ($packet['stime'] ? 'downloading' : '&nbsp;')).'</td>
							<td class="'.$class.'">'.($packet['md5Verified'] ? 'y' : '&nbsp;').'</td>
						</tr>
					';
				}
				$smarty->assign('stack', $stack);
				
				dbClose($dbh);
				
			}
			$smarty->display($tpl, $cacheId);
			
		break;
		
		case 'dlpacketEdit':
			
			$tpl = $a.'.tpl';
			$cacheId = $a;
			if(!$smarty->isCached($tpl, $cacheId)){
				smartyAssignStd($smarty);
				
				$dbh = dbConnect();
				dbClose($dbh);
				
				$filesOut = '';
				if($id){
					// Edit
				}
				else{
					// Add new
				}
				
				
				$smarty->assign('id', $id);
				$smarty->assign('files', $filesOut);
				
			}
			$smarty->display($tpl, $cacheId);
			
			
		break;
		
		case 'dlpacketEditSave':
			
			$name = checkInput($_POST['name'], 'a-zA-Z0-9._-', 256);
			$urlsstr = $_POST['urls'];
			$urlsstr = str_replace("\r", '', $urlsstr);
			
			$urls =  array();
			foreach(preg_split("/\n/s", $urlsstr) as $url)
				if($url != '')
					$urls[] = urlencode($url);
			
			$dbh = dbConnect();
			if($id){
				// Edit
			}
			else{
				// Add new
				mysql_query("insert into packets(_user, name, ctime) values ('".$user->get('id')."', '$name', '".mktime()."');", $dbh);
				$pid = mysql_insert_id($dbh);
				foreach($urls as $url)
					#print "insert into files(_user, _packet, uri, ctime) values ('".$user->get('id')."', '$pid', '$url', '".mktime()."');<br>";
					mysql_query("insert into files(_user, _packet, uri, ctime) values ('".$user->get('id')."', '$pid', '$url', '".mktime()."');", $dbh);
			}
			dbClose($dbh);
			
			//header('Location: ?');
			
		break;
		
	}
	
}

?>