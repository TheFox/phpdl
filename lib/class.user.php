<?php

/*
	Created @ 16.10.2010 by TheFox@fox21.at
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


class user{
	
	var $dbh;
	var $dbhConfig;
	var $user; // User infos from db.
	var $userChanges;
	
	public $isGuest;
	
	function __construct($dbHost, $dbName, $dbUser, $dbPass){
		
		$this->dbh = null;
		$this->dbhConfig = array('DB_HOST' => $dbHost, 'DB_NAME' => $dbName, 'DB_USER' => $dbUser, 'DB_PASS' => $dbPass);
		$this->user = array();
		$this->userChanges = array();
		$this->isGuest = true;
		
	}
	
	function loadBySessionId($sessionId){
		/*
			Get the $sessionId from cookie and read the
			user from db.
		*/
		
		if($sessionId != ''){
			$this->_dbhCheck();
			
			$res = mysql_query("select id from users where sessionId like '$sessionId' limit 1;", $this->dbh);
			if($res){
				$row = mysql_fetch_assoc($res);
				if(isset($row['id']))
					$this->loadByUserId($row['id']);
			}
		}
	}
	
	function loadByLoginAndPassword($login, $password){
		// Login
		
		$this->_dbhCheck();
		
		$res = mysql_query("select id from users where login like '$login' and password = '$password' limit 1;", $this->dbh);
		if($res){
			$row = mysql_fetch_assoc($res);
			if(isset($row['id']))
				if($this->loadByUserId($row['id'])){
					list($usec, $sec) = explode(' ', microtime());
					$sessionId = md5($_SERVER['REMOTE_ADDR'].rand(1, 999999).((float)$usec + (float)$sec));
					$this->set('sessionId', $sessionId);
					$this->save();
				}	
		}
		return $this->get('sessionId') != '';
	}
	
	function loadByUserId($uid){
		$this->_dbhCheck();
		
		$res = mysql_query("select id, login, password, sessionId, ctime from users where id = '$uid' limit 1;", $this->dbh);
		if($res){
			$row = mysql_fetch_assoc($res);
			if(isset($row['id'])){
				foreach($row as $key => $val)
					$this->user[$key] = $val;
				$this->isGuest = false;
				return true;
			}
		}
		return false;
	}
	
	function get($item){
		return $this->user[$item];
	}
	
	function set($item, $value){
		$this->user[$item] = $value;
		$this->userChanges[$item] = true;
	}
	
	function save(){
		// Save the user to the db.
		
		$this->_dbhCheck();
		
		$userChangesLen = count($this->userChanges);
		if($userChangesLen){
			$n = 0;
			$sql = "update users set ";
			foreach($this->userChanges as $key => $val){
				$sql .= "$key = '".$this->get($key)."'";
				$n++;
				if($n < $userChangesLen)
					$sql .= ', ';
			}
			$sql .= " where id = '".$this->get('id')."' limit 1;";
			mysql_query($sql, $this->dbh);
		}
	}
	
	
	// Internal functions.
	
	function _dbhCheck(){
		if(!$this->dbh && $this->dbhConfig['DB_HOST'] != '' && $this->dbhConfig['DB_NAME'] != '' && $this->dbhConfig['DB_USER'] != '' && $this->dbhConfig['DB_PASS'] != ''){
			$this->dbh = @mysql_connect($this->dbhConfig['DB_HOST'], $this->dbhConfig['DB_USER'], $this->dbhConfig['DB_PASS']);
			if(!$this->dbh)
				die('no connection to database');
			@mysql_select_db($this->dbhConfig['DB_NAME'], $this->dbh);
		}
	}
	
	function _dbhClose(){
		if($this->dbh){
			@mysql_close($this->dbh);
			$this->dbh = null;
		}
	}
	
	function __destruct(){
		// __destruct
		$this->_dbhClose();
	}
	
};

?>