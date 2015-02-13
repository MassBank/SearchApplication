<?php
class Peak_Search_Peak_By_Mz_Param extends Abstract_Search_Param
{
	
	private $_compound_id;
	private $_num_of_mz;
	
	public function get_compound_id(){
		return $this->_compound_id;
	}
	
	public function set_compound_id($_compound_id){
		$this->_compound_id = $_compound_id;
	}
	
	public function get_num_of_mz(){
		return $this->_num_of_mz;
	}
	
	public function set_num_of_mz($_num_of_mz){
		$this->_num_of_mz = $_num_of_mz;
	}
	
}
?>