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

if(file_exists('install')){
	print "ERROR: You must first install PHPDL.\n";
	exit(1);
}

include_once('./lib/config.php');
include_once('./lib/functions.php');
include_once('./lib/class.dlpacket.php');
include_once('./lib/class.dlfile.php');

declare(ticks = 1);


function main(){
	global $CONFIG;
	
	
	print "pid: ".posix_getpid()."\n";
	print "cwd: ".getcwd()."\n";
	
	$fh = fopen($CONFIG['PHPDL_STACK_PIDFILE'], 'w');
	fwrite($fh, posix_getpid());
	fclose($fh);
	
	$n = 0;
	while(true){
		
		$n++;
		$dbh = dbConnect();
		$scheduler = scheduler($dbh);
		
		#print "dg $n: $scheduler\n";
		
		$resdls = mysql_fetch_assoc(mysql_query("select count(id) c from files where stime != '0' and ftime = '0';", $dbh));
		if($resdls['c'] >= $CONFIG['DL_SLOTS']){
			print "no free download slots\n";
		}
		elseif($scheduler <= 0){
			print "scheduler inactive $scheduler\n";
		}
		elseif($scheduler > 0){
			print "scheduler ok $scheduler\n";
			$res = mysql_query("select id from packets where archive = '0' and ftime = '0' order by sortnr, id;", $dbh);
			while($row = mysql_fetch_assoc($res)){
				$packet = new dlpacket($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
				if($packet->loadById($row['id'])){
					
					print "packet ".$packet->get('id')."\n";
					
					$packetDirBn = $packet->get('id').'.'.strtolower($packet->get('name'));
					$packetDirBn = str_replace(' ', '.', $packetDirBn);
					
					$packetDownloadDir = 'downloads/loading/'.$packetDirBn;
					$packetFinishedDir = 'downloads/finished/'.$packetDirBn;
					
					if(!$packet->fileErrors()){
						if($packet->loadFiles()){
							
							if($packet->filesUnfinished()){
								
								if(!$packet->get('stime')){
									$packet->save('stime', mktime());
								}
								
								if($nextfile = $packet->getFileNextUnfinished()){
									$nextfile->set('error', $DLFILE_ERROR_NO_ERROR);
									$nextfile->set('stime', mktime());
									$nextfile->set('ftime', 0);
									$nextfile->save();
									
									if(!file_exists($packetDownloadDir)){
										mkdir($packetDownloadDir);
										chmod($packetDownloadDir, 0755);
									}
									
									print "\tstart download ".$nextfile->get('id')."\n";
									system('php wget.php '.$nextfile->get('id').' "'.$packetDownloadDir.'" &> /dev/null &');
									
									break;
								}
								else
									print "\tno next file\n";
							}
							else{
								print "\tall files finished\n";
								$packet->save('ftime', mktime());
								$packet->md5Verify();
								
								rename($packetDownloadDir, $packetFinishedDir);
							}
						}
					}
				}
				unset($packet);
			}
		}
		dbClose($dbh);
		
		
		print "sleep 5\n\n";
		sleep(5);
	}
}

function sigHandler($sig){
	global $CONFIG;
	print "sigHandler $sig\n";
	switch($sig){
		case SIGTERM:
		case SIGINT:
			if(file_exists($CONFIG['PHPDL_STACK_PIDFILE'])){
				unlink($CONFIG['PHPDL_STACK_PIDFILE']);
				exit();
			}
		break;
	}
}

pcntl_signal(SIGTERM, 'sigHandler');
pcntl_signal(SIGINT, 'sigHandler');
main();

?>