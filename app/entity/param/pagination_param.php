<?php
class Pagination_Param
{
	
	private $_start;
	private $_limit;
	private $_sort;
	private $_order;
	
	public function get_start(){
		return $this->_start;
	}
	
	public function set_start($_start){
		$this->_start = $_start;
	}
	
	public function get_limit(){
		return $this->_limit;
	}
	
	public function set_limit($_limit){
		$this->_limit = $_limit;
	}
	
	public function get_sort(){
		return $this->_sort;
	}
	
	public function set_sort($_sort){
		$this->_sort = $_sort;
	}
	
	public function get_order(){
		return $this->_order;
	}
	
	public function set_order($_order){
		$this->_order = $_order;
	}
	
}
?>