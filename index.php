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
			$password = md5($CONFIG['USER_PASSWORD_SALT'].$_POST['password']);
			
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
			$password = md5($CONFIG['USER_PASSWORD_SALT'].$_POST['password']);
			
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
				#$packets = getDbTable($dbh, 'packets', "where _user = '".$user->get('id')."'");
				$packets = getDbTable($dbh, 'packets', "where archive = '0' order by id");
				
				$stack = '';
				foreach($packets as $packetId => $packet){
					
					$res = mysql_fetch_assoc(mysql_query("select count(id) c from files where _packet = ".$packet['id']." and error != '".$DLFILE_ERROR_NO_ERROR."'", $dbh));
					$errors = $res['c'];
					
					$class = '';
					if($errors)
						$class = 'packetHasError';
					elseif($packet['stime'] && !$packet['ftime'])
						$class = 'packetIsDownloading';
					
					$status = array();
					if($packet['md5Verified'])
						$status[] = 'verified';
					if(!$packet['stime'])
						$status[] = 'waiting';
					elseif($packet['stime'] && !$packet['ftime'])
						$status[] = 'downloading';
					elseif($packet['stime'] && $packet['ftime'])
						$status[] = 'finished';
					
					$stack .= '
						<tr id="packetTr'.$packet['id'].'">
							<td class="'.$class.'">'.$packet['id'].'</td>
							<td class="'.$class.'">'.$users[$packet['_user']]['login'].'</td>
							<td class="'.$class.'"><a href="?a=packetEdit&amp;id='.$packet['id'].'">'.$packet['name'].'</a></td>
							<td class="'.$class.'">'.date($CONFIG['DATE_FORMAT'], $packet['ctime']).'</td>
							<td class="'.$class.'">'.($packet['stime'] ? date($CONFIG['DATE_FORMAT'], $packet['stime']) : '&nbsp;').'</td>
							<td class="'.$class.'">'.($packet['ftime'] ? date($CONFIG['DATE_FORMAT'], $packet['ftime']) : '&nbsp;').'</td>
							<td class="'.$class.'">'.join(', ', $status).'</td>
							<td class="'.$class.'" align="center">'.($packet['_user'] == $user->get('id') ? '<input id="packetArchiveButton'.$packet['id'].'" type="button" value="-" onClick="packetArchive('.$packet['id'].');" />' : '').'</td>
						</tr>
					';
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
			$name = str_replace(' ', '-', $name);
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
		
	}
	
}

?>