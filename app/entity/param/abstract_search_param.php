<?php
abstract class Abstract_Search_Param
{
	
	private $_instrument_types;
	private $_ms_types;
	
	public function get_instrument_types(){
		return $this->_instrument_types;
	}
	
	public function set_instrument_types($_instrument_types){
		$this->set_array_variable($this->_instrument_types, $_instrument_types);
// 		if ( !empty($_instrument_types) )
// 		{
// 			if ( is_null($this->_instrument_types) ) {
// 				$this->_instrument_types = array();
// 			}
// 			if ( is_array($_instrument_types) ) {
// 				array_merge($this->_instrument_types, $_instrument_types);
// 			} else {
// 				array_push($this->_instrument_types, $_instrument_types);
// 			}
// 		}
// 		$this->_instrument_types = $_instrument_types;
	}
	
	public function get_ms_types(){
		return $this->_ms_types;
	}
	
	public function set_ms_types($_ms_types){
		$this->set_array_variable($this->_ms_types, $_ms_types);
// 		if ( !empty($_ms_types) )
// 		{
// 			if ( is_null($this->_ms_types) ) {
// 				$this->_ms_types = array();
// 			}
// 			if ( is_array($_ms_types) ) {
// 				array_merge($this->_ms_types, $_ms_types);
// 			} else {
// 				array_push($this->_ms_types, $_ms_types);
// 			}
// 		}
// 		$this->_ms_types = $_ms_types;
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