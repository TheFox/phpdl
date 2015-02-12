<?php

/*
	Created @ 16.10.2010
	Copyright (C) 2010 Christian Mayer <http://fox21.at>
	
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


class user extends dbh{
	
	public $isGuest;
	
	
	function __construct($dbHost, $dbName, $dbUser, $dbPass){
		
		#print "user.__construct<br>\n";
		$this->dbh = null;
		$this->dbhConfig = array('DB_HOST' => $dbHost, 'DB_NAME' => $dbName, 'DB_USER' => $dbUser, 'DB_PASS' => $dbPass, 'DB_TABLE' => 'users');
		$this->data = array();
		$this->dataChanges = array();
		
		$this->isGuest = true;
		
		
	}
	
	function loadBySessionId($sessionId){
		/*
			Get the $sessionId from cookie and read the
			user from db.
		*/
		
		#print "dbh.loadBySessionId $sessionId<br>\n";
		if(strlen($sessionId) == 32){ # must be a md5 hex.
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
		
		#print "dbh.loadByLoginAndPassword <br>\n";
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
		$retval = $this->loadById($uid);
		$this->isGuest = !$retval;
		return $retval;
	}
	
	function __destruct(){
		$this->_dbhClose();
	}
	
};
