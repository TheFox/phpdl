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
if(isset($_SERVER['SERVER_ADDR'])) die('Hacking attempt.');


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

function main(){
	global $CONFIG, $SCHEDULER_LAST;
	
	
	print "\n";
	printd("start\n");
	printd("cwd: ".getcwd()."\n");
	
	$date = date('Ymd');
	
	fileWrite($CONFIG['PHPDL_STACK_PIDFILE'], posix_getpid());
	
	$n = 0;
	while(true){
		
		$n++;
		$dbh = dbConnect();
		$scheduler = scheduler($dbh);
		$filesDownloading = filesDownloading($dbh);
		
		if($SCHEDULER_LAST != $scheduler){
			plog("scheduler '$scheduler' matched\n");
			$SCHEDULER_LAST = $scheduler;
		}
		
		$res = mysql_query("select id from packets where archive = '0' and ftime = '0' order by sortnr, id;", $dbh);
		while($row = mysql_fetch_assoc($res)){
			$packet = new dlpacket($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
			if($packet->loadById($row['id'])){
				
				$packetDirBn = $packet->get('id').'.'.strtolower($packet->get('name'));
				$packetDirBn = str_replace(' ', '.', $packetDirBn);
				
				$packetDownloadDir = 'downloads/loading/'.$packetDirBn;
				$packetFinishedDir = 'downloads/finished/'.$packetDirBn;
				
				if($packet->loadFiles()){
					
					if($packet->filesUnfinished()){
						
						if($scheduler > 0){
							
							if($filesDownloading < $CONFIG['DL_SLOTS']){
								
								if(!$packet->get('stime')){
									$packet->save('stime', mktime());
									
									if(file_exists($packetDownloadDir))
										rmdirr($packetDownloadDir);
									if(file_exists($packetFinishedDir))
										rmdirr($packetFinishedDir);
								}
								
								if($nextfile = $packet->getFileNextUnfinished()){
									
									printd("nextfile error\n");
									while($nextfile->get('error')){
										printd("nextfile ".$nextfile->get('id')."\n");
										$nextfile = $packet->getFileNextUnfinished();
										if(!$nextfile)
											break;
										sleep(1);
									}
									printd("nextfile error done\n");
									
									if($nextfile){
										if(!file_exists($packetDownloadDir)){
											mkdir($packetDownloadDir);
											chmod($packetDownloadDir, 0755);
										}
										
										printd("packet ".$packet->get('id').": download ".$nextfile->get('id')."\n");
										$sh = 'php wget.php '.$nextfile->get('id').' "'.$packetDownloadDir.'" "'.$packet->get('speed').'" 1>> log/wget.'.$date.'.log 2>> log/wget.'.$date.'.log &';
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
						if(!$packet->fileErrors()){
							printd("packet ".$packet->get('id').": all files finished with no errors\n");
							if(!$packet->get('stime'))
								$packet->set('stime', mktime());
							$packet->save('ftime', mktime());
							$packet->md5Verify();
							
							rename($packetDownloadDir, $packetFinishedDir);
						}
					}
				}
			}
			unset($packet);
		}
		
		dbClose($dbh);
		
		sleep(5);
	}
	
	printd("exit\n");
}

function sigHandler($sig){
	global $CONFIG;
	
	printd("sigHandler $sig\n");
	switch($sig){
		case SIGTERM:
		case SIGINT:
			if(file_exists($CONFIG['PHPDL_STACK_PIDFILE'])){
				unlink($CONFIG['PHPDL_STACK_PIDFILE']);
				
				printd("exit\n");
				exit(1);
			}
		break;
	}
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