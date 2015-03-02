<?php
abstract class Abstract_Search_Param
{
	
	private $_instrument_types;
	private $_ms_types;
	private $_ion_mode;
	// paging
	private $_start 	= 1;
	private $_num		= 20;
	
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
	
	public function get_num(){
		return $this->_num;
	}
	
	public function set_num($_num){
		if (isset($_num)) {
			$this->_num = $_num;
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