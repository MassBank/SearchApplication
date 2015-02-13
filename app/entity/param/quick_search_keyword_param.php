<?php
class Quick_Search_Keyword_Param extends Abstract_Search_Param
{
	private $_ion_mode;
	private $_compound_name_term;
	private $_exact_mass;
	private $_tolerance;
	private $_formula_term;
	private $_op1;
	private $_op2;
	
	public function get_ion_mode(){
		return $this->_ion_mode;
	}
	
	public function set_ion_mode($_ion_mode){
		$this->_ion_mode = $_ion_mode;
	}
	
	public function get_compound_name_term(){
		return $this->_compound_name_term;
	}
	
	public function set_compound_name_term($_compound_name_term){
		$this->_compound_name_term = $_compound_name_term;
	}
	
	public function get_exact_mass(){
		return $this->_exact_mass;
	}
	
	public function set_exact_mass($_exact_mass){
		$this->_exact_mass = $_exact_mass;
	}
	
	public function get_tolerance(){
		return $this->_tolerance;
	}
	
	public function set_tolerance($_tolerance){
		$this->_tolerance = $_tolerance;
	}
	
	public function get_formula_term(){
		return $this->_formula_term;
	}
	
	public function set_formula_term($_formula_term){
		$this->_formula_term = $_formula_term;
	}
	
	public function get_op1(){
		return $this->_op1;
	}
	
	public function set_op1($_op1){
		$this->_op1 = $_op1;
	}
	
	public function get_op2(){
		return $this->_op2;
	}
	
	public function set_op2($_op2){
		$this->_op2 = $_op2;
	}
	
}
?>