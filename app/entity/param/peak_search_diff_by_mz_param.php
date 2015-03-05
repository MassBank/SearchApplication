<?php
class Peak_Search_Diff_By_Mz_Param extends Abstract_Search_Param
{
	
	private $_mz_diff_list;
	private $_formula_list;
	private $_rel_inte;
	private $_tolerance;
	private $_operator;
	
	public function get_mz_diff_list(){
		return $this->_mz_diff_list;
	}

	public function set_mz_diff_list($_mz_diff_list){
		$this->_mz_diff_list = $_mz_diff_list;
	}

	public function get_formula_list(){
		return $this->_formula_list;
	}

	public function set_formula_list($_formula_list){
		$this->_formula_list = $_formula_list;
	}

	public function get_rel_inte(){
		return $this->_rel_inte;
	}

	public function set_rel_inte($_rel_inte){
		$this->_rel_inte = $_rel_inte;
	}

	public function get_tolerance(){
		return $this->_tolerance;
	}

	public function set_tolerance($_tolerance){
		$this->_tolerance = $_tolerance;
	}

	public function get_operator(){
		return $this->_operator;
	}

	public function set_operator($_operator){
		$this->_operator = $_operator;
	}
	
}
?>