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
		$compound_name_term = $params->get_compound_name_term();
		$exact_mass = $params->get_exact_mass();
		$tolerance = $params->get_tolerance();
		$formula_term = $params->get_formula_term();
		$op1 = $params->get_op1();
		$op2 = $params->get_op2();
		
		$instrument_types = $params->get_instrument_types();
		$ms_types = $params->get_ms_types();
		$ion_mode = $params->get_ion_mode();
		$start = $params->get_start();
		$size = $params->get_num();
		
		$this->_compound_model = $this->get_compound_model();
		
		// compound name term
		$compound_name_term = $this->get_mysql_safe_term($compound_name_term);
		
		// formula term
		$formula_term = $this->get_mysql_safe_term($formula_term);
		
		// instrument ids by types
		$instrument_ids = $this->get_instance_ids_by_types($instrument_types);
		
		// mass spectrometry ids by types
		$ms_type_ids = $this->get_ms_type_ids_by_names($ms_types);
		
		// mz1 & mz2
		$mz1 = NULL;
		$mz2 = NULL;
		if ( !empty($exact_mass) ) {
			$tolerance = abs($tolerance); // get absolute value of tolerance. (2 or -2 => 2)
			$mz1 = $exact_mass - $tolerance - 0.00001;
			$mz2 = $exact_mass + $tolerance + 0.00001;
		}
		
		$compounds = $this->_compound_model->get_compounds_by_keywords(
				$compound_name_term, $formula_term, $mz1, $mz2, $op1, $op2,
				$ion_mode, $instrument_ids, $ms_type_ids, $start, $size);
		
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
	
}
?>