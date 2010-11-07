<?php

/*
	Created @ 15.10.2010 by TheFox@fox21.at
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


if(file_exists('install')){
	header('Location: install/install.php');
	exit();
}

include_once('./lib/config.php');
include_once('./lib/functions.php');
include_once('./lib/class.user.php');
include_once('./lib/class.dlpacket.php');
include_once('./lib/class.dlfile.php');


$a = $_GET['a']; # action
$sa = checkInput($_GET['sa'], 'a-z0-9', 32); # subaction
$id = (int)$_GET['id'];
$noredirect = (int)$_GET['noredirect'] == 1;
$smarty = smartyNew();

$user = new user($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
$user->loadBySessionId($_COOKIE['userSessionId']);


if($user->isGuest){
	
	// Only for non-loggedin users.
	switch($a){
		
		default:
		case 'login':
			
			$dbh = dbConnect();
			$res = mysql_fetch_assoc(mysql_query("select count(id) c from users;", $dbh));
			$count = $res['c'];
			dbClose($dbh);
			
			if($count)
				$tpl = 'default-guest.tpl';
			else
				$tpl = 'default-guest-superuseradd.tpl';
			
			$cacheId = 'default-guest';
			if(!$smarty->isCached($tpl, $cacheId)){
				smartyAssignStd($smarty);
			}
			$smarty->display($tpl, $cacheId);
			
		break;
		
		case 'loginExec':
			
			$login = checkInput($_POST['login'], 'a-z0-9_', 32);
			$password = mkpasswd($CONFIG['USER_PASSWORD_SALT'], $_POST['password']);
			
			$loginuser = new user($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
			if($loginuser->loadByLoginAndPassword($login, $password))
				setcookie('userSessionId', $loginuser->get('sessionId'), mktime() + $CONFIG['USER_SESSION_TTL']);
			else
				setcookie('userSessionId', 'x', mktime());
			
			#print "$login|$password|".$loginuser->get('sessionId')."|".(mktime() + $CONFIG['USER_SESSION_TTL']);
			
			header('Location: ?');
			
		break;
		
		case 'superuserAddExec':
			
			$dbh = dbConnect();
			$res = mysql_fetch_assoc(mysql_query("select count(id) c from users;", $dbh));
			$count = $res['c'];
			if($count)
				die('Hacking attempt.');
			
			$login = checkInput($_POST['login'], 'a-z0-9_', 32);
			$password = mkpasswd($CONFIG['USER_PASSWORD_SALT'], $_POST['password']);
			
			mysql_query("insert users(login, password, superuser, ctime) values ('$login', '$password', '1', '".mktime()."');");
			
			$tpl = 'default-guest-superuseradd-exec.tpl';
			$cacheId = 'default-guest-superuseradd-exec';
			if(!$smarty->isCached($tpl, $cacheId)){
				smartyAssignStd($smarty);
				
				$smarty->assign('status', '<b><font color="#009900">User added.</font></b> Now you can <a href="?">log in</a> with this user.');
			}
			$smarty->display($tpl, $cacheId);
			
			dbClose($dbh);
			
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
				
				$packet = new dlpacket($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
				
				$stack = '';
				$res = mysql_query("select id from packets where archive = '0' order by id;", $dbh);
				#foreach($packets as $packetId => $packet){
				while($row = mysql_fetch_assoc($res)){
					
					if($packet->reloadById($row['id'])){
					
						$trClass = '';
						$status = array();
						
						if(!$packet->get('stime'))
							$status[] = 'waiting';
						elseif($packet->get('stime') && !$packet->get('ftime')){
							$trClass = 'packetIsDownloading';
							$status[] = 'downloading (~'.(int)($packet->filesFinished() / $packet->filesC() * 100).' %)';
						}
						elseif($packet->get('stime') && $packet->get('ftime')){
							$trClass = 'packetHasFinished';
							$status[] = 'finished';
						}
						if($packet->fileErrors())
							$trClass = 'packetHasError';
						if($packet->get('md5Verified'))
							$status[] = 'verified';
						
						$stack .= '
							<tr id="packetTr'.$packet->get('id').'">
								<td class="'.$trClass.'">'.$packet->get('id').'</td>
								<td class="'.$trClass.'">'.$users[$packet->get('_user')]['login'].'</td>
								<td class="'.$trClass.'"><a href="?a=packetEdit&amp;id='.$packet->get('id').'">'.$packet->get('name').'</a></td>
								<td class="'.$trClass.'">'.date($CONFIG['DATE_FORMAT'], $packet->get('ctime')).'</td>
								<td class="'.$trClass.'">'.($packet->get('stime') ? date($CONFIG['DATE_FORMAT'], $packet->get('stime')) : '&nbsp;').'</td>
								<td class="'.$trClass.'">'.($packet->get('ftime') ? date($CONFIG['DATE_FORMAT'], $packet->get('ftime')) : '&nbsp;').'</td>
								<td class="'.$trClass.'">'.join(', ', $status).'</td>
								<td class="'.$trClass.'"><a href="?a=packetInfo&amp;id='.$packet->get('id').'">info</a></td>
								<td class="'.$trClass.'" align="center">'.($packet->get('_user') == $user->get('id') ? '<input id="packetArchiveButton'.$packet->get('id').'" type="button" value="-" onClick="packetArchive('.$packet->get('id').');" />' : '').'</td>
							</tr>
						';
					}
					
				}
				$smarty->assign('stack', $stack);
				
				dbClose($dbh);
				
			}
			$smarty->display($tpl, $cacheId);
			
		break;
		
		case 'packetEdit':
			
			$tpl = $a.'.tpl';
			$cacheId = $a;
			if(!$smarty->isCached($tpl, $cacheId)){
				smartyAssignStd($smarty);
				
				$error = '';
				$packet = new dlpacket($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
				
				$filesOut = '';
				if($id){
					// Edit
					if($packet->loadById($id)){
						if($user->get('id') != $packet->get('_user'))
							$error .= '<li>This packet is owned by another user.</li>';
						if($packet->filesDownloading())
							$error .= '<li>You can not modify a downloading packet.</li>';
						if($packet->get('ftime'))
							$error .= '<li>You can not modify a finished packet.</li>';
						if($packet->get('archive'))
							$error .= '<li>This packet is archived.</li>';
					
						$smarty->assign('nameValue', $packet->get('name'));
						$smarty->assign('nameDisabled', 'disabled="disabled"');
						$smarty->assign('source', $packet->get('source'));
						$smarty->assign('password', $packet->get('password'));
						
						if($packet->loadFiles())
							foreach($packet->files as $fileId => $file)
								$filesOut .= $file->get('uri')."\n";
					}
				}
				else
					$filesOut = $_POST['files'];
				
				$smarty->assign('id', $id);
				$smarty->assign('files', $filesOut);
				if($error != ''){
					$smarty->assign('error', '<ul>'.$error.'</ul>');
				}
				else{
					$smarty->assign('formBegin', '<form action="?a=packetEditSave&amp;id='.$id.'" method="post">');
					$smarty->assign('formEnd', '</form>');
					$smarty->assign('save', '<input type="submit" value="Save" />');
					
				}
				
			}
			$smarty->display($tpl, $cacheId);
			
			
		break;
		
		case 'packetEditSave':
			
			$name = checkInput($_POST['name'], 'a-zA-Z0-9._ -', 256);
			$name = str_replace(array(' ', 'ä', 'Ä', 'ü', 'Ü', 'ö', 'Ö', 'ß'), array('-', 'ae', 'Ae', 'ue', 'Ue', 'oe', 'Oe', 'ss'), $name);
			if($name == '')
				$name = 'noname';
			
			$urlsstr = $_POST['urls'];
			$urlsstr = str_replace("\r", '', $urlsstr);
			
			$urls =  array();
			foreach(preg_split("/\n/s", $urlsstr) as $url)
				if($url != '')
					$urls[] = preg_replace('/["\']/', '', $url);
			
			$source = preg_replace('/["\']/', '', $_POST['source']);
			$password = preg_replace('/["\']/', '', $_POST['password']);
			
			$dbh = dbConnect();
			if($id){
				// Edit
				$packet = new dlpacket($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
				if($packet->loadById($id)){
					
					if($user->get('id') != $packet->get('_user') || $packet->filesDownloading() || $packet->get('ftime'))
						exit();
					
					$files = getDbTable($dbh, 'files', "where _packet = '$id'");
					foreach($files as $fileId => $file)
						foreach($urls as $urlNum => $url)
							if($file['uri'] == $url){
								$files[$fileId]['__hold'] = true;
								break;
							}
					
					foreach($files as $fileId => $file)
						if(!isset($file['__hold']))
							mysql_query("delete from files where id = '".$file['id']."' limit 1;", $dbh);
					
					// Add new
					foreach($urls as $urlNum => $url){
						$found = false;
						foreach($files as $fileId => $file)
							if($file['uri'] == $url){
								$found = true;
								break;
							}
						
						if(!$found)
							mysql_query("insert into files(_user, _packet, uri, ctime) values ('".$user->get('id')."', '$id', '$url', '".mktime()."');", $dbh);
						
					}
					
					mysql_query("update packets set source = '$source', password = '$password' where id = '$id' limit 1;");
					
				}
			}
			else{
				// Add new
				mysql_query("insert into packets(_user, name, archive, source, password, ctime) values ('".$user->get('id')."', '$name', '1', '$source', '$password', '".mktime()."');", $dbh);
				$pid = mysql_insert_id($dbh);
				foreach($urls as $url)
					mysql_query("insert into files(_user, _packet, uri, ctime) values ('".$user->get('id')."', '$pid', '$url', '".mktime()."');", $dbh);
				mysql_query("update packets set archive = '0' where id = '$pid' limit 1;");
			}
			dbClose($dbh);
			
			header('Location: ?');
			
		break;
		
		case 'packetArchive':
			
			$packet = new dlpacket($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
			if($packet->loadById($id)){
				
				if($user->get('id') != $packet->get('_user'))
					exit();
				
				#mysql_query("delete from files where _packet = '$id';");
				#mysql_query("delete from packets where id = '$id' limit 1;");
				
				mysql_query("update packets set archive = '1' where id = '$id' limit 1;");
				
			}
			
			
			if(!$noredirect)
				header('Location: ?');
			
		break;
		
		case 'packetInfo':
			
			$packet = new dlpacket($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
			if($packet->loadById($id)){
				
				$filename = 'phpdl.'.$packet->get('id').'.'.$packet->get('name').'.txt';
				
				#header('Content-Type: application/octet-stream');
				header('Content-Type: text/plain');
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				
				print "Name: ".$packet->get('name')."\n";
				if($packet->get('source') != '')
					print "Source: ".$packet->get('source')."\n";
				if($packet->get('password') != '')
					print "Password: ".$packet->get('password')."\n";
				
				if($packet->loadFiles()){
					print "\n\n";
					
					foreach($packet->files as $fileId => $file)
						print $file->get('uri')."\n";
				}
				
				flush();
			}
			else
				print "failed";
			
		break;
		
		case 'container':
			
			$container = checkInput($_GET['c'], 'a-z0-9', 8);
			$containerLibPath = './lib/container/container.'.$container.'.php';
			
			$tpl = $a.'.tpl';
			$cacheId = $a;
			if(!$smarty->isCached($tpl, $cacheId)){
				smartyAssignStd($smarty);
				
				$error = '';
				if(!file_exists($containerLibPath))
					$error .= '<li>Container path "'.$containerLibPath.'" does not exist.</li>';
				
				$smarty->assign('container', $container);
				$smarty->assign('error', $error != '' ? '
					<tr>
						<td colspan="2" class="msgError"><ul>'.$error.'</ul></td>
					</tr>
				' : '');
				
				if($sa == 'exec'){
					if(file_exists($containerLibPath)){
						include_once($containerLibPath);
						
						$content = $_POST['content'];
						$contentPlain = '';
						
						if($content == '')
							if(isset($_FILES['file']))
								if($_FILES['file']['size'] > 0 && $_FILES['file']['error'] == 0)
									$content = file_get_contents($_FILES['file']['tmp_name']);
						
						if($content != '')
							$contentPlain = containerExec($content);
						
						$smarty->assign('contentPlain', $contentPlain != '' ? '
							<form action="?a=packetEdit" method="post">
								<tr><td colspan="2"><textarea name="files" rows="20" cols="60">'.$contentPlain.'</textarea></td></tr>
								<tr><td colspan="2"><input type="submit" value="Assume to a new packet"></td></tr>
							</form>
							<tr><td colspan="2">&nbsp;</td></tr>
						' : '');
					}
				}
				
			}
			$smarty->display($tpl, $cacheId);
			
		break;
		
		case 'scheduler':
			
			$tpl = $a.'.tpl';
			$cacheId = $a;
			if(!$smarty->isCached($tpl, $cacheId)){
				smartyAssignStd($smarty);
				
				$dbh = dbConnect();
				$schedulerActive = abs(scheduler($dbh));
				
				$schedulerOut = '';
				$res = mysql_query("select * from scheduler order by sortnr;", $dbh);
				$schedulerNum = mysql_num_rows($res);
				$schedulerC = 0;
				while($sched = mysql_fetch_assoc($res)){
					
					$schedulerC++;
					$move = '';
					if($schedulerNum > 1){
						if($schedulerC == 1)
							$move = '<a href="?a=schedulerMove&amp;id='.$sched['id'].'&amp;sortnr='.$sched['sortnr'].'&amp;dir=d"><img src="img/button_down.gif" border="0" /></a>';
						elseif($schedulerC <= $schedulerNum - 1)
							$move = '<a href="?a=schedulerMove&amp;id='.$sched['id'].'&amp;sortnr='.$sched['sortnr'].'&amp;dir=d"><img src="img/button_down.gif" border="0" /></a> <a href="?a=schedulerMove&amp;id='.$sched['id'].'&amp;sortnr='.$sched['sortnr'].'&amp;dir=u"><img src="img/button_up.gif" border="0" /></a>';
						else
							$move = '<a href="?a=schedulerMove&amp;id='.$sched['id'].'&amp;sortnr='.$sched['sortnr'].'&amp;dir=u"><img src="img/button_up.gif" border="0" /></a>';
						
					}
					
					$trClass = '';
					if($sched['id'] == $schedulerActive)
						$trClass = 'schedulerActive';
					
					$schedulerOut .= '
						<tr>
							<td class="'.$trClass.'"><input type="checkbox" id="active'.$sched['id'].'" '.($sched['active'] ? 'checked="checked"' : '').' onChange="active('.$sched['id'].');" /></td>
							<td class="'.$trClass.'">'.$move.'</td>
							<td class="'.$trClass.'">'.$sched['sortnr'].'</td>
							<td class="'.$trClass.'"><a href="?a=schedulerEdit&amp;id='.$sched['id'].'"><b>'.($sched['name'] == '' ? 'noname' : $sched['name']).'</b></a></td>
							<!--<td class="'.$trClass.'">'.$sched['repeat'].'</td>//-->
							<td class="'.$trClass.'">'.($sched['download'] ? 'yes' : 'no').'</td>
							<td class="'.$trClass.'">'.($sched['activeDayTimeInvert'] ? 'yes' : 'no').'</td>
							<td class="'.$trClass.'">'.date('H:i:s', mktime(0, 0, 0, date('n'), date('j'), date('Y')) + $sched['activeDayTimeBegin']).'</td>
							<td class="'.$trClass.'">'.date('H:i:s', mktime(0, 0, 0, date('n'), date('j'), date('Y')) + $sched['activeDayTimeEnd']).'</td>
							
							
						</td>
					';
				}
				/*if(!$schedulerActive){
					$trClass = 'schedulerActive';
					$schedulerOut .= '
						<tr>
							<td class="'.$trClass.'"></td>
							<td class="'.$trClass.'"></td>
							<td class="'.$trClass.'">0</td>
							<td class="'.$trClass.'">0</td>
							<!--<td class="'.$trClass.'"></td>//-->
							<td class="'.$trClass.'" colspan="2">Default scheduler</td>
							<td class="'.$trClass.'">yes</td>
							<td class="'.$trClass.'">no</td>
						</td>
					';
				}*/
				
				$smarty->assign('tableColspan', 8);
				$smarty->assign('scheduler', $schedulerOut);
				
				dbClose($dbh);
			}
			$smarty->display($tpl, $cacheId);
			
		break;
		
		case 'schedulerEdit':
			
			$tpl = $a.'.tpl';
			$cacheId = $a;
			if(!$smarty->isCached($tpl, $cacheId)){
				smartyAssignStd($smarty);
				
				$dbh = dbConnect();
				if($id){
					$scheduler = getDbTable($dbh, 'scheduler', "where id = '$id'");
					$sched = $scheduler[$id];
					
					$smarty->assign('name', $sched['name']);
					if($sched['active'])
						$smarty->assign('activeChecked', 'checked="checked"');
					$smarty->assign('activeDayTimeBegin', date('H:i:s', mktime(0, 0, 0, date('n'), date('j'), date('Y')) + $sched['activeDayTimeBegin']));
					$smarty->assign('activeDayTimeEnd', date('H:i:s', mktime(0, 0, 0, date('n'), date('j'), date('Y')) + $sched['activeDayTimeEnd']));
					if($sched['download'])
						$smarty->assign('downloadChecked', 'checked="checked"');
					if($sched['activeDayTimeInvert'])
						$smarty->assign('activeDayTimeInvertChecked', 'checked="checked"');
					$smarty->assign('sortnr', $sched['sortnr']);
					
					$smarty->assign('del', '
						<tr>
							<td colspan="2"><a href="?a=schedulerDel&amp;id='.$sched['id'].'">Delete</a></td>
						</tr>
					');
				}
				else{
					$res = mysql_fetch_assoc(mysql_query("select max(sortnr) m from scheduler;", $dbh));
					$sortnr = $res['m'] + 1;
					
					$smarty->assign('activeChecked', 'checked="checked"');
					$smarty->assign('downloadChecked', 'checked="checked"');
					$smarty->assign('sortnr', $sortnr);
				}
				dbClose($dbh);
				
				$smarty->assign('id', $id);
			}
			$smarty->display($tpl, $cacheId);
			
		break;
		
		case 'schedulerEditExec':
			
			$name = checkInput($_POST['name'], 'a-zA-Z0-9.,-_ ', 255);
			$active = (int)$_POST['active'];
			$activeDayTimeBegin = $_POST['activeDayTimeBegin'];
			$activeDayTimeEnd = $_POST['activeDayTimeEnd'];
			$download = (int)$_POST['download'];
			$activeDayTimeInvert = (int)$_POST['activeDayTimeInvert'];
			$sortnr = (int)$_POST['sortnr'];
			
			$activeDayTimeBeginHour = 0;
			$activeDayTimeBeginMin = 0;
			$activeDayTimeBeginSec = 0;
			
			$activeDayTimeEndHour = 0;
			$activeDayTimeEndMin = 0;
			$activeDayTimeEndSec = 0;
			
			
			if($name == '')
				$name = 'noname';
			
			if(preg_match('/^(\d{1,2}).(\d{1,2})$/', $activeDayTimeBegin, $res))
				list(, $activeDayTimeBeginHour, $activeDayTimeBeginMin) = $res;
			elseif(preg_match('/^(\d{1,2}).(\d{1,2}).(\d{1,2})$/', $activeDayTimeBegin, $res))
				list(, $activeDayTimeBeginHour, $activeDayTimeBeginMin, $activeDayTimeBeginSec) = $res;
			
			if(preg_match('/^(\d{1,2}).(\d{1,2})$/', $activeDayTimeEnd, $res))
				list(, $activeDayTimeEndHour, $activeDayTimeEndMin) = $res;
			elseif(preg_match('/^(\d{1,2}).(\d{1,2}).(\d{1,2})$/', $activeDayTimeEnd, $res))
				list(, $activeDayTimeEndHour, $activeDayTimeEndMin, $activeDayTimeEndSec) = $res;
			
			$activeDayTimeBeginTs = mktime($activeDayTimeBeginHour, $activeDayTimeBeginMin, $activeDayTimeBeginSec, date('n'), date('j'), date('Y')) - mktime(0, 0, 0, date('n'), date('j'), date('Y'));
			$activeDayTimeEndTs = mktime($activeDayTimeEndHour, $activeDayTimeEndMin, $activeDayTimeEndSec, date('n'), date('j'), date('Y')) - mktime(0, 0, 0, date('n'), date('j'), date('Y'));
			
			if($activeDayTimeBeginTs >= 86400 || $activeDayTimeBeginTs < 0)
				$activeDayTimeBeginTs = 0;
			if($activeDayTimeEndTs >= 86400 || $activeDayTimeEndTs < 0)
				$activeDayTimeEndTs = 86399;
			
			$dbh = dbConnect();
			if($id)
				mysql_query("update scheduler set name = '$name', active = '$active', activeDayTimeInvert = '$activeDayTimeInvert', activeDayTimeBegin = '$activeDayTimeBeginTs', activeDayTimeEnd = '$activeDayTimeEndTs', sortnr = '$sortnr', download = '$download' where id = '$id' limit 1;", $dbh);
			else
				mysql_query("insert into scheduler(_users, name, active, activeDayTimeInvert, activeDayTimeBegin, activeDayTimeEnd, sortnr, download, ctime) values ('".$user->get('id')."', '$name', '$active', '$activeDayTimeInvert', '$activeDayTimeBeginTs', '$activeDayTimeEndTs', '$sortnr', '$download', '".mktime()."');", $dbh);
			
			dbClose($dbh);
			
			header('Location: ?a=scheduler');
			
		break;
		
		case 'schedulerMove':
			
			$direction = checkInput($_GET['dir'], 'du', 1);
			$sortnr = (int)$_GET['sortnr'];
			
			$dbh = dbConnect();
			
			if($direction == 'd'){
				mysql_query("update scheduler set sortnr = sortnr - 1 where sortnr = ".($sortnr + 1).";");
				mysql_query("update scheduler set sortnr = sortnr + 1 where id = '$id' limit 1;");
			}
			else{
				mysql_query("update scheduler set sortnr = sortnr + 1 where sortnr = ".($sortnr - 1).";");
				mysql_query("update scheduler set sortnr = sortnr - 1 where id = '$id' limit 1;");
			}
			
			dbClose($dbh);
			
			header('Location: ?a=scheduler');
			
		break;
		
		case 'schedulerSort':
			
			$dbh = dbConnect();
			
			$sortnr = 1;
			$res = mysql_query("select id, sortnr from scheduler order by sortnr, id;", $dbh);
			while($sched = mysql_fetch_assoc($res))
				mysql_query("update scheduler set sortnr = '".($sortnr++)."' where id = '".$sched['id']."' limit 1;", $dbh);
			dbClose($dbh);
			
			header('Location: ?a=scheduler');
			
		break;
		
		case 'schedulerDel':
			
			$dbh = dbConnect();
			mysql_query("delete from scheduler where id = '$id' limit 1;", $dbh);
			dbClose($dbh);
			
			header('Location: ?a=schedulerSort');
			
		break;
		
		case 'schedulerActive':
			
			$active = (int)$_GET['active'];
			
			$dbh = dbConnect();
			mysql_query("update scheduler set active = '$active' where id = '$id' limit 1;", $dbh);
			dbClose($dbh);
			
		break;
		
		case 'logoutExec':
			
			setcookie('userSessionId', 'x', mktime());
			$user->save('sessionId', 'y');
			header('Location: ?');
			
		break;
		
	}
	
}

?>