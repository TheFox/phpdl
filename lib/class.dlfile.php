<?php

/* Created @ 16.10.2010 by TheFox */

class dlFile{
	
	var $dbh;
	
	function __construct($dbh){
		
		$this->dbh = $dbh;
		
	}
	
	function __destruct(){
		// __destruct
	}
	
};

?>