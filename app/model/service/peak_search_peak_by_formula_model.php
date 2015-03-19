<?php
class Peak_Search_Peak_By_Formula_Model extends Abstract_Search_Model
{
	
	private $_product_ion_model;
	private $_compound_model;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($params)
	{
		$this->_product_ion_model = $this->get_product_ion_model();
		$this->_compound_model = $this->get_compound_model();
		
		$formula_list = $this->_get_unique_formula_list( $params->get_formula_list() );
		$mode = $params->get_mode();
		$compound_ids = array();
		
		#--------------------
		# AND
		#--------------------
		if ( strcasecmp($mode , "AND") == 0 )
		{
			$db_result_list = $this->_product_ion_model->get_compound_ids_intersect_by_formulas($formula_list);
			foreach ( $db_result_list as $db_result )
			{
				array_push( $compound_ids, $db_result['COMPOUND_ID'] );
			}
		}
		
		#--------------------
		# OR
		#--------------------
		else if ( strcasecmp($mode, 'OR') == 0 )
		{
			$db_result_list = $this->_product_ion_model->get_compound_ids_in_formulas($formula_list);
			foreach ( $db_result_list as $db_result )
			{
				array_push( $compound_ids, $db_result['COMPOUND_ID'] );
			}
		}
		
		$compounds = $this->_compound_model->get_compounds_by_ids($compound_ids);
		return $this->get_output($compounds);
	}
	
	protected function get_output($compounds = NULL)
	{
		if ( !empty($compounds) ) {
			foreach ($compounds as $compound)
			{
				$data[] = array(
					'compound_id' => $compound[Column::COMPOUND_ID],
					'title' => $compound[Column::COMPOUND_TITLE],
					'ion_mode' => $compound[Column::COMPOUND_ION_MODE],
					'formula' => $compound[Column::COMPOUND_FORMULA],
					'exact_mass' => $compound[Column::COMPOUND_EXACT_MASS]
				);
			}
		} else {
			$data = array();
		}
	
		return $data;
	}
	
	private function _get_unique_formula_list($formula_list)
	{
		$result = array();
		if ( sizeof($formula_list) > 0 ) {
			$unique_formula_list = array_unique($formula_list); // remove duplicates
			foreach ( $unique_formula_list as $unique_formula ) {
				array_push($result, $unique_formula);
			}
		}
		return $result;
	}
	
}
?>