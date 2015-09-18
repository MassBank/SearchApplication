<?php
class Peak_Search_Peak_By_Formula_Param extends Abstract_Search_Param
{
	
	private $_compound_id;
	private $_formula_list;
	private $_mode;
	
	public function get_compound_id(){
		return $this->_compound_id;
	}
	
	public function set_compound_id($_compound_id){
		$this->_compound_id = $_compound_id;
	}
	
	public function get_formula_list(){
		return $this->_formula_list;
	}
	
	public function set_formula_list($_formula_list){
		$this->_formula_list = $_formula_list;
	}
	
	public function get_mode(){
		return $this->_mode;
	}
	
	public function set_mode($_mode){
		$this->_mode = $_mode;
	}
	
}
?>