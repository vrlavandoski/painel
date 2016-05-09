<?php

class ListModel {
	
	protected $result;	

	function ListModel() {
		
	}  
    
    function getRow() {
		return mysqli_fetch_object($this->result);
	}
	
	function getNumRows() {
		return mysqli_num_rows($this->result);
	}
		
}
?>