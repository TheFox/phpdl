<?php

/*
	Created @ 16.10.2010 by TheFox@fox21.at
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

if(!defined('ANTIHACK')) die('Hacking attempt.');

include_once('class.dbh.php');
include_once('class.dlfile.php');


class dlpacket extends dbh{
	
	var $files;
	
	function __construct($dbHost, $dbName, $dbUser, $dbPass){
		#print "packet.__construct\n";
		$this->dbh = null;
		$this->dbhConfig = array('DB_HOST' => $dbHost, 'DB_NAME' => $dbName, 'DB_USER' => $dbUser, 'DB_PASS' => $dbPass, 'DB_TABLE' => 'packets');
		$this->data = array();
		$this->dataChanges = array();
	}
	
	function loadFiles(){
		#print "packet.loadFiles\n";
		$res = mysql_query("select id from files where _packet = ".$this->data['id']." order by id;", $this->dbh);
		while($row = mysql_fetch_assoc($res)){
			#print "packet.loadFiles ".$row['id']."\n";
			$newfile = new dlfile($this->dbhConfig['DB_HOST'], $this->dbhConfig['DB_NAME'], $this->dbhConfig['DB_USER'], $this->dbhConfig['DB_PASS']);
			$newfile->loadById($row['id']);
			$this->files[$row['id']] = $newfile;
		}
		return $this->filesC();
	}
	
	function reloadById($id){
		$this->files = array();
		$this->data = array();
		$this->dataChanges = array();
		return $this->loadById($id);
	}
	
	function filesC(){
		if(count($this->files))
			return count(array_keys($this->files));
		return 0;
	}
	
	function fileErrors(){
		global $DLFILE_ERROR;
		
		$this->_dbhCheck();
		
		$res = mysql_fetch_assoc(mysql_query("select count(id) c from files where _packet = ".$this->data['id']." and error != '".$DLFILE_ERROR['ERROR_NO_ERROR']."';", $this->dbh));
		return (int)$res['c'];
	}
	
	function filesFinished(){
		$this->_dbhCheck();
		
		$res = mysql_fetch_assoc(mysql_query("select count(id) c from files where _packet = ".$this->data['id']." and stime != '0' and ftime != '0';", $this->dbh));
		return (int)$res['c'];
	}
	
	function filesUnfinished(){
		$this->_dbhCheck();
		
		$res = mysql_fetch_assoc(mysql_query("select count(id) c from files where _packet = ".$this->data['id']." and ftime = '0';", $this->dbh));
		return (int)$res['c'];
	}
	
	function filesDownloading(){
		$this->_dbhCheck();
		
		$res = mysql_fetch_assoc(mysql_query("select count(id) c from files where _packet = ".$this->data['id']." and stime != '0' and ftime = '0';", $this->dbh));
		return (int)$res['c'];
	}
	
	function getFileNextUnfinished(){
		foreach($this->files as $id => $file)
			if(!$file->get('stime') && !$file->get('ftime'))
				return $file;
		return null;
	}
	
	function md5Verify(){
		$v = true;
		foreach($this->files as $id => $file)
			if(!$file->get('md5Verified')){
				$v = false;
				break;
			}
		if($v)
			$this->save('md5Verified', 1);
	}
	
	function isDownloading(){
		return $this->get('stime') && !$this->get('ftime');
	}
	
	function isFinished(){
		return $this->get('stime') && $this->get('ftime');
	}
	
	function isArchived(){
		return $this->get('archive');
	}
	
	function __destruct(){
		// __destruct
		#print "packet.__destruct ".$this->data['id']."\n";
		$this->_dbhClose();
	}
	
};

?>