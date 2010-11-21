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


$DLFILE_ERROR = array(
	'ERROR_NO_ERROR' => 0,
	'ERROR_UNKOWN' => 1,
	'ERROR_NO_FREE_SLUTS' => 2,
	'ERROR_MD5_FAILED' => 3,
	'ERROR_DOWNLOAD_FAILED' => 4,
	'ERROR_FILE_SIZE_IS_NULL' => 5,
	'ERROR_NO_HOSTERPLUGIN_FOUND' => 6,
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