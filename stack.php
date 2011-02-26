<?php

/*
	Created @ 19.10.2010 by TheFox@fox21.at
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
if(PHP_SAPI != 'cli') die('Please start this script from terminal with <b>./stackstart</b>');


chdir(substr($_SERVER['SCRIPT_FILENAME'], 0, strlen($_SERVER['SCRIPT_FILENAME']) - strlen(basename($_SERVER['SCRIPT_FILENAME']))));

if(!file_exists('install/INSTALLED')){
	print "ERROR: You must first install PHPDL.\n";
	exit(1);
}

include_once('./lib/config.php');
include_once('./lib/functions.php');
include_once('./lib/class.dlpacket.php');
include_once('./lib/class.dlfile.php');

declare(ticks = 1);
$LOG_LAST = '';
$SCHEDULER_LAST = 0;
$SHUTDOWN = false;

function main(){
	global $CONFIG, $SCHEDULER_LAST;
	
	
	print "\n";
	printd("start\n");
	printd("cwd: ".getcwd()."\n");
	
	$date = date('Ymd');
	
	fileWrite($CONFIG['PHPDL_STACK_PIDFILE'], posix_getpid());
	
	$n = 0;
	while(!$SHUTDOWN){
		
		$n++;
		
		$dbh = dbConnect();
		$scheduler = scheduler($dbh);
		$filesDownloading = filesDownloading($dbh);
		
		if($SCHEDULER_LAST != $scheduler){
			plog("scheduler '$scheduler' matched\n");
			$SCHEDULER_LAST = $scheduler;
		}
		
		$res = mysql_query("select id from packets where archive = '0' and active = '1' and ftime = '0' order by sortnr, id;", $dbh);
		if(mysql_num_rows($res))
			while($row = mysql_fetch_assoc($res)){
				$packet = new dlpacket($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
				if($packet->loadById($row['id'])){
					
					$packetId = $packet->get('id');
					$packetName = $packet->get('name');
					$packetDirBn = getPacketFilename($packetId, $packetName);
					$packetDownloadDir = 'downloads/loading/'.$packetDirBn;
					$packetFinishedDir = 'downloads/finished/'.$packetDirBn;
					
					if($packet->loadFiles()){
						
						if($packet->filesUnfinished()){
							
							if($scheduler > 0){
								
								if($filesDownloading < $CONFIG['DL_SLOTS']){
									
									if(!$packet->get('stime')){
										$packet->save('stime', mktime());
										printd("hook: packet_download_start.sh\n");
										system("./lib/hook/packet_download_start.sh $packetId '$packetName'");
									}
									
									if($nextfile = $packet->getFileNextUnfinished()){
										
										while($nextfile->get('error')){
											printd("nextfile ".$nextfile->get('id')."\n");
											$nextfile = $packet->getFileNextUnfinished();
											if(!$nextfile)
												break;
											sleep(1);
										}
										
										if($nextfile){
											printd("packet $packetId: download ".$nextfile->get('id')."\n");
											$sh = 'php wget.php '.$nextfile->get('id').' 1>> log/wget.'.$date.'.log 2>> log/wget.'.$date.'.log &';
											printd("exec '$sh'\n");
											system($sh);
											sleep(1);
											break;
										}
									}
								}
								else
									plog("no free download slots (".$CONFIG['DL_SLOTS'].")\n");
							}
						}
						else{
							
							printd("packet $packetId: all files finished\n");
							if(!$packet->get('stime'))
								$packet->set('stime', mktime());
							$packet->save('ftime', mktime());
							$packet->md5Verify();
							$packet->sizeVerify();
							
							if(!$packet->fileErrors()){
								if(file_exists($packetFinishedDir)){
									$files = scandir($packetDownloadDir);
									foreach($files as $file)
										if($file != '.' && $file != '..' && file_exists($packetDownloadDir.'/'.$file))
											rename($packetDownloadDir.'/'.$file, $packetFinishedDir.'/'.$file);
									
									reset($files);
									rmdir($packetDownloadDir);
								}
								else
									rename($packetDownloadDir, $packetFinishedDir);
								
							}
							
							printd("hook: packet_download_end.sh\n");
							system("./lib/hook/packet_download_end.sh $packetId '$packetName'");
							
						}
					}
				}
				unset($packet);
			}
		else
			plog("no active download\n");
		
		dbClose($dbh);
		
		$SHUTDOWN = shutdownCheck();
		
		sleep(5);
	}
	
	shutdown();
}

function sigHandler($sig){
	printd("sigHandler $sig\n");
	switch($sig){
		case SIGTERM:
		case SIGINT:
			shutdown(1);
		break;
	}
}

function shutdown($err = 0){
	global $CONFIG;
	if(file_exists($CONFIG['PHPDL_STACK_PIDFILE'])){
		unlink($CONFIG['PHPDL_STACK_PIDFILE']);
		
		printd("exit $err\n");
		exit($err);
	}
}

function shutdownCheck(){
	global $CONFIG;
	if(file_exists($CONFIG['PHPDL_STACK_SDFILE'])){
		unlink($CONFIG['PHPDL_STACK_SDFILE']);
		return true;
	}
	return false;
}

function plog($text){
	// Print log.
	global $LOG_LAST;
	if($LOG_LAST != $text){
		printd($text);
		$LOG_LAST = $text;
	}
}

pcntl_signal(SIGTERM, 'sigHandler');
pcntl_signal(SIGINT, 'sigHandler');
main();

?>