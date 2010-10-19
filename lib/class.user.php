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

class user{
	
	var $dbh;
	var $user; // User infos from db.
	
	public $id;
	public $isActive;
	public $isGuest;
	
	function __construct($dbh){
		
		$this->dbh = $dbh;
		$this->user =  array();
		$this->id = 0;
		$this->isActive = false;
		$this->isGuest = true;
		
	}
	
	function loadBySessionId($sessionId){
		/*
			Get the $sessionId from cookie and read the
			user from db.
		*/
		
		if($this->dbh){
			
		}
	}
	
	function loadByUserId($uid){
		if($this->dbh){
			
		}
	}
	
	function get($item){
		return $this->user[$item];
	}
	
	function set($item, $value){
		$this->user[$item] = $value;
	}
	
	function save(){
		// Save the user to the db.
		
		if($this->dbh && count($this->user)){
			
		}
	}
	
	// Internal functions.
	function _isActive(){
		if(isset($this->user['password']))
			if($this->user['password'] != '' && $this->user['password'] != 'x')
				$this->isActive = true;
	}
	
	function __destruct(){
		// __destruct
	}
	
};

?>