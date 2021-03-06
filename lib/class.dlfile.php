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
include_once('class.dlpacket.php');


$DLFILE_ERROR = array(
	
	'ERROR_NO_ERROR' => 0,
	
	'ERROR_UNKOWN' => 100,
	'ERROR_DOWNLOAD_FAILED' => 101,
	'ERROR_NO_FREE_SLUTS' => 200,
	
	
	'ERROR_FILE_SIZE_IS_NULL' => 300,
	'ERROR_FILE_SIZE_IS_WRONG' => 301,
	'ERROR_FILE_MD5_FAILED' => 320,
	
	'ERROR_HOSTERPLUGIN_NOT_FOUND' => 600,
	
);

function getDlFileErrorMsg($errno){
	global $DLFILE_ERROR;
	
	$num = 0;
	$c = count(array_keys($DLFILE_ERROR));
	foreach($DLFILE_ERROR as $err => $num)
		if($num == $errno)
			return $err;
	
	return 'ERROR_UNKOWN';
}

class dlfile extends dbh{
	
	public $packet;
	
	function __construct($dbHost, $dbName, $dbUser, $dbPass){
		
		$this->dbh = null;
		$this->dbhConfig = array('DB_HOST' => $dbHost, 'DB_NAME' => $dbName, 'DB_USER' => $dbUser, 'DB_PASS' => $dbPass, 'DB_TABLE' => 'files');
		$this->data = array();
		$this->dataChanges = array();
		
		$this->packet = null;
		
	}
	
	function packetLoad(){
		$this->packet = new dlpacket($this->dbhConfig['DB_HOST'], $this->dbhConfig['DB_NAME'], $this->dbhConfig['DB_USER'], $this->dbhConfig['DB_PASS']);
		return $this->packet->loadById($this->data['_packet']);
	}
	
	function __destruct(){
		// __destruct
		$this->_dbhClose();
	}
	
};
