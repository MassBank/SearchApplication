<?php
class Quick_Search_Keyword_Model extends Abstract_Search_Model
{
	
	private $_compound_model;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($params)
	{
		$ion_mode = $params->get_ion_mode();
		$instrument_types = $params->get_instrument_types();
		$ms_types = $params->get_ms_types();
		$compound_name_term = $params->get_compound_name_term();
		$exact_mass = $params->get_exact_mass();
		$tolerance = $params->get_tolerance();
		$formula_term = $params->get_formula_term();
		$op1 = $params->get_op1();
		$op2 = $params->get_op2();
		
		$this->_compound_model = $this->get_compound_model();
		
		$instrument_ids = $this->get_instance_ids_by_types($instrument_types);
		$ms_type_ids = $this->get_ms_type_ids_by_types($ms_types);
		
// 		// instrument ids
// 		if ( !empty($instrument_types) && sizeof($instrument_types) >= 0 ) {
// 			if ( !in_array('all', $instrument_types) ) { // is not all instruments
// 				$instruments = $this->_instrument_model->get_instruments_by_types($instrument_types);
// 				foreach ($instruments as $instrument) {
// 					array_push($instrument_ids, $instrument['INSTRUMENT_ID']);
// 				}
// 			}
// 		}
		
// 		// ms type ids
// 		if ( !empty($ms_types) && sizeof($ms_types) >= 0 ) {
// 			if ( !in_array('all', $ms_types) ) { // is not all instruments
// 				$ms_list = $this->_ms_model->get_ms_list_by_types($ms_types);
// 				foreach ($ms_list as $ms_item) {
// 					array_push($ms_type_ids, $ms_item['MS_ID']);
// 				}
// 			}
// 		}
		
		// mz1 & mz2
		$mz1 = NULL;
		$mz2 = NULL;
		if ( !empty($exact_mass) ) {
			$tolerance = abs($tolerance); // get absolute value of tolerance. (2 or -2 => 2)
			$mz1 = $exact_mass - $tolerance - 0.00001;
			$mz2 = $exact_mass + $tolerance + 0.00001;
		}
		
		// formula term
		if ( !empty($formula_term) ) {
			$formula_term = str_replace("*", "%", $formula_term);
		}
		
		$compounds = $this->_compound_model->get_keyword_search_compounds(
				$compound_name_term, $mz1, $mz2, $formula_term, $op1, $op2,
				$ion_mode, $instrument_ids, $ms_type_ids);
		
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
	
}
?>