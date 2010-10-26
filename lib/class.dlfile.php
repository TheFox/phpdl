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

include_once('class.dbh.php');


$DLFILE_ERROR_NO_ERROR = 0;
$DLFILE_ERROR_UNKOWN = 1;
$DLFILE_ERROR_NO_FREE_SLUTS = 2; # yes, sluts! rs response "All free download slots are full".
$DLFILE_ERROR_MD5_FAILED = 3;

function getDlFileErrorMsg($errno){
	
	if($errno == 0)
		return 'DLFILE_ERROR_NO_ERROR';
	if($errno == 1)
		return 'DLFILE_ERROR_UNKOWN';
	if($errno == 2)
		return 'DLFILE_ERROR_NO_FREE_SLUTS';
	if($errno == 3)
		return 'DLFILE_ERROR_MD5_FAILED';
	
	return '';
}

class dlfile extends dbh{
	
	function __construct($dbHost, $dbName, $dbUser, $dbPass){
		
		$this->dbh = null;
		$this->dbhConfig = array('DB_HOST' => $dbHost, 'DB_NAME' => $dbName, 'DB_USER' => $dbUser, 'DB_PASS' => $dbPass, 'DB_TABLE' => 'files');
		$this->data = array();
		$this->dataChanges = array();
		
	}
	
	function __destruct(){
		// __destruct
		$this->_dbhClose();
	}
	
};

?>