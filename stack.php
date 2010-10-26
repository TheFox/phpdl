<?php

/*
	Created @ 19.10.2010 by TheFox@fox21.at
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
if(isset($_SERVER['SERVER_ADDR'])) die('Hacking attempt.');

include_once('./lib/config.php');
include_once('./lib/functions.php');
include_once('./lib/class.dlpacket.php');
include_once('./lib/class.dlfile.php');


while(true){
	
	$dbh = dbConnect();
	
	#$packets = getDbTable($dbh, 'packets', "where archive = '0'");
	
	$resdls = mysql_fetch_assoc(mysql_query("select count(id) c from files where stime != '0' and ftime = '0';", $dbh));
	if($resdls['c'] >= $CONFIG['DL_SLOTS']){
		print "no free download slots\n";
	}
	else{
		$res = mysql_query("select id from packets where archive = '0' order by id;", $dbh);
		while($row = mysql_fetch_assoc($res)){
			$packet = new dlpacket($CONFIG['DB_HOST'], $CONFIG['DB_NAME'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
			if($packet->loadById($row['id'])){
				print "packet ".$packet->get('id')."\n";
				if(!$packet->fileErrors() && !$packet->get('ftime')){
					if($packet->loadFiles()){
						if($packet->filesUnfinished()){
							
							if(!$packet->get('stime'))
								$packet->save('stime', mktime());
							
							if($nextfile = $packet->getFileNextUnfinished()){
								$nextfile->set('error', $DLFILE_ERROR_NO_ERROR);
								$nextfile->set('stime', mktime());
								$nextfile->set('ftime', 0);
								$nextfile->save();
								
								print "\tstart download ".$nextfile->get('id')."\n";
								system('php wget.php '.$nextfile->get('id').' &> /dev/null &');
								
								break;
							}
							else
								print "\tno next file\n";
						}
						else{
							print "\tall files finished ".$packet->filesUnfinished()."\n";
							$packet->save('ftime', mktime());
						}
					}
				}
			}
			unset($packet);
		}
	}
	dbClose($dbh);
	
	
	print "sleep 1\n\n";
	sleep(1);
}

?>