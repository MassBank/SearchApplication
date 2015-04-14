<?php
abstract class Abstract_Search_Param
{
	
	private $_instrument_types;
	private $_ms_types;
	private $_ion_mode;
	// paging
	private $_start 	= 1;
	private $_limit		= 20;
	private $_order;
	private $_sort;
	
	public function get_instrument_types(){
		return $this->_instrument_types;
	}
	
	public function set_instrument_types($_instrument_types){
		$this->set_array_variable($this->_instrument_types, $_instrument_types);
	}
	
	public function get_ms_types(){
		return $this->_ms_types;
	}
	
	public function set_ms_types($_ms_types){
		$this->set_array_variable($this->_ms_types, $_ms_types);
	}
	
	public function get_ion_mode(){
		return $this->_ion_mode;
	}
	
	public function set_ion_mode($_ion_mode){
		$this->_ion_mode = $_ion_mode;
	}
	
	public function get_start(){
		return $this->_start;
	}
	
	public function set_start($_start){
		if (isset($_start)) {
			$this->_start = $_start;
		}
	}
	
	public function get_limit(){
		return $this->_limit;
	}
	
	public function set_limit($_limit){
		if (isset($_limit)) {
			$this->_limit = $_limit;
		}
	}
	
	public function get_sort(){
		return $this->_sort;
	}
	
	public function set_sort($_sort){
		if (isset($_sort)) {
			$this->_sort = $_sort;
		}
	}
	
	public function get_order(){
		return $this->_order;
	}
	
	public function set_order($_order){
		if (isset($_order)) {
			$this->_order = $_order;
		}
	}
	
	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}
	
	public function __set($property, $value) {
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}
	
		return $this;
	}
	
	protected function set_array_variable(&$var, $value)
	{
		if ( !empty($value) )
		{
			if ( is_null($var) ) {
				$var = array();
			}
			if ( is_array($value) ) {
				$var = array_merge($var, $value);
			} else {
				array_push($var, $value);
			}
		}
	}
	
	protected function arrayval($var)
	{
		if ( !empty($var) && !is_array($var) ) {
			$tmp = $var;
			$var = array();
			array_push($var, $tmp);
		}
		return $var;
	}
}
?>