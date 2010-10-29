<?php

/*
	Created @ 25.10.2010 by TheFox@fox21.at
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

if(!defined('ANTIHACK')) die('Hacking attempt.');


class dbh{
	
	var $dbh;
	var $dbhConfig;
	var $data;
	var $dataChanges;
	
	
	function __construct(){
		//
	}
	
	function loadById($id){
		$this->_dbhCheck();
		
		$res = mysql_query("select * from ".$this->dbhConfig['DB_TABLE']." where id = '$id' limit 1;", $this->dbh);
		if($res){
			
			$row = mysql_fetch_assoc($res);
			if(isset($row['id'])){
				#print "dbh.loadById ".$row['id']."<br>\n";
				foreach($row as $key => $val)
					$this->data[$key] = $val;
				#ve($this->data);
				return true;
			}
		}
		return false;
	}
	
	function save($item = null, $value = null){
		
		
		$this->_dbhCheck();
		#print "dbh.save '$item' = '$value'\n";
		if($item && $value)
			$this->set($item, $value);
		
		
		$dataChangesLen = count($this->dataChanges);
		if($dataChangesLen){
			$n = 0;
			$sql = 'update '.$this->dbhConfig['DB_TABLE'].' set ';
			foreach($this->dataChanges as $key => $val){
				$sql .= "$key = '".$this->get($key)."'";
				$n++;
				if($n < $dataChangesLen)
					$sql .= ', ';
			}
			$sql .= " where id = '".$this->get('id')."' limit 1;";
			
			#print "dbh.save '$sql'\n";
			mysql_query($sql, $this->dbh);
			
			$this->dataChanges = array();
		}
	}
	
	function get($item){
		return $this->data[$item];
	}
	
	function set($item, $value){
		#print "dbh.set<br>\n";
		$this->data[$item] = $value;
		$this->dataChanges[$item] = true;
	}
	
	
	// Internal functions.
	
	function _dbhConnect(){
		$this->dbh = @mysql_connect($this->dbhConfig['DB_HOST'], $this->dbhConfig['DB_USER'], $this->dbhConfig['DB_PASS']);
		if(!$this->dbh)
			die('ERROR: no connection to database [1]');
		mysql_select_db($this->dbhConfig['DB_NAME'], $this->dbh);
	}
	
	function _dbhCheck(){
		#print "dbh._dbhCheck<br>\n";
		if($this->dbh){
			if(!mysql_ping($this->dbh))
				$this->_dbhConnect();
		}
		else{
			if($this->dbhConfig['DB_HOST'] != '' && $this->dbhConfig['DB_NAME'] != '' && $this->dbhConfig['DB_USER'] != '' && $this->dbhConfig['DB_PASS'] != '')
				$this->_dbhConnect();
			else
				die('ERROR: no dbh config is set');
		}
	}
	
	function _dbhClose(){
		if($this->dbh){
			@mysql_close($this->dbh);
			$this->dbh = null;
		}
	}
	
	function __destruct(){
		//
	}
	
}

?>