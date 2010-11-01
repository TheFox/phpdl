<?php

/*
	Created @ 30.10.2010 by TheFox@fox21.at
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


$SESSION_TTL = 900;
$SMARTY_PATH = '../lib/smarty/smarty.php';
$CONFIG_PATH = '../lib/config.php';
$CONFIG_TPL_PATH = 'config.php.tpl';
$INSTALL_SQL_PATH = 'install.sql';


session_start();
if(isset($_SESSION['TTL'])){
	if($_SESSION['TTL'] <= mktime())
		sessionNew();
}
else
	sessionNew();

if(file_exists($CONFIG_PATH)){
	if(filesize($CONFIG_PATH) > 0){
		htmlHead();
		htmlInstallFinished();
		htmlFooter();
		
		exit();
	}
}
else{
	print "you must first run ./install/install.sh in your shell.";
	exit(1);
}

$smarty = null;
if(file_exists($SMARTY_PATH)){
	include_once($SMARTY_PATH);
	$smarty = new Smarty();
}

$a = $_GET['a'];

switch($a){

	default:
		
		
		
		htmlHead();
?>
<form action="?a=save" method="post">
	<table border="0" cellpadding="3" cellspacing="3" width="100%">
		<tr>
			<td colspan="2">0. General terms and conditions</td>
		</tr>
		<tr>
			<td width="30">&nbsp;</td>
			<td>
				<b>USE AT YOUR OWN RISK!</b><br />
				Used brand names and trademarks are the property of their respective owners. The programmer of PHPDL (in the following "programmer") has no liability for, from the use of the information service (whether correct or incorrect), resulting damages or consequences. The programmer assumes no liability for damage caused without his fault by downloading, installation, storage and use of PHPDL.<br />
				All rights reserved.
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2">1. License</td>
		</tr>
		<tr>
			<td width="30">&nbsp;</td>
			<td>
				PHPDL is licensed under the <a href="http://www.gnu.org/licenses/" target="_blank">GNU General Public License</a>, version 3.<br />
				For more information see <a href="../LICENSE" target="_blank">LICENSE</a>.
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2">2. MySQL connection</td>
		</tr>
		<tr>
			<td width="30">&nbsp;</td>
			<td>
				<table border="0" cellpadding="3" cellspacing="3">
					<tr>
						<td colspan="3">You must first create a new database.</td>
					</tr>
					<tr>
						<td>Database Host</td>
						<td><pre>$DB_HOST</pre></td>
						<td><input type="text" name="DB_HOST" id="DB_HOST" value="<?php print isset($_SESSION['DB_HOST']) ? $_SESSION['DB_HOST'] : ''; ?>" /></td>
					</tr>
					<tr>
						<td>Database Name</td>
						<td><pre>$DB_NAME</pre></td>
						<td><input type="text" name="DB_NAME" id="DB_NAME" value="<?php print isset($_SESSION['DB_NAME']) ? $_SESSION['DB_NAME'] : ''; ?>" /></td>
					</tr>
					<tr>
						<td>Database Username</td>
						<td><pre>$DB_USER</pre></td>
						<td><input type="text" name="DB_USER" id="DB_USER" value="<?php print isset($_SESSION['DB_USER']) ? $_SESSION['DB_USER'] : ''; ?>" /></td>
					</tr>
					<tr>
						<td>Database Password</td>
						<td><pre>$DB_PASS</pre></td>
						<td><input type="text" name="DB_PASS" id="DB_PASS" value="<?php print isset($_SESSION['DB_PASS']) ? $_SESSION['DB_PASS'] : ''; ?>" /></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="button" id="mysqlCheckConnectionButton" value="Check MySQL connection" onClick="mysqlCheckConnection();" />
						</td>
					</tr>
					<tr>
						<td colspan="3"><div id="mysqlCheckConnectionSatus"></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2">3. MySQL Import</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<table border="0" cellpadding="3" cellspacing="3">
					<tr>
						<td><input type="button" id="mysqlImportButton" value="Import" disabled="disabled" onClick="mysqlImport();" /> (First 'Check MySQL connection' in step 1.)</td>
					</tr>
					<tr>
						<td><div id="mysqlImportSatus"></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2">4. Classes check</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<table border="0" cellpadding="3" cellspacing="3">
					<tr>
						<td>Smarty Class</td>
						<td><?php print $smarty ? '<b><font color="#009900">Loaded</font></b> ('.$smarty->_version.')' : '<b><font color="#cc0000">Failed</font></b>'; ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2">5. File/Directory permissions</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<table border="0" cellpadding="3" cellspacing="3">
					<tr>
						<td><pre>lib/config.php</pre></td>
						<td><?php print is_writeable($CONFIG_PATH) ? '<b><font color="#009900">OK</font></b>' : '<b><font color="#cc0000">Failed</font></b>'; ?></td>
					</tr>
					<tr>
						<td><pre>downloads</pre></td>
						<td><?php print dirWriteable('../downloads') ? '<b><font color="#009900">OK</font></b>' : '<b><font color="#cc0000">Failed</font></b>'; ?></td>
					</tr>
					<tr>
						<td><pre>downloads/finished</pre></td>
						<td><?php print dirWriteable('../downloads/finished') ? '<b><font color="#009900">OK</font></b>' : '<b><font color="#cc0000">Failed</font></b>'; ?></td>
					</tr>
					<tr>
						<td><pre>downloads/loading</pre></td>
						<td><?php print dirWriteable('../downloads/loading') ? '<b><font color="#009900">OK</font></b>' : '<b><font color="#cc0000">Failed</font></b>'; ?></td>
					</tr>
					<tr>
						<td><pre>tpl</pre></td>
						<td><?php print file_exists('../tpl') ? '<b><font color="#009900">OK</font></b>' : '<b><font color="#cc0000">Failed</font></b>'; ?></td>
					</tr>
					<tr>
						<td><pre>cache/tpl_c</pre></td>
						<td><?php print dirWriteable('../cache/tpl_c') ? '<b><font color="#009900">OK</font></b>' : '<b><font color="#cc0000">Failed</font></b>'; ?></td>
					</tr>
					<tr>
						<td><pre>cache/html</pre></td>
						<td><?php print dirWriteable('../cache/html') ? '<b><font color="#009900">OK</font></b>' : '<b><font color="#cc0000">Failed</font></b>'; ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2"><input type="submit" value="Save" /></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><small>Copyright &copy; 2010 <a href="http://fox21.at/">FOX21.at</a></small></td>
		</tr>
	</table>
</form>
<?php
		htmlFooter();
		
	break;
	
	case 'save':
		
		htmlHead();
		
		$DB_HOST = $_POST['DB_HOST'];
		$DB_NAME = $_POST['DB_NAME'];
		$DB_USER = $_POST['DB_USER'];
		$DB_PASS = $_POST['DB_PASS'];
		
		
		if($smarty){
			$smarty->debugging = false;
			$smarty->caching = false;
			$smarty->cache_lifetime = $CONFIG['SMARTY_CACHE_LIFETIME'];
			$smarty->template_dir = '../tpl';
			$smarty->compile_dir = '../cache/tpl_c';
			$smarty->cache_dir = '../cache/html';
			
			
			$smarty->assign('PHPDL_INSTALLED', mktime());
			
			$smarty->assign('DB_HOST', $DB_HOST);
			$smarty->assign('DB_NAME', $DB_NAME);
			$smarty->assign('DB_USER', $DB_USER);
			$smarty->assign('DB_PASS', $DB_PASS);
			
			$ok = false;
			
			if(is_writeable($CONFIG_PATH))
				if($fh = fopen($CONFIG_PATH, 'w')){
					fwrite($fh, $smarty->fetch($CONFIG_TPL_PATH));
					fclose($fh);
				}
			
			if(file_exists($CONFIG_PATH))
				if(filesize($CONFIG_PATH) > 0)
					$ok = true;
			
			if($ok)
				htmlInstallFinished();
			else
				print '<b><font color="#cc0000">Installation failed</font></b>';
			
		}
		else
			print '<b><font color="#cc0000">Installation failed. No Smarty available!</font></b>';
		
		htmlFooter();
		
	break;
	
	case 'mysqlCheckConnection':
		
		
		$_SESSION['DB_HOST'] = $_GET['DB_HOST'];
		$_SESSION['DB_NAME'] = $_GET['DB_NAME'];
		$_SESSION['DB_USER'] = $_GET['DB_USER'];
		$_SESSION['DB_PASS'] = $_GET['DB_PASS'];
		
		$dbh = @mysql_connect($_GET['DB_HOST'], $_GET['DB_USER'], $_GET['DB_PASS']);
		if($dbh){
			$sel = @mysql_select_db($_GET['DB_NAME'], $dbh);
			if($sel)
				if(@mysql_close($dbh)){
					print 'true';
					exit();
				}
			
		}
		print 'false';
	break;
	
	case 'mysqlImport':
		
		$retval = 'false';
		$DB_HOST = $_GET['DB_HOST'];
		$DB_NAME = $_GET['DB_NAME'];
		$DB_USER = $_GET['DB_USER'];
		$DB_PASS = $_GET['DB_PASS'];
		
		if(file_exists($INSTALL_SQL_PATH)){
			$sh = "mysql --host=$DB_HOST --user=$DB_USER --password=$DB_PASS --database=$DB_NAME --default_character_set utf8 < $INSTALL_SQL_PATH";
			exec($sh, $res, $ret);
			if(!$ret)
				$retval = 'true';
		}
		
		print $retval;
		
	break;
	
}

function htmlHead(){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>PHPDL Install</title>
		<script type="text/javascript" src="../lib/jquery/jquery-1.4.3.js"></script>
	</head>
	<body>
		<script type="text/javascript">
			
			function mysqlCheckConnection(){
				var button = $('#mysqlCheckConnectionButton');
				var status = $('#mysqlCheckConnectionSatus');
				var DB_HOST = $('#DB_HOST');
				var DB_NAME = $('#DB_NAME');
				var DB_USER = $('#DB_USER');
				var DB_PASS = $('#DB_PASS');
				var loadingtxt = $('<div>');
				var mysqlImportButton = $('#mysqlImportButton');
				
				status.text('Checking...');
				
				if(DB_HOST.val() == '' || DB_NAME.val() == '' || DB_USER.val() == '' || DB_PASS.val() == ''){
					alert('ERROR: You must complete all fields.');
				}
				else{
					button.attr('disabled', 'disabled');
					//status.append(loadingtxt);
					
					$.ajax({
						type: 'GET',
						url: '?a=mysqlCheckConnection&DB_HOST=' + DB_HOST.val() + '&DB_NAME=' + DB_NAME.val() + '&DB_USER=' + DB_USER.val() + '&DB_PASS=' + DB_PASS.val(),
						success: function(data){
							button.removeAttr('disabled');
							status.text('MySQL connection ');
							if(data == 'true'){
								status.append('<b><font color="#009900">OK</font></b>');
								mysqlImportButton.removeAttr('disabled');
							}
							else
								status.append('<b><font color="#cc0000">Failed</font></b>');
						}
					});
				}
			}
			
			function mysqlImport(){
				var mysqlImportButton = $('#mysqlImportButton');
				var status = $('#mysqlImportSatus');
				var DB_HOST = $('#DB_HOST');
				var DB_NAME = $('#DB_NAME');
				var DB_USER = $('#DB_USER');
				var DB_PASS = $('#DB_PASS');
				
				mysqlImportButton.attr('disabled', 'disabled');
				status.text('Checking...');
				
				$.ajax({
					type: 'GET',
					url: '?a=mysqlImport&DB_HOST=' + DB_HOST.val() + '&DB_NAME=' + DB_NAME.val() + '&DB_USER=' + DB_USER.val() + '&DB_PASS=' + DB_PASS.val(),
					success: function(data){
						
						status.text('Import ');
						if(data == 'true'){
							status.append('<b><font color="#009900">OK</font></b>');
						}
						else{
							status.append('<b><font color="#cc0000">Failed</font></b>');
							mysqlImportButton.removeAttr('disabled');
						}
						
					}
				});
			}
			
			function userAdd(){
				
			}
			
		</script>
<?php
}

function htmlFooter(){
?>
	</body>
</html>
<?php
}

function htmlInstallFinished(){
	print '
		<b><font color="#009900">Installation OK.</font></b><br />
		<br />
		<ul>
			<li>Now you must delete the "install" directory.</li>
			<li>Change the mode for lib/config.php to 644.</li>
			<li>Run ./startstack in your terminal.</li>
		</ul>
	';
}

function ve($v){
	print '<pre>';
	var_export($v);
	print '</pre>';
}

function sessionNew(){
	global $SESSION_TTL;
	
	$_SESSION = array();
	$_SESSION['TTL'] = mktime() + $SESSION_TTL;
}

function dirWriteable($dir){
	$retval = false;
	
	if(file_exists($dir)){
		$file = $dir.'/.tmp';
		if($fh = @fopen($file, 'w')){
			fwrite($fh, 'test');
			fclose($fh);
		}
		
		if(file_exists($file)){
			$retval = true;
			unlink($file);
		}
	}
	
	return $retval;
}


?>