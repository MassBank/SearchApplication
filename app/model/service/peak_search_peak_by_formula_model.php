<?php
class Peak_Search_Peak_By_Formula_Model extends Abstract_Search_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($params)
	{
		$formula_list = array();
		if ( sizeof($params->get_formula_list()) > 0 ) {
			$unique_formula_list = array_unique($params->get_formula_list()); // remove duplicates
			foreach ($unique_formula_list as $unique_formula) {
				array_push($formula_list, $unique_formula);
			}
		}
		foreach ($formula_list as $formula) {
			
		}
	}
}
?>