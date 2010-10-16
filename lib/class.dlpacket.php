<?php

/* Created @ 16.10.2010 by TheFox */

include_once('class.dlfile.php');

class dlPacket{
	
	var $dbh;
	
	function __construct($dbh){
		
		$this->dbh = $dbh;
		
	}
	
	function __destruct(){
		// __destruct
	}
	
};

?>